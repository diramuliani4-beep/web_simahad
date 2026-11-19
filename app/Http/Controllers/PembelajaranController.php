<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\KelompokBelajarAnggota;
use App\Models\KelompokPengajar;
use App\Models\MataPelajaran;
use App\Models\PengumpulanTugas;
use App\Models\PertemuanPembelajaran;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Menu Pembelajaran Mahasiswa.
 *
 * Seluruh data (mata pelajaran, jadwal, materi, tugas, dan pengajar) hanya
 * diambil dari kelompok belajar yang ditetapkan Musyrifah untuk setiap mata
 * pelajaran. Tidak ada fallback ke kelompok lain: mahasiswa hanya melihat
 * kelompoknya sendiri, dan bila belum ditempatkan, halaman menampilkan
 * keterangan bahwa kelompok belajar belum ditetapkan.
 */
class PembelajaranController extends Controller
{
    /** Warna aksen kartu mata pelajaran. */
    private const WARNA = ['#790c20', '#2563eb', '#10b981', '#f59e0b', '#8b5cf6'];

    /** Nama ikon kartu mata pelajaran. */
    private const IKON = ['book-open', 'award', 'sparkles', 'languages', 'heart-handshake'];

    /** Target pertemuan bawaan bila mata pelajaran belum menentukan target. */
    private const TARGET_PERTEMUAN = 16;

    /**
     * Menampilkan pembelajaran mahasiswa sesuai kelompok belajar per mata pelajaran.
     */
    public function index(Request $request): View
    {
        $user = $this->mahasiswa($request);
        $penempatan = $user->penempatanKelompokBelajar();

        if ($penempatan->isEmpty()) {
            return view('mahasiswa.pembelajaran_mhs', $this->dataTanpaKelompok($user));
        }

        $pasangan = $this->pasanganKelompok($penempatan);
        $daftarKelompokSaya = $penempatan->pluck('kelompokBelajar.nama_kelompok')
            ->filter()
            ->unique()
            ->values();
        $kelompokSaya = $this->labelKelompok($daftarKelompokSaya);

        // Jadwal, materi, dan tugas dibatasi pada pasangan mapel + kelompok milik mahasiswa.
        $jadwalSaya = $this->batasiPasangan(JadwalPembelajaran::query(), $pasangan)
            ->with('mataPelajaran')
            ->get()
            ->sortBy([
                fn (JadwalPembelajaran $a, JadwalPembelajaran $b) => $a->nomorHari() <=> $b->nomorHari(),
                fn (JadwalPembelajaran $a, JadwalPembelajaran $b) => strcmp((string) $a->jam_mulai, (string) $b->jam_mulai),
            ])
            ->values();

        $materiPembelajaran = $this->materiPembelajaran($pasangan);
        $daftarTugas = $this->daftarTugas($user, $pasangan);

        $jadwalPerMapel = $jadwalSaya->groupBy('mata_pelajaran_id');
        $materiPerMapel = $materiPembelajaran->groupBy('mata_pelajaran_id');
        $tugasPerMapel = $daftarTugas->groupBy(fn (array $item) => (int) $item['tugas']->mata_pelajaran_id);

        // 1. Mata pelajaran beserta kelompok, jadwal, dan progres kehadirannya.
        $daftarMataPelajaran = $penempatan->values()->map(function (KelompokBelajarAnggota $anggota, int $index) use ($user, $jadwalPerMapel, $materiPerMapel, $tugasPerMapel) {
            $mapel = $anggota->mataPelajaran;
            $kelompok = (string) $anggota->kelompokBelajar->nama_kelompok;
            $jadwal = $jadwalPerMapel->get($mapel->getKey())?->first();

            $absensi = AbsensiPembelajaran::query()
                ->where('user_id', $user->getKey())
                ->where('mata_pelajaran_id', $mapel->getKey())
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
                'kelompok' => $kelompok,
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
            'jumlahKelompokSaya' => $daftarKelompokSaya->count(),
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
     * Mahasiswa hanya dapat membuka mata pelajaran yang kelompoknya sudah
     * ditetapkan oleh Musyrifah, dan selalu dibatasi pada data miliknya sendiri.
     */
    public function detailPertemuan(Request $request, int $id): JsonResponse
    {
        $user = $this->mahasiswa($request);
        $penempatan = $user->penempatanKelompokBelajar()
            ->first(fn (KelompokBelajarAnggota $anggota) => (int) $anggota->mata_pelajaran_id === $id);

        abort_unless(
            $penempatan !== null,
            403,
            'Anda belum ditetapkan ke kelompok belajar untuk mata pelajaran ini.'
        );

        /** @var MataPelajaran $mapel */
        $mapel = $penempatan->mataPelajaran;
        $kelompok = (string) $penempatan->kelompokBelajar->nama_kelompok;

        $absensi = AbsensiPembelajaran::with('pertemuan')
            ->where('user_id', $user->getKey())
            ->where('mata_pelajaran_id', $mapel->getKey())
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
     * Pasangan mata pelajaran dan kelompok belajar milik mahasiswa.
     *
     * @param  Collection<int, KelompokBelajarAnggota>  $penempatan
     * @return Collection<int, array{mata_pelajaran_id: int, kelompok: string}>
     */
    private function pasanganKelompok(Collection $penempatan): Collection
    {
        return $penempatan
            ->map(fn (KelompokBelajarAnggota $anggota) => [
                'mata_pelajaran_id' => (int) $anggota->mata_pelajaran_id,
                'kelompok' => (string) $anggota->kelompokBelajar->nama_kelompok,
            ])
            ->unique(fn (array $item) => $item['mata_pelajaran_id'].'|'.$item['kelompok'])
            ->values();
    }

    /**
     * Membatasi kueri pada pasangan mata pelajaran + kelompok milik mahasiswa.
     *
     * @param  Collection<int, array{mata_pelajaran_id: int, kelompok: string}>  $pasangan
     */
    private function batasiPasangan(Builder $query, Collection $pasangan): Builder
    {
        return $query->where(function (Builder $sub) use ($pasangan) {
            foreach ($pasangan as $item) {
                $sub->orWhere(function (Builder $pair) use ($item) {
                    $pair->where('mata_pelajaran_id', $item['mata_pelajaran_id'])
                        ->where('kelompok', $item['kelompok']);
                });
            }
        });
    }

    /**
     * Materi pertemuan yang dibagikan Guru pada kelompok mahasiswa.
     *
     * @param  Collection<int, array{mata_pelajaran_id: int, kelompok: string}>  $pasangan
     * @return Collection<int, array<string, mixed>>
     */
    private function materiPembelajaran(Collection $pasangan): Collection
    {
        return $this->batasiPasangan(PertemuanPembelajaran::query(), $pasangan)
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
     * @param  Collection<int, array{mata_pelajaran_id: int, kelompok: string}>  $pasangan
     * @return Collection<int, array{tugas: Tugas, pengumpulan: PengumpulanTugas|null, status: array<string, string>}>
     */
    private function daftarTugas(User $user, Collection $pasangan): Collection
    {
        return $this->batasiPasangan(Tugas::query()->dipublikasikan(), $pasangan)
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
     * Label kelompok milik mahasiswa, misalnya "Kelompok 1, 2, 3".
     *
     * @param  Collection<int, string|null>  $namaKelompok
     */
    private function labelKelompok(Collection $namaKelompok): string
    {
        $nama = $namaKelompok->filter()->unique()->values();

        if ($nama->isEmpty()) {
            return '-';
        }

        $nomor = $nama->map(function (string $teks): ?string {
            return preg_match('/^Kelompok\s+(.+)$/i', trim($teks), $cocok) === 1 ? trim($cocok[1]) : null;
        });

        if ($nomor->every(fn (?string $item) => $item !== null)) {
            return 'Kelompok '.$nomor->sort()->values()->implode(', ');
        }

        return $nama->implode(', ');
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
