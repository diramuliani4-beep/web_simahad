<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class SimahadUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_seluruh_role_merender_satu_komponen_umpan_balik_simahad(): void
    {
        $halaman = [
            'admin' => 'admin.dashboard',
            'pimpinan' => 'pimpinan.dashboard',
            'musyrifah' => 'musyrifah.dashboard',
            'guru' => 'guru.dashboard',
            'mahasiswa' => 'dashboard',
        ];

        foreach ($halaman as $role => $rute) {
            $pengguna = User::factory()->create(['role' => $role, 'status' => 'aktif']);

            $html = $this->actingAs($pengguna)->get(route($rute))->assertOk()->getContent();

            foreach (['suiToastRegion', 'suiConfirmDialog', 'suiPromptDialog'] as $id) {
                $this->assertSame(1, substr_count($html, 'id="'.$id.'"'), "Komponen {$id} tidak unik pada dashboard {$role}.");
            }

            $this->assertStringContainsString('window.SimahadUI = Object.freeze', $html, "API umpan balik bersama tidak tersedia pada dashboard {$role}.");
        }
    }

    public function test_halaman_autentikasi_merender_komponen_umpan_balik_simahad(): void
    {
        $halaman = [
            '/login',
            '/forgot-password',
            '/aktivasi-akun/token-tidak-valid',
        ];

        foreach ($halaman as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            foreach (['suiToastRegion', 'suiConfirmDialog', 'suiPromptDialog'] as $id) {
                $this->assertSame(1, substr_count($html, 'id="'.$id.'"'), "Komponen {$id} tidak unik pada halaman {$url}.");
            }

            $this->assertStringContainsString('window.SimahadUI = Object.freeze', $html, "API umpan balik bersama tidak tersedia pada halaman {$url}.");
        }
    }

    public function test_flash_server_merender_toast_dan_mengandung_html_berbahaya(): void
    {
        $pesan = 'Pembayaran <script>window.__simahadXss = true</script> & berhasil';

        $html = $this->withSession(['success' => $pesan])
            ->get('/login')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('class="sui-toast sui-toast--success"', $html);
        $this->assertStringContainsString(e($pesan), $html);
        $this->assertStringNotContainsString('<script>window.__simahadXss = true</script>', $html);
    }

    public function test_flash_server_yang_sama_hanya_merender_satu_toast(): void
    {
        $pesan = 'Notifikasi yang sama';

        $html = $this->withSession(['success' => $pesan, 'error' => $pesan])
            ->get('/login')
            ->assertOk()
            ->getContent();

        $this->assertSame(1, substr_count($html, $pesan));
        $this->assertStringContainsString('class="sui-toast sui-toast--success"', $html);
        $this->assertStringNotContainsString('class="sui-toast sui-toast--error"', $html);
    }

    public function test_seluruh_view_tidak_memanggil_dialog_bawaan_browser(): void
    {
        $akarView = resource_path('views');
        $berkas = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($akarView));
        $temuan = [];
        $pola = '/(?<![\w$.])(?:(?:window|globalThis|self)\s*\.\s*)?(?:alert|confirm|prompt)\s*\(/i';

        foreach ($berkas as $berkasView) {
            if (! $berkasView->isFile() || $berkasView->getExtension() !== 'php') {
                continue;
            }

            $isi = file_get_contents($berkasView->getPathname());

            if (! preg_match_all($pola, $isi, $kecocokan, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            $pathRelatif = str_replace($akarView.DIRECTORY_SEPARATOR, '', $berkasView->getPathname());

            foreach ($kecocokan[0] as [$panggilan, $posisi]) {
                $baris = substr_count(substr($isi, 0, $posisi), "\n") + 1;
                $temuan[] = $pathRelatif.':'.$baris.' memakai '.$panggilan;
            }
        }

        $this->assertSame([], $temuan, implode(PHP_EOL, $temuan));
    }
}
