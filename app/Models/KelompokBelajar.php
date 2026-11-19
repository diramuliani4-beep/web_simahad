<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokBelajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelompok',
        'nomor_kelompok',
        'keterangan',
    ];

    /**
     * Anggota mahasiswa yang terdaftar pada kelompok belajar ini.
     */
    public function anggotas(): HasMany
    {
        return $this->hasMany(KelompokBelajarAnggota::class, 'kelompok_belajar_id');
    }
}
