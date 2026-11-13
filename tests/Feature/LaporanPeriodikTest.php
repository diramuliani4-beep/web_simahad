<?php

namespace Tests\Feature;

use App\Models\LaporanMahad;
use App\Models\Pembayaran;
use App\Models\Pengumuman;
use App\Models\User;
use App\Services\LaporanPeriodikService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LaporanPeriodikTest extends TestCase
{
    use RefreshDatabase;

    private User $musyrifah;

    private User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(CarbonImmutable::create(2026, 6, 15, 9, 0, 0));

        $this->musyrifah = User::factory()->create([
            'name' => 'Musyrifah Uji',
            'role' => 'musyrifah',
        ]);

        $this->mahasiswa = User::factory()->create([
            'name' => 'Nadia Putri',
            'nim' => '2317000001',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);

        $this->buatPembayaran('Tagihan Juni', 'Juni 2026', 1000000.0, '2026-06-10 08:00:00', '2026-06-12 09:00:00');
        $this->buatPembayaran('Tagihan Mei', 'Mei 2026', 2000000.0, '2026-05-10 08:00:00', '2026-05-12 09:00:00');
        $this->buatPembayaran('Tagihan Lintas Bulan', 'Mei 2026', 500000.0, '2026-05-20 08:00:00', '2026-06-05 09:00:00');

        $this->buatKegiatan('Kajian Rutin Juni', '2026-06-05');
        $this->buatKegiatan('Kerja Bakti Mei', '2026-05-15');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_musyrifah_can_open_buat_laporan_page_with_monthly_and_annual_options(): void
    {
        $this->actingAs($this->musyrifah)
            ->get('/musyrifah/laporan/buat')
            ->assertOk()
            ->assertSee('Laporan Bulanan')
            ->assertSee('Laporan Tahunan')
            ->assertSee('Bulan laporan')
            ->assertSee('Tahun laporan');
    }

    public function test_monthly_preview_only_uses_the_selected_month(): void
    {
        $this->actingAs($this->musyrifah)
            ->post('/musyrifah/laporan/buat/pratinjau', [
                'jenis' => 'bulanan',
                'tahun' => 2026,
                'bulan' => 6,
                'evaluasi' => 'Evaluasi khusus Juni',
            ])
            ->assertOk()
            ->assertSee('Juni 2026')
            ->assertSee('Tagihan Juni')
            ->assertSee('Tagihan Lintas Bulan')
            ->assertDontSee('Tagihan Mei')
            ->assertSee('Kajian Rutin Juni')
            ->assertDontSee('Kerja Bakti Mei')
            ->assertSee('Evaluasi khusus Juni');
    }

    public function test_annual_preview_combines_all_months_in_the_selected_year(): void
    {
        $this->actingAs($this->musyrifah)
            ->post('/musyrifah/laporan/buat/pratinjau', [
                'jenis' => 'tahunan',
                'tahun' => 2026,
                'bulan' => 6,
                'evaluasi' => 'Evaluasi tahunan',
            ])
            ->assertOk()
            ->assertSee('Tahun 2026')
            ->assertSee('Rekap Pembayaran Selama Satu Tahun')
            ->assertSee('Kajian Rutin Juni')
            ->assertSee('Kerja Bakti Mei')
            ->assertSee('Evaluasi tahunan');
    }

    public function test_annual_recap_counts_each_operational_record_once(): void
    {
        $periodik = app(LaporanPeriodikService::class);

        $tahunan = $periodik->susun('tahunan', 2026, 6);
        $bulanan = $periodik->susun('bulanan', 2026, 6);

        $seksiTahunan = collect($tahunan['seksi'])->firstWhere('kode', 'pembayaran');
        $seksiBulanan = collect($bulanan['seksi'])->firstWhere('kode', 'pembayaran');

        $this->assertSame(3, collect($seksiTahunan['ringkasan'])->firstWhere('label', 'Jumlah Tagihan')['nilai']);
        $this->assertSame(2, collect($seksiBulanan['ringkasan'])->firstWhere('label', 'Jumlah Tagihan')['nilai']);
    }

    public function test_saving_monthly_report_stores_period_and_immutable_snapshot(): void
    {
        $this->actingAs($this->musyrifah)
            ->post('/musyrifah/laporan/buat', [
                'jenis' => 'bulanan',
                'tahun' => 2026,
                'bulan' => 6,
                'evaluasi' => 'Evaluasi Juni',
                'kendala' => 'Kendala Juni',
                'catatan' => 'Dokumen bulanan',
            ])
            ->assertRedirect();

        $laporan = LaporanMahad::query()->firstOrFail();

        $this->assertSame('bulanan', $laporan->jenis);
        $this->assertSame('2026-06', $laporan->periode);
        $this->assertSame(2026, $laporan->tahun);
        $this->assertSame(6, $laporan->bulan);
        $this->assertSame(LaporanMahad::STATUS_DRAFT, $laporan->status);
        $this->assertSame(1, $laporan->versi);
        $this->assertIsArray($laporan->konten);
        $this->assertNotEmpty($laporan->konten['seksi']);

        // Data operasional baru tidak boleh mengubah isi laporan yang sudah tersimpan.
        $this->buatPembayaran('Tagihan Setelah Laporan', 'Juni 2026', 900000.0, '2026-06-20 08:00:00', '2026-06-21 09:00:00');

        $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.laporan.periodik', $laporan))
            ->assertOk()
            ->assertSee('Tagihan Juni')
            ->assertDontSee('Tagihan Setelah Laporan');

        $this->assertSame(1, $laporan->fresh()->versi);
    }

    public function test_saving_annual_report_uses_year_only_period(): void
    {
        $this->actingAs($this->musyrifah)
            ->post('/musyrifah/laporan/buat', [
                'jenis' => 'tahunan',
                'tahun' => 2026,
                'bulan' => 6,
            ])
            ->assertRedirect();

        $laporan = LaporanMahad::query()->firstOrFail();

        $this->assertSame('tahunan', $laporan->jenis);
        $this->assertSame('2026', $laporan->periode);
        $this->assertSame(2026, $laporan->tahun);
        $this->assertNull($laporan->bulan);
    }

    public function test_saved_report_can_be_downloaded_as_pdf_and_excel(): void
    {
        $laporan = $this->simpanLaporan('bulanan', 2026, 6);

        $pdf = $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.laporan.periodik.pdf', $laporan));

        $pdf->assertOk();
        $pdf->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', (string) $pdf->getContent());

        $excel = $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.laporan.periodik.excel', $laporan));

        $excel->assertOk();
        $excel->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringStartsWith('PK', (string) $excel->getContent());
    }

    public function test_saved_report_can_be_opened_in_print_view(): void
    {
        $laporan = $this->simpanLaporan('tahunan', 2026, 6);

        $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.laporan.periodik.cetak', $laporan))
            ->assertOk()
            ->assertSee('Cetak Dokumen')
            ->assertSee('Tahun 2026')
            ->assertSee('Keterbatasan Data');
    }

    public function test_monthly_report_requires_month_selection(): void
    {
        $response = $this->actingAs($this->musyrifah)
            ->from('/musyrifah/laporan/buat')
            ->post('/musyrifah/laporan/buat', [
                'jenis' => 'bulanan',
                'tahun' => 2026,
            ]);

        $response->assertRedirect('/musyrifah/laporan/buat');
        $response->assertSessionHasErrors('bulan');
        $this->assertSame(0, LaporanMahad::query()->count());
    }

    public function test_regenerating_report_increments_version_and_records_audit(): void
    {
        $laporan = $this->simpanLaporan('bulanan', 2026, 6);

        $this->buatPembayaran('Tagihan Tambahan', 'Juni 2026', 750000.0, '2026-06-22 08:00:00', '2026-06-23 09:00:00');

        $this->actingAs($this->musyrifah)
            ->patch(route('musyrifah.laporan.periodik.perbarui', $laporan), [
                'evaluasi' => 'Evaluasi diperbarui',
                'kendala' => 'Kendala diperbarui',
            ])
            ->assertRedirect(route('musyrifah.laporan.periodik', $laporan));

        $laporan->refresh();

        $this->assertSame(2, $laporan->versi);
        $this->assertSame('Evaluasi diperbarui', $laporan->konten['catatan']['evaluasi']);
        $this->assertSame('Kendala diperbarui', $laporan->konten['catatan']['kendala']);

        $this->assertDatabaseHas('laporan_audits', [
            'laporan_mahad_id' => $laporan->id,
            'aksi' => 'diperbarui',
            'versi' => 2,
        ]);

        $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.laporan.periodik', $laporan))
            ->assertOk()
            ->assertSee('Tagihan Tambahan');
    }

    public function test_archived_report_cannot_be_regenerated(): void
    {
        $laporan = $this->simpanLaporan('bulanan', 2026, 6);

        $laporan->status = LaporanMahad::STATUS_DIARSIPKAN;
        $laporan->diarsipkan_pada = now();
        $laporan->save();

        $this->actingAs($this->musyrifah)
            ->patch(route('musyrifah.laporan.periodik.perbarui', $laporan), [
                'evaluasi' => 'Tidak boleh tersimpan',
            ])
            ->assertForbidden();

        $this->assertSame(1, $laporan->fresh()->versi);
    }

    public function test_pimpinan_can_view_and_download_periodic_report_read_only(): void
    {
        $laporan = $this->simpanLaporan('bulanan', 2026, 6);
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get(route('pimpinan.laporan.detail', $laporan))
            ->assertOk()
            ->assertSee('Identitas Laporan')
            ->assertSee('Tagihan Juni')
            ->assertSee('Dokumen Laporan Periodik');

        $this->actingAs($pimpinan)
            ->get(route('pimpinan.laporan.pdf', $laporan))
            ->assertOk();

        $this->actingAs($pimpinan)
            ->get(route('pimpinan.laporan.excel', $laporan))
            ->assertOk();

        $this->actingAs($pimpinan)
            ->get(route('pimpinan.laporan.cetak', $laporan))
            ->assertOk()
            ->assertSee('Juni 2026');

        // Pimpinan tidak boleh membuka area kerja Musyrifah.
        $this->actingAs($pimpinan)
            ->get(route('musyrifah.laporan.periodik', $laporan))
            ->assertForbidden();
    }

    public function test_mahasiswa_cannot_access_periodic_report_flows(): void
    {
        $laporan = $this->simpanLaporan('bulanan', 2026, 6);
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($mahasiswa)->get('/musyrifah/laporan/buat')->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('musyrifah.laporan.periodik', $laporan))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('musyrifah.laporan.periodik.pdf', $laporan))->assertForbidden();

        $this->actingAs($mahasiswa)
            ->post('/musyrifah/laporan/buat', [
                'jenis' => 'tahunan',
                'tahun' => 2026,
            ])
            ->assertForbidden();
    }

    public function test_history_can_be_filtered_by_report_type(): void
    {
        $bulanan = $this->simpanLaporan('bulanan', 2026, 6);
        $tahunan = $this->simpanLaporan('tahunan', 2026, 6);

        $response = $this->actingAs($this->musyrifah)->get('/musyrifah/laporan?p_jenis=tahunan');

        $response->assertOk();

        $daftar = $response->viewData('daftarPeriodik');

        $this->assertSame([$tahunan->id], $daftar->pluck('id')->all());
        $this->assertNotContains($bulanan->id, $daftar->pluck('id')->all());
    }

    private function simpanLaporan(string $jenis, int $tahun, int $bulan): LaporanMahad
    {
        $this->actingAs($this->musyrifah)
            ->post('/musyrifah/laporan/buat', [
                'jenis' => $jenis,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'evaluasi' => 'Evaluasi '.$jenis,
                'kendala' => 'Kendala '.$jenis,
            ])
            ->assertRedirect();

        return LaporanMahad::query()->latest('id')->firstOrFail();
    }

    private function buatKegiatan(string $judul, string $tanggal): void
    {
        Pengumuman::create([
            'user_id' => $this->musyrifah->id,
            'penulis_nama' => $this->musyrifah->name,
            'judul' => $judul,
            'kategori' => Pengumuman::KATEGORI_KEGIATAN,
            'isi' => 'Kegiatan Ma’had untuk pengujian laporan periodik.',
            'tanggal_publikasi' => $tanggal,
            'status' => Pengumuman::STATUS_DIPUBLIKASIKAN,
            'dipublikasikan_pada' => $tanggal.' 08:00:00',
        ]);
    }

    private function buatPembayaran(
        string $nama,
        string $periode,
        float $nominal,
        string $dibuat,
        string $dibayar,
    ): Pembayaran {
        $pembayaran = Pembayaran::create([
            'user_id' => $this->mahasiswa->id,
            'kode_transaksi' => 'TRX-'.str()->slug($nama),
            'nama_tagihan' => $nama,
            'periode' => $periode,
            'nominal' => $nominal,
            'status' => 'lunas',
            'tanggal_bayar' => $dibayar,
        ]);

        $pembayaran->created_at = CarbonImmutable::parse($dibuat);
        $pembayaran->updated_at = CarbonImmutable::parse($dibuat);
        $pembayaran->save();

        return $pembayaran;
    }
}
