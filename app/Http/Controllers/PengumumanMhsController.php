<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PengumumanMhsController extends Controller
{
    /**
     * Daftar pengumuman terbit terbaru untuk mahasiswa.
     */
    public function index(Request $request): View
    {
        $this->penggunaMahasiswa($request);

        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:60'],
        ]);

        $pengumuman = Pengumuman::query()
            ->dipublikasikan()
            ->cari($filter['cari'] ?? null)
            ->when(filled($filter['kategori'] ?? null), fn ($query) => $query->where('kategori', $filter['kategori']))
            ->orderByDesc('tanggal_publikasi')
            ->orderByDesc('id')
            ->get();

        return view('mahasiswa.pengumuman_mhs', [
            'pengumuman' => $pengumuman,
            'filter' => $filter,
            'kategoriPilihan' => Pengumuman::kategoriTersedia(),
        ]);
    }

    /**
     * Detail pengumuman yang sudah dipublikasikan.
     */
    public function show(Request $request, Pengumuman $pengumuman): View
    {
        $this->penggunaMahasiswa($request);
        abort_unless($pengumuman->isDipublikasikan(), 404);

        return view('mahasiswa.detail_pengumuman_mhs', [
            'pengumuman' => $pengumuman->load('penulis'),
            'pengumumanLain' => Pengumuman::query()
                ->dipublikasikan()
                ->whereKeyNot($pengumuman->id)
                ->orderByDesc('tanggal_publikasi')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
        ]);
    }

    /**
     * Mengunduh gambar/dokumen pendukung pengumuman yang sudah terbit.
     */
    public function berkas(Request $request, Pengumuman $pengumuman, string $jenis): BinaryFileResponse
    {
        $this->penggunaMahasiswa($request);
        abort_unless($pengumuman->isDipublikasikan(), 404);

        $path = match ($jenis) {
            'gambar' => $pengumuman->gambar_path,
            'dokumen' => $pengumuman->dokumen_path,
            default => null,
        };

        abort_unless(filled($path), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }

    /**
     * Hanya mahasiswa yang boleh membuka halaman pengumuman mahasiswa.
     */
    private function penggunaMahasiswa(Request $request): void
    {
        $role = strtolower(trim((string) $request->user()?->role));
        abort_unless($role === 'mahasiswa', 403);
    }
}
