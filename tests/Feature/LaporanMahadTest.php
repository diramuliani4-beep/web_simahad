<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\Kamar;
use App\Models\KelompokBelajar;
use App\Models\MataPelajaran;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\PenempatanKelompokBelajar;
use App\Models\User;
use App\Services\LaporanMahadService;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanMahadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buatDataLaporan();
    }

    public function test_musyrifah_can_view_laporan_index(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $response = $this->actingAs($musyrifah)->get('/musyrifah/laporan');

        $response->assertOk();
        $response->assertSee('Laporan Kehadiran');
        $response->assertSee('Laporan Pembayaran');
        $response->assertSee('Laporan Pendaftaran');
        $response->assertSee('Laporan Data Kamar');
        $response->assertSee('Lihat Laporan');
    }

    public function test_musyrifah_attendance_print_contains_fixed_leader_signature(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        User::factory()->create([
            'name' => 'Pimpinan Lama',
            'role' => 'pimpinan',
            'status' => 'aktif',
        ]);
        User::factory()->create([
            'name' => 'Dr. H. Ahmad Fauzi, M.Pd. <script>alert("pimpinan")</script>',
            'nip' => '198765432112345001',
            'role' => 'pimpinan',
            'status' => 'aktif',
        ]);
        $musyrifah = User::factory()->create(['role' => 'musyrifah', 'status' => 'aktif']);

        $response = $this->actingAs($musyrifah)->get('/musyrifah/laporan/kehadiran?periode=2026-11');

        $response->assertSee('aria-label="Tanda tangan pimpinan"', false)
            ->assertSee('Dr. H. Ahmad Fauzi, M.Pd. &lt;script&gt;', false)
            ->assertSee('NIP. 198765432112345001')
            ->assertSee('Padang, 25 November 2026')
            ->assertSee('class="laporan-ttd-ruang"', false)
            ->assertDontSee('Pimpinan Lama')
            ->assertDontSee('<script>alert("pimpinan")</script>', false)
            ->assertDontSee('NIP. ................................', false);

        $this->assertSame(1, substr_count($response->getContent(), 'class="laporan-ttd"'));
    }

    public function test_musyrifah_can_view_kehadiran_report_with_real_data(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $response = $this->actingAs($musyrifah)->get('/musyrifah/laporan/kehadiran?periode='.$this->periode());

        $response->assertOk();
        $response->assertSee('Rekap Kehadiran Mahasiswa');
        $response->assertSee('Nadia Putri');
        $response->assertSee('2317000001');
        $response->assertSee('100%');
    }

    public function test_musyrifah_attendance_report_renders_sixteen_meetings_with_only_recorded_statuses(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $mahasiswa = $this->mahasiswaUji('2317000101', 'Aulia Hadir');
        $mapel = $this->mataPelajaranUji('Akhlak Laporan', 'AKH-LAP');
        $this->buatPendaftaranDisetujui($mahasiswa);
        $this->buatAbsensiEmpatStatus($mahasiswa, $mapel, 'Kelompok Uji');
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.laporan.detail', [
            'jenis' => 'kehadiran',
            'periode' => '2026-11',
            'mata_pelajaran' => $mapel->id,
            'kelompok' => 'Kelompok Uji',
            'cari' => $mahasiswa->nim,
        ]));

        $response->assertOk()
            ->assertSee('Daftar Hadir Mahasiswa')
            ->assertSee('PRODI')
            ->assertSee('Sistem Informasi')
            ->assertSee('Kehadiran per Pertemuan')
            ->assertSee('Kolom kosong menunjukkan pertemuan yang belum memiliki data absensi.');

        $dom = new DOMDocument;
        $dom->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        $headerPertemuan = $xpath->query('//table[contains(@class, "lap-table-kehadiran")]/thead//th[contains(@class, "lap-col-pertemuan")]');
        $rows = $xpath->query('//table[contains(@class, "lap-table-kehadiran")]/tbody/tr');

        $this->assertSame(16, $headerPertemuan->length);
        $this->assertSame(1, $rows->length);

        $cells = $rows->item(0)->getElementsByTagName('td');

        $this->assertSame(27, $cells->length);
        $this->assertSame('Sistem Informasi', trim($cells->item(3)->textContent));
        $this->assertSame('H', trim($cells->item(6)->textContent));
        $this->assertSame('I', trim($cells->item(7)->textContent));
        $this->assertSame('S', trim($cells->item(8)->textContent));
        $this->assertSame('', trim($cells->item(9)->textContent));
        $this->assertSame('A', trim($cells->item(21)->textContent));
        $this->assertSame('1', trim($cells->item(22)->textContent));
        $this->assertSame('1', trim($cells->item(23)->textContent));
        $this->assertSame('1', trim($cells->item(24)->textContent));
        $this->assertSame('1', trim($cells->item(25)->textContent));
        $this->assertSame('4', trim($cells->item(26)->textContent));
    }

    public function test_musyrifah_attendance_csv_preserves_status_codes_and_blank_meetings(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $mahasiswa = $this->mahasiswaUji('2317000102', 'Aulia Csv');
        $mapel = $this->mataPelajaranUji('Akhlak CSV', 'AKH-CSV');
        $this->buatAbsensiEmpatStatus($mahasiswa, $mapel, 'Kelompok CSV');
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.laporan.unduh', [
            'jenis' => 'kehadiran',
            'periode' => '2026-11',
            'mata_pelajaran' => $mapel->id,
            'kelompok' => 'Kelompok CSV',
            'cari' => $mahasiswa->nim,
        ]));

        $response->assertOk()->assertDownload('laporan-kehadiran-2026-11.csv');

        $lines = explode("\n", str_replace("\r\n", "\n", trim($response->streamedContent())));
        $header = str_getcsv(ltrim($lines[0], "\xEF\xBB\xBF"));
        $row = str_getcsv($lines[1]);

        $this->assertSame('PRODI', $header[2]);
        $this->assertSame('Pertemuan 1', $header[5]);
        $this->assertSame('Pertemuan 16', $header[20]);
        $this->assertSame(['H', 'I', 'S', '', '', '', '', '', '', '', '', '', '', '', '', 'A'], array_slice($row, 5, 16));
        $this->assertSame(['1', '1', '1', '1', '4'], array_slice($row, 21, 5));
    }

    public function test_musyrifah_attendance_report_keeps_subject_statuses_in_separate_rows(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $mahasiswa = $this->mahasiswaUji('2317000103', 'Aulia Dua Mapel');
        $mapelAkhlak = $this->mataPelajaranUji('Akhlak Dua Mapel', 'AKH-DUA');
        $mapelBahasa = $this->mataPelajaranUji('Bahasa Dua Mapel', 'BHS-DUA');
        $this->buatAbsensi($mahasiswa, $mapelAkhlak, 'Kelompok Akhlak', 1, 'hadir');
        $this->buatAbsensi($mahasiswa, $mapelBahasa, 'Kelompok Bahasa', 1, 'izin');
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.detail', [
                'jenis' => 'kehadiran',
                'periode' => '2026-11',
                'cari' => $mahasiswa->nim,
            ]))
            ->assertOk()
            ->assertViewHas('laporan', function (array $laporan): bool {
                $baris = collect($laporan['baris'])->keyBy('mata_pelajaran');
                $ringkasan = collect($laporan['ringkasan'])->pluck('nilai', 'label');

                return count($baris) === 2
                    && $baris['Akhlak Dua Mapel']['pertemuan_1'] === 'H'
                    && $baris['Bahasa Dua Mapel']['pertemuan_1'] === 'I'
                    && $ringkasan['Total Mahasiswa'] === 1
                    && $ringkasan['Total Absensi'] === 2;
            });
    }

    public function test_musyrifah_attendance_report_filters_by_subject_and_group(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $mahasiswa = $this->mahasiswaUji('2317000104', 'Aulia Filter');
        $mapelAkhlak = $this->mataPelajaranUji('Akhlak Filter', 'AKH-FILTER');
        $mapelBahasa = $this->mataPelajaranUji('Bahasa Filter', 'BHS-FILTER');
        $this->buatAbsensi($mahasiswa, $mapelAkhlak, 'Kelompok Pilihan', 1, 'hadir');
        $this->buatAbsensi($mahasiswa, $mapelBahasa, 'Kelompok Lain', 1, 'izin');
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.detail', [
                'jenis' => 'kehadiran',
                'periode' => '2026-11',
                'mata_pelajaran' => $mapelAkhlak->id,
                'kelompok' => 'Kelompok Pilihan',
                'cari' => $mahasiswa->nim,
            ]))
            ->assertOk()
            ->assertViewHas('laporan', fn (array $laporan): bool => count($laporan['baris']) === 1
                && $laporan['baris'][0]['mata_pelajaran'] === 'Akhlak Filter'
                && $laporan['baris'][0]['kelompok'] === 'Kelompok Pilihan'
                && $laporan['baris'][0]['pertemuan_1'] === 'H')
            ->assertViewHas('filterTambahan', function (array $filters) use ($mapelAkhlak): bool {
                $filter = collect($filters)->keyBy('name');

                return $filter['mata_pelajaran']['nilai'] === (string) $mapelAkhlak->id
                    && $filter['kelompok']['nilai'] === 'Kelompok Pilihan';
            });
    }

    public function test_musyrifah_attendance_report_lists_registered_students_with_blank_unrecorded_meetings(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $mahasiswa = $this->mahasiswaUji('2317000105', 'Aulia Belum Absen');
        $mapel = $this->mataPelajaranUji('Akhlak Belum Absen', 'AKH-KOSONG');
        $kelompok = KelompokBelajar::query()->create([
            'nama_kelompok' => 'Kelompok Kosong',
            'nomor_kelompok' => 90,
        ]);
        $this->buatPenempatanAktif($mahasiswa, $kelompok);
        $this->buatPendaftaranDisetujui($mahasiswa);
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.detail', [
                'jenis' => 'kehadiran',
                'periode' => '2026-11',
                'mata_pelajaran' => $mapel->id,
                'kelompok' => $kelompok->nama_kelompok,
                'cari' => $mahasiswa->nim,
            ]))
            ->assertOk()
            ->assertViewHas('laporan', function (array $laporan): bool {
                if (count($laporan['baris']) !== 1) {
                    return false;
                }

                $baris = $laporan['baris'][0];

                if ($baris['program_studi'] !== 'Sistem Informasi'
                    || $baris['total'] !== 0
                    || $baris['alpa'] !== 0) {
                    return false;
                }

                for ($pertemuan = 1; $pertemuan <= 16; $pertemuan++) {
                    if ($baris['pertemuan_'.$pertemuan] !== '') {
                        return false;
                    }
                }

                return true;
            });
    }

    public function test_laporan_kehadiran_mencakup_semua_mata_pelajaran_untuk_penempatan_global(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $mahasiswa = $this->mahasiswaUji('2317000107', 'Aulia Penempatan Global');
        $mapelAkhlak = $this->mataPelajaranUji('Akhlak Global', 'AKH-GLOBAL');
        $mapelBahasa = $this->mataPelajaranUji('Bahasa Global', 'BHS-GLOBAL');
        $kelompok = KelompokBelajar::query()->create([
            'nama_kelompok' => 'Kelompok Global',
            'nomor_kelompok' => 91,
        ]);
        $this->buatPendaftaranDisetujui($mahasiswa);
        $this->buatPenempatanAktif($mahasiswa, $kelompok);
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);
        $jumlahMataPelajaran = MataPelajaran::query()->count();

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.detail', [
                'jenis' => 'kehadiran',
                'periode' => '2026-11',
                'kelompok' => $kelompok->nama_kelompok,
                'cari' => $mahasiswa->nim,
            ]))
            ->assertOk()
            ->assertViewHas('laporan', function (array $laporan) use ($mapelAkhlak, $mapelBahasa, $kelompok, $jumlahMataPelajaran): bool {
                $baris = collect($laporan['baris'])->keyBy('mata_pelajaran');

                if (count($baris) !== $jumlahMataPelajaran) {
                    return false;
                }

                foreach ([$mapelAkhlak, $mapelBahasa] as $mapel) {
                    $item = $baris->get($mapel->nama);

                    if ($item === null
                        || $item['kelompok'] !== $kelompok->nama_kelompok
                        || $item['total'] !== 0
                        || $item['pertemuan_1'] !== '') {
                        return false;
                    }
                }

                return true;
            });
    }

    public function test_laporan_kehadiran_menyaring_penempatan_global_berdasarkan_mata_pelajaran(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $mahasiswa = $this->mahasiswaUji('2317000108', 'Aulia Filter Global');
        $mapelAkhlak = $this->mataPelajaranUji('Akhlak Filter Global', 'AKH-FILTER-GLOBAL');
        $mapelBahasa = $this->mataPelajaranUji('Bahasa Filter Global', 'BHS-FILTER-GLOBAL');
        $kelompok = KelompokBelajar::query()->create([
            'nama_kelompok' => 'Kelompok Filter Global',
            'nomor_kelompok' => 92,
        ]);
        $this->buatPendaftaranDisetujui($mahasiswa);
        $this->buatPenempatanAktif($mahasiswa, $kelompok);
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.laporan.detail', [
                'jenis' => 'kehadiran',
                'periode' => '2026-11',
                'mata_pelajaran' => $mapelBahasa->id,
                'kelompok' => $kelompok->nama_kelompok,
                'cari' => $mahasiswa->nim,
            ]))
            ->assertOk()
            ->assertViewHas('laporan', function (array $laporan) use ($mapelBahasa, $kelompok): bool {
                if (count($laporan['baris']) !== 1) {
                    return false;
                }

                $baris = $laporan['baris'][0];

                return $baris['mata_pelajaran'] === $mapelBahasa->nama
                    && $baris['kelompok'] === $kelompok->nama_kelompok
                    && $baris['total'] === 0;
            });
    }

    public function test_musyrifah_attendance_report_ignores_invalid_statuses_and_meetings_outside_sixteen(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $mahasiswa = $this->mahasiswaUji('2317000106', 'Aulia Data Tidak Valid');
        $mapel = $this->mataPelajaranUji('Akhlak Validasi', 'AKH-VALID');
        $this->buatAbsensi($mahasiswa, $mapel, 'Kelompok Validasi', 1, 'hadir');
        $this->buatAbsensi($mahasiswa, $mapel, 'Kelompok Validasi', 2, 'tidak diketahui');
        $this->buatAbsensi($mahasiswa, $mapel, 'Kelompok Validasi', 17, 'hadir');
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.laporan.detail', [
            'jenis' => 'kehadiran',
            'periode' => '2026-11',
            'mata_pelajaran' => $mapel->id,
            'kelompok' => 'Kelompok Validasi',
            'cari' => $mahasiswa->nim,
        ]));

        $response->assertViewHas('laporan', function (array $laporan): bool {
            if (count($laporan['baris']) !== 1) {
                return false;
            }

            $baris = $laporan['baris'][0];
            $ringkasan = collect($laporan['ringkasan'])->pluck('nilai', 'label');

            return $baris['pertemuan_1'] === 'H'
                && $baris['pertemuan_2'] === ''
                && $baris['total'] === 1
                && $baris['hadir'] === 1
                && $baris['izin'] === 0
                && $baris['sakit'] === 0
                && $baris['alpa'] === 0
                && $ringkasan['Total Absensi'] === 1
                && $ringkasan['Total Hadir'] === 1
                && $ringkasan['Rata-rata Kehadiran'] === '100%';
        });
    }

    public function test_musyrifah_can_view_pembayaran_report_with_real_data(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $response = $this->actingAs($musyrifah)->get('/musyrifah/laporan/pembayaran?periode='.$this->periode());

        $response->assertOk();
        $response->assertSee('Data Pembayaran Mahasiswa');
        $response->assertSee('Nadia Putri');
        $response->assertSee('Rp 2.500.000');
        $response->assertSee('Lunas');
    }

    public function test_musyrifah_can_view_pendaftaran_report_with_real_data(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $response = $this->actingAs($musyrifah)->get('/musyrifah/laporan/pendaftaran?periode='.$this->periode());

        $response->assertOk();
        $response->assertSee('Data Pendaftar');
        $response->assertSee('Nadia Putri');
        $response->assertSee('Sistem Informasi');
        $response->assertSee('Disetujui');
    }

    public function test_musyrifah_can_view_kamar_report_with_real_data(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $response = $this->actingAs($musyrifah)->get('/musyrifah/laporan/kamar?periode='.$this->periode());

        $response->assertOk();
        $response->assertSee('Data Kamar Ma’had');
        $response->assertSee('TEST-01');
        $response->assertSee('Gedung Uji');
        $response->assertSee('Tersedia');
    }

    public function test_musyrifah_can_download_report_as_csv(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $response = $this->actingAs($musyrifah)
            ->get('/musyrifah/laporan/kamar/unduh?periode='.$this->periode());

        $response->assertOk();
        $response->assertDownload('laporan-kamar-'.$this->periode().'.csv');
    }

    public function test_musyrifah_laporan_rejects_unknown_report_type(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $this->actingAs($musyrifah)
            ->get('/musyrifah/laporan/tidak-ada')
            ->assertNotFound();
    }

    public function test_pimpinan_has_no_laporan_summary_page(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-mahad')
            ->assertNotFound();

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-penghuni')
            ->assertOk()
            ->assertSee('Laporan Penghuni')
            ->assertDontSee('Register Laporan');
    }

    public function test_pimpinan_can_view_all_monitoring_reports(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);
        $periode = $this->periode();

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-penghuni?periode='.$periode)
            ->assertOk()
            ->assertSee('Nadia Putri')
            ->assertSee('2317000001')
            ->assertSee('Kamar TEST-01');

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-kamar?periode='.$periode)
            ->assertOk()
            ->assertSee('TEST-01')
            ->assertSee('Gedung Uji');

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-kehadiran?periode='.$periode)
            ->assertOk()
            ->assertSee('Nadia Putri')
            ->assertSee('Rekap Kehadiran Mahasiswa');

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-pembayaran?periode='.$periode)
            ->assertOk()
            ->assertSee('Rp 2.500.000')
            ->assertSee('Lunas');

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-pendaftaran?periode='.$periode)
            ->assertOk()
            ->assertSee('Sistem Informasi')
            ->assertSee('Disetujui');
    }

    public function test_pimpinan_attendance_monitoring_keeps_existing_print_format(): void
    {
        $this->travelTo('2026-11-25 09:00:00');
        $pimpinan = User::factory()->create([
            'name' => 'Pimpinan Monitoring',
            'nip' => '198765432112345001',
            'role' => 'pimpinan',
            'status' => 'aktif',
        ]);

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-kehadiran?periode=2026-11')
            ->assertOk()
            ->assertDontSee('aria-label="Tanda tangan pimpinan"', false);
    }

    public function test_pimpinan_report_detail_shows_dashboard_breadcrumb_without_monitoring_badge(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-kamar?periode='.$this->periode())
            ->assertOk()
            ->assertSee('aria-label="Breadcrumb"', false)
            ->assertSee(route('pimpinan.dashboard'), false)
            ->assertSee('Laporan Data Kamar')
            ->assertDontSee('Mode Monitoring');
    }

    public function test_pimpinan_can_download_monitoring_report(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan/unduh/kehadiran?periode='.$this->periode());

        $response->assertOk();
        $response->assertDownload('laporan-kehadiran-'.$this->periode().'.csv');
    }

    public function test_mahasiswa_cannot_access_laporan_pages(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($mahasiswa)->get('/musyrifah/laporan')->assertForbidden();
        $this->actingAs($mahasiswa)->get('/pimpinan/laporan-penghuni')->assertForbidden();
        $this->actingAs($mahasiswa)->get('/pimpinan/laporan-kamar')->assertForbidden();
    }

    private function periode(): string
    {
        return now()->format('Y-m');
    }

    private function mahasiswaUji(string $nim, string $nama): User
    {
        return User::factory()->create([
            'name' => $nama,
            'nim' => $nim,
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);
    }

    private function mataPelajaranUji(string $nama, string $kode): MataPelajaran
    {
        return MataPelajaran::query()->create([
            'nama' => $nama,
            'kode' => $kode,
            'kategori' => 'Wajib',
        ]);
    }

    private function buatPendaftaranDisetujui(User $mahasiswa, string $programStudi = 'Sistem Informasi'): Pendaftaran
    {
        return Pendaftaran::query()->create([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->name,
            'nim' => $mahasiswa->nim,
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2003-01-01',
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '081234567891',
            'program_studi' => $programStudi,
            'fakultas' => 'Sains dan Teknologi',
            'semester' => 1,
            'status' => 'disetujui',
            'tahun_angkatan' => '2023',
        ]);
    }

    private function buatPenempatanAktif(User $mahasiswa, KelompokBelajar $kelompok): PenempatanKelompokBelajar
    {
        return PenempatanKelompokBelajar::query()->create([
            'user_id' => $mahasiswa->id,
            'kelompok_belajar_id' => $kelompok->id,
            'status' => 'aktif',
            'ditugaskan_pada' => now(),
            'dicabut_pada' => null,
        ]);
    }

    private function buatAbsensiEmpatStatus(User $mahasiswa, MataPelajaran $mapel, string $kelompok): void
    {
        foreach ([1 => 'hadir', 2 => 'izin', 3 => 'sakit', 16 => 'alpa'] as $pertemuan => $status) {
            $this->buatAbsensi($mahasiswa, $mapel, $kelompok, $pertemuan, $status);
        }
    }

    private function buatAbsensi(
        User $mahasiswa,
        MataPelajaran $mapel,
        string $kelompok,
        int $pertemuan,
        string $status,
    ): void {
        AbsensiPembelajaran::query()->create([
            'user_id' => $mahasiswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => $kelompok,
            'pertemuan_ke' => $pertemuan,
            'tanggal' => '2026-11-'.str_pad((string) $pertemuan, 2, '0', STR_PAD_LEFT),
            'status' => $status,
        ]);
    }

    private function buatDataLaporan(): void
    {
        $kamar = Kamar::factory()->create([
            'nomor_kamar' => 'TEST-01',
            'gedung' => 'Gedung Uji',
            'lantai' => 1,
            'kapasitas' => 4,
            'status' => 'Tersedia',
        ]);

        $mahasiswa = User::factory()->create([
            'name' => 'Nadia Putri',
            'email' => 'nadia@example.test',
            'nim' => '2317000001',
            'role' => 'mahasiswa',
            'status' => 'aktif',
            'kamar_id' => $kamar->id,
        ]);

        $mataPelajaran = MataPelajaran::create([
            'nama' => 'Akhlak dan Adab',
            'kode' => 'AKH-01',
            'kategori' => 'Wajib',
        ]);

        AbsensiPembelajaran::create([
            'user_id' => $mahasiswa->id,
            'mata_pelajaran_id' => $mataPelajaran->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => now()->startOfMonth()->addDays(2)->toDateString(),
            'status' => 'hadir',
        ]);

        Pembayaran::create([
            'user_id' => $mahasiswa->id,
            'kode_transaksi' => 'TRX-LAPORAN-001',
            'nama_tagihan' => 'Pembayaran Asrama',
            'periode' => app(LaporanMahadService::class)->labelPeriode($this->periode()),
            'nominal' => 2500000.00,
            'status' => 'lunas',
            'tanggal_bayar' => now(),
        ]);

        Pendaftaran::create([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => 'Nadia Putri',
            'nim' => '2317000001',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2003-01-01',
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '081234567890',
            'program_studi' => 'Sistem Informasi',
            'fakultas' => 'Sains dan Teknologi',
            'semester' => 1,
            'status' => 'disetujui',
            'tahun_angkatan' => '2023',
        ]);
    }
}
