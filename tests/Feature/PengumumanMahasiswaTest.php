<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengumumanMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat akun mahasiswa pembaca pengumuman.
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

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get(route('pengumuman.mhs'))->assertRedirect(route('login'));
    }

    public function test_musyrifah_cannot_open_mahasiswa_announcement_page(): void
    {
        $musyrifah = $this->buatMusyrifah();

        $this->actingAs($musyrifah)
            ->get(route('pengumuman.mhs'))
            ->assertForbidden();
    }

    public function test_mahasiswa_sees_only_published_announcements(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Pengumuman Terbit Untuk Mahasiswa',
        ]);

        Pengumuman::factory()->draft()->create([
            'judul' => 'Pengumuman Draft Musyrifah',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs'))
            ->assertOk()
            ->assertSee('Pengumuman Terbit Untuk Mahasiswa')
            ->assertDontSee('Pengumuman Draft Musyrifah');
    }

    public function test_announcements_are_ordered_by_publication_date_descending(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Pengumuman Terbaru',
            'tanggal_publikasi' => now()->toDateString(),
        ]);

        Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Pengumuman Terdahulu',
            'tanggal_publikasi' => now()->subDays(5)->toDateString(),
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs'))
            ->assertSeeInOrder(['Pengumuman Terbaru', 'Pengumuman Terdahulu']);
    }

    public function test_announcement_list_shows_category_and_summary(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Pembayaran Asrama Semester Ganjil',
            'kategori' => Pengumuman::KATEGORI_PEMBAYARAN,
            'isi' => 'Pembayaran asrama semester ganjil dapat dilakukan melalui bank yang telah ditunjuk Ma\'had.',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs'))
            ->assertOk()
            ->assertSee('Pembayaran Asrama Semester Ganjil')
            ->assertSee(Pengumuman::KATEGORI_PEMBAYARAN)
            ->assertSee('Pembayaran asrama semester ganjil dapat dilakukan')
            ->assertSee('Tanggal publikasi:');
    }

    public function test_mahasiswa_can_search_announcement_by_title(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Jadwal Ujian Tahfidz',
        ]);

        Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Pembayaran Asrama',
            'kategori' => Pengumuman::KATEGORI_PEMBAYARAN,
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs', ['cari' => 'Tahfidz']))
            ->assertOk()
            ->assertSee('Jadwal Ujian Tahfidz')
            ->assertDontSee('Pembayaran Asrama');
    }

    public function test_mahasiswa_can_filter_announcement_by_category(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Aturan Jam Malam Ma\'had',
            'kategori' => Pengumuman::KATEGORI_PERATURAN,
        ]);

        Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Kegiatan Muhadharah Pekanan',
            'kategori' => Pengumuman::KATEGORI_KEGIATAN,
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs', ['kategori' => Pengumuman::KATEGORI_PERATURAN]))
            ->assertOk()
            ->assertSee('Aturan Jam Malam Ma\'had')
            ->assertDontSee('Kegiatan Muhadharah Pekanan');
    }

    public function test_mahasiswa_can_open_announcement_detail(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Detail Informasi Ma\'had',
            'isi' => 'Isi lengkap pengumuman ini hanya dapat dibaca pada halaman detail pengumuman mahasiswa.',
            'penulis_nama' => 'Ustadzah Hanifah',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs.show', $pengumuman))
            ->assertOk()
            ->assertSee('Detail Informasi Ma\'had')
            ->assertSee('Isi lengkap pengumuman ini hanya dapat dibaca pada halaman detail pengumuman mahasiswa.')
            ->assertSee('Ustadzah Hanifah')
            ->assertSee('Kembali ke Daftar Pengumuman');
    }

    public function test_mahasiswa_cannot_open_draft_announcement_detail(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->draft()->create([
            'judul' => 'Draft Belum Terbit',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs.show', $pengumuman))
            ->assertNotFound();
    }

    public function test_mahasiswa_can_download_supporting_document(): void
    {
        Storage::fake('local');

        Storage::disk('local')->put('pengumuman/dokumen/juknis.pdf', 'isi dokumen');

        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->dipublikasikan()->create([
            'dokumen_path' => 'pengumuman/dokumen/juknis.pdf',
            'dokumen_nama' => 'juknis.pdf',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs.berkas', [$pengumuman, 'dokumen']))
            ->assertOk();

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs.show', $pengumuman))
            ->assertSee('Unduh Dokumen');
    }

    public function test_mahasiswa_cannot_download_draft_attachment(): void
    {
        Storage::fake('local');

        Storage::disk('local')->put('pengumuman/dokumen/rahasia.pdf', 'isi dokumen');

        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->draft()->create([
            'dokumen_path' => 'pengumuman/dokumen/rahasia.pdf',
            'dokumen_nama' => 'rahasia.pdf',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs.berkas', [$pengumuman, 'dokumen']))
            ->assertNotFound();
    }

    public function test_missing_attachment_returns_not_found(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->dipublikasikan()->create([
            'gambar_path' => 'pengumuman/gambar/tidak-ada.jpg',
            'gambar_nama' => 'tidak-ada.jpg',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs.berkas', [$pengumuman, 'gambar']))
            ->assertNotFound();
    }

    public function test_announcement_image_is_shown_on_list(): void
    {
        Storage::fake('local');

        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->dipublikasikan()->create([
            'judul' => 'Pengumuman Dengan Gambar',
            'gambar_path' => 'pengumuman/gambar/poster.jpg',
            'gambar_nama' => 'poster.jpg',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs'))
            ->assertSee(route('pengumuman.mhs.berkas', [$pengumuman, 'gambar']), false);
    }

    public function test_empty_state_and_read_only_page(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs'))
            ->assertOk()
            ->assertSee('Belum ada pengumuman yang sesuai dengan pencarian atau filter kategori.')
            ->assertDontSee(route('musyrifah.detail-pengumuman'), false);
    }
}
