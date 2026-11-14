<?php

namespace App\Services;

use App\Models\JadwalPembelajaran;
use App\Models\PertemuanPembelajaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Sumber tunggal aturan pembelajaran Guru.
 *
 * Penugasan Guru diturunkan dari jadwal_pembelajarans (nama guru_pengampu),
 * progres 1-16 dihitung dari tabel pertemuan_pembelajarans, dan seluruh
 * pembatasan akses dilakukan di sini agar controller tidak menduplikasi
 * aturan yang sama.
 */
class PembelajaranGuruService
{
    public const TARGET_PERTEMUAN = 16;

    /** Memastikan pengguna adalah Guru dan mengembalikan akunnya. */
    public function guru(Request $request): User
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));

        abort_unless($user && $role === 'guru', 403);

        return $user;
    }

    /**
     * Seluruh penugasan (jadwal) milik Guru.
     *
     * @return Collection<int, JadwalPembelajaran>
     */
    public function penugasan(User $guru): Collection
    {
        return JadwalPembelajaran::jadwalUntuk($guru)
            ->sortBy(fn (JadwalPembelajaran $jadwal) => implode('|', [
                $jadwal->namaMataPelajaran(),
                $jadwal->kelompok,
                $this->periode($jadwal),
            ]))
            ->values();
    }

    /** Periode yang dipakai sebagai kunci pertemuan. */
    public function periode(JadwalPembelajaran $jadwal): string
    {
        return trim((string) $jadwal->periode) ?: 'Semester Ganjil 2026/2027';
    }

    /**
     * Jadwal yang sedang dipilih Guru dari parameter mapel/kelompok/periode.
     *
     * @param  Collection<int, JadwalPembelajaran>  $penugasan
     */
    public function pilih(Collection $penugasan, ?int $mataPelajaranId, ?string $kelompok, ?string $periode): ?JadwalPembelajaran
    {
        if ($penugasan->isEmpty()) {
            return null;
        }

        if ($mataPelajaranId === null && blank($kelompok) && blank($periode)) {
            return $penugasan->first();
        }

        return $penugasan->first(fn (JadwalPembelajaran $jadwal) => (int) $jadwal->mata_pelajaran_id === (int) $mataPelajaranId
            && ($kelompok === null || $kelompok === '' || $jadwal->kelompok === $kelompok)
            && ($periode === null || $periode === '' || $this->periode($jadwal) === $periode));
    }

    /**
     * Memastikan Guru benar-benar ditugaskan pada mata pelajaran dan kelompok tersebut.
     */
    public function verifikasiPenugasan(User $guru, int $mataPelajaranId, string $kelompok, ?string $periode = null): JadwalPembelajaran
    {
        $jadwal = $this->penugasan($guru)->first(fn (JadwalPembelajaran $item) => (int) $item->mata_pelajaran_id === $mataPelajaranId
            && $item->kelompok === $kelompok
            && ($periode === null || $periode === '' || $this->periode($item) === $periode));

        abort_unless($jadwal instanceof JadwalPembelajaran, 403, 'Anda tidak ditugaskan pada mata pelajaran dan kelompok tersebut.');

        return $jadwal;
    }

    /** Menolak pertemuan milik Guru lain di luar penugasannya. */
    public function pastikanMilikGuru(User $guru, PertemuanPembelajaran $pertemuan): void
    {
        $valid = (int) $pertemuan->guru_id === (int) $guru->getKey()
            || $this->penugasan($guru)->contains(fn (JadwalPembelajaran $jadwal) => (int) $jadwal->mata_pelajaran_id === (int) $pertemuan->mata_pelajaran_id
                && $jadwal->kelompok === $pertemuan->kelompok
                && $this->periode($jadwal) === $pertemuan->periode);

        abort_unless($valid, 403, 'Pertemuan ini bukan bagian dari penugasan Anda.');
    }

    /**
     * Progres 16 pertemuan untuk satu penugasan.
     *
     * @return array{daftar: array<int, array<string, mixed>>, selesai: int, berlangsung: int|null, berikutnya: int|null, persentase: float}
     */
    public function progres(JadwalPembelajaran $jadwal): array
    {
        $tersimpan = PertemuanPembelajaran::query()
            ->withCount(['tugas', 'dokumentasi'])
            ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
            ->where('kelompok', $jadwal->kelompok)
            ->where('periode', $this->periode($jadwal))
            ->get()
            ->keyBy('pertemuan_ke');

        $daftar = [];
        $selesai = 0;
        $berlangsung = null;
        $berikutnya = null;

        for ($nomor = 1; $nomor <= self::TARGET_PERTEMUAN; $nomor++) {
            /** @var PertemuanPembelajaran|null $pertemuan */
            $pertemuan = $tersimpan->get($nomor);

            if ($pertemuan?->sudahSelesai()) {
                $selesai++;
            }

            if ($pertemuan?->berlangsung() && $berlangsung === null) {
                $berlangsung = $nomor;
            }

            if ($berikutnya === null && ! $pertemuan?->sudahSelesai()) {
                $berikutnya = $nomor;
            }

            $daftar[] = [
                'nomor' => $nomor,
                'pertemuan' => $pertemuan,
                'status' => $pertemuan?->status,
                'label_status' => $pertemuan?->labelStatus() ?? 'Terjadwal',
                'tanggal' => $pertemuan?->tanggalTeks(),
                'materi' => $pertemuan?->materi,
                'punya_materi' => (bool) $pertemuan?->punyaMateri(),
                'jumlah_tugas' => (int) ($pertemuan?->tugas_count ?? 0),
                'jumlah_dokumentasi' => (int) ($pertemuan?->dokumentasi_count ?? 0),
            ];
        }

        return [
            'daftar' => $daftar,
            'selesai' => $selesai,
            'berlangsung' => $berlangsung,
            'berikutnya' => $berlangsung ?? $berikutnya,
            'persentase' => round(($selesai / self::TARGET_PERTEMUAN) * 100, 1),
            'jumlah_dokumentasi' => (int) $tersimpan->sum('dokumentasi_count'),
        ];
    }

    /**
     * Daftar mahasiswa anggota kelompok belajar.
     *
     * Keanggotaan memakai konvensi yang sudah ada: baris absensi pembelajaran
     * pada kelompok tersebut.
     *
     * @return Collection<int, User>
     */
    public function mahasiswa(string $kelompok): Collection
    {
        return User::mahasiswaKelompok($kelompok);
    }

    /**
     * Pilihan filter mata pelajaran dari penugasan Guru.
     *
     * @param  Collection<int, JadwalPembelajaran>  $penugasan
     * @return array<int, string>
     */
    public function pilihanMataPelajaran(Collection $penugasan): array
    {
        return $penugasan
            ->mapWithKeys(fn (JadwalPembelajaran $jadwal) => [
                (int) $jadwal->mata_pelajaran_id => $jadwal->namaMataPelajaran(),
            ])
            ->unique()
            ->all();
    }

    /**
     * Pilihan kelompok dan periode dari penugasan Guru.
     *
     * @param  Collection<int, JadwalPembelajaran>  $penugasan
     * @return array<int, string>
     */
    public function pilihanKelompok(Collection $penugasan): array
    {
        return $penugasan->pluck('kelompok')->filter()->unique()->values()->all();
    }

    /**
     * @param  Collection<int, JadwalPembelajaran>  $penugasan
     * @return array<int, string>
     */
    public function pilihanPeriode(Collection $penugasan): array
    {
        return $penugasan
            ->map(fn (JadwalPembelajaran $jadwal) => $this->periode($jadwal))
            ->unique()
            ->values()
            ->all();
    }
}
