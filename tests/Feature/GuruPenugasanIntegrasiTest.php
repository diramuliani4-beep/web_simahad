<?php

namespace Tests\Feature;

use App\Models\JadwalPembelajaran;
use App\Models\KelompokPengajar;
use App\Models\MataPelajaran;
use App\Models\User;
use App\Services\PembelajaranGuruService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Integrasi penugasan Musyrifah ke akun Guru: jadwal yang dibuat melalui
 * Manajemen Pembelajaran harus langsung terbaca pada menu Pembelajaran Guru
 * yang ditetapkan, dan tidak terbaca oleh Guru lain.
 */
class GuruPenugasanIntegrasiTest extends TestCase
{
    use RefreshDatabase;

    private const PERIODE = 'Semester Ganjil 2026/2027';

    public function test_penugasan_dibuat_musyrifah_tampil_pada_akun_guru_yang_ditetapkan(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah', 'Musyrifah Pembelajaran');
        $guru = $this->buatPengguna('guru', 'Ustaz Fauzan');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');

        $this->kirimJadwal($musyrifah, $guru, $mapel)->assertRedirect();

        $this->assertDatabaseHas('jadwal_pembelajarans', [
            'guru_id' => $guru->id,
            'guru_pengampu' => $guru->name,
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => 'Kelompok 1',
            'periode' => self::PERIODE,
        ]);

        $this->actingAs($guru)
            ->get(route('guru.pembelajaran'))
            ->assertOk()
            ->assertSee('Bahasa Arab Dasar')
            ->assertSee('Kelompok 1')
            ->assertSee(self::PERIODE)
            ->assertSee('Senin')
            ->assertSee('08:00')
            ->assertSee('Pertemuan 16')
            ->assertDontSee('Belum ada penugasan');
    }

    public function test_penugasan_tidak_tampil_pada_akun_guru_lain(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah');
        $guru = $this->buatPengguna('guru', 'Ustaz Fauzan');
        $guruLain = $this->buatPengguna('guru', 'Ustazah Nabila');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');

        $this->kirimJadwal($musyrifah, $guru, $mapel)->assertRedirect();

        $this->actingAs($guruLain)
            ->get(route('guru.pembelajaran'))
            ->assertOk()
            ->assertSee('Belum ada penugasan')
            ->assertDontSee('Bahasa Arab Dasar');

        // Guru lain juga tidak boleh membuka progres penugasan tersebut.
        $this->actingAs($guruLain)
            ->get(route('guru.pembelajaran', [
                'mata_pelajaran' => $mapel->id,
                'kelompok' => 'Kelompok 1',
                'periode' => self::PERIODE,
            ]))
            ->assertForbidden();
    }

    public function test_perubahan_penugasan_tersinkron_ke_akun_guru_baru(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah');
        $guruAwal = $this->buatPengguna('guru', 'Ustaz Fauzan');
        $guruBaru = $this->buatPengguna('guru', 'Ustazah Nabila');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');

        $this->kirimJadwal($musyrifah, $guruAwal, $mapel)->assertRedirect();
        $jadwal = JadwalPembelajaran::query()->firstOrFail();

        $this->actingAs($musyrifah)
            ->put(route('musyrifah.manajemen_pembelajaran.jadwal.update', $jadwal->id), [
                'nama_mata_pelajaran' => $mapel->nama,
                'kelompok' => 'Kelompok 3',
                'guru_id' => $guruBaru->id,
                'periode' => self::PERIODE,
                'hari' => 'Rabu',
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:30',
                'ruangan' => 'Ruang 3',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('jadwal_pembelajarans', [
            'id' => $jadwal->id,
            'guru_id' => $guruBaru->id,
            'guru_pengampu' => $guruBaru->name,
            'kelompok' => 'Kelompok 3',
            'hari' => 'Rabu',
        ]);

        $this->actingAs($guruAwal)
            ->get(route('guru.pembelajaran'))
            ->assertOk()
            ->assertSee('Belum ada penugasan');

        $this->actingAs($guruBaru)
            ->get(route('guru.pembelajaran'))
            ->assertOk()
            ->assertSee('Bahasa Arab Dasar')
            ->assertSee('Kelompok 3')
            ->assertSee('Rabu')
            ->assertDontSee('Belum ada penugasan');
    }

    public function test_penghapusan_penugasan_menghilangkan_jadwal_dari_akun_guru(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah');
        $guru = $this->buatPengguna('guru', 'Ustaz Fauzan');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');

        $this->kirimJadwal($musyrifah, $guru, $mapel)->assertRedirect();
        $jadwal = JadwalPembelajaran::query()->firstOrFail();

        $this->actingAs($musyrifah)
            ->delete(route('musyrifah.manajemen_pembelajaran.jadwal.destroy', $jadwal->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('jadwal_pembelajarans', ['id' => $jadwal->id]);

        $this->actingAs($guru)
            ->get(route('guru.pembelajaran'))
            ->assertOk()
            ->assertSee('Belum ada penugasan')
            ->assertDontSee('Bahasa Arab Dasar');
    }

    public function test_progres_16_pertemuan_terpisah_per_mata_pelajaran_dan_periode(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah');
        $guru = $this->buatPengguna('guru', 'Ustaz Fauzan');
        $mapelA = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');
        $mapelB = $this->buatMapel('Fiqih Ibadah', 'FI-01');

        $this->kirimJadwal($musyrifah, $guru, $mapelA, ['periode' => 'Semester Ganjil 2026/2027'])->assertRedirect();
        $this->kirimJadwal($musyrifah, $guru, $mapelA, ['periode' => 'Semester Genap 2026/2027'])->assertRedirect();
        $this->kirimJadwal($musyrifah, $guru, $mapelB, ['periode' => 'Semester Ganjil 2026/2027'])->assertRedirect();

        $this->actingAs($guru)
            ->post(route('guru.pembelajaran.mulai'), [
                'mata_pelajaran_id' => $mapelA->id,
                'kelompok' => 'Kelompok 1',
                'periode' => 'Semester Ganjil 2026/2027',
            ])
            ->assertRedirect();

        $layanan = app(PembelajaranGuruService::class);
        $jadwalA = $this->jadwal($mapelA, 'Semester Ganjil 2026/2027');
        $jadwalAPeriodeLain = $this->jadwal($mapelA, 'Semester Genap 2026/2027');
        $jadwalB = $this->jadwal($mapelB, 'Semester Ganjil 2026/2027');

        $progresA = $layanan->progres($jadwalA);
        $this->assertCount(16, $progresA['daftar']);
        $this->assertSame(1, $progresA['berlangsung']);
        $this->assertSame(0, $progresA['selesai']);

        // Periode lain pada mata pelajaran yang sama tetap mulai dari TM 1.
        $progresAPeriodeLain = $layanan->progres($jadwalAPeriodeLain);
        $this->assertNull($progresAPeriodeLain['berlangsung']);
        $this->assertSame(0, $progresAPeriodeLain['selesai']);
        $this->assertSame(1, $progresAPeriodeLain['berikutnya']);

        // Mata pelajaran lain juga memiliki progres 16 pertemuan sendiri.
        $progresB = $layanan->progres($jadwalB);
        $this->assertNull($progresB['berlangsung']);
        $this->assertSame(0, $progresB['selesai']);
        $this->assertSame(1, $progresB['berikutnya']);
    }

    public function test_jadwal_lama_tanpa_akun_guru_tetap_terbaca_lewat_nama_pengampu(): void
    {
        $guru = $this->buatPengguna('guru', 'Ustaz Fauzan');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');

        JadwalPembelajaran::query()->create([
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => 'Kelompok 2',
            'guru_pengampu' => $guru->name,
            'periode' => self::PERIODE,
            'hari' => 'Kamis',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
            'ruangan' => 'Ruang 2',
        ]);

        $this->actingAs($guru)
            ->get(route('guru.pembelajaran'))
            ->assertOk()
            ->assertSee('Bahasa Arab Dasar')
            ->assertSee('Kelompok 2')
            ->assertDontSee('Belum ada penugasan');
    }

    public function test_guru_pengampu_harus_akun_guru_yang_sah(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah');
        $mahasiswa = $this->buatPengguna('mahasiswa', 'Siti Aminah');
        $mapel = $this->buatMapel('Bahasa Arab Dasar', 'BA-01');

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.manajemen_pembelajaran.jadwal.store'), [
                'nama_mata_pelajaran' => $mapel->nama,
                'kelompok' => 'Kelompok 1',
                'guru_id' => $mahasiswa->id,
                'periode' => self::PERIODE,
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'ruangan' => 'Ruang 1',
            ])
            ->assertSessionHasErrors('guru_id');

        $this->assertDatabaseCount('jadwal_pembelajarans', 0);
    }

    public function test_halaman_musyrifah_menampilkan_pilihan_akun_guru(): void
    {
        $musyrifah = $this->buatPengguna('musyrifah');
        $guru = $this->buatPengguna('guru', 'Ustaz Fauzan');
        $this->buatMapel('Bahasa Arab Dasar', 'BA-01');
        KelompokPengajar::query()->create([
            'nama_pengajar' => 'Ustaz Fauzan',
            'program' => 'Bahasa Arab Dasar',
            'kelompok_teks' => '1',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.manajemen_pembelajaran'))
            ->assertOk()
            ->assertSee('Guru Pengampu')
            ->assertSee($guru->name);
    }

    private function buatPengguna(string $role, ?string $nama = null): User
    {
        return User::factory()->create([
            'name' => $nama ?? ucfirst($role).' SIMAHAD',
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
    private function kirimJadwal(
        User $musyrifah,
        User $guru,
        MataPelajaran $mapel,
        array $override = [],
    ): TestResponse {
        return $this->actingAs($musyrifah)
            ->post(route('musyrifah.manajemen_pembelajaran.jadwal.store'), array_merge([
                'nama_mata_pelajaran' => $mapel->nama,
                'kelompok' => 'Kelompok 1',
                'guru_id' => $guru->id,
                'periode' => self::PERIODE,
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'ruangan' => 'Ruang 1',
            ], $override));
    }

    private function jadwal(MataPelajaran $mapel, string $periode): JadwalPembelajaran
    {
        return JadwalPembelajaran::query()
            ->where('mata_pelajaran_id', $mapel->id)
            ->where('periode', $periode)
            ->orderBy('id')
            ->firstOrFail();
    }
}
