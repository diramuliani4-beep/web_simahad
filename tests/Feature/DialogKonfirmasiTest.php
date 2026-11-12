<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan dialog konfirmasi tindakan (keluar, hapus akun, hapus pegawai)
 * tetap tampil lengkap dengan informasi dan tombol keputusan setelah
 * ukurannya diperkecil agar lebih proporsional.
 */
class DialogKonfirmasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_konfirmasi_keluar_menampilkan_informasi_dan_tombol(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'status' => 'aktif']);

        $this->actingAs($mahasiswa)
            ->get(route('logout.confirm'))
            ->assertOk()
            ->assertSee('Keluar dari Akun?')
            ->assertSee('Apakah kamu yakin ingin keluar dari akun SIMAHAD?')
            ->assertSee('Batal')
            ->assertSee('Ya, Keluar');
    }

    public function test_dialog_hapus_akun_menampilkan_konfirmasi_lengkap(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);

        $this->actingAs($admin)
            ->get(route('admin.manajemen-pengguna'))
            ->assertOk()
            ->assertSee('Hapus Akun Pengguna?')
            ->assertSee('Batal')
            ->assertSee('Ya, Hapus');
    }

    public function test_dialog_hapus_pegawai_menampilkan_konfirmasi_lengkap(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);

        $this->actingAs($admin)
            ->get(route('admin.data-pegawai.index'))
            ->assertOk()
            ->assertSee('Hapus Data Pegawai?')
            ->assertSee('Batal')
            ->assertSee('Ya, Hapus');
    }
}
