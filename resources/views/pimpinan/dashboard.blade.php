@extends('layouts.pimpinan')

@section('title', 'Dashboard Pimpinan')

@section('content')
    @php
        $pendaftaran = $ringkasan['pendaftaran'];
        $kamar = $ringkasan['kamar'];
        $pembayaran = $ringkasan['pembayaran'];
        $pembelajaran = $ringkasan['pembelajaran'];
        $absensi = $ringkasan['absensi'];
        $perizinan = $ringkasan['perizinan'];

        $grafik = collect($pendaftaran['per_bulan']);
        $maksGrafik = max(1, (int) $grafik->max('jumlah'));
        $totalGrafik = (int) $grafik->sum('jumlah');
        $zona = 'Asia/Jakarta';

        $nominalTeks = (float) $pembayaran['nominal'] > 0
            ? $pembayaran['nominalRupiah']
            : 'nominal belum diatur';

        $formatPersen = function (float $nilai): string {
            return rtrim(rtrim(number_format($nilai, 1, ',', '.'), '0'), ',');
        };

        $kpi = [
            [
                'judul' => 'Total Pendaftaran',
                'nilai' => number_format($pendaftaran['total'], 0, ',', '.'),
                'ket' => number_format($pendaftaran['menunggu'], 0, ',', '.').' menunggu · '
                    .number_format($pendaftaran['disetujui'], 0, ',', '.').' disetujui',
                'ikon' => 'pendaftaran',
                'nada' => 'is-maroon',
                'rute' => route('pimpinan.laporan-pendaftaran'),
            ],
            [
                'judul' => 'Kelompok Belajar',
                'nilai' => number_format($pembelajaran['kelompok'], 0, ',', '.'),
                'ket' => number_format($pembelajaran['mataPelajaran'], 0, ',', '.').' mata pelajaran · '
                    .number_format($pembelajaran['jadwal'], 0, ',', '.').' jadwal',
                'ikon' => 'pembelajaran',
                'nada' => 'is-blue',
                'rute' => route('pimpinan.monitoring.pembelajaran'),
            ],
            [
                'judul' => 'Penyelesaian Tugas',
                'nilai' => $pembelajaran['tugas'] > 0
                    ? $formatPersen((float) $pembelajaran['penyelesaian']).'%'
                    : '—',
                'ket' => $pembelajaran['tugas'] > 0
                    ? number_format($pembelajaran['tugasDinilai'], 0, ',', '.').' dari '
                        .number_format($pembelajaran['tugas'], 0, ',', '.').' tugas sudah dinilai'
                    : 'Belum ada tugas yang dipublikasikan',
                'ikon' => 'tugas',
                'nada' => 'is-amber',
                'rute' => route('pimpinan.monitoring.pembelajaran'),
            ],
            [
                'judul' => 'Perizinan Diproses',
                'nilai' => number_format($perizinan['total'], 0, ',', '.'),
                'ket' => number_format($perizinan['menunggu'], 0, ',', '.').' menunggu · '
                    .number_format($perizinan['disetujui'], 0, ',', '.').' disetujui',
                'ikon' => 'surat',
                'nada' => 'is-green',
                'rute' => route('pimpinan.monitoring.perizinan'),
            ],
        ];

        $akses = [
            ['label' => 'Mahasiswa', 'ikon' => 'mahasiswa', 'rute' => route('pimpinan.monitoring.mahasiswa')],
            ['label' => 'Kamar', 'ikon' => 'kamar', 'rute' => route('pimpinan.monitoring.kamar')],
            ['label' => 'Pembayaran', 'ikon' => 'pembayaran', 'rute' => route('pimpinan.monitoring.pembayaran')],
            ['label' => 'Pembelajaran', 'ikon' => 'pembelajaran', 'rute' => route('pimpinan.monitoring.pembelajaran')],
            ['label' => 'Perizinan', 'ikon' => 'surat', 'rute' => route('pimpinan.monitoring.perizinan')],
            ['label' => 'Laporan', 'ikon' => 'laporan', 'rute' => route('pimpinan.laporan-mahad')],
            
        ];
    @endphp

    <style>
        /* Semua aturan di-scope agar sidebar, header, dan dashboard role lain tidak berubah. */
        #simahad-pimpinan-dashboard {
            --sd-maroon: #6b0d1b;
            --sd-maroon-mid: #8d1c2e;
            --sd-gold: #c9a96a;
            --sd-text: #1f2430;
            --sd-soft: #4f5767;
            --sd-faint: #6b7280;
            --sd-line: #ecedf2;
            --sd-soft-bg: #f8f5f1;
            --sd-card: #ffffff;
            --sd-radius: 16px;
            display: flex;
            flex-direction: column;
            gap: 26px;
            color: var(--sd-text);
            font-size: 13px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        #simahad-pimpinan-dashboard * { box-sizing: border-box; }

        #simahad-pimpinan-dashboard svg {
            display: block;
            width: 18px;
            height: 18px;
        }

        /* ---------- Baris atas ---------- */
        #simahad-pimpinan-dashboard .sd-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: -8px;
        }

        #simahad-pimpinan-dashboard .sd-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--sd-soft);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        #simahad-pimpinan-dashboard .sd-eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--sd-maroon);
            box-shadow: 0 0 0 4px rgba(107, 13, 27, .08);
        }

        #simahad-pimpinan-dashboard .sd-date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border: 1px solid var(--sd-line);
            border-radius: 999px;
            background: var(--sd-card);
            color: var(--sd-maroon);
            font-size: 12.5px;
            font-weight: 700;
            box-shadow: 0 1px 2px rgba(31, 36, 48, .04);
            white-space: nowrap;
        }

        #simahad-pimpinan-dashboard .sd-date svg { width: 15px; height: 15px; color: var(--sd-maroon); }

        /* ---------- Hero ---------- */
        #simahad-pimpinan-dashboard .sd-hero {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 26px;
            padding: 32px 34px;
            border-radius: 20px;
            background:
                radial-gradient(120% 160% at 100% 0%, rgba(201, 169, 106, .22), transparent 58%),
                linear-gradient(135deg, #7d1022 0%, #5c0a17 100%);
            color: #fff;
            overflow: hidden;
        }

        #simahad-pimpinan-dashboard .sd-hero::after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -120px;
            width: 320px;
            height: 320px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .05);
        }

        #simahad-pimpinan-dashboard .sd-hero-content {
            position: relative;
            z-index: 1;
            min-width: 0;
            max-width: 560px;
        }

        #simahad-pimpinan-dashboard .sd-hero-label {
            display: inline-flex;
            padding: 5px 12px;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        #simahad-pimpinan-dashboard .sd-hero h1 {
            margin: 14px 0 8px;
            font-size: 29px;
            line-height: 1.25;
            letter-spacing: -.02em;
            font-weight: 750;
        }

        #simahad-pimpinan-dashboard .sd-hero p {
            margin: 0;
            max-width: 470px;
            color: rgba(255, 255, 255, .8);
            font-size: 13px;
            line-height: 1.65;
        }

        #simahad-pimpinan-dashboard .sd-hero-board {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(3, minmax(96px, 1fr));
            gap: 2px;
            padding: 6px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 16px;
            background: rgba(255, 255, 255, .08);
        }

        #simahad-pimpinan-dashboard .sd-hero-stat { padding: 12px 16px; }

        #simahad-pimpinan-dashboard .sd-hero-stat span {
            display: block;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, .72);
            font-size: 11px;
            font-weight: 600;
        }

        #simahad-pimpinan-dashboard .sd-hero-stat strong {
            font-size: 26px;
            line-height: 1.1;
            font-weight: 750;
            letter-spacing: -.02em;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- Akses monitoring ---------- */
        #simahad-pimpinan-dashboard .sd-quick {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(142px, 1fr));
            gap: 9px;
        }

        #simahad-pimpinan-dashboard .sd-quick-link {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
            padding: 9px 12px;
            border: 1px solid var(--sd-line);
            border-radius: 11px;
            background: var(--sd-card);
            color: #333a49;
            font-size: 11.5px;
            font-weight: 700;
            box-shadow: 0 1px 2px rgba(20, 28, 45, .03);
            transition: border-color .18s ease, background .18s ease, transform .18s ease;
        }

        #simahad-pimpinan-dashboard .sd-quick-link:hover {
            border-color: #e3afbc;
            background: #fffafb;
            transform: translateY(-2px);
        }

        #simahad-pimpinan-dashboard .sd-quick-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            border-radius: 8px;
            background: #fbecef;
            color: var(--sd-maroon);
        }

        #simahad-pimpinan-dashboard .sd-quick-icon svg { width: 15px; height: 15px; }

        #simahad-pimpinan-dashboard .sd-quick-link > span:last-child {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* ---------- Judul bagian ---------- */
        #simahad-pimpinan-dashboard .sd-section { display: flex; flex-direction: column; gap: 14px; }

        #simahad-pimpinan-dashboard .sd-section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        #simahad-pimpinan-dashboard .sd-section-head h2 {
            margin: 0;
            font-size: 17px;
            line-height: 1.3;
            letter-spacing: -.01em;
            font-weight: 750;
        }

        #simahad-pimpinan-dashboard .sd-section-head p {
            margin: 3px 0 0;
            color: var(--sd-faint);
            font-size: 12.5px;
            line-height: 1.5;
        }

        #simahad-pimpinan-dashboard .sd-link {
            display: inline-flex;
            align-items: center;
            color: var(--sd-maroon);
            font-size: 11.5px;
            font-weight: 750;
            white-space: nowrap;
        }

        #simahad-pimpinan-dashboard .sd-link::after {
            content: "";
            display: inline-block;
            width: 6px;
            height: 6px;
            margin-left: 7px;
            border-top: 1.6px solid currentColor;
            border-right: 1.6px solid currentColor;
            transform: rotate(45deg) translateY(-1px);
        }

        #simahad-pimpinan-dashboard .sd-link:hover { text-decoration: underline; }

        /* ---------- KPI ---------- */
        #simahad-pimpinan-dashboard .sd-kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        #simahad-pimpinan-dashboard .sd-kpi {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
            padding: 18px;
            border: 1px solid var(--sd-line);
            border-radius: var(--sd-radius);
            background: var(--sd-card);
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        #simahad-pimpinan-dashboard .sd-kpi:hover {
            transform: translateY(-3px);
            border-color: #e2c3cb;
            box-shadow: 0 18px 32px -24px rgba(107, 13, 27, .35);
        }

        #simahad-pimpinan-dashboard .sd-kpi-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: var(--sd-soft);
            font-size: 11.5px;
            font-weight: 700;
        }

        #simahad-pimpinan-dashboard .sd-kpi-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 11px;
            background: #fbe9ed;
            color: #9e2744;
        }

        #simahad-pimpinan-dashboard .sd-kpi-icon svg { width: 18px; height: 18px; }

        #simahad-pimpinan-dashboard .sd-kpi.is-blue .sd-kpi-icon { background: #eaf0fa; color: #3d5f8f; }
        #simahad-pimpinan-dashboard .sd-kpi.is-amber .sd-kpi-icon { background: #fff4df; color: #c58113; }
        #simahad-pimpinan-dashboard .sd-kpi.is-green .sd-kpi-icon { background: #e9f8ef; color: #178154; }

        #simahad-pimpinan-dashboard .sd-kpi-value {
            margin: 0;
            font-size: clamp(28px, 2.4vw, 34px);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -.045em;
            color: #1e2533;
            font-variant-numeric: tabular-nums;
        }

        #simahad-pimpinan-dashboard .sd-kpi-foot {
            margin-top: auto;
            color: var(--sd-faint);
            font-size: 10.5px;
            line-height: 1.45;
        }

        /* ---------- Kartu ---------- */
        #simahad-pimpinan-dashboard .sd-duo {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
            gap: 16px;
            align-items: stretch;
        }

        #simahad-pimpinan-dashboard .sd-duo.is-even { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        #simahad-pimpinan-dashboard .sd-card {
            display: flex;
            flex-direction: column;
            min-width: 0;
            border: 1px solid var(--sd-line);
            border-radius: var(--sd-radius);
            background: var(--sd-card);
            overflow: hidden;
        }

        #simahad-pimpinan-dashboard .sd-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 17px 20px 13px;
        }

        #simahad-pimpinan-dashboard .sd-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        #simahad-pimpinan-dashboard .sd-card-title h3 {
            margin: 0;
            font-size: 14.5px;
            line-height: 1.35;
            font-weight: 700;
        }

        #simahad-pimpinan-dashboard .sd-card-title p {
            margin: 1px 0 0;
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.45;
        }

        #simahad-pimpinan-dashboard .sd-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            border-radius: 11px;
            background: #f8eff2;
            color: var(--sd-maroon);
        }

        #simahad-pimpinan-dashboard .sd-card-link {
            color: var(--sd-maroon);
            font-size: 12px;
            font-weight: 650;
            white-space: nowrap;
        }

        #simahad-pimpinan-dashboard .sd-card-link:hover { text-decoration: underline; }

        #simahad-pimpinan-dashboard .sd-card-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 2px 20px 18px;
        }

        #simahad-pimpinan-dashboard .sd-big {
            margin: 0;
            font-size: 34px;
            line-height: 1;
            letter-spacing: -.04em;
            font-weight: 750;
            font-variant-numeric: tabular-nums;
        }

        #simahad-pimpinan-dashboard .sd-note {
            margin: 6px 0 0;
            color: var(--sd-faint);
            font-size: 12px;
            line-height: 1.55;
        }

        #simahad-pimpinan-dashboard .sd-footnote {
            margin: auto 0 0;
            padding-top: 14px;
            border-top: 1px solid var(--sd-line);
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.5;
        }

        /* ---------- Progres ---------- */
        #simahad-pimpinan-dashboard .sd-track {
            display: block;
            width: 100%;
            height: 8px;
            margin-top: 14px;
            border-radius: 999px;
            background: #f1f2f6;
            overflow: hidden;
        }

        #simahad-pimpinan-dashboard .sd-track > span {
            display: block;
            width: var(--sd-pct, 0%);
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #8d1c2e, #a93a4f);
        }

        #simahad-pimpinan-dashboard .sd-track.is-thin { height: 6px; margin-top: 8px; }

        #simahad-pimpinan-dashboard .sd-lantai { margin-top: 12px; }

        #simahad-pimpinan-dashboard .sd-lantai-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 0;
            border-top: 1px solid var(--sd-line);
        }

        #simahad-pimpinan-dashboard .sd-lantai-row:first-child { border-top: 0; padding-top: 0; }

        #simahad-pimpinan-dashboard .sd-lantai-row span { font-size: 12.5px; color: var(--sd-soft); }

        #simahad-pimpinan-dashboard .sd-lantai-row strong {
            font-size: 12.5px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- Pembayaran ---------- */
        #simahad-pimpinan-dashboard .sd-pay-rows { margin-top: 12px; }

        #simahad-pimpinan-dashboard .sd-pay-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
            border-top: 1px solid var(--sd-line);
            font-size: 12.5px;
        }

        #simahad-pimpinan-dashboard .sd-pay-row:first-child { border-top: 0; }
        #simahad-pimpinan-dashboard .sd-pay-row span { color: var(--sd-soft); }
        #simahad-pimpinan-dashboard .sd-pay-row strong { font-weight: 700; font-variant-numeric: tabular-nums; }

        #simahad-pimpinan-dashboard .sd-alert {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            margin-top: 12px;
            padding: 11px 13px;
            border-radius: 11px;
            background: #fdf6e7;
            color: #7d560f;
            font-size: 12px;
            line-height: 1.5;
        }

        #simahad-pimpinan-dashboard .sd-alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

        /* ---------- Grafik ---------- */
        #simahad-pimpinan-dashboard .sd-chart {
            display: flex;
            flex: 1;
            align-items: stretch;
            gap: 8px;
            min-height: 168px;
            margin-top: 6px;
            padding: 4px 2px 0;
        }

        #simahad-pimpinan-dashboard .sd-chart-col {
            display: flex;
            flex: 1;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }

        #simahad-pimpinan-dashboard .sd-chart-value {
            position: absolute;
            bottom: calc(100% + 4px);
            left: 50%;
            transform: translateX(-50%);
            color: #303747;
            font-size: 10.5px;
            font-weight: 800;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
            pointer-events: none;
        }

        #simahad-pimpinan-dashboard .sd-chart-col.is-empty .sd-chart-value { color: #9aa1ad; }

        #simahad-pimpinan-dashboard .sd-chart-track {
            display: flex;
            flex: 1;
            align-items: flex-end;
            justify-content: center;
            width: 100%;
            border-bottom: 1px solid #e9ebf1;
        }

        #simahad-pimpinan-dashboard .sd-chart-bar {
            position: relative;
            display: block;
            width: min(42px, 74%);
            height: var(--sd-bar, 6%);
            min-height: 4px;
            border-radius: 7px 7px 3px 3px;
            background: linear-gradient(180deg, #c45c73, #8d1c2e);
            transition: height .35s ease;
        }

        #simahad-pimpinan-dashboard .sd-chart-col.is-today .sd-chart-bar {
            background: linear-gradient(180deg, #a12441, #5f0a19);
            box-shadow: 0 6px 14px -6px rgba(107, 13, 27, .55);
        }

        #simahad-pimpinan-dashboard .sd-chart-col.is-empty .sd-chart-bar {
            background: #e5e8ef;
            box-shadow: none;
        }

        #simahad-pimpinan-dashboard .sd-chart-day {
            color: #5f6775;
            font-size: 10.5px;
            font-weight: 750;
            white-space: nowrap;
        }

        #simahad-pimpinan-dashboard .sd-chart-col.is-today .sd-chart-day { color: var(--sd-maroon); }

        #simahad-pimpinan-dashboard .sd-chart-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
            padding-top: 11px;
            border-top: 1px solid var(--sd-line);
            color: var(--sd-faint);
            font-size: 11.5px;
        }

        #simahad-pimpinan-dashboard .sd-chart-foot strong { color: #333b4c; font-weight: 750; }

        #simahad-pimpinan-dashboard .sd-legend {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        #simahad-pimpinan-dashboard .sd-legend i {
            width: 8px;
            height: 8px;
            border-radius: 3px;
            background: linear-gradient(180deg, #a12441, #5f0a19);
        }

        /* ---------- Kosong ---------- */
        #simahad-pimpinan-dashboard .sd-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 26px 14px 20px;
            text-align: center;
        }

        #simahad-pimpinan-dashboard .sd-empty-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            margin-bottom: 2px;
            border-radius: 11px;
            background: #f5f6f9;
            color: #a5abb7;
        }

        #simahad-pimpinan-dashboard .sd-empty strong {
            font-size: 12.5px;
            font-weight: 650;
            color: var(--sd-soft);
        }

        #simahad-pimpinan-dashboard .sd-empty small {
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.5;
            max-width: 270px;
        }

        /* ---------- Footer ---------- */
        #simahad-pimpinan-dashboard .sd-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            padding-top: 4px;
            color: var(--sd-faint);
            font-size: 11.5px;
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 1180px) {
            #simahad-pimpinan-dashboard .sd-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 980px) {
            #simahad-pimpinan-dashboard .sd-duo,
            #simahad-pimpinan-dashboard .sd-duo.is-even { grid-template-columns: minmax(0, 1fr); }
        }

        @media (max-width: 760px) {
            #simahad-pimpinan-dashboard { gap: 22px; }

            #simahad-pimpinan-dashboard .sd-hero {
                flex-direction: column;
                align-items: stretch;
                padding: 26px 22px;
                gap: 20px;
            }

            #simahad-pimpinan-dashboard .sd-hero h1 { font-size: 25px; }

            #simahad-pimpinan-dashboard .sd-hero-stat { padding: 10px; }
            #simahad-pimpinan-dashboard .sd-hero-stat strong { font-size: 22px; }

            #simahad-pimpinan-dashboard .sd-chart { min-height: 150px; gap: 6px; }
        }

        @media (max-width: 560px) {
            #simahad-pimpinan-dashboard .sd-kpis { gap: 10px; }
            #simahad-pimpinan-dashboard .sd-kpi { padding: 14px; }
            #simahad-pimpinan-dashboard .sd-kpi-value { font-size: 27px; }
            #simahad-pimpinan-dashboard .sd-card-head { padding: 15px 16px 11px; }
            #simahad-pimpinan-dashboard .sd-card-body { padding: 2px 16px 16px; }
            #simahad-pimpinan-dashboard .sd-hero p { font-size: 12.5px; }
            #simahad-pimpinan-dashboard .sd-quick { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>

    <main id="simahad-pimpinan-dashboard">
        <div class="sd-topline">
            <span class="sd-date">
                <x-panel.ikon nama="jadwal" />
                {{ now($zona)->locale('id')->translatedFormat('l, d F Y') }}
            </span>
        </div>

        <header class="sd-hero">
            <div class="sd-hero-content">
                <span class="sd-hero-label">Pimpinan</span>
                <h1>Assalamu’alaikum, {{ auth()->user()->name ?? 'Pimpinan' }}</h1>
                <p>Pantau kinerja Ma’had lintas unit </p>
            </div>
            <div class="sd-hero-board" role="group" aria-label="Ringkasan kinerja Ma’had">
                <div class="sd-hero-stat">
                    <span>Mahasiswa</span>
                    <strong>{{ number_format($ringkasan['totalMahasiswa'], 0, ',', '.') }}</strong>
                </div>
                <div class="sd-hero-stat">
                    <span>Keterisian Per Lantai</span>
                    <strong>{{ $formatPersen((float) $kamar['persentase']) }}%</strong>
                </div>
                <div class="sd-hero-stat">
                    <span>Kehadiran</span>
                    <strong title="{{ $absensi['total'] > 0 ? $absensi['hadir'].' hadir dari '.$absensi['total'].' catatan absensi' : 'Belum ada catatan absensi' }}">
                        {{ $absensi['total'] > 0 ? $formatPersen((float) $absensi['persentase']).'%' : '—' }}
                    </strong>
                </div>
            </div>
        </header>

        <nav class="sd-quick" aria-label="Akses monitoring dan laporan">
            @foreach ($akses as $item)
                <a class="sd-quick-link" href="{{ $item['rute'] }}">
                    <span class="sd-quick-icon"><x-panel.ikon nama="{{ $item['ikon'] }}" /></span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <section class="sd-section" aria-labelledby="sd-kinerja-heading">
            <div class="sd-section-head">
                <div>
                    <h2 id="sd-kinerja-heading">Ringkasan Kinerja</h2>
                    
                </div>
                <a class="sd-link" href="{{ route('pimpinan.laporan-mahad') }}">Register laporan</a>
            </div>
            <div class="sd-kpis">
                @foreach ($kpi as $item)
                    <a class="sd-kpi {{ $item['nada'] }}" href="{{ $item['rute'] }}">
                        <div class="sd-kpi-top">
                            <span>{{ $item['judul'] }}</span>
                            <span class="sd-kpi-icon"><x-panel.ikon nama="{{ $item['ikon'] }}" /></span>
                        </div>
                        <strong class="sd-kpi-value">{{ $item['nilai'] }}</strong>
                        <span class="sd-kpi-foot">{{ $item['ket'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="sd-duo">
            <article class="sd-card">
                <div class="sd-card-head">
                    <div class="sd-card-title">
                        <span class="sd-mark"><x-panel.ikon nama="laporan" /></span>
                        <div>
                            <h3>Perkembangan Pendaftaran</h3>
                            <p>Enam bulan terakhir</p>
                        </div>
                    </div>
                    <a class="sd-card-link" href="{{ route('pimpinan.laporan-pendaftaran') }}">Laporan</a>
                </div>
                <div class="sd-card-body">
                    @if ($totalGrafik > 0)
                        <div class="sd-chart" role="img" aria-label="Grafik pendaftaran enam bulan terakhir">
                            @foreach ($grafik as $titik)
                                @php $tinggi = max(6, (int) round($titik['jumlah'] / $maksGrafik * 100)); @endphp
                                <div class="sd-chart-col {{ $loop->last ? 'is-today' : '' }} {{ $titik['jumlah'] === 0 ? 'is-empty' : '' }}">
                                    <span class="sd-chart-track">
                                        <span class="sd-chart-bar" style="--sd-bar: {{ $tinggi }}%">
                                            <span class="sd-chart-value">{{ $titik['jumlah'] }}</span>
                                        </span>
                                    </span>
                                    <span class="sd-chart-day">{{ $titik['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="sd-chart-foot">
                            <span>Total <strong>{{ number_format($totalGrafik, 0, ',', '.') }}</strong> pendaftaran dalam enam bulan</span>
                            <span class="sd-legend"><i></i> Bulan berjalan</span>
                        </div>
                    @else
                        <div class="sd-empty">
                            <span class="sd-empty-icon"><x-panel.ikon nama="pendaftaran" /></span>
                            <strong>Belum ada data pendaftaran</strong>
                            <small>Grafik muncul setelah pendaftaran tercatat dalam enam bulan terakhir.</small>
                        </div>
                    @endif
                </div>
            </article>

            <article class="sd-card">
                <div class="sd-card-head">
                    <div class="sd-card-title">
                        <span class="sd-mark"><x-panel.ikon nama="pembayaran" /></span>
                        <div>
                            <h3>Ringkasan Pembayaran</h3>
                            <p>Status tagihan periode aktif</p>
                        </div>
                    </div>
                    <a class="sd-card-link" href="{{ route('pimpinan.monitoring.pembayaran') }}">Monitoring</a>
                </div>
                <div class="sd-card-body">
                    @if ($pembayaran['periode'])
                        <p class="sd-big">{{ $formatPersen((float) $pembayaran['persentase']) }}%</p>
                        <p class="sd-note">{{ $pembayaran['tahap'] ?: 'Tahap pembayaran' }} · {{ $pembayaran['periode'] }} · {{ $nominalTeks }}</p>
                        <span class="sd-track" role="img" aria-label="Mahasiswa lunas {{ $formatPersen((float) $pembayaran['persentase']) }} persen">
                            <span style="--sd-pct: {{ min(100, max(0, (float) $pembayaran['persentase'])) }}%"></span>
                        </span>

                        <div class="sd-pay-rows">
                            <div class="sd-pay-row"><span>Lunas terkonfirmasi</span><strong>{{ $pembayaran['lunas'] }} mahasiswa</strong></div>
                            <div class="sd-pay-row"><span>Menunggu konfirmasi</span><strong>{{ $pembayaran['menunggu'] }} mahasiswa</strong></div>
                            <div class="sd-pay-row"><span>Belum membayar</span><strong>{{ $pembayaran['belum'] }} mahasiswa</strong></div>
                        </div>

                        @if ($pembayaran['pengingat'])
                            <div class="sd-alert">
                                <x-panel.ikon nama="pembayaran" />
                                <span>Jatuh tempo {{ $pembayaran['jatuhTempo'] }} — sisa {{ $pembayaran['sisaHari'] }} hari.</span>
                            </div>
                        @endif

                        <p class="sd-footnote">
                            {{ $pembayaran['belum'] > 0
                                ? $pembayaran['belum'].' mahasiswa belum melunasi tagihan periode ini.'
                                : 'Seluruh mahasiswa sudah melunasi tagihan periode ini.' }}
                        </p>
                    @else
                        <div class="sd-empty">
                            <span class="sd-empty-icon"><x-panel.ikon nama="pembayaran" /></span>
                            <strong>Belum ada periode aktif</strong>
                            <small>Ringkasan tampil setelah periode pembayaran dibuka.</small>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <div class="sd-duo is-even">
            <article class="sd-card">
                <div class="sd-card-head">
                    <div class="sd-card-title">
                        <span class="sd-mark"><x-panel.ikon nama="kamar" /></span>
                        <div>
                            <h3>Keterisian per Lantai</h3>
                            <p>{{ $kamar['jumlah'] }} kamar terdaftar</p>
                        </div>
                    </div>
                    <a class="sd-card-link" href="{{ route('pimpinan.monitoring.kamar') }}">Monitoring</a>
                </div>
                <div class="sd-card-body">
                    @if ($kamar['per_lantai']->isNotEmpty())
                        <p class="sd-big">{{ $formatPersen((float) $kamar['persentase']) }}%</p>
                        <p class="sd-note">
                            {{ $kamar['terisi'] }} dari {{ $kamar['kapasitas'] }} tempat terisi · sisa {{ $kamar['sisa'] }} tempat
                        </p>

                        <div class="sd-lantai">
                            @foreach ($kamar['per_lantai'] as $lantai)
                                @php $persenLantai = $lantai['kapasitas'] > 0 ? min(100, round(($lantai['terisi'] / $lantai['kapasitas']) * 100, 1)) : 0; @endphp
                                <div class="sd-lantai-row">
                                    <span>Lantai {{ $lantai['lantai'] }} · {{ $lantai['jumlah'] }} kamar</span>
                                    <strong>{{ $lantai['terisi'] }}/{{ $lantai['kapasitas'] }}</strong>
                                </div>
                                <span class="sd-track is-thin" role="img" aria-label="Lantai {{ $lantai['lantai'] }} terisi {{ $formatPersen((float) $persenLantai) }} persen">
                                    <span style="--sd-pct: {{ $persenLantai }}%; background: #c9a96a;"></span>
                                </span>
                            @endforeach
                        </div>

                        <p class="sd-footnote">
                            {{ $kamar['sisa'] > 0
                                ? $kamar['sisa'].' tempat masih tersedia untuk penempatan mahasiswa.'
                                : 'Semua tempat sudah terisi.' }}
                        </p>
                    @else
                        <div class="sd-empty">
                            <span class="sd-empty-icon"><x-panel.ikon nama="kamar" /></span>
                            <strong>Data kamar belum tersedia</strong>
                            <small>Laporan per lantai muncul setelah data kamar diisi Musyrifah.</small>
                        </div>
                    @endif
                </div>
            </article>

            <article class="sd-card">
                <div class="sd-card-head">
                    <div class="sd-card-title">
                        <span class="sd-mark"><x-panel.ikon nama="surat" /></span>
                        <div>
                            <h3>Rekap Perizinan</h3>
                            <p>Pengajuan surat mahasiswa</p>
                        </div>
                    </div>
                    <a class="sd-card-link" href="{{ route('pimpinan.monitoring.perizinan') }}">Monitoring</a>
                </div>
                <div class="sd-card-body">
                    <div class="sd-pay-rows" style="margin-top: 0;">
                        <div class="sd-pay-row"><span>Total pengajuan</span><strong>{{ number_format($perizinan['total'], 0, ',', '.') }}</strong></div>
                        <div class="sd-pay-row"><span>Menunggu keputusan</span><strong>{{ number_format($perizinan['menunggu'], 0, ',', '.') }}</strong></div>
                        <div class="sd-pay-row"><span>Disetujui</span><strong>{{ number_format($perizinan['disetujui'], 0, ',', '.') }}</strong></div>
                        <div class="sd-pay-row"><span>Selesai</span><strong>{{ number_format($perizinan['selesai'], 0, ',', '.') }}</strong></div>
                    </div>

                    <p class="sd-footnote">
                        Pimpinan berfokus pada monitoring dan evaluasi; keputusan operasional dijalankan Musyrifah.
                    </p>
                </div>
            </article>
        </div>

        
    </main>
@endsection
