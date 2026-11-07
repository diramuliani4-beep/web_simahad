<?php

namespace App\Http\Controllers;

use App\Models\JadwalPembelajaran;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembelajaranController extends Controller
{
    /**
     * Menampilkan daftar mata pelajaran dan jadwal pembelajaran mahasiswa yang tersinkronisasi.
     */
    public function index(): View
    {
        $mapels = MataPelajaran::all();

        $colors = ['blue', 'purple', 'green', 'orange', 'teal'];
        $icons = ['book-open', 'award', 'sparkles', 'languages', 'heart-handshake'];

        $mataPelajaran = $mapels->map(function ($m, $index) use ($colors, $icons) {
            $jadwal = JadwalPembelajaran::where('mata_pelajaran_id', $m->id)->first();
            $jadwalStr = $jadwal ? ($jadwal->hari . ', ' . $jadwal->jam_mulai . ' WIB') : 'Jadwal Terjadwal';

            return [
                'nama' => $m->nama,
                'kategori' => $m->kategori ?? 'Program Ma\'had',
                'ikon' => $icons[$index % count($icons)],
                'warna' => $colors[$index % count($colors)],
                'jadwal' => $jadwalStr,
            ];
        })->toArray();

        $jadwals = JadwalPembelajaran::orderByRaw("CASE hari 
            WHEN 'Senin' THEN 1 
            WHEN 'Selasa' THEN 2 
            WHEN 'Rabu' THEN 3 
            WHEN 'Kamis' THEN 4 
            WHEN 'Jumat' THEN 5 
            WHEN 'Sabtu' THEN 6 
            ELSE 7 END")
            ->take(5)
            ->get();

        $jadwalTerdekat = $jadwals->map(function ($j) {
            return [
                'hari' => $j->hari,
                'tanggal' => 26,
                'bulan' => 'Okt',
                'waktu' => $j->jam_mulai . '–' . $j->jam_selesai . ' WIB',
                'nama' => $j->nama_mata_pelajaran,
                'lokasi' => $j->ruangan . ' (' . $j->kelompok . ')',
            ];
        })->toArray();

        $totalMataPelajaran = count($mataPelajaran);
        $kelompokSaya = 'Kelompok 1';

        return view('mahasiswa.pembelajaran_mhs', compact(
            'mataPelajaran',
            'jadwalTerdekat',
            'totalMataPelajaran',
            'kelompokSaya'
        ));
    }
}
