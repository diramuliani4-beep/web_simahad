<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminDataMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_melihat_daftar_mahasiswa_yang_sudah_disetujui(): void
    {
        $admin = $this->buatAdmin();
        $mahasiswa = $this->buatMahasiswa(['name' => 'Aisyah Putri', 'nim' => '231700321']);

        $this->buatPendaftaran($mahasiswa, [
            'nama_lengkap' => 'Aisyah Putri',
            'fakultas' => 'Tarbiyah dan Keguruan',
            'program_studi' => 'Pendidikan Agama Islam',
            'tahun_angkatan' => '2024',
            'validator_nama' => 'Ustadzah Huda',
            'catatan_validasi' => 'Dokumen lengkap dan pembayaran sudah diverifikasi.',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.data-mahasiswa.index'));

        $response->assertOk();
        $response->assertSee('Data Mahasiswa');
        $response->assertSee('Aisyah Putri');
        $response->assertSee('231700321');
        $response->assertSee('Pendidikan Agama Islam');
        $response->assertSee('Tarbiyah dan Keguruan');
        $response->assertSee('2024');
        $response->assertSee('Ustadzah Huda');
        // Penanda halaman read-only untuk Admin Sistem.
        $response->assertSee('hanya untuk melihat data');
        $response->assertSee('kewenangan Musyrifah');
    }

    public function test_hanya_pendaftaran_disetujui_yang_ditampilkan(): void
    {
        $admin = $this->buatAdmin();

        $disetujui = $this->buatMahasiswa(['name' => 'Mahasiswa Disetujui']);
        $this->buatPendaftaran($disetujui, [
            'nama_lengkap' => 'Mahasiswa Disetujui',
            'status' => 'disetujui',
        ]);

        $menunggu = $this->buatMahasiswa(['name' => 'Pendaftar Menunggu']);
        $this->buatPendaftaran($menunggu, [
            'nama_lengkap' => 'Pendaftar Menunggu',
            'status' => 'menunggu',
        ]);

        $ditolak = $this->buatMahasiswa(['name' => 'Pendaftar Ditolak']);
        $this->buatPendaftaran($ditolak, [
            'nama_lengkap' => 'Pendaftar Ditolak',
            'status' => 'ditolak',
        ]);

        $perbaikan = $this->buatMahasiswa(['name' => 'Pendaftar Perbaikan']);
        $this->buatPendaftaran($perbaikan, [
            'nama_lengkap' => 'Pendaftar Perbaikan',
            'status' => 'perlu_perbaikan',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.data-mahasiswa.index'));

        $response->assertOk();
        $response->assertSee('Mahasiswa Disetujui');
        $response->assertDontSee('Pendaftar Menunggu');
        $response->assertDontSee('Pendaftar Ditolak');
        $response->assertDontSee('Pendaftar Perbaikan');
    }

    public function test_admin_dapat_mencari_berdasarkan_nama_nim_dan_email(): void
    {
        $admin = $this->buatAdmin();

        $aisyah = $this->buatMahasiswa(['name' => 'Aisyah Putri', 'nim' => '231700321']);
        $this->buatPendaftaran($aisyah, [
            'nama_lengkap' => 'Aisyah Putri',
            'program_studi' => 'Pendidikan Agama Islam',
            'status' => 'disetujui',
        ]);

        $fatimah = $this->buatMahasiswa([
            'name' => 'Fatimah Azzahra',
            'nim' => '231700999',
            'email' => 'fatimah@example.org',
        ]);
        $this->buatPendaftaran($fatimah, [
            'nama_lengkap' => 'Fatimah Azzahra',
            'program_studi' => 'Hukum Keluarga',
            'status' => 'disetujui',
        ]);

        $cariNama = $this->actingAs($admin)->get(route('admin.data-mahasiswa.index', ['q' => 'Aisyah']));
        $cariNama->assertOk();
        $cariNama->assertSee('Aisyah Putri');
        $cariNama->assertDontSee('Fatimah Azzahra');

        $cariNim = $this->actingAs($admin)->get(route('admin.data-mahasiswa.index', ['q' => '231700999']));
        $cariNim->assertOk();
        $cariNim->assertSee('Fatimah Azzahra');
        $cariNim->assertDontSee('Aisyah Putri');

        $cariEmail = $this->actingAs($admin)->get(route('admin.data-mahasiswa.index', ['q' => 'fatimah@example.org']));
        $cariEmail->assertOk();
        $cariEmail->assertSee('Fatimah Azzahra');
        $cariEmail->assertDontSee('Aisyah Putri');
    }

    public function test_admin_dapat_memfilter_berdasarkan_kategori(): void
    {
        $admin = $this->buatAdmin();

        $kip = $this->buatMahasiswa(['name' => 'Aisyah Nur']);
        $this->buatPendaftaran($kip, [
            'nama_lengkap' => 'Aisyah Nur',
            'kategori' => 'kip',
            'status' => 'disetujui',
        ]);

        $umum = $this->buatMahasiswa(['name' => 'Zaid Alfarizi']);
        $this->buatPendaftaran($umum, [
            'nama_lengkap' => 'Zaid Alfarizi',
            'kategori' => 'umum',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.data-mahasiswa.index', ['kategori' => 'kip']));

        $response->assertOk();
        $response->assertSee('Aisyah Nur');
        $response->assertDontSee('Zaid Alfarizi');
    }

    public function test_detail_mahasiswa_menampilkan_hasil_verifikasi_musyrifah(): void
    {
        $admin = $this->buatAdmin();
        $mahasiswa = $this->buatMahasiswa(['name' => 'Aisyah Putri', 'nim' => '231700321']);

        $this->buatPendaftaran($mahasiswa, [
            'nama_lengkap' => 'Aisyah Putri',
            'validator_nama' => 'Ustadzah Huda',
            'catatan_validasi' => 'Berkas lengkap, silakan lanjut ke pemilihan kamar.',
            'divalidasi_pada' => now(),
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.data-mahasiswa.index'));

        $response->assertOk();
        $response->assertSee('Lihat Detail');
        $response->assertSee('Hasil Verifikasi Musyrifah');
        $response->assertSee('Berkas lengkap, silakan lanjut ke pemilihan kamar.');
        $response->assertSee('Waktu validasi');
    }

    public function test_role_selain_admin_tidak_dapat_membuka_data_mahasiswa(): void
    {
        foreach (['musyrifah', 'guru', 'pimpinan', 'mahasiswa'] as $peran) {
            $pengguna = User::factory()->create(['role' => $peran, 'status' => 'aktif']);

            $this->actingAs($pengguna)
                ->get(route('admin.data-mahasiswa.index'))
                ->assertForbidden();
        }
    }

    public function test_tamu_dialihkan_ke_halaman_login(): void
    {
        $this->get(route('admin.data-mahasiswa.index'))->assertRedirect(route('login'));
    }

    public function test_halaman_data_mahasiswa_hanya_menyediakan_baca_saja(): void
    {
        $this->assertTrue(Route::has('admin.data-mahasiswa.index'));

        foreach (['create', 'store', 'edit', 'update', 'destroy', 'verifikasi'] as $aksi) {
            $this->assertFalse(
                Route::has('admin.data-mahasiswa.'.$aksi),
                'Admin Sistem tidak boleh memiliki route '.$aksi.' untuk Data Mahasiswa.'
            );
        }

        $admin = $this->buatAdmin();

        // Hanya GET yang terdaftar; percobaan menulis ke URL yang sama ditolak.
        $this->actingAs($admin)->post('/admin/data-mahasiswa')->assertStatus(405);
    }

    private function buatAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
    }

    /**
     * @param  array<string, mixed>  $atribut
     */
    private function buatMahasiswa(array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ], $atribut));
    }

    /**
     * Kolom warisan (nim, tempat_lahir, tanggal_lahir, no_hp, semester) tetap diisi
     * karena skema SQLite pengujian dibangun dari berkas migrasi yang masih memintanya.
     *
     * @param  array<string, mixed>  $atribut
     */
    private function buatPendaftaran(User $mahasiswa, array $atribut = []): Pendaftaran
    {
        return Pendaftaran::create(array_merge([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->name,
            'nim' => $mahasiswa->nim ?: '23170'.str_pad((string) $mahasiswa->id, 4, '0', STR_PAD_LEFT),
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-01-01',
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '081234567890',
            'semester' => 1,
            'no_wa' => '081234567890',
            'asal_sekolah' => 'MAN 1 Padang',
            'nama_ortu' => 'Abdullah',
            'no_hp_ortu' => '081298765432',
            'kategori' => 'umum',
            'tahun_angkatan' => '2024',
            'fakultas' => 'Tarbiyah dan Keguruan',
            'program_studi' => 'Pendidikan Agama Islam',
            'email_aktif' => $mahasiswa->email,
            'nominal_pembayaran' => 1500000,
            'status' => 'disetujui',
        ], $atribut));
    }
}
