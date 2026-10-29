<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MusyrifahPengumumanController extends Controller
{
    public function index()
    {
        return view('musyrifah.pengumuman');
    }
}