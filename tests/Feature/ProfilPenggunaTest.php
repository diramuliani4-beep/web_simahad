<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\PegawaiAkun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilPenggunaTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_profil_staf_menampilkan_data_akun_dari_database(): void
    {
        $pegawai = Pegawai::create([
            'nama_pegawai' => 'Guru Contoh',
            'email' => 'guru.contoh@example.com',
            'jenis_kelamin' => 'L',
            'pendidikan_terakhir' => 'S2',
            'status_pegawai' => true,
        ]);

        $guru = $this->buatPengguna('guru', [
            'name' => 'Guru Contoh',
            'email' => 'guru.contoh@example.com',
            'no_hp' => '081234567890',
        ], $pegawai);

        $this->actingAs($guru)
            ->get(route('profil.pengguna'))
            ->assertOk()
            ->assertSee('Guru Contoh')
            ->assertSee('guru.contoh@example.com')
            ->assertSee('081234567890')
            ->assertSee('Jabatan')
            ->assertSee('Informasi Pribadi')
            ->assertSee('Nomor Telepon')
            ->assertSee('Menu foto profil')
            ->assertSee('Lihat Foto')
            ->assertSee('Edit Foto')
            ->assertSee('Hapus Foto')
            ->assertSee(route('profil.pengguna.foto.update'), false)
            ->assertSee(route('profil.pengguna.foto.destroy'), false);
    }

    public function test_lihat_profil_tidak_mencampur_form_data_saya_dan_pengaturan_akun(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah', ['name' => 'Musyrifah Contoh']);

        $this->actingAs($musyrifah)
            ->get(route('profil.pengguna'))
            ->assertOk()
            ->assertSee('Informasi Pribadi')
            // Form ubah data pribadi hanya ada di halaman Data Saya.
            ->assertDontSee('name="jenis_kelamin"', false)
            ->assertDontSee('name="alamat"', false)
            ->assertDontSee('name="pendidikan_terakhir"', false)
            // Form kata sandi dan email hanya ada di Pengaturan Akun.
            ->assertDontSee(route('pengaturan-akun.password.update'), false)
            ->assertDontSee(route('pengaturan-akun.email.update'), false);
    }

    public function test_foto_profil_staf_hanya_mengubah_foto_milik_sendiri(): void
    {
        Storage::fake('public');

        $guru = $this->buatPengguna('guru', ['name' => 'Guru Pemilik']);
        $pimpinan = $this->buatPengguna('pimpinan', ['name' => 'Pimpinan Lain']);

        $fotoLain = UploadedFile::fake()->image('lain.png')->store('foto-profil', 'public');
        $pimpinan->update(['foto_profil' => $fotoLain]);

        $this->actingAs($guru)->post(route('profil.pengguna.foto.update'), [
            'foto_profil' => UploadedFile::fake()->image('guru.jpg'),
        ])->assertRedirect(route('profil.pengguna'));

        $this->assertNotNull($guru->fresh()->foto_profil);
        // Foto akun lain tidak ikut berubah.
        $this->assertSame($fotoLain, $pimpinan->fresh()->foto_profil);
    }

    public function test_tamu_tidak_dapat_membuka_lihat_profil(): void
    {
        $this->get(route('profil.pengguna'))->assertRedirect(route('login'));
    }

    public function test_halaman_data_staf_menampilkan_biodata_kepegawaian(): void
    {
        $pegawai = Pegawai::create([
            'nama_pegawai' => 'Musyrifah Contoh',
            'email' => 'musyrifah@example.com',
            'jenis_kelamin' => 'P',
            'alamat' => 'Padang',
            'status_pegawai' => true,
        ]);

        $musyrifah = $this->buatPengguna('musyrifah', [
            'name' => 'Musyrifah Contoh',
            'email' => 'musyrifah@example.com',
        ], $pegawai);

        $this->actingAs($musyrifah)
            ->get(route('data.pengguna'))
            ->assertOk()
            ->assertSee('Data Akun')
            ->assertSee('Data Pribadi')
            ->assertSee('Jenis Kelamin')
            ->assertSee('Padang');
    }

    public function test_data_saya_menampilkan_tombol_edit_simpan_dan_batal(): void
    {
        $pimpinan = $this->buatPengguna('pimpinan', ['name' => 'Pimpinan Contoh']);

        $this->actingAs($pimpinan)
            ->get(route('data.pengguna'))
            ->assertOk()
            ->assertSee('Data Saya')
            ->assertSee('Data Pribadi')
            ->assertSee('Edit')
            ->assertSee('Simpan')
            ->assertSee('Batal')
            ->assertSee(route('data.pengguna.update'), false);
    }

    public function test_data_saya_memperbarui_data_pribadi_dan_menyelaraskan_pegawai(): void
    {
        $pegawai = Pegawai::create([
            'nama_pegawai' => 'Nama Lama',
            'email' => 'lama@example.com',
            'status_pegawai' => true,
        ]);

        $pimpinan = $this->buatPengguna('pimpinan', [
            'name' => 'Nama Lama',
            'email' => 'lama@example.com',
        ], $pegawai);

        $this->actingAs($pimpinan)->put(route('data.pengguna.update'), [
            'name' => 'Nama Baru',
            'no_hp' => '081200000000',
            'jenis_kelamin' => 'L',
            'pendidikan_terakhir' => 'S3',
            'alamat' => 'Jakarta',
        ])->assertRedirect(route('data.pengguna'));

        $pimpinan->refresh();
        $pegawai->refresh();

        $this->assertSame('Nama Baru', $pimpinan->name);
        $this->assertSame('081200000000', $pimpinan->no_hp);
        // Email tidak diubah dari halaman Data Saya.
        $this->assertSame('lama@example.com', $pimpinan->email);

        $this->assertSame('Nama Baru', $pegawai->nama_pegawai);
        $this->assertSame('081200000000', $pegawai->no_hp);
        $this->assertSame('L', $pegawai->jenis_kelamin);
        $this->assertSame('S3', $pegawai->pendidikan_terakhir);
        $this->assertSame('Jakarta', $pegawai->alamat);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $pimpinan->id,
            'activity' => 'Perbarui Data Pribadi',
        ]);
    }

    public function test_data_saya_memvalidasi_isian_yang_salah(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah');

        $response = $this->actingAs($musyrifah)->put(route('data.pengguna.update'), [
            'name' => '',
            'no_hp' => 'bukan nomor',
            'jenis_kelamin' => 'X',
        ]);

        $response->assertSessionHasErrors(['name', 'no_hp', 'jenis_kelamin']);
    }

    public function test_mahasiswa_tidak_dapat_menyimpan_data_pribadi_staf(): void
    {
        $mahasiswa = $this->buatPengguna('mahasiswa');

        $this->actingAs($mahasiswa)->put(route('data.pengguna.update'), [
            'name' => 'Nama Baru',
        ])->assertForbidden();
    }

    public function test_mahasiswa_tidak_dapat_membuka_halaman_profil_staf(): void
    {
        $mahasiswa = $this->buatPengguna('mahasiswa');

        $this->actingAs($mahasiswa)->get(route('profil.pengguna'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('data.pengguna'))->assertForbidden();
    }

    public function test_pengaturan_akun_terpadu_dapat_dibuka_semua_role(): void
    {
        foreach (['mahasiswa', 'guru', 'musyrifah', 'pimpinan', 'admin'] as $role) {
            $pengguna = $this->buatPengguna($role, ['name' => 'Pengguna '.ucfirst($role)]);

            $this->actingAs($pengguna)
                ->get(route('pengaturan-akun.index'))
                ->assertOk()
                ->assertSee('Pengaturan Akun')
                ->assertSee('Keamanan Akun')
                ->assertSee('Kata Sandi Saat Ini')
                ->assertSee('Tampilkan kata sandi');
        }
    }

    public function test_dropdown_profil_menampilkan_tiga_menu_untuk_setiap_role(): void
    {
        foreach (['mahasiswa', 'guru', 'musyrifah', 'pimpinan', 'admin'] as $role) {
            $pengguna = $this->buatPengguna($role);

            $halaman = $role === 'mahasiswa' ? route('dashboard') : $this->halamanDashboard($role);

            $this->actingAs($pengguna)
                ->get($halaman)
                ->assertOk()
                ->assertSee('Lihat Profil')
                ->assertSee('Data Saya')
                ->assertSee('Pengaturan Akun');
        }
    }

    private function halamanDashboard(string $role): string
    {
        return match ($role) {
            'guru' => route('guru.dashboard'),
            'musyrifah' => route('musyrifah.dashboard'),
            'pimpinan' => route('pimpinan.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('dashboard'),
        };
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
