<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuruJadwalSayaController extends Controller
{
    public function index()
    {
        return view('guru.jadwal_saya');
    }
}