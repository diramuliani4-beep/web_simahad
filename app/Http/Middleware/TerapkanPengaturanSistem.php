<?php

namespace App\Http\Middleware;

use App\Models\PengaturanSistem;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Menerapkan pengaturan sistem pada setiap permintaan web sehingga
 * perubahan dari admin (nama sistem, nama institusi, durasi sesi)
 * langsung terpakai tanpa perlu mengubah file konfigurasi.
 */
class TerapkanPengaturanSistem
{
    public function handle(Request $request, Closure $next): Response
    {
        $pengaturan = $this->pengaturan();

        if ($pengaturan) {
            config([
                'app.name' => $pengaturan->nama_sistem,
                'session.lifetime' => $pengaturan->durasi_sesi,
            ]);

            View::share([
                'namaSistem' => $pengaturan->nama_sistem,
                'namaInstitusi' => $pengaturan->nama_institusi,
            ]);
        }

        return $next($request);
    }

    /**
     * Tabel pengaturan mungkin belum ada saat migrasi pertama dijalankan,
     * sehingga kegagalan pengambilan data tidak boleh menggagalkan request.
     */
    private function pengaturan(): ?PengaturanSistem
    {
        try {
            return Schema::hasTable('pengaturan_sistems')
                ? PengaturanSistem::aktif()
                : null;
        } catch (Throwable) {
            return null;
        }
    }
}
