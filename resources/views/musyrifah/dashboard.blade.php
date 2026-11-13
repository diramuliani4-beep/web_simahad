@extends('layouts.musyrifah')

@section('title', 'Dashboard Musyrifah')

@section('content')
    @php
        $kamar = $ringkasan['kamar'];
        $pembayaran = $ringkasan['pembayaran'];
        $pendaftaran = $ringkasan['pendaftaran'];
        $pengajuan = $ringkasan['pengajuanKamar'];
        $perizinan = $ringkasan['suratPerizinan'];
        $pembelajaran = $ringkasan['pembelajaran'];

        $zona = $ringkasan['zonaWaktu'];

        $nominalTeks = (float) $pembayaran['nominal'] > 0
            ? $pembayaran['nominalRupiah']
            : 'nominal belum diatur';

        $perluTindakan = (int) $pendaftaran['menunggu']
            + (int) $pengajuan['menunggu']
            + (int) $perizinan['menunggu']
            + (int) $pembelajaran['absensiBelumDiisi'];

        $tindakan = [
            [
                'judul' => 'Pendaftaran',
                'meta' => $pendaftaran['disetujui'].' disetujui',
                'jumlah' => (int) $pendaftaran['menunggu'],
                'ikon' => 'pendaftaran',
                'rute' => route('musyrifah.pendaftaran'),
            ],
            [
                'judul' => 'Pengajuan kamar',
                'meta' => 'Pindah & penetapan',
                'jumlah' => (int) $pengajuan['menunggu'],
                'ikon' => 'penghuni',
                'rute' => route('musyrifah.data_kamar'),
            ],
            [
                'judul' => 'Surat perizinan',
                'meta' => $perizinan['perluPerbaikan'].' perlu perbaikan',
                'jumlah' => (int) $perizinan['menunggu'],
                'ikon' => 'surat',
                'rute' => route('musyrifah.surat_perizinan'),
            ],
            [
                'judul' => 'Absensi mengajar',
                'meta' => $pembelajaran['jadwalHariIni'].' jadwal hari ini',
                'jumlah' => (int) $pembelajaran['absensiBelumDiisi'],
                'ikon' => 'absensi',
                'rute' => route('musyrifah.manajemen_pembelajaran'),
            ],
        ];

        $formatPersen = function (float $nilai): string {
            return rtrim(rtrim(number_format($nilai, 1, ',', '.'), '0'), ',');
        };
    @endphp

    <style>
        /* Semua aturan di-scope agar sidebar, header, dan dashboard role lain tidak berubah. */
        #simahad-musyrifah-dashboard {
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

        #simahad-musyrifah-dashboard * { box-sizing: border-box; }

        #simahad-musyrifah-dashboard svg {
            display: block;
            width: 18px;
            height: 18px;
        }

        /* ---------- Baris atas ---------- */
        #simahad-musyrifah-dashboard .sd-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: -8px;
        }

        #simahad-musyrifah-dashboard .sd-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--sd-soft);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        #simahad-musyrifah-dashboard .sd-eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--sd-maroon);
            box-shadow: 0 0 0 4px rgba(107, 13, 27, .08);
        }

        #simahad-musyrifah-dashboard .sd-date {
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

        #simahad-musyrifah-dashboard .sd-date svg { width: 15px; height: 15px; color: var(--sd-maroon); }

        /* ---------- Hero ---------- */
        #simahad-musyrifah-dashboard .sd-hero {
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

        #simahad-musyrifah-dashboard .sd-hero::after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -120px;
            width: 320px;
            height: 320px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .05);
        }

        #simahad-musyrifah-dashboard .sd-hero-content {
            position: relative;
            z-index: 1;
            min-width: 0;
            max-width: 560px;
        }

        #simahad-musyrifah-dashboard .sd-hero-label {
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

        #simahad-musyrifah-dashboard .sd-hero h1 {
            margin: 14px 0 8px;
            font-size: 29px;
            line-height: 1.25;
            letter-spacing: -.02em;
            font-weight: 750;
        }

        #simahad-musyrifah-dashboard .sd-hero p {
            margin: 0;
            max-width: 460px;
            color: rgba(255, 255, 255, .8);
            font-size: 13px;
            line-height: 1.65;
        }

        #simahad-musyrifah-dashboard .sd-hero-board {
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

        #simahad-musyrifah-dashboard .sd-hero-stat { padding: 12px 16px; }

        #simahad-musyrifah-dashboard .sd-hero-stat span {
            display: block;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, .72);
            font-size: 11px;
            font-weight: 600;
        }

        #simahad-musyrifah-dashboard .sd-hero-stat strong {
            font-size: 26px;
            line-height: 1.1;
            font-weight: 750;
            letter-spacing: -.02em;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- Judul bagian ---------- */
        #simahad-musyrifah-dashboard .sd-section { display: flex; flex-direction: column; gap: 14px; }

        #simahad-musyrifah-dashboard .sd-section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        #simahad-musyrifah-dashboard .sd-section-head h2 {
            margin: 0;
            font-size: 17px;
            line-height: 1.3;
            letter-spacing: -.01em;
            font-weight: 750;
        }

        #simahad-musyrifah-dashboard .sd-section-head p {
            margin: 3px 0 0;
            color: var(--sd-faint);
            font-size: 12.5px;
            line-height: 1.5;
        }

        /* ---------- Tindakan ---------- */
        #simahad-musyrifah-dashboard .sd-actions {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        #simahad-musyrifah-dashboard .sd-act {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            border: 1px solid var(--sd-line);
            border-radius: var(--sd-radius);
            background: var(--sd-card);
            color: inherit;
            text-decoration: none;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        #simahad-musyrifah-dashboard .sd-act:hover {
            border-color: #e3d5d9;
            box-shadow: 0 14px 28px -22px rgba(31, 36, 48, .5);
            transform: translateY(-1px);
        }

        #simahad-musyrifah-dashboard .sd-act:focus-visible {
            outline: 2px solid var(--sd-maroon);
            outline-offset: 2px;
        }

        #simahad-musyrifah-dashboard .sd-act-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border-radius: 13px;
            background: #f8eff2;
            color: var(--sd-maroon);
        }

        #simahad-musyrifah-dashboard .sd-act-body { display: flex; flex-direction: column; min-width: 0; }

        #simahad-musyrifah-dashboard .sd-act-label {
            font-size: 13px;
            line-height: 1.35;
            font-weight: 700;
        }

        #simahad-musyrifah-dashboard .sd-act-meta {
            margin-top: 2px;
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.45;
        }

        #simahad-musyrifah-dashboard .sd-act-count {
            margin-left: auto;
            color: #b9bfc9;
            font-size: 28px;
            line-height: 1;
            font-weight: 750;
            letter-spacing: -.03em;
            font-variant-numeric: tabular-nums;
        }

        #simahad-musyrifah-dashboard .sd-act.is-open .sd-act-count { color: var(--sd-maroon); }

        /* ---------- Kartu ---------- */
        #simahad-musyrifah-dashboard .sd-duo {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
            gap: 16px;
            align-items: stretch;
        }

        #simahad-musyrifah-dashboard .sd-duo.is-even { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        #simahad-musyrifah-dashboard .sd-card {
            display: flex;
            flex-direction: column;
            min-width: 0;
            border: 1px solid var(--sd-line);
            border-radius: var(--sd-radius);
            background: var(--sd-card);
            overflow: hidden;
        }

        #simahad-musyrifah-dashboard .sd-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 17px 20px 13px;
        }

        #simahad-musyrifah-dashboard .sd-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        #simahad-musyrifah-dashboard .sd-card-title h3 {
            margin: 0;
            font-size: 14.5px;
            line-height: 1.35;
            font-weight: 700;
        }

        #simahad-musyrifah-dashboard .sd-card-title p {
            margin: 1px 0 0;
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.45;
        }

        #simahad-musyrifah-dashboard .sd-mark {
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

        #simahad-musyrifah-dashboard .sd-card-link {
            color: var(--sd-maroon);
            font-size: 12px;
            font-weight: 650;
            white-space: nowrap;
        }

        #simahad-musyrifah-dashboard .sd-card-link:hover { text-decoration: underline; }

        #simahad-musyrifah-dashboard .sd-card-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 2px 20px 18px;
        }

        #simahad-musyrifah-dashboard .sd-big {
            margin: 0;
            font-size: 34px;
            line-height: 1;
            letter-spacing: -.04em;
            font-weight: 750;
            font-variant-numeric: tabular-nums;
        }

        #simahad-musyrifah-dashboard .sd-note {
            margin: 6px 0 0;
            color: var(--sd-faint);
            font-size: 12px;
            line-height: 1.55;
        }

        #simahad-musyrifah-dashboard .sd-footnote {
            margin: auto 0 0;
            padding-top: 14px;
            border-top: 1px solid var(--sd-line);
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.5;
        }

        /* ---------- Progres ---------- */
        #simahad-musyrifah-dashboard .sd-track {
            display: block;
            width: 100%;
            height: 8px;
            margin-top: 14px;
            border-radius: 999px;
            background: #f1f2f6;
            overflow: hidden;
        }

        #simahad-musyrifah-dashboard .sd-track > span {
            display: block;
            width: var(--sd-pct, 0%);
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #8d1c2e, #a93a4f);
        }

        #simahad-musyrifah-dashboard .sd-track.is-thin { height: 6px; margin-top: 8px; }

        #simahad-musyrifah-dashboard .sd-lantai { margin-top: 16px; }

        #simahad-musyrifah-dashboard .sd-lantai-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 0;
            border-top: 1px solid var(--sd-line);
        }

        #simahad-musyrifah-dashboard .sd-lantai-row:first-child { border-top: 0; padding-top: 0; }

        #simahad-musyrifah-dashboard .sd-lantai-row span { font-size: 12.5px; color: var(--sd-soft); }

        #simahad-musyrifah-dashboard .sd-lantai-row strong {
            font-size: 12.5px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- Daftar ringkas ---------- */
        #simahad-musyrifah-dashboard .sd-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 11px 0;
            border-top: 1px solid var(--sd-line);
        }

        #simahad-musyrifah-dashboard .sd-row:first-child { border-top: 0; padding-top: 2px; }
        #simahad-musyrifah-dashboard .sd-row:last-child { padding-bottom: 0; }

        #simahad-musyrifah-dashboard .sd-row-time {
            flex-shrink: 0;
            min-width: 74px;
            padding: 4px 8px;
            border-radius: 8px;
            background: var(--sd-soft-bg);
            color: var(--sd-maroon);
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        #simahad-musyrifah-dashboard .sd-row-copy { display: flex; flex-direction: column; min-width: 0; }

        #simahad-musyrifah-dashboard .sd-row-title {
            font-size: 12.5px;
            font-weight: 650;
            line-height: 1.45;
        }

        #simahad-musyrifah-dashboard .sd-row-sub {
            margin-top: 1px;
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.45;
        }

        #simahad-musyrifah-dashboard .sd-row-date {
            margin-left: auto;
            flex-shrink: 0;
            color: var(--sd-faint);
            font-size: 11px;
            white-space: nowrap;
        }

        /* ---------- Pembayaran ---------- */
        #simahad-musyrifah-dashboard .sd-pay-rows { margin-top: 12px; }

        #simahad-musyrifah-dashboard .sd-pay-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
            border-top: 1px solid var(--sd-line);
            font-size: 12.5px;
        }

        #simahad-musyrifah-dashboard .sd-pay-row:first-child { border-top: 0; }
        #simahad-musyrifah-dashboard .sd-pay-row span { color: var(--sd-soft); }
        #simahad-musyrifah-dashboard .sd-pay-row strong { font-weight: 700; font-variant-numeric: tabular-nums; }

        #simahad-musyrifah-dashboard .sd-alert {
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

        #simahad-musyrifah-dashboard .sd-alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

        /* ---------- Kosong ---------- */
        #simahad-musyrifah-dashboard .sd-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 26px 14px 20px;
            text-align: center;
        }

        #simahad-musyrifah-dashboard .sd-empty-icon {
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

        #simahad-musyrifah-dashboard .sd-empty strong {
            font-size: 12.5px;
            font-weight: 650;
            color: var(--sd-soft);
        }

        #simahad-musyrifah-dashboard .sd-empty small {
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.5;
            max-width: 260px;
        }

        /* ---------- Footer ---------- */
        #simahad-musyrifah-dashboard .sd-footer {
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
            #simahad-musyrifah-dashboard .sd-actions { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 980px) {
            #simahad-musyrifah-dashboard .sd-duo,
            #simahad-musyrifah-dashboard .sd-duo.is-even { grid-template-columns: minmax(0, 1fr); }
        }

        @media (max-width: 760px) {
            #simahad-musyrifah-dashboard { gap: 22px; }

            #simahad-musyrifah-dashboard .sd-hero {
                flex-direction: column;
                align-items: stretch;
                padding: 26px 22px;
                gap: 20px;
            }

            #simahad-musyrifah-dashboard .sd-hero h1 { font-size: 25px; }

            #simahad-musyrifah-dashboard .sd-hero-stat { padding: 10px; }
            #simahad-musyrifah-dashboard .sd-hero-stat strong { font-size: 22px; }
        }

        @media (max-width: 560px) {
            #simahad-musyrifah-dashboard .sd-actions { grid-template-columns: minmax(0, 1fr); gap: 10px; }
            #simahad-musyrifah-dashboard .sd-act { padding: 15px 16px; }
            #simahad-musyrifah-dashboard .sd-card-head { padding: 15px 16px 11px; }
            #simahad-musyrifah-dashboard .sd-card-body { padding: 2px 16px 16px; }
            #simahad-musyrifah-dashboard .sd-hero p { font-size: 12.5px; }
            #simahad-musyrifah-dashboard .sd-row-date { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            #simahad-musyrifah-dashboard .sd-act { transition: none; }
        }
    </style>

    <main id="simahad-musyrifah-dashboard">
        <div class="sd-topline">
            <span class="sd-eyebrow"><span class="sd-eyebrow-dot"></span> Dashboard Musyrifah</span>
            <span class="sd-date">
                <x-panel.ikon nama="jadwal" />
                {{ now($zona)->locale('id')->translatedFormat('l, d F Y') }}
            </span>
        </div>

        <header class="sd-hero">
            <div class="sd-hero-content">
                <span class="sd-hero-label">Musyrifah</span>
                <h1>Assalamu’alaikum, {{ auth()->user()->name ?? 'Musyrifah' }}</h1>
                <p>Pantau keterisian kamar, verifikasi berkas, dan siapkan layanan Ma’had hari ini.</p>
            </div>
            <div class="sd-hero-board" role="group" aria-label="Ringkasan hari ini">
                <div class="sd-hero-stat">
                    <span>Mahasiswa</span>
                    <strong>{{ number_format($ringkasan['totalMahasiswa'], 0, ',', '.') }}</strong>
                </div>
                <div class="sd-hero-stat">
                    <span>Keterisian kamar</span>
                    <strong>{{ $formatPersen((float) $kamar['persentase']) }}%</strong>
                </div>
                <div class="sd-hero-stat">
                    <span>Perlu tindakan</span>
                    <strong>{{ number_format($perluTindakan, 0, ',', '.') }}</strong>
                </div>
            </div>
        </header>

        <section class="sd-section" aria-labelledby="sd-tindakan-heading">
            <div class="sd-section-head">
                <div>
                    <h2 id="sd-tindakan-heading">Perlu Tindakan</h2>
                    <p>Apa yang menunggu Anda selesaikan hari ini.</p>
                </div>
            </div>
            <div class="sd-actions">
                @foreach ($tindakan as $item)
                    <a class="sd-act {{ $item['jumlah'] > 0 ? 'is-open' : '' }}" href="{{ $item['rute'] }}">
                        <span class="sd-act-icon"><x-panel.ikon nama="{{ $item['ikon'] }}" /></span>
                        <span class="sd-act-body">
                            <span class="sd-act-label">{{ $item['judul'] }}</span>
                            <span class="sd-act-meta">{{ $item['meta'] }}</span>
                        </span>
                        <span class="sd-act-count" aria-label="{{ $item['jumlah'] }} menunggu">{{ $item['jumlah'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="sd-duo">
            <article class="sd-card">
                <div class="sd-card-head">
                    <div class="sd-card-title">
                        <span class="sd-mark"><x-panel.ikon nama="pembelajaran" /></span>
                        <div>
                            <h3>Jadwal Hari Ini</h3>
                            <p>{{ $pembelajaran['jadwalHariIni'] }} jadwal · {{ $pembelajaran['kelompok'] }} kelompok</p>
                        </div>
                    </div>
                    <a class="sd-card-link" href="{{ route('musyrifah.manajemen_pembelajaran') }}">Kelola</a>
                </div>
                <div class="sd-card-body">
                    @forelse ($pembelajaran['jadwal'] as $jadwal)
                        <div class="sd-row">
                            <span class="sd-row-time">{{ $jadwal->jamTeks() }}</span>
                            <span class="sd-row-copy">
                                <span class="sd-row-title">{{ $jadwal->namaMataPelajaran() }}</span>
                                <span class="sd-row-sub">{{ $jadwal->kelompok ?: 'Semua kelompok' }} · {{ $jadwal->guru_pengampu ?: 'Guru belum ditetapkan' }}</span>
                            </span>
                        </div>
                    @empty
                        <div class="sd-empty">
                            <span class="sd-empty-icon"><x-panel.ikon nama="jadwal" /></span>
                            <strong>Tidak ada jadwal hari ini</strong>
                            <small>Jadwal pembelajaran akan muncul sesuai hari yang berjalan.</small>
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="sd-card">
                <div class="sd-card-head">
                    <div class="sd-card-title">
                        <span class="sd-mark"><x-panel.ikon nama="pengumuman" /></span>
                        <div>
                            <h3>Pengumuman</h3>
                            <p>Informasi terbaru untuk mahasiswa</p>
                        </div>
                    </div>
                    <a class="sd-card-link" href="{{ route('musyrifah.pengumuman') }}">Semua</a>
                </div>
                <div class="sd-card-body">
                    @forelse ($ringkasan['pengumuman'] as $pengumuman)
                        <div class="sd-row">
                            <span class="sd-row-copy">
                                <span class="sd-row-title">{{ $pengumuman->judul }}</span>
                                <span class="sd-row-sub">{{ $pengumuman->kategori ?: 'Informasi umum' }}</span>
                            </span>
                            <span class="sd-row-date">{{ $pengumuman->tanggalTeks() }}</span>
                        </div>
                    @empty
                        <div class="sd-empty">
                            <span class="sd-empty-icon"><x-panel.ikon nama="pengumuman" /></span>
                            <strong>Belum ada pengumuman</strong>
                            <small>Pengumuman yang dipublikasikan akan tampil di sini.</small>
                        </div>
                    @endforelse
                </div>
            </article>
        </div>

        <div class="sd-duo is-even">
            <article class="sd-card">
                <div class="sd-card-head">
                    <div class="sd-card-title">
                        <span class="sd-mark"><x-panel.ikon nama="kamar" /></span>
                        <div>
                            <h3>Keterisian Kamar</h3>
                            <p>{{ $kamar['jumlah'] }} kamar terdaftar</p>
                        </div>
                    </div>
                    <a class="sd-card-link" href="{{ route('musyrifah.data_kamar') }}">Data kamar</a>
                </div>
                <div class="sd-card-body">
                    <p class="sd-big">{{ $formatPersen((float) $kamar['persentase']) }}%</p>
                    <p class="sd-note">{{ $kamar['terisi'] }} dari {{ $kamar['kapasitas'] }} tempat terisi · sisa {{ $kamar['sisa'] }} tempat</p>
                    <span class="sd-track" role="img" aria-label="Keterisian kamar {{ $formatPersen((float) $kamar['persentase']) }} persen">
                        <span style="--sd-pct: {{ min(100, max(0, (float) $kamar['persentase'])) }}%"></span>
                    </span>

                    @if ($kamar['per_lantai']->isNotEmpty())
                        <div class="sd-lantai">
                            @foreach ($kamar['per_lantai'] as $lantai)
                                @php $persenLantai = $lantai['kapasitas'] > 0 ? min(100, round(($lantai['terisi'] / $lantai['kapasitas']) * 100, 1)) : 0; @endphp
                                <div class="sd-lantai-row">
                                    <span>Lantai {{ $lantai['lantai'] }}</span>
                                    <strong>{{ $lantai['terisi'] }}/{{ $lantai['kapasitas'] }}</strong>
                                </div>
                                <span class="sd-track is-thin" role="img" aria-label="Lantai {{ $lantai['lantai'] }} terisi {{ $formatPersen((float) $persenLantai) }} persen">
                                    <span style="--sd-pct: {{ $persenLantai }}%; background: #c9a96a;"></span>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <p class="sd-footnote">
                        {{ $kamar['sisa'] > 0
                            ? $kamar['sisa'].' tempat masih tersedia untuk penempatan mahasiswa.'
                            : 'Semua tempat sudah terisi.' }}
                    </p>
                </div>
            </article>

            <article class="sd-card">
                <div class="sd-card-head">
                    <div class="sd-card-title">
                        <span class="sd-mark"><x-panel.ikon nama="pembayaran" /></span>
                        <div>
                            <h3>Pembayaran</h3>
                            <p>Status tagihan periode aktif</p>
                        </div>
                    </div>
                    <a class="sd-card-link" href="{{ route('musyrifah.pembayaran') }}">Verifikasi</a>
                </div>
                <div class="sd-card-body">
                    @if ($pembayaran['periode'])
                        <p class="sd-big">{{ $formatPersen((float) $pembayaran['persentase']) }}%</p>
                        <p class="sd-note">{{ $pembayaran['tahap'] ?: 'Tahap pembayaran' }} · {{ $pembayaran['periode'] }} · {{ $nominalTeks }}</p>
                        <span class="sd-track" role="img" aria-label="Mahasiswa lunas {{ $formatPersen((float) $pembayaran['persentase']) }} persen">
                            <span style="--sd-pct: {{ min(100, max(0, (float) $pembayaran['persentase'])) }}%"></span>
                        </span>

                        <div class="sd-pay-rows">
                            <div class="sd-pay-row"><span>Sudah lunas</span><strong>{{ $pembayaran['lunas'] }} mahasiswa</strong></div>
                            <div class="sd-pay-row"><span>Menunggu konfirmasi</span><strong>{{ $pembayaran['menunggu'] }} mahasiswa</strong></div>
                            <div class="sd-pay-row"><span>Belum membayar</span><strong>{{ $pembayaran['belum'] }} mahasiswa</strong></div>
                        </div>

                        @if ($pembayaran['pengingat'])
                            <div class="sd-alert">
                                <x-panel.ikon nama="pembayaran" />
                                <span>Jatuh tempo {{ $pembayaran['jatuhTempo'] }} — sisa {{ $pembayaran['sisaHari'] }} hari. Segera ingatkan mahasiswa.</span>
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
                            <small>Aktifkan periode pada menu Pembayaran agar tagihan muncul.</small>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <footer class="sd-footer">
            <span>SIMAHAD · Panel Musyrifah</span>
            <span>Diperbarui {{ now($zona)->locale('id')->translatedFormat('d M Y, H:i') }} WIB</span>
        </footer>
    </main>
@endsection
