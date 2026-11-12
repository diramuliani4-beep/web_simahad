<?php

namespace App\Http\Controllers;

use App\Services\PimpinanMonitoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Halaman Monitoring Pimpinan.
 *
 * Pimpinan hanya dapat melihat, memfilter, dan mengunduh rekap. Tidak ada
 * aksi yang mengubah data operasional pada controller ini.
 */
class PimpinanMonitoringController extends Controller
{
    public function __construct(private readonly PimpinanMonitoringService $monitoring) {}

    public function index(): RedirectResponse
    {
        $this->pastikanPimpinan();

        return redirect()->route('pimpinan.monitoring.mahasiswa');
    }

    public function mahasiswa(Request $request): View
    {
        return $this->tampilkan('mahasiswa', $request);
    }

    public function kamar(Request $request): View
    {
        return $this->tampilkan('kamar', $request);
    }

    public function pembayaran(Request $request): View
    {
        return $this->tampilkan('pembayaran', $request);
    }

    public function pembelajaran(Request $request): View
    {
        return $this->tampilkan('pembelajaran', $request);
    }

    public function perizinan(Request $request): View
    {
        return $this->tampilkan('perizinan', $request);
    }

    /**
     * Unduh rekap monitoring (CSV) sesuai filter yang sedang dipakai.
     */
    public function unduh(Request $request, string $jenis): StreamedResponse
    {
        $this->pastikanPimpinan();
        abort_unless(in_array($jenis, PimpinanMonitoringService::KATEGORI, true), 404);

        $data = $this->monitoring->untuk($jenis, $this->filter($request));
        $namaBerkas = 'monitoring-'.$jenis.'-'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($data): void {
            $berkas = fopen('php://output', 'wb');

            if ($berkas === false) {
                return;
            }

            fwrite($berkas, "\xEF\xBB\xBF");

            fputcsv($berkas, array_map(
                fn (array $kolom): string => $kolom['label'],
                $data['kolom']
            ));

            foreach ($data['baris'] as $baris) {
                fputcsv($berkas, array_map(
                    fn (array $kolom): string => (string) ($baris[$kolom['key']] ?? '-'),
                    $data['kolom']
                ));
            }

            fclose($berkas);
        }, $namaBerkas, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function tampilkan(string $kategori, Request $request): View
    {
        $this->pastikanPimpinan();

        $filter = $this->filter($request);
        $data = $this->monitoring->untuk($kategori, $filter);

        return view('pimpinan.monitoring', [
            'laporan' => $data,
            'kategori' => $kategori,
            'keyword' => $filter['cari'] ?? '',
            'opsiPeriode' => $data['opsi_periode'] ?? [],
            'filterTambahan' => $data['filter_tambahan'] ?? [],
            'filterAktif' => $filter,
            'filterAction' => route('pimpinan.monitoring.'.$kategori),
            'backUrl' => route('pimpinan.dashboard'),
            'unduhUrl' => route('pimpinan.monitoring.unduh', array_filter(
                array_merge($filter, ['jenis' => $kategori]),
                fn ($nilai): bool => filled($nilai)
            )),
            'modeMonitoring' => true,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function filter(Request $request): array
    {
        return [
            'periode' => trim((string) $request->query('periode', '')),
            'lantai' => trim((string) $request->query('lantai', '')),
            'angkatan' => trim((string) $request->query('angkatan', '')),
            'kategori' => trim((string) $request->query('kategori', '')),
            'status' => trim((string) $request->query('status', '')),
            'cari' => trim((string) $request->query('cari', '')),
        ];
    }

    /**
     * Pimpinan (dan Admin Sistem) hanya boleh melihat data.
     */
    private function pastikanPimpinan(): void
    {
        $role = strtolower((string) auth()->user()?->role);
        abort_unless(in_array($role, ['pimpinan', 'admin'], true), 403);
    }
}
