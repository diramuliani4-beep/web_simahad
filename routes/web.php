<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PembelajaranController;
use App\Http\Controllers\AbsensiMhsController;
use App\Http\Controllers\PengumumanMhsController;
use App\Http\Controllers\SuratPerizinanMhsController;
use App\Http\Controllers\PengajuanIzinPulangController;

use App\Http\Controllers\AdminLogAktivitasController;
use App\Http\Controllers\AdminManajemenPenggunaController;
use App\Http\Controllers\AdminPengaturanSistemController;
use App\Http\Controllers\AdminHakAksesController;
use App\Http\Controllers\AdminDataPegawaiController;
use App\Http\Controllers\AdminDataMahasiswaController;

use App\Http\Controllers\GuruJadwalSayaController;
use App\Http\Controllers\GuruAbsensiController;
use App\Http\Controllers\GuruKegiatanDokumentasiController;

use App\Http\Controllers\PimpinanDataPenghuniController;
use App\Http\Controllers\PimpinanLaporanMahadController;
use App\Http\Controllers\PimpinanPembelajaranController;

use App\Http\Controllers\MusyrifahPendaftaranController;
use App\Http\Controllers\MusyrifahDataKamarController;
use App\Http\Controllers\MusyrifahPembayaranController;
use App\Http\Controllers\MusyrifahManajemenPembelajaranController;
use App\Http\Controllers\MusyrifahSuratPerizinanController;
use App\Http\Controllers\MusyrifahLaporanController;
use App\Http\Controllers\MusyrifahPengaturanAkunController;
use App\Http\Controllers\MusyrifahPengumumanController;
use App\Http\Controllers\MusyrifahDetailPengumumanController;


/*
|--------------------------------------------------------------------------
| Halaman Utama dan Autentikasi
|--------------------------------------------------------------------------
*/

// Halaman utama
Route::get('/', [AuthController::class, 'showLoginForm']);

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/konfirmasi-keluar', function () {
    return view('auth.confirm-logout');
})->middleware('auth')->name('logout.confirm');

// Pendaftaran akun
Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.post');

// Lupa kata sandi
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');


/*
|--------------------------------------------------------------------------
| Fitur Pengguna yang Sudah Login
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard umum
    Route::get('/dashboard', [AuthController::class, 'dashboard'])
        ->name('dashboard');

    // Dashboard lama untuk kompatibilitas
    Route::get('/dashboard_admin', [AuthController::class, 'dashboard'])
        ->name('dashboard_admin');

    // Dashboard berdasarkan peran
    Route::get('/musyrifah/dashboard', [
        AuthController::class,
        'dashboardMusyrifah',
    ])->name('musyrifah.dashboard');

    Route::get('/admin/dashboard', [
        AuthController::class,
        'dashboardAdmin',
    ])->name('admin.dashboard');

    Route::get('/admin/dashboard_admin', [
        AuthController::class,
        'dashboardAdmin',
    ])->name('admin.dashboard_admin');

    Route::get('/guru/dashboard', [
        AuthController::class,
        'dashboardGuru',
    ])->name('guru.dashboard');

    Route::get('/pimpinan/dashboard', [
        AuthController::class,
        'dashboardPimpinan',
    ])->name('pimpinan.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Fitur Mahasiswa
    |--------------------------------------------------------------------------
    */

    // Kamar mahasiswa
    Route::get('/kamar', [AuthController::class, 'kamar'])
        ->name('kamar');

    Route::post('/kamar/pilih', [AuthController::class, 'pilihKamar'])
        ->name('kamar.pilih');

    // Pembayaran mahasiswa
    Route::get('/pembayaran', [PembayaranController::class, 'index'])
        ->name('pembayaran');

    Route::post('/pembayaran/bayar', [PembayaranController::class, 'bayar'])
        ->name('pembayaran.bayar');

    Route::post('/pembayaran/reset', [PembayaranController::class, 'reset'])
        ->name('pembayaran.reset');

    // Pendaftaran mahasiswa
    Route::get('/pendaftaran/pribadi', [PendaftaranController::class, 'pribadi'])
        ->name('pendaftaran.pribadi');

    Route::post('/pendaftaran/pribadi', [PendaftaranController::class, 'simpanPribadi'])
        ->name('pendaftaran.simpanPribadi');

    Route::get('/pendaftaran/akademik', [PendaftaranController::class, 'akademik'])
        ->name('pendaftaran.akademik');

    Route::post('/pendaftaran/akademik', [PendaftaranController::class, 'simpanAkademik'])
        ->name('pendaftaran.simpanAkademik');

    Route::get('/pendaftaran/dokumen', [PendaftaranController::class, 'dokumen'])
        ->name('pendaftaran.dokumen');

    Route::post('/pendaftaran/dokumen', [PendaftaranController::class, 'simpanDokumen'])
        ->name('pendaftaran.simpanDokumen');

    Route::get('/pendaftaran/konfirmasi', [PendaftaranController::class, 'konfirmasi'])
        ->name('pendaftaran.konfirmasi');

    
// Pilih kamar mahasiswa
Route::get('/pendaftaran/pilih-kamar', [
    PendaftaranController::class,
    'pilihKamarMhs',
])->name('pendaftaran.pilihKamarMhs');

// Detail kamar mahasiswa
Route::get('/pendaftaran/pilih-kamar/{kamarId}', [
    PendaftaranController::class,
    'detailKamarMhs',
])->whereNumber('kamarId')
  ->name('pendaftaran.detailKamarMhs');

// Simpan pengajuan kamar mahasiswa
Route::post('/pendaftaran/pilih-kamar', [
    PendaftaranController::class,
    'simpanPilihanKamarMhs',
])->name('pendaftaran.simpanPilihanKamarMhs');


    Route::post('/pendaftaran/selesai', [PendaftaranController::class, 'selesai'])
        ->name('pendaftaran.selesai');

    // Pembelajaran mahasiswa
    Route::get('/pembelajaran-mhs', [PembelajaranController::class, 'index'])
        ->name('pembelajaran.mhs');

    // Absensi mahasiswa
    Route::get('/absensi-mhs', [AbsensiMhsController::class, 'index'])
        ->name('absensi.mhs');

    // Pengumuman mahasiswa
    Route::get('/pengumuman-mhs', [PengumumanMhsController::class, 'index'])
        ->name('pengumuman.mhs');

    // Surat dan perizinan mahasiswa
    Route::get('/surat-perizinan-mhs', [
        SuratPerizinanMhsController::class,
        'index',
    ])->name('surat.perizinan.mhs');

    Route::get('/surat-perizinan-mhs/pengajuan-izin-pulang', [
        PengajuanIzinPulangController::class,
        'index',
    ])->name('surat.perizinan.mhs.pengajuan');

    Route::post('/surat-perizinan-mhs/pengajuan-izin-pulang', [
        PengajuanIzinPulangController::class,
        'store',
    ])->name('surat.perizinan.mhs.pengajuan.store');

    /*
    |--------------------------------------------------------------------------
    | Fitur Admin
    |--------------------------------------------------------------------------
    */

    // Manajemen pengguna
    Route::get('/admin/manajemen-pengguna', [
        AdminManajemenPenggunaController::class,
        'index',
    ])->name('admin.manajemen-pengguna');

    Route::get('/admin/manajemen-pengguna/tambah', [
        AdminManajemenPenggunaController::class,
        'create',
    ])->name('admin.manajemen-pengguna.create');

    Route::post('/admin/manajemen-pengguna', [
        AdminManajemenPenggunaController::class,
        'store',
    ])->name('admin.manajemen-pengguna.store');

    // Verifikasi akun pengguna
    Route::post('/admin/manajemen-pengguna/{id}/verifikasi', [
        AdminManajemenPenggunaController::class,
        'verifikasi',
    ])->name('admin.manajemen-pengguna.verifikasi');

    // Tolak pendaftaran akun
    Route::post('/admin/manajemen-pengguna/{id}/tolak', [
        AdminManajemenPenggunaController::class,
        'tolak',
    ])->name('admin.manajemen-pengguna.tolak');

    // Hak akses admin (dipertahankan untuk kompatibilitas).
    Route::get('/admin/hak-akses', [
        AdminHakAksesController::class,
        'index',
    ])->name('admin.hak-akses');

    // Pengaturan sistem
    Route::get('/admin/pengaturan-sistem', [
        AdminPengaturanSistemController::class,
        'index',
    ])->name('admin.pengaturan-sistem');

    // Log aktivitas
    Route::get('/admin/log-aktivitas', [
        AdminLogAktivitasController::class,
        'index',
    ])->name('admin.log-aktivitas');

    Route::get('/admin/manajemen-pengguna/{user}/edit', [
    AdminManajemenPenggunaController::class, 'edit',
])->whereNumber('user')->name('admin.manajemen-pengguna.edit');

Route::put('/admin/manajemen-pengguna/{user}', [
    AdminManajemenPenggunaController::class, 'update',
])->whereNumber('user')->name('admin.manajemen-pengguna.update');

Route::delete('/admin/manajemen-pengguna/{user}', [
    AdminManajemenPenggunaController::class, 'destroy',
])->whereNumber('user')->name('admin.manajemen-pengguna.destroy');



    // DATA PEGAWAI - daftar, lengkapi biodata, dan edit.
    // Urutan route statis sebelum route dinamis {pegawai}.
    Route::get('/admin/data-pegawai', [
        AdminDataPegawaiController::class,
        'index',
    ])->name('admin.data-pegawai.index');

    Route::get('/admin/data-pegawai/tambah', [
        AdminDataPegawaiController::class,
        'create',
    ])->name('admin.data-pegawai.create');

    Route::post('/admin/data-pegawai', [
        AdminDataPegawaiController::class,
        'store',
    ])->name('admin.data-pegawai.store');

    // Foto pegawai: diakses oleh Admin melalui controller, bukan URL storage publik.
    Route::get('/admin/data-pegawai/{pegawai}/foto', [
        AdminDataPegawaiController::class,
        'foto',
    ])->whereNumber('pegawai')->name('admin.data-pegawai.foto');

    Route::get('/admin/data-pegawai/{pegawai}/edit', [
        AdminDataPegawaiController::class,
        'edit',
    ])->whereNumber('pegawai')->name('admin.data-pegawai.edit');

    Route::put('/admin/data-pegawai/{pegawai}', [
        AdminDataPegawaiController::class,
        'update',
    ])->whereNumber('pegawai')->name('admin.data-pegawai.update');

    // Hapus biodata pegawai saja, tanpa menghapus akun pengguna.
    Route::delete('/admin/data-pegawai/{pegawai}', [
        AdminDataPegawaiController::class,
        'destroy',
    ])->whereNumber('pegawai')->name('admin.data-pegawai.destroy');

    // Data Mahasiswa
    Route::get('/admin/data-mahasiswa', [
        AdminDataMahasiswaController::class,
        'index',
    ])->name('admin.data-mahasiswa.index');



    /*
    |--------------------------------------------------------------------------
    | Fitur Guru
    |--------------------------------------------------------------------------
    */

    Route::get('/guru/jadwal-saya', [
        GuruJadwalSayaController::class,
        'index',
    ])->name('guru.jadwal-saya');

    Route::get('/guru/absensi', [
        GuruAbsensiController::class,
        'index',
    ])->name('guru.absensi');

    // Detail jadwal guru (data sementara).
    Route::get('/guru/jadwal-saya/detail/{mapel}', function ($mapel) {

        // Data contoh mata pelajaran untuk menampilkan halaman detail.
        // Belum terhubung dengan database atau CRUD.
        $dataMapel = [
            'akhlak' => [
                'mataPelajaran' => 'Akhlak dan Adab',
                'kelompok' => 'Kelompok A',
                'instruktur' => 'Nama Guru',
                'jadwal' => 'Senin, 08.00–09.00',
                'totalPertemuan' => 16,
                'absensiTerisi' => 8,
            ],

            'alquran' => [
                'mataPelajaran' => "Al-Qur'an",
                'kelompok' => 'Kelompok B',
                'instruktur' => 'Nama Guru',
                'jadwal' => 'Senin, 13.00–14.00',
                'totalPertemuan' => 16,
                'absensiTerisi' => 6,
            ],
        ];

        // Jika kode mata pelajaran tidak tersedia, tampilkan halaman 404.
        abort_unless(isset($dataMapel[$mapel]), 404);

        // Kirim data contoh ke halaman detail_jadwal.blade.php.
        return view('guru.detail_jadwal', $dataMapel[$mapel]);

    })->name('guru.jadwal.detail');

    // Form isi absensi guru.
    Route::get('/guru/absensi/isi', function () {

        // Menampilkan halaman formulir pengisian absensi.
        return view('guru.isi_absensi');

    })->name('guru.absensi.isi');

    Route::get('/guru/kegiatan-dokumentasi', [
        GuruKegiatanDokumentasiController::class,
        'index',
    ])->name('guru.kegiatan-dokumentasi');


    /*
    |--------------------------------------------------------------------------
    | Fitur Pimpinan
    |--------------------------------------------------------------------------
    */

    Route::get('/pimpinan/data-penghuni', [
        PimpinanDataPenghuniController::class,
        'index',
    ])->name('pimpinan.data-penghuni');

    Route::get('/pimpinan/laporan-mahad', [
        PimpinanLaporanMahadController::class,
        'index',
    ])->name('pimpinan.laporan-mahad');

    Route::get('/pimpinan/pembelajaran', [
        PimpinanPembelajaranController::class,
        'index',
    ])->name('pimpinan.pembelajaran');

    // Halaman Laporan Penghuni
    Route::get('/pimpinan/laporan-penghuni', [
        PimpinanLaporanMahadController::class,
        'penghuni'
    ])->name('pimpinan.laporan-penghuni');

    // Laporan kamar
    Route::get('/pimpinan/laporan-kamar', [
        PimpinanLaporanMahadController::class,
        'kamar',
    ])->name('pimpinan.laporan-kamar');

    // Laporan pembelajaran
    Route::get('/pimpinan/laporan-pembelajaran', [
        PimpinanLaporanMahadController::class,
        'pembelajaran',
    ])->name('pimpinan.laporan-pembelajaran');

    // Laporan pembayaran
    Route::get('/pimpinan/laporan-pembayaran', [
        PimpinanLaporanMahadController::class,
        'pembayaran',
    ])->name('pimpinan.laporan-pembayaran');

    // Laporan perizinan
    Route::get('/pimpinan/laporan-perizinan', [
        PimpinanLaporanMahadController::class,
        'perizinan',
    ])->name('pimpinan.laporan-perizinan');

    /*
    |--------------------------------------------------------------------------
    | Fitur Musyrifah
    |--------------------------------------------------------------------------
    */

    Route::get('/musyrifah/pendaftaran', [
        MusyrifahPendaftaranController::class,
        'index',
    ])->name('musyrifah.pendaftaran');

    // Validasi pengajuan oleh Musyrifah/Pengelola.
    Route::patch('/musyrifah/pendaftaran/{pendaftaran}/status', [
        MusyrifahPendaftaranController::class,
        'updateStatus',
    ])->name('musyrifah.pendaftaran.status');

    // Pemeriksaan bukti penerimaan UIN dan bukti pembayaran oleh Musyrifah.
    Route::get('/musyrifah/pendaftaran/{pendaftaran}/berkas/{jenis}', [
        MusyrifahPendaftaranController::class,
        'berkas',
    ])->whereIn('jenis', ['penerimaan', 'pembayaran'])
        ->name('musyrifah.pendaftaran.berkas');

    Route::get('/musyrifah/data-kamar', [
        MusyrifahDataKamarController::class,
        'index',
    ])->name('musyrifah.data_kamar');

    Route::get('/musyrifah/pembayaran', [
        MusyrifahPembayaranController::class,
        'index',
    ])->name('musyrifah.pembayaran');

    Route::get('/musyrifah/manajemen-pembelajaran', [
        MusyrifahManajemenPembelajaranController::class,
        'index',
    ])->name('musyrifah.manajemen_pembelajaran');


    // Jadwal Saya Musyrifah menggunakan tampilan Guru.
    Route::get('/musyrifah/jadwal-saya', function (\Illuminate\Http\Request $request) {

        $role = strtolower((string) $request->user()?->role);

        abort_unless(
            in_array($role, ['musyrifah', 'pengelola'], true),
            403
        );

        return view('guru.jadwal_saya');

    })->name('musyrifah.jadwal-saya');


    // Absensi Musyrifah menggunakan tampilan Guru.
    Route::get('/musyrifah/absensi', function (\Illuminate\Http\Request $request) {

        $role = strtolower((string) $request->user()?->role);

        abort_unless(
            in_array($role, ['musyrifah', 'pengelola'], true),
            403
        );

        return view('guru.absensi');

    })->name('musyrifah.absensi');


    Route::get('/musyrifah/surat-perizinan', [
        MusyrifahSuratPerizinanController::class,
        'index',
    ])->name('musyrifah.surat_perizinan');

    Route::get('/musyrifah/laporan', [
        MusyrifahLaporanController::class,
        'index',
    ])->name('musyrifah.laporan');

    // Pengaturan akun Musyrifah
    Route::get('/musyrifah/pengaturan-akun', [
        MusyrifahPengaturanAkunController::class,
        'index',
    ])->name('musyrifah.pengaturan_akun');

    // Pengumuman Musyrifah
    Route::get('/musyrifah/pengumuman', [
        MusyrifahPengumumanController::class,
        'index',
    ])->name('musyrifah.pengumuman');

    Route::get('/musyrifah/detail-pengumuman', [
        MusyrifahDetailPengumumanController::class,
        'index',
    ])->name('musyrifah.detail-pengumuman');

});