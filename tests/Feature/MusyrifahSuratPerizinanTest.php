<?php

namespace Tests\Feature;

use App\Models\SuratPerizinan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MusyrifahSuratPerizinanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat akun mahasiswa pemilik pengajuan.
     *
     * @param  array<string, mixed>  $atribut
     */
    private function buatMahasiswa(array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Nabila Zulfa',
            'email' => 'nabila.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'nim' => fake()->unique()->numerify('2317020###'),
            'role' => 'mahasiswa',
        ], $atribut));
    }

    /**
     * Membuat akun Musyrifah yang berwenang memeriksa pengajuan.
     */
    private function buatMusyrifah(): User
    {
        return User::factory()->create([
            'name' => 'Ustadzah Hanifah',
            'email' => 'musyrifah.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'role' => 'musyrifah',
        ]);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get(route('musyrifah.surat_perizinan'))->assertRedirect(route('login'));
    }

    public function test_mahasiswa_cannot_open_musyrifah_surat_perizinan(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $this->actingAs($mahasiswa)
            ->get(route('musyrifah.surat_perizinan'))
            ->assertForbidden();
    }

    public function test_mahasiswa_submission_appears_in_musyrifah_list(): void
    {
        $mahasiswa = $this->buatMahasiswa([
            'nim' => '2317020202',
        ]);
        $musyrifah = $this->buatMusyrifah();

        SuratPerizinan::factory()->kegiatanFakultas()->for($mahasiswa)->create([
            'nama_kegiatan' => 'Seminar Pendidikan Karakter',
            'lokasi' => 'Aula Fakultas Tarbiyah',
        ]);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.surat_perizinan'));

        $response->assertOk();
        $response->assertSee('Nabila Zulfa');
        $response->assertSee('2317020202');
        $response->assertSee('Izin Kegiatan Fakultas/Prodi');
        $response->assertSee('Menunggu Persetujuan');
        $response->assertSee('Aula Fakultas Tarbiyah');
    }

    public function test_musyrifah_can_filter_submissions_by_jenis_and_nama(): void
    {
        $musyrifah = $this->buatMusyrifah();

        $pemilikPulang = $this->buatMahasiswa([
            'name' => 'Ridwan Pulang',
            'email' => 'ridwan@mahad.ac.id',
            'nim' => '2317020303',
        ]);

        $pemilikKegiatan = $this->buatMahasiswa([
            'name' => 'Salma Kegiatan',
            'email' => 'salma@mahad.ac.id',
            'nim' => '2317020404',
        ]);

        SuratPerizinan::factory()->pulang()->for($pemilikPulang)->create([
            'lokasi' => 'Tujuan Pulang Ridwan',
        ]);

        SuratPerizinan::factory()->kegiatanKampus()->for($pemilikKegiatan)->create([
            'lokasi' => 'Lokasi Kegiatan Salma',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan', ['jenis' => SuratPerizinan::JENIS_PULANG]))
            ->assertOk()
            ->assertSee('Tujuan Pulang Ridwan')
            ->assertDontSee('Lokasi Kegiatan Salma');

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan', ['nama' => 'Salma']))
            ->assertOk()
            ->assertSee('Lokasi Kegiatan Salma')
            ->assertDontSee('Tujuan Pulang Ridwan');

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan', ['status' => SuratPerizinan::STATUS_MENUNGGU]))
            ->assertOk()
            ->assertSee('Menunggu Persetujuan');
    }

    public function test_musyrifah_can_open_submission_detail(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $pengajuan = SuratPerizinan::factory()->pulang()->for($mahasiswa)->create([
            'alasan' => 'Pulang menjenguk orang tua yang sakit.',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan.show', $pengajuan))
            ->assertOk()
            ->assertSee('Detail Pengajuan Perizinan')
            ->assertSee('Nabila Zulfa')
            ->assertSee('Pulang menjenguk orang tua yang sakit.')
            ->assertSee('Riwayat Perubahan Status')
            ->assertSee('Ambil Keputusan');
    }

    public function test_musyrifah_can_open_detail_of_semester_letter(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $surat = SuratPerizinan::factory()->semester()->for($mahasiswa)->create([
            'surat_tertanda_path' => 'surat-perizinan/'.$mahasiswa->id.'/tertanda/surat.pdf',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan.show', $surat))
            ->assertOk()
            ->assertSee('Verifikasi Surat Bertanda Tangan')
            ->assertSee('Sudah Diverifikasi')
            ->assertSee('Perlu Perbaikan');
    }

    public function test_approval_updates_student_history_with_note_and_time(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $pengajuan = SuratPerizinan::factory()->pulang()->for($mahasiswa)->create();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
                'catatan_musyrifah' => 'Izin disetujui, kembali tepat waktu.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $pengajuan));

        $pengajuan->refresh();

        $this->assertSame(SuratPerizinan::STATUS_DISETUJUI, $pengajuan->status);
        $this->assertSame($musyrifah->id, $pengajuan->keputusan_oleh);
        $this->assertSame('Ustadzah Hanifah', $pengajuan->keputusan_nama);
        $this->assertNotNull($pengajuan->keputusan_pada);
        $this->assertSame('Izin disetujui, kembali tepat waktu.', $pengajuan->catatan_musyrifah);

        $this->assertDatabaseHas('surat_perizinan_riwayats', [
            'surat_perizinan_id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'nama_pengubah' => 'Ustadzah Hanifah',
            'catatan' => 'Izin disetujui, kembali tepat waktu.',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs'))
            ->assertOk()
            ->assertSee('Disetujui')
            ->assertSee('Izin disetujui, kembali tepat waktu.');
    }

    public function test_rejection_requires_note_and_updates_student_history(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $pengajuan = SuratPerizinan::factory()->pulang()->for($mahasiswa)->create();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DITOLAK,
            ])
            ->assertSessionHasErrors('catatan_musyrifah');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DITOLAK,
                'catatan_musyrifah' => 'Alasan pengajuan belum lengkap.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $pengajuan));

        $pengajuan->refresh();

        $this->assertSame(SuratPerizinan::STATUS_DITOLAK, $pengajuan->status);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs'))
            ->assertOk()
            ->assertSee('Ditolak')
            ->assertSee('Alasan pengajuan belum lengkap.');
    }

    public function test_submission_cannot_be_decided_twice(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $pengajuan = SuratPerizinan::factory()->pulang()->disetujui()->for($mahasiswa)->create();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DITOLAK,
                'catatan_musyrifah' => 'Mencoba mengubah keputusan yang sudah final.',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_DISETUJUI,
        ]);
    }

    public function test_semester_letter_cannot_be_decided_through_keputusan_endpoint(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $surat = SuratPerizinan::factory()->semester()->for($mahasiswa)->create();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $surat), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $surat->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_semester_letter_requires_signed_document_before_verification(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $surat = SuratPerizinan::factory()->semester()->for($mahasiswa)->create();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.verifikasi', $surat), [
                'status' => SuratPerizinan::STATUS_SELESAI,
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $surat->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_semester_verification_and_reupload_flow(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $pathAwal = 'surat-perizinan/'.$mahasiswa->id.'/tertanda/awal.pdf';
        Storage::disk('local')->put($pathAwal, 'berkas awal');

        $surat = SuratPerizinan::factory()->semester()->for($mahasiswa)->create([
            'surat_tertanda_path' => $pathAwal,
            'surat_diunggah_pada' => now(),
        ]);

        // Musyrifah meminta perbaikan surat.
        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.verifikasi', $surat), [
                'status' => SuratPerizinan::STATUS_PERLU_PERBAIKAN,
                'catatan_musyrifah' => 'Tanda tangan orang tua belum terbaca.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $surat));

        $surat->refresh();

        $this->assertSame(SuratPerizinan::STATUS_PERLU_PERBAIKAN, $surat->status);
        $this->assertSame('Tanda tangan orang tua belum terbaca.', $surat->catatan_musyrifah);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs'))
            ->assertOk()
            ->assertSee('Perlu Perbaikan')
            ->assertSee('Tanda tangan orang tua belum terbaca.');

        // Mahasiswa mengunggah ulang surat yang sudah diperbaiki.
        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.semester.unggah', $surat), [
                'surat_tertanda' => UploadedFile::fake()->create('surat-perbaikan.pdf', 140, 'application/pdf'),
            ])
            ->assertRedirect(route('surat.perizinan.mhs.semester'));

        $surat->refresh();

        $this->assertSame(SuratPerizinan::STATUS_MENUNGGU, $surat->status);
        $this->assertNotSame($pathAwal, $surat->surat_tertanda_path);
        Storage::disk('local')->assertExists($surat->surat_tertanda_path);

        // Musyrifah memverifikasi surat hasil perbaikan.
        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.verifikasi', $surat), [
                'status' => SuratPerizinan::STATUS_SELESAI,
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $surat));

        $surat->refresh();

        $this->assertSame(SuratPerizinan::STATUS_SELESAI, $surat->status);
        $this->assertDatabaseHas('surat_perizinan_riwayats', [
            'surat_perizinan_id' => $surat->id,
            'status' => SuratPerizinan::STATUS_SELESAI,
            'nama_pengubah' => 'Ustadzah Hanifah',
        ]);
    }

    public function test_verification_requires_note_when_requesting_revision(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $surat = SuratPerizinan::factory()->semester()->for($mahasiswa)->create([
            'surat_tertanda_path' => 'surat-perizinan/'.$mahasiswa->id.'/tertanda/surat.pdf',
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.verifikasi', $surat), [
                'status' => SuratPerizinan::STATUS_PERLU_PERBAIKAN,
            ])
            ->assertSessionHasErrors('catatan_musyrifah');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $surat->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_musyrifah_can_download_submission_documents(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $path = 'surat-perizinan/'.$mahasiswa->id.'/surat.pdf';
        Storage::disk('local')->put($path, 'isi surat resmi');

        $pengajuan = SuratPerizinan::factory()->kegiatanKampus()->for($mahasiswa)->create([
            'dokumen_path' => $path,
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan.dokumen', [$pengajuan, 'pendukung']))
            ->assertOk();
    }

    public function test_student_cannot_download_document_through_musyrifah_route(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();

        $path = 'surat-perizinan/'.$mahasiswa->id.'/surat.pdf';
        Storage::disk('local')->put($path, 'isi surat resmi');

        $pengajuan = SuratPerizinan::factory()->kegiatanKampus()->for($mahasiswa)->create([
            'dokumen_path' => $path,
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('musyrifah.surat_perizinan.dokumen', [$pengajuan, 'pendukung']))
            ->assertForbidden();
    }

    public function test_kuota_monitoring_lists_quota_usage_of_current_month(): void
    {
        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        $mulai = now()->startOfMonth()->addDays(2);

        SuratPerizinan::factory()->pulang()->for($mahasiswa)->count(2)->create([
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDay()->toDateString(),
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan'))
            ->assertOk()
            ->assertSee('Monitoring Kuota Izin Pulang')
            ->assertSee('Kuota habis');
    }
}
