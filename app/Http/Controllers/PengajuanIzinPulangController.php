<?php

namespace App\Http\Controllers;

use App\Models\SuratPerizinan;
use App\Models\User;
use App\Services\SuratPerizinanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PengajuanIzinPulangController extends Controller
{
    /**
     * Menampilkan formulir izin pulang mingguan beserta monitoring kuota.
     */
    public function index(Request $request): View
    {
        return $this->tampilkanForm($request, false);
    }

    /**
     * Menampilkan formulir izin pulang darurat/khusus yang bebas kuota reguler.
     */
    public function darurat(Request $request): View
    {
        return $this->tampilkanForm($request, true);
    }

    /**
     * Memvalidasi kuota, jarak, dan durasi izin pulang mingguan.
     */
    public function store(Request $request, SuratPerizinanService $suratService): RedirectResponse
    {
        return $this->simpan($request, false, $suratService);
    }

    /**
     * Memvalidasi izin darurat tanpa mengurangi kuota izin pulang mingguan.
     */
    public function simpanDarurat(Request $request, SuratPerizinanService $suratService): RedirectResponse
    {
        return $this->simpan($request, true, $suratService);
    }

    private function tampilkanForm(Request $request, bool $darurat): View
    {
        $user = $this->penggunaMahasiswa($request);

        return view('surat dan perizinan.pengajuan_izin_pulang', [
            'user' => $user,
            'darurat' => $darurat,
            'kuotaPulang' => SuratPerizinan::kuotaPulang($user),
            'riwayatPulang' => $user->suratPerizinans()
                ->whereIn('jenis', [SuratPerizinan::JENIS_PULANG, SuratPerizinan::JENIS_DARURAT])
                ->with('riwayats')
                ->latest('id')
                ->get(),
        ]);
    }

    private function simpan(Request $request, bool $darurat, SuratPerizinanService $suratService): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);

        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'lokasi' => ['required', 'string', 'max:255'],
            'alasan' => ['required', 'string', 'min:10', 'max:2000'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'transportasi' => ['nullable', 'string', 'max:255'],
            'dokumen' => $darurat
                ? ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']
                : ['prohibited'],
        ], [
            'tanggal_mulai.required' => 'Tanggal mulai izin wajib diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai izin tidak boleh tanggal yang sudah lewat.',
            'tanggal_selesai.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal mulai.',
            'lokasi.required' => 'Alamat atau tujuan pulang wajib diisi.',
            'lokasi.max' => 'Alamat atau tujuan pulang maksimal 255 karakter.',
            'alasan.required' => 'Alasan izin wajib diisi.',
            'alasan.min' => 'Alasan izin minimal 10 karakter agar dapat diperiksa Musyrifah.',
            'alasan.max' => 'Alasan izin maksimal 2000 karakter.',
            'keterangan.max' => 'Keterangan maksimal 255 karakter.',
            'transportasi.max' => 'Transportasi maksimal 255 karakter.',
            'dokumen.mimes' => 'Bukti pendukung harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen.max' => 'Ukuran bukti pendukung maksimal 5 MB.',
        ]);

        $mulai = Carbon::parse($validated['tanggal_mulai'])->startOfDay();
        $kembali = Carbon::parse($validated['tanggal_selesai'])->startOfDay();
        $durasi = (int) $mulai->diffInDays($kembali) + 1;

        if (! $darurat && $durasi > SuratPerizinan::MAKS_DURASI_PULANG_HARI) {
            throw ValidationException::withMessages([
                'tanggal_selesai' => 'Durasi izin pulang maksimal '.SuratPerizinan::MAKS_DURASI_PULANG_HARI.' hari.',
            ]);
        }

        $path = $request->hasFile('dokumen')
            ? $request->file('dokumen')->store('surat-perizinan/'.$user->id.'/darurat', 'local')
            : null;

        if ($request->hasFile('dokumen') && ! $path) {
            return back()->withErrors(['dokumen' => 'Bukti pendukung gagal disimpan. Silakan coba lagi.'])->withInput();
        }

        try {
            $pengajuan = $suratService->buatPengajuan($user, [
                'jenis' => $darurat ? SuratPerizinan::JENIS_DARURAT : SuratPerizinan::JENIS_PULANG,
                'tanggal_mulai' => $mulai->toDateString(),
                'tanggal_selesai' => $kembali->toDateString(),
                'durasi_hari' => $durasi,
                'lokasi' => trim($validated['lokasi']),
                'alasan' => trim($validated['alasan']),
                'keterangan' => filled($validated['keterangan'] ?? null) ? trim($validated['keterangan']) : null,
                'transportasi' => filled($validated['transportasi'] ?? null) ? trim($validated['transportasi']) : null,
                'dokumen_path' => $path,
                'dokumen_nama' => $request->hasFile('dokumen')
                    ? $request->file('dokumen')->getClientOriginalName()
                    : null,
            ], $darurat
                ? 'Pengajuan izin pulang darurat/khusus dikirim mahasiswa.'
                : 'Pengajuan izin pulang mingguan dikirim mahasiswa ('.$durasi.' hari).');
        } catch (Throwable $exception) {
            if (filled($path)) {
                Storage::disk('local')->delete($path);
            }

            throw $exception;
        }

        return redirect()
            ->route('surat.perizinan.mhs')
            ->with('success', $darurat
                ? 'Pengajuan izin darurat berhasil dikirim untuk diproses khusus oleh Musyrifah.'
                : 'Pengajuan izin pulang berhasil dikirim. Menunggu persetujuan Musyrifah.');
    }

    /**
     * Memastikan hanya akun mahasiswa yang dapat mengajukan izin pulang.
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
}
