<?php

namespace App\Http\Controllers;

use App\Models\SuratPerizinan;
use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MusyrifahSuratPerizinanController extends Controller
{
    public function __construct(private readonly AuditTrailService $audit) {}

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

        $pengajuans = $this->batasiWilayahSurat(SuratPerizinan::query(), $lokasi)
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
        $kuotaMonitoring = $this->batasiWilayahSurat(SuratPerizinan::query(), $lokasi)
            ->with('user')
            ->where('jenis', SuratPerizinan::JENIS_PULANG)
            ->whereIn('status', SuratPerizinan::statusMenahanKuota())
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
        $this->pastikanAksesSurat($lokasi, $suratPerizinan);

        return view('musyrifah.detail_surat_perizinan', [
            'pengajuan' => $suratPerizinan->load(['user', 'riwayats', 'keputusanOleh']),
            'kuotaPulang' => $suratPerizinan->user
                ? SuratPerizinan::kuotaPulang($suratPerizinan->user, $suratPerizinan->tanggal_mulai)
                : null,
            'statusPilihan' => $this->statusPilihan(),
        ]);
    }

    /**
     * Menyetujui atau menolak pengajuan izin kegiatan/pulang.
     */
    public function keputusan(Request $request, SuratPerizinan $suratPerizinan): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesSurat($lokasi, $suratPerizinan);

        if ($suratPerizinan->suratSemester()) {
            return back()->with('error', 'Surat pulang semester diproses melalui verifikasi surat bertanda tangan.');
        }

        if (! $suratPerizinan->masihMenunggu()) {
            return back()->with('error', 'Pengajuan ini sudah diputus dan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'keputusan' => ['required', Rule::in([
                SuratPerizinan::STATUS_DISETUJUI,
                SuratPerizinan::STATUS_DITOLAK,
            ])],
            'catatan_musyrifah' => [
                'nullable',
                'string',
                'max:2000',
                Rule::requiredIf($request->input('keputusan') === SuratPerizinan::STATUS_DITOLAK),
            ],
        ], [
            'keputusan.required' => 'Keputusan wajib dipilih.',
            'keputusan.in' => 'Keputusan tidak valid.',
            'catatan_musyrifah.required' => 'Catatan keputusan wajib diisi saat menolak pengajuan.',
            'catatan_musyrifah.max' => 'Catatan keputusan maksimal 2000 karakter.',
        ]);

        $musyrifah = $request->user();
        $catatan = filled($validated['catatan_musyrifah'] ?? null) ? trim($validated['catatan_musyrifah']) : null;
        // Query perubahan tetap dibatasi gedung/lantai untuk menghindari
        // bypass lewat POST langsung. Update + riwayat + audit atomik.
        $berhasil = DB::transaction(function () use ($suratPerizinan, $lokasi, $validated, $musyrifah, $catatan): bool {
            $statusSebelum = $suratPerizinan->status;

            $diubah = $this->batasiWilayahSurat(SuratPerizinan::query(), $lokasi)
                ->whereKey($suratPerizinan->id)
                ->where('jenis', '!=', SuratPerizinan::JENIS_SEMESTER)
                ->whereIn('status', [
                    SuratPerizinan::STATUS_MENUNGGU,
                    SuratPerizinan::STATUS_PERLU_PERBAIKAN,
                ])
                ->update([
                    'status' => $validated['keputusan'],
                    'keputusan_oleh' => $musyrifah->id,
                    'keputusan_nama' => $musyrifah->name,
                    'keputusan_pada' => now(),
                    'catatan_musyrifah' => $catatan,
                ]);

            if (! $diubah) {
                return false;
            }

            $suratPerizinan->refresh()->catatRiwayat(
                $validated['keputusan'],
                $catatan,
                $musyrifah
            );

            $this->audit->catat(
                'perizinan',
                'verifikasi_'.$validated['keputusan'],
                'Izin '.$suratPerizinan->labelJenis(),
                (int) $suratPerizinan->getKey(),
                'Keputusan izin menjadi '.$validated['keputusan'].'.',
                $musyrifah,
                ['status' => $statusSebelum],
                ['status' => $validated['keputusan'], 'catatan_musyrifah' => $catatan],
            );

            return true;
        });

        if (! $berhasil) {
            return back()->with('error', 'Pengajuan sudah diproses atau bukan kewenangan wilayah Anda. Muat ulang halaman.');
        }

        $pesan = $validated['keputusan'] === SuratPerizinan::STATUS_DISETUJUI
            ? 'Pengajuan berhasil disetujui.'
            : 'Pengajuan telah ditolak.';

        return redirect()
            ->route('musyrifah.surat_perizinan.show', $suratPerizinan)
            ->with('success', $pesan);
    }

    /**
     * Memeriksa surat pulang semester bertanda tangan orang tua.
     */
    public function verifikasiSurat(Request $request, SuratPerizinan $suratPerizinan): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesSurat($lokasi, $suratPerizinan);

        abort_unless($suratPerizinan->suratSemester(), 404);

        if (! $suratPerizinan->punyaSuratTertanda()) {
            return back()->with('error', 'Mahasiswa belum mengunggah surat bertanda tangan orang tua.');
        }

        if (in_array($suratPerizinan->status, [SuratPerizinan::STATUS_SELESAI, SuratPerizinan::STATUS_DIBATALKAN], true)) {
            return back()->with('error', 'Surat ini sudah diverifikasi atau dibatalkan.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                SuratPerizinan::STATUS_SELESAI,
                SuratPerizinan::STATUS_PERLU_PERBAIKAN,
            ])],
            'catatan_musyrifah' => [
                'nullable',
                'string',
                'max:2000',
                Rule::requiredIf($request->input('status') === SuratPerizinan::STATUS_PERLU_PERBAIKAN),
            ],
        ], [
            'status.required' => 'Status verifikasi wajib dipilih.',
            'status.in' => 'Status verifikasi tidak valid.',
            'catatan_musyrifah.required' => 'Catatan wajib diisi saat meminta perbaikan surat.',
            'catatan_musyrifah.max' => 'Catatan maksimal 2000 karakter.',
        ]);

        $musyrifah = $request->user();
        $catatan = filled($validated['catatan_musyrifah'] ?? null) ? trim($validated['catatan_musyrifah']) : null;
        // Surat semester juga diperiksa ulang pada query UPDATE untuk
        // mencegah verifikasi ganda atau dari wilayah lain.
        $berhasil = DB::transaction(function () use ($suratPerizinan, $lokasi, $validated, $musyrifah, $catatan): bool {
            $statusSebelum = $suratPerizinan->status;

            $diubah = $this->batasiWilayahSurat(SuratPerizinan::query(), $lokasi)
                ->whereKey($suratPerizinan->id)
                ->where('jenis', SuratPerizinan::JENIS_SEMESTER)
                ->whereNotNull('surat_tertanda_path')
                ->whereNotIn('status', [
                    SuratPerizinan::STATUS_SELESAI,
                    SuratPerizinan::STATUS_DIBATALKAN,
                ])
                ->update([
                    'status' => $validated['status'],
                    'keputusan_oleh' => $musyrifah->id,
                    'keputusan_nama' => $musyrifah->name,
                    'keputusan_pada' => now(),
                    'catatan_musyrifah' => $catatan,
                ]);

            if (! $diubah) {
                return false;
            }

            $suratPerizinan->refresh()->catatRiwayat($validated['status'], $catatan, $musyrifah);

            $this->audit->catat(
                'perizinan',
                'verifikasi_surat_'.$validated['status'],
                'Surat izin '.$suratPerizinan->labelJenis(),
                (int) $suratPerizinan->getKey(),
                'Verifikasi surat bertanda tangan menjadi '.$validated['status'].'.',
                $musyrifah,
                ['status' => $statusSebelum],
                ['status' => $validated['status'], 'catatan_musyrifah' => $catatan],
            );

            return true;
        });

        if (! $berhasil) {
            return back()->with('error', 'Surat sudah diproses atau bukan kewenangan wilayah Anda. Muat ulang halaman.');
        }

        $pesan = $validated['status'] === SuratPerizinan::STATUS_SELESAI
            ? 'Surat bertanda tangan berhasil diverifikasi.'
            : 'Permintaan perbaikan surat dikirim ke mahasiswa.';

        return redirect()
            ->route('musyrifah.surat_perizinan.show', $suratPerizinan)
            ->with('success', $pesan);
    }

    /**
     * Mengirim dokumen pengajuan kepada Musyrifah yang berwenang.
     */
    public function dokumen(Request $request, SuratPerizinan $suratPerizinan, string $jenis): BinaryFileResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesSurat($lokasi, $suratPerizinan);

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
     * Filter utama semua daftar, monitoring, dan UPDATE surat.
     * Wilayah berasal dari users.kamar_id -> kamars.gedung/lantai.
     * Mahasiswa yang belum punya kamar aktif tidak otomatis diizinkan.
     *
     * @param array{gedung: string, lantai: int} $lokasi
     */
    private function batasiWilayahSurat(Builder $query, array $lokasi): Builder
    {
        return $query->whereHas('user.kamar', function (Builder $kamar) use ($lokasi): void {
            // Case-insensitive dan mengabaikan spasi tepi sesuai
            // cara membandingkan wilayah pada modul Data Kamar.
            $kamar->whereRaw('LOWER(TRIM(gedung)) = ?', [mb_strtolower($lokasi['gedung'])])
                ->where('lantai', $lokasi['lantai']);
        });
    }

    /**
     * Perlindungan langsung URL detail, dokumen, dan POST keputusan.
     *
     * @param array{gedung: string, lantai: int} $lokasi
     */
    private function pastikanAksesSurat(array $lokasi, SuratPerizinan $suratPerizinan): void
    {
        $diizinkan = $this->batasiWilayahSurat(SuratPerizinan::query(), $lokasi)
            ->whereKey($suratPerizinan->getKey())
            ->exists();

        abort_unless(
            $diizinkan,
            403,
            'Surat perizinan ini berada di luar gedung/lantai penempatan Musyrifah.'
        );
    }

    /**
     * @return array<string, string>
     */
    private function jenisPilihan(): array
    {
        return [
            SuratPerizinan::JENIS_KEGIATAN_FAKULTAS => 'Izin Kegiatan Fakultas/Prodi',
            SuratPerizinan::JENIS_KEGIATAN_KAMPUS => 'Izin Kegiatan Kampus',
            SuratPerizinan::JENIS_PULANG => 'Izin Pulang Mingguan',
            SuratPerizinan::JENIS_SEMESTER => 'Surat Pulang Semester',
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
