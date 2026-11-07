<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPembelajaran;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiMhsController extends Controller
{
    /**
     * Menampilkan riwayat absensi dan status kelulusan mahasiswa tersinkronisasi.
     */
    public function index(): View
    {
        $user = Auth::user();

        $absensis = AbsensiPembelajaran::with('mataPelajaran')
            ->where('user_id', $user?->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('pertemuan_ke', 'desc')
            ->get();

        $totalHadir = $absensis->where('status', 'hadir')->count();
        $totalIzin = $absensis->where('status', 'izin')->count();
        $totalSakit = $absensis->where('status', 'sakit')->count();
        $totalAlpa = $absensis->where('status', 'alpa')->count();
        $totalSesi = $absensis->count();

        $persentaseKehadiran = $totalSesi > 0
            ? round(($totalHadir / $totalSesi) * 100, 1)
            : 100.0;

        $maxPertemuan = $absensis->max('pertemuan_ke') ?: 16;

        // Cek syarat kelulusan: hadir 16 kali di seluruh mata pelajaran
        $totalMapel = MataPelajaran::count() ?: 5;
        $mapelKurang = MataPelajaran::whereDoesntHave('absensis', function ($q) use ($user) {
            $q->where('user_id', $user?->id)->where('status', 'hadir');
        }, '>=', 16)->count();

        $isLulus = ($totalSesi > 0 && $mapelKurang === 0);

        $daftarMapel = MataPelajaran::pluck('nama');

        return view('mahasiswa.absensi_mhs', compact(
            'absensis',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'persentaseKehadiran',
            'maxPertemuan',
            'isLulus',
            'daftarMapel'
        ));
    }
}
