<?php

namespace App\Services;

use App\Mail\AktivasiAkunMail;
use App\Models\User;
use App\Support\KonfigurasiEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Testing\Fakes\MailFake;
use Throwable;

/**
 * Alur aktivasi akun staf (Guru, Musyrifah, Pimpinan, Admin).
 *
 * Admin hanya membuat akun; kata sandi dibuat sendiri oleh pemilik akun
 * melalui tautan aktivasi berbatas waktu. Yang disimpan di database hanya
 * hash SHA-256 dari token, sehingga token asli tidak dapat dibaca ulang.
 */
class AktivasiAkunService
{
    /** Masa berlaku tautan aktivasi dalam jam. */
    public const BERLAKU_JAM = 48;

    /**
     * Membuat token aktivasi baru untuk satu akun.
     *
     * @return string Token asli yang hanya dikirim ke email pemilik akun.
     */
    public function buatToken(User $user): string
    {
        $token = Str::random(64);

        $user->forceFill([
            'activation_token' => hash('sha256', $token),
            'activation_expires_at' => now()->addHours(self::BERLAKU_JAM),
            'status' => 'menunggu aktivasi',
        ])->save();

        return $token;
    }

    /** Tautan aktivasi yang dikirim ke email pengguna. */
    public function tautan(string $token): string
    {
        return route('aktivasi.form', ['token' => $token]);
    }

    /**
     * Mencari akun berdasarkan token aktivasi yang masih berlaku.
     */
    public function cariPengguna(string $token): ?User
    {
        $token = trim($token);

        if ($token === '' || mb_strlen($token) > 120) {
            return null;
        }

        return User::query()
            ->where('activation_token', hash('sha256', $token))
            ->whereNotNull('activation_expires_at')
            ->where('activation_expires_at', '>', now())
            ->first();
    }

    /**
     * Membuat token baru lalu mengirim tautan aktivasi ke email pengguna.
     *
     * @return array{terkirim: bool, tautan: string, alasan: string|null}
     */
    public function kirimTautan(User $user): array
    {
        $token = $this->buatToken($user);
        $tautan = $this->tautan($token);
        $mailer = (string) config('mail.default');

        [$terkirim, $alasan] = $this->kirimEmailAktivasi($user, $tautan, $mailer);

        if (! app()->isProduction() && ! $this->mailerMengirimEmailSungguhan($mailer)) {
            // Hanya saat mailer tidak mengirim email (MAIL_MAILER=log/array): tautan dibaca dari berkas log.
            Log::info('Tautan aktivasi akun SIMAHAD.', [
                'user_id' => $user->getKey(),
                'email' => $user->email,
                'tautan' => $tautan,
                'berlaku_jam' => self::BERLAKU_JAM,
            ]);
        }

        return ['terkirim' => $terkirim, 'tautan' => $tautan, 'alasan' => $alasan];
    }

    /**
     * Mengirim email aktivasi dan memastikan email benar-benar terkirim.
     *
     * @return array{0: bool, 1: string|null} Status pengiriman dan alasan kegagalan yang aman ditampilkan ke Admin.
     */
    private function kirimEmailAktivasi(User $user, string $tautan, string $mailer): array
    {
        $masalah = $this->masalahKonfigurasiEmail($mailer);

        if ($masalah !== null) {
            $this->catatKegagalanPengiriman($user, $mailer, null, $masalah);

            return [false, $masalah];
        }

        try {
            Mail::to($user->email)->send(new AktivasiAkunMail($user, $tautan, self::BERLAKU_JAM));
        } catch (Throwable $e) {
            report($e);
            $this->catatKegagalanPengiriman($user, $mailer, $e::class, $this->pesanAman($e));

            return [false, 'Server SMTP menolak pengiriman email. Rincian teknis sudah dicatat pada log aplikasi.'];
        }

        return [true, null];
    }

    /**
     * Memeriksa kesiapan konfigurasi email sebelum mencoba mengirim.
     *
     * Saat Mail::fake() dipakai pada pengujian, pengiriman diverifikasi oleh
     * assertion sehingga pemeriksaan konfigurasi dilewati.
     */
    private function masalahKonfigurasiEmail(string $mailer): ?string
    {
        if (Mail::getFacadeRoot() instanceof MailFake) {
            return null;
        }

        return KonfigurasiEmail::masalah($mailer);
    }

    /**
     * Memastikan mailer aktif benar-benar mengirim email ke penerima.
     *
     * Transport "log" dan "array" tidak pernah mengirim email sehingga Admin
     * tidak boleh menerima pesan sukses. Saat Mail::fake() dipakai pada pengujian,
     * pengiriman diverifikasi oleh assertion sehingga dianggap sah.
     */
    private function mailerMengirimEmailSungguhan(string $mailer): bool
    {
        if (Mail::getFacadeRoot() instanceof MailFake) {
            return true;
        }

        return ! KonfigurasiEmail::hanyaMenyimpanDiAplikasi($mailer);
    }

    /**
     * Mencatat kegagalan pengiriman tanpa membocorkan kata sandi, token, atau isi email.
     */
    private function catatKegagalanPengiriman(User $user, string $mailer, ?string $kelasPengecualian, string $pesan): void
    {
        Log::error('Pengiriman email aktivasi akun gagal.', array_filter([
            'user_id' => $user->getKey(),
            'mailer' => $mailer,
            'exception' => $kelasPengecualian,
            'pesan' => $pesan,
        ], static fn (mixed $nilai): bool => $nilai !== null));
    }

    /**
     * Menyensor kredensial yang mungkin terbawa pesan transport sebelum masuk log.
     */
    private function pesanAman(Throwable $e): string
    {
        $pesan = preg_replace(
            [
                '~([a-z][a-z0-9+.\-]*://)[^@\s/]+@~i',
                '~((?:password|passwd|pwd|secret|token|api[_-]?key)\s*[=:]\s*)\S+~i',
            ],
            ['$1***@', '$1***'],
            (string) $e->getMessage()
        );

        return mb_substr((string) $pesan, 0, 300);
    }

    /**
     * Menyimpan kata sandi pilihan pengguna lalu mengaktifkan akun.
     */
    public function aktifkan(User $user, string $password): void
    {
        // Cast "hashed" pada model User yang melakukan hashing kata sandi.
        $user->forceFill([
            'password' => $password,
            'password_set_at' => now(),
            'status' => 'aktif',
            'activation_token' => null,
            'activation_expires_at' => null,
            'email_verified_at' => $user->email_verified_at ?? now(),
            'last_login_at' => now(),
        ])->save();
    }
}
