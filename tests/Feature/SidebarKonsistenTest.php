<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PembelajaranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan seluruh role memakai satu komponen sidebar yang sama:
 * latar gambar gedung Ma'had, overlay maroon, tombol collapse, dan
 * penanda menu aktif yang mengikuti halaman yang sedang dibuka.
 */
class SidebarKonsistenTest extends TestCase
{
    use RefreshDatabase;

    public function test_seluruh_role_memakai_sidebar_bersama_dengan_latar_mahad(): void
    {
        $beranda = [
            'admin' => 'admin.dashboard',
            'pimpinan' => 'pimpinan.dashboard',
            'musyrifah' => 'musyrifah.dashboard',
            'guru' => 'guru.dashboard',
            'mahasiswa' => 'dashboard',
        ];

        foreach ($beranda as $role => $rute) {
            $pengguna = User::factory()->create([
                'role' => $role,
                'status' => 'aktif',
            ]);

            $respon = $this->actingAs($pengguna)->get(route($rute));

            $respon->assertOk()
                ->assertSee('images/mahad.jpeg')
                ->assertSee("Ma'had Al-Jami'ah UIN Imam Bonjol", false);

            $html = $respon->getContent();

            $this->assertSame(1, substr_count($html, 'id="sidebar"'), "Sidebar ganda pada dashboard {$role}.");
            $this->assertStringContainsString('sb sb--', $html, "Sidebar bersama tidak terpakai pada dashboard {$role}.");
            $this->assertStringContainsString('data-sb-kerut', $html, "Tombol collapse hilang pada dashboard {$role}.");
            $this->assertStringContainsString('aria-current="page"', $html, "Menu aktif tidak ditandai pada dashboard {$role}.");
        }
    }

    public function test_halaman_mahasiswa_memakai_sidebar_yang_sama_dan_menu_aktif_mengikuti_halaman(): void
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'status' => 'aktif',
            'nim' => '2317020199',
        ]);

        $halamanMenu = [
            'dashboard',
            'kamar',
            'pengumuman.mhs',
            'tugas.mhs.index',
        ];

        foreach ($halamanMenu as $rute) {
            $respon = $this->actingAs($mahasiswa)->get(route($rute));

            $respon->assertOk()
                ->assertSee('images/mahad.jpeg')
                ->assertSee('UIN Imam Bonjol');

            $html = $respon->getContent();

            $this->assertSame(1, substr_count($html, 'id="sidebar"'), "Sidebar ganda pada halaman {$rute}.");
            $this->assertStringContainsString('sb sb--', $html, "Sidebar bersama tidak terpakai pada halaman {$rute}.");
            $this->assertStringContainsString('aria-current="page"', $html, "Menu aktif tidak ditandai pada halaman {$rute}.");
        }

        // Halaman di luar daftar menu tetap memakai sidebar yang sama.
        $halamanLain = [
            'profil.mhs',
            'data.mahasiswa',
        ];

        foreach ($halamanLain as $rute) {
            $respon = $this->actingAs($mahasiswa)->get(route($rute));

            $respon->assertOk()->assertSee('images/mahad.jpeg');

            $html = $respon->getContent();

            $this->assertSame(1, substr_count($html, 'id="sidebar"'), "Sidebar ganda pada halaman {$rute}.");
            $this->assertStringContainsString('sb sb--', $html, "Sidebar bersama tidak terpakai pada halaman {$rute}.");
        }
    }

    public function test_halaman_pendaftaran_pembelajaran_dan_surat_memakai_sidebar_bersama(): void
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'status' => 'aktif',
            'nim' => '2317020177',
        ]);

        (new PembelajaranSeeder)->run();

        $halaman = [
            'pendaftaran.pribadi',
            'pembelajaran.mhs',
            'absensi.mhs',
            'pembayaran',
            'surat.perizinan.mhs',
        ];

        foreach ($halaman as $rute) {
            $respon = $this->actingAs($mahasiswa)->get(route($rute));

            $respon->assertOk()->assertSee('images/mahad.jpeg');

            $html = $respon->getContent();

            $this->assertSame(1, substr_count($html, 'id="sidebar"'), "Sidebar ganda pada halaman {$rute}.");
            $this->assertStringContainsString('sb sb--', $html, "Sidebar bersama tidak terpakai pada halaman {$rute}.");
            $this->assertStringContainsString('data-sb-kerut', $html, "Tombol collapse hilang pada halaman {$rute}.");
        }
    }

    public function test_sidebar_mahasiswa_tidak_memuat_menu_pengaturan_akun(): void
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);

        $html = $this->actingAs($mahasiswa)->get(route('dashboard'))->assertOk()->getContent();

        $awal = strpos($html, 'id="sidebar"');
        $this->assertNotFalse($awal, 'Sidebar tidak dirender.');

        // Hanya bagian sidebar yang diperiksa; ikon profil di header tetap
        // menyediakan akses pengaturan akun.
        $sidebar = substr($html, $awal, strpos($html, 'data-sb-tirai') - $awal);

        $this->assertStringNotContainsString('Pengaturan Akun', $sidebar);
        $this->assertStringNotContainsString(route('pengaturan-akun.index'), $sidebar);
    }
}
