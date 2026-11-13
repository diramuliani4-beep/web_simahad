<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pembayaran extends Model
{
    use HasFactory;

    public const STATUS_LUNAS = 'lunas';

    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu';

    public const STATUS_MENUNGGU_REKTORAT = 'menunggu_konfirmasi';

    public const STATUS_BELUM_LUNAS = 'belum_lunas';

    public const VERIFIKASI_MENUNGGU = 'menunggu';

    public const VERIFIKASI_DITERIMA = 'diterima';

    public const VERIFIKASI_DITOLAK = 'ditolak';

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
        'angsuran_ke',
        'nominal',
        'jatuh_tempo',
        'kategori_biaya',
        'metode_pembayaran',
        'status',
        'status_verifikasi',
        'catatan_verifikasi',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
        'tanggal_upload_bukti',
        'tanggal_konfirmasi_rektorat',
        'reminder_terakhir_pada',
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
        'angsuran_ke' => 'integer',
        'jatuh_tempo' => 'date',
        'tanggal_bayar' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'tanggal_upload_bukti' => 'datetime',
        'tanggal_konfirmasi_rektorat' => 'datetime',
        'reminder_terakhir_pada' => 'datetime',
    ];

    /**
     * Pengguna/mahasiswa pemilik tagihan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Musyrifah yang memeriksa bukti transfer. */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /** Pengajuan keterlambatan untuk tagihan ini. */
    public function pengajuanKeterlambatan(): HasMany
    {
        return $this->hasMany(PengajuanKeterlambatanPembayaran::class);
    }

    public function pengajuanKeterlambatanTerakhir(): HasOne
    {
        return $this->hasOne(PengajuanKeterlambatanPembayaran::class)->latestOfMany();
    }

    public function scopeBelumLunas(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['lunas', 'paid', 'terkonfirmasi']);
    }

    /** Label status yang rapi dan konsisten untuk ditampilkan. */
    public function getStatusLabelAttribute(): string
    {
        return match (strtolower((string) $this->status)) {
            'lunas', 'paid', 'terkonfirmasi' => 'Lunas',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi Rektorat',
            'menunggu', 'pending' => 'Menunggu Verifikasi Bukti',
            default => 'Belum Lunas',
        };
    }

    public function sudahLunas(): bool
    {
        return in_array(strtolower((string) $this->status), ['lunas', 'paid', 'terkonfirmasi'], true);
    }

    public function menungguKonfirmasiRektorat(): bool
    {
        return strtolower((string) $this->status) === self::STATUS_MENUNGGU_REKTORAT;
    }

    public function menungguVerifikasiBukti(): bool
    {
        return strtolower((string) $this->status_verifikasi) === self::VERIFIKASI_MENUNGGU;
    }

    public function buktiDiterima(): bool
    {
        return strtolower((string) $this->status_verifikasi) === self::VERIFIKASI_DITERIMA;
    }

    public function buktiDitolak(): bool
    {
        return strtolower((string) $this->status_verifikasi) === self::VERIFIKASI_DITOLAK;
    }

    public function labelVerifikasi(): string
    {
        return match (strtolower((string) $this->status_verifikasi)) {
            self::VERIFIKASI_DITERIMA => 'Bukti Diterima',
            self::VERIFIKASI_DITOLAK => 'Bukti Ditolak',
            self::VERIFIKASI_MENUNGGU => 'Menunggu Pemeriksaan',
            default => filled($this->bukti_pembayaran) ? 'Bukti Terunggah' : 'Belum Ada Bukti',
        };
    }

    public function kelasVerifikasi(): string
    {
        return match (strtolower((string) $this->status_verifikasi)) {
            self::VERIFIKASI_DITERIMA => 'diterima',
            self::VERIFIKASI_DITOLAK => 'ditolak',
            self::VERIFIKASI_MENUNGGU => 'menunggu',
            default => filled($this->bukti_pembayaran) ? 'menunggu' : 'kosong',
        };
    }

    public function labelTahap(): string
    {
        if ($this->angsuran_ke) {
            return 'Tahap '.$this->angsuran_ke;
        }

        return $this->periode ?: 'Tagihan';
    }

    public function jatuhTempoTeks(): string
    {
        return $this->jatuh_tempo
            ? $this->jatuh_tempo->locale('id')->translatedFormat('d F Y')
            : '-';
    }

    public function sisaHari(): ?int
    {
        if (! $this->jatuh_tempo) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->jatuh_tempo->copy()->startOfDay(), false);
    }

    public function terlambat(): bool
    {
        return ! $this->sudahLunas() && $this->jatuh_tempo !== null && $this->jatuh_tempo->endOfDay()->isPast();
    }

    /** Tagihan masih dapat diunggah buktinya. */
    public function dapatDiunggahBukti(): bool
    {
        return ! $this->sudahLunas() && ! $this->menungguKonfirmasiRektorat();
    }

    /** Tagihan masih dapat diajukan keterlambatan. */
    public function dapatDiajukanKeterlambatan(): bool
    {
        return $this->dapatDiunggahBukti() && ! $this->menungguVerifikasiBukti();
    }

    /** Nama berkas bukti tanpa folder penyimpanan. */
    public function namaBerkasBukti(): string
    {
        return $this->bukti_pembayaran ? basename((string) $this->bukti_pembayaran) : '';
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
                'label_status' => 'Belum Ada Periode Aktif',
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
                ->orderByRaw("CASE WHEN LOWER(status) IN ('lunas', 'paid', 'terkonfirmasi') THEN 1 ELSE 0 END")
                ->orderByRaw('COALESCE(angsuran_ke, 1)')
                ->orderByRaw('COALESCE(jatuh_tempo, created_at)')
                ->orderBy('id')
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
            'belum_dibuka' => 'Belum Ada Periode Aktif',
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
            'va_nomor' => $dibuka ? PengaturanPembayaran::REKENING_NOMOR_RESMI : null,
            'tahap' => $tagihan?->labelTahap() ?? $pengaturan->nama_tahap,
            'jatuh_tempo' => $tagihan?->jatuh_tempo ? $tagihan->jatuhTempoTeks() : $pengaturan->jatuhTempoTeks(),
            'sisa_hari' => $tagihan?->jatuh_tempo ? $tagihan->sisaHari() : $pengaturan->sisaHari(),
            'pengingat' => $dibuka
                && in_array($status, ['belum_lunas', 'menunggu', 'terlambat'], true)
                && $pengaturan->dalamMasaPengingat(),
        ];
    }
}
