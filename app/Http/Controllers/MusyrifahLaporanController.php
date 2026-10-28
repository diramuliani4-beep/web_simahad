<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MusyrifahLaporanController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->query('periode', now()->format('Y-m'));

        $jenis = $request->query('jenis', 'semua');

        $laporan = [
            [
                'jenis' => 'kehadiran',
                'judul' => 'Laporan Kehadiran',
                'deskripsi' => 'Rekap absensi bulanan dan tahunan',
                'ikon' => '▣',
            ],
            [
                'jenis' => 'pembayaran',
                'judul' => 'Laporan Pembayaran',
                'deskripsi' => 'Status pembayaran, jumlah dibayar, dan sisa tagihan',
                'ikon' => '▣',
            ],
            [
                'jenis' => 'pendaftaran',
                'judul' => 'Laporan Pendaftaran',
                'deskripsi' => 'Rekap data pendaftar dan status pendaftaran',
                'ikon' => '▤',
            ],
            [
                'jenis' => 'kamar',
                'judul' => 'Laporan Data Kamar',
                'deskripsi' => 'Rekap kapasitas kamar dan jumlah penghuni',
                'ikon' => '▦',
            ],
        ];

        if ($jenis !== 'semua') {
            $laporan = array_values(array_filter(
                $laporan,
                fn ($item) => $item['jenis'] === $jenis
            ));
        }

        return view('musyrifah.laporan', [
            'laporan' => $laporan,
            'periode' => $periode,
            'jenis' => $jenis,
        ]);
    }
}