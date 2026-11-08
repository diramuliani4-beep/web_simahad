<?php

namespace Tests\Feature;

use App\Models\SuratPerizinan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuratPerizinanMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat akun mahasiswa untuk pengujian alur perizinan.
     *
     * @param  array<string, mixed>  $atribut
     */
    private function buatMahasiswa(array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Aisyah Rahmadani',
            'email' => 'aisyah.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'nim' => fake()->unique()->numerify('2317020###'),
            'role' => 'mahasiswa',
        ], $atribut));
    }

    /**
     * Menyiapkan data izin pulang pada bulan yang sama dengan tanggal pengajuan.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function periodePulang(): array
    {
        $mulai = now()->addDays(3)->startOfDay();

        return [$mulai, $mulai->copy()->addDays(2)];
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get(route('surat.perizinan.mhs'))->assertRedirect(route('login'));
    }

    public function test_non_mahasiswa_cannot_open_surat_perizinan_page(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah']);

        $this->actingAs($musyrifah)
            ->get(route('surat.perizinan.mhs'))
            ->assertForbidden();
    }

    public function test_mahasiswa_can_open_surat_perizinan_page_with_quota_summary(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $response = $this->actingAs($mahasiswa)->get(route('surat.perizinan.mhs'));

        $response->assertOk();
        $response->assertSee('slot bulan ini');
        $response->assertSee('Izin Kegiatan Fakultas/Prodi');
        $response->assertSee('Izin Pulang Mingguan');
    }

    public function test_mahasiswa_can_open_permit_form_pages(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.kegiatan', ['jenis' => SuratPerizinan::JENIS_KEGIATAN_KAMPUS]))
            ->assertOk()
            ->assertSee('Izin Kegiatan Kampus');

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.pengajuan'))
            ->assertOk()
            ->assertSee('Izin Pulang Mingguan')
            ->assertSee('Riwayat Izin Pulang');

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.semester'))
            ->assertOk()
            ->assertSee('Surat Pulang Semester');
    }

    public function test_mahasiswa_can_submit_izin_kegiatan_with_supporting_document(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $mulai = now()->addDays(4)->startOfDay();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.kegiatan.store'), [
            'jenis' => SuratPerizinan::JENIS_KEGIATAN_KAMPUS,
            'nama_kegiatan' => 'Seminar Nasional Moderasi Beragama',
            'penyelenggara' => 'Fakultas Ushuluddin',
            'lokasi' => 'Aula Kampus UIN Imam Bonjol',
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '15:00',
            'alasan' => 'Mengikuti kegiatan akademik yang diwajibkan program studi.',
            'dokumen' => UploadedFile::fake()->create('surat-resmi.pdf', 120, 'application/pdf'),
        ]);

        $response->assertRedirect(route('surat.perizinan.mhs'));
        $response->assertSessionHas('success');

        $pengajuan = SuratPerizinan::query()->firstOrFail();

        $this->assertSame($mahasiswa->id, $pengajuan->user_id);
        $this->assertSame(SuratPerizinan::JENIS_KEGIATAN_KAMPUS, $pengajuan->jenis);
        $this->assertSame(SuratPerizinan::STATUS_MENUNGGU, $pengajuan->status);
        $this->assertSame('Seminar Nasional Moderasi Beragama', $pengajuan->nama_kegiatan);
        $this->assertSame(1, $pengajuan->durasi_hari);
        Storage::disk('local')->assertExists($pengajuan->dokumen_path);

        $this->assertDatabaseHas('surat_perizinan_riwayats', [
            'surat_perizinan_id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_izin_kegiatan_requires_supporting_document(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $mulai = now()->addDays(2)->startOfDay();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.kegiatan.store'), [
            'jenis' => SuratPerizinan::JENIS_KEGIATAN_FAKULTAS,
            'nama_kegiatan' => 'Kuliah Umum',
            'penyelenggara' => 'Prodi Pendidikan Agama Islam',
            'lokasi' => 'Gedung Fakultas Tarbiyah',
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->toDateString(),
            'alasan' => 'Mengikuti kuliah umum wajib mahasiswa semester tiga.',
        ]);

        $response->assertSessionHasErrors('dokumen');
        $this->assertDatabaseCount('surat_perizinans', 0);
    }

    public function test_izin_kegiatan_rejects_past_start_date(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.kegiatan.store'), [
            'jenis' => SuratPerizinan::JENIS_KEGIATAN_FAKULTAS,
            'nama_kegiatan' => 'Kegiatan Lampau',
            'penyelenggara' => 'Prodi Sejarah',
            'lokasi' => 'Auditorium',
            'tanggal_mulai' => now()->subDays(2)->toDateString(),
            'tanggal_selesai' => now()->subDays(1)->toDateString(),
            'alasan' => 'Kegiatan yang sudah berlalu tidak boleh diajukan.',
            'dokumen' => UploadedFile::fake()->create('surat.pdf', 90, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('tanggal_mulai');
        $this->assertDatabaseCount('surat_perizinans', 0);
    }

    public function test_mahasiswa_can_submit_izin_pulang_with_computed_duration(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        [$mulai, $kembali] = $this->periodePulang();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
            'lokasi' => 'Jl. By Pass KM 12, Padang',
            'alasan' => 'Pulang menjenguk orang tua yang sedang sakit.',
        ]);

        $response->assertRedirect(route('surat.perizinan.mhs'));

        $pengajuan = SuratPerizinan::query()->firstOrFail();

        $this->assertSame(SuratPerizinan::JENIS_PULANG, $pengajuan->jenis);
        $this->assertSame(3, $pengajuan->durasi_hari);
        $this->assertSame('Jl. By Pass KM 12, Padang', $pengajuan->lokasi);
        $this->assertDatabaseHas('surat_perizinan_riwayats', [
            'surat_perizinan_id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_izin_pulang_rejects_duration_more_than_fourteen_days(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        [$mulai] = $this->periodePulang();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays(20)->toDateString(),
            'lokasi' => 'Bukittinggi',
            'alasan' => 'Pulang dalam waktu yang terlalu lama untuk izin mingguan.',
        ]);

        $response->assertSessionHasErrors('tanggal_selesai');
        $this->assertDatabaseCount('surat_perizinans', 0);
    }

    public function test_izin_pulang_accepts_exactly_fourteen_days(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        [$mulai] = $this->periodePulang();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays(13)->toDateString(),
            'lokasi' => 'Solok',
            'alasan' => 'Pulang membantu keluarga selama libur kegiatan ma had.',
        ]);

        $response->assertRedirect(route('surat.perizinan.mhs'));
        $this->assertSame(14, SuratPerizinan::query()->firstOrFail()->durasi_hari);
    }

    public function test_pending_submissions_hold_quota_slots(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        [$mulai, $kembali] = $this->periodePulang();

        SuratPerizinan::factory()->pulang()->for($mahasiswa)->count(2)->create([
            'status' => SuratPerizinan::STATUS_MENUNGGU,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
            'lokasi' => 'Pariaman',
            'alasan' => 'Pengajuan ketiga pada bulan yang sama harus ditolak.',
        ]);

        $response->assertSessionHasErrors('tanggal_mulai');
        $this->assertDatabaseCount('surat_perizinans', 2);
    }

    public function test_rejected_submissions_do_not_consume_quota(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        [$mulai, $kembali] = $this->periodePulang();

        SuratPerizinan::factory()->pulang()->for($mahasiswa)->create([
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
        ]);

        SuratPerizinan::factory()->pulang()->for($mahasiswa)->create([
            'status' => SuratPerizinan::STATUS_DITOLAK,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
            'lokasi' => 'Padang Panjang',
            'alasan' => 'Masih tersedia satu slot karena satu pengajuan ditolak.',
        ]);

        $response->assertRedirect(route('surat.perizinan.mhs'));
        $this->assertDatabaseCount('surat_perizinans', 3);
    }

    public function test_cancelling_submission_returns_the_quota_slot(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        [$mulai, $kembali] = $this->periodePulang();

        $pengajuan = SuratPerizinan::factory()->pulang()->for($mahasiswa)->count(2)->create([
            'status' => SuratPerizinan::STATUS_MENUNGGU,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.batalkan', $pengajuan->first()))
            ->assertRedirect(route('surat.perizinan.mhs'));

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $pengajuan->first()->id,
            'status' => SuratPerizinan::STATUS_DIBATALKAN,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
            'lokasi' => 'Agam',
            'alasan' => 'Slot kuota kembali tersedia setelah pembatalan pengajuan.',
        ]);

        $response->assertRedirect(route('surat.perizinan.mhs'));
        $this->assertSame(3, SuratPerizinan::query()->where('jenis', SuratPerizinan::JENIS_PULANG)->count());
    }

    public function test_mahasiswa_cannot_cancel_approved_submission(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $pengajuan = SuratPerizinan::factory()->pulang()->disetujui()->for($mahasiswa)->create();

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.batalkan', $pengajuan))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_DISETUJUI,
        ]);
    }

    public function test_mahasiswa_cannot_download_other_student_document(): void
    {
        Storage::fake('local');

        $pemilik = $this->buatMahasiswa();
        $lain = $this->buatMahasiswa(['name' => 'Mahasiswa Lain', 'email' => 'lain@mahad.ac.id']);

        Storage::disk('local')->put('surat-perizinan/'.$pemilik->id.'/surat.pdf', 'isi surat');

        $pengajuan = SuratPerizinan::factory()->kegiatanFakultas()->for($pemilik)->create([
            'dokumen_path' => 'surat-perizinan/'.$pemilik->id.'/surat.pdf',
        ]);

        $this->actingAs($lain)
            ->get(route('surat.perizinan.mhs.dokumen', [$pengajuan, 'pendukung']))
            ->assertForbidden();
    }

    public function test_mahasiswa_can_download_own_document(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();

        Storage::disk('local')->put('surat-perizinan/'.$mahasiswa->id.'/surat.pdf', 'isi surat');

        $pengajuan = SuratPerizinan::factory()->kegiatanFakultas()->for($mahasiswa)->create([
            'dokumen_path' => 'surat-perizinan/'.$mahasiswa->id.'/surat.pdf',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.dokumen', [$pengajuan, 'pendukung']))
            ->assertOk();
    }

    public function test_semester_letter_flow_creates_number_and_accepts_signed_upload(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.semester.store'), [
                'alasan' => 'Pulang semester ganjil tahun akademik 2026/2027.',
            ])
            ->assertRedirect(route('surat.perizinan.mhs.semester'));

        $surat = SuratPerizinan::query()->firstOrFail();

        $this->assertSame(SuratPerizinan::JENIS_SEMESTER, $surat->jenis);
        $this->assertNotEmpty($surat->nomor_surat);
        $this->assertStringStartsWith('SPM/', $surat->nomor_surat);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.semester.cetak', $surat))
            ->assertOk()
            ->assertSee('SURAT IZIN PULANG SEMESTER')
            ->assertSee($mahasiswa->name)
            ->assertSee($surat->nomor_surat);

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.semester.unggah', $surat), [
                'surat_tertanda' => UploadedFile::fake()->create('surat-tertanda.pdf', 150, 'application/pdf'),
            ])
            ->assertRedirect(route('surat.perizinan.mhs.semester'));

        $surat->refresh();

        $this->assertTrue($surat->punyaSuratTertanda());
        $this->assertNotNull($surat->surat_diunggah_pada);
        $this->assertSame(SuratPerizinan::STATUS_MENUNGGU, $surat->status);
        Storage::disk('local')->assertExists($surat->surat_tertanda_path);
    }

    public function test_signed_semester_letter_cannot_be_reuploaded_after_verified(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();

        $surat = SuratPerizinan::factory()->semester()->for($mahasiswa)->create([
            'status' => SuratPerizinan::STATUS_SELESAI,
            'surat_tertanda_path' => 'surat-perizinan/'.$mahasiswa->id.'/tertanda/lama.pdf',
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.semester.unggah', $surat), [
                'surat_tertanda' => UploadedFile::fake()->create('surat-baru.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $surat->id,
            'status' => SuratPerizinan::STATUS_SELESAI,
            'surat_tertanda_path' => 'surat-perizinan/'.$mahasiswa->id.'/tertanda/lama.pdf',
        ]);
    }
}
