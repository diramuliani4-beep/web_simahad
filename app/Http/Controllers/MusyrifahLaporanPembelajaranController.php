<?php

namespace App\Http\Controllers;

use App\Models\JadwalPembelajaran;
use App\Models\LaporanPembelajaran;
use App\Models\MataPelajaran;
use App\Models\PengaturanSistem;
use App\Models\PertemuanPembelajaran;
use App\Services\LaporanMahadService;
use App\Services\LaporanPembelajaranService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MusyrifahLaporanPembelajaranController extends Controller
{
    public function __construct(
        private readonly LaporanPembelajaranService $laporan,
        private readonly LaporanMahadService $laporanMahad,
    ) {}

    public function index(Request $request): View
    {
        $this->pastikanPengelola($request);

        $mapel = $this->mataPelajaranTerpilih($request);
        $filter = $this->laporan->normalisasiFilter(
            (string) $request->query('periode', ''),
            (string) $request->query('kelompok', ''),
            (string) $request->query('cari', ''),
        );
        $baris = $this->laporan->barisLaporan($mapel->id, $filter['kelompok'], $filter['periode'], $filter['cari']);

        return view('musyrifah.laporan_pembelajaran', [
            'daftarMataPelajaran' => $this->laporan->daftarMataPelajaran(),
            'mataPelajaran' => $mapel,
            'filter' => $filter,
            'barisLaporan' => $baris,
            'ringkasan' => $this->laporan->ringkasan($mapel->id, $baris),
            'daftarKelompok' => $this->daftarKelompok($mapel->id),
            'program' => $this->program($mapel, $filter['kelompok']),
        ]);
    }

    public function create(Request $request, int $mataPelajaran): View
    {
        $this->pastikanPengelola($request);
        $mapel = $this->laporan->mataPelajaran($mataPelajaran);
        $daftarPertemuan = $this->laporan->daftarPertemuan($mapel->id);
        $pertemuanId = (int) $request->query('pertemuan_id', 0);
        $pertemuan = $pertemuanId > 0
            ? $daftarPertemuan->firstWhere('id', $pertemuanId)
            : $daftarPertemuan->firstWhere('laporanPembelajaran', fn ($laporan): bool => $laporan->isEmpty());

        abort_if($pertemuanId > 0 && $pertemuan === null, 404);
        abort_if($pertemuan?->laporanPembelajaran->isNotEmpty(), 404);

        return view('musyrifah.laporan_pembelajaran_form', [
            'mataPelajaran' => $mapel,
            'daftarMataPelajaran' => $this->laporan->daftarMataPelajaran(),
            'daftarPertemuan' => $daftarPertemuan,
            'pertemuanTerpilih' => $pertemuan,
            'laporan' => null,
            'formAction' => route('musyrifah.laporan.pembelajaran.store', [
                'mataPelajaran' => $mapel,
            ] + $this->filterKembali($request)),
            'metodeForm' => 'POST',
            'filter' => $this->filterKembali($request),
        ]);
    }

    public function store(Request $request, int $mataPelajaran): RedirectResponse
    {
        $this->pastikanPengelola($request);
        $mapel = $this->laporan->mataPelajaran($mataPelajaran);
        $data = $this->validasi($request, $mapel);

        $this->laporan->simpan($request->user(), $data);

        return redirect()
            ->route('musyrifah.laporan.pembelajaran.index', $this->parameterKembali($mapel, $request))
            ->with('success', 'Laporan pembelajaran berhasil ditambahkan.');
    }

    public function edit(Request $request, int $mataPelajaran, LaporanPembelajaran $laporan): View
    {
        $this->pastikanPengelola($request);
        $mapel = $this->laporan->mataPelajaran($mataPelajaran);
        $this->pastikanMataPelajaran($laporan, $mapel);
        $laporan->load('pertemuan');

        return view('musyrifah.laporan_pembelajaran_form', [
            'mataPelajaran' => $mapel,
            'daftarMataPelajaran' => $this->laporan->daftarMataPelajaran(),
            'daftarPertemuan' => $this->laporan->daftarPertemuan($mapel->id),
            'pertemuanTerpilih' => $laporan->pertemuan,
            'laporan' => $laporan,
            'formAction' => route('musyrifah.laporan.pembelajaran.update', [
                'mataPelajaran' => $mapel,
                'laporan' => $laporan,
            ] + $this->filterKembali($request)),
            'metodeForm' => 'PUT',
            'filter' => $this->filterKembali($request),
        ]);
    }

    public function update(Request $request, int $mataPelajaran, LaporanPembelajaran $laporan): RedirectResponse
    {
        $this->pastikanPengelola($request);
        $mapel = $this->laporan->mataPelajaran($mataPelajaran);
        $this->pastikanMataPelajaran($laporan, $mapel);
        $data = $this->validasi($request, $mapel);

        $this->laporan->simpan($request->user(), $data, $laporan);

        return redirect()
            ->route('musyrifah.laporan.pembelajaran.index', $this->parameterKembali($mapel, $request))
            ->with('success', 'Laporan pembelajaran berhasil diperbarui.');
    }

    public function destroy(Request $request, int $mataPelajaran, LaporanPembelajaran $laporan): RedirectResponse
    {
        $this->pastikanPengelola($request);
        $mapel = $this->laporan->mataPelajaran($mataPelajaran);
        $this->pastikanMataPelajaran($laporan, $mapel);

        $this->laporan->hapus($laporan, $request->user());

        return redirect()
            ->route('musyrifah.laporan.pembelajaran.index', $this->parameterKembali($mapel, $request))
            ->with('success', 'Laporan pembelajaran berhasil dihapus.');
    }

    public function cetak(Request $request, int $mataPelajaran): View
    {
        $this->pastikanPengelola($request);
        $mapel = $this->laporan->mataPelajaran($mataPelajaran);
        $filter = $this->laporan->normalisasiFilter(
            (string) $request->query('periode', ''),
            (string) $request->query('kelompok', ''),
            '',
        );
        $baris = $this->laporan->barisLaporan($mapel->id, $filter['kelompok'], $filter['periode']);
        $pengaturan = PengaturanSistem::aktif();

        return view('musyrifah.laporan_pembelajaran_cetak', [
            'mataPelajaran' => $mapel,
            'program' => $this->program($mapel, $filter['kelompok']),
            'barisLaporan' => $baris,
            'filter' => $filter,
            'periodeLabel' => $filter['periode'] === ''
                ? 'Semua Periode'
                : CarbonImmutable::createFromFormat('Y-m-d', $filter['periode'].'-01')
                    ->locale('id')
                    ->translatedFormat('F Y'),
            'pengaturan' => $pengaturan,
            'pimpinan' => $this->laporanMahad->identitasPimpinan(),
            'backUrl' => route('musyrifah.laporan.pembelajaran.index', [
                'mata_pelajaran' => $mapel->id,
                'periode' => $filter['periode'],
                'kelompok' => $filter['kelompok'],
            ]),
        ]);
    }

    public function unduh(Request $request, int $mataPelajaran): StreamedResponse
    {
        $this->pastikanPengelola($request);
        $mapel = $this->laporan->mataPelajaran($mataPelajaran);
        $filter = $this->laporan->normalisasiFilter(
            (string) $request->query('periode', ''),
            (string) $request->query('kelompok', ''),
            '',
        );
        $baris = $this->laporan->barisLaporan($mapel->id, $filter['kelompok'], $filter['periode']);
        $namaBerkas = 'laporan-pembelajaran-'.Str::slug($mapel->nama).'-'.($filter['periode'] ?: 'semua-periode').'.csv';

        return response()->streamDownload(function () use ($baris): void {
            $berkas = fopen('php://output', 'wb');

            if ($berkas === false) {
                return;
            }

            fwrite($berkas, "\xEF\xBB\xBF");
            fputcsv($berkas, ['No', 'Hari/Tanggal', 'Materi', 'Tanda Tangan']);

            foreach ($baris as $nomor => $item) {
                fputcsv($berkas, array_map(
                    fn (mixed $nilai): string => $this->nilaiCsv($nilai),
                    [
                        $nomor + 1,
                        $item['hari'].', '.$item['tanggal'],
                        $item['materi'],
                        $item['tanda_tangan'],
                    ],
                ));
            }

            fclose($berkas);
        }, $namaBerkas, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validasi(Request $request, MataPelajaran $mapel): array
    {
        return $request->validate($this->laporan->aturanValidasi(
            (int) $mapel->id,
            $request->route('laporan')?->id,
        ));
    }

    private function mataPelajaranTerpilih(Request $request): MataPelajaran
    {
        $id = (int) $request->query('mata_pelajaran', 0);

        if ($id > 0) {
            return $this->laporan->mataPelajaran($id);
        }

        $mapelPertama = $this->laporan->daftarMataPelajaran()->first();
        abort_if($mapelPertama === null, 404, 'Belum ada mata pelajaran.');

        return $mapelPertama;
    }

    /**
     * @return Collection<int, string>
     */
    private function daftarKelompok(int $mataPelajaranId): Collection
    {
        return PertemuanPembelajaran::query()
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->distinct()
            ->orderBy('kelompok')
            ->pluck('kelompok');
    }

    /**
     * @return array{nama: string, waktu: string, instruktur: string, kelompok: string, periode: string}
     */
    private function program(MataPelajaran $mapel, string $kelompok = ''): array
    {
        $jadwal = JadwalPembelajaran::query()
            ->with('guru:id,name')
            ->where('mata_pelajaran_id', $mapel->id)
            ->when($kelompok !== '', fn ($query) => $query->where('kelompok', $kelompok))
            ->orderBy('id')
            ->first();

        return [
            'nama' => $mapel->nama,
            'waktu' => $jadwal && filled($jadwal->jam_mulai) && filled($jadwal->jam_selesai)
                ? $jadwal->jamTeks()
                : '-',
            'instruktur' => $jadwal?->guru_pengampu ?: ($jadwal?->guru?->name ?? '-'),
            'kelompok' => $jadwal?->kelompok ?: ($kelompok ?: '-'),
            'periode' => $jadwal?->periode ?: '-',
        ];
    }

    private function nilaiCsv(mixed $nilai): string
    {
        $teks = (string) $nilai;

        return preg_match('/^[=+@\-\t\r]/', $teks) === 1
            ? "'".$teks
            : $teks;
    }

    private function pastikanMataPelajaran(LaporanPembelajaran $laporan, MataPelajaran $mapel): void
    {
        abort_unless((int) $laporan->mata_pelajaran_id === (int) $mapel->id, 404);
    }

    /**
     * @return array<string, string>
     */
    private function filterKembali(Request $request): array
    {
        return array_filter([
            'periode' => (string) $request->query('periode', ''),
            'kelompok' => (string) $request->query('kelompok', ''),
            'cari' => (string) $request->query('cari', ''),
        ], fn (string $nilai): bool => $nilai !== '');
    }

    /**
     * @return array<string, int|string>
     */
    private function parameterKembali(MataPelajaran $mapel, Request $request): array
    {
        return ['mata_pelajaran' => $mapel->id] + $this->filterKembali($request);
    }

    private function pastikanPengelola(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['musyrifah', 'pengelola', 'admin'], true), 403);
    }
}
