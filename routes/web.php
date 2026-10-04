<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PendaftaranController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->name('dashboard')
    ->middleware('auth');

Route::get('/kamar', [AuthController::class, 'kamar'])
    ->name('kamar')
    ->middleware('auth');

Route::post('/kamar/pilih', [AuthController::class, 'pilihKamar'])
    ->name('kamar.pilih')
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

Route::post('/pendaftaran/selesai', [PendaftaranController::class, 'selesai'])
    ->name('pendaftaran.selesai')
    ->middleware('auth');