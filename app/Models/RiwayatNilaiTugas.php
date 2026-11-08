<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatNilaiTugas extends Model
{
    protected $table = 'riwayat_nilai_tugas';

    protected $fillable = [
        'pengumpulan_tugas_id',
        'nilai_lama',
        'nilai_baru',
        'komentar_lama',
        'komentar_baru',
        'diubah_oleh',
        'nama_pengubah',
    ];

    protected function casts(): array
    {
        return [
            'nilai_lama' => 'integer',
            'nilai_baru' => 'integer',
        ];
    }

    public function pengumpulan(): BelongsTo
    {
        return $this->belongsTo(PengumpulanTugas::class, 'pengumpulan_tugas_id');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
