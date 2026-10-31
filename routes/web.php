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
use App\Http\Controllers\AdminManajemenPenggunaController;
use App\Http\Controllers\AdminPengaturanSistemController;
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
use App\Http\Controllers\AdminLogAktivitasController;

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

    // Dashboard
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

    Route::get('/guru/dashboard', [
        AuthController::class,
        'dashboardGuru',
    ])->name('guru.dashboard');

    Route::get('/pimpinan/dashboard', [
        AuthController::class,
        'dashboardPimpinan',
    ])->name('pimpinan.dashboard');


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

    // Pengaturan sistem
    Route::get('/admin/pengaturan-sistem', [
        AdminPengaturanSistemController::class,
        'index',
    ])->name('admin.pengaturan-sistem');

    Route::get('/admin/manajemen-pengguna/tambah', [
        AdminManajemenPenggunaController::class,
        'create'
    ])->name('admin.manajemen-pengguna.create');

    Route::post('/admin/manajemen-pengguna', [
        AdminManajemenPenggunaController::class,
        'store'
    ])->name('admin.manajemen-pengguna.store');

    Route::get('/admin/log-aktivitas', [
        AdminLogAktivitasController::class,
        'index'
    ])->name('admin.log-aktivitas');
    
    Route::get('/admin/dashboard_admin', [
        AuthController::class, 'dashboardAdmin'
        ]) ->name('admin.dashboard_admin');


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


    /*
    |--------------------------------------------------------------------------
    | Fitur Musyrifah
    |--------------------------------------------------------------------------
    */

    Route::get('/musyrifah/pendaftaran', [
        MusyrifahPendaftaranController::class,
        'index',
    ])->name('musyrifah.pendaftaran');

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

    Route::get('/musyrifah/surat-perizinan', [
        MusyrifahSuratPerizinanController::class,
        'index',
    ])->name('musyrifah.surat_perizinan');

    Route::get('/musyrifah/laporan', [
        MusyrifahLaporanController::class,
        'index',
    ])->name('musyrifah.laporan');

});