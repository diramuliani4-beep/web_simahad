@extends('layouts.musyrifah')
@section('title', 'Manajemen Kamar')
@section('content')
<style>
    /* SIMAHAD — Manajemen Kamar Musyrifah. Semua gaya halaman dibatasi ke modul ini. */
    .data-kamar-page {
        --dk-wine: #800b22;
        --dk-wine-deep: #620819;
        --dk-ink: #292427;
        --dk-muted: #82797e;
        --dk-border: #eddee1;
        --dk-soft: #fbf5f7;
        box-sizing: border-box;
        position: relative;
        display: block !important;
        clear: both;
        float: none !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0;
        margin: 0 !important;
        padding: 0 0 28px;
        color: var(--dk-ink);
        font-family: inherit;
        font-size: 13px;
        container-type: inline-size;
    }
    .data-kamar-page *, .data-kamar-page *::before, .data-kamar-page *::after,
    .kamar-modal *, .kamar-modal *::before, .kamar-modal *::after { box-sizing: border-box; }
    .data-kamar-page a { text-decoration: none; }

    /* HEADER: sejajar dengan Manajemen Pengguna Admin. */
    .data-kamar-page .data-kamar-heading {
        display: flex !important;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        width: 100%;
        margin: 0 0 22px;
        padding: 0 0 19px;
        border-bottom: 1px solid #e8cfd6;
    }
    .data-kamar-page .data-kamar-heading-left { display: block; flex: 1 1 360px; min-width: 0; }
    .data-kamar-page .dk-breadcrumb { display:flex; align-items:center; gap:7px; flex-wrap:wrap; margin-bottom:11px; color:#8e8487; font-size:12px; }
    .data-kamar-page .dk-breadcrumb a { color:var(--dk-wine); }
    .data-kamar-page .dk-breadcrumb a:hover { text-decoration:underline; }
    .data-kamar-page .data-kamar-title { margin:0 0 6px; font-size:clamp(22px,2.5vw,27px); font-weight:750; letter-spacing:-.55px; line-height:1.3; color:#282126; }
    .data-kamar-page .dk-subtitle { margin:0; color:#797076; font-size:12.5px; line-height:1.7; }
    .data-kamar-page .kamar-btn-tambah {
        display:inline-flex; flex:0 0 auto; align-items:center; justify-content:center; gap:8px;
        min-height:43px; padding:10px 17px; background:var(--dk-wine); border:1px solid var(--dk-wine);
        color:#fff; border-radius:9px; line-height:1.35; font-family:inherit; font-size:12px; font-weight:700;
        box-shadow:0 4px 10px rgba(103,10,30,.1); cursor:pointer; transition:background .18s,transform .18s;
    }
    .data-kamar-page .kamar-btn-tambah:hover { background:var(--dk-wine-deep); transform:translateY(-1px); }
    .data-kamar-page .kamar-btn-tambah svg { width:16px; height:16px; }

    /* ALERT */
    .data-kamar-page .data-kamar-alert { padding:13px 16px; margin:0 0 18px; border:1px solid #e6d6da; border-radius:10px; font-size:12px; line-height:1.6; }
    .data-kamar-page .data-kamar-alert--success { background:#eefbf2; border-color:#bde8cb; color:#176437; }
    .data-kamar-page .data-kamar-alert--error { background:#fff1f3; border-color:#f2c6d0; color:#a0223d; }

    /* SUMMARY — kartu bersih tanpa blok pink tebal. */
    .data-kamar-page > .ringkasan-kamar-grid {
        display:grid !important; float:none !important; clear:both; position:relative;
        grid-template-columns:repeat(4,minmax(0,1fr)); gap:13px;
        width:100% !important; max-width:100% !important; min-width:0;
        margin:0 0 22px !important; padding:0 !important;
    }
    .data-kamar-page .ringkasan-card {
        display:flex; align-items:center; gap:13px; flex-direction:row; justify-content:flex-start;
        min-width:0; min-height:97px; padding:17px 18px;
        border:1px solid var(--dk-border); border-radius:12px; background:#fff;
        box-shadow:0 3px 12px rgba(57,19,30,.035);
    }
    .data-kamar-page .dk-stat-icon { flex:0 0 44px; width:44px; height:44px; display:grid; place-items:center; border-radius:11px; background:#f7e9ed; color:var(--dk-wine); }
    .data-kamar-page .dk-stat-icon svg { width:22px; height:22px; }
    .data-kamar-page .ringkasan-card:nth-child(2) .dk-stat-icon { background:#fdf2de; color:#a96c10; }
    .data-kamar-page .ringkasan-card:nth-child(3) .dk-stat-icon { background:#e9f2ff; color:#346ba4; }
    .data-kamar-page .ringkasan-card:nth-child(4) .dk-stat-icon { background:#e8f8ef; color:#1e7849; }
    .data-kamar-page .dk-stat-info { min-width:0; }
    .data-kamar-page .ringkasan-card__title { display:block; margin:0 0 3px; color:#81777d; font-size:11px; font-weight:600; letter-spacing:0; text-transform:none; }
    .data-kamar-page .ringkasan-card__value { color:#292427; font-size:24px; font-weight:750; line-height:1.2; font-variant-numeric:tabular-nums; }
    .data-kamar-page .ringkasan-card__sub { color:#93888e; font-size:10.5px; line-height:1.5; margin-top:4px; }

    /* SECTION layout reset: sengaja eksplisit untuk mencegah benturan CSS sidebar/layout. */
    .data-kamar-page > .pengajuan-kamar-section,
    .data-kamar-page > .dk-list-shell {
        display:block !important; float:none !important; clear:both !important;
        position:relative !important; left:auto !important; right:auto !important; top:auto !important;
        width:100% !important; max-width:100% !important; min-width:0 !important;
        margin:0 0 20px !important; padding:0 !important;
        border:1px solid var(--dk-border); border-radius:13px; background:#fff;
        box-shadow:0 3px 14px rgba(62,10,25,.035);
        overflow:hidden;
    }
    .data-kamar-page .pengajuan-kamar-head,
    .data-kamar-page .dk-list-heading {
        display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:14px;
        width:100%; margin:0; padding:20px 24px; border-bottom:1px solid #f0e8e9;
    }
    .data-kamar-page .pengajuan-kamar-title,
    .data-kamar-page .dk-list-title { margin:0 0 5px; color:#2c2529; font-size:16px; font-weight:750; }
    .data-kamar-page .pengajuan-kamar-sub,
    .data-kamar-page .dk-list-desc { margin:0; max-width:850px; color:var(--dk-muted); font-size:11.5px; line-height:1.65; }
    .data-kamar-page .pengajuan-kamar-badge {
        flex:0 0 auto; display:inline-flex; align-items:center; gap:6px; padding:7px 12px;
        border:1px solid #f2e0ba; border-radius:20px; background:#fff6e5;
        color:#956015; font-size:11px; font-weight:700;
    }
    .data-kamar-page .pengajuan-kamar-badge::before { content:''; width:7px; height:7px; border-radius:50%; background:currentColor; }
    .data-kamar-page .pengajuan-kamar-badge--empty { background:#eaf8ef; border-color:#cdebd5; color:#187044; }
    .data-kamar-page .pengajuan-kamar-section > .penghuni-empty { margin:20px 24px; }

    /* Tabel pengajuan, bukan lebar halaman. */
    .data-kamar-page .pengajuan-kamar-section > .penghuni-table-wrap,
    .data-kamar-page .pengajuan-riwayat .penghuni-table-wrap {
        display:block !important; width:100% !important; max-width:100%; min-width:0;
        overflow-x:auto; overscroll-behavior-inline:contain; border:0; border-radius:0; margin:0;
    }
    .data-kamar-page .pengajuan-table { width:100%; min-width:890px; border-collapse:collapse; background:#fff; table-layout:auto; }
    .data-kamar-page .pengajuan-table thead th { padding:14px 15px; background:var(--dk-wine); color:#fff; font-size:11px; font-weight:650; text-align:left; white-space:nowrap; border:0; }
    .data-kamar-page .pengajuan-table tbody td { padding:14px 15px; vertical-align:middle; border-bottom:1px solid #f1e8eb; color:#534c50; font-size:11.5px; line-height:1.55; }
    .data-kamar-page .pengajuan-table tbody tr:nth-child(even) { background:#fdfafb; }
    .data-kamar-page .pengajuan-table tbody tr:hover { background:#fcf4f6; }
    .data-kamar-page .pengajuan-table tbody td strong { color:#30282d; font-weight:700; }
    .data-kamar-page .pengajuan-table tbody td:last-child { white-space:nowrap; }
    .data-kamar-page .pengajuan-jenis,
    .data-kamar-page .pengajuan-status { display:inline-flex; padding:5px 10px; border-radius:20px; font-size:10.5px; font-weight:700; white-space:nowrap; }
    .data-kamar-page .pengajuan-jenis { background:#f5eaf0; color:#7e2e49; }
    .data-kamar-page .pengajuan-jenis--pindah { background:#eaf1ff; color:#245fa9; }
    .data-kamar-page .pengajuan-status--menunggu { background:#fff3d9; color:#936113; }
    .data-kamar-page .pengajuan-status--disetujui { background:#e4f8ec; color:#14783c; }
    .data-kamar-page .pengajuan-status--ditolak { background:#ffebee; color:#a52443; }
    .data-kamar-page .pengajuan-aksi { display:flex; align-items:center; flex-wrap:wrap; gap:7px; }
    .data-kamar-page .btn-pengajuan,
    .kamar-modal .btn-pengajuan { display:inline-flex; align-items:center; justify-content:center; min-height:36px; padding:8px 12px; border:1px solid transparent; border-radius:8px; font-family:inherit; font-size:11.5px; font-weight:700; text-decoration:none; cursor:pointer; }
    .data-kamar-page .btn-pengajuan--periksa,
    .kamar-modal .btn-pengajuan--periksa { background:#fff; border-color:#dfcfd4; color:var(--dk-wine,#800b22); }
    .data-kamar-page .btn-pengajuan--periksa:hover,
    .kamar-modal .btn-pengajuan--periksa:hover { background:#fdf4f6; }
    .kamar-modal .btn-pengajuan--setuju { background:#197246; color:#fff; }
    .kamar-modal .btn-pengajuan--tolak { background:#fff1f3; color:#a22241; border-color:#eac9d2; }
    .kamar-modal .btn-pengajuan:disabled { background:#eee9eb; color:#aaa; cursor:not-allowed; }
    .data-kamar-page .pengajuan-riwayat { margin:0; padding:15px 24px; border-top:1px solid #f1e8eb; }
    .data-kamar-page .pengajuan-riwayat summary { cursor:pointer; color:var(--dk-wine); font-size:12px; font-weight:700; }
    .data-kamar-page .pengajuan-riwayat[open] summary { margin-bottom:14px; }
    .data-kamar-page .pengajuan-riwayat .penghuni-table-wrap { border:1px solid #eddee1; border-radius:9px; }

    /* DAFTAR KAMAR: kepala + filter + grid selalu tetap satu kolom dalam konten utama. */
    .data-kamar-page .dk-list-count { margin-top:5px; font-size:11px; color:#a28c95; }
    .data-kamar-page .kamar-control-bar { display:flex !important; align-items:center; justify-content:space-between; gap:12px; margin:0; padding:17px 24px 19px; flex-wrap:wrap; }
    .data-kamar-page .kamar-filters { display:flex; align-items:center; flex:1 1 auto; flex-wrap:wrap; width:100%; max-width:100%; min-width:0; gap:10px; }
    .data-kamar-page .dk-search { display:flex; align-items:center; flex:1 1 260px; min-width:180px; min-height:44px; gap:9px; padding:0 12px; background:#fff; border:1px solid #e5d8db; border-radius:9px; }
    .data-kamar-page .dk-search:focus-within { border-color:#b47989; box-shadow:0 0 0 3px rgba(128,11,34,.07); }
    .data-kamar-page .dk-search svg { flex:none; width:18px; height:18px; color:#9e8c93; }
    .data-kamar-page .kamar-input-search { display:block; flex:1; min-width:0; width:100%; height:42px; padding:0; border:0; border-radius:0; background:transparent; box-shadow:none; color:#33282e; outline:0; font-family:inherit; font-size:12px; }
    .data-kamar-page .kamar-input-search:focus { outline:0; }
    .data-kamar-page .kamar-select-filter { flex:0 1 170px; min-width:135px; width:auto; max-width:100%; height:44px; padding:0 13px; background:#fff; border:1px solid #e5d8db; border-radius:9px; font-family:inherit; font-size:12px; color:#41373b; cursor:pointer; }
    .data-kamar-page .kamar-select-filter:focus { outline:2px solid rgba(128,11,34,.20); outline-offset:1px; }
    .data-kamar-page .dk-reset { flex:none; display:inline-flex; justify-content:center; align-items:center; min-height:43px; padding:8px 14px; background:#fff; border:1px solid #e6d8dc; border-radius:9px; color:#754354; font-family:inherit; font-size:11.5px; font-weight:700; cursor:pointer; }
    .data-kamar-page .dk-reset:hover { background:#fbf1f4; }
    .data-kamar-page .dk-list-hint { margin:0; padding:0 24px 16px; color:#8f858a; font-size:11px; }
    .data-kamar-page .dk-list-hint strong { color:var(--dk-wine); }

    /* Grid kartu kamar: tidak mengikuti aturan section global. */
    .data-kamar-page .daftar-kamar-grid {
        display:grid !important; position:relative; float:none !important;
        grid-template-columns:repeat(auto-fill,minmax(min(100%,265px),1fr));
        align-items:stretch; gap:16px;
        width:100% !important; max-width:100%; min-width:0;
        margin:0 !important; padding:0 24px 24px !important;
        background:transparent;
    }
    .data-kamar-page .kamar-card {
        display:flex; flex-direction:column; min-width:0; width:100%; max-width:100%;
        background:#fff; border:1px solid #e9dfe2; border-radius:12px;
        overflow:hidden; box-shadow:0 3px 11px rgba(58,15,28,.055); transition:box-shadow .2s,transform .2s;
    }
    .data-kamar-page .kamar-card:hover { transform:translateY(-2px); box-shadow:0 9px 20px rgba(58,15,28,.1); }
    .data-kamar-page .kamar-card__img-wrap { display:flex; width:100%; height:171px; position:relative; align-items:center; justify-content:center; overflow:hidden; background:#f6eff1; }
    .data-kamar-page .kamar-card__img-wrap[data-open-modal] { cursor:pointer; }
    .data-kamar-page .kamar-card__img { width:100%; height:100%; display:block; object-fit:cover; }
    .data-kamar-page .kamar-card__placeholder { display:flex; flex-direction:column; align-items:center; gap:7px; color:#ad8f9b; font-size:11px; }
    .data-kamar-page .kamar-card__placeholder svg { width:37px; height:37px; opacity:.75; }
    .data-kamar-page .kamar-card__foto-count { position:absolute; bottom:10px; left:10px; padding:5px 9px; background:rgba(30,19,25,.75); color:#fff; border-radius:6px; font-size:10px; font-weight:600; }
    .data-kamar-page .kamar-card__badge-status { display:inline-flex; align-items:center; position:absolute; top:11px; right:11px; padding:6px 10px; border-radius:20px; font-size:10px; line-height:1.2; font-weight:700; text-transform:none; box-shadow:0 2px 6px rgba(16,10,16,.09); }
    .data-kamar-page .badge-tersedia, .kamar-modal .badge-tersedia { background:#e5f8ea; color:#18723d; border:1px solid #c9e9d2; }
    .data-kamar-page .badge-penuh, .kamar-modal .badge-penuh { background:#fff0f2; color:#a52a40; border:1px solid #f2cbd4; }
    .data-kamar-page .badge-perbaikan, .kamar-modal .badge-perbaikan { background:#fff2df; color:#ad6c1d; border:1px solid #f6dfba; }
    .data-kamar-page .kamar-card__body { padding:16px; display:flex; flex-direction:column; flex:1; min-width:0; }
    .data-kamar-page .kamar-card__header { display:flex; flex-wrap:wrap; align-items:flex-start; justify-content:space-between; gap:5px 10px; margin:0 0 13px; }
    .data-kamar-page .kamar-card__nomor { margin:0; color:var(--dk-wine); font-size:17px; font-weight:750; line-height:1.3; }
    .data-kamar-page .kamar-card__lantai { margin-top:4px; color:#8d7c84; font-size:11px; font-weight:600; text-align:right; }
    .data-kamar-page .kamar-card__specs { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px 10px; margin:0 0 12px; padding:13px 12px; border:1px solid #f1e9eb; border-radius:9px; background:#fcf9fa; }
    .data-kamar-page .kamar-card__spec-item { display:flex; flex-direction:column; min-width:0; gap:3px; }
    .data-kamar-page .kamar-card__spec-label { color:#93858c; font-size:10.5px; }
    .data-kamar-page .kamar-card__spec-val { color:#342b30; font-size:11.5px; font-weight:750; overflow-wrap:anywhere; }
    .data-kamar-page .kamar-card__spec-val--available { color:#168047; }
    .data-kamar-page .kamar-card__spec-val--full { color:#a62a40; }
    .data-kamar-page .kamar-card__spec-val--pending { color:#a56a1a; }
    .data-kamar-page .kamar-card__fasilitas-text { margin:0 0 13px; color:#655b60; font-size:11.5px; line-height:1.6; display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2; line-clamp: 2; overflow:hidden; min-height:36px; }
    .data-kamar-page .kamar-card__actions { display:flex; align-items:center; flex-wrap:nowrap; gap:7px; margin-top:auto; padding-top:12px; border-top:1px solid #f0e8ea; }
    .data-kamar-page .kamar-card__actions form { margin:0; }
    .data-kamar-page .btn-card,
    .kamar-modal .btn-card { display:inline-flex; align-items:center; justify-content:center; min-height:36px; flex:1 1 auto; gap:5px; padding:8px 12px; border:1px solid transparent; border-radius:8px; font-family:inherit; font-size:11.5px; font-weight:700; text-decoration:none; white-space:nowrap; cursor:pointer; }
    .data-kamar-page .btn-card--detail, .kamar-modal .btn-card--detail { color:#fff; background:#800b22; border-color:#800b22; }
    .data-kamar-page .btn-card--detail:hover, .kamar-modal .btn-card--detail:hover { background:#620819; color:#fff; }
    .data-kamar-page .btn-card--edit, .kamar-modal .btn-card--edit { color:#744051; background:#fff; border-color:#e0cfd4; }
    .data-kamar-page .btn-card--edit:hover, .kamar-modal .btn-card--edit:hover { background:#fcf4f6; }
    .data-kamar-page .btn-card--delete { flex:0 0 37px; width:37px; min-height:36px; padding:0; background:#fff4f5; color:#ac2942; border-color:#f1cdd5; }
    .data-kamar-page .btn-card--delete:hover { background:#fbe2e8; }
    .data-kamar-page .kamar-kosong { grid-column:1/-1; display:none; padding:32px 18px; border:1px dashed #e4cfd5; border-radius:10px; background:#fdf9fa; color:#8d7f86; font-size:12px; text-align:center; }

    /* Modals tetap di luar arus layout normal dan dapat di-scroll. */
    .kamar-modal { width:min(760px,calc(100% - 24px)); max-width:760px; max-height:calc(100dvh - 28px); margin:auto; padding:0; border:0; border-radius:13px; background:#fff; color:#332b30; box-shadow:0 23px 65px rgba(0,0,0,.27); overflow-y:auto; overflow-x:hidden; font-family:inherit; }
    .kamar-modal::backdrop { background:rgba(17,13,20,.62); backdrop-filter:blur(3px); }
    .kamar-modal .kamar-modal__head { position:sticky; top:0; z-index:2; display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 21px; background:#800b22; color:#fff; }
    .kamar-modal .kamar-modal__title { margin:0; font-size:16px; font-weight:750; line-height:1.4; }
    .kamar-modal .kamar-modal__close { display:grid; place-items:center; flex:none; width:32px; height:32px; padding:0; border:1px solid rgba(255,255,255,.5); border-radius:50%; background:transparent; color:#fff; font-size:22px; line-height:1; cursor:pointer; }
    .kamar-modal .kamar-modal__close:hover { background:rgba(255,255,255,.12); }
    .kamar-modal .kamar-modal__body { padding:20px 22px 24px; }
    .kamar-modal .form-grid-2, .kamar-modal .pengajuan-detail { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:13px 15px; }
    .kamar-modal .form-group-item { min-width:0; margin:0; }
    .kamar-modal .form-group-item--full, .kamar-modal .pengajuan-detail__item--full { grid-column:1/-1; }
    .kamar-modal .form-label-kamar { display:block; margin:0 0 6px; color:#55484d; font-size:11.5px; font-weight:700; }
    .kamar-modal .form-input-kamar, .kamar-modal .form-select-kamar, .kamar-modal .form-textarea-kamar { width:100%; min-width:0; min-height:41px; padding:10px 11px; border:1px solid #e5d6da; border-radius:8px; background:#fff; color:#332a2e; font-family:inherit; font-size:12px; }
    .kamar-modal .form-input-kamar[readonly] { background:#f8f6f7; color:#8b7e84; }
    .kamar-modal .form-input-kamar:focus, .kamar-modal .form-select-kamar:focus, .kamar-modal .form-textarea-kamar:focus { outline:none; border-color:#a96075; box-shadow:0 0 0 3px rgba(128,11,34,.08); }
    .kamar-modal .form-textarea-kamar { min-height:88px; resize:vertical; }
    .kamar-modal .form-hint { margin:5px 0 0; color:#867d81; font-size:11px; line-height:1.55; }
    .kamar-modal .form-modal-actions { display:flex; align-items:center; justify-content:flex-end; gap:9px; flex-wrap:wrap; margin-top:20px; padding-top:15px; border-top:1px solid #eee1e4; }
    .kamar-modal .pengajuan-detail__item { min-width:0; padding:10px 12px; border:1px solid #eee3e6; border-radius:9px; background:#fcf9fa; font-size:12px; line-height:1.6; overflow-wrap:anywhere; }
    .kamar-modal .pengajuan-detail__item strong { display:block; margin-bottom:4px; color:#82777e; font-size:10.5px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .kamar-modal .pengajuan-alasan { padding:10px; border:1px solid #eadfe2; border-radius:8px; background:#fff; white-space:pre-line; }
    .kamar-modal .pengajuan-modal-actions { display:grid; gap:14px; margin-top:17px; padding-top:16px; border-top:1px solid #eee3e6; }
    .kamar-modal .pengajuan-tolak-form { display:grid; gap:8px; }
    .kamar-modal .pengajuan-aksi { display:flex; flex-wrap:wrap; gap:8px; }
    .kamar-modal .penghuni-table-wrap { display:block; width:100%; max-width:100%; overflow-x:auto; margin:10px 0 0; border:1px solid #ecdee1; border-radius:9px; }
    .kamar-modal .penghuni-table { width:100%; min-width:560px; border-collapse:collapse; font-size:11.5px; }
    .kamar-modal .penghuni-table th { padding:12px 13px; background:#800b22; color:#fff; font-weight:700; text-align:left; }
    .kamar-modal .penghuni-table td { padding:11px 13px; background:#fff; border-bottom:1px solid #f0e8ea; }
    .kamar-modal .penghuni-empty { margin:12px 0; padding:18px; border:1px dashed #e5d4d8; border-radius:9px; background:#fcf8f9; text-align:center; color:#857a7d; font-size:11.5px; line-height:1.6; }
    .kamar-modal .kamar-galeri { display:grid; grid-template-columns:repeat(auto-fill,minmax(min(100%,140px),1fr)); gap:11px; margin:12px 0 17px; }
    .kamar-modal .kamar-galeri__item { position:relative; display:flex; flex-direction:column; overflow:hidden; margin:0; background:#fff; border:1px solid #e9dce1; border-radius:10px; }
    .kamar-modal .kamar-galeri__item > a { display:block; }
    .kamar-modal .kamar-galeri__item img { display:block; width:100%; height:110px; object-fit:cover; }
    .kamar-modal .kamar-galeri__utama { position:absolute; left:7px; top:7px; padding:3px 8px; border-radius:20px; background:#800b22; color:#fff; font-size:10px; font-weight:700; }
    .kamar-modal .kamar-galeri__caption { padding:7px 9px; color:#777078; font-size:11px; font-weight:650; }
    .kamar-modal .kamar-galeri__actions { display:flex; gap:7px; padding:0 9px 9px; margin-top:auto; }
    .kamar-modal .kamar-galeri__actions form { flex:1; min-width:0; margin:0; }
    .kamar-modal .btn-foto { display:block; position:relative; width:100%; min-height:32px; padding:7px; overflow:hidden; border:1px solid #e2cfd5; border-radius:7px; background:#fff; color:#800b22; font-size:11px; font-weight:700; text-align:center; cursor:pointer; }
    .kamar-modal .btn-foto--ganti input[type="file"] { position:absolute; inset:0; width:100%; opacity:0; cursor:pointer; }
    .kamar-modal .btn-foto--hapus { border-color:#f1c7d0; color:#a02542; }
    .kamar-modal .kamar-galeri__kosong { grid-column:1/-1; padding:18px; border:1px dashed #e4d1d6; border-radius:9px; background:#fcf9fa; color:#9a8991; font-size:11.5px; text-align:center; }
    .kamar-modal .kamar-galeri__tambah { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:0 0 15px; padding:14px; border:1px dashed #e4d1d6; border-radius:9px; background:#fcf9fa; }
    .kamar-modal .kamar-galeri__tambah label { color:#714253; font-size:11.5px; font-weight:700; }
    .kamar-modal .kamar-galeri__tambah input[type="file"] { flex:1 1 180px; min-width:0; max-width:100%; font-size:11px; }
    .kamar-modal .kamar-galeri__tambah .form-hint { width:100%; }
    .kamar-modal .btn-foto--unggah { min-height:36px; padding:8px 14px; border:0; border-radius:8px; background:#800b22; color:#fff; font-size:11.5px; font-weight:700; cursor:pointer; }

    /* Responsif mengikuti ruang konten (sidebar terbuka tetap aman). */
    @container (max-width: 1060px) {
        .ringkasan-kamar-grid { grid-template-columns:repeat(2,minmax(0,1fr)) !important; }
    }
    @container (max-width: 680px) {
        .data-kamar-heading { align-items:flex-start !important; }
        .data-kamar-heading .kamar-btn-tambah { width:100%; }
        .ringkasan-kamar-grid { gap:10px !important; }
        .ringkasan-card { min-height:82px !important; padding:13px !important; }
        .dk-stat-icon { flex-basis:37px !important; width:37px !important; height:37px !important; }
        .pengajuan-kamar-head, .dk-list-heading { padding:17px 16px !important; }
        .kamar-control-bar { padding:16px !important; }
        .dk-list-hint { padding:0 16px 15px !important; }
        .daftar-kamar-grid { padding:0 16px 16px !important; grid-template-columns:repeat(auto-fill,minmax(min(100%,240px),1fr)) !important; }
        .pengajuan-kamar-section .penghuni-table-wrap { overflow:visible; }
        .pengajuan-table, .pengajuan-table tbody { display:block; min-width:0; width:100%; }
        .pengajuan-table thead { display:none; }
        .pengajuan-table tbody tr { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px 12px; margin:12px 16px; padding:13px; border:1px solid #ebdfe3; border-radius:10px; background:#fff !important; }
        .pengajuan-table tbody td { display:block; width:auto; min-width:0; padding:0 !important; border:0 !important; white-space:normal !important; overflow-wrap:anywhere; }
        .pengajuan-table tbody td::before { content:attr(data-label); display:block; margin-bottom:4px; color:#96878e; font-size:10px; font-weight:700; letter-spacing:.02em; }
        .pengajuan-table tbody td:nth-child(2), .pengajuan-table tbody td:nth-child(5), .pengajuan-table tbody td:last-child { grid-column:1/-1; }
        .pengajuan-table tbody td:last-child .btn-pengajuan { width:100%; }
        .pengajuan-riwayat { padding:13px 16px !important; }
        .pengajuan-riwayat .penghuni-table-wrap { overflow:visible !important; border:0 !important; }
        .pengajuan-riwayat .pengajuan-table tbody tr { margin:10px 0; }
    }
    @container (max-width: 430px) {
        .ringkasan-kamar-grid { grid-template-columns:minmax(0,1fr) !important; }
        .kamar-filters { flex-direction:column; align-items:stretch; }
        .dk-search, .kamar-select-filter, .dk-reset { width:100% !important; flex:0 0 auto !important; }
        .kamar-card__header { flex-direction:column; }
        .kamar-card__lantai { text-align:left !important; }
    }
    @media (max-width: 640px) {
        .kamar-modal { width:calc(100% - 16px); max-height:calc(100dvh - 18px); border-radius:11px; }
        .kamar-modal .kamar-modal__head { padding:13px 15px; }
        .kamar-modal .kamar-modal__title { font-size:14px; }
        .kamar-modal .kamar-modal__body { padding:15px; }
        .kamar-modal .form-grid-2, .kamar-modal .pengajuan-detail { grid-template-columns:minmax(0,1fr); }
        .kamar-modal .form-group-item--full, .kamar-modal .pengajuan-detail__item--full { grid-column:auto; }
        .kamar-modal .form-modal-actions .btn-card { flex:1; }
    }
    .data-kamar-page button:focus-visible, .data-kamar-page select:focus-visible,
    .kamar-modal button:focus-visible { outline:2px solid #a6526c; outline-offset:2px; }
</style>

<div class="data-kamar-page">
    <!-- Kepala halaman mengikuti bahasa visual Manajemen Pengguna Admin. -->
    <header class="data-kamar-heading">
        <div class="data-kamar-heading-left">
            <nav class="dk-breadcrumb" aria-label="Navigasi halaman">
                <a href="{{ route('musyrifah.dashboard') }}">Dashboard</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Data Kamar</span>
            </nav>
            <h1 class="data-kamar-title">Manajemen Data Kamar</h1>
            <p class="dk-subtitle">Kelola kapasitas, ketersediaan, foto kamar, dan pengajuan penempatan mahasiswa Ma'had.</p>
        </div>
        <button type="button" class="kamar-btn-tambah" id="btnBukaTambahKamar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Kamar Baru
        </button>
    </header>
    @if ($errors->any())
        <div class="data-kamar-alert data-kamar-alert--error" role="alert">
            <strong>Periksa kembali data formulir:</strong>
            <ul style="margin: 6px 0 0 18px;">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Ringkasan data kamar; setiap nilai tetap berasal dari controller. -->
    <section class="ringkasan-kamar-grid" aria-label="Ringkasan data kamar">
        <div class="ringkasan-card">
            <span class="dk-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 21v-5h6v5M8 8h2m4 0h2M8 12h2m4 0h2"/></svg></span>
            <div class="dk-stat-info"><span class="ringkasan-card__title">Total Kamar</span><strong class="ringkasan-card__value">{{ $jumlahKamar }}</strong><div class="ringkasan-card__sub">Kamar terdaftar</div></div>
        </div>
        <div class="ringkasan-card">
            <span class="dk-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17V9a3 3 0 0 1 6 0v4h12v4M3 17v3m18-3v3M3 13h18v4H3z"/></svg></span>
            <div class="dk-stat-info"><span class="ringkasan-card__title">Kapasitas Penghuni</span><strong class="ringkasan-card__value">{{ $totalKapasitas }}</strong><div class="ringkasan-card__sub">Total tempat tidur</div></div>
        </div>
        <div class="ringkasan-card">
            <span class="dk-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M17 5a3 3 0 0 1 0 6m1 4a5 5 0 0 1 3 5"/></svg></span>
            <div class="dk-stat-info"><span class="ringkasan-card__title">Mahasiswa Terisi</span><strong class="ringkasan-card__value">{{ $kamarTerisi }}</strong><div class="ringkasan-card__sub">Penghuni saat ini</div></div>
        </div>
        <div class="ringkasan-card">
            <span class="dk-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8m-4-4v8"/></svg></span>
            <div class="dk-stat-info"><span class="ringkasan-card__title">Sisa Kuota Kamar</span><strong class="ringkasan-card__value">{{ $sisaTempatTidur }}</strong><div class="ringkasan-card__sub">{{ $kamarTersedia }} kamar tersedia</div></div>
        </div>
    </section>
    <!-- PENGAJUAN KAMAR MAHASISWA (PENEMPATAN BARU & PINDAH KAMAR) -->
    @php
        $pengajuanMenunggu = collect($daftarPengajuan)->where('status', 'menunggu');
        $pengajuanSelesai = collect($daftarPengajuan)->where('status', '!=', 'menunggu')->take(10);
    @endphp
    <section class="pengajuan-kamar-section" id="pengajuan-kamar">
        <div class="pengajuan-kamar-head">
            <div>
                <h2 class="pengajuan-kamar-title">Pengajuan Kamar</h2>
                <p class="pengajuan-kamar-sub">
                    Periksa pengajuan penempatan baru maupun pengajuan pindah kamar. Penempatan
                    mahasiswa hanya diperbarui setelah disetujui. Perpindahan antarlantai/gedung
                    memerlukan persetujuan Musyrifah asal dan Musyrifah tujuan secara berurutan.
                    Penolakan menyimpan catatan yang dapat dibaca mahasiswa.
                </p>
            </div>
            <span class="pengajuan-kamar-badge {{ $pengajuanMenunggu->isEmpty() ? 'pengajuan-kamar-badge--empty' : '' }}">
                {{ $pengajuanMenunggu->count() }} menunggu
            </span>
        </div>
        @if ($pengajuanMenunggu->isEmpty())
            <div class="penghuni-empty">
                Tidak ada pengajuan kamar yang menunggu verifikasi.
            </div>
        @else
            <div class="penghuni-table-wrap">
                <table class="penghuni-table pengajuan-table pengajuan-table--pending">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Jenis</th>
                            <th>Kamar</th>
                            <th>Diajukan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengajuanMenunggu as $index => $pengajuan)
                            <tr>
                                <td data-label="No.">{{ $index + 1 }}</td>
                                <td data-label="Mahasiswa"><strong>{{ $pengajuan['nama'] }}</strong></td>
                                <td data-label="NIM">{{ $pengajuan['nim'] }}</td>
                                <td data-label="Jenis Pengajuan">
                                    <span class="pengajuan-jenis {{ $pengajuan['is_pindah'] ? 'pengajuan-jenis--pindah' : '' }}">
                                        {{ $pengajuan['jenis_label'] }}
                                    </span>
                                </td>
                                <td data-label="Kamar Tujuan">
                                    @if ($pengajuan['is_pindah'])
                                        <div>
                                            Kamar {{ $pengajuan['kamar_asal'] }}
                                            <span style="color:#777;">&rarr;</span>
                                            <strong>Kamar {{ $pengajuan['kamar'] }}</strong>
                                        </div>
                                        <div style="font-size:11px; color:#777;">
                                            Tujuan: {{ $pengajuan['gedung'] }} &bull; Lantai {{ $pengajuan['lantai'] }}
                                            &bull; Sisa {{ $pengajuan['sisa_kamar'] }} dari {{ $pengajuan['kapasitas'] }}
                                        </div>
                                        @if ($pengajuan['lintas_wilayah'])
                                            <div style="font-size:11px; font-weight:600; color:#800020; margin-top:4px;">
                                                Tahap: {{ $pengajuan['tahap_persetujuan'] === 'tujuan' ? 'Persetujuan Musyrifah Tujuan' : 'Persetujuan Musyrifah Asal' }}
                                            </div>
                                        @endif
                                    @else
                                        <div>Kamar {{ $pengajuan['kamar'] }}</div>
                                        <div style="font-size:11px; color:#777;">
                                            {{ $pengajuan['gedung'] }} &bull; Lantai {{ $pengajuan['lantai'] }}
                                            &bull; Sisa {{ $pengajuan['sisa_kamar'] }} dari {{ $pengajuan['kapasitas'] }}
                                        </div>
                                    @endif
                                </td>
                                <td data-label="Diajukan">
                                    <div>{{ $pengajuan['diajukan_pada'] }}</div>
                                    <span class="pengajuan-status pengajuan-status--menunggu" style="display:inline-block; margin-top:4px;">
                                        {{ $pengajuan['status_label'] }}
                                    </span>
                                </td>
                                <td data-label="Aksi">
                                    <div class="pengajuan-aksi">
                                        <button type="button" class="btn-pengajuan btn-pengajuan--periksa" data-open-modal="modal-pengajuan-{{ $pengajuan['id'] }}">
                                            Periksa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @foreach ($pengajuanMenunggu as $pengajuan)
                @php
                    $pindahLintasWilayah = $pengajuan['lintas_wilayah'];
                    $menungguTujuan = $pindahLintasWilayah && $pengajuan['tahap_persetujuan'] === 'tujuan';
                @endphp
                <dialog class="kamar-modal" id="modal-pengajuan-{{ $pengajuan['id'] }}">
                    <div class="kamar-modal__head">
                        <h2 class="kamar-modal__title">Periksa Pengajuan Kamar</h2>
                        <button type="button" class="kamar-modal__close" data-close-modal>&times;</button>
                    </div>
                    <div class="kamar-modal__body">
                        <div class="pengajuan-detail">
                            <div class="pengajuan-detail__item">
                                <strong>Mahasiswa</strong>
                                {{ $pengajuan['nama'] }}
                            </div>
                            <div class="pengajuan-detail__item">
                                <strong>NIM</strong>
                                {{ $pengajuan['nim'] }}
                            </div>
                            <div class="pengajuan-detail__item">
                                <strong>Jenis Pengajuan</strong>
                                {{ $pengajuan['jenis_label'] }}
                            </div>
                            <div class="pengajuan-detail__item">
                                <strong>Status</strong>
                                {{ $pengajuan['status_label'] }}
                            </div>
                            @if ($pindahLintasWilayah)
                                <div class="pengajuan-detail__item pengajuan-detail__item--full">
                                    <strong>Alur Persetujuan</strong>
                                    @if ($menungguTujuan)
                                        Musyrifah asal ({{ $pengajuan['persetujuan_asal_oleh'] ?: '—' }}) sudah menyetujui pada
                                        {{ $pengajuan['persetujuan_asal_pada'] ?: '-' }}.
                                        Langkah berikutnya: Musyrifah tujuan memutuskan penerimaan mahasiswa.
                                    @else
                                        Langkah pertama: Musyrifah asal menyetujui mahasiswa keluar.
                                        Langkah kedua: Musyrifah tujuan menyetujui penerimaan mahasiswa.
                                    @endif
                                    <p class="form-hint" style="margin-top:5px;">Kamar mahasiswa belum berubah sebelum kedua persetujuan selesai.</p>
                                </div>
                            @endif
                            @if ($pengajuan['is_pindah'])
                                <div class="pengajuan-detail__item">
                                    <strong>Kamar Asal</strong>
                                    Kamar {{ $pengajuan['kamar_asal'] }}
                                    ({{ $pengajuan['kamar_asal_gedung'] }}, Lantai {{ $pengajuan['kamar_asal_lantai'] }})
                                </div>
                            @endif
                            <div class="pengajuan-detail__item">
                                <strong>Kamar Tujuan</strong>
                                Kamar {{ $pengajuan['kamar'] }}
                                ({{ $pengajuan['gedung'] }}, Lantai {{ $pengajuan['lantai'] }})
                            </div>
                            <div class="pengajuan-detail__item">
                                <strong>Kapasitas Tujuan</strong>
                                {{ $pengajuan['kapasitas'] }} orang &bull; sisa {{ $pengajuan['sisa_kamar'] }} tempat
                            </div>
                            <div class="pengajuan-detail__item">
                                <strong>Diajukan Pada</strong>
                                {{ $pengajuan['diajukan_pada'] }}
                            </div>
                            @if ($pengajuan['is_pindah'])
                                <div class="pengajuan-detail__item pengajuan-detail__item--full">
                                    <strong>Alasan Perpindahan</strong>
                                    <div class="pengajuan-alasan">{{ $pengajuan['alasan'] ?: 'Alasan tidak dicatat.' }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="pengajuan-modal-actions">
                            <form
                                action="{{ route('musyrifah.pengajuan_kamar.approve', $pengajuan['id']) }}"
                                method="POST"
                                data-sui-confirm="Setujui pengajuan kamar ini?"
                                data-sui-confirm-title="Setujui pengajuan kamar?"
                                data-sui-confirm-label="Ya, Setujui">
                            >
                                @csrf
                                <button type="submit" class="btn-pengajuan btn-pengajuan--setuju" style="width:100%;" @disabled($pengajuan['sisa_kamar'] <= 0)>
                                    @if ($pindahLintasWilayah)
                                        {{ $menungguTujuan ? 'Terima Mahasiswa di Kamar Tujuan' : 'Setujui Keluar dari Kamar Asal' }}
                                    @else
                                        {{ $pengajuan['is_pindah'] ? 'Setujui Pindah Kamar' : 'Setujui Penempatan' }}
                                    @endif
                                </button>
                                @if ($pindahLintasWilayah)
                                    <p class="form-hint" style="margin-top:6px;">
                                        {{ $menungguTujuan
                                            ? 'Persetujuan ini menyelesaikan perpindahan dan memperbarui kamar mahasiswa.'
                                            : 'Persetujuan ini hanya meneruskan pengajuan ke Musyrifah tujuan. Mahasiswa belum dipindahkan.' }}
                                    </p>
                                @endif
                                @if ($pengajuan['sisa_kamar'] <= 0)
                                    <p class="form-hint" style="margin-top:6px;">
                                        Kamar tujuan tidak memiliki kuota yang mencukupi untuk menyetujui pengajuan.
                                    </p>
                                @endif
                            </form>
                            <form
                                class="pengajuan-tolak-form"
                                action="{{ route('musyrifah.pengajuan_kamar.reject', $pengajuan['id']) }}"
                                method="POST"
                                data-sui-confirm="Tolak pengajuan kamar ini? Catatan penolakan akan disimpan dan dapat dibaca mahasiswa."
                                data-sui-confirm-title="Tolak pengajuan kamar?"
                                data-sui-confirm-label="Ya, Tolak"
                                data-sui-confirm-tone="danger">
                            >
                                @csrf
                                <label class="form-label-kamar" for="catatan-{{ $pengajuan['id'] }}">
                                    Catatan Penolakan (dapat dibaca mahasiswa)
                                </label>
                                <textarea
                                    class="form-textarea-kamar"
                                    name="catatan"
                                    id="catatan-{{ $pengajuan['id'] }}"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Contoh: kamar tujuan sudah penuh, silakan pilih kamar lain."
                                ></textarea>
                                <div class="pengajuan-aksi" style="justify-content:flex-end;">
                                    <button type="button" class="btn-pengajuan btn-pengajuan--periksa" data-close-modal>
                                        Batal
                                    </button>
                                    <button type="submit" class="btn-pengajuan btn-pengajuan--tolak">
                                        Tolak Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </dialog>
            @endforeach
        @endif
        @if ($pengajuanSelesai->isNotEmpty())
            <details class="pengajuan-riwayat">
                <summary>Riwayat pengajuan terakhir ({{ $pengajuanSelesai->count() }})</summary>
                <div class="penghuni-table-wrap">
                    <table class="penghuni-table pengajuan-table pengajuan-table--history">
                        <thead>
                            <tr>
                                <th>Mahasiswa</th>
                                <th>NIM</th>
                                <th>Jenis</th>
                                <th>Kamar</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Diproses</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengajuanSelesai as $pengajuan)
                                <tr>
                                    <td data-label="Mahasiswa">{{ $pengajuan['nama'] }}</td>
                                    <td data-label="NIM">{{ $pengajuan['nim'] }}</td>
                                    <td data-label="Jenis">{{ $pengajuan['jenis_label'] }}</td>
                                    <td data-label="Kamar">
                                        @if ($pengajuan['is_pindah'])
                                            Kamar {{ $pengajuan['kamar_asal'] }} &rarr; Kamar {{ $pengajuan['kamar'] }}
                                        @else
                                            Kamar {{ $pengajuan['kamar'] }}
                                        @endif
                                    </td>
                                    <td data-label="Status">
                                        <span class="pengajuan-status pengajuan-status--{{ $pengajuan['status'] }}">
                                            {{ $pengajuan['status_label'] }}
                                        </span>
                                    </td>
                                    <td data-label="Catatan" style="max-width:220px; font-size:12px; color:#555;">
                                        {{ $pengajuan['catatan'] ?: '-' }}
                                    </td>
                                    <td data-label="Diproses">{{ $pengajuan['diproses_pada'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endif
    </section>
    <!-- Daftar kamar: blok terpisah, lebar penuh, sejajar dengan ringkasan dan pengajuan. -->
    <div class="dk-list-shell">
        <div class="dk-list-heading">
            <div>
                <h2 class="dk-list-title">Daftar Kamar</h2>
                <p class="dk-list-desc">Temukan kamar menurut gedung, lantai, status, dan fasilitas.</p>
                <div class="dk-list-count">{{ count($daftarKamar) }} kamar tercatat</div>
            </div>
        </div>
        <!-- KONTROL FILTER DAN PENCARIAN -->
        <div class="kamar-control-bar">
        <div class="kamar-filters">
            <label class="dk-search">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4 4"/></svg>
                <input type="search" id="inputCariKamar" class="kamar-input-search" placeholder="Cari nomor kamar, gedung, atau fasilitas..." aria-label="Cari kamar">
            </label>
            <select id="filterLantai" class="kamar-select-filter" aria-label="Filter lantai">
                <option value="">Semua Lantai</option>
                @php
                    $lantaiList = collect($daftarKamar)->pluck('lantai')->unique()->sort();
                @endphp
                @foreach ($lantaiList as $lt)
                    <option value="{{ $lt }}">Lantai {{ $lt }}</option>
                @endforeach
            </select>
            <select id="filterStatus" class="kamar-select-filter" aria-label="Filter status">
                <option value="">Semua Status</option>
                <option value="tersedia">Tersedia</option>
                <option value="penuh">Penuh</option>
                <option value="perbaikan">Dalam Perbaikan</option>
                <option value="tidak-tersedia">Tidak Tersedia</option>
            </select>
            <button type="button" class="dk-reset" id="btnResetFilterKamar">Reset Filter</button>
        </div>
    </div>
    <p class="dk-list-hint">Menampilkan <strong id="jumlahKamarTampil">{{ count($daftarKamar) }}</strong> dari {{ count($daftarKamar) }} kamar. Klik foto atau tombol detail untuk melihat informasi kamar.</p>
    <!-- DAFTAR KARTU KAMAR -->
    <section class="daftar-kamar-grid" id="containerDaftarKamar">
        @forelse ($daftarKamar as $kamar)
            @php
                $statusSlug = mb_strtolower(trim((string) $kamar['status']));
                $statusFilter = match ($statusSlug) {
                    'tersedia' => 'tersedia',
                    'penuh' => 'penuh',
                    'dalam perbaikan' => 'perbaikan',
                    'tidak tersedia' => 'tidak-tersedia',
                    default => 'lainnya',
                };
                $badgeClass = match ($statusSlug) {
                    'tersedia' => 'badge-tersedia',
                    'penuh', 'tidak tersedia' => 'badge-penuh',
                    default => 'badge-perbaikan',
                };
                $bolehKelola = (bool) ($kamar['boleh_kelola'] ?? false);
                $bolehLihatPenghuni = (bool) ($kamar['boleh_lihat_penghuni'] ?? false);
                $kelasWarnaSisa = ((int) $kamar['sisa'] > 0) ? 'kamar-card__spec-val--available' : 'kamar-card__spec-val--full';
                $kelasWarnaPengajuan = ((int) $kamar['pengajuan_menunggu'] > 0) ? 'kamar-card__spec-val--pending' : 'kamar-card__spec-val--normal';
            @endphp
            <article class="kamar-card"
                id="kamar-{{ $kamar['id'] }}"
                data-nomor="{{ strtolower($kamar['nomor']) }}"
                data-gedung="{{ strtolower($kamar['gedung']) }}"
                data-lantai="{{ $kamar['lantai'] }}"
                data-status="{{ $statusFilter }}"
                data-fasilitas="{{ strtolower($kamar['fasilitas'] ?? '') }}">
                <div class="kamar-card__img-wrap" data-open-modal="modal-detail-{{ $kamar['id'] }}" title="Lihat galeri foto kamar {{ $kamar['nomor'] }}">
                    @if ($kamar['foto_url'])
                        <img src="{{ $kamar['foto_url'] }}" alt="Foto Kamar {{ $kamar['nomor'] }}" class="kamar-card__img" loading="lazy">
                    @else
                        <div class="kamar-card__placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>Belum ada foto</span>
                        </div>
                    @endif
                    @if (count($kamar['fotos']) > 0)
                        <span class="kamar-card__foto-count">{{ count($kamar['fotos']) }} Foto</span>
                    @endif
                    <span class="kamar-card__badge-status {{ $badgeClass }}">
                        {{ $kamar['status'] }}
                    </span>
                </div>
                <div class="kamar-card__body">
                    <div class="kamar-card__header">
                        <div class="kamar-card__nomor">Kamar {{ $kamar['nomor'] }}</div>
                        <div class="kamar-card__lantai">Lantai {{ $kamar['lantai'] }} &bull; {{ $kamar['gedung'] }}</div>
                    </div>
                    <div class="kamar-card__specs">
                        <div class="kamar-card__spec-item">
                            <span class="kamar-card__spec-label">Kapasitas:</span>
                            <span class="kamar-card__spec-val">{{ $kamar['kapasitas'] }} Orang</span>
                        </div>
                        <div class="kamar-card__spec-item">
                            <span class="kamar-card__spec-label">Terisi:</span>
                            <span class="kamar-card__spec-val">{{ $kamar['terisi'] }} Mahasiswa</span>
                        </div>
                        <div class="kamar-card__spec-item">
                            <span class="kamar-card__spec-label">Sisa Kuota:</span>
                            <span class="kamar-card__spec-val {{ $kelasWarnaSisa }}">
                                {{ $kamar['sisa'] }} Tempat
                            </span>
                        </div>
                        <div class="kamar-card__spec-item">
                            <span class="kamar-card__spec-label">Pengajuan:</span>
                            @if ($bolehKelola)
                                <span class="kamar-card__spec-val {{ $kelasWarnaPengajuan }}">
                                    {{ $kamar['pengajuan_menunggu'] }} Menunggu
                                </span>
                            @else
                                <span class="kamar-card__spec-val" title="Antrian verifikasi wilayah lain tidak ditampilkan">—</span>
                            @endif
                        </div>
                        <div class="kamar-card__spec-item">
                            <span class="kamar-card__spec-label">Akses:</span>
                            <span class="kamar-card__spec-val">{{ $bolehKelola ? 'Wilayah Dikelola' : 'Informasi Kamar' }}</span>
                        </div>
                    </div>
                    <div class="kamar-card__fasilitas-text" title="{{ $kamar['fasilitas'] ?: 'Belum ada rincian fasilitas.' }}">
                        <strong>Fasilitas:</strong> {{ $kamar['fasilitas'] ?: 'Belum ada rincian fasilitas.' }}
                    </div>
                    <div class="kamar-card__actions">
                        <button type="button" class="btn-card btn-card--detail" data-open-modal="modal-detail-{{ $kamar['id'] }}">
                            {{ $bolehLihatPenghuni ? 'Detail & Penghuni' : 'Detail Kamar' }}
                        </button>
                        @if ($bolehKelola)
                            <button type="button" class="btn-card btn-card--edit" data-open-modal="modal-edit-{{ $kamar['id'] }}">
                                Edit
                            </button>
                        @endif
                        @if ($bolehKelola && (int) $kamar['terisi'] === 0)
                            <form action="{{ route('musyrifah.data_kamar.destroy', $kamar['id']) }}" method="POST"
                                data-sui-confirm="Yakin ingin menghapus kamar ini?"
                                data-sui-confirm-title="Hapus kamar?"
                                data-sui-confirm-label="Ya, Hapus"
                                data-sui-confirm-tone="danger">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-card btn-card--delete" title="Hapus Kamar">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
            <!-- MODAL DETAIL & PENGHUNI KAMAR -->
            <dialog class="kamar-modal" id="modal-detail-{{ $kamar['id'] }}">
                <div class="kamar-modal__head">
                    <h3 class="kamar-modal__title">{{ $bolehLihatPenghuni ? 'Detail & Daftar Penghuni' : 'Detail Kamar' }} - Kamar {{ $kamar['nomor'] }}</h3>
                    <button type="button" class="kamar-modal__close" data-close-modal>&times;</button>
                </div>
                <div class="kamar-modal__body">
                    <div style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <strong style="font-size: 13px; color: #800020;">Galeri Foto ({{ count($kamar['fotos']) }}/{{ $kamar['maks_foto'] }})</strong>
                        <span style="font-size: 11px; color: #6b7280;">Foto pertama otomatis menjadi foto utama (thumbnail)</span>
                    </div>
                    <div class="kamar-galeri">
                        @forelse ($kamar['fotos'] as $indexFoto => $foto)
                            <figure class="kamar-galeri__item">
                                <a href="{{ $foto['url'] }}" target="_blank" rel="noopener" title="Lihat foto ukuran penuh">
                                    <img src="{{ $foto['url'] }}" alt="Foto {{ $indexFoto + 1 }} Kamar {{ $kamar['nomor'] }}" loading="lazy">
                                </a>
                                @if ($indexFoto === 0)
                                    <span class="kamar-galeri__utama">Foto Utama</span>
                                @endif
                                <figcaption class="kamar-galeri__caption">Foto {{ $indexFoto + 1 }}</figcaption>
                                @if ($bolehKelola)
                                    <div class="kamar-galeri__actions">
                                    <form action="{{ route('musyrifah.data_kamar.foto.update', [$kamar['id'], $foto['id']]) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="btn-foto btn-foto--ganti" title="Ganti foto ini">
                                            Ganti
                                            <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" required onchange="if (this.files.length) { this.form.submit(); }">
                                        </label>
                                    </form>
                                    <form action="{{ route('musyrifah.data_kamar.foto.destroy', [$kamar['id'], $foto['id']]) }}" method="POST"
                                          data-sui-confirm="Hapus foto ini dari galeri kamar?"
                                          data-sui-confirm-title="Hapus foto kamar?"
                                          data-sui-confirm-label="Ya, Hapus"
                                          data-sui-confirm-tone="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-foto btn-foto--hapus">Hapus</button>
                                    </form>
                                </div>
                                @endif
                            </figure>
                        @empty
                            <div class="kamar-galeri__kosong">Belum ada foto untuk kamar ini. Unggah minimal satu foto agar tampil pada halaman Informasi Kamar mahasiswa.</div>
                        @endforelse
                    </div>
                    @if ($bolehKelola && $kamar['sisa_slot_foto'] > 0)
                        <form action="{{ route('musyrifah.data_kamar.foto.store', $kamar['id']) }}" method="POST" enctype="multipart/form-data" class="kamar-galeri__tambah">
                            @csrf
                            <label for="tambah_foto_{{ $kamar['id'] }}">Tambah Foto ({{ count($kamar['fotos']) }}/{{ $kamar['maks_foto'] }})</label>
                            <input type="file" id="tambah_foto_{{ $kamar['id'] }}" name="fotos[]" accept=".jpg,.jpeg,.png,.webp" multiple required>
                            <button type="submit" class="btn-foto--unggah">Unggah</button>
                            <div class="form-hint">Bisa memilih hingga {{ $kamar['sisa_slot_foto'] }} foto sekaligus. Format JPG, PNG, WEBP (maks. 5MB per foto).</div>
                        </form>
                    @elseif ($bolehKelola)
                        <div class="form-hint">Jumlah foto sudah maksimal ({{ $kamar['maks_foto'] }}). Hapus salah satu foto untuk menambahkan foto baru.</div>
                    @endif
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; margin-bottom: 20px; background: #fffafb; padding: 14px; border-radius: 12px; border: 1px solid #ecd8de;">
                        <div><strong style="font-size:12px; color:#777;">Gedung / Lantai:</strong><br>{{ $kamar['gedung'] }} &bull; Lantai {{ $kamar['lantai'] }}</div>
                        <div><strong style="font-size:12px; color:#777;">Kapasitas / Sisa:</strong><br>{{ $kamar['kapasitas'] }} Orang (Sisa {{ $kamar['sisa'] }})</div>
                        @if ($bolehKelola)
                            <div><strong style="font-size:12px; color:#777;">Setelah Pengajuan:</strong><br>{{ $kamar['sisa_setelah_pengajuan'] }} Tempat ({{ $kamar['pengajuan_menunggu'] }} menunggu)</div>
                        @else
                            <div><strong style="font-size:12px; color:#777;">Terisi:</strong><br>{{ $kamar['terisi'] }} dari {{ $kamar['kapasitas'] }} tempat</div>
                        @endif
                        <div><strong style="font-size:12px; color:#777;">Status Kamar:</strong><br><span class="kamar-card__badge-status {{ $badgeClass }}" style="position:static; display:inline-block; margin-top:2px;">{{ $kamar['status'] }}</span></div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <strong style="display:block; font-size: 13px; color: #800020; margin-bottom: 4px;">Fasilitas Kamar:</strong>
                        <p style="font-size: 13px; color: #444; line-height: 1.6; margin: 0;">{{ $kamar['fasilitas'] ?: 'Belum ada rincian fasilitas.' }}</p>
                    </div>
                    @if ($bolehLihatPenghuni)
                    <div>
                        <strong style="display:block; font-size: 14px; color: #800020; margin-bottom: 8px;">
                            Mahasiswa Penghuni Kamar ({{ $kamar['terisi'] }} / {{ $kamar['kapasitas'] }}):
                        </strong>
                        @if (!empty($kamar['penghuni']))
                            <div class="penghuni-table-wrap">
                                <table class="penghuni-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Mahasiswa</th>
                                            <th>NIM</th>
                                            <th>Kontak WhatsApp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kamar['penghuni'] as $idx => $mhs)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td><strong>{{ $mhs['name'] }}</strong></td>
                                                <td>{{ $mhs['nim'] }}</td>
                                                <td>{{ $mhs['no_hp'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="penghuni-empty">
                                Belum ada mahasiswa yang menempati kamar ini.
                            </div>
                        @endif
                    </div>
                    @else
                        <div class="penghuni-empty" role="note">
                            Informasi ketersediaan kamar dapat dilihat oleh semua Musyrifah. Identitas penghuni
                            hanya tersedia bagi Musyrifah yang ditugaskan Admin pada gedung dan lantai ini.
                        </div>
                    @endif
                    <div class="form-modal-actions">
                        <button type="button" class="btn-card btn-card--edit" data-close-modal style="flex:none; padding:8px 18px;">Tutup</button>
                    </div>
                </div>
            </dialog>
            <!-- MODAL EDIT KAMAR -->
            @if ($bolehKelola)
            <dialog class="kamar-modal" id="modal-edit-{{ $kamar['id'] }}">
                <div class="kamar-modal__head">
                    <h3 class="kamar-modal__title">Ubah Data Kamar {{ $kamar['nomor'] }}</h3>
                    <button type="button" class="kamar-modal__close" data-close-modal>&times;</button>
                </div>
                <div class="kamar-modal__body">
                    <form action="{{ route('musyrifah.data_kamar.update', $kamar['id']) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-grid-2">
                            <div class="form-group-item">
                                <label for="edit_nomor_{{ $kamar['id'] }}" class="form-label-kamar">Nomor Kamar <span style="color:red">*</span></label>
                                <input type="text" id="edit_nomor_{{ $kamar['id'] }}" name="nomor_kamar" class="form-input-kamar" value="{{ $kamar['nomor'] }}" required>
                            </div>
                            <div class="form-group-item">
                                <label for="edit_gedung_{{ $kamar['id'] }}" class="form-label-kamar">Nama Gedung</label>
                                <input type="text" id="edit_gedung_{{ $kamar['id'] }}" name="gedung" class="form-input-kamar" value="{{ $kamar['gedung'] }}" readonly required>
                                <div class="form-hint">Gedung tidak dapat dipindahkan melalui edit kamar.</div>
                            </div>
                            <div class="form-group-item">
                                <label for="edit_lantai_{{ $kamar['id'] }}" class="form-label-kamar">Lantai <span style="color:red">*</span></label>
                                <input type="number" id="edit_lantai_{{ $kamar['id'] }}" name="lantai" class="form-input-kamar" min="1" max="20" value="{{ $kamar['lantai'] }}" readonly required>
                            </div>
                            <div class="form-group-item">
                                <label for="edit_kapasitas_{{ $kamar['id'] }}" class="form-label-kamar">Kapasitas Tempat Tidur <span style="color:red">*</span></label>
                                <input type="number" id="edit_kapasitas_{{ $kamar['id'] }}" name="kapasitas" class="form-input-kamar" min="1" max="20" value="{{ $kamar['kapasitas'] }}" required>
                            </div>
                            <div class="form-group-item form-group-item--full">
                                <label for="edit_status_{{ $kamar['id'] }}" class="form-label-kamar">Status Ketersediaan <span style="color:red">*</span></label>
                                <select id="edit_status_{{ $kamar['id'] }}" name="status" class="form-select-kamar" required>
                                    <option value="Tersedia" @selected($kamar['status'] === 'Tersedia')>Tersedia</option>
                                    <option value="Penuh" @selected($kamar['status'] === 'Penuh')>Penuh</option>
                                    <option value="Dalam Perbaikan" @selected($kamar['status'] === 'Dalam Perbaikan')>Dalam Perbaikan</option>
                                    <option value="Tidak Tersedia" @selected($kamar['status'] === 'Tidak Tersedia')>Tidak Tersedia</option>
                                </select>
                            </div>
                            <div class="form-group-item form-group-item--full">
                                <label for="edit_fasilitas_{{ $kamar['id'] }}" class="form-label-kamar">Fasilitas Kamar</label>
                                <textarea id="edit_fasilitas_{{ $kamar['id'] }}" name="fasilitas" class="form-textarea-kamar" placeholder="Contoh: 4 Ranjang tidur, lemari pakaian, meja belajar, kamar mandi dalam">{{ $kamar['fasilitas'] }}</textarea>
                            </div>
                            <div class="form-group-item form-group-item--full">
                                <label for="edit_foto_{{ $kamar['id'] }}" class="form-label-kamar">Ganti Foto Utama (Opsional)</label>
                                <input type="file" id="edit_foto_{{ $kamar['id'] }}" name="foto" class="form-input-kamar" accept=".jpg,.jpeg,.png,.webp">
                                <div class="form-hint">Biarkan kosong jika tidak ingin mengganti foto utama. Format JPG, PNG, WEBP (maks. 5MB). Kelola seluruh foto melalui tombol Foto pada kartu kamar.</div>
                            </div>
                        </div>
                        <div class="form-modal-actions">
                            <button type="button" class="btn-card btn-card--edit" data-close-modal style="flex:none; padding:9px 18px;">Batal</button>
                            <button type="submit" class="btn-card btn-card--detail" style="flex:none; padding:9px 24px;">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </dialog>
            @endif
        @empty
            <div class="kamar-kosong" style="display:block">
                Belum ada data kamar Ma'had. Silakan klik tombol <strong>Tambah Kamar Baru</strong> di atas.
            </div>
        @endforelse
        <div class="kamar-kosong" id="pesanKamarKosong">
            Kamar yang dicari tidak ditemukan.
        </div>
    </section>
    </div><!-- /.dk-list-shell -->
</div><!-- /.data-kamar-page -->
<!-- MODAL TAMBAH KAMAR BARU -->
<dialog class="kamar-modal" id="modalTambahKamar">
    <div class="kamar-modal__head">
        <h3 class="kamar-modal__title">Tambah Data Kamar Baru</h3>
        <button type="button" class="kamar-modal__close" data-close-modal>&times;</button>
    </div>
    <div class="kamar-modal__body">
        <form action="{{ route('musyrifah.data_kamar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <p class="form-hint" style="margin:0 0 14px;">Semua Musyrifah dapat mendata gedung, lantai, dan kamar baru. Hak mengelola penghuni serta validasi tetap mengikuti penempatan dari Admin.</p>
            <div class="form-grid-2">
                <div class="form-group-item">
                    <label for="input_nomor_kamar" class="form-label-kamar">Nomor Kamar <span style="color:red">*</span></label>
                    <input type="text" id="input_nomor_kamar" name="nomor_kamar" class="form-input-kamar" value="{{ old('nomor_kamar', '') }}" maxlength="50" placeholder="Contoh: 101 atau 1.1" required>
                </div>
                <div class="form-group-item">
                    <label for="input_gedung" class="form-label-kamar">Nama Gedung <span style="color:red">*</span></label>
                    <input type="text" id="input_gedung" name="gedung" class="form-input-kamar" maxlength="150" value="{{ old('gedung', '') }}" placeholder="Contoh: Gedung A atau Gedung B" required>
                </div>
                <div class="form-group-item">
                    <label for="input_lantai" class="form-label-kamar">Lantai <span style="color:red">*</span></label>
                    <input type="number" id="input_lantai" name="lantai" class="form-input-kamar" min="1" max="20" value="{{ old('lantai', 1) }}" required>
                </div>
                <div class="form-group-item">
                    <label for="input_kapasitas" class="form-label-kamar">Kapasitas Santri <span style="color:red">*</span></label>
                    <input type="number" id="input_kapasitas" name="kapasitas" class="form-input-kamar" min="1" max="20" value="4" required>
                </div>
                <div class="form-group-item form-group-item--full">
                    <label for="input_status" class="form-label-kamar">Status Ketersediaan <span style="color:red">*</span></label>
                    <select id="input_status" name="status" class="form-select-kamar" required>
                        <option value="Tersedia" selected>Tersedia</option>
                        <option value="Penuh">Penuh</option>
                        <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
                <div class="form-group-item form-group-item--full">
                    <label for="input_fasilitas" class="form-label-kamar">Fasilitas Kamar</label>
                    <textarea id="input_fasilitas" name="fasilitas" class="form-textarea-kamar" placeholder="Contoh: 4 Ranjang bertingkat, kasur busa, lemari 2 pintu, meja belajar, kamar mandi dalam"></textarea>
                </div>
                <div class="form-group-item form-group-item--full">
                    <label for="input_foto" class="form-label-kamar">Upload Foto Kamar (maks. 4)</label>
                    <input type="file" id="input_foto" name="foto[]" class="form-input-kamar" accept=".jpg,.jpeg,.png,.webp" multiple>
                    <div class="form-hint">Dapat memilih beberapa foto sekaligus (maksimal 4). Foto pertama menjadi foto utama. Format JPG, PNG, WEBP (maks. 5MB per foto).</div>
                </div>
            </div>
            <div class="form-modal-actions">
                <button type="button" class="btn-card btn-card--edit" data-close-modal style="flex:none; padding:9px 18px;">Batal</button>
                <button type="submit" class="btn-card btn-card--detail" style="flex:none; padding:9px 24px;">Simpan Kamar</button>
            </div>
        </form>
    </div>
</dialog>
<!-- JAVASCRIPT FILTER & MODAL HANDLING -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputCari = document.getElementById('inputCariKamar');
        const filterLantai = document.getElementById('filterLantai');
        const filterStatus = document.getElementById('filterStatus');
        const kartuKamar = document.querySelectorAll('.kamar-card');
        const pesanKosong = document.getElementById('pesanKamarKosong');
        function filterKamar() {
            const cari = (inputCari?.value || '').toLowerCase().trim();
            const lantaiDipilih = filterLantai?.value || '';
            const statusDipilih = filterStatus?.value || '';
            let jumlahTampil = 0;
            kartuKamar.forEach(function (kartu) {
                const nomor = kartu.dataset.nomor || '';
                const gedung = kartu.dataset.gedung || '';
                const lantai = kartu.dataset.lantai || '';
                const status = kartu.dataset.status || '';
                const fasilitas = kartu.dataset.fasilitas || '';
                const cocokCari = !cari || nomor.includes(cari) || gedung.includes(cari) || fasilitas.includes(cari);
                const cocokLantai = !lantaiDipilih || lantai === lantaiDipilih;
                const cocokStatus = !statusDipilih || status === statusDipilih;
                const tampil = cocokCari && cocokLantai && cocokStatus;
                kartu.style.display = tampil ? '' : 'none';
                if (tampil) jumlahTampil++;
            });
            if (pesanKosong) {
                pesanKosong.style.display = (kartuKamar.length > 0 && jumlahTampil === 0) ? 'block' : 'none';
            }
            const hitung = document.getElementById('jumlahKamarTampil');
            if (hitung) hitung.textContent = String(jumlahTampil);
        }
        inputCari?.addEventListener('input', filterKamar);
        filterLantai?.addEventListener('change', filterKamar);
        filterStatus?.addEventListener('change', filterKamar);
        document.getElementById('btnResetFilterKamar')?.addEventListener('click', function () {
            if (inputCari) inputCari.value = '';
            if (filterLantai) filterLantai.value = '';
            if (filterStatus) filterStatus.value = '';
            filterKamar();
        });
        filterKamar();
        // Modal triggers
        document.getElementById('btnBukaTambahKamar')?.addEventListener('click', function () {
            const modal = document.getElementById('modalTambahKamar');
            if (modal && typeof modal.showModal === 'function') modal.showModal();
        });
        document.querySelectorAll('[data-open-modal]').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                const targetId = tombol.dataset.openModal;
                const modal = document.getElementById(targetId);
                if (modal && typeof modal.showModal === 'function') modal.showModal();
            });
        });
        document.querySelectorAll('[data-close-modal]').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                const modal = tombol.closest('dialog');
                if (modal && typeof modal.close === 'function') modal.close();
            });
        });
    });
</script>
@endsection
