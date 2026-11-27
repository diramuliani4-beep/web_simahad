<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\KelompokPengajar;
use App\Models\MataPelajaran;
use App\Models\PengumpulanTugas;
use App\Models\PertemuanPembelajaran;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Menu Pembelajaran Mahasiswa.
 *
 * All subjects and learning data use the student's single active group.
 * There is no fallback to another group; students without a placement see
 * the existing empty state.
 */
class PembelajaranController extends Controller
{
    /** Warna aksen kartu mata pelajaran. */
    private const WARNA = ['#790c20', '#2563eb', '#10b981', '#f59e0b', '#8b5cf6'];

    /** Nama ikon kartu mata pelajaran. */
    private const IKON = ['book-open', 'award', 'sparkles', 'languages', 'heart-handshake'];

    /** Target pertemuan bawaan bila mata pelajaran belum menentukan target. */
    private const TARGET_PERTEMUAN = 16;

    /** Show every subject using the student's single active group. */
    public function index(Request $request): View
    {
        $user = $this->mahasiswa($request);
        $penempatan = $user->penempatanKelompokBelajarAktif;
        $kelompokSaya = $penempatan?->kelompokBelajar?->nama_kelompok;

        if (blank($kelompokSaya)) {
            return view('mahasiswa.pembelajaran_mhs', $this->dataTanpaKelompok($user));
        }

        $jadwalSaya = JadwalPembelajaran::query()
            ->where('kelompok', $kelompokSaya)
            ->with('mataPelajaran')
            ->get()
            ->sortBy([
                fn (JadwalPembelajaran $a, JadwalPembelajaran $b) => $a->nomorHari() <=> $b->nomorHari(),
                fn (JadwalPembelajaran $a, JadwalPembelajaran $b) => strcmp((string) $a->jam_mulai, (string) $b->jam_mulai),
            ])
            ->values();

        $materiPembelajaran = $this->materiPembelajaran($kelompokSaya);
        $daftarTugas = $this->daftarTugas($user);

        $jadwalPerMapel = $jadwalSaya->groupBy('mata_pelajaran_id');
        $materiPerMapel = $materiPembelajaran->groupBy('mata_pelajaran_id');
        $tugasPerMapel = $daftarTugas->groupBy(fn (array $item) => (int) $item['tugas']->mata_pelajaran_id);

        // 1. Mata pelajaran beserta kelompok, jadwal, dan progres kehadirannya.
        $daftarMataPelajaran = MataPelajaran::query()->orderBy('id')->get()
            ->map(function (MataPelajaran $mapel, int $index) use ($user, $kelompokSaya, $jadwalPerMapel, $materiPerMapel, $tugasPerMapel) {
                $jadwal = $jadwalPerMapel->get($mapel->getKey())?->first();

                $absensi = AbsensiPembelajaran::query()
                    ->where('user_id', $user->getKey())
                    ->where('mata_pelajaran_id', $mapel->getKey())
                    ->where('kelompok', $kelompokSaya)
                    ->get();

                $ringkasan = $this->ringkasanKehadiran($absensi);
                $target = $this->targetPertemuan($mapel);

                $tugasMapel = $tugasPerMapel->get($mapel->getKey()) ?? collect();

                return [
                    'id' => $mapel->getKey(),
                    'nama' => $mapel->nama,
                    'kode' => $mapel->kode ?: ('PKK-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)),
                    'kategori' => $mapel->kategori ?: 'Program Ma\'had',
                    'deskripsi' => $mapel->deskripsi,
                    'kelompok' => $kelompokSaya,
                    'guru_pengampu' => $jadwal?->namaGuruPengampu() ?? 'Belum ditetapkan',
                    'ada_jadwal' => $jadwal !== null,
                    'hari' => $jadwal?->hari ?: '-',
                    'jam' => $jadwal?->jamTeks() ?: '-',
                    'ruangan' => $jadwal?->ruangan ?: '-',
                    'periode' => $jadwal?->periode ?: '-',
                    'target_pertemuan' => $target,
                    'hadir' => $ringkasan['hadir'],
                    'izin' => $ringkasan['izin'],
                    'sakit' => $ringkasan['sakit'],
                    'alpa' => $ringkasan['alpa'],
                    'total_tercatat' => $ringkasan['total'],
                    'persentase' => $target > 0 ? min(100, round(($ringkasan['hadir'] / $target) * 100, 1)) : 0.0,
                    'is_lulus' => $ringkasan['hadir'] >= $target,
                    'sisa_pertemuan' => max(0, $target - $ringkasan['hadir']),
                    'jumlah_materi' => $materiPerMapel->get($mapel->getKey())?->count() ?? 0,
                    'jumlah_tugas' => $tugasMapel->count(),
                    'tugas_terkumpul' => $tugasMapel->filter(fn (array $item) => $item['pengumpulan'] !== null)->count(),
                    'warna' => self::WARNA[$index % count(self::WARNA)],
                    'ikon' => self::IKON[$index % count(self::IKON)],
                ];
            });

        // 2. Status kelulusan dihitung dari mata pelajaran yang benar-benar diikuti.
        $totalMataPelajaran = $daftarMataPelajaran->count();
        $totalTargetKehadiran = (int) $daftarMataPelajaran->sum('target_pertemuan');
        $totalHadirSeluruh = (int) $daftarMataPelajaran->sum('hadir');
        $mapelBelumLulus = $daftarMataPelajaran->reject(fn (array $item) => $item['is_lulus'])->count();

        $isLulus = $totalMataPelajaran > 0 && $mapelBelumLulus === 0 && $totalHadirSeluruh >= $totalTargetKehadiran;
        $nomorSertifikat = 'SERT/SIMAHAD/'.date('Y').'/'.str_pad((string) $user->getKey(), 4, '0', STR_PAD_LEFT);

        // 3. Jadwal terdekat dan kalender dihitung dari jadwal kelompok mahasiswa.
        $hariMap = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        $todayIndex = Carbon::now()->dayOfWeek;
        $jadwalTerdekat = [];

        for ($offset = 0; $offset < 7; $offset++) {
            $namaHari = $hariMap[($todayIndex + $offset) % 7];
            $targetDate = Carbon::now()->addDays($offset);
            $statusLabel = match ($offset) {
                0 => 'Hari Ini',
                1 => 'Besok',
                default => $namaHari,
            };

            foreach ($jadwalSaya->where('hari', $namaHari) as $jadwal) {
                $jadwalTerdekat[] = [
                    'hari' => $jadwal->hari,
                    'status_hari' => $statusLabel,
                    'tanggal' => $targetDate->day,
                    'bulan' => $targetDate->translatedFormat('M'),
                    'tanggal_lengkap' => $targetDate->translatedFormat('d F Y'),
                    'waktu' => $jadwal->jamTeks(),
                    'nama' => $jadwal->namaMataPelajaran(),
                    'lokasi' => trim((string) $jadwal->ruangan.' ('.$jadwal->kelompok.')'),
                    'guru' => $jadwal->namaGuruPengampu(),
                ];

                if (count($jadwalTerdekat) >= 5) {
                    break 2;
                }
            }
        }

        $calendarEvents = [];
        $classDays = $jadwalSaya->pluck('hari')->unique()->all();
        $startDate = Carbon::now()->startOfMonth()->subDays(7);
        $endDate = Carbon::now()->endOfMonth()->addDays(14);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $namaHari = $hariMap[$date->dayOfWeek];

            if (! in_array($namaHari, $classDays, true)) {
                continue;
            }

            $calendarEvents[$date->format('Y-m-d')] = $jadwalSaya
                ->where('hari', $namaHari)
                ->values()
                ->map(fn (JadwalPembelajaran $jadwal) => [
                    'nama' => $jadwal->namaMataPelajaran(),
                    'waktu' => $jadwal->jamTeks(),
                    'ruangan' => $jadwal->ruangan,
                    'guru' => $jadwal->namaGuruPengampu(),
                    'kelompok' => $jadwal->kelompok,
                ])
                ->all();
        }

        // 4. Ringkasan tugas milik mahasiswa.
        $statistikTugas = [
            'total' => $daftarTugas->count(),
            'belum' => $daftarTugas->filter(fn (array $item) => $item['status']['status'] === Tugas::STATUS_BELUM_DIKUMPULKAN)->count(),
            'terkumpul' => $daftarTugas->filter(fn (array $item) => $item['pengumpulan'] !== null)->count(),
            'dinilai' => $daftarTugas->filter(fn (array $item) => $item['status']['status'] === Tugas::STATUS_SUDAH_DINILAI)->count(),
        ];

        return view('mahasiswa.pembelajaran_mhs', [
            'user' => $user,
            'belumAdaKelompok' => false,
            'kelompokSaya' => $kelompokSaya,
            'jumlahKelompokSaya' => 1,
            'daftarMataPelajaran' => $daftarMataPelajaran,
            'totalMataPelajaran' => $totalMataPelajaran,
            'totalTargetKehadiran' => $totalTargetKehadiran,
            'totalHadirSeluruh' => $totalHadirSeluruh,
            'mapelBelumLulus' => $mapelBelumLulus,
            'isLulus' => $isLulus,
            'targetPertemuan' => (int) ($daftarMataPelajaran->max('target_pertemuan') ?: self::TARGET_PERTEMUAN),
            'nomorSertifikat' => $nomorSertifikat,
            'jadwalSaya' => $jadwalSaya,
            'jadwalTerdekat' => $jadwalTerdekat,
            'daftarPengajarKelompok' => $this->pengajarKelompok($jadwalSaya),
            'materiPembelajaran' => $materiPembelajaran,
            'daftarTugas' => $daftarTugas,
            'statistikTugas' => $statistikTugas,
            'calendarEvents' => $calendarEvents,
        ]);
    }

    /**
     * Detail kehadiran mahasiswa pada satu mata pelajaran (AJAX/JSON).
     *
     * An active global placement grants access to every valid subject, while
     * attendance remains limited to the student's own records.
     */
    public function detailPertemuan(Request $request, int $id): JsonResponse
    {
        $user = $this->mahasiswa($request);
        $penempatan = $user->penempatanKelompokBelajarAktif;
        $kelompok = $penempatan?->kelompokBelajar?->nama_kelompok;

        abort_unless(
            filled($kelompok),
            403,
            'Anda belum ditetapkan ke kelompok belajar.'
        );

        $mapel = MataPelajaran::findOrFail($id);

        $absensi = AbsensiPembelajaran::with('pertemuan')
            ->where('user_id', $user->getKey())
            ->where('mata_pelajaran_id', $mapel->getKey())
            ->where('kelompok', $kelompok)
            ->orderBy('pertemuan_ke')
            ->get();

        $jadwal = JadwalPembelajaran::query()
            ->where('mata_pelajaran_id', $mapel->getKey())
            ->where('kelompok', $kelompok)
            ->first();

        $periodeDefault = trim((string) $jadwal?->periode);

        $periodeAbsensi = function (AbsensiPembelajaran $item) use ($periodeDefault, $kelompok): string {
            return trim((string) $item->pertemuan?->periode) ?: ($periodeDefault !== '' ? $periodeDefault : $kelompok);
        };

        $target = $this->targetPertemuan($mapel);
        $absensiPerPertemuan = $absensi->groupBy('pertemuan_ke');

        $pertemuanList = [];
        for ($p = 1; $p <= $target; $p++) {
            $item = $absensiPerPertemuan->get($p)?->first();

            $pertemuanList[] = [
                'pertemuan_ke' => $p,
                'tanggal' => $item?->tanggal
                    ? Carbon::parse($item->tanggal)->translatedFormat('d M Y')
                    : 'Minggu ke-'.$p,
                'status' => strtolower((string) ($item?->status ?: 'terjadwal')),
                'catatan' => $item?->catatan ?: ($item ? 'Pertemuan terlaksana' : 'Terjadwal tatap muka'),
                'periode' => $item ? $periodeAbsensi($item) : ($periodeDefault !== '' ? $periodeDefault : $kelompok),
            ];
        }

        $ringkasan = $this->ringkasanKehadiran($absensi);

        $ringkasanPeriode = $absensi
            ->groupBy(fn (AbsensiPembelajaran $item) => $periodeAbsensi($item))
            ->sortKeys()
            ->map(fn ($daftar, string $nama) => ['periode' => $nama] + $this->ringkasanKehadiran($daftar))
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'mapel' => [
                'id' => $mapel->getKey(),
                'nama' => $mapel->nama,
                'kode' => $mapel->kode ?: 'PKK',
                'kategori' => $mapel->kategori ?: 'Program Ma\'had',
                'kelompok' => $kelompok,
                'target_pertemuan' => $target,
                'hadir' => $ringkasan['hadir'],
                'is_lulus' => $ringkasan['hadir'] >= $target,
                'persentase' => $ringkasan['persentase'],
                'periode' => $periodeDefault,
            ],
            'ringkasan' => $ringkasan + ['target' => $target],
            'periode' => $ringkasanPeriode,
            'pertemuan' => $pertemuanList,
        ]);
    }

    /**
     * Data tampilan ketika mahasiswa belum ditempatkan pada kelompok belajar.
     *
     * @return array<string, mixed>
     */
    private function dataTanpaKelompok(User $user): array
    {
        return [
            'user' => $user,
            'belumAdaKelompok' => true,
            'kelompokSaya' => null,
            'jumlahKelompokSaya' => 0,
            'daftarMataPelajaran' => collect(),
            'totalMataPelajaran' => 0,
            'totalTargetKehadiran' => 0,
            'totalHadirSeluruh' => 0,
            'mapelBelumLulus' => 0,
            'isLulus' => false,
            'targetPertemuan' => self::TARGET_PERTEMUAN,
            'nomorSertifikat' => '-',
            'jadwalSaya' => collect(),
            'jadwalTerdekat' => [],
            'daftarPengajarKelompok' => collect(),
            'materiPembelajaran' => collect(),
            'daftarTugas' => collect(),
            'statistikTugas' => ['total' => 0, 'belum' => 0, 'terkumpul' => 0, 'dinilai' => 0],
            'calendarEvents' => [],
        ];
    }

    /**
     * Materi pertemuan yang dibagikan Guru pada kelompok mahasiswa.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function materiPembelajaran(string $kelompok): Collection
    {
        return PertemuanPembelajaran::query()
            ->where('kelompok', $kelompok)
            ->with(['mataPelajaran', 'guru', 'jadwal'])
            ->orderByDesc('tanggal')
            ->orderByDesc('pertemuan_ke')
            ->get()
            ->filter(fn (PertemuanPembelajaran $pertemuan) => $pertemuan->punyaMateri())
            ->map(fn (PertemuanPembelajaran $pertemuan) => [
                'id' => $pertemuan->getKey(),
                'mata_pelajaran_id' => (int) $pertemuan->mata_pelajaran_id,
                'mapel' => $pertemuan->mataPelajaran?->nama ?? 'Mata Pelajaran',
                'kelompok' => (string) $pertemuan->kelompok,
                'pertemuan_ke' => (int) $pertemuan->pertemuan_ke,
                'periode' => (string) $pertemuan->periode,
                'tanggal' => $pertemuan->tanggalTeks(),
                'judul' => $pertemuan->materi_nama ?: 'Materi Pertemuan '.$pertemuan->pertemuan_ke,
                'ringkasan' => trim((string) $pertemuan->materi),
                'guru' => $pertemuan->guru?->name ?? ($pertemuan->jadwal?->namaGuruPengampu() ?? 'Pengajar Ma\'had'),
                'ada_berkas' => $pertemuan->punyaBerkasMateri(),
            ])
            ->values();
    }

    /**
     * Tugas yang dipublikasikan pada kelompok mahasiswa.
     *
     * @return Collection<int, array{tugas: Tugas, pengumpulan: PengumpulanTugas|null, status: array<string, string>}>
     */
    private function daftarTugas(User $user): Collection
    {
        return Tugas::query()
            ->dipublikasikan()
            ->untukAnggota($user)
            ->with(['mataPelajaran', 'guru'])
            ->with(['pengumpulan' => fn ($query) => $query->where('user_id', $user->getKey())])
            ->orderByDesc('batas_pengumpulan')
            ->orderByDesc('id')
            ->get()
            ->map(function (Tugas $tugas) {
                $pengumpulan = $tugas->pengumpulan->first();

                return [
                    'tugas' => $tugas,
                    'pengumpulan' => $pengumpulan,
                    'status' => $tugas->statusUntuk($pengumpulan),
                ];
            })
            ->values();
    }

    /**
     * Pengajar kelompok mahasiswa, diambil dari jadwal yang sudah ditetapkan.
     *
     * @param  Collection<int, JadwalPembelajaran>  $jadwalSaya
     * @return Collection<int, array<string, mixed>>
     */
    private function pengajarKelompok(Collection $jadwalSaya): Collection
    {
        $acuan = KelompokPengajar::all();

        return $jadwalSaya
            ->map(function (JadwalPembelajaran $jadwal) use ($acuan) {
                $nama = $jadwal->namaGuruPengampu();
                $program = $jadwal->namaMataPelajaran();

                $detail = $acuan->first(fn (KelompokPengajar $pengajar) => mb_strtolower(trim((string) $pengajar->nama_pengajar)) === mb_strtolower(trim((string) $nama))
                    && mb_strtolower(trim((string) $pengajar->program)) === mb_strtolower(trim((string) $program)));

                return [
                    'id' => $jadwal->getKey(),
                    'nama' => $nama,
                    'program' => $program,
                    'kelompok' => (string) $jadwal->kelompok,
                    'jadwal' => trim($jadwal->hari.', '.$jadwal->jamTeks()),
                    'ruangan' => $jadwal->ruangan ?: '-',
                    'beban' => $detail?->jumlah_tm,
                    'keterangan' => $detail?->keterangan,
                ];
            })
            ->unique(fn (array $item) => $item['nama'].'|'.$item['program'].'|'.$item['kelompok'])
            ->values();
    }

    /**
     * Target pertemuan tatap muka satu mata pelajaran.
     */
    private function targetPertemuan(MataPelajaran $mapel): int
    {
        $target = (int) ($mapel->target_pertemuan ?: self::TARGET_PERTEMUAN);

        return $target > 0 ? $target : self::TARGET_PERTEMUAN;
    }

    /**
     * Rekap status kehadiran beserta persentase dari sekumpulan absensi.
     *
     * @param  Collection<int, AbsensiPembelajaran>  $absensi
     * @return array{hadir: int, izin: int, sakit: int, alpa: int, total: int, persentase: float}
     */
    protected function ringkasanKehadiran(Collection $absensi): array
    {
        $status = $absensi->countBy(
            fn (AbsensiPembelajaran $item) => strtolower((string) $item->status)
        );

        $hadir = (int) ($status['hadir'] ?? 0);
        $total = (int) $absensi->count();

        return [
            'hadir' => $hadir,
            'izin' => (int) ($status['izin'] ?? 0),
            'sakit' => (int) ($status['sakit'] ?? 0),
            'alpa' => (int) ($status['alpa'] ?? 0),
            'total' => $total,
            'persentase' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0.0,
        ];
    }

    /**
     * Memastikan pengguna yang mengakses adalah mahasiswa.
     */
    private function mahasiswa(Request $request): User
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));

        abort_unless($user && $role === 'mahasiswa', 403);

        return $user;
    }
}
