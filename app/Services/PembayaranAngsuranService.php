<?php

namespace App\Services;

use App\Models\AngsuranPembayaran;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeterlambatanPembayaran;
use App\Models\PengaturanPembayaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function bukaTahap(AngsuranPembayaran $jadwal): AngsuranPembayaran
    {
        $pengaturan = PengaturanPembayaran::query()->where('periode', $jadwal->periode)->first();

        if (! $pengaturan || ! $pengaturan->is_aktif || ! $pengaturan->sudahDibuka()) {
            throw ValidationException::withMessages([
                'periode' => 'Periode pembayaran harus aktif dan sudah dibuka sebelum tahap dapat dibuka.',
            ]);
        }

        if (! $jadwal->tanggalMulaiTelahTiba()) {
            throw ValidationException::withMessages([
                'periode' => 'Tahap belum dapat dibuka karena tanggal mulainya belum tiba.',
            ]);
        }

        $jadwal->update([
            'dibuka_pada' => $jadwal->dibuka_pada ?? now(),
            'ditutup_pada' => null,
        ]);

        return $jadwal->fresh();
    }

    public function tutupTahap(AngsuranPembayaran $jadwal): AngsuranPembayaran
    {
        if ($jadwal->sudahDibuka()) {
            $jadwal->update(['ditutup_pada' => now()]);
        }

        return $jadwal->fresh();
    }

    public function statusTahap(AngsuranPembayaran $jadwal): string
    {
        $pengaturan = $jadwal->pengaturan;

        if ($pengaturan && ! $pengaturan->is_aktif) {
            return 'ditutup';
        }

        if (! $pengaturan) {
            return 'nonaktif';
        }

        if (! $pengaturan->sudahDibuka() || $jadwal->ditutup_pada !== null) {
            return $pengaturan->sudahDibuka() ? 'ditutup' : 'terjadwal';
        }

        if ($jadwal->sudahDibuka()) {
            return 'dibuka';
        }

        if (! $jadwal->is_aktif) {
            return 'nonaktif';
        }

        return $jadwal->tanggal_mulai?->isFuture() ? 'terjadwal' : 'belum_dibuka';
    }

    /**
     * Periode utama harus aktif; pengajuan keterlambatan tetap tersedia
     * untuk setiap tagihan meskipun tahap pembayarannya belum dibuka.
     */
    public function pastikanPeriodeAktif(Pembayaran $tagihan): PengaturanPembayaran
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
     * Mahasiswa hanya dapat mengunggah bukti pada tahap yang dibuka Musyrifah.
     */
    public function pastikanPeriodeDibuka(Pembayaran $tagihan): PengaturanPembayaran
    {
        $pengaturan = $this->pastikanPeriodeAktif($tagihan);
        $jadwal = $tagihan->jadwalAngsuran();

        if (! $jadwal || ! $jadwal->sudahDibuka()) {
            throw ValidationException::withMessages([
                'periode' => 'Tahap pembayaran ini belum dibuka atau sudah ditutup oleh Musyrifah.',
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
                    'tanggal_mulai' => $dasar->copy()->addDays($angsuranKe > 1 ? AngsuranPembayaran::HARI_BAWAAN[$angsuranKe - 1] : 0)->toDateString(),
                    'reminder_hari' => AngsuranPembayaran::REMINDER_BAWAAN[$angsuranKe],
                    'is_aktif' => true,
                ]
            );

            if ($jadwal->tanggal_mulai === null) {
                $tanggalMulai = $dasar->copy()->addDays($angsuranKe > 1 ? AngsuranPembayaran::HARI_BAWAAN[$angsuranKe - 1] : 0);
                if ($jadwal->jatuh_tempo && $tanggalMulai->greaterThan($jadwal->jatuh_tempo)) {
                    $tanggalMulai = $jadwal->jatuh_tempo->copy()->startOfDay();
                }
                $jadwal->update(['tanggal_mulai' => $tanggalMulai->toDateString()]);
            }

            // Tahap pertama wajib minimal Rp1.000.000 agar aturan tetap terjaga
            // meski jadwal dibuat sebelum aturan ini berlaku.
            if ($angsuranKe === 1 && $jadwal->nominal < AngsuranPembayaran::ANGSURAN_PERTAMA_MINIMAL) {
                $jadwal->update(['nominal' => AngsuranPembayaran::ANGSURAN_PERTAMA_MINIMAL]);
            }
        }

        return AngsuranPembayaran::query()
            ->periode($pengaturan->periode)
            ->with('pengaturan')
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

            $nominal = (int) ($data['nominal'] ?? 0);

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

            $tanggalMulai = filled($data['tanggal_mulai'] ?? null)
                ? Carbon::parse($data['tanggal_mulai'])->startOfDay()
                : ($jadwal->tanggal_mulai?->copy() ?? $pengaturan->periode_dibuka_pada?->copy() ?? now()->startOfDay());
            $jatuhTempo = Carbon::parse($data['jatuh_tempo'])->startOfDay();

            if ($jatuhTempo->lessThan($tanggalMulai)) {
                throw ValidationException::withMessages([
                    'angsuran' => 'Tanggal berakhir tahap '.$angsuranKe.' tidak boleh sebelum tanggal mulainya.',
                ]);
            }

            $jadwal->update([
                'nama_tahap' => filled($data['nama_tahap'] ?? null)
                    ? (string) $data['nama_tahap']
                    : 'Tahap '.$angsuranKe,
                'nominal' => $nominal,
                'tanggal_mulai' => $tanggalMulai->toDateString(),
                'jatuh_tempo' => $jatuhTempo->toDateString(),
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
            $query = Pembayaran::query()
                ->where('periode', $periode)
                ->where('angsuran_ke', $baris->angsuran_ke)
                ->belumLunas()
                ->where(function ($query) {
                    $query->whereNull('bukti_pembayaran')->orWhere('bukti_pembayaran', '');
                })
                ->where(function ($query) {
                    $query->whereNull('status_verifikasi')
                        ->orWhere('status_verifikasi', '!=', Pembayaran::VERIFIKASI_MENUNGGU);
                });

            $jumlah += $query->clone()->update([
                'nominal' => (int) $baris->nominal,
                'nama_tagihan' => $baris->label(),
                'reminder_terakhir_pada' => null,
                'updated_at' => now(),
            ]);

            // Jatuh tempo hasil persetujuan surat keterlambatan tidak ditimpa
            // oleh jadwal umum, baik saat disinkronkan maupun saat diubah.
            if ($baris->jatuh_tempo) {
                $query->whereDoesntHave('pengajuanKeterlambatan', function (Builder $pengajuan): void {
                    $pengajuan->where('status', PengajuanKeterlambatanPembayaran::STATUS_DISETUJUI);
                })->update([
                    'jatuh_tempo' => $baris->jatuh_tempo->toDateString(),
                    'updated_at' => now(),
                ]);
            }
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

        if (! $pengaturan) {
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
                    'nominal' => (int) $baris->nominal,
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
                $punyaPersetujuan = $tagihan->pengajuanKeterlambatan()
                    ->where('status', PengajuanKeterlambatanPembayaran::STATUS_DISETUJUI)
                    ->exists();

                if (! $punyaPersetujuan
                    && $baris->jatuh_tempo
                    && (! $tagihan->jatuh_tempo || ! $tagihan->jatuh_tempo->equalTo($baris->jatuh_tempo))) {
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
            ->with(['pengajuanKeterlambatan' => fn ($query) => $query->latest('id'), 'jadwalPembayaran.pengaturan'])
            ->orderBy('angsuran_ke')
            ->orderBy('id')
            ->get();
    }

    /**
     * Sinkronkan tagihan mahasiswa dalam cakupan pengelola yang login.
     */
    public function sinkronkanSemuaMahasiswa(?User $pengelola = null): int
    {
        $pengaturan = PengaturanPembayaran::aktif();

        if (! $pengaturan) {
            return 0;
        }

        $query = User::query()->whereRaw('LOWER(role) = ?', ['mahasiswa']);

        if ($pengelola && $this->peran($pengelola) === 'musyrifah') {
            $lantai = $this->lantaiMusyrifah($pengelola);
            if ($lantai === null) {
                return 0;
            }
            $query->whereKey($this->queryMahasiswaLantai($lantai)->select('id'));
        }

        $jumlah = 0;

        $query->select('id')->chunkById(200, function ($mahasiswas) use (&$jumlah, $pengaturan) {
            foreach ($mahasiswas as $mahasiswa) {
                $this->sinkronkanTagihan($mahasiswa, $pengaturan);
                $jumlah++;
            }
        });

        return $jumlah;
    }

    public function lantaiMahasiswa(User $mahasiswa): ?int
    {
        return $mahasiswa->kamar?->lantai ?? $mahasiswa->lantai_penempatan;
    }

    public function lantaiMusyrifah(User $musyrifah): ?int
    {
        return $musyrifah->pegawai?->lantai_yang_dikelola ?? $musyrifah->lantai_penempatan;
    }

    public function queryMahasiswaLantai(int $lantai): Builder
    {
        return User::query()
            ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
            ->where(function (Builder $query) use ($lantai): void {
                $query->whereHas('kamar', fn (Builder $kamar) => $kamar->where('lantai', $lantai))
                    ->orWhere(function (Builder $query) use ($lantai): void {
                        $query->whereDoesntHave('kamar')->where('lantai_penempatan', $lantai);
                    });
            });
    }

    public function queryPembayaran(User $pengelola): Builder
    {
        $query = Pembayaran::query();

        if ($this->peran($pengelola) !== 'musyrifah') {
            return $query;
        }

        $lantai = $this->lantaiMusyrifah($pengelola);
        if ($lantai === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('user_id', $this->queryMahasiswaLantai($lantai)->select('id'));
    }

    public function queryPengajuan(User $pengelola): Builder
    {
        return PengajuanKeterlambatanPembayaran::query()
            ->whereIn('pembayaran_id', $this->queryPembayaran($pengelola)->select('id'));
    }

    public function bolehAksesPembayaran(User $pengelola, Pembayaran $pembayaran): bool
    {
        return $this->queryPembayaran($pengelola)->whereKey($pembayaran->getKey())->exists();
    }

    public function bolehAksesPengajuan(User $pengelola, PengajuanKeterlambatanPembayaran $pengajuan): bool
    {
        return $this->queryPengajuan($pengelola)->whereKey($pengajuan->getKey())->exists();
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
            ->with([
                'pembayaran',
                'dokumenTerakhir',
                'dokumens' => fn ($query) => $query->orderBy('versi'),
                'riwayats' => fn ($query) => $query->with('pengubah')->orderBy('id'),
            ])
            ->latest('id')
            ->get();
        $dibuka = (bool) ($pengaturan?->is_aktif && $pengaturan?->sudahDibuka());
        $tagihan = $pengaturan ? $this->sinkronkanTagihan($user, $pengaturan) : collect();
        $daftar = $tagihan->map(fn (Pembayaran $p): array => $this->rincianTagihan($p))->values();

        $total = (float) $tagihan->sum(fn (Pembayaran $p): float => (float) $p->nominal);
        $dibayar = (float) $tagihan->filter(fn (Pembayaran $p): bool => $p->sudahLunas())
            ->sum(fn (Pembayaran $p): float => (float) $p->nominal);
        $menunggu = (float) $tagihan
            ->filter(fn (Pembayaran $p): bool => ! $p->sudahLunas()
                && in_array(strtolower((string) $p->status), [Pembayaran::STATUS_MENUNGGU_VERIFIKASI, Pembayaran::STATUS_MENUNGGU_REKTORAT], true))
            ->sum(fn (Pembayaran $p): float => (float) $p->nominal);
        $sisa = max(0.0, $total - $dibayar);
        $belumDibayar = max(0.0, $sisa - $menunggu);
        $progres = $total > 0 ? (int) round($dibayar / $total * 100) : 0;

        $terdekat = $tagihan
            ->reject(fn (Pembayaran $p): bool => $p->sudahLunas()
                || $p->menungguVerifikasiBukti()
                || $p->menungguKonfirmasiRektorat())
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
            'belum_dibayar' => $belumDibayar,
            'belum_dibayar_rupiah' => $this->rupiah($belumDibayar),
            'tahap_dibuka' => $daftar->where('status_periode', 'dibuka')->count(),
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
        $jadwal = $pembayaran->jadwalAngsuran();
        $pengaturan = PengaturanPembayaran::aktif();
        $periodeAktif = (bool) ($pengaturan?->is_aktif
            && $pengaturan?->sudahDibuka()
            && $pengaturan?->periode === $pembayaran->periode);
        $tahapDibuka = $periodeAktif && (bool) $jadwal?->sudahDibuka();
        $statusPeriode = $jadwal ? $this->statusTahap($jadwal) : 'belum_dibuka';

        return [
            'id' => $pembayaran->id,
            'angsuran_ke' => (int) ($pembayaran->angsuran_ke ?? 1),
            'label' => $pembayaran->labelTahap(),
            'periode' => $pembayaran->periode,
            'nominal' => (float) $pembayaran->nominal,
            'nominal_rupiah' => $pembayaran->nominal_rupiah,
            'jatuh_tempo' => $pembayaran->jatuhTempoTeks(),
            'jatuh_tempo_iso' => $pembayaran->jatuh_tempo?->toDateString(),
            'tanggal_mulai' => $jadwal?->tanggalMulaiTeks() ?? '-',
            'tanggal_mulai_iso' => $jadwal?->tanggal_mulai?->toDateString(),
            'tanggal_berakhir' => $jadwal?->jatuhTempoTeks() ?? $pembayaran->jatuhTempoTeks(),
            'status_periode' => $statusPeriode,
            'status_periode_label' => match ($statusPeriode) {
                'dibuka' => 'Dibuka',
                'ditutup' => 'Ditutup',
                'terjadwal' => 'Terjadwal',
                'nonaktif' => 'Nonaktif',
                default => 'Belum Dibuka',
            },
            'sisa_hari' => $pembayaran->sisaHari(),
            'status' => strtolower((string) $pembayaran->status),
            'status_label' => $pembayaran->status_label,
            'kelas_status' => $this->kelasStatus($pembayaran),
            'verifikasi' => filled($pembayaran->status_verifikasi) ? strtolower((string) $pembayaran->status_verifikasi) : null,
            'verifikasi_label' => $pembayaran->labelVerifikasi(),
            'kelas_verifikasi' => $pembayaran->kelasVerifikasi(),
            'catatan_verifikasi' => $pembayaran->catatan_verifikasi,
            'bukti_url' => filled($pembayaran->bukti_pembayaran)
                ? route('pembayaran.bukti.lihat', $pembayaran->id)
                : null,
            'bukti_grup' => $pembayaran->bukti_grup,
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
            // Form yang sama dipakai untuk unggah pertama atau mengganti bukti
            // selama belum diterima Musyrifah.
            'bukti_sudah_diunggah' => filled($pembayaran->bukti_pembayaran),
            'bukti_ditolak' => $pembayaran->buktiDitolak(),
            'dapat_unggah' => $tahapDibuka && $this->bolehUnggahAtauGantiBukti($pembayaran),
            'dapat_ajukan' => $periodeAktif && $pembayaran->dapatDiajukanKeterlambatan(),
            'terlambat' => $pembayaran->terlambat(),
            'pengajuan_menunggu' => $pengajuanTerakhir
                ? ($pengajuanTerakhir->masihMenunggu() || $pengajuanTerakhir->perluUnggahDokumen())
                : false,
            'pengajuan_terakhir' => $pengajuanTerakhir,
        ];
    }

    /**
     * Unggah bukti untuk satu tahap atau seluruh sisa tagihan sekaligus.
     * Satu bukti yang sama diperiksa dan dikonfirmasi sebagai satu grup.
     */
    public function unggahBukti(User $user, Pembayaran $tagihan, UploadedFile $berkas, bool $bayarSemua = false): Pembayaran
    {
        if ($tagihan->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'bukti' => 'Tagihan ini bukan milik Anda.',
            ]);
        }

        $path = null;

        try {
            $hasil = DB::transaction(function () use ($user, $tagihan, $berkas, $bayarSemua, &$path): Pembayaran {
                $tagihan = Pembayaran::query()->whereKey($tagihan->getKey())->lockForUpdate()->firstOrFail();
                $pengaturan = $this->pastikanPeriodeDibuka($tagihan);

                if (! $this->bolehUnggahAtauGantiBukti($tagihan)) {
                    throw ValidationException::withMessages([
                        'bukti' => 'Bukti tidak dapat diubah karena sudah diterima Musyrifah, menunggu konfirmasi Rektorat, atau tagihan sudah Lunas.',
                    ]);
                }

                $query = Pembayaran::query()
                    ->where('user_id', $user->id)
                    ->where('periode', $pengaturan->periode)
                    ->lockForUpdate();

                if ($bayarSemua) {
                    $query->where(function (Builder $query) use ($tagihan): void {
                        $query->where('status', Pembayaran::STATUS_BELUM_LUNAS)
                            ->orWhereKey($tagihan->id);
                        if (filled($tagihan->bukti_grup)) {
                            $query->orWhere('bukti_grup', $tagihan->bukti_grup);
                        }
                    });
                } elseif (filled($tagihan->bukti_grup)) {
                    $query->where('bukti_grup', $tagihan->bukti_grup);
                } else {
                    $query->whereKey($tagihan->id);
                }

                $target = $query->get()->filter(fn (Pembayaran $item): bool => ! $item->sudahLunas()
                    && ! $item->menungguKonfirmasiRektorat()
                    && ($item->status === Pembayaran::STATUS_BELUM_LUNAS
                        || $item->id === $tagihan->id
                        || (filled($tagihan->bukti_grup) && $item->bukti_grup === $tagihan->bukti_grup)));

                if ($target->doesntContain('id', $tagihan->id)) {
                    throw ValidationException::withMessages([
                        'bukti' => 'Tagihan ini sudah tidak memerlukan pembayaran.',
                    ]);
                }

                $path = $berkas->store('pembayaran/bukti', 'local');
                if (! is_string($path) || $path === '') {
                    throw ValidationException::withMessages([
                        'bukti' => 'Gagal menyimpan bukti transfer. Silakan coba kembali.',
                    ]);
                }

                $grup = $tagihan->bukti_grup ?: (string) Str::uuid();
                $target->each->update([
                    'bukti_pembayaran' => $path,
                    'bukti_grup' => $grup,
                    'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
                    'status_verifikasi' => Pembayaran::VERIFIKASI_MENUNGGU,
                    'catatan_verifikasi' => null,
                    'tanggal_upload_bukti' => now(),
                    'tanggal_verifikasi' => null,
                    'diverifikasi_oleh' => null,
                    'tanggal_bayar' => null,
                    'tanggal_konfirmasi_rektorat' => null,
                ]);

                return $tagihan->fresh();
            });
        } catch (Throwable $exception) {
            // Hanya berkas baru yang dibuang; bukti lama tetap disimpan.
            if (filled($path)) {
                Storage::disk('local')->delete($path);
            }
            throw $exception;
        }

        $this->kirimNotifikasiMusyrifah(
            'Bukti pembayaran '.$hasil->labelTahap(),
            $user->name.' mengunggah bukti transfer untuk '.$hasil->labelTahap().'. Bukti menunggu pemeriksaan Musyrifah.',
            'pembayaran:'.$hasil->id,
            $user
        );

        return $hasil;
    }

    /**
     * @return Collection<int, Pembayaran>
     */
    private function grupTagihan(Pembayaran $tagihan, bool $kunci = false): Collection
    {
        $query = Pembayaran::query()
            ->where('user_id', $tagihan->user_id)
            ->where('periode', $tagihan->periode);

        if (filled($tagihan->bukti_grup)) {
            $query->where('bukti_grup', $tagihan->bukti_grup);
        } else {
            $query->whereKey($tagihan->id);
        }

        if ($kunci) {
            $query->lockForUpdate();
        }

        return $query->orderBy('angsuran_ke')->get();
    }

    /**
     * Hak mahasiswa untuk unggah/ganti bukti: belum disetujui Musyrifah.
     * Saat status masih menunggu pemeriksaan, bukti boleh diperbaiki.
     */
    private function bolehUnggahAtauGantiBukti(Pembayaran $tagihan): bool
    {
        if ($tagihan->sudahLunas() || $tagihan->menungguKonfirmasiRektorat()) {
            return false;
        }

        if (strtolower(trim((string) $tagihan->status_verifikasi)) === strtolower((string) Pembayaran::VERIFIKASI_DITERIMA)) {
            return false;
        }

        return $tagihan->dapatDiunggahBukti()
            || $tagihan->menungguVerifikasiBukti()
            || $tagihan->buktiDitolak();
    }

    /**
     * Musyrifah menerima bukti transfer. Dana belum dinyatakan Lunas.
     */
    public function terimaBukti(Pembayaran $tagihan, User $petugas, ?string $catatan = null): Pembayaran
    {
        return DB::transaction(function () use ($tagihan, $petugas, $catatan): Pembayaran {
            $tagihan = Pembayaran::query()->whereKey($tagihan->id)->lockForUpdate()->firstOrFail();

            if ($tagihan->sudahLunas() || $tagihan->menungguKonfirmasiRektorat()) {
                throw ValidationException::withMessages([
                    'status' => 'Tagihan ini sudah Lunas atau buktinya sudah diterima.',
                ]);
            }

            $grup = $this->grupTagihan($tagihan, true)->reject(fn (Pembayaran $item): bool => $item->sudahLunas());
            if ($grup->isEmpty() || $grup->contains(fn (Pembayaran $item): bool => blank($item->bukti_pembayaran))) {
                throw ValidationException::withMessages([
                    'status' => 'Belum ada bukti transfer yang dapat diperiksa.',
                ]);
            }

            $grup->each->update([
                'status' => Pembayaran::STATUS_MENUNGGU_REKTORAT,
                'status_verifikasi' => Pembayaran::VERIFIKASI_DITERIMA,
                'catatan_verifikasi' => $catatan,
                'diverifikasi_oleh' => $petugas->id,
                'tanggal_verifikasi' => now(),
            ]);

            return $tagihan->fresh();
        });
    }

    /**
     * Musyrifah menolak bukti transfer beserta alasannya.
     */
    public function tolakBukti(Pembayaran $tagihan, User $petugas, string $catatan): Pembayaran
    {
        return DB::transaction(function () use ($tagihan, $petugas, $catatan): Pembayaran {
            $tagihan = Pembayaran::query()->whereKey($tagihan->id)->lockForUpdate()->firstOrFail();

            if ($tagihan->sudahLunas()) {
                throw ValidationException::withMessages([
                    'status' => 'Tagihan yang sudah Lunas tidak dapat ditolak buktinya.',
                ]);
            }

            if (blank($tagihan->bukti_pembayaran)) {
                throw ValidationException::withMessages([
                    'status' => 'Belum ada bukti transfer yang dapat ditolak.',
                ]);
            }

            $this->grupTagihan($tagihan, true)->reject(fn (Pembayaran $item): bool => $item->sudahLunas())->each->update([
                'status' => Pembayaran::STATUS_BELUM_LUNAS,
                'status_verifikasi' => Pembayaran::VERIFIKASI_DITOLAK,
                'catatan_verifikasi' => $catatan,
                'diverifikasi_oleh' => $petugas->id,
                'tanggal_verifikasi' => now(),
                'tanggal_bayar' => null,
                'tanggal_konfirmasi_rektorat' => null,
            ]);

            return $tagihan->fresh();
        });
    }

    /**
     * Tetapkan Lunas setelah Rektorat mengonfirmasi dana benar-benar diterima.
     */
    public function konfirmasiLunas(Pembayaran $tagihan, User $petugas, ?string $catatan = null): Pembayaran
    {
        return DB::transaction(function () use ($tagihan, $catatan): Pembayaran {
            $tagihan = Pembayaran::query()->whereKey($tagihan->id)->lockForUpdate()->firstOrFail();

            if ($tagihan->sudahLunas()) {
                throw ValidationException::withMessages([
                    'status' => 'Tagihan ini sudah berstatus Lunas.',
                ]);
            }

            $grup = $this->grupTagihan($tagihan, true)->reject(fn (Pembayaran $item): bool => $item->sudahLunas());

            if ($grup->contains(fn (Pembayaran $item): bool => $item->menungguVerifikasiBukti())) {
                throw ValidationException::withMessages([
                    'status' => 'Periksa dan terima bukti transfer terlebih dahulu sebelum menetapkan Lunas.',
                ]);
            }

            if ($grup->contains(fn (Pembayaran $item): bool => $item->buktiDitolak())) {
                throw ValidationException::withMessages([
                    'status' => 'Bukti transfer sudah ditolak. Minta mahasiswa mengunggah bukti yang benar.',
                ]);
            }

            $grup->each(function (Pembayaran $item) use ($catatan): void {
                $item->update([
                    'status' => Pembayaran::STATUS_LUNAS,
                    'status_verifikasi' => $item->status_verifikasi ?: Pembayaran::VERIFIKASI_DITERIMA,
                    'catatan_verifikasi' => filled($catatan) ? $catatan : $item->catatan_verifikasi,
                    'tanggal_bayar' => $item->tanggal_bayar ?? now(),
                    'tanggal_konfirmasi_rektorat' => now(),
                    'metode_pembayaran' => 'Transfer '.PengaturanPembayaran::REKENING_BANK.' (konfirmasi Rektorat)',
                ]);
            });

            return $tagihan->fresh();
        });
    }

    /**
     * Kembalikan tagihan menjadi Belum Lunas bila konfirmasi dibatalkan.
     */
    public function tandaiBelumLunas(Pembayaran $tagihan, User $petugas, ?string $catatan = null): Pembayaran
    {
        return DB::transaction(function () use ($tagihan, $catatan): Pembayaran {
            $tagihan = Pembayaran::query()->whereKey($tagihan->id)->lockForUpdate()->firstOrFail();

            $this->grupTagihan($tagihan, true)->each(function (Pembayaran $item) use ($catatan): void {
                $item->update([
                    'status' => Pembayaran::STATUS_BELUM_LUNAS,
                    'tanggal_bayar' => null,
                    'tanggal_konfirmasi_rektorat' => null,
                    'catatan_verifikasi' => filled($catatan) ? $catatan : $item->catatan_verifikasi,
                ]);
            });

            return $tagihan->fresh();
        });
    }

    /**
     * Sesuaikan nominal satu tagihan untuk kategori mahasiswa tertentu.
     */
    public function perbaruiNominal(Pembayaran $tagihan, int $nominal, User $petugas): Pembayaran
    {
        return DB::transaction(function () use ($tagihan, $nominal): Pembayaran {
            $tagihan = Pembayaran::query()->whereKey($tagihan->id)->lockForUpdate()->firstOrFail();

            if ($tagihan->sudahLunas()) {
                throw ValidationException::withMessages([
                    'nominal' => 'Nominal tagihan yang sudah Lunas tidak dapat diubah.',
                ]);
            }

            $grup = $this->grupTagihan($tagihan, true);
            if ($grup->contains(fn (Pembayaran $item): bool => $item->menungguVerifikasiBukti()
                || $item->menungguKonfirmasiRektorat()
                || $item->buktiDiterima()
                || (filled($item->bukti_pembayaran) && ! $item->buktiDitolak()))) {
                throw ValidationException::withMessages([
                    'nominal' => 'Nominal tidak dapat diubah selama bukti transfer sedang diperiksa atau menunggu konfirmasi Rektorat.',
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
        });
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
        $pengajuan = DB::transaction(function () use ($user, $tagihan, $alasan, $tanggalRencanaBayar): PengajuanKeterlambatanPembayaran {
            $tagihan = Pembayaran::query()->whereKey($tagihan->getKey())->lockForUpdate()->firstOrFail();

            if ($tagihan->user_id !== $user->id) {
                throw ValidationException::withMessages([
                    'alasan' => 'Tagihan ini bukan milik Anda.',
                ]);
            }

            $this->pastikanPeriodeAktif($tagihan);

            if ($tagihan->sudahLunas()) {
                throw ValidationException::withMessages([
                    'alasan' => 'Tagihan ini sudah Lunas sehingga tidak perlu diajukan keterlambatan.',
                ]);
            }

            if ($tagihan->pengajuanKeterlambatan()->menunggu()->exists()
                || $tagihan->pengajuanKeterlambatan()->menungguDokumen()->exists()) {
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
                'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU_DOKUMEN,
                'dokumen_wajib' => true,
            ]);

            $pengajuan->riwayats()->create([
                'status_sebelum' => null,
                'status_sesudah' => $pengajuan->status,
                'catatan' => 'Pengajuan dibuat. Menunggu mahasiswa mengunggah surat yang telah ditandatangani dan dibubuhkan materai.',
                'diubah_oleh' => $user->id,
            ]);

            return $pengajuan;
        });

        $this->kirimNotifikasiMusyrifah(
            'Pengajuan keterlambatan '.$tagihan->labelTahap(),
            $user->name.' mengajukan keterlambatan pembayaran '.$tagihan->labelTahap().
                ' dengan rencana bayar '.$pengajuan->tanggalRencanaTeks().'.',
            'keterlambatan:'.$pengajuan->id,
            $user
        );

        return $pengajuan;
    }

    /**
     * Simpan scan/foto surat sebagai versi baru pada pengajuan yang sama.
     */
    public function unggahDokumenKeterlambatan(
        User $user,
        PengajuanKeterlambatanPembayaran $pengajuan,
        UploadedFile $berkas
    ): PengajuanKeterlambatanPembayaran {
        if ($pengajuan->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'dokumen' => 'Pengajuan ini bukan milik Anda.',
            ]);
        }

        $path = null;

        try {
            $hasil = DB::transaction(function () use ($user, $pengajuan, $berkas, &$path): PengajuanKeterlambatanPembayaran {
                $pengajuan = PengajuanKeterlambatanPembayaran::query()
                    ->whereKey($pengajuan->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $pengajuan->bisaUnggahDokumen()) {
                    throw ValidationException::withMessages([
                        'dokumen' => 'Dokumen hanya dapat diunggah saat pengajuan menunggu unggahan atau setelah penolakan Musyrifah.',
                    ]);
                }

                $versi = (int) $pengajuan->dokumens()->max('versi') + 1;
                $path = $berkas->store('keterlambatan/dokumen/'.$pengajuan->id, 'local');

                if (! is_string($path) || $path === '') {
                    throw ValidationException::withMessages([
                        'dokumen' => 'Gagal menyimpan dokumen surat. Silakan coba kembali.',
                    ]);
                }

                $dokumen = $pengajuan->dokumens()->create([
                    'diunggah_oleh' => $user->id,
                    'path' => $path,
                    'nama_asli' => Str::limit(basename($berkas->getClientOriginalName()), 250, ''),
                    'mime_type' => $berkas->getMimeType() ?: 'application/octet-stream',
                    'ukuran' => (int) $berkas->getSize(),
                    'versi' => $versi,
                ]);

                $statusSebelum = $pengajuan->status;
                $pengajuan->update([
                    'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
                    'dokumen_wajib' => true,
                    'catatan_musyrifah' => null,
                    'diproses_oleh' => null,
                    'diproses_pada' => null,
                ]);

                $pengajuan->riwayats()->create([
                    'dokumen_id' => $dokumen->id,
                    'status_sebelum' => $statusSebelum,
                    'status_sesudah' => $pengajuan->status,
                    'catatan' => 'Mahasiswa mengunggah dokumen surat versi '.$versi.'.',
                    'diubah_oleh' => $user->id,
                ]);

                return $pengajuan->fresh();
            });
        } catch (Throwable $exception) {
            if (filled($path)) {
                Storage::disk('local')->delete($path);
            }

            throw $exception;
        }

        $this->kirimNotifikasiMusyrifah(
            'Dokumen surat keterlambatan diunggah',
            $user->name.' mengunggah dokumen surat untuk pengajuan '.$hasil->nomor_surat.'.',
            'keterlambatan:dokumen:'.$hasil->id,
            $user
        );

        return $hasil;
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
        $catatan = filled($catatan) ? trim((string) $catatan) : null;

        $hasil = DB::transaction(function () use ($pengajuan, $petugas, $setuju, $catatan): PengajuanKeterlambatanPembayaran {
            $pengajuan = PengajuanKeterlambatanPembayaran::query()
                ->whereKey($pengajuan->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($pengajuan->menungguDokumen()) {
                throw ValidationException::withMessages([
                    'keputusan' => 'Dokumen surat yang ditandatangani dan dibubuhkan materai belum diunggah mahasiswa.',
                ]);
            }

            if (! $pengajuan->masihMenunggu()) {
                throw ValidationException::withMessages([
                    'keputusan' => 'Pengajuan ini sudah diproses sebelumnya.',
                ]);
            }

            if (! $setuju && ($catatan === null || mb_strlen($catatan) < 3)) {
                throw ValidationException::withMessages([
                    'catatan' => 'Alasan penolakan wajib diisi minimal 3 karakter.',
                ]);
            }

            if (! $pengajuan->siapDiputuskan()) {
                throw ValidationException::withMessages([
                    'keputusan' => 'Dokumen surat yang ditandatangani dan dibubuhkan materai belum diunggah mahasiswa.',
                ]);
            }

            $statusSebelum = $pengajuan->status;
            $pengajuan->update([
                'status' => $setuju
                    ? PengajuanKeterlambatanPembayaran::STATUS_DISETUJUI
                    : PengajuanKeterlambatanPembayaran::STATUS_DITOLAK,
                'catatan_musyrifah' => $catatan,
                'diproses_oleh' => $petugas->id,
                'diproses_pada' => now(),
            ]);

            $pengajuan->riwayats()->create([
                'dokumen_id' => $pengajuan->dokumenTerakhir?->id,
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => $pengajuan->status,
                'catatan' => $catatan ?? 'Pengajuan disetujui Musyrifah.',
                'diubah_oleh' => $petugas->id,
            ]);

            $tagihan = $pengajuan->pembayaran;

            // Persetujuan hanya memperbarui jadwal dan pengingat, bukan status Lunas.
            if ($setuju && $tagihan) {
                $tagihan->update([
                    'jatuh_tempo' => $pengajuan->tanggal_rencana_bayar?->toDateString(),
                    'reminder_terakhir_pada' => null,
                ]);
            }

            return $pengajuan->fresh();
        });

        $tagihan = $hasil->pembayaran;

        Notifikasi::kirim(
            $hasil->user_id,
            Notifikasi::TIPE_PEMBAYARAN,
            $setuju ? 'Pengajuan keterlambatan disetujui' : 'Pengajuan keterlambatan ditolak',
            $setuju
                ? 'Jadwal pembayaran '.($tagihan?->labelTahap() ?? 'tagihan').' diperbarui menjadi '.
                    $hasil->tanggalRencanaTeks().'. Status tagihan belum Lunas sampai dana dikonfirmasi Rektorat.'
                : 'Pengajuan keterlambatan '.($tagihan?->labelTahap() ?? 'tagihan').' ditolak. '.
                    ($catatan ?? ''),
            route('pembayaran'),
            'keterlambatan:'.$hasil->id
        );

        return $hasil;
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
            ->with(['user', 'jadwalPembayaran'])
            ->chunkById(200, function ($daftar) use (&$dikirim, &$diperiksa, $hariIni, $periodeTerbuka) {
                foreach ($daftar as $tagihan) {
                    $diperiksa++;

                    if ($periodeTerbuka === null
                        || (filled($tagihan->periode) && $tagihan->periode !== $periodeTerbuka)) {
                        continue;
                    }

                    if (! $tagihan->user
                        || ! $tagihan->jadwalAngsuran()?->sudahDibuka()
                        || $tagihan->menungguVerifikasiBukti()
                        || $tagihan->menungguKonfirmasiRektorat()) {
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

    private function kirimNotifikasiMusyrifah(string $judul, string $pesan, string $kunci, User $mahasiswa): void
    {
        $lantai = $this->lantaiMahasiswa($mahasiswa);
        if ($lantai === null) {
            return;
        }

        User::query()
            ->whereRaw('LOWER(role) = ?', ['musyrifah'])
            ->where(function (Builder $query) use ($lantai): void {
                $query->whereHas('pegawai', fn (Builder $pegawai) => $pegawai->where('lantai_yang_dikelola', $lantai))
                    ->orWhere(function (Builder $query) use ($lantai): void {
                        $query->whereDoesntHave('pegawai', fn (Builder $pegawai) => $pegawai->whereNotNull('lantai_yang_dikelola'))
                            ->where('lantai_penempatan', $lantai);
                    });
            })
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

    private function peran(User $user): string
    {
        return strtolower(trim((string) $user->role));
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
