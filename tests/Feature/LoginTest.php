<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SIMAHAD');
        $response->assertSee('Sistem Informasi Manajemen');
        $response->assertSee('Masuk & Akses Layanan Ma\'had', false);
        $response->assertSee('Email atau Username');
        $response->assertSee('Kata Sandi');
        $response->assertSee('Masuk');
        $response->assertSee('Lupa Kata Sandi?');
        $response->assertSee('Mahasiswa baru?');
        $response->assertSee('Daftar di sini');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Akun Mahasiswa');
        $response->assertSee('Nama Lengkap');
        $response->assertSee('NIM');
        $response->assertSee('Email');
        $response->assertSee('Daftar Akun Baru');
    }

    public function test_new_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Fikri Mahasiswa Baru',
            'nim' => '240999',
            'email' => 'fikri@uinib.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'fikri@uinib.ac.id',
            'nim' => '240999',
            'name' => 'Fikri Mahasiswa Baru',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi');
        $response->assertSee('Email atau NIM Terdaftar');
        $response->assertSee('Kata Sandi Baru');
    }

    public function test_user_can_reset_password_using_email_or_nim(): void
    {
        $user = User::factory()->create([
            'email' => 'resetuser@uinib.ac.id',
            'nim' => '240888',
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->post('/forgot-password', [
            'identifier' => 'resetuser@uinib.ac.id',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        // Test logging in with the new password
        $loginResponse = $this->post('/login', [
            'login' => 'resetuser@uinib.ac.id',
            'password' => 'newpassword123',
        ]);

        $this->assertAuthenticatedAs($user->fresh());
        $loginResponse->assertRedirect(route('dashboard'));
    }

    public function test_users_can_authenticate_using_nim(): void
    {
        $user = User::factory()->create([
            'nim' => '240101',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login' => '240101',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_can_authenticate_using_email(): void
    {
        $user = User::factory()->create([
            'email' => 'mahasiswa@asrama.ac.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'mahasiswa@asrama.ac.id',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'nim' => '240101',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'login' => '240101',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_screen_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Mahasiswa',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('SELAMAT DATANG, AHMAD MAHASISWA');
        $response->assertSee('Pendaftaran');
        $response->assertSee('Disetujui');
        $response->assertSee('Kamar A-102');
        $response->assertSee('Pembayaran Belum Lunas');
        $response->assertSee('Jadwal Hari Ini');
        $response->assertSee('08:00-10:00');
        $response->assertSee('Bahasa Arab');
        $response->assertSee('Kelas-A');
        $response->assertSee('Home');
        $response->assertSee('Pembalajaran');
        $response->assertSee('Perizinan');
        $response->assertSee('Profil');
        $response->assertSee('Pengaturan');
        $response->assertSee('Keluar');
    }

    public function test_kamar_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/kamar');

        $response->assertStatus(200);
        $response->assertSee('Cari Kamar');
        $response->assertSee('Lantai');
        $response->assertSee('Kamar 101');
        $response->assertSee('Kamar 102');
        $response->assertSee('Kapasitas 4');
        $response->assertSee('Tersedia');
        $response->assertSee('Penuh');
        $response->assertSee('Pilih');
        $response->assertSee('Lanjut');
    }

    public function test_user_can_select_kamar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/kamar/pilih', [
            'room_name' => 'Kamar 101',
        ]);

        $response->assertRedirect(route('kamar'));
        $response->assertSessionHas('status', 'Berhasil memilih Kamar 101!');
    }
}
