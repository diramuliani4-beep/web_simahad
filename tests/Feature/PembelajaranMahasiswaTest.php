<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\MataPelajaran;
use App\Models\PertemuanPembelajaran;
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

        (new PembelajaranSeeder)->run();

        $response = $this->actingAs($user)->get(route('pembelajaran.mhs'));

        $response->assertStatus(200);
        $response->assertSee('Pembelajaran Mahasiswa');
        $response->assertSee('Kelompok Belajar Saya');
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

        (new PembelajaranSeeder)->run();

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

        (new PembelajaranSeeder)->run();

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

    public function test_detail_kehadiran_hanya_menampilkan_data_mahasiswa_yang_login(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $mahasiswaLain = User::factory()->create(['role' => 'mahasiswa']);

        [$mapel, $jadwal] = $this->buatMapelDenganJadwal();

        $this->simpanAbsensi($mahasiswa, $mapel, $jadwal, 1, 'hadir');
        $this->simpanAbsensi($mahasiswa, $mapel, $jadwal, 2, 'izin');
        $this->simpanAbsensi($mahasiswaLain, $mapel, $jadwal, 3, 'alpa');

        $response = $this->actingAs($mahasiswa)
            ->getJson(route('pembelajaran.mhs.detail_pertemuan', $mapel->id));

        $response->assertOk()
            ->assertJsonPath('ringkasan.hadir', 1)
            ->assertJsonPath('ringkasan.izin', 1)
            ->assertJsonPath('ringkasan.sakit', 0)
            ->assertJsonPath('ringkasan.alpa', 0)
            ->assertJsonPath('ringkasan.total', 2)
            ->assertJsonPath('ringkasan.target', 16)
            ->assertJsonPath('periode.0.periode', 'Semester Ganjil 2026/2027')
            ->assertJsonPath('periode.0.hadir', 1)
            ->assertJsonPath('periode.0.total', 2)
            ->assertJsonCount(16, 'pertemuan');

        $this->assertEquals(50.0, $response->json('ringkasan.persentase'));
        $this->assertEquals(50.0, $response->json('periode.0.persentase'));
    }

    public function test_sidebar_mahasiswa_tidak_lagi_memuat_menu_absensi(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        (new PembelajaranSeeder)->run();

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('absensi.mhs'), false);

        $this->actingAs($mahasiswa)
            ->get(route('pembelajaran.mhs'))
            ->assertOk()
            ->assertDontSee(route('absensi.mhs'), false)
            ->assertSee('Lihat Kehadiran &amp; Pertemuan', false);
    }

    /**
     * @return array{0: MataPelajaran, 1: JadwalPembelajaran}
     */
    private function buatMapelDenganJadwal(): array
    {
        $mapel = MataPelajaran::create([
            'nama' => 'Pembelajaran Kitab Kuning',
            'kode' => 'PKK-01',
            'kategori' => 'Program Ma\'had',
            'target_pertemuan' => 16,
        ]);

        $jadwal = JadwalPembelajaran::create([
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => 'Kelompok 1',
            'guru_pengampu' => 'Ust. Fulan',
            'periode' => 'Semester Ganjil 2026/2027',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'ruangan' => 'Ruang 1',
        ]);

        return [$mapel, $jadwal];
    }

    private function simpanAbsensi(
        User $mahasiswa,
        MataPelajaran $mapel,
        JadwalPembelajaran $jadwal,
        int $pertemuanKe,
        string $status
    ): AbsensiPembelajaran {
        $pertemuan = PertemuanPembelajaran::firstOrCreate([
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => 'Kelompok 1',
            'periode' => 'Semester Ganjil 2026/2027',
            'pertemuan_ke' => $pertemuanKe,
        ], [
            'jadwal_id' => $jadwal->id,
            'tanggal' => '2026-09-07',
            'status' => PertemuanPembelajaran::STATUS_SELESAI,
        ]);

        return AbsensiPembelajaran::create([
            'user_id' => $mahasiswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'jadwal_id' => $jadwal->id,
            'pertemuan_id' => $pertemuan->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => $pertemuanKe,
            'tanggal' => '2026-09-07',
            'status' => $status,
            'catatan' => 'Pertemuan terlaksana',
        ]);
    }
}
