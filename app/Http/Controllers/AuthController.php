<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

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

        return view('auth.login');
    }

    /**
     * Proses login pengguna.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($credentials['login']);
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $fieldsToTry = ['email', 'nim', 'name'];
        } elseif (ctype_digit($loginInput)) {
            $fieldsToTry = ['nim', 'email', 'name'];
        } else {
            $fieldsToTry = ['name', 'email', 'nim'];
        }

        foreach ($fieldsToTry as $field) {
            if (Auth::attempt([
                $field => $loginInput,
                'password' => $password,
            ], $remember)) {
                $request->session()->regenerate();

                return $this->redirectToRoleDashboard()
                    ->with(
                        'status',
                        'Selamat datang kembali, '.Auth::user()->name.'!'
                    );
            }
        }

        return back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors([
                'login' => 'Email/Username atau kata sandi yang Anda masukkan salah.',
            ]);
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
            'admin' => 'admin.dashboard',
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

        $user = User::create([
            'name' => $validated['name'],
            'nim' => $validated['nim'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectToRoleDashboard()
            ->with(
                'status',
                'Pendaftaran berhasil! Selamat datang di SIMAHAD, '.$user->name.'!'
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
     * Dashboard Mahasiswa yang lama.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $role = strtolower(trim((string) $user->role));

        if ($role !== 'mahasiswa') {
            return $this->redirectToRoleDashboard();
        }

        $kamar = $user->kamar;

        return view('dashboard', compact('user', 'kamar'));
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
    public function dashboardAdmin(): View|RedirectResponse
    {
        $redirect = $this->authorizeRole('admin');

        if ($redirect) {
            return $redirect;
        }
        $user = Auth::user();
        $kamar =  $user->kamar;

        return view('admin.dashboard', compact('user','kamar'));
    }

    /**
     * Dashboard Guru.
     */
    public function dashboardGuru(): View|RedirectResponse
    {
        $redirect = $this->authorizeRole('guru');

        if ($redirect) {
            return $redirect;
        }

        return view('guru.dashboard');
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
     * Menampilkan halaman pemilihan kamar.
     */
    public function kamar(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $rooms = [
            [
                'id' => 101,
                'name' => 'Kamar 101',
                'lantai' => 1,
                'kapasitas' => 4,
                'status' => 'Tersedia',
            ],
            [
                'id' => 102,
                'name' => 'Kamar 102',
                'lantai' => 1,
                'kapasitas' => 4,
                'status' => 'Penuh',
            ],
            [
                'id' => 103,
                'name' => 'Kamar 103',
                'lantai' => 1,
                'kapasitas' => 4,
                'status' => 'Tersedia',
            ],
            [
                'id' => 201,
                'name' => 'Kamar 201',
                'lantai' => 2,
                'kapasitas' => 4,
                'status' => 'Tersedia',
            ],
            [
                'id' => 202,
                'name' => 'Kamar 202',
                'lantai' => 2,
                'kapasitas' => 4,
                'status' => 'Penuh',
            ],
            [
                'id' => 203,
                'name' => 'Kamar 203',
                'lantai' => 2,
                'kapasitas' => 4,
                'status' => 'Tersedia',
            ],
        ];

        return view('kamar', compact('rooms'));
    }

    /**
     * Memproses pemilihan kamar.
     */
    public function pilihKamar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_name' => ['required', 'string'],
        ]);

        return redirect()->route('kamar')
            ->with(
                'status',
                'Berhasil memilih '.$validated['room_name'].'!'
            );
     }
}
