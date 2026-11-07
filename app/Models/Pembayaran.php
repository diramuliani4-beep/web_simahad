<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'kode_transaksi',
        'nama_tagihan',
        'periode',
        'nominal',
        'metode_pembayaran',
        'status',
        'tanggal_bayar',
        'keterangan',
        'bukti_pembayaran',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
    ];

    /**
     * Pengguna/mahasiswa pemilik tagihan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label status yang rapi dan konsisten untuk ditampilkan.
     */
    public function getStatusLabelAttribute(): string
    {
        return match (strtolower((string) $this->status)) {
            'lunas', 'paid', 'terkonfirmasi' => 'Lunas',
            'menunggu', 'menunggu_konfirmasi', 'pending' => 'Menunggu',
            default => 'Belum Lunas',
        };
    }

    /**
     * Nominal berformat Rupiah.
     */
    public function getNominalRupiahAttribute(): string
    {
        return 'Rp '.number_format((float) $this->nominal, 0, ',', '.');
    }
}
