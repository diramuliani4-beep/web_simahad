<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminPengaturanSistemController extends Controller
{
    public function index()
    {
        return view('admin.pengaturan_sistem');
    }

    public function create()
    {
        return view('Admin.tambah_pengguna');
    }
}