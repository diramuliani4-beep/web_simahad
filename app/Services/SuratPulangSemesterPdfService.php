<?php

namespace App\Services;

use App\Models\SuratPerizinan;
use Illuminate\Support\Carbon;

/**
 * Penulis PDF A4 untuk surat izin pulang semester sesuai format resmi SIMAHAD.
 * Menggunakan font inti PDF sehingga tidak memerlukan package tambahan.
 */
class SuratPulangSemesterPdfService
{
    private const PAGE_W = 595.28;

    private const PAGE_H = 841.89;

    private const LEFT = 48.0;

    private const RIGHT = 547.0;

    private string $content = '';

    private float $y = 0.0;

    public function render(SuratPerizinan $surat): string
    {
        abort_unless($surat->suratSemester(), 404);
        abort_unless(filled($surat->nomor_surat), 422, 'Nomor surat belum tersedia.');

        $surat->loadMissing(['user.kamar']);
        $mahasiswa = $surat->user;
        abort_unless($mahasiswa !== null, 404, 'Data mahasiswa tidak ditemukan.');

        $pendaftaran = $mahasiswa->pendaftarans()
            ->where('status', 'disetujui')
            ->latest('id')
            ->first();

        $nama = $this->nilaiPertama($mahasiswa->name, $pendaftaran?->nama_lengkap);
        $nim = $this->nilaiPertama($mahasiswa->nim, $pendaftaran?->nim);
        $prodi = $this->nilaiPertama($pendaftaran?->program_studi);

        $dataKurang = array_values(array_filter([
            $nama === '' ? 'nama mahasiswa' : null,
            $nim === '' ? 'NIM' : null,
            $prodi === '' ? 'program studi' : null,
        ]));

        abort_if(
            $dataKurang !== [],
            422,
            'Identitas surat belum lengkap: '.implode(', ', $dataKurang).'. Perbarui data mahasiswa terlebih dahulu.',
        );

        $tanggalSurat = ($surat->created_at ?? now())
            ->copy()
            ->timezone('Asia/Jakarta')
            ->locale('id')
            ->translatedFormat('d F Y');

        $this->content = '';
        $this->kopSurat();
        $this->teksKanan('Padang, '.$tanggalSurat, self::RIGHT, 694, 11);
        $this->teksTengah('SURAT IZIN PULANG MAHASANTRI', 297.64, 662, 13, true);
        $this->teksTengah('Nomor: '.$surat->nomor_surat.'    Paraf: ................', 297.64, 642, 10.5);

        $this->y = 608;
        $this->paragraf(
            "Yang bertanda tangan di bawah ini, Pengurus / Musyrifah Ma'had Al-Jami'ah Tuanku Imam Bonjol Padang, memberikan izin pulang kepada mahasiswa yang tersebut di bawah ini:",
            11,
            17,
            28,
        );

        $this->y -= 8;
        $kamar = $mahasiswa->kamar;
        $namaKamar = $kamar === null ? '-' : trim($kamar->nama_kamar.' '.$kamar->nomor_kamar);

        foreach ([
            'Nama Lengkap' => $nama,
            'Prodi' => $prodi,
            'Kamar' => $namaKamar,
            'Tujuan' => $this->nilaiTeks($surat->lokasi),
            'Tanggal Pulang' => $this->tanggalTeks($surat->tanggal_mulai),
            'Tanggal Kembali' => $this->tanggalTeks($surat->tanggal_selesai),
            'Alasan Pulang' => $this->nilaiTeks($surat->alasan),
            'Keterangan' => $this->nilaiTeks($surat->keterangan),
            'Penjemputan/Transportasi' => $this->nilaiTeks($surat->transportasi),
        ] as $label => $value) {
            $this->barisIdentitas($label, $value);
        }

        $this->y -= 10;
        $this->paragraf(
            "Mahasiswa yang bersangkutan wajib menjaga nama baik Ma'had Al-Jami'ah Tuanku Imam Bonjol Padang selama berada di luar lingkungan asrama, serta mengikuti segala aturan yang berlaku. Demikian surat izin ini dibuat untuk dipergunakan sebagaimana mestinya.",
            11,
            16,
            28,
        );
        $this->paragraf('Atas perhatian dan kerjasama, kami ucapkan terima kasih.', 11, 16, 0);

        abort_if($this->y < 280, 422, 'Isi surat terlalu panjang. Ringkas alasan dan keterangan sebelum mencetak.');

        $labelTtd = min(270.0, $this->y - 20);
        $this->teksTengah('Mengelah,', 171, $labelTtd, 11);
        $this->teksTengah('Mahasiswa Pemohon', 171, $labelTtd - 89, 11, true);
        $this->teksTengah($nama, 171, $labelTtd - 105, 11);
        $this->teksTengah('NIM. '.$nim, 171, $labelTtd - 121, 10.5);

        $this->garisPutus(374, $labelTtd - 60, 510, $labelTtd - 60);
        $this->teksTengah('(                                  )', 442, $labelTtd - 76, 10.5);
        $this->teksTengah('NIM. ..............................', 442, $labelTtd - 93, 10.5);

        $this->tabelKeluarKembali();

        return $this->rakitPdf();
    }

    private function kopSurat(): void
    {
        $this->teksTengah('KEMENTERIAN AGAMA REPUBLIK INDONESIA', 297.64, 806, 11, true);
        $this->teksTengah('UNIVERSITAS ISLAM NEGERI IMAM BONJOL PADANG', 297.64, 788, 11, true);
        $this->teksTengah("MA'HAD AL-JAMI'AH TUANKU IMAM BONJOL", 297.64, 770, 12, true);
        $this->teksTengah('Kampus III Sungai Bangek, Balai Gadang, Koto Tangah, Padang, Sumatera Barat', 297.64, 754, 8.5);
        $this->garis(self::LEFT, 738, self::RIGHT, 738, 1.4);
        $this->garis(self::LEFT, 734, self::RIGHT, 734, 0.5);
    }

    private function barisIdentitas(string $label, string $value): void
    {
        $lines = $this->bungkus($value, 322, 10.5);
        $this->teks($label, self::LEFT, $this->y, 10.5);
        $this->teks(':', 207, $this->y, 10.5);

        foreach ($lines as $index => $line) {
            $this->teks($line, 220, $this->y - ($index * 14), 10.5);
        }

        $this->y -= max(21, count($lines) * 14 + 5);
    }

    private function paragraf(string $text, float $size, float $leading, float $indent): void
    {
        $width = self::RIGHT - self::LEFT;
        $lines = $this->bungkus($text, $width - $indent, $size);

        if (count($lines) > 1) {
            $first = array_shift($lines);
            $lines = array_merge([$first], $this->bungkus(implode(' ', $lines), $width, $size));
        }

        $last = count($lines) - 1;
        foreach ($lines as $index => $line) {
            $available = $index === 0 ? $width - $indent : $width;
            $wordSpacing = 0.0;

            if ($index < $last && substr_count($line, ' ') > 0) {
                $wordSpacing = max(0.0, ($available - $this->perkiraanLebar($line, $size)) / substr_count($line, ' '));
            }

            $this->teks(
                $line,
                self::LEFT + ($index === 0 ? $indent : 0),
                $this->y,
                $size,
                wordSpacing: $wordSpacing,
            );
            $this->y -= $leading;
        }
    }

    private function tabelKeluarKembali(): void
    {
        $top = 112.0;
        $height = 30.0;
        $widths = [92.0, 55.0, 92.0, 42.0, 62.0, 38.0, 118.0];
        $labels = ['Keluar Ma’had', 'Hari/Tgl', '', 'Pukul', '', 'WIB', 'Paraf'];
        $secondLabels = ['Kembali ke Ma’had', 'Hari/Tgl', '', 'Pukul', '', 'WIB', 'Paraf'];
        $x = self::LEFT;

        foreach ($widths as $index => $columnWidth) {
            $this->garis($x, $top, $x, $top - ($height * 2), 0.65);
            $this->teks($labels[$index], $x + 4, $top - 12, 8.5, $index === 0);
            $this->teks($secondLabels[$index], $x + 4, $top - $height - 12, 8.5, $index === 0);

            if (in_array($index, [2, 4, 6], true)) {
                $this->garisPutus($x + 5, $top - 23, $x + $columnWidth - 5, $top - 23);
                $this->garisPutus($x + 5, $top - $height - 23, $x + $columnWidth - 5, $top - $height - 23);
            }

            $x += $columnWidth;
        }

        $this->garis($x, $top, $x, $top - ($height * 2), 0.65);
        $this->garis(self::LEFT, $top, self::RIGHT, $top, 0.65);
        $this->garis(self::LEFT, $top - $height, self::RIGHT, $top - $height, 0.65);
        $this->garis(self::LEFT, $top - ($height * 2), self::RIGHT, $top - ($height * 2), 0.65);
    }

    /** @return list<string> */
    private function bungkus(string $text, float $maxWidth, float $size): array
    {
        $result = [];
        $line = '';

        foreach (preg_split('/\s+/u', trim($text)) ?: [] as $word) {
            if ($word === '') {
                continue;
            }

            $next = $line === '' ? $word : $line.' '.$word;
            if ($line !== '' && $this->perkiraanLebar($next, $size) > $maxWidth) {
                $result[] = $line;
                $line = $word;
            } else {
                $line = $next;
            }
        }

        if ($line !== '') {
            $result[] = $line;
        }

        return $result ?: ['-'];
    }

    private function perkiraanLebar(string $text, float $size): float
    {
        $width = 0.0;

        foreach (preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $character) {
            $width += match (true) {
                $character === ' ' => 0.25,
                str_contains("ilI.,:;!'|", $character) => 0.27,
                str_contains('MW@%w', $character) => 0.81,
                preg_match('/[A-Z]/', $character) === 1 => 0.67,
                default => 0.51,
            };
        }

        return $width * $size;
    }

    private function teks(string $value, float $x, float $y, float $size = 11, bool $bold = false, float $wordSpacing = 0.0): void
    {
        $font = $bold ? 'F2' : 'F1';
        $this->content .= sprintf(
            "BT /%s %.2F Tf %.2F Tw 0 g 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n",
            $font,
            $size,
            $wordSpacing,
            $x,
            $y,
            $this->pdfString($value),
        );
    }

    private function teksTengah(string $value, float $centerX, float $y, float $size, bool $bold = false): void
    {
        $width = $this->perkiraanLebar($value, $size) * ($bold ? 1.035 : 1.0);
        $this->teks($value, $centerX - ($width / 2), $y, $size, $bold);
    }

    private function teksKanan(string $value, float $rightX, float $y, float $size): void
    {
        $this->teks($value, $rightX - $this->perkiraanLebar($value, $size), $y, $size);
    }

    private function garis(float $x1, float $y1, float $x2, float $y2, float $width): void
    {
        $this->content .= sprintf('0 G %.2F w %.2F %.2F m %.2F %.2F l S', $width, $x1, $y1, $x2, $y2)."\n";
    }

    private function garisPutus(float $x1, float $y1, float $x2, float $y2): void
    {
        $this->content .= sprintf('0.55 G [3 3] 0 d %.2F %.2F m %.2F %.2F l S [] 0 d', $x1, $y1, $x2, $y2)."\n";
    }

    private function nilaiPertama(?string ...$values): string
    {
        foreach ($values as $value) {
            $text = trim((string) $value);

            if ($text !== '') {
                return $text;
            }
        }

        return '';
    }

    private function nilaiTeks(?string $value): string
    {
        return filled($value) ? trim($value) : '-';
    }

    private function tanggalTeks(mixed $tanggal): string
    {
        if ($tanggal === null) {
            return '-';
        }

        return Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y');
    }

    private function pdfString(string $text): string
    {
        $text = str_replace(['’', '‘', '“', '”', '–', '—', "\r", "\n"], ["'", "'", '"', '"', '-', '-', ' ', ' '], $text);
        $encoded = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        $text = $encoded !== false ? $encoded : $text;
        $text = preg_replace('/[\x00-\x1F]/', ' ', $text) ?? $text;

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function rakitPdf(): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Times-Roman /Encoding /WinAnsiEncoding >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Times-Bold /Encoding /WinAnsiEncoding >>',
            5 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents 6 0 R >>',
            6 => '<< /Length '.strlen($this->content).">>\nstream\n".$this->content.'endstream',
            2 => '<< /Type /Pages /Kids [5 0 R] /Count 1 >>',
        ];

        ksort($objects);
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $positions = [];

        for ($id = 1; $id <= 6; $id++) {
            $positions[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$objects[$id]."\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 7\n0000000000 65535 f \n";

        foreach ($positions as $position) {
            $pdf .= sprintf("%010d 00000 n \n", $position);
        }

        return $pdf."trailer\n<< /Size 7 /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";
    }
}
