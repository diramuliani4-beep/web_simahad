
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
use App\Http\Controllers\ProfilMhsController;

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
| HALAMAN UTAMA DAN AUTENTIKASI
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

// Registrasi akun
Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.post');

// Lupa password
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');


/*
|--------------------------------------------------------------------------
| FITUR PENGGUNA YANG SUDAH LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [AuthController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/dashboard_admin', [AuthController::class, 'dashboard'])
        ->name('dashboard_admin');

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
    | FITUR MAHASISWA
    |--------------------------------------------------------------------------
    */

    // Kamar mahasiswa
    Route::get('/kamar', [AuthController::class, 'kamar'])
        ->name('kamar');

    Route::post('/kamar/pilih', [AuthController::class, 'pilihKamar'])
        ->name('kamar.pilih');

    // Profil mahasiswa
    Route::get('/profil-mhs', [ProfilMhsController::class, 'index'])
        ->name('profil.mhs');

    Route::put('/profil-mhs', [ProfilMhsController::class, 'update'])
        ->name('profil.mhs.update');

    // Pembayaran mahasiswa
    Route::get('/pembayaran', [PembayaranController::class, 'index'])
        ->name('pembayaran');

    Route::post('/pembayaran/bayar', [PembayaranController::class, 'bayar'])
        ->name('pembayaran.bayar');

    Route::post('/pembayaran/reset', [PembayaranController::class, 'reset'])
        ->name('pembayaran.reset');


    /*
    |--------------------------------------------------------------------------
    | PENDAFTARAN MAHASISWA
    |--------------------------------------------------------------------------
    |
    | STEP 1 : DATA DIRI
    | STEP 2 : DATA AKADEMIK
    | STEP 3 : PERSYARATAN
    | STEP 4 : KONFIRMASI
    | STEP 5 : PILIH KAMAR
    |
    */

    // STEP 1: DATA DIRI
    Route::get('/pendaftaran/pribadi', [
        PendaftaranController::class,
        'pribadi',
    ])->name('pendaftaran.pribadi');

    Route::post('/pendaftaran/pribadi', [
        PendaftaranController::class,
        'simpanPribadi',
    ])->name('pendaftaran.simpanPribadi');


    // STEP 2: DATA AKADEMIK
    Route::get('/pendaftaran/akademik', [
        PendaftaranController::class,
        'akademik',
    ])->name('pendaftaran.akademik');

    Route::post('/pendaftaran/akademik', [
        PendaftaranController::class,
        'simpanAkademik',
    ])->name('pendaftaran.simpanAkademik');


    // STEP 3: PERSYARATAN
    Route::get('/pendaftaran/persyaratan', [
        PendaftaranController::class,
        'persyaratan',
    ])->name('pendaftaran.persyaratan');

    Route::post('/pendaftaran/persyaratan', [
        PendaftaranController::class,
        'simpanDokumen',
    ])->name('pendaftaran.simpanDokumen');


    // STEP 4: KONFIRMASI
    Route::get('/pendaftaran/konfirmasi', [
        PendaftaranController::class,
        'konfirmasi',
    ])->name('pendaftaran.konfirmasi');


    // STEP 5: PILIH KAMAR
    Route::get('/pendaftaran/pilih-kamar', [
        PendaftaranController::class,
        'pilihKamarMhs',
    ])->name('pendaftaran.pilihKamarMhs');

    // Detail kamar
    Route::get('/pendaftaran/pilih-kamar/{kamarId}', [
        PendaftaranController::class,
        'detailKamarMhs',
    ])->whereNumber('kamarId')
      ->name('pendaftaran.detailKamarMhs');

    // Simpan pilihan kamar
    Route::post('/pendaftaran/pilih-kamar', [
        PendaftaranController::class,
        'simpanPilihanKamarMhs',
    ])->name('pendaftaran.simpanPilihanKamarMhs');


    // Selesaikan pendaftaran
    Route::post('/pendaftaran/selesai', [
        PendaftaranController::class,
        'selesai',
    ])->name('pendaftaran.selesai');


    // Pembelajaran mahasiswa
    Route::get('/pembelajaran-mhs', [
        PembelajaranController::class,
        'index',
    ])->name('pembelajaran.mhs');


    // Absensi mahasiswa
    Route::get('/absensi-mhs', [
        AbsensiMhsController::class,
        'index',
    ])->name('absensi.mhs');


    // Pengumuman mahasiswa
    Route::get('/pengumuman-mhs', [
        PengumumanMhsController::class,
        'index',
    ])->name('pengumuman.mhs');


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
    | FITUR ADMIN
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


    // Verifikasi pengguna
    Route::post('/admin/manajemen-pengguna/{id}/verifikasi', [
        AdminManajemenPenggunaController::class,
        'verifikasi',
    ])->whereNumber('id')
      ->name('admin.manajemen-pengguna.verifikasi');


    // Tolak pendaftaran akun
    Route::post('/admin/manajemen-pengguna/{id}/tolak', [
        AdminManajemenPenggunaController::class,
        'tolak',
    ])->whereNumber('id')
      ->name('admin.manajemen-pengguna.tolak');


    // Edit pengguna
    Route::get('/admin/manajemen-pengguna/{user}/edit', [
        AdminManajemenPenggunaController::class,
        'edit',
    ])->whereNumber('user')
      ->name('admin.manajemen-pengguna.edit');


    // Update pengguna
    Route::put('/admin/manajemen-pengguna/{user}', [
        AdminManajemenPenggunaController::class,
        'update',
    ])->whereNumber('user')
      ->name('admin.manajemen-pengguna.update');


    // Hapus pengguna
    Route::delete('/admin/manajemen-pengguna/{user}', [
        AdminManajemenPenggunaController::class,
        'destroy',
    ])->whereNumber('user')
      ->name('admin.manajemen-pengguna.destroy');


    // Hak akses
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


    /*
    |--------------------------------------------------------------------------
    | DATA PEGAWAI ADMIN
    |--------------------------------------------------------------------------
    */

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


    // Foto pegawai
    Route::get('/admin/data-pegawai/{pegawai}/foto', [
        AdminDataPegawaiController::class,
        'foto',
    ])->whereNumber('pegawai')
      ->name('admin.data-pegawai.foto');


    // Edit pegawai
    Route::get('/admin/data-pegawai/{pegawai}/edit', [
        AdminDataPegawaiController::class,
        'edit',
    ])->whereNumber('pegawai')
      ->name('admin.data-pegawai.edit');


    // Update pegawai
    Route::put('/admin/data-pegawai/{pegawai}', [
        AdminDataPegawaiController::class,
        'update',
    ])->whereNumber('pegawai')
      ->name('admin.data-pegawai.update');


    // Hapus pegawai
    Route::delete('/admin/data-pegawai/{pegawai}', [
        AdminDataPegawaiController::class,
        'destroy',
    ])->whereNumber('pegawai')
      ->name('admin.data-pegawai.destroy');


    // Data mahasiswa
    Route::get('/admin/data-mahasiswa', [
        AdminDataMahasiswaController::class,
        'index',
    ])->name('admin.data-mahasiswa.index');


    /*
    |--------------------------------------------------------------------------
    | FITUR GURU
    |--------------------------------------------------------------------------
    */

    // Jadwal saya
    Route::get('/guru/jadwal-saya', [
        GuruJadwalSayaController::class,
        'index',
    ])->name('guru.jadwal-saya');


    // Absensi guru
    Route::get('/guru/absensi', [
        GuruAbsensiController::class,
        'index',
    ])->name('guru.absensi');


    // Detail jadwal guru
    Route::get('/guru/jadwal-saya/detail/{mapel}', function ($mapel) {

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

        abort_unless(isset($dataMapel[$mapel]), 404);

        return view('guru.detail_jadwal', $dataMapel[$mapel]);

    })->name('guru.jadwal.detail');


    // Form absensi guru
    Route::get('/guru/absensi/isi', function () {
        return view('guru.isi_absensi');
    })->name('guru.absensi.isi');


    // Kegiatan dokumentasi
    Route::get('/guru/kegiatan-dokumentasi', [
        GuruKegiatanDokumentasiController::class,
        'index',
    ])->name('guru.kegiatan-dokumentasi');


    /*
    |--------------------------------------------------------------------------
    | FITUR PIMPINAN
    |--------------------------------------------------------------------------
    */

    // Data penghuni
    Route::get('/pimpinan/data-penghuni', [
        PimpinanDataPenghuniController::class,
        'index',
    ])->name('pimpinan.data-penghuni');


    // Laporan Ma'had
    Route::get('/pimpinan/laporan-mahad', [
        PimpinanLaporanMahadController::class,
        'index',
    ])->name('pimpinan.laporan-mahad');


    // Pembelajaran
    Route::get('/pimpinan/pembelajaran', [
        PimpinanPembelajaranController::class,
        'index',
    ])->name('pimpinan.pembelajaran');


    // Laporan penghuni
    Route::get('/pimpinan/laporan-penghuni', [
        PimpinanLaporanMahadController::class,
        'penghuni',
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
    | FITUR MUSYRIFAH
    |--------------------------------------------------------------------------
    */

    // Pendaftaran mahasiswa
    Route::get('/musyrifah/pendaftaran', [
        MusyrifahPendaftaranController::class,
        'index',
    ])->name('musyrifah.pendaftaran');


    // Validasi pengajuan pendaftaran
    Route::patch('/musyrifah/pendaftaran/{pendaftaran}/status', [
        MusyrifahPendaftaranController::class,
        'updateStatus',
    ])->name('musyrifah.pendaftaran.status');


    // Pemeriksaan berkas
    Route::get('/musyrifah/pendaftaran/{pendaftaran}/berkas/{jenis}', [
        MusyrifahPendaftaranController::class,
        'berkas',
    ])->whereIn('jenis', ['penerimaan', 'pembayaran'])
      ->name('musyrifah.pendaftaran.berkas');


    // Data kamar
    Route::get('/musyrifah/data-kamar', [
        MusyrifahDataKamarController::class,
        'index',
    ])->name('musyrifah.data_kamar');


    // Pembayaran
    Route::get('/musyrifah/pembayaran', [
        MusyrifahPembayaranController::class,
        'index',
    ])->name('musyrifah.pembayaran');


    // Manajemen pembelajaran
    Route::get('/musyrifah/manajemen-pembelajaran', [
        MusyrifahManajemenPembelajaranController::class,
        'index',
    ])->name('musyrifah.manajemen_pembelajaran');


    // Jadwal saya Musyrifah
    Route::get('/musyrifah/jadwal-saya', function (\Illuminate\Http\Request $request) {

        $role = strtolower((string) $request->user()?->role);

        abort_unless(
            in_array($role, ['musyrifah', 'pengelola'], true),
            403
        );

        return view('guru.jadwal_saya');

    })->name('musyrifah.jadwal-saya');


    // Absensi Musyrifah
    Route::get('/musyrifah/absensi', function (\Illuminate\Http\Request $request) {

        $role = strtolower((string) $request->user()?->role);

        abort_unless(
            in_array($role, ['musyrifah', 'pengelola'], true),
            403
        );

        return view('guru.absensi');

    })->name('musyrifah.absensi');


    // Surat perizinan
    Route::get('/musyrifah/surat-perizinan', [
        MusyrifahSuratPerizinanController::class,
        'index',
    ])->name('musyrifah.surat_perizinan');


    // Laporan
    Route::get('/musyrifah/laporan', [
        MusyrifahLaporanController::class,
        'index',
    ])->name('musyrifah.laporan');


    // Pengaturan akun
    Route::get('/musyrifah/pengaturan-akun', [
        MusyrifahPengaturanAkunController::class,
        'index',
    ])->name('musyrifah.pengaturan_akun');


    // Pengumuman
    Route::get('/musyrifah/pengumuman', [
        MusyrifahPengumumanController::class,
        'index',
    ])->name('musyrifah.pengumuman');


    // Detail pengumuman
    Route::get('/musyrifah/detail-pengumuman', [
        MusyrifahDetailPengumumanController::class,
        'index',
    ])->name('musyrifah.detail-pengumuman');

});
