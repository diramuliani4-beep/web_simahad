<?php

namespace Tests\Feature;

use App\Models\AbsensiPembelajaran;
use App\Models\ActivityLog;
use App\Models\KelompokBelajar;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\PenempatanKelompokBelajar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MusyrifahKelompokBelajarTest extends TestCase
{
    use RefreshDatabase;

    private User $musyrifah;

    private User $mahasiswaDisetujui;

    private KelompokBelajar $kelompok1;

    private KelompokBelajar $kelompok2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->musyrifah = User::factory()->create([
            'name' => 'Musyrifah Asrama',
            'email' => 'musyrifah@mahad.ac.id',
            'role' => 'musyrifah',
        ]);

        $this->mahasiswaDisetujui = $this->mahasiswa(
            'Fatimah Zahra',
            '2317020055',
        );

        $this->kelompok1 = KelompokBelajar::create([
            'nama_kelompok' => 'Kelompok 1',
            'nomor_kelompok' => 1,
            'keterangan' => 'Kelompok Belajar 1',
        ]);

        $this->kelompok2 = KelompokBelajar::create([
            'nama_kelompok' => 'Kelompok 2',
            'nomor_kelompok' => 2,
            'keterangan' => 'Kelompok Belajar 2',
        ]);
    }

    public function test_non_musyrifah_cannot_access_manajemen_pembelajaran(): void
    {
        $response = $this->actingAs($this->mahasiswaDisetujui)
            ->get(route('musyrifah.manajemen_pembelajaran'));

        $response->assertStatus(403);
    }

    public function test_musyrifah_can_view_kelompok_belajar_page_without_tambah_pengajar_button(): void
    {
        $response = $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok']));

        $response->assertStatus(200)
            ->assertDontSee('+ Tambah Pengajar Kelompok')
            ->assertSee('Manajemen Anggota Kelompok Belajar')
            ->assertSee('+ Tambah Mahasiswa ke Kelompok')
            ->assertSee('Cari nama atau NIM...')
            ->assertSee('name="user_ids[]"', false)
            ->assertSee('Kelompok 1')
            ->assertSee('Kelompok 2');
    }

    public function test_halaman_hanya_menawarkan_mahasiswa_disetujui_tanpa_penempatan_aktif(): void
    {
        $anggotaAktif = $this->mahasiswa('Aisyah Rahma', '2317020056');
        $this->tempatkan($anggotaAktif, $this->kelompok2);
        $this->mahasiswa('Calon Mahasiswa', '2317020057', 'pending');

        $response = $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok']));

        $response->assertOk()
            ->assertViewHas('mahasiswaDisetujui', fn ($items): bool => $items->pluck('id')->all() === [$this->mahasiswaDisetujui->id])
            ->assertViewHas('daftarAnggotaKelompok', fn ($items): bool => $items->count() === 1
                && $items->first()->user_id === $anggotaAktif->id
                && $items->first()->kelompok_belajar_id === $this->kelompok2->id)
            ->assertViewHas('daftarKelompok', fn ($items): bool => $items->count() === 2
                && $items->firstWhere('id', $this->kelompok1->id)->penempatan_aktif_count === 0
                && $items->firstWhere('id', $this->kelompok2->id)->penempatan_aktif_count === 1);
    }

    public function test_musyrifah_can_add_approved_student_to_kelompok_belajar(): void
    {
        $response = $this->actingAs($this->musyrifah)
            ->post(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_ids' => [$this->mahasiswaDisetujui->id],
                'kelompok_belajar_id' => $this->kelompok1->id,
                'keterangan' => 'Penempatan awal',
            ]);

        $response->assertRedirect(route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'user_id' => $this->mahasiswaDisetujui->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
            'keterangan' => 'Penempatan awal',
        ]);
        $this->assertDatabaseMissing('kelompok_belajar_anggotas', [
            'user_id' => $this->mahasiswaDisetujui->id,
        ]);
    }

    public function test_musyrifah_can_bulk_add_students_and_return_json_with_audit_records(): void
    {
        $mahasiswaKedua = $this->mahasiswa('Nur Amalina', '2317020056');

        $response = $this->actingAs($this->musyrifah)
            ->postJson(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_ids' => [$this->mahasiswaDisetujui->id, $mahasiswaKedua->id],
                'kelompok_belajar_id' => $this->kelompok1->id,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');

        $this->assertDatabaseCount('penempatan_kelompok_belajars', 2);
        $this->assertSame(2, PenempatanKelompokBelajar::query()
            ->where('kelompok_belajar_id', $this->kelompok1->id)
            ->aktif()
            ->count());
        $this->assertSame(2, ActivityLog::query()
            ->where('aksi', 'anggota_kelompok_ditambahkan')
            ->where('user_id', $this->musyrifah->id)
            ->count());
    }

    public function test_unapproved_student_cannot_be_added_to_kelompok_belajar(): void
    {
        $unapprovedStudent = $this->mahasiswa('Calon Mahasiswa', '2317020057', 'pending');

        $response = $this->actingAs($this->musyrifah)
            ->post(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_ids' => [$unapprovedStudent->id],
                'kelompok_belajar_id' => $this->kelompok1->id,
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('penempatan_kelompok_belajars', [
            'user_id' => $unapprovedStudent->id,
        ]);
    }

    public function test_mixed_batch_with_unapproved_student_is_rejected_atomically(): void
    {
        $unapprovedStudent = $this->mahasiswa('Calon Mahasiswa', '2317020057', 'pending');

        $response = $this->actingAs($this->musyrifah)
            ->postJson(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_ids' => [$this->mahasiswaDisetujui->id, $unapprovedStudent->id],
                'kelompok_belajar_id' => $this->kelompok1->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
        $this->assertDatabaseCount('penempatan_kelompok_belajars', 0);
        $this->assertDatabaseMissing('activity_logs', [
            'aksi' => 'anggota_kelompok_ditambahkan',
        ]);
    }

    public function test_non_mahasiswa_with_approved_registration_cannot_be_added(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $this->createPendaftaran($guru, 'disetujui');

        $response = $this->actingAs($this->musyrifah)
            ->postJson(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_ids' => [$guru->id],
                'kelompok_belajar_id' => $this->kelompok1->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
        $this->assertDatabaseMissing('penempatan_kelompok_belajars', [
            'user_id' => $guru->id,
        ]);
    }

    public function test_duplicate_student_ids_are_rejected_by_validation(): void
    {
        $response = $this->actingAs($this->musyrifah)
            ->postJson(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_ids' => [$this->mahasiswaDisetujui->id, $this->mahasiswaDisetujui->id],
                'kelompok_belajar_id' => $this->kelompok1->id,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('user_ids.0');
        $this->assertDatabaseCount('penempatan_kelompok_belajars', 0);
    }

    public function test_student_cannot_have_more_than_one_active_group(): void
    {
        $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok1);

        $response = $this->actingAs($this->musyrifah)
            ->postJson(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_ids' => [$this->mahasiswaDisetujui->id],
                'kelompok_belajar_id' => $this->kelompok2->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
        $this->assertDatabaseCount('penempatan_kelompok_belajars', 1);
        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'user_id' => $this->mahasiswaDisetujui->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
        ]);
    }

    public function test_inactive_placement_is_reactivated_without_creating_a_duplicate(): void
    {
        $penempatan = $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok1, 'tidak_aktif');

        $response = $this->actingAs($this->musyrifah)
            ->postJson(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_ids' => [$this->mahasiswaDisetujui->id],
                'kelompok_belajar_id' => $this->kelompok2->id,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);
        $this->assertDatabaseCount('penempatan_kelompok_belajars', 1);
        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'id' => $penempatan->id,
            'user_id' => $this->mahasiswaDisetujui->id,
            'kelompok_belajar_id' => $this->kelompok2->id,
            'status' => 'aktif',
            'dicabut_pada' => null,
        ]);
    }

    public function test_musyrifah_can_move_student_to_another_group(): void
    {
        $anggota = $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok1);

        $response = $this->actingAs($this->musyrifah)
            ->putJson(route('musyrifah.manajemen_pembelajaran.anggota.pindah', $anggota->id), [
                'kelompok_belajar_id' => $this->kelompok2->id,
                'keterangan' => 'Pindah jadwal',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $anggota->id)
            ->assertJsonPath('data.kelompok_belajar_id', $this->kelompok2->id);
        $this->assertDatabaseCount('penempatan_kelompok_belajars', 1);
        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'id' => $anggota->id,
            'kelompok_belajar_id' => $this->kelompok2->id,
            'keterangan' => 'Pindah jadwal',
            'status' => 'aktif',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->musyrifah->id,
            'aksi' => 'anggota_kelompok_dipindahkan',
            'objek_id' => $anggota->id,
        ]);
    }

    public function test_moving_student_to_same_group_returns_error(): void
    {
        $anggota = $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok1);

        $response = $this->actingAs($this->musyrifah)
            ->putJson(route('musyrifah.manajemen_pembelajaran.anggota.pindah', $anggota->id), [
                'kelompok_belajar_id' => $this->kelompok1->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'id' => $anggota->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
        ]);
        $this->assertDatabaseMissing('activity_logs', [
            'aksi' => 'anggota_kelompok_dipindahkan',
        ]);
    }

    public function test_inactive_placement_cannot_be_moved_or_removed_again(): void
    {
        $anggota = $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok1, 'tidak_aktif');

        $this->actingAs($this->musyrifah)
            ->putJson(route('musyrifah.manajemen_pembelajaran.anggota.pindah', $anggota->id), [
                'kelompok_belajar_id' => $this->kelompok2->id,
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->actingAs($this->musyrifah)
            ->deleteJson(route('musyrifah.manajemen_pembelajaran.anggota.destroy', $anggota->id))
            ->assertNotFound();

        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'id' => $anggota->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'tidak_aktif',
        ]);
    }

    public function test_musyrifah_can_remove_student_without_deleting_placement_history(): void
    {
        $anggota = $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok1);

        $response = $this->actingAs($this->musyrifah)
            ->deleteJson(route('musyrifah.manajemen_pembelajaran.anggota.destroy', $anggota->id));

        $response->assertOk()
            ->assertJsonPath('success', true);
        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'id' => $anggota->id,
            'user_id' => $this->mahasiswaDisetujui->id,
            'status' => 'tidak_aktif',
        ]);
        $this->assertNotNull($anggota->fresh()->dicabut_pada);
        $this->assertNull($this->mahasiswaDisetujui->fresh()->penempatanKelompokBelajarAktif);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->musyrifah->id,
            'aksi' => 'anggota_kelompok_dikeluarkan',
            'objek_id' => $anggota->id,
        ]);
    }

    public function test_mahasiswa_cannot_manage_memberships_or_view_graduation_detail(): void
    {
        $anggota = $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok1);

        $this->actingAs($this->mahasiswaDisetujui)
            ->postJson(route('musyrifah.manajemen_pembelajaran.anggota.store'), [
                'user_ids' => [$this->mahasiswaDisetujui->id],
                'kelompok_belajar_id' => $this->kelompok2->id,
            ])
            ->assertForbidden();

        $this->actingAs($this->mahasiswaDisetujui)
            ->putJson(route('musyrifah.manajemen_pembelajaran.anggota.pindah', $anggota->id), [
                'kelompok_belajar_id' => $this->kelompok2->id,
            ])
            ->assertForbidden();

        $this->actingAs($this->mahasiswaDisetujui)
            ->deleteJson(route('musyrifah.manajemen_pembelajaran.anggota.destroy', $anggota->id))
            ->assertForbidden();

        $this->actingAs($this->mahasiswaDisetujui)
            ->getJson(route('musyrifah.manajemen_pembelajaran.detail_kelulusan', $this->mahasiswaDisetujui->id))
            ->assertForbidden();

        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'id' => $anggota->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
        ]);
    }

    public function test_placement_cannot_be_changed_after_registration_is_no_longer_approved(): void
    {
        $anggota = $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok1);
        Pendaftaran::query()
            ->where('user_id', $this->mahasiswaDisetujui->id)
            ->update(['status' => 'ditolak']);

        $this->actingAs($this->musyrifah)
            ->putJson(route('musyrifah.manajemen_pembelajaran.anggota.pindah', $anggota->id), [
                'kelompok_belajar_id' => $this->kelompok2->id,
            ])
            ->assertNotFound();

        $this->actingAs($this->musyrifah)
            ->deleteJson(route('musyrifah.manajemen_pembelajaran.anggota.destroy', $anggota->id))
            ->assertNotFound();

        $this->assertDatabaseHas('penempatan_kelompok_belajars', [
            'id' => $anggota->id,
            'kelompok_belajar_id' => $this->kelompok1->id,
            'status' => 'aktif',
        ]);
    }

    public function test_graduation_summary_uses_active_global_group_without_fallback(): void
    {
        $mapel = MataPelajaran::query()->create([
            'nama' => 'Bahasa Arab Dasar',
            'kode' => 'BA-01',
            'kategori' => 'Bahasa',
        ]);
        AbsensiPembelajaran::query()->create([
            'user_id' => $this->mahasiswaDisetujui->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => 'Kelompok 1',
            'pertemuan_ke' => 1,
            'tanggal' => now()->toDateString(),
            'status' => 'hadir',
        ]);

        $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.manajemen_pembelajaran'))
            ->assertOk()
            ->assertViewHas('dataKelulusan', fn (array $data): bool => collect($data['daftar'])
                ->firstWhere('id', $this->mahasiswaDisetujui->id)['kelompok'] === '-');

        $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok2);

        $this->actingAs($this->musyrifah)
            ->get(route('musyrifah.manajemen_pembelajaran'))
            ->assertOk()
            ->assertViewHas('dataKelulusan', fn (array $data): bool => collect($data['daftar'])
                ->firstWhere('id', $this->mahasiswaDisetujui->id)['kelompok'] === 'Kelompok 2');
    }

    public function test_schedule_notifications_only_reach_approved_students_in_active_group(): void
    {
        $mapel = MataPelajaran::query()->create([
            'nama' => 'Bahasa Arab Dasar',
            'kode' => 'BA-01',
            'kategori' => 'Bahasa',
        ]);
        $this->tempatkan($this->mahasiswaDisetujui, $this->kelompok1);
        $this->assertSame(1, User::mahasiswaKelompok($this->kelompok1->nama_kelompok)->count());
        $tanpaKelompok = $this->mahasiswa('Nur Amalina', '2317020056');
        $this->mahasiswa('Calon Mahasiswa', '2317020057', 'pending');

        $this->actingAs($this->musyrifah)
            ->post(route('musyrifah.manajemen_pembelajaran.jadwal.store'), [
                'nama_mata_pelajaran' => $mapel->nama,
                'kelompok' => $this->kelompok1->nama_kelompok,
                'guru_pengampu' => 'Ustaz Fauzan',
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'ruangan' => 'Ruang 1',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('musyrifah.manajemen_pembelajaran', ['tab' => 'jadwal']));

        $this->assertDatabaseHas('jadwal_pembelajarans', [
            'mata_pelajaran_id' => $mapel->id,
            'kelompok' => $this->kelompok1->nama_kelompok,
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $this->mahasiswaDisetujui->id,
            'tipe' => Notifikasi::TIPE_JADWAL_BERUBAH,
        ]);
        $this->assertDatabaseMissing('notifikasis', [
            'user_id' => $tanpaKelompok->id,
            'tipe' => Notifikasi::TIPE_JADWAL_BERUBAH,
        ]);
        $this->assertSame(1, Notifikasi::query()->where('tipe', Notifikasi::TIPE_JADWAL_BERUBAH)->count());
    }

    private function mahasiswa(string $nama, string $nim, string $status = 'disetujui'): User
    {
        $user = User::factory()->create([
            'name' => $nama,
            'role' => 'mahasiswa',
            'nim' => $nim,
        ]);

        $this->createPendaftaran($user, $status);

        return $user;
    }

    private function createPendaftaran(User $user, string $status = 'disetujui'): Pendaftaran
    {
        return Pendaftaran::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'nim' => $user->nim ?: 'NIM-'.$user->id,
            'tempat_lahir' => 'Padang',
            'tanggal_lahir' => '2005-01-01',
            'semester' => 1,
            'no_hp' => '081234567890',
            'no_wa' => '081234567890',
            'jenis_kelamin' => 'Perempuan',
            'asal_sekolah' => 'MAN 1 Padang',
            'status' => $status,
            'fakultas' => 'Sains dan Teknologi',
            'program_studi' => 'Sistem Informasi',
            'tahun_angkatan' => '2024',
            'kategori' => 'umum',
        ]);
    }

    private function tempatkan(
        User $mahasiswa,
        KelompokBelajar $kelompok,
        string $status = 'aktif',
    ): PenempatanKelompokBelajar {
        return PenempatanKelompokBelajar::query()->updateOrCreate(
            ['user_id' => $mahasiswa->id],
            [
                'kelompok_belajar_id' => $kelompok->id,
                'status' => $status,
                'keterangan' => 'Penempatan uji',
                'ditugaskan_pada' => now(),
                'dicabut_pada' => $status === 'aktif' ? null : now(),
            ],
        );
    }
}
