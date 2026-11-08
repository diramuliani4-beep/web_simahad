<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruTugasTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_dapat_membuka_halaman_tugas(): void
    {
        [$guru] = $this->guruDenganJadwal();

        $this->actingAs($guru)
            ->get(route('guru.tugas.index'))
            ->assertOk()
            ->assertSee('Tugas Pembelajaran');
    }

    public function test_mahasiswa_tidak_dapat_membuka_halaman_tugas_guru(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'status' => 'aktif']);

        $this->actingAs($mahasiswa)
            ->get(route('guru.tugas.index'))
            ->assertForbidden();
    }

    public function test_guru_dapat_membuat_tugas_terpublikasi_dan_mengirim_notifikasi(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);

        $response = $this->actingAs($guru)->post(route('guru.tugas.store'), [
            'judul' => 'Tugas Mufradat Pekan Ini',
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => 'Kelompok 1',
            'deskripsi' => 'Hafalkan mufradat berikut lalu tuliskan pada lembar jawaban.',
            'tanggal_mulai' => now()->toDateString(),
            'batas_pengumpulan' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'status' => Tugas::STATUS_DIPUBLIKASIKAN,
        ]);

        $response->assertRedirect(route('guru.tugas.index'));

        $tugas = Tugas::query()->first();

        $this->assertNotNull($tugas);
        $this->assertSame(Tugas::STATUS_DIPUBLIKASIKAN, $tugas->status);
        $this->assertNotNull($tugas->dipublikasikan_pada);
        $this->assertSame($guru->id, $tugas->user_id);

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_TUGAS_BARU,
        ]);
    }

    public function test_guru_tidak_dapat_memberi_tugas_di_luar_penugasannya(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();

        $this->actingAs($guru)->post(route('guru.tugas.store'), [
            'judul' => 'Tugas Kelompok Lain',
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => 'Kelompok 9',
            'deskripsi' => 'Tugas ini tidak boleh dibuat karena di luar penugasan guru.',
            'tanggal_mulai' => now()->toDateString(),
            'batas_pengumpulan' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'status' => Tugas::STATUS_DIPUBLIKASIKAN,
        ])->assertForbidden();

        $this->assertDatabaseCount('tugas', 0);
    }

    public function test_guru_tidak_dapat_mengakses_tugas_guru_lain(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $guruLain = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);

        $tugasGuruLain = Tugas::factory()->dipublikasikan()->create([
            'user_id' => $guruLain->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => 'Kelompok 2',
            'judul' => 'Tugas Milik Guru Lain',
        ]);

        $this->actingAs($guru)
            ->get(route('guru.tugas.edit', $tugasGuruLain))
            ->assertForbidden();

        $this->actingAs($guru)
            ->get(route('guru.pengumpulan.index', $tugasGuruLain))
            ->assertForbidden();
    }

    public function test_daftar_pengumpulan_menampilkan_mahasiswa_kelompok_tujuan(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);
        $tugas = $this->tugasDipublikasikan($guru, $mapel);

        $this->actingAs($guru)
            ->get(route('guru.pengumpulan.index', $tugas))
            ->assertOk()
            ->assertSee($mahasiswa->name)
            ->assertSee($tugas->judul);
    }

    public function test_nilai_di_luar_rentang_nol_sampai_seratus_ditolak(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);
        $tugas = $this->tugasDipublikasikan($guru, $mapel);
        $pengumpulan = $this->pengumpulan($tugas, $mahasiswa);

        $this->actingAs($guru)->post(route('guru.pengumpulan.nilai', [
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan,
        ]), ['nilai' => 101])->assertSessionHasErrors('nilai');

        $this->actingAs($guru)->post(route('guru.pengumpulan.nilai', [
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan,
        ]), ['nilai' => -5])->assertSessionHasErrors('nilai');

        $this->assertNull($pengumpulan->fresh()->nilai);
    }

    public function test_guru_dapat_menilai_dan_perubahan_nilai_menyimpan_riwayat(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);
        $tugas = $this->tugasDipublikasikan($guru, $mapel);
        $pengumpulan = $this->pengumpulan($tugas, $mahasiswa);

        $this->actingAs($guru)->post(route('guru.pengumpulan.nilai', [
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan,
        ]), ['nilai' => 80, 'komentar' => 'Tulisan sudah rapi.'])->assertSessionHasNoErrors();

        $pengumpulan->refresh();
        $this->assertSame(80, $pengumpulan->nilai);
        $this->assertSame('Tulisan sudah rapi.', $pengumpulan->komentar_guru);
        $this->assertNotNull($pengumpulan->dinilai_pada);
        $this->assertDatabaseCount('riwayat_nilai_tugas', 1);

        $this->actingAs($guru)->post(route('guru.pengumpulan.nilai', [
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan,
        ]), ['nilai' => 90, 'komentar' => 'Perbaikan sudah bagus.'])->assertSessionHasNoErrors();

        $this->assertSame(90, $pengumpulan->fresh()->nilai);
        $this->assertDatabaseCount('riwayat_nilai_tugas', 2);
        $this->assertDatabaseHas('riwayat_nilai_tugas', [
            'pengumpulan_tugas_id' => $pengumpulan->id,
            'nilai_lama' => 80,
            'nilai_baru' => 90,
        ]);
    }

    public function test_publikasi_nilai_mengirim_notifikasi_kepada_mahasiswa(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);
        $tugas = $this->tugasDipublikasikan($guru, $mapel);
        $pengumpulan = $this->pengumpulan($tugas, $mahasiswa);

        $this->actingAs($guru)->post(route('guru.pengumpulan.nilai', [
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan,
        ]), ['nilai' => 88]);

        $this->actingAs($guru)->post(route('guru.pengumpulan.publikasi', [
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan,
        ]))->assertSessionHasNoErrors();

        $this->assertNotNull($pengumpulan->fresh()->nilai_dipublikasikan_pada);

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_NILAI_DIPUBLIKASIKAN,
        ]);
    }

    /**
     * Menyiapkan Guru beserta jadwal/penugasan yang sah.
     *
     * @return array{0: User, 1: MataPelajaran}
     */
    private function guruDenganJadwal(string $namaGuru = 'Ustaz Fauzan'): array
    {
        $guru = User::factory()->create([
            'name' => $namaGuru,
            'role' => 'guru',
            'status' => 'aktif',
        ]);

        $mapel = MataPelajaran::query()->create([
            'nama' => 'Bahasa Arab Dasar',
            'kode' => 'BA-01',
            'kategori' => 'Bahasa',
        ]);

        JadwalPembelajaran::query()->create([
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => 'Kelompok 1',
            'guru_pengampu' => $guru->name,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'ruangan' => 'Ruang 1',
        ]);

        return [$guru, $mapel];
    }

    private function mahasiswaKelompok(MataPelajaran $mapel, string $kelompok = 'Kelompok 1'): User
    {
        $mahasiswa = User::factory()->create([
            'name' => 'Siti Aminah',
            'nim' => '2317020111',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);

        AbsensiPembelajaran::query()->create([
            'user_id' => $mahasiswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => $kelompok,
            'pertemuan_ke' => 1,
            'tanggal' => now()->toDateString(),
            'status' => 'hadir',
        ]);

        return $mahasiswa;
    }

    private function tugasDipublikasikan(User $guru, MataPelajaran $mapel): Tugas
    {
        return Tugas::factory()->dipublikasikan()->create([
            'user_id' => $guru->id,
            'nama_guru' => $guru->name,
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => 'Kelompok 1',
            'judul' => 'Tugas Bahasa Arab',
            'batas_pengumpulan' => now()->addDays(3),
        ]);
    }

    private function pengumpulan(Tugas $tugas, User $mahasiswa): PengumpulanTugas
    {
        return PengumpulanTugas::factory()->create([
            'tugas_id' => $tugas->id,
            'user_id' => $mahasiswa->id,
        ]);
    }
}
