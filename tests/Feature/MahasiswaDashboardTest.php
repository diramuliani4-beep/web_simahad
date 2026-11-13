<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\Kamar;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\PengaturanPembayaran;
use App\Models\PengumpulanTugas;
use App\Models\Pengumuman;
use App\Models\SuratPerizinan;
use App\Models\Tugas;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_menampilkan_profil_status_dan_kamar(): void
    {
        $kamar = Kamar::factory()->create([
            'nama_kamar' => 'Kamar 214',
            'nomor_kamar' => '214',
            'gedung' => 'Gedung B',
            'lantai' => 2,
        ]);

        [$mahasiswa] = $this->mahasiswa(['kamar_id' => $kamar->id]);

        Pendaftaran::query()->create([
            'user_id' => $mahasiswa->id,
            'nama_lengkap' => $mahasiswa->name,
            'nim' => $mahasiswa->nim,
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-04-12',
            'jenis_kelamin' => 'Perempuan',
            'no_hp' => '081234567890',
            'fakultas' => 'Tarbiyah dan Keguruan',
            'semester' => 4,
            'program_studi' => 'Pendidikan Agama Islam',
            'tahun_angkatan' => '2024',
            'status' => 'disetujui',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan Mahasiswa')
            ->assertSee($mahasiswa->name)
            ->assertSee('Kelompok 1')
            ->assertSee('Kamar 214')
            ->assertSee('Lantai 2')
            ->assertSee('Disetujui')
            ->assertSee('Pendidikan Agama Islam');
    }

    public function test_dashboard_menghitung_absensi_dari_data_pembelajaran(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswa();

        $this->absensi($mahasiswa, $mapel, 'hadir', 2);
        $this->absensi($mahasiswa, $mapel, 'alpa', 3);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan Absensi')
            ->assertSee('Total pertemuan: 3 kali')
            ->assertSee('66.7%');
    }

    public function test_kalender_menampilkan_jadwal_kelompok_mahasiswa(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswa();
        $hariIni = JadwalPembelajaran::HARI[now()->dayOfWeekIso];

        JadwalPembelajaran::query()->create([
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => 'Bahasa Arab Dasar',
            'kelompok' => 'Kelompok 1',
            'guru_pengampu' => 'Ustaz Fauzan',
            'hari' => $hariIni,
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'ruangan' => 'Ruang 1',
        ]);

        JadwalPembelajaran::query()->create([
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => 'Fiqih Ibadah',
            'kelompok' => 'Kelompok 2',
            'guru_pengampu' => 'Ustazah Latifah',
            'hari' => $hariIni,
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:30',
            'ruangan' => 'Ruang 2',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard', [
                'bulan' => now()->format('Y-m'),
                'tanggal' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Kalender Jadwal Pembelajaran')
            ->assertSee('Bahasa Arab Dasar')
            ->assertSee('Ustaz Fauzan')
            ->assertSee('08:00–09:30 WIB')
            ->assertDontSee('Fiqih Ibadah')
            ->assertSee('Jadwal '.now()->locale('id')->translatedFormat('l, d F Y'));
    }

    public function test_ringkasan_tugas_menampilkan_tugas_terpublikasi_saja(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswa();
        $guru = $this->guru();

        $this->tugas($guru, $mapel, 'Tugas Mufradat Dashboard');
        $this->tugas($guru, $mapel, 'Tugas Draft Tersembunyi', draft: true);
        $this->tugas($guru, $mapel, 'Tugas Kelompok Dua', kelompok: 'Kelompok 2');

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tugas Terbaru')
            ->assertSee('Tugas Mufradat Dashboard')
            ->assertSee('Belum Dikumpulkan')
            ->assertSee('Lihat semua tugas')
            ->assertDontSee('Tugas Draft Tersembunyi')
            ->assertDontSee('Tugas Kelompok Dua');
    }

    public function test_ringkasan_nilai_hanya_menampilkan_nilai_terpublikasi(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswa();
        $guru = $this->guru();

        $tugasTerbit = $this->tugas($guru, $mapel, 'Tugas Dinilai Terbit');
        $tugasTersembunyi = $this->tugas($guru, $mapel, 'Tugas Nilai Tersembunyi');

        PengumpulanTugas::factory()->nilaiDipublikasikan(87, 'Sangat baik.')->create([
            'tugas_id' => $tugasTerbit->id,
            'user_id' => $mahasiswa->id,
        ]);

        PengumpulanTugas::factory()->dinilai(43, 'Belum dipublikasikan.')->create([
            'tugas_id' => $tugasTersembunyi->id,
            'user_id' => $mahasiswa->id,
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Nilai Terbaru')
            ->assertSee('Tugas Dinilai Terbit')
            ->assertSee('87</span>/100', false)
            ->assertDontSee('43</span>/100', false);
    }

    public function test_dashboard_menampilkan_pengumuman_yang_dipublikasikan(): void
    {
        [$mahasiswa] = $this->mahasiswa();
        $musyrifah = User::factory()->create(['role' => 'musyrifah', 'status' => 'aktif']);

        Pengumuman::factory()->dipublikasikan()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Pengumuman Orientasi Ma\'had',
            'kategori' => Pengumuman::KATEGORI_KEGIATAN,
            'isi' => 'Seluruh mahasiswa wajib mengikuti orientasi pada pekan depan.',
            'tanggal_publikasi' => now()->toDateString(),
        ]);

        Pengumuman::factory()->draft()->create([
            'user_id' => $musyrifah->id,
            'judul' => 'Draft Pengumuman Internal',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pengumuman Terbaru')
            ->assertSee('Pengumuman Orientasi Ma\'had')
            ->assertSee('Kegiatan Ma\'had')
            ->assertDontSee('Draft Pengumuman Internal');
    }

    public function test_dashboard_meringkas_status_perizinan(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        SuratPerizinan::factory()->create(['user_id' => $mahasiswa->id]);
        SuratPerizinan::factory()->disetujui()->create(['user_id' => $mahasiswa->id]);
        SuratPerizinan::factory()->ditolak()->create(['user_id' => $mahasiswa->id]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Status Perizinan')
            ->assertSee('Total 3 pengajuan surat dan perizinan.');
    }

    public function test_dashboard_menampilkan_periode_pembayaran_belum_dibuka(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        PengaturanPembayaran::factory()->belumDibuka()->create();

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada periode pembayaran aktif. Silakan menunggu Musyrifah membuka periode pembayaran.')
            ->assertSee('akan dibuka pada')
            ->assertDontSee('7322022268');
    }

    public function test_dashboard_menampilkan_tagihan_nomor_rekening_bersama_dan_pengingat(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        PengaturanPembayaran::factory()->mendekatiJatuhTempo()->create();

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 2.500.000')
            ->assertSee('Tahap 1')
            ->assertSee('7322022268')
            ->assertSee('Salin No. Rek')
            ->assertSee('Pengingat: jatuh tempo tinggal 3 hari');
    }

    public function test_dashboard_menampilkan_status_terlambat_saat_jatuh_tempo_terlewati(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        PengaturanPembayaran::factory()->terlewat()->create();

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Terlambat')
            ->assertSee('Tagihan melewati jatuh tempo');
    }

    public function test_status_lunas_hanya_mengikuti_konfirmasi_rektorat(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        $pengaturan = PengaturanPembayaran::factory()->create();

        Pembayaran::query()->create([
            'user_id' => $mahasiswa->id,
            'periode' => $pengaturan->periode,
            'nominal' => $pengaturan->nominal,
            'status' => 'menunggu_konfirmasi',
            'bukti_pembayaran' => 'pembayaran/bukti.jpg',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Menunggu Konfirmasi')
            ->assertSee('Menunggu konfirmasi Rektorat')
            ->assertDontSee('dikonfirmasi Lunas oleh Rektorat');

        Pembayaran::query()->where('user_id', $mahasiswa->id)->update(['status' => 'lunas']);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tagihan sudah dikonfirmasi Lunas oleh Rektorat. Pengingat dihentikan.');
    }

    public function test_halaman_pembayaran_menampilkan_rekening_resmi_bsi_dan_tiga_angsuran(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        PengaturanPembayaran::factory()->create();

        $this->actingAs($mahasiswa)
            ->get(route('pembayaran'))
            ->assertOk()
            ->assertSee('Rp 5.000.000')
            ->assertSee('Tahap 1')
            ->assertSee('7322022268')
            ->assertSee('RPL 010 BLU UIN P2B MAHAD UTK DR')
            ->assertSee('Salin Nomor Rekening');
    }

    public function test_notifikasi_terbaru_dan_penanda_sudah_dibaca(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        $notifikasi = Notifikasi::kirim(
            $mahasiswa,
            Notifikasi::TIPE_TUGAS_BARU,
            'Tugas baru: Mufradat Pekan Ini',
            'Silakan kerjakan sebelum batas pengumpulan.',
            route('tugas.mhs.index'),
            'uji:tugas-baru'
        );

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Notifikasi Terbaru')
            ->assertSee('Tugas baru: Mufradat Pekan Ini')
            ->assertSee('1 belum dibaca');

        $this->actingAs($mahasiswa)
            ->post(route('notifikasi.baca', $notifikasi))
            ->assertRedirect(route('tugas.mhs.index'));

        $this->assertNotNull($notifikasi->fresh()->dibaca_pada);
    }

    public function test_guru_diarahkan_ke_dashboard_guru(): void
    {
        $guru = $this->guru();

        $this->actingAs($guru)
            ->get(route('dashboard'))
            ->assertRedirect(route('guru.dashboard'));
    }

    /**
     * Mahasiswa beserta kelompok pembelajaran yang tercatat pada absensi.
     *
     * @param  array<string, mixed>  $atribut
     * @return array{0: User, 1: MataPelajaran}
     */
    private function mahasiswa(array $atribut = [], string $kelompok = 'Kelompok 1'): array
    {
        $mapel = MataPelajaran::query()->create([
            'nama' => 'Bahasa Arab Dasar',
            'kode' => 'BA-01',
            'kategori' => 'Bahasa',
        ]);

        $mahasiswa = User::factory()->create(array_merge([
            'name' => 'Siti Aminah',
            'nim' => '2317020111',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ], $atribut));

        $this->absensi($mahasiswa, $mapel, 'hadir', 1, $kelompok);

        return [$mahasiswa, $mapel];
    }

    private function absensi(
        User $mahasiswa,
        MataPelajaran $mapel,
        string $status,
        int $pertemuan,
        string $kelompok = 'Kelompok 1',
    ): void {
        AbsensiPembelajaran::query()->create([
            'user_id' => $mahasiswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => $kelompok,
            'pertemuan_ke' => $pertemuan,
            'tanggal' => now()->subDays($pertemuan)->toDateString(),
            'status' => $status,
        ]);
    }

    private function guru(): User
    {
        return User::factory()->create([
            'name' => 'Ustaz Fauzan',
            'role' => 'guru',
            'status' => 'aktif',
        ]);
    }

    private function tugas(
        User $guru,
        MataPelajaran $mapel,
        string $judul,
        string $kelompok = 'Kelompok 1',
        bool $draft = false,
        ?CarbonInterface $batas = null,
    ): Tugas {
        $factory = $draft
            ? Tugas::factory()->draft()
            : Tugas::factory()->dipublikasikan();

        return $factory->create([
            'user_id' => $guru->id,
            'nama_guru' => $guru->name,
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $mapel->nama,
            'kelompok' => $kelompok,
            'judul' => $judul,
            'batas_pengumpulan' => $batas ?? now()->addDays(3),
        ]);
    }
}
