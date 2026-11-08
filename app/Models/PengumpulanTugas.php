<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengumpulanTugas extends Model
{
    use HasFactory;

    /**
     * Satu baris per mahasiswa per tugas, sehingga pengunggahan ulang
     * memperbarui data yang sama dan tidak membuat duplikasi.
     */
    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'user_id',
        'file_path',
        'file_nama',
        'dikumpulkan_pada',
        'keterangan',
        'nilai',
        'komentar_guru',
        'dinilai_oleh',
        'dinilai_pada',
        'nilai_dipublikasikan_pada',
    ];

    protected function casts(): array
    {
        return [
            'dikumpulkan_pada' => 'datetime',
            'dinilai_pada' => 'datetime',
            'nilai_dipublikasikan_pada' => 'datetime',
            'nilai' => 'integer',
        ];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    /** Mahasiswa pemilik pengumpulan. */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Guru yang memberikan nilai. */
    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }

    public function riwayatNilai(): HasMany
    {
        return $this->hasMany(RiwayatNilaiTugas::class, 'pengumpulan_tugas_id')
            ->latest('id');
    }

    public function sudahDinilai(): bool
    {
        return $this->nilai !== null;
    }

    public function nilaiDipublikasikan(): bool
    {
        return $this->nilai !== null && $this->nilai_dipublikasikan_pada !== null;
    }

    /** Pengumpulan yang belum dinilai sama sekali. */
    public function scopeBelumDinilai(Builder $query): Builder
    {
        return $query->whereNull('nilai');
    }

    /** Pengumpulan yang nilainya sudah tampil untuk mahasiswa. */
    public function scopeNilaiDipublikasikan(Builder $query): Builder
    {
        return $query->whereNotNull('nilai')
            ->whereNotNull('nilai_dipublikasikan_pada');
    }

    public function dikumpulkanTeks(): string
    {
        return $this->dikumpulkan_pada
            ? $this->dikumpulkan_pada->locale('id')->translatedFormat('d F Y, H:i').' WIB'
            : '-';
    }

    public function dinilaiTeks(): string
    {
        return $this->dinilai_pada
            ? $this->dinilai_pada->locale('id')->translatedFormat('d F Y, H:i').' WIB'
            : '-';
    }

    /** Tugas dikumpulkan melewati batas waktu. */
    public function terlambat(): bool
    {
        return $this->tugas !== null
            && $this->tugas->batas_pengumpulan !== null
            && $this->dikumpulkan_pada !== null
            && $this->dikumpulkan_pada->greaterThan($this->tugas->batas_pengumpulan);
    }
}
