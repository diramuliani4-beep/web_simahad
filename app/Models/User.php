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
use Illuminate\Support\Collection;

#[Fillable([
    'name',
    'email',
    'nim',
    'kamar_id',
    'password',
    'password_set_at',
    'role',
    'no_hp',
    'status',
    'foto_profil',
    'activation_token',
    'activation_expires_at',
    'last_login_at',
])]
#[Hidden(['password', 'remember_token', 'activation_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Relasi pengguna dengan kamar: dipertahankan. */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    /** Pengajuan penempatan kamar mahasiswa. */
    public function pengajuanKamar(): HasOne
    {
        return $this->hasOne(PengajuanKamar::class);
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

    /** Pengajuan surat & perizinan mahasiswa. */
    public function suratPerizinans(): HasMany
    {
        return $this->hasMany(SuratPerizinan::class);
    }

    /** Pengumuman Ma'had yang ditulis oleh Musyrifah. */
    public function pengumumans(): HasMany
    {
        return $this->hasMany(Pengumuman::class);
    }

    /** Tugas yang dibuat Guru untuk kelompok dan mata pelajarannya. */
    public function tugasDibuat(): HasMany
    {
        return $this->hasMany(Tugas::class, 'user_id');
    }

    /** Pengumpulan tugas milik mahasiswa. */
    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'user_id');
    }

    /** Notifikasi terintegrasi milik pengguna. */
    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    /**
     * Kelompok belajar mahasiswa, diambil dari data absensi pembelajaran
     * yang sudah ada (sumber yang sama dengan halaman Pembelajaran).
     */
    public function kelompokPembelajaran(): string
    {
        return AbsensiPembelajaran::query()
            ->where('user_id', $this->id)
            ->value('kelompok') ?: 'Kelompok 1';
    }

    /**
     * Daftar mahasiswa anggota satu kelompok belajar.
     * Keanggotaan diambil dari absensi pembelajaran yang sudah ada.
     *
     * @return Collection<int, self>
     */
    public static function mahasiswaKelompok(string $kelompok): Collection
    {
        $ids = AbsensiPembelajaran::query()
            ->where('kelompok', $kelompok)
            ->distinct()
            ->pluck('user_id');

        if ($ids->isEmpty()) {
            return collect();
        }

        return static::query()
            ->whereIn('id', $ids)
            ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
            ->orderBy('name')
            ->get();
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
            'activation_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password_set_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Pemilik akun sudah menentukan kata sandinya sendiri.
     *
     * Akun yang dibuat Admin belum memilikinya, sehingga Pengaturan Akun
     * menampilkan formulir pembuatan kata sandi pertama tanpa meminta
     * kata sandi saat ini.
     */
    public function sudahPunyaKataSandi(): bool
    {
        return filled($this->password) && filled($this->password_set_at);
    }

    /** Akun pegawai (Guru, Musyrifah, Pimpinan) masih menunggu aktivasi mandiri. */
    public function menungguAktivasi(): bool
    {
        return in_array(strtolower((string) $this->status), ['menunggu aktivasi', 'menunggu', 'pending'], true)
            && strtolower((string) $this->role) !== 'mahasiswa';
    }

    /** Nama peran dalam bahasa tampilan. */
    public function labelPeran(): string
    {
        return match (strtolower((string) $this->role)) {
            'mahasiswa' => 'Mahasiswa',
            'guru' => 'Guru',
            'musyrifah' => 'Musyrifah',
            'pimpinan' => 'Pimpinan',
            'admin' => 'Admin Sistem',
            default => ucfirst((string) $this->role),
        };
    }

    /** Inisial nama untuk avatar tanpa foto. */
    public function inisial(): string
    {
        $nama = trim((string) $this->name);

        if ($nama === '') {
            return '?';
        }

        $kata = preg_split('/\s+/', $nama) ?: [];
        $inisial = mb_substr($kata[0] ?? '', 0, 1);

        if (count($kata) > 1) {
            $inisial .= mb_substr($kata[count($kata) - 1], 0, 1);
        }

        return mb_strtoupper($inisial);
    }

    /** URL foto profil bila tersedia. */
    public function urlFotoProfil(): ?string
    {
        return filled($this->foto_profil)
            ? asset('storage/'.$this->foto_profil)
            : null;
    }
}
