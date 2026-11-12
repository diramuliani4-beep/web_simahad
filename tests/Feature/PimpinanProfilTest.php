<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\PegawaiAkun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tiga menu profil Pimpinan: Lihat Profil, Data Saya, dan Pengaturan Akun
 * harus memiliki fungsi yang berbeda dan tidak saling tumpang tindih.
 */
class PimpinanProfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_dropdown_profil_pimpinan_menampilkan_tiga_menu_profil(): void
    {
        $pimpinan = $this->buatPimpinan();

        $this->actingAs($pimpinan)
            ->get(route('pimpinan.dashboard'))
            ->assertOk()
            ->assertSee('Lihat Profil')
            ->assertSee('Data Saya')
            ->assertSee('Pengaturan Akun')
            ->assertSee(route('profil.pengguna'), false)
            ->assertSee(route('data.pengguna'), false)
            ->assertSee(route('pengaturan-akun.index'), false);
    }

    public function test_lihat_profil_menampilkan_foto_nama_jabatan_dan_identitas_singkat(): void
    {
        $pimpinan = $this->buatPimpinan();

        $this->actingAs($pimpinan)
            ->get(route('profil.pengguna'))
            ->assertOk()
            ->assertSee('Pimpinan SIMAHAD')
            ->assertSee('Pimpinan Ma’had')
            ->assertSee('Jabatan: Pimpinan')
            ->assertSee('pimpinan@example.com')
            ->assertSee('Menu foto profil')
            ->assertSee('Lihat Foto')
            ->assertSee('Edit Foto')
            ->assertSee('Hapus Foto')
            // Halaman Lihat Profil hanya menampilkan data, tanpa form ubah.
            ->assertDontSee('name="jenis_kelamin"', false);
    }

    public function test_data_saya_menyediakan_edit_simpan_batal_dan_menyimpan_perubahan(): void
    {
        $pimpinan = $this->buatPimpinan();

        $this->actingAs($pimpinan)
            ->get(route('data.pengguna'))
            ->assertOk()
            ->assertSee('Data Pribadi')
            ->assertSee('Edit')
            ->assertSee('Simpan')
            ->assertSee('Batal')
            ->assertSee(route('data.pengguna.update'), false);

        $response = $this->actingAs($pimpinan)->put(route('data.pengguna.update'), [
            'name' => 'Pimpinan Baru',
            'no_hp' => '081298765432',
            'jenis_kelamin' => 'L',
            'pendidikan_terakhir' => 'S3',
            'alamat' => 'Padang',
        ]);

        $response->assertRedirect(route('data.pengguna'));
        $response->assertSessionHas('success');

        $pimpinan->refresh();

        $this->assertSame('Pimpinan Baru', $pimpinan->name);
        $this->assertSame('081298765432', $pimpinan->no_hp);
        $this->assertDatabaseHas('pegawais', [
            'nama_pegawai' => 'Pimpinan Baru',
            'pendidikan_terakhir' => 'S3',
            'alamat' => 'Padang',
        ]);
    }

    public function test_pengaturan_akun_khusus_kata_sandi_dan_email(): void
    {
        $pimpinan = $this->buatPimpinan();

        $this->actingAs($pimpinan)
            ->get(route('pengaturan-akun.index'))
            ->assertOk()
            ->assertSee('Keamanan Akun')
            ->assertSee('Email Akun')
            ->assertSee('Kata Sandi Saat Ini')
            ->assertSee('Perbarui Kata Sandi')
            // Bukan halaman profil dan bukan halaman data pribadi.
            ->assertDontSee('Informasi Pribadi')
            ->assertDontSee('Data Pribadi')
            ->assertDontSee('Menu foto profil');
    }

    public function test_kata_sandi_pimpinan_disimpan_sebagai_hash_dan_tidak_ditampilkan(): void
    {
        $pimpinan = $this->buatPimpinan();

        $this->actingAs($pimpinan)->put(route('pengaturan-akun.password.update'), [
            'password_lama' => 'password',
            'password' => 'sandi-pimpinan-123',
            'password_confirmation' => 'sandi-pimpinan-123',
        ])->assertRedirect(route('pengaturan-akun.index'));

        $pimpinan->refresh();

        $this->assertNotSame('sandi-pimpinan-123', $pimpinan->password);
        $this->assertTrue(Hash::check('sandi-pimpinan-123', $pimpinan->password));

        $this->actingAs($pimpinan)
            ->get(route('pengaturan-akun.index'))
            ->assertOk()
            ->assertDontSee('sandi-pimpinan-123')
            ->assertDontSee($pimpinan->password);
    }

    public function test_pimpinan_tidak_dapat_membuka_pengaturan_akun_role_lain(): void
    {
        $pimpinan = $this->buatPimpinan();

        $this->actingAs($pimpinan)->get(route('guru.pengaturan-akun'))->assertForbidden();
        $this->actingAs($pimpinan)->get(route('admin.pengaturan-akun'))->assertForbidden();
    }

    /**
     * Pimpinan aktif dengan biodata pegawai tertaut.
     */
    private function buatPimpinan(): User
    {
        $pegawai = Pegawai::create([
            'nama_pegawai' => 'Pimpinan Ma’had',
            'email' => 'pimpinan@example.com',
            'jenis_kelamin' => 'L',
            'pendidikan_terakhir' => 'S2',
            'status_pegawai' => true,
        ]);

        $pimpinan = User::factory()->create([
            'name' => 'Pimpinan Ma’had',
            'email' => 'pimpinan@example.com',
            'role' => 'pimpinan',
            'status' => 'aktif',
        ]);

        PegawaiAkun::create([
            'pegawai_id' => $pegawai->getKey(),
            'user_id' => $pimpinan->getKey(),
        ]);

        return $pimpinan;
    }
}
