
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PembelajaranController;
use App\Http\Controllers\AbsensiMhsController;
use App\Http\Controllers\PengumumanMhsController;
use App\Http\Controllers\SuratPerizinanMhsController;
use App\Http\Controllers\PengajuanIzinPulangController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Utama dan Autentikasi
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLoginForm']);

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Pendaftaran Akun
|--------------------------------------------------------------------------
*/

Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.post');

/*
|--------------------------------------------------------------------------
| Lupa Kata Sandi
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Dashboard dan Fitur Pengguna yang Sudah Login
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard Mahasiswa
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [AuthController::class, 'dashboard'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dashboard Musyrifah
    |--------------------------------------------------------------------------
    */

    Route::get('/musyrifah/dashboard', [
        AuthController::class,
        'dashboardMusyrifah',
    ])->name('musyrifah.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dashboard Admin
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/dashboard', [
        AuthController::class,
        'dashboardAdmin',
    ])->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dashboard Guru
    |--------------------------------------------------------------------------
    */

    Route::get('/guru/dashboard', [
        AuthController::class,
        'dashboardGuru',
    ])->name('guru.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dashboard Pimpinan
    |--------------------------------------------------------------------------
    */

    Route::get('/pimpinan/dashboard', [
        AuthController::class,
        'dashboardPimpinan',
    ])->name('pimpinan.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Kamar Mahasiswa
    |--------------------------------------------------------------------------
    */

    Route::get('/kamar', [AuthController::class, 'kamar'])
        ->name('kamar');

    Route::post('/kamar/pilih', [AuthController::class, 'pilihKamar'])
        ->name('kamar.pilih');

    /*
    |--------------------------------------------------------------------------
    | Pembayaran Mahasiswa
    |--------------------------------------------------------------------------
    */

    Route::get('/pembayaran', [PembayaranController::class, 'index'])
        ->name('pembayaran');

    Route::post('/pembayaran/bayar', [PembayaranController::class, 'bayar'])
        ->name('pembayaran.bayar');

    Route::post('/pembayaran/reset', [PembayaranController::class, 'reset'])
        ->name('pembayaran.reset');

    /*
    |--------------------------------------------------------------------------
    | Pendaftaran Mahasiswa
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Pembelajaran Mahasiswa
    |--------------------------------------------------------------------------
    */

    Route::get('/pembelajaran-mhs', [PembelajaranController::class, 'index'])
        ->name('pembelajaran.mhs');

    /*
    |--------------------------------------------------------------------------
    | Absensi Mahasiswa
    |--------------------------------------------------------------------------
    */

    Route::get('/absensi-mhs', [AbsensiMhsController::class, 'index'])
        ->name('absensi.mhs');

    /*
    |--------------------------------------------------------------------------
    | Pengumuman Mahasiswa
    |--------------------------------------------------------------------------
    */

    Route::get('/pengumuman-mhs', [PengumumanMhsController::class, 'index'])
        ->name('pengumuman.mhs');

    Route::get('/surat-perizinan-mhs', [SuratPerizinanMhsController::class, 'index'])
    ->name('surat.perizinan.mhs');

    
Route::get('/surat-perizinan-mhs/pengajuan-izin-pulang', [
    PengajuanIzinPulangController::class,
    'index'
])->name('surat.perizinan.mhs.pengajuan');

Route::post('/surat-perizinan-mhs/pengajuan-izin-pulang', [
    PengajuanIzinPulangController::class,
    'store'
])->name('surat.perizinan.mhs.pengajuan.store');

});
