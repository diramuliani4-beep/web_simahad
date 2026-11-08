<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
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

    public function test_musyrifah_can_request_revision_and_student_can_resubmit(): void
    {
        $musyrifah = User::factory()->create([
            'role' => 'musyrifah',
        ]);

        $student = User::factory()->create([
            'role' => 'mahasiswa',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $student->id,
            'nama_lengkap' => $student->name,
            'nim' => '202611001',
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-01-01',
            'semester' => 1,
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'Perempuan',
            'no_wa' => '081234567890',
            'asal_sekolah' => 'MAN 2 Padang',
            'nama_ortu' => 'Fatimah',
            'no_hp_ortu' => '081298765432',
            'kategori' => 'kip',
            'tahun_angkatan' => 2026,
            'fakultas' => 'Tarbiyah',
            'program_studi' => 'Pendidikan Agama Islam',
            'alasan_daftar' => 'Ingin memperdalam ilmu agama.',
            'nominal_pembayaran' => 1000000,
            'bukti_penerimaan' => 'dokumen_pendaftaran/test_penerimaan.pdf',
            'bukti_pembayaran' => 'dokumen_pembayaran/test_bayar.jpg',
            'konfirmasi_data' => true,
            'status' => 'menunggu',
        ]);

        // Musyrifah minta perbaikan
        $response = $this->actingAs($musyrifah)->patch(route('musyrifah.pendaftaran.status', $pendaftaran->id), [
            'status' => 'perlu_perbaikan',
            'catatan_validasi' => 'Bukti pembayaran buram, tolong unggah ulang.',
        ]);

        $response->assertRedirect();
        $pendaftaran->refresh();
        $this->assertSame('perlu_perbaikan', $pendaftaran->status);
        $this->assertSame('Bukti pembayaran buram, tolong unggah ulang.', $pendaftaran->catatan_validasi);

        // Mahasiswa melihat halaman konfirmasi dengan status perbaikan
        $viewResponse = $this->actingAs($student)->get(route('pendaftaran.konfirmasi'));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Pendaftaran Perlu Perbaikan');
        $viewResponse->assertSee('Bukti pembayaran buram, tolong unggah ulang.');

        // Mahasiswa mengirimkan perbaikan
        $fixResponse = $this->actingAs($student)->post(route('pendaftaran.simpanPerbaikan'), [
            'no_wa' => '081299998888',
        ]);

        $fixResponse->assertRedirect(route('pendaftaran.konfirmasi'));
        $pendaftaran->refresh();
        $this->assertSame('menunggu', $pendaftaran->status);
        $this->assertSame('081299998888', $pendaftaran->no_wa);
    }
}
