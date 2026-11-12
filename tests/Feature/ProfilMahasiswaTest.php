<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('profil.mhs'))->assertRedirect(route('login'));
        $this->get(route('data.mahasiswa'))->assertRedirect(route('login'));
    }

    public function test_halaman_profil_menampilkan_informasi_akun_mahasiswa(): void
    {
        $mahasiswa = $this->buatMahasiswa([
            'name' => 'Aisyah Putri',
            'nim' => '231700123',
            'email' => 'aisyah@example.com',
            'no_hp' => '081234567890',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('profil.mhs'));

        $response->assertOk();
        $response->assertSee('Aisyah Putri');
        $response->assertSee('231700123');
        $response->assertSee('aisyah@example.com');
        $response->assertSee('081234567890');
        $response->assertSee('Lihat Foto');
        $response->assertSee('Edit Foto');
        $response->assertSee('Hapus Foto');
        $response->assertSee('Edit Profil');
    }

    public function test_dropdown_navbar_berisi_foto_nama_dan_tiga_menu(): void
    {
        $mahasiswa = $this->buatMahasiswa([
            'name' => 'Aisyah Putri',
            'nim' => '231700124',
            'foto_profil' => 'foto-profil/aisyah.png',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('profilDropdown', false);
        $response->assertSee('Aisyah Putri');
        $response->assertSee('Lihat Profil');
        $response->assertSee('>Data Saya<', false);
        $response->assertSee('Pengaturan Akun');
        $response->assertSee(route('profil.mhs'), false);
        $response->assertSee(route('data.mahasiswa'), false);
        $response->assertSee('storage/foto-profil/aisyah.png', false);
    }

    public function test_mahasiswa_dapat_memperbarui_nama_email_dan_nomor_whatsapp(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $response = $this->actingAs($mahasiswa)->put(route('profil.mhs.update'), [
            'name' => 'Nama Diperbarui',
            'email' => 'diperbarui@example.com',
            'no_hp' => '0812-3456-789',
        ]);

        $response->assertRedirect(route('profil.mhs'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $mahasiswa->id,
            'name' => 'Nama Diperbarui',
            'email' => 'diperbarui@example.com',
            'no_hp' => '0812-3456-789',
        ]);
    }

    public function test_perubahan_profil_divalidasi(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $penggunaLain = $this->buatMahasiswa(['email' => 'lain@example.com']);

        $response = $this->actingAs($mahasiswa)->put(route('profil.mhs.update'), [
            'name' => '',
            'email' => $penggunaLain->email,
            'no_hp' => 'bukan nomor',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'no_hp']);
        $this->assertDatabaseMissing('users', [
            'id' => $mahasiswa->id,
            'email' => $penggunaLain->email,
        ]);
    }

    public function test_mahasiswa_dapat_mengganti_foto_profil_dan_foto_lama_dihapus(): void
    {
        Storage::fake('public');

        $mahasiswa = $this->buatMahasiswa();
        $fotoLama = UploadedFile::fake()->image('lama.png')->store('foto-profil', 'public');
        $mahasiswa->update(['foto_profil' => $fotoLama]);

        $response = $this->actingAs($mahasiswa)->post(route('profil.mhs.foto.update'), [
            'foto_profil' => UploadedFile::fake()->image('baru.jpg'),
        ]);

        $response->assertRedirect(route('profil.mhs'));
        $response->assertSessionHas('success');

        $mahasiswa->refresh();

        $this->assertNotSame($fotoLama, $mahasiswa->foto_profil);
        Storage::disk('public')->assertExists($mahasiswa->foto_profil);
        Storage::disk('public')->assertMissing($fotoLama);
    }

    public function test_foto_profil_harus_berupa_gambar_yang_valid(): void
    {
        Storage::fake('public');

        $mahasiswa = $this->buatMahasiswa();

        $response = $this->actingAs($mahasiswa)->post(route('profil.mhs.foto.update'), [
            'foto_profil' => UploadedFile::fake()->create('dokumen.pdf', 120, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('foto_profil');
        $this->assertNull($mahasiswa->fresh()->foto_profil);
    }

    public function test_mahasiswa_dapat_menghapus_foto_profil(): void
    {
        Storage::fake('public');

        $mahasiswa = $this->buatMahasiswa();
        $foto = UploadedFile::fake()->image('foto.png')->store('foto-profil', 'public');
        $mahasiswa->update(['foto_profil' => $foto]);

        $response = $this->actingAs($mahasiswa)->delete(route('profil.mhs.foto.destroy'));

        $response->assertRedirect(route('profil.mhs'));
        $response->assertSessionHas('success');

        $this->assertNull($mahasiswa->fresh()->foto_profil);
        Storage::disk('public')->assertMissing($foto);
    }

    public function test_role_selain_mahasiswa_tidak_dapat_mengakses_profil_mahasiswa(): void
    {
        $musyrifah = User::factory()->create([
            'role' => 'musyrifah',
            'status' => 'aktif',
        ]);

        $this->actingAs($musyrifah)->get(route('profil.mhs'))->assertForbidden();
        $this->actingAs($musyrifah)->delete(route('profil.mhs.foto.destroy'))->assertForbidden();
    }

    public function test_perubahan_nama_dan_email_profil_menyelaraskan_data_pendaftaran(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $pendaftaran = Pendaftaran::create([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->name,
            'nim' => '231700888',
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-01-01',
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '081234567890',
            'semester' => 1,
            'no_wa' => '081234567890',
            'asal_sekolah' => 'MAN 1 Padang',
            'nama_ortu' => 'Abdullah',
            'no_hp_ortu' => '081298765432',
            'kategori' => 'umum',
            'tahun_angkatan' => '2024',
            'fakultas' => 'Tarbiyah dan Keguruan',
            'program_studi' => 'Pendidikan Agama Islam',
            'email_aktif' => $mahasiswa->email,
            'status' => 'disetujui',
        ]);

        $this->actingAs($mahasiswa)->put(route('profil.mhs.update'), [
            'name' => 'Nama Sinkron',
            'email' => 'sinkron@example.com',
        ])->assertRedirect(route('profil.mhs'));

        $this->assertDatabaseHas('pendaftarans', [
            'id' => $pendaftaran->id,
            'nama_lengkap' => 'Nama Sinkron',
            'email_aktif' => 'sinkron@example.com',
            'status' => 'disetujui',
        ]);
    }

    /**
     * @param  array<string, mixed>  $atribut
     */
    private function buatMahasiswa(array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ], $atribut));
    }
}
