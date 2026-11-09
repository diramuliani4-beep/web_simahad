<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\Kamar;
use App\Models\MataPelajaran;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\LaporanMahadService;
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

    public function test_pimpinan_can_view_laporan_monitoring_index(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/pimpinan/laporan-mahad');

        $response->assertOk();
        $response->assertSee('Mode Monitoring');
        $response->assertSee('Laporan Penghuni');
        $response->assertSee('Laporan Kamar');
        $response->assertSee('Laporan Kehadiran');
        $response->assertSee('Laporan Pembayaran');
        $response->assertSee('Laporan Pendaftaran');
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

    public function test_pimpinan_report_detail_shows_monitoring_mode(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-kamar?periode='.$this->periode())
            ->assertOk()
            ->assertSee('Mode Monitoring · Hanya Lihat');
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
        $this->actingAs($mahasiswa)->get('/pimpinan/laporan-mahad')->assertForbidden();
        $this->actingAs($mahasiswa)->get('/pimpinan/laporan-kamar')->assertForbidden();
    }

    private function periode(): string
    {
        return now()->format('Y-m');
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
