<?php

namespace App\Services;

use ZipArchive;

/**
 * Penulis berkas Excel (.xlsx) tanpa dependensi tambahan.
 *
 * Berkas disusun langsung dari struktur Open XML (satu lembar per bagian
 * laporan) memakai ZipArchive bawaan PHP, sehingga tidak memerlukan
 * pustaka pihak ketiga.
 */
class LaporanExcelService
{
    private const GAYA_NORMAL = 0;

    private const GAYA_HEADER = 1;

    private const GAYA_ISI = 2;

    private const GAYA_JUDUL = 3;

    private const GAYA_LABEL = 4;

    private const GAYA_SUBJUDUL = 5;

    private const GAYA_RUPIAH = 6;

    private const GAYA_PERSEN = 7;

    /** @var list<string> */
    private array $namaTerpakai = [];

    /**
     * @param  array<string, mixed>  $laporan
     * @param  array<string, string>  $meta
     */
    public function render(array $laporan, array $meta = []): string
    {
        $this->namaTerpakai = [];

        $lembar = [];
        $lembar[] = [
            'nama' => $this->namaLembar('Identitas'),
            'baris' => $this->barisIdentitas($laporan, $meta),
        ];

        foreach (($laporan['seksi'] ?? []) as $seksi) {
            $lembar[] = [
                'nama' => $this->namaLembar((string) ($seksi['judul'] ?? 'Bagian')),
                'baris' => $this->barisSeksi($seksi),
            ];
        }

        $keterbatasan = $laporan['keterbatasan'] ?? [];

        if ($keterbatasan !== []) {
            $baris = [$this->barisJudul('Keterbatasan Data')];

            foreach ($keterbatasan as $catatan) {
                $baris[] = $this->barisTeks('', (string) $catatan);
            }

            $lembar[] = [
                'nama' => $this->namaLembar('Keterbatasan Data'),
                'baris' => $baris,
            ];
        }

        return $this->susunBerkas($lembar);
    }

    /**
     * @param  array<string, mixed>  $laporan
     * @param  array<string, string>  $meta
     * @return array<int, array<string, mixed>>
     */
    private function barisIdentitas(array $laporan, array $meta): array
    {
        $baris = [$this->barisJudul((string) ($laporan['judul'] ?? 'Laporan'))];

        $baris[] = $this->barisLabelIsi('Jenis laporan', (string) ($laporan['jenis_label'] ?? '-'));
        $baris[] = $this->barisLabelIsi('Periode laporan', (string) ($laporan['periode_label'] ?? '-'));
        $baris[] = $this->barisLabelIsi('Cakupan data', (string) ($laporan['cakupan'] ?? '-'));
        $baris[] = $this->barisLabelIsi('Dibangkitkan', (string) ($laporan['dibangkitkan_label'] ?? '-'));

        foreach ($meta as $label => $nilai) {
            $baris[] = $this->barisLabelIsi((string) $label, (string) $nilai);
        }

        $evaluasi = (string) ($laporan['catatan']['evaluasi'] ?? '');
        $kendala = (string) ($laporan['catatan']['kendala'] ?? '');

        $baris[] = $this->barisKosong();
        $baris[] = $this->barisSubJudul('Catatan Evaluasi');
        $baris[] = $this->barisTeks('', $evaluasi !== '' ? $evaluasi : 'Belum diisi.');
        $baris[] = $this->barisSubJudul('Kendala');
        $baris[] = $this->barisTeks('', $kendala !== '' ? $kendala : 'Belum diisi.');

        return $baris;
    }

    /**
     * @param  array<string, mixed>  $seksi
     * @return array<int, array<string, mixed>>
     */
    private function barisSeksi(array $seksi): array
    {
        $baris = [$this->barisJudul((string) ($seksi['judul'] ?? 'Bagian'))];

        if (filled($seksi['keterangan'] ?? null)) {
            $baris[] = $this->barisTeks('', (string) $seksi['keterangan']);
        }

        if (filled($seksi['ringkasan'] ?? null)) {
            $baris[] = $this->barisKosong();
            $baris[] = $this->barisSubJudul('Ringkasan');

            foreach ($seksi['ringkasan'] as $ringkasan) {
                $baris[] = $this->barisLabelIsi((string) ($ringkasan['label'] ?? ''), (string) $ringkasan['nilai']);
            }
        }

        foreach (($seksi['tabel'] ?? []) as $tabel) {
            $baris[] = $this->barisKosong();
            $baris[] = $this->barisSubJudul((string) ($tabel['judul'] ?? 'Tabel'));
            $baris[] = $this->barisHeader($tabel['kolom'] ?? []);

            if (($tabel['baris'] ?? []) === []) {
                $baris[] = $this->barisTeks('', 'Tidak ada data pada periode ini.');

                continue;
            }

            foreach ($tabel['baris'] as $nilaiBaris) {
                $sel = [];

                foreach (($tabel['kolom'] ?? []) as $kolom) {
                    $sel[] = $this->sel($kolom, $nilaiBaris);
                }

                $baris[] = ['gaya' => 'isi', 'sel' => $sel];
            }
        }

        foreach (($seksi['teks'] ?? []) as $label => $isi) {
            $baris[] = $this->barisKosong();
            $baris[] = $this->barisSubJudul((string) $label);
            $baris[] = $this->barisTeks('', (string) $isi);
        }

        foreach (($seksi['catatan'] ?? []) as $catatan) {
            $baris[] = $this->barisTeks('Catatan', (string) $catatan);
        }

        return $baris;
    }

    /**
     * @param  array<string, mixed>  $kolom
     * @param  array<string, mixed>  $nilaiBaris
     * @return array{nilai: mixed, format: string|null}
     */
    private function sel(array $kolom, array $nilaiBaris): array
    {
        $nilai = $nilaiBaris[$kolom['key'] ?? ''] ?? '';
        $format = $kolom['format'] ?? null;

        if (in_array($format, ['rupiah', 'persen'], true) && is_numeric($nilai)) {
            return ['nilai' => (float) $nilai, 'format' => $format];
        }

        if (is_bool($nilai)) {
            return ['nilai' => $nilai ? 'Ya' : 'Tidak', 'format' => null];
        }

        return ['nilai' => (string) $nilai, 'format' => null];
    }

    /**
     * @param  array<int, array<string, mixed>>  $kolom
     * @return array<string, mixed>
     */
    private function barisHeader(array $kolom): array
    {
        $sel = [];

        foreach ($kolom as $definisi) {
            $sel[] = ['nilai' => (string) ($definisi['label'] ?? ''), 'format' => null];
        }

        return ['gaya' => 'header', 'sel' => $sel];
    }

    /**
     * @return array<string, mixed>
     */
    private function barisJudul(string $teks): array
    {
        return ['gaya' => 'judul', 'sel' => [['nilai' => $teks, 'format' => null]]];
    }

    /**
     * @return array<string, mixed>
     */
    private function barisSubJudul(string $teks): array
    {
        return ['gaya' => 'subjudul', 'sel' => [['nilai' => $teks, 'format' => null]]];
    }

    /**
     * @return array<string, mixed>
     */
    private function barisLabelIsi(string $label, string $nilai): array
    {
        return [
            'gaya' => 'label',
            'sel' => [
                ['nilai' => $label, 'format' => null],
                ['nilai' => $nilai, 'format' => null],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function barisTeks(string $label, string $teks): array
    {
        return [
            'gaya' => 'isi',
            'sel' => [
                ['nilai' => $label, 'format' => null],
                ['nilai' => $teks, 'format' => null],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function barisKosong(): array
    {
        return ['gaya' => 'kosong', 'sel' => []];
    }

    /**
     * Nama lembar Excel dibatasi 31 karakter dan tidak boleh memuat
     * karakter terlarang.
     */
    private function namaLembar(string $judul): string
    {
        $nama = trim(preg_replace('/[\\\\\\/?*\\[\\]:]/', ' ', $judul) ?? '');
        $nama = trim(preg_replace('/\s+/', ' ', $nama) ?? '');
        $nama = mb_substr($nama !== '' ? $nama : 'Laporan', 0, 31);

        $dasar = $nama;
        $nomor = 2;

        while (in_array(mb_strtolower($nama), $this->namaTerpakai, true)) {
            $akhiran = ' ('.$nomor.')';
            $nama = mb_substr($dasar, 0, 31 - mb_strlen($akhiran)).$akhiran;
            $nomor++;
        }

        $this->namaTerpakai[] = mb_strtolower($nama);

        return $nama;
    }

    /**
     * @param  array<int, array{nama: string, baris: array<int, array<string, mixed>>}>  $lembar
     */
    private function susunBerkas(array $lembar): string
    {
        $path = tempnam(sys_get_temp_dir(), 'laporan-xlsx');

        if ($path === false) {
            throw new \RuntimeException('Berkas sementara Excel tidak dapat dibuat.');
        }

        $zip = new ZipArchive;

        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Berkas Excel tidak dapat disusun.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes(count($lembar)));
        $zip->addFromString('_rels/.rels', $this->relsUtama());
        $zip->addFromString('xl/workbook.xml', $this->workbook($lembar));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->relsWorkbook(count($lembar)));
        $zip->addFromString('xl/styles.xml', $this->styles());

        foreach ($lembar as $indeks => $data) {
            $zip->addFromString(
                'xl/worksheets/sheet'.($indeks + 1).'.xml',
                $this->worksheet($data['baris'])
            );
        }

        $zip->close();

        $isi = (string) file_get_contents($path);
        @unlink($path);

        return $isi;
    }

    /**
     * @param  array<int, array<string, mixed>>  $baris
     */
    private function worksheet(array $baris): string
    {
        $lebarKolom = $this->lebarKolom($baris);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';

        if ($lebarKolom !== []) {
            $xml .= '<cols>';

            foreach ($lebarKolom as $indeks => $lebar) {
                $nomor = $indeks + 1;
                $xml .= '<col min="'.$nomor.'" max="'.$nomor.'" width="'.$lebar.'" customWidth="1"/>';
            }

            $xml .= '</cols>';
        }

        $xml .= '<sheetData>';

        foreach ($baris as $nomorBaris => $data) {
            $nomor = $nomorBaris + 1;
            $gaya = $this->nomorGaya((string) ($data['gaya'] ?? 'isi'));

            $xml .= '<row r="'.$nomor.'"'.($gaya === self::GAYA_JUDUL ? ' ht="22" customHeight="1"' : '').'>';

            foreach (($data['sel'] ?? []) as $indeksSel => $sel) {
                $huruf = $this->hurufKolom($indeksSel + 1);
                $xml .= $this->selXml($huruf.$nomor, $sel, $gaya);
            }

            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    /**
     * @param  array{nilai: mixed, format: string|null}  $sel
     */
    private function selXml(string $referensi, array $sel, int $gaya): string
    {
        $nilai = $sel['nilai'] ?? '';
        $format = $sel['format'] ?? null;

        if (($format === 'rupiah' || $format === 'persen') && is_numeric($nilai)) {
            $gaya = $format === 'rupiah' ? self::GAYA_RUPIAH : self::GAYA_PERSEN;

            return '<c r="'.$referensi.'" s="'.$gaya.'"><v>'.(float) $nilai.'</v></c>';
        }

        $teks = $this->teksAman((string) $nilai);

        if ($teks === '') {
            return '<c r="'.$referensi.'" s="'.$gaya.'"/>';
        }

        return '<c r="'.$referensi.'" s="'.$gaya.'" t="inlineStr"><is><t xml:space="preserve">'
            .$teks.'</t></is></c>';
    }

    /**
     * @param  array<int, array<string, mixed>>  $baris
     * @return array<int, int>
     */
    private function lebarKolom(array $baris): array
    {
        $lebar = [];

        foreach ($baris as $data) {
            foreach (($data['sel'] ?? []) as $indeks => $sel) {
                $panjang = mb_strlen((string) ($sel['nilai'] ?? ''));
                $lebar[$indeks] = max($lebar[$indeks] ?? 10, min(70, $panjang + 2));
            }
        }

        return $lebar;
    }

    private function nomorGaya(string $gaya): int
    {
        return match ($gaya) {
            'header' => self::GAYA_HEADER,
            'judul' => self::GAYA_JUDUL,
            'label' => self::GAYA_LABEL,
            'subjudul' => self::GAYA_SUBJUDUL,
            'kosong' => self::GAYA_NORMAL,
            default => self::GAYA_ISI,
        };
    }

    private function hurufKolom(int $nomor): string
    {
        $huruf = '';

        while ($nomor > 0) {
            $nomor--;
            $huruf = chr(65 + ($nomor % 26)).$huruf;
            $nomor = intdiv($nomor, 26);
        }

        return $huruf;
    }

    private function teksAman(string $teks): string
    {
        $teks = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $teks) ?? '';

        return htmlspecialchars($teks, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function contentTypes(int $jumlahLembar): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
        $xml .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
        $xml .= '<Default Extension="xml" ContentType="application/xml"/>';
        $xml .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';
        $xml .= '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';

        for ($indeks = 1; $indeks <= $jumlahLembar; $indeks++) {
            $xml .= '<Override PartName="/xl/worksheets/sheet'.$indeks.'.xml" '
                .'ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        $xml .= '</Types>';

        return $xml;
    }

    private function relsUtama(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    /**
     * @param  array<int, array{nama: string, baris: array<int, array<string, mixed>>}>  $lembar
     */
    private function workbook(array $lembar): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>';

        foreach ($lembar as $indeks => $data) {
            $xml .= '<sheet name="'.$this->teksAman($data['nama']).'" sheetId="'.($indeks + 1)
                .'" r:id="rId'.($indeks + 1).'"/>';
        }

        $xml .= '</sheets></workbook>';

        return $xml;
    }

    private function relsWorkbook(int $jumlahLembar): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';

        for ($indeks = 1; $indeks <= $jumlahLembar; $indeks++) {
            $xml .= '<Relationship Id="rId'.$indeks.'" '
                .'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" '
                .'Target="worksheets/sheet'.$indeks.'.xml"/>';
        }

        $xml .= '<Relationship Id="rId'.($jumlahLembar + 1).'" '
            .'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" '
            .'Target="styles.xml"/>';
        $xml .= '</Relationships>';

        return $xml;
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="2">'
            .'<numFmt numFmtId="164" formatCode="&quot;Rp&quot; #,##0"/>'
            .'<numFmt numFmtId="165" formatCode="0&quot;%&quot;"/>'
            .'</numFmts>'
            .'<fonts count="4">'
            .'<font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="14"/><color rgb="FF6B0D1B"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FF6B0D1B"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="3">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFF8EDEF"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="2">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border><left style="thin"><color rgb="FFDDD3D6"/></left>'
            .'<right style="thin"><color rgb="FFDDD3D6"/></right>'
            .'<top style="thin"><color rgb="FFDDD3D6"/></top>'
            .'<bottom style="thin"><color rgb="FFDDD3D6"/></bottom>'
            .'<diagonal/></border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="8">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment wrapText="1" vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment wrapText="1" vertical="top"/></xf>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment vertical="top"/></xf>'
            .'<xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment vertical="top"/></xf>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }
}
