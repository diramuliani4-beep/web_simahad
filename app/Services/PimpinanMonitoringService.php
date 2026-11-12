<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\User;

/**
 * Data halaman Monitoring Pimpinan.
 *
 * Seluruh angka pada kartu ringkasan diambil dari DashboardRingkasanService
 * (sumber yang sama dengan dashboard) dan seluruh tabel rekap memakai
 * LaporanMahadService (sumber yang sama dengan laporan). Tidak ada angka
 * contoh maupun perhitungan terpisah, sehingga dashboard, monitoring, dan
 * laporan selalu menampilkan nilai yang konsisten.
 */
class PimpinanMonitoringService
{
    /** @var array<int, string> */
    public const KATEGORI = ['mahasiswa', 'kamar', 'pembayaran', 'pembelajaran', 'perizinan'];

    public function __construct(
        private readonly DashboardRingkasanService $ringkasan,
        private readonly LaporanMahadService $laporan,
    ) {}

    /**
     * Metadata kategori monitoring untuk sidebar dan kartu navigasi.
     *
     * @return array<string, array{judul: string, deskripsi: string, ikon: string}>
     */
    public static function daftarKategori(): array
    {
        return [
            'mahasiswa' => [
                'judul' => 'Monitoring Mahasiswa',
                'deskripsi' => 'Data dan perkembangan mahasiswa Ma’had.',
                'ikon' => 'mahasiswa',
            ],
            'kamar' => [
                'judul' => 'Monitoring Kamar',
                'deskripsi' => 'Kapasitas, penghuni, dan ketersediaan kamar.',
                'ikon' => 'kamar',
            ],
            'pembayaran' => [
                'judul' => 'Monitoring Pembayaran',
                'deskripsi' => 'Tagihan, angsuran, dan pemeriksaan bukti pembayaran.',
                'ikon' => 'pembayaran',
            ],
            'pembelajaran' => [
                'judul' => 'Monitoring Pembelajaran',
                'deskripsi' => 'Rekap jadwal, kelompok, kehadiran, dan tugas.',
                'ikon' => 'pembelajaran',
            ],
            'perizinan' => [
                'judul' => 'Monitoring Perizinan',
                'deskripsi' => 'Rekap pengajuan izin dan status verifikasinya.',
                'ikon' => 'surat',
            ],
        ];
    }

    /**
     * Ringkasan dashboard Pimpinan (dipakai bersama oleh dashboard).
     *
     * @return array<string, mixed>
     */
    public function ringkasanMaad(): array
    {
        return $this->ringkasan->pimpinan();
    }

    /**
     * Data satu halaman monitoring sesuai kategori.
     *
     * @param  array<string, mixed>  $filter
     * @return array<string, mixed>
     */
    public function untuk(string $kategori, array $filter = []): array
    {
        $filter = $this->normalisasiFilter($filter);
        $ringkasanMaad = $this->ringkasanMaad();

        return match ($kategori) {
            'mahasiswa' => $this->mahasiswa($filter, $ringkasanMaad),
            'kamar' => $this->kamar($filter, $ringkasanMaad),
            'pembayaran' => $this->pembayaran($filter, $ringkasanMaad),
            'pembelajaran' => $this->pembelajaran($filter, $ringkasanMaad),
            'perizinan' => $this->perizinan($filter, $ringkasanMaad),
            default => abort(404),
        };
    }

    /**
     * Opsi lantai kamar yang benar-benar ada di database.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function opsiLantai(): array
    {
        return Kamar::query()
            ->whereNotNull('lantai')
            ->distinct()
            ->orderBy('lantai')
            ->pluck('lantai')
            ->map(fn ($lantai): array => [
                'value' => (string) (int) $lantai,
                'label' => 'Lantai '.(int) $lantai,
            ])
            ->all();
    }

    /**
     * Opsi angkatan dari data pendaftaran.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function opsiAngkatan(): array
    {
        return Pendaftaran::query()
            ->whereNotNull('tahun_angkatan')
            ->where('tahun_angkatan', '<>', '')
            ->distinct()
            ->orderByDesc('tahun_angkatan')
            ->pluck('tahun_angkatan')
            ->map(fn ($angkatan): array => [
                'value' => (string) $angkatan,
                'label' => (string) $angkatan,
            ])
            ->all();
    }

    /**
     * Opsi kategori mahasiswa dari data pendaftaran.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function opsiKategoriMahasiswa(): array
    {
        return Pendaftaran::query()
            ->whereNotNull('kategori')
            ->where('kategori', '<>', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori')
            ->map(fn ($kategori): array => [
                'value' => (string) $kategori,
                'label' => ucfirst((string) $kategori),
            ])
            ->all();
    }

    /**
     * Opsi periode pembayaran yang benar-benar tercatat.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function opsiPeriodePembayaran(): array
    {
        return Pembayaran::query()
            ->whereNotNull('periode')
            ->where('periode', '<>', '')
            ->distinct()
            ->orderBy('periode')
            ->pluck('periode')
            ->map(fn ($periode): array => [
                'value' => (string) $periode,
                'label' => (string) $periode,
            ])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function opsiPeriodeBulan(): array
    {
        return $this->laporan->opsiPeriode();
    }

    // -----------------------------------------------------------------
    // Kategori
    // -----------------------------------------------------------------

    /**
     * @param  array<string, string>  $filter
     * @param  array<string, mixed>  $ringkasanMaad
     * @return array<string, mixed>
     */
    private function mahasiswa(array $filter, array $ringkasanMaad): array
    {
        $semua = User::query()
            ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
            ->with([
                'kamar' => fn ($query) => $query->select('id', 'nomor_kamar', 'gedung', 'lantai'),
                'pendaftarans' => fn ($query) => $query->orderByDesc('id'),
            ])
            ->orderBy('name')
            ->get();

        $totalMahasiswa = (int) $ringkasanMaad['totalMahasiswa'];
        $ditempatkan = $semua->filter(fn (User $user): bool => $user->kamar_id !== null)->count();
        $aktif = $semua->filter(
            fn (User $user): bool => strtolower((string) $user->status) === 'aktif'
        )->count();

        $tersaring = $semua->filter(function (User $user) use ($filter): bool {
            /** @var Pendaftaran|null $pendaftaran */
            $pendaftaran = $user->pendaftarans->first();

            if ($filter['lantai'] !== '' && (string) (int) ($user->kamar?->lantai ?? 0) !== $filter['lantai']) {
                return false;
            }

            if ($filter['angkatan'] !== '' && (string) ($pendaftaran?->tahun_angkatan ?? '') !== $filter['angkatan']) {
                return false;
            }

            if ($filter['kategori'] !== '' && (string) ($pendaftaran?->kategori ?? '') !== $filter['kategori']) {
                return false;
            }

            if ($filter['status'] !== ''
                && strtolower((string) $user->status) !== strtolower($filter['status'])) {
                return false;
            }

            return $this->cocokCari($filter['cari'], [
                (string) $user->name,
                (string) $user->nim,
                (string) ($user->kamar?->nomor_kamar ?? ''),
            ]);
        })->values();

        $baris = $tersaring->map(function (User $user): array {
            /** @var Pendaftaran|null $pendaftaran */
            $pendaftaran = $user->pendaftarans->first();

            return [
                'nim' => $user->nim ?: '-',
                'nama' => $user->name,
                'prodi' => $pendaftaran?->program_studi ?: '-',
                'angkatan' => $pendaftaran?->tahun_angkatan ?: '-',
                'kategori' => $pendaftaran?->kategori ? ucfirst((string) $pendaftaran->kategori) : '-',
                'kamar' => $user->kamar
                    ? 'Kamar '.$user->kamar->nomor_kamar.' (Lantai '.(int) $user->kamar->lantai.')'
                    : 'Belum ditempatkan',
                'status' => $this->laporan->labelStatusPengguna($user->status),
            ];
        })->all();

        return $this->payload('mahasiswa', [
            'deskripsi' => 'Kondisi dan perkembangan mahasiswa Ma’had dari seluruh lantai.',
            'periode_label' => 'Data terkini',
            'judul_tabel' => 'Daftar Mahasiswa',
            'placeholder_cari' => 'Cari NIM, nama, atau nomor kamar...',
            'ringkasan' => [
                ['label' => 'Total Mahasiswa', 'nilai' => $totalMahasiswa],
                ['label' => 'Status Aktif', 'nilai' => $aktif],
                ['label' => 'Sudah Ditempatkan', 'nilai' => $ditempatkan],
                ['label' => 'Belum Ditempatkan', 'nilai' => max(0, $totalMahasiswa - $ditempatkan)],
            ],
            'kolom' => [
                ['key' => 'nim', 'label' => 'NIM', 'class' => 'nim'],
                ['key' => 'nama', 'label' => 'Nama Mahasiswa'],
                ['key' => 'prodi', 'label' => 'Program Studi'],
                ['key' => 'angkatan', 'label' => 'Angkatan', 'align' => 'center'],
                ['key' => 'kategori', 'label' => 'Kategori', 'align' => 'center'],
                ['key' => 'kamar', 'label' => 'Kamar'],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'badge' => ['Aktif' => 'aktif', 'Tidak Aktif' => 'nonaktif'],
                ],
            ],
            'baris' => $baris,
            'filter_tambahan' => [
                $this->filterSelect('lantai', 'Lantai', $filter['lantai'], $this->opsiLantai()),
                $this->filterSelect('angkatan', 'Angkatan', $filter['angkatan'], $this->opsiAngkatan()),
                $this->filterSelect('kategori', 'Kategori Mahasiswa', $filter['kategori'], $this->opsiKategoriMahasiswa()),
                $this->filterSelect('status', 'Status Akun', $filter['status'], [
                    ['value' => 'aktif', 'label' => 'Aktif'],
                    ['value' => 'nonaktif', 'label' => 'Tidak Aktif'],
                ]),
            ],
        ]);
    }

    /**
     * @param  array<string, string>  $filter
     * @param  array<string, mixed>  $ringkasanMaad
     * @return array<string, mixed>
     */
    private function kamar(array $filter, array $ringkasanMaad): array
    {
        $semua = Kamar::query()
            ->withCount('users')
            ->orderBy('gedung')
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get();

        $tersaring = $semua->filter(function (Kamar $kamar) use ($filter): bool {
            if ($filter['lantai'] !== '' && (string) (int) $kamar->lantai !== $filter['lantai']) {
                return false;
            }

            $kapasitas = (int) $kamar->kapasitas;
            $terisi = (int) $kamar->users_count;
            $status = $this->laporan->statusKamar($kamar, $terisi, $kapasitas);

            if ($filter['status'] !== '' && strtolower($status) !== strtolower($filter['status'])) {
                return false;
            }

            return $this->cocokCari($filter['cari'], [
                (string) $kamar->nomor_kamar,
                (string) $kamar->gedung,
            ]);
        })->values();

        $baris = $tersaring->map(function (Kamar $kamar): array {
            $kapasitas = (int) $kamar->kapasitas;
            $terisi = (int) $kamar->users_count;

            return [
                'gedung' => $kamar->gedung ?: 'Gedung A',
                'nomor' => $kamar->nomor_kamar ?: '-',
                'lantai' => (int) $kamar->lantai,
                'kapasitas' => $kapasitas,
                'terisi' => $terisi,
                'sisa' => max(0, $kapasitas - $terisi),
                'status' => $this->laporan->statusKamar($kamar, $terisi, $kapasitas),
            ];
        })->all();

        $kamarMaad = $ringkasanMaad['kamar'];

        return $this->payload('kamar', [
            'deskripsi' => 'Kapasitas, penghuni, dan ketersediaan kamar pada tiga lantai Ma’had.',
            'periode_label' => 'Data terkini',
            'judul_tabel' => 'Ketersediaan Kamar',
            'placeholder_cari' => 'Cari nomor kamar atau gedung...',
            'ringkasan' => [
                ['label' => 'Total Kamar', 'nilai' => $kamarMaad['jumlah']],
                ['label' => 'Total Kapasitas', 'nilai' => $kamarMaad['kapasitas']],
                ['label' => 'Terisi', 'nilai' => $kamarMaad['terisi']],
                ['label' => 'Sisa Slot', 'nilai' => $kamarMaad['sisa']],
                ['label' => 'Keterisian', 'nilai' => $kamarMaad['persentase'].'%'],
            ],
            'per_lantai' => $kamarMaad['per_lantai']->all(),
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
                    ],
                ],
            ],
            'baris' => $baris,
            'filter_tambahan' => [
                $this->filterSelect('lantai', 'Lantai', $filter['lantai'], $this->opsiLantai()),
                $this->filterSelect('status', 'Status Kamar', $filter['status'], [
                    ['value' => 'tersedia', 'label' => 'Tersedia'],
                    ['value' => 'penuh', 'label' => 'Penuh'],
                    ['value' => 'dalam perbaikan', 'label' => 'Dalam Perbaikan'],
                    ['value' => 'tidak tersedia', 'label' => 'Tidak Tersedia'],
                ]),
            ],
        ]);
    }

    /**
     * @param  array<string, string>  $filter
     * @param  array<string, mixed>  $ringkasanMaad
     * @return array<string, mixed>
     */
    private function pembayaran(array $filter, array $ringkasanMaad): array
    {
        $pembayaranMaad = $ringkasanMaad['pembayaran'];
        $periode = $filter['periode'] !== ''
            ? $filter['periode']
            : (string) ($pembayaranMaad['periode'] ?? '');

        $baris = collect();

        if ($periode !== '') {
            $baris = Pembayaran::query()
                ->with([
                    'user' => fn ($query) => $query->select('id', 'name', 'nim', 'kamar_id'),
                    'user.kamar' => fn ($query) => $query->select('id', 'nomor_kamar', 'lantai'),
                ])
                ->where('periode', $periode)
                ->orderBy('id')
                ->get()
                ->filter(function (Pembayaran $item) use ($filter): bool {
                    if ($filter['lantai'] !== '' && (string) (int) ($item->user?->kamar?->lantai ?? 0) !== $filter['lantai']) {
                        return false;
                    }

                    if ($filter['status'] !== ''
                        && strtolower((string) $item->status) !== strtolower($filter['status'])) {
                        return false;
                    }

                    return $this->cocokCari($filter['cari'], [
                        (string) ($item->user?->name ?? ''),
                        (string) ($item->user?->nim ?? ''),
                        (string) $item->kode_transaksi,
                    ]);
                })
                ->map(function (Pembayaran $item): array {
                    return [
                        'nim' => $item->user?->nim ?: '-',
                        'nama' => $item->user?->name ?? 'Mahasiswa',
                        'tagihan' => $item->nama_tagihan ?: '-',
                        'nominal' => $this->laporan->formatRupiah((float) $item->nominal),
                        'bukti' => filled($item->bukti_pembayaran) ? 'Sudah diunggah' : 'Belum ada',
                        'tanggal' => $item->tanggal_bayar
                            ? $item->tanggal_bayar->format('d/m/Y')
                            : '-',
                        'status' => $item->status_label,
                    ];
                })
                ->values();
        }

        $perPeriode = Pembayaran::query()
            ->selectRaw('periode, count(*) as jumlah, sum(nominal) as nominal')
            ->groupBy('periode')
            ->orderBy('periode')
            ->get()
            ->map(fn (Pembayaran $item): array => [
                'periode' => (string) $item->periode,
                'jumlah' => (int) $item->jumlah,
                'nominal' => $this->laporan->formatRupiah((float) $item->nominal),
            ])
            ->all();

        return $this->payload('pembayaran', [
            'deskripsi' => 'Tagihan, angsuran, pemeriksaan bukti, dan pembayaran yang sudah dikonfirmasi resmi.',
            'periode' => $periode,
            'periode_label' => $periode !== '' ? $periode : 'Periode belum ditentukan',
            'judul_tabel' => 'Rincian Pembayaran '.($periode !== '' ? $periode : ''),
            'placeholder_cari' => 'Cari NIM, nama, atau kode transaksi...',
            'ringkasan' => [
                ['label' => 'Total Tagihan', 'nilai' => $pembayaranMaad['totalTagihan']],
                ['label' => 'Lunas / Terkonfirmasi', 'nilai' => $pembayaranMaad['lunas']],
                ['label' => 'Menunggu Pemeriksaan', 'nilai' => $pembayaranMaad['menunggu']],
                ['label' => 'Belum Bayar', 'nilai' => $pembayaranMaad['belum']],
                ['label' => 'Persentase Lunas', 'nilai' => $pembayaranMaad['persentase'].'%'],
            ],
            'opsi_periode' => $this->opsiPeriodePembayaran(),
            'per_periode' => $perPeriode,
            'kolom' => [
                ['key' => 'nim', 'label' => 'NIM', 'class' => 'nim'],
                ['key' => 'nama', 'label' => 'Nama Mahasiswa'],
                ['key' => 'tagihan', 'label' => 'Tagihan'],
                ['key' => 'nominal', 'label' => 'Nominal'],
                ['key' => 'bukti', 'label' => 'Bukti Bayar', 'align' => 'center'],
                ['key' => 'tanggal', 'label' => 'Tanggal Bayar', 'align' => 'center'],
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
            'baris' => $baris->all(),
            'filter_tambahan' => [
                $this->filterSelect('lantai', 'Lantai', $filter['lantai'], $this->opsiLantai()),
                $this->filterSelect('status', 'Status Pembayaran', $filter['status'], [
                    ['value' => 'lunas', 'label' => 'Lunas'],
                    ['value' => 'menunggu', 'label' => 'Menunggu Konfirmasi'],
                    ['value' => 'belum_lunas', 'label' => 'Belum Lunas'],
                ]),
            ],
        ]);
    }

    /**
     * @param  array<string, string>  $filter
     * @param  array<string, mixed>  $ringkasanMaad
     * @return array<string, mixed>
     */
    private function pembelajaran(array $filter, array $ringkasanMaad): array
    {
        $rekap = $this->laporan->laporan(
            'pembelajaran',
            $filter['periode'] !== '' ? $filter['periode'] : now()->format('Y-m'),
            $filter['cari'],
        );

        $baris = collect($rekap['baris']);

        if ($filter['kategori'] !== '') {
            $baris = $baris
                ->filter(fn (array $item): bool => (string) ($item['kelompok'] ?? '') === $filter['kategori'])
                ->values();
        }

        $pembelajaranMaad = $ringkasanMaad['pembelajaran'];
        $opsiKelompok = collect($rekap['baris'])
            ->pluck('kelompok')
            ->filter()
            ->unique()
            ->sort()
            ->map(fn (string $kelompok): array => ['value' => $kelompok, 'label' => $kelompok])
            ->values()
            ->all();

        return $this->payload('pembelajaran', [
            'deskripsi' => 'Rekap jadwal, kelompok, kehadiran, dan tugas. Tanpa akses pengelolaan.',
            'periode' => $rekap['periode'],
            'periode_label' => $rekap['periode_label'],
            'judul_tabel' => 'Rekap Pembelajaran per Kelompok',
            'placeholder_cari' => 'Cari kelompok atau mata pelajaran...',
            'ringkasan' => [
                ['label' => 'Kelompok Belajar', 'nilai' => $pembelajaranMaad['kelompok']],
                ['label' => 'Mata Pelajaran', 'nilai' => $pembelajaranMaad['mataPelajaran']],
                ['label' => 'Total Jadwal', 'nilai' => $pembelajaranMaad['jadwal']],
                ['label' => 'Penyelesaian Tugas', 'nilai' => $pembelajaranMaad['penyelesaian'].'%'],
            ],
            'opsi_periode' => $this->opsiPeriodeBulan(),
            'kolom' => $rekap['kolom'],
            'baris' => $baris->all(),
            'filter_tambahan' => [
                $this->filterSelect('kategori', 'Kelompok', $filter['kategori'], $opsiKelompok),
            ],
        ]);
    }

    /**
     * @param  array<string, string>  $filter
     * @param  array<string, mixed>  $ringkasanMaad
     * @return array<string, mixed>
     */
    private function perizinan(array $filter, array $ringkasanMaad): array
    {
        $rekap = $this->laporan->laporan(
            'perizinan',
            $filter['periode'] !== '' ? $filter['periode'] : now()->format('Y-m'),
            $filter['cari'],
        );

        $baris = collect($rekap['baris']);

        if ($filter['kategori'] !== '') {
            $baris = $baris
                ->filter(fn (array $item): bool => (string) ($item['jenis'] ?? '') === $filter['kategori'])
                ->values();
        }

        if ($filter['status'] !== '') {
            $baris = $baris
                ->filter(fn (array $item): bool => strtolower((string) ($item['status'] ?? '')) === strtolower($filter['status']))
                ->values();
        }

        $perizinanMaad = $ringkasanMaad['perizinan'];
        $opsiJenis = collect($rekap['baris'])
            ->pluck('jenis')
            ->filter()
            ->unique()
            ->sort()
            ->map(fn (string $jenis): array => ['value' => $jenis, 'label' => $jenis])
            ->values()
            ->all();

        return $this->payload('perizinan', [
            'deskripsi' => 'Rekap pengajuan izin beserta status verifikasinya.',
            'periode' => $rekap['periode'],
            'periode_label' => $rekap['periode_label'],
            'judul_tabel' => $rekap['judul_tabel'],
            'placeholder_cari' => $rekap['placeholder_cari'],
            'ringkasan' => [
                ['label' => 'Total Pengajuan', 'nilai' => $perizinanMaad['total']],
                ['label' => 'Menunggu Verifikasi', 'nilai' => $perizinanMaad['menunggu']],
                ['label' => 'Disetujui', 'nilai' => $perizinanMaad['disetujui']],
                ['label' => 'Selesai', 'nilai' => $perizinanMaad['selesai']],
            ],
            'opsi_periode' => $this->opsiPeriodeBulan(),
            'kolom' => $rekap['kolom'],
            'baris' => $baris->all(),
            'filter_tambahan' => [
                $this->filterSelect('kategori', 'Jenis Izin', $filter['kategori'], $opsiJenis),
                $this->filterSelect('status', 'Status Verifikasi', $filter['status'], [
                    ['value' => 'Menunggu Persetujuan', 'label' => 'Menunggu Persetujuan'],
                    ['value' => 'Disetujui', 'label' => 'Disetujui'],
                    ['value' => 'Ditolak', 'label' => 'Ditolak'],
                    ['value' => 'Perlu Perbaikan', 'label' => 'Perlu Perbaikan'],
                    ['value' => 'Selesai', 'label' => 'Selesai'],
                ]),
            ],
        ]);
    }

    // -----------------------------------------------------------------
    // Bantuan
    // -----------------------------------------------------------------

    /**
     * @param  array<string, mixed>  $filter
     * @return array<string, string>
     */
    private function normalisasiFilter(array $filter): array
    {
        return [
            'periode' => trim((string) ($filter['periode'] ?? '')),
            'lantai' => trim((string) ($filter['lantai'] ?? '')),
            'angkatan' => trim((string) ($filter['angkatan'] ?? '')),
            'kategori' => trim((string) ($filter['kategori'] ?? '')),
            'status' => trim((string) ($filter['status'] ?? '')),
            'cari' => trim((string) ($filter['cari'] ?? '')),
        ];
    }

    /**
     * @param  array<int, string>  $nilai
     */
    private function cocokCari(string $cari, array $nilai): bool
    {
        if ($cari === '') {
            return true;
        }

        $kataKunci = mb_strtolower($cari);

        foreach ($nilai as $teks) {
            if (mb_strpos(mb_strtolower($teks), $kataKunci) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, array{value: string, label: string}>  $options
     * @return array{name: string, label: string, nilai: string, options: array<int, array{value: string, label: string}>}
     */
    private function filterSelect(string $name, string $label, string $nilai, array $options): array
    {
        return [
            'name' => $name,
            'label' => $label,
            'nilai' => $nilai,
            'options' => $options,
        ];
    }

    /**
     * Menyusun payload seragam untuk partial laporan.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(string $kategori, array $data): array
    {
        $meta = self::daftarKategori()[$kategori];

        return array_merge([
            'kategori' => $kategori,
            'judul' => $meta['judul'],
            'deskripsi' => $meta['deskripsi'],
            'ikon' => $meta['ikon'],
            'periode' => null,
            'periode_label' => 'Data terkini',
            'judul_tabel' => $meta['judul'],
            'placeholder_cari' => 'Cari data...',
            'ringkasan' => [],
            'opsi_periode' => [],
            'kolom' => [],
            'baris' => [],
            'filter_tambahan' => [],
        ], $data, [
            'total' => count($data['baris'] ?? []),
        ]);
    }
}
