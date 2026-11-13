<?php

namespace App\Services;

/**
 * Penulis PDF ringan tanpa dependensi tambahan.
 *
 * Hanya memakai font inti PDF (Helvetica) sehingga berkas tetap kecil dan
 * dapat dibuka di peramban maupun pembaca PDF mana pun. Dipakai untuk
 * mengunduh dokumen laporan periodik Musyrifah.
 */
class LaporanPdfService
{
    private const LEBAR_HALAMAN = 842.0;

    private const TINGGI_HALAMAN = 595.0;

    private const MARGIN = 34.0;

    private const TINGGI_KAKI = 30.0;

    private const WARNA_UTAMA = [0.42, 0.05, 0.11];

    private const WARNA_GELAP = [0.16, 0.14, 0.16];

    private const WARNA_REDUP = [0.45, 0.42, 0.44];

    /**
     * Lebar glyph Helvetica (per 1000 unit) untuk kode ASCII 32-126.
     *
     * @var array<int, int>
     */
    private const LEBAR_GLYPH = [
        32 => 278, 33 => 278, 34 => 355, 35 => 556, 36 => 556, 37 => 889, 38 => 667,
        39 => 191, 40 => 333, 41 => 333, 42 => 389, 43 => 584, 44 => 278, 45 => 333,
        46 => 278, 47 => 278, 48 => 556, 49 => 556, 50 => 556, 51 => 556, 52 => 556,
        53 => 556, 54 => 556, 55 => 556, 56 => 556, 57 => 556, 58 => 278, 59 => 278,
        60 => 584, 61 => 584, 62 => 584, 63 => 556, 64 => 1015, 65 => 667, 66 => 667,
        67 => 722, 68 => 722, 69 => 667, 70 => 611, 71 => 778, 72 => 722, 73 => 278,
        74 => 500, 75 => 667, 76 => 556, 77 => 833, 78 => 722, 79 => 778, 80 => 667,
        81 => 778, 82 => 722, 83 => 667, 84 => 611, 85 => 722, 86 => 667, 87 => 944,
        88 => 667, 89 => 667, 90 => 611, 91 => 278, 92 => 278, 93 => 278, 94 => 469,
        95 => 556, 96 => 333, 97 => 556, 98 => 556, 99 => 500, 100 => 556, 101 => 556,
        102 => 278, 103 => 556, 104 => 556, 105 => 222, 106 => 222, 107 => 500,
        108 => 222, 109 => 833, 110 => 556, 111 => 556, 112 => 556, 113 => 556,
        114 => 333, 115 => 500, 116 => 278, 117 => 556, 118 => 500, 119 => 722,
        120 => 500, 121 => 500, 122 => 500, 123 => 334, 124 => 260, 125 => 334,
        126 => 584,
    ];

    /** @var list<string> */
    private array $isi = [];

    private string $aktif = '';

    private float $y = 0.0;

    private string $judul = 'Laporan';

    private string $subjudul = '';

    private float $lebarIsi = 0.0;

    /**
     * @param  array<string, mixed>  $laporan
     * @param  array<string, string>  $meta
     */
    public function render(array $laporan, array $meta = []): string
    {
        $this->isi = [];
        $this->judul = (string) ($laporan['judul'] ?? 'Laporan');
        $this->subjudul = trim((string) ($laporan['periode_label'] ?? ''));
        $this->lebarIsi = self::LEBAR_HALAMAN - (self::MARGIN * 2);

        $this->halamanBaru();
        $this->tulisIdentitas($laporan, $meta);

        foreach (($laporan['seksi'] ?? []) as $seksi) {
            $this->tulisSeksi($seksi);
        }

        $this->tulisKeterbatasan($laporan);

        return $this->susunBerkas();
    }

    /**
     * @param  array<string, mixed>  $laporan
     * @param  array<string, string>  $meta
     */
    private function tulisIdentitas(array $laporan, array $meta): void
    {
        $this->tulisBaris(
            (string) ($laporan['jenis_label'] ?? 'Laporan'),
            self::MARGIN,
            20,
            true,
            self::WARNA_UTAMA
        );

        $this->tulisBaris(
            'Periode '.($laporan['periode_label'] ?? '-'),
            self::MARGIN,
            12,
            true,
            self::WARNA_GELAP
        );

        $this->y -= 4;

        $cakupan = (string) ($laporan['cakupan'] ?? '');

        if ($cakupan !== '') {
            $this->tulisParagraf('Cakupan data: '.$cakupan, self::MARGIN, 8.5, self::WARNA_REDUP, $this->lebarIsi);
        }

        $daftarMeta = $meta;
        $daftarMeta['Dibangkitkan'] = (string) ($laporan['dibangkitkan_label'] ?? '-');

        foreach ($daftarMeta as $label => $nilai) {
            $this->tulisBaris($label.': '.$nilai, self::MARGIN, 8.5, false, self::WARNA_GELAP);
        }

        $this->y -= 6;
    }

    /**
     * @param  array<string, mixed>  $seksi
     */
    private function tulisSeksi(array $seksi): void
    {
        $this->butuhRuang(46);

        $this->garis(self::MARGIN, $this->y, self::MARGIN + $this->lebarIsi, $this->y, self::WARNA_UTAMA, 0.8);
        $this->y -= 16;

        $this->tulisParagraf(
            (string) ($seksi['judul'] ?? 'Bagian'),
            self::MARGIN,
            11.5,
            self::WARNA_UTAMA,
            $this->lebarIsi,
            true
        );

        if (filled($seksi['keterangan'] ?? null)) {
            $this->tulisParagraf((string) $seksi['keterangan'], self::MARGIN, 8.5, self::WARNA_REDUP, $this->lebarIsi);
        }

        $this->y -= 2;

        foreach (($seksi['ringkasan'] ?? []) as $ringkasan) {
            $this->tulisBaris(
                '- '.($ringkasan['label'] ?? '').': '.$ringkasan['nilai'],
                self::MARGIN + 6,
                9,
                false,
                self::WARNA_GELAP
            );
        }

        foreach (($seksi['tabel'] ?? []) as $tabel) {
            $this->tulisTabel($tabel);
        }

        foreach (($seksi['teks'] ?? []) as $label => $isi) {
            $this->y -= 4;
            $this->tulisParagraf((string) $label, self::MARGIN, 9, self::WARNA_GELAP, $this->lebarIsi, true);
            $this->tulisParagraf((string) $isi, self::MARGIN + 6, 9, self::WARNA_GELAP, $this->lebarIsi - 6);
        }

        foreach (($seksi['catatan'] ?? []) as $catatan) {
            $this->tulisParagraf('Catatan: '.$catatan, self::MARGIN + 6, 8, self::WARNA_REDUP, $this->lebarIsi - 6);
        }

        $this->y -= 10;
    }

    /**
     * @param  array<string, mixed>  $tabel
     */
    private function tulisTabel(array $tabel): void
    {
        $kolom = $tabel['kolom'] ?? [];
        $baris = $tabel['baris'] ?? [];

        if ($kolom === []) {
            return;
        }

        $this->butuhRuang(54);
        $this->y -= 4;

        if (filled($tabel['judul'] ?? null)) {
            $this->tulisParagraf((string) $tabel['judul'], self::MARGIN, 9, self::WARNA_GELAP, $this->lebarIsi, true);
            $this->y -= 2;
        }

        $lebar = $this->lebarKolom($kolom, $baris);
        $this->gambarKepalaTabel($kolom, $lebar);

        if ($baris === []) {
            $this->butuhRuang(20);
            $this->kotak(self::MARGIN, $this->y - 16, $this->lebarIsi, 16, null, [0.9, 0.88, 0.89]);
            $this->tulisBaris('Tidak ada data pada periode ini.', self::MARGIN + 4, 8, false, self::WARNA_REDUP, $this->y - 11);
            $this->y -= 22;

            return;
        }

        foreach ($baris as $indeks => $nilaiBaris) {
            $sel = [];

            foreach ($kolom as $indeksKolom => $definisi) {
                $sel[$indeksKolom] = $this->potongTeks(
                    $this->nilaiSel($definisi, $nilaiBaris),
                    $lebar[$indeksKolom] - 8,
                    7.5
                );
            }

            $jumlahBaris = max(array_map(fn (array $potongan): int => count($potongan), $sel));
            $tinggi = max(15, ($jumlahBaris * 9.5) + 5);

            if ($this->y - $tinggi < self::MARGIN + self::TINGGI_KAKI) {
                $this->halamanBaru();
                $this->gambarKepalaTabel($kolom, $lebar);
            }

            if ($indeks % 2 === 1) {
                $this->kotak(self::MARGIN, $this->y - $tinggi, $this->lebarIsi, $tinggi, [0.977, 0.965, 0.968], null);
            }

            $x = self::MARGIN;

            foreach ($kolom as $indeksKolom => $definisi) {
                $rata = ($definisi['align'] ?? 'left') === 'center' ? 'center' : 'left';
                $barisY = $this->y - 10.5;

                foreach ($sel[$indeksKolom] as $potongan) {
                    $posisi = $x + 4;

                    if ($rata === 'center') {
                        $posisi = $x + (($lebar[$indeksKolom] - $this->lebarTeks($potongan, 7.5)) / 2);
                    }

                    $this->tulisBaris($potongan, $posisi, 7.5, false, self::WARNA_GELAP, $barisY);
                    $barisY -= 9.5;
                }

                $x += $lebar[$indeksKolom];
            }

            $this->garis(self::MARGIN, $this->y - $tinggi, self::MARGIN + $this->lebarIsi, $this->y - $tinggi, [0.88, 0.86, 0.87], 0.5);
            $this->y -= $tinggi;
        }

        $this->y -= 2;
    }

    /**
     * @param  array<int, array<string, mixed>>  $kolom
     * @param  array<int, array<string, mixed>>  $baris
     * @return array<int, float>
     */
    private function lebarKolom(array $kolom, array $baris): array
    {
        $bobot = [];

        foreach ($kolom as $definisi) {
            $panjang = mb_strlen((string) ($definisi['label'] ?? ''));
            $kunci = (string) ($definisi['key'] ?? '');

            foreach ($baris as $nilaiBaris) {
                $panjang = max($panjang, mb_strlen($this->nilaiSel($definisi, $nilaiBaris)));
            }

            $bobot[$kunci] = max(7, min(38, $panjang));
        }

        $total = array_sum($bobot) ?: 1;
        $lebar = [];
        $terpakai = 0.0;

        foreach ($bobot as $nilai) {
            $bagian = round($this->lebarIsi * ($nilai / $total), 2);
            $lebar[] = $bagian;
            $terpakai += $bagian;
        }

        // Selisih pembulatan diberikan ke kolom terakhir agar tabel pas.
        $lebar[count($lebar) - 1] += round($this->lebarIsi - $terpakai, 2);

        return $lebar;
    }

    /**
     * @param  array<int, array<string, mixed>>  $kolom
     * @param  array<int, float>  $lebar
     */
    private function gambarKepalaTabel(array $kolom, array $lebar): void
    {
        $this->butuhRuang(28);

        $this->kotak(self::MARGIN, $this->y - 16, $this->lebarIsi, 16, [0.96, 0.93, 0.94], [0.85, 0.82, 0.83]);

        $x = self::MARGIN;

        foreach ($kolom as $indeks => $definisi) {
            $label = $this->potongTeks((string) ($definisi['label'] ?? ''), $lebar[$indeks] - 8, 7.5, true);
            $this->tulisBaris($label[0] ?? '', $x + 4, 7.5, true, self::WARNA_UTAMA, $this->y - 11);
            $x += $lebar[$indeks];
        }

        $this->y -= 16;
    }

    /**
     * @param  array<string, mixed>  $kolom
     * @param  array<string, mixed>  $baris
     */
    private function nilaiSel(array $kolom, array $baris): string
    {
        $nilai = $baris[$kolom['key'] ?? ''] ?? '-';
        $format = $kolom['format'] ?? null;

        if ($format === 'rupiah' && is_numeric($nilai)) {
            return 'Rp '.number_format((float) $nilai, 0, ',', '.');
        }

        if ($format === 'persen' && is_numeric($nilai)) {
            return (int) round((float) $nilai).'%';
        }

        if (is_bool($nilai)) {
            return $nilai ? 'Ya' : 'Tidak';
        }

        if ($nilai === null || $nilai === '') {
            return '-';
        }

        return (string) $nilai;
    }

    /**
     * @param  array<string, mixed>  $laporan
     */
    private function tulisKeterbatasan(array $laporan): void
    {
        $daftar = $laporan['keterbatasan'] ?? [];

        if ($daftar === []) {
            return;
        }

        $this->butuhRuang(60);
        $this->y -= 6;
        $this->tulisParagraf('Keterbatasan Data', self::MARGIN, 9.5, self::WARNA_UTAMA, $this->lebarIsi, true);

        foreach ($daftar as $catatan) {
            $this->tulisParagraf('- '.$catatan, self::MARGIN + 4, 8, self::WARNA_REDUP, $this->lebarIsi - 4);
        }
    }

    private function halamanBaru(): void
    {
        if ($this->aktif !== '') {
            $this->isi[] = $this->aktif;
        }

        $this->aktif = '';
        $this->y = self::TINGGI_HALAMAN - self::MARGIN;

        $nomor = count($this->isi) + 1;
        $judul = $this->judul.($this->subjudul !== '' ? ' · '.$this->subjudul : '');

        $this->tulisBaris($judul, self::MARGIN, 9, true, self::WARNA_UTAMA, $this->y - 2);
        $this->tulisBaris(
            'Halaman '.$nomor,
            self::MARGIN,
            8,
            false,
            self::WARNA_REDUP,
            $this->y - 2,
            $this->lebarIsi
        );

        $this->garis(
            self::MARGIN,
            $this->y - 10,
            self::MARGIN + $this->lebarIsi,
            $this->y - 10,
            [0.85, 0.82, 0.83],
            0.5
        );

        $this->y -= 24;
    }

    private function butuhRuang(float $tinggi): void
    {
        if ($this->y - $tinggi < self::MARGIN + self::TINGGI_KAKI) {
            $this->halamanBaru();
        }
    }

    private function tulisBaris(
        string $teks,
        float $x,
        float $ukuran,
        bool $tebal = false,
        array $warna = self::WARNA_GELAP,
        ?float $y = null,
        ?float $lebarMaks = null,
    ): void {
        $otomatis = $y === null;
        $y ??= $this->y;

        $teks = $this->normalisasi($teks);

        if ($lebarMaks !== null) {
            $tersedia = $lebarMaks;

            if ($this->lebarTeks($teks, $ukuran, $tebal) > $tersedia) {
                while ($teks !== '' && $this->lebarTeks($teks, $ukuran, $tebal) > $tersedia) {
                    $teks = mb_substr($teks, 0, mb_strlen($teks) - 1);
                }
            }

            $x = self::MARGIN + $lebarMaks - $this->lebarTeks($teks, $ukuran, $tebal);
        }

        $this->aktif .= sprintf(
            "BT /%s %.2F Tf %.3F %.3F %.3F rg 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n",
            $tebal ? 'F2' : 'F1',
            $ukuran,
            $warna[0],
            $warna[1],
            $warna[2],
            $x,
            $y,
            $this->escape($teks)
        );

        if ($otomatis) {
            $this->y -= $ukuran * 1.45;
        }
    }

    private function tulisParagraf(
        string $teks,
        float $x,
        float $ukuran,
        array $warna,
        float $lebarMaks,
        bool $tebal = false,
    ): void {
        foreach ($this->potongTeks($teks, $lebarMaks, $ukuran, $tebal) as $potongan) {
            $this->tulisBaris($potongan, $x, $ukuran, $tebal, $warna);
        }
    }

    /**
     * @return array<int, string>
     */
    private function potongTeks(string $teks, float $lebarMaks, float $ukuran, bool $tebal = false): array
    {
        $teks = trim(preg_replace('/\s+/', ' ', $teks) ?? '');

        if ($teks === '') {
            return [''];
        }

        if ($lebarMaks <= 0) {
            return [$teks];
        }

        $baris = [];
        $aktif = '';

        foreach (explode(' ', $teks) as $kata) {
            $calon = $aktif === '' ? $kata : $aktif.' '.$kata;

            if ($this->lebarTeks($calon, $ukuran, $tebal) <= $lebarMaks) {
                $aktif = $calon;

                continue;
            }

            if ($aktif !== '') {
                $baris[] = $aktif;
                $aktif = '';
            }

            // Kata tunggal yang lebih panjang dari kolom dipotong per karakter.
            while ($this->lebarTeks($kata, $ukuran, $tebal) > $lebarMaks && mb_strlen($kata) > 1) {
                $potong = mb_strlen($kata);

                while ($potong > 0 && $this->lebarTeks(mb_substr($kata, 0, $potong), $ukuran, $tebal) > $lebarMaks) {
                    $potong--;
                }

                $potong = max(1, $potong);
                $baris[] = mb_substr($kata, 0, $potong);
                $kata = mb_substr($kata, $potong);
            }

            $aktif = $kata;
        }

        if ($aktif !== '') {
            $baris[] = $aktif;
        }

        return $baris === [] ? [''] : $baris;
    }

    private function lebarTeks(string $teks, float $ukuran, bool $tebal = false): float
    {
        $teks = $this->normalisasi($teks);
        $total = 0;
        $panjang = strlen($teks);

        for ($i = 0; $i < $panjang; $i++) {
            $total += self::LEBAR_GLYPH[ord($teks[$i])] ?? 556;
        }

        return ($total / 1000) * $ukuran * ($tebal ? 1.04 : 1.0);
    }

    private function garis(float $x1, float $y1, float $x2, float $y2, array $warna, float $tebal): void
    {
        $this->aktif .= sprintf(
            "%.3F %.3F %.3F RG %.2F w %.2F %.2F m %.2F %.2F l S\n",
            $warna[0],
            $warna[1],
            $warna[2],
            $tebal,
            $x1,
            $y1,
            $x2,
            $y2
        );
    }

    private function kotak(float $x, float $y, float $lebar, float $tinggi, ?array $isi, ?array $garis): void
    {
        if ($isi !== null) {
            $this->aktif .= sprintf(
                "%.3F %.3F %.3F rg %.2F %.2F %.2F %.2F re f\n",
                $isi[0],
                $isi[1],
                $isi[2],
                $x,
                $y,
                $lebar,
                $tinggi
            );
        }

        if ($garis !== null) {
            $this->aktif .= sprintf(
                "%.3F %.3F %.3F RG 0.5 w %.2F %.2F %.2F %.2F re S\n",
                $garis[0],
                $garis[1],
                $garis[2],
                $x,
                $y,
                $lebar,
                $tinggi
            );
        }
    }

    private function normalisasi(string $teks): string
    {
        $teks = str_replace(
            ['’', '‘', '“', '”', '–', '—', '…', '·', '•', "\r", "\n"],
            ["'", "'", '"', '"', '-', '-', '...', '|', '-', ' ', ' '],
            $teks
        );

        $hasil = @mb_convert_encoding($teks, 'Windows-1252', 'UTF-8');

        return $hasil === false ? $teks : $hasil;
    }

    private function escape(string $teks): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $teks);
    }

    private function susunBerkas(): string
    {
        if ($this->aktif !== '') {
            $this->isi[] = $this->aktif;
            $this->aktif = '';
        }

        $totalHalaman = count($this->isi);

        foreach ($this->isi as $indeks => $konten) {
            $kaki = '';

            $kaki .= sprintf(
                "%.3F %.3F %.3F RG 0.5 w %.2F %.2F m %.2F %.2F l S\n",
                0.85,
                0.82,
                0.83,
                self::MARGIN,
                self::TINGGI_KAKI - 4,
                self::MARGIN + $this->lebarIsi,
                self::TINGGI_KAKI - 4
            );

            $kaki .= sprintf(
                "BT /F1 7.5 Tf 0.45 0.42 0.44 rg 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n",
                self::MARGIN,
                self::TINGGI_KAKI - 15,
                $this->escape($this->normalisasi($this->judul.' · '.$this->subjudul))
            );

            $labelHalaman = 'Halaman '.($indeks + 1).' dari '.$totalHalaman;
            $xHalaman = self::MARGIN + $this->lebarIsi - $this->lebarTeks($labelHalaman, 7.5);

            $kaki .= sprintf(
                "BT /F1 7.5 Tf 0.45 0.42 0.44 rg 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n",
                $xHalaman,
                self::TINGGI_KAKI - 15,
                $this->escape($labelHalaman)
            );

            $this->isi[$indeks] = $konten.$kaki;
        }

        $objek = [];
        $objek[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objek[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objek[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        $anak = [];
        $nomor = 5;

        foreach ($this->isi as $konten) {
            $idHalaman = $nomor++;
            $idKonten = $nomor++;

            $anak[] = $idHalaman.' 0 R';
            $objek[$idHalaman] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.0F %.0F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                self::LEBAR_HALAMAN,
                self::TINGGI_HALAMAN,
                $idKonten
            );
            $objek[$idKonten] = '<< /Length '.strlen($konten)." >>\nstream\n".$konten.'endstream';
        }

        $objek[2] = '<< /Type /Pages /Kids ['.implode(' ', $anak).'] /Count '.count($anak).' >>';

        ksort($objek);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $posisi = [];
        $maksimal = (int) max(array_keys($objek));

        for ($id = 1; $id <= $maksimal; $id++) {
            $posisi[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".($objek[$id] ?? '<< >>')."\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= 'xref'."\n".'0 '.($maksimal + 1)."\n0000000000 65535 f \n";

        for ($id = 1; $id <= $maksimal; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $posisi[$id] ?? 0);
        }

        $pdf .= "trailer\n<< /Size ".($maksimal + 1)." /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";

        return $pdf;
    }
}
