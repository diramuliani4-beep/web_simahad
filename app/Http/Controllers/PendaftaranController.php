<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    /**
     * Step 1: Form Pendaftaran Pribadi (Data Diri)
     */
    public function pribadi(): View
    {
        $data = session('pendaftaran', []);

        return view('pendaftaran.pribadi', compact('data'));
    }

    /**
     * Save Step 1 and proceed to Step 2
     */
    public function simpanPribadi(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'no_wa' => ['required', 'string', 'max:20'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'nama_ortu' => ['required', 'string', 'max:255'],
            'no_hp_ortu' => ['required', 'string', 'max:20'],
            'kategori' => ['nullable', 'string', 'in:umum,kip'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email aktif wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
            'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
            'nama_ortu.required' => 'Nama orang tua wajib diisi.',
            'no_hp_ortu.required' => 'Nomor HP orang tua wajib diisi.',
        ]);

        $pendaftaran = session('pendaftaran', []);
        $pendaftaran = array_merge($pendaftaran, $validated);
        session(['pendaftaran' => $pendaftaran]);

        return redirect()->route('pendaftaran.akademik');
    }

    /**
     * Step 2: Form Pendaftaran Akademik
     */
    public function akademik(): View
    {
        $data = session('pendaftaran', []);

        return view('pendaftaran.akademik', compact('data'));
    }

    /**
     * Save Step 2 and proceed to Step 3
     */
    public function simpanAkademik(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_angkatan' => ['required', 'string'],
            'fakultas' => ['required', 'string', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'bukti_penerimaan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'tahun_angkatan.required' => 'Tahun angkatan wajib dipilih.',
            'fakultas.required' => 'Nama fakultas wajib diisi.',
            'program_studi.required' => 'Program studi wajib diisi.',
        ]);

        $pendaftaran = session('pendaftaran', []);
        $pendaftaran = array_merge($pendaftaran, [
            'tahun_angkatan' => $validated['tahun_angkatan'],
            'fakultas' => $validated['fakultas'],
            'program_studi' => $validated['program_studi'],
        ]);

        if ($request->hasFile('bukti_penerimaan')) {
            $path = $request->file('bukti_penerimaan')->store('dokumen_pendaftaran', 'public');
            $pendaftaran['bukti_penerimaan'] = $path;
            $pendaftaran['bukti_penerimaan_nama'] = $request->file('bukti_penerimaan')->getClientOriginalName();
        }

        session(['pendaftaran' => $pendaftaran]);

        return redirect()->route('pendaftaran.dokumen');
    }

    /**
     * Step 3: Form Pendaftaran Dokumen dan Pembayaran (Data Dok-Mhs)
     */
    public function dokumen(): View
    {
        $data = session('pendaftaran', []);

        return view('pendaftaran.dokumen', compact('data'));
    }

    /**
     * Save Step 3 and proceed to Confirmation
     */
    public function simpanDokumen(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'alasan_daftar' => ['required', 'string'],
            'nominal_pembayaran' => ['required', 'string'],
            'bukti_pembayaran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'konfirmasi_data' => ['required'],
        ], [
            'alasan_daftar.required' => 'Alasan daftar ke Ma\'had wajib diisi.',
            'nominal_pembayaran.required' => 'Nominal pembayaran awal wajib diisi.',
            'konfirmasi_data.required' => 'Anda harus mencentang konfirmasi data sebelum mengirim.',
        ]);

        $pendaftaran = session('pendaftaran', []);
        $pendaftaran = array_merge($pendaftaran, [
            'alasan_daftar' => $validated['alasan_daftar'],
            'nominal_pembayaran' => $validated['nominal_pembayaran'],
            'konfirmasi_data' => true,
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('dokumen_pembayaran', 'public');
            $pendaftaran['bukti_pembayaran'] = $path;
            $pendaftaran['bukti_pembayaran_nama'] = $request->file('bukti_pembayaran')->getClientOriginalName();
        }

        session(['pendaftaran' => $pendaftaran, 'pendaftaran_selesai' => true]);

        return redirect()->route('pendaftaran.konfirmasi');
    }

    /**
     * Step 4: Konfirmasi Pendaftaran Sudah Dikirim
     */
    public function konfirmasi(): View
    {
        return view('pendaftaran.konfirmasi');
    }

    /**
     * Fallback for previous selesai route
     */
    public function selesai(Request $request): RedirectResponse
    {
        return redirect()->route('pendaftaran.konfirmasi');
    }
}
