<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuruAbsensiController extends Controller
{
    public function index()
    {
        return view('guru.absensi');
    }
}