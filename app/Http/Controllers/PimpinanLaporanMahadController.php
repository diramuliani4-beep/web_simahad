<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PimpinanLaporanMahadController extends Controller
{
 // Halaman utama laporan Ma'had
    public function index()
    {
        $laporan = [
            [
                'ikon' => 'users',
                'judul' => 'Laporan Penghuni',
                'deskripsi' => 'Melihat informasi data penghuni Ma’had, termasuk NIM, nama, program studi, angkatan, dan kamar.',
                'url' => route('pimpinan.laporan-penghuni'),
            ],
            [
                'ikon' => 'bed',
                'judul' => 'Laporan Kamar',
                'deskripsi' => 'Melihat informasi penggunaan kamar dan data penghuni berdasarkan kamar.',
                'url' => route('pimpinan.laporan-kamar'),
            ],
            [
                'ikon' => 'book-open',
                'judul' => 'Laporan Pembelajaran',
                'deskripsi' => 'Melihat informasi kegiatan dan pelaksanaan pembelajaran di Ma’had.',
                'url' => route('pimpinan.laporan-pembelajaran'),
            ],
            [
                'ikon' => 'wallet',
                'judul' => 'Laporan Pembayaran',
                'deskripsi' => 'Melihat informasi pembayaran dan administrasi keuangan penghuni Ma’had.',
                'url' => route('pimpinan.laporan-pembayaran'),
            ],
            [
                'ikon' => 'file-check',
                'judul' => 'Laporan Administrasi',
                'deskripsi' => 'Melihat informasi administrasi dan dokumen penghuni Ma’had.',
                'url' => '#',
            ],
        ];

        return view('pimpinan.laporan_mahad', compact('laporan'));
    }

    // Halaman detail laporan penghuni
    public function penghuni(Request $request)
    {
        // Data contoh penghuni
        $semuaPenghuni = [
            [
                'nim' => '202600101',
                'nama' => 'Nadia Putri',
                'prodi' => 'Sistem Informasi',
                'angkatan' => '2026',
                'kamar' => 'Kamar A-01',
                'status' => 'Aktif',
            ],
            [
                'nim' => '202600102',
                'nama' => 'Alya Rahma',
                'prodi' => 'Informatika',
                'angkatan' => '2026',
                'kamar' => 'Kamar A-02',
                'status' => 'Aktif',
            ],
            [
                'nim' => '202600103',
                'nama' => 'Siti Azzahra',
                'prodi' => 'Sistem Informasi',
                'angkatan' => '2026',
                'kamar' => 'Kamar A-03',
                'status' => 'Aktif',
            ],
            [
                'nim' => '202600104',
                'nama' => 'Nurul Hidayah',
                'prodi' => 'Manajemen Pendidikan Islam',
                'angkatan' => '2026',
                'kamar' => 'Kamar B-01',
                'status' => 'Tidak Aktif',
            ],
            [
                'nim' => '202600105',
                'nama' => 'Putri Amelia',
                'prodi' => 'Pendidikan Agama Islam',
                'angkatan' => '2026',
                'kamar' => 'Kamar B-02',
                'status' => 'Aktif',
            ],
        ];

        // Pencarian berdasarkan NIM atau nama
        $keyword = $request->query('cari', '');

        $dataPenghuni = collect($semuaPenghuni);

        $penghuni = $dataPenghuni
            ->filter(function ($item) use ($keyword) {
                return stripos($item['nama'], $keyword) !== false
                    || stripos($item['nim'], $keyword) !== false;
            })
            ->values();

        // Mengirim data ke halaman laporan penghuni
        return view('pimpinan.laporan_penghuni', [
            'penghuni' => $penghuni,
            'totalPenghuni' => $dataPenghuni->count(),
            'penghuniAktif' => $dataPenghuni
                ->where('status', 'Aktif')
                ->count(),
            'keyword' => $keyword,
        ]);
    }
    
public function kamar(Request $request)
{
    $semuaKamar = [
        [
            'lantai' => 'Lantai 1',
            'nomor_kamar' => 'A-01',
            'kapasitas' => 4,
            'jumlah_penghuni' => 3,
            'status' => 'Terisi',
        ],
        [
            'lantai' => 'Lantai 1',
            'nomor_kamar' => 'A-02',
            'kapasitas' => 4,
            'jumlah_penghuni' => 4,
            'status' => 'Penuh',
        ],
        [
            'lantai' => 'Lantai 1',
            'nomor_kamar' => 'A-03',
            'kapasitas' => 4,
            'jumlah_penghuni' => 0,
            'status' => 'Kosong',
        ],
        [
            'lantai' => 'Lantai 2',
            'nomor_kamar' => 'B-01',
            'kapasitas' => 4,
            'jumlah_penghuni' => 2,
            'status' => 'Terisi',
        ],
    ];

    $dataKamar = collect($semuaKamar);

    // Pencarian kamar
    $keyword = $request->query('cari', '');

    $kamar = $dataKamar
        ->filter(function ($item) use ($keyword) {
            return stripos($item['nomor_kamar'], $keyword) !== false
                || stripos($item['lantai'], $keyword) !== false;
        })
        ->values();

    // Statistik seluruh kamar
    $totalKamar = $dataKamar->count();

    $kamarTerisi = $dataKamar
        ->where('jumlah_penghuni', '>', 0)
        ->count();

    $kamarKosong = $dataKamar
        ->where('jumlah_penghuni', 0)
        ->count();

    return view('pimpinan.laporan_kamar', compact(
        'kamar',
        'totalKamar',
        'kamarTerisi',
        'kamarKosong',
        'keyword'
    ));
}


public function pembelajaran(Request $request)
{
    // Data contoh pembelajaran
    $semuaPembelajaran = [
        [
            'kelompok' => 'Kelompok A',
            'pengajar' => 'Ustaz Ahmad',
            'jumlah_pertemuan' => 12,
            'kehadiran' => '90%',
        ],
        [
            'kelompok' => 'Kelompok B',
            'pengajar' => 'Ustazah Fatimah',
            'jumlah_pertemuan' => 10,
            'kehadiran' => '85%',
        ],
        [
            'kelompok' => 'Kelompok C',
            'pengajar' => 'Ustaz Abdullah',
            'jumlah_pertemuan' => 8,
            'kehadiran' => '95%',
        ],
    ];

    $dataPembelajaran = collect($semuaPembelajaran);

    $keyword = $request->query('cari', '');

    $pembelajaran = $dataPembelajaran
        ->filter(function ($item) use ($keyword) {
            return stripos($item['kelompok'], $keyword) !== false
                || stripos($item['pengajar'], $keyword) !== false;
        })
        ->values();

    $totalKelompok = $dataPembelajaran->count();

    $totalPertemuan = $dataPembelajaran->sum('jumlah_pertemuan');

    $totalAbsensi = $dataPembelajaran->sum(function ($item) {
        return (float) str_replace('%', '', $item['kehadiran']);
    });

    return view('pimpinan.laporan_pembelajaran', compact(
        'pembelajaran',
        'totalKelompok',
        'totalPertemuan',
        'totalAbsensi',
        'keyword'
    ));
}


public function pembayaran(Request $request)
{
    // Data contoh pembayaran
    $semuaPembayaran = [
        [
            'nim' => '202600101',
            'nama' => 'Nadia Putri',
            'jenis_tagihan' => 'Uang Asrama',
            'jumlah_pembayaran' => 1500000,
            'tanggal_pembayaran' => '01-11-2026',
            'status_pembayaran' => 'Lunas',
        ],
        [
            'nim' => '202600102',
            'nama' => 'Alya Rahma',
            'jenis_tagihan' => 'Uang Makan',
            'jumlah_pembayaran' => 750000,
            'tanggal_pembayaran' => '02-11-2026',
            'status_pembayaran' => 'Belum Dibayar',
        ],
        [
            'nim' => '202600103',
            'nama' => 'Siti Azzahra',
            'jenis_tagihan' => 'Uang Asrama',
            'jumlah_pembayaran' => 1500000,
            'tanggal_pembayaran' => '03-11-2026',
            'status_pembayaran' => 'Lunas',
        ],
    ];

    $dataPembayaran = collect($semuaPembayaran);

    $keyword = $request->query('cari', '');

    // Pencarian berdasarkan NIM atau nama
    $pembayaran = $dataPembayaran
        ->filter(function ($item) use ($keyword) {
            return stripos($item['nim'], $keyword) !== false
                || stripos($item['nama'], $keyword) !== false;
        })
        ->values();

    // Statistik berdasarkan seluruh data, bukan hasil pencarian
    $totalTagihan = $dataPembayaran->count();

    $totalSudahDibayar = $dataPembayaran
        ->where('status_pembayaran', 'Lunas')
        ->count();

    $totalBelumDibayar = $dataPembayaran
        ->where('status_pembayaran', 'Belum Dibayar')
        ->count();

    return view('pimpinan.laporan_pembayaran', compact(
        'pembayaran',
        'totalTagihan',
        'totalSudahDibayar',
        'totalBelumDibayar',
        'keyword'
    ));
}

}