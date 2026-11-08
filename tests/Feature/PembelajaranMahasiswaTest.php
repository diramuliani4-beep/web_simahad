<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\User;
use Database\Seeders\PembelajaranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelajaranMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_pembelajaran_mhs(): void
    {
        $response = $this->get(route('pembelajaran.mhs'));

        $response->assertRedirect(route('login'));
    }

    public function test_mahasiswa_can_view_synchronized_pembelajaran(): void
    {
        $user = User::factory()->create([
            'name' => 'Siti Aminah',
            'email' => 'siti@mahad.ac.id',
            'role' => 'mahasiswa',
            'nim' => '2317020999',
        ]);

        (new PembelajaranSeeder())->run();

        $response = $this->actingAs($user)->get(route('pembelajaran.mhs'));

        $response->assertStatus(200);
        $response->assertSee('Pembelajaran Mahasiswa');
        $response->assertSee('Tersinkronisasi dengan Manajemen Pembelajaran Musyrifah');
        $response->assertSee('Pembelajaran Kitab Kuning');
        $response->assertSee('Penguatan Bahasa Asing');
        $response->assertSee('Penguatan Keterampilan Khusus Wawasan Interdisipliner dan Moderasi Beragama');
        $response->assertSee('Jadwal Pembelajaran Saya');
        $response->assertSee('Kelompok &amp; Pengajar', false);
        $response->assertSee('Kelulusan &amp; Sertifikat', false);
        $response->assertSee('Kalender Pembelajaran');
    }

    public function test_mahasiswa_graduation_status_shows_memenuhi_syarat_when_16_meetings_completed(): void
    {
        $userLulus = User::factory()->create([
            'name' => 'Ahmad Santoso',
            'email' => 'mahasiswa@asrama.ac.id',
            'role' => 'mahasiswa',
            'nim' => '2317020134',
        ]);

        (new PembelajaranSeeder())->run();

        $response = $this->actingAs($userLulus)->get(route('pembelajaran.mhs'));

        $response->assertStatus(200);
        $response->assertSee('Memenuhi Syarat Kelulusan');
        $response->assertSee('Lihat &amp; Cetak Sertifikat', false);
    }

    public function test_detail_pertemuan_ajax_returns_all_16_meetings(): void
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Santoso',
            'email' => 'mahasiswa@asrama.ac.id',
            'role' => 'mahasiswa',
        ]);

        (new PembelajaranSeeder())->run();

        $mapel = MataPelajaran::first();

        $response = $this->actingAs($user)->getJson(route('pembelajaran.mhs.detail_pertemuan', $mapel->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'mapel' => ['id', 'nama', 'kode', 'kategori', 'target_pertemuan', 'hadir', 'is_lulus'],
            'pertemuan' => [
                '*' => ['pertemuan_ke', 'tanggal', 'status', 'catatan'],
            ],
        ]);

        $this->assertCount(16, $response->json('pertemuan'));
    }
}
