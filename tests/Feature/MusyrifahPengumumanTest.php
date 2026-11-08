<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MusyrifahPengumumanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat akun Musyrifah yang mengelola pengumuman.
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

    /**
     * Membuat akun mahasiswa penerima pengumuman.
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
     * Payload formulir pengumuman yang valid.
     *
     * @param  array<string, mixed>  $tambahan
     * @return array<string, mixed>
     */
    private function payload(array $tambahan = []): array
    {
        return array_merge([
            'judul' => 'Jadwal Kegiatan Mahasantri',
            'kategori' => Pengumuman::KATEGORI_AKADEMIK,
            'isi' => 'Seluruh mahasantri diharapkan mengikuti kegiatan sesuai jadwal yang telah ditetapkan oleh pengelola Ma\'had.',
            'tanggal_publikasi' => now()->toDateString(),
            'status' => Pengumuman::STATUS_DRAFT,
        ], $tambahan);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get(route('musyrifah.pengumuman'))->assertRedirect(route('login'));
    }

    public function test_mahasiswa_cannot_manage_announcements(): void
    {
        $mahasiswa = $this->buatMahasiswa();

        $this->actingAs($mahasiswa)
            ->get(route('musyrifah.pengumuman'))
            ->assertForbidden();

        $this->actingAs($mahasiswa)
            ->post(route('musyrifah.pengumuman.store'), $this->payload())
            ->assertForbidden();

        $this->actingAs($mahasiswa)
            ->get(route('musyrifah.detail-pengumuman'))
            ->assertForbidden();
    }

    public function test_musyrifah_can_open_management_pages(): void
    {
        $musyrifah = $this->buatMusyrifah();

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman'))
            ->assertOk()
            ->assertSee('Kelola Informasi Untuk Mahasantri')
            ->assertSee('Daftar Pengumuman');

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.detail-pengumuman'))
            ->assertOk()
            ->assertSee('Buat Pengumuman Baru Untuk Mahasantri');
    }

    public function test_musyrifah_can_store_draft_announcement(): void
    {
        Storage::fake('local');

        $musyrifah = $this->buatMusyrifah();

        $response = $this->actingAs($musyrifah)
            ->post(route('musyrifah.pengumuman.store'), $this->payload());

        $pengumuman = Pengumuman::query()->firstOrFail();

        $response->assertRedirect(route('musyrifah.pengumuman.show', $pengumuman));

        $this->assertDatabaseHas('pengumumans', [
            'judul' => 'Jadwal Kegiatan Mahasantri',
            'kategori' => Pengumuman::KATEGORI_AKADEMIK,
            'status' => Pengumuman::STATUS_DRAFT,
            'user_id' => $musyrifah->id,
            'penulis_nama' => 'Ustadzah Hanifah',
        ]);

        $this->assertNull($pengumuman->dipublikasikan_pada);
    }

    public function test_draft_announcement_is_not_visible_for_mahasiswa(): void
    {
        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        Pengumuman::factory()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Draft Rahasia Ma\'had',
            'status' => Pengumuman::STATUS_DRAFT,
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs'))
            ->assertOk()
            ->assertDontSee('Draft Rahasia Ma\'had');
    }

    public function test_published_announcement_appears_for_mahasiswa_without_duplicate_record(): void
    {
        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        $this->actingAs($musyrifah)->post(route('musyrifah.pengumuman.store'), $this->payload([
            'judul' => 'Informasi Tata Tertib Asrama',
            'status' => Pengumuman::STATUS_DIPUBLIKASIKAN,
        ]));

        $this->assertDatabaseCount('pengumumans', 1);
        $this->assertNotNull(Pengumuman::query()->firstOrFail()->dipublikasikan_pada);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs'))
            ->assertOk()
            ->assertSee('Informasi Tata Tertib Asrama')
            ->assertSee(Pengumuman::KATEGORI_AKADEMIK);

        $pengumuman = Pengumuman::query()->firstOrFail();

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs.show', $pengumuman))
            ->assertOk()
            ->assertSee('Informasi Tata Tertib Asrama');
    }

    public function test_musyrifah_can_update_announcement_and_changes_are_visible_for_mahasiswa(): void
    {
        Storage::fake('local');

        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->dipublikasikan()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Judul Lama Pengumuman',
            'isi' => 'Isi pengumuman sebelumnya yang akan diperbarui oleh Musyrifah.',
        ]);

        $this->actingAs($musyrifah)
            ->put(route('musyrifah.pengumuman.update', $pengumuman), $this->payload([
                'judul' => 'Judul Baru Pengumuman',
                'status' => Pengumuman::STATUS_DIPUBLIKASIKAN,
            ]))
            ->assertRedirect(route('musyrifah.pengumuman.show', $pengumuman));

        $this->assertDatabaseCount('pengumumans', 1);
        $this->assertDatabaseHas('pengumumans', [
            'id' => $pengumuman->id,
            'judul' => 'Judul Baru Pengumuman',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs'))
            ->assertSee('Judul Baru Pengumuman')
            ->assertDontSee('Judul Lama Pengumuman');
    }

    public function test_musyrifah_can_change_status_back_to_draft(): void
    {
        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->dipublikasikan()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Pengumuman Yang Akan Ditarik',
        ]);

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pengumuman.status', $pengumuman), [
                'status' => Pengumuman::STATUS_DRAFT,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pengumumans', [
            'id' => $pengumuman->id,
            'status' => Pengumuman::STATUS_DRAFT,
            'dipublikasikan_pada' => null,
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs'))
            ->assertDontSee('Pengumuman Yang Akan Ditarik');

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs.show', $pengumuman))
            ->assertNotFound();
    }

    public function test_musyrifah_can_delete_announcement_and_files(): void
    {
        Storage::fake('local');

        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        Storage::disk('local')->put('pengumuman/gambar/contoh.jpg', 'isi');

        $pengumuman = Pengumuman::factory()->dipublikasikan()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Pengumuman Untuk Dihapus',
            'gambar_path' => 'pengumuman/gambar/contoh.jpg',
            'gambar_nama' => 'contoh.jpg',
        ]);

        $this->actingAs($musyrifah)
            ->delete(route('musyrifah.pengumuman.destroy', $pengumuman))
            ->assertRedirect(route('musyrifah.pengumuman'));

        $this->assertDatabaseCount('pengumumans', 0);
        Storage::disk('local')->assertMissing('pengumuman/gambar/contoh.jpg');

        $this->actingAs($mahasiswa)
            ->get(route('pengumuman.mhs.show', $pengumuman))
            ->assertNotFound();
    }

    public function test_announcement_form_requires_mandatory_fields(): void
    {
        $musyrifah = $this->buatMusyrifah();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pengumuman.store'), [])
            ->assertSessionHasErrors([
                'judul',
                'kategori',
                'isi',
                'tanggal_publikasi',
                'status',
            ]);
    }

    public function test_announcement_rejects_invalid_file_types(): void
    {
        Storage::fake('local');

        $musyrifah = $this->buatMusyrifah();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pengumuman.store'), $this->payload([
                'gambar' => UploadedFile::fake()->create('gambar.txt', 20, 'text/plain'),
                'dokumen' => UploadedFile::fake()->create('dokumen.exe', 20, 'application/octet-stream'),
            ]))
            ->assertSessionHasErrors(['gambar', 'dokumen']);

        $this->assertDatabaseCount('pengumumans', 0);
    }

    public function test_musyrifah_can_upload_and_download_supporting_files(): void
    {
        Storage::fake('local');

        $musyrifah = $this->buatMusyrifah();

        $this->actingAs($musyrifah)->post(route('musyrifah.pengumuman.store'), $this->payload([
            'judul' => 'Pengumuman Dengan Lampiran',
            'status' => Pengumuman::STATUS_DIPUBLIKASIKAN,
            'gambar' => UploadedFile::fake()->image('poster.jpg', 800, 600),
            'dokumen' => UploadedFile::fake()->create('juknis.pdf', 120, 'application/pdf'),
        ]))->assertRedirect();

        $pengumuman = Pengumuman::query()->firstOrFail();

        $this->assertTrue($pengumuman->punyaGambar());
        $this->assertTrue($pengumuman->punyaDokumen());
        Storage::disk('local')->assertExists($pengumuman->gambar_path);
        Storage::disk('local')->assertExists($pengumuman->dokumen_path);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman.berkas', [$pengumuman, 'dokumen']))
            ->assertOk();

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman.berkas', [$pengumuman, 'gambar']))
            ->assertOk();
    }

    public function test_musyrifah_can_remove_uploaded_image_when_editing(): void
    {
        Storage::fake('local');

        $musyrifah = $this->buatMusyrifah();

        Storage::disk('local')->put('pengumuman/gambar/lama.jpg', 'isi');

        $pengumuman = Pengumuman::factory()->create([
            'user_id' => $musyrifah->id,
            'gambar_path' => 'pengumuman/gambar/lama.jpg',
            'gambar_nama' => 'lama.jpg',
        ]);

        $this->actingAs($musyrifah)
            ->put(route('musyrifah.pengumuman.update', $pengumuman), $this->payload([
                'hapus_gambar' => '1',
            ]))
            ->assertRedirect();

        $pengumuman->refresh();

        $this->assertNull($pengumuman->gambar_path);
        $this->assertNull($pengumuman->gambar_nama);
        Storage::disk('local')->assertMissing('pengumuman/gambar/lama.jpg');
    }

    public function test_musyrifah_can_add_custom_category(): void
    {
        $musyrifah = $this->buatMusyrifah();

        $this->actingAs($musyrifah)->post(route('musyrifah.pengumuman.store'), $this->payload([
            'kategori' => 'lainnya',
            'kategori_baru' => 'Beasiswa',
        ]))->assertRedirect();

        $this->assertDatabaseHas('pengumumans', ['kategori' => 'Beasiswa']);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman'))
            ->assertSee('Beasiswa');
    }

    public function test_custom_category_requires_category_name(): void
    {
        $musyrifah = $this->buatMusyrifah();

        $this->actingAs($musyrifah)
            ->post(route('musyrifah.pengumuman.store'), $this->payload([
                'kategori' => 'lainnya',
                'kategori_baru' => '',
            ]))
            ->assertSessionHasErrors(['kategori_baru']);
    }

    public function test_list_can_be_searched_by_title(): void
    {
        $musyrifah = $this->buatMusyrifah();

        Pengumuman::factory()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Jadwal Ujian Semester Ganjil',
        ]);

        Pengumuman::factory()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Pembayaran Asrama Bulan Ini',
            'kategori' => Pengumuman::KATEGORI_PEMBAYARAN,
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman', ['cari' => 'Ujian']))
            ->assertOk()
            ->assertSee('Jadwal Ujian Semester Ganjil')
            ->assertDontSee('Pembayaran Asrama Bulan Ini');
    }

    public function test_list_can_be_filtered_by_category_and_status(): void
    {
        $musyrifah = $this->buatMusyrifah();

        Pengumuman::factory()->dipublikasikan()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Pembayaran Semester Ini',
            'kategori' => Pengumuman::KATEGORI_PEMBAYARAN,
        ]);

        Pengumuman::factory()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Draft Aturan Baru',
            'kategori' => Pengumuman::KATEGORI_PERATURAN,
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman', ['kategori' => Pengumuman::KATEGORI_PEMBAYARAN]))
            ->assertSee('Pembayaran Semester Ini')
            ->assertDontSee('Draft Aturan Baru');

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman', ['status' => Pengumuman::STATUS_DRAFT]))
            ->assertSee('Draft Aturan Baru')
            ->assertDontSee('Pembayaran Semester Ini');
    }

    public function test_list_shows_announcement_statistics(): void
    {
        $musyrifah = $this->buatMusyrifah();

        Pengumuman::factory()->dipublikasikan()->count(2)->create(['user_id' => $musyrifah->id]);
        Pengumuman::factory()->count(3)->create(['user_id' => $musyrifah->id]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman'))
            ->assertSee('5 Pengumuman')
            ->assertSee('2 Dipublikasikan')
            ->assertSee('3 Belum Terbit');
    }

    public function test_detail_page_shows_full_announcement(): void
    {
        $musyrifah = $this->buatMusyrifah();

        $pengumuman = Pengumuman::factory()->dipublikasikan()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Detail Pengumuman Lengkap',
            'isi' => 'Isi lengkap pengumuman yang harus tampil pada halaman detail Musyrifah.',
            'dokumen_path' => 'pengumuman/dokumen/juknis.pdf',
            'dokumen_nama' => 'juknis.pdf',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman.show', $pengumuman))
            ->assertOk()
            ->assertSee('Detail Pengumuman Lengkap')
            ->assertSee('Isi lengkap pengumuman yang harus tampil pada halaman detail Musyrifah.')
            ->assertSee('juknis.pdf')
            ->assertSee('Edit Pengumuman');
    }

    public function test_musyrifah_can_open_edit_form(): void
    {
        $musyrifah = $this->buatMusyrifah();

        $pengumuman = Pengumuman::factory()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Pengumuman Yang Diedit',
        ]);

        $this->actingAs($musyrifah)
            ->get(route('musyrifah.pengumuman.edit', $pengumuman))
            ->assertOk()
            ->assertSee('Edit Pengumuman')
            ->assertSee('Pengumuman Yang Diedit');
    }

    public function test_mahasiswa_cannot_open_musyrifah_announcement_detail(): void
    {
        $musyrifah = $this->buatMusyrifah();
        $mahasiswa = $this->buatMahasiswa();

        $pengumuman = Pengumuman::factory()->create(['user_id' => $musyrifah->id]);

        $this->actingAs($mahasiswa)
            ->get(route('musyrifah.pengumuman.show', $pengumuman))
            ->assertForbidden();

        $this->actingAs($mahasiswa)
            ->delete(route('musyrifah.pengumuman.destroy', $pengumuman))
            ->assertForbidden();

        $this->assertDatabaseHas('pengumumans', ['id' => $pengumuman->id]);
    }
}
