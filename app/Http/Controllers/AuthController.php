<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\JadwalPembelajaran;
use App\Models\Kamar;
use App\Models\PengajuanKamar;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectToRoleDashboard();
        }

        return view('auth.login', [
            'googleLoginSiap' => $this->googleLoginTerkonfigurasi(),
        ]);
    }

    /**
     * Proses login pengguna.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'nim';

        $loginData = [
            $loginField => $credentials['login'],
            'password' => $credentials['password'],
        ];

        if (! Auth::attempt($loginData)) {
            return back()
                ->withErrors([
                    'login' => 'Email/NIM atau kata sandi yang Anda masukkan salah.',
                ])
                ->onlyInput('login');
        }

        $user = Auth::user();

        // Akun belum diverifikasi admin
        if ($user->status === 'pending') {
            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda belum diverifikasi oleh admin. Silakan tunggu persetujuan admin sebelum login.');
        }

        // Akun ditolak admin
        if ($user->status === 'rejected') {
            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda ditolak oleh admin. Silakan hubungi admin Ma’had untuk informasi lebih lanjut.');
        }

        // Akun sudah disetujui
        $request->session()->regenerate();

        return $this->redirectToRoleDashboard();
    }

    /**
     * Mengarahkan pengguna ke halaman persetujuan akun Google.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectToRoleDashboard();
        }

        if (! $this->googleLoginTerkonfigurasi()) {
            return redirect()
                ->route('login')
                ->with('error', 'Login dengan Google belum dikonfigurasi. Hubungi admin Ma’had untuk mengaktifkannya.');
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('login')
                ->with('error', 'Tidak dapat terhubung ke Google. Silakan coba lagi.');
        }
    }

    /**
     * Memproses callback Google: validasi email, cocokkan akun, lalu login.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectToRoleDashboard();
        }

        if (! $this->googleLoginTerkonfigurasi()) {
            return redirect()
                ->route('login')
                ->with('error', 'Login dengan Google belum dikonfigurasi. Hubungi admin Ma’had untuk mengaktifkannya.');
        }

        if ($request->filled('error')) {
            return redirect()
                ->route('login')
                ->with('error', 'Login dengan Google dibatalkan atau ditolak oleh Google.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('login')
                ->with('error', 'Autentikasi Google gagal. Silakan coba lagi.');
        }

        $email = mb_strtolower(trim((string) $googleUser->getEmail()));

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return redirect()
                ->route('login')
                ->with('error', 'Akun Google tidak memberikan alamat email yang valid sehingga login tidak dapat dilanjutkan.');
        }

        // Pastikan email benar-benar sudah diverifikasi oleh penyedia Google.
        if (data_get($googleUser->getRaw(), 'email_verified', true) === false) {
            return redirect()
                ->route('login')
                ->with('error', 'Email Google Anda belum terverifikasi. Verifikasi email terlebih dahulu, lalu coba lagi.');
        }

        // Cocokkan akun SIMAHAD berdasarkan email tanpa membedakan huruf besar/kecil.
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('error', 'Email '.$email.' belum terdaftar di SIMAHAD. Silakan daftar terlebih dahulu, lalu tunggu verifikasi admin.')
                ->withInput(['login' => $email]);
        }

        // Akun belum diverifikasi admin
        if ($user->status === 'pending') {
            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda belum diverifikasi oleh admin. Silakan tunggu persetujuan admin sebelum login.');
        }

        // Akun ditolak admin
        if ($user->status === 'rejected') {
            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda ditolak oleh admin. Silakan hubungi admin Ma’had untuk informasi lebih lanjut.');
        }

        // Login memakai akun SIMAHAD yang sudah cocok, bukan membuat akun baru.
        Auth::login($user, true);
        $request->session()->regenerate();

        return $this->redirectToRoleDashboard();
    }

    /**
     * Memastikan kredensial Google OAuth sudah tersedia di environment.
     */
    private function googleLoginTerkonfigurasi(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'));
    }

    /**
     * Menentukan dashboard sesuai role pengguna.
     */
    protected function redirectToRoleDashboard(): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $role = strtolower(trim((string) $user->role));

        $routes = [
            'mahasiswa' => 'dashboard',
            'musyrifah' => 'musyrifah.dashboard',
            'admin' => 'admin.dashboard_admin',
            'guru' => 'guru.dashboard',
            'pimpinan' => 'pimpinan.dashboard',
        ];

        $route = $routes[$role] ?? 'dashboard';

        return redirect()->route($route);
    }

    /**
     * Memastikan pengguna memiliki role yang sesuai.
     */
    protected function authorizeRole(string $role): ?RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = strtolower(trim((string) Auth::user()->role));

        if ($userRole !== $role) {
            return $this->redirectToRoleDashboard()
                ->with(
                    'error',
                    'Anda tidak memiliki akses ke halaman tersebut.'
                );
        }

        return null;
    }

    /**
     * Menampilkan formulir pendaftaran akun.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectToRoleDashboard();
        }

        return view('auth.register');
    }

    /**
     * Memproses pendaftaran akun baru.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:20', 'unique:users,nim'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar dalam sistem.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Simpan akun dengan status menunggu verifikasi
        $user = User::create([
            'name' => $validated['name'],
            'nim' => $validated['nim'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'pending',
        ]);

        // Tidak melakukan login otomatis
        return redirect()
            ->route('login')
            ->with(
                'status',
                'Pendaftaran berhasil! Akun Anda sedang menunggu verifikasi admin. Silakan tunggu persetujuan sebelum login.'
            );
    }

    /**
     * Menampilkan formulir lupa kata sandi.
     */
    public function showForgotPasswordForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectToRoleDashboard();
        }

        return view('auth.forgot-password');
    }

    /**
     * Memproses perubahan kata sandi.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'identifier.required' => 'Email atau NIM wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $identifier = trim($validated['identifier']);

        $user = User::where('email', $identifier)
            ->orWhere('nim', $identifier)
            ->first();

        if (! $user) {
            return back()
                ->withInput($request->only('identifier'))
                ->withErrors([
                    'identifier' => 'Akun dengan Email atau NIM tersebut tidak ditemukan.',
                ]);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('login')
            ->with(
                'status',
                'Kata sandi untuk akun '.$user->name.
                ' berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.'
            );
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Anda telah berhasil keluar.');
    }

    /**
     * Dashboard Mahasiswa.
     *
     * Data ringkasan dashboard disusun oleh MahasiswaDashboardController
     * agar route dan alur login yang sudah ada tetap sama.
     */
    public function dashboard(): View|RedirectResponse
    {
        return app(MahasiswaDashboardController::class)->index(request());
    }

    /**
     * Dashboard Musyrifah.
     */
    public function dashboardMusyrifah(): View|RedirectResponse
    {
        $redirect = $this->authorizeRole('musyrifah');

        if ($redirect) {
            return $redirect;
        }

        return view('musyrifah.dashboard');
    }

    /**
     * Dashboard Admin.
     */
    public function dashboardAdmin()
    {
        $user = auth()->user();

        // Menghitung total seluruh pengguna
        $totalPengguna = User::count();

        // Menghitung akun aktif
        $akunAktif = User::where('status', 'aktif')->count();

        // Menghitung akun yang menunggu aktivasi
        $menungguAktivasi = User::where(
            'status',
            'menunggu aktivasi'
        )->count();

        // Menghitung akun nonaktif
        $akunNonaktif = User::where(
            'status',
            'nonaktif'
        )->count();

        // Mengambil 5 aktivitas terbaru
        $aktivitasTerbaru = ActivityLog::latest()
            ->take(5)
            ->get();

        return view('Admin.dashboard_admin', compact(
            'user',
            'totalPengguna',
            'akunAktif',
            'menungguAktivasi',
            'akunNonaktif',
            'aktivitasTerbaru'
        ));
    }

    /**
     * Dashboard Guru dengan ringkasan penugasan yang sesungguhnya.
     */
    public function dashboardGuru(): View|RedirectResponse
    {
        $redirect = $this->authorizeRole('guru');

        if ($redirect) {
            return $redirect;
        }

        $guru = Auth::user();
        $jadwal = JadwalPembelajaran::jadwalUntuk($guru);
        $penugasan = JadwalPembelajaran::penugasanUntuk($guru);

        return view('guru.dashboard', [
            'guru' => $guru,
            'jumlahMapel' => $penugasan->pluck('mata_pelajaran_id')->filter()->unique()->count(),
            'jumlahKelompok' => $penugasan->pluck('kelompok')->filter()->unique()->count(),
            'jadwalMingguIni' => $jadwal->count(),
            'absensiPerluDiisi' => $jadwal->filter(
                fn (JadwalPembelajaran $item) => $item->absensis()->count() === 0
            )->count(),
            'jadwalMendatang' => $jadwal->take(4),
            'jumlahTugas' => Tugas::query()->where('user_id', $guru->id)->count(),
        ]);
    }

    /**
     * Dashboard Pimpinan.
     */
    public function dashboardPimpinan(): View|RedirectResponse
    {
        $redirect = $this->authorizeRole('pimpinan');

        if ($redirect) {
            return $redirect;
        }

        return view('pimpinan.dashboard');
    }

    /**
     * Menampilkan informasi kamar mahasiswa beserta pengajuan penempatannya.
     */
    public function kamar(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        abort_unless(strtolower((string) $user->role) === 'mahasiswa', 403);

        // Kamar yang sudah ditetapkan Musyrifah untuk mahasiswa ini
        $kamarSaya = $user->kamar;

        $pengajuanSaya = PengajuanKamar::with('kamar')
            ->where('user_id', $user->id)
            ->where('jenis', PengajuanKamar::JENIS_PENEMPATAN)
            ->first();

        // Pengajuan pindah kamar terakhir milik mahasiswa ini
        $pengajuanPindah = PengajuanKamar::with(['kamar', 'kamarAsal'])
            ->where('user_id', $user->id)
            ->where('jenis', PengajuanKamar::JENIS_PINDAH)
            ->latest('id')
            ->first();

        // Daftar kamar mengikuti data yang dikelola Musyrifah pada menu data kamar
        $daftarKamar = Kamar::query()
            ->with('fotos')
            ->withCount([
                'users',
                'pengajuanKamars as pengajuan_menunggu_count' => fn ($query) => $query
                    ->where('status', PengajuanKamar::STATUS_MENUNGGU)
                    ->where('user_id', '!=', $user->id),
            ])
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get()
            ->map(function (Kamar $kamar): array {
                $sisa = max(
                    0,
                    (int) $kamar->kapasitas - (int) $kamar->users_count - (int) $kamar->pengajuan_menunggu_count
                );

                return [
                    'id' => $kamar->id,
                    'nomor' => $kamar->nomor_kamar,
                    'nama' => $kamar->nama_kamar,
                    'gedung' => $kamar->gedung ?: 'Gedung A',
                    'lantai' => (int) $kamar->lantai,
                    'kapasitas' => (int) $kamar->kapasitas,
                    'terisi' => (int) $kamar->users_count,
                    'sisa' => $sisa,
                    'status' => $kamar->status,
                    'bisa_diajukan' => $kamar->bisaDiajukan() && $sisa > 0,
                    'fasilitas' => $kamar->fasilitas,
                    'foto_url' => $kamar->urlFotoUtama(),
                    // Galeri foto kamar; foto lama (kamars.foto) tetap tampil sebagai cadangan.
                    'fotos' => $kamar->fotos->isNotEmpty()
                        ? $kamar->fotos->map(fn ($foto) => [
                            'id' => $foto->id,
                            'url' => $foto->url,
                        ])->values()->all()
                        : ($kamar->foto ? [['id' => 0, 'url' => Storage::url($kamar->foto)]] : []),
                ];
            })
            ->all();

        return view('mahasiswa.kamar', compact(
            'kamarSaya',
            'pengajuanSaya',
            'pengajuanPindah',
            'daftarKamar'
        ));
    }

    /**
     * Memproses pengajuan kamar mahasiswa untuk disetujui Musyrifah.
     */
    public function pilihKamar(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && strtolower((string) $user->role) === 'mahasiswa', 403);

        $validated = $request->validate([
            'kamar_id' => ['required', 'integer', 'exists:kamars,id'],
        ], [
            'kamar_id.required' => 'Silakan pilih kamar terlebih dahulu.',
            'kamar_id.exists' => 'Kamar yang dipilih tidak ditemukan.',
        ]);

        if ($user->kamar_id) {
            return redirect()
                ->route('kamar')
                ->with('error', 'Kamu sudah memiliki penempatan kamar.');
        }

        try {
            PengajuanKamar::ajukan($user, Kamar::findOrFail($validated['kamar_id']));
        } catch (\Throwable $e) {
            return redirect()
                ->route('kamar')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('kamar')
            ->with(
                'success',
                'Pengajuan kamar berhasil dikirim. Silakan menunggu persetujuan Musyrifah.'
            );
    }

    /**
     * Memproses pengajuan pindah kamar tanpa mengubah penempatan yang aktif.
     */
    public function pindahKamar(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && strtolower((string) $user->role) === 'mahasiswa', 403);

        $validated = $request->validate([
            'kamar_id' => ['required', 'integer', 'exists:kamars,id'],
            'alasan' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'kamar_id.required' => 'Silakan pilih kamar tujuan terlebih dahulu.',
            'kamar_id.exists' => 'Kamar tujuan yang dipilih tidak ditemukan.',
            'alasan.required' => 'Alasan perpindahan wajib diisi.',
            'alasan.min' => 'Alasan perpindahan minimal 10 karakter agar dapat diperiksa Musyrifah.',
            'alasan.max' => 'Alasan perpindahan maksimal 1000 karakter.',
        ]);

        if (! $user->kamar_id) {
            return redirect()
                ->route('kamar')
                ->with('error', 'Kamu belum memiliki penempatan kamar. Silakan ajukan kamar terlebih dahulu.');
        }

        try {
            PengajuanKamar::ajukanPindah(
                $user,
                Kamar::findOrFail($validated['kamar_id']),
                trim($validated['alasan'])
            );
        } catch (\Throwable $e) {
            return redirect()
                ->route('kamar')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('kamar')
            ->with(
                'success',
                'Pengajuan pindah kamar berhasil dikirim. Penempatan kamu tetap pada kamar saat ini sampai Musyrifah menyetujui pengajuan.'
            );
    }
}
