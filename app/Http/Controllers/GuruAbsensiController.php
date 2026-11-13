<?php

namespace App\Http\Controllers;

use App\Models\JadwalPembelajaran;
use App\Models\PertemuanPembelajaran;
use App\Services\PembelajaranGuruService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Rekap absensi pembelajaran per mata pelajaran, kelompok, periode,
 * dan pertemuan. Guru hanya melihat penugasannya sendiri; Musyrifah
 * melihat seluruh pertemuan sebagai bagian monitoring.
 */
class GuruAbsensiController extends Controller
{
    public function __construct(private readonly PembelajaranGuruService $layanan) {}

    public function index(Request $request): View
    {
        $guru = $this->layanan->guru($request);
        $penugasan = $this->layanan->penugasan($guru);

        return $this->bangun($request, $penugasan, false);
    }

    /** Rekap seluruh pembelajaran untuk Musyrifah/Pengelola/Admin Sistem. */
    public function untukMusyrifah(Request $request): View
    {
        $role = strtolower(trim((string) $request->user()?->role));

        abort_unless(in_array($role, ['musyrifah', 'pengelola', 'admin'], true), 403);

        return $this->bangun($request, collect(), true);
    }

    /**
     * @param  Collection<int, JadwalPembelajaran>  $penugasan
     */
    private function bangun(Request $request, Collection $penugasan, bool $semua): View
    {
        $filter = $request->validate([
            'mata_pelajaran' => ['nullable', 'integer'],
            'kelompok' => ['nullable', 'string', 'max:60'],
            'periode' => ['nullable', 'string', 'max:100'],
            'pertemuan' => ['nullable', 'integer', 'min:1', 'max:'.PembelajaranGuruService::TARGET_PERTEMUAN],
        ]);

        $daftarPeriode = $semua
            ? PertemuanPembelajaran::query()->distinct()->orderBy('periode')->pluck('periode')->filter()->values()
            : $this->layanan->pilihanPeriode($penugasan);

        $daftarKelompok = $semua
            ? PertemuanPembelajaran::query()->distinct()->orderBy('kelompok')->pluck('kelompok')->filter()->values()
            : $this->layanan->pilihanKelompok($penugasan);

        $pilihanMapel = $semua
            ? PertemuanPembelajaran::query()
                ->with('mataPelajaran:id,nama')
                ->get()
                ->mapWithKeys(fn (PertemuanPembelajaran $item) => [
                    (int) $item->mata_pelajaran_id => $item->mataPelajaran?->nama ?? 'Mata Pelajaran',
                ])
                ->unique()
                ->all()
            : $this->layanan->pilihanMataPelajaran($penugasan);

        $query = PertemuanPembelajaran::query()
            ->with('mataPelajaran')
            ->withCount([
                'absensis',
                'absensis as hadir_count' => fn (Builder $item) => $item->where('status', 'hadir'),
                'absensis as izin_count' => fn (Builder $item) => $item->where('status', 'izin'),
                'absensis as sakit_count' => fn (Builder $item) => $item->where('status', 'sakit'),
                'absensis as alpa_count' => fn (Builder $item) => $item->where('status', 'alpa'),
                'tugas',
            ]);

        if (! $semua) {
            $query->where(function (Builder $inner) use ($penugasan) {
                if ($penugasan->isEmpty()) {
                    $inner->whereRaw('1 = 0');

                    return;
                }

                foreach ($penugasan as $jadwal) {
                    $inner->orWhere(function (Builder $cocok) use ($jadwal) {
                        $cocok->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                            ->where('kelompok', $jadwal->kelompok)
                            ->where('periode', $this->layanan->periode($jadwal));
                    });
                }
            });
        }

        $query
            ->when(filled($filter['mata_pelajaran'] ?? null), fn (Builder $item) => $item->where('mata_pelajaran_id', (int) $filter['mata_pelajaran']))
            ->when(filled($filter['kelompok'] ?? null), fn (Builder $item) => $item->where('kelompok', $filter['kelompok']))
            ->when(filled($filter['periode'] ?? null), fn (Builder $item) => $item->where('periode', $filter['periode']))
            ->when(filled($filter['pertemuan'] ?? null), fn (Builder $item) => $item->where('pertemuan_ke', (int) $filter['pertemuan']));

        $rekap = $query
            ->orderBy('mata_pelajaran_id')
            ->orderBy('kelompok')
            ->orderBy('periode')
            ->orderBy('pertemuan_ke')
            ->get();

        $totalAbsensi = (int) $rekap->sum('absensis_count');
        $totalHadir = (int) $rekap->sum('hadir_count');

        $ringkasan = [
            'pertemuan' => $rekap->count(),
            'selesai' => $rekap->where('status', PertemuanPembelajaran::STATUS_SELESAI)->count(),
            'berlangsung' => $rekap->where('status', PertemuanPembelajaran::STATUS_BERLANGSUNG)->count(),
            'absensi' => $totalAbsensi,
            'hadir' => $totalHadir,
            'persentase' => $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100, 1) : 0.0,
        ];

        return view('guru.absensi', [
            'rekap' => $rekap,
            'ringkasan' => $ringkasan,
            'filter' => $filter,
            'pilihanMapel' => $pilihanMapel,
            'pilihanKelompok' => $daftarKelompok,
            'pilihanPeriode' => $daftarPeriode,
            'targetPertemuan' => PembelajaranGuruService::TARGET_PERTEMUAN,
            'dariMusyrifah' => $semua,
        ]);
    }
}
