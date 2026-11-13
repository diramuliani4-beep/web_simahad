<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\ActivityLog;
use App\Models\JadwalPembelajaran;
use App\Models\KelompokPengajar;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\PertemuanPembelajaran;
use App\Models\Tugas;
use App\Models\User;
use App\Support\MenuPeran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Modul Guru: pembelajaran 16 pertemuan, absensi, materi, tugas, penilaian,
 * integrasi monitoring Musyrifah, dan pembatasan akses antar-Guru.
 */
class GuruPembelajaranTest extends TestCase
{
    use RefreshDatabase;

    private const PERIODE = 'Semester Ganjil 2026/2027';

    public function test_alur_guru_lengkap_dari_memulai_pertemuan_sampai_reopen_detail(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);

        $this->actingAs($guru)
            ->get(route('guru.pembelajaran'))
            ->assertOk()
            ->assertSee('Pembelajaran')
            ->assertSee('Pertemuan 16');

        $pertemuanExisting = $this->mulaiPertemuan($guru, $mapel);

        $this->assertSame(1, $pertemuanExisting->pertemuan_ke);
        $this->assertSame(PertemuanPembelajaran::STATUS_BERLANGSUNG, $pertemuanExisting->status);
        $this->assertSame(now()->toDateString(), $pertemuanExisting->tanggal->toDateString());

        $this->actingAs($guru)
            ->put(route('guru.pembelajaran.absensi', $pertemuanExisting), [
                'absensi' => [
                    $mahasiswa->id => ['status' => 'hadir', 'catatan' => 'Tepat waktu'],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('absensi_pembelajarans', [
            'pertemuan_id' => $pertemuanExisting->id,
            'user_id' => $mahasiswa->id,
            'status' => 'hadir',
            'pertemuan_ke' => 1,
        ]);

        $this->actingAs($guru)
            ->post(route('guru.pembelajaran.materi', $pertemuanExisting), [
                'materi' => 'Mufradat pekan pertama beserta contoh kalimat.',
            ])
            ->assertRedirect();

        $this->actingAs($guru)
            ->post(route('guru.pembelajaran.tugas', $pertemuanExisting), [
                'judul' => 'Hafalan Mufradat',
                'deskripsi' => 'Hafalkan sepuluh mufradat berikut lalu setorkan.',
                'batas_pengumpulan' => now()->addDays(2)->format('Y-m-d\TH:i'),
                'status' => Tugas::STATUS_DIPUBLIKASIKAN,
            ])
            ->assertRedirect();

        $tugas = Tugas::query()->firstOrFail();
        $this->assertSame($pertemuanExisting->id, $tugas->pertemuan_id);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_TUGAS_BARU,
        ]);

        $this->actingAs($guru)
            ->put(route('guru.pembelajaran.selesai', $pertemuanExisting))
            ->assertRedirect()
            ->assertSessionHas('success');

        $pertemuanExisting->refresh();
        $this->assertSame(PertemuanPembelajaran::STATUS_SELESAI, $pertemuanExisting->status);
        $this->assertNotNull($pertemuanExisting->selesai_pada);

        // Pertemuan selesai tetap dapat dibuka kembali untuk melihat detail.
        $this->actingAs($guru)
            ->get(route('guru.pembelajaran.pertemuan', $pertemuanExisting))
            ->assertOk()
            ->assertSee('Mufradat pekan pertama')
            ->assertSee('Hafalan Mufradat')
            ->assertSee($mahasiswa->name)
            ->assertSee('Pertemuan sudah diselesaikan')
            ->assertSee('Riwayat Pertemuan');

        foreach (['pertemuan_dimulai', 'absensi_disimpan', 'materi_diunggah', 'tugas_ditambahkan', 'pertemuan_selesai'] as $aksi) {
            $this->assertDatabaseHas('activity_logs', [
                'modul' => 'pembelajaran',
                'aksi' => $aksi,
            ]);
        }
    }

    public function test_guru_dapat_memulai_pertemuan_berikutnya_sesuai_progres(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $this->mahasiswaKelompok($mapel);

        $this->actingAs($guru)->post(route('guru.pembelajaran.mulai'), $this->parameterMulai($mapel))
            ->assertRedirect()
            ->assertSessionHas('success');

        $pertama = PertemuanPembelajaran::query()->firstOrFail();
        $this->assertSame(1, $pertama->pertemuan_ke);
        $this->assertSame(now()->toDateString(), $pertama->tanggal->toDateString());

        // Pertemuan yang sama tidak boleh dibuat dua kali.
        $this->actingAs($guru)->post(route('guru.pembelajaran.mulai'), $this->parameterMulai($mapel))
            ->assertRedirect()
            ->assertSessionHas('info');

        $this->assertDatabaseCount('pertemuan_pembelajarans', 1);

        $this->isiAbsensi($guru, $pertama, 'hadir');
        $this->actingAs($guru)->put(route('guru.pembelajaran.selesai', $pertama))->assertRedirect();

        $this->actingAs($guru)->post(route('guru.pembelajaran.mulai'), $this->parameterMulai($mapel))
            ->assertRedirect();

        $this->assertSame(
            [1, 2],
            PertemuanPembelajaran::query()->orderBy('pertemuan_ke')->pluck('pertemuan_ke')->all()
        );
    }

    public function test_progres_16_pertemuan_terpisah_per_mata_pelajaran_dan_periode(): void
    {
        [$guru, $mapelGanjil] = $this->guruDenganJadwal();

        $mapelLain = MataPelajaran::query()->create([
            'nama' => 'Fiqih Ibadah',
            'kode' => 'FI-01',
            'kategori' => 'Fiqih',
        ]);
        $this->buatJadwal($mapelLain, 'Kelompok 1', $guru->name, self::PERIODE);
        $this->buatJadwal($mapelGanjil, 'Kelompok 1', $guru->name, 'Semester Genap 2026/2027');

        $this->mahasiswaKelompok($mapelGanjil);

        $pertemuanGanjil = $this->mulaiPertemuan($guru, $mapelGanjil, self::PERIODE);
        $this->isiAbsensi($guru, $pertemuanGanjil, 'hadir');
        $this->actingAs($guru)->put(route('guru.pembelajaran.selesai', $pertemuanGanjil))->assertRedirect();

        $this->assertSame(1, PertemuanPembelajaran::query()
            ->where('mata_pelajaran_id', $mapelGanjil->id)
            ->where('periode', self::PERIODE)
            ->count());
        $this->assertSame(0, PertemuanPembelajaran::query()
            ->where('periode', 'Semester Genap 2026/2027')
            ->count());
        $this->assertSame(0, PertemuanPembelajaran::query()
            ->where('mata_pelajaran_id', $mapelLain->id)
            ->count());

        // Mata pelajaran lain dengan nomor pertemuan yang sama tetap terpisah.
        $pertemuanLain = $this->mulaiPertemuan($guru, $mapelLain, self::PERIODE);
        $this->assertSame(1, $pertemuanLain->pertemuan_ke);
        $this->assertNotSame($pertemuanGanjil->id, $pertemuanLain->id);

        // Periode lain tetap terpisah meski mapel dan kelompoknya sama.
        $pertemuanGenap = $this->mulaiPertemuan($guru, $mapelGanjil, 'Semester Genap 2026/2027');
        $this->assertSame(1, $pertemuanGenap->pertemuan_ke);
        $this->assertDatabaseCount('pertemuan_pembelajarans', 3);

        $this->actingAs($guru)
            ->get(route('guru.pembelajaran', [
                'mata_pelajaran' => $mapelGanjil->id,
                'kelompok' => 'Kelompok 1',
                'periode' => 'Semester Genap 2026/2027',
            ]))
            ->assertOk()
            ->assertSee('0/16 selesai');
    }

    public function test_absensi_tersimpan_dan_koreksi_tercatat_pada_detail_pertemuan(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);
        $pertemuan = $this->mulaiPertemuan($guru, $mapel);

        $this->isiAbsensi($guru, $pertemuan, 'izin', 'Ada urusan keluarga');

        $this->assertDatabaseHas('absensi_pembelajarans', [
            'pertemuan_id' => $pertemuan->id,
            'user_id' => $mahasiswa->id,
            'status' => 'izin',
            'catatan' => 'Ada urusan keluarga',
        ]);
        $this->assertDatabaseHas('activity_logs', ['aksi' => 'absensi_disimpan']);

        // Koreksi mengganti baris yang sama dan tercatat pada audit trail.
        $this->isiAbsensi($guru, $pertemuan, 'hadir', null);

        $this->assertSame(1, AbsensiPembelajaran::query()
            ->where('pertemuan_id', $pertemuan->id)
            ->where('user_id', $mahasiswa->id)
            ->count());

        $baris = AbsensiPembelajaran::query()
            ->where('pertemuan_id', $pertemuan->id)
            ->where('user_id', $mahasiswa->id)
            ->firstOrFail();
        $this->assertSame('hadir', $baris->status);

        $catatan = ActivityLog::query()->where('aksi', 'absensi_dikoreksi')->latest('id')->firstOrFail();
        $this->assertSame('izin · Ada urusan keluarga', $catatan->data_sebelum[$mahasiswa->name] ?? null);
        $this->assertSame('hadir', $catatan->data_sesudah[$mahasiswa->name] ?? null);

        $this->actingAs($guru)
            ->get(route('guru.pembelajaran.pertemuan', $pertemuan))
            ->assertOk()
            ->assertSee($mahasiswa->name)
            ->assertSee('Simpan Koreksi Absensi');
    }

    public function test_status_absensi_di_luar_hadir_izin_sakit_alpa_ditolak(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);
        $pertemuan = $this->mulaiPertemuan($guru, $mapel);

        $this->actingAs($guru)
            ->put(route('guru.pembelajaran.absensi', $pertemuan), [
                'absensi' => [$mahasiswa->id => ['status' => 'bolos']],
            ])
            ->assertSessionHasErrors('absensi.'.$mahasiswa->id.'.status');

        $this->assertDatabaseCount('absensi_pembelajarans', 1); // hanya baris keanggotaan kelompok
    }

    public function test_pertemuan_tidak_dapat_diselesaikan_tanpa_absensi(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $this->mahasiswaKelompok($mapel);
        $pertemuan = $this->mulaiPertemuan($guru, $mapel);

        $this->actingAs($guru)
            ->put(route('guru.pembelajaran.selesai', $pertemuan))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(PertemuanPembelajaran::STATUS_BERLANGSUNG, $pertemuan->fresh()->status);
    }

    public function test_guru_tidak_dapat_memulai_pertemuan_ketujuh_belas(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $jadwalId = JadwalPembelajaran::query()->value('id');

        for ($nomor = 1; $nomor <= 16; $nomor++) {
            PertemuanPembelajaran::query()->create([
                'jadwal_id' => $jadwalId,
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => $guru->id,
                'kelompok' => 'Kelompok 1',
                'periode' => self::PERIODE,
                'pertemuan_ke' => $nomor,
                'tanggal' => now()->toDateString(),
                'status' => PertemuanPembelajaran::STATUS_SELESAI,
                'selesai_pada' => now(),
            ]);
        }

        $this->actingAs($guru)
            ->post(route('guru.pembelajaran.mulai'), $this->parameterMulai($mapel))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('pertemuan_pembelajarans', 16);
    }

    public function test_monitoring_musyrifah_membaca_progres_absensi_materi_tugas_dan_nilai(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);

        // Cegah seeder otomatis pada halaman monitoring.
        KelompokPengajar::query()->create([
            'nama_pengajar' => $guru->name,
            'program' => 'Tahfizh',
            'kelompok_teks' => '1',
            'jumlah_tm' => '16 TM',
        ]);

        $pertemuan = $this->mulaiPertemuan($guru, $mapel);
        $this->isiAbsensi($guru, $pertemuan, 'hadir');

        $this->actingAs($guru)->post(route('guru.pembelajaran.materi', $pertemuan), [
            'materi' => 'Materi pertemuan pertama untuk pemantauan.',
        ])->assertRedirect();

        $this->actingAs($guru)->post(route('guru.pembelajaran.tugas', $pertemuan), [
            'judul' => 'Tugas Pemantauan',
            'deskripsi' => 'Tugas ini dipakai pada pengujian monitoring Musyrifah.',
            'batas_pengumpulan' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'status' => Tugas::STATUS_DIPUBLIKASIKAN,
        ])->assertRedirect();

        $this->actingAs($guru)->put(route('guru.pembelajaran.selesai', $pertemuan))->assertRedirect();

        $tugas = Tugas::query()->firstOrFail();
        PengumpulanTugas::factory()->dinilai(90)->create([
            'tugas_id' => $tugas->id,
            'user_id' => $mahasiswa->id,
        ]);

        $musyrifah = User::factory()->create(['role' => 'musyrifah', 'status' => 'aktif']);

        $respons = $this->actingAs($musyrifah)->getJson(route('musyrifah.manajemen_pembelajaran.detail_pemantauan', [
            'id' => $mapel->id,
            'kelompok' => 'Kelompok 1',
            'periode' => self::PERIODE,
        ]))->assertOk()->assertJsonPath('success', true);

        $daftar = collect($respons->json('pertemuan'));
        $pertama = $daftar->firstWhere('pertemuan_ke', 1);

        $this->assertSame('Terlaksana', $pertama['status_pertemuan']);
        $this->assertSame(1, $pertama['hadir']);
        $this->assertTrue($pertama['materi']);
        $this->assertSame(1, $pertama['jumlah_tugas']);
        $this->assertSame(90.0, (float) $pertama['nilai_rata_rata']);
        $this->assertSame(16, $daftar->count());

        // Data yang sama terbaca pada halaman pemantauan Musyrifah.
        $this->actingAs($musyrifah)
            ->get(route('musyrifah.manajemen_pembelajaran'))
            ->assertOk()
            ->assertSee('1 dari 16 Pertemuan')
            ->assertSee('1 materi')
            ->assertSee('1 tugas')
            ->assertSee('Nilai rata-rata: 90');
    }

    public function test_monitoring_musyrifah_menampilkan_rekap_absensi_melalui_menu_absensi(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $this->mahasiswaKelompok($mapel);
        $pertemuan = $this->mulaiPertemuan($guru, $mapel);
        $this->isiAbsensi($guru, $pertemuan, 'hadir');

        $musyrifah = User::factory()->create(['role' => 'musyrifah', 'status' => 'aktif']);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.absensi', ['mata_pelajaran' => $mapel->id]))
            ->assertOk()
            ->assertSee('Rekap Absensi')
            ->assertSee($mapel->nama)
            ->assertSee('Pertemuan 1');
    }

    public function test_guru_tidak_dapat_mengakses_pertemuan_guru_lain(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);
        $pertemuan = $this->mulaiPertemuan($guru, $mapel);

        $guruLain = User::factory()->create([
            'name' => 'Ustaz Guru Lain',
            'role' => 'guru',
            'status' => 'aktif',
        ]);

        $this->actingAs($guruLain)
            ->get(route('guru.pembelajaran.pertemuan', $pertemuan))
            ->assertForbidden();

        $this->actingAs($guruLain)
            ->put(route('guru.pembelajaran.absensi', $pertemuan), [
                'absensi' => [$mahasiswa->id => ['status' => 'hadir']],
            ])
            ->assertForbidden();

        $this->actingAs($guruLain)
            ->put(route('guru.pembelajaran.selesai', $pertemuan))
            ->assertForbidden();

        $this->actingAs($guruLain)
            ->post(route('guru.pembelajaran.mulai'), $this->parameterMulai($mapel))
            ->assertForbidden();

        $this->actingAs($guruLain)
            ->get(route('guru.pembelajaran', ['mata_pelajaran' => $mapel->id]))
            ->assertForbidden();

        $this->actingAs($guruLain)
            ->get(route('guru.penilaian', ['mata_pelajaran' => $mapel->id]))
            ->assertForbidden();

        $this->assertDatabaseMissing('activity_logs', ['aksi' => 'pertemuan_selesai']);
    }

    public function test_guru_tidak_dapat_mengubah_penugasan_musyrifah(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();

        $this->actingAs($guru)
            ->post(route('musyrifah.manajemen_pembelajaran.jadwal.store'), [
                'mata_pelajaran_id' => $mapel->id,
                'nama_mata_pelajaran' => $mapel->nama,
                'kelompok' => 'Kelompok 2',
                'guru_pengampu' => $guru->name,
                'periode' => self::PERIODE,
                'hari' => 'Selasa',
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:30',
                'ruangan' => 'Ruang 2',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('jadwal_pembelajarans', 1);

        $this->actingAs($guru)
            ->post(route('musyrifah.manajemen_pembelajaran.kelompok.store'), [
                'nama_pengajar' => $guru->name,
                'program' => 'Tahfizh',
                'kelompok_teks' => '2',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('kelompok_pengajars', 0);
    }

    public function test_menu_penilaian_menampilkan_tugas_dan_rekap_nilai_sesuai_penugasan(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();
        $mahasiswa = $this->mahasiswaKelompok($mapel);

        $tugas = Tugas::factory()->dipublikasikan()->create([
            'user_id' => $guru->id,
            'nama_guru' => $guru->name,
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => 'Kelompok 1',
            'judul' => 'Tugas Penilaian Guru',
            'batas_pengumpulan' => now()->addDays(2),
        ]);

        PengumpulanTugas::factory()->dinilai(85)->create([
            'tugas_id' => $tugas->id,
            'user_id' => $mahasiswa->id,
        ]);

        $this->actingAs($guru)
            ->get(route('guru.penilaian'))
            ->assertOk()
            ->assertSee('Tugas Penilaian Guru')
            ->assertSee($mahasiswa->name)
            ->assertSee('85');

        $this->actingAs($guru)
            ->get(route('guru.penilaian', ['kelompok' => 'Kelompok 1']))
            ->assertOk()
            ->assertSee('Tugas Penilaian Guru');
    }

    public function test_sidebar_guru_tanpa_menu_profil_dan_pengaturan_akun(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);

        $label = array_column(MenuPeran::utama($guru), 'label');

        $this->assertSame(
            ['Dashboard', 'Pembelajaran', 'Absensi', 'Penilaian'],
            $label
        );

        $this->assertNotContains('Profil', $label);
        $this->assertNotContains('Pengaturan Akun', array_column(MenuPeran::bawah($guru), 'label'));

        // Profil dan pengaturan akun tetap dapat dibuka lewat ikon profil di header.
        $this->actingAs($guru)->get(route('profil.pengguna'))->assertOk();
        $this->actingAs($guru)->get(route('pengaturan-akun.index'))->assertOk();
    }

    public function test_halaman_jadwal_guru_mengarah_ke_menu_pembelajaran(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);

        $this->actingAs($guru)
            ->get(route('guru.jadwal-saya'))
            ->assertRedirect(route('guru.pembelajaran'));
    }

    public function test_role_lain_tetap_memiliki_akses_sesuai_kewenangannya(): void
    {
        [$guru, $mapel] = $this->guruDenganJadwal();

        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'status' => 'aktif']);
        $this->actingAs($mahasiswa)->get(route('guru.pembelajaran'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('guru.penilaian'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('guru.absensi'))->assertForbidden();

        $musyrifah = User::factory()->create(['role' => 'musyrifah', 'status' => 'aktif']);
        $this->actingAs($musyrifah)->get(route('musyrifah.absensi'))->assertOk();
        $this->actingAs($musyrifah)->get(route('guru.pembelajaran'))->assertForbidden();

        $pimpinan = User::factory()->create(['role' => 'pimpinan', 'status' => 'aktif']);
        $this->actingAs($pimpinan)->get(route('guru.penilaian'))->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $this->actingAs($admin)->get(route('musyrifah.absensi'))->assertOk();
        $this->actingAs($admin)->get(route('guru.pembelajaran'))->assertForbidden();

        // Autentikasi dan dashboard Guru tetap berjalan.
        $this->actingAs($guru)->get(route('guru.dashboard'))->assertOk();
    }

    /**
     * Menyiapkan Guru beserta jadwal/penugasan yang sah.
     *
     * @return array{0: User, 1: MataPelajaran}
     */
    private function guruDenganJadwal(
        string $namaGuru = 'Ustaz Fauzan',
        string $namaMapel = 'Bahasa Arab Dasar',
        string $kode = 'BA-01',
        string $kelompok = 'Kelompok 1',
        string $periode = self::PERIODE,
    ): array {
        $guru = User::factory()->create([
            'name' => $namaGuru,
            'role' => 'guru',
            'status' => 'aktif',
        ]);

        $mapel = MataPelajaran::query()->create([
            'nama' => $namaMapel,
            'kode' => $kode,
            'kategori' => 'Bahasa',
        ]);

        $this->buatJadwal($mapel, $kelompok, $namaGuru, $periode);

        return [$guru, $mapel];
    }

    private function buatJadwal(MataPelajaran $mapel, string $kelompok, string $namaGuru, string $periode): JadwalPembelajaran
    {
        return JadwalPembelajaran::query()->create([
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => $kelompok,
            'guru_pengampu' => $namaGuru,
            'periode' => $periode,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'ruangan' => 'Ruang 1',
        ]);
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

    /**
     * @return array<string, mixed>
     */
    private function parameterMulai(MataPelajaran $mapel, string $kelompok = 'Kelompok 1', string $periode = self::PERIODE): array
    {
        return [
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => $kelompok,
            'periode' => $periode,
        ];
    }

    private function mulaiPertemuan(
        User $guru,
        MataPelajaran $mapel,
        string $periode = self::PERIODE,
        string $kelompok = 'Kelompok 1',
    ): PertemuanPembelajaran {
        $this->actingAs($guru)
            ->post(route('guru.pembelajaran.mulai'), $this->parameterMulai($mapel, $kelompok, $periode))
            ->assertRedirect();

        return PertemuanPembelajaran::query()
            ->where('mata_pelajaran_id', $mapel->id)
            ->where('kelompok', $kelompok)
            ->where('periode', $periode)
            ->orderByDesc('id')
            ->firstOrFail();
    }

    private function isiAbsensi(
        User $guru,
        PertemuanPembelajaran $pertemuan,
        string $status,
        ?string $catatan = null,
    ): void {
        $mahasiswa = User::query()
            ->whereIn('id', AbsensiPembelajaran::query()->where('kelompok', $pertemuan->kelompok)->pluck('user_id'))
            ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
            ->firstOrFail();

        $this->actingAs($guru)
            ->put(route('guru.pembelajaran.absensi', $pertemuan), [
                'absensi' => [
                    $mahasiswa->id => ['status' => $status, 'catatan' => $catatan],
                ],
            ])
            ->assertRedirect();
    }
}
