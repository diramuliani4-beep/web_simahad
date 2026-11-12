<?php

namespace App\Models;

use App\Services\LaporanMahadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Register laporan Ma'had.
 *
 * Isi laporan selalu dibangkitkan dari data operasional terkini melalui
 * LaporanMahadService, sedangkan model ini menyimpan identitas, periode,
 * sumber, status, dan versi laporan.
 */
class LaporanMahad extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_DITERBITKAN = 'diterbitkan';

    public const STATUS_DIARSIPKAN = 'diarsipkan';

    public const SUMBER_SISTEM = 'sistem';

    public const SUMBER_MUSYRIFAH = 'musyrifah';

    protected $fillable = [
        'jenis',
        'judul',
        'periode',
        'lantai',
        'unit',
        'sumber',
        'status',
        'versi',
        'dibuat_oleh',
        'pembuat_nama',
        'pembuat_role',
        'diterbitkan_pada',
        'diterbitkan_oleh',
        'diarsipkan_pada',
        'catatan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lantai' => 'integer',
            'versi' => 'integer',
            'diterbitkan_pada' => 'datetime',
            'diarsipkan_pada' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, self>
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * @return BelongsTo<User, self>
     */
    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }

    /**
     * @return HasMany<LaporanAudit, self>
     */
    public function audits(): HasMany
    {
        return $this->hasMany(LaporanAudit::class)->latest('id');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeTerbit(Builder $query): void
    {
        $query->whereIn('status', [self::STATUS_DITERBITKAN, self::STATUS_DIARSIPKAN]);
    }

    public function labelPeriode(): string
    {
        return app(LaporanMahadService::class)->labelPeriode($this->periode);
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            self::STATUS_DITERBITKAN => 'Diterbitkan',
            self::STATUS_DIARSIPKAN => 'Diarsipkan',
            default => 'Draft',
        };
    }

    public function kelasStatus(): string
    {
        return match ($this->status) {
            self::STATUS_DITERBITKAN => 'diterbitkan',
            self::STATUS_DIARSIPKAN => 'diarsipkan',
            default => 'draft',
        };
    }

    public function labelSumber(): string
    {
        return $this->sumber === self::SUMBER_MUSYRIFAH ? 'Musyrifah' : 'Sistem';
    }

    /**
     * Identitas pembuat yang ditampilkan pada daftar laporan.
     */
    public function identitasPembuat(): string
    {
        if ($this->sumber === self::SUMBER_SISTEM && blank($this->pembuat_nama)) {
            return 'Rekap otomatis sistem';
        }

        $nama = $this->pembuat_nama ?: $this->pembuat?->name;

        return filled($nama) ? (string) $nama : 'Tidak tercatat';
    }

    public function labelUnit(): string
    {
        if ($this->lantai) {
            return 'Lantai '.$this->lantai;
        }

        return $this->unit ?: 'Seluruh Ma’had';
    }
}
