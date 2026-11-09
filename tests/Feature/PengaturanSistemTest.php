<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\PengaturanSistem;
use App\Models\User;
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
            ->assertSee('Informasi Aplikasi')
            ->assertSee('Notifikasi')
            ->assertSee('Keamanan Sistem')
            ->assertSee('Simpan Pengaturan');
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
            'durasi_sesi' => 45,
            'notifikasi_pengumuman' => '1',
            'notifikasi_pembelajaran' => '0',
            'notifikasi_pembayaran' => '1',
        ]);

        $response->assertRedirect(route('admin.pengaturan-sistem'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengaturan_sistems', [
            'nama_sistem' => 'SIMAHAD Akademik',
            'nama_institusi' => 'Ma’had Uji Coba',
            'durasi_sesi' => 45,
            'notifikasi_pengumuman' => true,
            'notifikasi_pembelajaran' => false,
            'notifikasi_pembayaran' => true,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'activity' => 'Pengaturan Sistem',
        ]);

        // Nama sistem dan durasi sesi ikut terpakai pada request berikutnya.
        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('SIMAHAD Akademik');

        $this->assertSame(45, config('session.lifetime'));
        $this->assertSame('SIMAHAD Akademik', config('app.name'));
    }

    public function test_pengaturan_sistem_divalidasi(): void
    {
        $admin = $this->buatPengguna('admin');

        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.update'), [
                'nama_sistem' => '',
                'nama_institusi' => '',
                'durasi_sesi' => 2,
            ])
            ->assertSessionHasErrors(['nama_sistem', 'nama_institusi', 'durasi_sesi']);

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
            ->assertSee('profileMenuToggle', false)
            ->assertSee('profileMenuPanel', false)
            ->assertSee(route('admin.pengaturan-akun'), false)
            ->assertSee('Admin SIMAHAD');
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
