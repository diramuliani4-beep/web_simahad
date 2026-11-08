<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MusyrifahDataKamarTest extends TestCase
{
    use RefreshDatabase;

    public function test_musyrifah_can_access_data_kamar_page(): void
    {
        $musyrifah = User::factory()->create([
            'role' => 'musyrifah',
        ]);

        Kamar::create([
            'nama_kamar' => 'Kamar 101',
            'nomor_kamar' => '101',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
            'fasilitas' => 'Kasur, lemari, meja belajar',
        ]);

        $response = $this->actingAs($musyrifah)->get(route('musyrifah.data_kamar'));

        $response->assertOk();
        $response->assertSee('Manajemen Data Kamar');
        $response->assertSee('Kamar 101');
        $response->assertSee('Kasur, lemari, meja belajar');
    }

    public function test_musyrifah_can_create_new_room_with_photo(): void
    {
        Storage::fake('public');

        $musyrifah = User::factory()->create([
            'role' => 'musyrifah',
        ]);

        $photo = UploadedFile::fake()->image('kamar201.jpg');

        $response = $this->actingAs($musyrifah)->post(route('musyrifah.data_kamar.store'), [
            'nomor_kamar' => '201',
            'gedung' => 'Gedung A',
            'lantai' => 2,
            'kapasitas' => 4,
            'status' => 'Tersedia',
            'fasilitas' => 'Kasur tingkat, meja, AC',
            'foto' => $photo,
        ]);

        $response->assertRedirect(route('musyrifah.data_kamar'));

        $kamar = Kamar::where('nomor_kamar', '201')->first();
        $this->assertNotNull($kamar);
        $this->assertSame(2, $kamar->lantai);
        $this->assertSame(4, $kamar->kapasitas);
        $this->assertNotNull($kamar->foto);

        Storage::disk('public')->assertExists($kamar->foto);
    }

    public function test_musyrifah_can_update_room_and_view_residents(): void
    {
        $musyrifah = User::factory()->create([
            'role' => 'musyrifah',
        ]);

        $kamar = Kamar::create([
            'nama_kamar' => 'Kamar 301',
            'nomor_kamar' => '301',
            'gedung' => 'Gedung A',
            'lantai' => 3,
            'kapasitas' => 3,
            'terisi' => 1,
            'status' => 'Tersedia',
            'fasilitas' => 'Fasilitas standar',
        ]);

        $santri = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Ahmad Santri',
            'nim' => '20261199',
            'kamar_id' => $kamar->id,
        ]);

        // Musyrifah update status
        $updateResponse = $this->actingAs($musyrifah)->put(route('musyrifah.data_kamar.update', $kamar->id), [
            'nomor_kamar' => '301',
            'gedung' => 'Gedung A',
            'lantai' => 3,
            'kapasitas' => 3,
            'status' => 'Penuh',
            'fasilitas' => 'Fasilitas baru lengkap',
        ]);

        $updateResponse->assertRedirect(route('musyrifah.data_kamar'));

        $kamar->refresh();
        $this->assertSame('Penuh', $kamar->status);
        $this->assertSame('Fasilitas baru lengkap', $kamar->fasilitas);

        // Halaman memuat daftar santri di kamar tersebut
        $pageResponse = $this->actingAs($musyrifah)->get(route('musyrifah.data_kamar'));
        $pageResponse->assertSee('Ahmad Santri');
        $pageResponse->assertSee('20261199');
    }
}
