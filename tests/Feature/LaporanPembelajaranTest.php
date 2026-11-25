<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\DokumentasiKegiatan;
use App\Models\JadwalPembelajaran;
use App\Models\LaporanPembelajaran;
use App\Models\MataPelajaran;
use App\Models\PengaturanSistem;
use App\Models\PertemuanPembelajaran;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LaporanPembelajaranTest extends TestCase
{
    use RefreshDatabase;

    private const MATA_PELAJARAN = [
        'Pembelajaran Kitab Kuning',
        'Penguatan Keterampilan Khusus Wawasan Interdisipliner dan Moderasi Beragama',
        'Penguatan Keterampilan Khusus',
        'Penguatan Bahasa Asing',
        'Penguatan Keterampilan Khusus (Karakter & Dakwah)',
        'Bahasa Arab',
    ];

    public function test_report_menu_exposes_six_subjects_in_required_order(): void
    {
        $musyrifah = $this->pengguna('musyrifah');

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan'))
            ->assertOk()
            ->assertSee('Laporan Pembelajaran')
            ->assertSee('Kelola per Mata Pelajaran');

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.pembelajaran.index'))
            ->assertOk()
            ->assertSeeInOrder(self::MATA_PELAJARAN)
            ->assertViewHas('mataPelajaran', fn (MataPelajaran $mapel): bool => $mapel->nama === self::MATA_PELAJARAN[0]);
    }

    public function test_report_rows_follow_source_meeting_changes_and_stay_subject_isolated(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $guru = $this->pengguna('guru');
        $guru->update(['name' => 'Ustaz Sinkron']);
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $mapelLain = $this->mataPelajaran(self::MATA_PELAJARAN[5]);
        $jadwal = $this->jadwal($mapel, 'Kelompok 1', $guru);
        $pertemuan = $this->pertemuan($mapel, $jadwal, [
            'tanggal' => '2026-11-02',
            'materi' => 'Materi kitab awal',
            'status' => 'selesai',
        ]);
        $this->pertemuan($mapelLain, null, [
            'tanggal' => '2026-11-03',
            'materi' => 'Materi mata pelajaran lain',
        ]);

        $this->absensi($this->pengguna('mahasiswa'), $mapel, [
            'jadwal_id' => $jadwal->id,
            'pertemuan_id' => $pertemuan->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => '2026-11-02',
            'status' => 'hadir',
        ]);
        $this->absensi($this->pengguna('mahasiswa'), $mapel, [
            'jadwal_id' => $jadwal->id,
            'pertemuan_id' => $pertemuan->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => '2026-11-02',
            'status' => 'hadir',
        ]);
        $this->absensi($this->pengguna('mahasiswa'), $mapel, [
            'jadwal_id' => $jadwal->id,
            'pertemuan_id' => $pertemuan->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => '2026-11-02',
            'status' => 'izin',
        ]);

        LaporanPembelajaran::query()->create([
            'mata_pelajaran_id' => $mapel->id,
            'pertemuan_id' => $pertemuan->id,
            'tanggal' => '2026-10-01',
            'kelompok' => 'Kelompok lama',
            'periode_semester' => 'Semester lama',
            'materi' => 'Materi laporan lama',
            'instruktur' => 'Instruktur lama',
            'tanda_tangan' => 'TTD',
        ]);

        Tugas::query()->create([
            'user_id' => $guru->id,
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => 'Kelompok 1',
            'pertemuan_id' => $pertemuan->id,
            'judul' => 'Tugas kitab',
            'deskripsi' => 'Deskripsi tugas',
            'batas_pengumpulan' => '2026-11-10 23:59:59',
            'status' => 'draft',
        ]);
        $dokumentasi = new DokumentasiKegiatan([
            'guru_id' => $guru->id,
            'judul' => 'Dokumentasi kitab',
            'kelompok' => 'Kelompok 1',
            'tanggal' => '2026-11-02',
        ]);
        $dokumentasi->pertemuan_id = $pertemuan->id;
        $dokumentasi->save();

        $pertemuan->update([
            'tanggal' => '2026-11-04',
            'materi' => 'Materi kitab terbaru',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.pembelajaran.index', ['mata_pelajaran' => $mapel->id]))
            ->assertOk()
            ->assertSee('Materi kitab terbaru')
            ->assertSee('4 November 2026')
            ->assertSee('Ustaz Sinkron')
            ->assertSee('Total 3 absensi')
            ->assertSee('1 tugas · 1 dokumentasi')
            ->assertDontSee('Materi laporan lama')
            ->assertDontSee('Materi mata pelajaran lain')
            ->assertViewHas('barisLaporan', fn (array $baris): bool => count($baris) === 1
                && $baris[0]['hadir'] === 2
                && $baris[0]['izin'] === 1
                && $baris[0]['total_absensi'] === 3);
    }

    public function test_legacy_attendance_is_merged_without_duplicate_rows(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $jadwal = $this->jadwal($mapel);
        $pertemuan = $this->pertemuan($mapel, $jadwal, ['tanggal' => '2026-11-02']);

        $this->absensi($this->pengguna('mahasiswa'), $mapel, [
            'jadwal_id' => $jadwal->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => '2026-11-02',
            'status' => 'hadir',
        ]);
        $this->absensi($this->pengguna('mahasiswa'), $mapel, [
            'jadwal_id' => $jadwal->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => '2026-11-02',
            'status' => 'izin',
        ]);
        $this->absensi($this->pengguna('mahasiswa'), $mapel, [
            'jadwal_id' => $jadwal->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 2,
            'tanggal' => '2026-11-09',
            'status' => 'hadir',
        ]);
        $this->absensi($this->pengguna('mahasiswa'), $mapel, [
            'jadwal_id' => $jadwal->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 2,
            'tanggal' => '2026-11-09',
            'status' => 'alpa',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.pembelajaran.index', ['mata_pelajaran' => $mapel->id]))
            ->assertOk()
            ->assertSee('Data absensi')
            ->assertViewHas('barisLaporan', function (array $baris) use ($pertemuan): bool {
                $ringkasan = array_map(fn (array $baris): array => [
                    'pertemuan_id' => $baris['pertemuan_id'],
                    'hadir' => $baris['hadir'],
                    'izin' => $baris['izin'],
                    'alpa' => $baris['alpa'],
                ], $baris);

                $this->assertSame([
                    ['pertemuan_id' => $pertemuan->id, 'hadir' => 1, 'izin' => 1, 'alpa' => 0],
                    ['pertemuan_id' => null, 'hadir' => 1, 'izin' => 0, 'alpa' => 1],
                ], $ringkasan);

                return true;
            });
    }

    public function test_manual_reports_respect_month_group_and_keyword_filters(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);

        $this->laporan($mapel, ['tanggal' => '2026-11-03', 'kelompok' => 'Kelompok 1', 'materi' => 'Kitab kuning lanjutan']);
        $this->laporan($mapel, ['tanggal' => '2026-11-04', 'kelompok' => 'Kelompok 1', 'materi' => 'Bahasa Inggris']);
        $this->laporan($mapel, ['tanggal' => '2026-11-05', 'kelompok' => 'Kelompok 2', 'materi' => 'Kitab kuning kelompok lain']);
        $this->laporan($mapel, ['tanggal' => '2026-12-03', 'kelompok' => 'Kelompok 1', 'materi' => 'Kitab kuning Desember']);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.pembelajaran.index', [
                'mata_pelajaran' => $mapel->id,
                'periode' => '2026-11',
                'kelompok' => 'Kelompok 1',
                'cari' => 'kitab',
            ]))
            ->assertOk()
            ->assertSee('Kitab kuning lanjutan')
            ->assertDontSee('Bahasa Inggris')
            ->assertDontSee('Kitab kuning kelompok lain')
            ->assertDontSee('Kitab kuning Desember')
            ->assertViewHas('barisLaporan', fn (array $baris): bool => count($baris) === 1);
    }

    public function test_invalid_month_filter_is_ignored_without_hiding_rows(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $this->laporan($mapel, ['tanggal' => '2026-11-03', 'materi' => 'Laporan November']);
        $this->laporan($mapel, ['tanggal' => '2026-12-03', 'materi' => 'Laporan Desember']);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.pembelajaran.index', [
                'mata_pelajaran' => $mapel->id,
                'periode' => '2026-99',
            ]))
            ->assertOk()
            ->assertSee('Laporan November')
            ->assertSee('Laporan Desember')
            ->assertViewHas('filter', fn (array $filter): bool => $filter['periode'] === '')
            ->assertViewHas('barisLaporan', fn (array $baris): bool => count($baris) === 2);
    }

    public function test_musyrifah_can_create_manual_report_and_preserve_return_filters(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $penggunaLain = $this->pengguna('admin');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $parameter = [
            'mataPelajaran' => $mapel->id,
            'periode' => '2026-11',
            'kelompok' => 'Kelompok 1',
            'cari' => 'kitab',
        ];

        $response = $this->actingAs($musyrifah)->post(
            route('musyrifah.laporan.pembelajaran.store', $parameter),
            array_merge($this->dataManual($mapel), ['dibuat_oleh' => $penggunaLain->id]),
        );

        $response->assertRedirect(route('musyrifah.laporan.pembelajaran.index', [
            'mata_pelajaran' => $mapel->id,
            'periode' => '2026-11',
            'kelompok' => 'Kelompok 1',
            'cari' => 'kitab',
        ]));
        $response->assertSessionHas('success', 'Laporan pembelajaran berhasil ditambahkan.');

        $laporan = LaporanPembelajaran::query()->firstOrFail();
        $this->assertSame($mapel->id, $laporan->mata_pelajaran_id);
        $this->assertSame('2026-11-03', $laporan->tanggal->toDateString());
        $this->assertSame('Kelompok 1', $laporan->kelompok);
        $this->assertSame('Materi manual kitab', $laporan->materi);
        $this->assertSame('IH', $laporan->tanda_tangan);
        $this->assertSame($musyrifah->id, $laporan->dibuat_oleh);
        $this->assertDatabaseHas('activity_logs', [
            'aksi' => 'laporan_pembelajaran_ditambahkan',
            'objek_id' => $laporan->id,
        ]);
    }

    public function test_musyrifah_can_update_manual_report(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $laporan = $this->laporan($mapel, ['dibuat_oleh' => $musyrifah->id]);
        $data = array_merge($this->dataManual($mapel), [
            'materi' => 'Materi manual diperbarui',
            'tanda_tangan' => 'TTD',
            'catatan' => 'Catatan diperbarui',
            'jumlah_hadir' => 12,
        ]);

        $this->actingAs($musyrifah)
            ->put(route('musyrifah.laporan.pembelajaran.update', [
                'mataPelajaran' => $mapel->id,
                'laporan' => $laporan->id,
            ]), $data)
            ->assertRedirect(route('musyrifah.laporan.pembelajaran.index', ['mata_pelajaran' => $mapel->id]))
            ->assertSessionHas('success', 'Laporan pembelajaran berhasil diperbarui.');

        $laporan->refresh();
        $this->assertSame('Materi manual diperbarui', $laporan->materi);
        $this->assertSame('TTD', $laporan->tanda_tangan);
        $this->assertSame('Catatan diperbarui', $laporan->catatan);
        $this->assertSame(12, $laporan->jumlah_hadir);
        $this->assertDatabaseHas('activity_logs', [
            'aksi' => 'laporan_pembelajaran_diperbarui',
            'objek_id' => $laporan->id,
        ]);
    }

    public function test_deleting_linked_report_keeps_source_meeting(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $pertemuan = $this->pertemuan($mapel);
        $laporan = $this->laporan($mapel, [
            'pertemuan_id' => $pertemuan->id,
            'dibuat_oleh' => $musyrifah->id,
        ]);

        $this->actingAs($musyrifah)
            ->delete(route('musyrifah.laporan.pembelajaran.destroy', [
                'mataPelajaran' => $mapel->id,
                'laporan' => $laporan->id,
            ]))
            ->assertRedirect(route('musyrifah.laporan.pembelajaran.index', ['mata_pelajaran' => $mapel->id]))
            ->assertSessionHas('success', 'Laporan pembelajaran berhasil dihapus.');

        $this->assertModelMissing($laporan);
        $this->assertModelExists($pertemuan);
        $this->assertDatabaseHas('activity_logs', [
            'aksi' => 'laporan_pembelajaran_dihapus',
            'objek_id' => $laporan->id,
        ]);
    }

    public function test_linked_report_store_uses_source_fields_instead_of_tampered_values(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $guru = $this->pengguna('guru');
        $guru->update(['name' => 'Ustaz Sumber']);
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $jadwal = $this->jadwal($mapel, 'Kelompok 1', $guru);
        $pertemuan = $this->pertemuan($mapel, $jadwal, [
            'tanggal' => '2026-11-02',
            'materi' => 'Materi sumber asli',
        ]);
        $data = array_merge($this->dataManual($mapel), [
            'pertemuan_id' => $pertemuan->id,
            'tanggal' => '2020-01-01',
            'kelompok' => 'Kelompok palsu',
            'periode_semester' => 'Periode palsu',
            'materi' => 'Materi palsu',
            'instruktur' => 'Instruktur palsu',
            'tanda_tangan' => 'IH',
            'catatan' => 'Catatan laporan',
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.laporan.pembelajaran.store', ['mataPelajaran' => $mapel->id]), $data)
            ->assertRedirect(route('musyrifah.laporan.pembelajaran.index', ['mata_pelajaran' => $mapel->id]))
            ->assertSessionHas('success', 'Laporan pembelajaran berhasil ditambahkan.');

        $laporan = LaporanPembelajaran::query()->firstOrFail();
        $this->assertSame($pertemuan->id, $laporan->pertemuan_id);
        $this->assertSame('2026-11-02', $laporan->tanggal->toDateString());
        $this->assertSame('Kelompok 1', $laporan->kelompok);
        $this->assertSame('Semester Ganjil 2026/2027', $laporan->periode_semester);
        $this->assertSame('Materi sumber asli', $laporan->materi);
        $this->assertSame('Ustaz Sumber', $laporan->instruktur);
        $this->assertSame('IH', $laporan->tanda_tangan);
        $this->assertSame('Catatan laporan', $laporan->catatan);
        $this->assertSame('Materi sumber asli', $pertemuan->fresh()->materi);
    }

    public function test_linked_report_update_refreshes_changed_source_fields(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $guruBaru = $this->pengguna('guru');
        $guruBaru->update(['name' => 'Ustaz Pengganti']);
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $pertemuan = $this->pertemuan($mapel);
        $laporan = $this->laporan($mapel, [
            'pertemuan_id' => $pertemuan->id,
            'dibuat_oleh' => $musyrifah->id,
        ]);

        $pertemuan->update([
            'guru_id' => $guruBaru->id,
            'tanggal' => '2026-12-05',
            'kelompok' => 'Kelompok 2',
            'periode' => 'Semester Genap 2026/2027',
            'materi' => 'Materi sumber setelah diubah',
        ]);

        $this->actingAs($musyrifah)
            ->put(route('musyrifah.laporan.pembelajaran.update', [
                'mataPelajaran' => $mapel->id,
                'laporan' => $laporan->id,
            ]), array_merge($this->dataManual($mapel), [
                'pertemuan_id' => $pertemuan->id,
                'tanggal' => '2020-01-01',
                'kelompok' => 'Kelompok palsu',
                'periode_semester' => 'Periode palsu',
                'materi' => 'Materi palsu',
                'instruktur' => 'Instruktur palsu',
                'tanda_tangan' => 'TTD baru',
            ]))
            ->assertRedirect(route('musyrifah.laporan.pembelajaran.index', ['mata_pelajaran' => $mapel->id]));

        $laporan->refresh();
        $this->assertSame('2026-12-05', $laporan->tanggal->toDateString());
        $this->assertSame('Kelompok 2', $laporan->kelompok);
        $this->assertSame('Semester Genap 2026/2027', $laporan->periode_semester);
        $this->assertSame('Materi sumber setelah diubah', $laporan->materi);
        $this->assertSame('Ustaz Pengganti', $laporan->instruktur);
        $this->assertSame('TTD baru', $laporan->tanda_tangan);
        $this->assertSame('Materi sumber setelah diubah', $pertemuan->fresh()->materi);
    }

    public function test_store_rejects_duplicate_report_for_one_meeting(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $pertemuan = $this->pertemuan($mapel);
        $this->laporan($mapel, ['pertemuan_id' => $pertemuan->id]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.laporan.pembelajaran.store', ['mataPelajaran' => $mapel->id]), [
                'mata_pelajaran_id' => $mapel->id,
                'pertemuan_id' => $pertemuan->id,
                'tanda_tangan' => 'IH',
            ])
            ->assertSessionHasErrors([
                'pertemuan_id' => 'Pertemuan ini sudah memiliki laporan. Gunakan fitur Edit pada laporan tersebut.',
            ]);

        $this->assertSame(1, LaporanPembelajaran::query()->count());
    }

    public function test_create_form_preselects_requested_unreported_meeting(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $pertemuan = $this->pertemuan($mapel);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.pembelajaran.create', [
                'mataPelajaran' => $mapel->id,
                'pertemuan_id' => $pertemuan->id,
            ]))
            ->assertOk()
            ->assertSee('Tambah Laporan Pembelajaran')
            ->assertViewHas('pertemuanTerpilih', fn (?PertemuanPembelajaran $terpilih): bool => $terpilih?->id === $pertemuan->id);
    }

    public function test_create_form_rejects_meeting_that_already_has_report(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $pertemuan = $this->pertemuan($mapel);
        $this->laporan($mapel, ['pertemuan_id' => $pertemuan->id]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.pembelajaran.create', [
                'mataPelajaran' => $mapel->id,
                'pertemuan_id' => $pertemuan->id,
            ]))
            ->assertNotFound();
    }

    public function test_store_requires_manual_date_and_material(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.laporan.pembelajaran.store', ['mataPelajaran' => $mapel->id]), [])
            ->assertSessionHasErrors(['mata_pelajaran_id', 'tanggal', 'materi']);

        $this->assertSame(0, LaporanPembelajaran::query()->count());
    }

    #[DataProvider('nilaiManualTidakValid')]
    public function test_store_rejects_invalid_manual_values(string $field, mixed $value): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $data = array_merge($this->dataManual($mapel), [$field => $value]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.laporan.pembelajaran.store', ['mataPelajaran' => $mapel->id]), $data)
            ->assertSessionHasErrors($field);

        $this->assertSame(0, LaporanPembelajaran::query()->count());
    }

    public static function nilaiManualTidakValid(): array
    {
        return [
            'tanggal tidak valid' => ['tanggal', 'bukan-tanggal'],
            'materi terlalu panjang' => ['materi', str_repeat('a', 5001)],
            'kelompok terlalu panjang' => ['kelompok', str_repeat('a', 61)],
            'periode terlalu panjang' => ['periode_semester', str_repeat('a', 101)],
            'instruktur terlalu panjang' => ['instruktur', str_repeat('a', 256)],
            'hadir negatif' => ['jumlah_hadir', -1],
            'izin melebihi batas' => ['jumlah_izin', 501],
            'sakit bukan angka' => ['jumlah_sakit', 'bukan-angka'],
            'alpa negatif' => ['jumlah_alpa', -1],
            'tanda tangan terlalu panjang' => ['tanda_tangan', str_repeat('a', 256)],
            'catatan terlalu panjang' => ['catatan', str_repeat('a', 2001)],
        ];
    }

    public function test_store_rejects_meeting_from_another_subject(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $mapelLain = $this->mataPelajaran(self::MATA_PELAJARAN[5]);
        $pertemuanLain = $this->pertemuan($mapelLain);
        $data = array_merge($this->dataManual($mapel), ['pertemuan_id' => $pertemuanLain->id]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.laporan.pembelajaran.store', ['mataPelajaran' => $mapel->id]), $data)
            ->assertSessionHasErrors('pertemuan_id');

        $this->assertSame(0, LaporanPembelajaran::query()->count());
    }

    public function test_store_rejects_subject_id_that_does_not_match_route(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $mapelLain = $this->mataPelajaran(self::MATA_PELAJARAN[5]);

        $this->actingAs($musyrifah)
            ->post(
                route('musyrifah.laporan.pembelajaran.store', ['mataPelajaran' => $mapel->id]),
                $this->dataManual($mapelLain),
            )
            ->assertSessionHasErrors('mata_pelajaran_id');

        $this->assertSame(0, LaporanPembelajaran::query()->count());
    }

    #[DataProvider('operasiLaporan')]
    public function test_cross_subject_report_requests_return_404(string $method, string $action): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $mapelLain = $this->mataPelajaran(self::MATA_PELAJARAN[5]);
        $laporan = $this->laporan($mapelLain);
        $url = route("musyrifah.laporan.pembelajaran.{$action}", [
            'mataPelajaran' => $mapel->id,
            'laporan' => $laporan->id,
        ]);
        $response = $this->actingAs($musyrifah);

        $result = match ($method) {
            'get' => $response->get($url),
            'put' => $response->put($url, $this->dataManual($mapel)),
            'delete' => $response->delete($url),
        };

        $result->assertNotFound();
        $this->assertModelExists($laporan);
    }

    public static function operasiLaporan(): array
    {
        return [
            'edit' => ['get', 'edit'],
            'update' => ['put', 'update'],
            'delete' => ['delete', 'destroy'],
        ];
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('musyrifah.laporan.pembelajaran.index'))->assertRedirect(route('login'));
    }

    #[DataProvider('peranTanpaAkses')]
    public function test_non_manager_roles_cannot_open_report_index(string $role): void
    {
        $this->actingAs($this->pengguna($role))
            ->get(route('musyrifah.laporan.pembelajaran.index'))
            ->assertForbidden();
    }

    public static function peranTanpaAkses(): array
    {
        return [
            'mahasiswa' => ['mahasiswa'],
            'guru' => ['guru'],
            'pimpinan' => ['pimpinan'],
        ];
    }

    public function test_non_manager_cannot_create_report(): void
    {
        $mahasiswa = $this->pengguna('mahasiswa');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);

        $this->actingAs($mahasiswa)
            ->post(
                route('musyrifah.laporan.pembelajaran.store', ['mataPelajaran' => $mapel->id]),
                $this->dataManual($mapel),
            )
            ->assertForbidden();

        $this->assertSame(0, LaporanPembelajaran::query()->count());
    }

    public function test_unknown_subject_returns_404(): void
    {
        $this->actingAs($this->pengguna('musyrifah'))
            ->get(route('musyrifah.laporan.pembelajaran.index', ['mata_pelajaran' => 999999]))
            ->assertNotFound();
    }

    #[DataProvider('mataPelajaranCetak')]
    public function test_every_subject_print_uses_fixed_leader_signature(string $namaMataPelajaran): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $musyrifah = $this->pengguna('musyrifah');
        User::factory()->create([
            'name' => 'Dr. H. Ahmad Fauzi, M.Pd.',
            'nip' => '198765432112345001',
            'role' => 'pimpinan',
            'status' => 'aktif',
        ]);
        $mapel = $this->mataPelajaran($namaMataPelajaran);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.laporan.pembelajaran.cetak', [
            'mataPelajaran' => $mapel->id,
        ]));

        $response->assertSee('aria-label="Tanda tangan pimpinan"', false)
            ->assertSee('Dr. H. Ahmad Fauzi, M.Pd.')
            ->assertSee('NIP. 198765432112345001')
            ->assertSee('Padang, 25 November 2026')
            ->assertSee('class="laporan-ttd-ruang"', false)
            ->assertDontSee('NIP. ................................', false);

        $this->assertSame(1, substr_count($response->getContent(), 'class="laporan-ttd"'));
    }

    public static function mataPelajaranCetak(): array
    {
        return [
            'kitab kuning' => [self::MATA_PELAJARAN[0]],
            'wawasan interdisipliner' => [self::MATA_PELAJARAN[1]],
            'keterampilan khusus' => [self::MATA_PELAJARAN[2]],
            'bahasa asing' => [self::MATA_PELAJARAN[3]],
            'karakter dan dakwah' => [self::MATA_PELAJARAN[4]],
            'bahasa arab' => [self::MATA_PELAJARAN[5]],
        ];
    }

    public function test_print_view_uses_official_format_and_escapes_report_content(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $musyrifah = $this->pengguna('musyrifah');
        $guru = $this->pengguna('guru');
        $guru->update(['name' => 'Ustaz Cetak']);
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $jadwal = $this->jadwal($mapel, 'Kelompok 1', $guru);
        $pertemuan = $this->pertemuan($mapel, $jadwal, [
            'tanggal' => '2026-11-02',
            'materi' => '<script>alert("xss")</script> & materi',
        ]);
        $this->laporan($mapel, [
            'pertemuan_id' => $pertemuan->id,
            'tanda_tangan' => '<b>IH</b>',
        ]);
        $pengaturan = PengaturanSistem::aktif();
        $pengaturan->update([
            'nama_institusi' => 'Ma’had Al-Jami’ah UIN Imam Bonjol Padang',
            'logo_path' => 'logo/uin.png',
        ]);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.laporan.pembelajaran.cetak', [
            'mataPelajaran' => $mapel->id,
            'periode' => '2026-11',
            'kelompok' => 'Kelompok 1',
        ]));

        $response->assertOk()
            ->assertSee('KEMENTERIAN AGAMA REPUBLIK INDONESIA')
            ->assertSee('UNIVERSITAS ISLAM NEGERI IMAM BONJOL PADANG')
            ->assertSee('Laporan Pembelajaran')
            ->assertSee('November 2026')
            ->assertSee('08:00–09:30 WIB')
            ->assertSee('Ustaz Cetak')
            ->assertSee('Kelompok 1')
            ->assertSee('Kepala Ma’had Al-Jami’ah')
            ->assertSee('Padang, 25 November 2026')
            ->assertSee(asset('storage/logo/uin.png'))
            ->assertSeeInOrder(['No', 'Hari/Tanggal', 'Materi', 'Tanda Tangan'])
            ->assertSee('&lt;script&gt;', false)
            ->assertSee('&lt;b&gt;IH&lt;/b&gt;', false)
            ->assertDontSee('<script>alert("xss")</script>', false);

        $this->assertSame(4, substr_count($response->getContent(), '<th scope="col"'));
    }

    public function test_csv_download_is_subject_scoped_and_blocks_spreadsheet_formulas(): void
    {
        $musyrifah = $this->pengguna('musyrifah');
        $mapel = $this->mataPelajaran(self::MATA_PELAJARAN[0]);
        $mapelLain = $this->mataPelajaran(self::MATA_PELAJARAN[5]);
        $this->laporan($mapel, [
            'tanggal' => '2026-11-03',
            'materi' => '=HYPERLINK("https://example.com","klik")',
            'tanda_tangan' => '+SUM(1,2)',
        ]);
        $this->laporan($mapelLain, [
            'tanggal' => '2026-11-03',
            'materi' => 'Materi mata pelajaran lain',
        ]);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.laporan.pembelajaran.unduh', [
            'mataPelajaran' => $mapel->id,
            'periode' => '2026-11',
        ]));

        $response->assertOk()
            ->assertDownload('laporan-pembelajaran-pembelajaran-kitab-kuning-2026-11.csv')
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('No,Hari/Tanggal,Materi,"Tanda Tangan"', $csv);
        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringContainsString("'+SUM", $csv);
        $this->assertStringNotContainsString('Materi mata pelajaran lain', $csv);
    }

    private function pengguna(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'status' => 'aktif',
        ]);
    }

    private function mataPelajaran(string $nama): MataPelajaran
    {
        return MataPelajaran::query()->where('nama', $nama)->firstOrFail();
    }

    private function jadwal(MataPelajaran $mapel, string $kelompok = 'Kelompok 1', ?User $guru = null): JadwalPembelajaran
    {
        $guru ??= $this->pengguna('guru');

        return JadwalPembelajaran::query()->create([
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => $kelompok,
            'guru_id' => $guru->id,
            'guru_pengampu' => $guru->name,
            'periode' => 'Semester Ganjil 2026/2027',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'ruangan' => 'Ruang Uji',
        ]);
    }

    private function pertemuan(MataPelajaran $mapel, ?JadwalPembelajaran $jadwal = null, array $atribut = []): PertemuanPembelajaran
    {
        $jadwal ??= $this->jadwal($mapel, $atribut['kelompok'] ?? 'Kelompok 1');

        return PertemuanPembelajaran::query()->create(array_merge([
            'jadwal_id' => $jadwal->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $jadwal->guru_id,
            'kelompok' => $jadwal->kelompok,
            'periode' => $jadwal->periode,
            'pertemuan_ke' => 1,
            'tanggal' => '2026-11-02',
            'materi' => 'Materi awal',
            'status' => 'berlangsung',
        ], $atribut));
    }

    private function absensi(User $mahasiswa, MataPelajaran $mapel, array $atribut = []): AbsensiPembelajaran
    {
        return AbsensiPembelajaran::query()->create(array_merge([
            'user_id' => $mahasiswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'jadwal_id' => null,
            'pertemuan_id' => null,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => '2026-11-02',
            'status' => 'hadir',
        ], $atribut));
    }

    private function laporan(MataPelajaran $mapel, array $atribut = []): LaporanPembelajaran
    {
        return LaporanPembelajaran::query()->create(array_merge([
            'mata_pelajaran_id' => $mapel->id,
            'pertemuan_id' => null,
            'tanggal' => '2026-11-03',
            'kelompok' => 'Kelompok 1',
            'periode_semester' => 'Semester Ganjil 2026/2027',
            'materi' => 'Materi manual',
            'instruktur' => 'Musyrifah Uji',
            'jumlah_hadir' => 10,
            'jumlah_izin' => 0,
            'jumlah_sakit' => 0,
            'jumlah_alpa' => 0,
        ], $atribut));
    }

    /**
     * @return array<string, mixed>
     */
    private function dataManual(MataPelajaran $mapel): array
    {
        return [
            'mata_pelajaran_id' => $mapel->id,
            'pertemuan_id' => null,
            'tanggal' => '2026-11-03',
            'kelompok' => 'Kelompok 1',
            'periode_semester' => 'Semester Ganjil 2026/2027',
            'materi' => 'Materi manual kitab',
            'instruktur' => 'Musyrifah Uji',
            'jumlah_hadir' => 10,
            'jumlah_izin' => 1,
            'jumlah_sakit' => 0,
            'jumlah_alpa' => 0,
            'tanda_tangan' => 'IH',
            'catatan' => 'Catatan manual',
        ];
    }
}
