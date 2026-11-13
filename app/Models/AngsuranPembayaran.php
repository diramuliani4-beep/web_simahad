<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Jadwal angsuran pembayaran Ma'had untuk satu periode.
 *
 * Maksimal tiga angsuran per tahun. Angsuran pertama minimal Rp1.000.000
 * dan total biaya standar Rp5.000.000. Mahasiswa dengan kategori berbeda
 * tetap dapat memiliki nominal tagihan sendiri; lihat
 * PembayaranAngsuranService::nominalTagihan().
 */
class AngsuranPembayaran extends Model
{
    public const JUMLAH_ANGSURAN = 3;

    public const ANGSURAN_PERTAMA_MINIMAL = 1000000.0;

    public const TOTAL_BIAYA_UMUM = 5000000.0;

    /** Nominal bawaan angsuran 1, 2, dan 3 (total Rp5.000.000). */
    public const NOMINAL_BAWAAN = [1 => 1000000.0, 2 => 2000000.0, 3 => 2000000.0];

    /** Hari jatuh tempo bawaan dihitung dari tanggal periode dibuka. */
    public const HARI_BAWAAN = [1 => 30, 2 => 90, 3 => 150];

    public const REMINDER_BAWAAN = [1 => 7, 2 => 3, 3 => 3];

    protected $fillable = [
        'periode',
        'angsuran_ke',
        'nama_tahap',
        'nominal',
        'jatuh_tempo',
        'reminder_hari',
        'is_aktif',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'angsuran_ke' => 'integer',
            'nominal' => 'decimal:2',
            'jatuh_tempo' => 'date',
            'reminder_hari' => 'integer',
            'is_aktif' => 'boolean',
        ];
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    public function scopePeriode(Builder $query, string $periode): Builder
    {
        return $query->where('periode', $periode);
    }

    public function nominalRupiah(): string
    {
        return 'Rp '.number_format((float) $this->nominal, 0, ',', '.');
    }

    public function jatuhTempoTeks(): string
    {
        return $this->jatuh_tempo
            ? $this->jatuh_tempo->locale('id')->translatedFormat('d F Y')
            : '-';
    }

    public function label(): string
    {
        return $this->nama_tahap ?: 'Tahap '.$this->angsuran_ke;
    }
}
