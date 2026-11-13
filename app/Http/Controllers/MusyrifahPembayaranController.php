<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AngsuranPembayaran;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\PengajuanKeterlambatanPembayaran;
use App\Models\PengaturanPembayaran;
use App\Services\AuditTrailService;
use App\Services\PembayaranAngsuranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MusyrifahPembayaranController extends Controller
{
    public function __construct(private readonly PembayaranAngsuranService $layanan) {}

    /**
     * Halaman pembayaran mahasiswa untuk Musyrifah.
     */
    public function index(Request $request): View
    {
        $this->pastikanPengelola($request);
        $this->layanan->sinkronkanSemuaMahasiswa();

        $periodeKerja = $this->layanan->periodeKerja();

        return view('musyrifah.pembayaran', array_merge([
            'daftarPembayaran' => $this->ambilDataPembayaran(),
            'daftarPeriode' => $this->ambilPeriode(),
            'jadwal' => $this->ambilJadwal($periodeKerja),
            'pengajuan' => $this->ambilPengajuan(),
            'pengaturan' => PengaturanPembayaran::aktif(),
            'periodeKerja' => $periodeKerja,
            'rekening' => [
                'bank' => PengaturanPembayaran::REKENING_BANK,
                'nomor' => PengaturanPembayaran::REKENING_NOMOR_RESMI,
                'nama' => PengaturanPembayaran::REKENING_NAMA_RESMI,
            ],
        ], $this->hitungStatistik()));
    }

    /**
     * Data pembayaran terbaru untuk sinkronisasi halaman tanpa muat ulang.
     */
    public function apiData(Request $request): JsonResponse
    {
        $this->pastikanPengelola($request);
        $this->layanan->sinkronkanSemuaMahasiswa();

        return response()->json([
            'success' => true,
            'data' => $this->ambilDataPembayaran(),
            'summary' => $this->hitungStatistik(),
            'last_sync' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Detail satu tagihan beserta riwayat aktivitas mahasiswa.
     */
    public function detail(Request $request, int $id): JsonResponse
    {
        $this->pastikanPengelola($request);

        $pembayaran = Pembayaran::with(['user', 'verifikator', 'pengajuanKeterlambatan'])->findOrFail($id);
        $user = $pembayaran->user;

        return response()->json([
            'success' => true,
            'pembayaran' => $this->petakanTagihan($pembayaran) + [
                'kode_transaksi' => $pembayaran->kode_transaksi ?? ('TRX-'.$pembayaran->id),
                'nama_tagihan' => $pembayaran->nama_tagihan,
                'metode_pembayaran' => $pembayaran->metode_pembayaran ?? 'Belum ada',
                'keterangan' => $pembayaran->keterangan ?? '-',
                'diverifikasi_oleh' => $pembayaran->verifikator?->name ?? '-',
            ],
            'mahasiswa' => [
                'id' => $user?->id,
                'name' => $user?->name ?? 'Mahasiswa',
                'nim' => $user?->nim ?? '-',
                'email' => $user?->email ?? '-',
                'no_hp' => $user?->no_hp ?? '-',
                'status' => $user?->status ?? 'aktif',
            ],
            'riwayat_aktivitas' => $this->riwayatAktivitas($pembayaran->user_id),
            'pengajuan' => $pembayaran->pengajuanKeterlambatan
                ->sortByDesc('id')
                ->values()
                ->map(fn (PengajuanKeterlambatanPembayaran $pengajuan): array => $this->petakanPengajuan($pengajuan))
                ->all(),
            'riwayat_angsuran' => $user
                ? Pembayaran::query()
                    ->where('user_id', $user->id)
                    ->where('periode', $pembayaran->periode)
                    ->orderBy('angsuran_ke')
                    ->get()
                    ->map(fn (Pembayaran $item): array => $this->petakanTagihan($item))
                    ->values()
                : [],
        ]);
    }

    /**
     * Tetapkan Lunas (setelah konfirmasi dana Rektorat) atau Belum Lunas.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $this->pastikanPengelola($request);

        $validated = $request->validate([
            'status' => ['required', Rule::in([Pembayaran::STATUS_LUNAS, Pembayaran::STATUS_BELUM_LUNAS])],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $pembayaran = Pembayaran::with('user')->findOrFail($id);
        $petugas = $request->user();
        $statusSebelum = $pembayaran->status;

        try {
            $pembayaran = $validated['status'] === Pembayaran::STATUS_LUNAS
                ? $this->layanan->konfirmasiLunas($pembayaran, $petugas, $validated['catatan'] ?? null)
                : $this->layanan->tandaiBelumLunas($pembayaran, $petugas, $validated['catatan'] ?? null);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first(),
            ], 422);
        }

        $mahasiswa = $pembayaran->user;
        $statusLabel = $pembayaran->status_label;
        $catatan = trim((string) ($validated['catatan'] ?? ''));

        ActivityLog::create([
            'user_id' => $mahasiswa?->id,
            'user_name' => $mahasiswa?->name ?? 'Mahasiswa',
            'user_email' => $mahasiswa?->email,
            'user_role' => 'mahasiswa',
            'activity' => 'Verifikasi Pembayaran',
            'description' => 'Musyrifah '.($petugas?->name ?? 'Musyrifah').' menetapkan status pembayaran '.
                $pembayaran->labelTahap().' ('.$mahasiswa?->name.' - '.$mahasiswa?->nim.') menjadi '.$statusLabel.
                ($catatan !== '' ? '. Catatan: '.$catatan : '.'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        app(AuditTrailService::class)->catat(
            'pembayaran',
            'status_'.$validated['status'],
            'Pembayaran '.$pembayaran->labelTahap().' ('.$mahasiswa?->name.')',
            (int) $pembayaran->getKey(),
            'Musyrifah menetapkan status pembayaran menjadi '.$statusLabel.'.',
            $petugas,
            ['status' => $statusSebelum],
            ['status' => $validated['status'], 'catatan' => $catatan ?: null],
        );

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran '.$mahasiswa?->name.' berhasil diperbarui menjadi '.$statusLabel.'.',
            'pembayaran' => $this->petakanTagihan($pembayaran->fresh()),
        ]);
    }

    /**
     * Terima atau tolak bukti transfer yang diunggah mahasiswa.
     */
    public function verifikasiBukti(Request $request, int $id): JsonResponse
    {
        $this->pastikanPengelola($request);

        $validated = $request->validate([
            'keputusan' => ['required', Rule::in(['terima', 'tolak'])],
            'catatan' => ['required_if:keputusan,tolak', 'nullable', 'string', 'max:500'],
        ], [
            'catatan.required_if' => 'Alasan penolakan bukti transfer wajib diisi.',
        ]);

        $pembayaran = Pembayaran::with('user')->findOrFail($id);
        $petugas = $request->user();

        try {
            $pembayaran = $validated['keputusan'] === 'terima'
                ? $this->layanan->terimaBukti($pembayaran, $petugas, $validated['catatan'] ?? null)
                : $this->layanan->tolakBukti($pembayaran, $petugas, (string) $validated['catatan']);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first(),
            ], 422);
        }

        $mahasiswa = $pembayaran->user;
        $diterima = $validated['keputusan'] === 'terima';

        Notifikasi::kirim(
            $pembayaran->user_id,
            Notifikasi::TIPE_PEMBAYARAN,
            $diterima ? 'Bukti transfer diterima' : 'Bukti transfer ditolak',
            $diterima
                ? 'Bukti transfer '.$pembayaran->labelTahap().' sudah diterima Musyrifah. Status tagihan menunggu konfirmasi penerimaan dana dari Rektorat.'
                : 'Bukti transfer '.$pembayaran->labelTahap().' ditolak. Alasan: '.trim((string) ($validated['catatan'] ?? '')).' Silakan unggah bukti yang benar.',
            route('pembayaran'),
            'pembayaran:'.$pembayaran->id.':verifikasi'
        );

        ActivityLog::create([
            'user_id' => $mahasiswa?->id,
            'user_name' => $mahasiswa?->name ?? 'Mahasiswa',
            'user_email' => $mahasiswa?->email,
            'user_role' => 'mahasiswa',
            'activity' => 'Verifikasi Bukti Pembayaran',
            'description' => 'Musyrifah '.($petugas?->name ?? 'Musyrifah').' '.($diterima ? 'menerima' : 'menolak').
                ' bukti transfer '.$pembayaran->labelTahap().' ('.$mahasiswa?->name.' - '.$mahasiswa?->nim.').'.
                (filled($validated['catatan'] ?? null) ? ' Catatan: '.$validated['catatan'] : ''),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $diterima
                ? 'Bukti transfer diterima. Tagihan masih menunggu konfirmasi dana dari Rektorat sebelum Lunas.'
                : 'Bukti transfer ditolak. Mahasiswa dapat mengunggah ulang bukti.',
            'pembayaran' => $this->petakanTagihan($pembayaran->fresh()),
        ]);
    }

    /**
     * Sesuaikan nominal tagihan untuk kategori mahasiswa tertentu.
     */
    public function perbaruiNominal(Request $request, int $id): JsonResponse
    {
        $this->pastikanPengelola($request);

        $validated = $request->validate([
            'nominal' => ['required', 'numeric', 'min:1'],
        ], [
            'nominal.required' => 'Nominal tagihan wajib diisi.',
            'nominal.numeric' => 'Nominal tagihan harus berupa angka.',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);

        try {
            $pembayaran = $this->layanan->perbaruiNominal($pembayaran, (float) $validated['nominal'], $request->user());
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Nominal '.$pembayaran->labelTahap().' diperbarui menjadi '.$pembayaran->nominal_rupiah.'.',
            'pembayaran' => $this->petakanTagihan($pembayaran),
        ]);
    }

    /**
     * Atur nominal, jatuh tempo, dan jadwal pengingat tiap angsuran.
     */
    public function simpanJadwal(Request $request): RedirectResponse|JsonResponse
    {
        $this->pastikanMusyrifah($request);

        $validated = $request->validate([
            'pengaturan_id' => ['nullable', 'integer', Rule::exists('pengaturan_pembayarans', 'id')],
            'tanggal_mulai' => ['nullable', 'date'],
            'angsuran' => ['required', 'array', 'min:1', 'max:3'],
            'angsuran.*.angsuran_ke' => ['required', 'integer', Rule::in([1, 2, 3])],
            'angsuran.*.nama_tahap' => ['nullable', 'string', 'max:80'],
            'angsuran.*.nominal' => ['required', 'numeric', 'min:1'],
            'angsuran.*.jatuh_tempo' => ['required', 'date'],
            'angsuran.*.reminder_hari' => ['required', 'integer', 'min:0', 'max:30'],
        ], [
            'angsuran.required' => 'Jadwal angsuran wajib diisi.',
            'angsuran.*.nominal.required' => 'Nominal angsuran wajib diisi.',
            'angsuran.*.jatuh_tempo.required' => 'Jatuh tempo angsuran wajib diisi.',
            'angsuran.*.reminder_hari.required' => 'Jumlah hari pengingat wajib diisi.',
        ]);

        try {
            $pengaturan = filled($validated['pengaturan_id'] ?? null)
                ? PengaturanPembayaran::find((int) $validated['pengaturan_id'])
                : null;

            $this->layanan->simpanJadwal(
                $validated['angsuran'],
                $pengaturan,
                $validated['tanggal_mulai'] ?? null
            );
        } catch (ValidationException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($exception->errors())->flatten()->first(),
                ], 422);
            }

            return back()->withErrors($exception->errors())->withInput();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal angsuran berhasil diperbarui. Tagihan yang belum dibayar mengikuti jadwal baru.',
                'jadwal' => $this->ambilJadwal(),
            ]);
        }

        return back()->with('success', 'Jadwal angsuran berhasil diperbarui. Tagihan yang belum dibayar mengikuti jadwal baru.');
    }

    /**
     * Buat periode pembayaran baru. Kewenangan ini hanya milik Musyrifah.
     */
    public function simpanPeriode(Request $request): RedirectResponse
    {
        $this->pastikanMusyrifah($request);

        $validated = $request->validate([
            'periode' => ['required', 'string', 'max:60', Rule::unique('pengaturan_pembayarans', 'periode')],
            'tanggal_mulai' => ['required', 'date'],
            'nama_tahap' => ['nullable', 'string', 'max:80'],
        ], [
            'periode.required' => 'Nama periode wajib diisi.',
            'periode.unique' => 'Periode dengan nama ini sudah ada.',
            'tanggal_mulai.required' => 'Tanggal mulai periode wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai periode tidak valid.',
        ]);

        $periode = $this->layanan->buatPeriode($validated);

        $this->catatAktivitasPeriode(
            $request,
            'Membuat Periode Pembayaran',
            'Musyrifah membuat periode pembayaran '.$periode->periode.' mulai '.$periode->periodeDibukaTeks().'.'
        );

        return back()->with('success', 'Periode pembayaran '.$periode->periode.' dibuat dan menjadi periode aktif. '.
            'Atur nominal angsuran, jatuh tempo, dan jadwal pengingat pada panel Jadwal Angsuran.');
    }

    /**
     * Buka periode pembayaran agar mahasiswa dapat melihat dan membayar tagihan.
     */
    public function bukaPeriode(Request $request, PengaturanPembayaran $periode): RedirectResponse
    {
        $this->pastikanMusyrifah($request);

        $periode = $this->layanan->bukaPeriode($periode);
        $this->layanan->sinkronkanSemuaMahasiswa();

        $this->catatAktivitasPeriode(
            $request,
            'Membuka Periode Pembayaran',
            'Musyrifah membuka periode pembayaran '.$periode->periode.' untuk mahasiswa.'
        );

        return back()->with('success', 'Periode pembayaran '.$periode->periode.' dibuka. '.
            'Mahasiswa dapat melihat tagihan dan mengunggah bukti transfer.');
    }

    /**
     * Tutup periode pembayaran tanpa menghapus tagihan yang sudah tercatat.
     */
    public function tutupPeriode(Request $request, PengaturanPembayaran $periode): RedirectResponse
    {
        $this->pastikanMusyrifah($request);

        $periode = $this->layanan->tutupPeriode($periode);

        $this->catatAktivitasPeriode(
            $request,
            'Menutup Periode Pembayaran',
            'Musyrifah menutup periode pembayaran '.$periode->periode.'. Data tagihan tetap tersimpan.'
        );

        return back()->with('success', 'Periode pembayaran '.$periode->periode.' ditutup. '.
            'Mahasiswa tidak dapat melihat tagihan sampai periode dibuka kembali.');
    }

    /**
     * Setujui atau tolak pengajuan keterlambatan mahasiswa.
     */
    public function putuskanKeterlambatan(Request $request, int $id): JsonResponse
    {
        $this->pastikanPengelola($request);

        $validated = $request->validate([
            'keputusan' => ['required', Rule::in(['setuju', 'tolak'])],
            'catatan' => ['required_if:keputusan,tolak', 'nullable', 'string', 'max:500'],
        ], [
            'catatan.required_if' => 'Alasan penolakan pengajuan wajib diisi.',
        ]);

        $pengajuan = PengajuanKeterlambatanPembayaran::with('pembayaran')->findOrFail($id);
        $petugas = $request->user();

        try {
            $pengajuan = $this->layanan->putuskanKeterlambatan(
                $pengajuan,
                $petugas,
                $validated['keputusan'] === 'setuju',
                $validated['catatan'] ?? null
            );
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first(),
            ], 422);
        }

        ActivityLog::create([
            'user_id' => $pengajuan->user_id,
            'user_name' => $pengajuan->user?->name ?? 'Mahasiswa',
            'user_email' => $pengajuan->user?->email,
            'user_role' => 'mahasiswa',
            'activity' => 'Keputusan Pengajuan Keterlambatan',
            'description' => 'Musyrifah '.($petugas?->name ?? 'Musyrifah').' '.
                ($pengajuan->disetujui() ? 'menyetujui' : 'menolak').' pengajuan keterlambatan '.
                $pengajuan->nomor_surat.'.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $pengajuan->disetujui()
                ? 'Pengajuan disetujui. Jatuh tempo diperbarui ke '.$pengajuan->tanggalRencanaTeks().' dan status tagihan tetap belum Lunas.'
                : 'Pengajuan keterlambatan ditolak.',
            'pengajuan' => [
                'id' => $pengajuan->id,
                'status' => $pengajuan->status,
                'status_label' => $pengajuan->statusLabel(),
                'kelas_status' => $pengajuan->kelasStatus(),
                'catatan_musyrifah' => $pengajuan->catatan_musyrifah,
            ],
        ]);
    }

    /**
     * Daftar tagihan seluruh mahasiswa untuk tabel Musyrifah.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function ambilDataPembayaran(): array
    {
        return Pembayaran::query()
            ->with(['user', 'pengajuanKeterlambatan' => fn ($query) => $query->latest('id')])
            ->orderByRaw("CASE
                WHEN LOWER(status) = 'menunggu' THEN 1
                WHEN LOWER(status) = 'menunggu_konfirmasi' THEN 2
                WHEN LOWER(status) IN ('lunas', 'paid', 'terkonfirmasi') THEN 4
                ELSE 3 END")
            ->orderByRaw('COALESCE(jatuh_tempo, created_at)')
            ->latest('updated_at')
            ->get()
            ->map(fn (Pembayaran $pembayaran): array => $this->petakanTagihan($pembayaran))
            ->values()
            ->all();
    }

    /**
     * Jadwal angsuran periode aktif untuk formulir pengaturan.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function ambilJadwal(?PengaturanPembayaran $pengaturan = null): array
    {
        return $this->layanan->jadwalAktif($pengaturan)
            ->map(fn (AngsuranPembayaran $jadwal): array => [
                'angsuran_ke' => $jadwal->angsuran_ke,
                'nama_tahap' => $jadwal->nama_tahap,
                'nominal' => (float) $jadwal->nominal,
                'nominal_rupiah' => $jadwal->nominalRupiah(),
                'jatuh_tempo' => $jadwal->jatuh_tempo?->toDateString(),
                'jatuh_tempo_teks' => $jadwal->jatuhTempoTeks(),
                'reminder_hari' => $jadwal->reminder_hari,
            ])
            ->values()
            ->all();
    }

    /**
     * Daftar periode pembayaran beserta status buka/tutupnya.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function ambilPeriode(): array
    {
        return $this->layanan->daftarPeriode()
            ->map(function (PengaturanPembayaran $periode): array {
                $status = $this->statusPeriode($periode);

                return [
                    'id' => $periode->id,
                    'periode' => $periode->periode,
                    'nama_tahap' => $periode->nama_tahap,
                    'tanggal_mulai' => $periode->periode_dibuka_pada?->toDateString(),
                    'tanggal_mulai_teks' => $periode->periodeDibukaTeks(),
                    'jatuh_tempo_teks' => $periode->jatuhTempoTeks(),
                    'status' => $status,
                    'status_label' => match ($status) {
                        'dibuka' => 'Dibuka',
                        'terjadwal' => 'Terjadwal',
                        default => 'Ditutup',
                    },
                    'kelas_status' => $status,
                    'aktif' => (bool) $periode->is_aktif,
                    'dibuka' => $periode->sudahDibuka(),
                    'jumlah_tagihan' => Pembayaran::query()->where('periode', $periode->periode)->count(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Status periode: dibuka, terjadwal, atau ditutup.
     */
    private function statusPeriode(PengaturanPembayaran $periode): string
    {
        if (! $periode->is_aktif) {
            return 'ditutup';
        }

        return $periode->sudahDibuka() ? 'dibuka' : 'terjadwal';
    }

    /**
     * Pengajuan keterlambatan yang perlu diperiksa Musyrifah.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function ambilPengajuan(): array
    {
        return PengajuanKeterlambatanPembayaran::query()
            ->with(['user', 'pembayaran', 'pemroses'])
            ->latest('id')
            ->get()
            ->map(fn (PengajuanKeterlambatanPembayaran $pengajuan): array => $this->petakanPengajuan($pengajuan))
            ->values()
            ->all();
    }

    /**
     * Peta satu pengajuan keterlambatan untuk JSON dan tabel.
     *
     * @return array<string, mixed>
     */
    protected function petakanPengajuan(PengajuanKeterlambatanPembayaran $pengajuan): array
    {
        return [
            'id' => $pengajuan->id,
            'nomor_surat' => $pengajuan->nomor_surat,
            'nama' => $pengajuan->user?->name ?? 'Mahasiswa',
            'nim' => $pengajuan->user?->nim ?? '-',
            'tahap' => $pengajuan->pembayaran?->labelTahap() ?? '-',
            'nominal_rupiah' => $pengajuan->pembayaran?->nominal_rupiah ?? '-',
            'alasan' => $pengajuan->alasan,
            'tanggal_rencana' => $pengajuan->tanggalRencanaTeks(),
            'tanggal_rencana_iso' => $pengajuan->tanggal_rencana_bayar?->toDateString(),
            'status' => $pengajuan->status,
            'status_label' => $pengajuan->statusLabel(),
            'kelas_status' => $pengajuan->kelasStatus(),
            'catatan_musyrifah' => $pengajuan->catatan_musyrifah,
            'pemroses' => $pengajuan->pemroses?->name,
            'diproses_pada' => $pengajuan->diproses_pada?->locale('id')->translatedFormat('d F Y, H:i'),
            'surat_url' => route('pembayaran.keterlambatan.surat', $pengajuan->id),
            'menunggu' => $pengajuan->masihMenunggu(),
        ];
    }

    /**
     * Statistik ringkas halaman pembayaran Musyrifah.
     *
     * @return array<string, mixed>
     */
    protected function hitungStatistik(): array
    {
        $semua = Pembayaran::query()->get();
        $lunas = $semua->filter(fn (Pembayaran $p): bool => $p->sudahLunas());
        $menungguVerifikasi = $semua->filter(fn (Pembayaran $p): bool => $p->menungguVerifikasiBukti());
        $menungguRektorat = $semua->filter(fn (Pembayaran $p): bool => $p->menungguKonfirmasiRektorat());
        $belumLunas = $semua->reject(fn (Pembayaran $p): bool => $p->sudahLunas()
            || $p->menungguVerifikasiBukti()
            || $p->menungguKonfirmasiRektorat());

        $terkumpul = (float) $lunas->sum(fn (Pembayaran $p): float => (float) $p->nominal);
        $totalTagihan = (float) $semua->sum(fn (Pembayaran $p): float => (float) $p->nominal);

        return [
            'totalMahasiswa' => $semua->pluck('user_id')->unique()->count(),
            'totalTagihan' => $semua->count(),
            'totalLunas' => $lunas->count(),
            'totalMenunggu' => $menungguVerifikasi->count() + $menungguRektorat->count(),
            'totalMenungguVerifikasi' => $menungguVerifikasi->count(),
            'totalMenungguRektorat' => $menungguRektorat->count(),
            'totalBelumLunas' => $belumLunas->count(),
            'totalPengajuan' => PengajuanKeterlambatanPembayaran::query()->menunggu()->count(),
            'totalNominalTerkumpul' => $terkumpul,
            'totalNominalTerkumpulRupiah' => 'Rp '.number_format($terkumpul, 0, ',', '.'),
            'totalNominalTagihan' => $totalTagihan,
            'totalNominalTagihanRupiah' => 'Rp '.number_format($totalTagihan, 0, ',', '.'),
        ];
    }

    /**
     * Peta satu tagihan untuk JSON dan tabel.
     *
     * @return array<string, mixed>
     */
    protected function petakanTagihan(Pembayaran $pembayaran): array
    {
        $user = $pembayaran->user;
        $rincian = $this->layanan->rincianTagihan($pembayaran);

        return [
            'id' => $pembayaran->id,
            'nama' => $user?->name ?? 'Mahasiswa',
            'nim' => $user?->nim ?? '-',
            'email' => $user?->email ?? '-',
            'no_hp' => $user?->no_hp ?? '-',
            'periode' => $pembayaran->periode,
            'angsuran_ke' => (int) ($pembayaran->angsuran_ke ?? 1),
            'tahap' => $pembayaran->labelTahap(),
            'nominal' => (float) $pembayaran->nominal,
            'nominal_rupiah' => $pembayaran->nominal_rupiah,
            'jatuh_tempo' => $pembayaran->jatuhTempoTeks(),
            'jatuh_tempo_iso' => $pembayaran->jatuh_tempo?->toDateString(),
            'status' => $pembayaran->status_label,
            'status_label' => $pembayaran->status_label,
            'raw_status' => strtolower((string) $pembayaran->status),
            'kelas_status' => $rincian['kelas_status'],
            'verifikasi' => $rincian['verifikasi'],
            'verifikasi_label' => $rincian['verifikasi_label'],
            'kelas_verifikasi' => $rincian['kelas_verifikasi'],
            'catatan_verifikasi' => $pembayaran->catatan_verifikasi,
            'bukti_url' => $rincian['bukti_url'],
            'bukti_nama' => $rincian['bukti_nama'],
            'tanggal_upload' => $rincian['tanggal_upload'],
            'tanggal_konfirmasi' => $rincian['tanggal_konfirmasi'],
            'metode' => $pembayaran->metode_pembayaran ?? '-',
            'tanggal_bayar' => $pembayaran->tanggal_bayar?->format('d/m/Y H:i') ?? '-',
            'updated_at' => $pembayaran->updated_at?->format('d/m/Y H:i') ?? '-',
            'dapat_lunas' => $pembayaran->sudahLunas() === false
                && $pembayaran->menungguVerifikasiBukti() === false
                && $pembayaran->buktiDitolak() === false,
            'pengajuan_menunggu' => $rincian['pengajuan_menunggu'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function riwayatAktivitas(?int $userId): array
    {
        $logs = ActivityLog::query()
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->where('activity', 'like', '%pembayaran%')
                    ->orWhere('activity', 'like', '%tagihan%')
                    ->orWhere('description', 'like', '%pembayaran%')
                    ->orWhere('description', 'like', '%tagihan%');
            })
            ->latest('created_at')
            ->take(20)
            ->get();

        if ($logs->isEmpty()) {
            $logs = ActivityLog::query()
                ->where('user_id', $userId)
                ->latest('created_at')
                ->take(10)
                ->get();
        }

        return $logs->map(fn (ActivityLog $log): array => [
            'id' => $log->id,
            'activity' => $log->activity,
            'description' => $log->description,
            'time_formatted' => $log->created_at ? $log->created_at->format('d M Y, H:i') : '-',
            'time_diff' => $log->created_at ? $log->created_at->diffForHumans() : '-',
        ])->values()->all();
    }

    /**
     * Hanya Musyrifah yang boleh membuka, menutup, dan mengatur periode
     * pembayaran. Admin Sistem tidak memiliki kewenangan ini.
     */
    private function pastikanMusyrifah(Request $request): void
    {
        $role = strtolower(trim((string) $request->user()?->role));

        abort_unless($role === 'musyrifah', 403);
    }

    /**
     * Catat perubahan periode pembayaran pada log aktivitas.
     */
    private function catatAktivitasPeriode(Request $request, string $aktivitas, string $deskripsi): void
    {
        $petugas = $request->user();

        ActivityLog::create([
            'user_id' => $petugas?->id,
            'user_name' => $petugas?->name ?? 'Musyrifah',
            'user_email' => $petugas?->email,
            'user_role' => 'musyrifah',
            'activity' => $aktivitas,
            'description' => $deskripsi,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Hanya Musyrifah/Pengelola/Admin yang boleh mengelola pembayaran.
     */
    private function pastikanPengelola(Request $request): void
    {
        $role = strtolower(trim((string) $request->user()?->role));
        abort_unless(in_array($role, ['musyrifah', 'pengelola', 'admin'], true), 403);
    }
}
