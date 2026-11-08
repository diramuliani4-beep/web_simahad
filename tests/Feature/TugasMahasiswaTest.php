<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TugasMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_hanya_melihat_tugas_terpublikasi_untuk_kelompoknya(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswaDenganKelompok();
        $guru = $this->guru();

        $this->tugas($guru, $mapel, 'Tugas Kelompok Satu', 'Kelompok 1');
        $this->tugas($guru, $mapel, 'Tugas Masih Draft', 'Kelompok 1', draft: true);
        $this->tugas($guru, $mapel, 'Tugas Kelompok Dua', 'Kelompok 2');

        $this->actingAs($mahasiswa)
            ->get(route('tugas.mhs.index'))
            ->assertOk()
            ->assertSee('Tugas Mahasiswa')
            ->assertSee('Tugas Kelompok Satu')
            ->assertDontSee('Tugas Masih Draft')
            ->assertDontSee('Tugas Kelompok Dua');
    }

    public function test_guru_tidak_dapat_membuka_halaman_tugas_mahasiswa(): void
    {
        $guru = $this->guru();

        $this->actingAs($guru)
            ->get(route('tugas.mhs.index'))
            ->assertForbidden();
    }

    public function test_mahasiswa_dapat_mengumpulkan_tugas_dan_guru_mendapat_notifikasi(): void
    {
        Storage::fake('local');

        [$mahasiswa, $mapel] = $this->mahasiswaDenganKelompok();
        $guru = $this->guru();
        $tugas = $this->tugas($guru, $mapel);

        $this->actingAs($mahasiswa)
            ->post(route('tugas.mhs.kumpulkan', $tugas), [
                'file' => UploadedFile::fake()->create('jawaban.pdf', 120, 'application/pdf'),
                'keterangan' => 'Sudah saya kerjakan seluruhnya.',
            ])
            ->assertRedirect(route('tugas.mhs.show', $tugas))
            ->assertSessionHasNoErrors();

        $pengumpulan = PengumpulanTugas::query()->first();

        $this->assertNotNull($pengumpulan);
        $this->assertSame($tugas->id, $pengumpulan->tugas_id);
        $this->assertSame($mahasiswa->id, $pengumpulan->user_id);
        $this->assertSame('jawaban.pdf', $pengumpulan->file_nama);
        $this->assertSame('Sudah saya kerjakan seluruhnya.', $pengumpulan->keterangan);
        $this->assertNotNull($pengumpulan->dikumpulkan_pada);
        Storage::disk('local')->assertExists($pengumpulan->file_path);

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $guru->id,
            'tipe' => Notifikasi::TIPE_PENGUMPULAN_BARU,
        ]);

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_PENGUMPULAN_BARU,
        ]);
    }

    public function test_pengumpulan_ulang_memperbarui_berkas_tanpa_duplikasi(): void
    {
        Storage::fake('local');

        [$mahasiswa, $mapel] = $this->mahasiswaDenganKelompok();
        $tugas = $this->tugas($this->guru(), $mapel);

        $this->actingAs($mahasiswa)
            ->post(route('tugas.mhs.kumpulkan', $tugas), [
                'file' => UploadedFile::fake()->create('pertama.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasNoErrors();

        $pengumpulan = PengumpulanTugas::query()->firstOrFail();
        $berkasPertama = $pengumpulan->file_path;

        $this->actingAs($mahasiswa)
            ->post(route('tugas.mhs.kumpulkan', $tugas), [
                'file' => UploadedFile::fake()->create('kedua.png', 100, 'image/png'),
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, PengumpulanTugas::query()->count());

        $pengumpulan->refresh();
        $this->assertSame('kedua.png', $pengumpulan->file_nama);
        $this->assertNotSame($berkasPertama, $pengumpulan->file_path);
        Storage::disk('local')->assertMissing($berkasPertama);
        Storage::disk('local')->assertExists($pengumpulan->file_path);
    }

    public function test_pengumpulan_setelah_batas_waktu_ditolak(): void
    {
        Storage::fake('local');

        [$mahasiswa, $mapel] = $this->mahasiswaDenganKelompok();
        $tugas = $this->tugas($this->guru(), $mapel, batas: now()->subDay());

        $this->actingAs($mahasiswa)
            ->post(route('tugas.mhs.kumpulkan', $tugas), [
                'file' => UploadedFile::fake()->create('telat.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, PengumpulanTugas::query()->count());
    }

    public function test_format_berkas_di_luar_ketentuan_ditolak(): void
    {
        Storage::fake('local');

        [$mahasiswa, $mapel] = $this->mahasiswaDenganKelompok();
        $tugas = $this->tugas($this->guru(), $mapel);

        $this->actingAs($mahasiswa)
            ->post(route('tugas.mhs.kumpulkan', $tugas), [
                'file' => UploadedFile::fake()->create('tugas.docx', 100, 'application/msword'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, PengumpulanTugas::query()->count());
    }

    public function test_nilai_tersembunyi_sampai_dipublikasikan_guru(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswaDenganKelompok();
        $tugas = $this->tugas($this->guru(), $mapel);

        $pengumpulan = PengumpulanTugas::factory()
            ->dinilai(78, 'Perbaiki tulisan huruf hijaiyah.')
            ->create([
                'tugas_id' => $tugas->id,
                'user_id' => $mahasiswa->id,
            ]);

        $this->actingAs($mahasiswa)
            ->get(route('tugas.mhs.show', $tugas))
            ->assertOk()
            ->assertSee('akan tampil setelah dipublikasikan')
            ->assertDontSee('nilai-angka">78', false)
            ->assertDontSee('Perbaiki tulisan huruf hijaiyah.');

        $pengumpulan->update(['nilai_dipublikasikan_pada' => now()]);

        $this->actingAs($mahasiswa)
            ->get(route('tugas.mhs.show', $tugas))
            ->assertOk()
            ->assertSee('nilai-angka">78', false)
            ->assertSee('Perbaiki tulisan huruf hijaiyah.')
            ->assertSee('Komentar Guru');
    }

    public function test_mahasiswa_tidak_dapat_membuka_tugas_kelompok_lain(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswaDenganKelompok();
        $tugas = $this->tugas($this->guru(), $mapel, 'Tugas Kelompok Lain', 'Kelompok 2');

        $this->actingAs($mahasiswa)
            ->get(route('tugas.mhs.show', $tugas))
            ->assertNotFound();
    }

    /**
     * Mahasiswa beserta kelompok pembelajaran yang tercatat pada absensi.
     *
     * @return array{0: User, 1: MataPelajaran}
     */
    private function mahasiswaDenganKelompok(string $kelompok = 'Kelompok 1'): array
    {
        $mapel = MataPelajaran::query()->create([
            'nama' => 'Bahasa Arab Dasar',
            'kode' => 'BA-01',
            'kategori' => 'Bahasa',
        ]);

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

        return [$mahasiswa, $mapel];
    }

    private function guru(): User
    {
        return User::factory()->create([
            'name' => 'Ustaz Fauzan',
            'role' => 'guru',
            'status' => 'aktif',
        ]);
    }

    private function tugas(
        User $guru,
        MataPelajaran $mapel,
        string $judul = 'Tugas Bahasa Arab',
        string $kelompok = 'Kelompok 1',
        bool $draft = false,
        ?CarbonInterface $batas = null,
    ): Tugas {
        $factory = $draft
            ? Tugas::factory()->draft()
            : Tugas::factory()->dipublikasikan();

        return $factory->create([
            'user_id' => $guru->id,
            'nama_guru' => $guru->name,
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => $kelompok,
            'judul' => $judul,
            'batas_pengumpulan' => $batas ?? now()->addDays(3),
        ]);
    }
}
