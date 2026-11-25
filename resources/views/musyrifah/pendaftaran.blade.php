@extends('layouts.musyrifah')

@section('title', 'Pendaftaran Mahasiswa')

@section('content')

<style>
/* SIMAHAD | Pendaftaran Mahasiswa - gaya selaras Manajemen Pengguna Admin.
   Seluruh kelas dibatasi ke .pd supaya tidak mengubah layout Musyrifah. */
.pd {
    --pd-maroon: #800b22;
    --pd-maroon-dark: #63091c;
    --pd-text: #282329;
    --pd-muted: #81777e;
    --pd-border: #eadde0;
    --pd-soft: #fbf5f7;
    --pd-gold: #c9a96a;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    margin: 0;
    padding: 0 0 26px;
    color: var(--pd-text);
    font: inherit;
    font-size: 13px;
    container-type: inline-size;
    display: flow-root;
    height: auto;
    min-height: 0;
}
.pd *, .pd *::before, .pd *::after { box-sizing: border-box; }
.pd a { text-decoration: none; }
.pd button, .pd input, .pd textarea { font-family: inherit; }
.pd button:focus-visible, .pd a:focus-visible, .pd input:focus-visible,
.pd textarea:focus-visible { outline: 3px solid rgba(128,11,34,.23); outline-offset: 2px; }

/* Header mengikuti pola halaman Manajemen Pengguna. */
.pd .pd-page-header {
    padding: 0 0 19px;
    margin: 0 0 22px;
    border-bottom: 1px solid #e8cfd6;
}
.pd .pd-breadcrumb { display: flex; align-items: center; flex-wrap: wrap; gap: 5px; margin-bottom: 12px; color: #8c8487; font-size: 12px; }
.pd .pd-breadcrumb a { color: var(--pd-maroon); }
.pd .pd-breadcrumb a:hover { text-decoration: underline; }
.pd .pd-page-header h1 { margin: 0 0 6px; font-size: clamp(22px, 2.4vw, 28px); line-height: 1.3; font-weight: 750; letter-spacing: -.025em; color: #251f24; }
.pd .pd-page-header p { margin: 0; color: #7d7378; font-size: 13px; line-height: 1.65; }

/* Pesan status Laravel. */
.pd .pd-alert { margin-bottom: 16px; padding: 12px 16px; border: 1px solid var(--pd-border); border-radius: 10px; font-size: 12px; line-height: 1.6; }
.pd .pd-alert--success { background: #edfbf1; border-color: #b7e5c8; color: #176c3b; }
.pd .pd-alert--error { background: #fff2f3; border-color: #efc3cb; color: #9c2540; }
.pd .pd-alert ul { padding-left: 19px; margin: 5px 0 0; }

/* Tiga kartu statistik - dimensi dan intensitas lembut. */
.pd .pd-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px; }
.pd .pd-stat { display: flex; align-items: center; gap: 13px; min-width: 0; min-height: 91px; padding: 16px 18px; border: 1px solid var(--pd-border); border-radius: 12px; background: #fff; box-shadow: 0 3px 12px rgba(61, 10, 25, .028); }
.pd .pd-stat-icon { display: grid; place-items: center; flex: 0 0 43px; width: 43px; height: 43px; border-radius: 11px; background: #f7e9ed; color: var(--pd-maroon); }
.pd .pd-stat-icon svg { width: 20px; height: 20px; stroke: currentColor; }
.pd .pd-stat-icon--amber { background: #fff4df; color: #a96911; }
.pd .pd-stat-icon--orange { background: #fff0e2; color: #b56416; }
.pd .pd-stat-copy { min-width: 0; }
.pd .pd-stat-copy span { display: block; margin-bottom: 4px; color: #81777e; font-size: 11.5px; font-weight: 550; line-height: 1.4; }
.pd .pd-stat-copy strong { color: #32272d; font-size: 23px; font-weight: 750; line-height: 1.1; font-variant-numeric: tabular-nums; }


/* PERBAIKAN POSISI DAFTAR: hindari style global tag section pada layout
   Musyrifah, pastikan statistik dan panel selalu berada pada alur normal. */
.pd > .pd-page-header,
.pd > .pd-stats,
.pd > .pd-panel {
    position: relative;
    inset: auto;
    float: none !important;
    clear: both;
    width: 100% !important;
    max-width: none !important;
    min-width: 0 !important;
    height: auto !important;
    min-height: 0 !important;
    box-sizing: border-box;
    transform: none;
}
.pd > .pd-page-header,
.pd > .pd-panel { display: block !important; }
.pd > .pd-stats { display: grid !important; }
.pd > .pd-panel { margin: 0 !important; }
.pd > .pd-stats { margin-top: 0 !important; margin-bottom: 20px !important; }
.pd .pd-panel-head,
.pd .pd-toolbar,
.pd .pd-tabel-wrap,
.pd .pd-table-footer { width: 100%; max-width: 100%; min-width: 0; }
.pd .pd-tabel { table-layout: auto; }
/* Dialog detail tetap merupakan overlay, bukan bagian susunan halaman. */
.pd .pd-modal:not([open]) { display: none; }

/* Kartu utama: header, pencarian, filter dan tabel dalam satu bingkai. */
.pd .pd-panel { overflow: hidden; min-width: 0; background: #fff; border: 1px solid #eadfe1; border-radius: 13px; box-shadow: 0 3px 15px rgba(62, 10, 25, .036); }
.pd .pd-panel-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 21px 24px; border-bottom: 1px solid #f0e8ea; }
.pd .pd-panel-head h2 { margin: 0 0 5px; font-size: 17px; font-weight: 750; line-height: 1.4; color: #231d21; }
.pd .pd-panel-head p { margin: 0; color: var(--pd-muted); font-size: 12px; line-height: 1.45; }
.pd .pd-head-badge { display: inline-flex; align-items: center; gap: 7px; padding: 8px 11px; border: 1px solid #e7d9de; border-radius: 8px; color: #704352; background: #fcf6f7; font-size: 11px; font-weight: 650; }
.pd .pd-head-badge svg { width: 15px; height: 15px; }
.pd .pd-toolbar { padding: 19px 24px 14px; }
.pd .pd-search-row { display: flex; align-items: stretch; gap: 9px; }
.pd .pd-cari { display: flex; align-items: center; flex: 1; gap: 10px; min-width: 0; min-height: 52px; padding: 5px 13px; border: 1px solid #e6d9dd; border-radius: 10px; background: #fff; transition: border-color .18s, box-shadow .18s; }
.pd .pd-cari:focus-within { border-color: #ad6d7c; box-shadow: 0 0 0 3px rgba(128,11,34,.07); }
.pd .pd-cari svg { flex: none; width: 18px; height: 18px; color: #95858b; }
.pd .pd-cari input { flex: 1; width: 100%; min-width: 0; height: 38px; padding: 0; border: 0; outline: 0; background: transparent; color: #362d32; font-size: 12px; }
.pd .pd-cari input:focus-visible { outline: none; }
.pd .pd-cari input::placeholder { color: #98919a; }
.pd .pd-search-reset { flex: none; padding: 0 15px; border: 1px solid #e9dce0; border-radius: 9px; background: #fff; color: var(--pd-maroon); font-size: 12px; font-weight: 650; cursor: pointer; }
.pd .pd-search-reset:hover { background: var(--pd-soft); }
.pd .pd-search-reset[hidden] { display: none !important; }
.pd .pd-filters { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
.pd .pd-filter { min-height: 34px; padding: 7px 14px; border: none; border-radius: 20px; background: #f1eef0; color: #4d4248; font-size: 11.5px; font-weight: 650; line-height: 1.35; cursor: pointer; transition: background .18s, color .18s; }
.pd .pd-filter:hover { background: #e9dce1; color: var(--pd-maroon); }
.pd .pd-filter[aria-pressed="true"] { background: var(--pd-maroon); color: #fff; }
.pd .pd-toolbar-bottom { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 12px; color: #8a7f84; font-size: 11px; }
.pd .pd-toolbar-bottom strong { color: var(--pd-maroon); }
.pd .pd-hasil { display: none; width: 100%; margin-top: 10px; padding: 11px 14px; border: 1px dashed #e3d5d9; border-radius: 8px; background: #fdfafb; color: #857981; font-size: 12px; text-align: center; }

/* Tabel seperti Manajemen Pengguna. */
.pd .pd-tabel-wrap { width: 100%; min-width: 0; overflow-x: auto; -webkit-overflow-scrolling: touch; }
.pd .pd-tabel { width: 100%; min-width: 760px; border-collapse: collapse; background: #fff; font-size: 12px; }
.pd .pd-tabel thead th { padding: 15px 18px; background: var(--pd-maroon); color: #fff; font-size: 11px; font-weight: 650; line-height: 1.4; text-align: left; white-space: nowrap; }
.pd .pd-tabel thead th:first-child { padding-left: 24px; }
.pd .pd-tabel tbody td { padding: 16px 18px; border-bottom: 1px solid #f0e9eb; color: #554d51; vertical-align: middle; line-height: 1.55; }
.pd .pd-tabel tbody td:first-child { padding-left: 24px; }
.pd .pd-tabel tbody tr:nth-child(even) { background: #fdfafb; }
.pd .pd-tabel tbody tr:hover { background: #fcf4f6; }
.pd .pd-tabel .pd-no { width: 62px; color: #857b80; font-variant-numeric: tabular-nums; }
.pd .pd-tabel .pd-aksi-kolom { width: 155px; text-align: left; }
.pd .pd-peserta { display: flex; align-items: center; gap: 11px; min-width: 210px; }
.pd .pd-avatar { display: grid; flex: none; place-items: center; width: 43px; height: 43px; border-radius: 11px; background: #f8e9ed; color: var(--pd-maroon); }
.pd .pd-avatar svg { width: 23px; height: 23px; stroke: currentColor; }
.pd .pd-peserta-info { min-width: 0; }
.pd .pd-nama { color: #2d252a; font-size: 12.5px; font-weight: 750; overflow-wrap: anywhere; }
.pd .pd-kategori { display: inline-block; margin-top: 4px; padding: 3px 8px; border-radius: 6px; font-size: 10.5px; font-weight: 650; line-height: 1.35; }
.pd .pd-kategori--umum { background: #f5eef1; color: #79445a; }
.pd .pd-kategori--kip { background: #fff4df; color: #9a641c; }
.pd .pd-nim { color: #51454b; font-size: 12px; font-variant-numeric: tabular-nums; }
.pd .pd-status { display: inline-flex; align-items: center; gap: 7px; padding: 6px 11px; border-radius: 20px; font-size: 10.5px; font-weight: 750; white-space: nowrap; }
.pd .pd-status::before { content: ''; flex: none; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.pd .pd-status--menunggu { background: #fff3d6; color: #926114; }
.pd .pd-status--disetujui { background: #e0f8e8; color: #147439; }
.pd .pd-status--ditolak { background: #ffeaed; color: #a52a40; }
.pd .pd-status--perbaikan { background: #fff0e1; color: #a75917; }
.pd .pd-validator { display: block; margin-top: 5px; color: #92868c; font-size: 10.5px; overflow-wrap: anywhere; }
.pd .pd-kosong { padding: 42px 16px !important; text-align: center; color: #897e83; font-size: 12px; }
.pd .pd-table-footer { display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center; gap: 10px; padding: 17px 24px; color: #81777e; font-size: 11px; }
.pd .pd-table-footer strong { color: #4d4248; }
.pd .pd-table-footer-note { display: inline-flex; align-items: center; gap: 6px; }
.pd .pd-table-footer-note svg { width: 15px; height: 15px; }

/* Tombol detail dan dokumen. */
.pd .pd-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 36px; padding: 8px 13px; border: 1px solid #e5d7db; border-radius: 8px; background: #fff; color: var(--pd-maroon); font-size: 11.5px; line-height: 1.3; font-weight: 700; text-decoration: none; white-space: nowrap; cursor: pointer; transition: background .17s, border-color .17s, color .17s; }
.pd .pd-btn svg { flex: none; width: 15px; height: 15px; }
.pd .pd-btn--detail { min-height: 36px; border-color: var(--pd-maroon); background: var(--pd-maroon); color: #fff; }
.pd .pd-btn--detail:hover { border-color: var(--pd-maroon-dark); background: var(--pd-maroon-dark); }
.pd .pd-btn--outline:hover { background: var(--pd-soft); }
.pd .pd-btn--approve { background: #1c783e; border-color: #1c783e; color: #fff; }
.pd .pd-btn--approve:hover { background: #145c30; border-color: #145c30; }
.pd .pd-btn--revise { background: #b97716; border-color: #b97716; color: #fff; }
.pd .pd-btn--revise:hover { background: #976010; border-color: #976010; }
.pd .pd-btn--reject { background: #aa273b; border-color: #aa273b; color: #fff; }
.pd .pd-btn--reject:hover { background: #8d1b2d; border-color: #8d1b2d; }
.pd .pd-btn--document { background: #fffaf1; border-color: #e3c9a6; color: #75451f; }
.pd .pd-btn--document:hover { background: #fdf2dd; border-color: var(--pd-gold); }

/* Dialog native HTML, mempertahankan field, form dan validasi. */
.pd dialog.pd-modal { width: min(820px, calc(100vw - 32px)); max-width: calc(100vw - 24px); max-height: min(91dvh, 900px); margin: auto; padding: 0; border: 1px solid #e6d7dc; border-radius: 14px; background: #fff; color: #31282d; box-shadow: 0 24px 70px rgba(18, 9, 13, .26); overflow-x: hidden; overflow-y: auto; overscroll-behavior: contain; }
.pd dialog.pd-modal::backdrop { background: rgba(16, 10, 16, .64); backdrop-filter: blur(3px); }
.pd .pd-modal-head { position: sticky; top: 0; z-index: 3; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding: 19px 23px; background: var(--pd-maroon); color: #fff; }
.pd .pd-modal-head h2 { margin: 0 0 5px; color: #fff; font-size: 17px; font-weight: 700; line-height: 1.4; }
.pd .pd-modal-head p { margin: 0; color: rgba(255,255,255,.85); font-size: 11px; line-height: 1.6; overflow-wrap: anywhere; }
.pd .pd-modal-close { display: inline-flex; align-items: center; justify-content: center; flex: 0 0 32px; width: 32px; height: 32px; border: 1px solid rgba(255,255,255,.43); border-radius: 9px; background: transparent; color: #fff; font-size: 22px; line-height: 1; cursor: pointer; }
.pd .pd-modal-close:hover { background: rgba(255,255,255,.15); }
.pd .pd-modal-body { padding: 20px 23px 24px; }
.pd .pd-section { display: block; width: 100%; min-height: 0; margin: 0 0 20px; }
.pd .pd-section:last-of-type { margin-bottom: 14px; }
.pd .pd-section-title { position: relative; margin: 0 0 12px; padding: 0 0 0 11px; color: var(--pd-maroon); font-size: 13px; font-weight: 750; }
.pd .pd-section-title::before { content: ''; position: absolute; left: 0; top: 2px; bottom: 2px; width: 3px; border-radius: 4px; background: var(--pd-gold); }
.pd .pd-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px; }
.pd .pd-detail { min-width: 0; padding: 11px 13px; border: 1px solid #ece1e4; border-radius: 9px; background: #fdfafb; }
.pd .pd-detail--full { grid-column: 1 / -1; }
.pd .pd-detail-label { display: block; margin-bottom: 4px; color: #897d82; font-size: 10.5px; font-weight: 650; }
.pd .pd-detail-value { display: block; color: #2f272b; font-size: 12px; font-weight: 600; line-height: 1.5; overflow-wrap: anywhere; white-space: pre-line; }
.pd .pd-documents { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
.pd .pd-notice { padding: 12px 14px; border: 1px solid #f1d5b2; border-left: 3px solid #c9a96a; border-radius: 9px; background: #fffaf0; color: #81561e; font-size: 11.5px; line-height: 1.65; }
.pd .pd-action-area { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-top: 12px; }
.pd .pd-action-form { display: flex; flex-direction: column; min-width: 0; padding: 14px; border: 1px solid #ece2e5; border-radius: 10px; background: #fdfafb; }
.pd .pd-action-form h4 { margin: 0 0 6px; color: #42363c; font-size: 12px; font-weight: 750; }
.pd .pd-action-form p { margin: 0 0 13px; color: #81777e; font-size: 10.7px; line-height: 1.55; }
.pd .pd-action-form label { display: block; margin-bottom: 6px; color: #685b62; font-size: 10.7px; font-weight: 650; }
.pd .pd-action-form textarea { display: block; width: 100%; min-height: 86px; margin-bottom: 12px; padding: 9px 10px; border: 1px solid #e3d5da; border-radius: 8px; background: #fff; color: #352c31; font-size: 11px; line-height: 1.55; resize: vertical; }
.pd .pd-action-form textarea:focus { border-color: #ad7080; outline: 3px solid rgba(128,11,34,.08); }
.pd .pd-action-form .pd-btn { width: 100%; min-height: 39px; margin-top: auto; white-space: normal; }
.pd .pd-finished { padding: 14px; border: 1px solid #e9e0e3; border-radius: 9px; background: #faf8f9; color: #786c73; font-size: 11.5px; line-height: 1.7; }

@container (max-width: 850px) {
    .pd-stats { gap: 9px !important; }
    .pd-stat { gap: 9px !important; padding: 13px !important; }
    .pd-stat-icon { width: 37px !important; height: 37px !important; flex-basis: 37px !important; }
    .pd-panel-head, .pd-toolbar, .pd-table-footer { padding-left: 16px !important; padding-right: 16px !important; }
    .pd-action-area { grid-template-columns: 1fr !important; }
}
@container (max-width: 700px) {
    /* Kartu berlabel menggantikan tabel lebar. Tidak ada scroll horizontal. */
    .pd-stats { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .pd-stat:first-child { grid-column: 1 / -1; }
    .pd-tabel-wrap { overflow: visible !important; }
    .pd-tabel, .pd-tabel tbody { display: block; width: 100%; min-width: 0 !important; }
    .pd-tabel thead { display: none; }
    .pd-tabel tbody { padding: 0 14px 14px; }
    .pd-tabel tbody tr.baris-pendaftar { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 13px 10px; margin: 0 0 11px; padding: 15px; border: 1px solid #eadfe1; border-radius: 11px; background: #fff !important; }
    .pd-tabel tbody tr.baris-pendaftar:first-child { margin-top: 5px; }
    .pd-tabel tbody tr.baris-pendaftar:hover { background: #fffbfc !important; }
    .pd-tabel tbody tr.baris-pendaftar td { display: block; min-width: 0; width: auto !important; padding: 0 !important; border: none; }
    .pd-tabel tbody tr.baris-pendaftar td.pd-no { grid-column: 1 / -1; color: #8b7780; font-size: 10.5px; font-weight: 700; }
    .pd-tabel tbody tr.baris-pendaftar td.pd-no::before { content: 'PENDAFTAR #'; }
    .pd-tabel tbody tr.baris-pendaftar td:nth-child(2) { grid-column: 1 / -1; }
    .pd-tabel tbody tr.baris-pendaftar td:nth-child(3)::before,
    .pd-tabel tbody tr.baris-pendaftar td:nth-child(4)::before { display: block; margin-bottom: 6px; color: #94878d; font-size: 10px; font-weight: 750; letter-spacing: .04em; text-transform: uppercase; }
    .pd-tabel tbody tr.baris-pendaftar td:nth-child(3)::before { content: 'NIM'; }
    .pd-tabel tbody tr.baris-pendaftar td:nth-child(4)::before { content: 'Status'; }
    .pd-tabel tbody tr.baris-pendaftar td:nth-child(5) { grid-column: 1 / -1; width: 100% !important; }
    .pd-tabel tbody tr.baris-pendaftar .pd-btn--detail { width: 100%; min-height: 41px; }
    .pd-tabel tbody tr:not(.baris-pendaftar) { display: block; padding: 20px; }
    .pd-tabel tbody tr:not(.baris-pendaftar) td { display: block; width: 100%; }
    .pd-modal-body { padding: 15px !important; }
}
@container (max-width: 420px) {
    .pd-page-header h1 { font-size: 22px !important; }
    .pd-stat { min-height: 83px !important; }
    .pd-stat-copy span { font-size: 10px !important; }
    .pd-stat-copy strong { font-size: 21px !important; }
    .pd-search-row { flex-wrap: wrap; }
    .pd-search-reset { width: 100%; min-height: 35px; }
    .pd-filters { gap: 6px !important; }
    .pd-filter { padding: 7px 11px !important; font-size: 10.8px !important; }
    .pd-panel-head { padding-top: 17px !important; padding-bottom: 17px !important; }
    .pd-peserta { min-width: 0 !important; }
    .pd-modal-head { padding: 14px !important; }
    .pd-modal-head h2 { font-size: 15px !important; }
    .pd-detail-grid { grid-template-columns: 1fr !important; }
    .pd-detail--full { grid-column: auto !important; }
}
@media (max-width: 760px) {
    .pd dialog.pd-modal { width: calc(100vw - 16px); max-width: calc(100vw - 16px); max-height: calc(100dvh - 20px); border-radius: 12px; }
    .pd .pd-modal-body { padding: 15px; }
    .pd .pd-detail-grid { grid-template-columns: minmax(0, 1fr); }
    .pd .pd-detail--full { grid-column: auto; }
    .pd .pd-action-area { grid-template-columns: minmax(0,1fr); }
}
</style>

<div class="pd">
    <header class="pd-page-header">
        <nav class="pd-breadcrumb" aria-label="Navigasi halaman">
            <a href="{{ route('musyrifah.dashboard') }}">Dashboard</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">Pendaftaran Mahasiswa</span>
        </nav>
        <h1>Pendaftaran Mahasiswa</h1>
        <p>Kelola data pendaftaran, periksa dokumen, dan validasi pengajuan mahasiswa Ma'had.</p>
    </header>

    @if ($errors->any())
        <div class="pd-alert pd-alert--error" role="alert">
            <strong>Periksa kembali data validasi:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pd-stats" role="region" aria-label="Ringkasan pendaftaran">
        <div class="pd-stat">
            <span class="pd-stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <div class="pd-stat-copy">
                <span>Total Pendaftaran</span>
                <strong>{{ number_format($pendaftarans->count(), 0, ',', '.') }}</strong>
            </div>
        </div>
        <div class="pd-stat">
            <span class="pd-stat-icon pd-stat-icon--amber" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <div class="pd-stat-copy">
                <span>Menunggu Validasi</span>
                <strong>{{ number_format($pendaftarans->where('status', 'menunggu')->count(), 0, ',', '.') }}</strong>
            </div>
        </div>
        <div class="pd-stat">
            <span class="pd-stat-icon pd-stat-icon--orange" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m10 3-7 7 7 7 11-11-7-3-4 0Z"/><path d="M8 12l4 4 7-7"/></svg>
            </span>
            <div class="pd-stat-copy">
                <span>Perlu Perbaikan</span>
                <strong>{{ number_format($pendaftarans->where('status', 'perlu_perbaikan')->count(), 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <div class="pd-panel" role="region" aria-labelledby="pdDaftarTitle">
        <div class="pd-panel-head">
            <div>
                <h2 id="pdDaftarTitle">Daftar Pendaftar</h2>
                <p>{{ $pendaftarans->count() }} mahasiswa telah mengajukan pendaftaran.</p>
            </div>
            <span class="pd-head-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M8 4h13M8 10h13M8 16h13M3 4h.01M3 10h.01M3 16h.01"/></svg>
                Validasi Pendaftaran
            </span>
        </div>
        <div class="pd-toolbar">
            <div class="pd-search-row" role="search">
                <label class="pd-cari" for="cariMahasiswa">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                    <input type="search" id="cariMahasiswa" placeholder="Cari nama mahasiswa, NIM, kategori, status, atau validator..." aria-label="Cari pendaftar" autocomplete="off">
                </label>
                <button type="button" id="pdBersihkan" class="pd-search-reset" hidden>Bersihkan</button>
            </div>
            <nav class="pd-filters" aria-label="Filter status pendaftaran">
                <button type="button" class="pd-filter" data-status="semua" aria-pressed="true">Semua</button>
                <button type="button" class="pd-filter" data-status="menunggu" aria-pressed="false">Menunggu</button>
                <button type="button" class="pd-filter" data-status="disetujui" aria-pressed="false">Disetujui</button>
                <button type="button" class="pd-filter" data-status="perlu_perbaikan" aria-pressed="false">Perlu Perbaikan</button>
                <button type="button" class="pd-filter" data-status="ditolak" aria-pressed="false">Ditolak</button>
            </nav>
            <div class="pd-toolbar-bottom">
                <span>Menampilkan <strong id="pdJumlahTampil">{{ $pendaftarans->count() }}</strong> dari <strong>{{ $pendaftarans->count() }}</strong> pendaftar</span>
                <span>Gunakan “Lihat Detail” untuk memeriksa dan memvalidasi berkas.</span>
            </div>
            <div class="pd-hasil" id="hasilTidakDitemukan" role="status" aria-live="polite">
                Tidak ada mahasiswa yang cocok dengan pencarian atau filter ini.
            </div>
        </div>
        <div class="pd-tabel-wrap" role="region" aria-label="Tabel daftar pendaftar" tabindex="0">
            <table class="pd-tabel">
                <thead>
                    <tr>
                        <th scope="col" class="pd-no">No.</th>
                        <th scope="col">Mahasiswa</th>
                        <th scope="col">NIM</th>
                        <th scope="col">Status Pendaftaran</th>
                        <th scope="col" class="pd-aksi-kolom">Aksi</th>
                    </tr>
                </thead>
                <tbody id="daftarPendaftar">
                    @forelse ($pendaftarans as $index => $pendaftaran)
                        <tr class="baris-pendaftar" data-status="{{ $pendaftaran->status ?? 'menunggu' }}">
                            <td class="pd-no">{{ $index + 1 }}</td>
                            <td>
                                <div class="pd-peserta">
                                    <span class="pd-avatar" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-5 3-8 8-8s8 3 8 8"/></svg>
                                    </span>
                                    <div class="pd-peserta-info">
                                        <div class="pd-nama">{{ $pendaftaran->nama_lengkap ?: '-' }}</div>
                                        <span class="pd-kategori pd-kategori--{{ ($pendaftaran->kategori ?? 'umum') === 'kip' ? 'kip' : 'umum' }}">{{ ($pendaftaran->kategori ?? 'umum') === 'kip' ? 'Mahasiswa KIP' : 'Mahasiswa Umum' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="pd-nim">{{ $pendaftaran->user?->nim ?: '-' }}</span></td>
                            <td>
                                @if ($pendaftaran->status === 'disetujui')
                                    <span class="pd-status pd-status--disetujui">Disetujui</span>
                                @elseif ($pendaftaran->status === 'ditolak')
                                    <span class="pd-status pd-status--ditolak">Ditolak</span>
                                @elseif ($pendaftaran->status === 'perlu_perbaikan')
                                    <span class="pd-status pd-status--perbaikan">Perlu Perbaikan</span>
                                @else
                                    <span class="pd-status pd-status--menunggu">Menunggu</span>
                                @endif
                                @if ($pendaftaran->validator_nama)
                                    <span class="pd-validator">Oleh: {{ $pendaftaran->validator_nama }}</span>
                                @endif
                            </td>
                            <td class="pd-aksi-kolom">
                                <button type="button" class="pd-btn pd-btn--detail pendaftaran-open-detail" data-target="detail-pendaftaran-{{ $pendaftaran->id }}" aria-haspopup="dialog" aria-label="Lihat detail pendaftaran {{ $pendaftaran->nama_lengkap }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="pd-kosong">Belum ada data pendaftaran mahasiswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pd-table-footer">
            <span>Data pendaftaran dan keputusan validasi ditampilkan sesuai akses Musyrifah.</span>
            <span class="pd-table-footer-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 3 3 7v6c0 5 3.5 8 9 9 5.5-1 9-4 9-9V7l-9-4Z"/><path d="m9 12 2 2 4-4"/></svg>
                Periksa data sebelum memutuskan.
            </span>
        </div>
    </div>

    {{-- Dialog di luar tabel agar struktur HTML tetap valid. --}}
    @foreach ($pendaftarans as $pendaftaran)

        <dialog class="pd-modal" id="detail-pendaftaran-{{ $pendaftaran->id }}"

            aria-labelledby="judul-detail-{{ $pendaftaran->id }}">

            <div class="pd-modal-head">

                <div>

                    <h2 id="judul-detail-{{ $pendaftaran->id }}">Detail Pendaftaran #{{ str_pad((string) $pendaftaran->id, 6, '0', STR_PAD_LEFT) }}</h2>

                    <p>{{ $pendaftaran->nama_lengkap }} &bull; Diajukan {{ $pendaftaran->created_at?->format('d/m/Y H:i') ?? '-' }}</p>

                </div>

                <button type="button" class="pd-modal-close" data-close-detail aria-label="Tutup detail">&times;</button>

            </div>

            <div class="pd-modal-body">

                <div class="pd-section">

                    <h3 class="pd-section-title">Data Pribadi</h3>

                    <div class="pd-detail-grid">

                        <div class="pd-detail">

                            <span class="pd-detail-label">Nama lengkap</span>

                            <span class="pd-detail-value">{{ $pendaftaran->nama_lengkap ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">NIM</span>

                            <span class="pd-detail-value">{{ $pendaftaran->user?->nim ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Email akun</span>

                            <span class="pd-detail-value">{{ $pendaftaran->user?->email ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Jenis kelamin</span>

                            <span class="pd-detail-value">{{ $pendaftaran->jenis_kelamin ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">No. WhatsApp</span>

                            <span class="pd-detail-value">{{ $pendaftaran->no_wa ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Asal sekolah</span>

                            <span class="pd-detail-value">{{ $pendaftaran->asal_sekolah ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Nama orang tua/wali</span>

                            <span class="pd-detail-value">{{ $pendaftaran->nama_ortu ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">No. HP orang tua/wali</span>

                            <span class="pd-detail-value">{{ $pendaftaran->no_hp_ortu ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Kategori</span>

                            <span class="pd-detail-value">{{ strtoupper($pendaftaran->kategori ?: '-') }}</span>

                        </div>

                    </div>

                </div>

                <div class="pd-section">

                    <h3 class="pd-section-title">Data Akademik</h3>

                    <div class="pd-detail-grid">

                        <div class="pd-detail">

                            <span class="pd-detail-label">Tahun angkatan</span>

                            <span class="pd-detail-value">{{ $pendaftaran->tahun_angkatan ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Fakultas</span>

                            <span class="pd-detail-value">{{ $pendaftaran->fakultas ?: '-' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Program studi</span>

                            <span class="pd-detail-value">{{ $pendaftaran->program_studi ?: '-' }}</span>

                        </div>

                    </div>

                </div>

                <div class="pd-section">

                    <h3 class="pd-section-title">Persyaratan dan Pembayaran</h3>

                    <div class="pd-detail-grid">

                        <div class="pd-detail">

                            <span class="pd-detail-label">Nominal pembayaran yang dilaporkan</span>

                            <span class="pd-detail-value">Rp{{ number_format((float) $pendaftaran->nominal_pembayaran, 0, ',', '.') }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Konfirmasi kebenaran data</span>

                            <span class="pd-detail-value">{{ $pendaftaran->konfirmasi_data ? 'Sudah dikonfirmasi' : 'Belum dikonfirmasi' }}</span>

                        </div>

                        <div class="pd-detail pd-detail--full">

                            <span class="pd-detail-label">Alasan mendaftar</span>

                            <span class="pd-detail-value">{{ $pendaftaran->alasan_daftar ?: '-' }}</span>

                        </div>

                        <div class="pd-detail pd-detail--full">

                            <span class="pd-detail-label">Berkas pendukung</span>

                            <div class="pd-documents">

                                @if ($pendaftaran->bukti_penerimaan)

                                    <a class="pd-btn pd-btn--document" target="_blank" rel="noopener noreferrer"

                                        href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'penerimaan']) }}">

                                        Lihat Bukti Penerimaan UIN

                                    </a>

                                @else

                                    <span class="pd-detail-value">Bukti penerimaan belum diunggah.</span>

                                @endif

                                @if ($pendaftaran->bukti_pembayaran)

                                    <a class="pd-btn pd-btn--document" target="_blank" rel="noopener noreferrer"

                                        href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'pembayaran']) }}">

                                        Lihat Bukti Pembayaran

                                    </a>

                                @else

                                    <span class="pd-detail-value">Bukti pembayaran belum diunggah.</span>

                                @endif

                                @if ($pendaftaran->ijazah_sma)

                                    <a class="pd-btn pd-btn--document" target="_blank" rel="noopener noreferrer"

                                        href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'ijazah']) }}">

                                        Lihat Ijazah SMA

                                    </a>

                                @endif

                                @if ($pendaftaran->ktp)

                                    <a class="pd-btn pd-btn--document" target="_blank" rel="noopener noreferrer"

                                        href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'ktp']) }}">

                                        Lihat KTP

                                    </a>

                                @endif

                                @if ($pendaftaran->kk)

                                    <a class="pd-btn pd-btn--document" target="_blank" rel="noopener noreferrer"

                                        href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'kk']) }}">

                                        Lihat Kartu Keluarga

                                    </a>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

                <div class="pd-section">

                    <h3 class="pd-section-title">Status dan Riwayat Validasi</h3>

                    <div class="pd-detail-grid">

                        <div class="pd-detail">

                            <span class="pd-detail-label">Status pendaftaran</span>

                            <span class="pd-detail-value">

                                @if ($pendaftaran->status === 'menunggu')

                                    Menunggu Verifikasi

                                @elseif ($pendaftaran->status === 'disetujui')

                                    Disetujui

                                @elseif ($pendaftaran->status === 'ditolak')

                                    Ditolak

                                @elseif ($pendaftaran->status === 'perlu_perbaikan')

                                    Perlu Perbaikan

                                @else

                                    {{ ucfirst(str_replace('_', ' ', (string) $pendaftaran->status)) }}

                                @endif

                            </span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Divalidasi oleh</span>

                            <span class="pd-detail-value">{{ $pendaftaran->validator_nama ?: 'Belum divalidasi' }}</span>

                        </div>

                        <div class="pd-detail">

                            <span class="pd-detail-label">Waktu validasi</span>

                            <span class="pd-detail-value">{{ $pendaftaran->divalidasi_pada?->format('d/m/Y H:i') ?? '-' }}</span>

                        </div>

                        <div class="pd-detail pd-detail--full">

                            <span class="pd-detail-label">Catatan validator</span>

                            <span class="pd-detail-value">{{ $pendaftaran->catatan_validasi ?: '-' }}</span>

                        </div>

                    </div>

                </div>

                @if (in_array($pendaftaran->status, ['menunggu', 'perlu_perbaikan'], true))

                    <div class="pd-notice">

                        Periksa kelengkapan berkas fisik/digital dan pembayaran. Anda dapat <strong>Menyetujui</strong>, meminta <strong>Perbaikan</strong>, atau <strong>Menolak</strong> pengajuan pendaftaran ini.

                    </div>

                    <div class="pd-action-area">

                        <form class="pd-action-form" method="POST"

                            action="{{ route('musyrifah.pendaftaran.status', $pendaftaran->id) }}"

                            data-sui-confirm="Yakin menyetujui pendaftaran ini? Pastikan dokumen dan pembayaran sudah diverifikasi."
                            data-sui-confirm-title="Setujui pendaftaran?"
                            data-sui-confirm-label="Ya, Setujui">

                            @csrf

                            @method('PATCH')

                            <input type="hidden" name="status" value="disetujui">

                            <h4>Setujui Pendaftaran</h4>

                            <p>Mahasiswa dinyatakan diterima dan dapat melanjutkan ke tahap pemilihan kamar.</p>

                            <label for="catatan-setujui-{{ $pendaftaran->id }}">Catatan (opsional)</label>

                            <textarea id="catatan-setujui-{{ $pendaftaran->id }}" name="catatan_validasi" maxlength="2000" placeholder="Misalnya: Dokumen lengkap dan pembayaran telah diverifikasi."></textarea>

                            <button type="submit" class="pd-btn pd-btn--approve">✓ Setujui</button>

                        </form>

                        <form class="pd-action-form" method="POST"

                            action="{{ route('musyrifah.pendaftaran.status', $pendaftaran->id) }}"

                            data-sui-confirm="Yakin meminta perbaikan untuk pendaftaran ini? Tuliskan instruksi perbaikan dengan jelas."
                            data-sui-confirm-title="Minta perbaikan?"
                            data-sui-confirm-label="Ya, Minta Perbaikan">

                            @csrf

                            @method('PATCH')

                            <input type="hidden" name="status" value="perlu_perbaikan">

                            <h4>Perlu Perbaikan</h4>

                            <p>Mahasiswa diminta memperbaiki data atau mengunggah ulang dokumen yang belum sesuai.</p>

                            <label for="catatan-perbaikan-{{ $pendaftaran->id }}">Instruksi Perbaikan <span aria-hidden="true">*</span></label>

                            <textarea id="catatan-perbaikan-{{ $pendaftaran->id }}" name="catatan_validasi" maxlength="2000" required placeholder="Contoh: Bukti pembayaran terpotong, mohon unggah ulang bukti transfer yang jelas."></textarea>

                            <button type="submit" class="pd-btn pd-btn--revise">⚠ Minta Perbaikan</button>

                        </form>

                        <form class="pd-action-form" method="POST"

                            action="{{ route('musyrifah.pendaftaran.status', $pendaftaran->id) }}"

                            data-sui-confirm="Yakin menolak pendaftaran ini? Alasan penolakan akan dicatat."
                            data-sui-confirm-title="Tolak pendaftaran?"
                            data-sui-confirm-label="Ya, Tolak"
                            data-sui-confirm-tone="danger">

                            @csrf

                            @method('PATCH')

                            <input type="hidden" name="status" value="ditolak">

                            <h4>Tolak Pendaftaran</h4>

                            <p>Pendaftaran dihentikan karena tidak memenuhi kriteria penerimaan Ma'had.</p>

                            <label for="catatan-tolak-{{ $pendaftaran->id }}">Alasan Penolakan <span aria-hidden="true">*</span></label>

                            <textarea id="catatan-tolak-{{ $pendaftaran->id }}" name="catatan_validasi" maxlength="2000" required placeholder="Tuliskan alasan penolakan secara jelas."></textarea>

                            <button type="submit" class="pd-btn pd-btn--reject">✕ Tolak</button>

                        </form>

                    </div>

                @else

                    <div class="pd-finished">

                        Pendaftaran sudah diproses dan tidak dapat divalidasi ulang melalui halaman ini.

                        @if ($pendaftaran->validator_nama)

                            Keputusan tercatat atas nama <strong>{{ $pendaftaran->validator_nama }}</strong>.

                        @endif

                    </div>

                @endif

            </div>

        </dialog>

    @endforeach

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputCari = document.getElementById('cariMahasiswa');
    const barisPendaftar = Array.from(document.querySelectorAll('#daftarPendaftar .baris-pendaftar'));
    const pesanKosong = document.getElementById('hasilTidakDitemukan');
    const jumlahTampil = document.getElementById('pdJumlahTampil');
    const tombolReset = document.getElementById('pdBersihkan');
    const tombolFilter = Array.from(document.querySelectorAll('.pd-filter'));
    let statusAktif = 'semua';

    function terapkanFilter() {
        const kataKunci = (inputCari?.value || '').trim().toLocaleLowerCase('id-ID');
        let ditemukan = 0;

        barisPendaftar.forEach(function (baris) {
            const cocokTeks = baris.textContent.toLocaleLowerCase('id-ID').includes(kataKunci);
            const cocokStatus = statusAktif === 'semua' || baris.dataset.status === statusAktif;
            const tampil = cocokTeks && cocokStatus;
            baris.style.display = tampil ? '' : 'none';
            if (tampil) ditemukan++;
        });

        if (jumlahTampil) jumlahTampil.textContent = String(ditemukan);
        if (pesanKosong) pesanKosong.style.display = barisPendaftar.length > 0 && ditemukan === 0 ? 'block' : 'none';
        if (tombolReset) tombolReset.hidden = !kataKunci && statusAktif === 'semua';

        tombolFilter.forEach(function (tombol) {
            tombol.setAttribute('aria-pressed', tombol.dataset.status === statusAktif ? 'true' : 'false');
        });
    }

    if (inputCari) inputCari.addEventListener('input', terapkanFilter);
    tombolFilter.forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            statusAktif = tombol.dataset.status || 'semua';
            terapkanFilter();
        });
    });
    if (tombolReset) {
        tombolReset.addEventListener('click', function () {
            if (inputCari) inputCari.value = '';
            statusAktif = 'semua';
            terapkanFilter();
            inputCari?.focus();
        });
    }

    // Detail & aksi validasi tetap menggunakan form POST/PATCH asli.
    document.querySelectorAll('.pendaftaran-open-detail').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            const dialog = document.getElementById(tombol.dataset.target);
            if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
        });
    });
    document.querySelectorAll('.pd-modal').forEach(function (dialog) {
        dialog.querySelectorAll('[data-close-detail]').forEach(function (tombol) {
            tombol.addEventListener('click', function () { dialog.close(); });
        });
        dialog.addEventListener('click', function (event) {
            if (event.target !== dialog) return;
            const area = dialog.getBoundingClientRect();
            if (event.clientX < area.left || event.clientX > area.right ||
                event.clientY < area.top || event.clientY > area.bottom) dialog.close();
        });
    });
    terapkanFilter();
});
</script>
@endsection
