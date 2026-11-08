<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MusyrifahDetailPengumumanController extends Controller
{
    /**
     * Menampilkan halaman tambah pengumuman Musyrifah.
     */
    public function create(Request $request): View
    {
        $role = strtolower(trim((string) $request->user()?->role));
        abort_unless(in_array($role, ['musyrifah', 'pengelola'], true), 403);

        return view('musyrifah.detail_pengumuman', [
            'pengumuman' => null,
            'mode' => 'tambah',
        ]);
    }
}
