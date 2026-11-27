<?php

namespace Tests\Feature;

use App\Events\SuratPerizinanDicatat;
use App\Mail\SuratPerizinanMail;
use App\Models\Kamar;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\SuratPerizinan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class SuratPerizinanEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-11-10 02:00:00'));
    }

    private function buatMahasiswa(array $atribut = []): User
    {
        $mahasiswa = User::factory()->create(array_merge([
            'name' => 'Aisyah Rahmadani',
            'email' => 'aisyah.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'nim' => fake()->unique()->numerify('2317020###'),
            'role' => 'mahasiswa',
        ], $atribut));

        $kamar = Kamar::factory()->create([
            'gedung' => 'Gedung A',
            'lantai' => 1,
        ]);
        $mahasiswa->update(['kamar_id' => $kamar->id]);

        Pendaftaran::query()->create([
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
            'status' => 'disetujui',
            'tahun_angkatan' => '2023',
        ]);

        return $mahasiswa;
    }

    private function buatMusyrifah(array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Ustadzah Hanifah',
            'email' => 'musyrifah.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'role' => 'musyrifah',
            'status' => 'aktif',
            'gedung_penempatan' => 'Gedung A',
            'lantai_penempatan' => 1,
        ], $atribut));
    }

    private function buatPengajuan(
        User $mahasiswa,
        User $musyrifah,
        string $jenis = SuratPerizinan::JENIS_PULANG,
        array $atribut = [],
    ): SuratPerizinan {
        $factory = match ($jenis) {
            SuratPerizinan::JENIS_SEMESTER => SuratPerizinan::factory()->semester(),
            SuratPerizinan::JENIS_KEGIATAN_FAKULTAS => SuratPerizinan::factory()->kegiatanFakultas(),
            default => SuratPerizinan::factory()->pulang(),
        };

        return $factory->for($mahasiswa)->create(array_merge([
            'jenis' => $jenis,
            'musyrifah_id' => $musyrifah->id,
        ], $atribut));
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadPulang(): array
    {
        $mulai = now()->addDays(5)->startOfDay();

        return [
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays(2)->toDateString(),
            'lokasi' => 'Bukittinggi',
            'alasan' => 'Pulang menjenguk orang tua yang sakit.',
        ];
    }

    public function test_new_submission_emails_student_and_musyrifah_and_keeps_in_app_notifications(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.pengajuan.store'), $this->payloadPulang())
            ->assertRedirect(route('surat.perizinan.mhs'));

        Mail::assertSent(SuratPerizinanMail::class, 2);
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($mahasiswa->email)
                && str_contains($mail->render(), 'Pengajuan Baru Diterima')
        );
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($musyrifah->email)
                && str_contains($mail->render(), $mahasiswa->name)
        );

        $this->assertDatabaseCount('notifikasis', 2);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_SURAT_PERIZINAN,
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $musyrifah->id,
            'tipe' => Notifikasi::TIPE_SURAT_PERIZINAN,
        ]);
        $this->assertDatabaseCount('surat_perizinan_email_logs', 2);
        $this->assertDatabaseHas('surat_perizinan_email_logs', [
            'user_id' => $mahasiswa->id,
            'status' => 'terkirim',
            'terkirim_pada' => now(),
        ]);
    }

    public function test_approval_emails_student_with_decision_note(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
                'catatan_musyrifah' => 'Izin disetujui, kembali tepat waktu.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $pengajuan));

        Mail::assertSent(SuratPerizinanMail::class, 1);
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($mahasiswa->email)
                && str_contains($mail->render(), 'Pengajuan Disetujui')
                && str_contains($mail->render(), 'Izin disetujui, kembali tepat waktu.')
        );
        Mail::assertNotSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($musyrifah->email)
        );
    }

    public function test_rejection_emails_student_with_reason(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DITOLAK,
                'catatan_musyrifah' => 'Alasan pengajuan belum lengkap.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $pengajuan));

        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($mahasiswa->email)
                && str_contains($mail->render(), 'Pengajuan Ditolak')
                && str_contains($mail->render(), 'Alasan pengajuan belum lengkap.')
        );
    }

    public function test_rejection_email_escapes_musyrifah_note(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DITOLAK,
                'catatan_musyrifah' => '<b>Alasan belum lengkap</b>',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $pengajuan));

        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => str_contains($mail->render(), '&lt;b&gt;Alasan belum lengkap&lt;/b&gt;')
                && ! str_contains($mail->render(), '<b>Alasan belum lengkap</b>')
        );
    }

    public function test_correction_request_emails_student_with_musyrifah_note(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $surat), [
                'keputusan' => SuratPerizinan::STATUS_PERLU_PERBAIKAN,
                'catatan_musyrifah' => 'Tanda tangan orang tua belum terbaca.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $surat));

        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($mahasiswa->email)
                && str_contains($mail->render(), 'Pengajuan Perlu Diperbaiki')
                && str_contains($mail->render(), 'Tanda tangan orang tua belum terbaca.')
        );
    }

    public function test_cancellation_emails_student_and_musyrifah(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.batalkan', $pengajuan))
            ->assertRedirect(route('surat.perizinan.mhs'));

        Mail::assertSent(SuratPerizinanMail::class, 2);
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($mahasiswa->email)
        );
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($musyrifah->email)
                && str_contains($mail->render(), 'Pengajuan Dibatalkan Mahasiswa')
        );
    }

    public function test_signed_letter_reupload_emails_student_and_musyrifah(): void
    {
        Mail::fake();
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER, [
            'status' => SuratPerizinan::STATUS_PERLU_PERBAIKAN,
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.semester.unggah', $surat), [
                'surat_tertanda' => UploadedFile::fake()->create('surat-perbaikan.pdf', 140, 'application/pdf'),
            ])
            ->assertRedirect(route('surat.perizinan.mhs.semester'));

        Mail::assertSent(SuratPerizinanMail::class, 2);
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($musyrifah->email)
                && str_contains($mail->render(), 'Surat Bertanda Tangan Diunggah Ulang')
        );
    }

    public function test_departure_and_completion_send_distinct_student_emails(): void
    {
        Mail::fake();
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $path = 'surat-perizinan/'.$mahasiswa->id.'/tertanda/surat.pdf';
        Storage::disk('local')->put($path, 'isi surat');
        $surat = $this->buatPengajuan($mahasiswa, $musyrifah, SuratPerizinan::JENIS_SEMESTER, [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'surat_tertanda_path' => $path,
        ]);
        $sekarang = Carbon::now('Asia/Jakarta');

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.waktu', $surat), [
                'waktu_keluar' => $sekarang->copy()->subHours(2)->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $surat));

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.waktu', $surat), [
                'waktu_keluar' => $sekarang->copy()->subHours(2)->format('Y-m-d\TH:i'),
                'waktu_kembali' => $sekarang->copy()->subHour()->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $surat));

        Mail::assertSent(SuratPerizinanMail::class, 2);
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($mahasiswa->email)
                && str_contains($mail->render(), 'Waktu Keluar Dicatat')
        );
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($mahasiswa->email)
                && str_contains($mail->render(), 'Pengajuan Selesai')
        );
        $this->assertSame(1, $surat->riwayats()
            ->where('catatan', 'like', 'Waktu keluar Ma’had dicatat:%')
            ->count());
    }

    public function test_legacy_unassigned_submission_notifies_current_floor_musyrifah(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);
        $pengajuan->update(['musyrifah_id' => null]);

        $pengajuan->catatRiwayat(
            SuratPerizinan::STATUS_MENUNGGU,
            'Pengajuan lama tanpa penugasan.',
            $mahasiswa,
            SuratPerizinanDicatat::PENGAJUAN_BARU,
        );

        Mail::assertSent(SuratPerizinanMail::class, 2);
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($musyrifah->email)
        );
    }

    public function test_stale_musyrifah_assignment_does_not_receive_email_after_room_move(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifahLama = $this->buatMusyrifah();
        $musyrifahBaru = $this->buatMusyrifah([
            'gedung_penempatan' => 'Gedung B',
        ]);
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifahLama);
        $kamarBaru = Kamar::factory()->create([
            'gedung' => 'Gedung B',
            'lantai' => 1,
        ]);
        $mahasiswa->update(['kamar_id' => $kamarBaru->id]);

        $this->actingAs($mahasiswa)
            ->post(route('surat.perizinan.mhs.batalkan', $pengajuan))
            ->assertRedirect(route('surat.perizinan.mhs'));

        Mail::assertSent(SuratPerizinanMail::class, 1);
        Mail::assertSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($mahasiswa->email)
        );
        Mail::assertNotSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($musyrifahLama->email)
        );
        Mail::assertNotSent(
            SuratPerizinanMail::class,
            fn (SuratPerizinanMail $mail): bool => $mail->hasTo($musyrifahBaru->email)
        );
    }

    public function test_repeated_dispatch_of_one_history_event_does_not_duplicate_email(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);
        $riwayat = $pengajuan->catatRiwayat(
            SuratPerizinan::STATUS_MENUNGGU,
            'Pengajuan baru dicatat.',
            $mahasiswa,
            SuratPerizinanDicatat::PENGAJUAN_BARU,
        );

        SuratPerizinanDicatat::dispatch($pengajuan, $riwayat, SuratPerizinanDicatat::PENGAJUAN_BARU);
        SuratPerizinanDicatat::dispatch($pengajuan, $riwayat, SuratPerizinanDicatat::PENGAJUAN_BARU);

        Mail::assertSent(SuratPerizinanMail::class, 2);
        $this->assertDatabaseCount('surat_perizinan_email_logs', 2);
        $this->assertDatabaseCount('notifikasis', 2);
    }

    public function test_rolled_back_history_event_does_not_send_email(): void
    {
        Mail::fake();

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

        try {
            DB::transaction(function () use ($pengajuan, $mahasiswa): void {
                $pengajuan->catatRiwayat(
                    SuratPerizinan::STATUS_MENUNGGU,
                    'Transaksi akan dibatalkan.',
                    $mahasiswa,
                    SuratPerizinanDicatat::PENGAJUAN_BARU,
                );

                throw new RuntimeException('Simulasi transaksi gagal.');
            });
        } catch (RuntimeException $e) {
            $this->assertSame('Simulasi transaksi gagal.', $e->getMessage());
        }

        Mail::assertNothingSent();
        $this->assertDatabaseCount('surat_perizinan_riwayats', 0);
        $this->assertDatabaseCount('surat_perizinan_email_logs', 0);
    }

    public function test_email_failure_does_not_cancel_approval_or_in_app_notification(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('Server SMTP tidak tersedia.'));

        $mahasiswa = $this->buatMahasiswa();
        $musyrifah = $this->buatMusyrifah();
        $pengajuan = $this->buatPengajuan($mahasiswa, $musyrifah);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.surat_perizinan.keputusan', $pengajuan), [
                'keputusan' => SuratPerizinan::STATUS_DISETUJUI,
                'catatan_musyrifah' => 'Izin disetujui meskipun SMTP sedang gagal.',
            ])
            ->assertRedirect(route('musyrifah.surat_perizinan.show', $pengajuan));

        $pengajuan->refresh();

        $this->assertSame(SuratPerizinan::STATUS_DISETUJUI, $pengajuan->status);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_SURAT_PERIZINAN,
        ]);
        $this->assertDatabaseCount('surat_perizinan_email_logs', 1);
        $this->assertDatabaseHas('surat_perizinan_email_logs', [
            'user_id' => $mahasiswa->id,
            'status' => 'gagal',
            'terkirim_pada' => null,
        ]);
    }
}
