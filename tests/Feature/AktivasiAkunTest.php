<?php

namespace Tests\Feature;

use App\Mail\AktivasiAkunMail;
use App\Models\User;
use App\Services\AktivasiAkunService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class AktivasiAkunTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_membuat_akun_tanpa_membuat_kata_sandi_dan_mengirim_aktivasi(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);

        $response = $this->actingAs($admin)->post(route('admin.manajemen-pengguna.store'), [
            'name' => 'Guru Baru',
            'email' => 'guru.baru@example.com',
            'role' => 'guru',
        ]);

        $response->assertRedirect(route('admin.manajemen-pengguna'));
        $response->assertSessionHas('success');

        $akun = User::query()->where('email', 'guru.baru@example.com')->firstOrFail();

        $this->assertSame('menunggu aktivasi', strtolower((string) $akun->status));
        $this->assertSame('guru', strtolower((string) $akun->role));
        $this->assertNotNull($akun->activation_token);
        $this->assertNotNull($akun->activation_expires_at);
        $this->assertNull($akun->last_login_at);

        // Admin tidak pernah mengetahui kata sandi pengguna.
        $this->assertFalse(Hash::check('password', (string) $akun->password));

        Mail::assertSent(AktivasiAkunMail::class, function (AktivasiAkunMail $mail) use ($akun): bool {
            return $mail->hasTo($akun->email) && str_contains($mail->tautan, '/aktivasi-akun/');
        });

        $this->assertDatabaseHas('pegawais', [
            'nama_pegawai' => 'Guru Baru',
            'email' => 'guru.baru@example.com',
        ]);
    }

    public function test_pemilik_akun_membuat_kata_sandi_melalui_tautan_aktivasi(): void
    {
        $token = str_repeat('a', 64);
        $akun = User::factory()->create([
            'role' => 'guru',
            'status' => 'menunggu aktivasi',
            'activation_token' => hash('sha256', $token),
            'activation_expires_at' => now()->addHours(24),
        ]);

        $this->get(route('aktivasi.form', ['token' => $token]))
            ->assertOk()
            ->assertSee('Aktivasi Akun')
            ->assertSee($akun->name);

        $response = $this->post(route('aktivasi.simpan', ['token' => $token]), [
            'password' => 'RahasiaBaru123',
            'password_confirmation' => 'RahasiaBaru123',
        ]);

        $response->assertRedirect(route('guru.dashboard'));
        $this->assertAuthenticatedAs($akun->fresh());

        $akun->refresh();

        $this->assertSame('aktif', strtolower((string) $akun->status));
        $this->assertNull($akun->activation_token);
        $this->assertNull($akun->activation_expires_at);
        $this->assertNotNull($akun->last_login_at);
        $this->assertTrue(Hash::check('RahasiaBaru123', (string) $akun->password));

        // Tautan sekali pakai: percobaan kedua tidak lagi dikenali.
        $this->assertNull(app(AktivasiAkunService::class)->cariPengguna($token));
    }

    public function test_tautan_kedaluwarsa_tidak_dapat_dipakai(): void
    {
        $token = str_repeat('b', 64);
        User::factory()->create([
            'role' => 'musyrifah',
            'status' => 'menunggu aktivasi',
            'activation_token' => hash('sha256', $token),
            'activation_expires_at' => now()->subMinute(),
        ]);

        $this->get(route('aktivasi.form', ['token' => $token]))
            ->assertOk()
            ->assertSee('Tautan tidak berlaku');

        $this->post(route('aktivasi.simpan', ['token' => $token]), [
            'password' => 'RahasiaBaru123',
            'password_confirmation' => 'RahasiaBaru123',
        ])->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_kata_sandi_aktivasi_wajib_dikonfirmasi(): void
    {
        $token = str_repeat('c', 64);
        $akun = User::factory()->create([
            'role' => 'pimpinan',
            'status' => 'menunggu aktivasi',
            'activation_token' => hash('sha256', $token),
            'activation_expires_at' => now()->addHour(),
        ]);

        $this->post(route('aktivasi.simpan', ['token' => $token]), [
            'password' => 'RahasiaBaru123',
            'password_confirmation' => 'Berbeda123',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
        $this->assertSame('menunggu aktivasi', strtolower((string) $akun->fresh()->status));
    }

    public function test_akun_menunggu_aktivasi_tidak_dapat_login_walau_punya_kata_sandi(): void
    {
        $akun = User::factory()->create([
            'email' => 'menunggu@example.com',
            'role' => 'musyrifah',
            'status' => 'menunggu aktivasi',
            'password' => Hash::make('KataSandiLama1'),
        ]);

        $response = $this->post(route('login.post'), [
            'login' => 'menunggu@example.com',
            'password' => 'KataSandiLama1',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
        $this->assertSame('menunggu aktivasi', strtolower((string) $akun->fresh()->status));
    }

    public function test_admin_dapat_mengirim_ulang_tautan_aktivasi(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);

        $akun = User::factory()->create([
            'role' => 'guru',
            'status' => 'menunggu aktivasi',
            'activation_token' => hash('sha256', str_repeat('d', 64)),
            'activation_expires_at' => now()->addHour(),
        ]);

        $tokenLama = $akun->activation_token;

        $this->actingAs($admin)
            ->post(route('admin.manajemen-pengguna.aktivasi', $akun->getKey()))
            ->assertSessionHas('success');

        $this->assertNotSame($tokenLama, $akun->fresh()->activation_token);
        Mail::assertSent(AktivasiAkunMail::class);
    }

    public function test_halaman_manajemen_akun_tidak_meminta_admin_membuat_kata_sandi(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $akun = User::factory()->create([
            'role' => 'guru',
            'status' => 'menunggu aktivasi',
            'activation_token' => hash('sha256', str_repeat('f', 64)),
            'activation_expires_at' => now()->addHour(),
        ]);

        $this->actingAs($admin)->get(route('admin.manajemen-pengguna.create'))
            ->assertOk()
            ->assertSee('Aktivasi Akun')
            ->assertDontSee('name="password"', false);

        $this->actingAs($admin)->get(route('admin.manajemen-pengguna.edit', $akun->getKey()))
            ->assertOk()
            ->assertSee('Kirim Ulang Aktivasi')
            ->assertDontSee('name="password"', false);

        $this->actingAs($admin)->get(route('admin.manajemen-pengguna'))
            ->assertOk()
            ->assertSee('Menunggu Aktivasi')
            ->assertSee('Kirim Aktivasi');
    }

    public function test_aktivasi_tidak_dapat_dikirim_untuk_akun_yang_sudah_aktif(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $akun = User::factory()->create([
            'role' => 'guru',
            'status' => 'aktif',
            'activation_token' => null,
            'activation_expires_at' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.manajemen-pengguna.aktivasi', $akun->getKey()))
            ->assertSessionHas('error');

        $this->assertNull($akun->fresh()->activation_token);
        Mail::assertNothingSent();
    }

    public function test_aktivasi_ditandai_gagal_saat_mailer_tidak_benar_benar_mengirim_email(): void
    {
        Log::spy();

        // Mailer log hanya menulis pesan ke berkas log, email tidak pernah terkirim.
        config(['mail.default' => 'log']);

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $akun = User::factory()->create([
            'role' => 'guru',
            'status' => 'menunggu aktivasi',
            'activation_token' => hash('sha256', str_repeat('e', 64)),
            'activation_expires_at' => now()->addHour(),
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.manajemen-pengguna.aktivasi', $akun->getKey()));

        $response->assertSessionHas('error');
        $response->assertSessionMissing('success');

        Log::shouldHaveReceived('error')
            ->withArgs(function (string $pesan, array $konteks = []) use ($akun): bool {
                $isi = $pesan.' '.json_encode($konteks);

                return str_contains($pesan, 'gagal')
                    && ! str_contains($isi, '/aktivasi-akun/')
                    && ($konteks['user_id'] ?? null) === $akun->getKey();
            })
            ->once();
    }

    public function test_kegagalan_smtp_menampilkan_pesan_gagal_dan_log_tersensor(): void
    {
        Log::spy();

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1025,
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
            'mail.from.address' => 'noreply@simahad.test',
        ]);

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $akun = User::factory()->create([
            'role' => 'guru',
            'status' => 'menunggu aktivasi',
            'activation_token' => hash('sha256', str_repeat('a', 64)),
            'activation_expires_at' => now()->addHour(),
        ]);

        Mail::shouldReceive('to->send')
            ->once()
            ->andThrow(new RuntimeException('Expected response code 250 but got 535, password=RahasiaSmtp123 smtp://admin:RahasiaSmtp123@smtp.example.com'));

        $response = $this->actingAs($admin)
            ->post(route('admin.manajemen-pengguna.aktivasi', $akun->getKey()));

        $response->assertSessionHas('error');
        $response->assertSessionMissing('success');
        $this->assertStringNotContainsString('RahasiaSmtp123', (string) session('error'));

        Log::shouldHaveReceived('error')
            ->withArgs(function (string $pesan, array $konteks = []): bool {
                $isi = json_encode($konteks);

                return str_contains($pesan, 'gagal')
                    && ! str_contains($isi, 'RahasiaSmtp123')
                    && ($konteks['exception'] ?? null) === RuntimeException::class;
            })
            ->once();
    }

    public function test_aktivasi_memberi_pesan_jelas_saat_kredensial_smtp_belum_diisi(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.penyedia-email.test',
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
            'mail.from.address' => 'noreply@simahad.test',
        ]);

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $akun = User::factory()->create([
            'role' => 'guru',
            'status' => 'menunggu aktivasi',
            'activation_token' => hash('sha256', str_repeat('b', 64)),
            'activation_expires_at' => now()->addHour(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.manajemen-pengguna.aktivasi', $akun->getKey()))
            ->assertSessionHas('error', fn (mixed $pesan): bool => str_contains((string) $pesan, 'MAIL_USERNAME'))
            ->assertSessionMissing('success');
    }
}
