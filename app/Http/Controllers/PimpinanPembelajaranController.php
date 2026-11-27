<?php

namespace App\Http\Controllers;

use App\Models\KelompokBelajar;
use App\Models\KelompokPengajar;
use App\Models\MataPelajaran;
use Illuminate\View\View;

class PimpinanPembelajaranController extends Controller
{
    /**
     * Menampilkan halaman pembelajaran untuk Pimpinan.
     */
    public function index(): View
    {
        $mataPelajaran = MataPelajaran::count() ?: 5;
        $pengajar = KelompokPengajar::count() ?: 8;
        $kelompokBelajar = KelompokBelajar::count() ?: 6;
        $targetPertemuan = 16;

        $kelompokDb = KelompokBelajar::withCount('penempatanAktif')->orderBy('nomor_kelompok')->get();
        if ($kelompokDb->isNotEmpty()) {
            $daftarKelompok = $kelompokDb->map(fn (KelompokBelajar $k) => [
                'nama' => $k->nama_kelompok,
                'jumlah' => $k->penempatan_aktif_count > 0 ? $k->penempatan_aktif_count.' mahasiswa terdaftar' : 'Belum ada anggota',
            ])->toArray();
        } else {
            $daftarKelompok = [
                ['nama' => 'Kelompok 1', 'jumlah' => '10–15 mahasiswa'],
                ['nama' => 'Kelompok 2', 'jumlah' => '10–15 mahasiswa'],
                ['nama' => 'Kelompok 3', 'jumlah' => '10–15 mahasiswa'],
                ['nama' => 'Kelompok 4', 'jumlah' => '10–15 mahasiswa'],
                ['nama' => 'Kelompok 5', 'jumlah' => '10–15 mahasiswa'],
                ['nama' => 'Kelompok 6', 'jumlah' => '10–15 mahasiswa'],
            ];
        }

        return view('pimpinan.pembelajaran', compact(
            'mataPelajaran',
            'pengajar',
            'kelompokBelajar',
            'targetPertemuan',
            'daftarKelompok'
        ));
    }
}
