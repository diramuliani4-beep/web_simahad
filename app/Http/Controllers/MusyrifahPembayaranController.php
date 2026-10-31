<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MusyrifahPembayaranController extends Controller
{
    /**
     * Menampilkan halaman pembayaran mahasiswa untuk Musyrifah.
     */
    public function index()
    {
        // Data contoh mengikuti desain halaman pembayaran.
        $daftarPembayaran = [
            [
                'nama' => 'Ahmad',
                'nim' => '2317001',
                'tagihan' => 1000000,
                'status' => 'Menunggu',
            ],
            [
                'nama' => 'Budi',
                'nim' => '2317002',
                'tagihan' => 500000,
                'status' => 'Menunggu',
            ],
            [
                'nama' => 'Cinta',
                'nim' => '2317003',
                'tagihan' => 500000,
                'status' => 'Lunas',
            ],
            [
                'nama' => 'Dinda',
                'nim' => '2317004',
                'tagihan' => 1000000,
                'status' => 'Lunas',
            ],
            [
                'nama' => 'Gibran',
                'nim' => '2317005',
                'tagihan' => 1000000,
                'status' => 'Lunas',
            ],
            [
                'nama' => 'Hakim',
                'nim' => '2317006',
                'tagihan' => 1500000,
                'status' => 'Menunggu',
            ],
            [
                'nama' => 'Indah',
                'nim' => '2317007',
                'tagihan' => 1500000,
                'status' => 'Lunas',
            ],
        ];

        return view(
            'musyrifah.pembayaran',
            compact('daftarPembayaran')
        );
    }
}