<?php

namespace App\Support;

/**
 * Pemeriksaan kesiapan konfigurasi pengiriman email SIMAHAD.
 *
 * Pengembangan lokal memakai Mailpit (SMTP tanpa autentikasi), sedangkan
 * produksi memakai server SMTP yang membutuhkan kredensial. Pemeriksaan ini
 * dipakai bersama oleh alur aktivasi akun dan perintah `php artisan email:uji`
 * agar Admin menerima alasan kegagalan yang sama. Semua pesan di sini aman
 * ditampilkan: tidak pernah memuat kata sandi SMTP atau token aktivasi.
 */
class KonfigurasiEmail
{
    /** Nama mailer aktif pada config/mail.php (misalnya "smtp" atau "log"). */
    public static function mailer(): string
    {
        return (string) config('mail.default');
    }

    /** Nama transport dari mailer aktif, bukan nama mailer itu sendiri. */
    public static function transport(?string $mailer = null): string
    {
        $mailer ??= self::mailer();

        return (string) config("mail.mailers.{$mailer}.transport", $mailer);
    }

    /** True bila mailer hanya menulis pesan ke aplikasi sehingga email tidak terkirim. */
    public static function hanyaMenyimpanDiAplikasi(?string $mailer = null): bool
    {
        return in_array(strtolower(self::transport($mailer)), ['log', 'array'], true);
    }

    /** True bila SMTP menuju server lokal (Mailpit) yang tidak memakai autentikasi. */
    public static function smtpLokal(?string $mailer = null): bool
    {
        $mailer ??= self::mailer();

        return in_array(strtolower((string) config("mail.mailers.{$mailer}.host")), ['127.0.0.1', 'localhost', '::1', 'mailpit'], true);
    }

    /** Alamat pengirim yang dipakai seluruh email SIMAHAD. */
    public static function pengirim(): string
    {
        return trim((string) config('mail.from.address'));
    }

    /** True bila alamat pengirim masih kosong atau memakai domain contoh. */
    public static function pengirimBelumDiatur(): bool
    {
        $pengirim = strtolower(self::pengirim());

        return $pengirim === '' || str_ends_with($pengirim, '@example.com');
    }

    /**
     * Alasan konfigurasi belum siap mengirim email, atau null bila sudah siap.
     */
    public static function masalah(?string $mailer = null): ?string
    {
        $mailer ??= self::mailer();

        if (self::hanyaMenyimpanDiAplikasi($mailer)) {
            return 'MAIL_MAILER masih "'.$mailer.'" sehingga email hanya ditulis ke log aplikasi, bukan dikirim. Atur MAIL_MAILER=smtp pada berkas .env.';
        }

        if (self::transport($mailer) === 'smtp'
            && ! self::smtpLokal($mailer)
            && (blank(config("mail.mailers.{$mailer}.username")) || blank(config("mail.mailers.{$mailer}.password")))) {
            return 'MAIL_USERNAME dan MAIL_PASSWORD belum diisi pada berkas .env untuk server SMTP tujuan.';
        }

        if (self::pengirimBelumDiatur()) {
            return 'MAIL_FROM_ADDRESS belum diisi. Gunakan alamat pengirim yang valid, misalnya noreply@simahad.test untuk Mailpit.';
        }

        return null;
    }
}
