<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDataMahasiswaController extends Controller
{
    /**
     * Daftar mahasiswa yang pendaftarannya sudah diverifikasi dan disetujui Musyrifah.
     *
     * Sumber data sama dengan hasil verifikasi pendaftaran: baris `pendaftarans`
     * berstatus "disetujui" milik akun `users`. Admin Sistem hanya membaca data;
     * tidak ada aksi tambah, ubah, hapus, atau validasi di halaman ini.
     */
    public function index(Request $request): View
    {
        $this->pastikanAdmin($request);

        $rawQ = $request->query('q', '');
        $pencarian = is_string($rawQ) ? mb_substr(trim($rawQ), 0, 150) : '';

        $rawKategori = $request->query('kategori', 'semua');
        $kategoriFilter = in_array($rawKategori, ['semua', 'umum', 'kip'], true)
            ? $rawKategori : 'semua';

        $mahasiswa = Pendaftaran::query()
            ->with('user')
            ->where('status', 'disetujui')
            // Tanpa akun, data verifikasi tidak dapat ditampilkan sebagai mahasiswa.
            ->whereHas('user')
            ->when($pencarian !== '', function ($query) use ($pencarian) {
                $query->where(function ($sub) use ($pencarian) {
                    // Kolom identitas akun (NIM dan email) dibaca dari tabel users.
                    $sub->where('nama_lengkap', 'like', "%{$pencarian}%")
                        ->orWhere('program_studi', 'like', "%{$pencarian}%")
                        ->orWhere('fakultas', 'like', "%{$pencarian}%")
                        ->orWhere('email_aktif', 'like', "%{$pencarian}%")
                        ->orWhere('no_wa', 'like', "%{$pencarian}%")
                        ->orWhereHas('user', function ($akun) use ($pencarian) {
                            $akun->where('nim', 'like', "%{$pencarian}%")
                                ->orWhere('email', 'like', "%{$pencarian}%");
                        });
                });
            })
            ->when($kategoriFilter !== 'semua', function ($query) use ($kategoriFilter) {
                $query->where('kategori', $kategoriFilter);
            })
            ->orderBy('nama_lengkap')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('Admin.data_mahasiswa', compact('mahasiswa', 'pencarian', 'kategoriFilter'));
    }

    /**
     * Hanya Admin Sistem yang boleh membuka daftar ini.
     * Pengelolaan dan verifikasi tetap kewenangan Musyrifah.
     */
    private function pastikanAdmin(Request $request): void
    {
        abort_unless(
            strtolower((string) $request->user()?->role) === 'admin',
            403,
            'Hanya Admin Sistem yang dapat melihat Data Mahasiswa.'
        );
    }
}
