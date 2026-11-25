<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminManajemenPenggunaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_forms_expose_pimpinan_nip(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $pimpinan = User::factory()->create([
            'role' => 'pimpinan',
            'status' => 'aktif',
            'nip' => '198765432112345001',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.manajemen-pengguna.create'))
            ->assertSee('name="nip"', false);

        $this->actingAs($admin)
            ->get(route('admin.manajemen-pengguna.edit', $pimpinan->getKey()))
            ->assertSee('name="nip"', false)
            ->assertSee('value="198765432112345001"', false);
    }

    public function test_admin_creates_pimpinan_account_with_nip(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);

        $response = $this->actingAs($admin)->post(route('admin.manajemen-pengguna.store'), [
            'name' => 'Dr. H. Ahmad Fauzi, M.Pd.',
            'email' => 'pimpinan.baru@example.com',
            'role' => 'pimpinan',
            'nip' => '198765432112345001',
        ]);

        $response->assertRedirect(route('admin.manajemen-pengguna'));
        $response->assertSessionHas('success');

        $akun = User::query()->where('email', 'pimpinan.baru@example.com')->firstOrFail();
        $this->assertSame('Dr. H. Ahmad Fauzi, M.Pd.', $akun->name);
        $this->assertSame('198765432112345001', $akun->nip);
    }

    public function test_admin_updates_pimpinan_nip(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $pimpinan = User::factory()->create([
            'name' => 'Dr. H. Ahmad Fauzi, M.Pd.',
            'email' => 'pimpinan@example.com',
            'role' => 'pimpinan',
            'status' => 'aktif',
            'nip' => '198765432112345001',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.manajemen-pengguna.update', $pimpinan->getKey()), [
            'name' => 'Dr. H. Ahmad Fauzi, M.Pd.',
            'email' => 'pimpinan@example.com',
            'role' => 'pimpinan',
            'status' => 'aktif',
            'nip' => '198001152006041002',
        ]);

        $response->assertRedirect(route('admin.manajemen-pengguna'));
        $response->assertSessionHas('success');
        $this->assertSame('198001152006041002', $pimpinan->fresh()->nip);
    }

    public function test_pimpinan_account_requires_nip(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);

        $response = $this->actingAs($admin)->post(route('admin.manajemen-pengguna.store'), [
            'name' => 'Pimpinan Tanpa NIP',
            'email' => 'pimpinan.tanpa-nip@example.com',
            'role' => 'pimpinan',
        ]);

        $response->assertSessionHasErrors(['nip' => 'NIP pimpinan wajib diisi.']);
        $this->assertDatabaseMissing('users', ['email' => 'pimpinan.tanpa-nip@example.com']);
    }

    public function test_pimpinan_account_rejects_duplicate_nip(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        User::factory()->create([
            'role' => 'pimpinan',
            'status' => 'aktif',
            'nip' => '198765432112345001',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.manajemen-pengguna.store'), [
            'name' => 'Pimpinan Duplikat',
            'email' => 'pimpinan.duplikat@example.com',
            'role' => 'pimpinan',
            'nip' => '198765432112345001',
        ]);

        $response->assertSessionHasErrors(['nip' => 'NIP sudah digunakan akun lain.']);
        $this->assertDatabaseMissing('users', ['email' => 'pimpinan.duplikat@example.com']);
    }
}
