<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit trail laporan yang bersifat tambah-saja.
 *
 * Riwayat aktivitas laporan tidak boleh diubah maupun dihapus oleh pengguna
 * biasa, sehingga model ini menolak operasi update dan delete.
 */
class LaporanAudit extends Model
{
    protected $fillable = [
        'laporan_mahad_id',
        'aksi',
        'jenis_laporan',
        'periode',
        'lantai',
        'versi',
        'user_id',
        'pengguna_nama',
        'pengguna_role',
        'keterangan',
        'data_sebelum',
        'data_sesudah',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lantai' => 'integer',
            'versi' => 'integer',
            'data_sebelum' => 'array',
            'data_sesudah' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new \RuntimeException('Riwayat audit laporan tidak dapat diubah.');
        });

        static::deleting(function (): void {
            throw new \RuntimeException('Riwayat audit laporan tidak dapat dihapus.');
        });
    }

    /**
     * @return BelongsTo<LaporanMahad, self>
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanMahad::class, 'laporan_mahad_id');
    }

    /**
     * @return BelongsTo<User, self>
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function labelAksi(): string
    {
        return match ($this->aksi) {
            'dibuat' => 'Laporan dibuat',
            'diperbarui' => 'Laporan diperbarui',
            'diterbitkan' => 'Laporan diterbitkan',
            'diarsipkan' => 'Laporan diarsipkan',
            'dilihat' => 'Laporan dibuka',
            'diunduh' => 'Laporan diunduh',
            'dicetak' => 'Laporan dicetak',
            default => ucfirst(str_replace('_', ' ', (string) $this->aksi)),
        };
    }
}
