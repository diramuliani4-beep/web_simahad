<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PengaturanSistem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPengaturanSistemController extends Controller
{
    /**
     * Menampilkan konfigurasi umum aplikasi.
     */
    public function index(Request $request): View
    {
        $this->pastikanAdmin($request);

        return view('Admin.pengaturan_sistem', [
            'pengaturan' => PengaturanSistem::aktif(),
        ]);
    }

    /**
     * Menyimpan perubahan konfigurasi umum aplikasi.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);

        $validated = $request->validate([
            'nama_sistem' => ['required', 'string', 'max:60'],
            'nama_institusi' => ['required', 'string', 'max:150'],
            'durasi_sesi' => ['required', 'integer', 'min:5', 'max:1440'],
            'notifikasi_pengumuman' => ['required', 'boolean'],
            'notifikasi_pembelajaran' => ['required', 'boolean'],
            'notifikasi_pembayaran' => ['required', 'boolean'],
        ], [
            'nama_sistem.required' => 'Nama sistem wajib diisi.',
            'nama_institusi.required' => 'Nama institusi wajib diisi.',
            'durasi_sesi.required' => 'Durasi sesi wajib diisi.',
            'durasi_sesi.min' => 'Durasi sesi minimal 5 menit.',
            'durasi_sesi.max' => 'Durasi sesi maksimal 1440 menit (24 jam).',
        ]);

        $pengaturan = PengaturanSistem::aktif();

        $pengaturan->update([
            'nama_sistem' => $validated['nama_sistem'],
            'nama_institusi' => $validated['nama_institusi'],
            'durasi_sesi' => (int) $validated['durasi_sesi'],
            'notifikasi_pengumuman' => $request->boolean('notifikasi_pengumuman'),
            'notifikasi_pembelajaran' => $request->boolean('notifikasi_pembelajaran'),
            'notifikasi_pembayaran' => $request->boolean('notifikasi_pembayaran'),
        ]);

        $this->catat($request, $pengaturan);

        return redirect()
            ->route('admin.pengaturan-sistem')
            ->with('success', 'Pengaturan sistem berhasil disimpan dan langsung diterapkan.');
    }

    public function create(): View
    {
        return view('Admin.tambah_pengguna');
    }

    /**
     * Hanya admin yang boleh mengubah konfigurasi sistem.
     */
    private function pastikanAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }

    private function catat(Request $request, PengaturanSistem $pengaturan): void
    {
        $admin = $request->user();

        ActivityLog::create([
            'user_id' => $admin?->getKey(),
            'user_name' => $admin?->name,
            'user_email' => $admin?->email,
            'user_role' => $admin?->role,
            'activity' => 'Pengaturan Sistem',
            'description' => ($admin?->name ?? 'Admin').' memperbarui pengaturan sistem: '
                ."nama sistem {$pengaturan->nama_sistem}, "
                ."durasi sesi {$pengaturan->durasi_sesi} menit, "
                .'notifikasi pengumuman '.($pengaturan->notifikasi_pengumuman ? 'aktif' : 'nonaktif').', '
                .'notifikasi pembelajaran '.($pengaturan->notifikasi_pembelajaran ? 'aktif' : 'nonaktif').', '
                .'notifikasi pembayaran '.($pengaturan->notifikasi_pembayaran ? 'aktif' : 'nonaktif').'.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
