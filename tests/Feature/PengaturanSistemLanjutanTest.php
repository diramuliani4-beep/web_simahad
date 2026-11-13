<?php

namespace Tests\Feature;

use App\Models\PengaturanSistem;
use App\Models\User;
use App\Services\BackupDatabaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Pengujian empat bagian halaman Pengaturan Sistem: Identitas Aplikasi,
 * Pengaturan Email, Keamanan Sistem, serta Backup dan Pemeliharaan.
 */
class PengaturanSistemLanjutanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_mengunggah_logo_dan_kembali_ke_logo_bawaan(): void
    {
        Storage::fake('public');

        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.update'), [
                'nama_sistem' => 'SIMAHAD',
                'nama_institusi' => 'Ma’had Al-Jami’ah Tuanku Imam Bonjol',
                'logo' => UploadedFile::fake()->image('logo-baru.png', 240, 240),
            ])
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHasNoErrors();

        $logo = PengaturanSistem::aktif()->logo_path;

        $this->assertNotNull($logo);
        Storage::disk('public')->assertExists($logo);

        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('storage/'.$logo);

        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.update'), [
                'nama_sistem' => 'SIMAHAD',
                'nama_institusi' => 'Ma’had Al-Jami’ah Tuanku Imam Bonjol',
                'hapus_logo' => '1',
            ])
            ->assertRedirect(route('admin.pengaturan-sistem'));

        $this->assertNull(PengaturanSistem::aktif()->logo_path);
        Storage::disk('public')->assertMissing($logo);

        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('logo_uin.png');
    }

    public function test_unggahan_logo_divalidasi(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->put(route('admin.pengaturan-sistem.update'), [
                'nama_sistem' => 'SIMAHAD',
                'nama_institusi' => 'Ma’had Al-Jami’ah Tuanku Imam Bonjol',
                'logo' => UploadedFile::fake()->create('dokumen.pdf', 20, 'application/pdf'),
            ])
            ->assertSessionHasErrors('logo');

        $this->assertNull(PengaturanSistem::aktif()->logo_path);
    }

    public function test_admin_dapat_menyimpan_pengaturan_email_smtp_kustom(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.email'), [
                'mail_mode' => 'smtp',
                'mail_host' => 'smtp.contoh.test',
                'mail_port' => 587,
                'mail_scheme' => '',
                'mail_username' => 'admin@contoh.test',
                'mail_password' => 'rahasia-smtp',
                'mail_from_address' => 'noreply@contoh.test',
                'mail_from_name' => 'SIMAHAD',
            ])
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHasNoErrors();

        $baris = DB::table('pengaturan_sistems')->first();

        $this->assertSame('smtp', $baris->mail_mode);
        $this->assertSame('smtp.contoh.test', $baris->mail_host);
        $this->assertSame(587, (int) $baris->mail_port);
        // Kata sandi disimpan terenkripsi, bukan sebagai teks biasa.
        $this->assertNotSame('rahasia-smtp', $baris->mail_password);
        $this->assertSame('rahasia-smtp', PengaturanSistem::aktif()->mail_password);

        $this->assertDatabaseHas('activity_logs', [
            'modul' => 'pengaturan',
            'aksi' => 'diperbarui',
            'objek' => 'Pengaturan Email',
        ]);

        // Konfigurasi diterapkan pada request berikutnya.
        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('smtp.contoh.test');

        $this->assertSame('smtp.contoh.test', config('mail.mailers.smtp.host'));
        $this->assertSame(587, (int) config('mail.mailers.smtp.port'));
        $this->assertSame('rahasia-smtp', config('mail.mailers.smtp.password'));
        $this->assertSame('noreply@contoh.test', config('mail.from.address'));
    }

    public function test_mode_lokal_mempertahankan_konfigurasi_mailpit(): void
    {
        $admin = $this->admin();

        PengaturanSistem::aktif()->update([
            'mail_mode' => 'smtp',
            'mail_host' => 'smtp.contoh.test',
            'mail_port' => 587,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.email'), ['mail_mode' => 'lokal'])
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHasNoErrors();

        $pengaturan = PengaturanSistem::aktif();

        $this->assertSame('lokal', $pengaturan->mail_mode);
        $this->assertNull($pengaturan->mail_host);

        // Konfigurasi runtime direset agar terlihat apakah middleware menimpanya.
        config([
            'mail.default' => 'array',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1025,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('Lokal (Mailpit)');

        // Mode lokal tidak menimpa konfigurasi .env sehingga Mailpit tetap dipakai.
        $this->assertSame('array', config('mail.default'));
        $this->assertSame('127.0.0.1', config('mail.mailers.smtp.host'));
    }

    public function test_smtp_non_lokal_membutuhkan_kredensial_sedangkan_mailpit_tidak(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.email'), [
                'mail_mode' => 'smtp',
                'mail_host' => 'smtp.contoh.test',
                'mail_port' => 587,
                'mail_from_address' => 'noreply@contoh.test',
            ])
            ->assertSessionHasErrors('mail_username');

        // Server lokal (Mailpit) tidak membutuhkan kredensial.
        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.email'), [
                'mail_mode' => 'smtp',
                'mail_host' => '127.0.0.1',
                'mail_port' => 1025,
                'mail_from_address' => 'noreply@simahad.test',
            ])
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHasNoErrors();

        $this->assertSame('127.0.0.1', PengaturanSistem::aktif()->mail_host);
    }

    public function test_tes_koneksi_email_mengirim_email_uji(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1025,
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
            'mail.from.address' => 'noreply@simahad.test',
        ]);

        Mail::shouldReceive('raw')->once();

        $this->actingAs($this->admin())
            ->post(route('admin.pengaturan-sistem.email.tes'))
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHas('success', fn (string $pesan): bool => str_contains($pesan, 'Email uji terkirim'));
    }

    public function test_tes_koneksi_menolak_saat_konfigurasi_belum_siap(): void
    {
        // Lingkungan pengujian memakai MAIL_MAILER=array sehingga email tidak dikirim.
        $this->actingAs($this->admin())
            ->post(route('admin.pengaturan-sistem.email.tes'))
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHas('error', fn (string $pesan): bool => str_contains($pesan, 'belum dapat dijalankan'));
    }

    public function test_admin_dapat_mengubah_durasi_sesi_login(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.pengaturan-sistem.keamanan'), ['durasi_sesi' => 45])
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pengaturan_sistems', ['durasi_sesi' => 45]);

        $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem'))
            ->assertOk()
            ->assertSee('45 menit');

        $this->assertSame(45, config('session.lifetime'));
    }

    public function test_durasi_sesi_divalidasi(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.pengaturan-sistem.keamanan'), ['durasi_sesi' => 2])
            ->assertSessionHasErrors('durasi_sesi');

        $this->assertDatabaseHas('pengaturan_sistems', ['durasi_sesi' => 120]);
    }

    public function test_admin_dapat_membuat_mengunduh_dan_menghapus_backup(): void
    {
        Storage::fake('local');

        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.pengaturan-sistem.backup.buat'))
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHas('success');

        $berkas = collect(Storage::disk('local')->files('backups'))->first();

        $this->assertNotNull($berkas);
        $this->assertStringContainsString('INSERT INTO', (string) Storage::disk('local')->get($berkas));
        $this->assertStringContainsString('CREATE', strtoupper((string) Storage::disk('local')->get($berkas)));

        $this->assertDatabaseHas('activity_logs', [
            'modul' => 'pemeliharaan',
            'aksi' => 'backup_dibuat',
        ]);

        $nama = basename((string) $berkas);

        $unduh = $this->actingAs($admin)
            ->get(route('admin.pengaturan-sistem.backup.unduh', ['berkas' => $nama]));

        $unduh->assertOk();
        $this->assertStringContainsString($nama, (string) $unduh->headers->get('content-disposition'));

        $this->actingAs($admin)
            ->delete(route('admin.pengaturan-sistem.backup.hapus', ['berkas' => $nama]))
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHas('success');

        Storage::disk('local')->assertMissing($berkas);
        $this->assertDatabaseHas('activity_logs', ['aksi' => 'backup_dihapus']);
    }

    public function test_retensi_backup_memangkas_berkas_lama(): void
    {
        Storage::fake('local');

        $layanan = app(BackupDatabaseService::class);

        $layanan->buat(2);
        $this->travel(2)->seconds();
        $layanan->buat(2);
        $this->travel(2)->seconds();
        $layanan->buat(2);

        $this->assertCount(2, Storage::disk('local')->files('backups'));
    }

    public function test_pemeliharaan_cache_dicatat_pada_audit(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.pengaturan-sistem.pemeliharaan'), ['tindakan' => 'cache_tampilan'])
            ->assertRedirect(route('admin.pengaturan-sistem'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_logs', [
            'modul' => 'pemeliharaan',
            'aksi' => 'pemeliharaan_dijalankan',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.pengaturan-sistem.pemeliharaan'), ['tindakan' => 'hapus_semua'])
            ->assertSessionHasErrors('tindakan');
    }

    public function test_selain_admin_tidak_dapat_mengubah_pengaturan(): void
    {
        $musyrifah = User::factory()->create(['role' => 'musyrifah', 'status' => 'aktif']);

        $permintaan = [
            ['put', route('admin.pengaturan-sistem.email'), ['mail_mode' => 'lokal']],
            ['post', route('admin.pengaturan-sistem.email.tes'), []],
            ['put', route('admin.pengaturan-sistem.keamanan'), ['durasi_sesi' => 15]],
            ['put', route('admin.pengaturan-sistem.backup'), ['backup_retensi' => 2]],
            ['post', route('admin.pengaturan-sistem.backup.buat'), []],
            ['get', route('admin.pengaturan-sistem.backup.unduh', ['berkas' => 'backup-20260101-000000.sql']), []],
            ['delete', route('admin.pengaturan-sistem.backup.hapus', ['berkas' => 'backup-20260101-000000.sql']), []],
            ['post', route('admin.pengaturan-sistem.pemeliharaan'), ['tindakan' => 'cache_aplikasi']],
        ];

        foreach ($permintaan as [$metode, $alamat, $data]) {
            $this->actingAs($musyrifah)->{$metode}($alamat, $data)->assertForbidden();
        }
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
    }
}
