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

    public function test_halaman_pengaturan_akun_staf_hanya_berisi_kata_sandi_dan_email(): void
    {
        $pimpinan = $this->buatPengguna('pimpinan');

        $this->actingAs($pimpinan)
            ->get(route('pimpinan.pengaturan-akun'))
            ->assertOk()
            ->assertSee('Keamanan Akun')
            ->assertSee('Email Akun')
            ->assertDontSee('Informasi Profil')
            ->assertDontSee('Menu foto profil');

        // Akun mahasiswa tetap memakai formulir profil lamanya.
        $mahasiswa = $this->buatPengguna('mahasiswa');

        $this->actingAs($mahasiswa)
            ->get(route('pengaturan-akun.index'))
            ->assertOk()
            ->assertSee('Informasi Profil')
            ->assertSee('Menu foto profil');
    }

    public function test_staf_mengganti_email_dengan_verifikasi_kata_sandi(): void
    {
        $pegawai = Pegawai::create([
            'nama_pegawai' => 'Musyrifah Contoh',
            'email' => 'lama@example.com',
            'status_pegawai' => true,
        ]);

        $musyrifah = $this->buatPengguna('musyrifah', [
            'email' => 'lama@example.com',
        ], $pegawai);

        $response = $this->actingAs($musyrifah)->put(route('pengaturan-akun.email.update'), [
            'email' => 'baru@example.com',
            'password_saat_ini' => 'password',
        ]);

        $response->assertRedirect(route('pengaturan-akun.index'));
        $response->assertSessionHas('success');

        $this->assertSame('baru@example.com', $musyrifah->fresh()->email);
        $this->assertSame('baru@example.com', $pegawai->fresh()->email);
        $this->assertNotNull($musyrifah->fresh()->email_verified_at);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $musyrifah->id,
            'activity' => 'Ubah Email Akun',
        ]);
    }

    public function test_ganti_email_ditolak_bila_kata_sandi_salah_atau_email_dipakai(): void
    {
        $pimpinan = $this->buatPengguna('pimpinan', ['email' => 'lama@example.com']);
        $this->buatPengguna('guru', ['email' => 'dipakai@example.com']);

        $this->actingAs($pimpinan)->put(route('pengaturan-akun.email.update'), [
            'email' => 'baru@example.com',
            'password_saat_ini' => 'salah',
        ])->assertSessionHasErrors('password_saat_ini');

        $this->assertSame('lama@example.com', $pimpinan->fresh()->email);

        $this->actingAs($pimpinan)->put(route('pengaturan-akun.email.update'), [
            'email' => 'dipakai@example.com',
            'password_saat_ini' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertSame('lama@example.com', $pimpinan->fresh()->email);
    }

    public function test_akun_buatan_admin_membuat_kata_sandi_pertama(): void
    {
        $pimpinan = User::factory()->belumPunyaKataSandi()->create([
            'role' => 'pimpinan',
            'status' => 'menunggu aktivasi',
            'email' => 'pimpinan@example.com',
        ]);

        $this->actingAs($pimpinan)
            ->get(route('pimpinan.pengaturan-akun'))
            ->assertOk()
            ->assertSee('Buat Kata Sandi')
            ->assertSee('Email Akun')
            ->assertDontSee('Kata Sandi Saat Ini')
            ->assertDontSee('name="password_lama"', false);

        // Penggantian email belum tersedia sebelum kata sandi dibuat sendiri.
        $this->actingAs($pimpinan)->put(route('pengaturan-akun.email.update'), [
            'email' => 'baru@example.com',
            'password_saat_ini' => 'password',
        ])->assertForbidden();

        $response = $this->actingAs($pimpinan)->put(route('pengaturan-akun.password.update'), [
            'email_konfirmasi' => 'pimpinan@example.com',
            'password' => 'sandi-pertama-123',
            'password_confirmation' => 'sandi-pertama-123',
        ]);

        $response->assertRedirect(route('pengaturan-akun.index'));
        $response->assertSessionHas('success');

        $pimpinan->refresh();

        $this->assertNotNull($pimpinan->password_set_at);
        $this->assertNotSame('sandi-pertama-123', $pimpinan->password);
        $this->assertTrue(Hash::check('sandi-pertama-123', $pimpinan->password));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $pimpinan->id,
            'activity' => 'Buat Kata Sandi',
        ]);
    }

    public function test_kata_sandi_pertama_memerlukan_email_konfirmasi_yang_sesuai(): void
    {
        $pimpinan = User::factory()->belumPunyaKataSandi()->create([
            'role' => 'pimpinan',
            'status' => 'menunggu aktivasi',
            'email' => 'pimpinan@example.com',
        ]);

        $response = $this->actingAs($pimpinan)->put(route('pengaturan-akun.password.update'), [
            'email_konfirmasi' => 'bukan-email-akun@example.com',
            'password' => 'sandi-pertama-123',
            'password_confirmation' => 'sandi-pertama-123',
        ]);

        $response->assertSessionHas('error');
        $this->assertNull($pimpinan->fresh()->password_set_at);
    }

    public function test_kata_sandi_tidak_pernah_ditampilkan_di_halaman_maupun_array_model(): void
    {
        $hash = Hash::make('rahasia-sekali');
        $pimpinan = User::factory()->create([
            'role' => 'pimpinan',
            'status' => 'aktif',
            'password' => $hash,
        ]);

        $this->assertArrayNotHasKey('password', $pimpinan->toArray());

        $this->actingAs($pimpinan)
            ->get(route('pimpinan.pengaturan-akun'))
            ->assertOk()
            ->assertDontSee($hash)
            ->assertDontSee('rahasia-sekali');
    }

    public function test_staf_dapat_mengubah_kata_sandi(): void
    {
        $guru = $this->buatPengguna('guru');

        $response = $this->actingAs($guru)->put(route('pengaturan-akun.password.update'), [
            'password_lama' => 'password',
            'password' => 'sandi-baru-123',
            'password_confirmation' => 'sandi-baru-123',
        ]);

        $response->assertRedirect(route('pengaturan-akun.index'));
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
        ])->assertRedirect(route('profil.pengguna'));

        $admin->refresh();

        $this->assertNotSame($fotoLama, $admin->foto_profil);
        Storage::disk('public')->assertExists($admin->foto_profil);
        Storage::disk('public')->assertMissing($fotoLama);

        $fotoBaru = $admin->foto_profil;

        $this->actingAs($admin)->delete(route('pengaturan-akun.foto.destroy'))
            ->assertRedirect(route('profil.pengguna'));

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
