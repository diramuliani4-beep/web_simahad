<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PimpinanLaporanMahadController extends Controller
{
    // Halaman utama laporan Ma'had
public function index()
{
    $laporan = [
        [
            'ikon' => 'users',
            'judul' => 'Laporan Penghuni',
            'deskripsi' => 'Melihat informasi data penghuni Ma’had, termasuk NIM, nama, program studi, angkatan, dan kamar.',
            'url' => route('pimpinan.laporan-penghuni'),
        ],
        [
            'ikon' => 'bed',
            'judul' => 'Laporan Kamar',
            'deskripsi' => 'Melihat informasi penggunaan kamar dan data penghuni berdasarkan kamar.',
            'url' => '#',
        ],
        [
            'ikon' => 'book-open',
            'judul' => 'Laporan Pembelajaran',
            'deskripsi' => 'Melihat informasi kegiatan dan pelaksanaan pembelajaran di Ma’had.',
            'url' => route('pimpinan.pembelajaran'),
        ],
        [
            'ikon' => 'wallet',
            'judul' => 'Laporan Pembayaran',
            'deskripsi' => 'Melihat informasi pembayaran dan administrasi keuangan penghuni Ma’had.',
            'url' => '#',
        ],
        [
            'ikon' => 'file-check',
            'judul' => 'Laporan Administrasi',
            'deskripsi' => 'Melihat informasi administrasi dan dokumen penghuni Ma’had.',
            'url' => '#',
        ],
    ];

    return view('pimpinan.laporan_mahad', compact('laporan'));
}

    // Halaman detail laporan penghuni
    public function penghuni(Request $request)
    {
        // Data contoh penghuni
        $semuaPenghuni = [
            [
                'nim' => '202600101',
                'nama' => 'Nadia Putri',
                'prodi' => 'Sistem Informasi',
                'angkatan' => '2026',
                'kamar' => 'Kamar A-01',
                'status' => 'Aktif',
            ],
            [
                'nim' => '202600102',
                'nama' => 'Alya Rahma',
                'prodi' => 'Informatika',
                'angkatan' => '2026',
                'kamar' => 'Kamar A-02',
                'status' => 'Aktif',
            ],
            [
                'nim' => '202600103',
                'nama' => 'Siti Azzahra',
                'prodi' => 'Sistem Informasi',
                'angkatan' => '2026',
                'kamar' => 'Kamar A-03',
                'status' => 'Aktif',
            ],
            [
                'nim' => '202600104',
                'nama' => 'Nurul Hidayah',
                'prodi' => 'Manajemen Pendidikan Islam',
                'angkatan' => '2026',
                'kamar' => 'Kamar B-01',
                'status' => 'Tidak Aktif',
            ],
            [
                'nim' => '202600105',
                'nama' => 'Putri Amelia',
                'prodi' => 'Pendidikan Agama Islam',
                'angkatan' => '2026',
                'kamar' => 'Kamar B-02',
                'status' => 'Aktif',
            ],
        ];

        // Pencarian berdasarkan NIM atau nama
        $keyword = $request->query('cari', '');

        $penghuni = collect($semuaPenghuni)
            ->filter(function ($item) use ($keyword) {
                return stripos($item['nama'], $keyword) !== false
                    || stripos($item['nim'], $keyword) !== false;
            })
            ->values();

        // Mengirim data ke halaman laporan penghuni
        return view('pimpinan.laporan_penghuni', [
            'penghuni' => $penghuni,
            'totalPenghuni' => count($semuaPenghuni),
            'penghuniAktif' => collect($semuaPenghuni)
                ->where('status', 'Aktif')
                ->count(),
            'keyword' => $keyword,
        ]);
    }
}