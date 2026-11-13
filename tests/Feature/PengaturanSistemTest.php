<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\PengaturanSistem;
use App\Models\User;
use App\Support\MenuPeran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PengaturanSistemTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('admin.pengaturan-sistem'))->assertRedirect(route('login'));
        $this->put(route('admin.pengaturan-sistem.update'), [])->assertRedirect(route('login'));
    }

    public function test_admin_dapat_membuka_halaman_pengaturan_sistem(): void
    {
        $admin = $this->buatPengguna('admin');

        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('Pengaturan Sistem')
            ->assertSee('Identitas Aplikasi')
            ->assertSee('Pengaturan Email')
            ->assertSee('Nama Aplikasi')
            ->assertSee('Nama Institusi')
            ->assertSee('Versi Aplikasi')
            ->assertSee('v'.config('app.version'))
            ->assertSee('logo_uin.png')
            ->assertSee('Keamanan Sistem')
            ->assertSee('Backup dan Pemeliharaan')
            ->assertSee('Riwayat Aktivitas Pengguna')
            ->assertSee('Riwayat Perubahan Sistem')
            ->assertSee('Tes Koneksi')
            ->assertSee('Buat Backup Sekarang')
            ->assertSee('data-edit="identitas"', false)
            ->assertSee('data-batal="identitas"', false)
            // Pengaturan akun dan preferensi notifikasi tidak diduplikasi di halaman ini.
            ->assertDontSee('Notifikasi pengumuman');
    }

    public function test_selain_admin_tidak_dapat_membuka_pengaturan_sistem(): void
    {
        foreach (['musyrifah', 'guru', 'pimpinan'] as $role) {
            $pengguna = $this->buatPengguna($role);

            $this->actingAs($pengguna)
                ->get(route('admin.pengaturan-sistem'))
                ->assertForbidden();

            $this->actingAs($pengguna)
                ->put(route('admin.pengaturan-sistem.update'), [])
                ->assertForbidden();
        }
    }

    public function test_admin_dapat_menyimpan_pengaturan_dan_langsung_diterapkan(): void
    {
        $admin = $this->buatPengguna('admin');

        $response = $this->actingAs($admin)->put(route('admin.pengaturan-sistem.update'), [
            'nama_sistem' => 'SIMAHAD Akademik',
            'nama_institusi' => 'Ma’had Uji Coba',
        ]);

        $response->assertRedirect(route('admin.pengaturan-sistem'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengaturan_sistems', [
            'nama_sistem' => 'SIMAHAD Akademik',
            'nama_institusi' => 'Ma’had Uji Coba',
            // Preferensi notifikasi dan durasi sesi tidak disentuh halaman ini.
            'durasi_sesi' => 120,
            'notifikasi_pengumuman' => true,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'user_role' => 'admin',
            'modul' => 'pengaturan',
            'aksi' => 'diperbarui',
            'objek' => 'Identitas Aplikasi',
        ]);

        // Nama aplikasi langsung terpakai dan perubahan tampil pada riwayat sistem.
        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('SIMAHAD Akademik')
            ->assertSee('Riwayat Perubahan Sistem')
            ->assertSee('memperbarui identitas aplikasi');

        $this->assertSame('SIMAHAD Akademik', config('app.name'));
    }

    public function test_halaman_tidak_mengubah_notifikasi_dan_durasi_sesi(): void
    {
        $admin = $this->buatPengguna('admin');

        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.update'), [
                'nama_sistem' => 'SIMAHAD',
                'nama_institusi' => 'Ma’had Al-Jami’ah Tuanku Imam Bonjol',
                'durasi_sesi' => 5,
                'notifikasi_pengumuman' => '0',
            ])
            ->assertRedirect(route('admin.pengaturan-sistem'));

        $this->assertDatabaseHas('pengaturan_sistems', [
            'durasi_sesi' => 120,
            'notifikasi_pengumuman' => true,
            'notifikasi_pembelajaran' => true,
            'notifikasi_pembayaran' => true,
        ]);

        // Tanpa perubahan nilai, riwayat perubahan sistem tidak bertambah.
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_riwayat_aktivitas_pengguna_dan_perubahan_sistem_ditampilkan(): void
    {
        $admin = $this->buatPengguna('admin', ['name' => 'Admin SIMAHAD']);

        ActivityLog::create([
            'user_name' => 'Ustadzah Huda',
            'user_role' => 'musyrifah',
            'activity' => 'Pengumuman · diterbitkan',
            'description' => 'Menerbitkan pengumuman jadwal ujian',
        ]);

        ActivityLog::create([
            'user_name' => 'Admin Lama',
            'user_role' => 'admin',
            'activity' => 'Pengaturan Sistem',
            'description' => 'Admin Lama memperbarui pengaturan sistem',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('Ustadzah Huda')
            ->assertSee('Menerbitkan pengumuman jadwal ujian')
            ->assertSee('Admin Lama memperbarui pengaturan sistem');
    }

    public function test_pengaturan_sistem_divalidasi(): void
    {
        $admin = $this->buatPengguna('admin');

        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.update'), [
                'nama_sistem' => '',
                'nama_institusi' => '',
            ])
            ->assertSessionHasErrors(['nama_sistem', 'nama_institusi']);

        $this->assertDatabaseHas('pengaturan_sistems', [
            'nama_sistem' => 'SIMAHAD',
            'durasi_sesi' => 120,
        ]);
    }

    public function test_notifikasi_mengikuti_preferensi_sistem(): void
    {
        $mahasiswa = $this->buatPengguna('mahasiswa');

        PengaturanSistem::aktif()->update([
            'notifikasi_pengumuman' => false,
            'notifikasi_pembelajaran' => true,
            'notifikasi_pembayaran' => false,
        ]);

        $this->assertNull(
            Notifikasi::kirim($mahasiswa, Notifikasi::TIPE_PENGUMUMAN_BARU, 'Pengumuman uji')
        );
        $this->assertDatabaseCount('notifikasis', 0);

        $this->assertNotNull(
            Notifikasi::kirim($mahasiswa, Notifikasi::TIPE_TUGAS_BARU, 'Tugas uji')
        );
        $this->assertDatabaseCount('notifikasis', 1);

        $this->assertNull(
            Notifikasi::kirim($mahasiswa, Notifikasi::TIPE_PEMBAYARAN, 'Tagihan uji')
        );
        $this->assertDatabaseCount('notifikasis', 1);
    }

    public function test_menu_pengaturan_akun_admin_tersedia_dari_ikon_profil(): void
    {
        $admin = $this->buatPengguna('admin', ['name' => 'Admin SIMAHAD']);

        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('profilDropdown', false)
            ->assertSee('Lihat Profil')
            ->assertSee(route('pengaturan-akun.index'), false)
            ->assertSee('Admin SIMAHAD');
    }

    public function test_sidebar_admin_tidak_lagi_memuat_menu_pengaturan_akun(): void
    {
        $admin = $this->buatPengguna('admin');

        $labelMenu = array_column(MenuPeran::bawah($admin), 'label');

        $this->assertContains('Pengaturan Sistem', $labelMenu);
        $this->assertNotContains('Pengaturan Akun', $labelMenu);
    }

    public function test_pengaturan_sistem_aman_disimpan_pada_cache_database(): void
    {
        config(['cache.default' => 'database']);

        Cache::store('database')->forget(PengaturanSistem::KUNCI_CACHE);

        $pertama = PengaturanSistem::aktif();
        $kedua = PengaturanSistem::aktif();

        $this->assertSame('SIMAHAD', $kedua->nama_sistem);
        $this->assertSame($pertama->id, $kedua->id);
        $this->assertTrue($kedua->notifikasi_pengumuman);
    }

    private function buatPengguna(string $role, array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'status' => 'aktif',
        ], $atribut));
    }
}
