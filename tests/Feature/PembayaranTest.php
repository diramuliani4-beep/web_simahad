<?php

namespace Tests\Feature;

use App\Models\AngsuranPembayaran;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeterlambatanPembayaran;
use App\Models\PengaturanPembayaran;
use App\Models\User;
use App\Services\PembayaranAngsuranService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
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
            ->assertSee('BSI')
            ->assertSee('7322022268')
            ->assertSee('RPL 010 BLU UIN P2B MAHAD UTK DR')
            ->assertSee('Salin Nomor Rekening')
            ->assertSee('Riwayat Pembayaran')
            ->assertSee('Riwayat Pengajuan Keterlambatan');

        $this->assertDatabaseCount('angsuran_pembayarans', 3);
        $this->assertDatabaseCount('pembayarans', 3);
    }

    public function test_tagihan_lama_tahap_pertama_dipakai_ulang_tanpa_duplikat(): void
    {
        [$mahasiswa] = $this->mahasiswa();
        PengaturanPembayaran::factory()->create();

        $tagihanLama = Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'kode_transaksi' => 'TRX-LEGACY-0001',
            'nama_tagihan' => 'Pembayaran Asrama',
            'periode' => 'Januari 2027',
            'angsuran_ke' => 1,
            'nominal' => 1000000,
            'status' => Pembayaran::STATUS_LUNAS,
            'tanggal_bayar' => now(),
        ]);

        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();

        $this->assertDatabaseCount('pembayarans', 3);
        $this->assertSame(1, Pembayaran::query()->where('user_id', $mahasiswa->id)->where('angsuran_ke', 1)->count());
        $this->assertDatabaseHas('pembayarans', [
            'id' => $tagihanLama->id,
            'status' => Pembayaran::STATUS_LUNAS,
            'tanggal_bayar' => $tagihanLama->tanggal_bayar,
        ]);

        $ringkasan = app(PembayaranAngsuranService::class)->ringkasan($mahasiswa);

        $this->assertSame(5000000.0, $ringkasan['total']);
        $this->assertSame(1000000.0, $ringkasan['dibayar']);
        $this->assertSame(4000000.0, $ringkasan['sisa']);
    }

    public function test_musyrifah_tidak_dapat_membuka_halaman_pembayaran_mahasiswa(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $this->actingAs($musyrifah)->get(route('pembayaran'))->assertForbidden();
    }

    public function test_mahasiswa_mengunggah_bukti_dan_status_menunggu_verifikasi(): void
    {
        Storage::fake('local');

        [$mahasiswa] = $this->mahasiswa();
        PengaturanPembayaran::factory()->create();

        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();

        $tagihan = Pembayaran::query()
            ->where('user_id', $mahasiswa->id)
            ->orderBy('angsuran_ke')
            ->firstOrFail();

        $this->bukaTahap($tagihan);

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
        Storage::disk('local')->assertExists($tagihan->bukti_pembayaran);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $mahasiswa->id,
            'activity' => 'Unggah Bukti Pembayaran',
        ]);
    }

    public function test_bukti_tidak_boleh_diunggah_untuk_tagihan_milik_mahasiswa_lain(): void
    {
        Storage::fake('local');

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
            ->assertSee('7322022268')
            ->assertSee('RPL 010 BLU UIN P2B MAHAD UTK DR');
    }

    public function test_unggah_bukti_ditolak_saat_periode_belum_dibuka(): void
    {
        Storage::fake('local');

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

    public function test_halaman_menampilkan_rentang_semua_tahap_yang_belum_dibuka(): void
    {
        $this->freezeTime();

        [$mahasiswa] = $this->mahasiswa();
        $pengaturan = PengaturanPembayaran::factory()->create([
            'periode' => 'Januari 2027',
            'periode_dibuka_pada' => '2026-11-01',
        ]);
        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();

        $rentang = [
            1 => ['2026-11-01', '2026-11-30'],
            2 => ['2026-12-01', '2026-12-31'],
            3 => ['2027-01-01', '2027-01-31'],
        ];
        foreach ($rentang as $tahap => [$tanggalMulai, $tanggalBerakhir]) {
            AngsuranPembayaran::query()
                ->where('periode', $pengaturan->periode)
                ->where('angsuran_ke', $tahap)
                ->update([
                    'tanggal_mulai' => $tanggalMulai,
                    'jatuh_tempo' => $tanggalBerakhir,
                    'dibuka_pada' => null,
                    'ditutup_pada' => null,
                ]);
        }

        $this->actingAs($mahasiswa)
            ->get(route('pembayaran'))
            ->assertOk()
            ->assertSee('Rentang Periode')
            ->assertSee('Belum Dibuka')
            ->assertSee('1 November 2026')
            ->assertSee('30 November 2026')
            ->assertSee('1 Desember 2026')
            ->assertSee('31 Desember 2026')
            ->assertSee('1 Januari 2027')
            ->assertSee('31 Januari 2027')
            ->assertDontSee('Lunasi seluruh sisa tagihan sekaligus', false);

        $this->assertDatabaseHas('angsuran_pembayarans', [
            'periode' => $pengaturan->periode,
            'angsuran_ke' => 1,
            'dibuka_pada' => null,
        ]);
        $this->assertDatabaseHas('angsuran_pembayarans', [
            'periode' => $pengaturan->periode,
            'angsuran_ke' => 2,
            'dibuka_pada' => null,
        ]);
        $this->assertDatabaseHas('angsuran_pembayarans', [
            'periode' => $pengaturan->periode,
            'angsuran_ke' => 3,
            'dibuka_pada' => null,
        ]);
    }

    public function test_unggah_bukti_ditolak_saat_tahap_belum_dibuka(): void
    {
        $this->freezeTime();
        Storage::fake('local');

        [$mahasiswa] = $this->mahasiswa();
        $pengaturan = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);
        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();
        $tagihan = Pembayaran::query()
            ->where('user_id', $mahasiswa->id)
            ->where('periode', $pengaturan->periode)
            ->where('angsuran_ke', 2)
            ->firstOrFail();
        $jadwal = $tagihan->jadwalAngsuran();
        $this->assertNotNull($jadwal);
        $this->assertNull($jadwal->dibuka_pada);

        $this->actingAs($mahasiswa)
            ->post(route('pembayaran.bukti', $tagihan->id), [
                'bukti' => UploadedFile::fake()->image('bukti-tahap-belum-dibuka.jpg'),
            ])
            ->assertSessionHasErrors([
                'periode' => 'Tahap pembayaran ini belum dibuka atau sudah ditutup oleh Musyrifah.',
            ]);

        $this->assertDatabaseHas('pembayarans', [
            'id' => $tagihan->id,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
            'status_verifikasi' => null,
            'bukti_pembayaran' => null,
        ]);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    #[DataProvider('tahapPembayaran')]
    public function test_mahasiswa_dapat_mengajukan_keterlambatan_pada_tahap_yang_belum_dibuka(int $tahap): void
    {
        $this->freezeTime();

        [$mahasiswa] = $this->mahasiswa();
        $pengaturan = PengaturanPembayaran::factory()->create(['periode' => 'Januari 2027']);
        $this->actingAs($mahasiswa)->get(route('pembayaran'))->assertOk();
        $tagihan = Pembayaran::query()
            ->where('user_id', $mahasiswa->id)
            ->where('periode', $pengaturan->periode)
            ->where('angsuran_ke', $tahap)
            ->firstOrFail();
        $jadwal = $tagihan->jadwalAngsuran();
        $this->assertNotNull($jadwal);
        $this->assertNull($jadwal->dibuka_pada);

        $this->actingAs($mahasiswa)
            ->post(route('pembayaran.keterlambatan', $tagihan->id), [
                'alasan' => 'Dana kiriman orang tua belum tersedia.',
                'tanggal_rencana_bayar' => now()->addDays(14)->toDateString(),
            ])
            ->assertRedirect(route('pembayaran'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_keterlambatan_pembayarans', [
            'user_id' => $mahasiswa->id,
            'pembayaran_id' => $tagihan->id,
            'status' => PengajuanKeterlambatanPembayaran::STATUS_MENUNGGU,
        ]);
        $this->assertDatabaseHas('pembayarans', [
            'id' => $tagihan->id,
            'status' => Pembayaran::STATUS_BELUM_LUNAS,
        ]);
    }

    /** @return array<string, array{0: int}> */
    public static function tahapPembayaran(): array
    {
        return [
            'tahap pertama' => [1],
            'tahap kedua' => [2],
            'tahap ketiga' => [3],
        ];
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

    private function bukaTahap(Pembayaran $tagihan): void
    {
        AngsuranPembayaran::query()
            ->where('periode', $tagihan->periode)
            ->where('angsuran_ke', $tagihan->angsuran_ke)
            ->update(['dibuka_pada' => now()]);
    }
}
