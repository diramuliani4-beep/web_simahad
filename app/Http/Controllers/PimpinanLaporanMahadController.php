<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PimpinanLaporanMahadController extends Controller
{
    public function index(): View
    {
        $laporan = [
            [
                'judul' => 'Laporan Penghuni',
                'deskripsi' => 'Data penghuni berdasarkan angkatan dan penempatan.',
                'ikon' => 'users',
                'url' => '#',
            ],
            [
                'judul' => 'Laporan Kamar',
                'deskripsi' => 'Informasi kapasitas, keterisian, dan kamar tersedia.',
                'ikon' => 'bed',
                'url' => '#',
            ],
            [
                'judul' => 'Laporan Pembelajaran',
                'deskripsi' => 'Informasi kelompok pembelajaran dan rekap absensi.',
                'ikon' => 'book-open',
                'url' => '#',
            ],
            [
                'judul' => 'Laporan Pembayaran',
                'deskripsi' => 'Informasi status pembayaran yang tercatat.',
                'ikon' => 'wallet',
                'url' => '#',
            ],
            [
                'judul' => 'Laporan Perizinan',
                'deskripsi' => 'Rekap pengajuan izin berdasarkan status.',
                'ikon' => 'file-check',
                'url' => '#',
            ],
        ];

        return view('pimpinan.laporan_mahad', compact('laporan'));
    }
}