@extends('layouts.mahasiswa')

@section('title', 'Tugas')

@section('content')
<style>
    /* =========================================================
       SIMAHAD — TUGAS MAHASISWA
       Hanya tampilan: variabel, route dan alur tugas tetap sama.
       Semua aturan berada dalam .tugas-page agar tidak mengubah layout.
       ========================================================= */
    .tugas-page {
        --tm-maroon: #800b22;
        --tm-maroon-dark: #64081b;
        --tm-ink: #292229;
        --tm-muted: #81757e;
        --tm-line: #eadfe3;
        --tm-tint: #fff6f8;
        display: block !important;
        clear: both;
        position: relative;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        margin: 0 !important;
        padding: 0 0 28px !important;
        color: var(--tm-ink);
        font-family: 'Plus Jakarta Sans', 'Poppins', Arial, sans-serif;
        font-size: 13px;
        line-height: 1.55;
        container-type: inline-size;
    }
    .tugas-page *, .tugas-page *::before, .tugas-page *::after { box-sizing: border-box; }
    .tugas-page > * { min-width: 0; }
    .tugas-page a { text-decoration: none; }

    /* Breadcrumb seragam dengan navigasi Musyrifah. */
    .tugas-page .tm-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin: 0 0 12px;
        color: #93858c;
        font-size: 12px;
        line-height: 1.5;
    }
    .tugas-page .tm-breadcrumb a {
        color: var(--tm-maroon);
        font-weight: 600;
        text-decoration: none;
    }
    .tugas-page .tm-breadcrumb a:hover { text-decoration: underline; }
    .tugas-page .tm-breadcrumb a:focus-visible {
        outline: 3px solid rgba(128, 11, 34, .18);
        outline-offset: 3px;
    }
    /* Header ringkas & identitas kelompok */
    .tugas-page .tm-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px 18px;
        padding: 0 0 18px;
        margin: 0 0 17px;
        border-bottom: 1px solid #e9d6dc;
    }
    .tugas-page .tm-head-copy { min-width: 0; flex: 1 1 360px; }
    .tugas-page .tm-head h1 {
        margin: 0;
        color: #251d23;
        font-size: clamp(21px, 2.2vw, 27px);
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: -.035em;
    }
    .tugas-page .tm-head p {
        margin: 5px 0 0;
        color: var(--tm-muted);
        font-size: 12.5px;
        line-height: 1.65;
    }
    .tugas-page .tm-kelompok {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        max-width: 100%;
        min-height: 37px;
        padding: 8px 12px;
        border: 1px solid #e9cbd3;
        border-radius: 10px;
        background: #fff8fa;
        color: #77152b;
        font-size: 11.5px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }
    .tugas-page .tm-kelompok svg { width: 17px; height: 17px; flex: 0 0 17px; }

    /* Navigasi kembali — tampil jelas tanpa mengubah fitur tugas. */
    .tugas-page .tm-head-actions {
        display: flex;
        flex: 0 1 auto;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px;
        min-width: 0;
    }
    .tugas-page .tm-back-dashboard {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 37px;
        padding: 8px 13px;
        border: 1px solid #e4cdd3;
        border-radius: 9px;
        background: #fff;
        color: var(--tm-maroon);
        font-family: inherit;
        font-size: 12px;
        font-weight: 750;
        white-space: nowrap;
        text-decoration: none;
        transition: border-color .15s ease, background-color .15s ease;
    }
    .tugas-page .tm-back-dashboard:hover {
        border-color: #c99aa7;
        background: #fff4f7;
        color: var(--tm-maroon-dark);
    }
    .tugas-page .tm-back-dashboard:focus-visible {
        outline: 3px solid rgba(128, 11, 34, .18);
        outline-offset: 2px;
    }
    .tugas-page .tm-back-dashboard svg {
        width: 16px;
        height: 16px;
        flex: 0 0 16px;
    }

    /* Statistik kompak seperti modul SIMAHAD lain */
    .tugas-page .tm-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin: 0 0 17px;
    }
    .tugas-page .tm-stat {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        min-height: 82px;
        padding: 13px 15px;
        border: 1px solid var(--tm-line);
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 3px 13px rgba(77, 22, 39, .025);
    }
    .tugas-page .tm-stat-icon {
        display: grid;
        place-items: center;
        flex: 0 0 39px;
        width: 39px;
        height: 39px;
        border-radius: 11px;
        background: #faedf1;
        color: var(--tm-maroon);
    }
    .tugas-page .tm-stat-icon svg { width: 20px; height: 20px; }
    .tugas-page .tm-stat--warning .tm-stat-icon { background: #fff3e1; color: #ac6b14; }
    .tugas-page .tm-stat--success .tm-stat-icon { background: #e9f7ee; color: #15804b; }
    .tugas-page .tm-stat-copy { min-width: 0; }
    .tugas-page .tm-stat-label {
        display: block;
        color: var(--tm-muted);
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.45;
    }
    .tugas-page .tm-stat-value {
        display: block;
        margin-top: 2px;
        color: #261c24;
        font-size: 23px;
        line-height: 1.22;
        font-weight: 800;
        letter-spacing: -.025em;
    }
    .tugas-page .tm-stat--success .tm-stat-value { color: #176d42; }

    /* Panel filter */
    .tugas-page .tm-filter-panel {
        width: 100%;
        min-width: 0;
        margin: 0 0 17px;
        padding: 15px 17px;
        border: 1px solid var(--tm-line);
        border-radius: 14px;
        background: #fff;
    }
    .tugas-page .tm-filter {
        display: grid;
        grid-template-columns: minmax(190px, 1fr) minmax(160px, 230px) auto auto;
        gap: 9px;
        align-items: end;
        margin: 0;
    }
    .tugas-page .tm-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
    .tugas-page .tm-field label {
        color: #62545c;
        font-size: 11.5px;
        font-weight: 700;
    }
    .tugas-page .tm-field input, .tugas-page .tm-field select {
        display: block;
        width: 100%;
        min-width: 0;
        height: 39px;
        margin: 0;
        padding: 8px 12px;
        border: 1px solid #e2d3d8;
        border-radius: 9px;
        outline: none;
        background: #fff;
        color: #342a30;
        font: 500 12px/1.4 'Plus Jakarta Sans', 'Poppins', Arial, sans-serif;
        transition: border-color .15s, box-shadow .15s;
    }
    .tugas-page .tm-field input:focus, .tugas-page .tm-field select:focus {
        border-color: #b98596;
        box-shadow: 0 0 0 3px rgba(128, 11, 34, .09);
    }
    .tugas-page .tm-field input::placeholder { color: #a0949b; }
    .tugas-page .tm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 39px;
        min-width: 0;
        margin: 0;
        padding: 8px 15px;
        border: 1px solid var(--tm-maroon);
        border-radius: 9px;
        background: var(--tm-maroon);
        color: #fff;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.3;
        white-space: nowrap;
        cursor: pointer;
        transition: background-color .16s, border-color .16s;
    }
    .tugas-page .tm-btn:hover { background: var(--tm-maroon-dark); border-color: var(--tm-maroon-dark); color: #fff; }
    .tugas-page .tm-btn--outline {
        border-color: #e3cdd5;
        background: #fff;
        color: var(--tm-maroon);
    }
    .tugas-page .tm-btn--outline:hover { border-color: #bd95a2; background: var(--tm-tint); color: var(--tm-maroon); }
    .tugas-page a:focus-visible, .tugas-page button:focus-visible {
        outline: 3px solid rgba(193, 146, 69, .35);
        outline-offset: 2px;
    }

    /* Panel daftar: memadukan heading dan kartu agar tidak berjauhan */
    .tugas-page .tm-list-panel {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        padding: 0;
        margin: 0;
        border: 1px solid var(--tm-line);
        border-radius: 15px;
        background: #fff;
        overflow: hidden;
    }
    .tugas-page .tm-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px 14px;
        flex-wrap: wrap;
        padding: 17px 19px;
        border-bottom: 1px solid #f1e7e9;
    }
    .tugas-page .tm-panel-head h2 {
        margin: 0;
        color: #2e222b;
        font-size: 15.5px;
        font-weight: 800;
        letter-spacing: -.015em;
    }
    .tugas-page .tm-count {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 100px;
        background: #f8f2f4;
        color: #806d77;
        font-size: 11px;
        font-weight: 700;
    }
    /* Kartu mengalir kiri ke kanan, lalu baris berikutnya di bawah. */
    .tugas-page .tm-task-list {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        grid-auto-flow: row;
        align-items: stretch;
        gap: 13px;
        padding: 17px;
    }
    .tugas-page .tm-task-card {
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 228px;
        padding: 16px 17px;
        border: 1px solid #e9dfe3;
        border-left: 3px solid var(--tm-maroon);
        border-radius: 11px;
        background: #fff;
        transition: border-color .16s, box-shadow .16s, transform .16s;
    }
    .tugas-page .tm-task-card:hover {
        border-color: #d9b6c2;
        box-shadow: 0 8px 22px rgba(100, 20, 43, .07);
        transform: translateY(-1px);
    }
    .tugas-page .tm-task-card.tm-late { border-left-color: #c23746; }
    .tugas-page .tm-task-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 8px 12px;
    }
    .tugas-page .tm-task-heading { flex: 1 1 180px; min-width: 0; }
    .tugas-page .tm-mapel {
        display: block;
        margin-bottom: 5px;
        color: var(--tm-maroon);
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .06em;
        line-height: 1.4;
        text-transform: uppercase;
        overflow-wrap: anywhere;
    }
    .tugas-page .tm-task-title {
        margin: 0;
        color: #2c2128;
        font-size: 14.5px;
        font-weight: 800;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }
    .tugas-page .tm-task-title a { color: inherit; }
    .tugas-page .tm-task-title a:hover { color: var(--tm-maroon); }
    .tugas-page .tm-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        max-width: 100%;
        padding: 5px 9px;
        border-radius: 999px;
        background: #f1eff0;
        color: #665d63;
        font-size: 10.5px;
        font-weight: 800;
        line-height: 1.35;
        white-space: normal;
        text-align: center;
    }
    .tugas-page .tm-status.belum { background: #fff2d9; color: #986310; }
    .tugas-page .tm-status.terkumpul { background: #e6f0ff; color: #245c9e; }
    .tugas-page .tm-status.terlambat { background: #ffebed; color: #ad2635; }
    .tugas-page .tm-status.dinilai { background: #e9f7ee; color: #167445; }
    .tugas-page .tm-task-desc {
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 35px;
        margin: 9px 0 12px;
        color: #756a72;
        font-size: 12px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }
    .tugas-page .tm-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 10px;
        padding: 10px 11px;
        border-radius: 9px;
        background: #fbf7f8;
    }
    /* Baris guru tetap penuh; tanggal dan tenggat bersebelahan. */
    .tugas-page .tm-meta-item:first-child { grid-column: 1 / -1; }
    .tugas-page .tm-meta-item { min-width: 0; }
    .tugas-page .tm-meta-label {
        display: block;
        margin: 0 0 3px;
        color: #93838c;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.3;
    }
    .tugas-page .tm-meta-value {
        display: block;
        color: #493a43;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }
    .tugas-page .tm-meta-item:last-child .tm-meta-value { color: #7b2437; }
    .tugas-page .tm-task-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: auto;
        padding-top: 13px;
    }
    .tugas-page .tm-task-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .tugas-page .tm-task-actions .tm-btn { min-height: 33px; padding: 7px 11px; font-size: 11px; }
    .tugas-page .tm-score {
        display: inline-flex;
        align-items: baseline;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 8px;
        background: #eaf7ef;
        color: #176e43;
        font-size: 10.5px;
        font-weight: 700;
        white-space: nowrap;
    }
    .tugas-page .tm-score strong { font-size: 15px; font-weight: 800; }
    .tugas-page .tm-submitted {
        color: #7d7179;
        font-size: 10.5px;
        font-weight: 600;
        line-height: 1.5;
    }

    /* Halaman tanpa tugas */
    .tugas-page .tm-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 7px;
        min-height: 195px;
        justify-content: center;
        padding: 34px 20px;
        text-align: center;
    }
    .tugas-page .tm-empty-icon {
        display: grid;
        place-items: center;
        width: 53px;
        height: 53px;
        margin-bottom: 2px;
        border-radius: 15px;
        background: #faf0f2;
        color: var(--tm-maroon);
    }
    .tugas-page .tm-empty-icon svg { width: 25px; height: 25px; }
    .tugas-page .tm-empty h3 { margin: 0; color: #382a33; font-size: 14px; font-weight: 800; }
    .tugas-page .tm-empty p { margin: 0; max-width: 460px; color: var(--tm-muted); font-size: 12px; line-height: 1.65; }

    /* Responsif pada lebar konten, termasuk saat sidebar terbuka. */
    @container (max-width: 1050px) {
        /* Sidebar aktif: tetap menyamping 2 kolom, bukan langsung 1 kolom. */
        .tugas-page .tm-task-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .tugas-page .tm-task-card { min-height: auto; }
    }
    @container (max-width: 690px) {
        /* Satu kolom hanya jika ruang benar-benar sempit. */
        .tugas-page .tm-task-list { grid-template-columns: minmax(0, 1fr); }
    }
    @container (max-width: 800px) {
        .tugas-page .tm-filter { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .tugas-page .tm-field--search { grid-column: 1 / -1; }
        .tugas-page .tm-filter .tm-btn { width: 100%; }
    }
    @container (max-width: 620px) {
        .tugas-page .tm-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px; }
        .tugas-page .tm-stat { min-height: 72px; padding: 11px 12px; gap: 9px; }
        .tugas-page .tm-stat-icon { width: 34px; height: 34px; flex-basis: 34px; border-radius: 10px; }
        .tugas-page .tm-stat-icon svg { width: 18px; height: 18px; }
        .tugas-page .tm-stat-value { font-size: 20px; }
        .tugas-page .tm-stat:last-child { grid-column: 1 / -1; }
        .tugas-page .tm-head { align-items: flex-start; }
        .tugas-page .tm-head-actions { width: 100%; justify-content: flex-start; }
        .tugas-page .tm-kelompok { width: auto; }
        .tugas-page .tm-panel-head { padding: 14px; }
        .tugas-page .tm-task-list { padding: 12px; gap: 10px; }
        .tugas-page .tm-task-card { padding: 13px; }
        .tugas-page .tm-meta { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .tugas-page .tm-meta-item:first-child { grid-column: 1 / -1; }
        .tugas-page .tm-filter-panel { padding: 12px; }
    }
    @container (max-width: 420px) {
        .tugas-page .tm-back-dashboard { width: 100%; }
        .tugas-page .tm-kelompok { width: 100%; }
        .tugas-page .tm-filter { grid-template-columns: minmax(0, 1fr); }
        .tugas-page .tm-field--search { grid-column: auto; }
        .tugas-page .tm-task-footer { align-items: stretch; }
        .tugas-page .tm-task-actions { width: 100%; }
        .tugas-page .tm-task-actions .tm-btn { flex: 1 1 auto; }
        .tugas-page .tm-score, .tugas-page .tm-submitted { align-self: flex-start; }
    }
</style>

@php
    // Selaras dengan route yang digunakan Dashboard Mahasiswa sendiri
    // (misalnya pada navigasi kalender: route('dashboard', [...])).
    $urlDashboardMahasiswa = route('dashboard');
@endphp
<div class="tugas-page">
    <nav class="tm-breadcrumb" aria-label="Navigasi halaman">
        <a href="{{ $urlDashboardMahasiswa }}">Dashboard</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Tugas Mahasiswa</span>
    </nav>
    <header class="tm-head">
        <div class="tm-head-copy">
            <h1>Tugas Mahasiswa</h1>
            <p>Daftar tugas dari Guru yang ditujukan kepada kelompok pembelajaran Anda.</p>
        </div>
        <div class="tm-head-actions">
            <a class="tm-back-dashboard" href="{{ $urlDashboardMahasiswa }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/><path d="M9 12h12"/></svg>
                <span>Kembali ke Dashboard</span>
            </a>
            <div class="tm-kelompok" aria-label="Kelompok pembelajaran">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="9" cy="8" r="3"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 5v1"/>
                </svg>
                <span>Kelompok: {{ $kelompok }}</span>
            </div>
        </div>
    </header>

    <div class="tm-stat-grid" aria-label="Ringkasan tugas">
        <div class="tm-stat">
            <span class="tm-stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
            </span>
            <div class="tm-stat-copy">
                <span class="tm-stat-label">Total Tugas</span>
                <strong class="tm-stat-value">{{ $statistik['total'] }}</strong>
            </div>
        </div>
        <div class="tm-stat tm-stat--warning">
            <span class="tm-stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <div class="tm-stat-copy">
                <span class="tm-stat-label">Belum Dikumpulkan</span>
                <strong class="tm-stat-value">{{ $statistik['belum'] }}</strong>
            </div>
        </div>
        <div class="tm-stat tm-stat--success">
            <span class="tm-stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg>
            </span>
            <div class="tm-stat-copy">
                <span class="tm-stat-label">Sudah Dinilai</span>
                <strong class="tm-stat-value">{{ $statistik['dinilai'] }}</strong>
            </div>
        </div>
    </div>

    <section class="tm-filter-panel" aria-label="Pencarian dan filter tugas">
        <form method="GET" action="{{ route('tugas.mhs.index') }}" class="tm-filter">
            <div class="tm-field tm-field--search">
                <label for="tmCariTugas">Cari tugas</label>
                <input id="tmCariTugas" type="search" name="cari" value="{{ $filter['cari'] ?? '' }}" placeholder="Cari berdasarkan judul tugas..." autocomplete="off">
            </div>
            <div class="tm-field">
                <label for="tmStatusTugas">Status tugas</label>
                <select id="tmStatusTugas" name="status">
                    <option value="">Semua Status</option>
                    @foreach ($statusPilihan as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(($filter['status'] ?? '') === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="tm-btn">Terapkan</button>
            @if (filled($filter['cari'] ?? null) || filled($filter['status'] ?? null))
                <a href="{{ route('tugas.mhs.index') }}" class="tm-btn tm-btn--outline">Reset</a>
            @endif
        </form>
    </section>

    <section class="tm-list-panel" aria-labelledby="tmDaftarTugasTitle">
        <div class="tm-panel-head">
            <h2 id="tmDaftarTugasTitle">Daftar Tugas</h2>
            <span class="tm-count">{{ $daftarTugas->count() }} tugas ditampilkan</span>
        </div>

        @if ($daftarTugas->isEmpty())
            <div class="tm-empty">
                <span class="tm-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/></svg>
                </span>
                <h3>Belum ada tugas</h3>
                <p>
                    @if (filled($filter['cari'] ?? null) || filled($filter['status'] ?? null))
                        Tidak ada tugas yang cocok dengan pencarian atau filter Anda.
                    @else
                        Tugas dari Guru untuk kelompok {{ $kelompok }} akan tampil di halaman ini.
                    @endif
                </p>
            </div>
        @else
            <div class="tm-task-list">
                @foreach ($daftarTugas as $item)
                    @php
                        $tugas = $item['tugas'];
                        $pengumpulan = $item['pengumpulan'];
                        $status = $item['status'];
                    @endphp
                    <article class="tm-task-card {{ $status['kelas'] === 'terlambat' ? 'tm-late' : '' }}">
                        <div class="tm-task-top">
                            <div class="tm-task-heading">
                                <span class="tm-mapel">{{ $tugas->namaMataPelajaran() }}</span>
                                <h3 class="tm-task-title">
                                    <a href="{{ route('tugas.mhs.show', $tugas) }}">{{ $tugas->judul }}</a>
                                </h3>
                            </div>
                            <span class="tm-status {{ $status['kelas'] }}">{{ $status['label'] }}</span>
                        </div>

                        <p class="tm-task-desc">{{ \Illuminate\Support\Str::limit(strip_tags($tugas->deskripsi), 140) }}</p>

                        <div class="tm-meta">
                            <div class="tm-meta-item">
                                <span class="tm-meta-label">Guru</span>
                                <strong class="tm-meta-value">{{ $tugas->namaGuru() }}</strong>
                            </div>
                            <div class="tm-meta-item">
                                <span class="tm-meta-label">Diberikan</span>
                                <strong class="tm-meta-value">{{ $tugas->tanggalMulaiTeks() }}</strong>
                            </div>
                            <div class="tm-meta-item">
                                <span class="tm-meta-label">Batas Pengumpulan</span>
                                <strong class="tm-meta-value">{{ $tugas->batasTeks() }}</strong>
                            </div>
                        </div>

                        <div class="tm-task-footer">
                            <div class="tm-task-actions">
                                <a href="{{ route('tugas.mhs.show', $tugas) }}" class="tm-btn">
                                    Lihat Detail
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </a>
                                @if ($tugas->punyaLampiran())
                                    <a href="{{ route('tugas.mhs.lampiran', $tugas) }}" class="tm-btn tm-btn--outline">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6"/><path d="m7 10 5 5 5-5M12 15V3"/></svg>
                                        Lampiran
                                    </a>
                                @endif
                            </div>
                            @if ($pengumpulan && $pengumpulan->nilaiDipublikasikan())
                                <span class="tm-score">Nilai <strong>{{ $pengumpulan->nilai }}</strong></span>
                            @elseif ($pengumpulan)
                                <span class="tm-submitted">Dikumpulkan {{ $pengumpulan->dikumpulkanTeks() }}</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
