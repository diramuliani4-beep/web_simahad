<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\KelompokBelajar;
use App\Models\KelompokPengajar;
use App\Models\MataPelajaran;
use Carbon\Carbon;
use Database\Seeders\PembelajaranSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PembelajaranController extends Controller
{
    /**
     * Menampilkan fitur pembelajaran mahasiswa yang tersinkronisasi penuh dengan Manajemen Pembelajaran Musyrifah.
     */
    public function index(Request $request): View
    {
        $this->pastikanAdaData();

        $user = $request->user();

        // 1. Tentukan Kelompok Mahasiswa yang Sinkron
        $kelompokRequest = $request->query('kelompok');
        if ($kelompokRequest && is_string($kelompokRequest)) {
            $kelompokSaya = $kelompokRequest;
        } else {
            $kelompokSaya = AbsensiPembelajaran::where('user_id', $user?->id)->value('kelompok')
                ?: 'Kelompok 1';
        }

        preg_match('/\d+/', $kelompokSaya, $matchKelompok);
        $nomorKelompokSaya = isset($matchKelompok[0]) ? (int) $matchKelompok[0] : 1;

        $daftarKelompok = KelompokBelajar::orderBy('nomor_kelompok')->get();
        if ($daftarKelompok->isEmpty()) {
            for ($i = 1; $i <= 6; $i++) {
                KelompokBelajar::create([
                    'nomor_kelompok' => $i,
                    'nama_kelompok' => 'Kelompok '.$i,
                    'keterangan' => 'Kelompok Belajar '.$i.' Ma\'had Al-Jami\'ah',
                ]);
            }
            $daftarKelompok = KelompokBelajar::orderBy('nomor_kelompok')->get();
        }

        // 2. Data Mata Pelajaran & Progres 16 Pertemuan Mahasiswa
        $mapels = MataPelajaran::withCount('jadwals')->get();
        $colors = ['#790c20', '#2563eb', '#10b981', '#f59e0b', '#8b5cf6'];
        $iconNames = ['book-open', 'award', 'sparkles', 'languages', 'heart-handshake'];

        $daftarMataPelajaran = $mapels->map(function ($m, $index) use ($user, $kelompokSaya, $nomorKelompokSaya, $colors, $iconNames) {
            // Jadwal mapel untuk kelompok mahasiswa ini
            $jadwal = JadwalPembelajaran::where('mata_pelajaran_id', $m->id)
                ->where('kelompok', $kelompokSaya)
                ->first()
                ?: JadwalPembelajaran::where('mata_pelajaran_id', $m->id)->first();

            // Pengajar dari KelompokPengajar atau Jadwal
            $pengajar = KelompokPengajar::where('program', 'LIKE', '%'.$m->nama.'%')
                ->where('kelompok_teks', 'LIKE', '%'.$nomorKelompokSaya.'%')
                ->first();

            $namaPengajar = $pengajar?->nama_pengajar ?: ($jadwal?->guru_pengampu ?: 'Pengajar Ma\'had');

            // Absensi mahasiswa di mapel ini
            $absensiQuery = AbsensiPembelajaran::where('user_id', $user?->id)
                ->where('mata_pelajaran_id', $m->id);

            $hadir = (clone $absensiQuery)->where('status', 'hadir')->count();
            $izin = (clone $absensiQuery)->where('status', 'izin')->count();
            $sakit = (clone $absensiQuery)->where('status', 'sakit')->count();
            $alpa = (clone $absensiQuery)->where('status', 'alpa')->count();
            $totalRecorded = (clone $absensiQuery)->count();

            $target = (int) ($m->target_pertemuan ?: 16);
            $isLulusMapel = ($hadir >= $target);
            $persentase = $target > 0 ? min(100, round(($hadir / $target) * 100, 1)) : 0;
            $sisaPertemuan = max(0, $target - $hadir);

            return [
                'id' => $m->id,
                'nama' => $m->nama,
                'kode' => $m->kode ?: ('PKK-0'.($index + 1)),
                'kategori' => $m->kategori ?: 'Program Ma\'had',
                'deskripsi' => $m->deskripsi,
                'guru_pengampu' => $namaPengajar,
                'hari' => $jadwal?->hari ?: 'Senin',
                'jam' => $jadwal ? ($jadwal->jam_mulai.'–'.$jadwal->jam_selesai.' WIB') : '08:00–09:30 WIB',
                'ruangan' => $jadwal?->ruangan ?: 'Ruang Kelas',
                'periode' => $jadwal?->periode ?: 'Semester Ganjil 2026/2027',
                'target_pertemuan' => $target,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total_tercatat' => $totalRecorded,
                'persentase' => $persentase,
                'is_lulus' => $isLulusMapel,
                'sisa_pertemuan' => $sisaPertemuan,
                'warna' => $colors[$index % count($colors)],
                'ikon' => $iconNames[$index % count($iconNames)],
            ];
        });

        // 3. Status Kelulusan & Sertifikat (Syarat: Wajib Hadir 16x di SELURUH Mata Pelajaran)
        $totalMataPelajaran = $daftarMataPelajaran->count();
        $totalTargetKehadiran = $totalMataPelajaran * 16;
        $totalHadirSeluruh = $daftarMataPelajaran->sum('hadir');
        $mapelBelumLulus = $daftarMataPelajaran->filter(fn ($item) => ! $item['is_lulus'])->count();

        $isLulus = ($totalMataPelajaran > 0 && $mapelBelumLulus === 0 && $totalHadirSeluruh >= $totalTargetKehadiran);
        $statusKelulusanLabel = $isLulus ? 'Memenuhi Syarat' : 'Belum Memenuhi Syarat';
        $persentaseKelulusan = $totalTargetKehadiran > 0
            ? min(100, round(($totalHadirSeluruh / $totalTargetKehadiran) * 100, 1))
            : 0;
        $nomorSertifikat = 'SERT/SIMAHAD/'.date('Y').'/'.str_pad((string) ($user?->id ?? 1), 4, '0', STR_PAD_LEFT);

        // 4. Jadwal Pembelajaran Kelompok Saya & Mingguan
        $orderCase = "CASE hari 
            WHEN 'Senin' THEN 1 
            WHEN 'Selasa' THEN 2 
            WHEN 'Rabu' THEN 3 
            WHEN 'Kamis' THEN 4 
            WHEN 'Jumat' THEN 5 
            WHEN 'Sabtu' THEN 6 
            ELSE 7 END";

        $jadwalSaya = JadwalPembelajaran::where('kelompok', $kelompokSaya)
            ->orderByRaw($orderCase)
            ->orderBy('jam_mulai')
            ->get();

        if ($jadwalSaya->isEmpty()) {
            $jadwalSaya = JadwalPembelajaran::orderByRaw($orderCase)->take(6)->get();
        }

        $semuaJadwal = JadwalPembelajaran::orderByRaw($orderCase)
            ->orderBy('kelompok')
            ->orderBy('jam_mulai')
            ->get();

        // 5. Jadwal Terdekat / Hari Ini & Mendatang (Dihitung Dinamis dari Hari Nyata)
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
            $checkDayIndex = ($todayIndex + $offset) % 7;
            $namaHari = $hariMap[$checkDayIndex];

            $classes = $jadwalSaya->where('hari', $namaHari);
            $targetDate = Carbon::now()->addDays($offset);

            foreach ($classes as $c) {
                $statusLabel = $offset === 0 ? 'Hari Ini' : ($offset === 1 ? 'Besok' : $namaHari);

                $jadwalTerdekat[] = [
                    'hari' => $c->hari,
                    'status_hari' => $statusLabel,
                    'tanggal' => $targetDate->day,
                    'bulan' => $targetDate->translatedFormat('M'),
                    'tanggal_lengkap' => $targetDate->translatedFormat('d F Y'),
                    'waktu' => $c->jam_mulai.'–'.$c->jam_selesai.' WIB',
                    'nama' => $c->nama_mata_pelajaran,
                    'lokasi' => $c->ruangan.' ('.$c->kelompok.')',
                    'guru' => $c->guru_pengampu,
                ];

                if (count($jadwalTerdekat) >= 5) {
                    break 2;
                }
            }
        }

        if (empty($jadwalTerdekat)) {
            foreach ($jadwalSaya as $jdw) {
                $jadwalTerdekat[] = [
                    'hari' => $jdw->hari,
                    'status_hari' => $jdw->hari,
                    'tanggal' => Carbon::now()->day,
                    'bulan' => Carbon::now()->translatedFormat('M'),
                    'tanggal_lengkap' => Carbon::now()->translatedFormat('d F Y'),
                    'waktu' => $jdw->jam_mulai.'–'.$jdw->jam_selesai.' WIB',
                    'nama' => $jdw->nama_mata_pelajaran,
                    'lokasi' => $jdw->ruangan.' ('.$jdw->kelompok.')',
                    'guru' => $jdw->guru_pengampu,
                ];
            }
        }

        // 6. Data Pengajar Kelompok Belajar (Sinkron dari Menu 2 Musyrifah)
        $daftarPengajarKelompok = KelompokPengajar::orderBy('id')->get()->map(function ($p) use ($nomorKelompokSaya) {
            $isMengajarKelompokSaya = (bool) preg_match('/\b'.$nomorKelompokSaya.'\b/', (string) $p->kelompok_teks);

            return [
                'id' => $p->id,
                'nama' => $p->nama_pengajar,
                'program' => $p->program,
                'kelompok_teks' => $p->kelompok_teks,
                'jumlah_tm' => $p->jumlah_tm,
                'keterangan' => $p->keterangan,
                'is_kelompok_saya' => $isMengajarKelompokSaya,
            ];
        });

        // 7. Event Kalender Dinamis
        $calendarEvents = [];
        $classDays = $jadwalSaya->pluck('hari')->unique()->toArray();

        // Cari tanggal-tanggal dalam bulan berjalan dan bulan berikutnya yang sesuai hari jadwal
        $startDate = Carbon::now()->startOfMonth()->subDays(7);
        $endDate = Carbon::now()->endOfMonth()->addDays(14);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $hariDate = $hariMap[$date->dayOfWeek];
            if (in_array($hariDate, $classDays, true)) {
                $matchingClasses = $jadwalSaya->where('hari', $hariDate)->values();
                $calendarEvents[$date->format('Y-m-d')] = $matchingClasses->map(function ($item) {
                    return [
                        'nama' => $item->nama_mata_pelajaran,
                        'waktu' => $item->jam_mulai.'–'.$item->jam_selesai.' WIB',
                        'ruangan' => $item->ruangan,
                        'guru' => $item->guru_pengampu,
                    ];
                })->toArray();
            }
        }

        return view('mahasiswa.pembelajaran_mhs', compact(
            'user',
            'kelompokSaya',
            'nomorKelompokSaya',
            'daftarKelompok',
            'daftarMataPelajaran',
            'totalMataPelajaran',
            'totalTargetKehadiran',
            'totalHadirSeluruh',
            'mapelBelumLulus',
            'isLulus',
            'statusKelulusanLabel',
            'persentaseKelulusan',
            'nomorSertifikat',
            'jadwalSaya',
            'semuaJadwal',
            'jadwalTerdekat',
            'daftarPengajarKelompok',
            'calendarEvents'
        ));
    }

    /**
     * Detail kehadiran mahasiswa pada satu mata pelajaran (AJAX/JSON).
     *
     * Riwayat, total, dan persentase dihitung dari data absensi yang sudah
     * ada dan selalu dibatasi pada mahasiswa yang sedang login.
     */
    public function detailPertemuan(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $mapel = MataPelajaran::findOrFail($id);

        $absensi = AbsensiPembelajaran::with('pertemuan')
            ->where('user_id', $user?->id)
            ->where('mata_pelajaran_id', $mapel->id)
            ->orderBy('pertemuan_ke')
            ->get();

        // Data lama tanpa pertemuan_id tetap mendapat periode dari jadwal kelompok.
        $kelompok = $absensi->first()?->kelompok
            ?: AbsensiPembelajaran::where('user_id', $user?->id)->value('kelompok');

        $jadwal = JadwalPembelajaran::where('mata_pelajaran_id', $mapel->id)
            ->when(filled($kelompok), fn ($query) => $query->where('kelompok', $kelompok))
            ->first()
            ?: JadwalPembelajaran::where('mata_pelajaran_id', $mapel->id)->first();

        $periodeDefault = trim((string) $jadwal?->periode) ?: 'Semester Ganjil 2026/2027';

        $periodeAbsensi = function (AbsensiPembelajaran $item) use ($periodeDefault): string {
            return trim((string) $item->pertemuan?->periode) ?: $periodeDefault;
        };

        $target = (int) ($mapel->target_pertemuan ?: 16);
        $target = $target > 0 ? $target : 16;

        $absensiPerPertemuan = $absensi->groupBy('pertemuan_ke');

        $pertemuanList = [];
        for ($p = 1; $p <= $target; $p++) {
            $item = $absensiPerPertemuan->get($p)?->first();
            $status = strtolower((string) ($item?->status ?: 'terjadwal'));

            $tanggal = $item?->tanggal
                ? Carbon::parse($item->tanggal)->translatedFormat('d M Y')
                : 'Minggu ke-'.$p;

            $catatan = $item?->catatan
                ?: ($item ? 'Pertemuan terlaksana' : 'Terjadwal tatap muka');

            $pertemuanList[] = [
                'pertemuan_ke' => $p,
                'tanggal' => $tanggal,
                'status' => $status,
                'catatan' => $catatan,
                'periode' => $item ? $periodeAbsensi($item) : $periodeDefault,
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
                'id' => $mapel->id,
                'nama' => $mapel->nama,
                'kode' => $mapel->kode ?: 'PKK',
                'kategori' => $mapel->kategori ?: 'Program Ma\'had',
                'target_pertemuan' => $target,
                'hadir' => $ringkasan['hadir'],
                'is_lulus' => ($ringkasan['hadir'] >= $target),
                'persentase' => $ringkasan['persentase'],
                'periode' => $periodeDefault,
            ],
            'ringkasan' => $ringkasan + ['target' => $target],
            'periode' => $ringkasanPeriode,
            'pertemuan' => $pertemuanList,
        ]);
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
     * Memastikan data master mata pelajaran, kelompok, dan jadwal tersedia di database.
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
}
