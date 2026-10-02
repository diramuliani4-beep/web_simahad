<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'login.required' => 'Email atau NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($credentials['login']);
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Determine if login is via email or NIM
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';

        // Attempt login with determined field
        if (Auth::attempt([$fieldType => $loginInput, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('status', 'Selamat datang kembali, '.Auth::user()->name.'!');
        }

        // If fieldType was nim but not matched, let's also try email just in case
        if ($fieldType === 'nim') {
            if (Auth::attempt(['email' => $loginInput, 'password' => $password], $remember)) {
                $request->session()->regenerate();

                return redirect()->intended(route('dashboard'))
                    ->with('status', 'Selamat datang kembali, '.Auth::user()->name.'!');
            }
        }

        return back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors([
                'login' => 'Email/NIM atau password yang Anda masukkan salah.',
            ]);
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

        return view('dashboard');
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
