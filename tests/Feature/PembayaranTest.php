<?php

namespace Tests\Feature;

use App\Models\Pembayaran;
use App\Models\PengajuanKeterlambatanPembayaran;
use App\Models\PengaturanPembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PembayaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_pembayaran(): void
    {
        $this->get('/pembayaran')->assertRedirect(route('login'));
    }

    public function test_halaman_pembayaran_menampilkan_tiga_angsuran_dan_rekening_resmi(): void
    {
        [$mahasiswa] = $this->mahasiswa();
        PengaturanPembayaran::factory()->create();

        $this->actingAs($mahasiswa)
            ->get(route('pembayaran'))
            ->assertOk()
            ->assertSee('Pembayaran Mahasiswa')
            ->assertSee('Total Tagihan')
            ->assertSee('Rp 5.000.000')
            ->assertSee('Tahap 1')
            ->assertSee('Tahap 2')
            ->assertSee('Tahap 3')
            ->assertSee('7322022268')
            ->assertSee('RPL 010 BLU UIN P2B MAHAD UTK DR')
            ->assertSee('Salin Nomor Rekening')
            ->assertSee('Riwayat Pembayaran')
            ->assertSee('Riwayat Pengajuan Keterlambatan');

        $this->assertDatabaseCount('angsuran_pembayarans', 3);
        $this->assertDatabaseCount('pembayarans', 3);
    }

    public function test_musyrifah_tidak_dapat_membuka_halaman_pembayaran_mahasiswa(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $this->actingAs($musyrifah)->get(route('pembayaran'))->assertForbidden();
    }

    public function test_mahasiswa_mengunggah_bukti_dan_status_menunggu_verifikasi(): void
    {
        Storage::fake('public');

        [$mahasiswa] = $this->mahasiswa();
        PengaturanPembayaran::factory()->create();

        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();

        $tagihan = Pembayaran::query()
            ->where('user_id', $mahasiswa->id)
            ->orderBy('angsuran_ke')
            ->firstOrFail();

        $response = $this->actingAs($mahasiswa)->post(route('pembayaran.bukti', $tagihan->id), [
            'bukti' => UploadedFile::fake()->image('bukti-transfer.jpg'),
        ]);

        $response->assertRedirect(route('pembayaran'));
        $response->assertSessionHas('success');

        $tagihan->refresh();

        $this->assertSame(Pembayaran::STATUS_MENUNGGU_VERIFIKASI, $tagihan->status);
        $this->assertSame(Pembayaran::VERIFIKASI_MENUNGGU, $tagihan->status_verifikasi);
        $this->assertNull($tagihan->tanggal_bayar);
        $this->assertNull($tagihan->tanggal_konfirmasi_rektorat);
        $this->assertNotNull($tagihan->bukti_pembayaran);
        Storage::disk('public')->assertExists($tagihan->bukti_pembayaran);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $mahasiswa->id,
            'activity' => 'Unggah Bukti Pembayaran',
        ]);
    }

    public function test_bukti_tidak_boleh_diunggah_untuk_tagihan_milik_mahasiswa_lain(): void
    {
        Storage::fake('public');

        [$mahasiswa] = $this->mahasiswa();
        [$mahasiswaLain] = $this->mahasiswa(['email' => 'lain@asrama.ac.id', 'nim' => '2317020999']);
        PengaturanPembayaran::factory()->create();

        $tagihanLain = Pembayaran::query()->create([
            'user_id' => $mahasiswaLain->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('pembayaran.bukti', $tagihanLain->id), [
                'bukti' => UploadedFile::fake()->image('bukti.jpg'),
            ])
            ->assertForbidden();
    }

    public function test_mahasiswa_mengajukan_keterlambatan_dan_mendapat_surat_pdf(): void
    {
        [$mahasiswa] = $this->mahasiswa();
        PengaturanPembayaran::factory()->create();

        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();

        $tagihan = Pembayaran::query()
            ->where('user_id', $mahasiswa->id)
            ->orderBy('angsuran_ke')
            ->firstOrFail();

        $response = $this->actingAs($mahasiswa)->post(route('pembayaran.keterlambatan', $tagihan->id), [
            'alasan' => 'Dana kiriman orang tua baru tersedia awal bulan depan.',
            'tanggal_rencana_bayar' => now()->addDays(14)->toDateString(),
        ]);

        $response->assertRedirect(route('pembayaran'));
        $response->assertSessionHas('success');

        $pengajuan = PengajuanKeterlambatanPembayaran::query()->firstOrFail();

        $this->assertSame(PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU, $pengajuan->status);
        $this->assertStringStartsWith('SKP/', $pengajuan->nomor_surat);
        $this->assertDatabaseHas('pembayarans', [
            'id' => $tagihan->id,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $surat = $this->actingAs($mahasiswa)->get(route('pembayaran.keterlambatan.surat', $pengajuan->id));

        $surat->assertOk();
        $this->assertStringContainsString('application/pdf', $surat->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $surat->getContent());
    }

    public function test_pengajuan_keterlambatan_ganda_ditolak(): void
    {
        [$mahasiswa] = $this->mahasiswa();
        PengaturanPembayaran::factory()->create();

        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();

        $tagihan = Pembayaran::query()->where('user_id', $mahasiswa->id)->orderBy('angsuran_ke')->firstOrFail();

        $data = [
            'alasan' => 'Belum ada dana karena orang tua sakit.',
            'tanggal_rencana_bayar' => now()->addDays(10)->toDateString(),
        ];

        $this->actingAs($mahasiswa)->post(route('pembayaran.keterlambatan', $tagihan->id), $data)
            ->assertSessionHasNoErrors();

        $this->actingAs($mahasiswa)->post(route('pembayaran.keterlambatan', $tagihan->id), $data)
            ->assertSessionHasErrors('alasan');

        $this->assertDatabaseCount('pengajuan_keterlambatan_pembayarans', 1);
    }

    public function test_halaman_pembayaran_menampilkan_pesan_periode_belum_aktif(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        PengaturanPembayaran::factory()->create([
            'periode' => 'Januari 2027',
            'is_aktif' => false,
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pembayaran'))
            ->assertOk()
            ->assertSee('Belum ada periode pembayaran aktif. Silakan menunggu Musyrifah membuka periode pembayaran.')
            ->assertDontSee('7322022268');
    }

    public function test_unggah_bukti_ditolak_saat_periode_belum_dibuka(): void
    {
        Storage::fake('public');

        [$mahasiswa] = $this->mahasiswa();

        PengaturanPembayaran::factory()->create([
            'periode' => 'Januari 2027',
            'is_aktif' => false,
        ]);

        $tagihan = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('pembayaran.bukti', $tagihan->id), [
                'bukti' => UploadedFile::fake()->image('bukti.jpg'),
            ])
            ->assertSessionHasErrors('periode');

        $tagihan->refresh();

        $this->assertSame(Pembayaran::STATUS_BELUM_LUNAS, $tagihan->status);
        $this->assertNull($tagihan->bukti_pembayaran);
    }

    public function test_halaman_detail_lama_diarahkan_ke_halaman_pembayaran(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        $this->actingAs($mahasiswa)
            ->get(route('pembayaran.detail'))
            ->assertRedirect(route('pembayaran'));
    }

    /**
     * @param  array<string, mixed>  $atribut
     * @return array<int, User>
     */
    private function mahasiswa(array $atribut = []): array
    {
        $mahasiswa = User::factory()->create(array_merge([
            'name' => 'Ahmad Santoso',
            'email' => 'ahmad@asrama.ac.id',
            'nim' => '2317020134',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ], $atribut));

        return [$mahasiswa];
    }
}
