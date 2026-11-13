<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

/**
 * Jadwal mengajar Guru ditampilkan di dalam menu Pembelajaran bersama
 * progres 16 pertemuan, sehingga halaman jadwal terpisah tidak lagi
 * menyimpan data contoh yang terpisah dari database.
 */
class GuruJadwalSayaController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('guru.pembelajaran');
    }
}
