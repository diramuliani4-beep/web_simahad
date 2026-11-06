<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Pegawai extends Model
{
    protected $table = 'pegawais';

    protected $fillable = [
        'nama_pegawai',
        'email',
        'no_hp',
        'foto',
        'jenis_kelamin',
        'pendidikan_terakhir',
        'alamat',
        'status_pegawai',
    ];

    protected $casts = [
        'status_pegawai' => 'boolean',
    ];

    public function akunLink(): HasOne
    {
        return $this->hasOne(PegawaiAkun::class, 'pegawai_id', 'id');
    }

    /** Satu akun melalui tabel penghubung (pegawai_akun.user_id -> users.id).
     * Cocok untuk $pegawai->user dan with('user') pada Blade lama. */
    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            PegawaiAkun::class,
            'pegawai_id', // Foreign key di pegawai_akun menuju pegawais
            'id',         // Primary key users
            'id',         // Primary key pegawais
            'user_id'     // Foreign key di pegawai_akun menuju users
        );
    }
}
