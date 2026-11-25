<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\AngsuranPembayaran;
use App\Models\Kamar;
use App\Models\Notifikasi;
use App\Models\Pegawai;
use App\Models\PegawaiAkun;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeterlambatanPembayaran;
use App\Models\PengaturanPembayaran;
use App\Models\User;
use App\Services\PembayaranAngsuranService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MusyrifahPembayaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_musyrifah_pembayaran(): void
    {
        $this->get('/musyrifah/pembayaran')->assertRedirect(route('login'));
    }

    public function test_musyrifah_can_access_page_and_see_student_payments(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();

        Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'kode_transaksi' => 'TRX-TEST-001',
            'nama_tagihan' => 'Tahap 1',
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->actingAs($musyrifah)
            ->get('/musyrifah/pembayaran')
            ->assertOk()
            ->assertSee('Pembayaran Mahasiswa')
            ->assertSee('Zaidan Mahasiswa')
            ->assertSee('2317990011')
            ->assertSee('Tahap 1')
            ->assertSee('Belum Lunas');
    }

    public function test_mahasiswa_tidak_boleh_mengakses_endpoint_musyrifah(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();

        $tagihan = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->actingAs($mahasiswa)->get('/musyrifah/pembayaran')->assertForbidden();
        $this->actingAs($mahasiswa)->getJson('/musyrifah/pembayaran/data')->assertForbidden();
        $this->actingAs($mahasiswa)
            ->getJson("/musyrifah/pembayaran/{$tagihan->id}/detail")
            ->assertForbidden();
        $this->actingAs($mahasiswa)
            ->postJson("/musyrifah/pembayaran/{$tagihan->id}/status", ['status' => 'lunas'])
            ->assertForbidden();
        $this->actingAs($mahasiswa)
            ->postJson("/musyrifah/pembayaran/{$tagihan->id}/verifikasi", ['keputusan' => 'terima'])
            ->assertForbidden();
        $this->actingAs($mahasiswa)
            ->postJson("/musyrifah/pembayaran/{$tagihan->id}/nominal", ['nominal' => 1000000])
            ->assertForbidden();
    }

    public function test_detail_endpoint_returns_payment_and_activity_logs(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna(['name' => 'Aulia Zahra', 'nim' => '2317020888']);

        $pembayaran = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'kode_transaksi' => 'TRX-TEST-888',
            'nama_tagihan' => 'Tahap 1',
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_LUNAS,
            'metode_pembayaran' => 'BRIVA',
            'tanggal_bayar' => now(),
        ]);

        ActivityLog::query()->create([
            'user_id' => $mahasiswa->id,
            'user_name' => $mahasiswa->name,
            'user_email' => $mahasiswa->email,
            'user_role' => 'mahasiswa',
            'activity' => 'Pembayaran Tagihan',
            'description' => 'Mahasiswa Aulia Zahra berhasil membayar via BRIVA',
        ]);

        $response = $this->actingAs($musyrifah)
            ->getJson("/musyrifah/pembayaran/{$pembayaran->id}/detail");

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('mahasiswa.name', 'Aulia Zahra');
        $response->assertJsonPath('pembayaran.status_label', 'Lunas');
        $response->assertJsonPath('pembayaran.metode_pembayaran', 'BRIVA');
        $response->assertJsonPath('riwayat_angsuran.0.id', $pembayaran->id);

        $logs = $response->json('riwayat_aktivitas');
        $this->assertNotEmpty($logs);
        $this->assertEquals('Pembayaran Tagihan', $logs[0]['activity']);
    }

    public function test_lunas_tidak_dapat_ditetapkan_saat_bukti_belum_diperiksa(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();

        $pembayaran = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
            'status_verifikasi' => Pembayaran::VERIFIKASI_MENUNGGU,
            'bukti_pembayaran' => 'pembayaran/bukti.jpg',
        ]);

        $this->actingAs($musyrifah)
            ->postJson("/musyrifah/pembayaran/{$pembayaran->id}/status", ['status' => 'lunas'])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
        ]);
    }

    public function test_musyrifah_dapat_menetapkan_lunas_setelah_konfirmasi_rektorat(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();

        $pembayaran = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_MENUNGGU_REKTORAT,
            'status_verifikasi' => Pembayaran::VERIFIKASI_DITERIMA,
            'bukti_pembayaran' => 'pembayaran/bukti.jpg',
        ]);

        $this->actingAs($musyrifah)
            ->postJson("/musyrifah/pembayaran/{$pembayaran->id}/status", [
                'status' => 'lunas',
                'catatan' => 'Dana sudah masuk rekening kampus sesuai mutasi BSI',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'status' => Pembayaran::STATUS_LUNAS,
        ]);
        $this->assertNotNull($pembayaran->fresh()->tanggal_konfirmasi_rektorat);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $mahasiswa->id,
            'activity' => 'Verifikasi Pembayaran',
        ]);
    }

    public function test_musyrifah_menerima_bukti_tanpa_langsung_menjadi_lunas(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();

        $pembayaran = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
            'status_verifikasi' => Pembayaran::VERIFIKASI_MENUNGGU,
            'bukti_pembayaran' => 'pembayaran/bukti.jpg',
        ]);

        $this->actingAs($musyrifah)
            ->postJson("/musyrifah/pembayaran/{$pembayaran->id}/verifikasi", ['keputusan' => 'terima'])
            ->assertOk()
            ->assertJsonPath('success', true);

        $pembayaran->refresh();

        $this->assertSame(Pembayaran::STATUS_MENUNGGU_REKTORAT, $pembayaran->status);
        $this->assertSame(Pembayaran::VERIFIKASI_DITERIMA, $pembayaran->status_verifikasi);
        $this->assertSame($musyrifah->id, $pembayaran->diverifikasi_oleh);
        $this->assertNull($pembayaran->tanggal_bayar);
        $this->assertNotNull($pembayaran->tanggal_verifikasi);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $mahasiswa->id,
            'activity' => 'Verifikasi Bukti Pembayaran',
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_PEMBAYARAN,
        ]);
    }

    public function test_musyrifah_menolak_bukti_wajib_menyertakan_alasan(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();

        $pembayaran = $this->buktiMenunggu($mahasiswa);

        $this->actingAs($musyrifah)
            ->postJson("/musyrifah/pembayaran/{$pembayaran->id}/verifikasi", ['keputusan' => 'tolak'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('catatan');

        $this->actingAs($musyrifah)
            ->postJson("/musyrifah/pembayaran/{$pembayaran->id}/verifikasi", [
                'keputusan' => 'tolak',
                'catatan' => 'Nominal transfer tidak sesuai tagihan.',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
            'status_verifikasi' => Pembayaran::VERIFIKASI_DITOLAK,
            'catatan_verifikasi' => 'Nominal transfer tidak sesuai tagihan.',
        ]);
    }

    public function test_musyrifah_mengatur_jadwal_angsuran_dan_nominal_tagihan(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();
        $pengaturan = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);

        $tagihan = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => $pengaturan->periode,
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.jadwal'), [
                'pengaturan_id' => $pengaturan->id,
                'tanggal_mulai' => now()->addDay()->toDateString(),
                'angsuran' => [
                    [
                        'angsuran_ke' => 1,
                        'nama_tahap' => 'Tahap 1',
                        'nominal' => 1500000,
                        'tanggal_mulai' => now()->addDay()->toDateString(),
                        'jatuh_tempo' => now()->addDays(20)->toDateString(),
                        'reminder_hari' => 7,
                    ],
                    [
                        'angsuran_ke' => 2,
                        'nama_tahap' => 'Tahap 2',
                        'nominal' => 1750000,
                        'tanggal_mulai' => now()->addDays(31)->toDateString(),
                        'jatuh_tempo' => now()->addDays(80)->toDateString(),
                        'reminder_hari' => 5,
                    ],
                    [
                        'angsuran_ke' => 3,
                        'nama_tahap' => 'Tahap 3',
                        'nominal' => 1750000,
                        'tanggal_mulai' => now()->addDays(91)->toDateString(),
                        'jatuh_tempo' => now()->addDays(140)->toDateString(),
                        'reminder_hari' => 3,
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('angsuran_pembayarans', [
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1500000,
        ]);
        $this->assertDatabaseHas('angsuran_pembayarans', [
            'periode' => 'Januari 2027',
            'angsuran_ke' => 2,
            'nominal' => 1750000,
            'reminder_hari' => 5,
        ]);
        $this->assertDatabaseHas('pembayarans', [
            'id' => $tagihan->id,
            'nominal' => 1500000,
        ]);
        $this->assertSame(
            now()->addDay()->toDateString(),
            $pengaturan->fresh()->periode_dibuka_pada->toDateString()
        );
    }

    public function test_angsuran_pertama_di_bawah_satu_juta_ditolak(): void
    {
        [$musyrifah] = $this->pengguna();
        PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);

        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.jadwal'), [
                'angsuran' => [
                    [
                        'angsuran_ke' => 1,
                        'nama_tahap' => 'Tahap 1',
                        'nominal' => 500000,
                        'jatuh_tempo' => now()->addDays(20)->toDateString(),
                        'reminder_hari' => 7,
                    ],
                ],
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_musyrifah_dapat_membuat_periode_pembayaran(): void
    {
        [$musyrifah] = $this->pengguna();

        $periodeLama = PengaturanPembayaran::factory()->create([
            'periode' => 'Januari 2027',
            'is_aktif' => true,
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pembayaran.periode.simpan'), [
                'periode' => 'Februari 2027',
                'tanggal_mulai' => now()->toDateString(),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pengaturan_pembayarans', [
            'periode' => 'Februari 2027',
            'is_aktif' => true,
        ]);
        $this->assertDatabaseHas('pengaturan_pembayarans', [
            'id' => $periodeLama->id,
            'is_aktif' => false,
        ]);
        $this->assertDatabaseCount('angsuran_pembayarans', 3);
        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'Membuat Periode Pembayaran',
        ]);
    }

    public function test_musyrifah_dapat_menutup_dan_membuka_periode_pembayaran(): void
    {
        [$musyrifah] = $this->pengguna();

        $periode = PengaturanPembayaran::factory()->create([
            'periode' => 'Januari 2027',
            'periode_dibuka_pada' => now()->subDays(2)->toDateString(),
            'is_aktif' => true,
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pembayaran.periode.tutup', $periode->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pengaturan_pembayarans', [
            'id' => $periode->id,
            'is_aktif' => false,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'Menutup Periode Pembayaran',
        ]);

        // Periode terjadwal di masa depan dibuka sekarang juga oleh Musyrifah.
        $periode->update(['periode_dibuka_pada' => now()->addDays(5)->toDateString()]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pembayaran.periode.buka', $periode->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $periode->refresh();

        $this->assertTrue($periode->is_aktif);
        $this->assertSame(now()->toDateString(), $periode->periode_dibuka_pada->toDateString());
        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'Membuka Periode Pembayaran',
        ]);
    }

    public function test_admin_dan_mahasiswa_tidak_dapat_mengatur_periode_pembayaran(): void
    {
        $periode = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);

        $admin = User::factory()->create(['role' => 'admin']);
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        foreach ([$admin, $mahasiswa] as $pengguna) {
            $this->actingAs($pengguna)
                ->post(route('musyrifah.pembayaran.periode.simpan'), [
                    'periode' => 'Maret 2027',
                    'tanggal_mulai' => now()->toDateString(),
                ])
                ->assertForbidden();

            $this->actingAs($pengguna)
                ->post(route('musyrifah.pembayaran.periode.buka', $periode->id))
                ->assertForbidden();

            $this->actingAs($pengguna)
                ->post(route('musyrifah.pembayaran.periode.tutup', $periode->id))
                ->assertForbidden();

            $this->actingAs($pengguna)
                ->post(route('musyrifah.pembayaran.jadwal'), [
                    'angsuran' => [
                        [
                            'angsuran_ke' => 1,
                            'nominal' => 1000000,
                            'jatuh_tempo' => now()->addDays(20)->toDateString(),
                            'reminder_hari' => 7,
                        ],
                    ],
                ])
                ->assertForbidden();
        }

        $this->assertDatabaseCount('pengaturan_pembayarans', 1);
        $this->assertDatabaseMissing('pengaturan_pembayarans', ['periode' => 'Maret 2027']);
    }

    public function test_persetujuan_keterlambatan_memperbarui_jatuh_tempo_tanpa_menjadi_lunas(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();

        $tagihan = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'jatuh_tempo' => now()->addDays(3)->toDateString(),
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $rencanaBayar = now()->addDays(21)->toDateString();

        $pengajuan = PengajuanKeterlambatanPembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'pembayaran_id' => $tagihan->id,
            'nomor_surat' => 'SKP/TEST/0001-01/MAHAD',
            'alasan' => 'Dana kiriman orang tua baru tersedia akhir bulan.',
            'tanggal_rencana_bayar' => $rencanaBayar,
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);

        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.keterlambatan', $pengajuan->id), [
                'keputusan' => 'setuju',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pengajuan_keterlambatan_pembayarans', [
            'id' => $pengajuan->id,
            'status' => PengajuanKeterlambatanPembayaran::STATUS_DISETUJUI,
            'diproses_oleh' => $musyrifah->id,
        ]);
        $this->assertDatabaseHas('pembayarans', [
            'id' => $tagihan->id,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);
        $this->assertSame($rencanaBayar, $tagihan->fresh()->jatuh_tempo->toDateString());
    }

    public function test_pengajuan_keterlambatan_ditolak_wajib_menyertakan_alasan(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();

        $tagihan = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $pengajuan = PengajuanKeterlambatanPembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'pembayaran_id' => $tagihan->id,
            'nomor_surat' => 'SKP/TEST/0001-02/MAHAD',
            'alasan' => 'Dana belum tersedia.',
            'tanggal_rencana_bayar' => now()->addDays(21)->toDateString(),
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);

        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.keterlambatan', $pengajuan->id), [
                'keputusan' => 'tolak',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('catatan');

        $this->assertDatabaseHas('pengajuan_keterlambatan_pembayarans', [
            'id' => $pengajuan->id,
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);
    }

    public function test_pengingat_dikirim_sebelum_jatuh_tempo_dan_tidak_berulang_di_hari_yang_sama(): void
    {
        Mail::fake();

        [, $mahasiswa] = $this->pengguna();

        $pengaturan = PengaturanPembayaran::factory()->create([
            'periode' => 'Januari 2027',
            'is_aktif' => true,
        ]);
        $this->bukaTahapPertama($pengaturan);

        $tagihan = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'jatuh_tempo' => now()->addDays(2)->toDateString(),
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->artisan('pembayaran:pengingat')->assertExitCode(0);

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_PEMBAYARAN,
        ]);
        $this->assertNotNull($tagihan->fresh()->reminder_terakhir_pada);

        $jumlahNotifikasi = Notifikasi::query()->where('user_id', $mahasiswa->id)->count();

        $this->artisan('pembayaran:pengingat')->assertExitCode(0);

        $this->assertSame(
            $jumlahNotifikasi,
            Notifikasi::query()->where('user_id', $mahasiswa->id)->count()
        );
    }

    public function test_pengingat_tidak_dikirim_saat_periode_ditutup(): void
    {
        Mail::fake();

        [, $mahasiswa] = $this->pengguna();

        PengaturanPembayaran::factory()->create([
            'periode' => 'Januari 2027',
            'is_aktif' => false,
        ]);

        $tagihan = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'jatuh_tempo' => now()->addDays(2)->toDateString(),
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->artisan('pembayaran:pengingat')->assertExitCode(0);

        $this->assertNull($tagihan->fresh()->reminder_terakhir_pada);
        $this->assertDatabaseMissing('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_PEMBAYARAN,
        ]);
    }

    #[DataProvider('jumlahPembayaran')]
    public function test_mahasiswa_dapat_melunasi_dalam_satu_dua_atau_tiga_pembayaran(int $jumlahPembayaran): void
    {
        $this->freezeTime();
        Storage::fake('local');

        [$musyrifah, $mahasiswa] = $this->pengguna();
        $pengaturan = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);

        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();

        AngsuranPembayaran::query()
            ->where('periode', $pengaturan->periode)
            ->update(['tanggal_mulai' => now()->subDay()->toDateString()]);

        $tagihan = Pembayaran::query()
            ->where('user_id', $mahasiswa->id)
            ->where('periode', $pengaturan->periode)
            ->get()
            ->keyBy('angsuran_ke');
        $nominalTahap = [1 => 1000000.0, 2 => 2000000.0, 3 => 2000000.0];
        $strategi = match ($jumlahPembayaran) {
            1 => ['semua'],
            2 => ['satu', 'semua'],
            default => ['satu', 'satu', 'satu'],
        };
        $layanan = app(PembayaranAngsuranService::class);
        $dibayar = 0.0;

        foreach ($strategi as $indeks => $caraBayar) {
            $tahap = $indeks + 1;
            $item = $tagihan->get($tahap);
            $this->assertNotNull($item);

            $this->actingAs($musyrifah)
                ->post(route('musyrifah.pembayaran.tahap.buka', $item->jadwalAngsuran()->id))
                ->assertSessionHasNoErrors();

            $bayarSemua = $caraBayar === 'semua';
            $this->actingAs($mahasiswa)
                ->post(route('pembayaran.bukti', $item->id), [
                    'bukti' => UploadedFile::fake()->image('bukti-tahap-'.$tahap.'.jpg'),
                    'bayar_semua' => $bayarSemua ? '1' : '0',
                ])
                ->assertSessionHasNoErrors();

            $cakupan = $bayarSemua ? 3 - $indeks : 1;
            $nominalMenunggu = array_sum(array_slice($nominalTahap, $indeks, $cakupan, true));
            $belumDibayar = 5000000.0 - $dibayar - $nominalMenunggu;
            $ringkasan = $layanan->ringkasan($mahasiswa);

            $this->assertSame(5000000.0, $ringkasan['total']);
            $this->assertSame($dibayar, $ringkasan['dibayar']);
            $this->assertSame($nominalMenunggu, $ringkasan['menunggu']);
            $this->assertSame(5000000.0 - $dibayar, $ringkasan['sisa']);
            $this->assertSame($belumDibayar, $ringkasan['belum_dibayar']);
            $this->assertSame(
                $cakupan,
                Pembayaran::query()
                    ->where('user_id', $mahasiswa->id)
                    ->where('status', Pembayaran::STATUS_MENUNGGU_VERIFIKASI)
                    ->count()
            );

            $this->actingAs($musyrifah)
                ->postJson(route('musyrifah.pembayaran.verifikasi', $item->id), ['keputusan' => 'terima'])
                ->assertOk()
                ->assertJsonPath('success', true);

            $ringkasan = $layanan->ringkasan($mahasiswa);
            $this->assertSame($dibayar, $ringkasan['dibayar']);
            $this->assertSame($nominalMenunggu, $ringkasan['menunggu']);
            $this->assertSame($belumDibayar, $ringkasan['belum_dibayar']);

            $this->actingAs($musyrifah)
                ->postJson(route('musyrifah.pembayaran.status', $item->id), [
                    'status' => Pembayaran::STATUS_LUNAS,
                    'catatan' => 'Dana diterima sesuai rekening kampus.',
                ])
                ->assertOk()
                ->assertJsonPath('success', true);

            $dibayar += $nominalMenunggu;
            $ringkasan = $layanan->ringkasan($mahasiswa);
            $this->assertSame($dibayar, $ringkasan['dibayar']);
            $this->assertSame(0.0, $ringkasan['menunggu']);
            $this->assertSame(5000000.0 - $dibayar, $ringkasan['sisa']);
            $this->assertSame(5000000.0 - $dibayar, $ringkasan['belum_dibayar']);
        }

        $ringkasan = $layanan->ringkasan($mahasiswa);
        $this->assertTrue($ringkasan['lunas']);
        $this->assertSame(5000000.0, $ringkasan['dibayar']);
        $this->assertSame(0.0, $ringkasan['sisa']);
        $this->assertSame(0.0, $ringkasan['belum_dibayar']);
        $this->assertSame(3, Pembayaran::query()->where('user_id', $mahasiswa->id)->where('status', Pembayaran::STATUS_LUNAS)->count());

        $tahapKedua = $tagihan->get(2);
        $this->assertNotNull($tahapKedua);
        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pembayaran.tahap.buka', $tahapKedua->jadwalAngsuran()->id))
            ->assertSessionHasNoErrors();
        $this->actingAs($mahasiswa)
            ->post(route('pembayaran.bukti', $tahapKedua->id), [
                'bukti' => UploadedFile::fake()->image('bukti-setelah-lunas.jpg'),
            ])
            ->assertSessionHasErrors('bukti');
    }

    public static function jumlahPembayaran(): array
    {
        return [
            'satu pembayaran' => [1],
            'dua pembayaran' => [2],
            'tiga pembayaran' => [3],
        ];
    }

    public function test_musyrifah_dapat_membuka_dan_menutup_tahap_secara_independen(): void
    {
        $this->freezeTime();

        [$musyrifah] = $this->pengguna();
        $pengaturan = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);

        $this->actingAs($musyrifah)->get(route('musyrifah.pembayaran'))->assertOk();

        $jadwal = AngsuranPembayaran::query()
            ->where('periode', $pengaturan->periode)
            ->get()
            ->keyBy('angsuran_ke');

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pembayaran.tahap.buka', $jadwal->get(1)->id))
            ->assertSessionHas('success');
        $this->assertNotNull($jadwal->get(1)->fresh()->dibuka_pada);
        $this->assertNull($jadwal->get(2)->fresh()->dibuka_pada);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pembayaran.tahap.buka', $jadwal->get(2)->id))
            ->assertSessionHasErrors('periode');
        $this->assertNull($jadwal->get(2)->fresh()->dibuka_pada);

        $jadwal->get(2)->update(['tanggal_mulai' => now()->toDateString()]);
        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pembayaran.tahap.buka', $jadwal->get(2)->id))
            ->assertSessionHas('success');
        $this->assertNotNull($jadwal->get(2)->fresh()->dibuka_pada);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pembayaran.tahap.tutup', $jadwal->get(1)->id))
            ->assertSessionHas('success');
        $this->assertNotNull($jadwal->get(1)->fresh()->ditutup_pada);
        $this->assertNull($jadwal->get(2)->fresh()->ditutup_pada);
    }

    public function test_jadwal_tahap_dengan_tanggal_berakhir_sebelum_mulai_ditolak(): void
    {
        $this->freezeTime();

        [$musyrifah] = $this->pengguna();
        $pengaturan = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);

        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.jadwal'), [
                'pengaturan_id' => $pengaturan->id,
                'angsuran' => [[
                    'angsuran_ke' => 1,
                    'nama_tahap' => 'Tahap 1',
                    'nominal' => 1000000,
                    'tanggal_mulai' => now()->addDays(10)->toDateString(),
                    'jatuh_tempo' => now()->addDays(5)->toDateString(),
                    'reminder_hari' => 7,
                ]],
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_penolakan_dan_pembatalan_grup_tidak_menghapus_bukti(): void
    {
        Storage::fake('local');

        [$musyrifah, $mahasiswa] = $this->pengguna();
        $pengaturan = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);
        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();
        $tagihan = Pembayaran::query()->where('user_id', $mahasiswa->id)->orderBy('angsuran_ke')->firstOrFail();
        $this->bukaTahapPertama($pengaturan);

        $this->actingAs($mahasiswa)
            ->post(route('pembayaran.bukti', $tagihan->id), [
                'bukti' => UploadedFile::fake()->image('bukti-grup.jpg'),
                'bayar_semua' => '1',
            ])
            ->assertSessionHasNoErrors();

        $grup = Pembayaran::query()->where('user_id', $mahasiswa->id)->get();
        $this->assertCount(3, $grup);
        $this->assertSame(1, $grup->pluck('bukti_grup')->unique()->count());
        $this->assertSame(1, $grup->pluck('bukti_pembayaran')->unique()->count());

        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.verifikasi', $tagihan->id), [
                'keputusan' => 'tolak',
                'catatan' => 'Nominal transfer tidak sesuai.',
            ])
            ->assertOk();
        $this->assertSame(3, Pembayaran::query()->where('user_id', $mahasiswa->id)->where('status_verifikasi', Pembayaran::VERIFIKASI_DITOLAK)->count());

        $this->actingAs($mahasiswa)
            ->post(route('pembayaran.bukti', $tagihan->id), [
                'bukti' => UploadedFile::fake()->image('bukti-grup-perbaikan.jpg'),
                'bayar_semua' => '1',
            ])
            ->assertSessionHasNoErrors();
        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.verifikasi', $tagihan->id), ['keputusan' => 'terima'])
            ->assertOk();
        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.status', $tagihan->id), ['status' => Pembayaran::STATUS_LUNAS])
            ->assertOk();
        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.status', $tagihan->id), ['status' => Pembayaran::STATUS_BELUM_LUNAS])
            ->assertOk();

        $grup = Pembayaran::query()->where('user_id', $mahasiswa->id)->get();
        $this->assertSame(3, $grup->where('status', Pembayaran::STATUS_BELUM_LUNAS)->count());
        $this->assertSame(1, $grup->pluck('bukti_grup')->unique()->count());
        $this->assertSame(1, $grup->pluck('bukti_pembayaran')->unique()->count());
        $this->assertNotNull($grup->first()->bukti_pembayaran);
        Storage::disk('local')->assertExists($grup->first()->bukti_pembayaran);
    }

    public function test_jatuh_tempo_yang_sudah_disetujui_tidak_ditimpa_jadwal_umum(): void
    {
        $this->freezeTime();

        [$musyrifah, $mahasiswa] = $this->pengguna();
        $pengaturan = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);
        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();

        $tagihan = Pembayaran::query()
            ->where('user_id', $mahasiswa->id)
            ->where('periode', $pengaturan->periode)
            ->where('angsuran_ke', 2)
            ->firstOrFail();
        $jatuhTempoDisetujui = now()->addDays(60)->toDateString();
        $pengajuan = PengajuanKeterlambatanPembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'pembayaran_id' => $tagihan->id,
            'nomor_surat' => 'SKP/TEST/JADWAL-02/MAHAD',
            'alasan' => 'Dana kiriman orang tua tersedia akhir bulan.',
            'tanggal_rencana_bayar' => $jatuhTempoDisetujui,
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);

        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.keterlambatan', $pengajuan->id), [
                'keputusan' => 'setuju',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.jadwal'), [
                'pengaturan_id' => $pengaturan->id,
                'angsuran' => [[
                    'angsuran_ke' => 2,
                    'nama_tahap' => 'Tahap 2',
                    'nominal' => 2100000,
                    'tanggal_mulai' => now()->toDateString(),
                    'jatuh_tempo' => now()->addDays(30)->toDateString(),
                    'reminder_hari' => 5,
                ]],
            ])
            ->assertOk();

        $tagihan->refresh();
        $this->assertSame($jatuhTempoDisetujui, $tagihan->jatuh_tempo->toDateString());
        $this->assertSame(2100000.0, (float) $tagihan->nominal);
    }

    public function test_nominal_tidak_dapat_diubah_saat_bukti_sedang_diperiksa(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();
        $pembayaran = $this->buktiMenunggu($mahasiswa);

        $this->actingAs($musyrifah)
            ->postJson(route('musyrifah.pembayaran.nominal', $pembayaran->id), ['nominal' => 2000000])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertSame(1000000.0, (float) $pembayaran->fresh()->nominal);
    }

    public function test_musyrifah_hanya_melihat_pembayaran_lantai_yang_dikelola(): void
    {
        [$musyrifahLantai1, $mahasiswaLantai1] = $this->pengguna();
        [$musyrifahLantai2, $mahasiswaLantai2] = $this->pengguna([
            'name' => 'Mahasiswa Lantai Dua',
            'email' => 'mahasiswa.lantai.dua@asrama.ac.id',
            'nim' => '2317990022',
        ], 2);

        $musyrifahLantai1->update(['lantai_penempatan' => 2]);
        $musyrifahLantai2->update(['lantai_penempatan' => 1]);
        $mahasiswaLantai1->update(['lantai_penempatan' => 2]);
        $mahasiswaLantai2->update(['lantai_penempatan' => 1]);

        $pembayaranLantai1 = Pembayaran::query()->create([
            'user_id' => $mahasiswaLantai1->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);
        $pembayaranLantai2 = Pembayaran::query()->create([
            'user_id' => $mahasiswaLantai2->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->actingAs($musyrifahLantai1)
            ->get(route('musyrifah.pembayaran'))
            ->assertSee('Zaidan Mahasiswa')
            ->assertDontSee('Mahasiswa Lantai Dua');
        $this->actingAs($musyrifahLantai1)
            ->getJson(route('musyrifah.pembayaran.data'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $pembayaranLantai1->id);

        $this->actingAs($musyrifahLantai2)
            ->get(route('musyrifah.pembayaran'))
            ->assertSee('Mahasiswa Lantai Dua')
            ->assertDontSee('Zaidan Mahasiswa');
        $this->actingAs($musyrifahLantai2)
            ->getJson(route('musyrifah.pembayaran.data'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $pembayaranLantai2->id);
    }

    #[DataProvider('aksiPembayaranLintasLantai')]
    public function test_aksi_pembayaran_lintas_lantai_mengembalikan_404(
        string $metode,
        string $namaRoute,
        array $data,
        string $jenisTarget
    ): void {
        [$musyrifahLantai1] = $this->pengguna();
        [, $mahasiswaLantai2] = $this->pengguna([
            'name' => 'Mahasiswa Lantai Dua',
            'email' => 'mahasiswa.lintas.lantai@asrama.ac.id',
            'nim' => '2317990033',
        ], 2);

        $pembayaran = Pembayaran::query()->create([
            'user_id' => $mahasiswaLantai2->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);
        $pengajuan = PengajuanKeterlambatanPembayaran::query()->create([
            'user_id' => $mahasiswaLantai2->id,
            'pembayaran_id' => $pembayaran->id,
            'nomor_surat' => 'SKP/TEST/LINTAS-LANTAI/MAHAD',
            'alasan' => 'Dana kiriman orang tua belum tersedia.',
            'tanggal_rencana_bayar' => now()->addDays(14)->toDateString(),
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);
        $target = $jenisTarget === 'pengajuan' ? $pengajuan->id : $pembayaran->id;

        $this->actingAs($musyrifahLantai1)
            ->{$metode}(route($namaRoute, $target), $data)
            ->assertNotFound();

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
            'status_verifikasi' => null,
            'nominal' => 1000000,
            'diverifikasi_oleh' => null,
        ]);
        $this->assertDatabaseHas('pengajuan_keterlambatan_pembayarans', [
            'id' => $pengajuan->id,
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
            'diproses_oleh' => null,
        ]);
        $this->assertDatabaseMissing('activity_logs', ['user_id' => $mahasiswaLantai2->id]);
    }

    public function test_bukti_dan_surat_lintas_lantai_ditolak_tanpa_mengubah_data(): void
    {
        Storage::fake('public');

        [$musyrifahLantai1] = $this->pengguna();
        [, $mahasiswaLantai2] = $this->pengguna([
            'name' => 'Mahasiswa Lantai Dua',
            'email' => 'mahasiswa.berkas.lintas.lantai@asrama.ac.id',
            'nim' => '2317990044',
        ], 2);
        $pembayaran = Pembayaran::query()->create([
            'user_id' => $mahasiswaLantai2->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
            'status_verifikasi' => Pembayaran::VERIFIKASI_MENUNGGU,
            'bukti_pembayaran' => 'pembayaran/bukti-lintas-lantai.jpg',
        ]);
        Storage::disk('public')->put($pembayaran->bukti_pembayaran, 'bukti-lantai-dua');
        $pengajuan = PengajuanKeterlambatanPembayaran::query()->create([
            'user_id' => $mahasiswaLantai2->id,
            'pembayaran_id' => $pembayaran->id,
            'nomor_surat' => 'SKP/TEST/BERKAS-LINTAS/MAHAD',
            'alasan' => 'Dana kiriman orang tua belum tersedia.',
            'tanggal_rencana_bayar' => now()->addDays(14)->toDateString(),
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);

        $this->actingAs($musyrifahLantai1)
            ->get(route('pembayaran.bukti.lihat', $pembayaran->id))
            ->assertForbidden();
        $this->actingAs($musyrifahLantai1)
            ->get(route('pembayaran.keterlambatan.surat', $pengajuan->id))
            ->assertForbidden();

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
            'status_verifikasi' => Pembayaran::VERIFIKASI_MENUNGGU,
            'bukti_pembayaran' => $pembayaran->bukti_pembayaran,
        ]);
        $this->assertDatabaseHas('pengajuan_keterlambatan_pembayarans', [
            'id' => $pengajuan->id,
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);
    }

    #[DataProvider('diskBukti')]
    public function test_bukti_mahasiswa_lantai_yang_sama_dapat_diakses(string $disk): void
    {
        Storage::fake($disk);

        [$musyrifah, $mahasiswa] = $this->pengguna();
        $pembayaran = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
            'status_verifikasi' => Pembayaran::VERIFIKASI_MENUNGGU,
            'bukti_pembayaran' => 'pembayaran/bukti-'.$disk.'.jpg',
        ]);
        Storage::disk($disk)->put($pembayaran->bukti_pembayaran, 'isi-bukti-'.$disk);

        $this->actingAs($musyrifah)
            ->get(route('pembayaran.bukti.lihat', $pembayaran->id))
            ->assertOk()
            ->assertStreamedContent('isi-bukti-'.$disk);
    }

    public function test_musyrifah_dapat_membuka_surat_keterlambatan_mahasiswa_lantai_yang_sama(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna();
        Pendaftaran::query()->create([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->name,
            'nim' => $mahasiswa->nim,
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2003-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'no_hp' => '081234567890',
            'program_studi' => 'Sistem Informasi',
            'fakultas' => 'Sains dan Teknologi',
            'semester' => 1,
            'status' => 'disetujui',
            'tahun_angkatan' => '2023',
        ]);
        $pembayaran = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);
        $pengajuan = PengajuanKeterlambatanPembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'pembayaran_id' => $pembayaran->id,
            'nomor_surat' => 'SKP/TEST/SURAT-LANTAI/MAHAD',
            'alasan' => 'Dana kiriman orang tua belum tersedia.',
            'tanggal_rencana_bayar' => now()->addDays(14)->toDateString(),
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);

        $surat = $this->actingAs($musyrifah)
            ->get(route('pembayaran.keterlambatan.surat', $pengajuan->id));

        $surat->assertOk();
        $this->assertStringContainsString('application/pdf', $surat->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $surat->getContent());
    }

    public function test_notifikasi_bukti_hanya_dikirim_ke_musyrifah_lantai_mahasiswa(): void
    {
        $this->freezeTime();
        Storage::fake('local');

        [$musyrifahLantai1, $mahasiswaLantai1] = $this->pengguna();
        [$musyrifahLantai2] = $this->pengguna([
            'name' => 'Mahasiswa Lantai Dua',
            'email' => 'mahasiswa.notifikasi.dua@asrama.ac.id',
            'nim' => '2317990055',
        ], 2);
        $pengaturan = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);
        $this->actingAs($mahasiswaLantai1)->get(route('pembayaran'))->assertOk();
        $tagihan = Pembayaran::query()
            ->where('user_id', $mahasiswaLantai1->id)
            ->where('periode', $pengaturan->periode)
            ->where('angsuran_ke', 1)
            ->firstOrFail();
        $jadwal = $tagihan->jadwalAngsuran();
        $this->assertNotNull($jadwal);

        $this->actingAs($musyrifahLantai1)
            ->post(route('musyrifah.pembayaran.tahap.buka', $jadwal->id))
            ->assertSessionHasNoErrors();
        $this->actingAs($mahasiswaLantai1)
            ->post(route('pembayaran.bukti', $tagihan->id), [
                'bukti' => UploadedFile::fake()->image('bukti-notifikasi.jpg'),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $musyrifahLantai1->id,
            'tipe' => Notifikasi::TIPE_PEMBAYARAN,
            'judul' => 'Bukti pembayaran Tahap 1',
        ]);
        $this->assertDatabaseMissing('notifikasis', [
            'user_id' => $musyrifahLantai2->id,
            'tipe' => Notifikasi::TIPE_PEMBAYARAN,
        ]);
    }

    public function test_musyrifah_tanpa_lantai_tidak_melihat_atau_mengakses_pembayaran(): void
    {
        [$musyrifah, $mahasiswa] = $this->pengguna([], null);
        $pembayaran = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pembayaran'))
            ->assertOk()
            ->assertDontSee('Zaidan Mahasiswa');
        $this->actingAs($musyrifah)
            ->getJson(route('musyrifah.pembayaran.data'))
            ->assertOk()
            ->assertJsonPath('data', []);
        $this->actingAs($musyrifah)
            ->getJson(route('musyrifah.pembayaran.detail', $pembayaran->id))
            ->assertNotFound();
    }

    public function test_lantai_penempatan_dipakai_saat_kamar_dan_data_pegawai_tidak_tersedia(): void
    {
        $musyrifah = User::factory()->create([
            'name' => 'Musyrifah Lantai Dua',
            'email' => 'musyrifah.fallback.dua@asrama.com',
            'role' => 'musyrifah',
            'lantai_penempatan' => 2,
        ]);
        $mahasiswa = User::factory()->create([
            'name' => 'Mahasiswa Fallback Lantai Dua',
            'email' => 'mahasiswa.fallback.dua@asrama.ac.id',
            'nim' => '2317990066',
            'role' => 'mahasiswa',
            'lantai_penempatan' => 2,
        ]);
        Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pembayaran'))
            ->assertOk()
            ->assertSee('Mahasiswa Fallback Lantai Dua');
    }

    #[DataProvider('peranPengelolaGlobal')]
    public function test_admin_dan_pengelola_tetap_dapat_mengakses_pembayaran_semua_lantai(string $role): void
    {
        [, $mahasiswaLantai1] = $this->pengguna();
        [, $mahasiswaLantai2] = $this->pengguna([
            'name' => 'Mahasiswa Lantai Dua',
            'email' => 'mahasiswa.global.dua@asrama.ac.id',
            'nim' => '2317990077',
        ], 2);
        $pengelola = User::factory()->create([
            'name' => 'Pengelola Global',
            'email' => 'pengelola.global.'.$role.'@asrama.com',
            'role' => $role,
        ]);
        Pembayaran::query()->create([
            'user_id' => $mahasiswaLantai1->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);
        $pembayaranLantai2 = Pembayaran::query()->create([
            'user_id' => $mahasiswaLantai2->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->actingAs($pengelola)
            ->get(route('musyrifah.pembayaran'))
            ->assertOk()
            ->assertSee('Zaidan Mahasiswa')
            ->assertSee('Mahasiswa Lantai Dua');
        $this->actingAs($pengelola)
            ->getJson(route('musyrifah.pembayaran.data'))
            ->assertOk()
            ->assertJsonCount(2, 'data');
        $this->actingAs($pengelola)
            ->getJson(route('musyrifah.pembayaran.detail', $pembayaranLantai2->id))
            ->assertOk()
            ->assertJsonPath('mahasiswa.id', $mahasiswaLantai2->id);
    }

    /** @return array<string, array{0: string, 1: string, 2: array<string, mixed>, 3: string}> */
    public static function aksiPembayaranLintasLantai(): array
    {
        return [
            'detail' => ['getJson', 'musyrifah.pembayaran.detail', [], 'pembayaran'],
            'status' => ['postJson', 'musyrifah.pembayaran.status', ['status' => Pembayaran::STATUS_LUNAS], 'pembayaran'],
            'verifikasi bukti' => ['postJson', 'musyrifah.pembayaran.verifikasi', ['keputusan' => 'terima'], 'pembayaran'],
            'nominal' => ['postJson', 'musyrifah.pembayaran.nominal', ['nominal' => 2000000], 'pembayaran'],
            'keputusan keterlambatan' => ['postJson', 'musyrifah.pembayaran.keterlambatan', ['keputusan' => 'setuju'], 'pengajuan'],
        ];
    }

    /** @return array<string, array{0: string}> */
    public static function diskBukti(): array
    {
        return [
            'bukti baru privat' => ['local'],
            'bukti lama publik' => ['public'],
        ];
    }

    /** @return array<string, array{0: string}> */
    public static function peranPengelolaGlobal(): array
    {
        return [
            'admin' => ['admin'],
            'pengelola' => ['pengelola'],
        ];
    }

    /**
     * @param  array<string, mixed>  $atributMahasiswa
     * @return array{0: User, 1: User}
     */
    private function pengguna(array $atributMahasiswa = [], ?int $lantai = 1): array
    {
        $kamar = $lantai === null ? null : Kamar::factory()->create(['lantai' => $lantai]);

        $musyrifah = User::factory()->create([
            'name' => 'Ustadzah Musyrifah',
            'email' => $lantai === null
                ? 'musyrifah.pembayaran@asrama.com'
                : 'musyrifah.pembayaran.lantai.'.$lantai.'@asrama.com',
            'role' => 'musyrifah',
        ]);

        if ($lantai !== null) {
            $pegawai = Pegawai::query()->create([
                'nama_pegawai' => $musyrifah->name,
                'email' => $musyrifah->email,
                'status_pegawai' => true,
                'lantai_yang_dikelola' => $lantai,
            ]);
            PegawaiAkun::query()->create([
                'pegawai_id' => $pegawai->id,
                'user_id' => $musyrifah->id,
            ]);
        }

        $mahasiswa = User::factory()->create(array_merge([
            'name' => 'Zaidan Mahasiswa',
            'email' => 'zaidan@asrama.ac.id',
            'nim' => '2317990011',
            'role' => 'mahasiswa',
            'kamar_id' => $kamar?->id,
        ], $atributMahasiswa));

        return [$musyrifah, $mahasiswa];
    }

    private function bukaTahapPertama(PengaturanPembayaran $pengaturan): void
    {
        app(PembayaranAngsuranService::class)
            ->pastikanJadwal($pengaturan)
            ->first()
            ->update(['dibuka_pada' => now()]);
    }

    private function buktiMenunggu(User $mahasiswa): Pembayaran
    {
        return Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
            'status_verifikasi' => Pembayaran::VERIFIKASI_MENUNGGU,
            'bukti_pembayaran' => 'pembayaran/bukti.jpg',
        ]);
    }
}
