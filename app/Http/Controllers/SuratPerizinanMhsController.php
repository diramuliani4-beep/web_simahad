<?php

namespace App\Http\Controllers;

use App\Models\SuratPerizinan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SuratPerizinanMhsController extends Controller
{
    /**
     * Menampilkan ringkasan, kuota, dan riwayat pengajuan milik mahasiswa.
     */
    public function index(Request $request): View
    {
        $user = $this->penggunaMahasiswa($request);

        $pengajuans = $user->suratPerizinans()
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
     * Formulir izin kegiatan fakultas/prodi maupun kampus.
     */
    public function kegiatan(Request $request): View
    {
        $user = $this->penggunaMahasiswa($request);

        $jenis = $request->query('jenis');

        if (! in_array($jenis, [SuratPerizinan::JENIS_KEGIATAN_FAKULTAS, SuratPerizinan::JENIS_KEGIATAN_KAMPUS], true)) {
            $jenis = SuratPerizinan::JENIS_KEGIATAN_FAKULTAS;
        }

        return view('surat dan perizinan.kegiatan', [
            'user' => $user,
            'jenis' => $jenis,
            'riwayatKegiatan' => $user->suratPerizinans()
                ->whereIn('jenis', [SuratPerizinan::JENIS_KEGIATAN_FAKULTAS, SuratPerizinan::JENIS_KEGIATAN_KAMPUS])
                ->latest('id')
                ->get(),
        ]);
    }

    /**
     * Menyimpan pengajuan izin kegiatan fakultas/prodi maupun kampus.
     */
    public function simpanKegiatan(Request $request): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);

        $validated = $request->validate([
            'jenis' => ['required', Rule::in([
                SuratPerizinan::JENIS_KEGIATAN_FAKULTAS,
                SuratPerizinan::JENIS_KEGIATAN_KAMPUS,
            ])],
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

        $pengajuan = $user->suratPerizinans()->create([
            'jenis' => $validated['jenis'],
            'status' => SuratPerizinan::STATUS_MENUNGGU,
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
        ]);

        $pengajuan->catatRiwayat(SuratPerizinan::STATUS_MENUNGGU, 'Pengajuan dikirim mahasiswa.', $user);

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
            'pengajuans' => $user->suratPerizinans()
                ->where('jenis', SuratPerizinan::JENIS_SEMESTER)
                ->latest('id')
                ->get(),
        ]);
    }

    /**
     * Membuat permohonan surat pulang semester yang dapat dicetak mahasiswa.
     */
    public function semesterStore(Request $request): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);

        $validated = $request->validate([
            'alasan' => ['nullable', 'string', 'max:2000'],
        ], [
            'alasan.max' => 'Keterangan maksimal 2000 karakter.',
        ]);

        $pengajuan = DB::transaction(function () use ($user, $validated): SuratPerizinan {
            $pengajuan = $user->suratPerizinans()->create([
                'jenis' => SuratPerizinan::JENIS_SEMESTER,
                'status' => SuratPerizinan::STATUS_MENUNGGU,
                'tanggal_mulai' => now()->toDateString(),
                'alasan' => filled($validated['alasan'] ?? null) ? trim($validated['alasan']) : null,
            ]);

            // Nomor surat dibentuk dari identitas pengajuan agar unik dan mudah ditelusuri.
            $pengajuan->update([
                'nomor_surat' => 'SPM/'.now()->format('Ymd').'/'.str_pad((string) $pengajuan->id, 4, '0', STR_PAD_LEFT),
            ]);

            $pengajuan->catatRiwayat(SuratPerizinan::STATUS_MENUNGGU, 'Permohonan surat pulang semester dibuat.', $user);

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
            'pendaftaran' => $user->pendaftarans()->latest('id')->first(),
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

        $suratPerizinan->update([
            'surat_tertanda_path' => $pathBaru,
            'surat_tertanda_nama' => $request->file('surat_tertanda')->getClientOriginalName(),
            'surat_diunggah_pada' => now(),
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);

        if (filled($pathLama) && $pathLama !== $pathBaru) {
            Storage::disk('local')->delete($pathLama);
        }

        $suratPerizinan->catatRiwayat(
            SuratPerizinan::STATUS_MENUNGGU,
            'Mahasiswa mengunggah surat bertanda tangan orang tua.',
            $user
        );

        return redirect()
            ->route('surat.perizinan.mhs.semester')
            ->with('success', 'Surat bertanda tangan berhasil diunggah dan menunggu pemeriksaan Musyrifah.');
    }

    /**
     * Membatalkan pengajuan yang belum diputus dan mengembalikan slot kuota.
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
            'Pengajuan dibatalkan mahasiswa sehingga slot kuota dikembalikan.',
            $user
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
