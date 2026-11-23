<?php

namespace App\Services;

use App\Models\JadwalPembelajaran;
use App\Models\KelompokBelajarAnggota;
use App\Models\PertemuanPembelajaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Sumber aturan pembelajaran Guru.
 * Keanggotaan peserta berasal dari penempatan per mata pelajaran oleh Musyrifah.
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

    /** @return Collection<int, JadwalPembelajaran> */
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
     * Setiap filter boleh dipakai sendiri; tidak harus mengirim mata_pelajaran.
     *
     * @param Collection<int, JadwalPembelajaran> $penugasan
     */
    public function pilih(
        Collection $penugasan,
        ?int $mataPelajaranId,
        ?string $kelompok,
        ?string $periode
    ): ?JadwalPembelajaran {
        if ($penugasan->isEmpty()) {
            return null;
        }

        if ($mataPelajaranId === null && blank($kelompok) && blank($periode)) {
            return $penugasan->first();
        }

        return $penugasan->first(fn (JadwalPembelajaran $jadwal) =>
            ($mataPelajaranId === null || (int) $jadwal->mata_pelajaran_id === $mataPelajaranId)
            && (blank($kelompok) || $jadwal->kelompok === $kelompok)
            && (blank($periode) || $this->periode($jadwal) === $periode)
        );
    }

    /** Memastikan Guru ditugaskan pada mata pelajaran dan kelompok tersebut. */
    public function verifikasiPenugasan(
        User $guru,
        int $mataPelajaranId,
        string $kelompok,
        ?string $periode = null
    ): JadwalPembelajaran {
        $jadwal = $this->penugasan($guru)->first(fn (JadwalPembelajaran $item) =>
            (int) $item->mata_pelajaran_id === $mataPelajaranId
            && $item->kelompok === $kelompok
            && (blank($periode) || $this->periode($item) === $periode)
        );

        abort_unless(
            $jadwal instanceof JadwalPembelajaran,
            403,
            'Anda tidak ditugaskan pada mata pelajaran dan kelompok tersebut.'
        );

        return $jadwal;
    }

    /**
     * Aturan akses lama dipertahankan untuk menjaga kompatibilitas dengan
     * pertemuan yang telah dibuat. Perubahan kebijakan pengalihan Guru
     * memerlukan pemeriksaan tersendiri.
     */
    public function pastikanMilikGuru(User $guru, PertemuanPembelajaran $pertemuan): void
    {
        $valid = (int) $pertemuan->guru_id === (int) $guru->getKey()
            || $this->penugasan($guru)->contains(fn (JadwalPembelajaran $jadwal) =>
                (int) $jadwal->mata_pelajaran_id === (int) $pertemuan->mata_pelajaran_id
                && $jadwal->kelompok === $pertemuan->kelompok
                && $this->periode($jadwal) === $pertemuan->periode
            );

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
     * Peserta pada pasangan mata pelajaran DAN kelompok, ditentukan Musyrifah.
     * Pendaftaran harus disetujui dan keanggotaan harus aktif.
     *
     * @return Collection<int, User>
     */
    public function mahasiswaPerMataPelajaran(int $mataPelajaranId, string $kelompok): Collection
    {
        $kelompok = trim($kelompok);

        if ($mataPelajaranId < 1 || $kelompok === '') {
            return collect();
        }

        return KelompokBelajarAnggota::query()
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->where('status', 'aktif')
            ->whereHas('kelompokBelajar', fn ($query) => $query->where('nama_kelompok', $kelompok))
            ->whereHas('user', fn ($query) => $query
                ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
                ->whereHas('pendaftarans', fn ($pendaftaran) => $pendaftaran->where('status', 'disetujui'))
            )
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    /** @return Collection<int, User> */
    public function mahasiswaUntukJadwal(JadwalPembelajaran $jadwal): Collection
    {
        return $this->mahasiswaPerMataPelajaran(
            (int) $jadwal->mata_pelajaran_id,
            (string) $jadwal->kelompok
        );
    }

    /** @return Collection<int, User> */
    public function mahasiswaUntukPertemuan(PertemuanPembelajaran $pertemuan): Collection
    {
        return $this->mahasiswaPerMataPelajaran(
            (int) $pertemuan->mata_pelajaran_id,
            (string) $pertemuan->kelompok
        );
    }

    /**
     * @param Collection<int, JadwalPembelajaran> $penugasan
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
     * @param Collection<int, JadwalPembelajaran> $penugasan
     * @return array<int, string>
     */
    public function pilihanKelompok(Collection $penugasan): array
    {
        return $penugasan->pluck('kelompok')->filter()->unique()->values()->all();
    }

    /**
     * @param Collection<int, JadwalPembelajaran> $penugasan
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
