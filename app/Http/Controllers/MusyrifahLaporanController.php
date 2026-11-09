<?php

namespace App\Http\Controllers;

use App\Services\LaporanMahadService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MusyrifahLaporanController extends Controller
{
    public function __construct(private readonly LaporanMahadService $laporanMahad) {}

    /**
     * Menampilkan daftar kategori laporan Musyrifah.
     */
    public function index(Request $request): View
    {
        $this->pastikanPengelola($request);

        $periode = $this->laporanMahad->normalisasiPeriode($request->query('periode'));
        $jenis = (string) $request->query('jenis', 'semua');

        $kategori = $this->laporanMahad->kategoriMusyrifah();

        if ($jenis !== 'semua') {
            $kategori = array_values(array_filter(
                $kategori,
                fn (array $item): bool => $item['jenis'] === $jenis
            ));
        }

        $laporan = array_map(function (array $item) use ($periode): array {
            $item['url'] = route('musyrifah.laporan.detail', [
                'jenis' => $item['jenis'],
                'periode' => $periode,
            ]);

            return $item;
        }, $kategori);

        return view('musyrifah.laporan', [
            'laporan' => $laporan,
            'periode' => $periode,
            'jenis' => $jenis,
            'opsiPeriode' => $this->laporanMahad->opsiPeriode(),
        ]);
    }

    /**
     * Menampilkan detail laporan berdasarkan periode dan kata kunci.
     */
    public function show(Request $request, string $jenis): View
    {
        $this->pastikanPengelola($request);
        abort_unless(in_array($jenis, LaporanMahadService::JENIS_MUSYRIFAH, true), 404);

        $periode = $this->laporanMahad->normalisasiPeriode($request->query('periode'));
        $keyword = trim((string) $request->query('cari', ''));

        return view('musyrifah.laporan_detail', [
            'laporan' => $this->laporanMahad->laporan($jenis, $periode, $keyword),
            'keyword' => $keyword,
            'opsiPeriode' => $this->laporanMahad->opsiPeriode(),
            'filterAction' => route('musyrifah.laporan.detail', ['jenis' => $jenis]),
            'backUrl' => route('musyrifah.laporan', ['periode' => $periode, 'jenis' => $jenis]),
            'unduhUrl' => route('musyrifah.laporan.unduh', [
                'jenis' => $jenis,
                'periode' => $periode,
                'cari' => $keyword,
            ]),
            'modeMonitoring' => false,
        ]);
    }

    /**
     * Mengunduh laporan terpilih dalam format CSV.
     */
    public function unduh(Request $request, string $jenis): StreamedResponse
    {
        $this->pastikanPengelola($request);
        abort_unless(in_array($jenis, LaporanMahadService::JENIS_MUSYRIFAH, true), 404);

        $periode = $this->laporanMahad->normalisasiPeriode($request->query('periode'));
        $keyword = trim((string) $request->query('cari', ''));

        return $this->unduhCsv($this->laporanMahad->laporan($jenis, $periode, $keyword));
    }

    /**
     * @param  array<string, mixed>  $laporan
     */
    private function unduhCsv(array $laporan): StreamedResponse
    {
        $namaBerkas = 'laporan-'.$laporan['jenis'].'-'.$laporan['periode'].'.csv';

        return response()->streamDownload(function () use ($laporan): void {
            $berkas = fopen('php://output', 'wb');

            if ($berkas === false) {
                return;
            }

            // BOM agar karakter Indonesia terbaca rapi di Excel.
            fwrite($berkas, "\xEF\xBB\xBF");

            fputcsv($berkas, array_map(
                fn (array $kolom): string => $kolom['label'],
                $laporan['kolom']
            ));

            foreach ($laporan['baris'] as $baris) {
                fputcsv($berkas, array_map(
                    fn (array $kolom): string => (string) ($baris[$kolom['key']] ?? '-'),
                    $laporan['kolom']
                ));
            }

            fclose($berkas);
        }, $namaBerkas, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Hanya akun Musyrifah/Pengelola/Admin yang boleh membuka laporan.
     */
    private function pastikanPengelola(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['musyrifah', 'pengelola', 'admin'], true), 403);
    }
}
