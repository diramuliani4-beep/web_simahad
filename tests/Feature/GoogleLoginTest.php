<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'dummy-client-id',
            'services.google.client_secret' => 'dummy-client-secret',
            'services.google.redirect' => '/auth/google/callback',
        ]);
    }

    public function test_halaman_login_menampilkan_tautan_google_saat_kredensial_tersedia(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee(route('auth.google.redirect'));
        $response->assertSee('Lanjutkan dengan Google');
    }

    public function test_halaman_login_menampilkan_pemberitahuan_saat_kredensial_belum_diisi(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('openGoogleNotice()', false);
        $response->assertDontSee(route('auth.google.redirect'));
    }

    public function test_tombol_google_mengarahkan_pengguna_ke_google(): void
    {
        Socialite::shouldReceive('driver->redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_callback_google_melakukan_login_untuk_email_terdaftar(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santri',
            'email' => 'Budi.Santri@uinib.ac.id',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);

        $this->mockProviderWithUser($this->googleUser('budi.santri@uinib.ac.id'));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_callback_google_mengarahkan_role_lain_ke_dashboard_yang_sesuai(): void
    {
        $user = User::factory()->create([
            'email' => 'musyrifah@uinib.ac.id',
            'role' => 'musyrifah',
            'status' => 'aktif',
        ]);

        $this->mockProviderWithUser($this->googleUser('musyrifah@uinib.ac.id'));

        $this->get(route('auth.google.callback'))->assertRedirect(route('musyrifah.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_callback_google_menolak_email_yang_belum_terdaftar(): void
    {
        $this->mockProviderWithUser($this->googleUser('belum.terdaftar@uinib.ac.id'));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'belum.terdaftar@uinib.ac.id']);
    }

    public function test_callback_google_menolak_email_google_yang_belum_terverifikasi(): void
    {
        User::factory()->create(['email' => 'belum.verifikasi@uinib.ac.id']);

        $this->mockProviderWithUser($this->googleUser('belum.verifikasi@uinib.ac.id', verified: false));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_callback_google_menolak_akun_yang_belum_diverifikasi_admin(): void
    {
        User::factory()->create([
            'email' => 'pending@uinib.ac.id',
            'status' => 'pending',
        ]);

        $this->mockProviderWithUser($this->googleUser('pending@uinib.ac.id'));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_callback_google_menolak_akun_yang_ditolak_admin(): void
    {
        User::factory()->create([
            'email' => 'ditolak@uinib.ac.id',
            'status' => 'rejected',
        ]);

        $this->mockProviderWithUser($this->googleUser('ditolak@uinib.ac.id'));

        $this->get(route('auth.google.callback'))->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_callback_google_tidak_bekerja_saat_kredensial_belum_diisi(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_callback_google_menangani_pembatalan_dari_google(): void
    {
        $response = $this->get(route('auth.google.callback', ['error' => 'access_denied']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_pengguna_yang_sudah_login_tidak_diarahkan_ke_google(): void
    {
        $user = User::factory()->create(['role' => 'musyrifah']);

        $this->actingAs($user)
            ->get(route('auth.google.redirect'))
            ->assertRedirect(route('musyrifah.dashboard'));
    }

    private function googleUser(string $email, bool $verified = true): SocialiteUser
    {
        return (new SocialiteUser)
            ->setRaw([
                'email' => $email,
                'email_verified' => $verified,
            ])
            ->map([
                'id' => 'google-123',
                'name' => 'Budi Santri',
                'email' => $email,
            ]);
    }

    private function mockProviderWithUser(SocialiteUser $user): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($user);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }
}
