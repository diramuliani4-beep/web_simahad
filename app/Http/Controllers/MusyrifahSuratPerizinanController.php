<?php

namespace App\Http\Controllers;

use App\Events\SuratPerizinanDicatat;
use App\Models\SuratPerizinan;
use App\Models\User;
use App\Services\AuditTrailService;
use App\Services\SuratPerizinanService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MusyrifahSuratPerizinanController extends Controller
{
    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly SuratPerizinanService $suratService,
    ) {}

    /**
     * Daftar pengajuan mahasiswa dengan filter jenis, status, nama, dan periode.
     */
    public function index(Request $request): View
    {
        $lokasi = $this->lokasiPengelola($request);

        $filter = $request->validate([
            'jenis' => ['nullable', Rule::in([
                SuratPerizinan::JENIS_KEGIATAN_FAKULTAS,
                SuratPerizinan::JENIS_KEGIATAN_KAMPUS,
                SuratPerizinan::JENIS_PULANG,
                SuratPerizinan::JENIS_DARURAT,
                SuratPerizinan::JENIS_SEMESTER,
            ])],
            'status' => ['nullable', Rule::in([
                SuratPerizinan::STATUS_MENUNGGU,
                SuratPerizinan::STATUS_DISETUJUI,
                SuratPerizinan::STATUS_DITOLAK,
                SuratPerizinan::STATUS_PERLU_PERBAIKAN,
                SuratPerizinan::STATUS_SELESAI,
                SuratPerizinan::STATUS_DIBATALKAN,
            ])],
            'nama' => ['nullable', 'string', 'max:100'],
            'periode_dari' => ['nullable', 'date'],
            'periode_sampai' => ['nullable', 'date', 'after_or_equal:periode_dari'],
        ], [
            'jenis.in' => 'Jenis izin tidak dikenali.',
            'status.in' => 'Status tidak dikenali.',
            'periode_sampai.after_or_equal' => 'Periode akhir tidak boleh sebelum periode awal.',
        ]);

        $pengajuans = $this->batasiWilayahSurat(SuratPerizinan::query(), $request, $lokasi)
            ->with('user')
            ->when(filled($filter['jenis'] ?? null), fn ($query) => $query->where('jenis', $filter['jenis']))
            ->when(filled($filter['status'] ?? null), fn ($query) => $query->where('status', $filter['status']))
            ->when(filled($filter['nama'] ?? null), function ($query) use ($filter): void {
                $nama = '%'.trim($filter['nama']).'%';

                // Kelompokkan pencarian OR agar tidak memperluas batas wilayah.
                $query->whereHas('user', fn (Builder $user) => $user
                    ->where(fn (Builder $pencarian) => $pencarian
                        ->where('name', 'like', $nama)
                        ->orWhere('nim', 'like', $nama)));
            })
            ->when(filled($filter['periode_dari'] ?? null), fn ($query) => $query->whereDate('tanggal_mulai', '>=', $filter['periode_dari']))
            ->when(filled($filter['periode_sampai'] ?? null), fn ($query) => $query->whereDate('tanggal_mulai', '<=', $filter['periode_sampai']))
            ->latest('id')
            ->get();

        $awalBulan = Carbon::now()->startOfMonth()->toDateString();
        $akhirBulan = Carbon::now()->endOfMonth()->toDateString();

        // Monitoring kuota izin pulang pada bulan berjalan.
        $kuotaMonitoring = $this->batasiWilayahSurat(SuratPerizinan::query(), $request, $lokasi)
            ->with('user')
            ->where('jenis', SuratPerizinan::JENIS_PULANG)
            ->whereIn('status', SuratPerizinan::statusMenggunakanKuota())
            ->whereBetween('tanggal_mulai', [$awalBulan, $akhirBulan])
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows): array => [
                'nama' => $rows->first()->user?->name ?? '-',
                'nim' => $rows->first()->user?->nim ?: '-',
                'terpakai' => $rows->count(),
                'sisa' => max(0, SuratPerizinan::KUOTA_PULANG_PER_BULAN - $rows->count()),
            ])
            ->sortByDesc('terpakai')
            ->values();

        return view('musyrifah.surat_perizinan', [
            'pengajuans' => $pengajuans,
            'kuotaMonitoring' => $kuotaMonitoring,
            'filter' => $filter,
            'bulanBerjalan' => Carbon::now()->locale('id')->translatedFormat('F Y'),
            'jumlahMenunggu' => $pengajuans->whereIn('status', [
                SuratPerizinan::STATUS_MENUNGGU,
                SuratPerizinan::STATUS_PERLU_PERBAIKAN,
            ])->count(),
            'jenisPilihan' => $this->jenisPilihan(),
            'statusPilihan' => $this->statusPilihan(),
        ]);
    }

    /**
     * Detail satu pengajuan beserta riwayat perubahan status.
     */
    public function show(Request $request, SuratPerizinan $suratPerizinan): View
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesSurat($request, $lokasi, $suratPerizinan);

        return view('musyrifah.detail_surat_perizinan', [
            'pengajuan' => $suratPerizinan->load(['user.kamar', 'musyrifah', 'riwayats', 'keputusanOleh']),
            'kuotaPulang' => $suratPerizinan->user
                ? SuratPerizinan::kuotaPulang($suratPerizinan->user, $suratPerizinan->tanggal_mulai)
                : null,
            'statusPilihan' => $this->statusPilihan(),
        ]);
    }

    /**
     * Menyetujui, menolak, atau meminta perbaikan surat semester.
     */
    public function keputusan(Request $request, SuratPerizinan $suratPerizinan): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesSurat($request, $lokasi, $suratPerizinan);

        if (! $suratPerizinan->masihMenunggu()) {
            return back()->with('error', 'Pengajuan ini sudah diputus dan tidak dapat diubah.');
        }

        $pilihanKeputusan = [
            SuratPerizinan::STATUS_DISETUJUI,
            SuratPerizinan::STATUS_DITOLAK,
        ];

        if ($suratPerizinan->suratSemester()) {
            $pilihanKeputusan[] = SuratPerizinan::STATUS_PERLU_PERBAIKAN;
        }

        $validated = $request->validate([
            'keputusan' => ['required', Rule::in($pilihanKeputusan)],
            'catatan_musyrifah' => [
                'nullable',
                'string',
                'max:2000',
                Rule::requiredIf(in_array($request->input('keputusan'), [
                    SuratPerizinan::STATUS_DITOLAK,
                    SuratPerizinan::STATUS_PERLU_PERBAIKAN,
                ], true)),
            ],
        ], [
            'keputusan.required' => 'Keputusan wajib dipilih.',
            'keputusan.in' => 'Keputusan tidak valid.',
            'catatan_musyrifah.required' => 'Catatan wajib diisi saat menolak atau meminta perbaikan.',
            'catatan_musyrifah.max' => 'Catatan keputusan maksimal 2000 karakter.',
        ]);

        if ($validated['keputusan'] === SuratPerizinan::STATUS_DISETUJUI) {
            if ($suratPerizinan->suratSemester() && (
                ! $suratPerizinan->punyaSuratTertanda()
                || ! Storage::disk('local')->exists($suratPerizinan->surat_tertanda_path)
            )) {
                return back()->with('error', 'Surat bertanda tangan belum diunggah mahasiswa atau berkasnya tidak tersedia.');
            }

            if ($suratPerizinan->izinKegiatan() && (
                ! $suratPerizinan->punyaDokumen()
                || ! Storage::disk('local')->exists($suratPerizinan->dokumen_path)
            )) {
                return back()->with('error', 'Surat resmi fakultas/prodi belum diunggah mahasiswa atau berkasnya tidak tersedia.');
            }
        }

        $musyrifah = $request->user();
        $catatan = filled($validated['catatan_musyrifah'] ?? null) ? trim($validated['catatan_musyrifah']) : null;

        // Kunci baris mahasiswa dan pengajuan dalam transaksi agar persetujuan
        // bersamaan tidak melampaui kuota dan keputusan tidak tertukar.
        $berhasil = DB::transaction(function () use ($request, $suratPerizinan, $lokasi, $validated, $musyrifah, $catatan): bool {
            $mahasiswa = User::query()->lockForUpdate()->findOrFail($suratPerizinan->user_id);

            $terkunci = $this->batasiWilayahSurat(SuratPerizinan::query(), $request, $lokasi)
                ->whereKey($suratPerizinan->id)
                ->whereIn('status', [SuratPerizinan::STATUS_MENUNGGU, SuratPerizinan::STATUS_PERLU_PERBAIKAN])
                ->when(
                    $validated['keputusan'] === SuratPerizinan::STATUS_DISETUJUI && $suratPerizinan->suratSemester(),
                    fn ($query) => $query->whereNotNull('surat_tertanda_path')
                )
                ->when(
                    $validated['keputusan'] === SuratPerizinan::STATUS_DISETUJUI && $suratPerizinan->izinKegiatan(),
                    fn ($query) => $query->whereNotNull('dokumen_path')
                )
                ->lockForUpdate()
                ->first();

            if ($terkunci === null) {
                return false;
            }

            if ($terkunci->izinPulangMingguan() && $validated['keputusan'] === SuratPerizinan::STATUS_DISETUJUI) {
                $this->suratService->pastikanAturanPulang($mahasiswa, $terkunci->tanggal_mulai, $terkunci->id);
            }

            $statusSebelum = $terkunci->status;
            $terkunci->update([
                'status' => $validated['keputusan'],
                'keputusan_oleh' => $musyrifah->id,
                'keputusan_nama' => $musyrifah->name,
                'keputusan_pada' => now(),
                'catatan_musyrifah' => $catatan,
            ]);

            $terkunci->catatRiwayat(
                $validated['keputusan'],
                $catatan,
                $musyrifah,
                match ($validated['keputusan']) {
                    SuratPerizinan::STATUS_DISETUJUI => SuratPerizinanDicatat::DISETUJUI,
                    SuratPerizinan::STATUS_DITOLAK => SuratPerizinanDicatat::DITOLAK,
                    SuratPerizinan::STATUS_PERLU_PERBAIKAN => SuratPerizinanDicatat::PERLU_PERBAIKAN,
                },
            );

            $this->audit->catat(
                'perizinan',
                'keputusan_'.$validated['keputusan'],
                'Izin '.$terkunci->labelJenis(),
                (int) $terkunci->getKey(),
                'Keputusan izin menjadi '.$validated['keputusan'].'.',
                $musyrifah,
                ['status' => $statusSebelum],
                ['status' => $validated['keputusan'], 'catatan_musyrifah' => $catatan],
            );

            return true;
        });

        if (! $berhasil) {
            return back()->with('error', 'Pengajuan sudah diproses, dokumen belum lengkap, atau bukan kewenangan wilayah Anda. Muat ulang halaman.');
        }

        $pesan = match ($validated['keputusan']) {
            SuratPerizinan::STATUS_DISETUJUI => 'Pengajuan berhasil disetujui.',
            SuratPerizinan::STATUS_PERLU_PERBAIKAN => 'Permintaan perbaikan surat dikirim ke mahasiswa.',
            default => 'Pengajuan telah ditolak.',
        };

        return redirect()
            ->route('musyrifah.surat_perizinan.show', $suratPerizinan)
            ->with('success', $pesan);
    }

    /**
     * Endpoint lama verifikasi surat diteruskan ke alur keputusan yang sama.
     */
    public function verifikasiSurat(Request $request, SuratPerizinan $suratPerizinan): RedirectResponse
    {
        abort_unless($suratPerizinan->suratSemester(), 404);

        $request->merge(['keputusan' => $request->input('status')]);

        return $this->keputusan($request, $suratPerizinan);
    }

    /**
     * Mencatat waktu keluar dan kembali mahasiswa ke Ma'had dalam WIB.
     */
    public function catatWaktu(Request $request, SuratPerizinan $suratPerizinan): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesSurat($request, $lokasi, $suratPerizinan);

        abort_unless($suratPerizinan->suratSemester(), 404);

        if ($suratPerizinan->status !== SuratPerizinan::STATUS_DISETUJUI) {
            return back()->with('error', 'Waktu hanya dapat dicatat setelah surat disetujui.');
        }

        $request->validate([
            'waktu_keluar' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'waktu_kembali' => ['nullable', 'date_format:Y-m-d\TH:i'],
        ], [
            'waktu_keluar.date_format' => 'Format waktu keluar tidak valid.',
            'waktu_kembali.date_format' => 'Format waktu kembali tidak valid.',
        ]);

        $keluarInput = filled($request->input('waktu_keluar')) ? (string) $request->input('waktu_keluar') : null;
        $kembaliInput = filled($request->input('waktu_kembali')) ? (string) $request->input('waktu_kembali') : null;

        if ($keluarInput === null && $kembaliInput === null) {
            throw ValidationException::withMessages(['waktu_keluar' => 'Isi waktu keluar atau waktu kembali terlebih dahulu.']);
        }

        $sekarang = Carbon::now('Asia/Jakarta');
        $keluar = $keluarInput !== null
            ? Carbon::parse($keluarInput, 'Asia/Jakarta')
            : $suratPerizinan->waktu_keluar?->copy()->timezone('Asia/Jakarta');
        $kembali = $kembaliInput !== null
            ? Carbon::parse($kembaliInput, 'Asia/Jakarta')
            : $suratPerizinan->waktu_kembali?->copy()->timezone('Asia/Jakarta');

        if ($keluarInput !== null && $keluar->greaterThan($sekarang)) {
            throw ValidationException::withMessages(['waktu_keluar' => 'Waktu keluar tidak boleh melebihi waktu sekarang.']);
        }

        if ($kembaliInput !== null && $kembali->greaterThan($sekarang)) {
            throw ValidationException::withMessages(['waktu_kembali' => 'Waktu kembali tidak boleh melebihi waktu sekarang.']);
        }

        if ($kembaliInput !== null && $keluar === null) {
            throw ValidationException::withMessages(['waktu_kembali' => 'Waktu keluar harus dicatat sebelum waktu kembali.']);
        }

        if ($keluar !== null && $kembali !== null && $kembali->lessThan($keluar)) {
            throw ValidationException::withMessages(['waktu_kembali' => 'Waktu kembali tidak boleh sebelum waktu keluar.']);
        }

        $musyrifah = $request->user();
        $berhasil = DB::transaction(function () use ($request, $suratPerizinan, $lokasi, $musyrifah, $keluarInput, $kembaliInput, $keluar, $kembali): bool {
            $terkunci = $this->batasiWilayahSurat(SuratPerizinan::query(), $request, $lokasi)
                ->whereKey($suratPerizinan->id)
                ->where('jenis', SuratPerizinan::JENIS_SEMESTER)
                ->where('status', SuratPerizinan::STATUS_DISETUJUI)
                ->lockForUpdate()
                ->first();

            if ($terkunci === null) {
                return false;
            }

            $statusSebelum = $terkunci->status;
            $waktuKeluarSebelum = $terkunci->waktu_keluar;
            $terkunci->update([
                'waktu_keluar' => $keluarInput !== null ? $keluar?->utc() : $terkunci->waktu_keluar,
                'waktu_kembali' => $kembaliInput !== null ? $kembali?->utc() : $terkunci->waktu_kembali,
                'status' => $kembaliInput !== null ? SuratPerizinan::STATUS_SELESAI : $terkunci->status,
            ]);

            $perluCatatKeluar = $keluarInput !== null && (
                $waktuKeluarSebelum === null
                || ! $waktuKeluarSebelum->equalTo($keluar)
            );

            if ($perluCatatKeluar) {
                $terkunci->catatRiwayat(
                    $statusSebelum,
                    'Waktu keluar Ma’had dicatat: '.$keluar?->locale('id')->translatedFormat('d F Y H:i').' WIB.',
                    $musyrifah,
                    SuratPerizinanDicatat::WAKTU_KELUAR,
                );
            }

            if ($kembaliInput !== null) {
                $terkunci->catatRiwayat(
                    SuratPerizinan::STATUS_SELESAI,
                    'Waktu kembali ke Ma’had dicatat: '.$kembali?->locale('id')->translatedFormat('d F Y H:i').' WIB.',
                    $musyrifah,
                    SuratPerizinanDicatat::SELESAI,
                );
            }

            $this->audit->catat(
                'perizinan',
                'catat_waktu_surat_semester',
                'Izin '.$terkunci->labelJenis(),
                (int) $terkunci->getKey(),
                'Waktu keluar/kembali surat semester diperbarui.',
                $musyrifah,
                ['status' => $statusSebelum],
                [
                    'status' => $terkunci->status,
                    'waktu_keluar' => $terkunci->waktu_keluar,
                    'waktu_kembali' => $terkunci->waktu_kembali,
                ],
            );

            return true;
        });

        if (! $berhasil) {
            return back()->with('error', 'Surat sudah diproses atau bukan kewenangan wilayah Anda. Muat ulang halaman.');
        }

        return redirect()
            ->route('musyrifah.surat_perizinan.show', $suratPerizinan)
            ->with('success', $kembaliInput !== null
                ? 'Waktu kembali dicatat dan surat dinyatakan selesai.'
                : 'Waktu keluar mahasiswa berhasil dicatat.');
    }

    /**
     * Mengirim dokumen pengajuan kepada Musyrifah yang berwenang.
     */
    public function dokumen(Request $request, SuratPerizinan $suratPerizinan, string $jenis): BinaryFileResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesSurat($request, $lokasi, $suratPerizinan);

        $path = match ($jenis) {
            'pendukung' => $suratPerizinan->dokumen_path,
            'tertanda' => $suratPerizinan->surat_tertanda_path,
            default => null,
        };

        abort_unless(filled($path), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }

    /**
     * Mengambil wilayah dari profil Musyrifah yang ditetapkan Admin.
     * Musyrifah tanpa wilayah penempatan tidak boleh mengakses data perizinan.
     *
     * @return array{gedung: string, lantai: int}
     */
    private function lokasiPengelola(Request $request): array
    {
        $akun = $request->user();
        $role = strtolower(trim((string) $akun?->role));
        abort_unless(in_array($role, ['musyrifah', 'pengelola'], true), 403);

        abort_unless(
            strtolower(trim((string) $akun->status)) === 'aktif',
            403,
            'Akun Musyrifah belum aktif.'
        );

        $gedung = trim((string) $akun->gedung_penempatan);
        $lantai = filter_var($akun->lantai_penempatan, FILTER_VALIDATE_INT);

        abort_if(
            $gedung === '' || $lantai === false || $lantai < 1,
            403,
            'Penempatan gedung dan lantai belum ditetapkan oleh Admin.'
        );

        return ['gedung' => $gedung, 'lantai' => (int) $lantai];
    }

    /**
     * Membatasi daftar, monitoring, dokumen, dan UPDATE surat berdasarkan
     * gedung/lantai kamar mahasiswa serta Musyrifah yang ditugaskan.
     * Data lama tanpa penugasan tetap dapat diakses Musyrifah di lantai yang sama.
     *
     * @param  array{gedung: string, lantai: int}  $lokasi
     */
    private function batasiWilayahSurat(Builder $query, Request $request, array $lokasi): Builder
    {
        $query->whereHas('user.kamar', function (Builder $kamar) use ($lokasi): void {
            $kamar->whereRaw('LOWER(TRIM(gedung)) = ?', [mb_strtolower($lokasi['gedung'])])
                ->where('lantai', $lokasi['lantai']);
        });

        $akun = $request->user();

        if (strtolower(trim((string) $akun?->role)) === 'pengelola') {
            return $query;
        }

        return $query->where(function (Builder $izin) use ($akun): void {
            $izin->whereNull('musyrifah_id')
                ->orWhere('musyrifah_id', $akun?->id);
        });
    }

    /**
     * Menyembunyikan pengajuan di luar wilayah/penugasan dengan respons 404.
     *
     * @param  array{gedung: string, lantai: int}  $lokasi
     */
    private function pastikanAksesSurat(Request $request, array $lokasi, SuratPerizinan $suratPerizinan): void
    {
        $diizinkan = $this->batasiWilayahSurat(SuratPerizinan::query(), $request, $lokasi)
            ->whereKey($suratPerizinan->getKey())
            ->exists();

        abort_unless($diizinkan, 404);
    }

    /**
     * @return array<string, string>
     */
    private function jenisPilihan(): array
    {
        return [
            SuratPerizinan::JENIS_KEGIATAN_FAKULTAS => 'Izin Kegiatan Fakultas/Prodi',
            SuratPerizinan::JENIS_PULANG => 'Izin Pulang Mingguan',
            SuratPerizinan::JENIS_DARURAT => 'Izin Pulang Darurat/Khusus',
            SuratPerizinan::JENIS_SEMESTER => 'Izin Pulang Semester',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function statusPilihan(): array
    {
        return [
            SuratPerizinan::STATUS_MENUNGGU => 'Menunggu Persetujuan',
            SuratPerizinan::STATUS_DISETUJUI => 'Disetujui',
            SuratPerizinan::STATUS_DITOLAK => 'Ditolak',
            SuratPerizinan::STATUS_PERLU_PERBAIKAN => 'Perlu Perbaikan',
            SuratPerizinan::STATUS_SELESAI => 'Selesai',
            SuratPerizinan::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
    }
}
