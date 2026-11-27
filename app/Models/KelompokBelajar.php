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

    /** Legacy subject-scoped membership history. */
    public function anggotas(): HasMany
    {
        return $this->hasMany(KelompokBelajarAnggota::class, 'kelompok_belajar_id');
    }

    /** All global placement history for this group. */
    public function penempatans(): HasMany
    {
        return $this->hasMany(PenempatanKelompokBelajar::class, 'kelompok_belajar_id');
    }

    /** Approved students with an active global placement. */
    public function penempatanAktif(): HasMany
    {
        return $this->penempatans()->aktif()->mahasiswaDisetujui();
    }
}
