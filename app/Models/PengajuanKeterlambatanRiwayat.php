<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanKeterlambatanRiwayat extends Model
{
    protected $fillable = [
        'pengajuan_id',
        'dokumen_id',
        'status_sebelum',
        'status_sesudah',
        'catatan',
        'diubah_oleh',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanKeterlambatanPembayaran::class);
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(PengajuanKeterlambatanDokumen::class, 'dokumen_id');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
