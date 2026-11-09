<?php

namespace App\Services;

use App\Models\AbsensiPembelajaran;
use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Sumber data tunggal untuk laporan Ma'had.
 *
 * Dipakai bersama oleh laporan Musyrifah (operasional) dan laporan Pimpinan
 * (monitoring) agar angka yang ditampilkan selalu sinkron.
 */
class LaporanMahadService
{
    /** @var array<int, string> */
    public const JENIS_MUSYRIFAH = ['kehadiran', 'pembayaran', 'pendaftaran', 'kamar'];

    /** @var array<int, string> */
    public const JENIS_PIMPINAN = ['penghuni', 'kamar', 'kehadiran', 'pembayaran', 'pendaftaran'];

    /** @var array<int, string> */
    private const NAMA_BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Kategori laporan yang ditampilkan pada menu Laporan Musyrifah.
     *
     * @return array<int, array{jenis: string, judul: string, deskripsi: string}>
     */
    public function kategoriMusyrifah(): array
    {
        return [
            [
                'jenis' => 'kehadiran',
                'judul' => 'Laporan Kehadiran',
                'deskripsi' => 'Rekap absensi pembelajaran per mahasiswa pada periode terpilih.',
            ],
            [
                'jenis' => 'pembayaran',
                'judul' => 'Laporan Pembayaran',
                'deskripsi' => 'Status pembayaran, jumlah dibayar, dan sisa tagihan pada periode terpilih.',
            ],
            [
                'jenis' => 'pendaftaran',
                'judul' => 'Laporan Pendaftaran',
                'deskripsi' => 'Rekap data pendaftar dan status pendaftaran pada periode terpilih.',
            ],
            [
                'jenis' => 'kamar',
                'judul' => 'Laporan Data Kamar',
                'deskripsi' => 'Rekap kapasitas kamar dan jumlah penghuni saat ini.',
            ],
        ];
    }

    /**
     * Memastikan periode berformat Y-m dan berada pada rentang wajar.
     */
    public function normalisasiPeriode(?string $periode): string
    {
        if (is_string($periode) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode) === 1) {
            return $periode;
        }

        return now()->format('Y-m');
    }

    /**
     * Label periode berbahasa Indonesia, misalnya "November 2026".
     */
    public function labelPeriode(string $periode): string
    {
        $tanggal = CarbonImmutable::createFromFormat('Y-m-d', $periode.'-01');

        return self::NAMA_BULAN[(int) $tanggal->month].' '.$tanggal->year;
    }

    /**
     * Opsi periode untuk filter laporan.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function opsiPeriode(): array
    {
        $tahunSekarang = (int) now()->year;
        $opsi = [];

        for ($tahun = $tahunSekarang - 3; $tahun <= $tahunSekarang + 1; $tahun++) {
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $opsi[] = [
                    'value' => sprintf('%04d-%02d', $tahun, $bulan),
                    'label' => self::NAMA_BULAN[$bulan].' '.$tahun,
                ];
            }
        }

        return $opsi;
    }

    /**
     * Menyusun satu laporan siap tampil.
     *
     * @return array<string, mixed>
     */
    public function laporan(string $jenis, string $periode, string $keyword = ''): array
    {
        $periode = $this->normalisasiPeriode($periode);
        $keyword = trim($keyword);

        $laporan = match ($jenis) {
            'kehadiran' => $this->laporanKehadiran($periode, $keyword),
            'pembayaran' => $this->laporanPembayaran($periode, $keyword),
            'pendaftaran' => $this->laporanPendaftaran($periode, $keyword),
            'kamar' => $this->laporanKamar($keyword),
            'penghuni' => $this->laporanPenghuni($keyword),
            default => abort(404),
        };

        return array_merge([
            'jenis' => $jenis,
            'periode' => $periode,
            'periode_label' => $this->labelPeriode($periode),
        ], $laporan);
    }

    /**
     * @return array<string, mixed>
     */
    private function laporanKehadiran(string $periode, string $keyword): array
    {
        [$mulai, $akhir] = $this->rentangBulan($periode);

        $absensi = AbsensiPembelajaran::query()
            ->with(['user' => fn ($query) => $query->select('id', 'name', 'nim')])
            ->whereBetween('tanggal', [$mulai->toDateString(), $akhir->toDateString()])
            ->get();

        $rekap = $absensi
            ->groupBy('user_id')
            ->map(function (Collection $items): array {
                /** @var AbsensiPembelajaran|null $contoh */
                $contoh = $items->first();
                $user = $contoh?->user;

                $jumlahStatus = $items->countBy(
                    fn (AbsensiPembelajaran $absen): string => strtolower((string) $absen->status)
                );

                $total = $items->count();
                $hadir = (int) ($jumlahStatus['hadir'] ?? 0);

                return [
                    'nim' => $user?->nim ?: '-',
                    'nama' => $user?->name ?? 'Mahasiswa',
                    'total' => $total,
                    'hadir' => $hadir,
                    'izin' => (int) ($jumlahStatus['izin'] ?? 0),
                    'sakit' => (int) ($jumlahStatus['sakit'] ?? 0),
                    'alpa' => (int) ($jumlahStatus['alpa'] ?? 0),
                    'persentase' => $total > 0 ? (int) round($hadir / $total * 100) : 0,
                ];
            })
            ->values();

        $rekap = $this->saring($rekap, $keyword, ['nama', 'nim'])
            ->sortBy('nama')
            ->values();

        $totalMahasiswa = $rekap->count();
        $totalPertemuan = (int) $rekap->sum('total');
        $totalHadir = (int) $rekap->sum('hadir');
        $rataKehadiran = $totalPertemuan > 0 ? (int) round($totalHadir / $totalPertemuan * 100) : 0;

        $baris = $rekap->map(function (array $item): array {
            $item['persentase'] = $item['persentase'].'%';

            return $item;
        })->all();

        return [
            'judul' => 'Laporan Kehadiran',
            'deskripsi' => 'Rekap absensi pembelajaran mahasiswa pada periode '.$this->labelPeriode($periode).'.',
            'judul_tabel' => 'Rekap Kehadiran Mahasiswa',
            'placeholder_cari' => 'Cari NIM atau nama mahasiswa...',
            'ringkasan' => [
                ['label' => 'Total Mahasiswa', 'nilai' => $totalMahasiswa],
                ['label' => 'Total Pertemuan', 'nilai' => $totalPertemuan],
                ['label' => 'Total Hadir', 'nilai' => $totalHadir],
                ['label' => 'Rata-rata Kehadiran', 'nilai' => $rataKehadiran.'%'],
            ],
            'kolom' => [
                ['key' => 'nim', 'label' => 'NIM', 'class' => 'nim'],
                ['key' => 'nama', 'label' => 'Nama Mahasiswa'],
                ['key' => 'total', 'label' => 'Total Pertemuan', 'align' => 'center'],
                ['key' => 'hadir', 'label' => 'Hadir', 'align' => 'center'],
                ['key' => 'izin', 'label' => 'Izin', 'align' => 'center'],
                ['key' => 'sakit', 'label' => 'Sakit', 'align' => 'center'],
                ['key' => 'alpa', 'label' => 'Alpa', 'align' => 'center'],
                ['key' => 'persentase', 'label' => 'Kehadiran', 'align' => 'center'],
            ],
            'baris' => $baris,
            'total' => count($baris),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function laporanPembayaran(string $periode, string $keyword): array
    {
        [$mulai, $akhir] = $this->rentangBulan($periode);
        $labelPeriode = $this->labelPeriode($periode);

        $semuaPembayaran = Pembayaran::query()
            ->with(['user' => fn ($query) => $query->select('id', 'name', 'nim')])
            ->where(function ($query) use ($mulai, $akhir, $labelPeriode): void {
                $query->whereBetween('created_at', [$mulai, $akhir])
                    ->orWhere('periode', $labelPeriode)
                    ->orWhereBetween('tanggal_bayar', [$mulai, $akhir]);
            })
            ->orderBy('created_at')
            ->get()
            ->map(function (Pembayaran $item): array {
                $user = $item->user;

                return [
                    'nim' => $user?->nim ?: '-',
                    'nama' => $user?->name ?? 'Mahasiswa',
                    'tagihan' => $item->nama_tagihan ?: '-',
                    'periode' => $item->periode ?: '-',
                    'nominal' => $this->formatRupiah((float) $item->nominal),
                    'tanggal' => $item->tanggal_bayar ? $this->formatTanggal($item->tanggal_bayar) : 'Belum dibayar',
                    'status' => $item->status_label,
                ];
            });

        $totalTagihan = $semuaPembayaran->count();
        $totalLunas = $semuaPembayaran->where('status', 'Lunas')->count();
        $totalMenunggu = $semuaPembayaran->where('status', 'Menunggu')->count();
        $totalBelumLunas = $semuaPembayaran->where('status', 'Belum Lunas')->count();

        $baris = $this->saring($semuaPembayaran, $keyword, ['nama', 'nim'])->values()->all();

        return [
            'judul' => 'Laporan Pembayaran',
            'deskripsi' => 'Rekap pembayaran mahasiswa pada periode '.$this->labelPeriode($periode).'.',
            'judul_tabel' => 'Data Pembayaran Mahasiswa',
            'placeholder_cari' => 'Cari NIM atau nama mahasiswa...',
            'ringkasan' => [
                ['label' => 'Total Tagihan', 'nilai' => $totalTagihan],
                ['label' => 'Lunas', 'nilai' => $totalLunas],
                ['label' => 'Menunggu', 'nilai' => $totalMenunggu],
                ['label' => 'Belum Lunas', 'nilai' => $totalBelumLunas],
            ],
            'kolom' => [
                ['key' => 'nim', 'label' => 'NIM', 'class' => 'nim'],
                ['key' => 'nama', 'label' => 'Nama Mahasiswa'],
                ['key' => 'tagihan', 'label' => 'Jenis Tagihan'],
                ['key' => 'periode', 'label' => 'Periode'],
                ['key' => 'nominal', 'label' => 'Nominal'],
                ['key' => 'tanggal', 'label' => 'Tanggal Bayar'],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'badge' => [
                        'Lunas' => 'lunas',
                        'Menunggu' => 'menunggu',
                        'Belum Lunas' => 'belum',
                    ],
                ],
            ],
            'baris' => $baris,
            'total' => count($baris),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function laporanPendaftaran(string $periode, string $keyword): array
    {
        [$mulai, $akhir] = $this->rentangBulan($periode);

        $semuaPendaftaran = Pendaftaran::query()
            ->with(['user' => fn ($query) => $query->select('id', 'name', 'nim')])
            ->whereBetween('created_at', [$mulai, $akhir])
            ->orderBy('created_at')
            ->get()
            ->map(function (Pendaftaran $item): array {
                return [
                    'tanggal' => $this->formatTanggal($item->created_at),
                    'nama' => $item->nama_lengkap ?: ($item->user?->name ?? '-'),
                    'nim' => $item->nim ?: ($item->user?->nim ?: '-'),
                    'jenis_kelamin' => $item->jenis_kelamin ?: '-',
                    'program_studi' => $item->program_studi ?: '-',
                    'angkatan' => $item->tahun_angkatan ?: '-',
                    'status' => $this->labelStatusPendaftaran($item->status),
                ];
            });

        $totalPendaftar = $semuaPendaftaran->count();
        $totalMenunggu = $semuaPendaftaran->where('status', 'Menunggu')->count();
        $totalDisetujui = $semuaPendaftaran->where('status', 'Disetujui')->count();
        $totalDitolak = $semuaPendaftaran->where('status', 'Ditolak')->count();

        $baris = $this->saring($semuaPendaftaran, $keyword, ['nama', 'nim'])->values()->all();

        return [
            'judul' => 'Laporan Pendaftaran',
            'deskripsi' => 'Rekap pendaftar Ma’had pada periode '.$this->labelPeriode($periode).'.',
            'judul_tabel' => 'Data Pendaftar',
            'placeholder_cari' => 'Cari NIM atau nama pendaftar...',
            'ringkasan' => [
                ['label' => 'Total Pendaftar', 'nilai' => $totalPendaftar],
                ['label' => 'Menunggu', 'nilai' => $totalMenunggu],
                ['label' => 'Disetujui', 'nilai' => $totalDisetujui],
                ['label' => 'Ditolak', 'nilai' => $totalDitolak],
            ],
            'kolom' => [
                ['key' => 'tanggal', 'label' => 'Tanggal Daftar'],
                ['key' => 'nama', 'label' => 'Nama Pendaftar'],
                ['key' => 'nim', 'label' => 'NIM', 'class' => 'nim'],
                ['key' => 'jenis_kelamin', 'label' => 'Jenis Kelamin'],
                ['key' => 'program_studi', 'label' => 'Program Studi'],
                ['key' => 'angkatan', 'label' => 'Angkatan', 'align' => 'center'],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'badge' => [
                        'Menunggu' => 'menunggu',
                        'Disetujui' => 'disetujui',
                        'Ditolak' => 'ditolak',
                        'Perlu Perbaikan' => 'perbaikan',
                    ],
                ],
            ],
            'baris' => $baris,
            'total' => count($baris),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function laporanKamar(string $keyword): array
    {
        $semuaKamar = Kamar::query()
            ->withCount('users')
            ->orderBy('gedung')
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get()
            ->map(function (Kamar $kamar): array {
                $kapasitas = (int) $kamar->kapasitas;
                $terisi = (int) $kamar->users_count;

                return [
                    'gedung' => $kamar->gedung ?: 'Gedung A',
                    'nomor' => $kamar->nomor_kamar ?: '-',
                    'lantai' => (int) $kamar->lantai,
                    'kapasitas' => $kapasitas,
                    'terisi' => $terisi,
                    'sisa' => max(0, $kapasitas - $terisi),
                    'status' => $this->statusKamar($kamar, $terisi, $kapasitas),
                ];
            });

        $totalKamar = $semuaKamar->count();
        $totalKapasitas = (int) $semuaKamar->sum('kapasitas');
        $totalTerisi = (int) $semuaKamar->sum('terisi');
        $totalSisa = (int) $semuaKamar->sum('sisa');

        $baris = $this->saring($semuaKamar, $keyword, ['nomor', 'gedung'])->values()->all();

        return [
            'judul' => 'Laporan Data Kamar',
            'deskripsi' => 'Rekap kapasitas kamar dan jumlah penghuni saat ini.',
            'judul_tabel' => 'Data Kamar Ma’had',
            'placeholder_cari' => 'Cari nomor kamar atau gedung...',
            'ringkasan' => [
                ['label' => 'Total Kamar', 'nilai' => $totalKamar],
                ['label' => 'Total Kapasitas', 'nilai' => $totalKapasitas],
                ['label' => 'Total Terisi', 'nilai' => $totalTerisi],
                ['label' => 'Sisa Slot', 'nilai' => $totalSisa],
            ],
            'kolom' => [
                ['key' => 'gedung', 'label' => 'Gedung'],
                ['key' => 'nomor', 'label' => 'No. Kamar', 'class' => 'nim'],
                ['key' => 'lantai', 'label' => 'Lantai', 'align' => 'center'],
                ['key' => 'kapasitas', 'label' => 'Kapasitas', 'align' => 'center'],
                ['key' => 'terisi', 'label' => 'Terisi', 'align' => 'center'],
                ['key' => 'sisa', 'label' => 'Sisa', 'align' => 'center'],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'badge' => [
                        'Penuh' => 'penuh',
                        'Tersedia' => 'tersedia',
                        'Dalam Perbaikan' => 'perbaikan',
                        'Tidak Tersedia' => 'nonaktif',
                        'Nonaktif' => 'nonaktif',
                    ],
                ],
            ],
            'baris' => $baris,
            'total' => count($baris),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function laporanPenghuni(string $keyword): array
    {
        $semuaPenghuni = User::query()
            ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
            ->with([
                'kamar' => fn ($query) => $query->select('id', 'nomor_kamar'),
                'pendaftarans' => fn ($query) => $query->orderByDesc('id'),
            ])
            ->orderBy('name')
            ->get()
            ->map(function (User $user): array {
                $pendaftaran = $user->pendaftarans->first();

                return [
                    'nim' => $user->nim ?: '-',
                    'nama' => $user->name,
                    'prodi' => $pendaftaran?->program_studi ?: '-',
                    'angkatan' => $pendaftaran?->tahun_angkatan ?: '-',
                    'kamar' => $user->kamar ? 'Kamar '.$user->kamar->nomor_kamar : 'Belum ditempatkan',
                    'status' => $this->labelStatusPengguna($user->status),
                ];
            });

        $totalPenghuni = $semuaPenghuni->count();
        $totalAktif = $semuaPenghuni->where('status', 'Aktif')->count();
        $totalDitempatkan = $semuaPenghuni
            ->filter(fn (array $item): bool => $item['kamar'] !== 'Belum ditempatkan')
            ->count();

        $baris = $this->saring($semuaPenghuni, $keyword, ['nama', 'nim'])->values()->all();

        return [
            'judul' => 'Laporan Penghuni',
            'deskripsi' => 'Data penghuni Ma’had beserta kamar yang ditempati.',
            'judul_tabel' => 'Data Penghuni Ma’had',
            'placeholder_cari' => 'Cari NIM atau nama penghuni...',
            'ringkasan' => [
                ['label' => 'Total Penghuni', 'nilai' => $totalPenghuni],
                ['label' => 'Penghuni Aktif', 'nilai' => $totalAktif],
                ['label' => 'Sudah Ditempatkan', 'nilai' => $totalDitempatkan],
            ],
            'kolom' => [
                ['key' => 'nim', 'label' => 'NIM', 'class' => 'nim'],
                ['key' => 'nama', 'label' => 'Nama'],
                ['key' => 'prodi', 'label' => 'Program Studi'],
                ['key' => 'angkatan', 'label' => 'Angkatan', 'align' => 'center'],
                ['key' => 'kamar', 'label' => 'Kamar'],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'badge' => [
                        'Aktif' => 'aktif',
                        'Tidak Aktif' => 'nonaktif',
                    ],
                ],
            ],
            'baris' => $baris,
            'total' => count($baris),
        ];
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function rentangBulan(string $periode): array
    {
        $mulai = CarbonImmutable::createFromFormat('Y-m-d', $periode.'-01')->startOfMonth();

        return [$mulai, $mulai->endOfMonth()];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $baris
     * @param  array<int, string>  $kolom
     * @return Collection<int, array<string, mixed>>
     */
    private function saring(Collection $baris, string $keyword, array $kolom): Collection
    {
        if ($keyword === '') {
            return $baris;
        }

        $kataKunci = mb_strtolower($keyword);

        return $baris->filter(function (array $item) use ($kolom, $kataKunci): bool {
            foreach ($kolom as $kunci) {
                if (mb_strpos(mb_strtolower((string) ($item[$kunci] ?? '')), $kataKunci) !== false) {
                    return true;
                }
            }

            return false;
        })->values();
    }

    private function statusKamar(Kamar $kamar, int $terisi, int $kapasitas): string
    {
        $statusMentah = strtolower(trim((string) $kamar->status));

        return match (true) {
            $statusMentah === 'dalam perbaikan' => 'Dalam Perbaikan',
            in_array($statusMentah, ['tidak tersedia', 'nonaktif'], true) => 'Tidak Tersedia',
            $kapasitas > 0 && $terisi >= $kapasitas => 'Penuh',
            default => 'Tersedia',
        };
    }

    private function labelStatusPendaftaran(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            'menunggu' => 'Menunggu',
            default => ucfirst(str_replace('_', ' ', (string) $status)) ?: '-',
        };
    }

    private function labelStatusPengguna(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'aktif' => 'Aktif',
            'nonaktif', 'tidak aktif', 'inactive' => 'Tidak Aktif',
            default => ucfirst((string) $status) ?: 'Aktif',
        };
    }

    private function formatRupiah(float $nilai): string
    {
        return 'Rp '.number_format($nilai, 0, ',', '.');
    }

    private function formatTanggal(?CarbonInterface $tanggal): string
    {
        if ($tanggal === null) {
            return '-';
        }

        return $tanggal->format('d').' '.self::NAMA_BULAN[(int) $tanggal->month].' '.$tanggal->year;
    }
}
