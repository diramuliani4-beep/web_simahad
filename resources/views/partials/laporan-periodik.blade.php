@php
    /** @var array<string, mixed> $laporan */
    /** @var array<string, string> $meta */
    $meta = $meta ?? [];

    $formatSel = function ($nilai, ?string $format = null): string {
        if ($nilai === null || $nilai === '') {
            return '-';
        }

        if ($format === 'rupiah' && is_numeric($nilai)) {
            return 'Rp '.number_format((float) $nilai, 0, ',', '.');
        }

        if ($format === 'persen' && is_numeric($nilai)) {
            return number_format((float) $nilai, 0, ',', '.').'%';
        }

        return (string) $nilai;
    };

    $seksi = $laporan['seksi'] ?? [];
    $keterbatasan = $laporan['keterbatasan'] ?? [];
@endphp

<style>
    .lp-dokumen {
        font-family: 'Poppins', sans-serif;
        color: #29252a;
        background: #fff;
    }

    .lp-kop {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
        padding-bottom: 16px;
        margin-bottom: 18px;
        border-bottom: 2px solid #780d20;
    }

    .lp-kop h1 {
        margin: 4px 0 6px;
        color: #780d20;
        font-size: 22px;
        font-weight: 700;
        line-height: 1.3;
    }

    .lp-kop-kicker {
        margin: 0;
        color: #8b7f84;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .lp-kop-periode {
        margin: 0;
        color: #352c30;
        font-size: 14px;
        font-weight: 600;
    }

    .lp-kop-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 8px 18px;
        margin: 0;
        min-width: 260px;
        max-width: 520px;
    }

    .lp-kop-meta div {
        min-width: 0;
    }

    .lp-kop-meta dt {
        margin: 0 0 2px;
        color: #8b7f84;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .lp-kop-meta dd {
        margin: 0;
        color: #352c30;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .lp-seksi {
        padding: 16px 0 6px;
        border-bottom: 1px solid #eee7e9;
        page-break-inside: auto;
    }

    .lp-seksi:last-child {
        border-bottom: 0;
    }

    .lp-seksi h2 {
        margin: 0 0 4px;
        color: #780d20;
        font-size: 15px;
        font-weight: 700;
    }

    .lp-seksi-ket {
        margin: 0 0 10px;
        color: #7b7176;
        font-size: 12px;
        line-height: 1.6;
    }

    .lp-ringkasan {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 10px;
        margin: 10px 0 14px;
    }

    .lp-ringkasan-item {
        padding: 10px 12px;
        border: 1px solid #f0e4e7;
        border-radius: 10px;
        background: #fdf8f9;
    }

    .lp-ringkasan-item span {
        display: block;
        margin-bottom: 4px;
        color: #81777d;
        font-size: 11px;
        font-weight: 600;
    }

    .lp-ringkasan-item strong {
        color: #780d20;
        font-size: 15px;
        font-weight: 700;
    }

    .lp-tabel-judul {
        margin: 12px 0 6px;
        color: #352c30;
        font-size: 12.5px;
        font-weight: 700;
    }

    .lp-tabel-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .lp-tabel {
        width: 100%;
        min-width: 620px;
        border-collapse: collapse;
        margin-bottom: 12px;
    }

    .lp-tabel th {
        padding: 8px 9px;
        border: 1px solid #e6dde0;
        background: #f8f0f2;
        color: #780d20;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
    }

    .lp-tabel td {
        padding: 7px 9px;
        border: 1px solid #f0eaec;
        color: #51474c;
        font-size: 11.5px;
        vertical-align: top;
    }

    .lp-tabel tbody tr:nth-child(even) {
        background: #fcfafa;
    }

    .lp-tabel .lp-tengah {
        text-align: center;
    }

    .lp-tabel .lp-kanan {
        text-align: right;
    }

    .lp-tabel-kosong {
        padding: 18px !important;
        color: #91858a !important;
        text-align: center;
        font-style: italic;
    }

    .lp-teks {
        margin: 10px 0;
        padding: 10px 12px;
        border-left: 3px solid #e3cfd4;
        background: #fdf8f9;
    }

    .lp-teks h3 {
        margin: 0 0 4px;
        color: #352c30;
        font-size: 12px;
        font-weight: 700;
    }

    .lp-teks p {
        margin: 0;
        color: #51474c;
        font-size: 12px;
        line-height: 1.65;
    }

    .lp-catatan {
        margin: 8px 0 0;
        padding-left: 18px;
        color: #7b7176;
        font-size: 11.5px;
        line-height: 1.6;
    }

    .lp-keterbatasan {
        margin-top: 8px;
        padding: 14px;
        border: 1px dashed #dccdd2;
        border-radius: 10px;
        background: #fbf7f8;
    }

    .lp-keterbatasan h2 {
        margin-bottom: 8px;
    }

    .lp-keterbatasan ul {
        margin: 0;
        padding-left: 18px;
        color: #6f636a;
        font-size: 11.5px;
        line-height: 1.6;
    }

    .lp-ttd {
        display: flex;
        justify-content: flex-end;
        margin-top: 26px;
        page-break-inside: avoid;
    }

    .lp-ttd div {
        min-width: 220px;
        text-align: center;
        color: #352c30;
        font-size: 12px;
        line-height: 1.7;
    }

    .lp-ttd-space {
        height: 52px;
    }

    @media print {
        @page {
            size: landscape;
            margin: 1cm;
        }

        .lp-tabel {
            min-width: 0;
        }

        .lp-tabel th,
        .lp-tabel td {
            border-color: #666 !important;
            color: #000 !important;
        }

        .lp-tabel th {
            background: #eee !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .lp-ringkasan-item,
        .lp-teks,
        .lp-keterbatasan {
            background: #fff !important;
        }
    }
</style>

<article class="lp-dokumen">
    <header class="lp-kop">
        <div>
            <p class="lp-kop-kicker">SIMAHAD · Ma’had Al-Jami’ah</p>
            <h1>{{ $laporan['judul'] ?? 'Laporan' }}</h1>
            <p class="lp-kop-periode">Periode {{ $laporan['periode_label'] ?? '-' }}</p>
        </div>

        <dl class="lp-kop-meta">
            <div>
                <dt>Jenis Laporan</dt>
                <dd>{{ $laporan['jenis_label'] ?? '-' }}</dd>
            </div>
            <div>
                <dt>Periode</dt>
                <dd>{{ $laporan['periode_label'] ?? '-' }}</dd>
            </div>
            <div>
                <dt>Cakupan Data</dt>
                <dd>{{ $laporan['cakupan'] ?? '-' }}</dd>
            </div>
            <div>
                <dt>Dibangkitkan</dt>
                <dd>{{ $laporan['dibangkitkan_label'] ?? '-' }}</dd>
            </div>

            @foreach ($meta as $labelMeta => $nilaiMeta)
                <div>
                    <dt>{{ $labelMeta }}</dt>
                    <dd>{{ $nilaiMeta }}</dd>
                </div>
            @endforeach
        </dl>
    </header>

    @foreach ($seksi as $bagian)
        <section class="lp-seksi">
            <h2>{{ $bagian['judul'] ?? 'Bagian' }}</h2>

            @if (filled($bagian['keterangan'] ?? null))
                <p class="lp-seksi-ket">{{ $bagian['keterangan'] }}</p>
            @endif

            @if (! empty($bagian['ringkasan']))
                <div class="lp-ringkasan">
                    @foreach ($bagian['ringkasan'] as $ringkasan)
                        <div class="lp-ringkasan-item">
                            <span>{{ $ringkasan['label'] ?? '-' }}</span>
                            <strong>{{ $ringkasan['nilai'] ?? '-' }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif

            @foreach (($bagian['tabel'] ?? []) as $tabel)
                @php $kolom = $tabel['kolom'] ?? []; @endphp

                @if ($kolom !== [])
                    @if (filled($tabel['judul'] ?? null))
                        <p class="lp-tabel-judul">{{ $tabel['judul'] }}</p>
                    @endif

                    <div class="lp-tabel-wrap">
                        <table class="lp-tabel">
                            <thead>
                                <tr>
                                    <th class="lp-tengah" style="width: 38px;">No</th>

                                    @foreach ($kolom as $definisi)
                                        <th class="{{ ($definisi['align'] ?? '') === 'center' ? 'lp-tengah' : (($definisi['align'] ?? '') === 'right' ? 'lp-kanan' : '') }}">
                                            {{ $definisi['label'] ?? '-' }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @forelse (($tabel['baris'] ?? []) as $indeks => $nilaiBaris)
                                    <tr>
                                        <td class="lp-tengah">{{ $indeks + 1 }}</td>

                                        @foreach ($kolom as $definisi)
                                            @php
                                                $nilai = $nilaiBaris[$definisi['key'] ?? ''] ?? null;
                                                $align = $definisi['align'] ?? '';
                                            @endphp

                                            <td class="{{ $align === 'center' ? 'lp-tengah' : ($align === 'right' ? 'lp-kanan' : '') }}">
                                                {{ $formatSel($nilai, $definisi['format'] ?? null) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($kolom) + 1 }}" class="lp-tabel-kosong">
                                            Tidak ada data pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach

            @foreach (($bagian['teks'] ?? []) as $labelTeks => $isiTeks)
                <div class="lp-teks">
                    <h3>{{ $labelTeks }}</h3>
                    <p>{!! nl2br(e($isiTeks)) !!}</p>
                </div>
            @endforeach

            @if (! empty($bagian['catatan']))
                <ul class="lp-catatan">
                    @foreach ($bagian['catatan'] as $catatan)
                        <li>{{ $catatan }}</li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endforeach

    @if ($keterbatasan !== [])
        <section class="lp-keterbatasan">
            <h2>Keterbatasan Data</h2>

            <ul>
                @foreach ($keterbatasan as $catatan)
                    <li>{{ $catatan }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <div class="lp-ttd">
        <div>
            <p>Musyrifah Ma’had</p>
            <div class="lp-ttd-space"></div>
            <p><strong>{{ $meta['Pembuat'] ?? '________________' }}</strong></p>
        </div>
    </div>
</article>
