<?php

namespace App\Services;

use App\Models\AngsuranPembayaran;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeterlambatanPembayaran;
use App\Models\PengaturanPembayaran;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Alur pembayaran Ma'had dalam tiga angsuran.
 *
 * Seluruh transfer dilakukan langsung ke rekening resmi kampus. SIMAHAD
 * hanya mencatat tagihan, bukti transfer, dan hasil pemeriksaan. Status
 * "Lunas" selalu berasal dari konfirmasi penerimaan dana oleh Rektorat,
 * tidak pernah otomatis setelah bukti diunggah.
 */
class PembayaranAngsuranService
{
    public function pengaturanAktif(): ?PengaturanPembayaran
    {
        return PengaturanPembayaran::aktif();
    }

    /**
     * Seluruh periode pembayaran, terbaru lebih dahulu.
     *
     * @return Collection<int, PengaturanPembayaran>
     */
    public function daftarPeriode(): Collection
    {
        return PengaturanPembayaran::query()
            ->orderByDesc('periode_dibuka_pada')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Periode yang sedang dikelola Musyrifah: periode aktif, atau periode
     * terakhir bila semua periode sudah ditutup.
     */
    public function periodeKerja(): ?PengaturanPembayaran
    {
        return PengaturanPembayaran::aktif()
            ?? PengaturanPembayaran::query()
                ->orderByDesc('periode_dibuka_pada')
                ->orderByDesc('id')
                ->first();
    }

    /**
     * Buat periode pembayaran baru. Periode baru langsung menjadi periode
     * aktif sehingga Musyrifah dapat mengatur jadwal angsurannya.
     *
     * @param  array{periode: string, tanggal_mulai: string, nama_tahap?: string|null}  $data
     */
    public function buatPeriode(array $data): PengaturanPembayaran
    {
        $mulai = Carbon::parse($data['tanggal_mulai'])->startOfDay();

        $periode = PengaturanPembayaran::query()->create([
            'periode' => trim((string) $data['periode']),
            'nama_tahap' => filled($data['nama_tahap'] ?? null) ? (string) $data['nama_tahap'] : 'Tahap 1',
            'nominal' => AngsuranPembayaran::NOMINAL_BAWAAN[1],
            'periode_dibuka_pada' => $mulai->toDateString(),
            'jatuh_tempo' => $mulai->copy()->addDays(AngsuranPembayaran::HARI_BAWAAN[1])->toDateString(),
            'is_aktif' => true,
        ]);

        $this->jadikanPeriodeAktif($periode);
        $this->pastikanJadwal($periode);

        return $periode->fresh();
    }

    /**
     * Buka periode sekarang juga. Tanggal mulai yang kosong atau masih di
     * masa depan dimajukan ke hari ini agar mahasiswa dapat langsung membayar.
     */
    public function bukaPeriode(PengaturanPembayaran $periode): PengaturanPembayaran
    {
        $mulai = $periode->periode_dibuka_pada;

        if (! $mulai || $mulai->copy()->startOfDay()->isFuture()) {
            $periode->periode_dibuka_pada = now()->startOfDay()->toDateString();
        }

        $periode->is_aktif = true;
        $periode->save();

        $this->jadikanPeriodeAktif($periode);
        $this->pastikanJadwal($periode->fresh());

        return $periode->fresh();
    }

    /**
     * Tutup periode tanpa menghapus tagihan maupun riwayat pembayarannya.
     */
    public function tutupPeriode(PengaturanPembayaran $periode): PengaturanPembayaran
    {
        $periode->update(['is_aktif' => false]);

        return $periode->fresh();
    }

    /**
     * Mahasiswa hanya dapat membayar saat periode aktif sudah dibuka.
     */
    public function pastikanPeriodeDibuka(Pembayaran $tagihan): PengaturanPembayaran
    {
        $pengaturan = PengaturanPembayaran::aktif();

        if (! $pengaturan || ! $pengaturan->sudahDibuka()) {
            throw ValidationException::withMessages([
                'periode' => 'Belum ada periode pembayaran aktif. Silakan menunggu Musyrifah membuka periode pembayaran.',
            ]);
        }

        if (filled($tagihan->periode) && $tagihan->periode !== $pengaturan->periode) {
            throw ValidationException::withMessages([
                'periode' => 'Tagihan ini bukan bagian dari periode pembayaran yang sedang dibuka.',
            ]);
        }

        return $pengaturan;
    }

    /**
     * Jadwal angsuran yang berlaku; baris bawaan dibuat bila belum ada.
     *
     * @return Collection<int, AngsuranPembayaran>
     */
    public function jadwalAktif(?PengaturanPembayaran $pengaturan = null): Collection
    {
        $pengaturan ??= PengaturanPembayaran::aktif();

        if (! $pengaturan) {
            return collect();
        }

        return $this->pastikanJadwal($pengaturan);
    }

    /**
     * Pastikan tiga baris jadwal tersedia untuk periode tertentu.
     *
     * @return Collection<int, AngsuranPembayaran>
     */
    public function pastikanJadwal(PengaturanPembayaran $pengaturan): Collection
    {
        $dasar = $pengaturan->periode_dibuka_pada?->copy()->startOfDay() ?? now()->startOfDay();

        for ($angsuranKe = 1; $angsuranKe <= AngsuranPembayaran::JUMLAH_ANGSURAN; $angsuranKe++) {
            $jadwal = AngsuranPembayaran::query()->firstOrCreate(
                [
                    'periode' => $pengaturan->periode,
                    'angsuran_ke' => $angsuranKe,
                ],
                [
                    'nama_tahap' => 'Tahap '.$angsuranKe,
                    'nominal' => AngsuranPembayaran::NOMINAL_BAWAAN[$angsuranKe],
                    'jatuh_tempo' => $dasar->copy()->addDays(AngsuranPembayaran::HARI_BAWAAN[$angsuranKe])->toDateString(),
                    'reminder_hari' => AngsuranPembayaran::REMINDER_BAWAAN[$angsuranKe],
                    'is_aktif' => true,
                ]
            );

            // Tahap pertama wajib minimal Rp1.000.000 agar aturan tetap terjaga
            // meski jadwal dibuat sebelum aturan ini berlaku.
            if ($angsuranKe === 1 && (float) $jadwal->nominal < AngsuranPembayaran::ANGSURAN_PERTAMA_MINIMAL) {
                $jadwal->update(['nominal' => AngsuranPembayaran::ANGSURAN_PERTAMA_MINIMAL]);
            }
        }

        return AngsuranPembayaran::query()
            ->periode($pengaturan->periode)
            ->orderBy('angsuran_ke')
            ->get();
    }

    /**
     * Simpan pengaturan angsuran dari formulir Musyrifah.
     *
     * @param  array<int, array<string, mixed>>  $baris
     * @return Collection<int, AngsuranPembayaran>
     */
    public function simpanJadwal(array $baris, ?PengaturanPembayaran $pengaturan = null, ?string $tanggalMulai = null): Collection
    {
        $pengaturan ??= $this->periodeKerja();

        if (! $pengaturan) {
            throw ValidationException::withMessages([
                'jadwal' => 'Belum ada periode pembayaran. Buat periode terlebih dahulu sebelum mengatur jadwal angsuran.',
            ]);
        }

        if (filled($tanggalMulai)) {
            $pengaturan->update(['periode_dibuka_pada' => $tanggalMulai]);
        }

        $this->pastikanJadwal($pengaturan);

        foreach ($baris as $data) {
            $angsuranKe = (int) ($data['angsuran_ke'] ?? 0);

            if ($angsuranKe < 1 || $angsuranKe > AngsuranPembayaran::JUMLAH_ANGSURAN) {
                continue;
            }

            $nominal = (float) ($data['nominal'] ?? 0);

            if ($angsuranKe === 1 && $nominal < AngsuranPembayaran::ANGSURAN_PERTAMA_MINIMAL) {
                throw ValidationException::withMessages([
                    'angsuran' => 'Angsuran pertama minimal Rp1.000.000.',
                ]);
            }

            $jadwal = AngsuranPembayaran::query()
                ->periode($pengaturan->periode)
                ->where('angsuran_ke', $angsuranKe)
                ->first();

            if (! $jadwal) {
                continue;
            }

            $jadwal->update([
                'nama_tahap' => filled($data['nama_tahap'] ?? null)
                    ? (string) $data['nama_tahap']
                    : 'Tahap '.$angsuranKe,
                'nominal' => $nominal,
                'jatuh_tempo' => $data['jatuh_tempo'],
                'reminder_hari' => (int) ($data['reminder_hari'] ?? 3),
                'is_aktif' => true,
            ]);
        }

        $this->terapkanJadwalKeTagihan($pengaturan->periode);

        return AngsuranPembayaran::query()
            ->periode($pengaturan->periode)
            ->orderBy('angsuran_ke')
            ->get();
    }

    /**
     * Terapkan nominal dan jatuh tempo baru ke tagihan yang belum dibayar.
     */
    public function terapkanJadwalKeTagihan(string $periode): int
    {
        $jadwal = AngsuranPembayaran::query()->periode($periode)->orderBy('angsuran_ke')->get();
        $jumlah = 0;

        foreach ($jadwal as $baris) {
            $jumlah += Pembayaran::query()
                ->where('periode', $periode)
                ->where('angsuran_ke', $baris->angsuran_ke)
                ->belumLunas()
                ->where(function ($query) {
                    $query->whereNull('bukti_pembayaran')->orWhere('bukti_pembayaran', '');
                })
                ->where(function ($query) {
                    $query->whereNull('status_verifikasi')
                        ->orWhere('status_verifikasi', '!=', Pembayaran::VERIFIKASI_MENUNGGU);
                })
                ->update([
                    'nominal' => (float) $baris->nominal,
                    'jatuh_tempo' => $baris->jatuh_tempo?->toDateString(),
                    'nama_tagihan' => $baris->label(),
                    'reminder_terakhir_pada' => null,
                    'updated_at' => now(),
                ]);
        }

        return $jumlah;
    }

    /**
     * Pastikan tagihan tiga angsuran milik satu mahasiswa tersedia.
     *
     * @return Collection<int, Pembayaran>
     */
    public function sinkronkanTagihan(User $user, ?PengaturanPembayaran $pengaturan = null): Collection
    {
        $pengaturan ??= PengaturanPembayaran::aktif();

        if (! $pengaturan || ! $pengaturan->sudahDibuka()) {
            return collect();
        }

        $jadwal = $this->pastikanJadwal($pengaturan);
        $kategori = $this->kategoriMahasiswa($user);

        foreach ($jadwal as $baris) {
            $tagihan = Pembayaran::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'periode' => $pengaturan->periode,
                    'angsuran_ke' => $baris->angsuran_ke,
                ],
                [
                    'kode_transaksi' => $this->kodeTransaksi($user, $baris->angsuran_ke, $pengaturan->periode),
                    'nama_tagihan' => $baris->label(),
                    'nominal' => (float) $baris->nominal,
                    'jatuh_tempo' => $baris->jatuh_tempo?->toDateString(),
                    'kategori_biaya' => $kategori,
                    'metode_pembayaran' => 'Transfer '.PengaturanPembayaran::REKENING_BANK,
                    'status' => Pembayaran::STATUS_BELUM_LUNAS,
                    'keterangan' => 'Tagihan '.$baris->label().' periode '.$pengaturan->periode,
                ]
            );

            // Tagihan yang belum dibayar mengikuti jadwal terbaru, tetapi
            // nominal yang sudah disesuaikan Musyrifah tidak ditimpa.
            if ($tagihan->wasRecentlyCreated) {
                continue;
            }

            $perubahan = [];

            if (! $tagihan->sudahLunas() && ! filled($tagihan->bukti_pembayaran)) {
                if ($baris->jatuh_tempo && (! $tagihan->jatuh_tempo || ! $tagihan->jatuh_tempo->equalTo($baris->jatuh_tempo))) {
                    $perubahan['jatuh_tempo'] = $baris->jatuh_tempo->toDateString();
                }

                if (($tagihan->kategori_biaya ?? null) !== $kategori) {
                    $perubahan['kategori_biaya'] = $kategori;
                }

                if (blank($tagihan->nama_tagihan) || $tagihan->nama_tagihan !== $baris->label()) {
                    $perubahan['nama_tagihan'] = $baris->label();
                }
            }

            if ($perubahan !== []) {
                $tagihan->update($perubahan);
            }
        }

        return Pembayaran::query()
            ->where('user_id', $user->id)
            ->where('periode', $pengaturan->periode)
            ->with(['pengajuanKeterlambatan' => fn ($query) => $query->latest('id')])
            ->orderBy('angsuran_ke')
            ->orderBy('id')
            ->get();
    }

    /**
     * Sinkronkan tagihan seluruh mahasiswa (dipakai halaman Musyrifah).
     */
    public function sinkronkanSemuaMahasiswa(): int
    {
        $pengaturan = PengaturanPembayaran::aktif();

        if (! $pengaturan || ! $pengaturan->sudahDibuka()) {
            return 0;
        }

        $jumlah = 0;

        User::query()
            ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
            ->select('id')
            ->chunkById(200, function ($mahasiswas) use (&$jumlah, $pengaturan) {
                foreach ($mahasiswas as $mahasiswa) {
                    $this->sinkronkanTagihan($mahasiswa, $pengaturan);
                    $jumlah++;
                }
            });

        return $jumlah;
    }

    /**
     * Ringkasan pembayaran satu mahasiswa untuk halaman pembayaran.
     *
     * @return array<string, mixed>
     */
    public function ringkasan(User $user): array
    {
        $pengaturan = PengaturanPembayaran::aktif();
        $rekening = [
            'bank' => PengaturanPembayaran::REKENING_BANK,
            'nomor' => PengaturanPembayaran::REKENING_NOMOR_RESMI,
            'nama' => PengaturanPembayaran::REKENING_NAMA_RESMI,
        ];
        $kategori = $this->kategoriMahasiswa($user);
        $pengajuan = PengajuanKeterlambatanPembayaran::query()
            ->where('user_id', $user->id)
            ->with('pembayaran')
            ->latest('id')
            ->get();

        if (! $pengaturan) {
            return [
                'pengaturan' => null,
                'dibuka' => false,
                'rekening' => $rekening,
                'kategori' => $kategori,
                'daftar_angsuran' => collect(),
                'tagihan' => collect(),
                'total' => 0.0,
                'total_rupiah' => 'Rp 0',
                'dibayar' => 0.0,
                'dibayar_rupiah' => 'Rp 0',
                'menunggu' => 0.0,
                'menunggu_rupiah' => 'Rp 0',
                'sisa' => 0.0,
                'sisa_rupiah' => 'Rp 0',
                'progres' => 0,
                'lunas' => false,
                'jatuh_tempo_terdekat' => null,
                'pengajuan' => $pengajuan,
            ];
        }

        $dibuka = $pengaturan->sudahDibuka();
        $tagihan = $dibuka ? $this->sinkronkanTagihan($user, $pengaturan) : collect();

        $daftar = $tagihan->map(fn (Pembayaran $p): array => $this->rincianTagihan($p))->values();

        $total = (float) $tagihan->sum(fn (Pembayaran $p): float => (float) $p->nominal);
        $dibayar = (float) $tagihan->filter(fn (Pembayaran $p): bool => $p->sudahLunas())
            ->sum(fn (Pembayaran $p): float => (float) $p->nominal);
        $menunggu = (float) $tagihan
            ->filter(fn (Pembayaran $p): bool => ! $p->sudahLunas()
                && in_array(strtolower((string) $p->status), [Pembayaran::STATUS_MENUNGGU_VERIFIKASI, Pembayaran::STATUS_MENUNGGU_REKTORAT], true))
            ->sum(fn (Pembayaran $p): float => (float) $p->nominal);
        $sisa = max(0.0, $total - $dibayar);
        $progres = $total > 0 ? (int) round($dibayar / $total * 100) : 0;

        $terdekat = $tagihan
            ->reject(fn (Pembayaran $p): bool => $p->sudahLunas() || $p->menungguKonfirmasiRektorat())
            ->filter(fn (Pembayaran $p): bool => $p->jatuh_tempo !== null)
            ->sortBy(fn (Pembayaran $p): int => $p->jatuh_tempo->timestamp)
            ->first();

        return [
            'pengaturan' => $pengaturan,
            'dibuka' => $dibuka,
            'rekening' => $rekening,
            'kategori' => $kategori,
            'daftar_angsuran' => $daftar,
            'tagihan' => $tagihan,
            'total' => $total,
            'total_rupiah' => $this->rupiah($total),
            'dibayar' => $dibayar,
            'dibayar_rupiah' => $this->rupiah($dibayar),
            'menunggu' => $menunggu,
            'menunggu_rupiah' => $this->rupiah($menunggu),
            'sisa' => $sisa,
            'sisa_rupiah' => $this->rupiah($sisa),
            'progres' => $progres,
            'lunas' => $total > 0 && $sisa <= 0.0,
            'jatuh_tempo_terdekat' => $terdekat ? $this->rincianTagihan($terdekat) : null,
            'pengajuan' => $pengajuan,
        ];
    }

    /**
     * Rincian satu tagihan angsuran untuk tampilan.
     *
     * @return array<string, mixed>
     */
    public function rincianTagihan(Pembayaran $pembayaran): array
    {
        $pengajuanTerakhir = $pembayaran->relationLoaded('pengajuanKeterlambatan')
            ? $pembayaran->pengajuanKeterlambatan->first()
            : $pembayaran->pengajuanKeterlambatan()->latest('id')->first();

        return [
            'id' => $pembayaran->id,
            'angsuran_ke' => (int) ($pembayaran->angsuran_ke ?? 1),
            'label' => $pembayaran->labelTahap(),
            'periode' => $pembayaran->periode,
            'nominal' => (float) $pembayaran->nominal,
            'nominal_rupiah' => $pembayaran->nominal_rupiah,
            'jatuh_tempo' => $pembayaran->jatuhTempoTeks(),
            'jatuh_tempo_iso' => $pembayaran->jatuh_tempo?->toDateString(),
            'sisa_hari' => $pembayaran->sisaHari(),
            'status' => strtolower((string) $pembayaran->status),
            'status_label' => $pembayaran->status_label,
            'kelas_status' => $this->kelasStatus($pembayaran),
            'verifikasi' => filled($pembayaran->status_verifikasi) ? strtolower((string) $pembayaran->status_verifikasi) : null,
            'verifikasi_label' => $pembayaran->labelVerifikasi(),
            'kelas_verifikasi' => $pembayaran->kelasVerifikasi(),
            'catatan_verifikasi' => $pembayaran->catatan_verifikasi,
            'bukti_url' => filled($pembayaran->bukti_pembayaran)
                ? Storage::disk('public')->url($pembayaran->bukti_pembayaran)
                : null,
            'bukti_nama' => $pembayaran->namaBerkasBukti(),
            'tanggal_upload' => $pembayaran->tanggal_upload_bukti
                ? $pembayaran->tanggal_upload_bukti->locale('id')->translatedFormat('d F Y, H:i').' WIB'
                : null,
            'tanggal_konfirmasi' => $pembayaran->tanggal_konfirmasi_rektorat
                ? $pembayaran->tanggal_konfirmasi_rektorat->locale('id')->translatedFormat('d F Y, H:i').' WIB'
                : null,
            'tanggal_bayar' => $pembayaran->tanggal_bayar
                ? $pembayaran->tanggal_bayar->locale('id')->translatedFormat('d F Y, H:i').' WIB'
                : null,
            'dapat_unggah' => $pembayaran->dapatDiunggahBukti(),
            'dapat_ajukan' => $pembayaran->dapatDiajukanKeterlambatan(),
            'terlambat' => $pembayaran->terlambat(),
            'pengajuan_menunggu' => $pengajuanTerakhir?->masihMenunggu() ?? false,
            'pengajuan_terakhir' => $pengajuanTerakhir,
        ];
    }

    /**
     * Unggah bukti transfer untuk satu angsuran. Status tidak menjadi Lunas.
     */
    public function unggahBukti(User $user, Pembayaran $tagihan, UploadedFile $berkas): Pembayaran
    {
        if ($tagihan->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'bukti' => 'Tagihan ini bukan milik Anda.',
            ]);
        }

        $this->pastikanPeriodeDibuka($tagihan);

        if (! $tagihan->dapatDiunggahBukti()) {
            throw ValidationException::withMessages([
                'bukti' => 'Tagihan ini tidak dapat diunggah buktinya karena sudah lunas atau sedang menunggu konfirmasi Rektorat.',
            ]);
        }

        $berkasLama = $tagihan->bukti_pembayaran;
        $path = $berkas->store('pembayaran/bukti', 'public');

        $tagihan->update([
            'bukti_pembayaran' => $path,
            'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
            'status_verifikasi' => Pembayaran::VERIFIKASI_MENUNGGU,
            'catatan_verifikasi' => null,
            'tanggal_upload_bukti' => now(),
            'tanggal_verifikasi' => null,
            'diverifikasi_oleh' => null,
            'tanggal_bayar' => null,
        ]);

        if (filled($berkasLama) && $berkasLama !== $path && Storage::disk('public')->exists($berkasLama)) {
            Storage::disk('public')->delete($berkasLama);
        }

        return $tagihan->fresh();
    }

    /**
     * Musyrifah menerima bukti transfer. Dana belum dinyatakan Lunas.
     */
    public function terimaBukti(Pembayaran $tagihan, User $petugas, ?string $catatan = null): Pembayaran
    {
        if ($tagihan->sudahLunas()) {
            throw ValidationException::withMessages([
                'status' => 'Tagihan ini sudah Lunas.',
            ]);
        }

        if (! filled($tagihan->bukti_pembayaran)) {
            throw ValidationException::withMessages([
                'status' => 'Belum ada bukti transfer yang dapat diperiksa.',
            ]);
        }

        $tagihan->update([
            'status' => Pembayaran::STATUS_MENUNGGU_REKTORAT,
            'status_verifikasi' => Pembayaran::VERIFIKASI_DITERIMA,
            'catatan_verifikasi' => $catatan,
            'diverifikasi_oleh' => $petugas->id,
            'tanggal_verifikasi' => now(),
        ]);

        return $tagihan->fresh();
    }

    /**
     * Musyrifah menolak bukti transfer beserta alasannya.
     */
    public function tolakBukti(Pembayaran $tagihan, User $petugas, string $catatan): Pembayaran
    {
        if ($tagihan->sudahLunas()) {
            throw ValidationException::withMessages([
                'status' => 'Tagihan yang sudah Lunas tidak dapat ditolak buktinya.',
            ]);
        }

        $tagihan->update([
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
            'status_verifikasi' => Pembayaran::VERIFIKASI_DITOLAK,
            'catatan_verifikasi' => $catatan,
            'diverifikasi_oleh' => $petugas->id,
            'tanggal_verifikasi' => now(),
            'tanggal_bayar' => null,
        ]);

        return $tagihan->fresh();
    }

    /**
     * Tetapkan Lunas setelah Rektorat mengonfirmasi dana benar-benar diterima.
     */
    public function konfirmasiLunas(Pembayaran $tagihan, User $petugas, ?string $catatan = null): Pembayaran
    {
        if ($tagihan->sudahLunas()) {
            throw ValidationException::withMessages([
                'status' => 'Tagihan ini sudah berstatus Lunas.',
            ]);
        }

        if ($tagihan->menungguVerifikasiBukti()) {
            throw ValidationException::withMessages([
                'status' => 'Periksa dan terima bukti transfer terlebih dahulu sebelum menetapkan Lunas.',
            ]);
        }

        if ($tagihan->buktiDitolak()) {
            throw ValidationException::withMessages([
                'status' => 'Bukti transfer sudah ditolak. Minta mahasiswa mengunggah bukti yang benar.',
            ]);
        }

        $tagihan->update([
            'status' => Pembayaran::STATUS_LUNAS,
            'status_verifikasi' => $tagihan->status_verifikasi ?: Pembayaran::VERIFIKASI_DITERIMA,
            'catatan_verifikasi' => filled($catatan) ? $catatan : $tagihan->catatan_verifikasi,
            'tanggal_bayar' => $tagihan->tanggal_bayar ?? now(),
            'tanggal_konfirmasi_rektorat' => now(),
            'metode_pembayaran' => 'Transfer '.PengaturanPembayaran::REKENING_BANK.' (konfirmasi Rektorat)',
        ]);

        return $tagihan->fresh();
    }

    /**
     * Kembalikan tagihan menjadi Belum Lunas bila konfirmasi dibatalkan.
     */
    public function tandaiBelumLunas(Pembayaran $tagihan, User $petugas, ?string $catatan = null): Pembayaran
    {
        $tagihan->update([
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
            'tanggal_bayar' => null,
            'tanggal_konfirmasi_rektorat' => null,
            'catatan_verifikasi' => filled($catatan) ? $catatan : $tagihan->catatan_verifikasi,
        ]);

        return $tagihan->fresh();
    }

    /**
     * Sesuaikan nominal satu tagihan untuk kategori mahasiswa tertentu.
     */
    public function perbaruiNominal(Pembayaran $tagihan, float $nominal, User $petugas): Pembayaran
    {
        if ($tagihan->sudahLunas()) {
            throw ValidationException::withMessages([
                'nominal' => 'Nominal tagihan yang sudah Lunas tidak dapat diubah.',
            ]);
        }

        if ($nominal <= 0) {
            throw ValidationException::withMessages([
                'nominal' => 'Nominal tagihan harus lebih dari nol.',
            ]);
        }

        if ((int) ($tagihan->angsuran_ke ?? 1) === 1 && $nominal < AngsuranPembayaran::ANGSURAN_PERTAMA_MINIMAL) {
            throw ValidationException::withMessages([
                'nominal' => 'Angsuran pertama minimal Rp1.000.000.',
            ]);
        }

        $tagihan->update([
            'nominal' => $nominal,
            'reminder_terakhir_pada' => null,
        ]);

        return $tagihan->fresh();
    }

    /**
     * Mahasiswa mengajukan keterlambatan beserta alasan dan rencana bayar.
     */
    public function ajukanKeterlambatan(
        User $user,
        Pembayaran $tagihan,
        string $alasan,
        string $tanggalRencanaBayar
    ): PengajuanKeterlambatanPembayaran {
        if ($tagihan->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'alasan' => 'Tagihan ini bukan milik Anda.',
            ]);
        }

        $this->pastikanPeriodeDibuka($tagihan);

        if ($tagihan->sudahLunas()) {
            throw ValidationException::withMessages([
                'alasan' => 'Tagihan ini sudah Lunas sehingga tidak perlu diajukan keterlambatan.',
            ]);
        }

        if ($tagihan->pengajuanKeterlambatan()->menunggu()->exists()) {
            throw ValidationException::withMessages([
                'alasan' => 'Masih ada pengajuan keterlambatan yang menunggu pemeriksaan Musyrifah.',
            ]);
        }

        $nomorSurat = sprintf(
            'SKP/%s/%04d-%02d/MAHAD',
            now()->format('Ymd'),
            $tagihan->id,
            $tagihan->pengajuanKeterlambatan()->count() + 1
        );

        $pengajuan = PengajuanKeterlambatanPembayaran::query()->create([
            'user_id' => $user->id,
            'pembayaran_id' => $tagihan->id,
            'nomor_surat' => $nomorSurat,
            'alasan' => $alasan,
            'tanggal_rencana_bayar' => $tanggalRencanaBayar,
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);

        $this->kirimNotifikasiMusyrifah(
            'Pengajuan keterlambatan '.$tagihan->labelTahap(),
            $user->name.' mengajukan keterlambatan pembayaran '.$tagihan->labelTahap().
                ' dengan rencana bayar '.$pengajuan->tanggalRencanaTeks().'.',
            'keterlambatan:'.$pengajuan->id
        );

        return $pengajuan;
    }

    /**
     * Musyrifah menyetujui atau menolak pengajuan keterlambatan.
     */
    public function putuskanKeterlambatan(
        PengajuanKeterlambatanPembayaran $pengajuan,
        User $petugas,
        bool $setuju,
        ?string $catatan = null
    ): PengajuanKeterlambatanPembayaran {
        if (! $pengajuan->masihMenunggu()) {
            throw ValidationException::withMessages([
                'keputusan' => 'Pengajuan ini sudah diproses sebelumnya.',
            ]);
        }

        if (! $setuju && blank($catatan)) {
            throw ValidationException::withMessages([
                'catatan' => 'Alasan penolakan wajib diisi.',
            ]);
        }

        $pengajuan->update([
            'status' => $setuju
                ? PengajuanKeterlambatanPembayaran::STATUS_DISETUJUI
                : PengajuanKeterlambatanPembayaran::STATUS_DITOLAK,
            'catatan_musyrifah' => $catatan,
            'diproses_oleh' => $petugas->id,
            'diproses_pada' => now(),
        ]);

        $tagihan = $pengajuan->pembayaran;

        // Persetujuan hanya memperbarui jadwal dan pengingat, bukan status Lunas.
        if ($setuju && $tagihan) {
            $tagihan->update([
                'jatuh_tempo' => $pengajuan->tanggal_rencana_bayar?->toDateString(),
                'reminder_terakhir_pada' => null,
            ]);
        }

        Notifikasi::kirim(
            $pengajuan->user_id,
            Notifikasi::TIPE_PEMBAYARAN,
            $setuju ? 'Pengajuan keterlambatan disetujui' : 'Pengajuan keterlambatan ditolak',
            $setuju
                ? 'Jadwal pembayaran '.($tagihan?->labelTahap() ?? 'tagihan').' diperbarui menjadi '.
                    $pengajuan->tanggalRencanaTeks().'. Status tagihan belum Lunas sampai dana dikonfirmasi Rektorat.'
                : 'Pengajuan keterlambatan '.($tagihan?->labelTahap() ?? 'tagihan').' ditolak. '.
                    trim((string) $catatan),
            route('pembayaran'),
            'keterlambatan:'.$pengajuan->id
        );

        return $pengajuan->fresh();
    }

    /**
     * Kirim pengingat sebelum jatuh tempo untuk seluruh tagihan aktif.
     *
     * @return array{dikirim: int, diperiksa: int}
     */
    public function kirimPengingatJatuhTempo(): array
    {
        $dikirim = 0;
        $diperiksa = 0;
        $hariIni = now()->startOfDay();

        // Pengingat hanya untuk periode yang sedang dibuka Musyrifah.
        $pengaturan = PengaturanPembayaran::aktif();
        $periodeTerbuka = $pengaturan && $pengaturan->sudahDibuka() ? $pengaturan->periode : null;

        Pembayaran::query()
            ->belumLunas()
            ->whereNotNull('jatuh_tempo')
            ->with('user')
            ->chunkById(200, function ($daftar) use (&$dikirim, &$diperiksa, $hariIni, $periodeTerbuka) {
                foreach ($daftar as $tagihan) {
                    $diperiksa++;

                    if ($periodeTerbuka === null
                        || (filled($tagihan->periode) && $tagihan->periode !== $periodeTerbuka)) {
                        continue;
                    }

                    if (! $tagihan->user || $tagihan->menungguVerifikasiBukti() || $tagihan->menungguKonfirmasiRektorat()) {
                        continue;
                    }

                    $sisaHari = $tagihan->sisaHari();

                    if ($sisaHari === null || $sisaHari < 0 || $sisaHari > $this->batasReminder($tagihan)) {
                        continue;
                    }

                    if ($tagihan->reminder_terakhir_pada && $tagihan->reminder_terakhir_pada->greaterThanOrEqualTo($hariIni)) {
                        continue;
                    }

                    $judul = 'Pengingat pembayaran '.$tagihan->labelTahap();
                    $pesan = 'Tagihan '.$tagihan->labelTahap().' sebesar '.$tagihan->nominal_rupiah.
                        ' jatuh tempo pada '.$tagihan->jatuhTempoTeks().' ('.$sisaHari.' hari lagi). '.
                        'Transfer langsung ke rekening '.PengaturanPembayaran::REKENING_BANK.' '.
                        PengaturanPembayaran::REKENING_NOMOR_RESMI.' a.n. '.PengaturanPembayaran::REKENING_NAMA_RESMI.'.';

                    Notifikasi::kirim(
                        $tagihan->user_id,
                        Notifikasi::TIPE_PEMBAYARAN,
                        $judul,
                        $pesan,
                        route('pembayaran'),
                        'pembayaran:'.$tagihan->id.':'.$hariIni->toDateString()
                    );

                    $this->kirimEmailPengingat($tagihan, $judul, $pesan);

                    $tagihan->update(['reminder_terakhir_pada' => now()]);
                    $dikirim++;
                }
            });

        return ['dikirim' => $dikirim, 'diperiksa' => $diperiksa];
    }

    /**
     * Batas hari pengingat; mengikuti jadwal angsuran bila tersedia.
     */
    public function batasReminder(Pembayaran $tagihan): int
    {
        $jadwal = AngsuranPembayaran::query()
            ->periode($tagihan->periode)
            ->where('angsuran_ke', (int) ($tagihan->angsuran_ke ?? 1))
            ->first();

        return max(0, (int) ($jadwal->reminder_hari ?? 3));
    }

    /**
     * Kategori biaya mahasiswa (umum/kip) dari data pendaftaran.
     */
    public function kategoriMahasiswa(User $user): string
    {
        $kategori = Pendaftaran::query()
            ->where('user_id', $user->id)
            ->value('kategori');

        $kategori = strtolower(trim((string) $kategori));

        return $kategori !== '' ? $kategori : 'umum';
    }

    /**
     * @return array<string, mixed>
     */
    private function kirimEmailPengingat(Pembayaran $tagihan, string $judul, string $pesan): void
    {
        if (! $tagihan->user?->email) {
            return;
        }

        try {
            Mail::raw(
                $pesan."\n\nSIMAHAD hanya mencatat pembayaran. Dana tidak dikelola oleh SIMAHAD.",
                fn ($mail) => $mail->to($tagihan->user->email)->subject($judul.' - SIMAHAD')
            );
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function kirimNotifikasiMusyrifah(string $judul, string $pesan, string $kunci): void
    {
        User::query()
            ->whereRaw('LOWER(role) = ?', ['musyrifah'])
            ->select('id')
            ->chunkById(200, function ($musyrifahs) use ($judul, $pesan, $kunci) {
                foreach ($musyrifahs as $musyrifah) {
                    Notifikasi::kirim(
                        $musyrifah->id,
                        Notifikasi::TIPE_PEMBAYARAN,
                        $judul,
                        $pesan,
                        route('musyrifah.pembayaran'),
                        $kunci.':'.$musyrifah->id
                    );
                }
            });
    }

    /**
     * Hanya satu periode yang boleh aktif pada satu waktu.
     */
    private function jadikanPeriodeAktif(PengaturanPembayaran $periode): void
    {
        PengaturanPembayaran::query()
            ->whereKeyNot($periode->getKey())
            ->where('is_aktif', true)
            ->update(['is_aktif' => false]);
    }

    private function kelasStatus(Pembayaran $pembayaran): string
    {
        if ($pembayaran->sudahLunas()) {
            return 'lunas';
        }

        if ($pembayaran->menungguKonfirmasiRektorat()) {
            return 'rektorat';
        }

        if ($pembayaran->menungguVerifikasiBukti()) {
            return 'menunggu';
        }

        if ($pembayaran->buktiDitolak()) {
            return 'ditolak';
        }

        if ($pembayaran->terlambat()) {
            return 'terlambat';
        }

        return 'belum';
    }

    private function kodeTransaksi(User $user, int $angsuranKe, string $periode): string
    {
        $kodePeriode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $periode) ?? 'PERIODE');

        return 'TRX-'.$kodePeriode.'-'.$angsuranKe.'-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT);
    }

    private function rupiah(float $nominal): string
    {
        return 'Rp '.number_format($nominal, 0, ',', '.');
    }
}
