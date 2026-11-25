<?php

namespace App\Http\Controllers;

use App\Services\LaporanMahadService;
use App\Services\LaporanRegisterService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PimpinanLaporanMahadController extends Controller
{
    public function __construct(
        private readonly LaporanMahadService $laporanMahad,
        private readonly LaporanRegisterService $register,
    ) {}

    public function penghuni(Request $request): View
    {
        return $this->tampilkan('penghuni', $request);
    }

    public function kamar(Request $request): View
    {
        return $this->tampilkan('kamar', $request);
    }

    public function kehadiran(Request $request): View
    {
        return $this->tampilkan('kehadiran', $request);
    }

    public function pembayaran(Request $request): View
    {
        return $this->tampilkan('pembayaran', $request);
    }

    public function pendaftaran(Request $request): View
    {
        return $this->tampilkan('pendaftaran', $request);
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
     * Mengunduh laporan terpilih dalam format CSV (hanya baca).
     */
    public function unduh(Request $request, string $jenis): StreamedResponse
    {
        $this->pastikanPimpinan($request);
        abort_unless(in_array($jenis, LaporanMahadService::JENIS_PIMPINAN, true), 404);

        $periode = $this->laporanMahad->normalisasiPeriode($request->query('periode'));
        $keyword = trim((string) $request->query('cari', ''));

        $laporan = $this->laporanMahad->laporan($jenis, $periode, $keyword);
        $namaBerkas = 'laporan-'.$jenis.'-'.$periode.'.csv';

        return response()->streamDownload(function () use ($laporan): void {
            $berkas = fopen('php://output', 'wb');

            if ($berkas === false) {
                return;
            }

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
     * Menampilkan satu laporan untuk Pimpinan (hanya lihat, tanpa aksi ubah data).
     */
    private function tampilkan(string $jenis, Request $request): View
    {
        $this->pastikanPimpinan($request);
        abort_unless(in_array($jenis, LaporanMahadService::JENIS_PIMPINAN, true), 404);

        $periode = $this->laporanMahad->normalisasiPeriode($request->query('periode'));
        $keyword = trim((string) $request->query('cari', ''));

        // Rekap otomatis sistem dipastikan tercatat untuk periode terpilih.
        $this->register->pastikanRekapSistem($periode);

        return view('pimpinan.laporan_detail', [
            'laporan' => $this->laporanMahad->laporan($jenis, $periode, $keyword),
            'keyword' => $keyword,
            'opsiPeriode' => $this->laporanMahad->opsiPeriode(),
            'filterAction' => route($this->namaRoute($jenis)),
            'unduhUrl' => route('pimpinan.laporan.unduh', [
                'jenis' => $jenis,
                'periode' => $periode,
                'cari' => $keyword,
            ]),
        ]);
    }

    private function namaRoute(string $jenis): string
    {
        return match ($jenis) {
            'penghuni' => 'pimpinan.laporan-penghuni',
            'kamar' => 'pimpinan.laporan-kamar',
            'kehadiran' => 'pimpinan.laporan-kehadiran',
            'pembayaran' => 'pimpinan.laporan-pembayaran',
            'pendaftaran' => 'pimpinan.laporan-pendaftaran',
            'pembelajaran' => 'pimpinan.laporan-pembelajaran',
            'perizinan' => 'pimpinan.laporan-perizinan',
            default => abort(404),
        };
    }

    /**
     * Pimpinan hanya boleh melihat laporan, bukan mengubah data.
     */
    private function pastikanPimpinan(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['pimpinan', 'admin'], true), 403);
    }
}
