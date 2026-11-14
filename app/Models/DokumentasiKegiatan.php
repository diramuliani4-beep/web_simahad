<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumentasiKegiatan extends Model
{
    protected $fillable = [
        'guru_id',
        'judul',
        'kelompok',
        'tanggal',
        'keterangan',
        'foto_path',
        'foto_nama',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(PertemuanPembelajaran::class, 'pertemuan_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
