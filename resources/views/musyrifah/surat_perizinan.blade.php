@extends('layouts.musyrifah')
@section('title', 'Surat & Perizinan')
@section('content')
@php
    // Hanya presentasi. Pembatasan akses gedung dan lantai tetap diputuskan di controller.
    $petugasPerizinan = auth()->user();
    $gedungWilayah = trim((string) ($petugasPerizinan?->gedung_penempatan ?? ''));
    $lantaiWilayah = (int) ($petugasPerizinan?->lantai_penempatan ?? 0);

    $jumlahHasil = $pengajuans->count();
    $jumlahDisetujui = $pengajuans->where('status', \App\Models\SuratPerizinan::STATUS_DISETUJUI)->count();
    $jumlahPemakaiKuota = $kuotaMonitoring->count();
    $filterAktif = collect(['jenis', 'status', 'nama', 'periode_dari', 'periode_sampai'])
        ->contains(fn (string $kunci) => filled($filter[$kunci] ?? null));
@endphp

<style>
    /* Seluruh CSS khusus halaman Musyrifah Surat & Perizinan.
       Bahasa visual diambil langsung dari Manajemen Data Kamar SIMAHAD. */
    #simahad-perizinan {
        --sp-wine: #800b22;
        --sp-wine-deep: #620819;
        --sp-ink: #292427;
        --sp-muted: #82797e;
        --sp-border: #eddee1;
        --sp-soft: #fbf5f7;
        display: block !important;
        position: relative;
        float: none !important;
        clear: both;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0;
        margin: 0 !important;
        padding: 0 0 28px;
        color: var(--sp-ink);
        font-family: inherit;
        font-size: 13px;
        line-height: 1.55;
        container: perizinan-layout / inline-size;
    }
    #simahad-perizinan *, #simahad-perizinan *::before, #simahad-perizinan *::after { box-sizing: border-box; }
    #simahad-perizinan a { text-decoration: none; }
    #simahad-perizinan svg { width: 18px; height: 18px; display: block; flex-shrink: 0; }
    #simahad-perizinan button, #simahad-perizinan input, #simahad-perizinan select { font-family: inherit; }

    /* Header: proporsi, breadcrumb, dan garis bawah sama dengan Data Kamar. */
    #simahad-perizinan .sp-heading {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px 18px;
        width: 100%;
        margin: 0 0 18px;
        padding: 0 0 17px;
        border-bottom: 1px solid #e8dde1;
    }
    #simahad-perizinan .sp-heading-main { flex: 1 1 360px; min-width: 0; }
    #simahad-perizinan .sp-breadcrumb { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; margin: 0 0 9px; color: #8e8487; font-size: 11.5px; }
    #simahad-perizinan .sp-breadcrumb a { color: var(--sp-wine); }
    #simahad-perizinan .sp-breadcrumb a:hover { text-decoration: underline; }
    #simahad-perizinan .sp-title { margin: 0 0 5px; color: #282126; font-size: clamp(22px, 2.7vw, 28px); font-weight: 750; letter-spacing: -.45px; line-height: 1.3; }
    #simahad-perizinan .sp-subtitle { margin: 0; max-width: 760px; color: #797076; font-size: 12.5px; line-height: 1.6; }
    #simahad-perizinan .sp-wilayah {
        display: inline-flex; align-items: center; gap: 8px;
        max-width: 100%; min-height: 41px; padding: 9px 14px;
        border: 1px solid #e7d5da; border-radius: 9px;
        background: #fff; color: var(--sp-wine);
        font-size: 11.5px; font-weight: 700; line-height: 1.4;
        overflow-wrap: anywhere;
    }
    #simahad-perizinan .sp-wilayah svg { width: 16px; height: 16px; }

    /* Alerts memakai warna dan ruang yang sama dengan Data Kamar. */
    #simahad-perizinan .sp-alert { margin: 0 0 16px; padding: 13px 16px; border: 1px solid #bde8cb; border-radius: 10px; background: #eefbf2; color: #176437; font-size: 12px; line-height: 1.6; }
    #simahad-perizinan .sp-alert--error { border-color: #f2c6d0; background: #fff1f3; color: #a0223d; }
    #simahad-perizinan .sp-alert ul { margin: 6px 0 0; padding-left: 18px; }

    /* Empat kartu ringkasan dengan ikon kiri: sesuai pola Data Kamar. */
    #simahad-perizinan .sp-stats {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px; margin: 0 0 18px !important; padding: 0 !important;
        width: 100%; min-width: 0;
    }
    #simahad-perizinan .sp-stat {
        display: flex; align-items: center; gap: 12px;
        min-height: 100px; min-width: 0; padding: 16px 17px;
        border: 1px solid #eae4e8; border-radius: 12px; background: #fff;
        box-shadow: 0 2px 7px rgba(34, 25, 30, .025);
        transition: border-color .16s ease, box-shadow .16s ease;
    }
    #simahad-perizinan .sp-stat:hover { border-color: #d9c6cc; box-shadow: 0 5px 15px rgba(43, 18, 25, .055); }
    #simahad-perizinan .sp-stat-icon { width: 44px; height: 44px; flex: 0 0 44px; display: grid; place-items: center; border-radius: 12px; background: #f7e9ed; color: var(--sp-wine); }
    #simahad-perizinan .sp-stat-icon svg { width: 22px; height: 22px; }
    #simahad-perizinan .sp-stat:nth-child(2) .sp-stat-icon { background: #fdf2de; color: #a96c10; }
    #simahad-perizinan .sp-stat:nth-child(3) .sp-stat-icon { background: #e9f2ff; color: #346ba4; }
    #simahad-perizinan .sp-stat:nth-child(4) .sp-stat-icon { background: #e8f8ef; color: #1e7849; }
    #simahad-perizinan .sp-stat-info { min-width: 0; }
    #simahad-perizinan .sp-stat-title { display: block; margin: 0 0 3px; color: #716874; font-size: 11.5px; font-weight: 600; line-height: 1.3; }
    #simahad-perizinan .sp-stat-value { display: block; color: #292427; font-size: 25px; font-weight: 750; line-height: 1.2; font-variant-numeric: tabular-nums; }
    #simahad-perizinan .sp-stat-sub { display: block; margin-top: 4px; color: #93888e; font-size: 10.5px; line-height: 1.5; }

    /* Lingkup kewenangan: catatan kecil, tidak menjadi blok besar. */
    #simahad-perizinan .sp-scope {
        display: flex; align-items: flex-start; gap: 9px;
        padding: 11px 14px; margin: 0 0 18px;
        border: 1px solid #efe4e7; border-radius: 9px;
        background: #fcf9fa; color: #756a70; font-size: 11.5px; line-height: 1.6;
    }
    #simahad-perizinan .sp-scope svg { width: 16px; height: 16px; margin-top: 1px; color: var(--sp-wine); }
    #simahad-perizinan .sp-scope strong { color: var(--sp-wine); }

    /* Panel mirip kotak Pengajuan Kamar / Daftar Kamar. */
    #simahad-perizinan .sp-panel {
        display: block !important;
        float: none !important; clear: both !important;
        width: 100% !important; max-width: 100%; min-width: 0;
        margin: 0 0 18px !important; padding: 0 !important;
        border: 1px solid #eae4e8; border-radius: 12px;
        background: #fff; box-shadow: 0 2px 9px rgba(44, 21, 28, .025);
        overflow: hidden;
    }
    #simahad-perizinan .sp-panel-head {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px 16px;
        width: 100%; margin: 0; padding: 17px 20px;
        border-bottom: 1px solid #f0e8e9;
    }
    #simahad-perizinan .sp-panel-head > div { min-width: 0; flex: 1 1 280px; }
    #simahad-perizinan .sp-panel-title { margin: 0 0 4px; color: #30252a; font-size: 15.5px; font-weight: 750; }
    #simahad-perizinan .sp-panel-desc { margin: 0; max-width: 900px; color: #786f78; font-size: 11.75px; line-height: 1.65; }
    #simahad-perizinan .sp-panel-badge { display: inline-flex; align-items: center; flex: 0 0 auto; gap: 6px; padding: 6px 12px; border: 1px solid #f2e0ba; border-radius: 20px; background: #fff6e5; color: #956015; font-size: 11px; font-weight: 700; }
    #simahad-perizinan .sp-panel-badge::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    #simahad-perizinan .sp-panel-badge--neutral { border-color: #e9dbe0; background: #faf6f8; color: #795168; }
    #simahad-perizinan .sp-panel-badge--empty { border-color: #cdebd5; background: #eaf8ef; color: #187044; }

    /* Filter serupa deretan pencarian dan select di Daftar Kamar. */
    #simahad-perizinan .sp-filters { display: flex; align-items: flex-end; flex-wrap: wrap; gap: 10px; padding: 14px 20px 17px; }
    #simahad-perizinan .sp-field { flex: 1 1 145px; min-width: 125px; }
    #simahad-perizinan .sp-field--search { flex: 2 1 240px; min-width: 190px; }
    #simahad-perizinan .sp-field--date { flex: 0 1 155px; min-width: 128px; }
    #simahad-perizinan .sp-label { display: block; margin-bottom: 6px; color: #746771; font-size: 11px; font-weight: 700; }
    #simahad-perizinan .sp-input, #simahad-perizinan .sp-select {
        display: block; width: 100%; min-width: 0; height: 41px; padding: 0 12px;
        border: 1px solid #e5dfe4; border-radius: 8px; background: #fff;
        color: #41373b; font-size: 12px; outline: none;
    }
    #simahad-perizinan .sp-input::placeholder { color: #a3979d; }
    #simahad-perizinan .sp-input:focus, #simahad-perizinan .sp-select:focus { border-color: #b47989; box-shadow: 0 0 0 3px rgba(128, 11, 34, .07); }
    #simahad-perizinan .sp-filter-actions { display: flex; align-items: center; flex: 0 0 auto; flex-wrap: wrap; gap: 8px; }
    #simahad-perizinan .sp-btn {
        display: inline-flex; justify-content: center; align-items: center; gap: 7px;
        min-height: 41px; padding: 9px 14px;
        border: 1px solid #e6d8dc; border-radius: 8px;
        background: #fff; color: #754354; font-size: 11.5px; font-weight: 700;
        white-space: nowrap; cursor: pointer; text-align: center;
        transition: background .16s ease, box-shadow .16s ease;
    }
    #simahad-perizinan .sp-btn:hover { background: #fbf1f4; }
    #simahad-perizinan .sp-btn--primary { background: var(--sp-wine); border-color: var(--sp-wine); color: #fff; }
    #simahad-perizinan .sp-btn--primary:hover { background: var(--sp-wine-deep); color: #fff; }
    #simahad-perizinan .sp-btn svg { width: 15px; height: 15px; }
    #simahad-perizinan .sp-filter-note { margin: -2px 0 0; padding: 0 20px 14px; color: #8f858a; font-size: 11px; }
    #simahad-perizinan .sp-filter-note strong { color: var(--sp-wine); }

    /* Tabel desktop setara Pengajuan Kamar: header maroon, sel lebih rapat. */
    #simahad-perizinan .sp-table-wrap { display: block !important; width: 100% !important; max-width: 100%; min-width: 0; overflow-x: auto; overscroll-behavior-inline: contain; }
    #simahad-perizinan .sp-table { width: 100%; min-width: 940px; border-collapse: separate; border-spacing: 0; background: #fff; }
    #simahad-perizinan .sp-table--quota { min-width: 620px; }
    #simahad-perizinan .sp-table thead th { padding: 12px 13px; border: 0; background: var(--sp-wine); color: #fff; font-size: 11.5px; font-weight: 650; text-align: left; white-space: nowrap; }
    #simahad-perizinan .sp-table tbody td { padding: 12px 13px; border-bottom: 1px solid #f1e8eb; color: #534c50; font-size: 12px; line-height: 1.55; vertical-align: middle; }
    #simahad-perizinan .sp-table tbody tr:nth-child(even) { background: #fdfafb; }
    #simahad-perizinan .sp-table tbody tr:hover { background: #fcf4f6; }
    #simahad-perizinan .sp-table tbody tr:last-child td { border-bottom: none; }
    #simahad-perizinan .sp-table tbody td strong { color: #30282d; font-weight: 700; }
    #simahad-perizinan .sp-table .sp-muted { color: #93888e; font-size: 11.5px; }
    #simahad-perizinan .sp-table .sp-name { font-weight: 700; color: #30282d; }
    #simahad-perizinan .sp-table .sp-destination { max-width: 180px; overflow-wrap: anywhere; }
    #simahad-perizinan .sp-table .sp-date { white-space: nowrap; }
    #simahad-perizinan .sp-badge { display: inline-flex; align-items: center; padding: 5px 10px; border-radius: 20px; font-size: 10.5px; font-weight: 700; white-space: nowrap; }
    #simahad-perizinan .sp-badge--menunggu, #simahad-perizinan .sp-badge--perbaikan { background: #fff3d9; color: #936113; }
    #simahad-perizinan .sp-badge--disetujui { background: #e4f8ec; color: #14783c; }
    #simahad-perizinan .sp-badge--ditolak { background: #ffebee; color: #a52443; }
    #simahad-perizinan .sp-badge--selesai { background: #eaf1ff; color: #245fa9; }
    #simahad-perizinan .sp-badge--dibatalkan { background: #f0eff1; color: #655d64; }
    #simahad-perizinan .sp-detail { display: inline-flex; align-items: center; justify-content: center; min-height: 33px; padding: 7px 12px; border: 1px solid #dfcfd4; border-radius: 8px; background: #fff; color: var(--sp-wine); font-size: 11.5px; font-weight: 700; white-space: nowrap; }
    #simahad-perizinan .sp-detail:hover { background: #fdf4f6; }
    #simahad-perizinan .sp-quota-ok { color: #168047; font-weight: 700; }
    #simahad-perizinan .sp-quota-full { color: #a52443; font-weight: 700; }
    #simahad-perizinan .sp-empty { padding: 22px 16px !important; color: #857a7d; text-align: center; font-size: 11.5px; }
    #simahad-perizinan .sp-panel-footer { padding: 12px 20px; border-top: 1px solid #f1e8eb; color: #93888e; font-size: 11px; line-height: 1.55; }

    /* Responsif BERDASARKAN LEBAR KONTEN (sidebar bisa buka/tutup). */
    @container perizinan-layout (max-width: 1060px) {
        .sp-stats { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    }
    @container perizinan-layout (max-width: 790px) {
        .sp-heading { align-items: flex-start !important; }
        .sp-panel-head { padding: 15px 16px !important; }
        .sp-filters { padding: 14px 16px !important; }
        .sp-filter-note { padding: 0 16px 14px !important; }
        .sp-field--search { flex-basis: 100%; }
        .sp-field { flex-grow: 1; }
        .sp-field--date { flex-grow: 1; }
        .sp-filter-actions { flex: 1 1 100%; justify-content: flex-end; }
    }
    @container perizinan-layout (max-width: 680px) {
        .sp-heading { gap: 12px !important; }
        .sp-wilayah { width: 100%; justify-content: center; }
        .sp-stats { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 10px !important; }
        .sp-stat { min-height: 90px !important; padding: 13px !important; gap: 9px !important; }
        .sp-stat-icon { width: 38px !important; height: 38px !important; flex: 0 0 38px !important; }
        .sp-stat-value { font-size: 23px !important; }
        .sp-table-wrap { overflow: visible !important; }
        .sp-table, .sp-table--quota { display: block; min-width: 0 !important; width: 100% !important; }
        .sp-table thead { display: none; }
        .sp-table tbody { display: block; }
        .sp-table tbody tr:not(.sp-empty-row) { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px 12px; margin: 10px 14px; padding: 13px; border: 1px solid #e9dfe3; border-radius: 10px; background: #fff !important; }
        .sp-table tbody tr:not(.sp-empty-row) td { display: block; width: auto; min-width: 0; padding: 0 !important; border: 0 !important; white-space: normal !important; overflow-wrap: anywhere; }
        .sp-table tbody tr:not(.sp-empty-row) td::before { content: attr(data-label); display: block; margin-bottom: 3px; color: #8a7e87; font-size: 10px; font-weight: 700; }
        .sp-table--permits tbody td:nth-child(2),
        .sp-table--permits tbody td:nth-child(4),
        .sp-table--permits tbody td:nth-child(7),
        .sp-table--permits tbody td:last-child,
        .sp-table--quota tbody td:nth-child(2) { grid-column: 1 / -1; }
        .sp-table--permits .sp-detail { width: 100%; }
        .sp-table tbody tr.sp-empty-row { display: block; padding: 0; margin: 0; }
        .sp-table tbody tr.sp-empty-row td { display: block; padding: 22px 15px !important; }
    }
    @container perizinan-layout (max-width: 440px) {
        .sp-stats { grid-template-columns: minmax(0, 1fr) !important; }
        .sp-stat { min-height: 79px !important; }
        .sp-filters { flex-direction: column; align-items: stretch; }
        .sp-field, .sp-field--search, .sp-field--date, .sp-filter-actions { flex: 0 0 auto; min-width: 0; width: 100%; }
        .sp-filter-actions > * { flex: 1 1 0; }
        .sp-table tbody tr:not(.sp-empty-row) { grid-template-columns: minmax(0, 1fr); }
        .sp-table tbody tr:not(.sp-empty-row) td { grid-column: 1 / -1; }
    }
    @media (prefers-reduced-motion: reduce) {
        #simahad-perizinan .sp-stat, #simahad-perizinan .sp-btn { transition: none; }
    }
    #simahad-perizinan a:focus-visible,
    #simahad-perizinan button:focus-visible,
    #simahad-perizinan input:focus-visible,
    #simahad-perizinan select:focus-visible { outline: 2px solid #a6526c; outline-offset: 2px; }
</style>

<div id="simahad-perizinan">
    <header class="sp-heading">
        <div class="sp-heading-main">
            <nav class="sp-breadcrumb" aria-label="Navigasi halaman">
                <a href="{{ route('musyrifah.dashboard') }}">Dashboard</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Surat &amp; Perizinan</span>
            </nav>
            <h1 class="sp-title">Surat &amp; Perizinan</h1>
            <p class="sp-subtitle">Pantau surat izin mahasiswa, periksa pengajuan, dan monitoring kuota izin pulang di wilayah penugasan Anda.</p>
        </div>
        <div class="sp-wilayah" role="note" aria-label="Wilayah penugasan Musyrifah">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M10 21v-4h4v4"/></svg>
            <span>{{ $gedungWilayah !== '' ? 'Gedung '.$gedungWilayah : 'Gedung belum ditetapkan' }} &bull; {{ $lantaiWilayah > 0 ? 'Lantai '.$lantaiWilayah : 'Lantai belum ditetapkan' }}</span>
        </div>
    </header>

    @if (session('success'))
        <div class="sp-alert" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="sp-alert sp-alert--error" role="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="sp-alert sp-alert--error" role="alert">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul>
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="sp-stats" aria-label="Ringkasan surat perizinan">
        <div class="sp-stat">
            <span class="sp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v5h5M9 12h6M9 16h6"/></svg></span>
            <div class="sp-stat-info"><span class="sp-stat-title">Total Pengajuan</span><strong class="sp-stat-value">{{ number_format($jumlahHasil, 0, ',', '.') }}</strong><span class="sp-stat-sub">Sesuai hasil filter</span></div>
        </div>
        <div class="sp-stat">
            <span class="sp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
            <div class="sp-stat-info"><span class="sp-stat-title">Perlu Tindakan</span><strong class="sp-stat-value">{{ number_format((int) $jumlahMenunggu, 0, ',', '.') }}</strong><span class="sp-stat-sub">Menunggu &amp; perlu perbaikan</span></div>
        </div>
        <div class="sp-stat">
            <span class="sp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg></span>
            <div class="sp-stat-info"><span class="sp-stat-title">Disetujui</span><strong class="sp-stat-value">{{ number_format($jumlahDisetujui, 0, ',', '.') }}</strong><span class="sp-stat-sub">Pengajuan hasil filter</span></div>
        </div>
        <div class="sp-stat">
            <span class="sp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18M8 15h8"/></svg></span>
            <div class="sp-stat-info"><span class="sp-stat-title">Pemakai Kuota</span><strong class="sp-stat-value">{{ number_format($jumlahPemakaiKuota, 0, ',', '.') }}</strong><span class="sp-stat-sub">{{ $bulanBerjalan }}</span></div>
        </div>
    </section>

    <section class="sp-panel" aria-labelledby="sp-filter-heading">
        <div class="sp-panel-head">
            <div><h2 class="sp-panel-title" id="sp-filter-heading">Filter &amp; Pencarian</h2><p class="sp-panel-desc">Cari berdasarkan mahasiswa, jenis izin, status, dan periode pengajuan.</p></div>
            @if ($filterAktif)
                <span class="sp-panel-badge sp-panel-badge--neutral">Filter aktif</span>
            @endif
        </div>
        <form method="GET" action="{{ route('musyrifah.surat_perizinan') }}" class="sp-filters">
            <div class="sp-field sp-field--search">
                <label class="sp-label" for="spCari">Nama / NIM</label>
                <input class="sp-input" type="search" id="spCari" name="nama" value="{{ $filter['nama'] ?? '' }}" placeholder="Cari nama atau NIM mahasiswa...">
            </div>
            <div class="sp-field">
                <label class="sp-label" for="spJenis">Jenis Izin</label>
                <select class="sp-select" name="jenis" id="spJenis">
                    <option value="">Semua jenis</option>
                    @foreach ($jenisPilihan as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(($filter['jenis'] ?? '') === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sp-field">
                <label class="sp-label" for="spStatus">Status</label>
                <select class="sp-select" name="status" id="spStatus">
                    <option value="">Semua status</option>
                    @foreach ($statusPilihan as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(($filter['status'] ?? '') === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sp-field sp-field--date">
                <label class="sp-label" for="spDari">Periode Mulai</label>
                <input class="sp-input" type="date" id="spDari" name="periode_dari" value="{{ $filter['periode_dari'] ?? '' }}">
            </div>
            <div class="sp-field sp-field--date">
                <label class="sp-label" for="spSampai">Periode Sampai</label>
                <input class="sp-input" type="date" id="spSampai" name="periode_sampai" value="{{ $filter['periode_sampai'] ?? '' }}">
            </div>
            <div class="sp-filter-actions">
                <button type="submit" class="sp-btn sp-btn--primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 4 4"/></svg>
                    Terapkan
                </button>
                <a href="{{ route('musyrifah.surat_perizinan') }}" class="sp-btn">Reset</a>
            </div>
        </form>
        <p class="sp-filter-note">Menampilkan <strong>{{ number_format($jumlahHasil, 0, ',', '.') }}</strong> pengajuan berdasarkan filter dan wilayah penempatan.</p>
    </section>

    <section class="sp-panel" aria-labelledby="sp-list-heading">
        <div class="sp-panel-head">
            <div><h2 class="sp-panel-title" id="sp-list-heading">Daftar Pengajuan Mahasiswa</h2><p class="sp-panel-desc">Periksa dokumen dan proses surat perizinan mahasiswa di wilayah Anda.</p></div>
            <span class="sp-panel-badge {{ $jumlahMenunggu > 0 ? '' : 'sp-panel-badge--empty' }}">{{ number_format((int) $jumlahMenunggu, 0, ',', '.') }} perlu tindakan</span>
        </div>
        <div class="sp-table-wrap">
            <table class="sp-table sp-table--permits">
                <thead><tr>
                    <th scope="col">No.</th><th scope="col">Mahasiswa</th><th scope="col">NIM</th><th scope="col">Jenis Izin</th>
                    <th scope="col">Tanggal Ajuan</th><th scope="col">Periode</th><th scope="col">Tujuan / Lokasi</th><th scope="col">Status</th><th scope="col">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse ($pengajuans as $indeks => $izin)
                        <tr>
                            <td data-label="No.">{{ $indeks + 1 }}</td>
                            <td data-label="Mahasiswa"><span class="sp-name">{{ $izin->user?->name ?? '-' }}</span></td>
                            <td data-label="NIM">{{ $izin->user?->nim ?: '-' }}</td>
                            <td data-label="Jenis Izin">{{ $izin->labelJenis() }}</td>
                            <td data-label="Tanggal Ajuan" class="sp-date">{{ $izin->created_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</td>
                            <td data-label="Periode">{{ $izin->periodeTeks() }}</td>
                            <td data-label="Tujuan / Lokasi" class="sp-destination">{{ $izin->tujuanTeks() }}</td>
                            <td data-label="Status"><span class="sp-badge sp-badge--{{ $izin->kelasStatus() }}">{{ $izin->labelStatus() }}</span></td>
                            <td data-label="Aksi"><a class="sp-detail" href="{{ route('musyrifah.surat_perizinan.show', $izin) }}" aria-label="Lihat detail surat mahasiswa {{ $izin->user?->name ?? 'mahasiswa' }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr class="sp-empty-row"><td colspan="9" class="sp-empty">Tidak ada pengajuan yang sesuai filter pada wilayah penugasan Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="sp-panel-footer">Klik Detail untuk memeriksa dokumen, riwayat pengajuan, dan memberikan keputusan sesuai kewenangan.</div>
    </section>

    <section class="sp-panel" aria-labelledby="sp-kuota-heading">
        <div class="sp-panel-head">
            <div><h2 class="sp-panel-title" id="sp-kuota-heading">Monitoring Kuota Izin Pulang</h2><p class="sp-panel-desc">Pemakaian kuota mahasiswa pada bulan {{ $bulanBerjalan }}.</p></div>
            <span class="sp-panel-badge sp-panel-badge--neutral">{{ number_format($jumlahPemakaiKuota, 0, ',', '.') }} mahasiswa</span>
        </div>
        <div class="sp-table-wrap">
            <table class="sp-table sp-table--quota">
                <thead><tr><th scope="col">No.</th><th scope="col">Nama Mahasiswa</th><th scope="col">NIM</th><th scope="col">Terpakai</th><th scope="col">Sisa Kuota</th></tr></thead>
                <tbody>
                    @forelse ($kuotaMonitoring as $indeks => $baris)
                        <tr>
                            <td data-label="No.">{{ $indeks + 1 }}</td>
                            <td data-label="Mahasiswa"><span class="sp-name">{{ $baris['nama'] }}</span></td>
                            <td data-label="NIM">{{ $baris['nim'] }}</td>
                            <td data-label="Terpakai">{{ $baris['terpakai'] }} / {{ \App\Models\SuratPerizinan::KUOTA_PULANG_PER_BULAN }}</td>
                            <td data-label="Sisa Kuota">
                                @if ((int) $baris['sisa'] > 0)
                                    <span class="sp-quota-ok">{{ $baris['sisa'] }} slot tersedia</span>
                                @else
                                    <span class="sp-quota-full">Kuota habis</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="sp-empty-row"><td colspan="5" class="sp-empty">Belum ada pemakaian kuota izin pulang pada bulan ini di wilayah penugasan Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="sp-panel-footer">Kuota dihitung dari data surat pulang mingguan yang memenuhi aturan kuota pada bulan berjalan.</div>
    </section>
</div>
@endsection
