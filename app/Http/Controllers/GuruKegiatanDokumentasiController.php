<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuruKegiatanDokumentasiController extends Controller
{
    public function index()
    {
        return view('guru.kegiatan_dokumentasi');
    }
}