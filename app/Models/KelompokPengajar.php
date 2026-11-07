<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokPengajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pengajar',
        'program',
        'kelompok_teks',
        'jumlah_tm',
        'keterangan',
    ];
}

