<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MusyrifahPembayaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_musyrifah_pembayaran(): void
    {
        $response = $this->get('/musyrifah/pembayaran');

        $response->assertRedirect(route('login'));
    }

    public function test_musyrifah_can_access_pembayaran_page_and_see_student_payments(): void
    {
        $musyrifah = User::factory()->create([
            'name' => 'Musyrifah Mahad',
            'email' => 'musyrifah@asrama.com',
            'role' => 'musyrifah',
        ]);

        $mahasiswa = User::factory()->create([
            'name' => 'Zaidan Mahasiswa',
            'email' => 'zaidan@asrama.ac.id',
            'nim' => '2317990011',
            'role' => 'mahasiswa',
        ]);

        Pembayaran::create([
            'user_id' => $mahasiswa->id,
            'kode_transaksi' => 'TRX-TEST-001',
            'nama_tagihan' => 'Pembayaran Asrama',
            'periode' => 'Januari 2027',
            'nominal' => 2500000.00,
            'status' => 'belum_lunas',
        ]);

        $response = $this->actingAs($musyrifah)->get('/musyrifah/pembayaran');

        $response->assertStatus(200);
        $response->assertSee('Pembayaran Mahasiswa');
        $response->assertSee('Zaidan Mahasiswa');
        $response->assertSee('2317990011');
        $response->assertSee('Belum Lunas');
    }

    public function test_mahasiswa_payment_activity_is_synchronized_with_musyrifah(): void
    {
        $mahasiswa = User::factory()->create([
            'name' => 'Fathur Rahman',
            'email' => 'fathur@asrama.ac.id',
            'nim' => '2317020999',
            'role' => 'mahasiswa',
        ]);

        $musyrifah = User::factory()->create([
            'name' => 'Ustadzah Musyrifah',
            'email' => 'musyrifah2@asrama.com',
            'role' => 'musyrifah',
        ]);

        // 1. Mahasiswa melakukan konfirmasi pembayaran di halamannya
        $responseBayar = $this->actingAs($mahasiswa)->post('/pembayaran/bayar', [
            'metode' => 'Virtual Account Bank Syariah Indonesia (BSI)',
        ]);

        $responseBayar->assertRedirect(route('pembayaran'));

        // Cek database pembayaran terupdate menjadi lunas
        $this->assertDatabaseHas('pembayarans', [
            'user_id' => $mahasiswa->id,
            'status' => 'lunas',
            'metode_pembayaran' => 'Virtual Account Bank Syariah Indonesia (BSI)',
        ]);

        // Cek activity log tercatat secara otomatis
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $mahasiswa->id,
            'activity' => 'Pembayaran Tagihan',
        ]);

        // 2. Musyrifah membuka halaman atau request live API data pembayaran
        $responseMusyrifah = $this->actingAs($musyrifah)->getJson('/musyrifah/pembayaran/data');

        $responseMusyrifah->assertStatus(200);
        $responseMusyrifah->assertJsonPath('success', true);

        // Pastikan nama mahasiswa dan status lunas muncul di data musyrifah
        $data = $responseMusyrifah->json('data');
        $fathurData = collect($data)->firstWhere('nim', '2317020999');

        $this->assertNotNull($fathurData);
        $this->assertEquals('Fathur Rahman', $fathurData['nama']);
        $this->assertEquals('Lunas', $fathurData['status']);
        $this->assertEquals('Virtual Account Bank Syariah Indonesia (BSI)', $fathurData['metode']);
    }

    public function test_musyrifah_detail_endpoint_returns_payment_and_activity_logs(): void
    {
        $mahasiswa = User::factory()->create([
            'name' => 'Aulia Zahra',
            'email' => 'aulia@asrama.ac.id',
            'nim' => '2317020888',
            'role' => 'mahasiswa',
        ]);

        $musyrifah = User::factory()->create([
            'role' => 'musyrifah',
        ]);

        $pembayaran = Pembayaran::create([
            'user_id' => $mahasiswa->id,
            'kode_transaksi' => 'TRX-TEST-888',
            'nama_tagihan' => 'Pembayaran Asrama',
            'periode' => 'Januari 2027',
            'nominal' => 2500000.00,
            'status' => 'lunas',
            'metode_pembayaran' => 'BRIVA',
            'tanggal_bayar' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $mahasiswa->id,
            'user_name' => $mahasiswa->name,
            'user_email' => $mahasiswa->email,
            'user_role' => 'mahasiswa',
            'activity' => 'Pembayaran Tagihan',
            'description' => 'Mahasiswa Aulia Zahra berhasil membayar via BRIVA',
        ]);

        $response = $this->actingAs($musyrifah)->getJson("/musyrifah/pembayaran/{$pembayaran->id}/detail");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('mahasiswa.name', 'Aulia Zahra');
        $response->assertJsonPath('pembayaran.status_label', 'Lunas');
        $response->assertJsonPath('pembayaran.metode_pembayaran', 'BRIVA');

        $logs = $response->json('riwayat_aktivitas');
        $this->assertNotEmpty($logs);
        $this->assertEquals('Pembayaran Tagihan', $logs[0]['activity']);
    }

    public function test_musyrifah_can_update_payment_status_and_it_records_activity_log(): void
    {
        $mahasiswa = User::factory()->create([
            'name' => 'Dimas Surya',
            'email' => 'dimas@asrama.ac.id',
            'nim' => '2317020777',
            'role' => 'mahasiswa',
        ]);

        $musyrifah = User::factory()->create([
            'name' => 'Ustadzah Sarah',
            'role' => 'musyrifah',
        ]);

        $pembayaran = Pembayaran::create([
            'user_id' => $mahasiswa->id,
            'kode_transaksi' => 'TRX-TEST-777',
            'nama_tagihan' => 'Pembayaran Asrama',
            'periode' => 'Januari 2027',
            'nominal' => 2500000.00,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($musyrifah)->postJson("/musyrifah/pembayaran/{$pembayaran->id}/status", [
            'status' => 'lunas',
            'catatan' => 'Bukti valid sesuai mutasi bank',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'status' => 'lunas',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $mahasiswa->id,
            'activity' => 'Verifikasi Pembayaran',
        ]);
    }
}
