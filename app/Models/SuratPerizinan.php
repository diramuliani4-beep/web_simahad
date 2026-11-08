<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class SuratPerizinan extends Model
{
    use HasFactory;

    public const JENIS_KEGIATAN_FAKULTAS = 'kegiatan_fakultas';

    public const JENIS_KEGIATAN_KAMPUS = 'kegiatan_kampus';

    public const JENIS_PULANG = 'pulang_mingguan';

    public const JENIS_SEMESTER = 'surat_pulang_semester';

    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_PERLU_PERBAIKAN = 'perlu_perbaikan';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    /** Kuota izin pulang mingguan per mahasiswa dalam satu bulan. */
    public const KUOTA_PULANG_PER_BULAN = 2;

    /** Durasi maksimal satu pengajuan izin pulang (dalam hari, termasuk hari pertama). */
    public const MAKS_DURASI_PULANG_HARI = 14;

    protected $fillable = [
        'user_id',
        'jenis',
        'status',
        'nama_kegiatan',
        'penyelenggara',
        'lokasi',
        'waktu_mulai',
        'waktu_selesai',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
        'alasan',
        'dokumen_path',
        'dokumen_nama',
        'nomor_surat',
        'surat_tertanda_path',
        'surat_tertanda_nama',
        'surat_diunggah_pada',
        'keputusan_oleh',
        'keputusan_nama',
        'keputusan_pada',
        'catatan_musyrifah',
        'dibatalkan_pada',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'surat_diunggah_pada' => 'datetime',
            'keputusan_pada' => 'datetime',
            'dibatalkan_pada' => 'datetime',
            'durasi_hari' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function keputusanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'keputusan_oleh');
    }

    public function riwayats(): HasMany
    {
        return $this->hasMany(SuratPerizinanRiwayat::class)->latest('id');
    }

    /**
     * Status yang masih menahan satu slot kuota izin pulang.
     *
     * Pengajuan yang menunggu tetap menahan slot sementara agar mahasiswa
     * tidak dapat mengirim pengajuan berulang melebihi batas.
     *
     * @return array<int, string>
     */
    public static function statusMenahanKuota(): array
    {
        return [
            self::STATUS_MENUNGGU,
            self::STATUS_DISETUJUI,
            self::STATUS_PERLU_PERBAIKAN,
            self::STATUS_SELESAI,
        ];
    }

    /**
     * Menghitung pemakaian kuota izin pulang pada bulan dari tanggal mulai.
     *
     * @return array{maksimal: int, terpakai: int, sisa: int, bulan: string}
     */
    public static function kuotaPulang(User $user, ?CarbonInterface $acuan = null): array
    {
        $acuan = $acuan ? Carbon::instance($acuan) : Carbon::now();

        $awal = $acuan->copy()->startOfMonth()->toDateString();
        $akhir = $acuan->copy()->endOfMonth()->toDateString();

        $terpakai = static::query()
            ->where('user_id', $user->id)
            ->where('jenis', self::JENIS_PULANG)
            ->whereIn('status', self::statusMenahanKuota())
            ->whereBetween('tanggal_mulai', [$awal, $akhir])
            ->count();

        return [
            'maksimal' => self::KUOTA_PULANG_PER_BULAN,
            'terpakai' => $terpakai,
            'sisa' => max(0, self::KUOTA_PULANG_PER_BULAN - $terpakai),
            'bulan' => $acuan->copy()->locale('id')->translatedFormat('F Y'),
        ];
    }

    /** Apakah pengajuan tergolong izin kegiatan fakultas/prodi maupun kampus. */
    public function izinKegiatan(): bool
    {
        return in_array($this->jenis, [self::JENIS_KEGIATAN_FAKULTAS, self::JENIS_KEGIATAN_KAMPUS], true);
    }

    /** Apakah pengajuan merupakan izin pulang mingguan. */
    public function izinPulang(): bool
    {
        return $this->jenis === self::JENIS_PULANG;
    }

    /** Apakah pengajuan merupakan surat pulang semester. */
    public function suratSemester(): bool
    {
        return $this->jenis === self::JENIS_SEMESTER;
    }

    /** Masih dapat diproses (disetujui/ditolak) oleh Musyrifah. */
    public function masihMenunggu(): bool
    {
        return in_array($this->status, [self::STATUS_MENUNGGU, self::STATUS_PERLU_PERBAIKAN], true);
    }

    public function labelJenis(): string
    {
        return match ($this->jenis) {
            self::JENIS_KEGIATAN_FAKULTAS => 'Izin Kegiatan Fakultas/Prodi',
            self::JENIS_KEGIATAN_KAMPUS => 'Izin Kegiatan Kampus',
            self::JENIS_PULANG => 'Izin Pulang Mingguan',
            self::JENIS_SEMESTER => 'Surat Pulang Semester',
            default => ucfirst(str_replace('_', ' ', (string) $this->jenis)),
        };
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'Menunggu Persetujuan',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_PERLU_PERBAIKAN => 'Perlu Perbaikan',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    /** Kelas CSS badge status yang dipakai pada tampilan. */
    public function kelasStatus(): string
    {
        return match ($this->status) {
            self::STATUS_DISETUJUI => 'disetujui',
            self::STATUS_DITOLAK => 'ditolak',
            self::STATUS_PERLU_PERBAIKAN => 'perbaikan',
            self::STATUS_SELESAI => 'selesai',
            self::STATUS_DIBATALKAN => 'dibatalkan',
            default => 'menunggu',
        };
    }

    /** Ringkasan periode izin untuk daftar dan laporan. */
    public function periodeTeks(): string
    {
        if (! $this->tanggal_mulai) {
            return '-';
        }

        $mulai = $this->tanggal_mulai->locale('id')->translatedFormat('d M Y');

        if (! $this->tanggal_selesai) {
            return $mulai;
        }

        return $mulai.' - '.$this->tanggal_selesai->locale('id')->translatedFormat('d M Y');
    }

    /** Ringkasan waktu kegiatan (jam mulai - jam selesai). */
    public function waktuTeks(): string
    {
        if (! filled($this->waktu_mulai) && ! filled($this->waktu_selesai)) {
            return '-';
        }

        $mulai = filled($this->waktu_mulai) ? substr((string) $this->waktu_mulai, 0, 5) : '-';
        $selesai = filled($this->waktu_selesai) ? substr((string) $this->waktu_selesai, 0, 5) : '-';

        return $mulai.' - '.$selesai.' WIB';
    }

    /** Tujuan/lokasi ringkas untuk kolom daftar. */
    public function tujuanTeks(): string
    {
        return $this->lokasi ?: '-';
    }

    /** Apakah dokumen pendukung sudah diunggah. */
    public function punyaDokumen(): bool
    {
        return filled($this->dokumen_path);
    }

    /** Apakah surat bertanda tangan orang tua sudah diunggah. */
    public function punyaSuratTertanda(): bool
    {
        return filled($this->surat_tertanda_path);
    }

    /**
     * Mencatat satu perubahan status ke riwayat pengajuan.
     */
    public function catatRiwayat(string $status, ?string $catatan = null, ?User $oleh = null): SuratPerizinanRiwayat
    {
        return $this->riwayats()->create([
            'status' => $status,
            'catatan' => $catatan,
            'diubah_oleh' => $oleh?->id,
            'nama_pengubah' => $oleh?->name,
        ]);
    }
}
