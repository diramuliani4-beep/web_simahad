<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembelajaranController extends Controller
{
    public function index()
    {
        $mataPelajaran = [
            [
                'nama' => 'Bahasa Arab',
                'kategori' => 'Bahasa',
                'ikon' => 'languages',
                'warna' => 'blue',
                'jadwal' => 'Senin, 08.00 WIB',
            ],
            [
                'nama' => 'Studi Islam',
                'kategori' => 'Keislaman',
                'ikon' => 'book-open',
                'warna' => 'purple',
                'jadwal' => 'Senin, 10.00 WIB',
            ],
            [
                'nama' => 'Tahsin Al-Qur’an',
                'kategori' => 'Al-Qur’an',
                'ikon' => 'book-marked',
                'warna' => 'green',
                'jadwal' => 'Selasa, 08.00 WIB',
            ],
            [
                'nama' => 'Tahfidz Al-Qur’an',
                'kategori' => 'Al-Qur’an',
                'ikon' => 'book-open-check',
                'warna' => 'orange',
                'jadwal' => 'Selasa, 10.00 WIB',
            ],
            [
                'nama' => 'Kajian Fiqih',
                'kategori' => 'Keislaman',
                'ikon' => 'book-open',
                'warna' => 'pink',
                'jadwal' => 'Rabu, 08.00 WIB',
            ],
            [
                'nama' => 'Akhlak',
                'kategori' => 'Keislaman',
                'ikon' => 'heart-handshake',
                'warna' => 'teal',
                'jadwal' => 'Rabu, 10.00 WIB',
            ],
            [
                'nama' => 'Praktik Ibadah',
                'kategori' => 'Praktik',
                'ikon' => 'hand-heart',
                'warna' => 'indigo',
                'jadwal' => 'Kamis, 08.00 WIB',
            ],
        ];

        $jadwalTerdekat = [
            [
                'hari' => 'Senin',
                'tanggal' => 26,
                'bulan' => 'Okt',
                'waktu' => '08.00–09.30 WIB',
                'nama' => 'Bahasa Arab',
                'lokasi' => 'Ruang Pembelajaran',
            ],
            [
                'hari' => 'Senin',
                'tanggal' => 26,
                'bulan' => 'Okt',
                'waktu' => '10.00–11.30 WIB',
                'nama' => 'Studi Islam',
                'lokasi' => 'Ruang Pembelajaran',
            ],
            [
                'hari' => 'Selasa',
                'tanggal' => 27,
                'bulan' => 'Okt',
                'waktu' => '08.00–09.30 WIB',
                'nama' => 'Tahsin Al-Qur’an',
                'lokasi' => 'Ruang Pembelajaran',
            ],
        ];

        return view('mahasiswa.pembelajaran_mhs', compact(
            'mataPelajaran',
            'jadwalTerdekat'
        ));
    }
}
