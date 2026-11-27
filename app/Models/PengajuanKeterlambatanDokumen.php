<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanKeterlambatanDokumen extends Model
{
    protected $fillable = [
        'pengajuan_id',
        'diunggah_oleh',
        'path',
        'nama_asli',
        'mime_type',
        'ukuran',
        'versi',
    ];

    protected function casts(): array
    {
        return [
            'ukuran' => 'integer',
            'versi' => 'integer',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanKeterlambatanPembayaran::class);
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}
