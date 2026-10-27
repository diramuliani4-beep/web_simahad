<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminHakAksesController extends Controller
{
    public function index()
    {
        return view('admin.hak_akses');
    }
}
