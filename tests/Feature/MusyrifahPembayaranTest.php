<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\PengajuanKeterlambatanPembayaran;
use App\Models\PengaturanPembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
                        'jatuh_tempo' => now()->addDays(20)->toDateString(),
                        'reminder_hari' => 7,
                    ],
                    [
                        'angsuran_ke' => 2,
                        'nama_tahap' => 'Tahap 2',
                        'nominal' => 1750000,
                        'jatuh_tempo' => now()->addDays(80)->toDateString(),
                        'reminder_hari' => 5,
                    ],
                    [
                        'angsuran_ke' => 3,
                        'nama_tahap' => 'Tahap 3',
                        'nominal' => 1750000,
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

        PengaturanPembayaran::factory()->create([
            'periode' => 'Januari 2027',
            'is_aktif' => true,
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

    /**
     * @param  array<string, mixed>  $atributMahasiswa
     * @return array{0: User, 1: User}
     */
    private function pengguna(array $atributMahasiswa = []): array
    {
        $musyrifah = User::factory()->create([
            'name' => 'Ustadzah Musyrifah',
            'email' => 'musyrifah.pembayaran@asrama.com',
            'role' => 'musyrifah',
        ]);

        $mahasiswa = User::factory()->create(array_merge([
            'name' => 'Zaidan Mahasiswa',
            'email' => 'zaidan@asrama.ac.id',
            'nim' => '2317990011',
            'role' => 'mahasiswa',
        ], $atributMahasiswa));

        return [$musyrifah, $mahasiswa];
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
