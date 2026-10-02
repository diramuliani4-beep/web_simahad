<?php

namespace Database\Seeders;

use App\Models\Kamar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KamarSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kamars = [
            [
                'nama_kamar' => 'Kamar 101',
                'nomor_kamar' => '101',
                'gedung' => 'Gedung A',
                'lantai' => 1,
                'kapasitas' => 4,
                'terisi' => 2,
                'status' => 'Tersedia',
                'fasilitas' => 'Tempat tidur, lemari pakaian, meja belajar, kamar mandi dalam',
            ],
            [
                'nama_kamar' => 'Kamar 102',
                'nomor_kamar' => '102',
                'gedung' => 'Gedung A',
                'lantai' => 1,
                'kapasitas' => 4,
                'terisi' => 4,
                'status' => 'Penuh',
                'fasilitas' => 'Tempat tidur, lemari pakaian, meja belajar, kamar mandi dalam',
            ],
            [
                'nama_kamar' => 'Kamar 103',
                'nomor_kamar' => '103',
                'gedung' => 'Gedung A',
                'lantai' => 1,
                'kapasitas' => 4,
                'terisi' => 1,
                'status' => 'Tersedia',
                'fasilitas' => 'Tempat tidur, lemari pakaian, meja belajar, kamar mandi dalam',
            ],
            [
                'nama_kamar' => 'Kamar 201',
                'nomor_kamar' => '201',
                'gedung' => 'Gedung A',
                'lantai' => 2,
                'kapasitas' => 4,
                'terisi' => 0,
                'status' => 'Tersedia',
                'fasilitas' => 'Tempat tidur, lemari pakaian, meja belajar, kamar mandi dalam',
            ],
            [
                'nama_kamar' => 'Kamar 202',
                'nomor_kamar' => '202',
                'gedung' => 'Gedung A',
                'lantai' => 2,
                'kapasitas' => 4,
                'terisi' => 4,
                'status' => 'Penuh',
                'fasilitas' => 'Tempat tidur, lemari pakaian, meja belajar, kamar mandi dalam',
            ],
            [
                'nama_kamar' => 'Kamar 203',
                'nomor_kamar' => '203',
                'gedung' => 'Gedung A',
                'lantai' => 2,
                'kapasitas' => 4,
                'terisi' => 3,
                'status' => 'Tersedia',
                'fasilitas' => 'Tempat tidur, lemari pakaian, meja belajar, kamar mandi dalam',
            ],
        ];

        foreach ($kamars as $data) {
            Kamar::updateOrCreate(
                ['nama_kamar' => $data['nama_kamar']],
                $data
            );
        }
    }
}
