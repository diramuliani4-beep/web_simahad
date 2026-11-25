<?php

namespace App\Services;

use App\Models\AbsensiPembelajaran;
use App\Models\LaporanPembelajaran;
use App\Models\MataPelajaran;
use App\Models\PertemuanPembelajaran;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Laporan pembelajaran per mata pelajaran.
 *
 * Pertemuan Manajemen Pembelajaran tetap menjadi sumber data utama. Baris ini
 * hanya menambah catatan laporan yang dapat dikelola Musyrifah tanpa mengubah
 * jadwal, absensi, tugas, maupun pertemuan asli.
 */
class LaporanPembelajaranService
{
    /** Urutan mata pelajaran mengikuti daftar program Ma'had. */
    public const NAMA_MATA_PELAJARAN = [
        'Pembelajaran Kitab Kuning',
        'Penguatan Keterampilan Khusus Wawasan Interdisipliner dan Moderasi Beragama',
        'Penguatan Keterampilan Khusus',
        'Penguatan Bahasa Asing',
        'Penguatan Keterampilan Khusus (Karakter & Dakwah)',
        'Bahasa Arab',
    ];

    public function __construct(private readonly AuditTrailService $audit) {}

    /**
     * Mata pelajaran wajib selalu tampil, termasuk yang belum memiliki jadwal.
     *
     * @return Collection<int, MataPelajaran>
     */
    public function daftarMataPelajaran(): Collection
    {
        $namaWajib = collect(self::NAMA_MATA_PELAJARAN);

        return MataPelajaran::query()
            ->orderBy('nama')
            ->get()
            ->sortBy(fn (MataPelajaran $mapel): int => $namaWajib->search($mapel->nama) === false
                ? 999
                : $namaWajib->search($mapel->nama))
            ->values();
    }

    public function mataPelajaran(int $id): MataPelajaran
    {
        return MataPelajaran::query()->findOrFail($id);
    }

    /**
     * Pertemuan satu mata pelajaran, opsional satu kelompok dan periode semester.
     *
     * @return Collection<int, PertemuanPembelajaran>
     */
    public function daftarPertemuan(int $mataPelajaranId, string $kelompok = '', string $periode = ''): Collection
    {
        return PertemuanPembelajaran::query()
            ->with([
                'jadwal' => fn ($query) => $query->select('id', 'mata_pelajaran_id', 'kelompok', 'periode', 'guru_pengampu', 'guru_id'),
                'guru:id,name',
                'laporanPembelajaran',
            ])
            ->withCount(['dokumentasi', 'tugas'])
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->when($kelompok !== '', fn (Builder $query) => $query->where('kelompok', $kelompok))
            ->when($periode !== '', fn (Builder $query) => $query->where('periode', $periode))
            ->orderBy('tanggal')
            ->orderBy('pertemuan_ke')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array{periode: string, kelompok: string, cari: string, rentang: array{0: string, 1: string}|null}
     */
    public function normalisasiFilter(string $periode, string $kelompok, string $cari): array
    {
        $periode = trim($periode);
        $kelompok = trim($kelompok);

        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode) === 1) {
            $bulan = CarbonImmutable::createFromFormat('Y-m-d', $periode.'-01');
            $rentang = [$bulan->toDateString(), $bulan->endOfMonth()->toDateString()];
        } else {
            $periode = '';
            $rentang = null;
        }

        $cari = trim($cari);

        return compact('periode', 'kelompok', 'cari') + ['rentang' => $rentang];
    }

    /**
     * Build report rows without copying operational learning data.
     *
     * @return array<int, array{
     *     id: string, jenis: string, pertemuan_id: int|null, laporan_id: int|null,
     *     hari: string, tanggal: string, tanggal_iso: string, materi: string,
     *     tanda_tangan: string, catatan: string|null, kelompok: string,
     *     periode_semester: string, instruktur: string, pertemuan_ke: int|null,
     *     hadir: int, izin: int, sakit: int, alpa: int, total_absensi: int,
     *     jumlah_tugas: int, jumlah_dokumentasi: int, status: string,
     *     terlaksana: bool, sinkron: bool, dapat_dikelola: bool
     * }>
     */
    public function barisLaporan(int $mataPelajaranId, string $kelompok = '', string $periode = '', string $cari = ''): array
    {
        $filter = $this->normalisasiFilter($periode, $kelompok, $cari);
        $kelompok = $filter['kelompok'];
        $cari = $filter['cari'];

        $pertemuan = $this->daftarPertemuan($mataPelajaranId);
        $pertemuanId = $pertemuan->pluck('id');
        [$absensiTerhubung, $absensiLama] = $this->dataAbsensi($mataPelajaranId, $pertemuanId);

        $manual = LaporanPembelajaran::query()
            ->with('pertemuan:id,mata_pelajaran_id,kelompok,periode,pertemuan_ke,tanggal')
            ->untukMataPelajaran($mataPelajaranId)
            ->whereNull('pertemuan_id')
            ->when($kelompok !== '', fn (Builder $query) => $query->where('kelompok', $kelompok))
            ->when($filter['rentang'] !== null, fn (Builder $query) => $query->whereBetween(
                'tanggal',
                [$filter['rentang'][0], $filter['rentang'][1]]
            ))
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $barisPertemuan = $pertemuan->map(function (PertemuanPembelajaran $item) use ($absensiTerhubung, $absensiLama): array {
            $kunciAbsensi = $this->kunciAbsensi(
                $item->kelompok,
                (int) $item->pertemuan_ke,
                $item->tanggal?->toDateString() ?? '',
            );
            $daftarAbsensi = $absensiTerhubung->get($item->id, collect());

            if ($daftarAbsensi->isEmpty()) {
                $daftarAbsensi = $absensiLama->get($kunciAbsensi, collect());
            }

            $ringkasanAbsensi = $this->ringkasanStatusAbsensi($daftarAbsensi);
            $laporan = $item->laporanPembelajaran->firstWhere('mata_pelajaran_id', $item->mata_pelajaran_id);
            $instruktur = $item->guru?->name ?: ($item->jadwal?->guru_pengampu ?: null);
            $materi = $item->materi ?: ($laporan?->materi ?: 'Materi belum diisi');

            return [
                'id' => 'pertemuan-'.$item->id,
                'jenis' => 'otomatis',
                'pertemuan_id' => (int) $item->id,
                'laporan_id' => $laporan?->id,
                'hari' => $item->tanggal?->locale('id')->translatedFormat('l') ?? '-',
                'tanggal' => $item->tanggalTeks(),
                'tanggal_iso' => $item->tanggal?->toDateString() ?? '',
                'materi' => $materi,
                'tanda_tangan' => (string) ($laporan?->tanda_tangan ?? ''),
                'catatan' => $laporan?->catatan,
                'kelompok' => $item->kelompok,
                'periode_semester' => $item->periode,
                'instruktur' => $instruktur ?: 'Pengajar Ma’had',
                'pertemuan_ke' => (int) $item->pertemuan_ke,
                'hadir' => $ringkasanAbsensi['hadir'],
                'izin' => $ringkasanAbsensi['izin'],
                'sakit' => $ringkasanAbsensi['sakit'],
                'alpa' => $ringkasanAbsensi['alpa'],
                'total_absensi' => $ringkasanAbsensi['total'],
                'jumlah_tugas' => (int) $item->tugas_count,
                'jumlah_dokumentasi' => (int) $item->dokumentasi_count,
                'status' => $item->labelStatus(),
                'terlaksana' => $ringkasanAbsensi['total'] > 0 || $item->sudahSelesai(),
                'sinkron' => true,
                'dapat_dikelola' => $laporan !== null,
            ];
        });

        $kunciTerpakai = $pertemuan->map(
            fn (PertemuanPembelajaran $item): string => $this->kunciAbsensi(
                $item->kelompok,
                (int) $item->pertemuan_ke,
                $item->tanggal?->toDateString() ?? '',
            )
        );

        $barisAbsensiLama = $absensiLama
            ->reject(fn (Collection $daftar, string $kunci): bool => $kunciTerpakai->contains($kunci))
            ->map(function (Collection $daftar, string $kunci): array {
                $contoh = $daftar->first();
                $ringkasanAbsensi = $this->ringkasanStatusAbsensi($daftar);

                return [
                    'id' => 'absensi-'.md5($kunci),
                    'jenis' => 'otomatis',
                    'pertemuan_id' => null,
                    'laporan_id' => null,
                    'hari' => $contoh->tanggal?->locale('id')->translatedFormat('l') ?? '-',
                    'tanggal' => $contoh->tanggal?->locale('id')->translatedFormat('d F Y') ?? '-',
                    'tanggal_iso' => $contoh->tanggal?->toDateString() ?? '',
                    'materi' => 'Materi belum diisi',
                    'tanda_tangan' => '',
                    'catatan' => null,
                    'kelompok' => filled($contoh->kelompok) ? $contoh->kelompok : 'Tanpa Kelompok',
                    'periode_semester' => '-',
                    'instruktur' => 'Pengajar Ma’had',
                    'pertemuan_ke' => (int) $contoh->pertemuan_ke,
                    'hadir' => $ringkasanAbsensi['hadir'],
                    'izin' => $ringkasanAbsensi['izin'],
                    'sakit' => $ringkasanAbsensi['sakit'],
                    'alpa' => $ringkasanAbsensi['alpa'],
                    'total_absensi' => $ringkasanAbsensi['total'],
                    'jumlah_tugas' => 0,
                    'jumlah_dokumentasi' => 0,
                    'status' => 'Absensi tercatat',
                    'terlaksana' => true,
                    'sinkron' => true,
                    'dapat_dikelola' => false,
                ];
            });

        $barisManual = $manual->map(function (LaporanPembelajaran $item): array {
            $pertemuanTerkait = $item->pertemuan;

            return [
                'id' => 'laporan-'.$item->id,
                'jenis' => 'manual',
                'pertemuan_id' => null,
                'laporan_id' => (int) $item->id,
                'hari' => $item->labelHari(),
                'tanggal' => $item->labelTanggal(),
                'tanggal_iso' => $item->tanggal?->toDateString() ?? '',
                'materi' => (string) $item->materi,
                'tanda_tangan' => (string) ($item->tanda_tangan ?? ''),
                'catatan' => $item->catatan,
                'kelompok' => $item->kelompok ?: ($pertemuanTerkait?->kelompok ?: 'Tanpa Kelompok'),
                'periode_semester' => $item->periode_semester ?: ($pertemuanTerkait?->periode ?: '-'),
                'instruktur' => $item->instruktur ?: 'Instruktur',
                'pertemuan_ke' => $pertemuanTerkait?->pertemuan_ke,
                'hadir' => (int) $item->jumlah_hadir,
                'izin' => (int) $item->jumlah_izin,
                'sakit' => (int) $item->jumlah_sakit,
                'alpa' => (int) $item->jumlah_alpa,
                'total_absensi' => (int) ($item->jumlah_hadir + $item->jumlah_izin + $item->jumlah_sakit + $item->jumlah_alpa),
                'jumlah_tugas' => 0,
                'jumlah_dokumentasi' => 0,
                'status' => 'Laporan manual',
                'terlaksana' => true,
                'sinkron' => false,
                'dapat_dikelola' => true,
            ];
        });

        $semuaBaris = $barisPertemuan->concat($barisAbsensiLama)->concat($barisManual)
            ->when($kelompok !== '', fn (Collection $items): Collection => $items->where('kelompok', $kelompok))
            ->when($filter['rentang'] !== null, fn (Collection $items): Collection => $items->filter(
                fn (array $item): bool => $item['tanggal_iso'] >= $filter['rentang'][0]
                    && $item['tanggal_iso'] <= $filter['rentang'][1]
            ))
            ->when($cari !== '', fn (Collection $items): Collection => $items->filter(
                fn (array $item): bool => str_contains(mb_strtolower(implode(' ', [
                    $item['materi'], $item['instruktur'], $item['kelompok'], $item['catatan'] ?? '',
                ])), mb_strtolower($cari))
            ))
            ->sort(fn (array $a, array $b): int => ($a['tanggal_iso'] <=> $b['tanggal_iso'])
                ?: (($a['pertemuan_ke'] ?? 999) <=> ($b['pertemuan_ke'] ?? 999))
                ?: ((int) ($a['jenis'] === 'manual') <=> (int) ($b['jenis'] === 'manual')))
            ->values();

        return $semuaBaris->all();
    }

    /**
     * @param  Collection<int, int>  $pertemuanId
     * @return array{
     *     0: Collection<int, Collection<int, AbsensiPembelajaran>>,
     *     1: Collection<string, Collection<int, AbsensiPembelajaran>>
     * }
     */
    private function dataAbsensi(int $mataPelajaranId, Collection $pertemuanId): array
    {
        $terhubung = $pertemuanId->isEmpty()
            ? collect()
            : AbsensiPembelajaran::query()
                ->whereIn('pertemuan_id', $pertemuanId)
                ->selectRaw('pertemuan_id, status, COUNT(*) as jumlah')
                ->groupBy('pertemuan_id', 'status')
                ->get()
                ->groupBy('pertemuan_id');

        $lama = AbsensiPembelajaran::query()
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->whereNull('pertemuan_id')
            ->selectRaw('kelompok, pertemuan_ke, tanggal, status, COUNT(*) as jumlah')
            ->groupBy('kelompok', 'pertemuan_ke', 'tanggal', 'status')
            ->get()
            ->groupBy(fn (AbsensiPembelajaran $item): string => $this->kunciAbsensi(
                $item->kelompok,
                (int) $item->pertemuan_ke,
                $item->tanggal?->toDateString() ?? '',
            ));

        return [$terhubung, $lama];
    }

    private function kunciAbsensi(?string $kelompok, int $pertemuanKe, string $tanggal): string
    {
        return $kelompok.'|'.$pertemuanKe.'|'.$tanggal;
    }

    /**
     * @param  Collection<int, AbsensiPembelajaran>  $daftar
     * @return array{hadir: int, izin: int, sakit: int, alpa: int, total: int}
     */
    private function ringkasanStatusAbsensi(Collection $daftar): array
    {
        return [
            'hadir' => (int) $daftar->where('status', 'hadir')->sum('jumlah'),
            'izin' => (int) $daftar->where('status', 'izin')->sum('jumlah'),
            'sakit' => (int) $daftar->where('status', 'sakit')->sum('jumlah'),
            'alpa' => (int) $daftar->where('status', 'alpa')->sum('jumlah'),
            'total' => (int) $daftar->sum('jumlah'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function ringkasan(int $mataPelajaranId, array $baris): array
    {
        $otomatis = collect($baris)->where('jenis', 'otomatis');
        $terlaksana = collect($baris)->where('terlaksana', true);
        $totalAbsensi = (int) $terlaksana->sum('total_absensi');
        $totalHadir = (int) $terlaksana->sum('hadir');

        return [
            'jumlah_pertemuan' => $otomatis->count(),
            'pertemuan_terlaksana' => $otomatis->where('terlaksana', true)->count(),
            'laporan_manual' => collect($baris)->where('jenis', 'manual')->count(),
            'jumlah_materi' => $otomatis->filter(fn (array $item): bool => $item['materi'] !== 'Materi belum diisi')->count(),
            'persentase_kehadiran' => $totalAbsensi > 0 ? round($totalHadir / $totalAbsensi * 100, 1) : 0,
            'total_absensi' => $totalAbsensi,
            'total_hadir' => $totalHadir,
            'total_tugas' => (int) $otomatis->sum('jumlah_tugas'),
            'total_dokumentasi' => (int) $otomatis->sum('jumlah_dokumentasi'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function aturanValidasi(int $mataPelajaranId, ?int $laporanId = null): array
    {
        return [
            'mata_pelajaran_id' => ['required', 'integer', 'in:'.$mataPelajaranId],
            'pertemuan_id' => [
                'nullable',
                'integer',
                Rule::exists('pertemuan_pembelajarans', 'id')->where('mata_pelajaran_id', $mataPelajaranId),
                function (string $attribute, mixed $value, \Closure $fail) use ($laporanId): void {
                    if (filled($value) && LaporanPembelajaran::query()
                        ->where('pertemuan_id', $value)
                        ->when($laporanId, fn (Builder $query) => $query->whereKeyNot($laporanId))
                        ->exists()) {
                        $fail('Pertemuan ini sudah memiliki laporan. Gunakan fitur Edit pada laporan tersebut.');
                    }
                },
            ],
            'tanggal' => ['nullable', 'required_without:pertemuan_id', 'date'],
            'kelompok' => ['nullable', 'string', 'max:60'],
            'periode_semester' => ['nullable', 'string', 'max:100'],
            'materi' => ['nullable', 'required_without:pertemuan_id', 'string', 'max:5000'],
            'instruktur' => ['nullable', 'string', 'max:255'],
            'jumlah_hadir' => ['nullable', 'integer', 'min:0', 'max:500'],
            'jumlah_izin' => ['nullable', 'integer', 'min:0', 'max:500'],
            'jumlah_sakit' => ['nullable', 'integer', 'min:0', 'max:500'],
            'jumlah_alpa' => ['nullable', 'integer', 'min:0', 'max:500'],
            'tanda_tangan' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function simpan(User $pengguna, array $data, ?LaporanPembelajaran $laporan = null): LaporanPembelajaran
    {
        if (filled($data['pertemuan_id'] ?? null)) {
            $pertemuan = PertemuanPembelajaran::query()
                ->with(['guru:id,name', 'jadwal:id,guru_pengampu'])
                ->findOrFail((int) $data['pertemuan_id']);

            $data['tanggal'] = $pertemuan->tanggal->toDateString();
            $data['kelompok'] = $pertemuan->kelompok;
            $data['periode_semester'] = $pertemuan->periode;
            $data['materi'] = $pertemuan->materi ?: ($data['materi'] ?? 'Materi belum diisi');
            $data['instruktur'] = $pertemuan->guru?->name
                ?: ($pertemuan->jadwal?->guru_pengampu ?: ($data['instruktur'] ?? null));
        }

        $data['materi'] = trim((string) ($data['materi'] ?? 'Materi belum diisi'));
        $data['tanda_tangan'] = filled($data['tanda_tangan'] ?? null) ? trim((string) $data['tanda_tangan']) : null;
        $sebelum = $laporan?->getAttributes() ?? [];
        $namaMapel = MataPelajaran::query()->findOrFail((int) $data['mata_pelajaran_id'])->nama;

        $laporan = DB::transaction(function () use ($pengguna, $data, $laporan): LaporanPembelajaran {
            $laporan ??= new LaporanPembelajaran;
            $laporan->fill($data);
            $laporan->dibuat_oleh ??= $pengguna->id;
            $laporan->save();

            return $laporan;
        });

        $this->audit->catat(
            'pembelajaran',
            $sebelum === [] ? 'laporan_pembelajaran_ditambahkan' : 'laporan_pembelajaran_diperbarui',
            'Laporan Pembelajaran '.$namaMapel,
            (int) $laporan->id,
            $sebelum === [] ? 'Laporan pembelajaran ditambahkan.' : 'Laporan pembelajaran diperbarui.',
            $pengguna,
            $sebelum,
            $laporan->getAttributes(),
        );

        return $laporan;
    }

    public function hapus(LaporanPembelajaran $laporan, User $pengguna): void
    {
        $sebelum = $laporan->getAttributes();
        $namaMapel = MataPelajaran::query()->findOrFail((int) $laporan->mata_pelajaran_id)->nama;
        $label = 'Laporan Pembelajaran '.$namaMapel;
        $id = (int) $laporan->id;

        DB::transaction(fn () => $laporan->delete());

        $this->audit->catat(
            'pembelajaran',
            'laporan_pembelajaran_dihapus',
            $label,
            $id,
            'Laporan pembelajaran dihapus.',
            $pengguna,
            $sebelum,
            [],
        );
    }
}
