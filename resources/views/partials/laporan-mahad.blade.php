@php

    /** @var array<string, mixed> $laporan */

    $modeMonitoring = $modeMonitoring ?? false;

    $jumlahKolom = count($laporan['kolom']);

@endphp



<style>

    .lap-page {

        font-family: 'Poppins', sans-serif;

        color: #29252a;

        padding: 8px 0 28px;

    }



    .lap-head {

        display: flex;

        justify-content: space-between;

        align-items: flex-start;

        flex-wrap: wrap;

        gap: 16px;

        margin-bottom: 22px;

    }



    .lap-back {

        display: inline-flex;

        align-items: center;

        gap: 6px;

        margin-bottom: 10px;

        color: #780d20;

        font-size: 13px;

        font-weight: 600;

        text-decoration: none;

    }



    .lap-back:hover {

        text-decoration: underline;

    }



    .lap-head h1 {

        margin: 0 0 7px;

        font-size: 26px;

        font-weight: 700;

        color: #780d20;

    }



    .lap-head p {

        margin: 0;

        color: #81777d;

        font-size: 13px;

        line-height: 1.7;

    }



    .lap-head-meta {

        display: flex;

        align-items: center;

        gap: 10px;

        flex-wrap: wrap;

    }



    .lap-monitor {

        display: inline-flex;

        align-items: center;

        gap: 6px;

        padding: 8px 12px;

        border: 1px solid #e3cfd4;

        border-radius: 20px;

        background: #fff6f8;

        color: #780d20;

        font-size: 12px;

        font-weight: 600;

    }



    .lap-date {

        padding: 10px 14px;

        border: 1px solid #eadde0;

        border-radius: 10px;

        background: #fff;

        color: #780d20;

        font-size: 13px;

        font-weight: 600;

        white-space: nowrap;

    }



    .lap-filter {

        display: flex;

        align-items: flex-end;

        flex-wrap: wrap;

        gap: 14px;

        padding: 18px;

        margin-bottom: 22px;

        border: 1px solid #eee7e9;

        border-radius: 14px;

        background: #fff;

        box-shadow: 0 4px 15px rgba(50, 20, 30, .04);

    }



    .lap-field {

        display: flex;

        flex-direction: column;

        gap: 7px;

        min-width: 190px;

    }



    .lap-field.lap-field-search {

        flex: 1;

        min-width: 240px;

    }



    .lap-field label {

        color: #81777d;

        font-size: 12px;

        font-weight: 600;

    }



    .lap-field select,

    .lap-field input {

        width: 100%;

        box-sizing: border-box;

        padding: 10px 12px;

        border: 1px solid #e8dfe2;

        border-radius: 9px;

        background: #fff;

        color: #30272b;

        font-family: inherit;

        font-size: 13px;

        outline-color: #780d20;

    }



    .lap-btn {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 8px;

        padding: 11px 18px;

        border: 1px solid #780d20;

        border-radius: 9px;

        color: #fff;

        background: #780d20;

        font-family: inherit;

        font-size: 13px;

        font-weight: 600;

        text-decoration: none;

        cursor: pointer;

        transition: .2s;

    }



    .lap-btn:hover {

        background: #650919;

        border-color: #650919;

    }



    .lap-btn.lap-btn-outline {

        color: #780d20;

        background: #fff;

    }



    .lap-btn.lap-btn-outline:hover {

        color: #fff;

        background: #780d20;

    }



    .lap-summary {

        display: grid;

        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));

        gap: 16px;

        margin-bottom: 22px;

    }



    .lap-summary-card {

        padding: 18px;

        border: 1px solid #eee7e9;

        border-radius: 14px;

        background: #fff;

        box-shadow: 0 4px 15px rgba(50, 20, 30, .04);

    }



    .lap-summary-card span {

        display: block;

        margin-bottom: 6px;

        color: #81777d;

        font-size: 12px;

        font-weight: 500;

    }



    .lap-summary-card strong {

        color: #780d20;

        font-size: 22px;

        font-weight: 700;

    }



    .lap-table-card {

        width: 100%;

        box-sizing: border-box;

        padding: 22px;

        border: 1px solid #eee7e9;

        border-radius: 15px;

        background: #fff;

        box-shadow: 0 4px 15px rgba(50, 20, 30, .04);

    }



    .lap-table-head {

        display: flex;

        justify-content: space-between;

        align-items: center;

        flex-wrap: wrap;

        gap: 12px;

        margin-bottom: 18px;

    }



    .lap-table-head h2 {

        margin: 0;

        color: #30272b;

        font-size: 17px;

        font-weight: 700;

    }



    .lap-actions {

        display: flex;

        align-items: center;

        gap: 10px;

        flex-wrap: wrap;

    }



    .lap-table-wrap {

        width: 100%;

        overflow-x: auto;

    }



    .lap-table {

        width: 100%;

        min-width: 760px;

        border-collapse: collapse;

    }



    .lap-table thead {

        background: #f8f0f2;

    }



    .lap-table th {

        padding: 13px 10px;

        border-bottom: 1px solid #eee1e4;

        color: #780d20;

        text-align: left;

        font-size: 12px;

        font-weight: 700;

        white-space: nowrap;

    }



    .lap-table td {

        padding: 13px 10px;

        border-bottom: 1px solid #f1ebed;

        color: #51474c;

        font-size: 12px;

        white-space: nowrap;

    }



    .lap-table tbody tr:hover {

        background: #fffafb;

    }



    .lap-center {

        text-align: center !important;

    }



    .lap-table td.nim {

        color: #780d20;

        font-weight: 600;

    }



    .lap-badge {

        display: inline-block;

        padding: 5px 10px;

        border-radius: 20px;

        font-size: 11px;

        font-weight: 600;

    }



    .lap-badge-lunas,

    .lap-badge-disetujui,

    .lap-badge-tersedia,

    .lap-badge-aktif {

        background: #eaf5e6;

        color: #4e8c35;

    }



    .lap-badge-menunggu,

    .lap-badge-perbaikan {

        background: #fdf3e2;

        color: #b07a1e;

    }



    .lap-badge-belum,

    .lap-badge-ditolak,

    .lap-badge-penuh {

        background: #fbe9e9;

        color: #c0392b;

    }



    .lap-badge-nonaktif {

        background: #efebed;

        color: #7a6f74;

    }



    .lap-empty {

        padding: 35px !important;

        color: #91858a !important;

        text-align: center;

    }



    .lap-print-head {

        display: none;

    }



    @media (max-width: 700px) {

        .lap-head h1 {

            font-size: 22px;

        }



        .lap-field,

        .lap-field.lap-field-search {

            min-width: 100%;

            width: 100%;

        }



        .lap-filter .lap-btn {

            width: 100%;

        }



        .lap-table-card {

            padding: 12px;

        }

    }



    /* Cetak dokumen resmi. Tidak mengubah antarmuka dan form di layar. */
    @page {
        size: A4 landscape;
        margin: 14mm 14mm 14mm 14mm;
    }

    @media print {
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

        /* Bersihkan kerangka panel (sidebar dan topbar tidak mengambil ruang). */
        .topbar, .sb, .sb-tirai, .sb-tombol,
        .lap-head, .lap-filter, .lap-no-print, .lap-actions {
            display: none !important;
        }

        .main-content, body.sb-sempit .main-content,
        .lap-page {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
            min-height: 0 !important;
        }

        /* Ruang halaman lain tersembunyi; hanya cetak isi laporan. */
        body * { visibility: hidden !important; }
        #lap-area, #lap-area * { visibility: visible !important; }
        #lap-area {
            position: static !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            font-family: "Times New Roman", Times, serif;
            color: #000 !important;
        }

        /* Kop: 3 kolom seimbang agar nama instansi benar-benar di tengah kertas. */
        #lap-area .lap-print-head {
            display: block !important;
            margin: 0 0 11mm !important;
            break-inside: avoid;
            page-break-inside: avoid;
            color: #000 !important;
        }
        #lap-area .lap-kop-resmi {
            display: grid !important;
            grid-template-columns: 27mm minmax(0, 1fr) 27mm;
            align-items: center;
            column-gap: 3mm;
            padding: 0 0 3mm;
            margin: 0 0 5mm;
            border-bottom: 2.5px solid #000;
        }
        #lap-area .lap-kop-logo {
            width: 27mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5mm;
        }
        #lap-area .lap-kop-logo img {
            display: block;
            width: 20mm;
            height: 20mm;
            object-fit: contain;
        }
        #lap-area .lap-kop-logo-nama {
            display: block;
            width: 100%;
            text-align: center;
            font: bold 7pt/1.05 Arial, sans-serif;
            letter-spacing: 0;
            color: #000;
        }
        #lap-area .lap-kop-identitas {
            min-width: 0;
            text-align: center;
            line-height: 1.13;
            overflow-wrap: break-word;
            color: #000;
        }
        #lap-area .lap-kop-atas {
            margin: 0;
            font-size: 13pt;
            font-weight: 700;
        }
        #lap-area .lap-kop-lembaga {
            margin: 1mm 0 1.4mm;
            font-size: 15pt;
            font-weight: 700;
        }
        #lap-area .lap-kop-alamat {
            margin: .5mm 0;
            font-size: 9pt;
            font-weight: 400;
            line-height: 1.15;
        }
        #lap-area .lap-print-judul {
            margin: 0 0 1.2mm;
            font: bold 15pt/1.2 "Times New Roman", Times, serif;
            text-align: center;
            text-transform: uppercase;
            color: #000;
        }
        #lap-area .lap-print-periode,
        #lap-area .lap-print-meta {
            margin: 0;
            font: 10pt/1.3 "Times New Roman", Times, serif;
            text-align: center;
            color: #000;
        }

        /* Ringkasan berbentuk kartu bukan bagian dokumen formal. */
        #lap-area .lap-summary { display: none !important; }
        #lap-area .lap-table-card,
        #lap-area .lap-table-wrap {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: #fff !important;
            overflow: visible !important;
        }
        #lap-area .lap-table-head {
            display: block !important;
            margin: 0 0 3mm !important;
        }
        #lap-area .lap-table-head h2 {
            margin: 0;
            font: bold 11pt/1.3 "Times New Roman", Times, serif;
            color: #000 !important;
        }
        #lap-area .lap-table {
            width: 100% !important;
            min-width: 0 !important;
            border-collapse: collapse;
            table-layout: auto;
            font-family: "Times New Roman", Times, serif;
        }
        #lap-area .lap-table thead { display: table-header-group; }
        #lap-area .lap-table tr { break-inside: avoid; page-break-inside: avoid; }
        #lap-area .lap-table th,
        #lap-area .lap-table td {
            border: 1px solid #222 !important;
            padding: 5px 6px;
            font-size: 9.5pt;
            line-height: 1.2;
            vertical-align: middle;
            white-space: normal !important;
            overflow-wrap: break-word;
            color: #000 !important;
        }
        #lap-area .lap-table th {
            background: #eee !important;
            text-align: center;
            font-weight: 700;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        #lap-area .lap-badge {
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent !important;
            color: #000 !important;
            font: inherit;
        }
    }
</style>



<div class="lap-page">

    <div class="lap-head">

        <div>

            <a href="{{ $backUrl }}" class="lap-back lap-no-print">← Kembali ke daftar laporan</a>

            <h1>{{ $laporan['judul'] }}</h1>

            <p>{{ $laporan['deskripsi'] }}</p>

        </div>



        <div class="lap-head-meta">

            @if ($modeMonitoring)

                <span class="lap-monitor">Mode Monitoring · Hanya Lihat</span>

            @endif

            <span class="lap-date">{{ now()->format('d/m/Y') }}</span>

        </div>

    </div>



    @if (empty($sembunyikanFilter))

    <form action="{{ $filterAction }}" method="GET" class="lap-filter lap-no-print">

        @if (! empty($opsiPeriode))

            <div class="lap-field">

                <label for="lap-periode">Periode laporan</label>

                <select name="periode" id="lap-periode">

                    @foreach ($opsiPeriode as $opsi)

                        <option

                            value="{{ $opsi['value'] }}"

                            @selected(($laporan['periode'] ?? null) === $opsi['value'])

                        >

                            {{ $opsi['label'] }}

                        </option>

                    @endforeach

                </select>

            </div>

        @endif



        @foreach (($filterTambahan ?? []) as $tambahan)

            <div class="lap-field">

                <label for="lap-filter-{{ $tambahan['name'] }}">{{ $tambahan['label'] }}</label>

                <select name="{{ $tambahan['name'] }}" id="lap-filter-{{ $tambahan['name'] }}">

                    <option value="">Semua</option>



                    @foreach ($tambahan['options'] as $opsi)

                        <option

                            value="{{ $opsi['value'] }}"

                            @selected((string) ($tambahan['nilai'] ?? '') === (string) $opsi['value'])

                        >

                            {{ $opsi['label'] }}

                        </option>

                    @endforeach

                </select>

            </div>

        @endforeach



        <div class="lap-field lap-field-search">

            <label for="lap-cari">Pencarian</label>

            <input

                type="text"

                name="cari"

                id="lap-cari"

                value="{{ $keyword }}"

                placeholder="{{ $laporan['placeholder_cari'] }}"

            >

        </div>



        <button type="submit" class="lap-btn">Terapkan</button>



        @if (! empty($resetUrl))

            <a href="{{ $resetUrl }}" class="lap-btn lap-btn-outline">Reset</a>

        @endif

    </form>

    @endif



    <div id="lap-area">

        {{-- Kop surat hanya ditampilkan pada hasil cetak, bukan dashboard. --}}
        <div class="lap-print-head">
            <div class="lap-kop-resmi">
                <div class="lap-kop-logo">
                    <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol Padang">
                    <span class="lap-kop-logo-nama">UIN IMAM BONJOL<br>PADANG</span>
                </div>
                <div class="lap-kop-identitas">
                    <div class="lap-kop-atas">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                    <div class="lap-kop-atas">UNIVERSITAS ISLAM NEGERI IMAM BONJOL PADANG</div>
                    <div class="lap-kop-lembaga">MA'HAD AL-JAMI'AH TUANKU IMAM BONJOL</div>
                    <div class="lap-kop-alamat">Alamat: Kampus III Sungai Bangek Kel. Balai Gadang Kec. Koto Tangah Padang</div>
                    <div class="lap-kop-alamat">Kode Pos: 25171 &nbsp; Website: https://mahad-thib.uinib.ac.id</div>
                    <div class="lap-kop-alamat">e-mail: ma'had_thib@uinib.ac.id</div>
                </div>
                <div aria-hidden="true"></div>
            </div>
            <h1 class="lap-print-judul">{{ $laporan['judul'] }}</h1>
            <p class="lap-print-periode">Periode {{ $laporan['periode_label'] ?? '-' }} &middot; Dicetak {{ now()->format('d/m/Y H:i') }}</p>
            @foreach (($metaCetak ?? []) as $labelMeta => $nilaiMeta)
                <p class="lap-print-meta">{{ $labelMeta }}: {{ $nilaiMeta }}</p>
            @endforeach
        </div>



        <div class="lap-summary">

            @foreach ($laporan['ringkasan'] as $ringkasan)

                <div class="lap-summary-card">

                    <span>{{ $ringkasan['label'] }}</span>

                    <strong>{{ $ringkasan['nilai'] }}</strong>

                </div>

            @endforeach

        </div>



        <div class="lap-table-card">

            <div class="lap-table-head">

                <h2>{{ $laporan['judul_tabel'] }}</h2>



                <div class="lap-actions lap-no-print">

                    <button type="button" class="lap-btn lap-btn-outline" onclick="window.print()">

                        Cetak Laporan

                    </button>



                    @if (! empty($unduhUrl))

                        <a href="{{ $unduhUrl }}" class="lap-btn">Unduh CSV</a>

                    @endif



                    @if (! empty($tautanTambahan))

                        @foreach ($tautanTambahan as $tautan)

                            <a href="{{ $tautan['url'] }}" class="lap-btn lap-btn-outline">{{ $tautan['label'] }}</a>

                        @endforeach

                    @endif

                </div>

            </div>



            <div class="lap-table-wrap">

                <table class="lap-table">

                    <thead>

                        <tr>

                            <th class="lap-center">No</th>



                            @foreach ($laporan['kolom'] as $kolom)

                                <th class="{{ ($kolom['align'] ?? '') === 'center' ? 'lap-center' : '' }}">

                                    {{ $kolom['label'] }}

                                </th>

                            @endforeach

                        </tr>

                    </thead>



                    <tbody>

                        @forelse ($laporan['baris'] as $index => $baris)

                            <tr>

                                <td class="lap-center">{{ $index + 1 }}</td>



                                @foreach ($laporan['kolom'] as $kolom)

                                    @php

                                        $nilai = $baris[$kolom['key']] ?? '-';

                                        $badge = $kolom['badge'][$nilai] ?? null;

                                    @endphp



                                    <td class="{{ $kolom['class'] ?? '' }} {{ ($kolom['align'] ?? '') === 'center' ? 'lap-center' : '' }}">

                                        @if ($badge)

                                            <span class="lap-badge lap-badge-{{ $badge }}">{{ $nilai }}</span>

                                        @else

                                            {{ $nilai }}

                                        @endif

                                    </td>

                                @endforeach

                            </tr>

                        @empty

                            <tr>

                                <td colspan="{{ $jumlahKolom + 1 }}" class="lap-empty">

                                    Tidak ada data yang sesuai dengan periode atau pencarian ini.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
