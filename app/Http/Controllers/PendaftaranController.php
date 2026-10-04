<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function pribadi()
    {
        return view('pendaftaran.pribadi');
    }

    public function simpanPribadi(Request $request)
    {
        return redirect()->route('pendaftaran.akademik');
    }

    public function akademik()
    {
        return view('pendaftaran.akademik');
    }

    public function selesai(Request $request)
    {
        return redirect()->route('dashboard');
    }
}