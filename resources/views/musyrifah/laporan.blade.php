@extends('layouts.musyrifah')
@section('title', 'Laporan Musyrifah')
@section('content')
<style>
/* SIMAHAD — Laporan Musyrifah, konsisten dengan Manajemen Data Kamar. */
.lap {
    --lap-maroon: #800b22; --lap-deep: #620819; --lap-ink: #292427;
    --lap-muted: #82797e; --lap-border: #eddee1; --lap-soft: #fbf5f7;
    box-sizing: border-box; display: block !important; position: relative;
    width: 100% !important; max-width: 100% !important; min-width: 0;
    clear: both; margin: 0 !important; padding: 0 0 28px;
    color: var(--lap-ink); font-family: inherit; font-size: 13px;
    container-type: inline-size;
}
.lap *, .lap *::before, .lap *::after { box-sizing: border-box; }
.lap a { text-decoration: none; }
.lap button, .lap input, .lap select { font-family: inherit; }
.lap svg { display: block; width: 18px; height: 18px; flex: none; }

/* Header, sama dengan Data Kamar */
.lap .lap-head {
    display: flex; align-items: flex-end; justify-content: space-between;
    flex-wrap: wrap; gap: 15px; width: 100%; margin: 0 0 21px;
    padding: 0 0 19px; border-bottom: 1px solid #e8cfd6;
}
.lap .lap-head-intro { flex: 1 1 320px; min-width: 0; }
.lap .lap-breadcrumb { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; margin: 0 0 11px; color: #8e8487; font-size: 12px; }
.lap .lap-breadcrumb a { color: var(--lap-maroon); }
.lap .lap-breadcrumb a:hover { text-decoration: underline; }
.lap .lap-head h1 { margin: 0 0 6px; color: #282126; font-size: clamp(22px, 2.5vw, 27px); font-weight: 750; line-height: 1.3; letter-spacing: -.55px; }
.lap .lap-head p { margin: 0; max-width: 740px; color: #797076; font-size: 12.5px; line-height: 1.65; }
.lap .lap-head-aksi { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; }

/* Buttons */
.lap .lap-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 40px; padding: 9px 14px; border: 1px solid transparent; border-radius: 9px; background: #fff; color: var(--lap-maroon); font-size: 12px; font-weight: 700; line-height: 1.25; text-decoration: none; cursor: pointer; transition: background .18s ease, border-color .18s ease, transform .18s ease; }
.lap .lap-btn--utama { color: #fff; background: var(--lap-maroon); border-color: var(--lap-maroon); }
.lap .lap-btn--utama:hover { background: var(--lap-deep); border-color: var(--lap-deep); }
.lap .lap-btn--garis { color: var(--lap-maroon); border-color: #e2cfd4; background: #fff; }
.lap .lap-btn--garis:hover { background: #fdf4f6; border-color: #bc9ca5; }
.lap .lap-btn--kecil { min-height: 32px; padding: 7px 11px; font-size: 11px; border-radius: 7px; white-space: nowrap; }
.lap .lap-btn:focus-visible, .lap .lap-page:focus-visible, .lap .lap-kat:focus-visible { outline: 2px solid #9b4962; outline-offset: 2px; }

/* Feedback */
.lap .lap-alert { margin: 0 0 18px; padding: 13px 16px; border: 1px solid #e6d6da; border-radius: 10px; color: #6b0d1b; background: #fff; font-size: 12px; line-height: 1.6; }
.lap .lap-alert--success { color: #176437; border-color: #bde8cb; background: #eefbf2; }
.lap .lap-alert--error { color: #a0223d; border-color: #f2c6d0; background: #fff1f3; }
.lap .lap-alert p { margin: 0; }
.lap .lap-alert ul { margin: 6px 0 0; padding-left: 18px; }

/* Four clean stats with left icons */
.lap .lap-stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 13px; width: 100%; min-width: 0; margin: 0 0 20px; }
.lap .lap-stat-card { display: flex; align-items: center; gap: 13px; min-width: 0; min-height: 97px; padding: 17px 18px; border: 1px solid var(--lap-border); border-radius: 12px; background: #fff; box-shadow: 0 3px 12px rgba(57,19,30,.035); }
.lap .lap-stat-icon { display: grid; place-items: center; flex: 0 0 44px; width: 44px; height: 44px; border-radius: 11px; background: #f7e9ed; color: var(--lap-maroon); }
.lap .lap-stat-icon svg { width: 22px; height: 22px; }
.lap .lap-stat-card:nth-child(2) .lap-stat-icon { background: #fdf2de; color: #a96c10; }
.lap .lap-stat-card:nth-child(3) .lap-stat-icon { background: #e8f8ef; color: #1e7849; }
.lap .lap-stat-card:nth-child(4) .lap-stat-icon { background: #e9f2ff; color: #346ba4; }
.lap .lap-stat-info { display: flex; flex-direction: column; min-width: 0; }
.lap .lap-stat-label { color: #81777d; font-size: 11px; font-weight: 600; line-height: 1.4; }
.lap .lap-stat-value { margin-top: 3px; color: #292427; font-size: 24px; font-weight: 750; line-height: 1.2; font-variant-numeric: tabular-nums; }
.lap .lap-stat-desc { margin-top: 4px; color: #93888e; font-size: 10.5px; line-height: 1.5; overflow-wrap: anywhere; }

/* White section panels */
.lap .lap-kartu { min-width: 0; margin: 0 0 19px; border: 1px solid var(--lap-border); border-radius: 13px; background: #fff; box-shadow: 0 3px 14px rgba(62,10,25,.035); overflow: hidden; }
.lap .lap-sec-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding: 19px 23px; border-bottom: 1px solid #f0e8e9; }
.lap .lap-sec-head h2 { margin: 0 0 5px; color: #2c2529; font-size: 16px; font-weight: 750; line-height: 1.3; }
.lap .lap-sec-head p { margin: 0; max-width: 840px; color: var(--lap-muted); font-size: 11.5px; line-height: 1.65; }
.lap .lap-kartu > .lap-kosong { margin: 18px 23px; }

/* Filter panels: inputs match Data Kamar */
.lap .lap-filter { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); align-items: end; gap: 12px; padding: 18px 23px; border-bottom: 1px solid #f2e9ec; background: #fff; }
.lap #riwayat-periodik .lap-filter { grid-template-columns: repeat(5, minmax(0, 1fr)); }
.lap .lap-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
.lap .lap-field label { color: #55484d; font-size: 11.5px; font-weight: 700; line-height: 1.4; }
.lap .lap-field select, .lap .lap-field input[type="text"] { display: block; width: 100%; height: 43px; min-width: 0; padding: 9px 12px; border: 1px solid #e5d6da; border-radius: 9px; background: #fff; color: #332a2e; font-size: 12px; outline: none; }
.lap .lap-field select:focus, .lap .lap-field input[type="text"]:focus { border-color: #a96075; box-shadow: 0 0 0 3px rgba(128,11,34,.08); }
.lap .lap-field--aksi { display: flex; flex-direction: row; align-items: flex-end; flex-wrap: wrap; gap: 8px; }
.lap .lap-field--aksi .lap-btn { min-height: 43px; }
.lap .lap-field--lebar { grid-column: 1 / -1; }
.lap .lap-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; padding: 20px 23px 23px; }
.lap .lap-form-aksi { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; padding-top: 2px; }

/* Category cards */
.lap .lap-kategori { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 13px; min-width: 0; padding: 20px 23px 23px; }
.lap .lap-kat { display: flex; align-items: flex-start; gap: 12px; min-width: 0; min-height: 104px; padding: 16px; border: 1px solid #e9dfe2; border-radius: 11px; background: #fff; transition: border-color .18s, box-shadow .18s, transform .18s; }
.lap .lap-kat:hover { border-color: #d9b5c0; background: #fdf9fa; box-shadow: 0 9px 20px rgba(58,15,28,.07); transform: translateY(-1px); }
.lap .lap-kat-ikon { display: grid; place-items: center; flex: 0 0 42px; width: 42px; height: 42px; border-radius: 11px; background: #f7e9ed; color: var(--lap-maroon); }
.lap .lap-kat-ikon svg { width: 21px; height: 21px; }
.lap .lap-kat-isi { display: flex; flex-direction: column; gap: 3px; min-width: 0; }
.lap .lap-kat-nama { color: #342b30; font-size: 12.5px; font-weight: 750; line-height: 1.45; overflow-wrap: anywhere; }
.lap .lap-kat-ket { display: -webkit-box; margin-top: 1px; color: #867b81; font-size: 11px; line-height: 1.55; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; }
.lap .lap-kat-go { margin-top: 4px; color: var(--lap-maroon); font-size: 11px; font-weight: 750; }
.lap .lap-kosong--full { grid-column: 1 / -1; }

/* Draft and register side by side only when there is enough content width */
.lap .lap-dua { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr); gap: 16px; margin-bottom: 19px; }
.lap .lap-dua .lap-kartu { margin: 0; }
.lap .lap-dua .lap-sec-head { min-height: 92px; }

/* Compact tables, maroon header, no horizontal page overflow */
.lap .lap-tabel-bungkus { display: block; width: 100%; max-width: 100%; min-width: 0; overflow-x: auto; overscroll-behavior-inline: contain; }
.lap .lap-tabel { width: 100%; min-width: 740px; border-collapse: collapse; background: #fff; }
.lap .lap-tabel--ringkas { min-width: 480px; }
.lap .lap-tabel th { padding: 13px 14px; border: 0; background: var(--lap-maroon); color: #fff; font-size: 11px; font-weight: 700; line-height: 1.3; text-align: left; white-space: nowrap; }
.lap .lap-tabel td { padding: 13px 14px; border-bottom: 1px solid #f1e8eb; color: #534c50; font-size: 11.5px; line-height: 1.5; vertical-align: middle; overflow-wrap: anywhere; }
.lap .lap-tabel tbody tr:nth-child(even) { background: #fdfafb; }
.lap .lap-tabel tbody tr:hover { background: #fcf4f6; }
.lap .lap-tabel tbody tr:last-child td { border-bottom: none; }
.lap .lap-sel { display: block; color: #30282d; font-size: 12px; font-weight: 750; line-height: 1.45; }
.lap .lap-sub { display: block; margin-top: 3px; color: #8f8288; font-size: 10.5px; line-height: 1.5; }
.lap .lap-badge { display: inline-flex; max-width: 100%; padding: 5px 9px; border: 1px solid #eddae0; border-radius: 20px; background: #f8eff2; color: #80344a; font-size: 10.5px; font-weight: 750; line-height: 1.3; white-space: nowrap; }
.lap .lap-kanan { text-align: right; }
.lap .lap-aksi { display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 6px; }
.lap .lap-aksi form { display: inline-flex; margin: 0; }
.lap .lap-dua .lap-tabel-bungkus { margin: 0; }
.lap .lap-kosong { margin: 0; padding: 24px 16px; border: 1px dashed #e6d7dc; border-radius: 9px; background: #fdf9fa; color: #857a7d; font-size: 11.5px; line-height: 1.6; text-align: center; }
.lap .lap-table-section { margin: 0; }
.lap .lap-table-section > .lap-kosong { margin: 18px 23px; }

/* Periodic pagination */
.lap #riwayat-periodik { scroll-margin-top: calc(var(--header-height, 68px) + 14px); }
.lap .lap-paginasi { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 16px 23px; border-top: 1px solid #f1e8eb; }
.lap .lap-paginasi-info { color: #81777d; font-size: 11px; }
.lap .lap-paginasi-nav { display: flex; align-items: center; flex-wrap: wrap; gap: 5px; }
.lap .lap-page { display: inline-flex; align-items: center; justify-content: center; min-width: 33px; min-height: 33px; padding: 7px 9px; border: 1px solid #e5d8dc; border-radius: 8px; background: #fff; color: var(--lap-maroon); font-size: 11.5px; font-weight: 700; }
.lap .lap-page:hover { background: #fdf4f6; }
.lap .lap-page.is-active { border-color: var(--lap-maroon); background: var(--lap-maroon); color: #fff; }
.lap .lap-page.is-off { color: #bfb5b9; background: #faf7f8; cursor: not-allowed; pointer-events: none; }

/* Container queries react to available content space, including expanded sidebar. */
@container (max-width: 1060px) {
    .lap .lap-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .lap .lap-dua { grid-template-columns: minmax(0, 1fr); }
    .lap .lap-dua .lap-sec-head { min-height: auto; }
    .lap #riwayat-periodik .lap-filter { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@container (max-width: 690px) {
    .lap .lap-head { align-items: flex-start; }
    .lap .lap-head-aksi { width: 100%; }
    .lap .lap-kategori { padding: 16px !important; }
    .lap .lap-sec-head { padding: 17px 16px !important; }
    .lap .lap-filter { padding: 16px !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .lap .lap-form { padding: 16px !important; }
    .lap .lap-paginasi { padding: 13px 16px !important; }
    .lap .lap-tabel-bungkus { overflow: visible !important; }
    .lap .lap-tabel, .lap .lap-tabel--ringkas, .lap .lap-tabel tbody { display: block; width: 100%; min-width: 0; }
    .lap .lap-tabel thead { display: none; }
    .lap .lap-tabel tbody tr { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 11px 12px; margin: 12px 16px; padding: 14px; border: 1px solid #ebdfe3; border-radius: 10px; background: #fff !important; }
    .lap .lap-tabel tbody td { display: block; min-width: 0; padding: 0 !important; border: 0 !important; text-align: left !important; white-space: normal !important; }
    .lap .lap-tabel tbody td::before { content: attr(data-label); display: block; margin-bottom: 4px; color: #96878e; font-size: 10px; font-weight: 700; }
    .lap .lap-tabel tbody td:first-child, .lap .lap-tabel tbody td:last-child { grid-column: 1 / -1; }
    .lap .lap-aksi { justify-content: flex-start; }
}
@container (max-width: 460px) {
    .lap .lap-stat-grid { gap: 10px; }
    .lap .lap-stat-card { min-height: 81px; padding: 12px; gap: 10px; }
    .lap .lap-stat-icon { flex-basis: 37px; width: 37px; height: 37px; }
    .lap .lap-stat-icon svg { width: 19px; height: 19px; }
    .lap .lap-stat-value { font-size: 21px; }
    .lap .lap-filter, .lap #riwayat-periodik .lap-filter { grid-template-columns: minmax(0, 1fr) !important; }
    .lap .lap-form { grid-template-columns: minmax(0, 1fr); }
    .lap .lap-field--lebar { grid-column: auto; }
    .lap .lap-head-aksi .lap-btn { flex: 1 1 auto; }
}
@container (max-width: 350px) {
    .lap .lap-stat-grid { grid-template-columns: minmax(0, 1fr); }
    .lap .lap-head-aksi { flex-direction: column; align-items: stretch; }
    .lap .lap-field--aksi .lap-btn, .lap .lap-form-aksi .lap-btn { flex: 1; }
}
@media (prefers-reduced-motion: reduce) {
    .lap .lap-btn, .lap .lap-kat { transition: none; }
}
</style>
<div class="lap">
    {{-- Header mengikuti Manajemen Data Kamar Musyrifah. --}}
    <header class="lap-head">
        <div class="lap-head-intro">
            <nav class="lap-breadcrumb" aria-label="Navigasi halaman">
                <a href="{{ route('musyrifah.dashboard') }}">Dashboard</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Laporan</span>
            </nav>
            <h1>Manajemen Laporan</h1>
            <p>Kelola rekap kategori, pencatatan laporan, dan dokumen bulanan maupun tahunan Ma’had.</p>
        </div>
        <div class="lap-head-aksi">
            <a href="#riwayat-periodik" class="lap-btn lap-btn--garis">Riwayat periodik</a>
            <a href="{{ route('musyrifah.laporan.buat') }}" class="lap-btn lap-btn--utama">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Buat Laporan Periodik
            </a>
        </div>
    </header>
    @if (session('status') || session('success'))
        <div class="lap-alert lap-alert--success" role="status">{{ session('status') ?: session('success') }}</div>
    @endif
    @if (session('error') || $errors->any())
        <div class="lap-alert lap-alert--error" role="alert">
            @if (session('error')) <p>{{ session('error') }}</p> @endif
            @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
    {{-- Hanya memakai data yang sudah dikirim controller; bukan angka contoh. --}}
    @php
        $jumlahKategoriDitampilkan = count($laporan);
        $jumlahRegisterPeriode = $register->count();
        $jumlahTerbitPeriode = $register->where('status', \App\Models\LaporanMahad::STATUS_DITERBITKAN)->count();
        $jumlahRiwayatPeriodik = $daftarPeriodik->total();
    @endphp
    <section class="lap-stat-grid" aria-label="Ringkasan laporan">
        <article class="lap-stat-card">
            <span class="lap-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg></span>
            <div class="lap-stat-info"><span class="lap-stat-label">Kategori ditampilkan</span><strong class="lap-stat-value">{{ $jumlahKategoriDitampilkan }}</strong><span class="lap-stat-desc">Rekap sesuai filter</span></div>
        </article>
        <article class="lap-stat-card">
            <span class="lap-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3h8l4 4v14H4V3z"/><path d="M16 3v5h4M8 12h8M8 16h6"/></svg></span>
            <div class="lap-stat-info"><span class="lap-stat-label">Register laporan</span><strong class="lap-stat-value">{{ $jumlahRegisterPeriode }}</strong><span class="lap-stat-desc">{{ $labelPeriode }}</span></div>
        </article>
        <article class="lap-stat-card">
            <span class="lap-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg></span>
            <div class="lap-stat-info"><span class="lap-stat-label">Sudah diterbitkan</span><strong class="lap-stat-value">{{ $jumlahTerbitPeriode }}</strong><span class="lap-stat-desc">Dari register periode</span></div>
        </article>
        <article class="lap-stat-card">
            <span class="lap-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 10h18M8 14h3M8 17h6"/></svg></span>
            <div class="lap-stat-info"><span class="lap-stat-label">Riwayat periodik</span><strong class="lap-stat-value">{{ $jumlahRiwayatPeriodik }}</strong><span class="lap-stat-desc">Sesuai filter riwayat</span></div>
        </article>
    </section>
    {{-- REKAP PER KATEGORI --}}
    <section class="lap-kartu">
        <div class="lap-sec-head">
            <div>
                <h2>Rekap per Kategori</h2>
                <p>Ringkasan cepat untuk periode {{ $labelPeriode }}.</p>
            </div>
        </div>
        <form action="{{ route('musyrifah.laporan') }}" method="GET" class="lap-filter">
            <div class="lap-field">
                <label for="periode">Periode laporan</label>
                <select name="periode" id="periode">
                    @foreach ($opsiPeriode as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($periode === $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lap-field">
                <label for="jenis">Jenis laporan</label>
                <select name="jenis" id="jenis">
                    <option value="semua" @selected($jenis === 'semua')>Semua laporan</option>
                    <option value="kehadiran" @selected($jenis === 'kehadiran')>Laporan Kehadiran</option>
                    <option value="pembayaran" @selected($jenis === 'pembayaran')>Laporan Pembayaran</option>
                    <option value="pendaftaran" @selected($jenis === 'pendaftaran')>Laporan Pendaftaran</option>
                    <option value="kamar" @selected($jenis === 'kamar')>Laporan Data Kamar</option>
                </select>
            </div>
            <div class="lap-field lap-field--aksi">
                <button type="submit" class="lap-btn lap-btn--utama">Tampilkan</button>
            </div>
        </form>
        <div class="lap-kategori">
            @forelse ($laporan as $item)
                <a href="{{ $item['url'] }}" class="lap-kat">
                    <span class="lap-kat-ikon" aria-hidden="true">
                        @if ($item['jenis'] === 'kehadiran')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18m-13 5 2 2 4-4"/></svg>
                        @elseif ($item['jenis'] === 'pembayaran')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h5"/></svg>
                        @elseif ($item['jenis'] === 'pendaftaran')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17H4zM9 21v-5h6v5M8 7h2m4 0h2M8 12h2m4 0h2"/></svg>
                        @endif
                    </span>
                    <span class="lap-kat-isi">
                        <span class="lap-kat-nama">{{ $item['judul'] }}</span>
                        <span class="lap-kat-ket">{{ $item['deskripsi'] }}</span>
                        <span class="lap-kat-go">Lihat Laporan &rsaquo;</span>
                    </span>
                </a>
            @empty
                <div class="lap-kosong lap-kosong--full">
                    Tidak ada jenis laporan yang tersedia.
                </div>
            @endforelse
        </div>
    </section>
    {{-- CATAT LAPORAN & REGISTER PERIODE --}}
    <div class="lap-dua">
        <section class="lap-kartu">
            <div class="lap-sec-head">
                <div>
                    <h2>Catat Laporan Baru</h2>
                    <p>Draft tercatat atas nama Anda beserta unit sumber laporan.</p>
                </div>
            </div>
            <form action="{{ route('musyrifah.laporan.simpan') }}" method="POST" class="lap-form">
                @csrf
                <div class="lap-field">
                    <label for="reg-jenis-baru">Jenis laporan</label>
                    <select name="jenis" id="reg-jenis-baru" required>
                        @foreach ($opsiJenis as $opsi)
                            <option value="{{ $opsi['value'] }}">{{ $opsi['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lap-field">
                    <label for="reg-periode-baru">Periode laporan</label>
                    <select name="periode" id="reg-periode-baru" required>
                        @foreach ($opsiPeriode as $opsi)
                            <option value="{{ $opsi['value'] }}" @selected($periode === $opsi['value'])>
                                {{ $opsi['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lap-field lap-field--lebar">
                    <label for="reg-lantai-baru">Lantai sumber laporan</label>
                    <select name="lantai" id="reg-lantai-baru">
                        <option value="">Seluruh Ma’had</option>
                        @foreach ($opsiLantai as $opsi)
                            <option value="{{ $opsi['value'] }}">{{ $opsi['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lap-field lap-field--lebar">
                    <label for="reg-catatan-baru">Catatan (opsional)</label>
                    <input
                        type="text"
                        name="catatan"
                        id="reg-catatan-baru"
                        maxlength="500"
                        placeholder="Misalnya: rekap kehadiran pekan ke-2"
                    >
                </div>
                <div class="lap-form-aksi lap-field--lebar">
                    <button type="submit" class="lap-btn lap-btn--utama">Simpan sebagai Draft</button>
                </div>
            </form>
        </section>
        <section class="lap-kartu">
            <div class="lap-sec-head">
                <div>
                    <h2>Register Laporan</h2>
                    <p>Periode {{ $labelPeriode }}.</p>
                </div>
            </div>
            @if ($register->isEmpty())
                <div class="lap-kosong">
                    Belum ada laporan yang Anda catat pada periode ini.
                </div>
            @else
                <div class="lap-tabel-bungkus">
                    <table class="lap-tabel lap-tabel--ringkas">
                        <thead>
                            <tr>
                                <th>Laporan</th>
                                <th>Status</th>
                                <th class="lap-kanan">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($register as $item)
                                <tr>
                                    <td data-label="Laporan">
                                        <span class="lap-sel">{{ $item->judul }}</span>
                                        <span class="lap-sub">
                                            {{ $item->labelUnit() }} · {{ $item->identitasPembuat() }}
                                        </span>
                                    </td>
                                    <td data-label="Status">
                                        <span class="lap-badge">{{ $item->labelStatus() }} · v{{ (int) $item->versi }}</span>
                                    </td>
                                    <td class="lap-kanan" data-label="Aksi">
                                        <div class="lap-aksi">
                                            @if ($item->status !== \App\Models\LaporanMahad::STATUS_DITERBITKAN)
                                                <form action="{{ route('musyrifah.laporan.terbitkan', $item) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="lap-btn lap-btn--utama lap-btn--kecil">
                                                        Terbitkan
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($item->status !== \App\Models\LaporanMahad::STATUS_DIARSIPKAN)
                                                <form action="{{ route('musyrifah.laporan.arsipkan', $item) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="lap-btn lap-btn--garis lap-btn--kecil">
                                                        Arsipkan
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
    {{-- RIWAYAT LAPORAN PERIODIK --}}
    <section class="lap-kartu" id="riwayat-periodik">
        <div class="lap-sec-head">
            <div>
                <h2>Riwayat Laporan Bulanan &amp; Tahunan</h2>
                <p>
                    Dokumen tetap: isinya tidak berubah walaupun data operasional setelah
                    tanggal pembuatan diperbarui.
                </p>
            </div>
        </div>
        <form action="{{ route('musyrifah.laporan') }}#riwayat-periodik" method="GET" class="lap-filter">
            <div class="lap-field">
                <label for="p_jenis">Jenis laporan</label>
                <select name="p_jenis" id="p_jenis">
                    <option value="">Semua jenis</option>
                    @foreach ($opsiJenisPeriodik as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($filterPeriodik['jenis'] === $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lap-field">
                <label for="p_tahun">Tahun</label>
                <select name="p_tahun" id="p_tahun">
                    <option value="">Semua tahun</option>
                    @foreach ($opsiTahun as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($filterPeriodik['tahun'] === (string) $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lap-field">
                <label for="p_bulan">Bulan (bulanan)</label>
                <select name="p_bulan" id="p_bulan">
                    <option value="">Semua bulan</option>
                    @foreach ($opsiBulan as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($filterPeriodik['bulan'] === (string) $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lap-field">
                <label for="p_status">Status</label>
                <select name="p_status" id="p_status">
                    <option value="">Semua status</option>
                    @foreach ($opsiStatusPeriodik as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($filterPeriodik['status'] === $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lap-field lap-field--aksi">
                <button type="submit" class="lap-btn lap-btn--utama">Terapkan</button>
                <a href="{{ route('musyrifah.laporan') }}#riwayat-periodik" class="lap-btn lap-btn--garis">Reset</a>
            </div>
        </form>
        <div class="lap-table-section">
            @if ($daftarPeriodik->isEmpty())
                <div class="lap-kosong">
                    Belum ada laporan bulanan atau tahunan yang tersimpan. Gunakan tombol
                    “Buat Laporan” untuk menyusun laporan pertama.
                </div>
            @else
                <div class="lap-tabel-bungkus">
                    <table class="lap-tabel">
                        <thead>
                            <tr>
                                <th>Laporan</th>
                                <th>Periode</th>
                                <th>Pembuat</th>
                                <th>Status</th>
                                <th class="lap-kanan">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftarPeriodik as $item)
                                <tr>
                                    <td data-label="Laporan"><span class="lap-sel">{{ $item->judul }}</span></td>
                                    <td data-label="Periode">{{ $item->labelPeriode() }}</td>
                                    <td data-label="Pembuat">{{ $item->identitasPembuat() }}</td>
                                    <td data-label="Status">
                                        <span class="lap-badge">{{ $item->labelStatus() }} · v{{ (int) $item->versi }}</span>
                                    </td>
                                    <td class="lap-kanan" data-label="Aksi">
                                        <div class="lap-aksi">
                                            <a href="{{ route('musyrifah.laporan.periodik', $item) }}"
                                                class="lap-btn lap-btn--garis lap-btn--kecil">
                                                Buka
                                            </a>
                                            <a href="{{ route('musyrifah.laporan.periodik.pdf', $item) }}"
                                                class="lap-btn lap-btn--garis lap-btn--kecil">
                                                PDF
                                            </a>
                                            <a href="{{ route('musyrifah.laporan.periodik.excel', $item) }}"
                                                class="lap-btn lap-btn--garis lap-btn--kecil">
                                                Excel
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($daftarPeriodik->hasPages())
                    @php
                        $halamanAwal = max(1, $daftarPeriodik->currentPage() - 2);
                        $halamanAkhir = min($daftarPeriodik->lastPage(), $daftarPeriodik->currentPage() + 2);
                    @endphp
                    <div class="lap-paginasi">
                        <span class="lap-paginasi-info">
                            Menampilkan {{ $daftarPeriodik->firstItem() }}–{{ $daftarPeriodik->lastItem() }}
                            dari {{ $daftarPeriodik->total() }} laporan
                        </span>
                        <div class="lap-paginasi-nav">
                            @if ($daftarPeriodik->onFirstPage())
                                <span class="lap-page is-off">&lsaquo;</span>
                            @else
                                <a href="{{ $daftarPeriodik->previousPageUrl() }}#riwayat-periodik" class="lap-page"
                                    rel="prev">&lsaquo;</a>
                            @endif
                            @foreach ($daftarPeriodik->getUrlRange($halamanAwal, $halamanAkhir) as $halaman => $url)
                                @if ($halaman === $daftarPeriodik->currentPage())
                                    <span class="lap-page is-active" aria-current="page">{{ $halaman }}</span>
                                @else
                                    <a href="{{ $url }}#riwayat-periodik" class="lap-page">{{ $halaman }}</a>
                                @endif
                            @endforeach
                            @if ($daftarPeriodik->hasMorePages())
                                <a href="{{ $daftarPeriodik->nextPageUrl() }}#riwayat-periodik" class="lap-page"
                                    rel="next">&rsaquo;</a>
                            @else
                                <span class="lap-page is-off">&rsaquo;</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>
@endsection
