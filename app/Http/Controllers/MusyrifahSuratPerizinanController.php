<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MusyrifahSuratPerizinanController extends Controller
{
    public function index()
    {
        $pengajuan = [
            [
                'no' => 1,
                'nama' => 'ahmad',
                'nim' => '23001',
                'jenis' => 'Pulang',
                'periode' => '16-18 agustus',
                'status' => 'Menunggu',
            ],
            [
                'no' => 2,
                'nama' => 'Budi',
                'nim' => '23002',
                'jenis' => 'Sakit',
                'periode' => '16 agustus',
                'status' => 'Menunggu',
            ],
            [
                'no' => 3,
                'nama' => 'Cinta',
                'nim' => '23003',
                'jenis' => 'Pulang',
                'periode' => '16-19 agustus',
                'status' => 'Menunggu',
            ],
            [
                'no' => 4,
                'nama' => 'Dinda',
                'nim' => '23004',
                'jenis' => 'Pulang',
                'periode' => '16-19 agustus',
                'status' => 'Menunggu',
            ],
            [
                'no' => 5,
                'nama' => 'Gibran',
                'nim' => '23005',
                'jenis' => 'Sakit',
                'periode' => '1 September',
                'status' => 'Menunggu',
            ],
            [
                'no' => 6,
                'nama' => 'Hakim',
                'nim' => '23006',
                'jenis' => 'Sakit',
                'periode' => '2 September',
                'status' => 'Menunggu',
            ],
            [
                'no' => 7,
                'nama' => 'Indah',
                'nim' => '23007',
                'jenis' => 'Pulang',
                'periode' => '1-2 September',
                'status' => 'Menunggu',
            ],
        ];

        return view('musyrifah.surat_perizinan', compact('pengajuan'));
    }
}