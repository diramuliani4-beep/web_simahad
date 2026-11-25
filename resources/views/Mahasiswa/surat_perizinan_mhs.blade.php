@extends('layouts.mahasiswa')

@section('title', 'Surat & Perizinan - SIMAHAD')

@section('content')
@php
    // Semua angka dan riwayat tetap menggunakan data asli dari controller.
    $kuotaMaksimal = max(0, (int) ($kuotaPulang['maksimal'] ?? 0));
    $kuotaTerpakai = max(0, (int) ($kuotaPulang['terpakai'] ?? 0));
    $kuotaSisa = max(0, (int) ($kuotaPulang['sisa'] ?? 0));
    $kuotaPersen = $kuotaMaksimal > 0
        ? min(100, round(($kuotaTerpakai / $kuotaMaksimal) * 100))
        : 0;
@endphp

<style>
    /* SIMAHAD — Surat & Perizinan Mahasiswa. Visual mengikuti Data Kamar Musyrifah. */
    .spm-page {
        --spm-wine: #800b22;
        --spm-wine-dark: #620819;
        --spm-ink: #292427;
        --spm-muted: #82797e;
        --spm-border: #eddee1;
        --spm-soft: #fbf5f7;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        margin: 0;
        padding: 0 0 28px;
        font-family: inherit;
        font-size: 13px;
        color: var(--spm-ink);
        container-type: inline-size;
    }
    .spm-page *, .spm-page *::before, .spm-page *::after { box-sizing: border-box; }
    .spm-page a { text-decoration: none; }
    .spm-page button { font-family: inherit; cursor: pointer; }
    .spm-page svg { display: block; width: 20px; height: 20px; }
    .spm-page button:focus-visible, .spm-page a:focus-visible, .spm-modal button:focus-visible,
    .spm-modal a:focus-visible { outline: 2px solid #a6526c; outline-offset: 3px; }

    /* Header: mengikuti judul, breadcrumb dan pembatas Data Kamar. */
    .spm-page .spm-header {
        display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap;
        gap: 14px; margin: 0 0 22px; padding: 0 0 19px; border-bottom: 1px solid #e8cfd6;
    }
    .spm-page .spm-breadcrumb { display: flex; flex-wrap: wrap; align-items: center; gap: 7px; margin-bottom: 11px; font-size: 12px; color: #8e8487; }
    .spm-page .spm-breadcrumb a { color: var(--spm-wine); }
    .spm-page .spm-breadcrumb a:hover { text-decoration: underline; }
    .spm-page .spm-header h1 { margin: 0 0 6px; font-size: clamp(22px, 2.5vw, 27px); font-weight: 750; line-height: 1.3; letter-spacing: -.55px; color: #282126; }
    .spm-page .spm-subtitle { max-width: 780px; margin: 0; font-size: 12.5px; line-height: 1.7; color: #797076; }
    .spm-page .spm-header-link {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        flex: 0 0 auto; min-height: 42px; padding: 9px 16px; border: 1px solid var(--spm-wine);
        border-radius: 9px; background: var(--spm-wine); color: #fff; font-size: 12px; font-weight: 700;
    }
    .spm-page .spm-header-link:hover { background: var(--spm-wine-dark); color: #fff; }
    .spm-page .spm-header-link svg { width: 16px; height: 16px; }

    .spm-page .spm-alert { margin-bottom: 16px; padding: 13px 16px; border-radius: 10px; border: 1px solid; font-size: 12px; line-height: 1.6; }
    .spm-page .spm-alert--success { color: #176437; background: #eefbf2; border-color: #bde8cb; }
    .spm-page .spm-alert--error { color: #a0223d; background: #fff1f3; border-color: #f2c6d0; }
    .spm-page .spm-alert ul { margin: 5px 0 0 18px; padding: 0; }

    /* Empat kartu horizontal seperti ringkasan Data Kamar. */
    .spm-page .spm-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 13px; margin-bottom: 20px; }
    .spm-page .spm-stat {
        display: flex; align-items: center; gap: 13px; min-width: 0; min-height: 96px;
        padding: 17px 18px; border: 1px solid var(--spm-border); border-radius: 12px;
        background: #fff; box-shadow: 0 3px 12px rgba(57, 19, 30, .035);
    }
    .spm-page .spm-stat-icon { display: grid; place-items: center; flex: 0 0 44px; width: 44px; height: 44px; border-radius: 11px; background: #f7e9ed; color: var(--spm-wine); }
    .spm-page .spm-stat-icon svg { width: 22px; height: 22px; }
    .spm-page .spm-stat:nth-child(2) .spm-stat-icon { background: #e8f8ef; color: #1e7849; }
    .spm-page .spm-stat:nth-child(3) .spm-stat-icon { background: #fff0f2; color: #ab3148; }
    .spm-page .spm-stat:nth-child(4) .spm-stat-icon { background: #e9f2ff; color: #346ba4; }
    .spm-page .spm-stat-info { min-width: 0; }
    .spm-page .spm-stat-label { display: block; margin-bottom: 3px; color: #81777d; font-size: 11px; font-weight: 600; }
    .spm-page .spm-stat-number { display: block; color: var(--spm-ink); font-size: 24px; font-weight: 750; line-height: 1.2; font-variant-numeric: tabular-nums; }
    .spm-page .spm-stat-note { display: block; margin-top: 4px; color: #93888e; font-size: 10.5px; line-height: 1.5; }

    .spm-page .spm-panel {
        display: block; min-width: 0; margin-bottom: 20px; border: 1px solid var(--spm-border);
        border-radius: 13px; background: #fff; box-shadow: 0 3px 14px rgba(62, 10, 25, .035); overflow: hidden;
    }
    .spm-page .spm-panel-header {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
        padding: 19px 23px; border-bottom: 1px solid #f0e8e9;
    }
    .spm-page .spm-panel-title { margin: 0 0 5px; color: #2c2529; font-size: 16px; font-weight: 750; }
    .spm-page .spm-panel-subtitle { margin: 0; color: var(--spm-muted); font-size: 11.5px; line-height: 1.65; }
    .spm-page .spm-panel-count {
        display: inline-flex; align-items: center; gap: 7px; padding: 7px 12px; border-radius: 20px;
        background: #f8eff2; border: 1px solid #ebd4db; color: var(--spm-wine); font-size: 11px; font-weight: 700;
    }
    .spm-page .spm-panel-count::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

    /* Informasi kuota — tidak menutupi ringkasan atau panel lain. */
    .spm-page .spm-quota { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; padding: 19px 23px; }
    .spm-page .spm-quota-copy { min-width: 0; flex: 1 1 300px; }
    .spm-page .spm-quota-title { display: flex; align-items: center; gap: 9px; margin: 0 0 6px; font-size: 13px; font-weight: 750; color: var(--spm-wine); }
    .spm-page .spm-quota-title svg { width: 19px; height: 19px; }
    .spm-page .spm-quota-description { margin: 0; color: #7a7176; font-size: 11.5px; line-height: 1.65; }
    .spm-page .spm-quota-meter { flex: 0 1 290px; width: 100%; min-width: min(100%, 210px); }
    .spm-page .spm-quota-meter-head { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; margin-bottom: 9px; }
    .spm-page .spm-quota-meter-head strong { font-size: 18px; color: var(--spm-wine); font-variant-numeric: tabular-nums; }
    .spm-page .spm-quota-meter-head span { color: #887980; font-size: 11px; }
    .spm-page .spm-meter { height: 8px; border-radius: 20px; overflow: hidden; background: #f1e8ec; }
    .spm-page .spm-meter-fill { display: block; width: var(--spm-progress, 0%); height: 100%; border-radius: inherit; background: var(--spm-wine); }

    /* Pilihan pengajuan */
    .spm-page .spm-requests { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 225px), 1fr)); gap: 14px; padding: 20px 23px 23px; }
    .spm-page .spm-request {
        display: flex; flex-direction: column; align-items: flex-start; gap: 0; min-width: 0; min-height: 185px;
        padding: 17px; border: 1px solid #e9dfe2; border-radius: 12px; background: #fff;
        box-shadow: 0 3px 11px rgba(58, 15, 28, .045); transition: border-color .18s, box-shadow .18s, transform .18s;
    }
    .spm-page .spm-request:hover { border-color: #dcc3cc; box-shadow: 0 8px 19px rgba(58, 15, 28, .09); transform: translateY(-1px); }
    .spm-page .spm-request-icon { display: grid; place-items: center; width: 40px; height: 40px; margin-bottom: 13px; border-radius: 10px; background: #f7e9ed; color: var(--spm-wine); }
    .spm-page .spm-request-icon svg { width: 21px; height: 21px; }
    .spm-page .spm-request-title { margin: 0 0 6px; color: #342b30; font-size: 13px; font-weight: 750; line-height: 1.45; }
    .spm-page .spm-request-text { margin: 0 0 14px; color: #82777d; font-size: 11.5px; line-height: 1.65; }
    .spm-page .spm-request-link {
        display: inline-flex; align-items: center; justify-content: center; gap: 7px;
        margin-top: auto; min-height: 35px; padding: 8px 11px; border: 1px solid #e0cfd4;
        border-radius: 8px; color: var(--spm-wine); background: #fff; font-size: 11.5px; font-weight: 700;
    }
    .spm-page .spm-request-link:hover { color: #fff; border-color: var(--spm-wine); background: var(--spm-wine); }
    .spm-page .spm-request-link svg { width: 14px; height: 14px; }

    /* Tabel riwayat identik dengan tabel pengajuan Data Kamar. */
    .spm-page .spm-table-wrap { width: 100%; max-width: 100%; overflow-x: auto; overscroll-behavior-inline: contain; }
    .spm-page .spm-table { width: 100%; min-width: 850px; border-collapse: collapse; background: #fff; }
    .spm-page .spm-table thead th { padding: 14px 15px; background: var(--spm-wine); color: #fff; font-size: 11px; font-weight: 700; text-align: left; white-space: nowrap; }
    .spm-page .spm-table tbody td { padding: 14px 15px; border-bottom: 1px solid #f1e8eb; vertical-align: middle; color: #534c50; font-size: 11.5px; line-height: 1.6; overflow-wrap: anywhere; }
    .spm-page .spm-table tbody tr:nth-child(even) { background: #fdfafb; }
    .spm-page .spm-table tbody tr:hover { background: #fcf4f6; }
    .spm-page .spm-table tbody tr:last-child td { border-bottom: 0; }
    .spm-page .spm-table .spm-date { color: #867980; font-variant-numeric: tabular-nums; }
    .spm-page .spm-table .spm-type { font-weight: 700; color: #30282d; }
    .spm-page .spm-status { display: inline-flex; align-items: center; justify-content: center; padding: 5px 10px; border-radius: 20px; font-size: 10.5px; font-weight: 700; white-space: nowrap; }
    .spm-page .spm-status--menunggu { background: #fff3d9; color: #936113; }
    .spm-page .spm-status--disetujui { background: #e4f8ec; color: #14783c; }
    .spm-page .spm-status--ditolak { background: #ffebee; color: #a52443; }
    .spm-page .spm-status--perbaikan { background: #fff0d9; color: #a66e11; }
    .spm-page .spm-status--selesai { background: #e9f1ff; color: #2c5da2; }
    .spm-page .spm-status--dibatalkan { background: #f0f1f3; color: #62646d; }
    .spm-page .spm-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 7px; }
    .spm-page .spm-actions form { margin: 0; }
    .spm-page .spm-btn { display: inline-flex; justify-content: center; align-items: center; min-height: 34px; padding: 7px 12px; border: 1px solid #dfcfd4; border-radius: 8px; background: #fff; color: var(--spm-wine); font-size: 11px; font-weight: 700; line-height: 1.4; white-space: nowrap; }
    .spm-page .spm-btn:hover { background: #fdf4f6; }
    .spm-page .spm-btn--danger { color: #a52443; border-color: #ecccd4; background: #fff8f9; }
    .spm-page .spm-btn--danger:hover { background: #ffeaf0; }
    .spm-page .spm-empty { padding: 34px 18px !important; color: #94858c; text-align: center; font-size: 12px; line-height: 1.7; }
    .spm-page #riwayat-pengajuan { scroll-margin-top: 90px; }

    /* Dapat menyesuaikan dengan ruang konten aktual ketika sidebar terbuka. */
    @container (max-width: 990px) {
        .spm-page .spm-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @container (max-width: 730px) {
        .spm-page .spm-panel-header { padding: 17px 16px; }
        .spm-page .spm-requests { padding: 16px; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .spm-page .spm-quota { padding: 16px; }
        .spm-page .spm-table-wrap { overflow: visible; }
        .spm-page .spm-table, .spm-page .spm-table tbody { display: block; width: 100%; min-width: 0; }
        .spm-page .spm-table thead { display: none; }
        .spm-page .spm-table tbody tr { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 10px 12px; margin: 12px 16px; padding: 13px; border: 1px solid #ebdfe3; border-radius: 10px; background: #fff !important; }
        .spm-page .spm-table tbody td { display: block; padding: 0 !important; width: auto; min-width: 0; border: 0 !important; }
        .spm-page .spm-table tbody td::before { display: block; content: attr(data-label); margin-bottom: 4px; color: #96878e; font-size: 10px; font-weight: 700; }
        .spm-page .spm-table tbody td:nth-child(2), .spm-page .spm-table tbody td:nth-child(4), .spm-page .spm-table tbody td:last-child { grid-column: 1 / -1; }
        .spm-page .spm-table tbody tr.spm-empty-row { display: block; }
        .spm-page .spm-table tbody tr.spm-empty-row td::before { content: none; }
    }
    @container (max-width: 510px) {
        .spm-page .spm-header-link { width: 100%; }
        .spm-page .spm-requests { grid-template-columns: minmax(0,1fr); }
        .spm-page .spm-quota-meter { flex: 1 1 100%; }
    }
    @container (max-width: 410px) {
        .spm-page .spm-stats { grid-template-columns: minmax(0,1fr); }
        .spm-page .spm-stat { min-height: 82px; padding: 13px; }
        .spm-page .spm-stat-icon { width: 38px; height: 38px; flex-basis: 38px; }
        .spm-page .spm-table tbody tr { grid-template-columns: minmax(0,1fr); }
        .spm-page .spm-table tbody td { grid-column: 1 / -1; }
    }
    @media (prefers-reduced-motion: reduce) { .spm-page .spm-request { transition: none; } }

    /* Modal detail menggunakan <dialog> native agar Escape, fokus dan scroll lebih aman. */
    .spm-modal {
        width: min(680px, calc(100% - 24px)); max-width: 680px; max-height: calc(100dvh - 28px);
        margin: auto; padding: 0; border: 0; border-radius: 13px; overflow-x: hidden; overflow-y: auto;
        background: #fff; color: #332b30; font-family: inherit;
        box-shadow: 0 23px 65px rgba(0,0,0,.27);
    }
    .spm-modal::backdrop { background: rgba(17,13,20,.62); backdrop-filter: blur(3px); }
    .spm-modal .spm-modal-header { position: sticky; top: 0; z-index: 1; display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 17px 21px; background: #800b22; color: #fff; }
    .spm-modal .spm-modal-title { margin: 0; font-size: 16px; font-weight: 750; }
    .spm-modal .spm-modal-close { display: grid; place-items: center; flex: 0 0 auto; width: 32px; height: 32px; border: 1px solid rgba(255,255,255,.5); border-radius: 50%; background: none; color: #fff; font-size: 22px; line-height: 1; }
    .spm-modal .spm-modal-close:hover { background: rgba(255,255,255,.13); }
    .spm-modal .spm-modal-content { padding: 20px 22px 24px; }
    .spm-modal .spm-detail-group { margin-bottom: 17px; padding-bottom: 13px; border-bottom: 1px solid #eee3e6; }
    .spm-modal .spm-detail-group:last-child { border-bottom: 0; margin-bottom: 0; padding-bottom: 0; }
    .spm-modal .spm-detail-group h3 { margin: 0 0 9px; color: #800b22; font-size: 12.5px; font-weight: 750; }
    .spm-modal .spm-detail-row { display: grid; grid-template-columns: minmax(130px, 38%) minmax(0,1fr); gap: 10px; padding: 9px 0; border-bottom: 1px solid #f6f0f2; font-size: 11.5px; line-height: 1.6; }
    .spm-modal .spm-detail-row:last-child { border-bottom: 0; }
    .spm-modal .spm-detail-label { color: #82777d; }
    .spm-modal .spm-detail-value { color: #392e33; font-weight: 650; overflow-wrap: anywhere; white-space: pre-wrap; }
    .spm-modal .spm-document-links { display: flex; flex-wrap: wrap; gap: 8px; }
    .spm-modal .spm-document-link { display: inline-flex; justify-content: center; align-items: center; min-height: 35px; padding: 8px 11px; border: 1px solid #ddcbd1; border-radius: 8px; background: #fff; color: #800b22; font-size: 11.5px; font-weight: 700; text-decoration: none; }
    .spm-modal .spm-document-link:hover { background: #fbf1f4; }
    @media (max-width: 640px) {
        .spm-modal { width: calc(100% - 16px); max-height: calc(100dvh - 16px); }
        .spm-modal .spm-modal-header { padding: 15px 17px; }
        .spm-modal .spm-modal-content { padding: 15px 17px 20px; }
        .spm-modal .spm-detail-row { grid-template-columns: minmax(0,1fr); gap: 4px; }
    }
</style>

<div class="spm-page">
    <header class="spm-header">
        <div>
            <nav class="spm-breadcrumb" aria-label="Navigasi halaman">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Surat &amp; Perizinan</span>
            </nav>
            <h1>Surat &amp; Perizinan</h1>
            <p class="spm-subtitle">Ajukan perizinan kegiatan dan izin pulang, serta pantau status dan keputusan Musyrifah.</p>
        </div>
        <a class="spm-header-link" href="#pilihan-pengajuan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Buat Pengajuan
        </a>
    </header>

    @if ($errors->any())
        <div class="spm-alert spm-alert--error" role="alert">
            <strong>Periksa kembali data pengajuan:</strong>
            <ul>
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="spm-stats" aria-label="Ringkasan pengajuan perizinan">
        <div class="spm-stat">
            <span class="spm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg></span>
            <div class="spm-stat-info"><span class="spm-stat-label">Menunggu</span><strong class="spm-stat-number">{{ number_format($jumlahMenunggu, 0, ',', '.') }}</strong><span class="spm-stat-note">Menunggu keputusan</span></div>
        </div>
        <div class="spm-stat">
            <span class="spm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg></span>
            <div class="spm-stat-info"><span class="spm-stat-label">Disetujui</span><strong class="spm-stat-number">{{ number_format($jumlahDisetujui, 0, ',', '.') }}</strong><span class="spm-stat-note">Pengajuan diterima</span></div>
        </div>
        <div class="spm-stat">
            <span class="spm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg></span>
            <div class="spm-stat-info"><span class="spm-stat-label">Ditolak</span><strong class="spm-stat-number">{{ number_format($jumlahDitolak, 0, ',', '.') }}</strong><span class="spm-stat-note">Pengajuan ditolak</span></div>
        </div>
        <div class="spm-stat">
            <span class="spm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10H3zM7 9V5h10v4M8 14h8M12 12v4"/></svg></span>
            <div class="spm-stat-info"><span class="spm-stat-label">Sisa Kuota Pulang</span><strong class="spm-stat-number">{{ $kuotaSisa }}</strong><span class="spm-stat-note">Slot bulan ini</span></div>
        </div>
    </section>

    <section class="spm-panel" aria-labelledby="spm-quota-heading">
        <div class="spm-panel-header">
            <div>
                <h2 id="spm-quota-heading" class="spm-panel-title">Kuota Izin Pulang Mingguan</h2>
                <p class="spm-panel-subtitle">Monitoring pemakaian izin pulang untuk bulan {{ $kuotaPulang['bulan'] }}.</p>
            </div>
            <span class="spm-panel-count">Maksimal {{ $kuotaMaksimal }} kali / bulan</span>
        </div>
        <div class="spm-quota">
            <div class="spm-quota-copy">
                <h3 class="spm-quota-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 10 9-7 9 7M5 9v12h14V9M9 21v-7h6v7"/></svg> Pemakaian Kuota</h3>
                <p class="spm-quota-description">Pengajuan izin pulang menggunakan kuota yang ditetapkan Ma’had. Kamu bisa memantau sisa slot sebelum mengajukan izin.</p>
            </div>
            <div class="spm-quota-meter" aria-label="{{ $kuotaTerpakai }} dari {{ $kuotaMaksimal }} kuota telah digunakan">
                <div class="spm-quota-meter-head"><strong>{{ $kuotaTerpakai }} / {{ $kuotaMaksimal }}</strong><span>Sisa {{ $kuotaSisa }} slot</span></div>
                <div class="spm-meter"><span class="spm-meter-fill" style="--spm-progress: {{ $kuotaPersen }}%;"></span></div>
            </div>
        </div>
    </section>

    <section class="spm-panel" id="pilihan-pengajuan" aria-labelledby="spm-pilihan-heading">
        <div class="spm-panel-header">
            <div>
                <h2 id="spm-pilihan-heading" class="spm-panel-title">Buat Pengajuan</h2>
                <p class="spm-panel-subtitle">Pilih layanan sesuai keperluan. Setiap pengajuan akan dicatat melalui sistem SIMAHAD.</p>
            </div>
        </div>
        <div class="spm-requests">
            <article class="spm-request">
                <span class="spm-request-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h2m2 0h2M9 14h2m2 0h2M10 21v-4h4v4"/></svg></span>
                <h3 class="spm-request-title">Izin Kegiatan Fakultas/Prodi</h3>
                <p class="spm-request-text">Ajukan izin untuk kegiatan resmi fakultas atau program studi di luar Ma’had.</p>
                <a class="spm-request-link" href="{{ route('surat.perizinan.mhs.kegiatan', ['jenis' => 'kegiatan_fakultas']) }}">Buat Pengajuan <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
            </article>
            <article class="spm-request">
                <span class="spm-request-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 10h18m-13 5 2 2 4-5"/></svg></span>
                <h3 class="spm-request-title">Izin Kegiatan Kampus</h3>
                <p class="spm-request-text">Ajukan izin kegiatan kampus dengan surat resmi dari pihak penyelenggara.</p>
                <a class="spm-request-link" href="{{ route('surat.perizinan.mhs.kegiatan', ['jenis' => 'kegiatan_kampus']) }}">Buat Pengajuan <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
            </article>
            <article class="spm-request">
                <span class="spm-request-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6zM15 3v5h4M9 12h7M9 16h7"/><path d="m9 8 1 1 2-2"/></svg></span>
                <h3 class="spm-request-title">Surat Pulang Semester</h3>
                <p class="spm-request-text">Unduh surat semester, minta tanda tangan orang tua, lalu unggah kembali.</p>
                <a class="spm-request-link" href="{{ route('surat.perizinan.mhs.semester') }}">Buka Surat Semester <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
            </article>
            <article class="spm-request">
                <span class="spm-request-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7M5 9v12h14V9M9 21v-7h6v7"/></svg></span>
                <h3 class="spm-request-title">Izin Pulang</h3>
                <p class="spm-request-text">Isi rencana keberangkatan dan kepulangan untuk mengajukan izin pulang.</p>
                <a class="spm-request-link" href="{{ route('surat.perizinan.mhs.pengajuan') }}">Buat Pengajuan <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
            </article>
            <article class="spm-request">
                <span class="spm-request-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16M4 10h16M4 15h10M4 20h8"/><path d="m16 18 2 2 3-4"/></svg></span>
                <h3 class="spm-request-title">Riwayat Pengajuan</h3>
                <p class="spm-request-text">Lihat kembali pengajuan, status, dokumen, dan catatan keputusan Musyrifah.</p>
                <a class="spm-request-link" href="#riwayat-pengajuan">Lihat Riwayat <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
            </article>
        </div>
    </section>

    <section class="spm-panel" id="riwayat-pengajuan" aria-labelledby="spm-history-heading">
        <div class="spm-panel-header">
            <div>
                <h2 class="spm-panel-title" id="spm-history-heading">Riwayat Pengajuan</h2>
                <p class="spm-panel-subtitle">Daftar pengajuan yang tersimpan pada akun kamu. Gunakan Detail untuk melihat dokumen dan hasil verifikasi.</p>
            </div>
            <span class="spm-panel-count">{{ $pengajuans->count() }} pengajuan</span>
        </div>
        <div class="spm-table-wrap">
            <table class="spm-table">
                <thead>
                    <tr>
                        <th scope="col">Tanggal Ajuan</th>
                        <th scope="col">Jenis Izin</th>
                        <th scope="col">Periode</th>
                        <th scope="col">Tujuan / Lokasi</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuans as $pengajuan)
                        @php
                            $rincian = [
                                'jenis' => $pengajuan->labelJenis(),
                                'status' => $pengajuan->labelStatus(),
                                'diajukan' => $pengajuan->created_at?->locale('id')->translatedFormat('d M Y H:i') ?? '-',
                                'periode' => $pengajuan->periodeTeks(),
                                'durasi' => $pengajuan->durasi_hari ? $pengajuan->durasi_hari.' hari' : '-',
                                'lokasi' => $pengajuan->tujuanTeks(),
                                'kegiatan' => $pengajuan->nama_kegiatan ?: '-',
                                'penyelenggara' => $pengajuan->penyelenggara ?: '-',
                                'waktu' => $pengajuan->waktuTeks(),
                                'alasan' => $pengajuan->alasan ?: '-',
                                'verifikator' => $pengajuan->keputusan_nama ?: '-',
                                'diverifikasi' => $pengajuan->keputusan_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-',
                                'catatan' => $pengajuan->catatan_musyrifah ?: '-',
                                'nomor_surat' => $pengajuan->nomor_surat ?: '-',
                                'dokumen' => $pengajuan->punyaDokumen() ? route('surat.perizinan.mhs.dokumen', [$pengajuan, 'pendukung']) : null,
                                'tertanda' => $pengajuan->punyaSuratTertanda() ? route('surat.perizinan.mhs.dokumen', [$pengajuan, 'tertanda']) : null,
                                'cetak' => $pengajuan->suratSemester() && $pengajuan->nomor_surat ? route('surat.perizinan.mhs.semester.cetak', $pengajuan) : null,
                            ];
                        @endphp
                        <tr>
                            <td data-label="Tanggal Ajuan" class="spm-date">{{ $pengajuan->created_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</td>
                            <td data-label="Jenis Izin"><span class="spm-type">{{ $pengajuan->labelJenis() }}</span></td>
                            <td data-label="Periode">{{ $pengajuan->periodeTeks() }}</td>
                            <td data-label="Tujuan / Lokasi">{{ $pengajuan->tujuanTeks() }}</td>
                            <td data-label="Status"><span class="spm-status spm-status--{{ $pengajuan->kelasStatus() }}">{{ $pengajuan->labelStatus() }}</span></td>
                            <td data-label="Aksi">
                                <div class="spm-actions">
                                    <button type="button" class="spm-btn" data-spm-detail="{{ json_encode($rincian, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}" aria-haspopup="dialog">Detail</button>
                                    @if (in_array($pengajuan->status, ['menunggu', 'perlu_perbaikan'], true))
                                        <form method="POST"
                                              action="{{ route('surat.perizinan.mhs.batalkan', $pengajuan) }}"
                                              data-sui-confirm="Batalkan pengajuan ini? Slot kuota akan dikembalikan sesuai ketentuan."
                                              data-sui-confirm-title="Batalkan pengajuan?"
                                              data-sui-confirm-label="Ya, Batalkan"
                                              data-sui-confirm-tone="danger">
                                            @csrf
                                            <button type="submit" class="spm-btn spm-btn--danger">Batalkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="spm-empty-row"><td colspan="6" class="spm-empty">Belum ada pengajuan. Pilih salah satu layanan di atas untuk membuat pengajuan pertama.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <dialog class="spm-modal" id="spmDetailModal" aria-labelledby="spmDetailTitle">
        <div class="spm-modal-header">
            <h2 id="spmDetailTitle" class="spm-modal-title">Detail Pengajuan</h2>
            <button type="button" class="spm-modal-close" data-spm-close aria-label="Tutup detail pengajuan">&times;</button>
        </div>
        <div class="spm-modal-content" id="spmDetailContent"></div>
    </dialog>
</div>

<script>
(function () {
    'use strict';

    const modal = document.getElementById('spmDetailModal');
    const detailContent = document.getElementById('spmDetailContent');
    if (!modal || !detailContent) return;

    const sections = [
        { title: 'Informasi Pengajuan', rows: [
            ['Jenis Izin', 'jenis'], ['Status', 'status'], ['Tanggal Ajuan', 'diajukan'],
            ['Periode', 'periode'], ['Durasi', 'durasi']
        ] },
        { title: 'Rincian', rows: [
            ['Nama Kegiatan', 'kegiatan'], ['Penyelenggara', 'penyelenggara'],
            ['Waktu Kegiatan', 'waktu'], ['Tujuan / Lokasi', 'lokasi'],
            ['Nomor Surat', 'nomor_surat'], ['Alasan', 'alasan']
        ] },
        { title: 'Keputusan Musyrifah', rows: [
            ['Diverifikasi Oleh', 'verifikator'], ['Waktu Verifikasi', 'diverifikasi'],
            ['Catatan', 'catatan']
        ] },
    ];

    function addTextRow(group, label, value) {
        const row = document.createElement('div');
        row.className = 'spm-detail-row';
        const title = document.createElement('span');
        title.className = 'spm-detail-label';
        title.textContent = label;
        const detail = document.createElement('span');
        detail.className = 'spm-detail-value';
        detail.textContent = value == null || value === '' ? '-' : String(value);
        row.append(title, detail);
        group.appendChild(row);
    }

    function addGroup(heading) {
        const group = document.createElement('section');
        group.className = 'spm-detail-group';
        const title = document.createElement('h3');
        title.textContent = heading;
        group.appendChild(title);
        detailContent.appendChild(group);
        return group;
    }

    function openDetail(data) {
        detailContent.replaceChildren();
        sections.forEach(function (section) {
            const group = addGroup(section.title);
            section.rows.forEach(function (item) {
                addTextRow(group, item[0], data[item[1]]);
            });
        });

        const documents = [
            ['Surat Pendukung', data.dokumen],
            ['Surat Bertanda Tangan', data.tertanda],
            ['Cetak Surat Semester', data.cetak]
        ].filter(function (item) { return Boolean(item[1]); });

        if (documents.length) {
            const group = addGroup('Berkas');
            const links = document.createElement('div');
            links.className = 'spm-document-links';
            documents.forEach(function (item) {
                const link = document.createElement('a');
                link.className = 'spm-document-link';
                link.textContent = item[0];
                link.href = item[1];
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                links.appendChild(link);
            });
            group.appendChild(links);
        }

        if (typeof modal.showModal === 'function') {
            modal.showModal();
        } else {
            modal.setAttribute('open', '');
        }
    }

    document.querySelectorAll('.spm-page [data-spm-detail]').forEach(function (button) {
        button.addEventListener('click', function () {
            try {
                openDetail(JSON.parse(button.getAttribute('data-spm-detail') || '{}'));
            } catch (error) {
                // Data yang tidak valid tidak boleh menghasilkan modal kosong.
                console.error('Detail perizinan tidak dapat ditampilkan.', error);
            }
        });
    });

    const closeButton = modal.querySelector('[data-spm-close]');
    if (closeButton) closeButton.addEventListener('click', function () { modal.close(); });
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            const bounds = modal.getBoundingClientRect();
            const inside = event.clientX >= bounds.left && event.clientX <= bounds.right &&
                event.clientY >= bounds.top && event.clientY <= bounds.bottom;
            if (!inside) modal.close();
        }
    });
})();
</script>
@endsection
