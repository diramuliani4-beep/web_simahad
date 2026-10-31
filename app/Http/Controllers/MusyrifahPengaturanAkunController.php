<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MusyrifahPengaturanAkunController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('musyrifah.pengaturan_akun', compact('user'));
    }
}