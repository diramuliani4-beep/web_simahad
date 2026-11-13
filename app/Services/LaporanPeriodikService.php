<?php

namespace App\Services;

use App\Models\AbsensiPembelajaran;
use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\Pengumuman;
use App\Models\PertemuanPembelajaran;
use App\Models\SuratPerizinan;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Penyusun laporan periodik Musyrifah (bulanan dan tahunan).
 *
 * Seluruh angka dihitung dari tabel operasional yang benar-benar tersedia.
 * Satu baris data hanya dihitung satu kali: setiap kueri mengambil satu kali
 * lalu agregasi dilakukan di memori, sehingga rekap tahunan tidak menggandakan
 * data ketika beberapa bulan digabung.
 */
class LaporanPeriodikService
{
    /** @var array<string, string> */
    public const JENIS = [
        'bulanan' => 'Laporan Bulanan',
        'tahunan' => 'Laporan Tahunan',
    ];

    public function __construct(private readonly LaporanMahadService $laporanMahad) {}

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function opsiJenis(): array
    {
        $opsi = [];

        foreach (self::JENIS as $nilai => $label) {
            $opsi[] = ['value' => $nilai, 'label' => $label];
        }

        return $opsi;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function opsiTahun(): array
    {
        $tahunSekarang = (int) now()->year;
        $opsi = [];

        for ($tahun = $tahunSekarang + 1; $tahun >= $tahunSekarang - 4; $tahun--) {
            $opsi[] = ['value' => (string) $tahun, 'label' => 'Tahun '.$tahun];
        }

        return $opsi;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function opsiBulan(): array
    {
        $opsi = [];

        foreach (LaporanMahadService::NAMA_BULAN as $nomor => $nama) {
            $opsi[] = ['value' => (string) $nomor, 'label' => $nama];
        }

        return $opsi;
    }

    public function normalisasiJenis(?string $jenis): string
    {
        $jenis = trim((string) $jenis);

        return array_key_exists($jenis, self::JENIS) ? $jenis : 'bulanan';
    }

    public function normalisasiTahun(?string $tahun): int
    {
        $tahun = trim((string) $tahun);

        if (preg_match('/^\d{4}$/', $tahun) === 1) {
            $nilai = (int) $tahun;

            if ($nilai >= 2000 && $nilai <= 2100) {
                return $nilai;
            }
        }

        return (int) now()->year;
    }

    public function normalisasiBulan(?string $bulan): int
    {
        $nilai = (int) $bulan;

        return $nilai >= 1 && $nilai <= 12 ? $nilai : (int) now()->month;
    }

    /**
     * Periode baku: "Y-m" untuk bulanan dan "Y" untuk tahunan.
     */
    public function periode(string $jenis, int $tahun, int $bulan): string
    {
        return $jenis === 'tahunan'
            ? sprintf('%04d', $tahun)
            : sprintf('%04d-%02d', $tahun, $bulan);
    }

    public function labelPeriode(string $jenis, int $tahun, int $bulan): string
    {
        return $jenis === 'tahunan'
            ? 'Tahun '.$tahun
            : LaporanMahadService::NAMA_BULAN[$bulan].' '.$tahun;
    }

    /**
     * Menyusun potret laporan lengkap untuk periode terpilih.
     *
     * @param  array{evaluasi?: string, kendala?: string}  $catatan
     * @return array<string, mixed>
     */
    public function susun(string $jenis, int $tahun, int $bulan, array $catatan = []): array
    {
        $jenis = $this->normalisasiJenis($jenis);
        $tahun = $this->normalisasiTahun((string) $tahun);
        $bulan = $this->normalisasiBulan((string) $bulan);

        [$mulai, $akhir] = $this->rentang($jenis, $tahun, $bulan);

        $seksi = $jenis === 'tahunan'
            ? $this->seksiTahunan($tahun, $mulai, $akhir)
            : $this->seksiBulanan($tahun, $bulan, $mulai, $akhir);

        $evaluasi = trim((string) ($catatan['evaluasi'] ?? ''));
        $kendala = trim((string) ($catatan['kendala'] ?? ''));

        $seksi[] = $this->seksiCatatan($evaluasi, $kendala);

        return [
            'skema' => 1,
            'jenis' => $jenis,
            'jenis_label' => self::JENIS[$jenis],
            'judul' => $jenis === 'tahunan' ? 'Laporan Tahunan Ma’had' : 'Laporan Bulanan Ma’had',
            'periode' => $this->periode($jenis, $tahun, $bulan),
            'periode_label' => $this->labelPeriode($jenis, $tahun, $bulan),
            'cakupan' => $this->cakupan($mulai, $akhir),
            'dibangkitkan_pada' => now()->format('Y-m-d H:i:s'),
            'dibangkitkan_label' => $this->formatTanggalWaktu(now()),
            'catatan' => [
                'evaluasi' => $evaluasi,
                'kendala' => $kendala,
            ],
            'seksi' => $seksi,
            'keterbatasan' => $this->keterbatasan(),
        ];
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function rentang(string $jenis, int $tahun, int $bulan): array
    {
        if ($jenis === 'tahunan') {
            $mulai = CarbonImmutable::create($tahun, 1, 1)->startOfDay();

            return [$mulai, $mulai->endOfYear()->endOfDay()];
        }

        $mulai = CarbonImmutable::create($tahun, $bulan, 1)->startOfDay();

        return [$mulai, $mulai->endOfMonth()->endOfDay()];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function seksiBulanan(int $tahun, int $bulan, CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        return [
            $this->seksiMahasiswaBulanan($mulai, $akhir),
            $this->seksiKamar(),
            $this->seksiPembayaranBulanan($tahun, $bulan, $mulai, $akhir),
            $this->seksiKegiatan($mulai, $akhir, false),
            $this->seksiKehadiranBulanan($mulai, $akhir),
            $this->seksiPembelajaran($mulai, $akhir, false),
            $this->seksiPerizinanBulanan($mulai, $akhir),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function seksiTahunan(int $tahun, CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        return [
            $this->seksiMahasiswaTahunan($tahun, $mulai, $akhir),
            $this->seksiKamar(),
            $this->seksiPembayaranTahunan($tahun, $mulai, $akhir),
            $this->seksiKegiatan($mulai, $akhir, true),
            $this->seksiKehadiranTahunan($mulai, $akhir),
            $this->seksiPembelajaran($mulai, $akhir, true),
            $this->seksiPerizinanTahunan($mulai, $akhir),
        ];
    }

    /**
     * @return Collection<int, User>
     */
    private function dataMahasiswa(): Collection
    {
        return User::query()
            ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
            ->with([
                'kamar' => fn ($query) => $query->select('id', 'nomor_kamar'),
                'pendaftarans' => fn ($query) => $query->orderByDesc('id'),
            ])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiMahasiswaBulanan(CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        $mahasiswa = $this->dataMahasiswa();

        $baris = $mahasiswa->map(function (User $user): array {
            $pendaftaran = $user->pendaftarans->first();

            return [
                'nim' => $user->nim ?: '-',
                'nama' => $user->name,
                'prodi' => $pendaftaran?->program_studi ?: '-',
                'angkatan' => $pendaftaran?->tahun_angkatan ?: '-',
                'kamar' => $user->kamar ? $user->kamar->nomor_kamar : 'Belum ditempatkan',
                'status' => $this->laporanMahad->labelStatusPengguna($user->status),
            ];
        })->all();

        $pendaftar = Pendaftaran::query()
            ->whereBetween('created_at', [$mulai, $akhir])
            ->get(['id', 'status']);

        $totalAktif = $mahasiswa
            ->filter(fn (User $user): bool => $this->laporanMahad->labelStatusPengguna($user->status) === 'Aktif')
            ->count();
        $ditempatkan = $mahasiswa->filter(fn (User $user): bool => $user->kamar !== null)->count();

        return [
            'kode' => 'mahasiswa',
            'judul' => 'Rekap Data Mahasiswa',
            'keterangan' => 'Data mahasiswa yang tercatat pada sistem beserta pendaftar baru yang masuk pada periode ini.',
            'ringkasan' => [
                ['label' => 'Total Mahasiswa', 'nilai' => $mahasiswa->count()],
                ['label' => 'Mahasiswa Aktif', 'nilai' => $totalAktif],
                ['label' => 'Sudah Ditempatkan', 'nilai' => $ditempatkan],
                ['label' => 'Pendaftar Baru Periode', 'nilai' => $pendaftar->count()],
                ['label' => 'Pendaftar Disetujui', 'nilai' => $this->hitungStatus($pendaftar, 'disetujui')],
            ],
            'tabel' => [[
                'judul' => 'Daftar Mahasiswa Ma’had',
                'kolom' => [
                    ['key' => 'nim', 'label' => 'NIM'],
                    ['key' => 'nama', 'label' => 'Nama Mahasiswa'],
                    ['key' => 'prodi', 'label' => 'Program Studi'],
                    ['key' => 'angkatan', 'label' => 'Angkatan', 'align' => 'center'],
                    ['key' => 'kamar', 'label' => 'Kamar'],
                    ['key' => 'status', 'label' => 'Status', 'align' => 'center'],
                ],
                'baris' => $baris,
            ]],
            'catatan' => [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiMahasiswaTahunan(int $tahun, CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        $mahasiswa = $this->dataMahasiswa();

        $pendaftar = Pendaftaran::query()
            ->whereBetween('created_at', [$mulai, $akhir])
            ->get(['id', 'status', 'created_at']);

        $perBulan = $pendaftar->groupBy(
            fn (Pendaftaran $item): string => $item->created_at instanceof CarbonInterface
                ? $item->created_at->format('m')
                : ''
        );

        $barisBulanan = [];

        foreach (LaporanMahadService::NAMA_BULAN as $nomor => $nama) {
            $daftar = $perBulan->get(sprintf('%02d', $nomor), collect());

            $barisBulanan[] = [
                'bulan' => $nama,
                'pendaftar' => $daftar->count(),
                'disetujui' => $this->hitungStatus($daftar, 'disetujui'),
                'ditolak' => $this->hitungStatus($daftar, 'ditolak'),
                'menunggu' => $daftar->count()
                    - $this->hitungStatus($daftar, 'disetujui')
                    - $this->hitungStatus($daftar, 'ditolak'),
            ];
        }

        $komposisi = $mahasiswa
            ->groupBy(function (User $user): string {
                $prodi = $user->pendaftarans->first()?->program_studi;

                return filled($prodi) ? (string) $prodi : 'Tidak tercatat';
            })
            ->map(fn (Collection $daftar, string $prodi): array => [
                'prodi' => $prodi,
                'jumlah' => $daftar->count(),
            ])
            ->sortByDesc('jumlah')
            ->values()
            ->all();

        $totalAktif = $mahasiswa
            ->filter(fn (User $user): bool => $this->laporanMahad->labelStatusPengguna($user->status) === 'Aktif')
            ->count();
        $ditempatkan = $mahasiswa->filter(fn (User $user): bool => $user->kamar !== null)->count();

        return [
            'kode' => 'mahasiswa',
            'judul' => 'Rekap Mahasiswa dan Perkembangan Jumlah Mahasiswa',
            'keterangan' => 'Rekap mahasiswa saat laporan dibuat serta jumlah pendaftar baru setiap bulan pada tahun laporan.',
            'ringkasan' => [
                ['label' => 'Total Mahasiswa', 'nilai' => $mahasiswa->count()],
                ['label' => 'Mahasiswa Aktif', 'nilai' => $totalAktif],
                ['label' => 'Sudah Ditempatkan', 'nilai' => $ditempatkan],
                ['label' => 'Pendaftar Tahun '.$tahun, 'nilai' => $pendaftar->count()],
                ['label' => 'Pendaftar Disetujui', 'nilai' => $this->hitungStatus($pendaftar, 'disetujui')],
            ],
            'tabel' => [
                [
                    'judul' => 'Perkembangan Pendaftar per Bulan',
                    'kolom' => [
                        ['key' => 'bulan', 'label' => 'Bulan'],
                        ['key' => 'pendaftar', 'label' => 'Pendaftar', 'align' => 'center'],
                        ['key' => 'disetujui', 'label' => 'Disetujui', 'align' => 'center'],
                        ['key' => 'ditolak', 'label' => 'Ditolak', 'align' => 'center'],
                        ['key' => 'menunggu', 'label' => 'Menunggu', 'align' => 'center'],
                    ],
                    'baris' => $barisBulanan,
                ],
                [
                    'judul' => 'Komposisi Mahasiswa per Program Studi',
                    'kolom' => [
                        ['key' => 'prodi', 'label' => 'Program Studi'],
                        ['key' => 'jumlah', 'label' => 'Jumlah Mahasiswa', 'align' => 'center'],
                    ],
                    'baris' => $komposisi,
                ],
            ],
            'catatan' => [
                'Perkembangan jumlah mahasiswa dihitung dari pendaftaran baru yang tercatat sistem, bukan dari jumlah mahasiswa yang masih aktif setiap bulan.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiKamar(): array
    {
        $kamar = Kamar::query()
            ->withCount('users')
            ->orderBy('gedung')
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get();

        $baris = $kamar->map(function (Kamar $item): array {
            $kapasitas = (int) $item->kapasitas;
            $terisi = (int) $item->users_count;

            return [
                'gedung' => $item->gedung ?: '-',
                'nomor' => $item->nomor_kamar ?: '-',
                'lantai' => (int) $item->lantai,
                'kapasitas' => $kapasitas,
                'terisi' => $terisi,
                'sisa' => max(0, $kapasitas - $terisi),
                'status' => $this->laporanMahad->statusKamar($item, $terisi, $kapasitas),
            ];
        })->all();

        $totalKapasitas = (int) $kamar->sum('kapasitas');
        $totalTerisi = (int) $kamar->sum(fn (Kamar $item): int => (int) $item->users_count);
        $kamarPenuh = $kamar
            ->filter(fn (Kamar $item): bool => (int) $item->kapasitas > 0 && (int) $item->users_count >= (int) $item->kapasitas)
            ->count();

        return [
            'kode' => 'kamar',
            'judul' => 'Kondisi Kamar dan Penghuni',
            'keterangan' => 'Rekap kapasitas dan penghuni setiap kamar pada saat laporan dibangkitkan.',
            'ringkasan' => [
                ['label' => 'Total Kamar', 'nilai' => $kamar->count()],
                ['label' => 'Total Kapasitas', 'nilai' => $totalKapasitas],
                ['label' => 'Total Terisi', 'nilai' => $totalTerisi],
                ['label' => 'Sisa Slot', 'nilai' => max(0, $totalKapasitas - $totalTerisi)],
                ['label' => 'Kamar Penuh', 'nilai' => $kamarPenuh],
            ],
            'tabel' => [[
                'judul' => 'Data Kamar dan Penghuni',
                'kolom' => [
                    ['key' => 'gedung', 'label' => 'Gedung'],
                    ['key' => 'nomor', 'label' => 'No. Kamar', 'align' => 'center'],
                    ['key' => 'lantai', 'label' => 'Lantai', 'align' => 'center'],
                    ['key' => 'kapasitas', 'label' => 'Kapasitas', 'align' => 'center'],
                    ['key' => 'terisi', 'label' => 'Terisi', 'align' => 'center'],
                    ['key' => 'sisa', 'label' => 'Sisa', 'align' => 'center'],
                    ['key' => 'status', 'label' => 'Status', 'align' => 'center'],
                ],
                'baris' => $baris,
            ]],
            'catatan' => [
                'Sistem hanya menyimpan kondisi kamar terkini, sehingga angka penghuni mencerminkan keadaan saat laporan dibuat dan bukan riwayat per bulan.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiPembayaranBulanan(int $tahun, int $bulan, CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        $labelPeriode = LaporanMahadService::NAMA_BULAN[$bulan].' '.$tahun;

        $pembayaran = Pembayaran::query()
            ->with(['user' => fn ($query) => $query->select('id', 'name', 'nim')])
            ->where(function ($query) use ($mulai, $akhir, $labelPeriode): void {
                $query->whereBetween('created_at', [$mulai, $akhir])
                    ->orWhere('periode', $labelPeriode)
                    ->orWhereBetween('tanggal_bayar', [$mulai, $akhir]);
            })
            ->orderBy('created_at')
            ->get();

        return $this->rangkaianPembayaran(
            'Rekap Tagihan dan Status Pembayaran',
            'Tagihan yang dibuat, jatuh bayar, atau dibayar pada periode ini.',
            $pembayaran,
            $pembayaran->map(fn (Pembayaran $item): array => [
                'nim' => $item->user?->nim ?: '-',
                'nama' => $item->user?->name ?? 'Mahasiswa',
                'tagihan' => $item->nama_tagihan ?: '-',
                'periode' => $item->periode ?: '-',
                'nominal' => (float) $item->nominal,
                'tanggal' => $item->tanggal_bayar ? $this->formatTanggal($item->tanggal_bayar) : 'Belum dibayar',
                'status' => $item->status_label,
            ])->all()
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiPembayaranTahunan(int $tahun, CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        $pembayaran = Pembayaran::query()
            ->with(['user' => fn ($query) => $query->select('id', 'name', 'nim')])
            ->where(function ($query) use ($tahun, $mulai, $akhir): void {
                $query->whereBetween('created_at', [$mulai, $akhir])
                    ->orWhereBetween('tanggal_bayar', [$mulai, $akhir])
                    ->orWhere('periode', 'like', '%'.$tahun.'%');
            })
            ->orderBy('created_at')
            ->get();

        $perBulan = [];

        foreach (LaporanMahadService::NAMA_BULAN as $nomor => $nama) {
            $perBulan[$nomor] = [
                'bulan' => $nama,
                'tagihan' => 0,
                'nominal' => 0.0,
                'terbayar' => 0.0,
                'tunggakan' => 0.0,
                'lunas' => 0,
                'menunggu' => 0,
                'belum' => 0,
            ];
        }

        $tanpaBulan = [
            'bulan' => 'Tanpa tanggal',
            'tagihan' => 0,
            'nominal' => 0.0,
            'terbayar' => 0.0,
            'tunggakan' => 0.0,
            'lunas' => 0,
            'menunggu' => 0,
            'belum' => 0,
        ];

        foreach ($pembayaran as $item) {
            $nomor = $this->bulanPembayaran($item, $tahun);
            $kunci = $nomor ?? 0;

            if ($kunci === 0) {
                $tanpaBulan['tagihan']++;
                $tanpaBulan['nominal'] += (float) $item->nominal;
                $this->tambahkanStatusPembayaran($tanpaBulan, $item);

                continue;
            }

            $perBulan[$kunci]['tagihan']++;
            $perBulan[$kunci]['nominal'] += (float) $item->nominal;
            $this->tambahkanStatusPembayaran($perBulan[$kunci], $item);
        }

        $baris = array_values($perBulan);

        if ($tanpaBulan['tagihan'] > 0) {
            $baris[] = $tanpaBulan;
        }

        foreach ($baris as $indeks => $nilai) {
            $baris[$indeks]['nominal'] = $nilai['nominal'];
            $baris[$indeks]['terbayar'] = $nilai['terbayar'];
            $baris[$indeks]['tunggakan'] = $nilai['tunggakan'];
        }

        return $this->rangkaianPembayaran(
            'Rekap Pembayaran Selama Satu Tahun',
            'Tagihan tahun '.$tahun.' yang dikelompokkan berdasarkan tanggal bayar, atau tanggal tagihan dibuat bila belum dibayar.',
            $pembayaran,
            $baris,
            true
        );
    }

    /**
     * @param  Collection<int, Pembayaran>  $pembayaran
     * @param  array<int, array<string, mixed>>  $baris
     * @return array<string, mixed>
     */
    private function rangkaianPembayaran(
        string $judul,
        string $keterangan,
        Collection $pembayaran,
        array $baris,
        bool $bulanan = false,
    ): array {
        $nominal = (float) $pembayaran->sum(fn (Pembayaran $item): float => (float) $item->nominal);
        $terbayar = (float) $pembayaran
            ->filter(fn (Pembayaran $item): bool => $item->status_label === 'Lunas')
            ->sum(fn (Pembayaran $item): float => (float) $item->nominal);

        $kolom = $bulanan
            ? [
                ['key' => 'bulan', 'label' => 'Bulan'],
                ['key' => 'tagihan', 'label' => 'Tagihan', 'align' => 'center'],
                ['key' => 'nominal', 'label' => 'Nominal Tagihan', 'format' => 'rupiah', 'align' => 'right'],
                ['key' => 'terbayar', 'label' => 'Terbayar', 'format' => 'rupiah', 'align' => 'right'],
                ['key' => 'tunggakan', 'label' => 'Tunggakan', 'format' => 'rupiah', 'align' => 'right'],
                ['key' => 'lunas', 'label' => 'Lunas', 'align' => 'center'],
                ['key' => 'menunggu', 'label' => 'Menunggu', 'align' => 'center'],
                ['key' => 'belum', 'label' => 'Belum Lunas', 'align' => 'center'],
            ]
            : [
                ['key' => 'nim', 'label' => 'NIM'],
                ['key' => 'nama', 'label' => 'Nama Mahasiswa'],
                ['key' => 'tagihan', 'label' => 'Jenis Tagihan'],
                ['key' => 'periode', 'label' => 'Periode'],
                ['key' => 'nominal', 'label' => 'Nominal', 'format' => 'rupiah', 'align' => 'right'],
                ['key' => 'tanggal', 'label' => 'Tanggal Bayar'],
                ['key' => 'status', 'label' => 'Status', 'align' => 'center'],
            ];

        return [
            'kode' => 'pembayaran',
            'judul' => $judul,
            'keterangan' => $keterangan,
            'ringkasan' => [
                ['label' => 'Jumlah Tagihan', 'nilai' => $pembayaran->count()],
                ['label' => 'Nominal Tagihan', 'nilai' => $this->formatRupiah($nominal)],
                ['label' => 'Terbayar', 'nilai' => $this->formatRupiah($terbayar)],
                ['label' => 'Tunggakan', 'nilai' => $this->formatRupiah(max(0, $nominal - $terbayar))],
                ['label' => 'Lunas', 'nilai' => $pembayaran->filter(fn (Pembayaran $item): bool => $item->status_label === 'Lunas')->count()],
            ],
            'tabel' => [[
                'judul' => $bulanan ? 'Rekap Pembayaran per Bulan' : 'Rincian Tagihan dan Pembayaran',
                'kolom' => $kolom,
                'baris' => $baris,
            ]],
            'catatan' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $baris
     */
    private function tambahkanStatusPembayaran(array &$baris, Pembayaran $item): void
    {
        $status = $item->status_label;
        $nominal = (float) $item->nominal;

        if ($status === 'Lunas') {
            $baris['lunas']++;
            $baris['terbayar'] += $nominal;

            return;
        }

        if ($status === 'Menunggu') {
            $baris['menunggu']++;

            return;
        }

        $baris['belum']++;
        $baris['tunggakan'] += $nominal;
    }

    private function bulanPembayaran(Pembayaran $item, int $tahun): ?int
    {
        if ($item->tanggal_bayar instanceof CarbonInterface && (int) $item->tanggal_bayar->year === $tahun) {
            return (int) $item->tanggal_bayar->month;
        }

        if ($item->created_at instanceof CarbonInterface && (int) $item->created_at->year === $tahun) {
            return (int) $item->created_at->month;
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiKegiatan(CarbonImmutable $mulai, CarbonImmutable $akhir, bool $tahunan): array
    {
        $kegiatan = Pengumuman::query()
            ->with('penulis:id,name')
            ->where('kategori', Pengumuman::KATEGORI_KEGIATAN)
            ->where('status', Pengumuman::STATUS_DIPUBLIKASIKAN)
            ->whereBetween('tanggal_publikasi', [$mulai->toDateString(), $akhir->toDateString()])
            ->orderBy('tanggal_publikasi')
            ->get();

        if ($tahunan) {
            $perBulan = $kegiatan->groupBy(
                fn (Pengumuman $item): string => $item->tanggal_publikasi
                    ? $item->tanggal_publikasi->format('m')
                    : ''
            );

            $rekapBulan = [];

            foreach (LaporanMahadService::NAMA_BULAN as $nomor => $nama) {
                $rekapBulan[] = [
                    'bulan' => $nama,
                    'jumlah' => $perBulan->get(sprintf('%02d', $nomor), collect())->count(),
                ];
            }

            $tabel = [
                [
                    'judul' => 'Rekap Kegiatan per Bulan',
                    'kolom' => [
                        ['key' => 'bulan', 'label' => 'Bulan'],
                        ['key' => 'jumlah', 'label' => 'Jumlah Kegiatan', 'align' => 'center'],
                    ],
                    'baris' => $rekapBulan,
                ],
                $this->tabelDaftarKegiatan($kegiatan),
            ];
        } else {
            $tabel = [$this->tabelDaftarKegiatan($kegiatan)];
        }

        return [
            'kode' => 'kegiatan',
            'judul' => 'Pelaksanaan Kegiatan Ma’had',
            'keterangan' => 'Kegiatan Ma’had yang terdokumentasi melalui pengumuman berkategori Kegiatan Ma’had dan dipublikasikan pada periode ini.',
            'ringkasan' => [
                ['label' => 'Kegiatan Terdokumentasi', 'nilai' => $kegiatan->count()],
            ],
            'tabel' => $tabel,
            'catatan' => [
                'Sistem belum memiliki tabel jadwal kegiatan tersendiri; kegiatan yang tidak diterbitkan sebagai pengumuman tidak terhitung pada rekap ini.',
            ],
        ];
    }

    /**
     * @param  Collection<int, Pengumuman>  $kegiatan
     * @return array<string, mixed>
     */
    private function tabelDaftarKegiatan(Collection $kegiatan): array
    {
        return [
            'judul' => 'Daftar Kegiatan Ma’had',
            'kolom' => [
                ['key' => 'tanggal', 'label' => 'Tanggal'],
                ['key' => 'judul', 'label' => 'Nama Kegiatan'],
                ['key' => 'penulis', 'label' => 'Penanggung Jawab'],
            ],
            'baris' => $kegiatan->map(fn (Pengumuman $item): array => [
                'tanggal' => $item->tanggal_publikasi ? $this->formatTanggal($item->tanggal_publikasi) : '-',
                'judul' => $item->judul,
                'penulis' => $item->penulis_nama ?: ($item->penulis?->name ?? '-'),
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiKehadiranBulanan(CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        $absensi = AbsensiPembelajaran::query()
            ->with(['user' => fn ($query) => $query->select('id', 'name', 'nim')])
            ->whereBetween('tanggal', [$mulai->toDateString(), $akhir->toDateString()])
            ->get();

        $rekap = $absensi
            ->groupBy('user_id')
            ->map(function (Collection $daftar): array {
                /** @var AbsensiPembelajaran|null $contoh */
                $contoh = $daftar->first();
                $status = $daftar->countBy(
                    fn (AbsensiPembelajaran $item): string => strtolower((string) $item->status)
                );

                $total = $daftar->count();
                $hadir = (int) ($status['hadir'] ?? 0);

                return [
                    'nim' => $contoh?->user?->nim ?: '-',
                    'nama' => $contoh?->user?->name ?? 'Mahasiswa',
                    'catatan' => $total,
                    'hadir' => $hadir,
                    'izin' => (int) ($status['izin'] ?? 0),
                    'sakit' => (int) ($status['sakit'] ?? 0),
                    'alpa' => (int) ($status['alpa'] ?? 0),
                    'kehadiran' => $total > 0 ? (int) round($hadir / $total * 100) : 0,
                ];
            })
            ->sortBy('nama')
            ->values();

        return $this->rangkaianKehadiran(
            'Rekap Kehadiran Mahasiswa',
            'Absensi pembelajaran yang tercatat pada periode ini, dikelompokkan per mahasiswa.',
            $rekap,
            [
                ['key' => 'nim', 'label' => 'NIM'],
                ['key' => 'nama', 'label' => 'Nama Mahasiswa'],
                ['key' => 'catatan', 'label' => 'Catatan Absensi', 'align' => 'center'],
                ['key' => 'hadir', 'label' => 'Hadir', 'align' => 'center'],
                ['key' => 'izin', 'label' => 'Izin', 'align' => 'center'],
                ['key' => 'sakit', 'label' => 'Sakit', 'align' => 'center'],
                ['key' => 'alpa', 'label' => 'Alpa', 'align' => 'center'],
                ['key' => 'kehadiran', 'label' => 'Kehadiran', 'format' => 'persen', 'align' => 'center'],
            ]
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiKehadiranTahunan(CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        $absensi = AbsensiPembelajaran::query()
            ->whereBetween('tanggal', [$mulai->toDateString(), $akhir->toDateString()])
            ->get(['id', 'user_id', 'tanggal', 'status']);

        $perBulan = $absensi->groupBy(
            fn (AbsensiPembelajaran $item): string => $item->tanggal ? $item->tanggal->format('m') : ''
        );

        $baris = [];

        foreach (LaporanMahadService::NAMA_BULAN as $nomor => $nama) {
            $daftar = $perBulan->get(sprintf('%02d', $nomor), collect());
            $status = $daftar->countBy(
                fn (AbsensiPembelajaran $item): string => strtolower((string) $item->status)
            );
            $total = $daftar->count();
            $hadir = (int) ($status['hadir'] ?? 0);

            $baris[] = [
                'bulan' => $nama,
                'catatan' => $total,
                'hadir' => $hadir,
                'izin' => (int) ($status['izin'] ?? 0),
                'sakit' => (int) ($status['sakit'] ?? 0),
                'alpa' => (int) ($status['alpa'] ?? 0),
                'kehadiran' => $total > 0 ? (int) round($hadir / $total * 100) : 0,
            ];
        }

        return $this->rangkaianKehadiran(
            'Rekap Kehadiran Mahasiswa Selama Satu Tahun',
            'Absensi pembelajaran per bulan pada tahun laporan. Setiap catatan absensi dihitung satu kali.',
            collect($baris),
            [
                ['key' => 'bulan', 'label' => 'Bulan'],
                ['key' => 'catatan', 'label' => 'Catatan Absensi', 'align' => 'center'],
                ['key' => 'hadir', 'label' => 'Hadir', 'align' => 'center'],
                ['key' => 'izin', 'label' => 'Izin', 'align' => 'center'],
                ['key' => 'sakit', 'label' => 'Sakit', 'align' => 'center'],
                ['key' => 'alpa', 'label' => 'Alpa', 'align' => 'center'],
                ['key' => 'kehadiran', 'label' => 'Kehadiran', 'format' => 'persen', 'align' => 'center'],
            ]
        );
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $baris
     * @param  array<int, array<string, mixed>>  $kolom
     * @return array<string, mixed>
     */
    private function rangkaianKehadiran(string $judul, string $keterangan, Collection $baris, array $kolom): array
    {
        $totalCatatan = (int) $baris->sum('catatan');
        $totalHadir = (int) $baris->sum('hadir');

        return [
            'kode' => 'kehadiran',
            'judul' => $judul,
            'keterangan' => $keterangan,
            'ringkasan' => [
                ['label' => 'Total Catatan Absensi', 'nilai' => $totalCatatan],
                ['label' => 'Total Hadir', 'nilai' => $totalHadir],
                ['label' => 'Izin', 'nilai' => (int) $baris->sum('izin')],
                ['label' => 'Sakit', 'nilai' => (int) $baris->sum('sakit')],
                ['label' => 'Alpa', 'nilai' => (int) $baris->sum('alpa')],
                ['label' => 'Rata-rata Kehadiran', 'nilai' => $totalCatatan > 0 ? (int) round($totalHadir / $totalCatatan * 100).'%' : '-'],
            ],
            'tabel' => [[
                'judul' => $judul,
                'kolom' => $kolom,
                'baris' => $baris->values()->all(),
            ]],
            'catatan' => [
                'Kehadiran bersumber dari absensi pembelajaran yang diisi Guru; kegiatan Ma’had tanpa pencatatan absensi tidak termasuk.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiPembelajaran(CarbonImmutable $mulai, CarbonImmutable $akhir, bool $tahunan): array
    {
        $semuaPertemuan = PertemuanPembelajaran::query()
            ->with(['mataPelajaran:id,nama,target_pertemuan', 'guru:id,name'])
            ->get();

        $kunciPeriode = $semuaPertemuan
            ->filter(fn (PertemuanPembelajaran $item): bool => $item->tanggal !== null
                && $item->tanggal->between($mulai, $akhir))
            ->groupBy(fn (PertemuanPembelajaran $item): string => $this->kunciPertemuan($item));

        $semuaKelompok = $semuaPertemuan->groupBy(
            fn (PertemuanPembelajaran $item): string => $this->kunciPertemuan($item)
        );

        $baris = $kunciPeriode->map(function (Collection $daftar, string $kunci) use ($semuaKelompok): array {
            /** @var PertemuanPembelajaran $contoh */
            $contoh = $daftar->first();
            $totalKelompok = $semuaKelompok->get($kunci, collect());
            $target = max(1, (int) ($contoh->mataPelajaran?->target_pertemuan ?? PertemuanPembelajaran::TARGET_PERTEMUAN));
            $totalSelesai = $totalKelompok
                ->filter(fn (PertemuanPembelajaran $item): bool => $item->status === PertemuanPembelajaran::STATUS_SELESAI)
                ->count();

            return [
                'mata_pelajaran' => $contoh->mataPelajaran?->nama ?: 'Mata pelajaran #'.$contoh->mata_pelajaran_id,
                'kelompok' => $contoh->kelompok ?: '-',
                'guru' => $totalKelompok
                    ->map(fn (PertemuanPembelajaran $item): string => (string) ($item->guru?->name ?? ''))
                    ->filter()
                    ->unique()
                    ->implode(', ') ?: '-',
                'pertemuan' => $daftar->count(),
                'selesai' => $daftar
                    ->filter(fn (PertemuanPembelajaran $item): bool => $item->status === PertemuanPembelajaran::STATUS_SELESAI)
                    ->count(),
                'total' => $totalKelompok->count(),
                'target' => $target,
                'capaian' => (int) round($totalSelesai / $target * 100),
            ];
        })
            ->sortBy([['mata_pelajaran', 'asc'], ['kelompok', 'asc']])
            ->values();

        return [
            'kode' => 'pembelajaran',
            'judul' => $tahunan
                ? 'Evaluasi Pembelajaran dan Pencapaian Pertemuan'
                : 'Progres Pembelajaran dan Pertemuan',
            'keterangan' => 'Pertemuan tercatat per mata pelajaran dan kelompok pada periode ini beserta capaian terhadap target pertemuan.',
            'ringkasan' => [
                ['label' => 'Mata Pelajaran', 'nilai' => $baris->pluck('mata_pelajaran')->unique()->count()],
                ['label' => 'Kelompok', 'nilai' => $baris->count()],
                ['label' => 'Pertemuan Periode', 'nilai' => (int) $baris->sum('pertemuan')],
                ['label' => 'Pertemuan Selesai', 'nilai' => (int) $baris->sum('selesai')],
                ['label' => 'Rata-rata Capaian', 'nilai' => $baris->isNotEmpty() ? (int) round($baris->avg('capaian')).'%' : '-'],
            ],
            'tabel' => [[
                'judul' => 'Progres Pertemuan per Mata Pelajaran',
                'kolom' => [
                    ['key' => 'mata_pelajaran', 'label' => 'Mata Pelajaran'],
                    ['key' => 'kelompok', 'label' => 'Kelompok'],
                    ['key' => 'guru', 'label' => 'Guru Pengampu'],
                    ['key' => 'pertemuan', 'label' => 'Pertemuan Periode', 'align' => 'center'],
                    ['key' => 'selesai', 'label' => 'Selesai Periode', 'align' => 'center'],
                    ['key' => 'total', 'label' => 'Total Tercatat', 'align' => 'center'],
                    ['key' => 'target', 'label' => 'Target', 'align' => 'center'],
                    ['key' => 'capaian', 'label' => 'Capaian', 'format' => 'persen', 'align' => 'center'],
                ],
                'baris' => $baris->all(),
            ]],
            'catatan' => [
                'Capaian dihitung dari pertemuan berstatus selesai dibandingkan target pertemuan mata pelajaran; pertemuan yang belum dimulai tidak dihitung.',
            ],
        ];
    }

    private function kunciPertemuan(PertemuanPembelajaran $item): string
    {
        return $item->mata_pelajaran_id.'|'.($item->kelompok ?: '-');
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiPerizinanBulanan(CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        $izin = SuratPerizinan::query()
            ->with(['user' => fn ($query) => $query->select('id', 'name', 'nim')])
            ->whereBetween('tanggal_mulai', [$mulai->toDateString(), $akhir->toDateString()])
            ->orderBy('tanggal_mulai')
            ->get();

        $baris = $izin->map(fn (SuratPerizinan $item): array => [
            'tanggal' => $item->created_at ? $this->formatTanggal($item->created_at) : '-',
            'nim' => $item->user?->nim ?: '-',
            'nama' => $item->user?->name ?? 'Mahasiswa',
            'jenis' => $item->labelJenis(),
            'mulai' => $item->tanggal_mulai ? $this->formatTanggal($item->tanggal_mulai) : '-',
            'selesai' => $item->tanggal_selesai ? $this->formatTanggal($item->tanggal_selesai) : '-',
            'durasi' => (int) ($item->durasi_hari ?? 0),
            'status' => $item->labelStatus(),
        ])->all();

        return $this->rangkaianPerizinan(
            'Rekap Perizinan Mahasiswa',
            'Pengajuan izin yang periode izinnya dimulai pada bulan laporan ini.',
            $baris,
            false
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiPerizinanTahunan(CarbonImmutable $mulai, CarbonImmutable $akhir): array
    {
        $izin = SuratPerizinan::query()
            ->with(['user' => fn ($query) => $query->select('id', 'name', 'nim')])
            ->whereBetween('tanggal_mulai', [$mulai->toDateString(), $akhir->toDateString()])
            ->orderBy('tanggal_mulai')
            ->get();

        $perBulan = $izin->groupBy(
            fn (SuratPerizinan $item): string => $item->tanggal_mulai ? $item->tanggal_mulai->format('m') : ''
        );

        $rekapBulan = [];

        foreach (LaporanMahadService::NAMA_BULAN as $nomor => $nama) {
            $daftar = $perBulan->get(sprintf('%02d', $nomor), collect());

            $rekapBulan[] = [
                'bulan' => $nama,
                'pengajuan' => $daftar->count(),
                'disetujui' => $daftar->filter(fn (SuratPerizinan $item): bool => $item->labelStatus() === 'Disetujui')->count(),
                'menunggu' => $daftar->filter(fn (SuratPerizinan $item): bool => $item->labelStatus() === 'Menunggu Persetujuan')->count(),
                'ditolak' => $daftar->filter(fn (SuratPerizinan $item): bool => $item->labelStatus() === 'Ditolak')->count(),
                'hari' => (int) $daftar->sum(fn (SuratPerizinan $item): int => (int) ($item->durasi_hari ?? 0)),
            ];
        }

        $rekapJenis = $izin
            ->groupBy(fn (SuratPerizinan $item): string => $item->labelJenis())
            ->map(fn (Collection $daftar, string $jenis): array => [
                'jenis' => $jenis,
                'pengajuan' => $daftar->count(),
                'disetujui' => $daftar->filter(fn (SuratPerizinan $item): bool => $item->labelStatus() === 'Disetujui')->count(),
                'ditolak' => $daftar->filter(fn (SuratPerizinan $item): bool => $item->labelStatus() === 'Ditolak')->count(),
                'hari' => (int) $daftar->sum(fn (SuratPerizinan $item): int => (int) ($item->durasi_hari ?? 0)),
            ])
            ->values()
            ->all();

        $hasil = $this->rangkaianPerizinan(
            'Rekap Perizinan Mahasiswa Selama Satu Tahun',
            'Pengajuan izin yang periode izinnya dimulai pada tahun laporan, dikelompokkan per bulan.',
            $rekapBulan,
            true
        );

        $hasil['tabel'][] = [
            'judul' => 'Rekap Perizinan per Jenis Izin',
            'kolom' => [
                ['key' => 'jenis', 'label' => 'Jenis Izin'],
                ['key' => 'pengajuan', 'label' => 'Pengajuan', 'align' => 'center'],
                ['key' => 'disetujui', 'label' => 'Disetujui', 'align' => 'center'],
                ['key' => 'ditolak', 'label' => 'Ditolak', 'align' => 'center'],
                ['key' => 'hari', 'label' => 'Total Hari', 'align' => 'center'],
            ],
            'baris' => $rekapJenis,
        ];

        return $hasil;
    }

    /**
     * @param  array<int, array<string, mixed>>  $baris
     * @return array<string, mixed>
     */
    private function rangkaianPerizinan(string $judul, string $keterangan, array $baris, bool $tahunan): array
    {
        $koleksi = collect($baris);

        $ringkasan = [
            ['label' => 'Total Pengajuan', 'nilai' => $tahunan ? (int) $koleksi->sum('pengajuan') : $koleksi->count()],
            ['label' => 'Disetujui', 'nilai' => $tahunan
                ? (int) $koleksi->sum('disetujui')
                : $koleksi->filter(fn (array $item): bool => $item['status'] === 'Disetujui')->count()],
            ['label' => 'Menunggu', 'nilai' => $tahunan
                ? (int) $koleksi->sum('menunggu')
                : $koleksi->filter(fn (array $item): bool => $item['status'] === 'Menunggu Persetujuan')->count()],
            ['label' => 'Ditolak', 'nilai' => $tahunan
                ? (int) $koleksi->sum('ditolak')
                : $koleksi->filter(fn (array $item): bool => $item['status'] === 'Ditolak')->count()],
            ['label' => 'Total Hari Izin', 'nilai' => $tahunan
                ? (int) $koleksi->sum('hari')
                : (int) $koleksi->sum('durasi')],
        ];

        $kolom = $tahunan
            ? [
                ['key' => 'bulan', 'label' => 'Bulan'],
                ['key' => 'pengajuan', 'label' => 'Pengajuan', 'align' => 'center'],
                ['key' => 'disetujui', 'label' => 'Disetujui', 'align' => 'center'],
                ['key' => 'menunggu', 'label' => 'Menunggu', 'align' => 'center'],
                ['key' => 'ditolak', 'label' => 'Ditolak', 'align' => 'center'],
                ['key' => 'hari', 'label' => 'Total Hari', 'align' => 'center'],
            ]
            : [
                ['key' => 'tanggal', 'label' => 'Tanggal Ajuan'],
                ['key' => 'nim', 'label' => 'NIM'],
                ['key' => 'nama', 'label' => 'Nama Mahasiswa'],
                ['key' => 'jenis', 'label' => 'Jenis Izin'],
                ['key' => 'mulai', 'label' => 'Mulai'],
                ['key' => 'selesai', 'label' => 'Selesai'],
                ['key' => 'durasi', 'label' => 'Durasi (hari)', 'align' => 'center'],
                ['key' => 'status', 'label' => 'Status', 'align' => 'center'],
            ];

        return [
            'kode' => 'perizinan',
            'judul' => $judul,
            'keterangan' => $keterangan,
            'ringkasan' => $ringkasan,
            'tabel' => [[
                'judul' => $tahunan ? 'Rekap Izin per Bulan' : 'Daftar Pengajuan Izin',
                'kolom' => $kolom,
                'baris' => $baris,
            ]],
            'catatan' => [
                'Izin dikelompokkan berdasarkan tanggal mulai izin agar satu pengajuan tidak terhitung pada dua periode.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seksiCatatan(string $evaluasi, string $kendala): array
    {
        return [
            'kode' => 'evaluasi',
            'judul' => 'Catatan Evaluasi dan Kendala',
            'keterangan' => 'Diisi Musyrifah saat laporan dibuat dan tersimpan bersama laporan.',
            'ringkasan' => [],
            'tabel' => [],
            'teks' => [
                'Catatan evaluasi' => $evaluasi !== '' ? $evaluasi : 'Belum diisi.',
                'Kendala selama periode' => $kendala !== '' ? $kendala : 'Belum diisi.',
            ],
            'catatan' => [],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function keterbatasan(): array
    {
        return [
            'Data kamar dan penghuni mengikuti kondisi terakhir sistem karena belum ada riwayat penghunian per periode.',
            'Kegiatan Ma’had diambil dari pengumuman berkategori Kegiatan Ma’had yang dipublikasikan; tidak ada tabel jadwal kegiatan khusus.',
            'Kehadiran dihitung dari absensi pembelajaran yang diisi Guru, bukan absensi kegiatan harian Ma’had.',
            'Data program studi dan angkatan mahasiswa diambil dari pendaftaran terakhir masing-masing mahasiswa.',
            'Laporan ini adalah potret data saat dibangkitkan; perubahan data operasional setelahnya tidak mengubah isi laporan.',
        ];
    }

    /**
     * @param  Collection<int, Pendaftaran>  $pendaftar
     */
    private function hitungStatus(Collection $pendaftar, string $status): int
    {
        return $pendaftar
            ->filter(fn (Pendaftaran $item): bool => strtolower((string) $item->status) === $status)
            ->count();
    }

    private function cakupan(CarbonImmutable $mulai, CarbonImmutable $akhir): string
    {
        return $this->formatTanggal($mulai).' s.d. '.$this->formatTanggal($akhir);
    }

    private function formatTanggal(CarbonInterface $tanggal): string
    {
        return $tanggal->format('d').' '.LaporanMahadService::NAMA_BULAN[(int) $tanggal->month].' '.$tanggal->year;
    }

    private function formatTanggalWaktu(CarbonInterface $tanggal): string
    {
        return $this->formatTanggal($tanggal).', '.$tanggal->format('H.i').' WIB';
    }

    public function formatRupiah(float $nilai): string
    {
        return 'Rp '.number_format($nilai, 0, ',', '.');
    }
}
