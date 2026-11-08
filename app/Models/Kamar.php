<?php

namespace App\Models;

use Database\Factories\KamarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kamar', 'nomor_kamar', 'gedung', 'lantai', 'kapasitas', 'terisi', 'status', 'fasilitas', 'foto'])]
class Kamar extends Model
{
    /** @use HasFactory<KamarFactory> */
    use HasFactory;

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
     * Hitung sisa kapasitas yang masih tersedia.
     */
    public function getSisaKapasitasAttribute(): int
    {
        return max(0, (int) $this->kapasitas - (int) $this->terisi);
    }
}
