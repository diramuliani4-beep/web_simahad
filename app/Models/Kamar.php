<?php

namespace App\Models;

use Database\Factories\KamarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kamar', 'nomor_kamar', 'gedung', 'lantai', 'kapasitas', 'terisi', 'status', 'fasilitas'])]
class Kamar extends Model
{
    /** @use HasFactory<KamarFactory> */
    use HasFactory;

    /**
     * Get the users/students in this room.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
