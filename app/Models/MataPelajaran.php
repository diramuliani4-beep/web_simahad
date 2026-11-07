<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kode',
        'kategori',
        'target_pertemuan',
        'deskripsi',
    ];

    public function jadwals(): HasMany
    {
        return $this->hasMany(JadwalPembelajaran::class, 'mata_pelajaran_id');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(AbsensiPembelajaran::class, 'mata_pelajaran_id');
    }
}

