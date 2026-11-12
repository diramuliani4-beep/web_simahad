<?php

namespace Tests\Feature;

use App\Support\KonfigurasiEmail;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Tests\TestCase;

class KonfigurasiEmailTest extends TestCase
{
    /**
     * Konfigurasi Mailpit lokal (tanpa autentikasi) untuk pengujian.
     */
    private function siapkanMailpit(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1025,
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
            'mail.from.address' => 'noreply@simahad.test',
        ]);
    }

    public function test_mailer_smtp_menuju_mailpit_lokal(): void
    {
        $this->siapkanMailpit();

        $transport = Mail::mailer('smtp')->getSymfonyTransport();

        $this->assertInstanceOf(EsmtpTransport::class, $transport);
        $this->assertStringContainsString('127.0.0.1:1025', (string) $transport);
    }

    public function test_konfigurasi_mailpit_tanpa_kredensial_dianggap_siap(): void
    {
        $this->siapkanMailpit();

        $this->assertNull(KonfigurasiEmail::masalah());
        $this->assertTrue(KonfigurasiEmail::smtpLokal());
    }

    public function test_perintah_email_uji_mengirim_email_bila_konfigurasi_lengkap(): void
    {
        Mail::fake();
        $this->siapkanMailpit();

        $this->artisan('email:uji', ['tujuan' => 'guru@gmail.com'])
            ->expectsOutputToContain('Email uji terkirim ke guru@gmail.com')
            ->expectsOutputToContain('http://127.0.0.1:8025')
            ->assertExitCode(0);
    }

    public function test_perintah_email_uji_menolak_alamat_tujuan_tidak_valid(): void
    {
        $this->artisan('email:uji', ['tujuan' => 'bukan-email'])
            ->expectsOutputToContain('tidak valid')
            ->assertExitCode(1);
    }

    public function test_perintah_email_uji_menolak_mailer_yang_tidak_mengirim_email(): void
    {
        config(['mail.default' => 'log']);

        $this->artisan('email:uji', ['tujuan' => 'guru@gmail.com'])
            ->expectsOutputToContain('MAIL_MAILER masih "log"')
            ->assertExitCode(1);
    }

    public function test_perintah_email_uji_menolak_kredensial_smtp_luar_yang_belum_diisi(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.penyedia-email.test',
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
            'mail.from.address' => 'noreply@simahad.test',
        ]);

        $this->artisan('email:uji', ['tujuan' => 'guru@gmail.com'])
            ->expectsOutputToContain('MAIL_USERNAME dan MAIL_PASSWORD belum diisi')
            ->assertExitCode(1);
    }

    public function test_perintah_email_uji_menolak_alamat_pengirim_yang_belum_diatur(): void
    {
        $this->siapkanMailpit();
        config(['mail.from.address' => '']);

        $this->artisan('email:uji', ['tujuan' => 'guru@gmail.com'])
            ->expectsOutputToContain('MAIL_FROM_ADDRESS belum diisi')
            ->assertExitCode(1);
    }
}
