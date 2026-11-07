<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'nim',
    'kamar_id',
    'password',
    'role',
    'no_hp',
    'status',
    'foto_profil',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Relasi pengguna dengan kamar: dipertahankan. */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    /** Riwayat pendaftaran mahasiswa: dipertahankan. */
    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    /** Data pembayaran mahasiswa. */
    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    /** Setiap akun hanya boleh ditautkan ke satu pegawai lewat tabel penghubung. */
    public function pegawaiAkun(): HasOne
    {
        return $this->hasOne(PegawaiAkun::class, 'user_id', 'id');
    }

    /** Satu biodata pegawai melalui pegawai_akun (tanpa pegawais.user_id). */
    public function pegawai(): HasOneThrough
    {
        return $this->hasOneThrough(
            Pegawai::class,
            PegawaiAkun::class,
            'user_id',    // pegawai_akun.user_id -> users.id
            'id',         // pegawais.id
            'id',         // users.id
            'pegawai_id'  // pegawai_akun.pegawai_id -> pegawais.id
        );
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
