<?php

namespace App\Http\Middleware;

use App\Models\Pendaftaran;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PastikanPendaftaranDisetujui
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user && strtolower((string) $user->role) === 'mahasiswa',
            403
        );

        $pendaftaran = Pendaftaran::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        if ($pendaftaran?->status === 'disetujui') {
            return $next($request);
        }

        $pesan = match ($pendaftaran?->status) {
            'menunggu' =>
                "Pendaftaran Ma'had Anda masih menunggu persetujuan Musyrifah. "
                . "Anda belum dapat melakukan pengajuan sebelum pendaftaran disetujui.",

            'perlu_perbaikan' =>
                "Pendaftaran Ma'had Anda memerlukan perbaikan. "
                . "Silakan perbaiki data pendaftaran sesuai arahan Musyrifah.",

            'ditolak' =>
                "Pendaftaran Ma'had Anda telah ditolak oleh Musyrifah. "
                . "Anda belum dapat melakukan pengajuan. "
                . "Silakan lihat informasi pendaftaran Anda.",

            null =>
                "Anda belum melakukan pendaftaran Ma'had. "
                . "Silakan selesaikan pendaftaran terlebih dahulu.",

            default =>
                "Pendaftaran Ma'had Anda belum disetujui oleh Musyrifah. "
                . "Pengajuan belum dapat dilakukan.",
        };

        return redirect()
            ->back()
            ->with('error', $pesan);
    }
}