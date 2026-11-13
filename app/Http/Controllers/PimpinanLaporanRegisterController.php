<?php

namespace App\Http\Controllers;

use App\Models\LaporanMahad;
use App\Services\LaporanExcelService;
use App\Services\LaporanMahadService;
use App\Services\LaporanPdfService;
use App\Services\LaporanRegisterService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Detail satu laporan pada register Pimpinan.
 *
 * Hanya menampilkan metadata, pratinjau isi, dan riwayat aktivitas. Tidak
 * ada aksi yang mengubah isi laporan buatan Musyrifah.
 */
class PimpinanLaporanRegisterController extends Controller
{
    public function __construct(
        private readonly LaporanMahadService $laporanMahad,
        private readonly LaporanRegisterService $register,
        private readonly LaporanPdfService $pdf,
        private readonly LaporanExcelService $excel,
    ) {}

    public function detail(Request $request, LaporanMahad $laporan): View
    {
        return $this->tampilkan($request, $laporan, false);
    }

    /**
     * Tampilan siap cetak (metadata ikut tercetak pada kepala laporan).
     */
    public function cetak(Request $request, LaporanMahad $laporan): View
    {
        return $this->tampilkan($request, $laporan, true);
    }

    /**
     * Unduh dokumen laporan periodik sebagai PDF (hanya baca).
     */
    public function unduhPdf(Request $request, LaporanMahad $laporan): Response
    {
        $this->pastikanPimpinan($request);
        $this->pastikanPeriodik($laporan);

        return response($this->pdf->render($laporan->konten, $this->metaDokumen($laporan)), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->namaBerkas($laporan, 'pdf').'"',
        ]);
    }

    /**
     * Unduh dokumen laporan periodik sebagai Excel (hanya baca).
     */
    public function unduhExcel(Request $request, LaporanMahad $laporan): Response
    {
        $this->pastikanPimpinan($request);
        $this->pastikanPeriodik($laporan);

        return response($this->excel->render($laporan->konten, $this->metaDokumen($laporan)), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$this->namaBerkas($laporan, 'xlsx').'"',
        ]);
    }

    private function tampilkan(Request $request, LaporanMahad $laporan, bool $cetak): View
    {
        $this->pastikanPimpinan($request);

        $laporan->load(['pembuat:id,name,role', 'penerbit:id,name']);

        $periodik = $laporan->periodik() && is_array($laporan->konten) && $laporan->konten !== [];
        $bisaPratinjau = $periodik || in_array($laporan->jenis, LaporanMahadService::JENIS_PIMPINAN, true);
        $rekap = (! $periodik && $bisaPratinjau)
            ? $this->laporanMahad->laporan($laporan->jenis, $laporan->periode)
            : null;

        return view('pimpinan.laporan_register_detail', [
            'register' => $laporan,
            'rekap' => $rekap,
            'periodik' => $periodik,
            'riwayat' => $this->register->riwayat($laporan),
            'cetak' => $cetak,
            'metaCetak' => [
                'Jenis laporan' => $laporan->judul,
                'Periode' => $laporan->labelPeriode(),
                'Unit sumber' => $laporan->labelUnit(),
                'Pembuat' => $laporan->identitasPembuat().' ('.$laporan->labelSumber().')',
                'Status' => $laporan->labelStatus(),
                'Dibuat' => $laporan->created_at?->format('d/m/Y H:i') ?? '-',
            ],
            'keyword' => '',
            'opsiPeriode' => [],
            'filterAction' => route('pimpinan.laporan-mahad'),
            'backUrl' => route('pimpinan.laporan-mahad'),
            'unduhUrl' => (! $periodik && $bisaPratinjau)
                ? route('pimpinan.laporan.unduh', [
                    'jenis' => $laporan->jenis,
                    'periode' => $laporan->periode,
                ])
                : null,
            'modeMonitoring' => true,
            'sembunyikanFilter' => true,
        ]);
    }

    private function pastikanPimpinan(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['pimpinan', 'admin'], true), 403);
    }

    private function pastikanPeriodik(LaporanMahad $laporan): void
    {
        abort_unless($laporan->periodik(), 404);
        abort_unless(is_array($laporan->konten) && $laporan->konten !== [], 404);
    }

    /**
     * @return array<string, string>
     */
    private function metaDokumen(LaporanMahad $laporan): array
    {
        return [
            'Nama laporan' => $laporan->judul,
            'Periode laporan' => $laporan->labelPeriode(),
            'Status' => $laporan->labelStatus(),
            'Pembuat' => $laporan->identitasPembuat(),
            'Versi' => 'Versi '.(int) $laporan->versi,
        ];
    }

    private function namaBerkas(LaporanMahad $laporan, string $ekstensi): string
    {
        $periode = preg_replace('/[^0-9a-zA-Z\-]/', '-', (string) $laporan->periode) ?: 'periode';

        return 'laporan-'.$laporan->jenis.'-'.$periode.'-v'.(int) $laporan->versi.'.'.$ekstensi;
    }
}
