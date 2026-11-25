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
use App\Services\LaporanMahadService;
use App\Services\LaporanRegisterService;
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

    public function test_pimpinan_monitoring_routes_are_removed(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get('/pimpinan/monitoring')
            ->assertNotFound();

        foreach (['mahasiswa', 'kamar', 'pembayaran', 'pembelajaran', 'perizinan'] as $kategori) {
            $this->actingAs($pimpinan)
                ->get('/pimpinan/monitoring/'.$kategori)
                ->assertNotFound();
        }

        $this->actingAs($pimpinan)
            ->get('/pimpinan/monitoring/kamar/unduh')
            ->assertNotFound();
    }

    public function test_pimpinan_dashboard_shows_laporan_links_without_monitoring(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/pimpinan/dashboard');

        $response->assertOk();
        $response->assertSee(route('pimpinan.laporan-penghuni'), false);
        $response->assertSee(route('pimpinan.laporan-kamar'), false);
        $response->assertSee(route('pimpinan.laporan-pembayaran'), false);
        $response->assertSee(route('pimpinan.laporan-pembelajaran'), false);
        $response->assertSee(route('pimpinan.laporan-perizinan'), false);
        $response->assertDontSee('/pimpinan/laporan-mahad', false);
        $response->assertDontSee('/pimpinan/monitoring', false);
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

        $response = $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-kamar?periode='.$this->periode());

        $response->assertOk();

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

        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-kamar?periode='.$this->periode())
            ->assertOk();

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
        $this->actingAs($pimpinan)
            ->get('/pimpinan/laporan-kamar?periode='.$this->periode());

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

    public function test_pimpinan_sidebar_shows_only_dashboard_and_laporan_and_hides_monitoring(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/pimpinan/dashboard');

        $response->assertOk();
        $response->assertSee('data-menu-grup', false);
        $response->assertSee('sb-grup-toggle', false);
        $response->assertSee('aria-expanded', false);

        // Hanya menu utama Dashboard dan Laporan yang tampil pada sidebar Pimpinan.
        $response->assertSee('Dashboard');
        $response->assertSee('Laporan');
        $response->assertDontSee('Monitoring Mahasiswa');
        $response->assertDontSee('Monitoring Kamar');
        $response->assertDontSee('Monitoring Pembayaran');
        $response->assertDontSee('Monitoring Pembelajaran');
        $response->assertDontSee('Monitoring Perizinan');

        // Pengaturan Akun tidak lagi muncul di sidebar Pimpinan.
        $response->assertDontSee(route('pimpinan.pengaturan-akun'), false);

        // Tetap dapat diakses melalui dropdown profil.
        $response->assertSee(route('pengaturan-akun.index'), false);
    }

    public function test_active_submenu_keeps_parent_group_open(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/pimpinan/laporan-kamar');

        $response->assertOk();
        $response->assertSee('is-active is-open', false);
        $response->assertSee('Dashboard', false);
        $response->assertSee('Laporan', false);
        $response->assertSee('Laporan Kehadiran', false);
        $response->assertSee('Laporan Pendaftaran', false);
        $response->assertSee('Data Kamar', false);
        $response->assertSee(route('pimpinan.dashboard'), false);
        $response->assertSee(route('pimpinan.laporan-kamar'), false);
        $response->assertDontSee('/pimpinan/laporan-mahad', false);
        $response->assertDontSee('Kembali ke daftar laporan');

        // Breadcrumb halaman laporan hanya Dashboard / Nama Laporan.
        $response->assertSee('<a href="'.route('pimpinan.dashboard').'">Dashboard</a>', false);
        $response->assertDontSee('<a href="'.route('pimpinan.laporan-kamar').'">Laporan</a>', false);
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
