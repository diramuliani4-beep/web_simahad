<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MusyrifahPendaftaranController extends Controller
{
    /**
     * Menampilkan halaman pendaftaran mahasiswa untuk Musyrifah.
     */
    public function index()
    {
        // Data contoh untuk tampilan awal.
        // Nantinya dapat diganti dengan data dari database.
        $daftarPendaftar = [
            [
                'nama' => 'Ahmad',
                'nim' => '230001',
                'status' => 'Menunggu',
            ],
            [
                'nama' => 'Budi',
                'nim' => '230002',
                'status' => 'Menunggu',
            ],
            [
                'nama' => 'Cinta',
                'nim' => '230003',
                'status' => 'Menunggu',
            ],
            [
                'nama' => 'Dinda',
                'nim' => '230004',
                'status' => 'Menunggu',
            ],
            [
                'nama' => 'Gibran',
                'nim' => '230005',
                'status' => 'Menunggu',
            ],
            [
                'nama' => 'Hakim',
                'nim' => '230006',
                'status' => 'Menunggu',
            ],
            [
                'nama' => 'Indah',
                'nim' => '230007',
                'status' => 'Menunggu',
            ],
        ];

        return view(
            'musyrifah.pendaftaran',
            compact('daftarPendaftar')
        );
    }
}