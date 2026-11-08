<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Pengumuman extends Model
{
    use HasFactory;

    /** Nama tabel ditetapkan eksplisit agar tidak dibentuk ulang oleh pluralizer. */
    protected $table = 'pengumumans';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_DIPUBLIKASIKAN = 'dipublikasikan';

    public const KATEGORI_AKADEMIK = 'Akademik';

    public const KATEGORI_KEGIATAN = "Kegiatan Ma'had";

    public const KATEGORI_PEMBAYARAN = 'Pembayaran';

    public const KATEGORI_PERATURAN = 'Peraturan';

    public const KATEGORI_INFORMASI_UMUM = 'Informasi Umum';

    protected $fillable = [
        'user_id',
        'penulis_nama',
        'judul',
        'kategori',
        'isi',
        'gambar_path',
        'gambar_nama',
        'dokumen_path',
        'dokumen_nama',
        'tanggal_publikasi',
        'status',
        'dipublikasikan_pada',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'tanggal_publikasi' => 'date',
            'dipublikasikan_pada' => 'datetime',
        ];
    }

    /** Musyrifah penulis pengumuman. */
    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Kategori baku yang selalu tersedia pada formulir pengumuman.
     *
     * @return array<int, string>
     */
    public static function kategoriBaku(): array
    {
        return [
            self::KATEGORI_AKADEMIK,
            self::KATEGORI_KEGIATAN,
            self::KATEGORI_PEMBAYARAN,
            self::KATEGORI_PERATURAN,
            self::KATEGORI_INFORMASI_UMUM,
        ];
    }

    /**
     * Seluruh kategori yang dapat difilter: kategori baku ditambah
     * kategori kustom yang sudah pernah dipakai Musyrifah.
     *
     * @return array<int, string>
     */
    public static function kategoriTersedia(): array
    {
        $kategoriDatabase = self::query()
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori')
            ->all();

        return array_values(array_unique(array_merge(
            self::kategoriBaku(),
            array_filter($kategoriDatabase),
        )));
    }

    /** @return array<string, string> */
    public static function statusPilihan(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_DIPUBLIKASIKAN => 'Dipublikasikan',
        ];
    }

    /** Hanya pengumuman yang sudah terbit untuk halaman mahasiswa. */
    public function scopeDipublikasikan(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DIPUBLIKASIKAN);
    }

    /** Pencarian berdasarkan judul pengumuman. */
    public function scopeCari(Builder $query, ?string $kataKunci): Builder
    {
        $kataKunci = trim((string) $kataKunci);

        if ($kataKunci === '') {
            return $query;
        }

        return $query->where('judul', 'like', '%'.$kataKunci.'%');
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
        return match ($this->status) {
            self::STATUS_DIPUBLIKASIKAN => 'Dipublikasikan',
            default => 'Draft',
        };
    }

    /** Kelas CSS badge status yang dipakai pada tampilan. */
    public function kelasStatus(): string
    {
        return $this->isDipublikasikan() ? 'terbit' : 'draft';
    }

    public function punyaGambar(): bool
    {
        return filled($this->gambar_path);
    }

    public function punyaDokumen(): bool
    {
        return filled($this->dokumen_path);
    }

    /** Ringkasan isi pengumuman untuk daftar dan kartu. */
    public function ringkasan(int $panjang = 180): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', (string) $this->isi)), $panjang);
    }

    /** Tanggal publikasi dalam format tampilan Indonesia. */
    public function tanggalTeks(): string
    {
        if (! $this->tanggal_publikasi) {
            return '-';
        }

        return $this->tanggal_publikasi->locale('id')->translatedFormat('d F Y');
    }

    /** Nama penulis yang ditampilkan pada detail pengumuman. */
    public function penulisTeks(): string
    {
        return $this->penulis_nama ?: ($this->penulis?->name ?: 'Pengelola Ma\'had');
    }

    /** Nama berkas asli yang diunggah Musyrifah. */
    public function namaBerkas(string $jenis): ?string
    {
        return match ($jenis) {
            'gambar' => $this->gambar_nama,
            'dokumen' => $this->dokumen_nama,
            default => null,
        };
    }
}
