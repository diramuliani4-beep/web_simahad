<?php

namespace Tests\Feature;

use App\Models\KelompokBelajar;
use App\Models\KelompokBelajarAnggota;
use App\Models\MataPelajaran;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MusyrifahKelompokBelajarTest extends TestCase
{
    use RefreshDatabase;

    private User $musyrifah;

    private User $mahasiswaDisetujui;

    private MataPelajaran $mapelKitabKuning;

    private MataPelajaran $mapelBahasaAsing;

    private KelompokBelajar $kelompok1;

    private KelompokBelajar $kelompok2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->musyrifah = User::factory()->create([
            'name' => 'Musyrifah Asrama',
            'email' => 'musyrifah@mahad.ac.id',
            'role' => 'musyrifah',
        ]);

        $this->mahasiswaDisetujui = User::factory()->create([
            'name' => 'Fatimah Zahra',
            'email' => 'fatimah@mahad.ac.id',
            'role' => 'mahasiswa',
            'nim' => '2317020055',
        ]);

        $this->createPendaftaran($this->mahasiswaDisetujui, 'disetujui');

        $this->kelompok1 = KelompokBelajar::create([
            'nama_kelompok' => 'Kelompok 1',
            'nomor_kelompok' => 1,
            'keterangan' => 'Kelompok Belajar 1',
        ]);

        $this->kelompok2 = KelompokBelajar::create([
            'nama_kelompok' => 'Kelompok 2',
            'nomor_kelompok' => 2,
            'keterangan' => 'Kelompok Belajar 2',
        ]);

        $this->mapelKitabKuning = MataPelajaran::create([
            'nama' => 'Pembelajaran Kitab Kuning',
            'kode' => 'PKK-01',
            'kategori' => 'Keislaman',
            'target_pertemuan' => 16,
        ]);

        $this->mapelBahasaAsing = MataPelajaran::create([
            'nama' => 'Penguatan Bahasa Asing',
            'kode' => 'PBA-01',
            'kategori' => 'Bahasa',
            'target_pertemuan' => 16,
        ]);
    }

    public function test_non_musyrifah_cannot_access_manajemen_pembelajaran(): void
    {
        $response = $this->actingAs($this->mahasiswaDisetujui)
            ->get(route('musyrifah.manajemen_pembelajaran'));

        $response->assertStatus(403);
    }

    public function test_musyrifah_can_view_kelompok_belajar_page_without_tambah_pengajar_button(): void
    {
        $response = $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok']));

        $response->assertStatus(200);
        // Pastikan fitur tambah pengajar kelompok telah dihapus
        $response->assertDontSee('+ Tambah Pengajar Kelompok');
        // Pastikan fitur kelola anggota kelompok tersedia
        $response->assertSee('Manajemen Anggota Kelompok Belajar');
        $response->assertSee('+ Tambah Mahasiswa ke Kelompok');
        $response->assertSee('Kelompok 1');
        $response->assertSee('Kelompok 2');
    }

    public function test_musyrifah_can_add_approved_student_to_kelompok_belajar(): void
    {
        $response = $this->actingAs($this->musyrifah)
            ->post(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_id' => $this->mahasiswaDisetujui->id,
                'mata_pelajaran_id' => $this->mapelKitabKuning->id,
                'kelompok_belajar_id' => $this->kelompok1->id,
                'keterangan' => 'Penempatan awal',
            ]);

        $response->assertRedirect(route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kelompok_belajar_anggotas', [
            'user_id' => $this->mahasiswaDisetujui->id,
            'mata_pelajaran_id' => $this->mapelKitabKuning->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
        ]);
    }

    public function test_unapproved_student_cannot_be_added_to_kelompok_belajar(): void
    {
        $unapprovedStudent = User::factory()->create([
            'name' => 'Calon Mahasiswa',
            'role' => 'mahasiswa',
        ]);

        $this->createPendaftaran($unapprovedStudent, 'pending');

        $response = $this->actingAs($this->musyrifah)
            ->post(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_id' => $unapprovedStudent->id,
                'mata_pelajaran_id' => $this->mapelKitabKuning->id,
                'kelompok_belajar_id' => $this->kelompok1->id,
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('kelompok_belajar_anggotas', [
            'user_id' => $unapprovedStudent->id,
        ]);
    }

    public function test_student_cannot_have_more_than_one_group_in_same_mata_pelajaran(): void
    {
        // Pertama, daftarkan mahasiswa ke Kelompok 1 untuk Kitab Kuning
        KelompokBelajarAnggota::create([
            'user_id' => $this->mahasiswaDisetujui->id,
            'mata_pelajaran_id' => $this->mapelKitabKuning->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
        ]);

        // Coba daftarkan kembali ke Kelompok 2 untuk Kitab Kuning yang sama
        $response = $this->actingAs($this->musyrifah)
            ->post(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_id' => $this->mahasiswaDisetujui->id,
                'mata_pelajaran_id' => $this->mapelKitabKuning->id,
                'kelompok_belajar_id' => $this->kelompok2->id,
            ]);

        $response->assertSessionHas('error');

        // Pastikan tetap hanya ada 1 keanggotaan dan tetap di Kelompok 1
        $this->assertEquals(1, KelompokBelajarAnggota::where('user_id', $this->mahasiswaDisetujui->id)->where('mata_pelajaran_id', $this->mapelKitabKuning->id)->count());
        $this->assertDatabaseHas('kelompok_belajar_anggotas', [
            'user_id' => $this->mahasiswaDisetujui->id,
            'mata_pelajaran_id' => $this->mapelKitabKuning->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
        ]);
    }

    public function test_student_can_be_assigned_to_group_in_another_mata_pelajaran(): void
    {
        // Mahasiswa di Kelompok 1 untuk Kitab Kuning
        KelompokBelajarAnggota::create([
            'user_id' => $this->mahasiswaDisetujui->id,
            'mata_pelajaran_id' => $this->mapelKitabKuning->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
        ]);

        // Mahasiswa didaftarkan ke Kelompok 2 untuk Bahasa Asing
        $response = $this->actingAs($this->musyrifah)
            ->post(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_id' => $this->mahasiswaDisetujui->id,
                'mata_pelajaran_id' => $this->mapelBahasaAsing->id,
                'kelompok_belajar_id' => $this->kelompok2->id,
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kelompok_belajar_anggotas', [
            'user_id' => $this->mahasiswaDisetujui->id,
            'mata_pelajaran_id' => $this->mapelBahasaAsing->id,
            'kelompok_belajar_id' => $this->kelompok2->id,
        ]);
    }

    public function test_musyrifah_can_move_student_to_another_group(): void
    {
        $anggota = KelompokBelajarAnggota::create([
            'user_id' => $this->mahasiswaDisetujui->id,
            'mata_pelajaran_id' => $this->mapelKitabKuning->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->musyrifah)
            ->put(route('musyrifah.manajemen_pembelajaran.anggota.pindah', $anggota->id), [
                'kelompok_belajar_id' => $this->kelompok2->id,
                'keterangan' => 'Pindah jadwal',
            ]);

        $response->assertRedirect(route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kelompok_belajar_anggotas', [
            'id' => $anggota->id,
            'kelompok_belajar_id' => $this->kelompok2->id,
        ]);
    }

    public function test_moving_student_to_same_group_returns_error(): void
    {
        $anggota = KelompokBelajarAnggota::create([
            'user_id' => $this->mahasiswaDisetujui->id,
            'mata_pelajaran_id' => $this->mapelKitabKuning->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->musyrifah)
            ->put(route('musyrifah.manajemen_pembelajaran.anggota.pindah', $anggota->id), [
                'kelompok_belajar_id' => $this->kelompok1->id,
            ]);

        $response->assertSessionHas('error');
    }

    public function test_musyrifah_can_remove_student_from_kelompok_belajar(): void
    {
        $anggota = KelompokBelajarAnggota::create([
            'user_id' => $this->mahasiswaDisetujui->id,
            'mata_pelajaran_id' => $this->mapelKitabKuning->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->musyrifah)
            ->delete(route('musyrifah.manajemen_pembelajaran.anggota.destroy', $anggota->id));

        $response->assertRedirect(route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok']));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('kelompok_belajar_anggotas', [
            'id' => $anggota->id,
        ]);
    }

    private function createPendaftaran(User $user, string $status = 'disetujui'): Pendaftaran
    {
        return Pendaftaran::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'nim' => $user->nim ?: '231700'.rand(100, 999),
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-01-01',
            'semester' => 1,
            'no_hp' => '081234567890',
            'no_wa' => '081234567890',
            'jenis_kelamin' => 'Perempuan',
            'asal_sekolah' => 'MAN 1 Padang',
            'status' => $status,
            'fakultas' => 'Sains dan Teknologi',
            'program_studi' => 'Sistem Informasi',
            'tahun_angkatan' => '2024',
            'kategori' => 'umum',
        ]);
    }
}
