<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\PengumpulanTugas;
use App\Models\Pengumuman;
use App\Models\SuratPerizinan;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MahasiswaDashboardController extends Controller
{
    /**
     * Dashboard mahasiswa: ringkasan profil, status, pembayaran, absensi,
     * kalender pembelajaran, tugas, nilai, pengumuman, perizinan, dan
     * notifikasi yang seluruhnya bersumber dari database.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $role = strtolower(trim((string) $user->role));

        if (in_array($role, ['guru', 'musyrifah', 'admin', 'pimpinan'], true)) {
            return redirect()->route($this->ruteDashboard($role));
        }

        $kamar = $user->kamar;
        $penempatan = $user->penempatanKelompokBelajarAktif;
        $kelompok = $penempatan?->kelompokBelajar?->nama_kelompok;
        $pendaftaran = Pendaftaran::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        $ringkasanPembayaran = Pembayaran::ringkasanUntuk($user);

        $absensi = $this->ringkasanAbsensi($user, $kelompok);
        $kalender = $this->kalenderPembelajaran($request, $kelompok);
        $tugas = $this->ringkasanTugas($user);

        $this->siapkanPengingat($user, $ringkasanPembayaran, $tugas['semua']);

        $notifikasi = $user->notifikasis()
            ->latest('id')
            ->limit(8)
            ->get();

        return view('Mahasiswa.dashboard', [
            'user' => $user,
            'kamar' => $kamar,
            'kelompok' => $kelompok,
            'belumAdaKelompok' => blank($kelompok),
            'pendaftaran' => $pendaftaran,
            'ringkasanPembayaran' => $ringkasanPembayaran,
            'absensi' => $absensi,
            'kalender' => $kalender,
            'tugasTerbaru' => $tugas['terbaru'],
            'jumlahTugasBelum' => $tugas['belum'],
            'nilaiTerbaru' => $this->nilaiTerbaru($user),
            'pengumumanTerbaru' => Pengumuman::query()
                ->dipublikasikan()
                ->orderByDesc('tanggal_publikasi')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
            'perizinan' => $this->ringkasanPerizinan($user),
            'notifikasi' => $notifikasi,
            'jumlahNotifikasiBelumDibaca' => $user->notifikasis()->belumDibaca()->count(),
        ]);
    }

    /** Limit learning records to the student's single active group. */
    private function batasiKelompokAktif(Builder $query, ?string $kelompok): Builder
    {
        if (blank($kelompok)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('kelompok', $kelompok);
    }

    /**
     * Rekap kehadiran pada kelompok aktif mahasiswa.
     *
     * @return array{total: int, hadir: int, izin: int, sakit: int, alpa: int, tidak_hadir: int, persentase: float}
     */
    private function ringkasanAbsensi(User $user, ?string $kelompok): array
    {
        $query = $this->batasiKelompokAktif(
            AbsensiPembelajaran::query()->where('user_id', $user->id),
            $kelompok
        );

        $hadir = (clone $query)->where('status', 'hadir')->count();
        $izin = (clone $query)->where('status', 'izin')->count();
        $sakit = (clone $query)->where('status', 'sakit')->count();
        $alpa = (clone $query)->where('status', 'alpa')->count();
        $total = (clone $query)->count();

        return [
            'total' => $total,
            'hadir' => $hadir,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpa' => $alpa,
            'tidak_hadir' => $total - $hadir,
            'persentase' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0.0,
        ];
    }

    /**
     * Kalender bulanan jadwal sesuai kelompok aktif mahasiswa.
     *
     * @return array<string, mixed>
     */
    private function kalenderPembelajaran(Request $request, ?string $kelompok): array
    {
        $bulan = (string) $request->query('bulan', '');
        $awalBulan = preg_match('/^\d{4}-\d{2}$/', $bulan) === 1
            ? Carbon::createFromFormat('Y-m-d', $bulan.'-01')->startOfMonth()
            : Carbon::now()->startOfMonth();

        $jadwal = $this->batasiKelompokAktif(JadwalPembelajaran::query(), $kelompok)
            ->with('mataPelajaran')
            ->orderBy('jam_mulai')
            ->get();

        $jadwalPerHari = $jadwal->groupBy(fn (JadwalPembelajaran $item) => $item->nomorHari());

        $tanggalDipilih = null;
        $tanggalQuery = (string) $request->query('tanggal', '');

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalQuery) === 1) {
            $tanggalDipilih = Carbon::createFromFormat('Y-m-d', $tanggalQuery)->startOfDay();
        }

        $jadwalTanggalDipilih = $tanggalDipilih
            ? $jadwalPerHari->get($tanggalDipilih->dayOfWeekIso, collect())
            : collect();

        $minggu = [];
        $kursor = $awalBulan->copy()->startOfWeek(Carbon::MONDAY);

        while ($kursor->lessThanOrEqualTo($awalBulan->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY))) {
            $pekan = [];

            for ($i = 0; $i < 7; $i++) {
                $tanggal = $kursor->copy();
                $pekan[] = [
                    'tanggal' => $tanggal,
                    'dalam_bulan' => $tanggal->month === $awalBulan->month,
                    'hari_ini' => $tanggal->isToday(),
                    'terpilih' => $tanggalDipilih !== null && $tanggal->isSameDay($tanggalDipilih),
                    'jadwal' => $jadwalPerHari->get($tanggal->dayOfWeekIso, collect()),
                ];

                $kursor->addDay();
            }

            $minggu[] = $pekan;
        }

        return [
            'bulan' => $awalBulan,
            'bulan_format' => $awalBulan->locale('id')->translatedFormat('F Y'),
            'bulan_sebelumnya' => $awalBulan->copy()->subMonth()->format('Y-m'),
            'bulan_berikutnya' => $awalBulan->copy()->addMonth()->format('Y-m'),
            'minggu' => $minggu,
            'hari' => JadwalPembelajaran::HARI,
            'jumlah_jadwal' => $jadwal->count(),
            'tanggal_dipilih' => $tanggalDipilih,
            'jadwal_tanggal_dipilih' => $jadwalTanggalDipilih,
        ];
    }

    /**
     * Tugas terbaru beserta status pengumpulan mahasiswa.
     *
     * @return array{terbaru: Collection<int, array<string, mixed>>, semua: Collection<int, Tugas>, belum: int}
     */
    private function ringkasanTugas(User $user): array
    {
        $semua = Tugas::query()
            ->dipublikasikan()
            ->untukAnggota($user)
            ->with(['mataPelajaran', 'guru'])
            ->with(['pengumpulan' => fn (HasMany $query) => $query->where('user_id', $user->id)])
            ->orderByDesc('dipublikasikan_pada')
            ->orderByDesc('id')
            ->get();

        $terbaru = $semua->take(3)->map(function (Tugas $tugas) {
            $pengumpulan = $tugas->pengumpulan->first();

            return [
                'tugas' => $tugas,
                'pengumpulan' => $pengumpulan,
                'status' => $tugas->statusUntuk($pengumpulan),
            ];
        })->values();

        $belum = $semua->filter(function (Tugas $tugas) {
            $status = $tugas->statusUntuk($tugas->pengumpulan->first())['status'];

            return in_array($status, [
                Tugas::STATUS_BELUM_DIKUMPULKAN,
                Tugas::STATUS_TERLAMBAT,
            ], true);
        })->count();

        return ['terbaru' => $terbaru, 'semua' => $semua, 'belum' => $belum];
    }

    /**
     * Nilai yang dipublikasikan untuk tugas pada kelompok aktif mahasiswa.
     *
     * @return Collection<int, PengumpulanTugas>
     */
    private function nilaiTerbaru(User $user): Collection
    {
        return PengumpulanTugas::query()
            ->where('user_id', $user->id)
            ->nilaiDipublikasikan()
            ->whereHas('tugas', fn (Builder $query) => $query->untukAnggota($user))
            ->with(['tugas.mataPelajaran'])
            ->orderByDesc('nilai_dipublikasikan_pada')
            ->limit(3)
            ->get();
    }

    /**
     * Rekap pengajuan surat dan perizinan mahasiswa.
     *
     * @return array{total: int, menunggu: int, disetujui: int, ditolak: int, selesai: int, terakhir: ?SuratPerizinan}
     */
    private function ringkasanPerizinan(User $user): array
    {
        $query = SuratPerizinan::query()->where('user_id', $user->id);

        return [
            'total' => (clone $query)->count(),
            'menunggu' => (clone $query)->where('status', SuratPerizinan::STATUS_MENUNGGU)->count(),
            'disetujui' => (clone $query)->where('status', SuratPerizinan::STATUS_DISETUJUI)->count(),
            'ditolak' => (clone $query)->where('status', SuratPerizinan::STATUS_DITOLAK)->count(),
            'selesai' => (clone $query)->where('status', SuratPerizinan::STATUS_SELESAI)->count(),
            'terakhir' => (clone $query)->latest('id')->first(),
        ];
    }

    /**
     * Membangkitkan pengingat pembayaran dan tenggat tugas tanpa
     * membuat notifikasi ganda (menggunakan kunci unik).
     *
     * @param  array<string, mixed>  $ringkasanPembayaran
     * @param  Collection<int, Tugas>  $semuaTugas
     */
    private function siapkanPengingat(User $user, array $ringkasanPembayaran, Collection $semuaTugas): void
    {
        $tagihan = $ringkasanPembayaran['tagihan'] ?? null;

        if ($tagihan instanceof Pembayaran
            && $ringkasanPembayaran['pengingat']
            && in_array($ringkasanPembayaran['status'], ['belum_lunas', 'menunggu', 'terlambat'], true)) {

            $terlambat = $ringkasanPembayaran['status'] === 'terlambat';

            Notifikasi::kirim(
                $user,
                Notifikasi::TIPE_PEMBAYARAN,
                $terlambat ? 'Tagihan melewati jatuh tempo' : 'Pengingat pembayaran '.$tagihan->periode,
                $terlambat
                    ? 'Tagihan '.$tagihan->labelTahap().' sebesar '.$tagihan->nominal_rupiah.' telah melewati jatuh tempo '.$tagihan->jatuhTempoTeks().'.'
                    : 'Tagihan '.$tagihan->labelTahap().' sebesar '.$tagihan->nominal_rupiah.' jatuh tempo pada '.$tagihan->jatuhTempoTeks().'.',
                route('pembayaran.detail'),
                'pembayaran:'.$tagihan->periode.':'.$ringkasanPembayaran['status']
            );
        }

        foreach ($semuaTugas as $tugas) {
            $pengumpulan = $tugas->pengumpulan->first();
            $sisaHari = $tugas->sisaHari();

            if ($pengumpulan === null && $sisaHari !== null && $sisaHari >= 0 && $sisaHari <= 2) {
                Notifikasi::kirim(
                    $user,
                    Notifikasi::TIPE_TENGGAT_TUGAS,
                    'Tenggat tugas: '.$tugas->judul,
                    'Batas pengumpulan '.$tugas->batasTeks().' ('.$sisaHari.' hari lagi).',
                    route('tugas.mhs.show', $tugas),
                    'tenggat:'.$tugas->id
                );
            }
        }
    }

    /** Rute dashboard sesuai role pengguna. */
    private function ruteDashboard(string $role): string
    {
        return match ($role) {
            'musyrifah' => 'musyrifah.dashboard',
            'admin' => 'admin.dashboard_admin',
            'guru' => 'guru.dashboard',
            'pimpinan' => 'pimpinan.dashboard',
            default => 'dashboard',
        };
    }
}
