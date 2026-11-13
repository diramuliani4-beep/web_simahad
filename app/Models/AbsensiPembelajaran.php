<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiPembelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mata_pelajaran_id',
        'jadwal_id',
        'pertemuan_id',
        'kelompok',
        'pertemuan_ke',
        'tanggal',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'pertemuan_ke' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalPembelajaran::class, 'jadwal_id');
    }

    /** Pertemuan tatap muka yang menjadi sumber absensi ini. */
    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(PertemuanPembelajaran::class, 'pertemuan_id');
    }
}
