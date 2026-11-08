<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendaftaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_pendaftaran_screen_can_be_rendered_for_authenticated_student(): void
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($user)->get(route('pendaftaran.pribadi'));

        $response->assertOk();
        $response->assertViewIs('pendaftaran.data_diri');
        $response->assertSee('Pendaftaran Mahasiswa Baru');
        $response->assertSee('Data Diri');
        $response->assertSee('Jenis Kelamin');
    }

    public function test_pendaftaran_aliases_are_accessible(): void
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
        ]);

        $this->actingAs($user)
            ->get(route('pendaftaran'))
            ->assertRedirect(route('pendaftaran.pribadi'));

        $this->actingAs($user)
            ->get(route('pendaftaran.data_diri'))
            ->assertOk()
            ->assertViewIs('pendaftaran.data_diri');
    }

    public function test_student_can_save_data_diri_and_proceed(): void
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($user)->post(route('pendaftaran.simpanPribadi'), [
            'nama_lengkap' => $user->name,
            'jenis_kelamin' => 'Laki-laki',
            'email' => $user->email,
            'no_wa' => '081234567890',
            'asal_sekolah' => 'MAN 1 Padang',
            'nama_ortu' => 'Ahmad',
            'no_hp_ortu' => '081298765432',
            'kategori' => 'umum',
        ]);

        $response->assertRedirect(route('pendaftaran.akademik'));
        $this->assertNotNull(session('pendaftaran'));
        $this->assertSame('Laki-laki', session('pendaftaran')['jenis_kelamin']);
    }
}
