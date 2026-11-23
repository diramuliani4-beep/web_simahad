<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_DIPUBLIKASIKAN = 'dipublikasikan';
    public const STATUS_BELUM_DIKUMPULKAN = 'belum_dikumpulkan';
    public const STATUS_SUDAH_DIKUMPULKAN = 'sudah_dikumpulkan';
    public const STATUS_MENUNGGU_PENILAIAN = 'menunggu_penilaian';
    public const STATUS_SUDAH_DINILAI = 'sudah_dinilai';
    public const STATUS_TERLAMBAT = 'terlambat';

    protected $fillable = [
        'user_id',
        'nama_guru',
        'mata_pelajaran_id',
        'nama_mata_pelajaran',
        'kelompok',
        'pertemuan_id',
        'judul',
        'deskripsi',
        'lampiran_path',
        'lampiran_nama',
        'tanggal_mulai',
        'batas_pengumpulan',
        'status',
        'dipublikasikan_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'batas_pengumpulan' => 'datetime',
            'dipublikasikan_pada' => 'datetime',
        ];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(PertemuanPembelajaran::class, 'pertemuan_id');
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }

    public function scopeDipublikasikan(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DIPUBLIKASIKAN);
    }

    public function scopeCari(Builder $query, ?string $kataKunci): Builder
    {
        $kataKunci = trim((string) $kataKunci);

        if ($kataKunci === '') {
            return $query;
        }

        return $query->where('judul', 'like', '%'.$kataKunci.'%');
    }

    public static function statusPilihan(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_DIPUBLIKASIKAN => 'Dipublikasikan',
        ];
    }

    public static function statusPengumpulanPilihan(): array
    {
        return [
            self::STATUS_BELUM_DIKUMPULKAN => 'Belum Dikumpulkan',
            self::STATUS_SUDAH_DIKUMPULKAN => 'Sudah Dikumpulkan',
            self::STATUS_MENUNGGU_PENILAIAN => 'Menunggu Penilaian',
            self::STATUS_SUDAH_DINILAI => 'Sudah Dinilai',
            self::STATUS_TERLAMBAT => 'Terlambat',
        ];
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isDipublikasikan(): bool
    {
        return $this->status === self::STATUS_DIPUBLIKASIKAN;
    }

    public function labelStatus(): string
    {
        return self::statusPilihan()[$this->status] ?? ucfirst((string) $this->status);
    }

    public function punyaLampiran(): bool
    {
        return filled($this->lampiran_path);
    }

    public function namaMataPelajaran(): string
    {
        return $this->nama_mata_pelajaran
            ?: ($this->mataPelajaran?->nama ?? 'Mata Pelajaran');
    }

    public function namaGuru(): string
    {
        return $this->nama_guru ?: ($this->guru?->name ?? 'Pengajar Ma\'had');
    }

    public function sudahMelewatiBatas(): bool
    {
        return $this->batas_pengumpulan !== null
            && $this->batas_pengumpulan->isPast();
    }

    public function masihDapatDikumpulkan(): bool
    {
        return $this->isDipublikasikan() && ! $this->sudahMelewatiBatas();
    }

    public function batasTeks(): string
    {
        return $this->batas_pengumpulan
            ? $this->batas_pengumpulan->locale('id')->translatedFormat('d F Y, H:i').' WIB'
            : '-';
    }

    public function tanggalMulaiTeks(): string
    {
        return $this->tanggal_mulai
            ? $this->tanggal_mulai->locale('id')->translatedFormat('d F Y')
            : '-';
    }

    public function sisaHari(): ?int
    {
        if (! $this->batas_pengumpulan) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays(
            $this->batas_pengumpulan->copy()->startOfDay(),
            false
        );
    }

    /**
     * Anggota aktif kelompok untuk mata pelajaran tugas ini.
     * Keanggotaan ditentukan Musyrifah, bukan diduga dari catatan absensi.
     *
     * @return Collection<int, User>
     */
    public function anggotaKelompok(): Collection
    {
        if (! $this->mata_pelajaran_id || blank($this->kelompok)) {
            return collect();
        }

        return KelompokBelajarAnggota::query()
            ->where('mata_pelajaran_id', $this->mata_pelajaran_id)
            ->where('status', 'aktif')
            ->whereHas('kelompokBelajar', function (Builder $query) {
                $query->where('nama_kelompok', $this->kelompok);
            })
            ->whereHas('user', function (Builder $query) {
                $query->whereRaw('LOWER(role) = ?', ['mahasiswa'])
                    ->whereHas('pendaftarans', function (Builder $pendaftaran) {
                        $pendaftaran->where('status', 'disetujui');
                    });
            })
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->values();
    }

    public function statusUntuk(?PengumpulanTugas $pengumpulan): array
    {
        if ($pengumpulan) {
            if ($pengumpulan->nilai !== null && $pengumpulan->nilai_dipublikasikan_pada !== null) {
                $status = self::STATUS_SUDAH_DINILAI;
            } elseif ($pengumpulan->dikumpulkan_pada !== null && $this->batas_pengumpulan !== null
                && $pengumpulan->dikumpulkan_pada->greaterThan($this->batas_pengumpulan)) {
                $status = self::STATUS_TERLAMBAT;
            } elseif ($pengumpulan->nilai !== null) {
                $status = self::STATUS_MENUNGGU_PENILAIAN;
            } else {
                $status = self::STATUS_SUDAH_DIKUMPULKAN;
            }
        } elseif ($this->sudahMelewatiBatas()) {
            $status = self::STATUS_TERLAMBAT;
        } else {
            $status = self::STATUS_BELUM_DIKUMPULKAN;
        }

        return [
            'status' => $status,
            'label' => self::statusPengumpulanPilihan()[$status] ?? $status,
            'kelas' => match ($status) {
                self::STATUS_SUDAH_DINILAI => 'dinilai',
                self::STATUS_MENUNGGU_PENILAIAN, self::STATUS_SUDAH_DIKUMPULKAN => 'terkumpul',
                self::STATUS_TERLAMBAT => 'terlambat',
                default => 'belum',
            },
        ];
    }

    public function jumlahAnggota(): int
    {
        return $this->anggotaKelompok()->count();
    }

    /**
     * Batasi tugas pada mata pelajaran + kelompok yang benar untuk mahasiswa.
     * Gunakan scope ini untuk fitur baru, bukan filter nama kelompok umum.
     */
    public function scopeUntukAnggota(Builder $query, User $mahasiswa): Builder
    {
        if (strtolower(trim((string) $mahasiswa->role)) !== 'mahasiswa'
            || ! $mahasiswa->pendaftarans()->where('status', 'disetujui')->exists()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereExists(function ($subquery) use ($mahasiswa) {
            $subquery->selectRaw('1')
                ->from('kelompok_belajar_anggotas as anggota')
                ->join('kelompok_belajars as kelompok', 'kelompok.id', '=', 'anggota.kelompok_belajar_id')
                ->whereColumn('anggota.mata_pelajaran_id', 'tugas.mata_pelajaran_id')
                ->whereColumn('kelompok.nama_kelompok', 'tugas.kelompok')
                ->where('anggota.user_id', $mahasiswa->getKey())
                ->where('anggota.status', 'aktif');
        });
    }

    /**
     * @deprecated Scope berbasis NAMA KELOMPOK saja tidak aman untuk otorisasi.
     * Dipertahankan sementara demi kompatibilitas kode lama. Semua penggunaan
     * untuk halaman mahasiswa HARUS dialihkan ke untukAnggota($mahasiswa).
     */
    public function scopeUntukMahasiswa(Builder $query, ?string $kelompok): Builder
    {
        return $query->where('kelompok', $kelompok);
    }
}
