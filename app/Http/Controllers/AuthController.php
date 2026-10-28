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
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
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

        // Check fields in preferred order based on input format
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $fieldsToTry = ['email', 'nim', 'name'];
        } elseif (ctype_digit($loginInput)) {
            $fieldsToTry = ['nim', 'email', 'name'];
        } else {
            $fieldsToTry = ['name', 'email', 'nim'];
        }

        foreach ($fieldsToTry as $field) {
            if (Auth::attempt([$field => $loginInput, 'password' => $password], $remember)) {
                $request->session()->regenerate();

                return redirect()->intended(route('dashboard'))
                    ->with('status', 'Selamat datang kembali, '.Auth::user()->name.'!');
            }
        }

        return back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors([
                'login' => 'Email/Username atau kata sandi yang Anda masukkan salah.',
            ]);
    }

    /**
     * Show user registration form.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle new user registration.
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

        return redirect()->route('dashboard')
            ->with('status', 'Pendaftaran berhasil! Selamat datang di SIMAHAD, '.$user->name.'!');
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPasswordForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.forgot-password');
    }

    /**
     * Handle password reset request.
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
            ->with('status', 'Kata sandi untuk akun '.$user->name.' berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }

    /**
     * Log the user out of the application.
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
     * Show user dashboard after login.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $kamar = $user->kamar;

        return view('dashboard', compact('user', 'kamar'));
    }
    
public function dashboardAdmin()
{
    $user = Auth::user();
    $kamar = $user->kamar;

    return view('Admin.dashboard_admin', compact('user', 'kamar'));
}

    /**
     * Show kamar (rooms) selection page.
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
     * Handle room selection.
     */
    public function pilihKamar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_name' => ['required', 'string'],
        ]);

        return redirect()->route('kamar')->with('status', 'Berhasil memilih '.$validated['room_name'].'!');
    }
}
