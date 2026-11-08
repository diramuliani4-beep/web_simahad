<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanPembayaran extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_pembayarans';

    protected $fillable = [
        'periode',
        'nama_tahap',
        'nominal',
        'va_nomor',
        'va_bank',
        'periode_dibuka_pada',
        'jatuh_tempo',
        'is_aktif',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'periode_dibuka_pada' => 'date',
            'jatuh_tempo' => 'date',
            'is_aktif' => 'boolean',
        ];
    }

    /**
     * Konfigurasi pembayaran yang sedang berlaku. Hanya satu konfigurasi
     * aktif yang dipakai untuk seluruh mahasiswa.
     */
    public static function aktif(): ?self
    {
        return static::query()
            ->where('is_aktif', true)
            ->latest('periode_dibuka_pada')
            ->latest('id')
            ->first();
    }

    /** Periode pembayaran sudah dibuka untuk mahasiswa. */
    public function sudahDibuka(): bool
    {
        return $this->periode_dibuka_pada !== null
            && $this->periode_dibuka_pada->startOfDay()->lessThanOrEqualTo(now()->startOfDay());
    }

    public function sudahJatuhTempo(): bool
    {
        return $this->jatuh_tempo !== null
            && $this->jatuh_tempo->endOfDay()->isPast();
    }

    /** Sisa hari menuju jatuh tempo (negatif jika sudah lewat). */
    public function sisaHari(): ?int
    {
        if (! $this->jatuh_tempo) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays(
            $this->jatuh_tempo->copy()->startOfDay(),
            false
        );
    }

    /** Pengingat merah ditampilkan mulai 7 hari sebelum jatuh tempo. */
    public function dalamMasaPengingat(): bool
    {
        $sisaHari = $this->sisaHari();

        return $sisaHari !== null && $sisaHari <= 7;
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

    public function periodeDibukaTeks(): string
    {
        return $this->periode_dibuka_pada
            ? $this->periode_dibuka_pada->locale('id')->translatedFormat('d F Y')
            : '-';
    }
}
