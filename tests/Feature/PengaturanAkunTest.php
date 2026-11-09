<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\PegawaiAkun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengaturanAkunTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('musyrifah.pengaturan_akun'))->assertRedirect(route('login'));
        $this->get(route('guru.pengaturan-akun'))->assertRedirect(route('login'));
        $this->get(route('pimpinan.pengaturan-akun'))->assertRedirect(route('login'));
        $this->get(route('admin.pengaturan-akun'))->assertRedirect(route('login'));
    }

    public function test_setiap_role_dapat_membuka_halaman_pengaturan_akunnya(): void
    {
        $halaman = [
            'musyrifah' => ['rute' => 'musyrifah.pengaturan_akun', 'label' => 'Musyrifah'],
            'guru' => ['rute' => 'guru.pengaturan-akun', 'label' => 'Guru'],
            'pimpinan' => ['rute' => 'pimpinan.pengaturan-akun', 'label' => 'Pimpinan'],
            'admin' => ['rute' => 'admin.pengaturan-akun', 'label' => 'Admin'],
        ];

        foreach ($halaman as $role => $data) {
            $pengguna = $this->buatPengguna($role, ['name' => 'Pengguna '.$data['label']]);

            $this->actingAs($pengguna)
                ->get(route($data['rute']))
                ->assertOk()
                ->assertSee('Pengaturan Akun')
                ->assertSee('Akses '.$data['label'])
                ->assertSee('Keamanan Akun');
        }
    }

    public function test_pengaturan_akun_role_lain_tidak_dapat_diakses(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah');

        $this->actingAs($musyrifah)->get(route('guru.pengaturan-akun'))->assertForbidden();
        $this->actingAs($musyrifah)->get(route('pimpinan.pengaturan-akun'))->assertForbidden();
        $this->actingAs($musyrifah)->get(route('admin.pengaturan-akun'))->assertForbidden();
    }

    public function test_mahasiswa_tidak_dapat_memakai_form_profil_staf(): void
    {
        $mahasiswa = $this->buatPengguna('mahasiswa');

        $this->actingAs($mahasiswa)->put(route('pengaturan-akun.update'), [
            'name' => 'Nama Baru',
            'email' => 'baru@example.com',
        ])->assertForbidden();
    }

    public function test_staf_memperbarui_profil_dan_biodata_pegawai_ikut_selaras(): void
    {
        $pegawai = Pegawai::create([
            'nama_pegawai' => 'Nama Lama',
            'email' => 'lama@example.com',
            'status_pegawai' => true,
        ]);

        $musyrifah = $this->buatPengguna('musyrifah', [
            'name' => 'Nama Lama',
            'email' => 'lama@example.com',
        ], $pegawai);

        $response = $this->actingAs($musyrifah)->put(route('pengaturan-akun.update'), [
            'name' => 'Nama Baru',
            'email' => 'baru@example.com',
            'no_hp' => '081234567890',
        ]);

        $response->assertRedirect(route('musyrifah.pengaturan_akun'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $musyrifah->id,
            'name' => 'Nama Baru',
            'email' => 'baru@example.com',
            'no_hp' => '081234567890',
        ]);

        $this->assertDatabaseHas('pegawais', [
            'id' => $pegawai->id,
            'nama_pegawai' => 'Nama Baru',
            'email' => 'baru@example.com',
            'no_hp' => '081234567890',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $musyrifah->id,
            'activity' => 'Perbarui Profil',
        ]);
    }

    public function test_perubahan_profil_staf_divalidasi(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah');
        $this->buatPengguna('guru', ['email' => 'dipakai@example.com']);

        $response = $this->actingAs($musyrifah)->put(route('pengaturan-akun.update'), [
            'name' => '',
            'email' => 'dipakai@example.com',
            'no_hp' => 'bukan nomor',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'no_hp']);
    }

    public function test_staf_dapat_mengubah_kata_sandi(): void
    {
        $guru = $this->buatPengguna('guru');

        $response = $this->actingAs($guru)->put(route('pengaturan-akun.password.update'), [
            'password_lama' => 'password',
            'password' => 'sandi-baru-123',
            'password_confirmation' => 'sandi-baru-123',
        ]);

        $response->assertRedirect(route('guru.pengaturan-akun'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('sandi-baru-123', $guru->fresh()->password));
    }

    public function test_kata_sandi_saat_ini_harus_benar(): void
    {
        $pimpinan = $this->buatPengguna('pimpinan');

        $response = $this->actingAs($pimpinan)->put(route('pengaturan-akun.password.update'), [
            'password_lama' => 'salah',
            'password' => 'sandi-baru-123',
            'password_confirmation' => 'sandi-baru-123',
        ]);

        $response->assertSessionHasErrors('password_lama');
        $this->assertTrue(Hash::check('password', $pimpinan->fresh()->password));
    }

    public function test_staf_dapat_mengganti_dan_menghapus_foto_profil(): void
    {
        Storage::fake('public');

        $admin = $this->buatPengguna('admin');
        $fotoLama = UploadedFile::fake()->image('lama.png')->store('foto-profil', 'public');
        $admin->update(['foto_profil' => $fotoLama]);

        $this->actingAs($admin)->post(route('pengaturan-akun.foto.update'), [
            'foto_profil' => UploadedFile::fake()->image('baru.jpg'),
        ])->assertRedirect(route('admin.pengaturan-akun'));

        $admin->refresh();

        $this->assertNotSame($fotoLama, $admin->foto_profil);
        Storage::disk('public')->assertExists($admin->foto_profil);
        Storage::disk('public')->assertMissing($fotoLama);

        $fotoBaru = $admin->foto_profil;

        $this->actingAs($admin)->delete(route('pengaturan-akun.foto.destroy'))
            ->assertRedirect(route('admin.pengaturan-akun'));

        $this->assertNull($admin->fresh()->foto_profil);
        Storage::disk('public')->assertMissing($fotoBaru);
    }

    public function test_mahasiswa_dapat_mengubah_kata_sandi_dari_halaman_profil(): void
    {
        $mahasiswa = $this->buatPengguna('mahasiswa');

        $this->actingAs($mahasiswa)
            ->get(route('profil.mhs'))
            ->assertOk()
            ->assertSee('Keamanan Akun')
            ->assertSee('Perbarui Kata Sandi');

        $response = $this->actingAs($mahasiswa)->put(route('profil.mhs.password.update'), [
            'password_lama' => 'password',
            'password' => 'sandi-baru-123',
            'password_confirmation' => 'sandi-baru-123',
        ]);

        $response->assertRedirect(route('profil.mhs'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('sandi-baru-123', $mahasiswa->fresh()->password));
    }

    /**
     * @param  array<string, mixed>  $atribut
     */
    private function buatPengguna(string $role, array $atribut = [], ?Pegawai $pegawai = null): User
    {
        $pengguna = User::factory()->create(array_merge([
            'role' => $role,
            'status' => 'aktif',
        ], $atribut));

        if ($pegawai) {
            PegawaiAkun::create([
                'pegawai_id' => $pegawai->getKey(),
                'user_id' => $pengguna->getKey(),
            ]);
        }

        return $pengguna;
    }
}
