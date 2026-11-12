<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Pendaftaran;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan dashboard tiap peran memakai data database sebenarnya
 * dan tetap menjaga batas kewenangan masing-masing peran.
 */
class DashboardRingkasanTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_musyrifah_menampilkan_data_sebenarnya(): void
    {
        $musyrifah = $this->pengguna('musyrifah');

        $this->pengguna('mahasiswa');
        $calon = $this->pengguna('mahasiswa');

        Kamar::factory()->create(['lantai' => 1, 'kapasitas' => 4]);
        Kamar::factory()->create(['lantai' => 2, 'kapasitas' => 2]);

        Pendaftaran::create([
            'user_id' => $calon->getKey(),
            'nama_lengkap' => 'Calon Mahasiswa Uji',
            'nim' => '231700777',
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
            'email_aktif' => 'calon@example.com',
            'status' => 'menunggu',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.dashboard'))
            ->assertOk()
            ->assertSee('Total Mahasiswa')
            ->assertSee('Kapasitas &amp; Keterisian Kamar', false)
            ->assertSee('Pendaftaran Menunggu Verifikasi')
            ->assertSee('Calon Mahasiswa Uji')
            ->assertSee('Lantai 1')
            ->assertSee('Lantai 2')
            ->assertDontSee('Siti mengirim bukti pembayaran');
    }

    public function test_dashboard_musyrifah_menampilkan_kondisi_kosong_informatif(): void
    {
        $musyrifah = $this->pengguna('musyrifah');

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.dashboard'))
            ->assertOk()
            ->assertSee('Tidak ada pendaftaran yang menunggu verifikasi.')
            ->assertSee('Belum ada periode pembayaran yang aktif.')
            ->assertSee('Tidak ada jadwal pembelajaran hari ini.');
    }

    public function test_dashboard_pimpinan_menampilkan_laporan_gabungan_tiga_lantai(): void
    {
        $pimpinan = $this->pengguna('pimpinan');

        $this->pengguna('mahasiswa');

        foreach ([1, 2, 3] as $lantai) {
            Kamar::factory()->create(['lantai' => $lantai, 'kapasitas' => 4]);
        }

        $this->actingAs($pimpinan)
            ->get(route('pimpinan.dashboard'))
            ->assertOk()
            ->assertSee('Laporan Gabungan per Lantai')
            ->assertSee('Lantai 1')
            ->assertSee('Lantai 2')
            ->assertSee('Lantai 3')
            ->assertSee('Ringkasan Pembayaran')
            ->assertDontSee('Status pembayaran terkonfirmasi');
    }

    public function test_dashboard_guru_hanya_menampilkan_tugas_miliknya(): void
    {
        $guru = $this->pengguna('guru');
        $guruLain = $this->pengguna('guru');

        Tugas::factory()->dipublikasikan()->create([
            'user_id' => $guru->getKey(),
            'judul' => 'Tugas Milik Guru Saya',
        ]);

        Tugas::factory()->dipublikasikan()->create([
            'user_id' => $guruLain->getKey(),
            'judul' => 'Tugas Rahasia Guru Lain',
        ]);

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('Tugas Milik Guru Saya')
            ->assertDontSee('Tugas Rahasia Guru Lain')
            ->assertSee('Belum ada kelompok yang ditugaskan.');
    }

    public function test_dashboard_admin_menampilkan_rekap_akun_dari_database(): void
    {
        $admin = $this->pengguna('admin', ['name' => 'Admin Uji']);

        $this->pengguna('guru');
        $this->pengguna('musyrifah');
        $this->pengguna('mahasiswa', ['name' => 'Mahasiswa Terbaru']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Jumlah Pengguna per Role')
            ->assertSee('Status Konfigurasi')
            ->assertSee('Mahasiswa Terbaru')
            ->assertSee('Admin Uji');
    }

    public function test_dashboard_admin_tidak_dapat_dibuka_peran_lain(): void
    {
        $guru = $this->pengguna('guru');

        $this->actingAs($guru)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('guru.dashboard'));
    }

    /**
     * @param  array<string, mixed>  $atribut
     */
    private function pengguna(string $role, array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'status' => 'aktif',
        ], $atribut));
    }
}
