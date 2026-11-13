<?php

namespace App\Services;

use App\Models\AbsensiPembelajaran;
use App\Models\ActivityLog;
use App\Models\JadwalPembelajaran;
use App\Models\Kamar;
use App\Models\KelompokBelajar;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\PengajuanKamar;
use App\Models\PengaturanPembayaran;
use App\Models\PengaturanSistem;
use App\Models\PengumpulanTugas;
use App\Models\Pengumuman;
use App\Models\SuratPerizinan;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Ringkasan dashboard per peran yang seluruhnya bersumber dari database.
 *
 * Tidak ada angka contoh: bila data belum tersedia, nilai nol dan daftar
 * kosong dikembalikan agar tampilan dapat menampilkan kondisi kosong.
 */
class DashboardRingkasanService
{
    private const ZONA_WAKTU = 'Asia/Jakarta';

    private const STATUS_LUNAS = ['lunas', 'paid', 'terkonfirmasi'];

    private const STATUS_MENUNGGU_BAYAR = ['menunggu', 'menunggu_konfirmasi', 'pending'];

    private const STATUS_PENDAFTARAN_MENUNGGU = ['menunggu', 'pending', 'baru'];

    private const STATUS_AKUN_MENUNGGU = ['menunggu aktivasi', 'menunggu', 'pending'];

    public function __construct(private readonly PembelajaranGuruService $pembelajaran) {}

    /**
     * Ringkasan operasional harian Musyrifah.
     *
     * @return array<string, mixed>
     */
    public function musyrifah(): array
    {
        $kamar = Kamar::query()->withCount('users')->get();
        $kapasitas = (int) $kamar->sum('kapasitas');
        $terisi = (int) $kamar->sum('users_count');

        $hariIni = JadwalPembelajaran::HARI[now()->dayOfWeekIso] ?? 'Senin';
        $jadwalHariIni = JadwalPembelajaran::query()->where('hari', $hariIni)->get();
        $absensiHariIni = AbsensiPembelajaran::query()
            ->whereDate('tanggal', now()->toDateString())
            ->distinct()
            ->count('jadwal_id');

        return [
            'totalMahasiswa' => $this->jumlahMahasiswa(),
            'pendaftaran' => [
                'menunggu' => Pendaftaran::query()
                    ->whereIn('status', self::STATUS_PENDAFTARAN_MENUNGGU)
                    ->count(),
                'disetujui' => Pendaftaran::query()->where('status', 'disetujui')->count(),
                'terbaru' => Pendaftaran::query()
                    ->whereIn('status', self::STATUS_PENDAFTARAN_MENUNGGU)
                    ->latest('id')
                    ->limit(5)
                    ->get(),
            ],
            'kamar' => [
                'jumlah' => $kamar->count(),
                'kapasitas' => $kapasitas,
                'terisi' => $terisi,
                'sisa' => max(0, $kapasitas - $terisi),
                'persentase' => $kapasitas > 0 ? round(($terisi / $kapasitas) * 100, 1) : 0.0,
                'per_lantai' => $this->kamarPerLantai($kamar),
            ],
            'pengajuanKamar' => [
                'menunggu' => PengajuanKamar::query()->where('status', PengajuanKamar::STATUS_MENUNGGU)->count(),
                'terbaru' => PengajuanKamar::query()
                    ->with(['user', 'kamar'])
                    ->where('status', PengajuanKamar::STATUS_MENUNGGU)
                    ->latest('id')
                    ->limit(5)
                    ->get(),
            ],
            'pembayaran' => $this->ringkasanPembayaran(),
            'suratPerizinan' => [
                'menunggu' => SuratPerizinan::query()->where('status', SuratPerizinan::STATUS_MENUNGGU)->count(),
                'perluPerbaikan' => SuratPerizinan::query()
                    ->where('status', SuratPerizinan::STATUS_PERLU_PERBAIKAN)
                    ->count(),
                'semesterMenunggu' => SuratPerizinan::query()
                    ->where('jenis', SuratPerizinan::JENIS_SEMESTER)
                    ->whereIn('status', [
                        SuratPerizinan::STATUS_MENUNGGU,
                        SuratPerizinan::STATUS_PERLU_PERBAIKAN,
                    ])
                    ->count(),
                'terbaru' => SuratPerizinan::query()
                    ->with('user')
                    ->whereIn('status', [SuratPerizinan::STATUS_MENUNGGU, SuratPerizinan::STATUS_PERLU_PERBAIKAN])
                    ->latest('id')
                    ->limit(5)
                    ->get(),
            ],
            'pembelajaran' => [
                'kelompok' => $this->jumlahKelompok(),
                'jadwalHariIni' => $jadwalHariIni->count(),
                'hariIni' => $hariIni,
                'absensiBelumDiisi' => max(0, $jadwalHariIni->count() - $absensiHariIni),
                'jadwal' => $jadwalHariIni->sortBy('jam_mulai')->take(5)->values(),
            ],
            'pengumuman' => $this->pengumumanTerbaru(),
            'dokumentasi' => [
                'jumlah' => Pengumuman::query()
                    ->dipublikasikan()
                    ->where('kategori', Pengumuman::KATEGORI_KEGIATAN)
                    ->count(),
                'terbaru' => Pengumuman::query()
                    ->dipublikasikan()
                    ->where('kategori', Pengumuman::KATEGORI_KEGIATAN)
                    ->orderByDesc('tanggal_publikasi')
                    ->orderByDesc('id')
                    ->limit(3)
                    ->get(),
            ],
            'zonaWaktu' => self::ZONA_WAKTU,
        ];
    }

    /**
     * Ringkasan monitoring Pimpinan (bukan operasional harian).
     *
     * @return array<string, mixed>
     */
    public function pimpinan(): array
    {
        $kamar = Kamar::query()->withCount('users')->get();
        $kapasitas = (int) $kamar->sum('kapasitas');
        $terisi = (int) $kamar->sum('users_count');

        $absensiTotal = AbsensiPembelajaran::query()->count();
        $absensiHadir = AbsensiPembelajaran::query()->where('status', 'hadir')->count();

        $tugasTotal = Tugas::query()->dipublikasikan()->count();
        $tugasDinilai = PengumpulanTugas::query()
            ->whereNotNull('nilai')
            ->distinct()
            ->count('tugas_id');

        return [
            'totalMahasiswa' => $this->jumlahMahasiswa(),
            'pendaftaran' => [
                'total' => Pendaftaran::query()->count(),
                'menunggu' => Pendaftaran::query()->whereIn('status', self::STATUS_PENDAFTARAN_MENUNGGU)->count(),
                'disetujui' => Pendaftaran::query()->where('status', 'disetujui')->count(),
                'ditolak' => Pendaftaran::query()->where('status', 'ditolak')->count(),
                'per_bulan' => $this->pendaftaranPerBulan(),
            ],
            'kamar' => [
                'jumlah' => $kamar->count(),
                'kapasitas' => $kapasitas,
                'terisi' => $terisi,
                'sisa' => max(0, $kapasitas - $terisi),
                'persentase' => $kapasitas > 0 ? round(($terisi / $kapasitas) * 100, 1) : 0.0,
                'per_lantai' => $this->kamarPerLantai($kamar),
            ],
            'pembayaran' => $this->ringkasanPembayaran(),
            'pembelajaran' => [
                'kelompok' => $this->jumlahKelompok(),
                'mataPelajaran' => JadwalPembelajaran::query()->distinct()->count('mata_pelajaran_id'),
                'jadwal' => JadwalPembelajaran::query()->count(),
                'tugas' => $tugasTotal,
                'tugasDinilai' => $tugasDinilai,
                'penyelesaian' => $tugasTotal > 0 ? round(($tugasDinilai / $tugasTotal) * 100, 1) : 0.0,
            ],
            'absensi' => [
                'total' => $absensiTotal,
                'hadir' => $absensiHadir,
                'persentase' => $absensiTotal > 0 ? round(($absensiHadir / $absensiTotal) * 100, 1) : 0.0,
            ],
            'perizinan' => [
                'total' => SuratPerizinan::query()->count(),
                'menunggu' => SuratPerizinan::query()->where('status', SuratPerizinan::STATUS_MENUNGGU)->count(),
                'disetujui' => SuratPerizinan::query()->where('status', SuratPerizinan::STATUS_DISETUJUI)->count(),
                'selesai' => SuratPerizinan::query()->where('status', SuratPerizinan::STATUS_SELESAI)->count(),
            ],
            'aktivitas' => $this->aktivitasTerbaru(6),
        ];
    }

    /**
     * Ringkasan penugasan Guru sesuai jadwal yang menjadi tanggung jawabnya.
     *
     * @return array<string, mixed>
     */
    public function guru(User $guru): array
    {
        $jadwal = JadwalPembelajaran::jadwalUntuk($guru);
        $penugasan = JadwalPembelajaran::penugasanUntuk($guru);

        $zona = self::ZONA_WAKTU;

        $jadwalHariIni = $jadwal
            ->filter(fn (JadwalPembelajaran $item) => $item->nomorHari() === now($zona)->dayOfWeekIso)
            ->sortBy('jam_mulai')
            ->values();

        // Absensi yang sudah diisi hari ini untuk jadwal milik Guru ini saja.
        $sudahDiabsen = $jadwalHariIni->isEmpty()
            ? collect()
            : AbsensiPembelajaran::query()
                ->whereDate('tanggal', now($zona)->toDateString())
                ->whereIn('jadwal_id', $jadwalHariIni->pluck('id')->all())
                ->distinct()
                ->pluck('jadwal_id');

        $daftarTugas = Tugas::query()
            ->where('user_id', $guru->getKey())
            ->withCount('pengumpulan')
            ->latest('id')
            ->get();

        $aktif = $daftarTugas->where('status', Tugas::STATUS_DIPUBLIKASIKAN);
        $idAktif = $aktif->pluck('id');

        // Jumlah pengumpulan yang belum dinilai per tugas aktif milik Guru.
        $belumDinilai = $idAktif->isEmpty()
            ? collect()
            : PengumpulanTugas::query()
                ->selectRaw('tugas_id, COUNT(*) as jumlah')
                ->whereIn('tugas_id', $idAktif)
                ->whereNull('nilai')
                ->groupBy('tugas_id')
                ->pluck('jumlah', 'tugas_id');

        $progres = $this->progresPenugasan($jadwal);

        return [
            'jadwal' => $jadwal->sortBy('jam_mulai')->take(6)->values(),
            'jadwalHariIni' => $jadwalHariIni->take(5),
            'jadwalBerikutnya' => $this->jadwalBerikutnya($jadwal),
            'jumlahJadwal' => $jadwal->count(),
            'jumlahKelompok' => $penugasan->pluck('kelompok')->filter()->unique()->count(),
            'jumlahMapel' => $penugasan->pluck('mata_pelajaran_id')->filter()->unique()->count(),
            'kelompok' => $penugasan->pluck('kelompok')->filter()->unique()->values(),
            'mataPelajaran' => $penugasan
                ->pluck('nama_mata_pelajaran')
                ->filter()
                ->unique()
                ->values(),
            'progresPenugasan' => $progres,
            'pertemuanSelesai' => (int) $progres->sum('selesai'),
            'targetPertemuan' => $progres->count() * PembelajaranGuruService::TARGET_PERTEMUAN,
            'tugasAktif' => $aktif->count(),
            'tugasDraft' => $daftarTugas->where('status', Tugas::STATUS_DRAFT)->count(),
            'tugasDaftar' => $daftarTugas->take(5)->values(),
            'jumlahPengumpulan' => (int) $daftarTugas->sum('pengumpulan_count'),
            'belumDinilai' => (int) $belumDinilai->sum(),
            'perluDiperiksa' => $aktif
                ->filter(fn (Tugas $tugas) => (int) ($belumDinilai[$tugas->getKey()] ?? 0) > 0)
                ->map(fn (Tugas $tugas) => [
                    'tugas' => $tugas,
                    'jumlah' => (int) $belumDinilai[$tugas->getKey()],
                ])
                ->sortByDesc('jumlah')
                ->take(5)
                ->values(),
            'absensi' => $this->rekapAbsensiGuru($jadwal),
            'absensiHariIni' => $jadwalHariIni->count(),
            'absensiBelumDiisi' => max(0, $jadwalHariIni->count() - $sudahDiabsen->count()),
            'pengumuman' => $this->pengumumanTerbaru(3),
            'aktivitas' => $this->aktivitasUntukGuru($guru, 5),
            'zonaWaktu' => self::ZONA_WAKTU,
        ];
    }

    /**
     * Progres 16 pertemuan untuk setiap penugasan unik
     * (mata pelajaran + kelompok + periode).
     *
     * @param  Collection<int, JadwalPembelajaran>  $jadwal
     * @return Collection<int, array<string, mixed>>
     */
    private function progresPenugasan(Collection $jadwal): Collection
    {
        return $jadwal
            ->unique(fn (JadwalPembelajaran $item) => implode('|', [
                $item->mata_pelajaran_id,
                $item->kelompok,
                $this->pembelajaran->periode($item),
            ]))
            ->map(function (JadwalPembelajaran $item) {
                $progres = $this->pembelajaran->progres($item);

                return [
                    'jadwal' => $item,
                    'mapel' => $item->namaMataPelajaran(),
                    'kelompok' => $item->kelompok ?: '-',
                    'periode' => $this->pembelajaran->periode($item),
                    'selesai' => $progres['selesai'],
                    'berlangsung' => $progres['berlangsung'],
                    'berikutnya' => $progres['berikutnya'],
                    'persentase' => $progres['persentase'],
                    'target' => PembelajaranGuruService::TARGET_PERTEMUAN,
                ];
            })
            ->sortBy(fn (array $baris) => $baris['mapel'].'|'.$baris['kelompok'].'|'.$baris['periode'])
            ->values();
    }

    /**
     * Jadwal mengajar terdekat dari seluruh penugasan Guru.
     *
     * @param  Collection<int, JadwalPembelajaran>  $jadwal
     */
    private function jadwalBerikutnya(Collection $jadwal): ?array
    {
        if ($jadwal->isEmpty()) {
            return null;
        }

        $sekarang = now(self::ZONA_WAKTU);

        $terdekat = $jadwal
            ->map(function (JadwalPembelajaran $item) use ($sekarang) {
                [$jam, $menit] = array_pad(
                    explode(':', substr((string) $item->jam_mulai, 0, 5)),
                    2,
                    '0'
                );

                $selisih = (
                    $item->nomorHari() - $sekarang->dayOfWeekIso + 7
                ) % 7;

                $waktu = $sekarang->copy()
                    ->startOfDay()
                    ->addDays($selisih)
                    ->setTime((int) $jam, (int) $menit);

                if ($waktu->lessThan($sekarang)) {
                    $waktu->addWeek();
                }

                return [
                    'jadwal' => $item,
                    'waktu' => $waktu,
                    'kapan' => $waktu->isToday()
                        ? 'Hari ini'
                        : ($waktu->isTomorrow()
                            ? 'Besok'
                            : $waktu->locale('id')->translatedFormat('l')),
                ];
            })
            ->sortBy(fn (array $item) => $item['waktu']->getTimestamp())
            ->first();

        if ($terdekat === null) {
            return null;
        }

        /** @var JadwalPembelajaran $jadwalBerikut */
        $jadwalBerikut = $terdekat['jadwal'];

        return [
            'jadwal' => $jadwalBerikut,
            'kapan' => $terdekat['kapan'],
            'tanggal' => $terdekat['waktu']->locale('id')->translatedFormat('d F Y'),
            'jam' => $jadwalBerikut->jamTeks(),
            'mapel' => $jadwalBerikut->namaMataPelajaran(),
            'kelompok' => $jadwalBerikut->kelompok ?: '-',
            'ruangan' => $jadwalBerikut->ruangan ?: 'Ruang Kelas',
        ];
    }

    /**
     * Rekap absensi dari seluruh jadwal milik Guru.
     *
     * @param  Collection<int, JadwalPembelajaran>  $jadwal
     * @return array{hadir: int, izin: int, sakit: int, alpa: int, total: int, persentaseHadir: float}
     */
    private function rekapAbsensiGuru(Collection $jadwal): array
    {
        $idJadwal = $jadwal->pluck('id')->filter()->all();

        $pasangan = $jadwal
            ->filter(fn (JadwalPembelajaran $item) => filled($item->mata_pelajaran_id) && filled($item->kelompok)
            )
            ->map(fn (JadwalPembelajaran $item) => [
                'mata_pelajaran_id' => (int) $item->mata_pelajaran_id,
                'kelompok' => (string) $item->kelompok,
            ])
            ->unique(fn (array $item) => $item['mata_pelajaran_id'].'|'.$item['kelompok']
            )
            ->values();

        $filterPasangan = function (Builder $query) use ($pasangan): void {
            foreach ($pasangan as $item) {
                $query->orWhere(fn (Builder $sub) => $sub
                    ->where('mata_pelajaran_id', $item['mata_pelajaran_id'])
                    ->where('kelompok', $item['kelompok'])
                );
            }
        };

        $hitungan = collect();

        if ($idJadwal !== [] || $pasangan->isNotEmpty()) {
            $hitungan = AbsensiPembelajaran::query()
                ->where(function (Builder $query) use (
                    $idJadwal,
                    $pasangan,
                    $filterPasangan
                ) {
                    if ($idJadwal === []) {
                        $filterPasangan($query);

                        return;
                    }

                    $query->whereIn('jadwal_id', $idJadwal);

                    if ($pasangan->isNotEmpty()) {
                        $query->orWhere(fn (Builder $lama) => $lama
                            ->whereNull('jadwal_id')
                            ->where(fn (Builder $sub) => $filterPasangan($sub))
                        );
                    }
                })
                ->selectRaw('status, COUNT(*) as jumlah')
                ->groupBy('status')
                ->pluck('jumlah', 'status');
        }

        $hadir = (int) ($hitungan['hadir'] ?? 0);
        $total = (int) $hitungan->sum();

        return [
            'hadir' => $hadir,
            'izin' => (int) ($hitungan['izin'] ?? 0),
            'sakit' => (int) ($hitungan['sakit'] ?? 0),
            'alpa' => (int) ($hitungan['alpa'] ?? 0),
            'total' => $total,
            'persentaseHadir' => $total > 0
                ? round(($hadir / $total) * 100, 1)
                : 0.0,
        ];
    }

    /**
     * Ringkasan teknis Admin Sistem:
     * akun, aktivitas login, log, dan konfigurasi.
     *
     * @return array<string, mixed>
     */
    public function admin(): array
    {
        $perRole = User::query()
            ->selectRaw('LOWER(role) as peran, COUNT(*) as jumlah')
            ->groupBy('peran')
            ->pluck('jumlah', 'peran');

        $zona = self::ZONA_WAKTU;

        $awalHari = now($zona)->startOfDay()->utc();
        $awalTujuhHari = now($zona)->subDays(6)->startOfDay()->utc();
        $seriAktivitas = $this->seriAktivitasHarian();

        return [
            'totalAkun' => User::query()->count(),
            'akunAktif' => User::query()->where('status', 'aktif')->count(),
            'akunMenunggu' => User::query()->whereIn('status', self::STATUS_AKUN_MENUNGGU)->count(),
            'akunNonaktif' => User::query()->where('status', 'nonaktif')->count(),
            'perRole' => [
                'mahasiswa' => (int) ($perRole['mahasiswa'] ?? 0),
                'guru' => (int) ($perRole['guru'] ?? 0),
                'musyrifah' => (int) ($perRole['musyrifah'] ?? 0),
                'pimpinan' => (int) ($perRole['pimpinan'] ?? 0),
                'admin' => (int) ($perRole['admin'] ?? 0),
            ],
            'akunTerbaru' => User::query()->latest('id')->limit(5)->get(['id', 'name', 'email', 'role', 'status', 'created_at']),
            'aktivitas' => [
                'hariIni' => ActivityLog::query()->where('created_at', '>=', $awalHari)->count(),
                'tujuhHari' => array_sum(array_column($seriAktivitas, 'jumlah')),
                'loginHariIni' => User::query()->where('last_login_at', '>=', $awalHari)->count(),
                'akunBaruTujuhHari' => User::query()->where('created_at', '>=', $awalTujuhHari)->count(),
            ],
            'aktivitasHarian' => $seriAktivitas,
            'aktivitasTerbaru' => ActivityLog::query()->latest('id')->limit(25)->get(),
            'zonaWaktu' => $zona,
            'konfigurasi' => $this->statusKonfigurasi(),
        ];
    }

    /**
     * Jumlah akun mahasiswa.
     *
     * Dibuka untuk publik agar halaman Monitoring Pimpinan memakai
     * sumber perhitungan yang sama dengan dashboard.
     */
    public function jumlahMahasiswa(): int
    {
        return User::query()->whereRaw('LOWER(role) = ?', ['mahasiswa'])->count();
    }

    /**
     * Jumlah kelompok belajar yang tercatat.
     *
     * Dibuka untuk publik agar rekap Monitoring Pembelajaran konsisten
     * dengan dashboard Pimpinan.
     */
    public function jumlahKelompok(): int
    {
        return Schema::hasTable('kelompok_belajars')
            ? KelompokBelajar::query()->count()
            : JadwalPembelajaran::query()->distinct()->count('kelompok');
    }

    /**
     * Keterisian kamar per lantai untuk laporan gabungan.
     *
     * @param  Collection<int, Kamar>  $kamar
     * @return Collection<int, array<string, mixed>>
     */
    public function kamarPerLantai(Collection $kamar): Collection
    {
        return $kamar
            ->groupBy(fn (Kamar $item) => (int) ($item->lantai ?: 1))
            ->sortKeys()
            ->map(fn (Collection $items, int $lantai) => [
                'lantai' => $lantai,
                'jumlah' => $items->count(),
                'kapasitas' => (int) $items->sum('kapasitas'),
                'terisi' => (int) $items->sum('users_count'),
                'sisa' => max(0, (int) $items->sum('kapasitas') - (int) $items->sum('users_count')),
            ])
            ->values();
    }

    /**
     * Ringkasan pembayaran satu periode.
     *
     * Tanpa argumen, periode yang dipakai adalah periode pembayaran aktif
     * (perilaku dashboard). Halaman Monitoring Pimpinan dapat meminta
     * periode tertentu tetapi tetap memakai rumus yang sama.
     *
     * @return array<string, mixed>
     */
    public function ringkasanPembayaran(?string $periode = null): array
    {
        $pengaturan = PengaturanPembayaran::aktif();

        // Periode yang dipakai: pilihan eksplisit, periode konfigurasi aktif,
        // atau periode terakhir yang benar-benar memiliki data pembayaran.
        // Tidak ada periode maupun angka yang dikarang.
        $periodeTarget = $periode
            ?: $pengaturan?->periode
            ?: Pembayaran::query()->orderByDesc('id')->value('periode');

        if (! $periodeTarget) {
            return [
                'periode' => null,
                'tahap' => null,
                'nominal' => 0.0,
                'nominalRupiah' => 'Rp 0',
                'jatuhTempo' => null,
                'dibuka' => false,
                'lunas' => 0,
                'menunggu' => 0,
                'belum' => 0,
                'totalTagihan' => 0,
                'persentase' => 0.0,
                'pengingat' => false,
                'sisaHari' => null,
            ];
        }

        $query = Pembayaran::query()->where('periode', $periodeTarget);
        $lunas = (clone $query)->whereIn('status', self::STATUS_LUNAS)->count();
        $menunggu = (clone $query)->whereIn('status', self::STATUS_MENUNGGU_BAYAR)->count();
        $totalMahasiswa = $this->jumlahMahasiswa();

        // Konteks pengaturan hanya relevan untuk periode yang sedang aktif.
        $pengaturanPeriode = $pengaturan?->periode === $periodeTarget ? $pengaturan : null;
        $nominal = (float) ($pengaturanPeriode?->nominal ?? 0);

        return [
            'periode' => $periodeTarget,
            'tahap' => $pengaturanPeriode?->nama_tahap,
            'nominal' => $nominal,
            'nominalRupiah' => 'Rp '.number_format($nominal, 0, ',', '.'),
            'jatuhTempo' => $pengaturanPeriode?->jatuhTempoTeks(),
            'dibuka' => $pengaturanPeriode?->sudahDibuka() ?? false,
            'lunas' => $lunas,
            'menunggu' => $menunggu,
            'belum' => max(0, $totalMahasiswa - $lunas - $menunggu),
            'totalTagihan' => $totalMahasiswa,
            'persentase' => $totalMahasiswa > 0 ? round(($lunas / $totalMahasiswa) * 100, 1) : 0.0,
            'pengingat' => $pengaturanPeriode?->dalamMasaPengingat() ?? false,
            'sisaHari' => $pengaturanPeriode?->sisaHari(),
        ];
    }

    /**
     * Jumlah pendaftaran per bulan untuk grafik perkembangan (6 bulan terakhir).
     *
     * @return Collection<int, array{label: string, jumlah: int}>
     */
    private function pendaftaranPerBulan(): Collection
    {
        $awal = now()->startOfMonth()->subMonths(5);
        $data = Pendaftaran::query()
            ->where('created_at', '>=', $awal)
            ->get(['id', 'created_at'])
            ->groupBy(fn (Pendaftaran $item) => $item->created_at?->format('Y-m'));

        $hasil = collect();

        for ($i = 0; $i < 6; $i++) {
            $bulan = $awal->copy()->addMonths($i);
            $hasil->push([
                'label' => $bulan->locale('id')->translatedFormat('M y'),
                'jumlah' => (int) ($data[$bulan->format('Y-m')] ?? collect())->count(),
            ]);
        }

        return $hasil;
    }

    /**
     * @return Collection<int, Pengumuman>
     */
    private function pengumumanTerbaru(int $batas = 3): Collection
    {
        return Pengumuman::query()
            ->dipublikasikan()
            ->orderByDesc('tanggal_publikasi')
            ->orderByDesc('id')
            ->limit($batas)
            ->get();
    }

    /**
     * @return Collection<int, ActivityLog>
     */
    private function aktivitasTerbaru(int $batas): Collection
    {
        return ActivityLog::query()->latest('id')->limit($batas)->get();
    }

    /**
     * Jumlah aktivitas per hari selama beberapa hari terakhir (zona WIB).
     *
     * @return array<int, array{tanggal: string, label: string, jumlah: int}>
     */
    private function seriAktivitasHarian(int $hari = 7): array
    {
        $awal = now(self::ZONA_WAKTU)->subDays($hari - 1)->startOfDay()->utc();

        $harian = ActivityLog::query()
            ->where('created_at', '>=', $awal)
            ->pluck('created_at')
            ->filter()
            ->countBy(fn ($waktu): string => $waktu->copy()->timezone(self::ZONA_WAKTU)->toDateString());

        return collect(range($hari - 1, 0))
            ->map(function (int $mundur) use ($harian): array {
                $tanggal = now(self::ZONA_WAKTU)->subDays($mundur)->startOfDay();
                $kunci = $tanggal->toDateString();

                return [
                    'tanggal' => $kunci,
                    'label' => $tanggal->locale('id')->translatedFormat('D'),
                    'jumlah' => (int) ($harian[$kunci] ?? 0),
                ];
            })
            ->all();
    }

    /**
     * Aktivitas Guru: log milik sendiri dan pengumpulan tugas terbaru.
     *
     * @return Collection<int, array{judul: string, keterangan: string, waktu: ?Carbon}>
     */
    private function aktivitasUntukGuru(User $guru, int $batas): Collection
    {
        $log = ActivityLog::query()
            ->where('user_id', $guru->getKey())
            ->latest('id')
            ->limit($batas)
            ->get()
            ->map(fn (ActivityLog $item) => [
                'judul' => (string) $item->description,
                'keterangan' => 'Aktivitas akun',
                'waktu' => $item->created_at,
            ]);

        $pengumpulan = PengumpulanTugas::query()
            ->whereIn('tugas_id', Tugas::query()->where('user_id', $guru->getKey())->select('id'))
            ->with(['mahasiswa', 'tugas'])
            ->latest('dikumpulkan_pada')
            ->limit($batas)
            ->get()
            ->map(fn (PengumpulanTugas $item) => [
                'judul' => $item->mahasiswa?->name ?? 'Mahasiswa',
                'keterangan' => 'Mengumpulkan tugas '.($item->tugas?->judul ?? ''),
                'waktu' => $item->dikumpulkan_pada,
            ]);

        return $log->concat($pengumpulan)
            ->sortByDesc(fn (array $item) => $item['waktu']?->getTimestamp() ?? 0)
            ->take($batas)
            ->values();
    }

    /**
     * Status konfigurasi sistem yang relevan untuk Admin Sistem.
     *
     * @return array<string, mixed>
     */
    private function statusKonfigurasi(): array
    {
        $sistem = Schema::hasTable('pengaturan_sistems') ? PengaturanSistem::aktif() : null;

        return [
            'nama_sistem' => $sistem?->nama_sistem ?? (string) config('app.name'),
            'nama_institusi' => $sistem?->nama_institusi ?? '-',
            'durasi_sesi' => $sistem?->durasi_sesi,
            'notifikasi' => $sistem ? [
                'pengumuman' => (bool) $sistem->notifikasi_pengumuman,
                'pembelajaran' => (bool) $sistem->notifikasi_pembelajaran,
                'pembayaran' => (bool) $sistem->notifikasi_pembayaran,
            ] : null,
            'mail' => (string) config('mail.default'),
            'google_login' => filled(config('services.google.client_id'))
                && filled(config('services.google.client_secret')),
            'debug' => (bool) config('app.debug'),
        ];
    }
}
