<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class JadwalPembelajaran extends Model
{
    use HasFactory;

    /** Nama hari sesuai penomoran ISO (1 = Senin). */
    public const HARI = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Ahad',
    ];

    protected $fillable = [
        'mata_pelajaran_id',
        'nama_mata_pelajaran',
        'kelompok',
        'guru_pengampu',
        'periode',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
    ];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(AbsensiPembelajaran::class, 'jadwal_id');
    }

    /** Jadwal untuk satu kelompok belajar. */
    public function scopeUntukKelompok(Builder $query, ?string $kelompok): Builder
    {
        return $query->where('kelompok', $kelompok);
    }

    /** Nomor hari ISO (1-7) berdasarkan nama hari jadwal. */
    public function nomorHari(): int
    {
        $nomor = array_search(trim((string) $this->hari), self::HARI, true);

        return $nomor === false ? 1 : (int) $nomor;
    }

    public function jamTeks(): string
    {
        return substr((string) $this->jam_mulai, 0, 5).'–'.substr((string) $this->jam_selesai, 0, 5).' WIB';
    }

    public function namaMataPelajaran(): string
    {
        return $this->nama_mata_pelajaran
            ?: ($this->mataPelajaran?->nama ?? 'Mata Pelajaran');
    }

    /**
     * Nama-nama pengajar yang mewakili akun Guru (nama akun dan nama pegawai).
     *
     * @return Collection<int, string>
     */
    public static function namaPengajarUntuk(User $guru): Collection
    {
        return collect([$guru->name, $guru->pegawai?->nama_pegawai])
            ->filter(fn ($nama) => filled($nama))
            ->map(fn ($nama) => mb_strtolower(trim((string) $nama)))
            ->unique()
            ->values();
    }

    /**
     * Seluruh jadwal yang menjadi tanggung jawab Guru.
     *
     * @return Collection<int, static>
     */
    public static function jadwalUntuk(User $guru): Collection
    {
        $namaPengajar = static::namaPengajarUntuk($guru);

        if ($namaPengajar->isEmpty()) {
            return collect();
        }

        return static::query()
            ->where(function (Builder $query) use ($namaPengajar) {
                foreach ($namaPengajar as $nama) {
                    $query->orWhereRaw('LOWER(guru_pengampu) = ?', [$nama]);
                }
            })
            ->with('mataPelajaran')
            ->orderBy('kelompok')
            ->orderBy('id')
            ->get();
    }

    /**
     * Daftar penugasan (mata pelajaran + kelompok) milik seorang Guru.
     *
     * Skema pembelajaran menyimpan nama pengampu sebagai teks pada
     * jadwal_pembelajarans.guru_pengampu, sehingga kewenangan Guru
     * diturunkan dari jadwal yang mencantumkan nama Guru tersebut
     * (nama akun atau nama pegawai yang tertaut).
     *
     * @return Collection<int, array{mata_pelajaran_id: int|null, nama_mata_pelajaran: string, kelompok: string}>
     */
    public static function penugasanUntuk(User $guru): Collection
    {
        return static::jadwalUntuk($guru)
            ->map(fn (self $jadwal) => [
                'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                'nama_mata_pelajaran' => $jadwal->namaMataPelajaran(),
                'kelompok' => $jadwal->kelompok,
            ])
            ->unique(fn (array $item) => $item['mata_pelajaran_id'].'|'.$item['kelompok'])
            ->values();
    }

    /**
     * Pasangan mata pelajaran + kelompok yang boleh diakses Guru.
     *
     * @return Collection<int, string>
     */
    public static function kunciPenugasan(User $guru): Collection
    {
        return static::penugasanUntuk($guru)
            ->map(fn (array $item) => $item['mata_pelajaran_id'].'|'.$item['kelompok']);
    }
}
