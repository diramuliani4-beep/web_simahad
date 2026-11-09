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

    @media print {
        @page {
            size: landscape;
            margin: 1cm;
        }

        body * {
            visibility: hidden !important;
        }

        #lap-area,
        #lap-area * {
            visibility: visible !important;
        }

        #lap-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .lap-no-print {
            display: none !important;
        }

        .lap-print-head {
            display: block;
            margin-bottom: 14px;
        }

        .lap-print-head h1 {
            margin: 0 0 4px;
            font-size: 20px;
            color: #000;
        }

        .lap-print-head p {
            margin: 0;
            font-size: 12px;
            color: #333;
        }

        #lap-area .lap-table {
            min-width: 0;
        }

        #lap-area th,
        #lap-area td {
            border: 1px solid #333;
            padding: 8px;
            color: #000;
        }

        #lap-area th {
            background: #eee !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        #lap-area .lap-summary {
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        #lap-area .lap-summary-card {
            box-shadow: none;
            border: 1px solid #333;
            padding: 10px;
        }

        #lap-area .lap-summary-card strong {
            color: #000;
            font-size: 16px;
        }

        #lap-area .lap-badge {
            padding: 2px 6px;
            border: 1px solid #999;
            background: #fff !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
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

    <form action="{{ $filterAction }}" method="GET" class="lap-filter lap-no-print">
        <div class="lap-field">
            <label for="lap-periode">Periode laporan</label>
            <select name="periode" id="lap-periode">
                @foreach ($opsiPeriode as $opsi)
                    <option
                        value="{{ $opsi['value'] }}"
                        @selected($laporan['periode'] === $opsi['value'])
                    >
                        {{ $opsi['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

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
    </form>

    <div id="lap-area">
        <div class="lap-print-head">
            <h1>{{ $laporan['judul'] }}</h1>
            <p>Periode {{ $laporan['periode_label'] }} · Dicetak {{ now()->format('d/m/Y H:i') }}</p>
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
