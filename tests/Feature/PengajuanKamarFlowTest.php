<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Pendaftaran;
use App\Models\PengajuanKamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanKamarFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_sees_room_data_managed_by_musyrifah(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        Kamar::create([
            'nama_kamar' => 'Kamar 101',
            'nomor_kamar' => '101',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
            'fasilitas' => 'Kasur, lemari, meja belajar',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('kamar'));

        $response->assertOk();
        $response->assertSee('Informasi Kamar');
        $response->assertSee('Kamar 101');
        $response->assertSee('Kasur, lemari, meja belajar');
        $response->assertSee('Ajukan Kamar Ini');
    }

    public function test_mahasiswa_can_apply_for_a_room(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $kamar = Kamar::create([
            'nama_kamar' => 'Kamar 202',
            'nomor_kamar' => '202',
            'gedung' => 'Gedung A',
            'lantai' => 2,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('kamar.pilih'), [
            'kamar_id' => $kamar->id,
        ]);

        $response->assertRedirect(route('kamar'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_kamars', [
            'user_id' => $mahasiswa->id,
            'kamar_id' => $kamar->id,
            'status' => PengajuanKamar::STATUS_MENUNGGU,
        ]);
    }

    public function test_mahasiswa_cannot_apply_for_a_full_room(): void
    {
        $kamar = Kamar::create([
            'nama_kamar' => 'Kamar 303',
            'nomor_kamar' => '303',
            'gedung' => 'Gedung A',
            'lantai' => 3,
            'kapasitas' => 1,
            'terisi' => 1,
            'status' => 'Penuh',
        ]);

        User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamar->id,
        ]);

        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($mahasiswa)->post(route('kamar.pilih'), [
            'kamar_id' => $kamar->id,
        ]);

        $response->assertRedirect(route('kamar'));
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('pengajuan_kamars', [
            'user_id' => $mahasiswa->id,
        ]);
    }

    public function test_musyrifah_can_approve_room_application_and_placement_is_synced(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $kamar = Kamar::create([
            'nama_kamar' => 'Kamar 105',
            'nomor_kamar' => '105',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 2,
            'terisi' => 0,
            'status' => 'Tersedia',
        ]);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Nur Aisyah',
            'nim' => '20261234',
        ]);

        $pengajuan = PengajuanKamar::create([
            'user_id' => $mahasiswa->id,
            'kamar_id' => $kamar->id,
            'status' => PengajuanKamar::STATUS_MENUNGGU,
        ]);

        // Pengajuan tampil pada halaman data kamar Musyrifah
        $page = $this->actingAs($musyrifah)->get(route('musyrifah.data_kamar'));
        $page->assertOk();
        $page->assertSee('Pengajuan Kamar');
        $page->assertSee('Nur Aisyah');
        $page->assertSee('Setujui');

        $response = $this->actingAs($musyrifah)->post(
            route('musyrifah.pengajuan_kamar.approve', $pengajuan->id)
        );

        $response->assertRedirect(route('musyrifah.data_kamar'));
        $response->assertSessionHas('success');

        $this->assertSame(
            PengajuanKamar::STATUS_DISETUJUI,
            $pengajuan->fresh()->status
        );
        $this->assertSame($kamar->id, $mahasiswa->fresh()->kamar_id);
        $this->assertSame(1, $kamar->fresh()->terisi);

        // Mahasiswa melihat kamar yang telah ditetapkan
        $kamarPage = $this->actingAs($mahasiswa->fresh())->get(route('kamar'));
        $kamarPage->assertSee('Kamar Saya');
        $kamarPage->assertSee('Telah Ditetapkan');
        $kamarPage->assertSee('Kamar 105');
    }

    public function test_musyrifah_can_reject_room_application(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $kamar = Kamar::create([
            'nama_kamar' => 'Kamar 106',
            'nomor_kamar' => '106',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
        ]);

        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $pengajuan = PengajuanKamar::create([
            'user_id' => $mahasiswa->id,
            'kamar_id' => $kamar->id,
            'status' => PengajuanKamar::STATUS_MENUNGGU,
        ]);

        $response = $this->actingAs($musyrifah)->post(
            route('musyrifah.pengajuan_kamar.reject', $pengajuan->id)
        );

        $response->assertRedirect(route('musyrifah.data_kamar'));
        $response->assertSessionHas('success');

        $this->assertSame(
            PengajuanKamar::STATUS_DITOLAK,
            $pengajuan->fresh()->status
        );
        $this->assertNull($mahasiswa->fresh()->kamar_id);

        // Mahasiswa dapat mengajukan kamar lain setelah ditolak
        $kamarPage = $this->actingAs($mahasiswa->fresh())->get(route('kamar'));
        $kamarPage->assertSee('Pengajuan kamar sebelumnya ditolak');
        $kamarPage->assertSee('Ajukan Kamar Ini');
    }

    public function test_mahasiswa_without_approved_registration_can_still_apply_for_room(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $kamar = Kamar::create([
            'nama_kamar' => 'Kamar 107',
            'nomor_kamar' => '107',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('kamar.pilih'), [
            'kamar_id' => $kamar->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pengajuan_kamars', [
            'user_id' => $mahasiswa->id,
            'kamar_id' => $kamar->id,
            'pendaftaran_id' => null,
        ]);
    }

    public function test_mahasiswa_dengan_penempatan_melihat_form_pengajuan_pindah_kamar(): void
    {
        $kamarSaatIni = $this->buatKamar('201', 2, 2);
        $kamarLain = $this->buatKamar('202', 2, 4);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamarSaatIni->id,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('kamar'));

        $response->assertOk();
        $response->assertSee('Pengajuan Pindah Kamar');
        $response->assertSee('Alasan Perpindahan');
        $response->assertSee('Pilih untuk Pindah');
        $response->assertSee('Kamar Saat Ini');
        $response->assertSee('Kamar '.$kamarLain->nomor_kamar);
    }

    public function test_mahasiswa_dapat_mengajukan_pindah_kamar_tanpa_mengubah_penempatan(): void
    {
        $kamarAsal = $this->buatKamar('301', 3, 2);
        $kamarTujuan = $this->buatKamar('302', 3, 4);
        $kamarAsal->update(['terisi' => 1]);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamarAsal->id,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('kamar.pindah'), [
            'kamar_id' => $kamarTujuan->id,
            'alasan' => 'Kamar saat ini terlalu padat dan sulit beristirahat.',
        ]);

        $response->assertRedirect(route('kamar'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_kamars', [
            'user_id' => $mahasiswa->id,
            'jenis' => PengajuanKamar::JENIS_PINDAH,
            'kamar_asal_id' => $kamarAsal->id,
            'kamar_id' => $kamarTujuan->id,
            'status' => PengajuanKamar::STATUS_MENUNGGU,
        ]);

        // Penempatan belum berubah sebelum Musyrifah menyetujui
        $this->assertSame($kamarAsal->id, $mahasiswa->fresh()->kamar_id);
        $this->assertSame(1, $kamarAsal->fresh()->terisi);
        $this->assertSame(0, $kamarTujuan->fresh()->terisi);
    }

    public function test_pengajuan_pindah_kamar_wajib_menyertakan_alasan(): void
    {
        $kamarAsal = $this->buatKamar('401', 4, 2);
        $kamarTujuan = $this->buatKamar('402', 4, 4);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamarAsal->id,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('kamar.pindah'), [
            'kamar_id' => $kamarTujuan->id,
            'alasan' => '',
        ]);

        $response->assertSessionHasErrors('alasan');
        $this->assertDatabaseCount('pengajuan_kamars', 0);
    }

    public function test_mahasiswa_tidak_dapat_mengajukan_pindah_ke_kamar_yang_sama(): void
    {
        $kamarAsal = $this->buatKamar('501', 5, 4);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamarAsal->id,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('kamar.pindah'), [
            'kamar_id' => $kamarAsal->id,
            'alasan' => 'Ingin pindah karena alasan pribadi yang mendesak.',
        ]);

        $response->assertRedirect(route('kamar'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('pengajuan_kamars', 0);
    }

    public function test_musyrifah_dapat_memeriksa_detail_pengajuan_pindah_kamar(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);
        $kamarAsal = $this->buatKamar('601', 6, 2);
        $kamarTujuan = $this->buatKamar('602', 6, 4);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Siti Hajar',
            'nim' => '20269001',
            'kamar_id' => $kamarAsal->id,
        ]);

        PengajuanKamar::create([
            'user_id' => $mahasiswa->id,
            'kamar_id' => $kamarTujuan->id,
            'kamar_asal_id' => $kamarAsal->id,
            'jenis' => PengajuanKamar::JENIS_PINDAH,
            'alasan' => 'Kamar lama dekat dengan area bising.',
            'status' => PengajuanKamar::STATUS_MENUNGGU,
        ]);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.data_kamar'));

        $response->assertOk();
        $response->assertSee('Pengajuan Kamar');
        $response->assertSee('Pindah Kamar');
        $response->assertSee('Siti Hajar');
        $response->assertSee('Kamar Asal');
        $response->assertSee('Alasan Perpindahan');
        $response->assertSee('Kamar lama dekat dengan area bising.');
        $response->assertSee('Menunggu Verifikasi');
        $response->assertSee('Setujui Pindah Kamar');
        $response->assertSee('Catatan Penolakan');
    }

    public function test_musyrifah_menyetujui_pindah_kamar_memperbarui_penempatan_dan_kapasitas(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);
        $kamarAsal = $this->buatKamar('701', 7, 2);
        $kamarTujuan = $this->buatKamar('702', 7, 4);
        $kamarAsal->update(['terisi' => 1]);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamarAsal->id,
        ]);

        $pengajuan = PengajuanKamar::create([
            'user_id' => $mahasiswa->id,
            'kamar_id' => $kamarTujuan->id,
            'kamar_asal_id' => $kamarAsal->id,
            'jenis' => PengajuanKamar::JENIS_PINDAH,
            'alasan' => 'Butuh kamar yang lebih lapang.',
            'status' => PengajuanKamar::STATUS_MENUNGGU,
        ]);

        $response = $this->actingAs($musyrifah)->post(
            route('musyrifah.pengajuan_kamar.approve', $pengajuan->id)
        );

        $response->assertRedirect(route('musyrifah.data_kamar'));
        $response->assertSessionHas('success');

        // Penempatan berpindah ke kamar tujuan setelah disetujui
        $this->assertSame($kamarTujuan->id, $mahasiswa->fresh()->kamar_id);
        $this->assertSame(PengajuanKamar::STATUS_DISETUJUI, $pengajuan->fresh()->status);
        $this->assertSame($kamarAsal->id, $pengajuan->fresh()->kamar_asal_id);
        $this->assertSame($musyrifah->id, $pengajuan->fresh()->diverifikasi_oleh);

        // Kapasitas kamar lama dan kamar tujuan diperbarui
        $this->assertSame(0, $kamarAsal->fresh()->terisi);
        $this->assertSame('Tersedia', $kamarAsal->fresh()->status);
        $this->assertSame(1, $kamarTujuan->fresh()->terisi);

        // Riwayat perpindahan tetap tersimpan
        $this->assertDatabaseHas('pengajuan_kamars', [
            'id' => $pengajuan->id,
            'jenis' => PengajuanKamar::JENIS_PINDAH,
            'kamar_asal_id' => $kamarAsal->id,
            'status' => PengajuanKamar::STATUS_DISETUJUI,
        ]);
    }

    public function test_musyrifah_menolak_pindah_kamar_menyimpan_catatan_dan_penempatan_tetap(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);
        $kamarAsal = $this->buatKamar('801', 8, 2);
        $kamarTujuan = $this->buatKamar('802', 8, 4);
        $kamarAsal->update(['terisi' => 1]);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamarAsal->id,
        ]);

        $pengajuan = PengajuanKamar::create([
            'user_id' => $mahasiswa->id,
            'kamar_id' => $kamarTujuan->id,
            'kamar_asal_id' => $kamarAsal->id,
            'jenis' => PengajuanKamar::JENIS_PINDAH,
            'alasan' => 'Ingin dekat dengan teman sekelas.',
            'status' => PengajuanKamar::STATUS_MENUNGGU,
        ]);

        $response = $this->actingAs($musyrifah)->post(
            route('musyrifah.pengajuan_kamar.reject', $pengajuan->id),
            ['catatan' => 'Kamar tujuan diprioritaskan untuk mahasiswa baru.']
        );

        $response->assertRedirect(route('musyrifah.data_kamar'));

        $this->assertSame(PengajuanKamar::STATUS_DITOLAK, $pengajuan->fresh()->status);
        $this->assertSame('Kamar tujuan diprioritaskan untuk mahasiswa baru.', $pengajuan->fresh()->catatan);

        // Mahasiswa tetap menempati kamar lama
        $this->assertSame($kamarAsal->id, $mahasiswa->fresh()->kamar_id);
        $this->assertSame(1, $kamarAsal->fresh()->terisi);
        $this->assertSame(0, $kamarTujuan->fresh()->terisi);

        // Catatan penolakan terlihat oleh mahasiswa
        $kamarPage = $this->actingAs($mahasiswa->fresh())->get(route('kamar'));
        $kamarPage->assertOk();
        $kamarPage->assertSee('Pengajuan pindah kamar ditolak');
        $kamarPage->assertSee('Kamar tujuan diprioritaskan untuk mahasiswa baru.');
    }

    public function test_tombol_pilih_kamar_pada_pendaftaran_mengarah_ke_informasi_kamar(): void
    {
        $kamar = $this->buatKamar('901', 9, 4);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamar->id,
        ]);

        Pendaftaran::create([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->name,
            'nim' => '20269002',
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-01-01',
            'semester' => 1,
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'Perempuan',
            'no_wa' => '081234567890',
            'asal_sekolah' => 'MAN 2 Padang',
            'nama_ortu' => 'Fatimah',
            'no_hp_ortu' => '081298765432',
            'kategori' => 'umum',
            'tahun_angkatan' => 2026,
            'fakultas' => 'Tarbiyah',
            'program_studi' => 'Pendidikan Agama Islam',
            'alasan_daftar' => 'Ingin memperdalam ilmu agama.',
            'nominal_pembayaran' => 1000000,
            'bukti_penerimaan' => 'dokumen_pendaftaran/test_penerimaan.pdf',
            'bukti_pembayaran' => 'dokumen_pembayaran/test_bayar.jpg',
            'konfirmasi_data' => true,
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('pendaftaran.pilihKamarMhs'));

        $response->assertRedirect(route('kamar'));
    }

    private function buatKamar(
        string $nomor,
        int $lantai = 1,
        int $kapasitas = 4,
        string $status = 'Tersedia'
    ): Kamar {
        return Kamar::create([
            'nama_kamar' => 'Kamar '.$nomor,
            'nomor_kamar' => $nomor,
            'gedung' => 'Gedung A',
            'lantai' => $lantai,
            'kapasitas' => $kapasitas,
            'terisi' => 0,
            'status' => $status,
        ]);
    }
}
