<?php

namespace Tests\Feature;

use App\Models\KelompokBelajar;
use App\Models\MataPelajaran;
use App\Models\Pendaftaran;
use App\Models\PenempatanKelompokBelajar;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PenempatanKelompokBelajarMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_backfill_memilih_penempatan_aktif_terbaru_dan_mempertahankan_data_legacy(): void
    {
        Schema::dropIfExists('penempatan_kelompok_belajars');

        $kelompok1 = $this->buatKelompok(1);
        $kelompok2 = $this->buatKelompok(2);
        $mapel1 = $this->buatMataPelajaran('MAPEL-MIG-1');
        $mapel2 = $this->buatMataPelajaran('MAPEL-MIG-2');

        $mahasiswaTerbaru = $this->buatMahasiswa('Mahasiswa Terbaru', 'MIG-001');
        $mahasiswaSeri = $this->buatMahasiswa('Mahasiswa Seri', 'MIG-002');
        $mahasiswaAktifTerakhir = $this->buatMahasiswa('Mahasiswa Aktif Terakhir', 'MIG-003');
        $mahasiswaDitolak = $this->buatMahasiswa('Mahasiswa Ditolak', 'MIG-004');
        $guru = $this->buatMahasiswa('Guru Berpendaftaran', 'MIG-005', 'guru');

        $this->buatPendaftaran($mahasiswaTerbaru, 'disetujui');
        $this->buatPendaftaran($mahasiswaSeri, 'disetujui');
        $this->buatPendaftaran($mahasiswaAktifTerakhir, 'disetujui');
        $this->buatPendaftaran($mahasiswaDitolak, 'ditolak');
        $this->buatPendaftaran($guru, 'disetujui');

        $this->buatKeanggotaanLegacy($mahasiswaTerbaru, $kelompok1, $mapel1, 'aktif', '2026-11-01 10:00:00', 'Catatan lama');
        $this->buatKeanggotaanLegacy($mahasiswaTerbaru, $kelompok2, $mapel2, 'aktif', '2026-11-02 10:00:00', 'Catatan terbaru');
        $this->buatKeanggotaanLegacy($mahasiswaSeri, $kelompok1, $mapel1, 'aktif', '2026-11-03 10:00:00');
        $this->buatKeanggotaanLegacy($mahasiswaSeri, $kelompok2, $mapel2, 'aktif', '2026-11-03 10:00:00');
        $this->buatKeanggotaanLegacy($mahasiswaAktifTerakhir, $kelompok1, $mapel1, 'aktif', '2026-11-04 10:00:00');
        $this->buatKeanggotaanLegacy($mahasiswaAktifTerakhir, $kelompok2, $mapel2, 'tidak_aktif', '2026-11-05 10:00:00');
        $this->buatKeanggotaanLegacy($mahasiswaDitolak, $kelompok1, $mapel1, 'aktif', '2026-11-06 10:00:00');
        $this->buatKeanggotaanLegacy($guru, $kelompok1, $mapel1, 'aktif', '2026-11-07 10:00:00');

        $legacySebelum = $this->snapshotLegacy();

        $this->jalankanMigrasi();

        $this->assertDatabaseCount('penempatan_kelompok_belajars', 3);
        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'user_id' => $mahasiswaTerbaru->id,
            'kelompok_belajar_id' => $kelompok2->id,
            'status' => 'aktif',
            'ditugaskan_pada' => '2026-11-02 10:00:00',
        ]);
        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'user_id' => $mahasiswaSeri->id,
            'kelompok_belajar_id' => $kelompok2->id,
            'status' => 'aktif',
            'ditugaskan_pada' => '2026-11-03 10:00:00',
        ]);
        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'user_id' => $mahasiswaAktifTerakhir->id,
            'kelompok_belajar_id' => $kelompok1->id,
            'status' => 'aktif',
        ]);
        $this->assertDatabaseMissing('penempatan_kelompok_belajars', [
            'user_id' => $mahasiswaDitolak->id,
        ]);
        $this->assertDatabaseMissing('penempatan_kelompok_belajars', [
            'user_id' => $guru->id,
        ]);
        $this->assertSame($legacySebelum, $this->snapshotLegacy());
        $this->assertStringContainsString(
            'Catatan terbaru',
            DB::table('penempatan_kelompok_belajars')
                ->where('user_id', $mahasiswaTerbaru->id)
                ->value('keterangan')
        );
    }

    public function test_database_menolak_penempatan_kedua_untuk_mahasiswa_yang_sama(): void
    {
        $mahasiswa = $this->buatMahasiswa('Mahasiswa Unik', 'MIG-006');
        $kelompok1 = $this->buatKelompok(1);
        $kelompok2 = $this->buatKelompok(2);

        PenempatanKelompokBelajar::query()->create([
            'user_id' => $mahasiswa->id,
            'kelompok_belajar_id' => $kelompok1->id,
            'status' => 'aktif',
        ]);

        $this->expectException(QueryException::class);

        PenempatanKelompokBelajar::query()->create([
            'user_id' => $mahasiswa->id,
            'kelompok_belajar_id' => $kelompok2->id,
            'status' => 'aktif',
        ]);
    }

    public function test_rollback_mempertahankan_tabel_dan_data_penempatan_global(): void
    {
        $mahasiswa = $this->buatMahasiswa('Mahasiswa Rollback', 'MIG-007');
        $kelompok = $this->buatKelompok(1);
        $penempatan = PenempatanKelompokBelajar::query()->create([
            'user_id' => $mahasiswa->id,
            'kelompok_belajar_id' => $kelompok->id,
            'status' => 'aktif',
            'ditugaskan_pada' => now(),
        ]);

        $this->muatMigrasi()->down();

        $this->assertModelExists($penempatan);
        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'user_id' => $mahasiswa->id,
            'kelompok_belajar_id' => $kelompok->id,
            'status' => 'aktif',
        ]);
    }

    private function jalankanMigrasi(): void
    {
        Schema::dropIfExists('penempatan_kelompok_belajars');

        $this->muatMigrasi()->up();
        $this->muatMigrasiUnique()->up();
    }

    private function muatMigrasi(): object
    {
        return require database_path(
            'migrations/2026_11_26_181143_create_penempatan_kelompok_belajars_table.php'
        );
    }

    private function muatMigrasiUnique(): object
    {
        return require database_path(
            'migrations/2026_11_26_192113_add_unique_user_id_to_penempatan_kelompok_belajars_table.php'
        );
    }

    private function buatMahasiswa(string $nama, string $nim, string $role = 'mahasiswa'): User
    {
        return User::factory()->create([
            'name' => $nama,
            'nim' => $role === 'mahasiswa' ? $nim : null,
            'role' => $role,
            'status' => 'aktif',
        ]);
    }

    private function buatPendaftaran(User $user, string $status): Pendaftaran
    {
        return Pendaftaran::query()->create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'nim' => $user->nim ?: 'NIM-'.$user->id,
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-01-01',
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '081234567890',
            'program_studi' => 'Sistem Informasi',
            'fakultas' => 'Sains dan Teknologi',
            'semester' => 1,
            'status' => $status,
            'tahun_angkatan' => '2024',
        ]);
    }

    private function buatKelompok(int $nomor): KelompokBelajar
    {
        return KelompokBelajar::query()->create([
            'nama_kelompok' => 'Kelompok '.$nomor,
            'nomor_kelompok' => $nomor,
            'keterangan' => 'Kelompok migrasi '.$nomor,
        ]);
    }

    private function buatMataPelajaran(string $kode): MataPelajaran
    {
        return MataPelajaran::query()->create([
            'nama' => 'Mata Pelajaran '.$kode,
            'kode' => $kode,
            'kategori' => 'Wajib',
        ]);
    }

    private function buatKeanggotaanLegacy(
        User $mahasiswa,
        KelompokBelajar $kelompok,
        MataPelajaran $mapel,
        string $status,
        string $waktu,
        ?string $keterangan = null,
    ): void {
        DB::table('kelompok_belajar_anggotas')->insert([
            'kelompok_belajar_id' => $kelompok->id,
            'user_id' => $mahasiswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'status' => $status,
            'keterangan' => $keterangan,
            'created_at' => $waktu,
            'updated_at' => $waktu,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function snapshotLegacy(): array
    {
        return DB::table('kelompok_belajar_anggotas')
            ->orderBy('id')
            ->get()
            ->map(fn ($row): array => (array) $row)
            ->all();
    }
}
