@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
    @php
        $zona = $ringkasan['zonaWaktu'];

        $tindakan = [
            [
                'judul' => 'Isi absensi',
                'meta' => $ringkasan['absensiHariIni'].' jadwal hari ini',
                'jumlah' => (int) $ringkasan['absensiBelumDiisi'],
                'ikon' => 'absensi',
                'rute' => route('guru.absensi'),
            ],
            [
                'judul' => 'Nilai pengumpulan',
                'meta' => $ringkasan['jumlahPengumpulan'].' pengumpulan masuk',
                'jumlah' => (int) $ringkasan['belumDinilai'],
                'ikon' => 'laporan',
                'rute' => route('guru.tugas.index'),
            ],
            [
                'judul' => 'Tugas draft',
                'meta' => $ringkasan['tugasAktif'].' tugas aktif',
                'jumlah' => (int) $ringkasan['tugasDraft'],
                'ikon' => 'tugas',
                'rute' => route('guru.tugas.index'),
            ],
        ];
    @endphp

    <style>
        /* Semua aturan di-scope agar sidebar, header, dan dashboard role lain tidak berubah. */
        #simahad-guru-dashboard {
            --sd-maroon: #6b0d1b;
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

        #simahad-guru-dashboard * { box-sizing: border-box; }

        #simahad-guru-dashboard svg {
            display: block;
            width: 18px;
            height: 18px;
        }

        /* ---------- Baris atas ---------- */
        #simahad-guru-dashboard .sd-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: -8px;
        }

        #simahad-guru-dashboard .sd-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--sd-soft);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        #simahad-guru-dashboard .sd-eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--sd-maroon);
            box-shadow: 0 0 0 4px rgba(107, 13, 27, .08);
        }

        #simahad-guru-dashboard .sd-date {
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

        #simahad-guru-dashboard .sd-date svg { width: 15px; height: 15px; color: var(--sd-maroon); }

        /* ---------- Hero ---------- */
        #simahad-guru-dashboard .sd-hero {
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

        #simahad-guru-dashboard .sd-hero::after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -120px;
            width: 320px;
            height: 320px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .05);
        }

        #simahad-guru-dashboard .sd-hero-content {
            position: relative;
            z-index: 1;
            min-width: 0;
            max-width: 560px;
        }

        #simahad-guru-dashboard .sd-hero-label {
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

        #simahad-guru-dashboard .sd-hero h1 {
            margin: 14px 0 8px;
            font-size: 29px;
            line-height: 1.25;
            letter-spacing: -.02em;
            font-weight: 750;
        }

        #simahad-guru-dashboard .sd-hero p {
            margin: 0;
            max-width: 460px;
            color: rgba(255, 255, 255, .8);
            font-size: 13px;
            line-height: 1.65;
        }

        #simahad-guru-dashboard .sd-hero-board {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(3, minmax(92px, 1fr));
            gap: 2px;
            padding: 6px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 16px;
            background: rgba(255, 255, 255, .08);
        }

        #simahad-guru-dashboard .sd-hero-stat { padding: 12px 16px; }

        #simahad-guru-dashboard .sd-hero-stat span {
            display: block;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, .72);
            font-size: 11px;
            font-weight: 600;
        }

        #simahad-guru-dashboard .sd-hero-stat strong {
            font-size: 26px;
            line-height: 1.1;
            font-weight: 750;
            letter-spacing: -.02em;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- Judul bagian ---------- */
        #simahad-guru-dashboard .sd-section { display: flex; flex-direction: column; gap: 14px; }

        #simahad-guru-dashboard .sd-section-head h2 {
            margin: 0;
            font-size: 17px;
            line-height: 1.3;
            letter-spacing: -.01em;
            font-weight: 750;
        }

        #simahad-guru-dashboard .sd-section-head p {
            margin: 3px 0 0;
            color: var(--sd-faint);
            font-size: 12.5px;
            line-height: 1.5;
        }

        /* ---------- Tindakan ---------- */
        #simahad-guru-dashboard .sd-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        #simahad-guru-dashboard .sd-act {
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

        #simahad-guru-dashboard .sd-act:hover {
            border-color: #e3d5d9;
            box-shadow: 0 14px 28px -22px rgba(31, 36, 48, .5);
            transform: translateY(-1px);
        }

        #simahad-guru-dashboard .sd-act:focus-visible {
            outline: 2px solid var(--sd-maroon);
            outline-offset: 2px;
        }

        #simahad-guru-dashboard .sd-act-icon {
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

        #simahad-guru-dashboard .sd-act-body { display: flex; flex-direction: column; min-width: 0; }

        #simahad-guru-dashboard .sd-act-label {
            font-size: 13px;
            line-height: 1.35;
            font-weight: 700;
        }

        #simahad-guru-dashboard .sd-act-meta {
            margin-top: 2px;
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.45;
        }

        #simahad-guru-dashboard .sd-act-count {
            margin-left: auto;
            color: #b9bfc9;
            font-size: 28px;
            line-height: 1;
            font-weight: 750;
            letter-spacing: -.03em;
            font-variant-numeric: tabular-nums;
        }

        #simahad-guru-dashboard .sd-act.is-open .sd-act-count { color: var(--sd-maroon); }

        /* ---------- Kartu ---------- */
        #simahad-guru-dashboard .sd-duo {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
            gap: 16px;
            align-items: stretch;
        }

        #simahad-guru-dashboard .sd-duo.is-even { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        #simahad-guru-dashboard .sd-card {
            display: flex;
            flex-direction: column;
            min-width: 0;
            border: 1px solid var(--sd-line);
            border-radius: var(--sd-radius);
            background: var(--sd-card);
            overflow: hidden;
        }

        #simahad-guru-dashboard .sd-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 17px 20px 13px;
        }

        #simahad-guru-dashboard .sd-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        #simahad-guru-dashboard .sd-card-title h3 {
            margin: 0;
            font-size: 14.5px;
            line-height: 1.35;
            font-weight: 700;
        }

        #simahad-guru-dashboard .sd-card-title p {
            margin: 1px 0 0;
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.45;
        }

        #simahad-guru-dashboard .sd-mark {
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

        #simahad-guru-dashboard .sd-tools {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        #simahad-guru-dashboard .sd-card-link {
            color: var(--sd-maroon);
            font-size: 12px;
            font-weight: 650;
            white-space: nowrap;
        }

        #simahad-guru-dashboard .sd-card-link:hover { text-decoration: underline; }

        #simahad-guru-dashboard .sd-button {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 9px;
            background: var(--sd-maroon);
            color: #fff;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        #simahad-guru-dashboard .sd-button:hover { background: #8d1c2e; }

        #simahad-guru-dashboard .sd-card-body { padding: 2px 20px 18px; }

        /* ---------- Daftar ringkas ---------- */
        #simahad-guru-dashboard .sd-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 11px 0;
            border-top: 1px solid var(--sd-line);
        }

        #simahad-guru-dashboard .sd-row:first-child { border-top: 0; padding-top: 2px; }
        #simahad-guru-dashboard .sd-row:last-child { padding-bottom: 0; }

        #simahad-guru-dashboard .sd-row-time {
            flex-shrink: 0;
            min-width: 88px;
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

        #simahad-guru-dashboard .sd-row-copy { display: flex; flex-direction: column; min-width: 0; }

        #simahad-guru-dashboard .sd-row-title {
            font-size: 12.5px;
            font-weight: 650;
            line-height: 1.45;
        }

        #simahad-guru-dashboard .sd-row-sub {
            margin-top: 1px;
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.45;
        }

        #simahad-guru-dashboard .sd-row-date {
            margin-left: auto;
            flex-shrink: 0;
            color: var(--sd-faint);
            font-size: 11px;
            white-space: nowrap;
        }

        /* ---------- Pil status ---------- */
        #simahad-guru-dashboard .sd-pill {
            display: inline-flex;
            flex-shrink: 0;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 700;
        }

        #simahad-guru-dashboard .sd-pill.is-active { background: #f8eff2; color: var(--sd-maroon); }
        #simahad-guru-dashboard .sd-pill.is-draft { background: #fdf6e7; color: #8a5a0b; }

        /* ---------- Chip ---------- */
        #simahad-guru-dashboard .sd-chips { display: flex; flex-wrap: wrap; gap: 7px; }

        #simahad-guru-dashboard .sd-chip {
            display: inline-flex;
            padding: 5px 11px;
            border-radius: 999px;
            background: #f6f7f9;
            color: var(--sd-soft);
            font-size: 11.5px;
            font-weight: 600;
            line-height: 1.4;
        }

        #simahad-guru-dashboard .sd-group-label {
            margin: 0 0 9px;
            color: var(--sd-faint);
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        #simahad-guru-dashboard .sd-group + .sd-group { margin-top: 16px; }

        /* ---------- Kosong ---------- */
        #simahad-guru-dashboard .sd-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 26px 14px 20px;
            text-align: center;
        }

        #simahad-guru-dashboard .sd-empty-icon {
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

        #simahad-guru-dashboard .sd-empty strong {
            font-size: 12.5px;
            font-weight: 650;
            color: var(--sd-soft);
        }

        #simahad-guru-dashboard .sd-empty small {
            color: var(--sd-faint);
            font-size: 11.5px;
            line-height: 1.5;
            max-width: 280px;
        }

        /* ---------- Footer ---------- */
        #simahad-guru-dashboard .sd-footer {
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
        @media (max-width: 980px) {
            #simahad-guru-dashboard .sd-duo,
            #simahad-guru-dashboard .sd-duo.is-even { grid-template-columns: minmax(0, 1fr); }
        }

        @media (max-width: 760px) {
            #simahad-guru-dashboard { gap: 22px; }

            #simahad-guru-dashboard .sd-hero {
                flex-direction: column;
                align-items: stretch;
                padding: 26px 22px;
                gap: 20px;
            }

            #simahad-guru-dashboard .sd-hero h1 { font-size: 25px; }

            #simahad-guru-dashboard .sd-hero-stat { padding: 10px; }
            #simahad-guru-dashboard .sd-hero-stat strong { font-size: 22px; }

            #simahad-guru-dashboard .sd-actions { grid-template-columns: minmax(0, 1fr); gap: 10px; }
        }

        @media (max-width: 560px) {
            #simahad-guru-dashboard .sd-act { padding: 15px 16px; }
            #simahad-guru-dashboard .sd-card-head { padding: 15px 16px 11px; }
            #simahad-guru-dashboard .sd-card-body { padding: 2px 16px 16px; }
            #simahad-guru-dashboard .sd-hero p { font-size: 12.5px; }
            #simahad-guru-dashboard .sd-row-date { display: none; }
            #simahad-guru-dashboard .sd-tools .sd-card-link { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            #simahad-guru-dashboard .sd-act { transition: none; }
        }
    </style>

    <main id="simahad-guru-dashboard">
        <div class="sd-topline">
            
            <span class="sd-date">
                <x-panel.ikon nama="jadwal" />
                {{ now($zona)->locale('id')->translatedFormat('l, d F Y') }}
            </span>
        </div>

        <header class="sd-hero">
            <div class="sd-hero-content">
                <span class="sd-hero-label">Guru</span>
                <h1>Assalamu’alaikum, {{ $guru?->name ?? 'Guru' }}</h1>
                <p>Fokus pada jadwal mengajar, absensi, dan tugas Anda hari ini.</p>
            </div>
            <div class="sd-hero-board" role="group" aria-label="Ringkasan penugasan">
                <div class="sd-hero-stat">
                    <span>Mata pelajaran</span>
                    <strong>{{ number_format($ringkasan['jumlahMapel'], 0, ',', '.') }}</strong>
                </div>
                <div class="sd-hero-stat">
                    <span>Jadwal mengajar</span>
                    <strong>{{ number_format($ringkasan['jumlahJadwal'], 0, ',', '.') }}</strong>
                </div>
                <div class="sd-hero-stat">
                    <span>Kelompok</span>
                    <strong>{{ number_format($ringkasan['jumlahKelompok'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </header>

        <section class="sd-section" aria-labelledby="sd-tindakan-heading">
            <div class="sd-section-head">
                <div>
                    <h2 id="sd-tindakan-heading">Perlu Tindakan</h2>
                    <p>Yang menunggu Anda selesaikan hari ini.</p>
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
                        <span class="sd-mark"><x-panel.ikon nama="jadwal" /></span>
                        <div>
                            <h3>Jadwal Mengajar Hari Ini</h3>
                            <p>{{ $ringkasan['jadwalHariIni']->count() }} jadwal</p>
                        </div>
                    </div>
                    <a class="sd-card-link" href="{{ route('guru.jadwal-saya') }}">Jadwal saya</a>
                </div>
                <div class="sd-card-body">
                    @forelse ($ringkasan['jadwalHariIni'] as $jadwal)
                        <div class="sd-row">
                            <span class="sd-row-time">{{ $jadwal->jamTeks() }}</span>
                            <span class="sd-row-copy">
                                <span class="sd-row-title">{{ $jadwal->namaMataPelajaran() }}</span>
                                <span class="sd-row-sub">{{ $jadwal->kelompok ?: 'Semua kelompok' }} · {{ $jadwal->ruangan ?: 'Ruang kelas' }}</span>
                            </span>
                        </div>
                    @empty
                        <div class="sd-empty">
                            <span class="sd-empty-icon"><x-panel.ikon nama="jadwal" /></span>
                            <strong>Tidak ada jadwal hari ini</strong>
                            <small>Jadwal mengajar ditetapkan Musyrifah pada menu Manajemen Pembelajaran.</small>
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
                            <p>Informasi terbaru Ma’had</p>
                        </div>
                    </div>
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
                        <span class="sd-mark"><x-panel.ikon nama="tugas" /></span>
                        <div>
                            <h3>Tugas Terbaru</h3>
                            <p>{{ $ringkasan['tugasAktif'] }} aktif · {{ $ringkasan['tugasDraft'] }} draft</p>
                        </div>
                    </div>
                    <div class="sd-tools">
                        <a class="sd-card-link" href="{{ route('guru.tugas.index') }}">Semua</a>
                        <a class="sd-button" href="{{ route('guru.tugas.create') }}">Buat tugas</a>
                    </div>
                </div>
                <div class="sd-card-body">
                    @forelse ($ringkasan['tugasDaftar'] as $tugas)
                        <div class="sd-row">
                            <span class="sd-row-copy">
                                <span class="sd-row-title">{{ $tugas->judul }}</span>
                                <span class="sd-row-sub">
                                    {{ $tugas->kelompok ?: 'Semua kelompok' }} · {{ $tugas->pengumpulan_count }} pengumpulan
                                    @if ($tugas->batas_pengumpulan)
                                        · batas {{ $tugas->batas_pengumpulan->locale('id')->translatedFormat('d M Y H:i') }}
                                    @endif
                                </span>
                            </span>
                            <span class="sd-pill {{ $tugas->status === \App\Models\Tugas::STATUS_DIPUBLIKASIKAN ? 'is-active' : 'is-draft' }}">
                                {{ $tugas->status === \App\Models\Tugas::STATUS_DIPUBLIKASIKAN ? 'Aktif' : 'Draft' }}
                            </span>
                        </div>
                    @empty
                        <div class="sd-empty">
                            <span class="sd-empty-icon"><x-panel.ikon nama="tugas" /></span>
                            <strong>Belum ada tugas</strong>
                            <small>Gunakan tombol Buat tugas untuk memulai.</small>
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="sd-card">
                <div class="sd-card-head">
                    <div class="sd-card-title">
                        <span class="sd-mark"><x-panel.ikon nama="pembelajaran" /></span>
                        <div>
                            <h3>Penugasan Mengajar</h3>
                            <p>Kelompok dan mata pelajaran Anda</p>
                        </div>
                    </div>
                </div>
                <div class="sd-card-body">
                    @if ($ringkasan['kelompok']->isNotEmpty() || $ringkasan['mataPelajaran']->isNotEmpty())
                        @if ($ringkasan['kelompok']->isNotEmpty())
                            <div class="sd-group">
                                <p class="sd-group-label">Kelompok</p>
                                <div class="sd-chips">
                                    @foreach ($ringkasan['kelompok'] as $kelompok)
                                        <span class="sd-chip">{{ $kelompok }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($ringkasan['mataPelajaran']->isNotEmpty())
                            <div class="sd-group">
                                <p class="sd-group-label">Mata pelajaran</p>
                                <div class="sd-chips">
                                    @foreach ($ringkasan['mataPelajaran'] as $mapel)
                                        <span class="sd-chip">{{ $mapel }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="sd-empty">
                            <span class="sd-empty-icon"><x-panel.ikon nama="pembelajaran" /></span>
                            <strong>Belum ada penugasan</strong>
                            <small>Hubungi Musyrifah bila penugasan mengajar belum tersedia.</small>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        
    </main>
@endsection
