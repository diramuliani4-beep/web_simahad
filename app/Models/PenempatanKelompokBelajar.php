<?php

namespace App\Models;

use Database\Factories\PenempatanKelompokBelajarFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenempatanKelompokBelajar extends Model
{
    /** @use HasFactory<PenempatanKelompokBelajarFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kelompok_belajar_id',
        'status',
        'keterangan',
        'ditugaskan_pada',
        'dicabut_pada',
    ];

    /** The student who owns this global placement. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The learning group assigned to the student. */
    public function kelompokBelajar(): BelongsTo
    {
        return $this->belongsTo(KelompokBelajar::class);
    }

    /** Limit the query to placements that have not been revoked. */
    public function scopeAktif(Builder $query): void
    {
        $query->whereRaw('LOWER(status) = ?', ['aktif']);
    }

    /** Limit the query to students with an approved registration. */
    public function scopeMahasiswaDisetujui(Builder $query): void
    {
        $query->whereHas('user', function (Builder $user): void {
            $user->whereRaw('LOWER(role) = ?', ['mahasiswa'])
                ->whereHas('pendaftarans', function (Builder $pendaftaran): void {
                    $pendaftaran->where('status', 'disetujui');
                });
        });
    }

    /** Match the group name used by learning records. */
    public function scopeUntukKelompok(Builder $query, string $namaKelompok): void
    {
        $query->whereHas('kelompokBelajar', function (Builder $kelompok) use ($namaKelompok): void {
            $kelompok->where('nama_kelompok', $namaKelompok);
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'ditugaskan_pada' => 'datetime',
            'dicabut_pada' => 'datetime',
        ];
    }
}
