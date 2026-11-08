<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPerizinanRiwayat extends Model
{
    protected $fillable = [
        'surat_perizinan_id',
        'status',
        'catatan',
        'diubah_oleh',
        'nama_pengubah',
    ];

    public function suratPerizinan(): BelongsTo
    {
        return $this->belongsTo(SuratPerizinan::class);
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    /** Label status pada riwayat perubahan. */
    public function labelStatus(): string
    {
        return match ($this->status) {
            SuratPerizinan::STATUS_MENUNGGU => 'Menunggu Persetujuan',
            SuratPerizinan::STATUS_DISETUJUI => 'Disetujui',
            SuratPerizinan::STATUS_DITOLAK => 'Ditolak',
            SuratPerizinan::STATUS_PERLU_PERBAIKAN => 'Perlu Perbaikan',
            SuratPerizinan::STATUS_SELESAI => 'Selesai',
            SuratPerizinan::STATUS_DIBATALKAN => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }
}
