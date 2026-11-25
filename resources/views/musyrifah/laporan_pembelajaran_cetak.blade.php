<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Pembelajaran {{ $mataPelajaran->nama }} - {{ $periodeLabel }}</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px;
            background: #f3eff1;
            color: #111;
            font-family: "Times New Roman", Times, serif;
            -webkit-font-smoothing: antialiased;
        }

        .cetak-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            width: min(210mm, 100%);
            margin: 0 auto 14px;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .cetak-toolbar-label { color: #6f6269; font-size: 12px; }
        .cetak-toolbar-aksi { display: flex; flex-wrap: wrap; gap: 8px; }

        .cetak-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 9px 15px;
            border: 1px solid #800b22;
            border-radius: 8px;
            background: #800b22;
            color: #fff;
            font: 700 12px/1.3 "Segoe UI", Arial, sans-serif;
            text-decoration: none;
            cursor: pointer;
        }

        .cetak-btn--garis { background: #fff; color: #800b22; }
        .cetak-btn:focus-visible { outline: 2px solid #a44c65; outline-offset: 2px; }

        .cetak-kertas {
            width: min(210mm, 100%);
            min-height: 297mm;
            margin: 0 auto;
            padding: 16mm 15mm;
            background: #fff;
            box-shadow: 0 5px 24px rgba(44, 17, 27, .09);
        }

        .cetak-kop {
            display: grid;
            grid-template-columns: 39mm minmax(0, 1fr);
            align-items: center;
            gap: 3mm;
            padding-bottom: 3mm;
            border-bottom: 2px solid #000;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .cetak-logo { text-align: center; font-family: Arial, Helvetica, sans-serif; }
        .cetak-logo img { display: block; width: 35mm; height: 35mm; margin: 0 auto; object-fit: contain; }
        .cetak-logo-nama { display: block; margin-top: 1mm; font-size: 7.6pt; font-weight: 700; line-height: 1.05; }

        .cetak-identitas { text-align: center; font-family: Arial, Helvetica, sans-serif; line-height: 1.12; }
        .cetak-identitas-besar { margin: 0; font-size: 18pt; font-weight: 400; white-space: nowrap; }
        .cetak-identitas-lembaga { margin: 1.5mm 0 0; font-size: 16pt; font-weight: 700; }
        .cetak-alamat { margin: 1.5mm 0 0; font-size: 8.5pt; line-height: 1.2; }

        .cetak-judul { margin: 7mm 0 1.5mm; font-size: 14pt; font-weight: 700; line-height: 1.25; text-align: center; text-transform: uppercase; }
        .cetak-subjudul { margin: 0 0 5mm; font-size: 12pt; font-weight: 700; line-height: 1.3; text-align: center; }

        .cetak-meta {
            display: grid;
            grid-template-columns: 31mm minmax(0, 1fr) 27mm minmax(0, 1fr);
            gap: 1.7mm 3mm;
            margin: 0 0 5mm;
            font-size: 10.5pt;
            line-height: 1.3;
        }

        .cetak-meta dt { font-weight: 700; }
        .cetak-meta dd { min-width: 0; margin: 0; overflow-wrap: anywhere; }
        .cetak-meta .cetak-meta-lebar { grid-column: 2 / -1; }

        .cetak-tabel-bungkus { width: 100%; overflow-x: auto; }
        .cetak-tabel { width: 100%; min-width: 650px; border-collapse: collapse; table-layout: fixed; font-size: 10pt; }
        .cetak-tabel thead { display: table-header-group; }
        .cetak-tabel tr { break-inside: avoid; page-break-inside: avoid; }
        .cetak-tabel th, .cetak-tabel td { padding: 2.2mm; border: 1px solid #000; vertical-align: middle; overflow-wrap: anywhere; }
        .cetak-tabel th { background: #f2f2f2; font-weight: 700; text-align: center; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .cetak-tabel .cetak-kolom-no { width: 10mm; text-align: center; }
        .cetak-tabel .cetak-kolom-tanggal { width: 42mm; text-align: center; }
        .cetak-tabel .cetak-kolom-ttd { width: 34mm; text-align: center; }
        .cetak-hari { display: block; font-weight: 700; }
        .cetak-pertemuan { display: block; margin-top: 1mm; font-size: 8.5pt; line-height: 1.2; }
        .cetak-materi { margin: 0; white-space: pre-line; }
        .cetak-inisial { font-size: 11pt; font-style: italic; font-weight: 700; }
        .cetak-ruang-ttd { display: block; width: 100%; height: 11mm; }
        .cetak-kosong { padding: 8mm; text-align: center; }

        @page { size: A4 portrait; margin: 14mm 15mm; }

        @media print {
            body { padding: 0; background: #fff; }
            .cetak-toolbar { display: none !important; }
            .cetak-kertas { width: 100%; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
            .cetak-tabel-bungkus { overflow: visible; }
            .cetak-tabel { min-width: 0; }
        }

        @media screen and (max-width: 760px) {
            body { padding: 0; }
            .cetak-toolbar { width: 100%; padding: 12px; }
            .cetak-toolbar-label { display: none; }
            .cetak-toolbar-aksi { width: 100%; }
            .cetak-btn { flex: 1; }
            .cetak-kertas { width: 100%; min-height: 0; padding: 18px 12px; box-shadow: none; }
            .cetak-kop { grid-template-columns: 27mm minmax(0, 1fr); gap: 2mm; }
            .cetak-logo img { width: 25mm; height: 25mm; }
            .cetak-logo-nama { font-size: 6.5pt; }
            .cetak-identitas-besar { font-size: 10.5pt; white-space: normal; }
            .cetak-identitas-lembaga { font-size: 10pt; }
            .cetak-alamat { font-size: 7pt; }
            .cetak-meta { grid-template-columns: 28mm minmax(0, 1fr); }
            .cetak-meta .cetak-meta-lebar { grid-column: 2; }
        }
    </style>
</head>
<body>
    <div class="cetak-toolbar">
        <span class="cetak-toolbar-label">{{ $mataPelajaran->nama }} · {{ $periodeLabel }}</span>
        <div class="cetak-toolbar-aksi">
            <button type="button" class="cetak-btn" onclick="window.print()">Cetak Dokumen</button>
            <a href="{{ $backUrl }}" class="cetak-btn cetak-btn--garis">Kembali</a>
        </div>
    </div>

    <main class="cetak-kertas">
        <header class="cetak-kop">
            <div class="cetak-logo">
                <img src="{{ $pengaturan->urlLogo() }}" alt="Logo {{ $pengaturan->nama_institusi }}">
                <span class="cetak-logo-nama">UIN IMAM BONJOL<br>PADANG</span>
            </div>
            <div class="cetak-identitas">
                <p class="cetak-identitas-besar">KEMENTERIAN AGAMA REPUBLIK INDONESIA</p>
                <p class="cetak-identitas-besar">UNIVERSITAS ISLAM NEGERI IMAM BONJOL PADANG</p>
                <p class="cetak-identitas-lembaga">{{ mb_strtoupper($pengaturan->nama_institusi ?: 'Ma’had Al-Jami’ah Tuanku Imam Bonjol') }}</p>
                <p class="cetak-alamat">Alamat: Kampus III Sungai Bangek Kel. Balai Gadang Kec. Koto Tangah Padang</p>
                <p class="cetak-alamat">Kode Pos: 25171 &nbsp; Website: https://mahad-thib.uinib.ac.id</p>
                <p class="cetak-alamat">e-mail: ma'had_thib@uinib.ac.id</p>
            </div>
        </header>

        <h1 class="cetak-judul">Laporan Pembelajaran</h1>
        <p class="cetak-subjudul">{{ $mataPelajaran->nama }}</p>

        <dl class="cetak-meta">
            <dt>Program</dt>
            <dd>{{ $program['nama'] }}</dd>
            <dt>Kelompok</dt>
            <dd>{{ $program['kelompok'] }}</dd>

            <dt>Waktu</dt>
            <dd>{{ $program['waktu'] }}</dd>
            <dt>Periode</dt>
            <dd>{{ $periodeLabel }}</dd>

            <dt>Instruktur</dt>
            <dd class="cetak-meta-lebar">{{ $program['instruktur'] }}</dd>
        </dl>

        <div class="cetak-tabel-bungkus">
            <table class="cetak-tabel">
                <thead>
                    <tr>
                        <th scope="col" class="cetak-kolom-no">No</th>
                        <th scope="col" class="cetak-kolom-tanggal">Hari/Tanggal</th>
                        <th scope="col">Materi</th>
                        <th scope="col" class="cetak-kolom-ttd">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barisLaporan as $index => $item)
                        <tr>
                            <td class="cetak-kolom-no">{{ $index + 1 }}</td>
                            <td class="cetak-kolom-tanggal">
                                <span class="cetak-hari">{{ $item['hari'] }}</span>
                                {{ $item['tanggal'] }}
                                @if ($item['pertemuan_ke'])
                                    <span class="cetak-pertemuan">Pertemuan {{ $item['pertemuan_ke'] }} · {{ $item['kelompok'] }}</span>
                                @endif
                            </td>
                            <td><p class="cetak-materi">{{ $item['materi'] }}</p></td>
                            <td class="cetak-kolom-ttd">
                                @if (filled($item['tanda_tangan']))
                                    <span class="cetak-inisial">{{ $item['tanda_tangan'] }}</span>
                                @else
                                    <span class="cetak-ruang-ttd" aria-label="Ruang tanda tangan"></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="cetak-kosong">Belum ada data laporan untuk mata pelajaran dan periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.tanda-tangan-pimpinan', ['pimpinan' => $pimpinan])
    </main>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
