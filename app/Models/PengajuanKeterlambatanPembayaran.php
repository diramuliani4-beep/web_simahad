<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pengajuan keterlambatan pembayaran satu angsuran.
 *
 * Disetujui berarti jadwal dan pengingat diperbarui, bukan berarti Lunas.
 */
class PengajuanKeterlambatanPembayaran extends Model
{
    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    protected $fillable = [
        'user_id',
        'pembayaran_id',
        'nomor_surat',
        'alasan',
        'tanggal_rencana_bayar',
        'status',
        'catatan_musyrifah',
        'diproses_oleh',
        'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_rencana_bayar' => 'date',
            'diproses_pada' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class);
    }

    public function pemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function scopeMenunggu(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    public function masihMenunggu(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }

    public function disetujui(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
    }

    public function kelasStatus(): string
    {
        return match ($this->status) {
            self::STATUS_DISETUJUI => 'disetujui',
            self::STATUS_DITOLAK => 'ditolak',
            default => 'menunggu',
        };
    }

    public function tanggalRencanaTeks(): string
    {
        return $this->tanggal_rencana_bayar
            ? $this->tanggal_rencana_bayar->locale('id')->translatedFormat('d F Y')
            : '-';
    }
}
