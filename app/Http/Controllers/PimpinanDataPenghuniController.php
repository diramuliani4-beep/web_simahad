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
            ['nama' => 'Putri Amelia', 'nim' => '202600105', 'kamar' => 'Kamar B-02'],
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
}