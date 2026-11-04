<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PimpinanDataPenghuniController extends Controller
{
    public function index(Request $request)
    {
        // Data contoh untuk tampilan awal
        $semuaPenghuni = [
            ['nama' => 'Nadia Putri', 'nim' => '202600101', 'kamar' => 'Kamar A-01'],
            ['nama' => 'Alya Rahma', 'nim' => '202600102', 'kamar' => 'Kamar A-02'],
            ['nama' => 'Siti Azzahra', 'nim' => '202600103', 'kamar' => 'Kamar A-03'],
            ['nama' => 'Nurul Hidayah', 'nim' => '202600104', 'kamar' => 'Kamar B-01'],
            ['nama' => 'Putri Amelia', 'nim' => 'a202600105', 'kamar' => 'Kamar B-02'],
        ];

        $keyword = $request->query('cari', '');

        $penghuni = collect($semuaPenghuni)
            ->filter(function ($item) use ($keyword) {
                return stripos($item['nama'], $keyword) !== false
                    || stripos($item['nim'], $keyword) !== false;
            })
            ->values();

        return view('pimpinan.data_penghuni', [
            'penghuni' => $penghuni,
            'totalPenghuni' => 120,
            'totalAngkatan' => 6,
            'keyword' => $keyword,
        ]);
    }

    public function penghuni(Request $request)
{
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

    $keyword = $request->query('cari', '');

    $penghuni = collect($semuaPenghuni)
        ->filter(function ($item) use ($keyword) {
            return stripos($item['nama'], $keyword) !== false
                || stripos($item['nim'], $keyword) !== false;
        })
        ->values();

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