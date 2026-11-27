<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\Kamar;
use App\Models\KelompokBelajar;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\PenempatanKelompokBelajar;
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

        [$mahasiswa] = $this->mahasiswa([
            'kamar_id' => $kamar->id,
            'gedung_penempatan' => 'Gedung Lama',
            'lantai_penempatan' => 9,
        ]);

        Pendaftaran::query()
            ->where('user_id', $mahasiswa->id)
            ->firstOrFail()
            ->update([
                'tanggal_lahir' => '2005-04-12',
                'fakultas' => 'Tarbiyah dan Keguruan',
                'semester' => 4,
                'program_studi' => 'Pendidikan Agama Islam',
            ]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan Mahasiswa')
            ->assertSee($mahasiswa->name)
            ->assertSee('Kelompok 1')
            ->assertSee('Kamar 214')
            ->assertSee('Lantai 2')
            ->assertDontSee('Gedung Lama')
            ->assertDontSee('Lantai 9')
            ->assertSee('Disetujui')
            ->assertSee('Pendidikan Agama Islam');
    }

    public function test_akun_baru_tanpa_penempatan_melihat_kondisi_kosong(): void
    {
        $mahasiswa = User::factory()->create([
            'name' => 'Mahasiswa Baru',
            'role' => 'mahasiswa',
            'status' => 'aktif',
            'gedung_penempatan' => 'Gedung Lama',
            'lantai_penempatan' => 9,
        ]);
        $mapel = $this->mapel('Mata Pelajaran Tidak Ditugaskan', 'TL-01');
        $guru = $this->guru();

        $this->jadwal($mapel, 'Kelompok 1', 'Ruang Tidak Ditugaskan');
        $this->tugas($guru, $mapel, 'Tugas Tidak Ditugaskan');
        $this->absensi($mahasiswa, $mapel, 'hadir', 1);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard', [
                'bulan' => now()->format('Y-m'),
                'tanggal' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Belum memiliki kelompok belajar')
            ->assertSee('Belum Mendapatkan Kamar')
            ->assertSee('Belum Mendaftar')
            ->assertSee('Aktif')
            ->assertDontSee('Mata Pelajaran Tidak Ditugaskan')
            ->assertDontSee('Ruang Tidak Ditugaskan')
            ->assertDontSee('Tugas Tidak Ditugaskan')
            ->assertDontSee('Total pertemuan: 1 kali')
            ->assertDontSee('Gedung Lama')
            ->assertDontSee('Lantai 9');
    }

    public function test_keanggotaan_nonaktif_tidak_menampilkan_data_kelompok(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswa();
        $guru = $this->guru();

        $mahasiswa->riwayatPenempatanKelompokBelajar()
            ->firstOrFail()
            ->update(['status' => 'nonaktif', 'dicabut_pada' => now()]);
        $this->jadwal($mapel, 'Kelompok 1', 'Ruang Nonaktif');
        $this->tugas($guru, $mapel, 'Tugas Nonaktif');

        $this->actingAs($mahasiswa)
            ->get(route('dashboard', [
                'bulan' => now()->format('Y-m'),
                'tanggal' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Belum memiliki kelompok belajar')
            ->assertDontSee('Ruang Nonaktif')
            ->assertDontSee('Tugas Nonaktif')
            ->assertDontSee('Total pertemuan: 1 kali');
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

    public function test_jadwal_dan_tugas_mengikuti_kelompok_global_saat_mahasiswa_dipindahkan(): void
    {
        [$mahasiswa, $mapelArab] = $this->mahasiswa();
        $mapelFiqih = $this->mapel('Fiqih Ibadah', 'FI-02');
        $guru = $this->guru();
        $hariIni = JadwalPembelajaran::HARI[now()->dayOfWeekIso];
        $kelompok2 = $this->kelompok(2);

        $this->jadwal($mapelArab, 'Kelompok 1', 'Ruang Arab Satu', $hariIni, 'Jadwal Arab Satu');
        $this->jadwal($mapelArab, 'Kelompok 2', 'Ruang Arab Dua', $hariIni, 'Jadwal Arab Dua');
        $this->jadwal($mapelFiqih, 'Kelompok 2', 'Ruang Fiqih Dua', $hariIni, 'Jadwal Fiqih Dua');
        $this->jadwal($mapelFiqih, 'Kelompok 1', 'Ruang Fiqih Satu', $hariIni, 'Jadwal Fiqih Satu');
        $this->tugas($guru, $mapelArab, 'Tugas Arab Kelompok Satu');
        $this->tugas($guru, $mapelArab, 'Tugas Arab Kelompok Dua', kelompok: 'Kelompok 2');
        $this->tugas($guru, $mapelFiqih, 'Tugas Fiqih Kelompok Dua', kelompok: 'Kelompok 2');
        $this->tugas($guru, $mapelFiqih, 'Tugas Fiqih Kelompok Satu');

        $this->actingAs($mahasiswa)
            ->get(route('dashboard', [
                'bulan' => now()->format('Y-m'),
                'tanggal' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertViewHas('kelompok', 'Kelompok 1')
            ->assertSee('Jadwal Arab Satu')
            ->assertSee('Jadwal Fiqih Satu')
            ->assertSee('Tugas Arab Kelompok Satu')
            ->assertSee('Tugas Fiqih Kelompok Satu')
            ->assertDontSee('Jadwal Arab Dua')
            ->assertDontSee('Jadwal Fiqih Dua')
            ->assertDontSee('Tugas Arab Kelompok Dua')
            ->assertDontSee('Tugas Fiqih Kelompok Dua');

        $mahasiswa->riwayatPenempatanKelompokBelajar()->firstOrFail()->update([
            'kelompok_belajar_id' => $kelompok2->id,
        ]);
        $mahasiswa->unsetRelation('penempatanKelompokBelajarAktif');

        $this->actingAs($mahasiswa)
            ->get(route('dashboard', [
                'bulan' => now()->format('Y-m'),
                'tanggal' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertViewHas('kelompok', 'Kelompok 2')
            ->assertSee('Jadwal Arab Dua')
            ->assertSee('Jadwal Fiqih Dua')
            ->assertSee('Tugas Arab Kelompok Dua')
            ->assertSee('Tugas Fiqih Kelompok Dua')
            ->assertDontSee('Jadwal Arab Satu')
            ->assertDontSee('Jadwal Fiqih Satu')
            ->assertDontSee('Tugas Arab Kelompok Satu')
            ->assertDontSee('Tugas Fiqih Kelompok Satu');
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

    public function test_dashboard_hanya_menampilkan_data_mahasiswa_yang_login(): void
    {
        [$mahasiswa, $mapel] = $this->mahasiswa();
        $mahasiswaLain = User::factory()->create([
            'name' => 'Mahasiswa Lain',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ]);
        $guru = $this->guru();

        $this->tempatkan($mahasiswaLain, $this->kelompok(1));
        $this->absensi($mahasiswaLain, $mapel, 'alpa', 2);

        $tugasLain = $this->tugas($guru, $mapel, 'Tugas Yang Dinilai Mahasiswa Lain');
        PengumpulanTugas::factory()->nilaiDipublikasikan(99, 'Nilai mahasiswa lain.')->create([
            'tugas_id' => $tugasLain->id,
            'user_id' => $mahasiswaLain->id,
        ]);

        $pengaturan = PengaturanPembayaran::factory()->create();
        $this->tagihan($mahasiswa, $pengaturan, ['nominal' => 1500000]);
        $this->tagihan($mahasiswaLain, $pengaturan, [
            'nominal' => 4200000,
            'status' => 'lunas',
        ]);

        SuratPerizinan::factory()->create(['user_id' => $mahasiswa->id]);
        SuratPerizinan::factory()->create(['user_id' => $mahasiswaLain->id]);
        Notifikasi::kirim(
            $mahasiswaLain,
            Notifikasi::TIPE_TUGAS_BARU,
            'Notifikasi Milik Mahasiswa Lain',
            'Pesan yang tidak boleh muncul.',
            route('tugas.mhs.index'),
            'uji:mahasiswa-lain'
        );

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total pertemuan: 1 kali')
            ->assertSee('Rp 1.500.000')
            ->assertSee('Total 1 pengajuan surat dan perizinan.')
            ->assertDontSee('Rp 4.200.000')
            ->assertDontSee('99</span>/100', false)
            ->assertDontSee('Total 2 pengajuan surat dan perizinan.')
            ->assertDontSee('Notifikasi Milik Mahasiswa Lain');
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

        $pengaturan = PengaturanPembayaran::factory()->mendekatiJatuhTempo()->create();
        $jatuhTempoTagihan = now()->addDays(5);
        $this->tagihan($mahasiswa, $pengaturan, [
            'angsuran_ke' => 2,
            'nominal' => 1750000,
            'jatuh_tempo' => $jatuhTempoTagihan->toDateString(),
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 1.750.000')
            ->assertSee('Tahap 2')
            ->assertSee('7322022268')
            ->assertSee('Salin No. Rek')
            ->assertSee('Pengingat: jatuh tempo tinggal 5 hari')
            ->assertSee($jatuhTempoTagihan->locale('id')->translatedFormat('d F Y'))
            ->assertDontSee('Rp 2.500.000');

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_PEMBAYARAN,
            'pesan' => 'Tagihan Tahap 2 sebesar Rp 1.750.000 jatuh tempo pada '.$jatuhTempoTagihan->locale('id')->translatedFormat('d F Y').'.',
        ]);
    }

    public function test_pengingat_tidak_ditampilkan_jika_jatuh_tempo_tagihan_melewihi_tujuh_hari(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        $pengaturan = PengaturanPembayaran::factory()->mendekatiJatuhTempo()->create();
        $jatuhTempoTagihan = now()->addDays(10);
        $this->tagihan($mahasiswa, $pengaturan, [
            'jatuh_tempo' => $jatuhTempoTagihan->toDateString(),
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tagihan belum dibayar. Bayar sebelum '.$jatuhTempoTagihan->locale('id')->translatedFormat('d F Y').'.')
            ->assertDontSee('Pengingat: jatuh tempo');

        $this->assertDatabaseMissing('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_PEMBAYARAN,
        ]);
    }

    public function test_dashboard_menampilkan_status_terlambat_saat_jatuh_tempo_terlewati(): void
    {
        [$mahasiswa] = $this->mahasiswa();

        $pengaturan = PengaturanPembayaran::factory()->terlewat()->create();
        $this->tagihan($mahasiswa, $pengaturan);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Terlambat')
            ->assertSee('Tagihan melewati jatuh tempo');
    }

    public function test_dashboard_tidak_membuat_tagihan_default_untuk_mahasiswa_tanpa_tagihan(): void
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'status' => 'aktif']);
        $mahasiswaLain = User::factory()->create(['role' => 'mahasiswa', 'status' => 'aktif']);
        $pengaturan = PengaturanPembayaran::factory()->mendekatiJatuhTempo()->create();

        $this->tagihan($mahasiswaLain, $pengaturan, ['nominal' => 4200000]);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum memiliki tagihan pembayaran untuk periode ini.')
            ->assertDontSee('Rp 2.500.000')
            ->assertDontSee('Rp 4.200.000')
            ->assertDontSee('7322022268')
            ->assertDontSee('Pengingat: jatuh tempo');

        $this->assertDatabaseCount('pembayarans', 1);
        $this->assertDatabaseMissing('notifikasis', [
            'user_id' => $mahasiswa->id,
            'tipe' => Notifikasi::TIPE_PEMBAYARAN,
        ]);
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
     * Mahasiswa dengan penempatan kelompok aktif dan satu catatan absensi.
     *
     * @param  array<string, mixed>  $atribut
     * @return array{0: User, 1: MataPelajaran}
     */
    private function mahasiswa(array $atribut = [], int $nomorKelompok = 1): array
    {
        $mapel = $this->mapel();

        $mahasiswa = User::factory()->create(array_merge([
            'name' => 'Siti Aminah',
            'nim' => '2317020111',
            'role' => 'mahasiswa',
            'status' => 'aktif',
        ], $atribut));

        $this->tempatkan($mahasiswa, $this->kelompok($nomorKelompok));
        $this->absensi($mahasiswa, $mapel, 'hadir', 1, 'Kelompok '.$nomorKelompok);

        return [$mahasiswa, $mapel];
    }

    private function mapel(string $nama = 'Bahasa Arab Dasar', string $kode = 'BA-01'): MataPelajaran
    {
        return MataPelajaran::query()->create([
            'nama' => $nama,
            'kode' => $kode,
            'kategori' => 'Bahasa',
        ]);
    }

    private function kelompok(int $nomor): KelompokBelajar
    {
        return KelompokBelajar::firstOrCreate(
            ['nomor_kelompok' => $nomor],
            ['nama_kelompok' => 'Kelompok '.$nomor]
        );
    }

    private function tempatkan(
        User $mahasiswa,
        KelompokBelajar $kelompok,
        string $status = 'aktif',
    ): PenempatanKelompokBelajar {
        Pendaftaran::query()->updateOrCreate(
            ['user_id' => $mahasiswa->id],
            [
                'nama_lengkap' => $mahasiswa->name,
                'nim' => $mahasiswa->nim ?: 'NIM-'.$mahasiswa->id,
                'tempat_lahir' => 'Padang',
                'tanggal_lahir' => '2005-01-01',
                'jenis_kelamin' => 'Perempuan',
                'no_hp' => '081234567890',
                'program_studi' => 'Sistem Informasi',
                'fakultas' => 'Sains dan Teknologi',
                'semester' => 1,
                'status' => 'disetujui',
                'tahun_angkatan' => '2024',
            ],
        );

        return PenempatanKelompokBelajar::query()->updateOrCreate(
            ['user_id' => $mahasiswa->id],
            [
                'kelompok_belajar_id' => $kelompok->id,
                'status' => $status,
                'ditugaskan_pada' => now(),
                'dicabut_pada' => $status === 'aktif' ? null : now(),
            ],
        );
    }

    private function jadwal(
        MataPelajaran $mapel,
        string $kelompok,
        string $ruangan,
        ?string $hari = null,
        ?string $namaMataPelajaran = null,
    ): JadwalPembelajaran {
        return JadwalPembelajaran::query()->create([
            'mata_pelajaran_id' => $mapel->id,
            'nama_mata_pelajaran' => $namaMataPelajaran ?: $mapel->nama,
            'kelompok' => $kelompok,
            'guru_pengampu' => 'Ustaz Fauzan',
            'hari' => $hari ?? JadwalPembelajaran::HARI[now()->dayOfWeekIso],
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'ruangan' => $ruangan,
        ]);
    }

    /**
     * @param  array<string, mixed>  $atribut
     */
    private function tagihan(
        User $mahasiswa,
        PengaturanPembayaran $pengaturan,
        array $atribut = [],
    ): Pembayaran {
        return Pembayaran::query()->create(array_merge([
            'user_id' => $mahasiswa->id,
            'periode' => $pengaturan->periode,
            'nominal' => $pengaturan->nominal,
            'jatuh_tempo' => $pengaturan->jatuh_tempo?->toDateString(),
            'status' => 'belum_lunas',
        ], $atribut));
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
