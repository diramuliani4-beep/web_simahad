<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Menandai satu notifikasi sebagai sudah dibaca lalu membuka
     * halaman terkait.
     */
    public function baca(Request $request, Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->user_id === $request->user()?->id, 403);

        if (! $notifikasi->sudahDibaca()) {
            $notifikasi->update(['dibaca_pada' => now()]);
        }

        return filled($notifikasi->url)
            ? redirect($notifikasi->url)
            : back();
    }

    /**
     * Menandai seluruh notifikasi milik pengguna sebagai sudah dibaca.
     */
    public function bacaSemua(Request $request): RedirectResponse
    {
        $request->user()
            ?->notifikasis()
            ->belumDibaca()
            ->update(['dibaca_pada' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
