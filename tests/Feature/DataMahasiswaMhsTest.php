<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataMahasiswaMhsTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_data_mahasiswa_menampilkan_data_pendaftaran_yang_disetujui(): void
    {
        $mahasiswa = $this->buatMahasiswa([
            'name' => 'Aisyah Putri',
            'nim' => '231700321',
        ]);

        $this->buatPendaftaran($mahasiswa, [
            'fakultas' => 'Tarbiyah dan Keguruan',
            'program_studi' => 'Pendidikan Agama Islam',
            'tahun_angkatan' => '2024',
            'asal_sekolah' => 'MAN 1 Padang',
            'nama_ortu' => 'Abdullah',
            'no_hp_ortu' => '081298765432',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('data.mahasiswa'));

        $response->assertOk();
        $response->assertSee('Data Mahasiswa');
        $response->assertSee('Aisyah Putri');
        $response->assertSee('231700321');
        $response->assertSee('Tarbiyah dan Keguruan');
        $response->assertSee('Pendidikan Agama Islam');
        $response->assertSee('2024');
        $response->assertSee('MAN 1 Padang');
        $response->assertSee('Abdullah');
        $response->assertSee('081298765432');
        $response->assertSee('Edit Data');
    }

    public function test_pendaftaran_yang_belum_disetujui_tidak_menampilkan_detail_data(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $this->buatPendaftaran($mahasiswa, [
            'fakultas' => 'Fakultas Rahasia',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('data.mahasiswa'));

        $response->assertOk();
        $response->assertSee('Data pendaftaran belum disetujui');
        $response->assertSee('Menunggu Verifikasi');
        $response->assertDontSee('Fakultas Rahasia');
        $response->assertDontSee('Edit Data');
    }

    public function test_mode_edit_menampilkan_form_data_yang_diperbolehkan(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $this->buatPendaftaran($mahasiswa, ['status' => 'disetujui']);

        $response = $this->actingAs($mahasiswa)->get(route('data.mahasiswa', ['edit' => 1]));

        $response->assertOk();
        $response->assertSee('Edit Data Mahasiswa');
        $response->assertSee('name="nama_lengkap"', false);
        $response->assertSee('name="no_wa"', false);
        $response->assertSee('name="nama_ortu"', false);
        $response->assertSee('NIM, status penerimaan, status mahasiswa');
    }

    public function test_mahasiswa_dapat_memperbarui_data_yang_diperbolehkan(): void
    {
        $mahasiswa = $this->buatMahasiswa(['name' => 'Nama Lama']);

        $pendaftaran = $this->buatPendaftaran($mahasiswa, ['status' => 'disetujui']);

        $response = $this->actingAs($mahasiswa)->put(route('data.mahasiswa.update'), [
            'nama_lengkap' => 'Nama Baru',
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '081211112222',
            'no_wa' => '081233334444',
            'asal_sekolah' => 'MAN 2 Padang',
            'nama_ortu' => 'Fatimah',
            'no_hp_ortu' => '081255556666',
        ]);

        $response->assertRedirect(route('data.mahasiswa'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftarans', [
            'id' => $pendaftaran->id,
            'nama_lengkap' => 'Nama Baru',
            'jenis_kelamin' => 'Perempuan',
            'no_wa' => '081233334444',
            'asal_sekolah' => 'MAN 2 Padang',
            'nama_ortu' => 'Fatimah',
            'no_hp_ortu' => '081255556666',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $mahasiswa->id,
            'name' => 'Nama Baru',
            'no_hp' => '081211112222',
        ]);
    }

    public function test_mahasiswa_tidak_dapat_mengubah_nim_status_role_dan_data_verifikasi(): void
    {
        $mahasiswa = $this->buatMahasiswa([
            'nim' => '231700777',
            'role' => 'mahasiswa',
        ]);

        $pendaftaran = $this->buatPendaftaran($mahasiswa, [
            'status' => 'disetujui',
            'validator_nama' => 'Musyrifah Asli',
        ]);

        $response = $this->actingAs($mahasiswa)->put(route('data.mahasiswa.update'), [
            'nama_lengkap' => 'Nama Baru',
            'jenis_kelamin' => 'Laki-laki',
            'nim' => '999999999',
            'status' => 'ditolak',
            'role' => 'admin',
            'validator_nama' => 'Penyusup',
            'status_pendaftaran' => 'ditolak',
        ]);

        $response->assertRedirect(route('data.mahasiswa'));

        $this->assertDatabaseHas('users', [
            'id' => $mahasiswa->id,
            'nim' => '231700777',
            'role' => 'mahasiswa',
        ]);

        $this->assertDatabaseHas('pendaftarans', [
            'id' => $pendaftaran->id,
            'status' => 'disetujui',
            'validator_nama' => 'Musyrifah Asli',
        ]);

        $this->assertNotSame('999999999', $pendaftaran->fresh()->nim);
    }

    public function test_data_mahasiswa_divalidasi(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $this->buatPendaftaran($mahasiswa, ['status' => 'disetujui']);

        $response = $this->actingAs($mahasiswa)->put(route('data.mahasiswa.update'), [
            'nama_lengkap' => '',
            'jenis_kelamin' => 'Robot',
            'no_hp' => 'telepon rumah',
            'no_wa' => 'abcdefgh',
            'no_hp_ortu' => 'nomor ortu',
            'asal_sekolah' => str_repeat('a', 300),
        ]);

        $response->assertSessionHasErrors([
            'nama_lengkap',
            'jenis_kelamin',
            'no_hp',
            'no_wa',
            'no_hp_ortu',
            'asal_sekolah',
        ]);
    }

    public function test_mahasiswa_hanya_dapat_mengubah_datanya_sendiri(): void
    {
        $mahasiswa = $this->buatMahasiswa(['name' => 'Mahasiswa Satu']);
        $mahasiswaLain = $this->buatMahasiswa(['name' => 'Mahasiswa Dua']);

        $this->buatPendaftaran($mahasiswa, ['status' => 'disetujui']);
        $pendaftaranLain = $this->buatPendaftaran($mahasiswaLain, [
            'nama_lengkap' => 'Mahasiswa Dua',
            'status' => 'disetujui',
        ]);

        $this->actingAs($mahasiswa)
            ->put(route('data.mahasiswa.update'), [
                'nama_lengkap' => 'Mahasiswa Satu Diubah',
                'jenis_kelamin' => 'Laki-laki',
                'no_hp' => '081200000001',
            ])
            ->assertRedirect(route('data.mahasiswa'));

        $this->assertDatabaseHas('pendaftarans', [
            'id' => $pendaftaranLain->id,
            'nama_lengkap' => 'Mahasiswa Dua',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $mahasiswaLain->id,
            'name' => 'Mahasiswa Dua',
        ]);
    }

    public function test_perubahan_data_ditolak_jika_pendaftaran_belum_disetujui(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $this->buatPendaftaran($mahasiswa, ['status' => 'menunggu']);

        $response = $this->actingAs($mahasiswa)->put(route('data.mahasiswa.update'), [
            'nama_lengkap' => 'Tidak Boleh Tersimpan',
            'jenis_kelamin' => 'Perempuan',
        ]);

        $response->assertRedirect(route('data.mahasiswa'));
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('pendaftarans', [
            'nama_lengkap' => 'Tidak Boleh Tersimpan',
        ]);
    }

    public function test_role_selain_mahasiswa_tidak_dapat_mengakses_data_mahasiswa(): void
    {
        $musyrifah = User::factory()->create([
            'role' => 'musyrifah',
            'status' => 'aktif',
        ]);

        $this->actingAs($musyrifah)->get(route('data.mahasiswa'))->assertForbidden();
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

    /**
     * @param  array<string, mixed>  $atribut
     */
    private function buatPendaftaran(User $mahasiswa, array $atribut = []): Pendaftaran
    {
        return Pendaftaran::create(array_merge([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->name,
            'nim' => $mahasiswa->nim ?: '23170'.str_pad((string) $mahasiswa->id, 4, '0', STR_PAD_LEFT),
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
        ], $atribut));
    }
}
