<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelompokBelajarAnggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelompok_belajar_id',
        'user_id',
        'mata_pelajaran_id',
        'status',
        'keterangan',
    ];

    /**
     * Mahasiswa pemilik keanggotaan kelompok belajar.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kelompok belajar yang diikuti mahasiswa.
     */
    public function kelompokBelajar(): BelongsTo
    {
        return $this->belongsTo(KelompokBelajar::class);
    }

    /**
     * Mata pelajaran yang terkait dengan pembagian kelompok ini.
     */
    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
