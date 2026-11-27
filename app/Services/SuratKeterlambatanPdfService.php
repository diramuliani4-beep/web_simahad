<?php

namespace App\Services;

use App\Models\PengajuanKeterlambatanPembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Surat pernyataan pembayaran Ma'had dalam PDF A4 potret.
 * Tidak mengubah LaporanPdfService yang dipakai oleh laporan lain.
 * Tanpa package PDF tambahan; mengambil public/images/logo_uin.png (GD diperlukan untuk PNG).
 */
class SuratKeterlambatanPdfService
{
    private const PAGE_W = 595.28;

    private const PAGE_H = 841.89;

    private const LEFT = 48.0;

    private const RIGHT = 547.0;

    private const PDF_LAYOUT_VERSION = 5;

    /** @var list<string> */
    private array $pages = [];

    private string $current = '';

    private float $y = 0;

    private ?string $logoJpeg = null;

    private int $logoWidth = 0;

    private int $logoHeight = 0;

    public function pdfSistem(PengajuanKeterlambatanPembayaran $pengajuan): string
    {
        if ($this->pdfSistemValid($pengajuan)) {
            return (string) Storage::disk('local')->get($pengajuan->pdf_sistem);
        }

        return DB::transaction(function () use ($pengajuan): string {
            $pengajuan = PengajuanKeterlambatanPembayaran::query()
                ->whereKey($pengajuan->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->pdfSistemValid($pengajuan)) {
                return (string) Storage::disk('local')->get($pengajuan->pdf_sistem);
            }

            $pdf = $this->render($pengajuan);
            $path = 'keterlambatan/surat-asli/v'.self::PDF_LAYOUT_VERSION.'/'
                .$pengajuan->id.'-'.Str::uuid().'.pdf';

            if (! Storage::disk('local')->put($path, $pdf)) {
                throw new RuntimeException('Gagal menyimpan PDF asli surat keterlambatan.');
            }

            try {
                $pengajuan->update(['pdf_sistem' => $path]);
            } catch (Throwable $exception) {
                Storage::disk('local')->delete($path);

                throw $exception;
            }

            return $pdf;
        });
    }

    private function pdfSistemValid(PengajuanKeterlambatanPembayaran $pengajuan): bool
    {
        return filled($pengajuan->pdf_sistem)
            && str_starts_with((string) $pengajuan->pdf_sistem, 'keterlambatan/surat-asli/v'.self::PDF_LAYOUT_VERSION.'/')
            && Storage::disk('local')->exists($pengajuan->pdf_sistem);
    }

    public function render(PengajuanKeterlambatanPembayaran $pengajuan): string
    {
        $pengajuan->loadMissing(['user', 'pembayaran']);
        $mahasiswa = $pengajuan->user;
        abort_unless($mahasiswa, 404, 'Data mahasiswa untuk surat tidak ditemukan.');

        $pendaftaran = $mahasiswa->pendaftarans()
            ->where('status', 'disetujui')
            ->orderByDesc('divalidasi_pada')
            ->orderByDesc('id')
            ->first();

        $nama = $this->nilaiPertama($pendaftaran?->nama_lengkap, $mahasiswa->name);
        $nim = $this->nilaiPertama($pendaftaran?->nim, $mahasiswa->nim);
        $prodi = $this->nilaiPertama($pendaftaran?->program_studi);
        $fakultas = $this->nilaiPertama($pendaftaran?->fakultas);
        $telepon = $this->nilaiPertama($pendaftaran?->no_hp, $pendaftaran?->no_wa, $mahasiswa->no_hp);

        $dataKurang = array_values(array_filter([
            $nama === '' ? 'nama lengkap' : null,
            $nim === '' ? 'NIM' : null,
            $prodi === '' ? 'program studi' : null,
            $fakultas === '' ? 'fakultas' : null,
            $telepon === '' ? 'nomor HP' : null,
        ]));

        abort_if(
            $dataKurang !== [],
            422,
            'Data mahasiswa untuk surat belum lengkap: '.implode(', ', $dataKurang).
                '. Perbarui Data Mahasiswa atau hubungi Musyrifah sebelum mencetak surat.'
        );

        // Tanggal surat = hari mahasiswa MENGAJUKAN keterlambatan.
        // Bukan tanggal validasi pendaftaran atau tanggal PDF dibuka.
        $tanggalSurat = $pengajuan->created_at
            ? $pengajuan->created_at->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d-m-Y')
            : now('Asia/Jakarta')->locale('id')->translatedFormat('d-m-Y');

        $tanggalPelunasan = $pengajuan->tanggalRencanaTeks();
        $alasan = trim((string) $pengajuan->alasan);
        $alasan = preg_replace('/\s+/u', ' ', $alasan) ?: '-';

        $this->pages = [];
        $this->current = '';
        $this->muatLogo();
        $this->halamanBaru();
        $this->kopSurat();

        $this->teksKanan('Padang, '.$tanggalSurat, self::RIGHT, 697, 11);
        $this->teksTengah('SURAT PERNYATAAN PEMBAYARAN MA\'HAD', 297.64, 656, 12, true);
        $this->teks('Saya yang bertanda tangan di bawah ini:', self::LEFT, 618, 11);

        $this->y = 591;
        $this->barisIdentitas('Nama Lengkap', $nama);
        $this->barisIdentitas('NIM', $nim);
        $this->barisIdentitas('Program Studi', $prodi);
        $this->barisIdentitas('Fakultas', $fakultas);
        $this->barisIdentitas('Nomor HP', $telepon);

        $this->y -= 29;
        $pernyataan = 'Dengan ini saya menyatakan bersedia melunasi tunggakan biaya sewa gedung dan '
            ."pembinaan Ma'had Al-Jami'ah Tuanku Imam Bonjol paling lambat pada "
            .$tanggalPelunasan.', dikarenakan '.$alasan;
        $this->paragraf($pernyataan, self::LEFT, 11.5, 18.5, 24);

        if ($this->y < 151) {
            $this->halamanBaru();
            $this->teks('SURAT PERNYATAAN PEMBAYARAN MA\'HAD (LANJUTAN)', self::LEFT, 783, 11, true);
            $this->y = 745;
        }

        $this->pastikanRuang(280);
        $this->blokTandaTangan($nama);

        $this->tutupHalaman();

        return $this->rakitPdf();
    }

    private function kopSurat(): void
    {
        if ($this->logoJpeg !== null) {
            $this->current .= "q 62 0 0 62 53 754 cm /Im1 Do Q\n";
        }

        $center = 343.0;
        $this->teksTengah('KEMENTERIAN AGAMA REPUBLIK INDONESIA', $center, 809, 10.5, true);
        $this->teksTengah('UNIVERSITAS ISLAM NEGERI IMAM BONJOL PADANG', $center, 792, 11.2, true);
        $this->teksTengah("MA'HAD AL-JAMI'AH NEGERI IMAM BONJOL PADANG", $center, 775, 11.2, true);
        $this->teksTengah('Alamat: Kampus III Sungai Bangek, Kel. Balai Gadang, Kec. Koto Tangah, Padang', $center, 760, 7.4);
        $this->teksTengah('Kode Pos: 25171 | Website: https://mahad-thib.uinib.ac.id | E-mail: mahad-thib@uinib.ac.id', $center, 749, 7.1);
        $this->garis(self::LEFT, 735, self::RIGHT, 735, 1.6);
        $this->garis(self::LEFT, 732, self::RIGHT, 732, 0.55);
    }

    private function barisIdentitas(string $label, string $value): void
    {
        $this->pastikanRuang(36);
        $this->teks($label, 69, $this->y, 11);
        $this->teks(':', 185, $this->y, 11);
        $lines = $this->bungkus($value, 330, 11);
        foreach ($lines as $i => $line) {
            $this->teks($line, 199, $this->y - ($i * 15), 11);
        }
        $this->y -= max(24, count($lines) * 15 + 5);
    }

    private function paragraf(string $paragraph, float $x, float $size, float $leading, float $indent = 0): void
    {
        $first = true;
        $available = self::RIGHT - $x;
        $lines = $this->bungkus($paragraph, $available - $indent, $size);
        // Bungkus ulang setelah baris pertama, sehingga lebar baris lanjutan normal.
        if (count($lines) > 1) {
            $firstLine = array_shift($lines);
            $rest = trim(implode(' ', $lines));
            $lines = array_merge([$firstLine], $this->bungkus($rest, $available, $size));
        }
        $lastIndex = count($lines) - 1;
        foreach ($lines as $index => $line) {
            $this->pastikanRuang(19);
            $lineWidth = $first ? $available - $indent : $available;
            $wordSpacing = 0.0;
            $spaceCount = substr_count($line, ' ');

            if ($index < $lastIndex && $spaceCount > 0) {
                $wordSpacing = max(0.0, ($lineWidth - $this->perkiraanLebar($line, $size)) / $spaceCount);
            }

            $this->teks($line, $x + ($first ? $indent : 0), $this->y, $size, wordSpacing: $wordSpacing);
            $this->y -= $leading;
            $first = false;
        }
    }

    private function blokTandaTangan(string $nama): void
    {
        $blockLeft = self::RIGHT - 222;
        $blockRight = self::RIGHT - 12;
        $blockCenter = ($blockLeft + $blockRight) / 2;
        $labelY = min(300.0, $this->y - 60);
        $signatureY = $labelY - 42;
        $this->teksTengah('Yang menyatakan,', $blockCenter, $labelY, 11);

        $materaiWidth = 80;
        $materaiHeight = 55;
        $materaiX = $blockLeft;
        $materaiY = $signatureY - ($materaiHeight / 2);
        $this->kotakPutus($materaiX, $materaiY, $materaiWidth, $materaiHeight);
        $this->teksTengah('Tempat Materai', $materaiX + ($materaiWidth / 2), $materaiY + ($materaiHeight / 2) - 3, 8.5);

        $signatureLeft = $materaiX + $materaiWidth - 8;
        $signatureRight = $blockRight;
        $signatureCenter = ($signatureLeft + $signatureRight) / 2;
        $this->garisPutus($signatureLeft, $signatureY, $signatureRight, $signatureY);

        $barisNama = $this->bungkus($nama, 150, 11);
        foreach ($barisNama as $index => $baris) {
            $this->teksTengah($baris, $signatureCenter, $signatureY - 26 - ($index * 14), 11);
        }
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
            $next = ($line === '') ? $word : $line.' '.$word;
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
        // Perkiraan konservatif Times-Roman untuk pembungkusan (tanpa memotong data).
        $w = 0.0;
        foreach (preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $char) {
            $w += match (true) {
                $char === ' ' => 0.25,
                str_contains('ilI.,:;!\'|', $char) => 0.27,
                str_contains('MW@%w', $char) => 0.81,
                preg_match('/[A-Z]/', $char) === 1 => 0.67,
                default => 0.51,
            };
        }

        return $w * $size;
    }

    private function halamanBaru(): void
    {
        $this->tutupHalaman();
        $this->current = '';
        $this->y = 790;
    }

    private function tutupHalaman(): void
    {
        if ($this->current !== '') {
            $this->pages[] = $this->current;
            $this->current = '';
        }
    }

    private function pastikanRuang(float $height): void
    {
        if ($this->y - $height < 72) {
            $this->halamanBaru();
            $this->teks('SURAT PERNYATAAN PEMBAYARAN MA\'HAD (LANJUTAN)', self::LEFT, 795, 10, true);
            $this->y = 766;
        }
    }

    private function teks(string $value, float $x, float $y, float $size = 11, bool $bold = false, float $wordSpacing = 0.0): void
    {
        $value = $this->pdfString($value);
        $font = $bold ? 'F2' : 'F1';
        $this->current .= sprintf("BT /%s %.2F Tf %.2F Tw 0 g 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n", $font, $size, $wordSpacing, $x, $y, $value);
    }

    private function teksTengah(string $value, float $centerX, float $y, float $size, bool $bold = false): void
    {
        $width = $this->perkiraanLebar($value, $size) * ($bold ? 1.035 : 1.0);
        $this->teks($value, $centerX - $width / 2, $y, $size, $bold);
    }

    private function teksKanan(string $value, float $rightX, float $y, float $size): void
    {
        $this->teks($value, $rightX - $this->perkiraanLebar($value, $size), $y, $size);
    }

    private function garis(float $x1, float $y1, float $x2, float $y2, float $width): void
    {
        $this->current .= sprintf("0 G %.2F w %.2F %.2F m %.2F %.2F l S\n", $width, $x1, $y1, $x2, $y2);
    }

    private function garisPutus(float $x1, float $y1, float $x2, float $y2): void
    {
        $this->current .= sprintf("0.55 G [3 3] 0 d %.2F %.2F m %.2F %.2F l S [] 0 d\n", $x1, $y1, $x2, $y2);
    }

    private function kotakPutus(float $x, float $y, float $width, float $height): void
    {
        $this->current .= sprintf(
            "0.55 G [3 3] 0 d %.2F %.2F %.2F %.2F re S [] 0 d\n",
            $x,
            $y,
            $width,
            $height
        );
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

    private function pdfString(string $text): string
    {
        $text = str_replace(['’', '‘', '“', '”', '–', '—', "\r", "\n"], ["'", "'", '"', '"', '-', '-', ' ', ' '], $text);
        $encoded = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        $text = $encoded !== false ? $encoded : $text;
        $text = preg_replace('/[\x00-\x1F]/', ' ', $text) ?? $text;

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function muatLogo(): void
    {
        // Gunakan logo yang SUDAH ada di proyek SIMAHAD.
        $file = public_path('images/logo_uin.png');
        if (! is_file($file)) {
            throw new RuntimeException('Logo tidak ditemukan: public/images/logo_uin.png');
        }
        $info = @getimagesize($file);
        if ($info === false) {
            throw new RuntimeException('Berkas logo tidak dapat dibaca sebagai gambar.');
        }
        $this->logoWidth = $info[0];
        $this->logoHeight = $info[1];
        if ($info[2] === IMAGETYPE_JPEG && (($info['channels'] ?? 3) === 3)) {
            $this->logoJpeg = file_get_contents($file) ?: null;
        } else {
            if (! extension_loaded('gd')) {
                throw new RuntimeException('Untuk memakai public/images/logo_uin.png, aktifkan ekstensi PHP GD di Laragon.');
            }
            $source = match ($info[2]) {
                IMAGETYPE_PNG => imagecreatefrompng($file),
                IMAGETYPE_JPEG => imagecreatefromjpeg($file),
                default => false,
            };
            if ($source === false) {
                throw new RuntimeException('Format logo hanya mendukung JPG dan PNG.');
            }
            $rgb = imagecreatetruecolor($this->logoWidth, $this->logoHeight);
            // Satukan transparansi PNG ke latar putih sebelum dijadikan JPEG PDF.
            imagealphablending($rgb, true);
            $white = imagecolorallocate($rgb, 255, 255, 255);
            imagefilledrectangle($rgb, 0, 0, $this->logoWidth, $this->logoHeight, $white);
            imagecopy($rgb, $source, 0, 0, 0, 0, $this->logoWidth, $this->logoHeight);
            ob_start();
            imagejpeg($rgb, null, 92);
            $this->logoJpeg = ob_get_clean() ?: null;
            imagedestroy($rgb);
            imagedestroy($source);
        }
        if ($this->logoJpeg === null) {
            throw new RuntimeException('Gagal mempersiapkan gambar logo untuk PDF.');
        }
    }

    private function rakitPdf(): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Times-Roman /Encoding /WinAnsiEncoding >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Times-Bold /Encoding /WinAnsiEncoding >>',
        ];
        $nextId = 5;
        $logoId = null;
        if ($this->logoJpeg !== null) {
            $logoId = $nextId++;
            $objects[$logoId] = sprintf(
                "<< /Type /XObject /Subtype /Image /Width %d /Height %d /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length %d >>\nstream\n",
                $this->logoWidth, $this->logoHeight, strlen($this->logoJpeg)
            ).$this->logoJpeg."\nendstream";
        }
        $pageIds = [];
        foreach ($this->pages as $stream) {
            $pageId = $nextId++;
            $streamId = $nextId++;
            $pageIds[] = $pageId.' 0 R';
            $xobject = $logoId !== null ? ' /XObject << /Im1 '.$logoId.' 0 R >>' : '';
            $objects[$pageId] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >>%s >> /Contents %d 0 R >>',
                self::PAGE_W, self::PAGE_H, $xobject, $streamId
            );
            $objects[$streamId] = '<< /Length '.strlen($stream).">>\nstream\n".$stream.'endstream';
        }
        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $pageIds).'] /Count '.count($this->pages).' >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $positions = [];
        $maxId = max(array_keys($objects));
        for ($id = 1; $id <= $maxId; $id++) {
            $positions[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".($objects[$id] ?? '<< >>')."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".($maxId + 1)."\n0000000000 65535 f \n";
        for ($id = 1; $id <= $maxId; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $positions[$id]);
        }
        $pdf .= "trailer\n<< /Size ".($maxId + 1)." /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";

        return $pdf;
    }
}
