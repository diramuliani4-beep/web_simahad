@extends('layouts.musyrifah')

@section('title', 'Laporan Pembelajaran')
@section('content')
<style>
    .lp {
        --lp-maroon: #800b22;
        --lp-deep: #620819;
        --lp-ink: #292427;
        --lp-muted: #82797e;
        --lp-border: #eddee1;
        --lp-soft: #fbf5f7;
        box-sizing: border-box;
        display: block !important;
        position: relative;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0;
        clear: both;
        margin: 0 !important;
        padding: 0 0 28px;
        color: var(--lp-ink);
        font-family: inherit;
        font-size: 13px;
        container-type: inline-size;
    }

    .lp *, .lp *::before, .lp *::after { box-sizing: border-box; }
    .lp a { text-decoration: none; }
    .lp button, .lp input, .lp select, .lp textarea { font-family: inherit; }
    .lp svg { display: block; width: 18px; height: 18px; flex: none; }

    .lp .lp-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        width: 100%;
        margin: 0 0 21px;
        padding: 0 0 19px;
        border-bottom: 1px solid #e8cfd6;
    }

    .lp .lp-head-intro { flex: 1 1 320px; min-width: 0; }
    .lp .lp-breadcrumb { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; margin: 0 0 11px; color: #8e8487; font-size: 12px; }
    .lp .lp-breadcrumb a { color: var(--lp-maroon); }
    .lp .lp-breadcrumb a:hover { text-decoration: underline; }
    .lp .lp-head h1 { margin: 0 0 6px; color: #282126; font-size: clamp(22px, 2.5vw, 27px); font-weight: 750; line-height: 1.3; letter-spacing: -.55px; }
    .lp .lp-head p { margin: 0; max-width: 760px; color: #797076; font-size: 12.5px; line-height: 1.65; }
    .lp .lp-head-aksi { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; }

    .lp .lp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 9px 14px;
        border: 1px solid transparent;
        border-radius: 9px;
        background: #fff;
        color: var(--lp-maroon);
        font-size: 12px;
        font-weight: 700;
        line-height: 1.25;
        cursor: pointer;
        transition: background .18s ease, border-color .18s ease, transform .18s ease;
    }

    .lp .lp-btn--utama { color: #fff; background: var(--lp-maroon); border-color: var(--lp-maroon); }
    .lp .lp-btn--utama:hover { background: var(--lp-deep); border-color: var(--lp-deep); }
    .lp .lp-btn--garis { color: var(--lp-maroon); border-color: #e2cfd4; background: #fff; }
    .lp .lp-btn--garis:hover { background: #fdf4f6; border-color: #bc9ca5; }
    .lp .lp-btn--bahaya { color: #a0223d; border-color: #efcbd3; background: #fff; }
    .lp .lp-btn--bahaya:hover { background: #fff1f3; border-color: #d89aaa; }
    .lp .lp-btn--kecil { min-height: 32px; padding: 7px 10px; font-size: 11px; border-radius: 7px; white-space: nowrap; }
    .lp .lp-btn:focus-visible, .lp .lp-subjek:focus-visible { outline: 2px solid #9b4962; outline-offset: 2px; }

    .lp .lp-stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 13px; width: 100%; margin: 0 0 20px; }
    .lp .lp-stat-card { display: flex; align-items: center; gap: 13px; min-width: 0; min-height: 97px; padding: 17px 18px; border: 1px solid var(--lp-border); border-radius: 12px; background: #fff; box-shadow: 0 3px 12px rgba(57,19,30,.035); }
    .lp .lp-stat-icon { display: grid; place-items: center; flex: 0 0 44px; width: 44px; height: 44px; border-radius: 11px; background: #f7e9ed; color: var(--lp-maroon); }
    .lp .lp-stat-icon svg { width: 22px; height: 22px; }
    .lp .lp-stat-card:nth-child(2) .lp-stat-icon { background: #fdf2de; color: #a96c10; }
    .lp .lp-stat-card:nth-child(3) .lp-stat-icon { background: #e8f8ef; color: #1e7849; }
    .lp .lp-stat-card:nth-child(4) .lp-stat-icon { background: #e9f2ff; color: #346ba4; }
    .lp .lp-stat-info { display: flex; flex-direction: column; min-width: 0; }
    .lp .lp-stat-label { color: #81777d; font-size: 11px; font-weight: 600; line-height: 1.4; }
    .lp .lp-stat-value { margin-top: 3px; color: #292427; font-size: 24px; font-weight: 750; line-height: 1.2; font-variant-numeric: tabular-nums; }
    .lp .lp-stat-desc { margin-top: 4px; color: #93888e; font-size: 10.5px; line-height: 1.5; }

    .lp .lp-kartu { min-width: 0; margin: 0 0 19px; border: 1px solid var(--lp-border); border-radius: 13px; background: #fff; box-shadow: 0 3px 14px rgba(62,10,25,.035); overflow: hidden; }
    .lp .lp-sec-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding: 19px 23px; border-bottom: 1px solid #f0e8e9; }
    .lp .lp-sec-head h2 { margin: 0 0 5px; color: #2c2529; font-size: 16px; font-weight: 750; line-height: 1.3; }
    .lp .lp-sec-head p { margin: 0; max-width: 850px; color: var(--lp-muted); font-size: 11.5px; line-height: 1.65; }
    .lp .lp-sec-aksi { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }

    .lp .lp-subjek-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 225px), 1fr)); gap: 12px; padding: 20px 23px 23px; }
    .lp .lp-subjek { display: flex; align-items: flex-start; gap: 12px; min-width: 0; min-height: 112px; padding: 15px; border: 1px solid #e9dfe2; border-radius: 11px; background: #fff; transition: border-color .18s, box-shadow .18s, background .18s; }
    .lp .lp-subjek:hover { border-color: #d9b5c0; background: #fdf9fa; box-shadow: 0 8px 18px rgba(58,15,28,.06); }
    .lp .lp-subjek.is-active { border-color: #b47a8c; background: #fdf5f7; box-shadow: inset 0 0 0 1px rgba(128,11,34,.08); }
    .lp .lp-subjek-nomor { display: grid; place-items: center; flex: 0 0 35px; width: 35px; height: 35px; border-radius: 10px; background: #f7e9ed; color: var(--lp-maroon); font-size: 12px; font-weight: 800; }
    .lp .lp-subjek.is-active .lp-subjek-nomor { background: var(--lp-maroon); color: #fff; }
    .lp .lp-subjek-isi { display: flex; flex-direction: column; gap: 4px; min-width: 0; }
    .lp .lp-subjek-nama { color: #342b30; font-size: 12px; font-weight: 750; line-height: 1.5; overflow-wrap: anywhere; }
    .lp .lp-subjek-kode { color: #94858c; font-size: 10.5px; line-height: 1.4; }
    .lp .lp-subjek-status { margin-top: auto; color: var(--lp-maroon); font-size: 10.5px; font-weight: 750; }

    .lp .lp-filter { display: grid; grid-template-columns: 170px 190px minmax(210px, 1fr) auto; align-items: end; gap: 12px; padding: 18px 23px; border-bottom: 1px solid #f2e9ec; }
    .lp .lp-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
    .lp .lp-field label { color: #55484d; font-size: 11.5px; font-weight: 700; line-height: 1.4; }
    .lp .lp-field input, .lp .lp-field select { display: block; width: 100%; height: 43px; min-width: 0; padding: 9px 12px; border: 1px solid #e5d6da; border-radius: 9px; background: #fff; color: #332a2e; font-size: 12px; outline: none; }
    .lp .lp-field input:focus, .lp .lp-field select:focus { border-color: #a96075; box-shadow: 0 0 0 3px rgba(128,11,34,.08); }
    .lp .lp-field--aksi { display: flex; flex-direction: row; align-items: flex-end; flex-wrap: wrap; gap: 8px; }
    .lp .lp-field--aksi .lp-btn { min-height: 43px; }

    .lp .lp-program { display: grid; grid-template-columns: minmax(0, 1.5fr) repeat(3, minmax(0, 1fr)); gap: 0; padding: 18px 23px; }
    .lp .lp-program-item { display: flex; flex-direction: column; gap: 5px; min-width: 0; padding: 0 17px; border-left: 1px solid #f0e5e8; }
    .lp .lp-program-item:first-child { padding-left: 0; border-left: 0; }
    .lp .lp-program-label { color: #95878d; font-size: 10px; font-weight: 700; letter-spacing: .35px; text-transform: uppercase; }
    .lp .lp-program-nilai { color: #3a3035; font-size: 12px; font-weight: 700; line-height: 1.5; overflow-wrap: anywhere; }

    .lp .lp-sinkron { display: flex; align-items: flex-start; gap: 10px; margin: 0 23px 18px; padding: 12px 14px; border: 1px solid #dbe9df; border-radius: 9px; background: #f3faf5; color: #276041; font-size: 11px; line-height: 1.6; }
    .lp .lp-sinkron svg { margin-top: 1px; width: 17px; height: 17px; }
    .lp .lp-sinkron strong { font-weight: 750; }

    .lp .lp-tabel-bungkus { display: block; width: 100%; max-width: 100%; min-width: 0; overflow-x: auto; overscroll-behavior-inline: contain; }
    .lp .lp-tabel { width: 100%; min-width: 1050px; border-collapse: collapse; background: #fff; }
    .lp .lp-tabel th { padding: 13px 14px; background: var(--lp-maroon); color: #fff; font-size: 11px; font-weight: 700; line-height: 1.3; text-align: left; white-space: nowrap; }
    .lp .lp-tabel td { padding: 14px; border-bottom: 1px solid #f1e8eb; color: #534c50; font-size: 11.5px; line-height: 1.55; vertical-align: top; overflow-wrap: anywhere; }
    .lp .lp-tabel tbody tr:nth-child(even) { background: #fdfafb; }
    .lp .lp-tabel tbody tr:hover { background: #fcf4f6; }
    .lp .lp-tabel tbody tr:last-child td { border-bottom: none; }
    .lp .lp-nomor { color: #8d7d84; font-weight: 700; text-align: center; }
    .lp .lp-kanan { text-align: right; }
    .lp .lp-tanggal { display: block; color: #30282d; font-size: 12px; font-weight: 750; white-space: nowrap; }
    .lp .lp-sub { display: block; margin-top: 3px; color: #8f8288; font-size: 10.5px; line-height: 1.5; }
    .lp .lp-materi { display: -webkit-box; max-width: 360px; overflow: hidden; color: #3e3439; font-weight: 650; line-height: 1.6; white-space: pre-line; -webkit-box-orient: vertical; -webkit-line-clamp: 4; line-clamp: 4; }
    .lp .lp-catatan { display: block; max-width: 360px; margin-top: 5px; color: #8a7c82; font-size: 10.5px; line-height: 1.5; white-space: pre-line; }
    .lp .lp-ttd { display: block; min-height: 24px; color: #6b0d1b; font-style: italic; font-weight: 700; }
    .lp .lp-garis-ttd { display: inline-block; width: 92px; border-bottom: 1px solid #bfaeb4; }

    .lp .lp-absensi { display: flex; flex-wrap: wrap; gap: 5px; }
    .lp .lp-absen { display: inline-flex; align-items: center; gap: 3px; padding: 3px 6px; border-radius: 5px; background: #f3f5f6; color: #687078; font-size: 9.5px; font-weight: 700; }
    .lp .lp-absen--hadir { background: #eaf7ef; color: #256640; }
    .lp .lp-absen--izin { background: #fff6df; color: #8a6414; }
    .lp .lp-absen--sakit { background: #eaf2fc; color: #35628f; }
    .lp .lp-absen--alpa { background: #fdecef; color: #9b2c41; }

    .lp .lp-badge { display: inline-flex; max-width: 100%; padding: 5px 8px; border: 1px solid #eddae0; border-radius: 20px; background: #f8eff2; color: #80344a; font-size: 10px; font-weight: 750; line-height: 1.3; }
    .lp .lp-badge--manual { border-color: #d8e5f2; background: #eef5fc; color: #35628f; }
    .lp .lp-badge--lama { border-color: #e8e1d4; background: #faf7f0; color: #7a6842; }
    .lp .lp-aksi { display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 6px; }
    .lp .lp-aksi form { display: inline-flex; margin: 0; }
    .lp .lp-kosong { margin: 18px 23px; padding: 28px 18px; border: 1px dashed #e6d7dc; border-radius: 10px; background: #fdf9fa; color: #857a7d; font-size: 12px; line-height: 1.7; text-align: center; }
    .lp .lp-kosong strong { display: block; margin-bottom: 4px; color: #55464c; font-size: 13px; }
    .lp .lp-kosong .lp-btn { margin-top: 12px; }
    .lp .lp-foot { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 15px 23px; border-top: 1px solid #f1e8eb; color: #8b7d83; font-size: 10.5px; }
    .lp .lp-foot a { color: var(--lp-maroon); font-weight: 750; }

    @container (max-width: 1120px) {
        .lp .lp-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .lp .lp-program { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 0; }
        .lp .lp-program-item:nth-child(3) { padding-left: 0; border-left: 0; }
        .lp .lp-filter { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .lp .lp-field--aksi { grid-column: 1 / -1; }
    }

    @container (max-width: 690px) {
        .lp .lp-head { align-items: flex-start; }
        .lp .lp-head-aksi { width: 100%; }
        .lp .lp-subjek-grid, .lp .lp-sec-head, .lp .lp-filter, .lp .lp-program { padding: 16px !important; }
        .lp .lp-sinkron { margin: 0 16px 16px; }
        .lp .lp-tabel-bungkus { overflow: visible !important; }
        .lp .lp-tabel, .lp .lp-tabel tbody { display: block; width: 100%; min-width: 0; }
        .lp .lp-tabel thead { display: none; }
        .lp .lp-tabel tbody tr { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin: 12px 16px; padding: 14px; border: 1px solid #ebdfe3; border-radius: 10px; background: #fff !important; }
        .lp .lp-tabel tbody td { display: block; min-width: 0; padding: 0 !important; border: 0 !important; text-align: left !important; }
        .lp .lp-tabel tbody td::before { content: attr(data-label); display: block; margin-bottom: 5px; color: #96878e; font-size: 10px; font-weight: 700; }
        .lp .lp-tabel tbody td:first-child, .lp .lp-tabel tbody td:last-child, .lp .lp-tabel tbody td:nth-child(3) { grid-column: 1 / -1; }
        .lp .lp-materi, .lp .lp-catatan { max-width: none; -webkit-line-clamp: unset; line-clamp: unset; }
        .lp .lp-aksi { justify-content: flex-start; }
        .lp .lp-kosong { margin: 16px; }
        .lp .lp-foot { padding: 13px 16px !important; }
    }

    @container (max-width: 460px) {
        .lp .lp-stat-grid { gap: 10px; }
        .lp .lp-stat-card { min-height: 82px; padding: 12px; gap: 10px; }
        .lp .lp-stat-icon { flex-basis: 37px; width: 37px; height: 37px; }
        .lp .lp-stat-icon svg { width: 19px; height: 19px; }
        .lp .lp-stat-value { font-size: 21px; }
        .lp .lp-filter, .lp .lp-program { grid-template-columns: minmax(0, 1fr) !important; }
        .lp .lp-program-item { padding: 0 !important; border-left: 0 !important; }
        .lp .lp-field--aksi { grid-column: auto; }
        .lp .lp-head-aksi .lp-btn { flex: 1 1 auto; }
    }

    @container (max-width: 350px) {
        .lp .lp-stat-grid { grid-template-columns: minmax(0, 1fr); }
        .lp .lp-head-aksi { flex-direction: column; align-items: stretch; }
    }

    @media (prefers-reduced-motion: reduce) {
        .lp .lp-btn, .lp .lp-subjek { transition: none; }
    }
</style>

@php
    $parameterFilter = array_filter([
        'mata_pelajaran' => $mataPelajaran->id,
        'periode' => $filter['periode'],
        'kelompok' => $filter['kelompok'],
        'cari' => $filter['cari'],
    ], fn ($nilai) => filled($nilai));
    $parameterTambah = $parameterFilter;
@endphp

<div class="lp">
    <header class="lp-head">
        <div class="lp-head-intro">
            <nav class="lp-breadcrumb" aria-label="Navigasi halaman">
                <a href="{{ route('musyrifah.dashboard') }}">Dashboard</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('musyrifah.laporan', ['periode' => $filter['periode'] ?: now()->format('Y-m')]) }}">Laporan</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Pembelajaran</span>
            </nav>
            <h1>Laporan Pembelajaran</h1>
            <p>Kelola laporan terpisah untuk setiap mata pelajaran. Materi, jadwal, absensi, tugas, dan dokumentasi mengikuti data pembelajaran terbaru.</p>
        </div>
        <div class="lp-head-aksi">
            <a href="{{ route('musyrifah.laporan.pembelajaran.cetak', ['mataPelajaran' => $mataPelajaran->id] + $parameterFilter) }}" class="lp-btn lp-btn--garis" target="_blank" rel="noopener">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v7H6z"/></svg>
                Cetak
            </a>
            <a href="{{ route('musyrifah.laporan.pembelajaran.unduh', ['mataPelajaran' => $mataPelajaran->id] + $parameterFilter) }}" class="lp-btn lp-btn--garis">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                Unduh CSV
            </a>
            <a href="{{ route('musyrifah.laporan.pembelajaran.create', ['mataPelajaran' => $mataPelajaran->id] + $parameterTambah) }}" class="lp-btn lp-btn--utama">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Laporan
            </a>
        </div>
    </header>

    <section class="lp-stat-grid" aria-label="Ringkasan laporan">
        <article class="lp-stat-card">
            <span class="lp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
            <span class="lp-stat-info">
                <span class="lp-stat-label">Pertemuan Tercatat</span>
                <span class="lp-stat-value">{{ $ringkasan['jumlah_pertemuan'] }}</span>
                <span class="lp-stat-desc">Termasuk data absensi lama</span>
            </span>
        </article>
        <article class="lp-stat-card">
            <span class="lp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg></span>
            <span class="lp-stat-info">
                <span class="lp-stat-label">Pertemuan Terlaksana</span>
                <span class="lp-stat-value">{{ $ringkasan['pertemuan_terlaksana'] }}</span>
                <span class="lp-stat-desc">Memiliki absensi atau selesai</span>
            </span>
        </article>
        <article class="lp-stat-card">
            <span class="lp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg></span>
            <span class="lp-stat-info">
                <span class="lp-stat-label">Laporan Manual</span>
                <span class="lp-stat-value">{{ $ringkasan['laporan_manual'] }}</span>
                <span class="lp-stat-desc">Catatan tambahan Musyrifah</span>
            </span>
        </article>
        <article class="lp-stat-card">
            <span class="lp-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg></span>
            <span class="lp-stat-info">
                <span class="lp-stat-label">Persentase Kehadiran</span>
                <span class="lp-stat-value">{{ $ringkasan['persentase_kehadiran'] }}%</span>
                <span class="lp-stat-desc">{{ $ringkasan['total_hadir'] }} dari {{ $ringkasan['total_absensi'] }} absensi</span>
            </span>
        </article>
    </section>

    <section class="lp-kartu" aria-labelledby="judul-mata-pelajaran">
        <div class="lp-sec-head">
            <div>
                <h2 id="judul-mata-pelajaran">Pilih Mata Pelajaran</h2>
                <p>Setiap mata pelajaran memiliki laporan tersendiri. Data tidak digabungkan dengan mata pelajaran lain.</p>
            </div>
            <span class="lp-badge">{{ count($daftarMataPelajaran) }} mata pelajaran</span>
        </div>
        <div class="lp-subjek-grid">
            @foreach ($daftarMataPelajaran as $index => $item)
                <a href="{{ route('musyrifah.laporan.pembelajaran.index', array_filter([
                    'mata_pelajaran' => $item->id,
                    'periode' => $filter['periode'],
                ], fn ($nilai) => filled($nilai))) }}"
                   class="lp-subjek {{ (int) $item->id === (int) $mataPelajaran->id ? 'is-active' : '' }}"
                   @if ((int) $item->id === (int) $mataPelajaran->id) aria-current="page" @endif>
                    <span class="lp-subjek-nomor">{{ $index + 1 }}</span>
                    <span class="lp-subjek-isi">
                        <span class="lp-subjek-nama">{{ $item->nama }}</span>
                        <span class="lp-subjek-kode">{{ $item->kode ?: 'Tanpa kode' }}{{ $item->kategori ? ' · '.$item->kategori : '' }}</span>
                        <span class="lp-subjek-status">{{ (int) $item->id === (int) $mataPelajaran->id ? 'Sedang ditampilkan' : 'Buka laporan' }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="lp-kartu" aria-labelledby="judul-program">
        <div class="lp-sec-head">
            <div>
                <h2 id="judul-program">{{ $mataPelajaran->nama }}</h2>
                <p>Informasi program diambil dari jadwal pembelajaran. Gunakan filter kelompok untuk menampilkan jadwal kelompok yang dipilih.</p>
            </div>
            <span class="lp-badge">Laporan per mata pelajaran</span>
        </div>
        <div class="lp-program">
            <div class="lp-program-item">
                <span class="lp-program-label">Mata Pelajaran</span>
                <span class="lp-program-nilai">{{ $program['nama'] }}</span>
            </div>
            <div class="lp-program-item">
                <span class="lp-program-label">Waktu</span>
                <span class="lp-program-nilai">{{ $program['waktu'] }}</span>
            </div>
            <div class="lp-program-item">
                <span class="lp-program-label">Instruktur</span>
                <span class="lp-program-nilai">{{ $program['instruktur'] }}</span>
            </div>
            <div class="lp-program-item">
                <span class="lp-program-label">Kelompok</span>
                <span class="lp-program-nilai">{{ $program['kelompok'] }}</span>
            </div>
        </div>
        <form method="GET" action="{{ route('musyrifah.laporan.pembelajaran.index') }}" class="lp-filter">
            <input type="hidden" name="mata_pelajaran" value="{{ $mataPelajaran->id }}">
            <div class="lp-field">
                <label for="lp-periode">Bulan laporan</label>
                <input type="month" id="lp-periode" name="periode" value="{{ $filter['periode'] }}">
            </div>
            <div class="lp-field">
                <label for="lp-kelompok">Kelompok</label>
                <select id="lp-kelompok" name="kelompok">
                    <option value="">Semua kelompok</option>
                    @foreach ($daftarKelompok as $kelompok)
                        <option value="{{ $kelompok }}" @selected($filter['kelompok'] === $kelompok)>{{ $kelompok }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lp-field">
                <label for="lp-cari">Kata kunci</label>
                <input type="search" id="lp-cari" name="cari" value="{{ $filter['cari'] }}" placeholder="Cari materi, instruktur, atau catatan...">
            </div>
            <div class="lp-field lp-field--aksi">
                <button type="submit" class="lp-btn lp-btn--utama">Tampilkan</button>
                <a href="{{ route('musyrifah.laporan.pembelajaran.index', ['mata_pelajaran' => $mataPelajaran->id]) }}" class="lp-btn lp-btn--garis">Reset</a>
            </div>
        </form>
        <div class="lp-sinkron">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            <span><strong>Sinkron otomatis.</strong> Baris “Data pembelajaran” dibaca langsung dari Manajemen Pembelajaran. Tombol Lengkapi hanya menambah tanda tangan dan catatan laporan tanpa mengubah data pertemuan asli.</span>
        </div>
    </section>

    <section class="lp-kartu" aria-labelledby="judul-daftar-laporan">
        <div class="lp-sec-head">
            <div>
                <h2 id="judul-daftar-laporan">Daftar Laporan Pembelajaran</h2>
                <p>{{ count($barisLaporan) }} baris ditampilkan untuk mata pelajaran dan filter yang dipilih.</p>
            </div>
            <div class="lp-sec-aksi">
                <a href="{{ route('musyrifah.manajemen_pembelajaran') }}" class="lp-btn lp-btn--garis lp-btn--kecil">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    Manajemen Pembelajaran
                </a>
                <a href="{{ route('musyrifah.laporan.pembelajaran.create', ['mataPelajaran' => $mataPelajaran->id] + $parameterTambah) }}" class="lp-btn lp-btn--utama lp-btn--kecil">Tambah Laporan</a>
            </div>
        </div>

        @if ($barisLaporan !== [])
            <div class="lp-tabel-bungkus">
                <table class="lp-tabel">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Hari / Tanggal</th>
                            <th scope="col">Materi</th>
                            <th scope="col">Instruktur / Tanda Tangan</th>
                            <th scope="col">Kehadiran</th>
                            <th scope="col">Sumber</th>
                            <th scope="col" class="lp-kanan">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barisLaporan as $index => $item)
                            <tr>
                                <td class="lp-nomor" data-label="No">{{ $index + 1 }}</td>
                                <td data-label="Hari / Tanggal">
                                    <span class="lp-tanggal">{{ $item['hari'] }}, {{ $item['tanggal'] }}</span>
                                    @if ($item['pertemuan_ke'])
                                        <span class="lp-sub">Pertemuan {{ $item['pertemuan_ke'] }} · {{ $item['kelompok'] }}</span>
                                    @else
                                        <span class="lp-sub">{{ $item['kelompok'] }} · {{ $item['periode_semester'] }}</span>
                                    @endif
                                </td>
                                <td data-label="Materi">
                                    <span class="lp-materi">{{ $item['materi'] }}</span>
                                    @if (filled($item['catatan']))
                                        <span class="lp-catatan">Catatan: {{ $item['catatan'] }}</span>
                                    @endif
                                </td>
                                <td data-label="Instruktur / Tanda Tangan">
                                    <span class="lp-sub">{{ $item['instruktur'] }}</span>
                                    <span class="lp-ttd">{{ filled($item['tanda_tangan']) ? $item['tanda_tangan'] : '________________' }}</span>
                                </td>
                                <td data-label="Kehadiran">
                                    <span class="lp-absensi">
                                        <span class="lp-absen lp-absen--hadir" title="Hadir">H {{ $item['hadir'] }}</span>
                                        <span class="lp-absen lp-absen--izin" title="Izin">I {{ $item['izin'] }}</span>
                                        <span class="lp-absen lp-absen--sakit" title="Sakit">S {{ $item['sakit'] }}</span>
                                        <span class="lp-absen lp-absen--alpa" title="Alpa">A {{ $item['alpa'] }}</span>
                                    </span>
                                    <span class="lp-sub">Total {{ $item['total_absensi'] }} absensi</span>
                                </td>
                                <td data-label="Sumber">
                                    @if ($item['jenis'] === 'manual')
                                        <span class="lp-badge lp-badge--manual">Laporan manual</span>
                                    @elseif ($item['pertemuan_id'] === null)
                                        <span class="lp-badge lp-badge--lama">Data absensi</span>
                                    @else
                                        <span class="lp-badge">Data pembelajaran</span>
                                    @endif
                                    <span class="lp-sub">{{ $item['status'] }}</span>
                                    @if ($item['jenis'] === 'otomatis' && $item['pertemuan_id'] !== null)
                                        <span class="lp-sub">{{ $item['jumlah_tugas'] }} tugas · {{ $item['jumlah_dokumentasi'] }} dokumentasi</span>
                                    @endif
                                </td>
                                <td data-label="Aksi">
                                    <div class="lp-aksi">
                                        @if ($item['laporan_id'])
                                            <a href="{{ route('musyrifah.laporan.pembelajaran.edit', ['mataPelajaran' => $mataPelajaran->id, 'laporan' => $item['laporan_id']] + $parameterFilter) }}" class="lp-btn lp-btn--garis lp-btn--kecil">Edit</a>
                                            <form method="POST"
                                                  action="{{ route('musyrifah.laporan.pembelajaran.destroy', ['mataPelajaran' => $mataPelajaran->id, 'laporan' => $item['laporan_id']] + $parameterFilter) }}"
                                                  data-sui-confirm="Hapus laporan ini? Data pembelajaran asli tidak akan dihapus."
                                                  data-sui-confirm-title="Hapus laporan?"
                                                  data-sui-confirm-label="Ya, Hapus"
                                                  data-sui-confirm-tone="danger">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="lp-btn lp-btn--bahaya lp-btn--kecil">Hapus</button>
                                            </form>
                                        @elseif ($item['pertemuan_id'])
                                            <a href="{{ route('musyrifah.laporan.pembelajaran.create', ['mataPelajaran' => $mataPelajaran->id, 'pertemuan_id' => $item['pertemuan_id']] + $parameterFilter) }}" class="lp-btn lp-btn--utama lp-btn--kecil">Lengkapi</a>
                                        @else
                                            <span class="lp-sub">Sumber absensi</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="lp-kosong">
                <strong>Belum ada data laporan</strong>
                Tidak ada baris yang cocok dengan mata pelajaran dan filter ini. Tambahkan laporan manual atau lengkapi data pertemuan dari Manajemen Pembelajaran.
                <br>
                <a href="{{ route('musyrifah.laporan.pembelajaran.create', ['mataPelajaran' => $mataPelajaran->id] + $parameterTambah) }}" class="lp-btn lp-btn--utama">Tambah Laporan</a>
            </div>
        @endif

        <div class="lp-foot">
            <span>Data otomatis selalu mengikuti perubahan terbaru pada pembelajaran.</span>
            <a href="{{ route('musyrifah.laporan', ['periode' => $filter['periode'] ?: now()->format('Y-m')]) }}">&lsaquo; Kembali ke menu Laporan</a>
        </div>
    </section>
</div>
@endsection
