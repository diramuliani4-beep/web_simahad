<?php

namespace Tests\Feature;

use App\Mail\PengumumanDipublikasikanMail;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class PengumumanEmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat akun Musyrifah penulis pengumuman.
     *
     * @param  array<string, mixed>  $atribut
     */
    private function buatMusyrifah(array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Ustadzah Hanifah',
            'email' => 'musyrifah.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'role' => 'musyrifah',
        ], $atribut));
    }

    /**
     * Membuat akun mahasiswa penerima pengumuman.
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
     * Payload formulir pengumuman yang valid.
     *
     * @param  array<string, mixed>  $tambahan
     * @return array<string, mixed>
     */
    private function payload(array $tambahan = []): array
    {
        return array_merge([
            'judul' => 'Jadwal Kegiatan Mahasantri',
            'kategori' => Pengumuman::KATEGORI_AKADEMIK,
            'isi' => 'Seluruh mahasantri diharapkan mengikuti kegiatan sesuai jadwal yang telah ditetapkan oleh pengelola Ma\'had.',
            'tanggal_publikasi' => '2026-11-25',
            'status' => Pengumuman::STATUS_DIPUBLIKASIKAN,
        ], $tambahan);
    }

    public function test_publishing_announcement_emails_each_registered_mahasiswa(): void
    {
        Mail::fake();

        $musyrifah = $this->buatMusyrifah();
        $aisyah = $this->buatMahasiswa(['name' => 'Aisyah Rahmadani']);
        $fatimah = $this->buatMahasiswa(['name' => 'Fatimah Azzahra']);
        $guru = User::factory()->create([
            'email' => 'guru.'.fake()->unique()->numerify('####').'@mahad.ac.id',
            'role' => 'guru',
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pengumuman.store'), $this->payload())
            ->assertRedirect();

        Mail::assertSent(PengumumanDipublikasikanMail::class, 2);
        Mail::assertSent(
            PengumumanDipublikasikanMail::class,
            fn (PengumumanDipublikasikanMail $mail): bool => $mail->hasTo($aisyah->email)
        );
        Mail::assertSent(
            PengumumanDipublikasikanMail::class,
            fn (PengumumanDipublikasikanMail $mail): bool => $mail->hasTo($fatimah->email)
        );
        Mail::assertNotSent(
            PengumumanDipublikasikanMail::class,
            fn (PengumumanDipublikasikanMail $mail): bool => $mail->hasTo($guru->email)
        );
    }

    public function test_email_contains_title_content_and_publication_date(): void
    {
        Mail::fake();

        $musyrifah = $this->buatMusyrifah();
        $this->buatMahasiswa();

        $this->actingAs($musyrifah)->post(route('musyrifah.pengumuman.store'), $this->payload([
            'judul' => 'Pembayaran Asrama Semester Ganjil',
            'isi' => 'Pembayaran dilakukan paling lambat akhir bulan melalui bank yang ditunjuk Ma\'had.',
        ]))->assertRedirect();

        $pengumuman = Pengumuman::query()->firstOrFail();

        Mail::assertSent(PengumumanDipublikasikanMail::class, function (PengumumanDipublikasikanMail $mail) use ($pengumuman): bool {
            $html = $mail->render();

            return $mail->pengumuman->is($pengumuman)
                && str_contains($html, 'Pembayaran Asrama Semester Ganjil')
                && str_contains($html, 'Pembayaran dilakukan paling lambat akhir bulan')
                && str_contains($html, '25 November 2026');
        });
    }

    public function test_draft_announcement_does_not_send_email(): void
    {
        Mail::fake();

        $musyrifah = $this->buatMusyrifah();
        $this->buatMahasiswa();

        $this->actingAs($musyrifah)->post(route('musyrifah.pengumuman.store'), $this->payload([
            'status' => Pengumuman::STATUS_DRAFT,
        ]))->assertRedirect();

        Mail::assertNothingSent();
    }

    public function test_publishing_existing_draft_via_status_update_sends_email(): void
    {
        Mail::fake();

        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->draft()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Pengumuman Yang Akan Diterbitkan',
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pengumuman.status', $pengumuman), [
                'status' => Pengumuman::STATUS_DIPUBLIKASIKAN,
            ])
            ->assertRedirect();

        Mail::assertSent(
            PengumumanDipublikasikanMail::class,
            fn (PengumumanDipublikasikanMail $mail): bool => $mail->hasTo($mahasiswa->email)
        );
    }

    public function test_publishing_existing_draft_via_edit_form_sends_email(): void
    {
        Mail::fake();

        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->draft()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Draft Yang Disunting Menjadi Terbit',
        ]);

        $this->actingAs($musyrifah)
            ->put(route('musyrifah.pengumuman.update', $pengumuman), $this->payload([
                'judul' => 'Draft Yang Disunting Menjadi Terbit',
            ]))
            ->assertRedirect();

        Mail::assertSent(
            PengumumanDipublikasikanMail::class,
            fn (PengumumanDipublikasikanMail $mail): bool => $mail->hasTo($mahasiswa->email)
        );
    }

    public function test_editing_published_announcement_does_not_resend_email(): void
    {
        Mail::fake();

        $musyrifah = $this->buatMusyrifah();
        $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->dipublikasikan()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Judul Sebelum Diperbarui',
        ]);

        $this->actingAs($musyrifah)
            ->put(route('musyrifah.pengumuman.update', $pengumuman), $this->payload([
                'judul' => 'Judul Setelah Diperbarui',
            ]))
            ->assertRedirect();

        Mail::assertNothingSent();
    }

    public function test_publishing_still_creates_in_app_notification(): void
    {
        Mail::fake();

        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pengumuman.store'), $this->payload())
            ->assertRedirect();

        $pengumuman = Pengumuman::query()->firstOrFail();

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_PENGUMUMAN_BARU,
            'judul' => 'Pengumuman baru: '.$pengumuman->judul,
        ]);
    }

    public function test_email_delivery_failure_does_not_cancel_publication(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('Server SMTP tidak tersedia.'));

        $musyrifah = $this->buatMusyrifah();
        $this->buatMahasiswa();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pengumuman.store'), $this->payload([
                'judul' => 'Pengumuman Tetap Terbit',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('pengumumans', [
            'judul' => 'Pengumuman Tetap Terbit',
            'status' => Pengumuman::STATUS_DIPUBLIKASIKAN,
        ]);
    }
}
