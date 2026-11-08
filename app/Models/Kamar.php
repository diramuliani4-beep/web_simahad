<?php

namespace App\Models;

use Database\Factories\KamarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['nama_kamar', 'nomor_kamar', 'gedung', 'lantai', 'kapasitas', 'terisi', 'status', 'fasilitas', 'foto'])]
class Kamar extends Model
{
    /** @use HasFactory<KamarFactory> */
    use HasFactory;

    /**
     * Jumlah maksimal foto yang boleh disimpan untuk satu kamar.
     */
    public const MAKS_FOTO = 4;

    protected $fillable = [
        'nama_kamar',
        'nomor_kamar',
        'gedung',
        'lantai',
        'kapasitas',
        'terisi',
        'status',
        'fasilitas',
        'foto',
    ];

    /**
     * Get the users/students in this room.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Pengajuan penempatan kamar yang menunjuk ke kamar ini.
     */
    public function pengajuanKamars(): HasMany
    {
        return $this->hasMany(PengajuanKamar::class);
    }

    /**
     * Galeri foto kamar. Foto dengan urutan terkecil menjadi foto utama.
     */
    public function fotos(): HasMany
    {
        return $this->hasMany(KamarFoto::class)->orderBy('urutan')->orderBy('id');
    }

    /**
     * Foto utama (thumbnail) kamar, yaitu foto dengan urutan terkecil.
     */
    public function fotoUtama(): ?KamarFoto
    {
        return $this->relationLoaded('fotos')
            ? $this->fotos->first()
            : $this->fotos()->first();
    }

    /**
     * Path foto utama, dengan fallback ke kolom lama kamars.foto.
     */
    public function pathFotoUtama(): ?string
    {
        return $this->fotoUtama()?->path ?: $this->foto;
    }

    /**
     * URL foto utama, dengan fallback ke kolom lama kamars.foto.
     */
    public function urlFotoUtama(): ?string
    {
        $path = $this->pathFotoUtama();

        return $path ? Storage::url($path) : null;
    }

    /**
     * Menyelaraskan kolom lama kamars.foto dengan foto urutan pertama.
     */
    public function sinkronkanFotoUtama(): void
    {
        $pathUtama = $this->fotos()->orderBy('urutan')->orderBy('id')->value('path');

        if ($this->foto !== $pathUtama) {
            $this->update(['foto' => $pathUtama]);
        }
    }

    /**
     * Jumlah mahasiswa yang benar-benar menempati kamar ini.
     */
    public function jumlahPenghuni(): int
    {
        return $this->users()->count();
    }

    /**
     * Status kamar yang masih boleh menerima pengajuan penempatan baru.
     */
    public function bisaDiajukan(): bool
    {
        $status = strtolower(trim((string) $this->status));

        return ! in_array($status, ['dalam perbaikan', 'tidak tersedia', 'nonaktif'], true);
    }

    /**
     * Hitung sisa kapasitas yang masih tersedia.
     */
    public function getSisaKapasitasAttribute(): int
    {
        return max(0, (int) $this->kapasitas - (int) $this->terisi);
    }

    /**
     * Menyelaraskan kolom terisi dan status kamar dengan penghuni sebenarnya.
     */
    public function sinkronkanOkupansi(): void
    {
        $jumlahPenghuni = $this->jumlahPenghuni();
        $statusSaatIni = trim((string) $this->status);
        $statusTerkunci = in_array(
            strtolower($statusSaatIni),
            ['dalam perbaikan', 'tidak tersedia', 'nonaktif'],
            true
        );

        $this->update([
            'terisi' => $jumlahPenghuni,
            'status' => $statusTerkunci
                ? $statusSaatIni
                : ($jumlahPenghuni >= (int) $this->kapasitas ? 'Penuh' : 'Tersedia'),
        ]);
    }
}
