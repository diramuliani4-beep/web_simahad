<?php

namespace App\Http\Controllers;

use App\Models\JadwalPembelajaran;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use App\Services\PembelajaranGuruService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Menu Penilaian Guru: rekap tugas dan nilai mahasiswa pada penugasan
 * yang diberikan Musyrifah. Penilaian tetap disimpan pada tabel
 * pengumpulan_tugas melalui halaman pengumpulan yang sudah ada.
 */
class GuruPenilaianController extends Controller
{
    public function __construct(private readonly PembelajaranGuruService $layanan) {}

    public function index(Request $request): View
    {
        $guru = $this->layanan->guru($request);
        $penugasan = $this->layanan->penugasan($guru);

        $filter = $request->validate([
            'mata_pelajaran' => ['nullable', 'integer'],
            'kelompok' => ['nullable', 'string', 'max:60'],
        ]);

        if (filled($filter['mata_pelajaran'] ?? null) || filled($filter['kelompok'] ?? null)) {
            abort_unless(
                $penugasan->contains(fn (JadwalPembelajaran $jadwal) => (! filled($filter['mata_pelajaran'] ?? null)
                    || (int) $jadwal->mata_pelajaran_id === (int) $filter['mata_pelajaran'])
                    && (! filled($filter['kelompok'] ?? null) || $jadwal->kelompok === $filter['kelompok'])),
                403,
                'Filter penilaian di luar penugasan Anda.'
            );
        }

        $daftarTugas = $this->queryTugas($penugasan)
            ->when(filled($filter['mata_pelajaran'] ?? null), fn (Builder $query) => $query->where('mata_pelajaran_id', (int) $filter['mata_pelajaran']))
            ->when(filled($filter['kelompok'] ?? null), fn (Builder $query) => $query->where('kelompok', $filter['kelompok']))
            ->withCount([
                'pengumpulan',
                'pengumpulan as pengumpulan_dinilai_count' => fn (Builder $query) => $query->whereNotNull('nilai'),
            ])
            ->withAvg('pengumpulan', 'nilai')
            ->orderByDesc('batas_pengumpulan')
            ->orderByDesc('id')
            ->get();

        $kelompokRekap = $filter['kelompok'] ?? $penugasan->pluck('kelompok')->unique()->sort()->first();
        $mahasiswa = filled($kelompokRekap)
            ? $this->layanan->mahasiswa((string) $kelompokRekap)
            : collect();

        $pengumpulan = $this->pengumpulanMahasiswa($mahasiswa, $daftarTugas->pluck('id'));

        $rekapMahasiswa = $mahasiswa->map(function ($siswa) use ($pengumpulan, $daftarTugas) {
            $milikSiswa = $pengumpulan->get($siswa->id, collect());
            $dinilai = $milikSiswa->whereNotNull('nilai');

            return [
                'mahasiswa' => $siswa,
                'dikumpulkan' => $milikSiswa->count(),
                'dinilai' => $dinilai->count(),
                'rata_rata' => $dinilai->isEmpty() ? null : round((float) $dinilai->avg('nilai'), 1),
                'target_tugas' => $daftarTugas->count(),
            ];
        });

        $totalPengumpulan = (int) $daftarTugas->sum('pengumpulan_count');
        $totalDinilai = (int) $daftarTugas->sum('pengumpulan_dinilai_count');

        $ringkasan = [
            'tugas' => $daftarTugas->count(),
            'pengumpulan' => $totalPengumpulan,
            'dinilai' => $totalDinilai,
            'belum_dinilai' => max(0, $totalPengumpulan - $totalDinilai),
            'rata_rata' => round((float) $daftarTugas->avg('pengumpulan_avg_nilai'), 1),
        ];

        return view('guru.penilaian', [
            'guru' => $guru,
            'penugasan' => $penugasan,
            'daftarTugas' => $daftarTugas,
            'rekapMahasiswa' => $rekapMahasiswa,
            'kelompokRekap' => $kelompokRekap,
            'ringkasan' => $ringkasan,
            'filter' => $filter,
            'pilihanMapel' => $this->layanan->pilihanMataPelajaran($penugasan),
            'pilihanKelompok' => $this->layanan->pilihanKelompok($penugasan),
        ]);
    }

    /**
     * Tugas yang berada pada penugasan Guru (mapel + kelompok).
     *
     * @param  Collection<int, JadwalPembelajaran>  $penugasan
     */
    private function queryTugas(Collection $penugasan): Builder
    {
        $query = Tugas::query()->with('mataPelajaran');

        $query->where(function (Builder $inner) use ($penugasan) {
            if ($penugasan->isEmpty()) {
                $inner->whereRaw('1 = 0');

                return;
            }

            foreach ($penugasan as $jadwal) {
                $inner->orWhere(function (Builder $cocok) use ($jadwal) {
                    $cocok->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                        ->where('kelompok', $jadwal->kelompok);
                });
            }
        });

        return $query;
    }

    /**
     * @param  Collection<int, User>  $mahasiswa
     * @param  Collection<int, int>  $tugasIds
     * @return Collection<int, Collection<int, PengumpulanTugas>>
     */
    private function pengumpulanMahasiswa(Collection $mahasiswa, Collection $tugasIds): Collection
    {
        if ($mahasiswa->isEmpty() || $tugasIds->isEmpty()) {
            return collect();
        }

        return PengumpulanTugas::query()
            ->whereIn('tugas_id', $tugasIds)
            ->whereIn('user_id', $mahasiswa->pluck('id'))
            ->get()
            ->groupBy('user_id');
    }
}
