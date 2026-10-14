<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembayaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_pembayaran(): void
    {
        $response = $this->get('/pembayaran');

        $response->assertRedirect(route('login'));
    }

    public function test_pembayaran_screen_can_be_rendered_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Santoso',
            'email' => 'ahmad@asrama.ac.id',
            'nim' => '2317020134',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->get('/pembayaran');

        $response->assertStatus(200);
        $response->assertSee('Pembayaran');
        $response->assertSee('Tagihan saat ini');
        $response->assertSee('Pembayaran Asrama');
        $response->assertSee('Periode : Januari 2027');
        $response->assertSee('RP: 2.500.00,00');
        $response->assertSee('Belum lunas');
        $response->assertSee('Bayar Sekarang');
        $response->assertSee('Riwayat Pembayaran');
        $response->assertSee('Periode : Agustus-Desember');
        $response->assertSee('Nominal: 2.500.000,00');
        $response->assertSee('Status: Lunas');
    }

    public function test_user_can_submit_payment_and_see_lunas_status(): void
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Santoso',
            'email' => 'ahmad@asrama.ac.id',
            'nim' => '2317020134',
        ]);

        $response = $this->actingAs($user)->post('/pembayaran/bayar', [
            'metode' => 'Virtual Account BSI',
        ]);

        $response->assertRedirect(route('pembayaran'));
        $response->assertSessionHas('pembayaran_status', 'lunas');

        $page = $this->actingAs($user)->get('/pembayaran');
        $page->assertStatus(200);
        $page->assertSee('Lunas');
        $page->assertSee('Sudah Dibayar');
    }

    public function test_user_can_reset_payment_simulation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->withSession(['pembayaran_status' => 'lunas'])
            ->post('/pembayaran/reset')
            ->assertRedirect(route('pembayaran'))
            ->assertSessionMissing('pembayaran_status');
    }
}
