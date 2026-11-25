<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu baris laporan pembelajaran. Baris manual dapat ditautkan ke pertemuan;
 * rekap Manajemen Pembelajaran tetap dibangkitkan langsung dari data operasional.
 */
class LaporanPembelajaran extends Model
{
    protected $fillable = [
        'mata_pelajaran_id',
        'pertemuan_id',
        'tanggal',
        'kelompok',
        'periode_semester',
        'materi',
        'instruktur',
        'jumlah_hadir',
        'jumlah_izin',
        'jumlah_sakit',
        'jumlah_alpa',
        'tanda_tangan',
        'catatan',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah_hadir' => 'integer',
            'jumlah_izin' => 'integer',
            'jumlah_sakit' => 'integer',
            'jumlah_alpa' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<MataPelajaran, self>
     */
    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    /**
     * @return BelongsTo<PertemuanPembelajaran, self>
     */
    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(PertemuanPembelajaran::class);
    }

    /**
     * @return BelongsTo<User, self>
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeUntukMataPelajaran(Builder $query, int $mataPelajaranId): void
    {
        $query->where('mata_pelajaran_id', $mataPelajaranId);
    }

    public function labelHari(): string
    {
        return $this->tanggal?->locale('id')->translatedFormat('l') ?? '-';
    }

    public function labelTanggal(): string
    {
        return $this->tanggal?->locale('id')->translatedFormat('d F Y') ?? '-';
    }
}
