<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MusyrifahDataKamarController extends Controller
{
    /**
     * Menampilkan halaman data kamar untuk Musyrifah.
     */
    public function index()
    {
        // Data contoh untuk tampilan awal.
        $jumlahKamar = 84;
        $kamarTerisi = 70;
        $kamarTersedia = 14;

        $daftarKamar = [
            [
                'nomor' => '1.1',
                'lantai' => 1,
                'kapasitas' => 3,
                'terisi' => 2,
            ],
            [
                'nomor' => '1.2',
                'lantai' => 1,
                'kapasitas' => 3,
                'terisi' => 3,
            ],
            [
                'nomor' => '2.1',
                'lantai' => 2,
                'kapasitas' => 3,
                'terisi' => 1,
            ],
            [
                'nomor' => '3.1',
                'lantai' => 3,
                'kapasitas' => 3,
                'terisi' => 3,
            ],
        ];

        return view('musyrifah.data_kamar', compact(
            'jumlahKamar',
            'kamarTerisi',
            'kamarTersedia',
            'daftarKamar'
        ));
    }
}