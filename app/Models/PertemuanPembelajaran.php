<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Satu pertemuan tatap muka (1-16) untuk satu mata pelajaran, kelompok,
 * dan periode. Absensi dan tugas tetap memakai tabel yang sudah ada,
 * lalu dihubungkan lewat kolom pertemuan_id.
 */
class PertemuanPembelajaran extends Model
{
    use HasFactory;

    public const STATUS_BERLANGSUNG = 'berlangsung';

    public const STATUS_SELESAI = 'selesai';

    public const TARGET_PERTEMUAN = 16;

    /** Status absensi yang diterima dari Guru. */
    public const STATUS_ABSENSI = [
        'hadir' => 'Hadir',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'alpa' => 'Alpa',
    ];

    protected $fillable = [
        'jadwal_id',
        'mata_pelajaran_id',
        'guru_id',
        'kelompok',
        'periode',
        'pertemuan_ke',
        'tanggal',
        'materi',
        'materi_path',
        'materi_nama',
        'status',
        'dimulai_pada',
        'selesai_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'pertemuan_ke' => 'integer',
            'dimulai_pada' => 'datetime',
            'selesai_pada' => 'datetime',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalPembelajaran::class, 'jadwal_id');
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(AbsensiPembelajaran::class, 'pertemuan_id');
    }

    /** Tugas yang diberikan pada pertemuan ini. */
    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'pertemuan_id');
    }

    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    public function sudahSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function berlangsung(): bool
    {
        return $this->status === self::STATUS_BERLANGSUNG;
    }

    public function labelStatus(): string
    {
        return self::labelUntukStatus($this->status);
    }

    public static function labelUntukStatus(?string $status): string
    {
        return match ($status) {
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_BERLANGSUNG => 'Berlangsung',
            default => 'Terjadwal',
        };
    }

    public function tanggalTeks(): string
    {
        return $this->tanggal
            ? $this->tanggal->locale('id')->translatedFormat('d F Y')
            : '-';
    }

    public function punyaMateri(): bool
    {
        return filled($this->materi_path) || filled($this->materi);
    }

    public function punyaBerkasMateri(): bool
    {
        return filled($this->materi_path)
            && Storage::disk('local')->exists($this->materi_path);
    }

    /** Ringkasan absensi pertemuan ini. */
    public function ringkasanAbsensi(): array
    {
        $hitungan = $this->absensis()->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        return [
            'hadir' => (int) ($hitungan['hadir'] ?? 0),
            'izin' => (int) ($hitungan['izin'] ?? 0),
            'sakit' => (int) ($hitungan['sakit'] ?? 0),
            'alpa' => (int) ($hitungan['alpa'] ?? 0),
            'total' => (int) $hitungan->sum(),
        ];
    }

    /** Label ringkas untuk judul audit dan notifikasi. */
    public function label(): string
    {
        $namaMapel = $this->mataPelajaran?->nama ?? 'Mata Pelajaran';

        return $namaMapel.' · '.$this->kelompok.' · Pertemuan '.$this->pertemuan_ke;
    }
}
