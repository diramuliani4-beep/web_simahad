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

    /**
     * Ringkasan tagihan periode aktif untuk ditampilkan pada dashboard
     * dan halaman detail pembayaran mahasiswa.
     *
     * @return array<string, mixed>
     */
    public static function ringkasanUntuk(User $user): array
    {
        $pengaturan = PengaturanPembayaran::aktif();

        if (! $pengaturan) {
            return [
                'pengaturan' => null,
                'dibuka' => false,
                'tagihan' => null,
                'nominal' => 0.0,
                'nominal_rupiah' => 'Rp 0',
                'status' => 'belum_dibuka',
                'label_status' => 'Periode Belum Dibuka',
                'kelas_status' => 'belum',
                'va_nomor' => null,
                'tahap' => null,
                'jatuh_tempo' => null,
                'sisa_hari' => null,
                'pengingat' => false,
            ];
        }

        $dibuka = $pengaturan->sudahDibuka();
        $tagihan = $dibuka
            ? static::query()
                ->where('user_id', $user->id)
                ->where('periode', $pengaturan->periode)
                ->latest('id')
                ->first()
            : null;

        $statusTercatat = strtolower((string) $tagihan?->status);

        $status = match (true) {
            ! $dibuka => 'belum_dibuka',
            in_array($statusTercatat, ['lunas', 'paid', 'terkonfirmasi'], true) => 'lunas',
            in_array($statusTercatat, ['menunggu', 'menunggu_konfirmasi', 'pending'], true) => 'menunggu',
            $pengaturan->sudahJatuhTempo() => 'terlambat',
            default => 'belum_lunas',
        };

        $labelStatus = match ($status) {
            'lunas' => 'Lunas',
            'menunggu' => 'Menunggu Konfirmasi',
            'terlambat' => 'Terlambat',
            'belum_dibuka' => 'Periode Belum Dibuka',
            default => 'Belum Lunas',
        };

        $nominal = (float) ($tagihan?->nominal ?? $pengaturan->nominal);

        return [
            'pengaturan' => $pengaturan,
            'dibuka' => $dibuka,
            'tagihan' => $tagihan,
            'nominal' => $nominal,
            'nominal_rupiah' => 'Rp '.number_format($nominal, 0, ',', '.'),
            'status' => $status,
            'label_status' => $labelStatus,
            'kelas_status' => match ($status) {
                'lunas' => 'lunas',
                'menunggu' => 'menunggu',
                'terlambat' => 'terlambat',
                default => 'belum',
            },
            'va_nomor' => $dibuka ? $pengaturan->va_nomor : null,
            'tahap' => $pengaturan->nama_tahap,
            'jatuh_tempo' => $pengaturan->jatuhTempoTeks(),
            'sisa_hari' => $pengaturan->sisaHari(),
            'pengingat' => $dibuka
                && in_array($status, ['belum_lunas', 'menunggu', 'terlambat'], true)
                && $pengaturan->dalamMasaPengingat(),
        ];
    }
}
