@extends('layouts.mahasiswa')
@section('title', 'Informasi Kamar')
@push('styles')
<style>
/* SIMAHAD · Informasi Kamar Mahasiswa
   Perubahan presentasi responsif; route, otorisasi, data & pengajuan tetap asli. */
.mhs-kamar-page {
    --maroon: #800b22;
    --maroon-dark: #61071a;
    --soft-pink: #fff5f7;
    --border: #ebdce0;
    --line: #f0e6e9;
    --ink: #251e25;
    --muted: #776c76;
    --radius: 14px;
    display: block !important;
    clear: both;
    position: relative;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    margin: 0 !important;
    padding: 0 0 30px !important;
    font: 400 13.5px/1.6 'Plus Jakarta Sans', Poppins, Arial, sans-serif;
    color: var(--ink);
    container-type: inline-size;
}
.mhs-kamar-page *, .mhs-kamar-page *::before,
.mhs-kamar-page *::after { box-sizing: border-box; }
.mhs-kamar-page > *, .mhs-kamar-page section, .mhs-kamar-page div,
.mhs-kamar-page form { min-width: 0; }
.mhs-kamar-page button, .mhs-kamar-page input,
.mhs-kamar-page select, .mhs-kamar-page textarea { font-family: inherit; }
.mhs-kamar-page a:focus-visible, .mhs-kamar-page button:focus-visible,
.mhs-kamar-page input:focus-visible, .mhs-kamar-page select:focus-visible,
.mhs-kamar-page textarea:focus-visible { outline: 3px solid rgba(174,116,133,.38); outline-offset: 2px; }

/* Navigasi — hanya breadcrumb, tanpa tombol dashboard ganda */
.mhs-kamar-page .breadcrumb {
    display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
    margin: 0 0 11px; padding: 0; color: #93868d; font-size: 12px;
    line-height: 1.5; font-weight: 500;
}
.mhs-kamar-page .breadcrumb a { color: var(--maroon); font-weight: 750; text-decoration: none; }
.mhs-kamar-page .breadcrumb a:hover { text-decoration: underline; }
.mhs-kamar-page .mhs-kamar-header {
    display: flex; align-items: center; width: 100%; padding: 0 0 17px;
    margin: 0 0 20px; border-bottom: 1px solid #e8d7dc;
}
.mhs-kamar-page .mhs-kamar-header > div { min-width: 0; }
.mhs-kamar-page .page-title {
    margin: 0; color: #251820; font-size: clamp(23px,2.15vw,28px);
    line-height: 1.25; font-weight: 800; letter-spacing: -.035em;
}
.mhs-kamar-page .page-subtitle {
    margin: 6px 0 0; max-width: 820px; color: #7a7078;
    font-size: 13px; line-height: 1.65;
}

/* Kartu ringkasan mengikuti tinggi asli kontennya (tanpa ruang kosong). */
.mhs-kamar-page .mhs-kamar-overview {
    display: grid; grid-template-columns: minmax(0,1.07fr) minmax(0,.93fr);
    align-items: start; gap: 16px; margin: 0 0 16px;
}
.mhs-kamar-page .mhs-kamar-overview > * { margin-bottom: 0 !important; align-self: start; }
.mhs-kamar-page .room-card,
.mhs-kamar-page .detail-card,
.mhs-kamar-page .additional-card {
    float: none; width: 100%; max-width: 100%; min-width: 0;
    margin: 0 0 16px; padding: 0; background: #fff;
    border: 1px solid var(--border); border-radius: var(--radius);
    overflow: hidden; box-shadow: 0 2px 14px rgba(57,21,33,.027);
}
.mhs-kamar-page .room-heading {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px 12px; margin: 0;
    padding: 14px 18px; border-bottom: 1px solid var(--line);
}
.mhs-kamar-page .room-heading h2,
.mhs-kamar-page .detail-card h2 {
    margin: 0; color: #28212a; font-size: 15px; font-weight: 800;
    letter-spacing: -.015em; line-height: 1.4;
}
.mhs-kamar-page .detail-card h2 {
    padding: 14px 18px; border-bottom: 1px solid var(--line);
}
.mhs-kamar-page .status, .mhs-kamar-page .badge {
    display: inline-flex; align-items: center; justify-content: center;
    max-width: 100%; padding: 5px 10px;
    border-radius: 999px; font-size: 11px; font-weight: 750;
    line-height: 1.35; text-align: center;
    background: #e8f7ef; color: #16714a;
}
.mhs-kamar-page .room-highlight {
    display: grid; grid-template-columns: 42px minmax(0,1fr);
    grid-template-rows: auto auto auto; gap: 0 13px; align-items: center;
    margin: 15px 18px 11px; padding: 13px 15px;
    background: linear-gradient(110deg,#fff3f6,#fffafb);
    border: 1px solid #f0dfe4; border-radius: 12px;
    color: var(--maroon);
}
.mhs-kamar-page .room-highlight svg {
    grid-column: 1; grid-row: 1/4; display: block;
    width: 42px; height: 42px; margin: 0;
    padding: 9px; border-radius: 11px; background: #f7e1e7;
    fill: none; stroke: currentColor; stroke-width: 1.8;
    stroke-linecap: round; stroke-linejoin: round;
}
.mhs-kamar-page .room-label { grid-column: 2; grid-row: 1; font-size: 11.5px; color: #855360; font-weight: 650; }
.mhs-kamar-page .room-number {
    grid-column: 2; grid-row: 2; margin: 1px 0 2px;
    color: var(--maroon); font-size: clamp(23px,2.2vw,29px);
    font-weight: 800; letter-spacing: -.03em; line-height: 1.2; overflow-wrap: anywhere;
}
.mhs-kamar-page .room-highlight .muted {
    grid-column: 2; grid-row: 3; font-size: 11.5px; line-height: 1.5;
}
.mhs-kamar-page .muted { color: var(--muted); font-size: 12.5px; line-height: 1.65; overflow-wrap: anywhere; }
.mhs-kamar-page .room-grid {
    display: grid; grid-template-columns: repeat(2,minmax(0,1fr));
    gap: 10px; margin: 0 18px 17px;
}
.mhs-kamar-page .room-info {
    padding: 11px 13px; border: 1px solid #ede3e6;
    border-radius: 10px; background: #fff;
}
.mhs-kamar-page .room-info svg {
    display: inline-block; width: 17px; height: 17px;
    margin: 0 0 5px; color: var(--maroon);
    fill: none; stroke: currentColor; stroke-width: 1.8;
    stroke-linecap: round; stroke-linejoin: round;
}
.mhs-kamar-page .info-label, .mhs-kamar-page .detail-label {
    color: #827681; font-size: 11px; font-weight: 650; line-height: 1.5;
}
.mhs-kamar-page .info-value {
    margin: 3px 0 0; color: #302730; font-size: 12.5px;
    line-height: 1.55; font-weight: 750; overflow-wrap: anywhere;
}
.mhs-kamar-page .detail-row {
    display: grid; grid-template-columns: minmax(118px,34%) minmax(0,1fr);
    gap: 10px 16px; align-items: center;
    margin: 0 18px; padding: 13px 0;
    border-bottom: 1px solid #f1e8eb;
}
.mhs-kamar-page .detail-row:last-child { border-bottom: 0; }
.mhs-kamar-page .detail-value {
    min-width: 0; color: #2b232b; font-size: 12.5px;
    font-weight: 700; line-height: 1.5; overflow-wrap: anywhere;
}

/* Keputusan pengajuan dibuat terbaca, bukan blok teks padat. */
.mhs-kamar-page .alert, .mhs-kamar-page .status-banner {
    width: 100%; min-width: 0; margin: 0 0 16px;
    padding: 15px 17px; background: #fffbf2; color: #755a2b;
    border: 1px solid #ecd8ac; border-left: 4px solid #cb9c40;
    border-radius: 12px; font-size: 12.5px; line-height: 1.75;
    overflow-wrap: anywhere;
}
.mhs-kamar-page .status-banner > strong:first-child {
    display: block; margin: 0 0 4px;
    font-size: 13.5px; color: #74511c; font-weight: 800;
}
.mhs-kamar-page .status-banner--rejected, .mhs-kamar-page .alert-error {
    border-color: #f1cbd3; border-left-color: #b72d43;
    background: #fff4f5; color: #963044;
}
.mhs-kamar-page .status-banner--rejected > strong:first-child { color: #981d36; }
.mhs-kamar-page .alert-success {
    border-color: #ccebd6; border-left-color: #258759;
    background: #eff9f3; color: #236242;
}
.mhs-kamar-page .catatan-penolakan, .mhs-kamar-page .persetujuan-detail {
    margin: 10px 0 0; padding: 10px 13px;
    border: 1px solid #ecd9db; border-radius: 8px;
    background: rgba(255,255,255,.72); font-size: 12px; line-height: 1.6;
}

/* Form pindah: pilihan di kiri, alasan di kanan, aksi pada baris bawah. */
.mhs-kamar-page .room-card > p.muted {
    margin: 14px 18px 0 !important; font-size: 12.5px;
    line-height: 1.72; max-width: 1060px;
}
.mhs-kamar-page .pindah-form {
    display: grid; grid-template-columns: minmax(0,.85fr) minmax(0,1.15fr);
    gap: 14px 16px; align-items: start;
    margin: 14px 18px 18px; padding: 16px 17px;
    background: #fdfafb; border: 1px solid #f0e5e8; border-radius: 11px;
}
.mhs-kamar-page .pindah-field { display: flex; flex-direction: column; min-width: 0; gap: 5px; }
.mhs-kamar-page .form-label {
    display: block; margin: 0; color: #4e414b; font-size: 12px; font-weight: 750;
}
.mhs-kamar-page .form-control {
    display: block; width: 100%; min-width: 0; min-height: 43px;
    padding: 10px 12px; border: 1px solid #e0d3d8;
    border-radius: 9px; background: #fff; color: #2d2730;
    font-size: 12.5px; line-height: 1.5;
    outline: none; box-shadow: none;
    transition: border-color .15s,box-shadow .15s;
}
.mhs-kamar-page .form-control:focus {
    border-color: #bc8798; box-shadow: 0 0 0 3px rgba(128,11,34,.085);
}
.mhs-kamar-page textarea.form-control { min-height: 102px; resize: vertical; }
.mhs-kamar-page .form-hint { margin: 1px 0 0; font-size: 11.5px; line-height: 1.55; color: #867a83; }
.mhs-kamar-page .pindah-actions {
    grid-column: 1/-1; display: flex; align-items: center; justify-content: flex-end;
    border-top: 1px solid #eee2e6; padding-top: 13px;
}
.mhs-kamar-page .btn-ajukan {
    display: inline-flex; align-items: center; justify-content: center;
    width: 100%; min-height: 41px; margin-top: auto;
    padding: 9px 15px; border: 1px solid var(--maroon);
    border-radius: 9px; background: var(--maroon); color: #fff;
    font-size: 12.5px; font-weight: 750; line-height: 1.4;
    text-align: center; cursor: pointer;
    transition: background .15s,border-color .15s;
}
.mhs-kamar-page .btn-ajukan:hover:not(:disabled) {
    background: var(--maroon-dark); border-color: var(--maroon-dark);
}
.mhs-kamar-page .btn-ajukan:disabled {
    border-color: #e7e2e4; background: #ece9eb; color: #948a91; cursor: not-allowed;
}
.mhs-kamar-page .pindah-actions .btn-ajukan { max-width: 260px; margin-top: 0; }

/* Katalog kamar */
.mhs-kamar-page .room-toolbar {
    display: grid; grid-template-columns: minmax(0,1.6fr) minmax(0,.8fr) minmax(0,.8fr);
    gap: 10px; margin: 15px 18px 16px;
}
.mhs-kamar-page .room-list {
    display: grid; grid-template-columns: repeat(2,minmax(0,1fr));
    align-items: start; gap: 15px; margin: 0 18px 18px;
}
.mhs-kamar-page .room-item {
    display: flex; flex-direction: column; gap: 11px; min-width: 0;
    padding: 15px; border: 1px solid #ebdfe3; border-radius: 12px;
    background: #fff; box-shadow: 0 2px 10px rgba(45,16,30,.025);
    transition: border-color .15s,box-shadow .15s;
}
.mhs-kamar-page .room-item:hover { border-color: #d6acb7; box-shadow: 0 4px 14px rgba(45,16,30,.055); }
.mhs-kamar-page .room-item.is-selected { border-color: var(--maroon); box-shadow: 0 0 0 2px rgba(128,11,34,.08); }
.mhs-kamar-page .room-item.is-current { border-color: #b87588; border-style: dashed; background: #fffbfc; }
.mhs-kamar-page .room-photo {
    display: block; width: 100%; height: 182px;
    border-radius: 9px; background: #f8f1f3; object-fit: cover;
}
.mhs-kamar-page .room-galeri { position: relative; width: 100%; overflow: hidden; border-radius: 9px; background: #f7f0f2; }
.mhs-kamar-page .room-galeri__foto { border-radius: 0; }
.mhs-kamar-page .room-galeri__foto:not(.is-active) { display: none; }
.mhs-kamar-page .room-photo--empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 7px; border: 1px dashed #dfd1d6; color: #96858c; text-align: center; font-size: 12px;
}
.mhs-kamar-page .room-photo--empty svg {
    width: 36px; height: 36px; fill: none; stroke: #c6aab4; stroke-width: 1.7;
    stroke-linecap: round; stroke-linejoin: round;
}
.mhs-kamar-page .room-galeri__nav {
    position: absolute; top: 50%; transform: translateY(-50%); z-index: 2;
    display: grid; place-items: center; width: 31px; height: 31px; padding: 0;
    border: 0; border-radius: 50%; background: rgba(35,17,27,.7); color: #fff;
    font-size: 19px; cursor: pointer;
}
.mhs-kamar-page .room-galeri__nav:hover { background: rgba(35,17,27,.9); }
.mhs-kamar-page .room-galeri__nav--prev { left: 8px; }
.mhs-kamar-page .room-galeri__nav--next { right: 8px; }
.mhs-kamar-page .room-galeri__counter {
    position: absolute; right: 9px; bottom: 9px; z-index: 2;
    padding: 3px 9px; border-radius: 999px; background: rgba(35,17,27,.72);
    color: #fff; font-size: 10.5px; font-weight: 750;
}
.mhs-kamar-page .room-galeri__dots {
    display: flex; gap: 6px; justify-content: center; margin: -5px 0 2px;
}
.mhs-kamar-page .room-galeri__dot {
    width: 8px; height: 8px; padding: 0; border: 0;
    border-radius: 50%; background: #dccbd1; cursor: pointer;
}
.mhs-kamar-page .room-galeri__dot.is-active { background: var(--maroon); }
.mhs-kamar-page .room-item-head {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: 9px 12px;
}
.mhs-kamar-page .room-item-title { margin: 0; color: #30232b; font-size: 15px; font-weight: 800; line-height: 1.45; }
.mhs-kamar-page .room-item-sub { margin: 2px 0 0; color: #81757d; font-size: 12px; }
.mhs-kamar-page .badge-available { background: #e8f8ef; color: #14754a; }
.mhs-kamar-page .badge-full { background: #fff0f2; color: #ac283c; }
.mhs-kamar-page .badge-pending { background: #fff4da; color: #91621b; }
.mhs-kamar-page .badge-closed { background: #f0f1f3; color: #5a6571; }
.mhs-kamar-page .badge-current { background: #f9e6eb; color: var(--maroon); }
.mhs-kamar-page .room-item-specs {
    display: grid; grid-template-columns: repeat(3,minmax(0,1fr));
    gap: 8px; padding: 11px 12px; border-radius: 9px; background: #fcf8f9;
}
.mhs-kamar-page .room-item-specs > div {
    min-width: 0; font-size: 12px; line-height: 1.55; color: #352a32;
}
.mhs-kamar-page .room-item-specs strong {
    display: block; margin-bottom: 1px;
    color: #877b82; font-size: 11px; font-weight: 650;
}
.mhs-kamar-page .room-item-fasilitas {
    margin: 0; font-size: 12px; line-height: 1.7; color: #796e75; overflow-wrap: anywhere;
}
.mhs-kamar-page .room-item-fasilitas strong { color: #4a4046; }
.mhs-kamar-page .room-item form { margin-top: auto !important; }
.mhs-kamar-page .room-item[hidden], .mhs-kamar-page .empty-state[hidden] { display: none !important; }
.mhs-kamar-page .empty-state {
    grid-column: 1/-1; min-width: 0; padding: 24px 15px;
    border: 1px dashed #e1d0d5; border-radius: 11px;
    background: #fffafc; text-align: center; color: #847982; font-size: 12.5px;
}
.mhs-kamar-page .additional-card {
    display: flex; align-items: flex-start; flex-wrap: wrap;
    gap: 8px 22px; padding: 15px 18px; margin: 0;
    border-left: 3px solid #bd9351;
}
.mhs-kamar-page .additional-heading {
    display: flex; align-items: center; gap: 8px;
    color: #4e3540; font-size: 12.5px; font-weight: 800;
}
.mhs-kamar-page .additional-heading svg {
    width: 18px; height: 18px; fill: none;
    stroke: var(--maroon); stroke-width: 1.8;
}
.mhs-kamar-page .additional-card p {
    flex: 1 1 480px; margin: 0; color: #81767e;
    font-size: 12px; line-height: 1.7;
}

/* Responsif mengikuti area konten aktual, termasuk saat sidebar terbuka. */
@container (max-width: 1070px) {
    .mhs-kamar-page .mhs-kamar-overview { grid-template-columns: minmax(0,1fr); }
    .mhs-kamar-page .room-list { grid-template-columns: repeat(2,minmax(0,1fr)); }
}
@container (max-width: 810px) {
    .mhs-kamar-page .room-list { grid-template-columns: minmax(0,1fr); }
    .mhs-kamar-page .pindah-form { grid-template-columns: minmax(0,1fr); }
    .mhs-kamar-page .room-toolbar { grid-template-columns: repeat(2,minmax(0,1fr)); }
    .mhs-kamar-page .room-toolbar .form-control:first-child { grid-column: 1/-1; }
}
@container (max-width: 560px) {
    .mhs-kamar-page .breadcrumb { margin-bottom: 9px; }
    .mhs-kamar-page .mhs-kamar-header { margin-bottom: 15px; padding-bottom: 15px; }
    .mhs-kamar-page .page-title { font-size: 22px; }
    .mhs-kamar-page .page-subtitle { font-size: 12px; }
    .mhs-kamar-page .room-heading, .mhs-kamar-page .detail-card h2 { padding: 13px 14px; }
    .mhs-kamar-page .room-highlight { margin: 12px 13px 9px; padding: 12px; }
    .mhs-kamar-page .room-grid { margin: 0 13px 13px; gap: 8px; }
    .mhs-kamar-page .room-info { padding: 10px; }
    .mhs-kamar-page .detail-row { margin: 0 14px; }
    .mhs-kamar-page .room-card > p.muted { margin: 12px 14px 0 !important; }
    .mhs-kamar-page .pindah-form { margin: 12px 14px 14px; padding: 13px; }
    .mhs-kamar-page .pindah-actions .btn-ajukan { max-width: none; }
    .mhs-kamar-page .room-toolbar { grid-template-columns: minmax(0,1fr); margin: 12px 14px 14px; }
    .mhs-kamar-page .room-toolbar .form-control:first-child { grid-column: auto; }
    .mhs-kamar-page .room-list { margin: 0 14px 14px; gap: 11px; }
    .mhs-kamar-page .room-item { padding: 12px; }
    .mhs-kamar-page .room-photo { height: 175px; }
    .mhs-kamar-page .form-control { font-size: 16px; }
    .mhs-kamar-page .status-banner { padding: 13px 14px; }
    .mhs-kamar-page .additional-card { padding: 13px 14px; }
}
@container (max-width: 375px) {
    .mhs-kamar-page .room-grid { grid-template-columns: minmax(0,1fr); }
    .mhs-kamar-page .detail-row { grid-template-columns: minmax(0,1fr); gap: 3px; }
    .mhs-kamar-page .room-highlight { grid-template-columns: 35px minmax(0,1fr); gap: 0 10px; }
    .mhs-kamar-page .room-highlight svg { width: 35px; height: 35px; padding: 7px; }
    .mhs-kamar-page .room-item-specs { grid-template-columns: repeat(2,minmax(0,1fr)); }
}
</style>
@endpush
@section('content')
<div class="mhs-kamar-page">
    <nav class="breadcrumb" aria-label="Navigasi halaman">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Informasi Kamar</span>
    </nav>
    <header class="mhs-kamar-header">
        <div>
            <h1 class="page-title">Informasi Kamar</h1>
            <p class="page-subtitle">Lihat penempatan, periksa ketersediaan, dan ajukan perpindahan kamar Ma’had.</p>
        </div>

    </header>
            @php
                $pengajuanPindahMenunggu = $pengajuanPindah && $pengajuanPindah->masihMenunggu();
                // Sesuai model pengajuan dua persetujuan terbaru.
                // Status tetap "menunggu" sampai keputusan akhir dari Musyrifah tujuan.
                $menungguMusyrifahTujuan = $pengajuanPindahMenunggu
                    && ! is_null($pengajuanPindah->persetujuan_asal_oleh);
                $kamarPindahAsal = $pengajuanPindah?->kamarAsal;
                $kamarPindahTujuan = $pengajuanPindah?->kamar;
                $pindahLintasWilayah = $kamarPindahAsal && $kamarPindahTujuan
                    && (mb_strtolower(trim((string) $kamarPindahAsal->gedung))
                        !== mb_strtolower(trim((string) $kamarPindahTujuan->gedung))
                        || (int) $kamarPindahAsal->lantai !== (int) $kamarPindahTujuan->lantai);
                $pengajuanPindahDitolak = $pengajuanPindah
                    && $pengajuanPindah->status === \App\Models\PengajuanKamar::STATUS_DITOLAK;
                $daftarLantai = collect($daftarKamar)->pluck('lantai')->unique()->sort()->values();
            @endphp
            @if ($kamarSaya)
                <div class="mhs-kamar-overview">
                    <section class="room-card">
                    <div class="room-heading">
                        <h2>Kamar Saya</h2>
                        <span class="status">Telah Ditetapkan</span>
                    </div>
                    <div class="room-highlight">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 18V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v11"/>
                            <path d="M12 11h7a2 2 0 0 1 2 2v5"/>
                            <path d="M3 14h18M3 18v2M21 18v2"/>
                        </svg>
                        <div class="room-label">Nomor Kamar</div>
                        <div class="room-number">
                            {{ $kamarSaya->nomor_kamar ?: 'Belum ditetapkan' }}
                        </div>
                        <div class="muted">
                            {{ $kamarSaya->nama_kamar ?: 'Kamar '.$kamarSaya->nomor_kamar }}
                            &bull; Penempatan ditetapkan oleh Musyrifah Ma’had.
                        </div>
                    </div>
                    <div class="room-grid">
                        <div class="room-info">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 21V5h12v16M15 10h6v11M1 21h22"/>
                            </svg>
                            <div class="info-label">Gedung / Lantai</div>
                            <div class="info-value">
                                {{ $kamarSaya->gedung ?: 'Belum tercatat' }}
                                @if ($kamarSaya->lantai)
                                    / Lantai {{ $kamarSaya->lantai }}
                                @endif
                            </div>
                        </div>
                        <div class="room-info">
                            <svg viewBox="0 0 24 24">
                                <circle cx="9" cy="7" r="3"/>
                                <path d="M3 20v-2a6 6 0 0 1 12 0v2"/>
                                <circle cx="18" cy="8" r="2.5"/>
                                <path d="M17 14a5 5 0 0 1 4 5"/>
                            </svg>
                            <div class="info-label">Kapasitas Kamar</div>
                            <div class="info-value">
                                {{ (int) $kamarSaya->kapasitas }} orang
                                ({{ $kamarSaya->jumlahPenghuni() }} terisi)
                            </div>
                        </div>
                    </div>
                </section>
                <section class="detail-card">
                    <h2>Detail Penempatan</h2>
                    <div class="detail-row">
                        <div class="detail-label">Nama Penghuni</div>
                        <div class="detail-value">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">NIM</div>
                        <div class="detail-value">{{ auth()->user()->nim ?: '-' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Fasilitas Kamar</div>
                        <div class="detail-value">{{ $kamarSaya->fasilitas ?: 'Belum ada rincian fasilitas.' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status Penempatan</div>
                        <div class="detail-value">Telah Ditetapkan</div>
                    </div>
                </section>
                </div>
                @if ($pengajuanPindahMenunggu)
                    <div class="status-banner">
                        @if ($menungguMusyrifahTujuan)
                            <strong>Tahap 1 selesai — menunggu persetujuan Musyrifah tujuan.</strong>
                            Musyrifah asal sudah menyetujui mahasiswa keluar dari kamar asal.
                            Pengajuan sekarang diperiksa oleh Musyrifah gedung dan lantai tujuan.
                            @if ($pengajuanPindah->persetujuan_asal_pada)
                                <div class="persetujuan-detail">
                                    Persetujuan asal: {{ $pengajuanPindah->persetujuan_asal_pada->translatedFormat('d M Y H:i') }}
                                </div>
                            @endif
                        @elseif ($pindahLintasWilayah)
                            <strong>Tahap 1 — menunggu persetujuan Musyrifah asal.</strong>
                            Setelah Musyrifah asal menyetujui, pengajuan akan diteruskan kepada
                            Musyrifah tujuan untuk keputusan akhir.
                        @else
                            <strong>Menunggu verifikasi Musyrifah wilayah kamar.</strong>
                            Perpindahan dalam gedung dan lantai yang sama cukup melalui satu persetujuan.
                        @endif
                        Kamar asal <strong>Kamar {{ $pengajuanPindah->kamarAsal?->nomor_kamar ?? $kamarSaya->nomor_kamar }}</strong>
                        ke kamar tujuan <strong>Kamar {{ $pengajuanPindah->kamar?->nomor_kamar ?? '-' }}</strong>
                        (diajukan {{ optional($pengajuanPindah->created_at)->translatedFormat('d M Y H:i') }}).
                        Kamu tetap menempati kamar asal sampai persetujuan yang diperlukan selesai
                        dan sistem memperbarui penempatanmu.
                        @if ($pengajuanPindah->alasan)
                            <div class="catatan-penolakan" style="background:#fff;border-color:#f0d9a8;">
                                <strong>Alasan perpindahan:</strong> {{ $pengajuanPindah->alasan }}
                            </div>
                        @endif
                    </div>
                @elseif ($pengajuanPindahDitolak)
                    <div class="status-banner status-banner--rejected">
                        <strong>Pengajuan pindah kamar ditolak.</strong>
                        Kamu tetap menempati kamar saat ini
                        (Kamar {{ $kamarSaya->nomor_kamar }}).
                        <div class="catatan-penolakan">
                            <strong>Catatan Musyrifah:</strong>
                            {{ $pengajuanPindah->catatan ?: 'Tidak ada catatan penolakan.' }}
                        </div>
                    </div>
                @endif
                @unless ($pengajuanPindahMenunggu)
                    <section class="room-card" id="pengajuan-pindah">
                        <div class="room-heading">
                            <h2>Pengajuan Pindah Kamar</h2>
                            <span class="status">Butuh Verifikasi</span>
                        </div>
                        <p class="muted" style="margin: 0;">
                            Pilih kamar tujuan yang masih memiliki sisa kapasitas dan tuliskan
                            alasan perpindahan. Untuk pindah ke gedung atau lantai lain,
                            diperlukan persetujuan Musyrifah asal lalu Musyrifah tujuan.
                            Jika masih di gedung dan lantai yang sama, cukup satu persetujuan.
                            Kamar asal tetap menjadi penempatanmu selama verifikasi.
                        </p>
                        @php
                            $kamarTujuanTersedia = collect($daftarKamar)
                                ->filter(fn ($kamar) => $kamar['id'] !== (int) $kamarSaya->id && $kamar['bisa_diajukan']);
                        @endphp
                        <form method="POST" action="{{ route('kamar.pindah') }}" class="pindah-form">
                            @csrf
                            <div class="pindah-field pindah-field--tujuan">
                            <label class="form-label" for="kamarTujuan">Kamar Tujuan</label>
                            <select
                                class="form-control"
                                name="kamar_id"
                                id="kamarTujuan"
                                required
                                @disabled($kamarTujuanTersedia->isEmpty())
                            >
                                <option value="">— Pilih kamar tujuan —</option>
                                @foreach ($kamarTujuanTersedia as $kamar)
                                    <option value="{{ $kamar['id'] }}" @selected((string) old('kamar_id') === (string) $kamar['id'])>
                                        Kamar {{ $kamar['nomor'] }} — {{ $kamar['gedung'] }}, Lantai {{ $kamar['lantai'] }}
                                        (sisa {{ $kamar['sisa'] }} tempat)
                                    </option>
                                @endforeach
                            </select>
                            <p class="form-hint">
                                @if ($kamarTujuanTersedia->isEmpty())
                                    Belum ada kamar lain yang memiliki sisa kapasitas saat ini.
                                @else
                                    Hanya kamar dengan sisa kapasitas yang dapat diajukan.
                                @endif
                            </p>
                            </div>
                            <div class="pindah-field pindah-field--alasan">
                            <label class="form-label" for="alasanPindah">Alasan Perpindahan</label>
                            <textarea
                                class="form-control"
                                name="alasan"
                                id="alasanPindah"
                                rows="4"
                                maxlength="1000"
                                required
                                placeholder="Contoh: kamar saat ini terlalu padat dan sulit beristirahat."
                            >{{ old('alasan') }}</textarea>
                            <p class="form-hint">Wajib diisi, minimal 10 karakter.</p>
                            </div>
                            <div class="pindah-actions">
                            <button
                                type="submit"
                                class="btn-ajukan"
                                @disabled($kamarTujuanTersedia->isEmpty())
                            >
                                Kirim Pengajuan Pindah
                            </button>
                            </div>
                        </form>
                    </section>
                @endunless
            @else
                @if ($pengajuanSaya)
                    @if ($pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_MENUNGGU)
                        <div class="status-banner">
                            <strong>Pengajuan kamar sedang menunggu persetujuan.</strong>
                            Kamar pilihanmu:
                            <strong>Kamar {{ $pengajuanSaya->kamar?->nomor_kamar ?? '-' }}</strong>
                            (diajukan {{ optional($pengajuanSaya->created_at)->translatedFormat('d M Y H:i') }}).
                            Kamu masih dapat mengubah pilihan kamar selama belum disetujui Musyrifah.
                        </div>
                    @elseif ($pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_DITOLAK)
                        <div class="status-banner status-banner--rejected">
                            <strong>Pengajuan kamar sebelumnya ditolak.</strong>
                            Silakan ajukan pilihan kamar lain di bawah ini.
                            @if ($pengajuanSaya->catatan)
                                <div class="catatan-penolakan">
                                    <strong>Catatan Musyrifah:</strong> {{ $pengajuanSaya->catatan }}
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            @endif
            <section class="room-card">
                <div class="room-heading">
                    <h2>Daftar Kamar Ma’had</h2>
                    <span class="status">{{ count($daftarKamar) }} Kamar</span>
                </div>
                <p class="muted" style="margin: 0;">
                    Data kamar mengikuti pengelolaan Musyrifah. Gunakan pencarian cepat
                    berdasarkan nomor kamar atau fasilitas, serta filter lantai dan status
                    ketersediaan.
                </p>
                <div class="room-toolbar">
                    <input
                        type="search"
                        id="cariKamar"
                        class="form-control"
                        placeholder="Cari nomor kamar atau fasilitas..."
                        aria-label="Cari nomor kamar atau fasilitas"
                    >
                    <select id="filterLantai" class="form-control" aria-label="Filter lantai kamar">
                        <option value="">Semua Lantai</option>
                        @foreach ($daftarLantai as $lantai)
                            <option value="{{ $lantai }}">Lantai {{ $lantai }}</option>
                        @endforeach
                    </select>
                    <select id="filterStatus" class="form-control" aria-label="Filter status ketersediaan kamar">
                        <option value="">Semua Status</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="penuh">Penuh</option>
                        <option value="lainnya">Tidak Tersedia</option>
                    </select>
                </div>
                <div class="room-list" id="daftarKamarList">
                    @forelse ($daftarKamar as $kamar)
                        @php
                            $kamarSaatIni = $kamarSaya && (int) $kamarSaya->id === (int) $kamar['id'];
                            $statusKamar = strtolower(trim((string) $kamar['status']));
                            $kategoriStatus = $kamar['bisa_diajukan']
                                ? 'tersedia'
                                : (in_array($statusKamar, ['dalam perbaikan', 'tidak tersedia', 'nonaktif'], true) ? 'lainnya' : 'penuh');
                            $sedangDiajukan = ! $kamarSaya
                                && $pengajuanSaya
                                && $pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_MENUNGGU
                                && (int) $pengajuanSaya->kamar_id === (int) $kamar['id'];
                            $sedangDiproses = ! $kamarSaya
                                && $pengajuanSaya
                                && $pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_MENUNGGU
                                && ! $sedangDiajukan;
                            $kataKunci = strtolower(trim(
                                $kamar['nomor'].' '.($kamar['nama'] ?? '').' '.$kamar['gedung'].' '.($kamar['fasilitas'] ?? '')
                            ));
                        @endphp
                        <div
                            class="room-item {{ $sedangDiajukan ? 'is-selected' : '' }} {{ $kamarSaatIni ? 'is-current' : '' }}"
                            data-lantai="{{ $kamar['lantai'] }}"
                            data-status="{{ $kategoriStatus }}"
                            data-kata="{{ $kataKunci }}"
                        >
                            @php
                                $fotosKamar = $kamar['fotos'] ?? [];
                            @endphp
                            @if (! empty($fotosKamar))
                                <div class="room-galeri" data-galeri>
                                    @foreach ($fotosKamar as $indexFoto => $foto)
                                        <img
                                            class="room-photo room-galeri__foto {{ $indexFoto === 0 ? 'is-active' : '' }}"
                                            src="{{ $foto['url'] }}"
                                            alt="Foto {{ $indexFoto + 1 }} Kamar {{ $kamar['nomor'] }}"
                                            loading="lazy"
                                            data-galeri-slide
                                        >
                                    @endforeach
                                    @if (count($fotosKamar) > 1)
                                        <button type="button" class="room-galeri__nav room-galeri__nav--prev" data-galeri-prev aria-label="Foto sebelumnya">&#8249;</button>
                                        <button type="button" class="room-galeri__nav room-galeri__nav--next" data-galeri-next aria-label="Foto berikutnya">&#8250;</button>
                                        <span class="room-galeri__counter" data-galeri-counter>1 / {{ count($fotosKamar) }}</span>
                                    @endif
                                </div>
                                @if (count($fotosKamar) > 1)
                                    <div class="room-galeri__dots">
                                        @foreach ($fotosKamar as $indexFoto => $foto)
                                            <button
                                                type="button"
                                                class="room-galeri__dot {{ $indexFoto === 0 ? 'is-active' : '' }}"
                                                data-galeri-dot="{{ $indexFoto }}"
                                                aria-label="Tampilkan foto {{ $indexFoto + 1 }}"
                                            ></button>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="room-photo room-photo--empty">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M3 18V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v11"/>
                                        <path d="M12 11h7a2 2 0 0 1 2 2v5"/>
                                        <path d="M3 14h18M3 18v2M21 18v2"/>
                                    </svg>
                                    <span>Foto kamar belum tersedia</span>
                                </div>
                            @endif
                            <div class="room-item-head">
                                <div>
                                    <h3 class="room-item-title">Kamar {{ $kamar['nomor'] }}</h3>
                                    <p class="room-item-sub">
                                        {{ $kamar['gedung'] }} &bull; Lantai {{ $kamar['lantai'] }}
                                    </p>
                                </div>
                                @if ($kamarSaatIni)
                                    <span class="badge badge-current">Kamar Saat Ini</span>
                                @elseif ($sedangDiajukan)
                                    <span class="badge badge-pending">Diajukan</span>
                                @elseif ($kamar['bisa_diajukan'])
                                    <span class="badge badge-available">Tersedia</span>
                                @elseif ($kategoriStatus === 'penuh')
                                    <span class="badge badge-full">Penuh</span>
                                @else
                                    <span class="badge badge-closed">{{ $kamar['status'] }}</span>
                                @endif
                            </div>
                            <div class="room-item-specs">
                                <div><strong>Kapasitas</strong>{{ $kamar['kapasitas'] }} orang</div>
                                <div><strong>Penghuni</strong>{{ $kamar['terisi'] }} orang</div>
                                <div><strong>Sisa Kapasitas</strong>{{ $kamar['sisa'] }} tempat</div>
                            </div>
                            <p class="room-item-fasilitas">
                                <strong>Fasilitas:</strong>
                                {{ $kamar['fasilitas'] ?: 'Belum ada rincian fasilitas.' }}
                            </p>
                            @if ($kamarSaatIni)
                                <button type="button" class="btn-ajukan" disabled>Kamar Saat Ini</button>
                            @elseif ($kamarSaya)
                                <button
                                    type="button"
                                    class="btn-ajukan"
                                    data-kamar-pindah="{{ $kamar['id'] }}"
                                    @disabled(! $kamar['bisa_diajukan'] || $pengajuanPindahMenunggu)
                                >
                                    @if ($pengajuanPindahMenunggu)
                                        Menunggu Verifikasi
                                    @elseif ($kamar['bisa_diajukan'])
                                        Pilih untuk Pindah
                                    @else
                                        Tidak Tersedia
                                    @endif
                                </button>
                            @else
                                <form method="POST" action="{{ route('kamar.pilih') }}" style="margin-top: auto;">
                                    @csrf
                                    <input type="hidden" name="kamar_id" value="{{ $kamar['id'] }}">
                                    <button type="submit" class="btn-ajukan" @disabled(! $kamar['bisa_diajukan'] || $sedangDiajukan)>
                                        @if ($sedangDiajukan)
                                            Sedang Diajukan
                                        @elseif ($kamar['bisa_diajukan'])
                                            {{ $sedangDiproses ? 'Ganti ke Kamar Ini' : 'Ajukan Kamar Ini' }}
                                        @else
                                            Tidak Tersedia
                                        @endif
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state">
                            Belum ada data kamar dari Musyrifah Ma’had.
                        </div>
                    @endforelse
                    <div class="empty-state" id="kamarKosong" hidden>
                        Tidak ada kamar yang sesuai dengan pencarian atau filter.
                    </div>
                </div>
            </section>
            <section class="additional-card">
                <div class="additional-heading">
                    <svg viewBox="0 0 24 24">
                        <rect x="5" y="4" width="14" height="17" rx="2"/>
                        <path d="M9 4V2h6v2M9 10h6M9 14h6M9 18h3"/>
                    </svg>
                    Informasi Tambahan
                </div>
                <p>
                    Penempatan pertama diverifikasi Musyrifah wilayah kamar tujuan.
                    Pindah lintas gedung atau lantai diverifikasi Musyrifah asal
                    lalu Musyrifah tujuan. Selama pengajuan belum disetujui, penempatan kamar
                    kamu tidak berubah.
                </p>
        </section>
    </div>
@endsection
@push('scripts')
<script>
        (function () {
            const inputCari = document.getElementById('cariKamar');
            const filterLantai = document.getElementById('filterLantai');
            const filterStatus = document.getElementById('filterStatus');
            const daftarKamar = document.getElementById('daftarKamarList');
            const pesanKosong = document.getElementById('kamarKosong');
            if (daftarKamar) {
                const semuaKamar = Array.from(daftarKamar.querySelectorAll('.room-item'));
                const terapkanFilter = function () {
                    const kata = (inputCari && inputCari.value ? inputCari.value : '').toLowerCase().trim();
                    const lantai = filterLantai ? filterLantai.value : '';
                    const status = filterStatus ? filterStatus.value : '';
                    let jumlahTampil = 0;
                    semuaKamar.forEach(function (item) {
                        const cocokKata = !kata || (item.dataset.kata || '').includes(kata);
                        const cocokLantai = !lantai || item.dataset.lantai === lantai;
                        const cocokStatus = !status || item.dataset.status === status;
                        const tampil = cocokKata && cocokLantai && cocokStatus;
                        item.hidden = !tampil;
                        if (tampil) {
                            jumlahTampil += 1;
                        }
                    });
                    if (pesanKosong) {
                        pesanKosong.hidden = jumlahTampil > 0 || semuaKamar.length === 0;
                    }
                };
                if (inputCari) {
                    inputCari.addEventListener('input', terapkanFilter);
                }
                if (filterLantai) {
                    filterLantai.addEventListener('change', terapkanFilter);
                }
                if (filterStatus) {
                    filterStatus.addEventListener('change', terapkanFilter);
                }
            }
            // Tombol "Pilih untuk Pindah" mengisi form pengajuan pindah kamar.
            const pilihanTujuan = document.getElementById('kamarTujuan');
            document.querySelectorAll('[data-kamar-pindah]').forEach(function (tombol) {
                tombol.addEventListener('click', function () {
                    if (! pilihanTujuan) {
                        return;
                    }
                    pilihanTujuan.value = tombol.dataset.kamarPindah;
                    document.getElementById('pengajuan-pindah')
                        .scrollIntoView({ behavior: 'smooth', block: 'start' });
                    document.getElementById('alasanPindah')
                        .focus({ preventScroll: true });
                });
            });
            // Galeri foto: tampilkan foto kamar satu per satu.
            document.querySelectorAll('[data-galeri]').forEach(function (galeri) {
                const slides = Array.from(galeri.querySelectorAll('[data-galeri-slide]'));
                if (slides.length === 0) {
                    return;
                }
                const counter = galeri.querySelector('[data-galeri-counter]');
                const dots = Array.from(galeri.closest('.room-item').querySelectorAll('[data-galeri-dot]'));
                let indeks = 0;
                const tampilkan = function (baru) {
                    indeks = (baru + slides.length) % slides.length;
                    slides.forEach(function (slide, i) {
                        slide.classList.toggle('is-active', i === indeks);
                    });
                    dots.forEach(function (dot, i) {
                        dot.classList.toggle('is-active', i === indeks);
                    });
                    if (counter) {
                        counter.textContent = (indeks + 1) + ' / ' + slides.length;
                    }
                };
                galeri.querySelector('[data-galeri-prev]')?.addEventListener('click', function () {
                    tampilkan(indeks - 1);
                });
                galeri.querySelector('[data-galeri-next]')?.addEventListener('click', function () {
                    tampilkan(indeks + 1);
                });
                dots.forEach(function (dot) {
                    dot.addEventListener('click', function () {
                        tampilkan(parseInt(dot.dataset.galeriDot, 10) || 0);
                    });
                });
            });
        })();
    </script>
@endpush
