<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Kamar;
use App\Models\LaporanAudit;
use App\Models\LaporanMahad;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\SuratPerizinan;
use App\Models\User;
use App\Services\DashboardRingkasanService;
use App\Services\LaporanMahadService;
use App\Services\LaporanRegisterService;
use App\Services\PimpinanMonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PimpinanMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buatDataOperasional();
    }

    public function test_pimpinan_can_open_every_monitoring_page(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        foreach (['mahasiswa', 'kamar', 'pembayaran', 'pembelajaran', 'perizinan'] as $kategori) {
            $this->actingAs($pimpinan)
                ->get('/pimpinan/monitoring/'.$kategori)
                ->assertOk()
                ->assertSee('Mode Monitoring · Hanya Lihat');
        }

        $this->actingAs($pimpinan)
            ->get('/pimpinan/monitoring')
            ->assertRedirect('/pimpinan/monitoring/mahasiswa');
    }

    public function test_monitoring_uses_real_data_from_database(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get('/pimpinan/monitoring/mahasiswa')
            ->assertOk()
            ->assertSee('Nadia Putri')
            ->assertSee('2317000001');

        $this->actingAs($pimpinan)
            ->get('/pimpinan/monitoring/kamar')
            ->assertOk()
            ->assertSee('TEST-01')
            ->assertSee('Gedung Uji');

        $this->actingAs($pimpinan)
            ->get('/pimpinan/monitoring/pembayaran')
            ->assertOk()
            ->assertSee('Nadia Putri');
    }

    public function test_pimpinan_dashboard_links_to_monitoring_pages(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/pimpinan/dashboard');

        $response->assertOk();

        foreach (['mahasiswa', 'kamar', 'pembayaran', 'pembelajaran', 'perizinan'] as $kategori) {
            $response->assertSee(route('pimpinan.monitoring.'.$kategori), false);
        }
    }

    public function test_dashboard_and_monitoring_share_the_same_numbers(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        // Data tambahan agar angka ringkasan tidak kebetulan bernilai nol semua.
        Kamar::factory()->create(['nomor_kamar' => 'TEST-02', 'lantai' => 2, 'kapasitas' => 6, 'status' => 'Tersedia']);
        User::factory()->create(['role' => 'mahasiswa', 'status' => 'aktif', 'kamar_id' => null]);

        $dashboard = app(DashboardRingkasanService::class)->pimpinan();
        $monitoring = app(PimpinanMonitoringService::class)->ringkasanMaad();

        // Dashboard dan Monitoring membaca satu sumber yang sama.
        $this->assertSame($dashboard['totalMahasiswa'], $monitoring['totalMahasiswa']);
        $this->assertEquals($dashboard['kamar'], $monitoring['kamar']);
        $this->assertEquals($dashboard['pembayaran'], $monitoring['pembayaran']);
        $this->assertEquals($dashboard['pembelajaran'], $monitoring['pembelajaran']);
        $this->assertEquals($dashboard['absensi'], $monitoring['absensi']);
        $this->assertEquals($dashboard['perizinan'], $monitoring['perizinan']);

        // Angka kartu ringkasan pada halaman Monitoring berasal dari sumber tersebut.
        foreach (['mahasiswa', 'kamar', 'pembayaran', 'pembelajaran', 'perizinan'] as $kategori) {
            $data = app(PimpinanMonitoringService::class)->untuk($kategori);

            $response = $this->actingAs($pimpinan)->get('/pimpinan/monitoring/'.$kategori);
            $response->assertOk();

            foreach ($data['ringkasan'] as $kartu) {
                $response->assertSee((string) $kartu['nilai']);
            }
        }
    }

    public function test_pimpinan_cannot_use_operational_learning_routes(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        // Monitoring pembelajaran hanya membaca.
        $this->actingAs($pimpinan)
            ->get('/pimpinan/monitoring/pembelajaran')
            ->assertOk()
            ->assertDontSee('Tambah Jadwal');
    }

    public function test_monitoring_export_downloads_csv(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/pimpinan/monitoring/kamar/unduh');

        $response->assertOk();
        $response->assertDownload('monitoring-kamar-'.now()->format('Ymd').'.csv');
    }

    public function test_pimpinan_can_download_monitoring_report_for_new_categories(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-pembelajaran?periode='.$this->periode())
            ->assertOk();

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-perizinan?periode='.$this->periode())
            ->assertOk();
    }

    public function test_report_register_marks_system_reports_without_inventing_a_creator(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/pimpinan/laporan-mahad');

        $response->assertOk();
        $response->assertSee('Register Laporan');
        $response->assertSee('Rekap otomatis sistem');
        $response->assertSee('Diterbitkan');

        $this->assertSame(
            count(LaporanRegisterService::JENIS_SISTEM),
            LaporanMahad::query()->where('sumber', LaporanMahad::SUMBER_SISTEM)->count()
        );

        $this->assertSame(
            count(LaporanRegisterService::JENIS_SISTEM),
            LaporanAudit::query()->where('aksi', 'dibuat')->count()
        );
    }

    public function test_pimpinan_can_open_report_detail_and_print_view(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)->get('/pimpinan/laporan-mahad')->assertOk();

        $laporan = LaporanMahad::query()->where('jenis', 'kamar')->firstOrFail();

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan/'.$laporan->id)
            ->assertOk()
            ->assertSee('Riwayat Aktivitas')
            ->assertSee('Rekap otomatis sistem');

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan/'.$laporan->id.'/cetak')
            ->assertOk();
    }

    public function test_report_audit_history_is_append_only(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);
        $this->actingAs($pimpinan)->get('/pimpinan/laporan-mahad');

        $jejak = LaporanAudit::query()->firstOrFail();

        $this->expectException(\RuntimeException::class);
        $jejak->update(['keterangan' => 'diubah']);
    }

    public function test_audit_page_is_visible_for_pimpinan_and_blocked_for_other_roles(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get('/pimpinan/audit')
            ->assertOk()
            ->assertSee('Audit Trail SIMAHAD')
            ->assertSee('Aktivitas Sistem')
            ->assertSee('Audit Laporan');

        // Admin Sistem juga berwenang menelusuri audit trail.
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/pimpinan/audit')
            ->assertOk()
            ->assertSee('Audit Trail SIMAHAD');

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee(route('pimpinan.audit'), false);

        foreach (['mahasiswa', 'guru', 'musyrifah'] as $role) {
            $pengguna = User::factory()->create(['role' => $role]);

            $this->actingAs($pengguna)
                ->get('/pimpinan/audit')
                ->assertForbidden();
        }
    }

    public function test_musyrifah_and_mahasiswa_cannot_open_monitoring_pages(): void
    {
        foreach (['mahasiswa', 'guru', 'musyrifah'] as $role) {
            $pengguna = User::factory()->create(['role' => $role]);

            foreach (['mahasiswa', 'kamar', 'pembayaran', 'pembelajaran', 'perizinan'] as $kategori) {
                $this->actingAs($pengguna)
                    ->get('/pimpinan/monitoring/'.$kategori)
                    ->assertForbidden();
            }
        }
    }

    public function test_musyrifah_can_create_publish_and_archive_report(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah', 'name' => 'Ustadzah Salma']);

        $this->actingAs($musyrifah)
            ->post('/musyrifah/laporan', [
                'jenis' => 'kehadiran',
                'periode' => $this->periode(),
                'lantai' => 2,
                'catatan' => 'Rekap pekan kedua',
            ])
            ->assertRedirect();

        $laporan = LaporanMahad::query()
            ->where('sumber', LaporanMahad::SUMBER_MUSYRIFAH)
            ->firstOrFail();

        $this->assertSame(LaporanMahad::STATUS_DRAFT, $laporan->status);
        $this->assertSame('Ustadzah Salma', $laporan->pembuat_nama);
        $this->assertSame(2, $laporan->lantai);
        $this->assertSame('Lantai 2', $laporan->labelUnit());

        $this->actingAs($musyrifah)
            ->patch('/musyrifah/laporan/'.$laporan->id.'/terbitkan')
            ->assertRedirect();

        $laporan->refresh();
        $this->assertSame(LaporanMahad::STATUS_DITERBITKAN, $laporan->status);
        $this->assertNotNull($laporan->diterbitkan_pada);

        $this->actingAs($musyrifah)
            ->patch('/musyrifah/laporan/'.$laporan->id.'/arsipkan')
            ->assertRedirect();

        $laporan->refresh();
        $this->assertSame(LaporanMahad::STATUS_DIARSIPKAN, $laporan->status);

        $aksi = LaporanAudit::query()
            ->where('laporan_mahad_id', $laporan->id)
            ->orderBy('id')
            ->pluck('aksi')
            ->all();

        $this->assertSame(['dibuat', 'diterbitkan', 'diarsipkan'], $aksi);
    }

    public function test_pimpinan_cannot_publish_or_archive_musyrifah_report(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($musyrifah)->post('/musyrifah/laporan', [
            'jenis' => 'kamar',
            'periode' => $this->periode(),
            'lantai' => 1,
        ]);

        $laporan = LaporanMahad::query()
            ->where('sumber', LaporanMahad::SUMBER_MUSYRIFAH)
            ->firstOrFail();

        $this->actingAs($pimpinan)
            ->patch('/musyrifah/laporan/'.$laporan->id.'/terbitkan')
            ->assertForbidden();

        $this->assertSame(LaporanMahad::STATUS_DRAFT, $laporan->fresh()->status);
    }

    public function test_payment_status_change_records_audit_trail(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah', 'name' => 'Ustadzah Rina']);
        $pembayaran = Pembayaran::query()->firstOrFail();

        $this->actingAs($musyrifah)
            ->postJson('/musyrifah/pembayaran/'.$pembayaran->id.'/status', ['status' => 'lunas'])
            ->assertOk();

        $log = ActivityLog::query()
            ->where('modul', 'pembayaran')
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('musyrifah', $log->user_role);
        $this->assertSame('Ustadzah Rina', $log->user_name);
        $this->assertSame('belum_lunas', $log->data_sebelum['status'] ?? null);
        $this->assertSame('lunas', $log->data_sesudah['status'] ?? null);
    }

    public function test_mahasiswa_cannot_change_payment_status(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $pembayaran = Pembayaran::query()->firstOrFail();

        $this->actingAs($mahasiswa)
            ->postJson('/musyrifah/pembayaran/'.$pembayaran->id.'/status', ['status' => 'lunas'])
            ->assertForbidden();
    }

    public function test_permit_decision_records_audit_trail(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah', 'name' => 'Ustadzah Nur']);
        $surat = SuratPerizinan::factory()->create([
            'status' => SuratPerizinan::STATUS_MENUNGGU,
            'user_id' => User::factory()->create(['role' => 'mahasiswa'])->id,
        ]);

        $this->actingAs($musyrifah)
            ->post('/musyrifah/surat-perizinan/'.$surat->id.'/keputusan', [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
            ])
            ->assertRedirect();

        $log = ActivityLog::query()
            ->where('modul', 'perizinan')
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('musyrifah', $log->user_role);
        $this->assertSame(SuratPerizinan::STATUS_MENUNGGU, $log->data_sebelum['status'] ?? null);
        $this->assertSame(SuratPerizinan::STATUS_DISETUJUI, $log->data_sesudah['status'] ?? null);
    }

    public function test_pimpinan_sidebar_uses_click_expand_collapse_and_hides_pengaturan_akun(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/pimpinan/dashboard');

        $response->assertOk();
        $response->assertSee('data-menu-grup', false);
        $response->assertSee('menu-grup-toggle', false);
        $response->assertSee('menu-chevron', false);
        $response->assertSee('aria-expanded', false);

        // Menu induk Monitoring dan Laporan harus ada pada sidebar.
        $response->assertSee('Monitoring');
        $response->assertSee('Laporan');

        // Pengaturan Akun tidak lagi muncul di sidebar Pimpinan.
        $response->assertDontSee(route('pimpinan.pengaturan-akun'), false);

        // Tetap dapat diakses melalui dropdown profil.
        $response->assertSee(route('pengaturan-akun.index'), false);
    }

    public function test_active_submenu_keeps_parent_group_open(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/pimpinan/monitoring/kamar');

        $response->assertOk();
        $response->assertSee('is-active is-open', false);
    }

    private function periode(): string
    {
        return now()->format('Y-m');
    }

    private function buatDataOperasional(): void
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

        Pembayaran::create([
            'user_id' => $mahasiswa->id,
            'kode_transaksi' => 'TRX-MONITOR-001',
            'nama_tagihan' => 'Pembayaran Asrama',
            'periode' => app(LaporanMahadService::class)->labelPeriode($this->periode()),
            'nominal' => 2500000.00,
            'status' => 'belum_lunas',
        ]);
    }
}
