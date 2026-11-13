<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MenuPeran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MusyrifahSidebarTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_musyrifah_tidak_memuat_menu_pengaturan_akun(): void
    {
        $musyrifah = User::factory()->create([
            'role' => 'musyrifah',
        ]);

        $labelMenu = array_column(MenuPeran::bawah($musyrifah), 'label');

        $this->assertNotContains('Pengaturan Akun', $labelMenu);

        // Pengaturan akun tetap dapat dibuka lewat ikon profil di header.
        $this->actingAs($musyrifah)
            ->get(route('pengaturan-akun.index'))
            ->assertOk()
            ->assertSee('Keamanan Akun');
    }

    public function test_sidebar_mahasiswa_tidak_memuat_menu_pengaturan_akun(): void
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
        ]);

        $this->assertNotContains('Pengaturan Akun', array_column(MenuPeran::bawah($mahasiswa), 'label'));

        // Fitur pengaturan akun tetap tersedia lewat ikon profil di header.
        $this->actingAs($mahasiswa)
            ->get(route('pengaturan-akun.index'))
            ->assertOk();
    }
}
