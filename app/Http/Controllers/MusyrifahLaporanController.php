<?php

namespace App\Http\Controllers;

use App\Models\LaporanMahad;
use App\Services\LaporanExcelService;
use App\Services\LaporanMahadService;
use App\Services\LaporanPdfService;
use App\Services\LaporanPeriodikService;
use App\Services\LaporanRegisterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MusyrifahLaporanController extends Controller
{
    public function __construct(
        private readonly LaporanMahadService $laporanMahad,
        private readonly LaporanRegisterService $register,
        private readonly LaporanPeriodikService $periodik,
        private readonly LaporanPdfService $pdf,
        private readonly LaporanExcelService $excel,
    ) {}

    /**
     * Menampilkan daftar kategori laporan Musyrifah.
     */
    public function index(Request $request): View
    {
        $this->pastikanPengelola($request);

        $periode = $this->laporanMahad->normalisasiPeriode($request->query('periode'));
        $jenis = (string) $request->query('jenis', 'semua');

        $filterPeriodik = [
            'jenis' => trim((string) $request->query('p_jenis', '')),
            'tahun' => trim((string) $request->query('p_tahun', '')),
            'bulan' => trim((string) $request->query('p_bulan', '')),
            'status' => trim((string) $request->query('p_status', '')),
        ];

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
            'labelPeriode' => $this->laporanMahad->labelPeriode($periode),
            'register' => $this->register->daftar([
                'sumber' => LaporanMahad::SUMBER_MUSYRIFAH,
                'periode' => $periode,
            ], 10),
            'opsiJenis' => collect(LaporanRegisterService::JENIS_SISTEM)
                ->map(fn (string $kunci): array => [
                    'value' => $kunci,
                    'label' => LaporanRegisterService::JUDUL[$kunci] ?? ucfirst($kunci),
                ])
                ->values()
                ->all(),
            'opsiLantai' => $this->laporanMahad->daftarLantai(),
            'daftarPeriodik' => $this->register->daftarPeriodik($filterPeriodik, 10),
            'filterPeriodik' => $filterPeriodik,
            'opsiJenisPeriodik' => $this->periodik->opsiJenis(),
            'opsiTahun' => $this->periodik->opsiTahun(),
            'opsiBulan' => $this->periodik->opsiBulan(),
            'opsiStatusPeriodik' => [
                ['value' => LaporanMahad::STATUS_DRAFT, 'label' => 'Draft'],
                ['value' => LaporanMahad::STATUS_DITERBITKAN, 'label' => 'Diterbitkan'],
                ['value' => LaporanMahad::STATUS_DIARSIPKAN, 'label' => 'Diarsipkan'],
            ],
        ]);
    }

    /**
     * Mencatat laporan baru milik Musyrifah (status awal: draft).
     */
    public function simpanLaporan(Request $request): RedirectResponse
    {
        $this->pastikanPengelola($request);

        $data = $request->validate([
            'jenis' => ['required', 'string', 'in:'.implode(',', LaporanRegisterService::JENIS_SISTEM)],
            'periode' => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'lantai' => ['nullable', 'integer', 'between:1,10'],
            'unit' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $laporan = $this->register->buatLaporanMusyrifah(
            $request->user(),
            $data['jenis'],
            $data['periode'],
            isset($data['lantai']) ? (int) $data['lantai'] : null,
            $data['unit'] ?? null,
            $data['catatan'] ?? null,
        );

        return redirect()
            ->route('musyrifah.laporan', ['periode' => $laporan->periode])
            ->with('status', 'Laporan '.$laporan->judul.' berhasil dibuat sebagai draft.');
    }

    /**
     * Menerbitkan laporan milik Musyrifah.
     */
    public function terbitkanLaporan(Request $request, LaporanMahad $laporan): RedirectResponse
    {
        $this->pastikanPengelola($request);
        $this->pastikanMilikMusyrifah($laporan);

        $catatan = trim((string) $request->input('catatan', ''));
        $this->register->terbitkan($laporan, $request->user(), $catatan ?: null);

        return redirect()
            ->route('musyrifah.laporan', ['periode' => $laporan->periode])
            ->with('status', 'Laporan '.$laporan->judul.' berhasil diterbitkan.');
    }

    /**
     * Mengarsipkan laporan milik Musyrifah.
     */
    public function arsipkanLaporan(Request $request, LaporanMahad $laporan): RedirectResponse
    {
        $this->pastikanPengelola($request);
        $this->pastikanMilikMusyrifah($laporan);

        $catatan = trim((string) $request->input('catatan', ''));
        $this->register->arsipkan($laporan, $request->user(), $catatan ?: null);

        return redirect()
            ->route('musyrifah.laporan', ['periode' => $laporan->periode])
            ->with('status', 'Laporan '.$laporan->judul.' berhasil diarsipkan.');
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
            'backUrl' => route('musyrifah.laporan', ['periode' => $periode,]),
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
     * Halaman "Buat Laporan": memilih Laporan Bulanan atau Laporan Tahunan.
     */
    public function buatLaporan(Request $request): View
    {
        $this->pastikanPengelola($request);

        $jenis = $this->periodik->normalisasiJenis($request->query('jenis'));
        $tahun = $this->periodik->normalisasiTahun($request->query('tahun'));
        $bulan = $this->periodik->normalisasiBulan($request->query('bulan'));

        return view('musyrifah.laporan_buat', [
            'jenis' => $jenis,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'opsiJenis' => $this->periodik->opsiJenis(),
            'opsiTahun' => $this->periodik->opsiTahun(),
            'opsiBulan' => $this->periodik->opsiBulan(),
            'labelPeriode' => $this->periodik->labelPeriode($jenis, $tahun, $bulan),
            'riwayatPeriodik' => $this->register->daftarPeriodik([], 5),
        ]);
    }

    /**
     * Pratinjau isi laporan periodik sebelum disimpan menjadi dokumen.
     */
    public function pratinjauPeriodik(Request $request): View
    {
        $this->pastikanPengelola($request);

        $data = $this->validasiPeriodik($request);

        return view('musyrifah.laporan_periodik', [
            'laporan' => $this->susunPeriodik($data),
            'pratinjau' => true,
            'register' => null,
            'riwayat' => collect(),
            'jenis' => $data['jenis'],
            'tahun' => (int) $data['tahun'],
            'bulan' => $data['bulan'],
            'evaluasi' => (string) ($data['evaluasi'] ?? ''),
            'kendala' => (string) ($data['kendala'] ?? ''),
            'catatan' => (string) ($data['catatan'] ?? ''),
            'backUrl' => route('musyrifah.laporan.buat', [
                'jenis' => $data['jenis'],
                'tahun' => $data['tahun'],
                'bulan' => $data['bulan'],
            ]),
        ]);
    }

    /**
     * Menyimpan laporan periodik beserta potret isinya (status awal draft).
     */
    public function simpanPeriodik(Request $request): RedirectResponse
    {
        $this->pastikanPengelola($request);

        $data = $this->validasiPeriodik($request);
        $konten = $this->susunPeriodik($data);

        $laporan = $this->register->buatLaporanPeriodik(
            $request->user(),
            (string) $konten['jenis'],
            (string) $konten['periode'],
            (int) $data['tahun'],
            $konten['jenis'] === 'tahunan' ? null : (int) $data['bulan'],
            $konten,
            filled($data['catatan'] ?? null) ? (string) $data['catatan'] : null,
        );

        return redirect()
            ->route('musyrifah.laporan.periodik', $laporan)
            ->with('status', $laporan->judul.' periode '.$laporan->labelPeriode().' tersimpan sebagai draft.');
    }

    /**
     * Membuka kembali laporan periodik yang sudah tersimpan (isi tidak berubah).
     */
    public function lihatPeriodik(Request $request, LaporanMahad $laporan): View
    {
        $this->pastikanPengelola($request);
        $this->pastikanPeriodik($laporan);

        return view('musyrifah.laporan_periodik', [
            'laporan' => $laporan->konten,
            'pratinjau' => false,
            'register' => $laporan,
            'riwayat' => $this->register->riwayat($laporan),
            'jenis' => $laporan->jenis,
            'tahun' => (int) $laporan->tahun,
            'bulan' => $laporan->bulan,
            'evaluasi' => (string) ($laporan->konten['catatan']['evaluasi'] ?? ''),
            'kendala' => (string) ($laporan->konten['catatan']['kendala'] ?? ''),
            'catatan' => (string) ($laporan->catatan ?? ''),
            'backUrl' => route('musyrifah.laporan', [
                'p_jenis' => $laporan->jenis,
                'p_tahun' => $laporan->tahun,
            ]),
        ]);
    }

    /**
     * Tampilan siap cetak dokumen laporan periodik.
     */
    public function cetakPeriodik(Request $request, LaporanMahad $laporan): View
    {
        $this->pastikanPengelola($request);
        $this->pastikanPeriodik($laporan);

        return view('musyrifah.laporan_periodik_cetak', [
            'laporan' => $laporan->konten,
            'meta' => $this->metaDokumen($laporan),
        ]);
    }

    /**
     * Mengunduh dokumen laporan periodik sebagai PDF.
     */
    public function unduhPdf(Request $request, LaporanMahad $laporan): Response
    {
        $this->pastikanPengelola($request);
        $this->pastikanPeriodik($laporan);

        return response($this->pdf->render($laporan->konten, $this->metaDokumen($laporan)), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->namaBerkas($laporan, 'pdf').'"',
        ]);
    }

    /**
     * Mengunduh dokumen laporan periodik sebagai Excel (.xlsx).
     */
    public function unduhExcel(Request $request, LaporanMahad $laporan): Response
    {
        $this->pastikanPengelola($request);
        $this->pastikanPeriodik($laporan);

        return response($this->excel->render($laporan->konten, $this->metaDokumen($laporan)), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$this->namaBerkas($laporan, 'xlsx').'"',
        ]);
    }

    /**
     * Membangkitkan ulang isi laporan secara sadar; hanya atas perintah Musyrifah.
     */
    public function perbaruiPeriodik(Request $request, LaporanMahad $laporan): RedirectResponse
    {
        $this->pastikanPengelola($request);
        $this->pastikanPeriodik($laporan);
        $this->pastikanPemilikPeriodik($request, $laporan);

        abort_if(
            $laporan->status === LaporanMahad::STATUS_DIARSIPKAN,
            403,
            'Laporan yang sudah diarsipkan tidak dapat dibangkitkan ulang.'
        );

        $data = $request->validate([
            'evaluasi' => ['nullable', 'string', 'max:2000'],
            'kendala' => ['nullable', 'string', 'max:2000'],
        ]);

        $catatanLama = (array) ($laporan->konten['catatan'] ?? []);
        $periode = $this->periodeLaporan($laporan);

        $konten = $this->periodik->susun(
            $laporan->jenis,
            $periode['tahun'],
            $periode['bulan'],
            [
                'evaluasi' => $request->has('evaluasi')
                    ? (string) $data['evaluasi']
                    : (string) ($catatanLama['evaluasi'] ?? ''),
                'kendala' => $request->has('kendala')
                    ? (string) $data['kendala']
                    : (string) ($catatanLama['kendala'] ?? ''),
            ],
        );

        $this->register->perbaruiKontenPeriodik($laporan, $request->user(), $konten);

        return redirect()
            ->route('musyrifah.laporan.periodik', $laporan)
            ->with('status', 'Isi laporan dibangkitkan ulang menjadi versi '.(int) $laporan->versi.'.');
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
     * Memvalidasi pilihan jenis dan periode laporan periodik.
     *
     * @return array<string, mixed>
     */
    private function validasiPeriodik(Request $request): array
    {
        $data = $request->validate([
            'jenis' => ['required', 'string', 'in:'.implode(',', array_keys(LaporanPeriodikService::JENIS))],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['nullable', 'required_if:jenis,bulanan', 'integer', 'between:1,12'],
            'evaluasi' => ['nullable', 'string', 'max:2000'],
            'kendala' => ['nullable', 'string', 'max:2000'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $data['jenis'] = $this->periodik->normalisasiJenis((string) $data['jenis']);
        $data['tahun'] = (int) $data['tahun'];
        $data['bulan'] = filled($data['bulan'] ?? null) ? (int) $data['bulan'] : null;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function susunPeriodik(array $data): array
    {
        return $this->periodik->susun(
            (string) $data['jenis'],
            (int) $data['tahun'],
            (int) ($data['bulan'] ?? 1),
            [
                'evaluasi' => (string) ($data['evaluasi'] ?? ''),
                'kendala' => (string) ($data['kendala'] ?? ''),
            ],
        );
    }

    /**
     * Laporan periodik wajib memiliki potret isi yang tersimpan.
     */
    private function pastikanPeriodik(LaporanMahad $laporan): void
    {
        abort_unless($laporan->periodik(), 404);
        abort_unless(is_array($laporan->konten) && $laporan->konten !== [], 404);
    }

    /**
     * Pembaruan isi hanya boleh dilakukan pembuat laporan atau admin/pengelola.
     */
    private function pastikanPemilikPeriodik(Request $request, LaporanMahad $laporan): void
    {
        $user = $request->user();
        $role = strtolower((string) $user?->role);

        if (in_array($role, ['pengelola', 'admin'], true)) {
            return;
        }

        abort_unless((int) $laporan->dibuat_oleh === (int) $user?->getKey(), 403);
    }

    /**
     * Tahun dan bulan laporan, termasuk untuk baris lama tanpa kolom periode.
     *
     * @return array{tahun: int, bulan: int}
     */
    private function periodeLaporan(LaporanMahad $laporan): array
    {
        if ($laporan->tahun !== null) {
            return [
                'tahun' => (int) $laporan->tahun,
                'bulan' => (int) ($laporan->bulan ?? 1),
            ];
        }

        $cocok = [];

        if (preg_match('/^(\d{4})(?:-(\d{2}))?/', (string) $laporan->periode, $cocok) === 1) {
            return [
                'tahun' => (int) $cocok[1],
                'bulan' => isset($cocok[2]) ? (int) $cocok[2] : 1,
            ];
        }

        return ['tahun' => (int) now()->year, 'bulan' => (int) now()->month];
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

    /**
     * Hanya akun Musyrifah/Pengelola/Admin yang boleh membuka laporan.
     */
    private function pastikanPengelola(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['musyrifah', 'pengelola', 'admin'], true), 403);
    }

    /**
     * Hanya laporan bersumber Musyrifah yang boleh diubah statusnya di sini.
     */
    private function pastikanMilikMusyrifah(LaporanMahad $laporan): void
    {
        abort_unless($laporan->sumber === LaporanMahad::SUMBER_MUSYRIFAH, 403);
    }
}
