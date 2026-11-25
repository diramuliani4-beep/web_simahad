<?php

namespace App\Services;

use App\Mail\PengumumanDipublikasikanMail;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Mengirim email pemberitahuan pengumuman yang baru dipublikasikan.
 *
 * Penerima email disamakan dengan penerima notifikasi dalam sistem, yaitu
 * seluruh akun mahasiswa yang terdaftar. Pengiriman memakai mailer Laravel
 * yang dikonfigurasi melalui berkas .env (MAIL_MAILER=smtp dan kredensial
 * MAIL_*), sehingga tidak ada pengaturan SMTP yang ditulis di dalam kode.
 */
class PengumumanEmailService
{
    /**
     * Mengirim pengumuman ke email seluruh mahasiswa penerima.
     *
     * Kegagalan pengiriman tidak membatalkan publikasi pengumuman. Setelah
     * kegagalan pertama, sisa pengiriman dihentikan agar satu permintaan web
     * tidak menunggu batas waktu server SMTP berulang kali.
     *
     * @return int Jumlah email yang berhasil dikirim.
     */
    public function kirim(Pengumuman $pengumuman): int
    {
        if (! $pengumuman->isDipublikasikan()) {
            return 0;
        }

        $terkirim = 0;

        User::query()
            ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select(['id', 'name', 'email'])
            ->chunkById(100, function ($penerima) use ($pengumuman, &$terkirim): bool {
                foreach ($penerima as $mahasiswa) {
                    try {
                        Mail::to($mahasiswa->email)->send(
                            new PengumumanDipublikasikanMail($pengumuman, $mahasiswa)
                        );
                    } catch (Throwable $e) {
                        report($e);
                        Log::error('Pengiriman email pengumuman gagal.', [
                            'pengumuman_id' => $pengumuman->id,
                            'user_id' => $mahasiswa->id,
                            'mailer' => (string) config('mail.default'),
                            'exception' => $e::class,
                            'terkirim' => $terkirim,
                        ]);

                        return false;
                    }

                    $terkirim++;
                }

                return true;
            });

        return $terkirim;
    }
}
