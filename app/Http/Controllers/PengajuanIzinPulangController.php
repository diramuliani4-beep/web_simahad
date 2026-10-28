<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengajuanIzinPulangController extends Controller
{
    /**
     * Menampilkan halaman formulir pengajuan izin pulang.
     */
    public function index()
    {
        return view('surat dan perizinan.pengajuan_izin_pulang');
    }

    /**
     * Memvalidasi data formulir pengajuan izin pulang.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mahasiswa' => 'required|string|max:255',
            'nomor_whatsapp' => 'required|string|max:20',
            'tujuan_pulang' => 'required|string|max:255',
            'tanggal_berangkat' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_berangkat',
            'alasan_pulang' => 'required|string',
            'kontak_keluarga' => 'required|string|max:20',
        ], [
            'nama_mahasiswa.required' => 'Nama mahasiswa wajib diisi.',
            'nomor_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'tujuan_pulang.required' => 'Tujuan pulang wajib diisi.',
            'tanggal_berangkat.required' => 'Tanggal berangkat wajib diisi.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal berangkat.',
            'alasan_pulang.required' => 'Alasan pulang wajib diisi.',
            'kontak_keluarga.required' => 'Kontak keluarga wajib diisi.',
        ]);

        return back()->with(
            'success',
            'Form berhasil divalidasi. Penyimpanan ke database belum diaktifkan.'
        );
    }
}