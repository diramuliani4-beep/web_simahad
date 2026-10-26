<?php

namespace App\Http\Controllers;

class PengumumanMhsController extends Controller
{
    public function index()
    {
        $pengumuman = [
            [
                'id' => 1,
                'judul' => 'Kegiatan Kebersamaan Mahasiswa Ma’had',
                'kategori' => 'Kegiatan',
                'ikon' => 'calendar-days',
                'warna' => 'purple',
                'deskripsi' => 'Mahasiswa diharapkan mengikuti kegiatan kebersamaan yang diselenggarakan oleh pihak Ma’had sesuai jadwal yang diinformasikan.',
                'tanggal' => '10 Oktober 2026',
                'status' => 'Kegiatan',
            ],
            [
                'id' => 2,
                'judul' => 'Informasi Ketertiban dan Kebersihan Asrama',
                'kategori' => 'Pengumuman',
                'ikon' => 'megaphone',
                'warna' => 'orange',
                'deskripsi' => 'Seluruh mahasiswa diimbau menjaga kebersihan kamar, merapikan barang pribadi, dan mematuhi tata tertib di lingkungan Ma’had.',
                'tanggal' => '9 Oktober 2026',
                'status' => 'Penting',
            ],
            [
                'id' => 3,
                'judul' => 'Informasi Jadwal Pembelajaran Ma’had',
                'kategori' => 'Pembelajaran',
                'ikon' => 'book-open',
                'warna' => 'blue',
                'deskripsi' => 'Mahasiswa diminta memantau jadwal pembelajaran dan mengikuti kegiatan belajar sesuai kelompok masing-masing.',
                'tanggal' => '8 Oktober 2026',
                'status' => 'Pembelajaran',
            ],
            [
                'id' => 4,
                'judul' => 'Pengingat Kegiatan Ibadah Mahasiswa',
                'kategori' => 'Kegiatan',
                'ikon' => 'heart-handshake',
                'warna' => 'green',
                'deskripsi' => 'Mahasiswa diharapkan mengikuti kegiatan ibadah bersama dan menjaga kedisiplinan dalam aktivitas harian di lingkungan Ma’had.',
                'tanggal' => '7 Oktober 2026',
                'status' => 'Kegiatan',
            ],
            [
                'id' => 5,
                'judul' => 'Informasi Evaluasi Pembelajaran',
                'kategori' => 'Pembelajaran',
                'ikon' => 'book-open',
                'warna' => 'pink',
                'deskripsi' => 'Mahasiswa diharapkan mempersiapkan diri untuk mengikuti evaluasi pembelajaran sesuai jadwal yang ditetapkan pengelola Ma’had.',
                'tanggal' => '6 Oktober 2026',
                'status' => 'Pembelajaran',
            ],
        ];

        return view('pengumuman_mhs', compact('pengumuman'));
    }
}