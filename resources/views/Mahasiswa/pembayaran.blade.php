@extends('layouts.mahasiswa')

@section('title', 'Pembayaran')

@section('content')

<style>

/* SIMAHAD · Pembayaran Mahasiswa

   Desain responsif saja — seluruh route, form, CSRF dan logika pembayaran tetap dipertahankan. */

.bayar-page {

    --maroon:#800b22; --maroon-dark:#610719; --maroon-tint:#fff3f6;

    --ink:#251e26; --secondary:#574d56; --muted:#7b707b;

    --border:#eadce1; --line:#f0e5e9; --gold:#c89e50;

    display:block !important; clear:both; position:relative;

    width:100% !important; max-width:100% !important; min-width:0 !important;

    margin:0 !important; padding:0 0 28px !important;

    color:var(--ink); font:400 13.5px/1.58 'Plus Jakarta Sans',Poppins,Arial,sans-serif;

    container-type:inline-size;

}

.bayar-page *, .bayar-page *::before, .bayar-page *::after { box-sizing:border-box; }

.bayar-page > *, .bayar-page div, .bayar-page article,

.bayar-page section, .bayar-page form { min-width:0; }

.bayar-page a { text-decoration:none; }

.bayar-page button,.bayar-page input,.bayar-page textarea { font-family:inherit; }

.bayar-page a:focus-visible,.bayar-page button:focus-visible,

.bayar-page input:focus-visible,.bayar-page textarea:focus-visible {

    outline:3px solid rgba(180,111,131,.36);outline-offset:2px;

}

/* Navigasi: Dashboard yang aktif, tanpa tombol kembali duplikat */

.bayar-page .bayar-breadcrumb {

    display:flex;align-items:center;flex-wrap:wrap;gap:8px;

    padding:0;margin:0 0 10px;color:#978b94;font-size:12px;

}

.bayar-page .bayar-breadcrumb a { color:var(--maroon);font-weight:750; }

.bayar-page .bayar-breadcrumb a:hover { text-decoration:underline; }

.bayar-page .page-head {

    display:flex;align-items:center;justify-content:space-between;

    gap:12px 18px;flex-wrap:wrap;margin:0 0 18px;

    padding:0 0 17px;border-bottom:1px solid #e8d7dc;

}

.bayar-page .page-head > div { flex:1 1 360px; }

.bayar-page .page-head h1 {

    margin:0;color:#261a22;font-size:clamp(23px,2vw,28px);

    line-height:1.22;letter-spacing:-.035em;font-weight:800;

}

.bayar-page .page-head p {

    margin:6px 0 0;max-width:800px;color:var(--muted);

    font-size:12.5px;line-height:1.65;

}

.bayar-page .periode-chip {

    display:inline-flex;align-items:center;justify-content:center;gap:7px;

    max-width:100%;padding:7px 12px;border:1px solid #ead6dc;

    border-radius:9px;background:#fff;color:var(--maroon);

    font-size:11.5px;font-weight:750;line-height:1.45;

    white-space:normal;text-align:center;

}

.bayar-page .periode-chip svg { flex:0 0 14px; }

.bayar-page .info-note {

    display:flex;align-items:flex-start;gap:11px;

    margin:0 0 16px;padding:12px 15px;

    border:1px solid #eddfbd;border-left:3px solid #cba457;

    border-radius:11px;background:#fffaf0;color:#735b2e;

    font-size:12px;line-height:1.75;

}

.bayar-page .info-note svg { flex:0 0 17px;margin-top:2px; }

.bayar-page .info-note strong { color:#695022; }

/* Ringkasan tagihan: empat kartu ringkas pada desktop */

.bayar-page .ringkasan-grid {

    display:grid;grid-template-columns:repeat(4,minmax(0,1fr));

    gap:12px;margin:0 0 16px;

}

.bayar-page .stat-card {

    position:relative;min-width:0;overflow:hidden;

    display:flex;flex-direction:column;justify-content:flex-start;

    gap:5px;padding:15px 16px;

    border:1px solid var(--border);border-radius:13px;

    background:#fff;box-shadow:0 2px 12px rgba(50,16,30,.026);

}

.bayar-page .stat-card::after {

    content:"";position:absolute;right:15px;top:15px;

    width:9px;height:9px;border-radius:50%;background:#ead1d9;

}

.bayar-page .stat-card:nth-child(2)::after { background:#bed9f3; }

.bayar-page .stat-card:nth-child(3)::after { background:#eed9a2; }

.bayar-page .stat-card:nth-child(4)::after { background:#c9e7d2; }

.bayar-page .stat-card--utama {

    border-color:#e6cbd3;background:linear-gradient(135deg,#fff,#fff5f7);

}

.bayar-page .stat-card .stat-label {

    display:block;max-width:calc(100% - 15px);

    color:#736771;font-size:11px;font-weight:750;line-height:1.5;

}

.bayar-page .stat-card .stat-nilai {

    display:block;margin-top:2px;color:#29212a;

    font-size:clamp(17px,1.45vw,21px);font-weight:800;letter-spacing:-.025em;

    line-height:1.25;overflow-wrap:anywhere;

}

.bayar-page .stat-card--utama .stat-nilai { color:var(--maroon); }

.bayar-page .stat-card .stat-sub {

    display:block;margin-top:2px;color:#867a84;

    font-size:11.2px;line-height:1.55;overflow-wrap:anywhere;

}

.bayar-page .stat-card .stat-nilai.stat-status-value { font-size:17px !important; }

.bayar-page .progres {

    height:5px;overflow:hidden;margin:6px 0 0;

    background:#f0e2e6;border-radius:99px;

}

.bayar-page .progres span {

    display:block;height:100%;border-radius:99px;

    background:linear-gradient(90deg,var(--maroon),#cc9860);

}

/* Informasi rekening */

.bayar-page .rekening-card {

    display:flex;align-items:center;justify-content:space-between;

    flex-wrap:wrap;gap:15px 24px;

    margin:0 0 20px;padding:18px 20px;

    border:1px solid #ead1d8;border-radius:13px;

    background:#fff;box-shadow:0 2px 12px rgba(50,16,30,.02);

}

.bayar-page .rekening-info { flex:1 1 260px;min-width:0; }

.bayar-page .rekening-bank {

    display:inline-flex;align-items:center;gap:6px;

    padding:4px 10px;background:#fff1f5;

    color:var(--maroon);border-radius:7px;

    font-size:11px;font-weight:800;line-height:1.4;

}

.bayar-page .rekening-nomor {

    margin:8px 0 3px;color:#2b1c23;

    font-size:clamp(20px,2vw,27px);font-weight:800;

    line-height:1.2;letter-spacing:.025em;overflow-wrap:anywhere;

}

.bayar-page .rekening-nama {

    color:var(--muted);font-size:12px;

    line-height:1.6;overflow-wrap:anywhere;

}

.bayar-page .rekening-nama strong {color:#30262d;}

.bayar-page .btn-salin {

    display:inline-flex;align-items:center;justify-content:center;gap:8px;

    flex:0 1 auto;min-height:39px;padding:9px 14px;

    border:1px solid var(--maroon);border-radius:9px;

    background:var(--maroon);color:#fff;

    font-size:11.5px;font-weight:750;line-height:1.4;

    cursor:pointer;transition:background .16s,border-color .16s;

}

.bayar-page .btn-salin:hover {background:var(--maroon-dark);border-color:var(--maroon-dark);}

.bayar-page .btn-salin.is-copied {background:#1c7c4f;border-color:#1c7c4f;}

.bayar-page .btn-salin svg {flex:none;}

/* Angsuran */

.bayar-page .bagian-judul {

    display:flex;flex-wrap:wrap;align-items:center;

    justify-content:space-between;gap:7px 14px;

    margin:0 0 12px;

}

.bayar-page .bagian-judul h2 {

    margin:0;color:#261e27;font-size:16px;

    font-weight:800;line-height:1.4;

}

.bayar-page .bagian-judul span {

    color:#887c85;font-size:11.5px;

}

.bayar-page .angsuran-grid {

    display:grid;grid-template-columns:repeat(3,minmax(0,1fr));

    gap:14px;align-items:start;margin:0 0 19px;

}

.bayar-page .angsuran-card {

    display:flex;flex-direction:column;gap:0;min-width:0;

    padding:16px;border:1px solid var(--border);

    border-radius:13px;background:#fff;

    box-shadow:0 2px 12px rgba(50,16,30,.027);

}

.bayar-page .angsuran-card.is-lunas {border-color:#cfe8d8;background:#fdfffd;}

.bayar-page .angsuran-card.is-terlambat {border-color:#f0cbd2;}

.bayar-page .angsuran-head {

    display:flex;align-items:flex-start;justify-content:space-between;

    flex-wrap:wrap;gap:7px;

}

.bayar-page .angsuran-tahap {

    color:var(--maroon);font-size:12px;

    font-weight:800;letter-spacing:.015em;line-height:1.5;

}

.bayar-page .angsuran-nominal {

    margin:10px 0 2px;color:#261b24;

    font-size:clamp(19px,1.7vw,24px);

    font-weight:800;letter-spacing:-.025em;

    line-height:1.25;overflow-wrap:anywhere;

}

.bayar-page .angsuran-meta {

    display:grid;grid-template-columns:repeat(2,minmax(0,1fr));

    gap:12px 11px;margin:13px 0 0;padding:13px 0 0;

    border-top:1px solid var(--line);

}

.bayar-page .meta-label {

    display:block;color:#8b7f87;font-size:10.5px;

    font-weight:650;line-height:1.4;

}

.bayar-page .meta-nilai {

    display:block;margin:4px 0 0;color:#312831;

    font-size:12px;font-weight:700;line-height:1.45;

    overflow-wrap:anywhere;

}

.bayar-page .badge {

    display:inline-flex;align-items:center;justify-content:center;

    gap:4px;max-width:100%;padding:4px 9px;

    border-radius:99px;font-size:10.3px;

    line-height:1.4;font-weight:750;

    white-space:normal;text-align:center;

    background:#f2f2f4;color:#606673;

}

.bayar-page .badge.lunas,.bayar-page .badge.verifikasi-diterima {

    background:#e4f6eb;color:#18784d;

}

.bayar-page .badge.menunggu,.bayar-page .badge.verifikasi-menunggu {

    background:#e7f1fd;color:#275f9b;

}

.bayar-page .badge.rektorat {background:#f1ebfa;color:#7051a0;}

.bayar-page .badge.ditolak,.bayar-page .badge.terlambat,

.bayar-page .badge.verifikasi-ditolak {

    background:#feebed;color:#ae253a;

}

.bayar-page .badge.belum,.bayar-page .badge.belum_dibuka {background:#fff2d5;color:#946117;}

.bayar-page .badge.dibuka {background:#e4f6eb;color:#18784d;}

.bayar-page .badge.terjadwal {background:#e7f1fd;color:#275f9b;}

.bayar-page .badge.ditutup,.bayar-page .badge.nonaktif {background:#f2f2f4;color:#606673;}

.bayar-page .badge.verifikasi-kosong {background:#f2f3f5;color:#69717c;}

.bayar-page .catatan-verifikasi {

    margin-top:12px;padding:10px 12px;

    border:1px solid #f1d5db;border-left:3px solid #c25b6d;

    border-radius:8px;background:#fff6f8;

    color:#8a283e;font-size:11.5px;line-height:1.7;

    overflow-wrap:anywhere;

}

.bayar-page .bukti-link {

    display:inline-flex;align-items:center;gap:6px;

    width:fit-content;max-width:100%;margin:10px 0 0;

    color:var(--maroon);font-size:11.7px;font-weight:750;

    line-height:1.5;overflow-wrap:anywhere;

}

.bayar-page .bukti-link:hover {text-decoration:underline;}

.bayar-page .bukti-link svg {flex:none;}

.bayar-page .aksi-baris {

    display:flex;flex-wrap:wrap;gap:8px;margin-top:15px;

}

.bayar-page .btn-aksi {

    display:inline-flex;align-items:center;justify-content:center;

    gap:6px;min-height:39px;padding:9px 13px;

    border:1px solid var(--maroon);border-radius:9px;

    background:var(--maroon);color:#fff;font-size:11.5px;

    font-weight:750;line-height:1.4;cursor:pointer;

    transition:background .16s,border-color .16s;

}

.bayar-page .btn-aksi:hover {background:var(--maroon-dark);border-color:var(--maroon-dark);}

.bayar-page .btn-aksi--garis {background:#fff;color:var(--maroon);}

.bayar-page .btn-aksi--garis:hover {background:#fff3f6;border-color:#bb7d8d;}

.bayar-page .form-sembunyi {

    display:none;margin:13px 0 0;padding:15px 0 0;

    border-top:1px solid var(--line);

}

/* Form aslinya tetap berada dalam DOM, tetapi hanya ditampilkan di modal. */
.bayar-page .form-sembunyi.terbuka {display:block;}

/* Modal di luar container .bayar-page agar tidak terpotong sidebar/layout. */
.bayar-modal[hidden] {display:none !important;}
.bayar-modal {
    position:fixed;inset:0;z-index:99999;
    display:grid;place-items:center;padding:20px;
    font-family:'Plus Jakarta Sans',Poppins,Arial,sans-serif;
}
.bayar-modal *, .bayar-modal *::before, .bayar-modal *::after {box-sizing:border-box;}
.bayar-modal__backdrop {position:absolute;inset:0;background:rgba(26,18,26,.62);}
.bayar-modal__dialog {
    position:relative;z-index:1;width:min(100%,520px);max-height:min(90vh,760px);
    overflow-y:auto;overscroll-behavior:contain;
    background:#fff;border:1px solid #eadce1;border-radius:18px;
    box-shadow:0 24px 70px rgba(37,18,28,.25);
}
.bayar-modal__header {
    display:flex;align-items:flex-start;justify-content:space-between;gap:14px;
    padding:21px 23px 17px;border-bottom:1px solid #f0e5e9;
}
.bayar-modal__eyebrow {margin:0 0 5px;color:#800b22;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;}
.bayar-modal__title {margin:0;color:#251e26;font-size:20px;font-weight:800;line-height:1.35;}
.bayar-modal__desc {margin:6px 0 0;color:#736771;font-size:12.5px;line-height:1.6;}
.bayar-modal__close {
    display:grid;place-items:center;flex:none;width:34px;height:34px;
    border:1px solid #eadce1;border-radius:9px;background:#fff;color:#51424b;
    font-size:23px;line-height:1;cursor:pointer;
}
.bayar-modal__close:hover {background:#fff3f6;color:#800b22;}
.bayar-modal__body {padding:21px 23px 23px;}
.bayar-modal__body .form-sembunyi {display:block !important;margin:0;padding:0;border:0;}
.bayar-modal__body .form-field {margin:0 0 18px;}
.bayar-modal__body .form-field label {display:block;margin:0 0 8px;color:#3e313b;font-size:12px;font-weight:750;}
.bayar-modal__body .form-field input,
.bayar-modal__body .form-field textarea {
    display:block;width:100%;min-width:0;min-height:44px;
    padding:11px 12px;border:1px solid #e4d5da;border-radius:10px;
    background:#fff;color:#251e26;font-family:inherit;font-size:12.5px;line-height:1.55;
}
.bayar-modal__body .form-field textarea {min-height:112px;resize:vertical;}
.bayar-modal__body .form-field input:focus,
.bayar-modal__body .form-field textarea:focus {outline:none;border-color:#b46c81;box-shadow:0 0 0 3px rgba(128,11,34,.08);}
.bayar-modal__body .form-field input[type=file] {
    padding:13px;border:1px dashed #cda7b2;background:#fff9fb;
    cursor:pointer;font-size:12px;
}
.bayar-modal__body .form-field input[type=file]::file-selector-button {
    border:1px solid #e1c6cf;border-radius:7px;padding:7px 11px;
    margin-right:12px;background:#fff;color:#800b22;font-weight:750;cursor:pointer;
}
.bayar-modal__body .hint {margin:8px 0 0;color:#81747d;font-size:11.5px;line-height:1.6;}
.bayar-modal__body .bayar-bukti-notice {
    margin-bottom:17px;padding:12px 13px;border-radius:10px;
    background:#fff8eb;border:1px solid #edd9ad;
    color:#6f5326;font-size:12px;line-height:1.65;
}
.bayar-modal__body .bayar-bukti-notice strong {display:block;margin-bottom:2px;}
.bayar-modal__actions {
    display:flex;align-items:center;justify-content:flex-end;gap:9px;flex-wrap:wrap;
    margin-top:21px;padding-top:17px;border-top:1px solid #f0e5e9;
}
.bayar-modal__actions button {
    display:inline-flex;align-items:center;justify-content:center;min-height:41px;
    padding:10px 15px;border:1px solid #800b22;border-radius:9px;
    font-family:inherit;font-weight:750;font-size:12px;line-height:1.4;cursor:pointer;
}
.bayar-modal__cancel {background:#fff !important;color:#800b22 !important;border-color:#d7bcc5 !important;}
.bayar-modal__cancel:hover {background:#fff3f6 !important;}
.bayar-modal__actions .btn-aksi {background:#800b22;color:#fff;}
.bayar-modal__actions .btn-aksi:hover {background:#610719;}
.bayar-modal__actions .btn-aksi:disabled {opacity:.7;cursor:wait;}
.bayar-modal__errors {
    margin:0 0 16px;padding:11px 13px;background:#fff1f2;
    border:1px solid #fac4cc;border-radius:9px;color:#9d1732;font-size:12px;line-height:1.6;
}
.bayar-modal__errors ul {margin:5px 0 0;padding-left:18px;}
@media (max-width:540px) {
    .bayar-modal {padding:12px;align-items:center;}
    .bayar-modal__dialog {max-height:calc(100dvh - 24px);border-radius:14px;}
    .bayar-modal__header {padding:17px 17px 14px;}
    .bayar-modal__body {padding:17px;}
    .bayar-modal__title {font-size:18px;}
    .bayar-modal__actions button {flex:1 1 auto;}
}

.bayar-page .form-field {margin:0 0 12px;}

.bayar-page .form-field label {

    display:block;margin:0 0 6px;color:#554852;

    font-size:11.5px;font-weight:750;

}

.bayar-page .form-field input,.bayar-page .form-field textarea {

    display:block;width:100%;min-width:0;min-height:41px;

    padding:9px 11px;border:1px solid #e3d5da;

    border-radius:9px;background:#fff;color:#2c252c;

    font-size:12px;line-height:1.5;

}

.bayar-page .form-field input[type="file"] {height:auto;max-width:100%;font-size:11px;}

.bayar-page .form-field textarea {min-height:85px;resize:vertical;}

.bayar-page .form-field input:focus,.bayar-page .form-field textarea:focus {

    outline:none;border-color:#c38d9e;box-shadow:0 0 0 3px rgba(128,11,34,.075);

}

.bayar-page .hint {margin:6px 0 0;color:#847981;font-size:11px;line-height:1.6;}

/* Riwayat dan panduan */

.bayar-page .riwayat-grid {

    display:grid;grid-template-columns:repeat(2,minmax(0,1fr));

    gap:14px;align-items:start;

}

.bayar-page .panel {

    min-width:0;padding:17px 19px;

    border:1px solid var(--border);

    border-radius:13px;background:#fff;

    box-shadow:0 2px 10px rgba(50,16,30,.02);

}

.bayar-page .panel h2 {

    margin:0 0 13px;padding:0 0 11px;

    border-bottom:1px solid var(--line);

    color:#2b222a;font-size:14px;font-weight:800;

}

.bayar-page .riwayat-item {

    min-width:0;padding:12px 0;

    border-top:1px solid var(--line);

}

.bayar-page .riwayat-item:first-of-type {padding-top:0;border-top:none;}

.bayar-page .riwayat-atas {

    display:flex;justify-content:space-between;align-items:flex-start;

    flex-wrap:wrap;gap:7px;

}

.bayar-page .riwayat-nama {

    color:#342832;font-size:12px;font-weight:750;

    line-height:1.5;overflow-wrap:anywhere;

}

.bayar-page .riwayat-sub {

    margin-top:5px;color:var(--muted);

    font-size:11.5px;line-height:1.65;overflow-wrap:anywhere;

}

.bayar-page .kosong {

    padding:20px 10px;color:#8b7e87;

    font-size:12px;line-height:1.65;text-align:center;

}

.bayar-page .langkah {

    margin:0;padding:0 0 0 20px;

    color:#62565f;font-size:12px;line-height:1.75;

}

.bayar-page .langkah li + li {margin-top:7px;}

.bayar-page .langkah li::marker {color:var(--maroon);font-weight:800;}

.bayar-page .mati {

    display:flex;flex-direction:column;align-items:center;

    justify-content:center;text-align:center;

    min-height:190px;padding:28px 20px;

    border:1px solid var(--border);border-radius:13px;

    background:#fff;

}

.bayar-page .mati::before {

    content:"";display:block;width:40px;height:40px;

    margin:0 0 12px;border-radius:12px;

    background:#fff0f4;border:1px solid #f0dce2;

}

.bayar-page .mati h2 {margin:0 0 6px;color:#3a2931;font-size:16px;font-weight:800;}

.bayar-page .mati p {

    margin:3px auto;max-width:650px;

    color:var(--muted);font-size:12px;line-height:1.7;

}

/* Mengikuti lebar area konten, bukan hanya ukuran viewport; aman untuk sidebar */

@container (max-width:1180px) {

    .bayar-page .ringkasan-grid {grid-template-columns:repeat(2,minmax(0,1fr));}

    .bayar-page .angsuran-grid {grid-template-columns:repeat(2,minmax(0,1fr));}

}

@container (max-width:860px) {

    .bayar-page .riwayat-grid {grid-template-columns:minmax(0,1fr);}

    .bayar-page .angsuran-grid {grid-template-columns:minmax(0,1fr);}

}

@container (max-width:520px) {

    .bayar-page .page-head {gap:10px;padding-bottom:15px;margin-bottom:14px;}

    .bayar-page .page-head h1 {font-size:23px;}

    .bayar-page .periode-chip {width:fit-content;}

    .bayar-page .ringkasan-grid {gap:9px;}

    .bayar-page .stat-card {padding:12px;}

    .bayar-page .stat-card .stat-nilai {font-size:17px;}

    .bayar-page .stat-card .stat-sub {font-size:10.5px;}

    .bayar-page .rekening-card {padding:15px;}

    .bayar-page .rekening-nomor {font-size:22px;}

    .bayar-page .btn-salin {width:100%;}

    .bayar-page .angsuran-card {padding:14px;}

    .bayar-page .panel {padding:15px;}

}

@container (max-width:340px) {

    .bayar-page .ringkasan-grid {grid-template-columns:minmax(0,1fr);}

    .bayar-page .angsuran-meta {gap:9px;}

    .bayar-page .aksi-baris .btn-aksi {flex:1 1 100%;}

}

@media (prefers-reduced-motion:reduce) {

    .bayar-page .btn-aksi,.bayar-page .btn-salin {transition:none;}

}

</style>

@php

    $pengaturan = $ringkasan['pengaturan'];

    $dibuka = (bool) $ringkasan['dibuka'];

    $rekening = $ringkasan['rekening'];

    $daftarAngsuran = $ringkasan['daftar_angsuran'];

    $lunasRiwayat = collect($daftarAngsuran)->filter(fn (array $angsuran): bool => in_array($angsuran['status'], ['lunas', 'paid', 'terkonfirmasi'], true));

@endphp

<div class="bayar-page">

    <nav class="bayar-breadcrumb" aria-label="Navigasi halaman">

        <a href="{{ route('dashboard') }}">Dashboard</a>

        <span aria-hidden="true">/</span>

        <span aria-current="page">Pembayaran</span>

    </nav>

    <div class="page-head">

        <div>

            <h1>Pembayaran Mahasiswa</h1>

            <p>Lihat tagihan, jadwal angsuran, bukti transfer, dan status pembayaran ke rekening resmi Ma'had.</p>

        </div>

        @if ($pengaturan)

            <span class="periode-chip">

                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>

                    <line x1="16" y1="2" x2="16" y2="6"></line>

                    <line x1="8" y1="2" x2="8" y2="6"></line>

                    <line x1="3" y1="10" x2="21" y2="10"></line>

                </svg>

                Periode {{ $pengaturan->periode }}

            </span>

        @endif

    </div>

    <div class="info-note">

        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>

        </svg>

        <span>

            Transfer langsung ke rekening resmi kampus. SIMAHAD tidak menerima atau mengelola dana.

            Status <strong>Lunas</strong> ditetapkan setelah pemeriksaan bukti dan konfirmasi penerimaan dana, bukan otomatis setelah bukti diunggah.

        </span>

    </div>

    @if ($errors->any() && !old('_bayar_modal_id'))
        <div role="alert" style="margin:0 0 15px;padding:12px 15px;border:1px solid #f0bdc8;border-radius:10px;background:#fff3f5;color:#8f1935;font-size:12px;">
            <strong>Form belum berhasil dikirim:</strong>
            <ul style="margin:6px 0 0;padding-left:18px;">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! $dibuka)

        <div class="info-note">

            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                <circle cx="12" cy="12" r="10"></circle>

                <line x1="12" y1="16" x2="12" y2="12"></line>

                <line x1="12" y1="8" x2="12.01" y2="8"></line>

            </svg>

            <span>

                @if ($pengaturan)

                    Periode <strong>{{ $pengaturan->periode }}</strong> belum dibuka. Seluruh tahap dan rentang waktunya tetap dapat dilihat; unggah bukti tersedia setelah Musyrifah membuka periode dan tahap terkait.

                @else

                    Belum ada periode pembayaran aktif. Silakan menunggu Musyrifah membuka periode pembayaran.

                @endif

            </span>

        </div>

    @endif

        <div class="ringkasan-grid">

            <div class="stat-card stat-card--utama">

                <span class="stat-label">Total Tagihan</span>

                <span class="stat-nilai">{{ $ringkasan['total_rupiah'] }}</span>

                <span class="stat-sub">{{ $daftarAngsuran->count() }} angsuran &middot; kategori {{ ucfirst($ringkasan['kategori']) }}</span>

            </div>

            <div class="stat-card">

                <span class="stat-label">Sudah Dibayar</span>

                <span class="stat-nilai">{{ $ringkasan['dibayar_rupiah'] }}</span>

                <span class="stat-sub">{{ $ringkasan['progres'] }}% dari total tagihan</span>

               <progress class="progres-bar" value="{{ $ringkasan['progres'] }}" max="100"></progress>

            </div>

            <div class="stat-card">

                <span class="stat-label">Sisa Tagihan</span>

                <span class="stat-nilai">{{ $ringkasan['sisa_rupiah'] }}</span>

                <span class="stat-sub">

                    Belum perlu transfer: <strong>{{ $ringkasan['belum_dibayar_rupiah'] }}</strong>

                    @if ($ringkasan['menunggu'] > 0)

                        &middot; {{ $ringkasan['menunggu_rupiah'] }} menunggu pemeriksaan/konfirmasi

                    @endif

                </span>

            </div>

            <div class="stat-card">

                <span class="stat-label">Status Keseluruhan</span>

                <span class="stat-nilai stat-status-value">

                    {{ $ringkasan['lunas'] ? 'Lunas' : ($ringkasan['total'] > 0 ? 'Belum Lunas' : 'Belum Ada Periode') }}

                </span>

                <span class="stat-sub">

                    @if ($ringkasan['jatuh_tempo_terdekat'])

                        Jatuh tempo terdekat: {{ $ringkasan['jatuh_tempo_terdekat']['jatuh_tempo'] }}

                    @else

                        Semua angsuran sudah dikonfirmasi

                    @endif

                </span>

            </div>

        </div>

        <div class="rekening-card">

            <div class="rekening-info">

                <span class="rekening-bank">{{ $rekening['bank'] }} &middot; Rekening Resmi</span>

                <div class="rekening-nomor" id="nomorRekening">{{ $rekening['nomor'] }}</div>

                <div class="rekening-nama">Atas nama: <strong>{{ $rekening['nama'] }}</strong></div>

            </div>

            <button type="button" class="btn-salin" id="btnSalinRekening" data-nomor="{{ $rekening['nomor'] }}">

                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                    <rect x="9" y="9" width="13" height="13" rx="2"></rect>

                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>

                </svg>

                <span id="labelSalinRekening">Salin Nomor Rekening</span>

            </button>

        </div>

        <div class="bagian-judul">

            <h2>Rincian Angsuran</h2>

            <span>Lunasi 1, 2, atau 3 kali &middot; {{ $ringkasan['tahap_dibuka'] }} tahap dibuka</span>

        </div>

        <div class="angsuran-grid">

            @foreach ($daftarAngsuran as $angsuran)

                @php

                    $kelasKartu = $angsuran['status'] === 'lunas' ? 'is-lunas' : ($angsuran['terlambat'] ? 'is-terlambat' : '');

                @endphp

                <article class="angsuran-card {{ $kelasKartu }}">

                    <div class="angsuran-head">

                        <span class="angsuran-tahap">{{ $angsuran['label'] }}</span>

                        <span class="badge {{ $angsuran['status_periode'] }}">{{ $angsuran['status_periode_label'] }}</span>

                        <span class="badge {{ $angsuran['kelas_status'] }}">{{ str_contains(mb_strtolower((string) $angsuran['status_label']), 'rektorat') ? 'Menunggu Konfirmasi Dana' : $angsuran['status_label'] }}</span>

                    </div>

                    <div class="angsuran-nominal">{{ $angsuran['nominal_rupiah'] }}</div>

                    <div class="angsuran-meta">

                        <div style="grid-column:1 / -1;">

                            <span class="meta-label">Rentang Periode</span>

                            <span class="meta-nilai">{{ $angsuran['tanggal_mulai'] }} &ndash; {{ $angsuran['tanggal_berakhir'] }}</span>

                        </div>

                        <div>

                            <span class="meta-label">Jatuh Tempo</span>

                            <span class="meta-nilai">{{ $angsuran['jatuh_tempo'] }}</span>

                        </div>

                        <div>

                            <span class="meta-label">Sisa Hari</span>

                            <span class="meta-nilai">

                                @if ($angsuran['sisa_hari'] === null)

                                    -

                                @elseif ($angsuran['sisa_hari'] < 0)

                                    Lewat {{ abs($angsuran['sisa_hari']) }} hari

                                @elseif ($angsuran['sisa_hari'] === 0)

                                    Jatuh tempo hari ini

                                @else

                                    {{ $angsuran['sisa_hari'] }} hari lagi

                                @endif

                            </span>

                        </div>

                        <div>

                            <span class="meta-label">Pemeriksaan Bukti</span>

                            <span class="meta-nilai">

                                <span class="badge verifikasi-{{ $angsuran['kelas_verifikasi'] }}">{{ $angsuran['verifikasi_label'] }}</span>

                            </span>

                        </div>

                        <div>

                            <span class="meta-label">Konfirmasi Dana</span>

                            <span class="meta-nilai">{{ $angsuran['tanggal_konfirmasi'] ?? '-' }}</span>

                        </div>

                    </div>

                    @if (filled($angsuran['catatan_verifikasi']))

                        <div class="catatan-verifikasi">Catatan Musyrifah: {{ $angsuran['catatan_verifikasi'] }}</div>

                    @endif

                    @if ($angsuran['bukti_url'])

                        <a class="bukti-link" href="{{ $angsuran['bukti_url'] }}" target="_blank" rel="noopener">

                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>

                            </svg>

                            Lihat bukti transfer{{ $angsuran['tanggal_upload'] ? ' ('.$angsuran['tanggal_upload'].')' : '' }}

                        </a>

                    @endif

                    @if (filled($angsuran['bukti_grup']))

                        <div class="riwayat-sub">Bukti transfer bersama untuk satu atau beberapa tagihan dalam periode ini.</div>

                    @endif

                    @if ($angsuran['pengajuan_menunggu'])

                        <div class="catatan-verifikasi" style="border-left-color:#93c5fd;background:#eff6ff;color:#1d4ed8;">

                            Pengajuan keterlambatan sedang menunggu pemeriksaan Musyrifah.

                        </div>

                    @endif

                    @if ($angsuran['dapat_unggah'] || $angsuran['dapat_ajukan'])

                        <div class="aksi-baris">

                            @if ($angsuran['dapat_unggah'])

                                <button type="button" class="btn-aksi" aria-haspopup="dialog" data-toggle-form="form-bukti-{{ $angsuran['id'] }}">

                                    @if ($angsuran['bukti_ditolak'])
                                        Unggah Ulang
                                    @elseif ($angsuran['bukti_sudah_diunggah'])
                                        Ganti Bukti
                                    @else
                                        Unggah Bukti
                                    @endif

                                </button>

                            @endif

                            @if ($angsuran['dapat_ajukan'] && ! $angsuran['pengajuan_menunggu'])

                                <button type="button" class="btn-aksi btn-aksi--garis" aria-haspopup="dialog" data-toggle-form="form-tunda-{{ $angsuran['id'] }}">

                                    Ajukan Keterlambatan

                                </button>

                            @endif

                        </div>

                        @if ($angsuran['dapat_unggah'])

                            <form class="form-sembunyi"

                                id="form-bukti-{{ $angsuran['id'] }}"

                                action="{{ route('pembayaran.bukti', $angsuran['id']) }}"

                                method="POST"

                                enctype="multipart/form-data">

                                @csrf
                                <input type="hidden" name="_bayar_modal_id" value="form-bukti-{{ $angsuran['id'] }}">

                                @if ($angsuran['bukti_sudah_diunggah'])
                                    <div class="bayar-bukti-notice">
                                        <strong>Bukti sebelumnya sudah tersimpan.</strong>
                                        Pilih file pengganti yang benar. Bukti terbaru akan diperiksa
                                        oleh Musyrifah; bukti lama akan digantikan setelah berhasil disimpan.
                                    </div>
                                @endif

                                <div class="form-field">

                                    <label for="bukti-{{ $angsuran['id'] }}">{{ $angsuran['bukti_sudah_diunggah'] ? 'Bukti transfer pengganti' : 'Bukti transfer' }} {{ $angsuran['label'] }}</label>

                                    <input type="file" id="bukti-{{ $angsuran['id'] }}" name="bukti" accept=".pdf,.jpg,.jpeg,.png" required>

                                    <div class="hint">PDF/JPG/PNG maksimal 5 MB. Pastikan nominal {{ $angsuran['nominal_rupiah'] }} dan tujuan rekening terlihat jelas.</div>

                                </div>

                                @if ($ringkasan['belum_dibayar'] > $angsuran['nominal'])

                                    <div class="bayar-bukti-notice">

                                        <label style="display:flex;align-items:flex-start;gap:9px;cursor:pointer;">

                                            <input type="checkbox" name="bayar_semua" value="1" style="width:17px;height:17px;margin-top:2px;flex:none;">

                                            <span>

                                                <strong>Lunasi seluruh sisa tagihan sekaligus</strong>

                                                Satu bukti untuk semua tagihan yang belum ditransfer, total {{ $ringkasan['belum_dibayar_rupiah'] }}. Tahap yang belum dibuka juga dicakup.

                                            </span>

                                        </label>

                                    </div>

                                @endif

                                <div class="bayar-modal__actions">
                                    <button type="button" class="bayar-modal__cancel" data-close-bayar-modal>Batal</button>
                                    <button type="submit" class="btn-aksi">{{ $angsuran['bukti_sudah_diunggah'] ? 'Simpan Bukti Baru' : 'Kirim Bukti' }}</button>
                                </div>

                            </form>

                        @endif

                        @if ($angsuran['dapat_ajukan'] && ! $angsuran['pengajuan_menunggu'])

                            <form class="form-sembunyi"

                                id="form-tunda-{{ $angsuran['id'] }}"

                                action="{{ route('pembayaran.keterlambatan', $angsuran['id']) }}"

                                method="POST">

                                @csrf
                                <input type="hidden" name="_bayar_modal_id" value="form-tunda-{{ $angsuran['id'] }}">

                                <div class="form-field">

                                    <label for="alasan-{{ $angsuran['id'] }}">Alasan keterlambatan</label>

                                    <textarea id="alasan-{{ $angsuran['id'] }}" name="alasan" minlength="10" maxlength="1000" required placeholder="Contoh: dana kiriman orang tua baru masuk awal bulan depan.">{{ old('alasan') }}</textarea>

                                </div>

                                <div class="form-field">

                                    <label for="rencana-{{ $angsuran['id'] }}">Tanggal rencana pembayaran</label>

                                    <input type="date" id="rencana-{{ $angsuran['id'] }}" name="tanggal_rencana_bayar" value="{{ old('tanggal_rencana_bayar') }}" min="{{ now()->addDay()->toDateString() }}" max="{{ now()->addMonths(6)->toDateString() }}" required>

                                    <div class="hint">Surat pengajuan PDF dibuat otomatis dan diperiksa Musyrifah. Persetujuan hanya memperbarui jadwal, status tetap belum Lunas.</div>

                                </div>

                                <div class="bayar-modal__actions">
                                    <button type="button" class="bayar-modal__cancel" data-close-bayar-modal>Batal</button>
                                    <button type="submit" class="btn-aksi">Kirim Pengajuan</button>
                                </div>

                            </form>

                        @endif

                    @endif

                </article>

            @endforeach

        </div>

        <div class="riwayat-grid">

            <div class="panel">

                <h2>Riwayat Pembayaran</h2>

                @forelse ($lunasRiwayat as $riwayat)

                    <div class="riwayat-item">

                        <div class="riwayat-atas">

                            <span class="riwayat-nama">{{ $riwayat['label'] }} &middot; {{ $riwayat['nominal_rupiah'] }}</span>

                            <span class="badge lunas">Lunas</span>

                        </div>

                        <div class="riwayat-sub">

                            Dana dikonfirmasi: {{ $riwayat['tanggal_konfirmasi'] ?? $riwayat['tanggal_bayar'] ?? '-' }}

                        </div>

                    </div>

                @empty

                    <div class="kosong">Belum ada angsuran yang dikonfirmasi Lunas.</div>

                @endforelse

                <div class="riwayat-sub" style="margin-top:12px;">

                    Unggahan bukti transfer belum otomatis membuat angsuran lunas. Status diperbarui setelah dana terkonfirmasi.

                </div>

            </div>

            <div class="panel">

                <h2>Riwayat Pengajuan Keterlambatan</h2>

                @forelse ($ringkasan['pengajuan'] as $pengajuan)

                    <div class="riwayat-item">

                        <div class="riwayat-atas">

                            <span class="riwayat-nama">{{ $pengajuan->nomor_surat }}</span>

                            <span class="badge {{ $pengajuan->kelasStatus() === 'disetujui' ? 'lunas' : ($pengajuan->kelasStatus() === 'ditolak' ? 'ditolak' : 'menunggu') }}">

                                {{ $pengajuan->statusLabel() }}

                            </span>

                        </div>

                        <div class="riwayat-sub">

                            {{ $pengajuan->pembayaran?->labelTahap() ?? 'Tagihan' }} &middot;

                            rencana bayar {{ $pengajuan->tanggalRencanaTeks() }}

                        </div>

                        @if (filled($pengajuan->catatan_musyrifah))

                            <div class="riwayat-sub">Catatan Musyrifah: {{ $pengajuan->catatan_musyrifah }}</div>

                        @endif

                        <a class="bukti-link" href="{{ route('pembayaran.keterlambatan.surat', $pengajuan->id) }}" target="_blank" rel="noopener">

                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>

                                <polyline points="14 2 14 8 20 8"></polyline>

                            </svg>

                            Lihat Surat PDF

                        </a>

                    </div>

                @empty

                    <div class="kosong">Belum ada pengajuan keterlambatan.</div>

                @endforelse

            </div>

        </div>

        <div class="panel" style="margin-top:14px;">

            <h2>Cara Pembayaran</h2>

            <ol class="langkah">

                <li>Salin nomor rekening {{ $rekening['bank'] }} resmi kampus di atas.</li>

                <li>Transfer sesuai nominal angsuran melalui ATM, mobile banking, atau teller {{ $rekening['bank'] }}. Anda dapat melunasi sekali, dua kali, atau tiga kali sesuai tahap yang dibuka.</li>

                <li>Simpan bukti transfer, lalu unggah pada angsuran yang dibayar.</li>

                <li>Tunggu pemeriksaan bukti oleh Musyrifah dan konfirmasi penerimaan dana sesuai prosedur pembayaran.</li>

            </ol>

        </div>

    @if ($daftarAngsuran->isEmpty())

        <div class="mati" style="margin-bottom:14px;">

            <h2>Jadwal Angsuran Belum Tersedia</h2>

            <p>Seluruh tahap akan tampil di sini setelah Musyrifah membuat periode pembayaran.</p>

        </div>

    @endif

</div>

<!-- Hanya satu modal aktif untuk semua angsuran. Form aslinya dipindahkan
     sementara melalui JavaScript agar action, CSRF dan validasi tetap utuh. -->
<div class="bayar-modal" id="bayarActionModal" role="dialog" aria-modal="true"
     aria-labelledby="bayarModalTitle" aria-describedby="bayarModalDesc"
     data-error-form="{{ $errors->any() ? old('_bayar_modal_id', '') : '' }}" hidden>
    <div class="bayar-modal__backdrop" data-close-bayar-modal aria-hidden="true"></div>
    <div class="bayar-modal__dialog" tabindex="-1">
        <div class="bayar-modal__header">
            <div>
                <p class="bayar-modal__eyebrow">SIMAHAD · Pembayaran</p>
                <h2 class="bayar-modal__title" id="bayarModalTitle">Form Pembayaran</h2>
                <p class="bayar-modal__desc" id="bayarModalDesc"></p>
            </div>
            <button type="button" class="bayar-modal__close" data-close-bayar-modal aria-label="Tutup formulir">&times;</button>
        </div>
        <div class="bayar-modal__body" id="bayarModalBody">
            @if ($errors->any() && old('_bayar_modal_id'))
                <div class="bayar-modal__errors" role="alert">
                    <strong>Periksa kembali isian formulir:</strong>
                    <ul>
                        @foreach ($errors->all() as $pesan)
                            <li>{{ $pesan }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')

<script>

    (function () {

        const tombolSalin = document.getElementById('btnSalinRekening');

        if (tombolSalin) {

            const label = document.getElementById('labelSalinRekening');

            tombolSalin.addEventListener('click', async function () {

                const nomor = tombolSalin.dataset.nomor || '';

                if (!nomor) {

                    return;

                }

                try {

                    if (navigator.clipboard && window.isSecureContext) {

                        await navigator.clipboard.writeText(nomor);

                    } else {

                        const area = document.createElement('textarea');

                        area.value = nomor;

                        area.setAttribute('readonly', 'readonly');

                        area.style.position = 'fixed';

                        area.style.opacity = '0';

                        document.body.appendChild(area);

                        area.select();

                        document.execCommand('copy');

                        document.body.removeChild(area);

                    }

                    tombolSalin.classList.add('is-copied');

                    label.textContent = 'Nomor Tersalin';

                    window.setTimeout(function () {

                        tombolSalin.classList.remove('is-copied');

                        label.textContent = 'Salin Nomor Rekening';

                    }, 2000);

                } catch (error) {

                    label.textContent = 'Gagal menyalin, salin manual';

                }

            });

        }

        // Satu modal terpusat: tidak ada dua form yang dapat terbuka bersamaan.
        const modal = document.getElementById('bayarActionModal');
        const modalBody = document.getElementById('bayarModalBody');
        const modalTitle = document.getElementById('bayarModalTitle');
        const modalDesc = document.getElementById('bayarModalDesc');
        // Pastikan modal berada langsung di body, tidak tertutup stacking context sidebar.
        if (modal && modal.parentNode !== document.body) document.body.appendChild(modal);
        let formAktif = null;
        let posisiAwal = null;
        let pemicuAktif = null;
        let overflowSebelumnya = '';

        function tutupModal() {
            if (!modal || modal.hidden || !formAktif) return;
            // Pulangkan form ke kartu asal tanpa menghilangkan input atau CSRF.
            if (posisiAwal && posisiAwal.parent) {
                posisiAwal.parent.insertBefore(formAktif, posisiAwal.nextSibling);
            }
            modal.hidden = true;
            document.body.style.overflow = overflowSebelumnya;
            const tombolFokus = pemicuAktif;
            if (tombolFokus) tombolFokus.setAttribute('aria-expanded', 'false');
            formAktif = null;
            posisiAwal = null;
            pemicuAktif = null;
            if (tombolFokus && tombolFokus.isConnected) tombolFokus.focus();
        }

        function bukaModal(tombol) {
            if (!modal || !modalBody) return;
            const form = document.getElementById(tombol.dataset.toggleForm || '');
            if (!form) return;
            if (formAktif) tutupModal();

            const kartu = tombol.closest('.angsuran-card');
            const tahap = kartu?.querySelector('.angsuran-tahap')?.textContent?.trim() || 'Angsuran';
            const nominal = kartu?.querySelector('.angsuran-nominal')?.textContent?.trim() || '';
            const unggah = form.id.startsWith('form-bukti-');
            const labelUnggah = (tombol.textContent || '').trim();
            modalTitle.textContent = unggah ? labelUnggah + ' Pembayaran' : 'Ajukan Keterlambatan';
            modalDesc.textContent = [tahap, nominal].filter(Boolean).join(' · ');

            posisiAwal = { parent: form.parentNode, nextSibling: form.nextSibling };
            formAktif = form;
            pemicuAktif = tombol;
            tombol.setAttribute('aria-expanded', 'true');
            modalBody.appendChild(form);
            overflowSebelumnya = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            modal.hidden = false;
            const fokus = form.querySelector('input[type="file"], textarea, input[type="date"]');
            (fokus || modal.querySelector('.bayar-modal__close'))?.focus({ preventScroll: true });
        }

        document.querySelectorAll('[data-toggle-form]').forEach(function (tombol) {
            tombol.addEventListener('click', function () { bukaModal(tombol); });
        });
        // Event delegation: tombol Batal berada di dalam form yang baru masuk modal saat diklik.
        // Dengan delegation, tombol Batal, ikon X, dan backdrop semuanya berfungsi.
        modal?.addEventListener('click', function (event) {
            if (event.target.closest('[data-close-bayar-modal]')) {
                tutupModal();
            }
        });
        document.addEventListener('keydown', function (e) {
            if (!modal || modal.hidden) return;
            if (e.key === 'Escape') { e.preventDefault(); tutupModal(); return; }
            if (e.key === 'Tab') {
                // Jaga fokus keyboard agar tidak berpindah ke halaman di balik modal.
                const daftar = Array.from(modal.querySelectorAll('button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), a[href]'));
                const terlihat = daftar.filter(el => el.getClientRects().length > 0);
                if (!terlihat.length) return;
                const pertama = terlihat[0], terakhir = terlihat[terlihat.length - 1];
                if (e.shiftKey && document.activeElement === pertama) { e.preventDefault(); terakhir.focus(); }
                else if (!e.shiftKey && document.activeElement === terakhir) { e.preventDefault(); pertama.focus(); }
            }
        });

        // Data validasi dikirim dari atribut HTML, supaya script tetap JavaScript murni.
        // Tidak memakai sintaks Blade di dalam JavaScript supaya tetap kompatibel.
        const idFormError = modal?.dataset.errorForm || '';
        if (idFormError) {
            const tombolError = Array.from(document.querySelectorAll('[data-toggle-form]'))
                .find(el => el.dataset.toggleForm === idFormError);
            if (tombolError) bukaModal(tombolError);
        }

    })();

</script>

@endpush

@endsection
