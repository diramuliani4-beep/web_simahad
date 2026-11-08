<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    /**
     * Nama tabel ditulis eksplisit agar tidak bergantung pada pluralisasi
     * otomatis Laravel untuk kata "notifikasi".
     */
    protected $table = 'notifikasis';

    public const TIPE_TUGAS_BARU = 'tugas_baru';

    public const TIPE_TENGGAT_TUGAS = 'tenggat_tugas';

    public const TIPE_PENGUMPULAN_BARU = 'pengumpulan_baru';

    public const TIPE_NILAI_DIPUBLIKASIKAN = 'nilai_dipublikasikan';

    public const TIPE_PENGUMUMAN_BARU = 'pengumuman_baru';

    public const TIPE_JADWAL_BERUBAH = 'jadwal_berubah';

    public const TIPE_PEMBAYARAN = 'pembayaran';

    protected $fillable = [
        'user_id',
        'tipe',
        'judul',
        'pesan',
        'url',
        'kunci',
        'dibaca_pada',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_pada' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sudahDibaca(): bool
    {
        return $this->dibaca_pada !== null;
    }

    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->whereNull('dibaca_pada');
    }

    public function scopeTerbaru(Builder $query): Builder
    {
        return $query->latest('id');
    }

    /** Ikon sederhana untuk ditampilkan pada daftar notifikasi. */
    public function ikon(): string
    {
        return match ($this->tipe) {
            self::TIPE_TUGAS_BARU => '📚',
            self::TIPE_TENGGAT_TUGAS => '⏰',
            self::TIPE_PENGUMPULAN_BARU => '📥',
            self::TIPE_NILAI_DIPUBLIKASIKAN => '🏅',
            self::TIPE_PENGUMUMAN_BARU => '📢',
            self::TIPE_JADWAL_BERUBAH => '🗓',
            self::TIPE_PEMBAYARAN => '💳',
            default => '🔔',
        };
    }

    /**
     * Membuat notifikasi. Jika $kunci diisi, notifikasi yang sama tidak
     * akan dibuat dua kali untuk pengguna yang sama.
     */
    public static function kirim(
        User|int $user,
        string $tipe,
        string $judul,
        ?string $pesan = null,
        ?string $url = null,
        ?string $kunci = null
    ): ?self {
        $userId = $user instanceof User ? $user->id : $user;

        if (! $userId) {
            return null;
        }

        $data = [
            'tipe' => $tipe,
            'judul' => $judul,
            'pesan' => $pesan,
            'url' => $url,
        ];

        if (filled($kunci)) {
            return self::updateOrCreate(
                ['user_id' => $userId, 'kunci' => $kunci],
                $data
            );
        }

        return self::create($data + ['user_id' => $userId, 'kunci' => null]);
    }
}
