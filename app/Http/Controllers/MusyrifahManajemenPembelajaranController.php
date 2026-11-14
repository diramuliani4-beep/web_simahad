<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\KelompokBelajar;
use App\Models\KelompokPengajar;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\PertemuanPembelajaran;
use App\Models\Tugas;
use App\Models\User;
use App\Services\AuditTrailService;
use Carbon\Carbon;
use Database\Seeders\PembelajaranSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MusyrifahManajemenPembelajaranController extends Controller
{
    public function __construct(private readonly AuditTrailService $audit) {}

    /**
     * Menampilkan halaman manajemen pembelajaran Musyrifah lengkap dengan 5 menu utama.
     */
    public function index(Request $request): View
    {
        $this->pastikanPengelola($request);
        $this->pastikanAdaData();

        // 1. Data Ringkasan Atas
        $jumlahMataPelajaran = MataPelajaran::count();
        $jumlahKelompokBelajar = KelompokBelajar::count();
        $jumlahPengajar = KelompokPengajar::count();
        $targetPertemuan = 16;

        // Pertemuan terlaksana tertinggi dari data Guru, atau data absensi lama.
        $maxPertemuan = (int) PertemuanPembelajaran::query()->selesai()->max('pertemuan_ke');
        if ($maxPertemuan === 0) {
            $maxPertemuan = (int) AbsensiPembelajaran::max('pertemuan_ke');
        }
        $jumlahPertemuanTerlaksana = min(16, $maxPertemuan);

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

        // Akun Guru yang dapat dipilih sebagai pengampu jadwal.
        $daftarGuru = User::query()
            ->whereRaw('LOWER(role) = ?', ['guru'])
            ->with('pegawai')
            ->orderBy('name')
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
            'daftarGuru',
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
        $this->pastikanPengelola($request);

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

        $this->audit->catat(
            'pembelajaran',
            'pengajar_ditambahkan',
            'Pengajar '.$item->nama_pengajar,
            (int) $item->getKey(),
            'Data pengajar kelompok ditambahkan.',
            $request->user(),
            [],
            $item->getAttributes(),
        );

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
        $this->pastikanPengelola($request);

        $validated = $request->validate([
            'nama_pengajar' => ['required', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:255'],
            'kelompok_teks' => ['required', 'string', 'max:255'],
            'jumlah_tm' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $item = KelompokPengajar::findOrFail($id);
        $sebelum = $item->getAttributes();
        $item->update($validated);

        $this->audit->catat(
            'pembelajaran',
            'pengajar_diperbarui',
            'Pengajar '.$item->nama_pengajar,
            (int) $item->getKey(),
            'Data pengajar kelompok diperbarui.',
            $request->user(),
            $sebelum,
            $item->getAttributes(),
        );

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
        $this->pastikanPengelola($request);

        $item = KelompokPengajar::findOrFail($id);
        $nama = $item->nama_pengajar;
        $idItem = (int) $item->getKey();
        $item->delete();

        $this->audit->catat(
            'pembelajaran',
            'pengajar_dihapus',
            'Pengajar '.$nama,
            $idItem,
            'Data pengajar kelompok dihapus.',
            $request->user(),
            ['nama_pengajar' => $nama],
            [],
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data kelompok pengajar berhasil dihapus.']);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'kelompok'])
            ->with('success', 'Data pengajar kelompok belajar berhasil dihapus.');
    }

    /**
     * CRUD Mata Pelajaran: Tambah mata pelajaran baru.
     */
    public function storeMataPelajaran(Request $request): RedirectResponse|JsonResponse
    {
        $this->pastikanPengelola($request);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', Rule::unique('mata_pelajarans', 'kode')],
            'kategori' => ['required', 'string', 'max:100'],
            'target_pertemuan' => ['required', 'integer', 'min:1', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ]);

        $item = MataPelajaran::create([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'kategori' => $validated['kategori'],
            'target_pertemuan' => $validated['target_pertemuan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        $this->audit->catat(
            'pembelajaran',
            'mata_pelajaran_ditambahkan',
            'Mata pelajaran '.$item->nama,
            (int) $item->getKey(),
            'Data mata pelajaran ditambahkan.',
            $request->user(),
            [],
            $item->getAttributes(),
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data mata pelajaran berhasil ditambahkan.', 'data' => $item]);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'mapel'])
            ->with('success', 'Mata pelajaran '.$item->nama.' berhasil ditambahkan.');
    }

    /**
     * CRUD Mata Pelajaran: Update data mata pelajaran.
     */
    public function updateMataPelajaran(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->pastikanPengelola($request);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', Rule::unique('mata_pelajarans', 'kode')->ignore($id)],
            'kategori' => ['required', 'string', 'max:100'],
            'target_pertemuan' => ['required', 'integer', 'min:1', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ]);

        $item = MataPelajaran::findOrFail($id);
        $sebelum = $item->getAttributes();
        $item->update([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'kategori' => $validated['kategori'],
            'target_pertemuan' => $validated['target_pertemuan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        $this->audit->catat(
            'pembelajaran',
            'mata_pelajaran_diperbarui',
            'Mata pelajaran '.$item->nama,
            (int) $item->getKey(),
            'Data mata pelajaran diperbarui.',
            $request->user(),
            $sebelum,
            $item->getAttributes(),
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data mata pelajaran berhasil diperbarui.', 'data' => $item]);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'mapel'])
            ->with('success', 'Mata pelajaran '.$item->nama.' berhasil diperbarui.');
    }

    /**
     * CRUD Mata Pelajaran: Hapus data mata pelajaran.
     */
    public function destroyMataPelajaran(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $this->pastikanPengelola($request);

        $item = MataPelajaran::withCount('jadwals')->findOrFail($id);

        if ($item->jadwals_count > 0) {
            $pesan = 'Mata pelajaran '.$item->nama.' tidak dapat dihapus karena masih memiliki '.$item->jadwals_count.' jadwal aktif.';

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $pesan], 422);
            }

            return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'mapel'])
                ->with('error', $pesan);
        }

        $nama = $item->nama;
        $idItem = (int) $item->getKey();
        $item->delete();

        $this->audit->catat(
            'pembelajaran',
            'mata_pelajaran_dihapus',
            'Mata pelajaran '.$nama,
            $idItem,
            'Data mata pelajaran dihapus.',
            $request->user(),
            ['nama' => $nama],
            [],
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data mata pelajaran berhasil dihapus.']);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'mapel'])
            ->with('success', 'Mata pelajaran '.$nama.' berhasil dihapus.');
    }

    /**
     * CRUD Jadwal: Tambah Jadwal Pembelajaran baru.
     */
    public function storeJadwal(Request $request): RedirectResponse|JsonResponse
    {
        $this->pastikanPengelola($request);

        $validated = $request->validate($this->aturanJadwal());

        $mapel = MataPelajaran::where('nama', $validated['nama_mata_pelajaran'])->first();

        $jadwal = JadwalPembelajaran::create(array_merge($this->penugasanGuru($validated), [
            'mata_pelajaran_id' => $mapel?->id,
            'nama_mata_pelajaran' => $validated['nama_mata_pelajaran'],
            'kelompok' => $validated['kelompok'],
            'periode' => $validated['periode'] ?: 'Semester Ganjil 2026/2027',
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'ruangan' => $validated['ruangan'] ?: 'Ruang Kelas',
        ]));

        $this->kirimNotifikasiJadwal($jadwal, 'jadwal-baru', 'Jadwal pembelajaran baru');

        $this->audit->catat(
            'pembelajaran',
            'jadwal_ditambahkan',
            'Jadwal '.$jadwal->nama_mata_pelajaran.' ('.$jadwal->kelompok.')',
            (int) $jadwal->getKey(),
            'Jadwal pembelajaran ditambahkan pada '.$jadwal->hari.' '.$jadwal->jam_mulai.'-'.$jadwal->jam_selesai.'.',
            $request->user(),
            [],
            $jadwal->getAttributes(),
        );

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
        $this->pastikanPengelola($request);

        $validated = $request->validate($this->aturanJadwal());

        $jadwal = JadwalPembelajaran::findOrFail($id);
        $mapel = MataPelajaran::where('nama', $validated['nama_mata_pelajaran'])->first();
        $sebelum = $jadwal->getAttributes();

        $jadwal->update(array_merge($validated, $this->penugasanGuru($validated), [
            'mata_pelajaran_id' => $mapel?->id ?? $jadwal->mata_pelajaran_id,
        ]));

        $this->kirimNotifikasiJadwal($jadwal, 'jadwal-ubah', 'Perubahan jadwal pembelajaran');

        $this->audit->catat(
            'pembelajaran',
            'jadwal_diperbarui',
            'Jadwal '.$jadwal->nama_mata_pelajaran.' ('.$jadwal->kelompok.')',
            (int) $jadwal->getKey(),
            'Jadwal pembelajaran diperbarui.',
            $request->user(),
            $sebelum,
            $jadwal->getAttributes(),
        );

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
        $this->pastikanPengelola($request);

        $jadwal = JadwalPembelajaran::findOrFail($id);

        $this->kirimNotifikasiJadwal($jadwal, 'jadwal-hapus', 'Jadwal pembelajaran dihapus');

        $sebelum = $jadwal->getAttributes();
        $label = 'Jadwal '.$jadwal->nama_mata_pelajaran.' ('.$jadwal->kelompok.')';
        $idJadwal = (int) $jadwal->getKey();

        $jadwal->delete();

        $this->audit->catat(
            'pembelajaran',
            'jadwal_dihapus',
            $label,
            $idJadwal,
            'Jadwal pembelajaran dihapus.',
            $request->user(),
            $sebelum,
            [],
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Jadwal pembelajaran berhasil dihapus.']);
        }

        return redirect()->route('musyrifah.manajemen_pembelajaran', ['tab' => 'jadwal'])
            ->with('success', 'Jadwal pembelajaran berhasil dihapus.');
    }

    /**
     * Aturan validasi jadwal beserta akun Guru pengampu.
     *
     * @return array<string, mixed>
     */
    private function aturanJadwal(): array
    {
        return [
            'nama_mata_pelajaran' => ['required', 'string', 'max:255'],
            'kelompok' => ['required', 'string', 'max:100'],
            'guru_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(
                fn ($query) => $query->whereRaw('LOWER(role) = ?', ['guru'])
            )],
            'guru_pengampu' => ['nullable', 'string', 'max:255'],
            'periode' => ['nullable', 'string', 'max:100'],
            'hari' => ['required', 'string', 'max:50'],
            'jam_mulai' => ['required', 'string', 'max:10'],
            'jam_selesai' => ['required', 'string', 'max:10'],
            'ruangan' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Menyelaraskan penugasan jadwal dengan akun Guru yang dipilih Musyrifah.
     *
     * Penugasan baru menyimpan guru_id sekaligus nama akun pada guru_pengampu,
     * sehingga langsung terbaca pada menu Pembelajaran akun Guru tersebut.
     *
     * @param  array<string, mixed>  $validated
     * @return array{guru_id: int|null, guru_pengampu: string}
     */
    private function penugasanGuru(array $validated): array
    {
        if (filled($validated['guru_id'] ?? null)) {
            $guru = User::find($validated['guru_id']);

            if ($guru) {
                return [
                    'guru_id' => (int) $guru->getKey(),
                    'guru_pengampu' => (string) $guru->name,
                ];
            }
        }

        $namaPengampu = trim((string) ($validated['guru_pengampu'] ?? ''));

        if ($namaPengampu === '') {
            throw ValidationException::withMessages([
                'guru_id' => 'Pilih akun Guru pengampu agar penugasan tampil pada menu Pembelajaran Guru tersebut.',
            ]);
        }

        return ['guru_id' => null, 'guru_pengampu' => $namaPengampu];
    }

    /**
     * Memberi tahu mahasiswa satu kelompok bahwa jadwalnya berubah.
     * Kunci unik memakai isi jadwal agar perubahan yang sama tidak
     * menghasilkan notifikasi ganda.
     */
    private function kirimNotifikasiJadwal(JadwalPembelajaran $jadwal, string $jenis, string $judul): void
    {
        $kunci = implode(':', [
            'jadwal',
            $jenis,
            $jadwal->id,
            md5(implode('|', [
                (string) $jadwal->hari,
                (string) $jadwal->jam_mulai,
                (string) $jadwal->jam_selesai,
                (string) $jadwal->ruangan,
                (string) $jadwal->guru_pengampu,
                (string) $jadwal->kelompok,
            ])),
        ]);

        $pesan = $jadwal->namaMataPelajaran().' - '.$jadwal->hari.', '.$jadwal->jamTeks()
            .' oleh '.($jadwal->guru_pengampu ?: 'Pengajar Ma\'had')
            .' ('.$jadwal->kelompok.').';

        $tanggal = Carbon::now()->next($jadwal->nomorHari());
        $tautan = route('dashboard', [
            'bulan' => $tanggal->format('Y-m'),
            'tanggal' => $tanggal->format('Y-m-d'),
        ]);

        foreach (User::mahasiswaKelompok((string) $jadwal->kelompok) as $mahasiswa) {
            Notifikasi::kirim(
                $mahasiswa->id,
                Notifikasi::TIPE_JADWAL_BERUBAH,
                $judul,
                $pesan,
                $tautan,
                $kunci
            );
        }
    }

    /**
     * Detail Pemantauan: Menampilkan rincian status 16 pertemuan untuk mata pelajaran tertentu.
     */
    public function detailPemantauan(Request $request, int $id): JsonResponse
    {
        $this->pastikanPengelola($request);
        $mapel = MataPelajaran::findOrFail($id);

        // Guru mengerjakan pertemuan per mata pelajaran, kelompok, dan periode,
        // sehingga detail dapat dipersempit melalui parameter opsional berikut.
        $kelompok = trim((string) $request->query('kelompok', ''));
        $periode = trim((string) $request->query('periode', ''));

        // Ambil rekap absensi per pertemuan 1 s/d 16.
        $pertemuanList = [];
        for ($p = 1; $p <= 16; $p++) {
            $pertemuan = PertemuanPembelajaran::query()
                ->withCount('dokumentasi')
                ->where('mata_pelajaran_id', $mapel->id)
                ->where('pertemuan_ke', $p)
                ->when($kelompok !== '', fn ($query) => $query->where('kelompok', $kelompok))
                ->when($periode !== '', fn ($query) => $query->where('periode', $periode))
                ->orderBy('id')
                ->first();

            $absensiQuery = AbsensiPembelajaran::where('mata_pelajaran_id', $mapel->id)
                ->where('pertemuan_ke', $p)
                ->when($kelompok !== '', fn ($query) => $query->where('kelompok', $kelompok))
                ->when(
                    $pertemuan,
                    fn ($query) => $query->where('pertemuan_id', $pertemuan->id),
                    fn ($query) => $query->whereNull('pertemuan_id'),
                );

            $total = (clone $absensiQuery)->count();
            $hadir = (clone $absensiQuery)->where('status', 'hadir')->count();
            $izin = (clone $absensiQuery)->where('status', 'izin')->count();
            $sakit = (clone $absensiQuery)->where('status', 'sakit')->count();
            $alpa = (clone $absensiQuery)->where('status', 'alpa')->count();

            $tanggal = $pertemuan?->tanggal?->format('d M Y')
                ?? (($tanggalLegacy = (clone $absensiQuery)->value('tanggal'))
                    ? Carbon::parse($tanggalLegacy)->format('d M Y')
                    : 'Minggu ke-'.$p);

            $statusPertemuan = match (true) {
                (bool) $pertemuan?->sudahSelesai() => 'Terlaksana',
                (bool) $pertemuan?->berlangsung() => 'Berlangsung',
                $total > 0 => 'Terlaksana',
                default => 'Terjadwal',
            };

            $persentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

            $idTugas = $pertemuan
                ? Tugas::where('pertemuan_id', $pertemuan->id)->pluck('id')
                : Tugas::query()
                    ->whereNull('pertemuan_id')
                    ->where('mata_pelajaran_id', $mapel->id)
                    ->when($kelompok !== '', fn ($query) => $query->where('kelompok', $kelompok))
                    ->pluck('id');

            $nilaiRata = $idTugas->isEmpty()
                ? null
                : PengumpulanTugas::whereIn('tugas_id', $idTugas)->whereNotNull('nilai')->avg('nilai');

            $pertemuanList[] = [
                'pertemuan_ke' => $p,
                'tanggal' => $tanggal,
                'status_pertemuan' => $statusPertemuan,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total_mahasiswa' => $total,
                'persentase_kehadiran' => $persentase.'%',
                'materi' => (bool) $pertemuan?->punyaMateri(),
                'materi_ringkas' => $pertemuan?->materi ? mb_substr((string) $pertemuan->materi, 0, 90) : null,
                'jumlah_dokumentasi' => (int) ($pertemuan?->dokumentasi_count ?? 0),
                'dokumentasi_url' => $pertemuan && $pertemuan->dokumentasi_count > 0
                    ? route('pembelajaran.dokumentasi', $pertemuan) : null,
                'jumlah_tugas' => $idTugas->count(),
                'nilai_rata_rata' => $nilaiRata !== null ? round((float) $nilaiRata, 1) : null,
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
                'kelompok' => $kelompok !== '' ? $kelompok : null,
                'periode' => $periode !== '' ? $periode : null,
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
                'status_mapel' => $memenuhiMapel ? 'Lengkap (16/16)' : 'Kurang '.(16 - $hadir).' Pertemuan',
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
        $jadwalList = JadwalPembelajaran::query()
            ->with('mataPelajaran')
            ->orderBy('mata_pelajaran_id')
            ->orderBy('kelompok')
            ->orderBy('id')
            ->get();

        if ($jadwalList->isEmpty()) {
            return MataPelajaran::all()->map(fn (MataPelajaran $mapel) => [
                'id' => $mapel->id,
                'nama' => $mapel->nama,
                'kode' => $mapel->kode,
                'kategori' => $mapel->kategori,
                'kelompok' => '-',
                'pengajar' => "Pengajar Ma'had",
                'periode' => 'Semester Ganjil 2026/2027',
                'pertemuan_terlaksana' => 0,
                'target_pertemuan' => 16,
                'persentase' => 0.0,
                'jumlah_materi' => 0,
                'jumlah_dokumentasi' => 0,
                'dokumentasi_pertemuan' => [],
                'jumlah_tugas' => 0,
                'nilai_rata_rata' => null,
            ])->toArray();
        }

        // Satu baris pemantauan untuk setiap penugasan (mapel + kelompok + periode).
        return $jadwalList->map(function (JadwalPembelajaran $jadwal) {
            $mapel = $jadwal->mataPelajaran;
            $periode = trim((string) $jadwal->periode) ?: 'Semester Ganjil 2026/2027';

            $pertemuan = PertemuanPembelajaran::query()
                ->withCount('dokumentasi')
                ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                ->where('kelompok', $jadwal->kelompok)
                ->where('periode', $periode)
                ->get();

            $idPertemuan = $pertemuan->pluck('id');

            // Progres diambil dari pertemuan yang diselesaikan Guru, lalu
            // memakai data absensi lama bila pertemuan belum pernah dijalankan.
            if ($idPertemuan->isEmpty()) {
                $pertemuanTerlaksana = min(16, (int) AbsensiPembelajaran::query()
                    ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                    ->where('kelompok', $jadwal->kelompok)
                    ->max('pertemuan_ke'));
            } else {
                $pertemuanTerlaksana = $pertemuan->filter(fn (PertemuanPembelajaran $item) => $item->sudahSelesai())->count();
            }

            $absensi = AbsensiPembelajaran::query()
                ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                ->where('kelompok', $jadwal->kelompok)
                ->where(function ($query) use ($idPertemuan) {
                    $query->whereIn('pertemuan_id', $idPertemuan)->orWhereNull('pertemuan_id');
                });

            $totalAbsensi = (clone $absensi)->count();
            $totalHadir = (clone $absensi)->where('status', 'hadir')->count();
            $persentase = $totalAbsensi > 0 ? min(100, round(($totalHadir / $totalAbsensi) * 100, 1)) : 0.0;

            $idTugas = Tugas::query()
                ->where(function ($query) use ($idPertemuan, $jadwal) {
                    $query->whereIn('pertemuan_id', $idPertemuan)
                        ->orWhere(function ($lama) use ($jadwal) {
                            $lama->whereNull('pertemuan_id')
                                ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                                ->where('kelompok', $jadwal->kelompok);
                        });
                })
                ->pluck('id');

            $nilaiRata = $idTugas->isEmpty()
                ? null
                : PengumpulanTugas::whereIn('tugas_id', $idTugas)->whereNotNull('nilai')->avg('nilai');

            return [
                'id' => $mapel?->id ?? (int) $jadwal->mata_pelajaran_id,
                'nama' => $mapel?->nama ?? $jadwal->namaMataPelajaran(),
                'kode' => $mapel?->kode ?? '-',
                'kategori' => $mapel?->kategori ?? '-',
                'kelompok' => $jadwal->kelompok ?: '-',
                'pengajar' => $jadwal->guru_pengampu ?: "Pengajar Ma'had",
                'periode' => $periode,
                'pertemuan_terlaksana' => $pertemuanTerlaksana,
                'target_pertemuan' => 16,
                'persentase' => $persentase,
                'jumlah_materi' => $pertemuan->filter(fn (PertemuanPembelajaran $item) => $item->punyaMateri())->count(),
                'jumlah_dokumentasi' => (int) $pertemuan->sum('dokumentasi_count'),
                'dokumentasi_pertemuan' => $pertemuan->where('dokumentasi_count', '>', 0)->map(fn (PertemuanPembelajaran $item): array => [
                    'id' => $item->id,
                    'nomor' => $item->pertemuan_ke,
                    'jumlah' => (int) $item->dokumentasi_count,
                ])->values()->all(),
                'jumlah_tugas' => $idTugas->count(),
                'nilai_rata_rata' => $nilaiRata !== null ? round((float) $nilaiRata, 1) : null,
            ];
        })->values()->toArray();
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

            $kelompok = AbsensiPembelajaran::where('user_id', $mhs->id)->value('kelompok') ?? ('Kelompok '.(($index % 6) + 1));

            $list[] = [
                'id' => $mhs->id,
                'nama' => $mhs->name,
                'nim' => $mhs->nim ?? '-',
                'kelompok' => $kelompok,
                'total_hadir' => $totalHadir,
                'target_hadir' => $targetTotalHadir,
                'is_lulus' => $isLulus,
                'status_label' => $isLulus ? 'Memenuhi Syarat' : 'Belum Memenuhi Syarat',
                'catatan_syarat' => $isLulus ? 'Lulus 16 pertemuan di seluruh mata pelajaran' : 'Kurang di '.$mapelKurang.' mata pelajaran',
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
                    (new PembelajaranSeeder)->run();
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Hanya Musyrifah/Pengelola/Admin yang boleh mengubah data pembelajaran.
     */
    private function pastikanPengelola(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['musyrifah', 'pengelola', 'admin'], true), 403);
    }
}
