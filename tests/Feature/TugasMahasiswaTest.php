<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\KelompokBelajar;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\PenempatanKelompokBelajar;
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

    public function test_absensi_sendirian_tidak_memberikan_akses_tugas(): void
    {
        $mahasiswa = User::factory()->create([
            'name' => 'Siti Aminah',
            'nim' => '2317020111',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);
        $mapel = $this->mataPelajaran();
        $tugas = $this->tugas($this->guru(), $mapel);

        $this->buatPendaftaranDisetujui($mahasiswa);
        AbsensiPembelajaran::query()->create([
            'user_id' => $mahasiswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => now()->toDateString(),
            'status' => 'hadir',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('tugas.mhs.index'))
            ->assertOk()
            ->assertDontSee($tugas->judul);

        $this->actingAs($mahasiswa)
            ->get(route('tugas.mhs.show', $tugas))
            ->assertNotFound();
    }

    public function test_perpindahan_kelompok_global_mengganti_tugas_dan_akses_detail_semua_mapel(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswaDenganKelompok();
        $mapelLain = $this->mataPelajaran('Fiqih Ibadah', 'FI-01');
        $guru = $this->guru();

        $tugasLamaPertama = $this->tugas($guru, $mapel, 'Tugas Lama Bahasa Arab', 'Kelompok 1');
        $tugasBaruPertama = $this->tugas($guru, $mapel, 'Tugas Baru Bahasa Arab', 'Kelompok 2');
        $tugasLamaKedua = $this->tugas($guru, $mapelLain, 'Tugas Lama Fiqih', 'Kelompok 1');
        $tugasBaruKedua = $this->tugas($guru, $mapelLain, 'Tugas Baru Fiqih', 'Kelompok 2');

        $penempatan = PenempatanKelompokBelajar::query()
            ->where('user_id', $mahasiswa->id)
            ->firstOrFail();
        $penempatan->update(['kelompok_belajar_id' => $this->kelompok(2)->id]);
        $mahasiswa->unsetRelation('penempatanKelompokBelajarAktif');

        $this->actingAs($mahasiswa)
            ->get(route('tugas.mhs.index'))
            ->assertOk()
            ->assertSee($tugasBaruPertama->judul)
            ->assertSee($tugasBaruKedua->judul)
            ->assertDontSee($tugasLamaPertama->judul)
            ->assertDontSee($tugasLamaKedua->judul);

        $this->actingAs($mahasiswa)
            ->get(route('tugas.mhs.show', $tugasLamaPertama))
            ->assertNotFound();

        $this->actingAs($mahasiswa)
            ->get(route('tugas.mhs.show', $tugasBaruKedua))
            ->assertOk()
            ->assertSee($tugasBaruKedua->judul);
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
     * Mahasiswa dengan pendaftaran disetujui dan satu penempatan kelompok global.
     *
     * @return array{0: User, 1: MataPelajaran}
     */
    private function mahasiswaDenganKelompok(int $nomorKelompok = 1): array
    {
        $mapel = $this->mataPelajaran();

        $mahasiswa = User::factory()->create([
            'name' => 'Siti Aminah',
            'nim' => '2317020111',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);

        $this->buatPendaftaranDisetujui($mahasiswa);
        $this->tempatkan($mahasiswa, $this->kelompok($nomorKelompok));

        return [$mahasiswa, $mapel];
    }

    private function mataPelajaran(string $nama = 'Bahasa Arab Dasar', string $kode = 'BA-01'): MataPelajaran
    {
        return MataPelajaran::query()->create([
            'nama' => $nama,
            'kode' => $kode,
            'kategori' => 'Bahasa',
        ]);
    }

    private function kelompok(int $nomor): KelompokBelajar
    {
        return KelompokBelajar::query()->firstOrCreate(
            ['nomor_kelompok' => $nomor],
            [
                'nama_kelompok' => 'Kelompok '.$nomor,
                'keterangan' => 'Kelompok Belajar '.$nomor,
            ]
        );
    }

    private function buatPendaftaranDisetujui(User $mahasiswa): Pendaftaran
    {
        return Pendaftaran::query()->create([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->name,
            'nim' => $mahasiswa->nim,
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-01-01',
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '081234567890',
            'program_studi' => 'Sistem Informasi',
            'fakultas' => 'Sains dan Teknologi',
            'semester' => 1,
            'status' => 'disetujui',
            'tahun_angkatan' => '2024',
        ]);
    }

    private function tempatkan(User $mahasiswa, KelompokBelajar $kelompok): PenempatanKelompokBelajar
    {
        return PenempatanKelompokBelajar::query()->create([
            'user_id' => $mahasiswa->id,
            'kelompok_belajar_id' => $kelompok->id,
            'status' => 'aktif',
            'ditugaskan_pada' => now(),
            'dicabut_pada' => null,
        ]);
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
