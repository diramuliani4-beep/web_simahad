<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PengaturanSistem extends Model
{
    /** Kunci cache agar pengaturan tidak diambil berulang kali per request. */
    public const KUNCI_CACHE = 'pengaturan_sistem_v2';

    /**
     * Nama tabel ditulis eksplisit agar tidak bergantung pada pluralisasi
     * otomatis Laravel untuk kata "sistem".
     */
    protected $table = 'pengaturan_sistems';

    protected $fillable = [
        'nama_sistem',
        'nama_institusi',
        'logo_path',
        'notifikasi_pengumuman',
        'notifikasi_pembelajaran',
        'notifikasi_pembayaran',
        'durasi_sesi',
        'mail_mode',
        'mail_host',
        'mail_port',
        'mail_scheme',
        'mail_username',
        'mail_password',
        'mail_from_address',
        'mail_from_name',
        'backup_retensi',
    ];

    protected function casts(): array
    {
        return [
            'notifikasi_pengumuman' => 'boolean',
            'notifikasi_pembelajaran' => 'boolean',
            'notifikasi_pembayaran' => 'boolean',
            'durasi_sesi' => 'integer',
            'mail_port' => 'integer',
            // Kata sandi SMTP disimpan terenkripsi dengan APP_KEY aplikasi.
            'mail_password' => 'encrypted',
            'backup_retensi' => 'integer',
        ];
    }

    /**
     * Mengambil baris pengaturan aktif. Data disimpan di cache sebagai
     * array atribut (bukan objek model) agar aman saat di-unserialize oleh
     * driver cache seperti database atau file.
     */
    public static function aktif(): self
    {
        $atribut = Cache::rememberForever(self::KUNCI_CACHE, fn (): array => self::ambilDariDatabase());

        if (! is_array($atribut)) {
            Cache::forget(self::KUNCI_CACHE);

            $atribut = self::ambilDariDatabase();
        }

        return (new self)->newFromBuilder($atribut);
    }

    /**
     * Mengambil (atau membuat) baris pengaturan di database.
     *
     * @return array<string, mixed>
     */
    private static function ambilDariDatabase(): array
    {
        return self::query()->firstOrCreate([], [
            'nama_sistem' => 'SIMAHAD',
            'nama_institusi' => 'Ma’had Al-Jami’ah Tuanku Imam Bonjol',
            'notifikasi_pengumuman' => true,
            'notifikasi_pembelajaran' => true,
            'notifikasi_pembayaran' => true,
            'durasi_sesi' => 120,
        ])->getAttributes();
    }

    /**
     * Setiap perubahan pengaturan menghapus cache agar nilai terbaru
     * langsung dipakai pada request berikutnya.
     */
    protected static function booted(): void
    {
        static::saved(static fn () => Cache::forget(self::KUNCI_CACHE));
        static::deleted(static fn () => Cache::forget(self::KUNCI_CACHE));
    }

    /**
     * Menentukan apakah notifikasi dengan tipe tertentu boleh dikirim.
     */
    public function notifikasiAktif(string $tipe): bool
    {
        return match ($tipe) {
            Notifikasi::TIPE_PENGUMUMAN_BARU => $this->notifikasi_pengumuman,
            Notifikasi::TIPE_PEMBAYARAN => $this->notifikasi_pembayaran,
            default => $this->notifikasi_pembelajaran,
        };
    }

    /**
     * Alamat logo aplikasi: berkas unggahan Admin bila ada, jika tidak logo bawaan.
     */
    public function urlLogo(): string
    {
        return filled($this->logo_path)
            ? asset('storage/'.$this->logo_path)
            : asset('images/logo_uin.png');
    }

    /**
     * True bila pengiriman email memakai server SMTP yang diatur dari halaman
     * pengaturan, bukan konfigurasi .env/Mailpit bawaan.
     */
    public function memakaiSmtpKustom(): bool
    {
        return strtolower((string) $this->mail_mode) === 'smtp' && filled($this->mail_host);
    }

    /** Jumlah berkas backup yang disimpan sebelum berkas tertua dihapus. */
    public function retensiBackup(): int
    {
        $nilai = (int) ($this->backup_retensi ?? 0);

        return $nilai > 0 ? $nilai : 5;
    }
}
