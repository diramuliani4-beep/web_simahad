<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PimpinanPembelajaranController extends Controller
{
    /**
     * Menampilkan halaman pembelajaran untuk Pimpinan.
     */
    public function index()
    {
        $mataPelajaran = 7;
        $pengajar = 7;
        $kelompokBelajar = 6;
        $targetPertemuan = 16;

        $daftarKelompok = [
            [
                'nama' => 'Kelompok A',
                'jumlah' => '10–15 mahasiswa',
            ],
            [
                'nama' => 'Kelompok B',
                'jumlah' => '10–15 mahasiswa',
            ],
            [
                'nama' => 'Kelompok C',
                'jumlah' => '10–15 mahasiswa',
            ],
        ];

        return view('pimpinan.pembelajaran', compact(
            'mataPelajaran',
            'pengajar',
            'kelompokBelajar',
            'targetPertemuan',
            'daftarKelompok'
        ));
    }
}