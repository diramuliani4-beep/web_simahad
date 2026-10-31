<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MusyrifahManajemenPembelajaranController extends Controller
{
    /**
     * Menampilkan halaman manajemen pembelajaran Musyrifah.
     */
    public function index()
    {
        // Data ringkasan sementara sesuai desain.
        $jumlahMataPelajaran = 6;
        $jumlahPengajar = 8;
        $jumlahPertemuanTerlaksana = 12;
        $targetPertemuan = 16;

        return view('musyrifah.manajemen_pembelajaran', compact(
            'jumlahMataPelajaran',
            'jumlahPengajar',
            'jumlahPertemuanTerlaksana',
            'targetPertemuan'
        ));
    }
}