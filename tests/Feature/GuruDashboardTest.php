<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\MataPelajaran;
use App\Models\PengumpulanTugas;
use App\Models\PertemuanPembelajaran;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dashboard Guru: sapaan akun, tanggal, ringkasan penugasan, jadwal hari ini,
 * progres 1-16 per mata pelajaran/periode, tugas yang perlu diperiksa, dan
 * ringkasan absensi, semuanya dibatasi pada penugasan Guru yang login.
 */
class GuruDashboardTest extends TestCase
{
    use RefreshDatabase;

    private const PERIODE = 'Semester Ganjil 2026/2027';

    public function test_dashboard_menampilkan_sapaan_tanggal_dan_ringkasan_penugasan(): void
    {
        $guru = $this->buatGuru('Ustaz Fauzan');
        $mapelA = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');
        $mapelB = $this->buatMapel('Fiqih Ibadah', 'FI-01');

        $jadwalA = $this->buatJadwal($guru, $mapelA, [
            'kelompok' => 'Kelompok 1',
            'hari' => $this->hariIni(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
        ]);
        $this->buatJadwal($guru, $mapelB, [
            'kelompok' => 'Kelompok 2',
            'hari' => $this->hariLain(),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:30',
        ]);

        // Dua pertemuan selesai pada mapel A.
        $this->buatPertemuan($guru, $jadwalA, 1, PertemuanPembelajaran::STATUS_SELESAI);
        $this->buatPertemuan($guru, $jadwalA, 2, PertemuanPembelajaran::STATUS_SELESAI);

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('Assalamu’alaikum, Ustaz Fauzan')
            ->assertSee(now()->locale('id')->translatedFormat('l, d F Y'))
            ->assertSee('Mata Pelajaran')
            ->assertSee('Pertemuan Selesai')
            ->assertSee('dari 32 pertemuan (1–16)')
            ->assertSee('Bahasa Arab Dasar')
            ->assertSee('Fiqih Ibadah')
            ->assertSee('Kelompok 1')
            ->assertSee('Kelompok 2')
            ->assertSee('2/16')
            ->assertSee('Jadwal berikutnya')
            ->assertSee('Siapkan pertemuan berikutnya');
    }

    public function test_jadwal_hari_ini_menampilkan_mapel_kelompok_dan_ruangan(): void
    {
        $guru = $this->buatGuru('Ustaz Fauzan');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');

        $this->buatJadwal($guru, $mapel, [
            'kelompok' => 'Kelompok 3',
            'hari' => $this->hariIni(),
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
            'ruangan' => 'Ruang 3',
        ]);

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('Jadwal Mengajar Hari Ini')
            ->assertSee('Bahasa Arab Dasar')
            ->assertSee('Kelompok 3')
            ->assertSee('Ruang 3')
            ->assertSee('07:30')
            ->assertSee('09:00')
            ->assertDontSee('Tidak ada jadwal mengajar hari ini.');
    }

    public function test_progres_16_pertemuan_dihitung_terpisah_per_mapel_dan_periode(): void
    {
        $guru = $this->buatGuru('Ustaz Fauzan');
        $mapelA = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');
        $mapelB = $this->buatMapel('Fiqih Ibadah', 'FI-01');

        $jadwalAGanjil = $this->buatJadwal($guru, $mapelA, ['periode' => 'Semester Ganjil 2026/2027']);
        $this->buatJadwal($guru, $mapelA, ['periode' => 'Semester Genap 2026/2027']);
        $jadwalB = $this->buatJadwal($guru, $mapelB, ['periode' => 'Semester Ganjil 2026/2027']);

        $this->buatPertemuan($guru, $jadwalAGanjil, 1, PertemuanPembelajaran::STATUS_SELESAI);
        $this->buatPertemuan($guru, $jadwalAGanjil, 2, PertemuanPembelajaran::STATUS_SELESAI);
        $this->buatPertemuan($guru, $jadwalB, 1, PertemuanPembelajaran::STATUS_SELESAI);

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('Progres Pertemuan (1–16)')
            ->assertSee('Semester Ganjil 2026/2027')
            ->assertSee('Semester Genap 2026/2027')
            // Ganjil mapel A = 2 selesai, Genap mapel A = 0, mapel B = 1.
            ->assertSeeInOrder(['2/16', '0/16', '1/16']);
    }

    public function test_tugas_yang_perlu_diperiksa_hanya_milik_guru_yang_login(): void
    {
        $guru = $this->buatGuru('Ustaz Fauzan');
        $guruLain = $this->buatGuru('Ustazah Nabila');
        $mahasiswaA = $this->buatPengguna('mahasiswa', 'Siti Aminah');
        $mahasiswaB = $this->buatPengguna('mahasiswa', 'Nur Aisyah');

        $tugas = Tugas::factory()->dipublikasikan()->create([
            'user_id' => $guru->id,
            'judul' => 'Tugas Mufradat Pekan Ini',
            'kelompok' => 'Kelompok 1',
        ]);

        PengumpulanTugas::factory()->create([
            'tugas_id' => $tugas->id,
            'user_id' => $mahasiswaA->id,
            'nilai' => null,
        ]);
        PengumpulanTugas::factory()->create([
            'tugas_id' => $tugas->id,
            'user_id' => $mahasiswaB->id,
            'nilai' => null,
        ]);
        PengumpulanTugas::factory()->dinilai(90)->create([
            'tugas_id' => $tugas->id,
            'user_id' => $this->buatPengguna('mahasiswa', 'Fatimah')->id,
        ]);

        $tugasLain = Tugas::factory()->dipublikasikan()->create([
            'user_id' => $guruLain->id,
            'judul' => 'Tugas Rahasia Guru Lain',
        ]);
        PengumpulanTugas::factory()->create([
            'tugas_id' => $tugasLain->id,
            'user_id' => $mahasiswaA->id,
            'nilai' => null,
        ]);

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('Perlu Diperiksa')
            ->assertSee('Tugas Mufradat Pekan Ini')
            ->assertSee('2 belum dinilai')
            ->assertSee(route('guru.pengumpulan.index', $tugas), false)
            ->assertDontSee('Tugas Rahasia Guru Lain');
    }

    public function test_ringkasan_absensi_hanya_menghitung_jadwal_milik_guru(): void
    {
        $guru = $this->buatGuru('Ustaz Fauzan');
        $guruLain = $this->buatGuru('Ustazah Nabila');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');

        $jadwal = $this->buatJadwal($guru, $mapel);
        $jadwalLain = $this->buatJadwal($guruLain, $mapel);

        $mahasiswa = $this->buatPengguna('mahasiswa', 'Siti Aminah');

        // Guru ini: 2 hadir + 1 izin = 66,7% hadir.
        $this->buatAbsensi($jadwal, $mahasiswa, 'hadir');
        $this->buatAbsensi($jadwal, $this->buatPengguna('mahasiswa', 'Nur Aisyah'), 'hadir');
        $this->buatAbsensi($jadwal, $this->buatPengguna('mahasiswa', 'Fatimah'), 'izin');

        // Guru lain: tidak boleh ikut terhitung.
        $this->buatAbsensi($jadwalLain, $mahasiswa, 'hadir');
        $this->buatAbsensi($jadwalLain, $this->buatPengguna('mahasiswa', 'Khadijah'), 'alpa');

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan Absensi')
            ->assertSee('3 catatan absensi')
            ->assertSee('66.7%')
            ->assertDontSee('5 catatan absensi');
    }

    public function test_ringkasan_absensi_membaca_absensi_lama_tanpa_jadwal_id(): void
    {
        $guru = $this->buatGuru('Ustaz Fauzan');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');
        $mapelLain = $this->buatMapel('Fiqih Ibadah', 'FI-01');

        $this->buatJadwal($guru, $mapel, ['kelompok' => 'Kelompok 1']);

        // Absensi lama (seeder) tidak memiliki jadwal_id sehingga dicocokkan
        // lewat pasangan mata pelajaran dan kelompok penugasan Guru.
        AbsensiPembelajaran::query()->create([
            'user_id' => $this->buatPengguna('mahasiswa', 'Siti Aminah')->id,
            'mata_pelajaran_id' => $mapel->id,
            'jadwal_id' => null,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => now()->toDateString(),
            'status' => 'hadir',
        ]);

        AbsensiPembelajaran::query()->create([
            'user_id' => $this->buatPengguna('mahasiswa', 'Nur Aisyah')->id,
            'mata_pelajaran_id' => $mapelLain->id,
            'jadwal_id' => null,
            'kelompok' => 'Kelompok 9',
            'pertemuan_ke' => 1,
            'tanggal' => now()->toDateString(),
            'status' => 'alpa',
        ]);

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('1 catatan absensi')
            ->assertSee('<strong>100%</strong> hadir', false)
            ->assertDontSee('2 catatan absensi');
    }

    public function test_dashboard_tanpa_penugasan_menampilkan_kondisi_kosong_informatif(): void
    {
        $guru = $this->buatGuru('Ustaz Fauzan');

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('Assalamu’alaikum, Ustaz Fauzan')
            ->assertSee('Belum ada jadwal berikutnya')
            ->assertSee('Tidak ada jadwal mengajar hari ini.')
            ->assertSee('Belum ada kelompok yang ditugaskan.')
            ->assertSee('Tidak ada pengumpulan yang menunggu.')
            ->assertSee('Belum ada absensi tercatat.')
            ->assertSee('Buka Pembelajaran');
    }

    public function test_guru_lain_tidak_melihat_penugasan_guru_ini(): void
    {
        $guru = $this->buatGuru('Ustaz Fauzan');
        $guruLain = $this->buatGuru('Ustazah Nabila');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');

        $jadwal = $this->buatJadwal($guru, $mapel, ['kelompok' => 'Kelompok 1']);
        $this->buatPertemuan($guru, $jadwal, 1, PertemuanPembelajaran::STATUS_SELESAI);

        $this->actingAs($guruLain)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertDontSee('Bahasa Arab Dasar')
            ->assertDontSee('Kelompok 1')
            ->assertSee('Belum ada kelompok yang ditugaskan.');
    }

    public function test_aksi_cepat_dashboard_mengarah_ke_halaman_guru_yang_benar(): void
    {
        $guru = $this->buatGuru('Ustaz Fauzan');

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee(route('guru.pembelajaran'), false)
            ->assertSee(route('guru.absensi'), false)
            ->assertSee(route('guru.penilaian'), false)
            ->assertSee(route('guru.tugas.index'), false)
            ->assertSee(route('guru.tugas.create'), false);
    }

    private function buatGuru(string $nama): User
    {
        return $this->buatPengguna('guru', $nama);
    }

    private function buatPengguna(string $role, string $nama): User
    {
        return User::factory()->create([
            'name' => $nama,
            'role' => $role,
            'status' => 'aktif',
        ]);
    }

    private function buatMapel(string $nama, string $kode): MataPelajaran
    {
        return MataPelajaran::query()->create([
            'nama' => $nama,
            'kode' => $kode,
            'kategori' => 'Keislaman',
            'target_pertemuan' => 16,
        ]);
    }

    /**
     * @param  array<string, mixed>  $override
     */
    private function buatJadwal(User $guru, MataPelajaran $mapel, array $override = []): JadwalPembelajaran
    {
        return JadwalPembelajaran::query()->create(array_merge([
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => 'Kelompok 1',
            'guru_id' => $guru->id,
            'guru_pengampu' => $guru->name,
            'periode' => self::PERIODE,
            'hari' => $this->hariIni(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'ruangan' => 'Ruang 1',
        ], $override));
    }

    private function buatPertemuan(
        User $guru,
        JadwalPembelajaran $jadwal,
        int $nomor,
        string $status,
    ): PertemuanPembelajaran {
        return PertemuanPembelajaran::query()->create([
            'jadwal_id' => $jadwal->id,
            'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
            'guru_id' => $guru->id,
            'kelompok' => $jadwal->kelompok,
            'periode' => $jadwal->periode,
            'pertemuan_ke' => $nomor,
            'tanggal' => now()->toDateString(),
            'status' => $status,
            'dimulai_pada' => now(),
            'selesai_pada' => $status === PertemuanPembelajaran::STATUS_SELESAI ? now() : null,
        ]);
    }

    private function buatAbsensi(JadwalPembelajaran $jadwal, User $mahasiswa, string $status): AbsensiPembelajaran
    {
        return AbsensiPembelajaran::query()->create([
            'user_id' => $mahasiswa->id,
            'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
            'jadwal_id' => $jadwal->id,
            'kelompok' => $jadwal->kelompok,
            'pertemuan_ke' => 1,
            'tanggal' => now()->toDateString(),
            'status' => $status,
        ]);
    }

    private function hariIni(): string
    {
        return JadwalPembelajaran::HARI[now()->dayOfWeekIso];
    }

    private function hariLain(): string
    {
        $besok = now()->dayOfWeekIso === 7 ? 1 : now()->dayOfWeekIso + 1;

        return JadwalPembelajaran::HARI[$besok];
    }
}
