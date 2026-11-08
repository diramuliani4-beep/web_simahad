<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\KamarFoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KamarFotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_musyrifah_dapat_mengunggah_beberapa_foto_saat_membuat_kamar(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->buatMusyrifah())->post(route('musyrifah.data_kamar.store'), [
            'nomor_kamar' => '102',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 4,
            'status' => 'Tersedia',
            'fasilitas' => 'AC, lemari',
            'foto' => [
                UploadedFile::fake()->image('depan.jpg'),
                UploadedFile::fake()->image('dalam.jpg'),
                UploadedFile::fake()->image('mandi.jpg'),
            ],
        ]);

        $response->assertRedirect(route('musyrifah.data_kamar'));

        $kamar = Kamar::where('nomor_kamar', '102')->firstOrFail();
        $fotos = $kamar->fotos()->get();

        $this->assertCount(3, $fotos);
        $this->assertSame([1, 2, 3], $fotos->pluck('urutan')->all());
        $this->assertSame($fotos->first()->path, $kamar->foto);

        $fotos->each(fn (KamarFoto $foto) => Storage::disk('public')->assertExists($foto->path));
    }

    public function test_unggahan_lebih_dari_empat_foto_ditolak(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->buatMusyrifah())->post(route('musyrifah.data_kamar.store'), [
            'nomor_kamar' => '103',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 4,
            'status' => 'Tersedia',
            'foto' => array_map(
                fn (int $i) => UploadedFile::fake()->image('foto-'.$i.'.jpg'),
                range(1, 5)
            ),
        ]);

        $response->assertSessionHasErrors('foto');
        $this->assertNull(Kamar::where('nomor_kamar', '103')->first());
    }

    public function test_musyrifah_dapat_menambah_foto_pada_galeri_kamar(): void
    {
        Storage::fake('public');
        $kamar = $this->buatKamar();

        $response = $this->actingAs($this->buatMusyrifah())->post(
            route('musyrifah.data_kamar.foto.store', $kamar),
            ['fotos' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')]]
        );

        $response->assertRedirect(route('musyrifah.data_kamar').'#kamar-'.$kamar->id);
        $response->assertSessionHas('success');

        $kamar->refresh();
        $this->assertSame(2, $kamar->fotos()->count());
        $this->assertSame($kamar->fotos()->orderBy('urutan')->value('path'), $kamar->foto);
    }

    public function test_penambahan_foto_dibatasi_maksimal_empat(): void
    {
        Storage::fake('public');
        $kamar = $this->buatKamar();

        foreach (range(1, 4) as $urutan) {
            KamarFoto::create([
                'kamar_id' => $kamar->id,
                'path' => UploadedFile::fake()->image('foto-'.$urutan.'.jpg')->store('foto_kamar', 'public'),
                'urutan' => $urutan,
            ]);
        }

        $kamar->sinkronkanFotoUtama();

        $response = $this->actingAs($this->buatMusyrifah())->post(
            route('musyrifah.data_kamar.foto.store', $kamar),
            ['fotos' => [UploadedFile::fake()->image('tambahan.jpg')]]
        );

        $response->assertSessionHas('error');
        $this->assertSame(4, $kamar->fotos()->count());
    }

    public function test_jumlah_unggahan_melebihi_sisa_slot_ditolak(): void
    {
        Storage::fake('public');
        $kamar = $this->buatKamar();

        foreach (range(1, 3) as $urutan) {
            KamarFoto::create([
                'kamar_id' => $kamar->id,
                'path' => UploadedFile::fake()->image('foto-'.$urutan.'.jpg')->store('foto_kamar', 'public'),
                'urutan' => $urutan,
            ]);
        }

        $response = $this->actingAs($this->buatMusyrifah())->post(
            route('musyrifah.data_kamar.foto.store', $kamar),
            ['fotos' => [UploadedFile::fake()->image('x.jpg'), UploadedFile::fake()->image('y.jpg')]]
        );

        $response->assertSessionHasErrors('foto');
        $this->assertSame(3, $kamar->fotos()->count());
    }

    public function test_musyrifah_dapat_mengganti_foto_tanpa_mengubah_urutan(): void
    {
        Storage::fake('public');
        $kamar = $this->buatKamar();

        foreach (range(1, 2) as $urutan) {
            KamarFoto::create([
                'kamar_id' => $kamar->id,
                'path' => UploadedFile::fake()->image('asli-'.$urutan.'.jpg')->store('foto_kamar', 'public'),
                'urutan' => $urutan,
            ]);
        }

        $kamar->sinkronkanFotoUtama();

        $fotoKedua = $kamar->fotos()->where('urutan', 2)->firstOrFail();
        $pathLama = $fotoKedua->path;

        $response = $this->actingAs($this->buatMusyrifah())->post(
            route('musyrifah.data_kamar.foto.update', [$kamar, $fotoKedua]),
            ['foto' => UploadedFile::fake()->image('pengganti.jpg')]
        );

        $response->assertRedirect(route('musyrifah.data_kamar').'#kamar-'.$kamar->id);
        $response->assertSessionHas('success');

        $fotoKedua->refresh();
        $this->assertNotSame($pathLama, $fotoKedua->path);
        $this->assertSame(2, $fotoKedua->urutan);
        Storage::disk('public')->assertMissing($pathLama);
        $this->assertSame($kamar->fotos()->where('urutan', 1)->value('path'), $kamar->fresh()->foto);
    }

    public function test_menghapus_foto_utama_menjadikan_foto_berikutnya_sebagai_utama(): void
    {
        Storage::fake('public');
        $kamar = $this->buatKamar();
        $paths = [];

        foreach (range(1, 3) as $urutan) {
            $paths[$urutan] = UploadedFile::fake()->image('foto-'.$urutan.'.jpg')->store('foto_kamar', 'public');

            KamarFoto::create([
                'kamar_id' => $kamar->id,
                'path' => $paths[$urutan],
                'urutan' => $urutan,
            ]);
        }

        $kamar->sinkronkanFotoUtama();
        $fotoPertama = $kamar->fotos()->where('urutan', 1)->firstOrFail();

        $response = $this->actingAs($this->buatMusyrifah())->delete(
            route('musyrifah.data_kamar.foto.destroy', [$kamar, $fotoPertama])
        );

        $response->assertRedirect(route('musyrifah.data_kamar').'#kamar-'.$kamar->id);
        $response->assertSessionHas('success');

        $kamar->refresh();
        $this->assertSame(2, $kamar->fotos()->count());
        $this->assertSame($paths[2], $kamar->foto);
        $this->assertSame([1, 2], $kamar->fotos()->get()->pluck('urutan')->all());
        Storage::disk('public')->assertMissing($paths[1]);
    }

    public function test_foto_tidak_dapat_diubah_melalui_kamar_lain(): void
    {
        Storage::fake('public');
        $kamar = $this->buatKamar();
        $kamarLain = $this->buatKamar(['nomor_kamar' => '102', 'nama_kamar' => 'Kamar 102']);

        $foto = KamarFoto::create([
            'kamar_id' => $kamar->id,
            'path' => UploadedFile::fake()->image('foto.jpg')->store('foto_kamar', 'public'),
            'urutan' => 1,
        ]);

        $this->actingAs($this->buatMusyrifah())
            ->delete(route('musyrifah.data_kamar.foto.destroy', [$kamarLain, $foto]))
            ->assertNotFound();

        $this->assertSame(1, $kamar->fotos()->count());
    }

    public function test_halaman_data_kamar_menampilkan_galeri_dan_aksi_pengelolaan(): void
    {
        Storage::fake('public');
        $kamar = $this->buatKamar();

        foreach (range(1, 2) as $urutan) {
            KamarFoto::create([
                'kamar_id' => $kamar->id,
                'path' => UploadedFile::fake()->image('galeri-'.$urutan.'.jpg')->store('foto_kamar', 'public'),
                'urutan' => $urutan,
            ]);
        }

        $kamar->sinkronkanFotoUtama();
        $foto = $kamar->fotos()->firstOrFail();

        $response = $this->actingAs($this->buatMusyrifah())->get(route('musyrifah.data_kamar'));

        $response->assertOk();
        $response->assertSee('Galeri Foto (2/4)');
        $response->assertSee('Foto Utama');
        $response->assertSee(route('musyrifah.data_kamar.foto.update', [$kamar->id, $foto->id]), false);
        $response->assertSee(route('musyrifah.data_kamar.foto.destroy', [$kamar->id, $foto->id]), false);
    }

    public function test_mahasiswa_melihat_seluruh_foto_kamar_pada_informasi_kamar(): void
    {
        Storage::fake('public');
        $kamar = $this->buatKamar();

        foreach (range(1, 3) as $urutan) {
            KamarFoto::create([
                'kamar_id' => $kamar->id,
                'path' => 'foto_kamar/kamar-'.$urutan.'.jpg',
                'urutan' => $urutan,
            ]);
        }

        $kamar->sinkronkanFotoUtama();

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamar->id,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('kamar'));

        $response->assertOk();
        $response->assertSee('data-galeri', false);
        $response->assertSee(Storage::url('foto_kamar/kamar-1.jpg'));
        $response->assertSee(Storage::url('foto_kamar/kamar-3.jpg'));
        $response->assertSee('1 / 3');
    }

    public function test_foto_lama_pada_kolom_kamars_foto_tetap_tampil(): void
    {
        Storage::fake('public');
        $kamar = $this->buatKamar(['foto' => 'foto_kamar/foto-lama.jpg']);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'kamar_id' => $kamar->id,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('kamar'));

        $response->assertOk();
        $response->assertSee('data-galeri', false);
        $response->assertSee(Storage::url('foto_kamar/foto-lama.jpg'));
    }

    public function test_foto_lama_dipertahankan_sebagai_foto_utama_saat_menambah_foto_baru(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('foto_kamar/lama.jpg', 'berkas-lama');

        $kamar = $this->buatKamar(['foto' => 'foto_kamar/lama.jpg']);

        $this->actingAs($this->buatMusyrifah())
            ->post(route('musyrifah.data_kamar.foto.store', $kamar), [
                'fotos' => [UploadedFile::fake()->image('baru.jpg')],
            ])
            ->assertSessionHas('success');

        $kamar->refresh();
        $fotos = $kamar->fotos()->get();

        $this->assertCount(2, $fotos);
        $this->assertSame('foto_kamar/lama.jpg', $fotos->first()->path);
        $this->assertSame('foto_kamar/lama.jpg', $kamar->foto);
        $this->assertSame([1, 2], $fotos->pluck('urutan')->all());
    }

    private function buatMusyrifah(): User
    {
        return User::factory()->create(['role' => 'musyrifah']);
    }

    /**
     * @param  array<string, mixed>  $atribut
     */
    private function buatKamar(array $atribut = []): Kamar
    {
        return Kamar::create(array_merge([
            'nama_kamar' => 'Kamar 101',
            'nomor_kamar' => '101',
            'gedung' => 'Gedung A',
            'lantai' => 1,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
            'fasilitas' => 'Kasur, lemari, meja belajar',
        ], $atribut));
    }
}
