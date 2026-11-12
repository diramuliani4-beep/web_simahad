<?php

namespace App\Support;

use App\Models\User;

/**
 * Definisi menu sidebar untuk seluruh role.
 *
 * Semua layout role memakai daftar yang sama agar ukuran ikon, jarak menu,
 * dan penanda menu aktif benar-benar seragam. Isi dashboard tetap berbeda
 * karena setiap role memiliki tanggung jawab yang berbeda.
 */
class MenuPeran
{
    /**
     * Menu utama tiap role.
     *
     * @return list<array{label: string, route: string, aktif: list<string>, ikon: string}>
     */
    public static function utama(?User $pengguna): array
    {
        return match (self::peran($pengguna)) {
            'mahasiswa' => [
                self::item('Dashboard', 'dashboard', ['dashboard', 'dashboard_admin'], 'dashboard'),
                self::item('Pendaftaran', 'pendaftaran.pribadi', ['pendaftaran.pribadi', 'pendaftaran.*'], 'pendaftaran'),
                self::item('Pembelajaran', 'pembelajaran.mhs', ['pembelajaran.mhs*'], 'pembelajaran'),
                self::item('Tugas', 'tugas.mhs.index', ['tugas.mhs.*'], 'tugas'),
                self::item('Informasi Kamar', 'kamar', ['kamar', 'kamar.*'], 'kamar'),
                self::item('Pembayaran', 'pembayaran', ['pembayaran', 'pembayaran.*'], 'pembayaran'),
                self::item('Absensi', 'absensi.mhs', ['absensi.mhs*'], 'absensi'),
                self::item('Surat dan Perizinan', 'surat.perizinan.mhs', ['surat.perizinan.mhs*'], 'surat'),
                self::item('Pengumuman', 'pengumuman.mhs', ['pengumuman.mhs*'], 'pengumuman'),
            ],

            'guru' => [
                self::item('Dashboard', 'guru.dashboard', ['guru.dashboard'], 'dashboard'),
                self::item('Jadwal Saya', 'guru.jadwal-saya', ['guru.jadwal-saya', 'guru.jadwal.detail'], 'jadwal'),
                self::item('Absensi', 'guru.absensi', ['guru.absensi*'], 'absensi'),
                self::item('Kegiatan & Dokumentasi', 'guru.kegiatan-dokumentasi', ['guru.kegiatan-dokumentasi*'], 'kegiatan'),
                self::item('Tugas', 'guru.tugas.index', ['guru.tugas.*', 'guru.pengumpulan.*'], 'tugas'),
            ],

            'musyrifah' => [
                self::item('Dashboard', 'musyrifah.dashboard', ['musyrifah.dashboard'], 'dashboard'),
                self::item('Pendaftaran', 'musyrifah.pendaftaran', ['musyrifah.pendaftaran*'], 'pendaftaran'),
                self::item('Data Kamar', 'musyrifah.data_kamar', ['musyrifah.data_kamar*', 'musyrifah.pengajuan_kamar*'], 'kamar'),
                self::item('Pembayaran', 'musyrifah.pembayaran', ['musyrifah.pembayaran*'], 'pembayaran'),
                self::item('Surat & Perizinan', 'musyrifah.surat_perizinan', ['musyrifah.surat_perizinan*'], 'surat'),
                self::item('Manajemen Pembelajaran', 'musyrifah.manajemen_pembelajaran', ['musyrifah.manajemen_pembelajaran*'], 'pembelajaran'),
                self::item('Laporan', 'musyrifah.laporan', ['musyrifah.laporan*'], 'laporan'),
                self::item('Pengumuman', 'musyrifah.pengumuman', ['musyrifah.pengumuman*', 'musyrifah.detail-pengumuman*'], 'pengumuman'),
            ],

            'pimpinan' => [
                self::item('Dashboard', 'pimpinan.dashboard', ['pimpinan.dashboard'], 'dashboard'),
                self::grup('Monitoring', 'monitoring', [
                    self::item('Monitoring Mahasiswa', 'pimpinan.monitoring.mahasiswa', ['pimpinan.monitoring.mahasiswa*'], 'mahasiswa'),
                    self::item('Monitoring Kamar', 'pimpinan.monitoring.kamar', ['pimpinan.monitoring.kamar*'], 'kamar'),
                    self::item('Monitoring Pembayaran', 'pimpinan.monitoring.pembayaran', ['pimpinan.monitoring.pembayaran*'], 'pembayaran'),
                    self::item('Monitoring Pembelajaran', 'pimpinan.monitoring.pembelajaran', ['pimpinan.monitoring.pembelajaran*'], 'pembelajaran'),
                    self::item('Monitoring Perizinan', 'pimpinan.monitoring.perizinan', ['pimpinan.monitoring.perizinan*'], 'surat'),
                ], ['pimpinan.monitoring.*']),
                self::grup('Laporan', 'laporan', [
                    self::item('Laporan Mahasiswa', 'pimpinan.laporan-penghuni', ['pimpinan.laporan-penghuni*'], 'penghuni'),
                    self::item('Laporan Kamar', 'pimpinan.laporan-kamar', ['pimpinan.laporan-kamar*'], 'kamar'),
                    self::item('Laporan Pembayaran', 'pimpinan.laporan-pembayaran', ['pimpinan.laporan-pembayaran*'], 'pembayaran'),
                    self::item('Laporan Pembelajaran', 'pimpinan.laporan-pembelajaran', ['pimpinan.laporan-pembelajaran*'], 'pembelajaran'),
                    self::item('Laporan Perizinan', 'pimpinan.laporan-perizinan', ['pimpinan.laporan-perizinan*'], 'surat'),
                ], ['pimpinan.laporan-mahad', 'pimpinan.laporan.detail*', 'pimpinan.laporan*', 'pimpinan.audit*']),
            ],

            'admin' => [
                self::item('Dashboard', 'admin.dashboard', ['admin.dashboard*', 'dashboard_admin'], 'dashboard'),
                self::item('Manajemen Pengguna', 'admin.manajemen-pengguna', ['admin.manajemen-pengguna*'], 'pengguna'),
                self::item('Data Pegawai', 'admin.data-pegawai.index', ['admin.data-pegawai*'], 'pegawai'),
                self::item('Data Mahasiswa', 'admin.data-mahasiswa.index', ['admin.data-mahasiswa*'], 'mahasiswa'),
                self::item('Log Aktivitas', 'admin.log-aktivitas', ['admin.log-aktivitas*'], 'log'),
                self::item('Audit Trail', 'pimpinan.audit', ['pimpinan.audit*'], 'laporan'),
            ],

            default => [],
        };
    }

    /**
     * Menu bagian bawah sidebar (pengaturan pribadi dan keluar).
     *
     * @return list<array{label: string, route: string, aktif: list<string>, ikon: string}>
     */
    public static function bawah(?User $pengguna): array
    {
        $menu = [];

        if (self::peran($pengguna) === 'admin') {
            $menu[] = self::item('Pengaturan Sistem', 'admin.pengaturan-sistem', ['admin.pengaturan-sistem*'], 'sistem');
        }

        // Pimpinan mengakses pengaturan akun lewat dropdown profil saja.
        if (self::peran($pengguna) !== 'pimpinan') {
            $menu[] = self::item('Pengaturan Akun', 'pengaturan-akun.index', ['pengaturan-akun.*'], 'pengaturan');
        }

        return $menu;
    }

    /** Route halaman profil sesuai role. */
    public static function routeProfil(?User $pengguna): string
    {
        return self::peran($pengguna) === 'mahasiswa' ? 'profil.mhs' : 'profil.pengguna';
    }

    /** Route halaman data sesuai role. */
    public static function routeData(?User $pengguna): string
    {
        return self::peran($pengguna) === 'mahasiswa' ? 'data.mahasiswa' : 'data.pengguna';
    }

    /** Peran yang sudah dikenal sistem. */
    public static function peran(?User $pengguna): string
    {
        $peran = strtolower((string) $pengguna?->role);

        return in_array($peran, ['mahasiswa', 'guru', 'musyrifah', 'pimpinan', 'admin'], true)
            ? $peran
            : 'mahasiswa';
    }

    public static function labelPeran(?User $pengguna): string
    {
        return match (self::peran($pengguna)) {
            'guru' => 'Guru',
            'musyrifah' => 'Musyrifah',
            'pimpinan' => 'Pimpinan',
            'admin' => 'Admin Sistem',
            default => 'Mahasiswa',
        };
    }

    /**
     * @param  list<string>  $aktif
     * @return array{label: string, route: string, aktif: list<string>, ikon: string}
     */
    private static function item(string $label, string $route, array $aktif, string $ikon): array
    {
        return [
            'label' => $label,
            'route' => $route,
            'aktif' => $aktif,
            'ikon' => $ikon,
        ];
    }

    /**
     * Menu induk dengan submenu expand/collapse.
     *
     * @param  list<array{label: string, route: string, aktif: list<string>, ikon: string}>  $anak
     * @param  list<string>  $aktif
     * @return array{label: string, ikon: string, anak: list<array<string, mixed>>, aktif: list<string>}
     */
    private static function grup(string $label, string $ikon, array $anak, array $aktif = []): array
    {
        $aktifAnak = [];

        foreach ($anak as $sub) {
            $aktifAnak = array_merge($aktifAnak, $sub['aktif']);
        }

        return [
            'label' => $label,
            'ikon' => $ikon,
            'anak' => $anak,
            'aktif' => array_values(array_unique(array_merge($aktif, $aktifAnak))),
        ];
    }
}
