<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PembelajaranController;
use App\Http\Controllers\AbsensiMhsController;
use App\Http\Controllers\PengumumanMhsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Pendaftaran Akun (Daftar di sini)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Lupa Kata Sandi
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->name('dashboard')
    ->middleware('auth');

Route::get('/kamar', [AuthController::class, 'kamar'])
    ->name('kamar')
    ->middleware('auth');

Route::post('/kamar/pilih', [AuthController::class, 'pilihKamar'])
    ->name('kamar.pilih')
    ->middleware('auth');

Route::get('/pembayaran', [PembayaranController::class, 'index'])
    ->name('pembayaran')
    ->middleware('auth');

Route::post('/pembayaran/bayar', [PembayaranController::class, 'bayar'])
    ->name('pembayaran.bayar')
    ->middleware('auth');

Route::post('/pembayaran/reset', [PembayaranController::class, 'reset'])
    ->name('pembayaran.reset')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Pendaftaran
|--------------------------------------------------------------------------
*/

Route::get('/pendaftaran/pribadi', [PendaftaranController::class, 'pribadi'])
    ->name('pendaftaran.pribadi')
    ->middleware('auth');

Route::post('/pendaftaran/pribadi', [PendaftaranController::class, 'simpanPribadi'])
    ->name('pendaftaran.simpanPribadi')
    ->middleware('auth');

Route::get('/pendaftaran/akademik', [PendaftaranController::class, 'akademik'])
    ->name('pendaftaran.akademik')
    ->middleware('auth');

Route::post('/pendaftaran/akademik', [PendaftaranController::class, 'simpanAkademik'])
    ->name('pendaftaran.simpanAkademik')
    ->middleware('auth');

Route::get('/pendaftaran/dokumen', [PendaftaranController::class, 'dokumen'])
    ->name('pendaftaran.dokumen')
    ->middleware('auth');

Route::post('/pendaftaran/dokumen', [PendaftaranController::class, 'simpanDokumen'])
    ->name('pendaftaran.simpanDokumen')
    ->middleware('auth');

Route::get('/pendaftaran/konfirmasi', [PendaftaranController::class, 'konfirmasi'])
    ->name('pendaftaran.konfirmasi')
    ->middleware('auth');

Route::post('/pendaftaran/selesai', [PendaftaranController::class, 'selesai'])
    ->name('pendaftaran.selesai')
    ->middleware('auth');

Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->name('dashboard')
    ->middleware('auth');


Route::get('/pembelajaran-mhs', [PembelajaranController::class, 'index'])
    ->name('pembelajaran.mhs')
    ->middleware('auth');

Route::get('/absensi-mhs', [AbsensiMhsController::class, 'index'])
    ->name('absensi.mhs');

Route::get('/pengumuman-mhs', [PengumumanMhsController::class, 'index'])
    ->name('pengumuman.mhs');