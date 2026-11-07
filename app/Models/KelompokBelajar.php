<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokBelajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelompok',
        'nomor_kelompok',
        'keterangan',
    ];
}

