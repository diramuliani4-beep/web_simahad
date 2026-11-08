<?php

namespace Tests\Feature;

use App\Models\Kamar;
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
            'status' => 'pending',
        ]);

        // Akun baru menunggu verifikasi, sehingga diarahkan ke login.
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $this->assertGuest();
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
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Ahmad Mahasiswa');
        $response->assertSee('Ringkasan Mahasiswa');
        $response->assertSee('Ringkasan Pembayaran');
        $response->assertSee('Ringkasan Absensi');
        $response->assertSee('Kalender Jadwal Pembelajaran');
        $response->assertSee('Tugas Terbaru');
        $response->assertSee('Nilai Terbaru');
        $response->assertSee('Pengumuman Terbaru');
        $response->assertSee('Status Perizinan');
        $response->assertSee('Notifikasi Terbaru');
        $response->assertSee('Keluar');
    }

    public function test_kamar_screen_can_be_rendered(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        Kamar::create([
            'nama_kamar' => 'Kamar 101',
            'nomor_kamar' => '101',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
        ]);

        $response = $this->actingAs($user)->get('/kamar');

        $response->assertStatus(200);
        $response->assertSee('Informasi Kamar');
        $response->assertSee('Kamar 101');
        $response->assertSee('Gedung A');
        $response->assertSee('Lantai 1');
        $response->assertSee('Kapasitas');
        $response->assertSee('Tersedia');
        $response->assertSee('Ajukan Kamar Ini');
    }

    public function test_user_can_select_kamar(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        $kamar = Kamar::create([
            'nama_kamar' => 'Kamar 101',
            'nomor_kamar' => '101',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
        ]);

        $response = $this->actingAs($user)->post('/kamar/pilih', [
            'kamar_id' => $kamar->id,
        ]);

        $response->assertRedirect(route('kamar'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pengajuan_kamars', [
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'status' => 'menunggu',
        ]);
    }
}
