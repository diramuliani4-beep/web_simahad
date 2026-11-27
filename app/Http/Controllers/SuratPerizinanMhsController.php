<?php

namespace App\Http\Controllers;

use App\Events\SuratPerizinanDicatat;
use App\Models\SuratPerizinan;
use App\Models\User;
use App\Services\SuratPerizinanService;
use App\Services\SuratPulangSemesterPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class SuratPerizinanMhsController extends Controller
{
    /**
     * Menampilkan ringkasan, kuota, dan riwayat pengajuan milik mahasiswa.
     */
    public function index(Request $request): View
    {
        $user = $this->penggunaMahasiswa($request);

        $pengajuans = $user->suratPerizinans()
            ->with(['musyrifah', 'riwayats'])
            ->latest('id')
            ->get();

        $jumlahMenunggu = $pengajuans
            ->whereIn('status', [SuratPerizinan::STATUS_MENUNGGU, SuratPerizinan::STATUS_PERLU_PERBAIKAN])
            ->count();

        return view('Mahasiswa.surat_perizinan_mhs', [
            'user' => $user,
            'pengajuans' => $pengajuans,
            'jumlahMenunggu' => $jumlahMenunggu,
            'jumlahDisetujui' => $pengajuans->where('status', SuratPerizinan::STATUS_DISETUJUI)->count(),
            'jumlahDitolak' => $pengajuans->where('status', SuratPerizinan::STATUS_DITOLAK)->count(),
            'kuotaPulang' => SuratPerizinan::kuotaPulang($user),
            'formAktif' => $request->query('form'),
        ]);
    }

    /**
     * Formulir izin kegiatan resmi fakultas/prodi.
     */
    public function kegiatan(Request $request): View
    {
        $user = $this->penggunaMahasiswa($request);

        return view('surat dan perizinan.kegiatan', [
            'user' => $user,
            'jenis' => SuratPerizinan::JENIS_KEGIATAN_FAKULTAS,
            'riwayatKegiatan' => $user->suratPerizinans()
                ->whereIn('jenis', [SuratPerizinan::JENIS_KEGIATAN_FAKULTAS, SuratPerizinan::JENIS_KEGIATAN_KAMPUS])
                ->with('riwayats')
                ->latest('id')
                ->get(),
        ]);
    }

    /**
     * Menyimpan pengajuan izin kegiatan resmi fakultas/prodi.
     */
    public function simpanKegiatan(Request $request, SuratPerizinanService $suratService): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);

        $validated = $request->validate([
            'jenis' => ['required', Rule::in([SuratPerizinan::JENIS_KEGIATAN_FAKULTAS])],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'waktu_mulai' => ['nullable', 'date_format:H:i'],
            'waktu_selesai' => ['nullable', 'date_format:H:i', 'after:waktu_mulai'],
            'alasan' => ['required', 'string', 'min:10', 'max:2000'],
            'dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'jenis.required' => 'Jenis izin kegiatan wajib dipilih.',
            'jenis.in' => 'Jenis izin kegiatan tidak dikenali.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'nama_kegiatan.max' => 'Nama kegiatan maksimal 255 karakter.',
            'penyelenggara.required' => 'Penyelenggara kegiatan wajib diisi.',
            'penyelenggara.max' => 'Penyelenggara maksimal 255 karakter.',
            'lokasi.required' => 'Lokasi kegiatan wajib diisi.',
            'lokasi.max' => 'Lokasi kegiatan maksimal 255 karakter.',
            'tanggal_mulai.required' => 'Tanggal kegiatan wajib diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal kegiatan tidak boleh tanggal yang sudah lewat.',
            'tanggal_selesai.required' => 'Tanggal selesai kegiatan wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'waktu_mulai.date_format' => 'Format jam mulai tidak valid.',
            'waktu_selesai.date_format' => 'Format jam selesai tidak valid.',
            'waktu_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'alasan.required' => 'Alasan izin wajib diisi.',
            'alasan.min' => 'Alasan izin minimal 10 karakter.',
            'alasan.max' => 'Alasan izin maksimal 2000 karakter.',
            'dokumen.required' => 'Surat resmi pendukung wajib diunggah.',
            'dokumen.file' => 'Surat pendukung gagal diunggah.',
            'dokumen.mimes' => 'Surat pendukung harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen.max' => 'Ukuran surat pendukung maksimal 5 MB.',
        ]);

        $path = $request->file('dokumen')->store('surat-perizinan/'.$user->id, 'local');

        if (! $path) {
            return back()
                ->withErrors(['dokumen' => 'Surat pendukung gagal disimpan. Silakan coba lagi.'])
                ->withInput();
        }

        try {
            $pengajuan = $suratService->buatPengajuan($user, [
                'jenis' => $validated['jenis'],
                'nama_kegiatan' => trim($validated['nama_kegiatan']),
                'penyelenggara' => trim($validated['penyelenggara']),
                'lokasi' => trim($validated['lokasi']),
                'waktu_mulai' => $validated['waktu_mulai'] ?? null,
                'waktu_selesai' => $validated['waktu_selesai'] ?? null,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'durasi_hari' => Carbon::parse($validated['tanggal_mulai'])
                    ->diffInDays(Carbon::parse($validated['tanggal_selesai'])) + 1,
                'alasan' => trim($validated['alasan']),
                'dokumen_path' => $path,
                'dokumen_nama' => $request->file('dokumen')->getClientOriginalName(),
            ], 'Pengajuan izin kegiatan fakultas/prodi dikirim mahasiswa.');
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        return redirect()
            ->route('surat.perizinan.mhs')
            ->with('success', 'Pengajuan '.$pengajuan->labelJenis().' berhasil dikirim. Menunggu persetujuan Musyrifah.');
    }

    /**
     * Halaman surat pulang semester: pengajuan surat, cetak, dan unggah berkas.
     */
    public function semester(Request $request): View
    {
        $user = $this->penggunaMahasiswa($request);

        return view('surat dan perizinan.surat_pulang_semester', [
            'user' => $user,
            'pendaftaran' => $user->pendaftarans()
                ->where('status', 'disetujui')
                ->latest('id')
                ->first(),
            'pengajuans' => $user->suratPerizinans()
                ->where('jenis', SuratPerizinan::JENIS_SEMESTER)
                ->with(['musyrifah', 'riwayats'])
                ->latest('id')
                ->get(),
        ]);
    }

    /**
     * Membuat permohonan surat pulang semester yang dapat dicetak mahasiswa.
     */
    public function semesterStore(Request $request, SuratPerizinanService $suratService): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);

        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'lokasi' => ['required', 'string', 'max:255'],
            'alasan' => ['required', 'string', 'min:10', 'max:2000'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'transportasi' => ['nullable', 'string', 'max:255'],
        ], [
            'tanggal_mulai.required' => 'Tanggal pulang wajib diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal pulang tidak boleh tanggal yang sudah lewat.',
            'tanggal_selesai.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pulang.',
            'lokasi.required' => 'Tujuan pulang wajib diisi.',
            'lokasi.max' => 'Tujuan pulang maksimal 255 karakter.',
            'alasan.required' => 'Alasan pulang wajib diisi.',
            'alasan.min' => 'Alasan pulang minimal 10 karakter.',
            'alasan.max' => 'Alasan pulang maksimal 2000 karakter.',
            'keterangan.max' => 'Keterangan maksimal 255 karakter.',
            'transportasi.max' => 'Transportasi maksimal 255 karakter.',
        ]);

        $pendaftaran = $user->pendaftarans()
            ->where('status', 'disetujui')
            ->latest('id')
            ->first();
        $nama = trim((string) ($user->name ?: $pendaftaran?->nama_lengkap));
        $nim = trim((string) ($user->nim ?: $pendaftaran?->nim));
        $prodi = trim((string) $pendaftaran?->program_studi);

        if ($nama === '' || $nim === '' || $prodi === '') {
            throw ValidationException::withMessages([
                'tanggal_mulai' => 'Identitas surat belum lengkap. Pastikan nama, NIM, dan program studi pada data mahasiswa sudah terisi.',
            ]);
        }

        $mulai = Carbon::parse($validated['tanggal_mulai'])->startOfDay();
        $kembali = Carbon::parse($validated['tanggal_selesai'])->startOfDay();

        $pengajuan = DB::transaction(function () use ($user, $validated, $suratService, $mulai, $kembali): SuratPerizinan {
            $pengajuan = $suratService->buatPengajuan($user, [
                'jenis' => SuratPerizinan::JENIS_SEMESTER,
                'tanggal_mulai' => $mulai->toDateString(),
                'tanggal_selesai' => $kembali->toDateString(),
                'durasi_hari' => (int) $mulai->diffInDays($kembali) + 1,
                'lokasi' => trim($validated['lokasi']),
                'alasan' => trim($validated['alasan']),
                'keterangan' => filled($validated['keterangan'] ?? null) ? trim($validated['keterangan']) : null,
                'transportasi' => filled($validated['transportasi'] ?? null) ? trim($validated['transportasi']) : null,
            ], 'Permohonan surat pulang semester dibuat.');

            // Nomor surat dibentuk dari identitas pengajuan agar unik dan mudah ditelusuri.
            $pengajuan->update([
                'nomor_surat' => 'SPM/'.now()->format('Ymd').'/'.str_pad((string) $pengajuan->id, 4, '0', STR_PAD_LEFT),
            ]);

            return $pengajuan;
        });

        return redirect()
            ->route('surat.perizinan.mhs.semester')
            ->with('success', 'Surat pulang semester berhasil dibuat. Silakan cetak, minta tanda tangan orang tua, lalu unggah kembali.');
    }

    /**
     * Menampilkan surat pulang semester dalam format siap cetak.
     */
    public function cetakSemester(Request $request, SuratPerizinan $suratPerizinan): View
    {
        $user = $this->penggunaMahasiswa($request);
        $this->pastikanMilikSendiri($user, $suratPerizinan);

        abort_unless($suratPerizinan->suratSemester(), 404);
        abort_unless(filled($suratPerizinan->nomor_surat), 404, 'Surat belum memiliki nomor.');

        return view('surat dan perizinan.cetak_surat_pulang_semester', [
            'surat' => $suratPerizinan,
            'user' => $user,
            'kamar' => $user->kamar,
            'pendaftaran' => $user->pendaftarans()
                ->where('status', 'disetujui')
                ->latest('id')
                ->first(),
        ]);
    }

    /**
     * Mengunduh surat pulang semester sebagai PDF resmi.
     */
    public function unduhSemester(
        Request $request,
        SuratPerizinan $suratPerizinan,
        SuratPulangSemesterPdfService $pdfService,
    ): Response {
        $user = $this->penggunaMahasiswa($request);
        $this->pastikanMilikSendiri($user, $suratPerizinan);

        abort_unless($suratPerizinan->suratSemester(), 404);

        $namaBerkas = 'surat-izin-pulang-'.preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $suratPerizinan->nomor_surat).'.pdf';

        return response($pdfService->render($suratPerizinan), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$namaBerkas.'"',
        ]);
    }

    /**
     * Mengunggah surat pulang semester yang sudah ditandatangani orang tua.
     */
    public function unggahSuratTertanda(Request $request, SuratPerizinan $suratPerizinan): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);
        $this->pastikanMilikSendiri($user, $suratPerizinan);

        abort_unless($suratPerizinan->suratSemester(), 404);

        if (! in_array($suratPerizinan->status, [SuratPerizinan::STATUS_MENUNGGU, SuratPerizinan::STATUS_PERLU_PERBAIKAN], true)) {
            return back()->with('error', 'Surat ini sudah diverifikasi dan tidak dapat diunggah ulang.');
        }

        $request->validate([
            'surat_tertanda' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'surat_tertanda.required' => 'Berkas surat bertanda tangan wajib dipilih.',
            'surat_tertanda.file' => 'Berkas surat gagal diunggah.',
            'surat_tertanda.mimes' => 'Berkas surat harus berformat PDF, JPG, JPEG, atau PNG.',
            'surat_tertanda.max' => 'Ukuran berkas surat maksimal 5 MB.',
        ]);

        $pathBaru = $request->file('surat_tertanda')->store('surat-perizinan/'.$user->id.'/tertanda', 'local');

        if (! $pathBaru) {
            return back()->with('error', 'Berkas surat gagal disimpan. Silakan coba lagi.');
        }

        $pathLama = $suratPerizinan->surat_tertanda_path;

        try {
            DB::transaction(function () use ($suratPerizinan, $request, $user, $pathBaru): void {
                $suratPerizinan->update([
                    'surat_tertanda_path' => $pathBaru,
                    'surat_tertanda_nama' => $request->file('surat_tertanda')->getClientOriginalName(),
                    'surat_diunggah_pada' => now(),
                    'status' => SuratPerizinan::STATUS_MENUNGGU,
                    'keputusan_oleh' => null,
                    'keputusan_nama' => null,
                    'keputusan_pada' => null,
                    'catatan_musyrifah' => null,
                ]);

                $suratPerizinan->catatRiwayat(
                    SuratPerizinan::STATUS_MENUNGGU,
                    'Mahasiswa mengunggah surat bertanda tangan orang tua.',
                    $user,
                    SuratPerizinanDicatat::SURAT_DIUPLOAD,
                );
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($pathBaru);

            throw $exception;
        }

        if (filled($pathLama) && $pathLama !== $pathBaru) {
            Storage::disk('local')->delete($pathLama);
        }

        return redirect()
            ->route('surat.perizinan.mhs.semester')
            ->with('success', 'Surat bertanda tangan berhasil diunggah dan menunggu pemeriksaan Musyrifah.');
    }

    /**
     * Membatalkan pengajuan yang belum diputus.
     */
    public function batalkan(Request $request, SuratPerizinan $suratPerizinan): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);
        $this->pastikanMilikSendiri($user, $suratPerizinan);

        if (! in_array($suratPerizinan->status, [SuratPerizinan::STATUS_MENUNGGU, SuratPerizinan::STATUS_PERLU_PERBAIKAN], true)) {
            return back()->with('error', 'Pengajuan yang sudah diputus tidak dapat dibatalkan.');
        }

        $suratPerizinan->update([
            'status' => SuratPerizinan::STATUS_DIBATALKAN,
            'dibatalkan_pada' => now(),
        ]);

        $suratPerizinan->catatRiwayat(
            SuratPerizinan::STATUS_DIBATALKAN,
            'Pengajuan dibatalkan mahasiswa.',
            $user,
            SuratPerizinanDicatat::DIBATALKAN,
        );

        return redirect()
            ->route('surat.perizinan.mhs')
            ->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    /**
     * Mengirim dokumen pengajuan hanya kepada pemiliknya.
     */
    public function dokumen(Request $request, SuratPerizinan $suratPerizinan, string $jenis): BinaryFileResponse
    {
        $user = $this->penggunaMahasiswa($request);
        $this->pastikanMilikSendiri($user, $suratPerizinan);

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
     * Memastikan hanya akun mahasiswa yang dapat mengakses fitur ini.
     */
    private function penggunaMahasiswa(Request $request): User
    {
        $user = $request->user();

        abort_unless(
            $user && strtolower(trim((string) $user->role)) === 'mahasiswa',
            403,
            'Halaman ini hanya dapat diakses oleh mahasiswa.'
        );

        return $user;
    }

    /**
     * Mahasiswa hanya boleh mengelola pengajuan miliknya sendiri.
     */
    private function pastikanMilikSendiri(User $user, SuratPerizinan $suratPerizinan): void
    {
        abort_unless((int) $suratPerizinan->user_id === (int) $user->id, 403);
    }
}
