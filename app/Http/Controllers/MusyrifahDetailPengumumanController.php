<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MusyrifahDetailPengumumanController extends Controller
{
    /**
     * Menampilkan halaman tambah pengumuman Musyrifah.
     */
    public function index()
    {
        return view('musyrifah.detail_pengumuman');
    }
}