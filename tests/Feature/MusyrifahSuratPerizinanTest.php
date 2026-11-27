<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\SuratPerizinan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MusyrifahSuratPerizinanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-11-10 02:00:00'));
    }

    /**
     * Create a student whose room determines the responsible Musyrifah.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function buatMahasiswa(
        array $attributes = [],
        string $gedung = 'Gedung A',
        int $lantai = 1,
    ): User {
        $mahasiswa = User::factory()->create(array_merge([
            'name' => 'Nabila Zulfa',
            'email' => 'nabila.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'nim' => fake()->unique()->numerify('2317020###'),
            'role' => 'mahasiswa',
        ], $attributes));

        $kamar = Kamar::factory()->create([
            'gedung' => $gedung,
            'lantai' => $lantai,
        ]);
        $mahasiswa->update(['kamar_id' => $kamar->id]);

        return $mahasiswa;
    }

    /**
     * Create an active Musyrifah with an explicit building and floor.
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
     * Create a submission using a named factory state and an explicit assignment.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function buatPengajuan(
        User $mahasiswa,
        ?User $musyrifah,
        string $jenis = SuratPerizinan::JENIS_PULANG,
        array $attributes = [],
    ): SuratPerizinan {
        $factory = match ($jenis) {
            SuratPerizinan::JENIS_KEGIATAN_FAKULTAS => SuratPerizinan::factory()->kegiatanFakultas(),
            SuratPerizinan::JENIS_PULANG => SuratPerizinan::factory()->pulang(),
            SuratPerizinan::JENIS_DARURAT => SuratPerizinan::factory()->darurat(),
            SuratPerizinan::JENIS_SEMESTER => SuratPerizinan::factory()->semester(),
            default => SuratPerizinan::factory(),
        };

        return $factory->for($mahasiswa)->create(array_merge([
            'jenis' => $jenis,
            'musyrifah_id' => $musyrifah?->id,
        ], $attributes));
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

        $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_KEGIATAN_FAKULTAS, [
            'nama_kegiatan' => 'Seminar Pendidikan Karakter',
            'lokasi' => 'Aula Fakultas Tarbiyah',
        ]);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.surat_perizinan'));

        $response->assertSee('Nabila Zulfa')
            ->assertSee('2317020202')
            ->assertSee('Izin Kegiatan Fakultas/Prodi')
            ->assertSee('Menunggu Persetujuan')
            ->assertSee('Aula Fakultas Tarbiyah');
    }

    public function test_list_and_detail_views_use_content_aware_responsive_styles(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan'))
            ->assertOk()
            ->assertSee('container: perizinan-layout / inline-size', false)
            ->assertSee('data-label="Aksi"', false)
            ->assertSee('@container perizinan-layout (max-width: 440px)', false);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan.show', $pengajuan))
            ->assertOk()
            ->assertSee('container: rincian-perizinan / inline-size', false)
            ->assertSee('@container rincian-perizinan (max-width: 480px)', false);
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

        $this->buatPengajuan($pemilikPulang, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'lokasi' => 'Tujuan Pulang Ridwan',
        ]);
        $this->buatPengajuan($pemilikKegiatan, $musyrifah, SuratPerizinan::JENIS_KEGIATAN_FAKULTAS, [
            'lokasi' => 'Lokasi Kegiatan Salma',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan', ['jenis' => SuratPerizinan::JENIS_PULANG]))
            ->assertSee('Tujuan Pulang Ridwan')
            ->assertDontSee('Lokasi Kegiatan Salma');

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan', ['nama' => 'Salma']))
            ->assertSee('Lokasi Kegiatan Salma')
            ->assertDontSee('Tujuan Pulang Ridwan');

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan', ['status' => SuratPerizinan::STATUS_MENUNGGU]))
            ->assertSee('Menunggu Persetujuan');
    }

    public function test_musyrifah_can_open_submission_detail(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'alasan' => 'Pulang menjenguk orang tua yang sakit.',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan.show', $pengajuan))
            ->assertOk()
            ->assertSee('Detail Pengajuan Perizinan')
            ->assertSee('Nabila Zulfa')
            ->assertSee('Pulang menjenguk orang tua yang sakit.')
            ->assertSee('Riwayat Perubahan Status')
            ->assertSee('Pemeriksaan Dokumen &amp; Keputusan', false)
            ->assertSee('Simpan Keputusan');
    }

    public function test_musyrifah_can_open_detail_of_semester_letter(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER, [
            'surat_tertanda_path' => 'surat-perizinan/'.$mahasiswa->id.'/tertanda/surat.pdf',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan.show', $surat))
            ->assertOk()
            ->assertSee('Pemeriksaan Dokumen &amp; Keputusan', false)
            ->assertSee('Perlu Perbaikan')
            ->assertDontSee('Sudah Diverifikasi');
    }

    public function test_approval_updates_student_history_with_note_and_time(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

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
        $this->assertSame(1, SuratPerizinan::kuotaPulang($mahasiswa, $pengajuan->tanggal_mulai)['terpakai']);

        $this->assertDatabaseHas('surat_perizinan_riwayats', [
            'surat_perizinan_id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'nama_pengubah' => 'Ustadzah Hanifah',
            'catatan' => 'Izin disetujui, kembali tepat waktu.',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs'))
            ->assertSee('Disetujui')
            ->assertSee('Izin disetujui, kembali tepat waktu.');
    }

    public function test_rejection_requires_note_and_updates_student_history(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

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
            ->assertSee('Ditolak')
            ->assertSee('Alasan pengajuan belum lengkap.');
    }

    public function test_submission_cannot_be_decided_twice(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
        ]);

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

    public function test_semester_letter_cannot_be_completed_directly_by_decision(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $surat), [
                'keputusan' => SuratPerizinan::STATUS_SELESAI,
            ])
            ->assertSessionHasErrors('keputusan');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $surat->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_semester_letter_requires_signed_document_before_approval(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER);

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

    public function test_semester_correction_reupload_and_approval_flow(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $surat), [
                'keputusan' => SuratPerizinan::STATUS_PERLU_PERBAIKAN,
                'catatan_musyrifah' => 'Tanda tangan orang tua belum terbaca.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $surat));

        $surat->refresh();

        $this->assertSame(SuratPerizinan::STATUS_PERLU_PERBAIKAN, $surat->status);
        $this->assertSame('Tanda tangan orang tua belum terbaca.', $surat->catatan_musyrifah);

        $this->actingAs($mahasiswa)
            ->get(route('surat.perizinan.mhs'))
            ->assertSee('Perlu Perbaikan')
            ->assertSee('Tanda tangan orang tua belum terbaca.');

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.semester.unggah', $surat), [
                'surat_tertanda' => UploadedFile::fake()->create('surat-perbaikan.pdf', 140, 'application/pdf'),
            ])
            ->assertRedirect(route('surat.perizinan.mhs.semester'));

        $surat->refresh();

        $this->assertSame(SuratPerizinan::STATUS_MENUNGGU, $surat->status);
        $this->assertNull($surat->catatan_musyrifah);
        Storage::disk('local')->assertExists($surat->surat_tertanda_path);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $surat), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
                'catatan_musyrifah' => 'Surat hasil perbaikan sudah lengkap.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $surat));

        $surat->refresh();

        $this->assertSame(SuratPerizinan::STATUS_DISETUJUI, $surat->status);
        $this->assertDatabaseHas('surat_perizinan_riwayats', [
            'surat_perizinan_id' => $surat->id,
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'nama_pengubah' => 'Ustadzah Hanifah',
        ]);
    }

    public function test_semester_correction_requires_note(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $surat), [
                'keputusan' => SuratPerizinan::STATUS_PERLU_PERBAIKAN,
            ])
            ->assertSessionHasErrors('catatan_musyrifah');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $surat->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_activity_approval_requires_official_supporting_document(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_KEGIATAN_FAKULTAS);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_weekly_approval_rechecks_monthly_quota(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $mulai = now()->addDays(3)->startOfDay();
        $kedua = $mulai->copy()->addDays(14);

        $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays(2)->toDateString(),
        ]);
        $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $kedua->toDateString(),
            'tanggal_selesai' => $kedua->copy()->addDays(2)->toDateString(),
        ]);
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'tanggal_mulai' => $kedua->copy()->addDay()->toDateString(),
            'tanggal_selesai' => $kedua->copy()->addDays(3)->toDateString(),
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
            ])
            ->assertSessionHasErrors('tanggal_mulai');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_weekly_approval_rechecks_fourteen_day_spacing(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $mulai = now()->addDays(3)->startOfDay();
        $terlaluDekat = $mulai->copy()->addDays(13);

        $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays(2)->toDateString(),
        ]);
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'tanggal_mulai' => $terlaluDekat->toDateString(),
            'tanggal_selesai' => $terlaluDekat->copy()->addDays(2)->toDateString(),
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
            ])
            ->assertSessionHasErrors('tanggal_mulai');

        $this->assertDatabaseHas('surat_perizinans', [
            'id' => $pengajuan->id,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
        ]);
    }

    public function test_emergency_approval_does_not_consume_regular_quota_or_spacing(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $mulai = now()->addDays(3)->startOfDay();
        $kedua = $mulai->copy()->addDays(14);

        $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays(2)->toDateString(),
        ]);
        $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $kedua->toDateString(),
            'tanggal_selesai' => $kedua->copy()->addDays(2)->toDateString(),
        ]);
        $darurat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_DARURAT, [
            'tanggal_mulai' => $mulai->copy()->addDay()->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays(3)->toDateString(),
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $darurat), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
                'catatan_musyrifah' => 'Diproses khusus karena kondisi keluarga mendesak.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $darurat));

        $darurat->refresh();

        $this->assertSame(SuratPerizinan::STATUS_DISETUJUI, $darurat->status);
        $this->assertSame(2, SuratPerizinan::kuotaPulang($mahasiswa, $darurat->tanggal_mulai)['terpakai']);
    }

    public function test_semester_departure_and_return_record_wib_times_and_complete_letter(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $path = 'surat-perizinan/'.$mahasiswa->id.'/tertanda/surat.pdf';
        Storage::disk('local')->put($path, 'surat bertanda tangan');
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER, [
            'surat_tertanda_path' => $path,
            'surat_diunggah_pada' => now(),
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $surat), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $surat));

        $keluar = Carbon::now('Asia/Jakarta')->subHours(2)->startOfMinute();
        $kembali = Carbon::now('Asia/Jakarta')->subHour()->startOfMinute();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.waktu', $surat), [
                'waktu_keluar' => $keluar->format('Y-m-d\TH:i'),
                'waktu_kembali' => $kembali->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $surat));

        $surat->refresh();

        $this->assertSame(SuratPerizinan::STATUS_SELESAI, $surat->status);
        $this->assertSame($keluar->utc()->toDateTimeString(), $surat->waktu_keluar->utc()->toDateTimeString());
        $this->assertSame($kembali->utc()->toDateTimeString(), $surat->waktu_kembali->utc()->toDateTimeString());
        $this->assertDatabaseHas('surat_perizinan_riwayats', [
            'surat_perizinan_id' => $surat->id,
            'status' => SuratPerizinan::STATUS_SELESAI,
            'nama_pengubah' => 'Ustadzah Hanifah',
        ]);
    }

    public function test_semester_time_cannot_be_recorded_before_approval(): void
    {
        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER);
        $waktu = Carbon::now('Asia/Jakarta')->subHour()->startOfMinute();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.waktu', $surat), [
                'waktu_keluar' => $waktu->format('Y-m-d\TH:i'),
            ])
            ->assertSessionHas('error');

        $this->assertNull($surat->refresh()->waktu_keluar);
        $this->assertSame(SuratPerizinan::STATUS_MENUNGGU, $surat->status);
    }

    public function test_semester_time_rejects_future_timestamp(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $path = 'surat-perizinan/'.$mahasiswa->id.'/tertanda/surat.pdf';
        Storage::disk('local')->put($path, 'surat bertanda tangan');
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'surat_tertanda_path' => $path,
        ]);
        $waktu = Carbon::now('Asia/Jakarta')->addHour()->startOfMinute();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.waktu', $surat), [
                'waktu_keluar' => $waktu->format('Y-m-d\TH:i'),
            ])
            ->assertSessionHasErrors('waktu_keluar');

        $this->assertNull($surat->refresh()->waktu_keluar);
    }

    public function test_cross_floor_submission_is_hidden_and_direct_access_returns_not_found(): void
    {
        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa([], 'Gedung A', 2);
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan'))
            ->assertDontSee($mahasiswa->name);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan.show', $pengajuan))
            ->assertNotFound();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
            ])
            ->assertNotFound();
    }

    public function test_same_floor_submission_assigned_to_another_musyrifah_is_not_accessible(): void
    {
        $penugasan = $this->buatMusyrifah(['name' => 'Ustadzah Aisyah']);
        $musyrifahLain = $this->buatMusyrifah(['name' => 'Ustadzah Maryam']);
        $mahasiswa = $this->buatMahasiswa();
        $pengajuan = $this->buatPengajuan($mahasiswa, $penugasan);

        $this->actingAs($musyrifahLain)
            ->get(route('musyrifah.surat_perizinan'))
            ->assertDontSee($mahasiswa->name);

        $this->actingAs($musyrifahLain)
            ->get(route('musyrifah.surat_perizinan.show', $pengajuan))
            ->assertNotFound();
    }

    public function test_legacy_unassigned_submission_on_same_floor_remains_accessible(): void
    {
        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();
        $pengajuan = $this->buatPengajuan($mahasiswa, null);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan.show', $pengajuan))
            ->assertOk()
            ->assertSee($mahasiswa->name);
    }

    public function test_inactive_musyrifah_cannot_access_submissions(): void
    {
        $musyrifah = $this->buatMusyrifah(['status' => 'nonaktif']);
        $mahasiswa = $this->buatMahasiswa();
        $this->buatPengajuan($mahasiswa, $musyrifah);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan'))
            ->assertForbidden();
    }

    public function test_musyrifah_without_floor_assignment_cannot_access_submissions(): void
    {
        $musyrifah = $this->buatMusyrifah([
            'gedung_penempatan' => null,
            'lantai_penempatan' => null,
        ]);
        $mahasiswa = $this->buatMahasiswa();
        $this->buatPengajuan($mahasiswa, $musyrifah);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan'))
            ->assertForbidden();
    }

    public function test_musyrifah_can_download_submission_documents(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $path = 'surat-perizinan/'.$mahasiswa->id.'/surat.pdf';
        Storage::disk('local')->put($path, 'isi surat resmi');

        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_KEGIATAN_FAKULTAS, [
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
        $musyrifah = $this->buatMusyrifah();

        $path = 'surat-perizinan/'.$mahasiswa->id.'/surat.pdf';
        Storage::disk('local')->put($path, 'isi surat resmi');

        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_KEGIATAN_FAKULTAS, [
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
        $kedua = $mulai->copy()->addDays(14);

        $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDay()->toDateString(),
        ]);
        $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'tanggal_mulai' => $kedua->toDateString(),
            'tanggal_selesai' => $kedua->copy()->addDay()->toDateString(),
        ]);
        $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_PULANG, [
            'status' => SuratPerizinan::STATUS_MENUNGGU,
            'tanggal_mulai' => $kedua->copy()->addDay()->toDateString(),
            'tanggal_selesai' => $kedua->copy()->addDays(2)->toDateString(),
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.surat_perizinan'))
            ->assertSee('Monitoring Kuota Izin Pulang')
            ->assertSee('Kuota habis');
    }
}
