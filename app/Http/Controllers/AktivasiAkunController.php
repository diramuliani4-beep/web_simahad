<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\AktivasiAkunService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Aktivasi akun staf melalui tautan bertoken.
 *
 * Halaman ini dapat diakses tanpa login karena pemilik akun baru
 * belum memiliki kata sandi sampai proses aktivasi selesai.
 */
class AktivasiAkunController extends Controller
{
    public function __construct(
        private readonly AktivasiAkunService $aktivasi,
    ) {}

    public function form(string $token): View
    {
        return view('auth.aktivasi', [
            'token' => $token,
            'pengguna' => $this->aktivasi->cariPengguna($token),
            'berlakuJam' => AktivasiAkunService::BERLAKU_JAM,
        ]);
    }

    public function simpan(Request $request, string $token): RedirectResponse
    {
        $pengguna = $this->aktivasi->cariPengguna($token);

        if (! $pengguna) {
            return redirect()
                ->route('login')
                ->with('error', 'Tautan aktivasi tidak valid atau sudah kedaluwarsa. Silakan minta Admin Sistem mengirim ulang tautan aktivasi.');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $this->aktivasi->aktifkan($pengguna, (string) $request->input('password'));

        ActivityLog::create([
            'user_id' => $pengguna->getKey(),
            'user_name' => $pengguna->name,
            'user_email' => $pengguna->email,
            'user_role' => $pengguna->role,
            'activity' => 'activate_user',
            'description' => 'Aktivasi akun mandiri melalui tautan email.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Auth::login($pengguna);
        $request->session()->regenerate();

        return app(AuthController::class)->redirectToRoleDashboard()
            ->with('success', 'Akun Anda berhasil diaktifkan. Kata sandi baru sudah tersimpan.');
    }
}
