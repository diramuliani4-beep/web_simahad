<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPembelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_pelajaran_id',
        'nama_mata_pelajaran',
        'kelompok',
        'guru_pengampu',
        'periode',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
    ];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(AbsensiPembelajaran::class, 'jadwal_id');
    }
}

