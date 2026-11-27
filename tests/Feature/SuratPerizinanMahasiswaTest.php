<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Pendaftaran;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-11-10 02:00:00'));
    }

    /**
     * Create an approved student with a room on the requested floor.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function buatMahasiswa(
        array $attributes = [],
        ?string $statusPendaftaran = 'disetujui',
        string $gedung = 'Gedung A',
        int $lantai = 1,
    ): User {
        $mahasiswa = User::factory()->create(array_merge([
            'name' => 'Aisyah Rahmadani',
            'email' => 'aisyah.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'nim' => fake()->unique()->numerify('2317020###'),
            'role' => 'mahasiswa',
        ], $attributes));

        $kamar = Kamar::factory()->create([
            'gedung' => $gedung,
            'lantai' => $lantai,
        ]);
        $mahasiswa->update(['kamar_id' => $kamar->id]);

        if ($statusPendaftaran !== null) {
            $this->buatPendaftaran($mahasiswa, $statusPendaftaran);
        }

        return $mahasiswa;
    }

    private function buatPendaftaran(User $mahasiswa, string $status = 'disetujui'): Pendaftaran
    {
        return Pendaftaran::query()->create([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->name,
            'nim' => $mahasiswa->nim,
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-01-01',
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '081234567890',
            'program_studi' => 'Sistem Informasi',
            'fakultas' => 'Sains dan Teknologi',
            'semester' => 3,
            'status' => $status,
            'tahun_angkatan' => '2023',
        ]);
    }

    /**
     * Create the active floor Musyrifah used for automatic routing.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function buatMusyrifah(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Ustadzah Hanifah',
            'email' => 'musyrifah.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'role' => 'musyrifah',
            'status' => 'aktif',
            'gedung_penempatan' => 'Gedung A',
            'lantai_penempatan' => 1,
        ], $attributes));
    }

    /**
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
        $musyrifah = $this->buatMusyrifah();

        $this->actingAs($musyrifah)
            ->get(route('surat.perizinan.mhs'))
            ->assertForbidden();
    }

    public function test_mahasiswa_can_open_surat_perizinan_page_with_four_submission_choices(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $response = $this->actingAs($mahasiswa)->get(route('surat.perizinan.mhs'));

        $response->assertSee('Slot bulan ini')
            ->assertSee('Izin Kegiatan Fakultas/Prodi')
            ->assertSee('Izin Pulang Mingguan')
            ->assertSee('Izin Pulang Darurat/Khusus')
            ->assertSee('Izin Pulang Semester')
            ->assertDontSee('Izin Kegiatan Kampus');
    }

    public function test_mahasiswa_can_open_all_four_permit_form_pages(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.kegiatan'))
            ->assertOk()
            ->assertSee('Izin Kegiatan Fakultas/Prodi')
            ->assertSee('Surat Resmi Fakultas/Prodi');

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.pengajuan'))
            ->assertOk()
            ->assertSee('Izin Pulang Mingguan')
            ->assertSee('Riwayat Izin Pulang');

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.pengajuan.darurat'))
            ->assertOk()
            ->assertSee('Izin Pulang Darurat/Khusus')
            ->assertSee('tidak menggunakan kuota reguler');

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.semester'))
            ->assertOk()
            ->assertSee('Izin Pulang Semester')
            ->assertSee('Identitas pada Surat');
    }

    public function test_history_tables_include_responsive_labels(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();

        SuratPerizinan::factory()->kegiatanFakultas()->create(['user_id' => $mahasiswa->id]);
        SuratPerizinan::factory()->pulang()->create(['user_id' => $mahasiswa->id]);
        SuratPerizinan::factory()->semester()->create(['user_id' => $mahasiswa->id]);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.kegiatan'))
            ->assertOk()
            ->assertSee('sp-table--kegiatan', false)
            ->assertSee('data-label="Tanggal Ajuan"', false)
            ->assertSee('data-label="Berkas"', false);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.pengajuan'))
            ->assertOk()
            ->assertSee('sp-table--pulang', false)
            ->assertSee('data-label="Catatan Musyrifah"', false);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.semester'))
            ->assertOk()
            ->assertSee('sp-table--semester', false)
            ->assertSee('data-label="Waktu Keluar / Kembali"', false);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs'))
            ->assertOk()
            ->assertSee('data-label="Aksi"', false)
            ->assertSee('container-type: inline-size', false)
            ->assertSee('@container (max-width: 410px)', false);
    }

    public function test_submission_forms_render_shared_layout_and_head_styles(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();

        $routes = [
            'surat.perizinan.mhs.kegiatan',
            'surat.perizinan.mhs.pengajuan',
            'surat.perizinan.mhs.pengajuan.darurat',
            'surat.perizinan.mhs.semester',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($mahasiswa)
                ->get(route($route))
                ->assertOk()
                ->assertSee('class="sp-perizinan-page"', false)
                ->assertSee('class="sp-wrap"', false)
                ->assertSee('.sp-perizinan-page .sp-field :is(input, select, textarea)', false)
                ->assertSee('min-height: 48px', false);

            $content = $response->getContent();
            $stylePosition = strpos($content, '.sp-perizinan-page .sp-field :is(input, select, textarea)');
            $mainPosition = strpos($content, '<main class="main-content"');

            $this->assertIsInt($stylePosition);
            $this->assertIsInt($mainPosition);
            $this->assertLessThan($mainPosition, $stylePosition);
        }
    }

    public function test_mahasiswa_can_submit_izin_kegiatan_with_supporting_document(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $mulai = now()->addDays(4)->startOfDay();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.kegiatan.store'), [
            'jenis' => SuratPerizinan::JENIS_KEGIATAN_FAKULTAS,
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
        $this->assertSame($musyrifah->id, $pengajuan->musyrifah_id);
        $this->assertSame(SuratPerizinan::JENIS_KEGIATAN_FAKULTAS, $pengajuan->jenis);
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
        $this->buatMusyrifah();
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

    public function test_izin_kegiatan_rejects_legacy_campus_activity_type(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();
        $mulai = now()->addDays(2)->startOfDay();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.kegiatan.store'), [
            'jenis' => SuratPerizinan::JENIS_KEGIATAN_KAMPUS,
            'nama_kegiatan' => 'Kegiatan Kampus Lama',
            'penyelenggara' => 'UIN Imam Bonjol',
            'lokasi' => 'Auditorium',
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->toDateString(),
            'alasan' => 'Jenis pengajuan lama tidak lagi tersedia.',
            'dokumen' => UploadedFile::fake()->create('surat.pdf', 90, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('jenis');
        $this->assertDatabaseCount('surat_perizinans', 0);
    }

    public function test_izin_kegiatan_rejects_past_start_date(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();

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
        $musyrifah = $this->buatMusyrifah();
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
        $this->assertSame($musyrifah->id, $pengajuan->musyrifah_id);
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
        $this->buatMusyrifah();
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

    public function test_pending_submissions_do_not_consume_quota(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();
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
            'alasan' => 'Pengajuan ketiga tetap dapat dikirim karena dua pengajuan lain masih menunggu.',
        ]);

        $response->assertRedirect(route('surat.perizinan.mhs'));
        $this->assertDatabaseCount('surat_perizinans', 3);
        $this->assertSame(0, SuratPerizinan::kuotaPulang($mahasiswa, $mulai)['terpakai']);
    }

    public function test_rejected_submissions_do_not_consume_quota(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();
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

        $tanggalBaru = $mulai->copy()->addDays(14);
        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $tanggalBaru->toDateString(),
            'tanggal_selesai' => $tanggalBaru->copy()->addDays(2)->toDateString(),
            'lokasi' => 'Padang Panjang',
            'alasan' => 'Masih tersedia satu slot karena satu pengajuan ditolak.',
        ]);

        $response->assertRedirect(route('surat.perizinan.mhs'));
        $this->assertDatabaseCount('surat_perizinans', 3);
        $this->assertSame(1, SuratPerizinan::kuotaPulang($mahasiswa, $tanggalBaru)['terpakai']);
    }

    public function test_cancelling_pending_submission_does_not_create_quota_usage(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();
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
            'alasan' => 'Pengajuan baru tetap dapat dikirim setelah pengajuan lain dibatalkan.',
        ]);

        $response->assertRedirect(route('surat.perizinan.mhs'));
        $this->assertSame(3, SuratPerizinan::query()->where('jenis', SuratPerizinan::JENIS_PULANG)->count());
        $this->assertSame(0, SuratPerizinan::kuotaPulang($mahasiswa, $mulai)['terpakai']);
    }

    public function test_weekly_submission_is_blocked_when_monthly_quota_is_full(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();
        [$mulai, $kembali] = $this->periodePulang();
        $kedua = $mulai->copy()->addDays(14);

        SuratPerizinan::factory()->pulang()->for($mahasiswa)->create([
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
        ]);
        SuratPerizinan::factory()->pulang()->for($mahasiswa)->create([
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $kedua->toDateString(),
            'tanggal_selesai' => $kedua->copy()->addDays(2)->toDateString(),
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $kedua->toDateString(),
            'tanggal_selesai' => $kedua->copy()->addDays(2)->toDateString(),
            'lokasi' => 'Padang',
            'alasan' => 'Pengajuan reguler ketiga harus ditolak karena kuota habis.',
        ]);

        $response->assertSessionHasErrors('tanggal_mulai');
        $this->assertDatabaseCount('surat_perizinans', 2);
    }

    public function test_weekly_submission_requires_fourteen_day_spacing(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();
        [$mulai, $kembali] = $this->periodePulang();
        $terlaluDekat = $mulai->copy()->addDays(13);

        SuratPerizinan::factory()->pulang()->for($mahasiswa)->create([
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $terlaluDekat->toDateString(),
            'tanggal_selesai' => $terlaluDekat->copy()->addDays(2)->toDateString(),
            'lokasi' => 'Padang',
            'alasan' => 'Jarak tiga belas hari belum memenuhi aturan dua minggu.',
        ]);

        $response->assertSessionHasErrors('tanggal_mulai');
        $this->assertDatabaseCount('surat_perizinans', 1);
    }

    public function test_emergency_submission_remains_available_when_regular_quota_is_full(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        [$mulai, $kembali] = $this->periodePulang();
        $kedua = $mulai->copy()->addDays(14);

        SuratPerizinan::factory()->pulang()->for($mahasiswa)->create([
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
        ]);
        SuratPerizinan::factory()->pulang()->for($mahasiswa)->create([
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $kedua->toDateString(),
            'tanggal_selesai' => $kedua->copy()->addDays(2)->toDateString(),
        ]);

        $darurat = $mulai->copy()->addDay();
        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.darurat.store'), [
            'tanggal_mulai' => $darurat->toDateString(),
            'tanggal_selesai' => $darurat->copy()->addDays(20)->toDateString(),
            'lokasi' => 'Bukittinggi',
            'alasan' => 'Kemalangan keluarga memerlukan kehadiran segera.',
            'keterangan' => 'Kondisi mendesak keluarga.',
            'transportasi' => 'Dijemput keluarga',
            'dokumen' => UploadedFile::fake()->create('bukti-darurat.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('surat.perizinan.mhs'));

        $pengajuan = SuratPerizinan::query()->where('jenis', SuratPerizinan::JENIS_DARURAT)->firstOrFail();
        $this->assertSame($musyrifah->id, $pengajuan->musyrifah_id);
        $this->assertSame(21, $pengajuan->durasi_hari);
        $this->assertSame(2, SuratPerizinan::kuotaPulang($mahasiswa, $darurat)['terpakai']);
        Storage::disk('local')->assertExists($pengajuan->dokumen_path);
    }

    public function test_submission_requires_an_active_musyrifah_for_the_student_floor(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        [$mulai, $kembali] = $this->periodePulang();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
            'lokasi' => 'Padang',
            'alasan' => 'Pengajuan tidak boleh tersimpan tanpa Musyrifah penanggung jawab.',
        ]);

        $response->assertSessionHasErrors('tanggal_mulai');
        $this->assertDatabaseCount('surat_perizinans', 0);
    }

    public function test_submission_is_blocked_until_registration_is_approved(): void
    {
        $mahasiswa = $this->buatMahasiswa([], 'menunggu');
        $this->buatMusyrifah();
        [$mulai, $kembali] = $this->periodePulang();

        $response = $this->actingAs($mahasiswa)->post(route('surat.perizinan.mhs.pengajuan.store'), [
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $kembali->toDateString(),
            'lokasi' => 'Padang',
            'alasan' => 'Pendaftaran yang belum disetujui tidak boleh membuat izin.',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('surat_perizinans', 0);
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

    public function test_semester_letter_flow_creates_number_prints_identity_and_downloads_pdf(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        [$mulai, $kembali] = $this->periodePulang();

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.semester.store'), [
                'tanggal_mulai' => $mulai->toDateString(),
                'tanggal_selesai' => $kembali->copy()->addDays(20)->toDateString(),
                'lokasi' => 'Bukittinggi',
                'alasan' => 'Pulang semester ganjil tahun akademik 2026/2027.',
                'keterangan' => 'Libur akhir semester.',
                'transportasi' => 'Dijemput keluarga',
            ])
            ->assertRedirect(route('surat.perizinan.mhs.semester'));

        $surat = SuratPerizinan::query()->firstOrFail();

        $this->assertSame(SuratPerizinan::JENIS_SEMESTER, $surat->jenis);
        $this->assertSame($musyrifah->id, $surat->musyrifah_id);
        $this->assertNotEmpty($surat->nomor_surat);
        $this->assertStringStartsWith('SPM/', $surat->nomor_surat);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.semester.cetak', $surat))
            ->assertOk()
            ->assertSee('SURAT IZIN PULANG MAHASANTRI')
            ->assertSee($mahasiswa->name)
            ->assertSee($mahasiswa->nim)
            ->assertSee('Sistem Informasi')
            ->assertSee($surat->nomor_surat);

        $pdf = $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs.semester.pdf', $surat));

        $pdf->assertHeader('Content-Type', 'application/pdf');
        $this->assertMatchesRegularExpression(
            '/^attachment; filename="surat-izin-pulang-.+\.pdf"$/',
            $pdf->headers->get('Content-Disposition') ?? '',
        );
        $this->assertStringStartsWith('%PDF-1.4', $pdf->getContent());
        $this->assertStringContainsString('SURAT IZIN PULANG MAHASANTRI', $pdf->getContent());
        $this->assertStringContainsString($mahasiswa->name, $pdf->getContent());
    }

    public function test_semester_letter_accepts_signed_upload_and_clears_stale_decision(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $this->buatMusyrifah();
        [$mulai, $kembali] = $this->periodePulang();

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.semester.store'), [
                'tanggal_mulai' => $mulai->toDateString(),
                'tanggal_selesai' => $kembali->toDateString(),
                'lokasi' => 'Bukittinggi',
                'alasan' => 'Pulang semester ganjil tahun akademik 2026/2027.',
            ])
            ->assertRedirect(route('surat.perizinan.mhs.semester'));

        $surat = SuratPerizinan::query()->firstOrFail();
        $surat->update([
            'keputusan_nama' => 'Keputusan lama',
            'keputusan_pada' => now()->subDay(),
            'catatan_musyrifah' => 'Catatan lama',
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.semester.unggah', $surat), [
                'surat_tertanda' => UploadedFile::fake()->create('surat-tertanda.pdf', 150, 'application/pdf'),
            ])
            ->assertRedirect(route('surat.perizinan.mhs.semester'));

        $surat->refresh();

        $this->assertTrue($surat->punyaSuratTertanda());
        $this->assertNotNull($surat->surat_diunggah_pada);
        $this->assertSame(SuratPerizinan::STATUS_MENUNGGU, $surat->status);
        $this->assertNull($surat->keputusan_nama);
        $this->assertNull($surat->keputusan_pada);
        $this->assertNull($surat->catatan_musyrifah);
        Storage::disk('local')->assertExists($surat->surat_tertanda_path);
    }

    public function test_signed_semester_letter_cannot_be_reuploaded_after_completed(): void
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
