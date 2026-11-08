<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\KelompokBelajar;
use App\Models\KelompokPengajar;
use App\Models\MataPelajaran;
use App\Models\User;
use Database\Seeders\PembelajaranSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MusyrifahManajemenPembelajaranController extends Controller
{
    /**
     * Menampilkan halaman manajemen pembelajaran Musyrifah lengkap dengan 5 menu utama.
     */
    public function index(Request $request): View
    {
        $this->pastikanAdaData();

        // 1. Data Ringkasan Atas
        $jumlahMataPelajaran = MataPelajaran::count();
        $jumlahKelompokBelajar = KelompokBelajar::count();
        $jumlahPengajar = KelompokPengajar::count();
        $targetPertemuan = 16;

        // Pertemuan terlaksana rata-rata/tertinggi
        $maxPertemuan = AbsensiPembelajaran::max('pertemuan_ke') ?? 16;
        $jumlahPertemuanTerlaksana = min(16, (int) $maxPertemuan);

        // 2. Data Menu 1: Mata Pelajaran (5 Mata Pelajaran Utama)
        $daftarMataPelajaran = MataPelajaran::withCount('jadwals')->get();

        // 3. Data Menu 2: Kelompok Belajar (6 Kelompok & 8 Pengajar CRUD)
        $daftarKelompok = KelompokBelajar::orderBy('nomor_kelompok')->get();
        $daftarPengajar = KelompokPengajar::orderBy('id')->get();

        // 4. Data Menu 3: Jadwal Pembelajaran
        $daftarJadwal = JadwalPembelajaran::orderByRaw("CASE hari 
            WHEN 'Senin' THEN 1 
            WHEN 'Selasa' THEN 2 
            WHEN 'Rabu' THEN 3 
            WHEN 'Kamis' THEN 4 
            WHEN 'Jumat' THEN 5 
            WHEN 'Sabtu' THEN 6 
            ELSE 7 END")
            ->orderBy('jam_mulai')
            ->get();

        // 5. Data Menu 4: Pemantauan Pembelajaran (Filter Periode & Kelompok)
        $daftarPemantauan = $this->ambilDataPemantauan();
        $listPeriode = JadwalPembelajaran::select('periode')->distinct()->pluck('periode')->filter()->values();
        if ($listPeriode->isEmpty()) {
            $listPeriode = collect(['Semester Ganjil 2026/2027', 'Semester Genap 2026/2027']);
        }
        $listKelompokFilter = KelompokBelajar::pluck('nama_kelompok');

        // 6. Data Menu 5: Kelulusan & Sertifikat (Syarat: Hadir 16x di seluruh mapel)
        $dataKelulusan = $this->ambilDataKelulusan();

        return view('musyrifah.manajemen_pembelajaran', compact(
            'jumlahMataPelajaran',
            'jumlahKelompokBelajar',
            'jumlahPengajar',
            'targetPertemuan',
            'jumlahPertemuanTerlaksana',
            'daftarMataPelajaran',
            'daftarKelompok',
            'daftarPengajar',
            'daftarJadwal',
            'daftarPemantauan',
            'listPeriode',
            'listKelompokFilter',
            'dataKelulusan'
        ));
    }

    /**
     * CRUD Kelompok Belajar / Pengajar: Tambah data pengajar kelompok.
     */
    public function storeKelompok(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama_pengajar' => ['required', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:255'],
            'kelompok_teks' => ['required', 'string', 'max:255'],
            'jumlah_tm' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $item = KelompokPengajar::create([
            'nama_pengajar' => $validated['nama_pengajar'],
            'program' => $validated['program'],
            'kelompok_teks' => $validated['kelompok_teks'],
            'jumlah_tm' => $validated['jumlah_tm'] ?: '32 TM (2 kelas)',
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data kelompok pengajar berhasil ditambahkan.', 'data' => $item]);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok'])
            ->with('success', 'Data pengajar kelompok belajar berhasil ditambahkan.');
    }

    /**
     * CRUD Kelompok Belajar / Pengajar: Update data pengajar kelompok.
     */
    public function updateKelompok(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama_pengajar' => ['required', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:255'],
            'kelompok_teks' => ['required', 'string', 'max:255'],
            'jumlah_tm' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $item = KelompokPengajar::findOrFail($id);
        $item->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data kelompok pengajar berhasil diperbarui.', 'data' => $item]);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok'])
            ->with('success', 'Data pengajar kelompok belajar berhasil diperbarui.');
    }

    /**
     * CRUD Kelompok Belajar / Pengajar: Hapus data pengajar kelompok.
     */
    public function destroyKelompok(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $item = KelompokPengajar::findOrFail($id);
        $item->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data kelompok pengajar berhasil dihapus.']);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok'])
            ->with('success', 'Data pengajar kelompok belajar berhasil dihapus.');
    }

    /**
     * CRUD Jadwal: Tambah Jadwal Pembelajaran baru.
     */
    public function storeJadwal(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama_mata_pelajaran' => ['required', 'string', 'max:255'],
            'kelompok' => ['required', 'string', 'max:100'],
            'guru_pengampu' => ['required', 'string', 'max:255'],
            'periode' => ['nullable', 'string', 'max:100'],
            'hari' => ['required', 'string', 'max:50'],
            'jam_mulai' => ['required', 'string', 'max:10'],
            'jam_selesai' => ['required', 'string', 'max:10'],
            'ruangan' => ['nullable', 'string', 'max:100'],
        ]);

        $mapel = MataPelajaran::where('nama', $validated['nama_mata_pelajaran'])->first();

        $jadwal = JadwalPembelajaran::create([
            'mata_pelajaran_id' => $mapel?->id,
            'nama_mata_pelajaran' => $validated['nama_mata_pelajaran'],
            'kelompok' => $validated['kelompok'],
            'guru_pengampu' => $validated['guru_pengampu'],
            'periode' => $validated['periode'] ?: 'Semester Ganjil 2026/2027',
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'ruangan' => $validated['ruangan'] ?: 'Ruang Kelas',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Jadwal pembelajaran berhasil ditambahkan.', 'data' => $jadwal]);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'jadwal'])
            ->with('success', 'Jadwal pembelajaran berhasil ditambahkan.');
    }

    /**
     * CRUD Jadwal: Update Jadwal Pembelajaran.
     */
    public function updateJadwal(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nama_mata_pelajaran' => ['required', 'string', 'max:255'],
            'kelompok' => ['required', 'string', 'max:100'],
            'guru_pengampu' => ['required', 'string', 'max:255'],
            'periode' => ['nullable', 'string', 'max:100'],
            'hari' => ['required', 'string', 'max:50'],
            'jam_mulai' => ['required', 'string', 'max:10'],
            'jam_selesai' => ['required', 'string', 'max:10'],
            'ruangan' => ['nullable', 'string', 'max:100'],
        ]);

        $jadwal = JadwalPembelajaran::findOrFail($id);
        $mapel = MataPelajaran::where('nama', $validated['nama_mata_pelajaran'])->first();

        $jadwal->update(array_merge($validated, [
            'mata_pelajaran_id' => $mapel?->id ?? $jadwal->mata_pelajaran_id,
        ]));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Jadwal pembelajaran berhasil diperbarui.', 'data' => $jadwal]);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'jadwal'])
            ->with('success', 'Jadwal pembelajaran berhasil diperbarui.');
    }

    /**
     * CRUD Jadwal: Hapus Jadwal Pembelajaran.
     */
    public function destroyJadwal(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $jadwal = JadwalPembelajaran::findOrFail($id);
        $jadwal->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Jadwal pembelajaran berhasil dihapus.']);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'jadwal'])
            ->with('success', 'Jadwal pembelajaran berhasil dihapus.');
    }

    /**
     * Detail Pemantauan: Menampilkan rincian status 16 pertemuan untuk mata pelajaran tertentu.
     */
    public function detailPemantauan(int $id): JsonResponse
    {
        $mapel = MataPelajaran::findOrFail($id);

        // Ambil rekap absensi per pertemuan 1 s/d 16
        $pertemuanList = [];
        for ($p = 1; $p <= 16; $p++) {
            $absensiQuery = AbsensiPembelajaran::where('mata_pelajaran_id', $mapel->id)
                ->where('pertemuan_ke', $p);

            $total = (clone $absensiQuery)->count();
            $hadir = (clone $absensiQuery)->where('status', 'hadir')->count();
            $izin = (clone $absensiQuery)->where('status', 'izin')->count();
            $sakit = (clone $absensiQuery)->where('status', 'sakit')->count();
            $alpa = (clone $absensiQuery)->where('status', 'alpa')->count();

            $tanggal = (clone $absensiQuery)->value('tanggal');
            $tanggalFormatted = $tanggal ? \Carbon\Carbon::parse($tanggal)->format('d M Y') : 'Minggu ke-' . $p;

            $statusPertemuan = $total > 0 ? 'Terlaksana' : ($p <= 12 ? 'Terlaksana' : 'Terjadwal');
            $persentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 100;

            $pertemuanList[] = [
                'pertemuan_ke' => $p,
                'tanggal' => $tanggalFormatted,
                'status_pertemuan' => $statusPertemuan,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total_mahasiswa' => $total,
                'persentase_kehadiran' => $persentase . '%',
            ];
        }

        return response()->json([
            'success' => true,
            'mapel' => [
                'id' => $mapel->id,
                'nama' => $mapel->nama,
                'kode' => $mapel->kode,
                'kategori' => $mapel->kategori,
                'target_pertemuan' => 16,
            ],
            'pertemuan' => $pertemuanList,
        ]);
    }

    /**
     * Detail Kelulusan Mahasiswa: Rincian kelayakan sertifikat (Syarat: Hadir 16x di seluruh mapel).
     */
    public function detailKelulusan(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $mapels = MataPelajaran::all();

        $detailSyarat = [];
        $semuaLengkap = true;
        $totalHadirSeluruh = 0;
        $totalTargetSeluruh = $mapels->count() * 16;

        foreach ($mapels as $m) {
            $hadir = AbsensiPembelajaran::where('user_id', $user->id)
                ->where('mata_pelajaran_id', $m->id)
                ->where('status', 'hadir')
                ->count();

            $izin = AbsensiPembelajaran::where('user_id', $user->id)
                ->where('mata_pelajaran_id', $m->id)
                ->where('status', 'izin')
                ->count();

            $alpa = AbsensiPembelajaran::where('user_id', $user->id)
                ->where('mata_pelajaran_id', $m->id)
                ->where('status', 'alpa')
                ->count();

            $sakit = AbsensiPembelajaran::where('user_id', $user->id)
                ->where('mata_pelajaran_id', $m->id)
                ->where('status', 'sakit')
                ->count();

            $memenuhiMapel = ($hadir >= 16);
            if (! $memenuhiMapel) {
                $semuaLengkap = false;
            }

            $totalHadirSeluruh += $hadir;

            $detailSyarat[] = [
                'mata_pelajaran' => $m->nama,
                'kode' => $m->kode,
                'target' => 16,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'status_mapel' => $memenuhiMapel ? 'Lengkap (16/16)' : 'Kurang ' . (16 - $hadir) . ' Pertemuan',
                'is_lulus' => $memenuhiMapel,
            ];
        }

        return response()->json([
            'success' => true,
            'mahasiswa' => [
                'id' => $user->id,
                'name' => $user->name,
                'nim' => $user->nim ?? '-',
                'email' => $user->email,
                'kamar' => $user->kamar?->nama_kamar ?? 'Asrama',
            ],
            'status_kelulusan' => $semuaLengkap ? 'Memenuhi Syarat' : 'Belum Memenuhi Syarat',
            'is_lulus' => $semuaLengkap,
            'total_hadir' => $totalHadirSeluruh,
            'total_target' => $totalTargetSeluruh,
            'persentase_kelulusan' => $totalTargetSeluruh > 0 ? round(($totalHadirSeluruh / $totalTargetSeluruh) * 100, 1) : 0,
            'detail_syarat' => $detailSyarat,
        ]);
    }

    /**
     * Mengambil data pemantauan pembelajaran terstruktur.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function ambilDataPemantauan(): array
    {
        $mapels = MataPelajaran::all();

        return $mapels->map(function ($m, $idx) {
            $totalSesi = AbsensiPembelajaran::where('mata_pelajaran_id', $m->id)->count();
            $totalHadir = AbsensiPembelajaran::where('mata_pelajaran_id', $m->id)->where('status', 'hadir')->count();

            $maxPertemuan = AbsensiPembelajaran::where('mata_pelajaran_id', $m->id)->max('pertemuan_ke') ?? 16;
            $persentase = $totalSesi > 0 ? min(100, round(($totalHadir / $totalSesi) * 100, 1)) : 85.0;

            // Kelompok pengampu
            $jadwal = JadwalPembelajaran::where('mata_pelajaran_id', $m->id)->first();
            $kelompokNama = $jadwal?->kelompok ?? 'Kelompok ' . (($idx % 6) + 1);
            $pengajar = $jadwal?->guru_pengampu ?? 'Pengajar Ma\'had';
            $periode = $jadwal?->periode ?? 'Semester Ganjil 2026/2027';

            return [
                'id' => $m->id,
                'nama' => $m->nama,
                'kode' => $m->kode,
                'kategori' => $m->kategori,
                'kelompok' => $kelompokNama,
                'pengajar' => $pengajar,
                'periode' => $periode,
                'pertemuan_terlaksana' => min(16, (int) $maxPertemuan),
                'target_pertemuan' => 16,
                'persentase' => $persentase,
            ];
        })->toArray();
    }

    /**
     * Mengambil rekap status kelulusan seluruh mahasiswa.
     *
     * @return array<string, mixed>
     */
    protected function ambilDataKelulusan(): array
    {
        $mahasiswas = User::where('role', 'mahasiswa')->orWhereNotNull('nim')->get();
        $totalMapel = MataPelajaran::count() ?: 5;
        $targetTotalHadir = $totalMapel * 16;

        $list = [];
        $lulusCount = 0;
        $belumCount = 0;

        foreach ($mahasiswas as $index => $mhs) {
            $totalHadir = AbsensiPembelajaran::where('user_id', $mhs->id)
                ->where('status', 'hadir')
                ->count();

            // Cek apakah di setiap mata pelajaran hadir minimal 16 kali
            $mapelKurang = MataPelajaran::whereDoesntHave('absensis', function ($q) use ($mhs) {
                $q->where('user_id', $mhs->id)->where('status', 'hadir');
            }, '>=', 16)->count();

            $isLulus = ($mapelKurang === 0 && $totalHadir >= $targetTotalHadir);

            if ($isLulus) {
                $lulusCount++;
            } else {
                $belumCount++;
            }

            $kelompok = AbsensiPembelajaran::where('user_id', $mhs->id)->value('kelompok') ?? ('Kelompok ' . (($index % 6) + 1));

            $list[] = [
                'id' => $mhs->id,
                'nama' => $mhs->name,
                'nim' => $mhs->nim ?? '-',
                'kelompok' => $kelompok,
                'total_hadir' => $totalHadir,
                'target_hadir' => $targetTotalHadir,
                'is_lulus' => $isLulus,
                'status_label' => $isLulus ? 'Memenuhi Syarat' : 'Belum Memenuhi Syarat',
                'catatan_syarat' => $isLulus ? 'Lulus 16 pertemuan di seluruh mata pelajaran' : 'Kurang di ' . $mapelKurang . ' mata pelajaran',
            ];
        }

        return [
            'daftar' => $list,
            'totalMahasiswa' => count($list),
            'totalLulus' => $lulusCount,
            'totalBelumLulus' => $belumCount,
        ];
    }

    /**
     * Memastikan data master mata pelajaran, kelompok, dan jadwal awal tersedia di database.
     */
    protected function pastikanAdaData(): void
    {
        try {
            if (! Schema::hasTable('mata_pelajarans') || ! Schema::hasTable('kelompok_belajars') || ! Schema::hasTable('kelompok_pengajars') || ! Schema::hasTable('jadwal_pembelajarans') || ! Schema::hasTable('absensi_pembelajarans')) {
                Artisan::call('migrate', ['--force' => true]);
            }

            if (Schema::hasTable('mata_pelajarans') && Schema::hasTable('kelompok_pengajars')) {
                if (MataPelajaran::count() === 0 || KelompokPengajar::count() === 0) {
                    (new PembelajaranSeeder())->run();
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
