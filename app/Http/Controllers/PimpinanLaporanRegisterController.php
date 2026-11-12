<?php

namespace App\Http\Controllers;

use App\Models\LaporanMahad;
use App\Services\LaporanMahadService;
use App\Services\LaporanRegisterService;
use Illuminate\Http\Request;
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

    private function tampilkan(Request $request, LaporanMahad $laporan, bool $cetak): View
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['pimpinan', 'admin'], true), 403);

        $laporan->load(['pembuat:id,name,role', 'penerbit:id,name']);

        $bisaPratinjau = in_array($laporan->jenis, LaporanMahadService::JENIS_PIMPINAN, true);
        $rekap = $bisaPratinjau
            ? $this->laporanMahad->laporan($laporan->jenis, $laporan->periode)
            : null;

        return view('pimpinan.laporan_register_detail', [
            'register' => $laporan,
            'rekap' => $rekap,
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
            'unduhUrl' => $bisaPratinjau
                ? route('pimpinan.laporan.unduh', [
                    'jenis' => $laporan->jenis,
                    'periode' => $laporan->periode,
                ])
                : null,
            'modeMonitoring' => true,
            'sembunyikanFilter' => true,
        ]);
    }
}
