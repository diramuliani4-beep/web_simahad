<?php

use App\Http\Controllers\AbsensiMhsController;
use App\Http\Controllers\AdminDataMahasiswaController;
use App\Http\Controllers\AdminDataPegawaiController;
use App\Http\Controllers\AdminHakAksesController;
use App\Http\Controllers\AdminLogAktivitasController;
use App\Http\Controllers\AdminManajemenPenggunaController;
use App\Http\Controllers\AdminPengaturanSistemController;
use App\Http\Controllers\AktivasiAkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataMahasiswaMhsController;
use App\Http\Controllers\GuruAbsensiController;
use App\Http\Controllers\GuruJadwalSayaController;
use App\Http\Controllers\GuruKegiatanDokumentasiController;
use App\Http\Controllers\GuruPembelajaranController;
use App\Http\Controllers\GuruPengumpulanController;
use App\Http\Controllers\GuruPenilaianController;
use App\Http\Controllers\GuruTugasController;
use App\Http\Controllers\MusyrifahDataKamarController;
use App\Http\Controllers\MusyrifahDetailPengumumanController;
use App\Http\Controllers\MusyrifahLaporanController;
use App\Http\Controllers\MusyrifahManajemenPembelajaranController;
use App\Http\Controllers\MusyrifahPembayaranController;
use App\Http\Controllers\MusyrifahPendaftaranController;
use App\Http\Controllers\MusyrifahPengumumanController;
use App\Http\Controllers\MusyrifahSuratPerizinanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PembelajaranController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PengajuanIzinPulangController;
use App\Http\Controllers\PengaturanAkunController;
use App\Http\Controllers\PengumumanMhsController;
use App\Http\Controllers\PimpinanAuditController;
use App\Http\Controllers\PimpinanDataPenghuniController;
use App\Http\Controllers\PimpinanLaporanMahadController;
use App\Http\Controllers\PimpinanLaporanRegisterController;
use App\Http\Controllers\PimpinanMonitoringController;
use App\Http\Controllers\PimpinanPembelajaranController;
use App\Http\Controllers\ProfilAkunController;
use App\Http\Controllers\ProfilMhsController;
use App\Http\Controllers\SuratPerizinanMhsController;
use App\Http\Controllers\TugasMhsController;
use App\Services\LaporanMahadService;
use App\Services\PimpinanMonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Login dengan Google (OAuth 2.0 melalui Laravel Socialite)
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])
    ->name('auth.google.redirect');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

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
| Aktivasi Akun Staf (Guru, Musyrifah, Pimpinan, Admin)
|--------------------------------------------------------------------------
| Tautan dikirim melalui email; pemilik akun membuat kata sandinya sendiri.
| Halaman ini harus tetap dapat diakses tanpa login.
*/

Route::get('/aktivasi-akun/{token}', [AktivasiAkunController::class, 'form'])
    ->middleware('throttle:30,1')
    ->name('aktivasi.form');

Route::post('/aktivasi-akun/{token}', [AktivasiAkunController::class, 'simpan'])
    ->middleware('throttle:10,1')
    ->name('aktivasi.simpan');

/*
|--------------------------------------------------------------------------
| Fitur Pengguna yang Sudah Login
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard umum
    Route::get('/dashboard', [AuthController::class, 'dashboard'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Akun Mandiri (semua role)
    |--------------------------------------------------------------------------
    */

    Route::post('/pengaturan-akun/foto', [PengaturanAkunController::class, 'updateFoto'])
        ->name('pengaturan-akun.foto.update');

    Route::delete('/pengaturan-akun/foto', [PengaturanAkunController::class, 'hapusFoto'])
        ->name('pengaturan-akun.foto.destroy');

    Route::put('/pengaturan-akun/password', [PengaturanAkunController::class, 'updatePassword'])
        ->name('pengaturan-akun.password.update');

    Route::put('/pengaturan-akun/email', [PengaturanAkunController::class, 'updateEmail'])
        ->name('pengaturan-akun.email.update');

    // Halaman Pengaturan Akun terpadu untuk seluruh role.
    Route::get('/pengaturan-akun', [PengaturanAkunController::class, 'saya'])
        ->name('pengaturan-akun.index');

    /*
    |--------------------------------------------------------------------------
    | Profil dan Data Akun Staf (Guru, Musyrifah, Pimpinan, Admin Sistem)
    |--------------------------------------------------------------------------
    */

    Route::get('/profil', [ProfilAkunController::class, 'profil'])
        ->name('profil.pengguna');

    // Foto profil staf dikelola dari menu kamera pada halaman Lihat Profil.
    // Penanganan unggah tetap memakai alur Pengaturan Akun agar aturan
    // penyimpanan foto seluruh role hanya ada di satu tempat.
    Route::post('/profil/foto', [PengaturanAkunController::class, 'updateFoto'])
        ->name('profil.pengguna.foto.update');

    Route::delete('/profil/foto', [PengaturanAkunController::class, 'hapusFoto'])
        ->name('profil.pengguna.foto.destroy');

    Route::get('/data-akun', [ProfilAkunController::class, 'data'])
        ->name('data.pengguna');

    // Penyimpanan data pribadi dilakukan dari halaman Data Saya.
    Route::put('/data-akun', [ProfilAkunController::class, 'update'])
        ->name('data.pengguna.update');

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

    // Pengajuan pindah kamar, diverifikasi Musyrifah pada menu Pengajuan Kamar
    Route::post('/kamar/pindah', [AuthController::class, 'pindahKamar'])
        ->name('kamar.pindah');

    // Profil mahasiswa
    Route::get('/profil-mhs', [ProfilMhsController::class, 'index'])
        ->name('profil.mhs');

    Route::put('/profil-mhs', [ProfilMhsController::class, 'update'])
        ->name('profil.mhs.update');

    // Pengelolaan foto profil mahasiswa (menu kamera pada halaman profil)
    Route::post('/profil-mhs/foto', [ProfilMhsController::class, 'updateFoto'])
        ->name('profil.mhs.foto.update');

    Route::delete('/profil-mhs/foto', [ProfilMhsController::class, 'hapusFoto'])
        ->name('profil.mhs.foto.destroy');

    // Ubah kata sandi akun mahasiswa dari halaman Pengaturan Akun.
    Route::put('/profil-mhs/password', [PengaturanAkunController::class, 'updatePassword'])
        ->name('profil.mhs.password.update');

    // Data mahasiswa milik akun yang sedang login
    Route::get('/data-mahasiswa', [DataMahasiswaMhsController::class, 'index'])
        ->name('data.mahasiswa');

    Route::put('/data-mahasiswa', [DataMahasiswaMhsController::class, 'update'])
        ->name('data.mahasiswa.update');

    // Pembayaran mahasiswa: angsuran, bukti transfer, pengajuan keterlambatan
    Route::get('/pembayaran', [PembayaranController::class, 'index'])
        ->name('pembayaran');

    // Halaman detail lama diarahkan ke halaman pembayaran terpadu.
    Route::get('/pembayaran/detail', [PembayaranController::class, 'detail'])
        ->name('pembayaran.detail');

    Route::post('/pembayaran/{pembayaran}/bukti', [PembayaranController::class, 'unggahBukti'])
        ->name('pembayaran.bukti');

    Route::post('/pembayaran/{pembayaran}/keterlambatan', [PembayaranController::class, 'ajukanKeterlambatan'])
        ->name('pembayaran.keterlambatan');

    Route::get('/pembayaran/keterlambatan/{pengajuan}/surat', [PembayaranController::class, 'suratKeterlambatan'])
        ->name('pembayaran.keterlambatan.surat');

    // Pendaftaran mahasiswa
    Route::get('/pendaftaran', function () {
        return redirect()->route('pendaftaran.pribadi');
    })->name('pendaftaran');

    Route::get('/pendaftaran/pribadi', [PendaftaranController::class, 'pribadi'])
        ->name('pendaftaran.pribadi');

    Route::get('/pendaftaran/data-diri', [PendaftaranController::class, 'pribadi'])
        ->name('pendaftaran.data_diri');

    Route::post('/pendaftaran/pribadi', [PendaftaranController::class, 'simpanPribadi'])
        ->name('pendaftaran.simpanPribadi');

    Route::post('/pendaftaran/data-diri', [PendaftaranController::class, 'simpanPribadi'])
        ->name('pendaftaran.simpanDataDiri');

    Route::get('/pendaftaran/akademik', [PendaftaranController::class, 'akademik'])
        ->name('pendaftaran.akademik');

    Route::post('/pendaftaran/akademik', [PendaftaranController::class, 'simpanAkademik'])
        ->name('pendaftaran.simpanAkademik');

    Route::get('/pendaftaran/persyaratan', [PendaftaranController::class, 'persyaratan'])
        ->name('pendaftaran.persyaratan');

    Route::get('/pendaftaran/dokumen', [PendaftaranController::class, 'persyaratan'])
        ->name('pendaftaran.dokumen');

    Route::post('/pendaftaran/persyaratan', [PendaftaranController::class, 'simpanDokumen'])
        ->name('pendaftaran.simpanPersyaratan');

    Route::post('/pendaftaran/dokumen', [PendaftaranController::class, 'simpanDokumen'])
        ->name('pendaftaran.simpanDokumen');

    Route::get('/pendaftaran/konfirmasi', [PendaftaranController::class, 'konfirmasi'])
        ->name('pendaftaran.konfirmasi');

    // Perbaikan pendaftaran mahasiswa jika diminta revisi oleh Musyrifah
    Route::post('/pendaftaran/perbaikan', [PendaftaranController::class, 'simpanPerbaikan'])
        ->name('pendaftaran.simpanPerbaikan');

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

    Route::get('/pembelajaran-mhs/detail-pertemuan/{id}', [
        PembelajaranController::class,
        'detailPertemuan',
    ])->whereNumber('id')->name('pembelajaran.mhs.detail_pertemuan');

    // Absensi mahasiswa
    Route::get('/absensi-mhs', [AbsensiMhsController::class, 'index'])
        ->name('absensi.mhs');

    // Tugas mahasiswa: daftar, detail, pengumpulan, dan unduhan berkas
    Route::get('/tugas-mhs', [TugasMhsController::class, 'index'])
        ->name('tugas.mhs.index');

    Route::get('/tugas-mhs/{tugas}/lampiran', [TugasMhsController::class, 'lampiran'])
        ->whereNumber('tugas')->name('tugas.mhs.lampiran');

    Route::get('/tugas-mhs/{tugas}/berkas', [TugasMhsController::class, 'berkas'])
        ->whereNumber('tugas')->name('tugas.mhs.berkas');

    Route::post('/tugas-mhs/{tugas}/kumpulkan', [TugasMhsController::class, 'kumpulkan'])
        ->whereNumber('tugas')->name('tugas.mhs.kumpulkan');

    Route::get('/tugas-mhs/{tugas}', [TugasMhsController::class, 'show'])
        ->whereNumber('tugas')->name('tugas.mhs.show');

    // Notifikasi terintegrasi Guru dan Mahasiswa
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])
        ->name('notifikasi.bacaSemua');

    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])
        ->whereNumber('notifikasi')->name('notifikasi.baca');

    // Pengumuman mahasiswa
    Route::get('/pengumuman-mhs', [PengumumanMhsController::class, 'index'])
        ->name('pengumuman.mhs');

    Route::get('/pengumuman-mhs/{pengumuman}/berkas/{jenis}', [
        PengumumanMhsController::class,
        'berkas',
    ])->whereNumber('pengumuman')->name('pengumuman.mhs.berkas');

    Route::get('/pengumuman-mhs/{pengumuman}', [PengumumanMhsController::class, 'show'])
        ->whereNumber('pengumuman')->name('pengumuman.mhs.show');

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

    // Izin kegiatan fakultas/prodi dan kampus
    Route::get('/surat-perizinan-mhs/kegiatan', [
        SuratPerizinanMhsController::class,
        'kegiatan',
    ])->name('surat.perizinan.mhs.kegiatan');

    Route::post('/surat-perizinan-mhs/kegiatan', [
        SuratPerizinanMhsController::class,
        'simpanKegiatan',
    ])->name('surat.perizinan.mhs.kegiatan.store');

    // Surat pulang semester (unduh, cetak, unggah berkas bertanda tangan)
    Route::get('/surat-perizinan-mhs/surat-pulang-semester', [
        SuratPerizinanMhsController::class,
        'semester',
    ])->name('surat.perizinan.mhs.semester');

    Route::post('/surat-perizinan-mhs/surat-pulang-semester', [
        SuratPerizinanMhsController::class,
        'semesterStore',
    ])->name('surat.perizinan.mhs.semester.store');

    Route::get('/surat-perizinan-mhs/{suratPerizinan}/cetak', [
        SuratPerizinanMhsController::class,
        'cetakSemester',
    ])->whereNumber('suratPerizinan')->name('surat.perizinan.mhs.semester.cetak');

    Route::post('/surat-perizinan-mhs/{suratPerizinan}/unggah-surat', [
        SuratPerizinanMhsController::class,
        'unggahSuratTertanda',
    ])->whereNumber('suratPerizinan')->name('surat.perizinan.mhs.semester.unggah');

    Route::post('/surat-perizinan-mhs/{suratPerizinan}/batalkan', [
        SuratPerizinanMhsController::class,
        'batalkan',
    ])->whereNumber('suratPerizinan')->name('surat.perizinan.mhs.batalkan');

    Route::get('/surat-perizinan-mhs/{suratPerizinan}/dokumen/{jenis}', [
        SuratPerizinanMhsController::class,
        'dokumen',
    ])->whereNumber('suratPerizinan')->name('surat.perizinan.mhs.dokumen');

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

    // Tolak pendaftaran akun;
    Route::post('/admin/manajemen-pengguna/{id}/tolak', [
        AdminManajemenPenggunaController::class,
        'tolak',
    ])->name('admin.manajemen-pengguna.tolak');

    // Kirim ulang tautan aktivasi akun (tanpa membuat kata sandi).
    Route::post('/admin/manajemen-pengguna/{user}/aktivasi', [
        AdminManajemenPenggunaController::class,
        'kirimAktivasi',
    ])->whereNumber('user')->name('admin.manajemen-pengguna.aktivasi');

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

    Route::put('/admin/pengaturan-sistem', [
        AdminPengaturanSistemController::class,
        'updateIdentitas',
    ])->name('admin.pengaturan-sistem.update');

    Route::put('/admin/pengaturan-sistem/email', [
        AdminPengaturanSistemController::class,
        'updateEmail',
    ])->name('admin.pengaturan-sistem.email');

    Route::post('/admin/pengaturan-sistem/email/tes', [
        AdminPengaturanSistemController::class,
        'tesEmail',
    ])->name('admin.pengaturan-sistem.email.tes');

    Route::put('/admin/pengaturan-sistem/keamanan', [
        AdminPengaturanSistemController::class,
        'updateKeamanan',
    ])->name('admin.pengaturan-sistem.keamanan');

    Route::put('/admin/pengaturan-sistem/backup', [
        AdminPengaturanSistemController::class,
        'updateBackup',
    ])->name('admin.pengaturan-sistem.backup');

    Route::post('/admin/pengaturan-sistem/backup', [
        AdminPengaturanSistemController::class,
        'buatBackup',
    ])->name('admin.pengaturan-sistem.backup.buat');

    Route::get('/admin/pengaturan-sistem/backup/{berkas}', [
        AdminPengaturanSistemController::class,
        'unduhBackup',
    ])->where('berkas', '[A-Za-z0-9.\-]+')->name('admin.pengaturan-sistem.backup.unduh');

    Route::delete('/admin/pengaturan-sistem/backup/{berkas}', [
        AdminPengaturanSistemController::class,
        'hapusBackup',
    ])->where('berkas', '[A-Za-z0-9.\-]+')->name('admin.pengaturan-sistem.backup.hapus');

    Route::post('/admin/pengaturan-sistem/pemeliharaan', [
        AdminPengaturanSistemController::class,
        'pemeliharaan',
    ])->name('admin.pengaturan-sistem.pemeliharaan');

    // Pengaturan akun Admin (profil pribadi, berbeda dari pengaturan sistem).
    Route::get('/admin/pengaturan-akun', [
        PengaturanAkunController::class,
        'admin',
    ])->name('admin.pengaturan-akun');

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

    // Pembelajaran Guru: pilih mapel, progres 1-16, pertemuan, absensi, materi, tugas.
    Route::get('/guru/pembelajaran', [
        GuruPembelajaranController::class,
        'index',
    ])->name('guru.pembelajaran');

    Route::post('/guru/pembelajaran/mulai', [
        GuruPembelajaranController::class,
        'mulai',
    ])->name('guru.pembelajaran.mulai');

    Route::get('/guru/pembelajaran/pertemuan/{pertemuan}', [
        GuruPembelajaranController::class,
        'pertemuan',
    ])->whereNumber('pertemuan')->name('guru.pembelajaran.pertemuan');

    Route::put('/guru/pembelajaran/pertemuan/{pertemuan}/absensi', [
        GuruPembelajaranController::class,
        'simpanAbsensi',
    ])->whereNumber('pertemuan')->name('guru.pembelajaran.absensi');

    Route::post('/guru/pembelajaran/pertemuan/{pertemuan}/materi', [
        GuruPembelajaranController::class,
        'unggahMateri',
    ])->whereNumber('pertemuan')->name('guru.pembelajaran.materi');

    Route::get('/guru/pembelajaran/pertemuan/{pertemuan}/materi/unduh', [
        GuruPembelajaranController::class,
        'unduhMateri',
    ])->whereNumber('pertemuan')->name('guru.pembelajaran.materi.unduh');

    Route::post('/guru/pembelajaran/pertemuan/{pertemuan}/dokumentasi', [
        GuruPembelajaranController::class,
        'unggahDokumentasi',
    ])->whereNumber('pertemuan')->name('guru.pembelajaran.dokumentasi.store');

    Route::delete('/guru/pembelajaran/pertemuan/{pertemuan}/dokumentasi/{dokumentasi}', [
        GuruPembelajaranController::class,
        'hapusDokumentasi',
    ])->whereNumber(['pertemuan', 'dokumentasi'])->name('guru.pembelajaran.dokumentasi.destroy');

    Route::get('/pembelajaran/pertemuan/{pertemuan}/dokumentasi', [
        GuruKegiatanDokumentasiController::class,
        'pertemuan',
    ])->whereNumber('pertemuan')->name('pembelajaran.dokumentasi');

    Route::post('/guru/pembelajaran/pertemuan/{pertemuan}/tugas', [
        GuruPembelajaranController::class,
        'simpanTugas',
    ])->whereNumber('pertemuan')->name('guru.pembelajaran.tugas');

    Route::put('/guru/pembelajaran/pertemuan/{pertemuan}/selesai', [
        GuruPembelajaranController::class,
        'selesaikan',
    ])->whereNumber('pertemuan')->name('guru.pembelajaran.selesai');

    // Penilaian Guru mengikuti penugasan mata pelajaran dan kelompok.
    Route::get('/guru/penilaian', [
        GuruPenilaianController::class,
        'index',
    ])->name('guru.penilaian');

    // Detail jadwal kini menyatu pada menu Pembelajaran (progres 16 pertemuan).
    Route::get('/guru/jadwal-saya/detail/{mapel}', function () {
        return redirect()->route('guru.pembelajaran');
    })->name('guru.jadwal.detail');

    // Pengisian absensi kini dilakukan di dalam pertemuan Pembelajaran.
    Route::get('/guru/absensi/isi', function () {
        return redirect()->route('guru.pembelajaran');
    })->name('guru.absensi.isi');

    Route::get('/guru/kegiatan-dokumentasi', [
        GuruKegiatanDokumentasiController::class,
        'index',
    ])->name('guru.kegiatan-dokumentasi');

    Route::post('/guru/kegiatan-dokumentasi', [
        GuruKegiatanDokumentasiController::class,
        'store',
    ])->name('guru.kegiatan-dokumentasi.store');

    Route::put('/guru/kegiatan-dokumentasi/{dokumentasi}', [
        GuruKegiatanDokumentasiController::class,
        'update',
    ])->whereNumber('dokumentasi')->name('guru.kegiatan-dokumentasi.update');

    Route::delete('/guru/kegiatan-dokumentasi/{dokumentasi}', [
        GuruKegiatanDokumentasiController::class,
        'destroy',
    ])->whereNumber('dokumentasi')->name('guru.kegiatan-dokumentasi.destroy');

    // Foto dokumentasi disajikan terpusat agar Guru (pemilik), Musyrifah,
    // Pimpinan, dan Admin dapat melihatnya lewat halaman masing-masing.
    Route::get('/dokumentasi-kegiatan/{dokumentasi}/foto', [
        GuruKegiatanDokumentasiController::class,
        'foto',
    ])->whereNumber('dokumentasi')->name('dokumentasi.foto');

    // Pengaturan akun Guru
    Route::get('/guru/pengaturan-akun', [
        PengaturanAkunController::class,
        'guru',
    ])->name('guru.pengaturan-akun');

    // Tugas yang dibuat Guru sesuai mata pelajaran dan kelompok penugasannya
    Route::get('/guru/tugas', [GuruTugasController::class, 'index'])
        ->name('guru.tugas.index');

    Route::get('/guru/tugas/tambah', [GuruTugasController::class, 'create'])
        ->name('guru.tugas.create');

    Route::post('/guru/tugas', [GuruTugasController::class, 'store'])
        ->name('guru.tugas.store');

    Route::get('/guru/tugas/{tugas}/edit', [GuruTugasController::class, 'edit'])
        ->whereNumber('tugas')->name('guru.tugas.edit');

    Route::put('/guru/tugas/{tugas}', [GuruTugasController::class, 'update'])
        ->whereNumber('tugas')->name('guru.tugas.update');

    Route::delete('/guru/tugas/{tugas}', [GuruTugasController::class, 'destroy'])
        ->whereNumber('tugas')->name('guru.tugas.destroy');

    Route::post('/guru/tugas/{tugas}/status', [GuruTugasController::class, 'ubahStatus'])
        ->whereNumber('tugas')->name('guru.tugas.status');

    Route::get('/guru/tugas/{tugas}/lampiran', [GuruTugasController::class, 'lampiran'])
        ->whereNumber('tugas')->name('guru.tugas.lampiran');

    // Pengumpulan tugas dan penilaian
    Route::get('/guru/tugas/{tugas}/pengumpulan', [GuruPengumpulanController::class, 'index'])
        ->whereNumber('tugas')->name('guru.pengumpulan.index');

    Route::get('/guru/tugas/{tugas}/pengumpulan/{pengumpulan}/berkas', [GuruPengumpulanController::class, 'berkas'])
        ->whereNumber('tugas')->whereNumber('pengumpulan')->name('guru.pengumpulan.berkas');

    Route::post('/guru/tugas/{tugas}/pengumpulan/{pengumpulan}/nilai', [GuruPengumpulanController::class, 'nilai'])
        ->whereNumber('tugas')->whereNumber('pengumpulan')->name('guru.pengumpulan.nilai');

    Route::post('/guru/tugas/{tugas}/pengumpulan/{pengumpulan}/publikasi', [GuruPengumpulanController::class, 'publikasiNilai'])
        ->whereNumber('tugas')->whereNumber('pengumpulan')->name('guru.pengumpulan.publikasi');

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

    // Pengaturan akun Pimpinan
    Route::get('/pimpinan/pengaturan-akun', [
        PengaturanAkunController::class,
        'pimpinan',
    ])->name('pimpinan.pengaturan-akun');

    // Halaman Laporan Penghuni
    Route::get('/pimpinan/laporan-penghuni', [
        PimpinanLaporanMahadController::class,
        'penghuni',
    ])->name('pimpinan.laporan-penghuni');

    // Laporan kamar
    Route::get('/pimpinan/laporan-kamar', [
        PimpinanLaporanMahadController::class,
        'kamar',
    ])->name('pimpinan.laporan-kamar');

    // Laporan kehadiran
    Route::get('/pimpinan/laporan-kehadiran', [
        PimpinanLaporanMahadController::class,
        'kehadiran',
    ])->name('pimpinan.laporan-kehadiran');

    // Laporan pembayaran
    Route::get('/pimpinan/laporan-pembayaran', [
        PimpinanLaporanMahadController::class,
        'pembayaran',
    ])->name('pimpinan.laporan-pembayaran');

    // Laporan pendaftaran
    Route::get('/pimpinan/laporan-pendaftaran', [
        PimpinanLaporanMahadController::class,
        'pendaftaran',
    ])->name('pimpinan.laporan-pendaftaran');

    // Laporan pembelajaran
    Route::get('/pimpinan/laporan-pembelajaran', [
        PimpinanLaporanMahadController::class,
        'pembelajaran',
    ])->name('pimpinan.laporan-pembelajaran');

    // Laporan perizinan
    Route::get('/pimpinan/laporan-perizinan', [
        PimpinanLaporanMahadController::class,
        'perizinan',
    ])->name('pimpinan.laporan-perizinan');

    // Unduh laporan monitoring (hanya baca)
    Route::get('/pimpinan/laporan/unduh/{jenis}', [
        PimpinanLaporanMahadController::class,
        'unduh',
    ])->whereIn('jenis', LaporanMahadService::JENIS_PIMPINAN)
        ->name('pimpinan.laporan.unduh');

    /*
    |--------------------------------------------------------------------------
    | Monitoring Pimpinan (hanya lihat, tanpa aksi operasional)
    |--------------------------------------------------------------------------
    */

    Route::get('/pimpinan/monitoring', [
        PimpinanMonitoringController::class,
        'index',
    ])->name('pimpinan.monitoring');

    Route::get('/pimpinan/monitoring/mahasiswa', [
        PimpinanMonitoringController::class,
        'mahasiswa',
    ])->name('pimpinan.monitoring.mahasiswa');

    Route::get('/pimpinan/monitoring/kamar', [
        PimpinanMonitoringController::class,
        'kamar',
    ])->name('pimpinan.monitoring.kamar');

    Route::get('/pimpinan/monitoring/pembayaran', [
        PimpinanMonitoringController::class,
        'pembayaran',
    ])->name('pimpinan.monitoring.pembayaran');

    Route::get('/pimpinan/monitoring/pembelajaran', [
        PimpinanMonitoringController::class,
        'pembelajaran',
    ])->name('pimpinan.monitoring.pembelajaran');

    Route::get('/pimpinan/monitoring/perizinan', [
        PimpinanMonitoringController::class,
        'perizinan',
    ])->name('pimpinan.monitoring.perizinan');

    Route::get('/pimpinan/monitoring/{jenis}/unduh', [
        PimpinanMonitoringController::class,
        'unduh',
    ])->whereIn('jenis', PimpinanMonitoringService::KATEGORI)
        ->name('pimpinan.monitoring.unduh');

    // Detail register laporan + cetak (hanya baca)
    Route::get('/pimpinan/laporan/{laporan}', [
        PimpinanLaporanRegisterController::class,
        'detail',
    ])->whereNumber('laporan')->name('pimpinan.laporan.detail');

    Route::get('/pimpinan/laporan/{laporan}/cetak', [
        PimpinanLaporanRegisterController::class,
        'cetak',
    ])->whereNumber('laporan')->name('pimpinan.laporan.cetak');

    // Unduh dokumen laporan periodik Musyrifah (hanya baca)
    Route::get('/pimpinan/laporan/{laporan}/pdf', [
        PimpinanLaporanRegisterController::class,
        'unduhPdf',
    ])->whereNumber('laporan')->name('pimpinan.laporan.pdf');

    Route::get('/pimpinan/laporan/{laporan}/excel', [
        PimpinanLaporanRegisterController::class,
        'unduhExcel',
    ])->whereNumber('laporan')->name('pimpinan.laporan.excel');

    // Audit trail (Pimpinan dan Admin Sistem)
    Route::get('/pimpinan/audit', [
        PimpinanAuditController::class,
        'index',
    ])->name('pimpinan.audit');

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

    // Pemeriksaan berkas pendaftaran oleh Musyrifah.
    Route::get('/musyrifah/pendaftaran/{pendaftaran}/berkas/{jenis}', [
        MusyrifahPendaftaranController::class,
        'berkas',
    ])->whereIn('jenis', ['penerimaan', 'pembayaran', 'ijazah', 'ktp', 'kk'])
        ->name('musyrifah.pendaftaran.berkas');

    Route::get('/musyrifah/data-kamar', [
        MusyrifahDataKamarController::class,
        'index',
    ])->name('musyrifah.data_kamar');

    Route::post('/musyrifah/data-kamar', [
        MusyrifahDataKamarController::class,
        'store',
    ])->name('musyrifah.data_kamar.store');

    Route::put('/musyrifah/data-kamar/{kamar}', [
        MusyrifahDataKamarController::class,
        'update',
    ])->name('musyrifah.data_kamar.update');

    Route::delete('/musyrifah/data-kamar/{kamar}', [
        MusyrifahDataKamarController::class,
        'destroy',
    ])->name('musyrifah.data_kamar.destroy');

    // Pengelolaan galeri foto kamar (maksimal 4 foto per kamar)
    Route::post('/musyrifah/data-kamar/{kamar}/foto', [
        MusyrifahDataKamarController::class,
        'tambahFoto',
    ])->name('musyrifah.data_kamar.foto.store');

    Route::post('/musyrifah/data-kamar/{kamar}/foto/{foto}', [
        MusyrifahDataKamarController::class,
        'gantiFoto',
    ])->name('musyrifah.data_kamar.foto.update');

    Route::delete('/musyrifah/data-kamar/{kamar}/foto/{foto}', [
        MusyrifahDataKamarController::class,
        'hapusFoto',
    ])->name('musyrifah.data_kamar.foto.destroy');

    // Persetujuan penempatan kamar oleh Musyrifah
    Route::post('/musyrifah/pengajuan-kamar/{pengajuan}/approve', [
        MusyrifahDataKamarController::class,
        'approvePengajuan',
    ])->name('musyrifah.pengajuan_kamar.approve');

    Route::post('/musyrifah/pengajuan-kamar/{pengajuan}/reject', [
        MusyrifahDataKamarController::class,
        'rejectPengajuan',
    ])->name('musyrifah.pengajuan_kamar.reject');

    Route::get('/musyrifah/pembayaran', [
        MusyrifahPembayaranController::class,
        'index',
    ])->name('musyrifah.pembayaran');

    Route::get('/musyrifah/pembayaran/data', [
        MusyrifahPembayaranController::class,
        'apiData',
    ])->name('musyrifah.pembayaran.data');

    Route::get('/musyrifah/pembayaran/{id}/detail', [
        MusyrifahPembayaranController::class,
        'detail',
    ])->name('musyrifah.pembayaran.detail');

    Route::post('/musyrifah/pembayaran/{id}/status', [
        MusyrifahPembayaranController::class,
        'updateStatus',
    ])->name('musyrifah.pembayaran.status');

    Route::post('/musyrifah/pembayaran/{id}/verifikasi', [
        MusyrifahPembayaranController::class,
        'verifikasiBukti',
    ])->name('musyrifah.pembayaran.verifikasi');

    Route::post('/musyrifah/pembayaran/{id}/nominal', [
        MusyrifahPembayaranController::class,
        'perbaruiNominal',
    ])->name('musyrifah.pembayaran.nominal');

    Route::post('/musyrifah/pembayaran/jadwal', [
        MusyrifahPembayaranController::class,
        'simpanJadwal',
    ])->name('musyrifah.pembayaran.jadwal');

    // Pembukaan dan pengaturan periode pembayaran (kewenangan Musyrifah).
    Route::post('/musyrifah/pembayaran/periode', [
        MusyrifahPembayaranController::class,
        'simpanPeriode',
    ])->name('musyrifah.pembayaran.periode.simpan');

    Route::post('/musyrifah/pembayaran/periode/{periode}/buka', [
        MusyrifahPembayaranController::class,
        'bukaPeriode',
    ])->name('musyrifah.pembayaran.periode.buka');

    Route::post('/musyrifah/pembayaran/periode/{periode}/tutup', [
        MusyrifahPembayaranController::class,
        'tutupPeriode',
    ])->name('musyrifah.pembayaran.periode.tutup');

    Route::post('/musyrifah/pembayaran/keterlambatan/{id}', [
        MusyrifahPembayaranController::class,
        'putuskanKeterlambatan',
    ])->name('musyrifah.pembayaran.keterlambatan');

    Route::get('/musyrifah/manajemen-pembelajaran', [
        MusyrifahManajemenPembelajaranController::class,
        'index',
    ])->name('musyrifah.manajemen_pembelajaran');

    Route::post('/musyrifah/manajemen-pembelajaran/kelompok', [
        MusyrifahManajemenPembelajaranController::class,
        'storeKelompok',
    ])->name('musyrifah.manajemen_pembelajaran.kelompok.store');

    Route::put('/musyrifah/manajemen-pembelajaran/kelompok/{id}', [
        MusyrifahManajemenPembelajaranController::class,
        'updateKelompok',
    ])->name('musyrifah.manajemen_pembelajaran.kelompok.update');

    Route::delete('/musyrifah/manajemen-pembelajaran/kelompok/{id}', [
        MusyrifahManajemenPembelajaranController::class,
        'destroyKelompok',
    ])->name('musyrifah.manajemen_pembelajaran.kelompok.destroy');

    Route::post('/musyrifah/manajemen-pembelajaran/jadwal', [
        MusyrifahManajemenPembelajaranController::class,
        'storeJadwal',
    ])->name('musyrifah.manajemen_pembelajaran.jadwal.store');

    Route::put('/musyrifah/manajemen-pembelajaran/jadwal/{id}', [
        MusyrifahManajemenPembelajaranController::class,
        'updateJadwal',
    ])->name('musyrifah.manajemen_pembelajaran.jadwal.update');

    Route::delete('/musyrifah/manajemen-pembelajaran/jadwal/{id}', [
        MusyrifahManajemenPembelajaranController::class,
        'destroyJadwal',
    ])->name('musyrifah.manajemen_pembelajaran.jadwal.destroy');

    Route::post('/musyrifah/manajemen-pembelajaran/mata-pelajaran', [
        MusyrifahManajemenPembelajaranController::class,
        'storeMataPelajaran',
    ])->name('musyrifah.manajemen_pembelajaran.mapel.store');

    Route::put('/musyrifah/manajemen-pembelajaran/mata-pelajaran/{id}', [
        MusyrifahManajemenPembelajaranController::class,
        'updateMataPelajaran',
    ])->name('musyrifah.manajemen_pembelajaran.mapel.update');

    Route::delete('/musyrifah/manajemen-pembelajaran/mata-pelajaran/{id}', [
        MusyrifahManajemenPembelajaranController::class,
        'destroyMataPelajaran',
    ])->name('musyrifah.manajemen_pembelajaran.mapel.destroy');

    Route::get('/musyrifah/manajemen-pembelajaran/detail-pemantauan/{id}', [
        MusyrifahManajemenPembelajaranController::class,
        'detailPemantauan',
    ])->name('musyrifah.manajemen_pembelajaran.detail_pemantauan');

    Route::get('/musyrifah/manajemen-pembelajaran/detail-kelulusan/{id}', [
        MusyrifahManajemenPembelajaranController::class,
        'detailKelulusan',
    ])->name('musyrifah.manajemen_pembelajaran.detail_kelulusan');

    // Jadwal Saya Musyrifah menggunakan tampilan Guru.
    Route::get('/musyrifah/jadwal-saya', function (Request $request) {

        $role = strtolower((string) $request->user()?->role);

        abort_unless(
            in_array($role, ['musyrifah', 'pengelola'], true),
            403
        );

        return view('guru.jadwal_saya');

    })->name('musyrifah.jadwal-saya');

    // Absensi Musyrifah memakai rekap pembelajaran yang sama (tanpa batas penugasan Guru).
    Route::get('/musyrifah/absensi', [
        GuruAbsensiController::class,
        'untukMusyrifah',
    ])->name('musyrifah.absensi');

    Route::get('/musyrifah/surat-perizinan', [
        MusyrifahSuratPerizinanController::class,
        'index',
    ])->name('musyrifah.surat_perizinan');

    Route::get('/musyrifah/surat-perizinan/{suratPerizinan}', [
        MusyrifahSuratPerizinanController::class,
        'show',
    ])->whereNumber('suratPerizinan')->name('musyrifah.surat_perizinan.show');

    Route::post('/musyrifah/surat-perizinan/{suratPerizinan}/keputusan', [
        MusyrifahSuratPerizinanController::class,
        'keputusan',
    ])->whereNumber('suratPerizinan')->name('musyrifah.surat_perizinan.keputusan');

    Route::post('/musyrifah/surat-perizinan/{suratPerizinan}/verifikasi-surat', [
        MusyrifahSuratPerizinanController::class,
        'verifikasiSurat',
    ])->whereNumber('suratPerizinan')->name('musyrifah.surat_perizinan.verifikasi');

    Route::get('/musyrifah/surat-perizinan/{suratPerizinan}/dokumen/{jenis}', [
        MusyrifahSuratPerizinanController::class,
        'dokumen',
    ])->whereNumber('suratPerizinan')->name('musyrifah.surat_perizinan.dokumen');

    Route::get('/musyrifah/laporan', [
        MusyrifahLaporanController::class,
        'index',
    ])->name('musyrifah.laporan');

    // Register laporan Musyrifah: catat draft, terbitkan, arsipkan.
    Route::post('/musyrifah/laporan', [
        MusyrifahLaporanController::class,
        'simpanLaporan',
    ])->name('musyrifah.laporan.simpan');

    Route::patch('/musyrifah/laporan/{laporan}/terbitkan', [
        MusyrifahLaporanController::class,
        'terbitkanLaporan',
    ])->whereNumber('laporan')->name('musyrifah.laporan.terbitkan');

    Route::patch('/musyrifah/laporan/{laporan}/arsipkan', [
        MusyrifahLaporanController::class,
        'arsipkanLaporan',
    ])->whereNumber('laporan')->name('musyrifah.laporan.arsipkan');

    // Laporan periodik: buat (bulanan/tahunan), pratinjau, simpan, dokumen.
    Route::get('/musyrifah/laporan/buat', [
        MusyrifahLaporanController::class,
        'buatLaporan',
    ])->name('musyrifah.laporan.buat');

    Route::post('/musyrifah/laporan/buat/pratinjau', [
        MusyrifahLaporanController::class,
        'pratinjauPeriodik',
    ])->name('musyrifah.laporan.pratinjau');

    Route::post('/musyrifah/laporan/buat', [
        MusyrifahLaporanController::class,
        'simpanPeriodik',
    ])->name('musyrifah.laporan.periodik.simpan');

    Route::get('/musyrifah/laporan/periodik/{laporan}', [
        MusyrifahLaporanController::class,
        'lihatPeriodik',
    ])->whereNumber('laporan')->name('musyrifah.laporan.periodik');

    Route::get('/musyrifah/laporan/periodik/{laporan}/cetak', [
        MusyrifahLaporanController::class,
        'cetakPeriodik',
    ])->whereNumber('laporan')->name('musyrifah.laporan.periodik.cetak');

    Route::get('/musyrifah/laporan/periodik/{laporan}/pdf', [
        MusyrifahLaporanController::class,
        'unduhPdf',
    ])->whereNumber('laporan')->name('musyrifah.laporan.periodik.pdf');

    Route::get('/musyrifah/laporan/periodik/{laporan}/excel', [
        MusyrifahLaporanController::class,
        'unduhExcel',
    ])->whereNumber('laporan')->name('musyrifah.laporan.periodik.excel');

    Route::patch('/musyrifah/laporan/periodik/{laporan}/perbarui', [
        MusyrifahLaporanController::class,
        'perbaruiPeriodik',
    ])->whereNumber('laporan')->name('musyrifah.laporan.periodik.perbarui');

    Route::get('/musyrifah/laporan/{jenis}/unduh', [
        MusyrifahLaporanController::class,
        'unduh',
    ])->whereIn('jenis', LaporanMahadService::JENIS_MUSYRIFAH)
        ->name('musyrifah.laporan.unduh');

    Route::get('/musyrifah/laporan/{jenis}', [
        MusyrifahLaporanController::class,
        'show',
    ])->whereIn('jenis', LaporanMahadService::JENIS_MUSYRIFAH)
        ->name('musyrifah.laporan.detail');

    // Pengaturan akun Musyrifah
    Route::get('/musyrifah/pengaturan-akun', [
        PengaturanAkunController::class,
        'musyrifah',
    ])->name('musyrifah.pengaturan_akun');

    // Pengumuman Musyrifah
    Route::get('/musyrifah/pengumuman', [
        MusyrifahPengumumanController::class,
        'index',
    ])->name('musyrifah.pengumuman');

    Route::get('/musyrifah/detail-pengumuman', [
        MusyrifahDetailPengumumanController::class,
        'create',
    ])->name('musyrifah.detail-pengumuman');

    Route::post('/musyrifah/pengumuman', [
        MusyrifahPengumumanController::class,
        'store',
    ])->name('musyrifah.pengumuman.store');

    Route::get('/musyrifah/pengumuman/{pengumuman}/edit', [
        MusyrifahPengumumanController::class,
        'edit',
    ])->whereNumber('pengumuman')->name('musyrifah.pengumuman.edit');

    Route::put('/musyrifah/pengumuman/{pengumuman}', [
        MusyrifahPengumumanController::class,
        'update',
    ])->whereNumber('pengumuman')->name('musyrifah.pengumuman.update');

    Route::delete('/musyrifah/pengumuman/{pengumuman}', [
        MusyrifahPengumumanController::class,
        'destroy',
    ])->whereNumber('pengumuman')->name('musyrifah.pengumuman.destroy');

    Route::post('/musyrifah/pengumuman/{pengumuman}/status', [
        MusyrifahPengumumanController::class,
        'ubahStatus',
    ])->whereNumber('pengumuman')->name('musyrifah.pengumuman.status');

    Route::get('/musyrifah/pengumuman/{pengumuman}/berkas/{jenis}', [
        MusyrifahPengumumanController::class,
        'berkas',
    ])->whereNumber('pengumuman')->name('musyrifah.pengumuman.berkas');

    Route::get('/musyrifah/pengumuman/{pengumuman}', [
        MusyrifahPengumumanController::class,
        'show',
    ])->whereNumber('pengumuman')->name('musyrifah.pengumuman.show');

});
