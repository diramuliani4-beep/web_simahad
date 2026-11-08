@extends('layouts.musyrifah')

@section('title', 'Manajemen Kamar')

@section('content')

<style>
    .data-kamar-page {
        font-family: 'Poppins', Arial, sans-serif;
        color: #111;
        width: 100%;
    }

    .data-kamar-page * {
        box-sizing: border-box;
    }

    /* JUDUL */
    .data-kamar-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin: 0 0 28px;
        flex-wrap: wrap;
    }

    .data-kamar-heading-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .data-kamar-back {
        color: #111;
        text-decoration: none;
        font-size: 34px;
        line-height: 1;
        flex-shrink: 0;
    }

    .data-kamar-back:hover {
        color: #800020;
    }

    .data-kamar-title {
        margin: 0;
        font-size: 30px;
        font-weight: 700;
        line-height: 1.3;
    }

    /* NOTIFIKASI */
    .data-kamar-alert {
        padding: 13px 18px;
        margin-bottom: 22px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.6;
    }

    .data-kamar-alert--success {
        background: #dcfce7;
        color: #14532d;
        border: 1px solid #86efac;
    }

    .data-kamar-alert--error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* RINGKASAN KAMAR */
    .ringkasan-kamar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .ringkasan-card {
        background: #f5e6e9;
        border: 1px solid #cdbfc0;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, .08);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .ringkasan-card__title {
        font-size: 13px;
        font-weight: 600;
        color: #655b5e;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 6px;
    }

    .ringkasan-card__value {
        font-size: 26px;
        font-weight: 800;
        color: #800020;
    }

    .ringkasan-card__sub {
        font-size: 12px;
        color: #554b4e;
        margin-top: 4px;
    }

    /* FILTER DAN TOMBOL AKSI */
    .kamar-control-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 26px;
        flex-wrap: wrap;
    }

    .kamar-filters {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        flex: 1;
        max-width: 780px;
    }

    .kamar-input-search {
        flex: 1;
        min-width: 200px;
        height: 46px;
        padding: 0 16px;
        border: 1px solid #cbb8bd;
        border-radius: 12px;
        background: #fff;
        font-size: 14px;
        font-family: inherit;
    }

    .kamar-input-search:focus {
        outline: 2px solid #800020;
        outline-offset: 1px;
    }

    .kamar-select-filter {
        height: 46px;
        padding: 0 36px 0 14px;
        border: 1px solid #cbb8bd;
        border-radius: 12px;
        background: #fff;
        font-size: 14px;
        font-family: inherit;
        cursor: pointer;
    }

    .kamar-btn-tambah {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 46px;
        padding: 0 20px;
        background: #800020;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 3px 8px rgba(128, 0, 32, .25);
        transition: background .2s, transform .2s;
    }

    .kamar-btn-tambah:hover {
        background: #600018;
        transform: translateY(-1px);
    }

    /* DAFTAR KARTU KAMAR */
    .daftar-kamar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 22px;
    }

    .kamar-card {
        display: flex;
        flex-direction: column;
        border: 1px solid #cdbfc0;
        border-radius: 18px;
        background: #f5e6e9;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .08);
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }

    .kamar-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, .12);
    }

    .kamar-card__img-wrap {
        width: 100%;
        height: 160px;
        background: #eed8dc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .kamar-card__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .kamar-card__placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        color: #8a6f76;
        font-size: 12px;
        font-weight: 500;
    }

    .kamar-card__placeholder svg {
        width: 42px;
        height: 42px;
        opacity: .7;
    }

    /* GALERI FOTO KAMAR */
    .kamar-card__img-wrap[data-open-modal] {
        cursor: pointer;
    }

    .kamar-card__foto-count {
        position: absolute;
        left: 12px;
        bottom: 12px;
        padding: 3px 10px;
        border-radius: 999px;
        background: rgba(0, 0, 0, .68);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
    }

    .kamar-galeri {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .kamar-galeri__item {
        margin: 0;
        border: 1px solid #ecd8de;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .kamar-galeri__item a {
        display: block;
    }

    .kamar-galeri__item img {
        width: 100%;
        height: 110px;
        object-fit: cover;
        background: #f4efec;
        display: block;
    }

    .kamar-galeri__utama {
        position: absolute;
        top: 8px;
        left: 8px;
        padding: 2px 8px;
        border-radius: 999px;
        background: #800020;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .3px;
    }

    .kamar-galeri__caption {
        padding: 6px 10px 0;
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
    }

    .kamar-galeri__actions {
        display: flex;
        gap: 6px;
        padding: 8px 10px 10px;
        margin-top: auto;
    }

    .kamar-galeri__actions form {
        flex: 1;
        margin: 0;
    }

    .btn-foto {
        width: 100%;
        padding: 6px 8px;
        border-radius: 7px;
        border: 1px solid #d9c2c8;
        background: #fff;
        color: #800020;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        text-align: center;
        display: block;
    }

    .btn-foto--ganti {
        position: relative;
        overflow: hidden;
    }

    .btn-foto--ganti input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .btn-foto--hapus {
        border-color: #fca5a5;
        color: #b91c1c;
    }

    .kamar-galeri__kosong {
        grid-column: 1 / -1;
        padding: 18px;
        border: 1px dashed #d9c2c8;
        border-radius: 12px;
        text-align: center;
        color: #8a6f76;
        font-size: 12px;
        background: #fffafb;
    }

    .kamar-galeri__tambah {
        border: 1px dashed #d9c2c8;
        border-radius: 12px;
        padding: 14px;
        background: #fffafb;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin: 0;
    }

    .kamar-galeri__tambah label {
        font-size: 12px;
        font-weight: 700;
        color: #800020;
    }

    .kamar-galeri__tambah input[type="file"] {
        flex: 1;
        min-width: 180px;
        font-size: 12px;
    }

    .kamar-galeri__tambah .form-hint {
        width: 100%;
        margin: 0;
    }

    .btn-foto--unggah {
        padding: 8px 18px;
        border-radius: 8px;
        border: none;
        background: #800020;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .kamar-card__badge-status {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .2);
    }

    .badge-tersedia {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .badge-penuh {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .badge-perbaikan {
        background: #ffedd5;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .kamar-card__body {
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .kamar-card__header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 8px;
    }

    .kamar-card__nomor {
        font-size: 20px;
        font-weight: 800;
        color: #800020;
    }

    .kamar-card__lantai {
        font-size: 13px;
        font-weight: 600;
        color: #554b4e;
    }

    .kamar-card__specs {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        background: #fff;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid #e7d8db;
        margin-bottom: 12px;
        font-size: 12px;
    }

    .kamar-card__spec-item {
        display: flex;
        flex-direction: column;
    }

    .kamar-card__spec-label {
        color: #7a6e71;
        font-size: 11px;
    }

    .kamar-card__spec-val {
        font-weight: 700;
        color: #111;
        margin-top: 1px;
    }

    .kamar-card__fasilitas-text {
        font-size: 12px;
        color: #4a4043;
        line-height: 1.45;
        margin-bottom: 14px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .kamar-card__actions {
        margin-top: auto;
        display: flex;
        gap: 8px;
        padding-top: 10px;
        border-top: 1px solid #e5d4d7;
    }

    .btn-card {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 8px 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        transition: background .2s, transform .1s;
    }

    .btn-card--detail {
        background: #800020;
        color: #fff;
    }

    .btn-card--detail:hover {
        background: #600018;
    }

    .btn-card--edit {
        background: #fff;
        color: #800020;
        border-color: #cbb8bd;
    }

    .btn-card--edit:hover {
        background: #faedf0;
    }

    .btn-card--delete {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
        flex: 0 0 34px;
        padding: 0;
    }

    .btn-card--delete:hover {
        background: #fca5a5;
    }

    .kamar-kosong {
        display: none;
        grid-column: 1 / -1;
        padding: 40px 20px;
        color: #777;
        text-align: center;
        background: #f9f5f6;
        border-radius: 16px;
        border: 1.5px dashed #cdbfc0;
    }

    /* MODAL UMUM */
    .kamar-modal {
        width: min(720px, calc(100% - 24px));
        max-height: min(92vh, 850px);
        margin: auto;
        padding: 0;
        border: none;
        border-radius: 18px;
        color: #222;
        box-shadow: 0 24px 60px rgba(0, 0, 0, .3);
        overflow: auto;
    }

    .kamar-modal::backdrop {
        background: rgba(16, 10, 12, .6);
        backdrop-filter: blur(2px);
    }

    .kamar-modal__head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        background: #650019;
        color: #fff;
    }

    .kamar-modal__title {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
    }

    .kamar-modal__close {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .5);
        background: transparent;
        color: #fff;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .kamar-modal__body {
        padding: 22px 24px;
    }

    /* FORM MODAL */
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .form-group-item {
        margin-bottom: 14px;
    }

    .form-group-item--full {
        grid-column: 1 / -1;
    }

    .form-label-kamar {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 5px;
    }

    .form-input-kamar,
    .form-select-kamar,
    .form-textarea-kamar {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #cdb6bd;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background: #fff;
    }

    .form-input-kamar:focus,
    .form-select-kamar:focus,
    .form-textarea-kamar:focus {
        outline: 2px solid #800020;
        outline-offset: 1px;
    }

    .form-textarea-kamar {
        min-height: 80px;
        resize: vertical;
    }

    .form-hint {
        font-size: 11px;
        color: #6b7280;
        margin-top: 3px;
    }

    .form-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
        padding-top: 14px;
        border-top: 1px solid #ecd8de;
    }

    /* TABEL PENGHUNI DALAM MODAL DETAIL */
    .penghuni-table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e5d4d7;
        border-radius: 10px;
        margin-top: 10px;
    }

    .penghuni-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .penghuni-table th {
        background: #800020;
        color: #fff;
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
    }

    .penghuni-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #ecd8de;
        background: #fff;
    }

    .penghuni-table tr:last-child td {
        border-bottom: none;
    }

    .penghuni-empty {
        padding: 16px;
        text-align: center;
        color: #777;
        font-style: italic;
        background: #faf7f8;
        border-radius: 8px;
        margin-top: 10px;
    }

    /* PANEL PENGAJUAN PENEMPATAN KAMAR */
    .pengajuan-kamar-section {
        background: #fff;
        border: 1px solid #ecd8de;
        border-radius: 14px;
        padding: 18px 20px 20px;
        margin-bottom: 22px;
        box-shadow: 0 2px 10px rgba(128, 0, 32, .05);
    }

    .pengajuan-kamar-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .pengajuan-kamar-title {
        font-size: 17px;
        color: #800020;
        margin: 0 0 4px;
    }

    .pengajuan-kamar-sub {
        font-size: 12.5px;
        color: #777;
        margin: 0;
        max-width: 620px;
        line-height: 1.6;
    }

    .pengajuan-kamar-badge {
        background: #fff4d6;
        border: 1px solid #f0d9a8;
        color: #a16207;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .pengajuan-kamar-badge--empty {
        background: #e7f7ee;
        border-color: #b7e4c9;
        color: #166534;
    }

    .pengajuan-aksi {
        display: flex;
        gap: 6px;
    }

    .btn-pengajuan {
        border: 1px solid transparent;
        border-radius: 8px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        padding: 7px 12px;
        cursor: pointer;
    }

    .btn-pengajuan--setuju {
        background: #166534;
        color: #fff;
    }

    .btn-pengajuan--tolak {
        background: #fdecef;
        border-color: #f3c2cc;
        color: #8a1428;
    }

    .btn-pengajuan--periksa {
        background: #fff;
        border-color: #cdb6bd;
        color: #800020;
    }

    .btn-pengajuan[disabled] {
        background: #e5e0dd;
        border-color: #e5e0dd;
        color: #9a9a9a;
        cursor: not-allowed;
    }

    .pengajuan-status {
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .pengajuan-status--menunggu {
        background: #fff4d6;
        color: #a16207;
    }

    .pengajuan-status--disetujui {
        background: #e7f7ee;
        color: #166534;
    }

    .pengajuan-status--ditolak {
        background: #fdecef;
        color: #8a1428;
    }

    .pengajuan-riwayat {
        margin-top: 14px;
    }

    .pengajuan-riwayat summary {
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: #800020;
    }

    .pengajuan-jenis {
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 11px;
        font-weight: 600;
        background: #f7e6ea;
        color: #800020;
        white-space: nowrap;
    }

    .pengajuan-jenis--pindah {
        background: #e8eefc;
        color: #1d4ed8;
    }

    .pengajuan-detail {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 16px;
        font-size: 13px;
        margin-bottom: 16px;
    }

    .pengajuan-detail__item strong {
        display: block;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #777;
        margin-bottom: 2px;
    }

    .pengajuan-detail__item--full {
        grid-column: 1 / -1;
    }

    .pengajuan-alasan {
        background: #faf7f8;
        border: 1px solid #ecd8de;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        line-height: 1.6;
        white-space: pre-line;
    }

    .pengajuan-modal-actions {
        display: grid;
        gap: 14px;
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #ecd8de;
    }

    .pengajuan-tolak-form {
        display: grid;
        gap: 6px;
    }

    @media (max-width: 640px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }

        .kamar-control-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .kamar-btn-tambah {
            justify-content: center;
        }
    }
</style>

<div class="data-kamar-page">

    <!-- JUDUL HALAMAN -->
    <div class="data-kamar-heading">
        <div class="data-kamar-heading-left">
            <a href="{{ route('musyrifah.dashboard') }}" class="data-kamar-back" aria-label="Kembali ke Dashboard" title="Kembali ke Dashboard">←</a>
            <h1 class="data-kamar-title">Manajemen Data Kamar Ma'had</h1>
        </div>

        <button type="button" class="kamar-btn-tambah" id="btnBukaTambahKamar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Kamar Baru
        </button>
    </div>

    <!-- NOTIFIKASI -->
    @if (session('success'))
        <div class="data-kamar-alert data-kamar-alert--success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="data-kamar-alert data-kamar-alert--error" role="alert">
            @if (session('error'))
                <div>{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <strong>Periksa kembali data formulir:</strong>
                <ul style="margin: 6px 0 0 18px;">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <!-- RINGKASAN JUMLAH DAN KAPASITAS KAMAR -->
    <section class="ringkasan-kamar-grid">
        <div class="ringkasan-card">
            <div class="ringkasan-card__title">Total Kamar</div>
            <div class="ringkasan-card__value">{{ $jumlahKamar }}</div>
            <div class="ringkasan-card__sub">Kamar terdaftar di sistem</div>
        </div>

        <div class="ringkasan-card">
            <div class="ringkasan-card__title">Kapasitas Penghuni</div>
            <div class="ringkasan-card__value">{{ $totalKapasitas }}</div>
            <div class="ringkasan-card__sub">Total tempat tidur santri</div>
        </div>

        <div class="ringkasan-card">
            <div class="ringkasan-card__title">Mahasiswa Terisi</div>
            <div class="ringkasan-card__value">{{ $kamarTerisi }}</div>
            <div class="ringkasan-card__sub">Santri yang telah menempati</div>
        </div>

        <div class="ringkasan-card">
            <div class="ringkasan-card__title">Sisa Kuota Kamar</div>
            <div class="ringkasan-card__value" style="color: #166534;">{{ $sisaTempatTidur }}</div>
            <div class="ringkasan-card__sub">{{ $kamarTersedia }} kamar masih tersedia</div>
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
                    mahasiswa hanya diperbarui setelah pengajuan disetujui, sedangkan penolakan
                    menyimpan catatan yang dapat dibaca mahasiswa.
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
                <table class="penghuni-table">
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
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $pengajuan['nama'] }}</strong></td>
                                <td>{{ $pengajuan['nim'] }}</td>
                                <td>
                                    <span class="pengajuan-jenis {{ $pengajuan['is_pindah'] ? 'pengajuan-jenis--pindah' : '' }}">
                                        {{ $pengajuan['jenis_label'] }}
                                    </span>
                                </td>
                                <td>
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
                                    @else
                                        <div>Kamar {{ $pengajuan['kamar'] }}</div>
                                        <div style="font-size:11px; color:#777;">
                                            {{ $pengajuan['gedung'] }} &bull; Lantai {{ $pengajuan['lantai'] }}
                                            &bull; Sisa {{ $pengajuan['sisa_kamar'] }} dari {{ $pengajuan['kapasitas'] }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $pengajuan['diajukan_pada'] }}</div>
                                    <span class="pengajuan-status pengajuan-status--menunggu" style="display:inline-block; margin-top:4px;">
                                        {{ $pengajuan['status_label'] }}
                                    </span>
                                </td>
                                <td>
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
                                onsubmit="return confirm('{{ $pengajuan['is_pindah'] ? 'Setujui perpindahan '.$pengajuan['nama'].' dari Kamar '.$pengajuan['kamar_asal'].' ke Kamar '.$pengajuan['kamar'].'?' : 'Setujui penempatan '.$pengajuan['nama'].' di Kamar '.$pengajuan['kamar'].'?' }}');"
                            >
                                @csrf
                                <button type="submit" class="btn-pengajuan btn-pengajuan--setuju" style="width:100%;" @disabled($pengajuan['sisa_kamar'] <= 0)>
                                    {{ $pengajuan['is_pindah'] ? 'Setujui Pindah Kamar' : 'Setujui Penempatan' }}
                                </button>
                                @if ($pengajuan['sisa_kamar'] <= 0)
                                    <p class="form-hint" style="margin-top:6px;">
                                        Kamar tujuan sudah penuh sehingga pengajuan tidak dapat disetujui.
                                    </p>
                                @endif
                            </form>

                            <form
                                class="pengajuan-tolak-form"
                                action="{{ route('musyrifah.pengajuan_kamar.reject', $pengajuan['id']) }}"
                                method="POST"
                                onsubmit="return confirm('Tolak pengajuan kamar {{ $pengajuan['nama'] }}?');"
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
                    <table class="penghuni-table">
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
                                    <td>{{ $pengajuan['nama'] }}</td>
                                    <td>{{ $pengajuan['nim'] }}</td>
                                    <td>{{ $pengajuan['jenis_label'] }}</td>
                                    <td>
                                        @if ($pengajuan['is_pindah'])
                                            Kamar {{ $pengajuan['kamar_asal'] }} &rarr; Kamar {{ $pengajuan['kamar'] }}
                                        @else
                                            Kamar {{ $pengajuan['kamar'] }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="pengajuan-status pengajuan-status--{{ $pengajuan['status'] }}">
                                            {{ $pengajuan['status_label'] }}
                                        </span>
                                    </td>
                                    <td style="max-width:220px; font-size:12px; color:#555;">
                                        {{ $pengajuan['catatan'] ?: '-' }}
                                    </td>
                                    <td>{{ $pengajuan['diproses_pada'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endif
    </section>

    <!-- KONTROL FILTER DAN PENCARIAN -->
    <div class="kamar-control-bar">
        <div class="kamar-filters">
            <input type="search" id="inputCariKamar" class="kamar-input-search" placeholder="Cari nomor kamar, gedung, atau fasilitas..." aria-label="Cari kamar">

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
            </select>
        </div>
    </div>

    <!-- DAFTAR KARTU KAMAR -->
    <section class="daftar-kamar-grid" id="containerDaftarKamar">
        @forelse ($daftarKamar as $kamar)
            @php
                $statusSlug = strtolower(str_replace(' ', '', $kamar['status']));
                $badgeClass = match(true) {
                    str_contains($statusSlug, 'tersedia') => 'badge-tersedia',
                    str_contains($statusSlug, 'penuh') => 'badge-penuh',
                    default => 'badge-perbaikan',
                };
            @endphp

            <article class="kamar-card"
                id="kamar-{{ $kamar['id'] }}"
                data-nomor="{{ strtolower($kamar['nomor']) }}"
                data-gedung="{{ strtolower($kamar['gedung']) }}"
                data-lantai="{{ $kamar['lantai'] }}"
                data-status="{{ str_contains($statusSlug, 'tersedia') ? 'tersedia' : (str_contains($statusSlug, 'penuh') ? 'penuh' : 'perbaikan') }}"
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
                            <span class="kamar-card__spec-val" style="color: {{ $kamar['sisa'] > 0 ? '#166534' : '#991b1b' }}">
                                {{ $kamar['sisa'] }} Tempat
                            </span>
                        </div>
                        <div class="kamar-card__spec-item">
                            <span class="kamar-card__spec-label">Pengajuan:</span>
                            <span class="kamar-card__spec-val" style="color: {{ $kamar['pengajuan_menunggu'] > 0 ? '#a16207' : '#444' }}">
                                {{ $kamar['pengajuan_menunggu'] }} Menunggu
                            </span>
                        </div>
                        <div class="kamar-card__spec-item">
                            <span class="kamar-card__spec-label">Penghuni:</span>
                            <span class="kamar-card__spec-val">{{ count($kamar['penghuni']) }} Terdata</span>
                        </div>
                    </div>

                    <div class="kamar-card__fasilitas-text" title="{{ $kamar['fasilitas'] ?: 'Belum ada rincian fasilitas.' }}">
                        <strong>Fasilitas:</strong> {{ $kamar['fasilitas'] ?: 'Belum ada rincian fasilitas.' }}
                    </div>

                    <div class="kamar-card__actions">
                        <button type="button" class="btn-card btn-card--detail" data-open-modal="modal-detail-{{ $kamar['id'] }}">
                            Penghuni ({{ count($kamar['penghuni']) }})
                        </button>

                        <button type="button" class="btn-card btn-card--edit" data-open-modal="modal-edit-{{ $kamar['id'] }}">
                            Edit
                        </button>

                        @if (empty($kamar['penghuni']) && $kamar['terisi'] == 0)
                            <form action="{{ route('musyrifah.data_kamar.destroy', $kamar['id']) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus Kamar {{ $kamar['nomor'] }}?');">
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
                    <h3 class="kamar-modal__title">Detail & Daftar Penghuni - Kamar {{ $kamar['nomor'] }}</h3>
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

                                <div class="kamar-galeri__actions">
                                    <form action="{{ route('musyrifah.data_kamar.foto.update', [$kamar['id'], $foto['id']]) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="btn-foto btn-foto--ganti" title="Ganti foto ini">
                                            Ganti
                                            <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" required onchange="if (this.files.length) { this.form.submit(); }">
                                        </label>
                                    </form>

                                    <form action="{{ route('musyrifah.data_kamar.foto.destroy', [$kamar['id'], $foto['id']]) }}" method="POST" onsubmit="return confirm('Hapus foto ini dari galeri kamar {{ $kamar['nomor'] }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-foto btn-foto--hapus">Hapus</button>
                                    </form>
                                </div>
                            </figure>
                        @empty
                            <div class="kamar-galeri__kosong">Belum ada foto untuk kamar ini. Unggah minimal satu foto agar tampil pada halaman Informasi Kamar mahasiswa.</div>
                        @endforelse
                    </div>

                    @if ($kamar['sisa_slot_foto'] > 0)
                        <form action="{{ route('musyrifah.data_kamar.foto.store', $kamar['id']) }}" method="POST" enctype="multipart/form-data" class="kamar-galeri__tambah">
                            @csrf
                            <label for="tambah_foto_{{ $kamar['id'] }}">Tambah Foto ({{ count($kamar['fotos']) }}/{{ $kamar['maks_foto'] }})</label>
                            <input type="file" id="tambah_foto_{{ $kamar['id'] }}" name="fotos[]" accept=".jpg,.jpeg,.png,.webp" multiple required>
                            <button type="submit" class="btn-foto--unggah">Unggah</button>
                            <div class="form-hint">Bisa memilih hingga {{ $kamar['sisa_slot_foto'] }} foto sekaligus. Format JPG, PNG, WEBP (maks. 5MB per foto).</div>
                        </form>
                    @else
                        <div class="form-hint">Jumlah foto sudah maksimal ({{ $kamar['maks_foto'] }}). Hapus salah satu foto untuk menambahkan foto baru.</div>
                    @endif

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; margin-bottom: 20px; background: #fffafb; padding: 14px; border-radius: 12px; border: 1px solid #ecd8de;">
                        <div><strong style="font-size:12px; color:#777;">Gedung / Lantai:</strong><br>{{ $kamar['gedung'] }} &bull; Lantai {{ $kamar['lantai'] }}</div>
                        <div><strong style="font-size:12px; color:#777;">Kapasitas / Sisa:</strong><br>{{ $kamar['kapasitas'] }} Orang (Sisa {{ $kamar['sisa'] }})</div>
                        <div><strong style="font-size:12px; color:#777;">Setelah Pengajuan:</strong><br>{{ $kamar['sisa_setelah_pengajuan'] }} Tempat ({{ $kamar['pengajuan_menunggu'] }} menunggu)</div>
                        <div><strong style="font-size:12px; color:#777;">Status Kamar:</strong><br><span class="kamar-card__badge-status {{ $badgeClass }}" style="position:static; display:inline-block; margin-top:2px;">{{ $kamar['status'] }}</span></div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <strong style="display:block; font-size: 13px; color: #800020; margin-bottom: 4px;">Fasilitas Kamar:</strong>
                        <p style="font-size: 13px; color: #444; line-height: 1.6; margin: 0;">{{ $kamar['fasilitas'] ?: 'Belum ada rincian fasilitas.' }}</p>
                    </div>

                    <div>
                        <strong style="display:block; font-size: 14px; color: #800020; margin-bottom: 8px;">
                            Mahasiswa Penghuni Kamar ({{ count($kamar['penghuni']) }} / {{ $kamar['kapasitas'] }}):
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

                    <div class="form-modal-actions">
                        <button type="button" class="btn-card btn-card--edit" data-close-modal style="flex:none; padding:8px 18px;">Tutup</button>
                    </div>
                </div>
            </dialog>

            <!-- MODAL EDIT KAMAR -->
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
                                <input type="text" id="edit_gedung_{{ $kamar['id'] }}" name="gedung" class="form-input-kamar" value="{{ $kamar['gedung'] }}">
                            </div>

                            <div class="form-group-item">
                                <label for="edit_lantai_{{ $kamar['id'] }}" class="form-label-kamar">Lantai <span style="color:red">*</span></label>
                                <input type="number" id="edit_lantai_{{ $kamar['id'] }}" name="lantai" class="form-input-kamar" min="1" max="20" value="{{ $kamar['lantai'] }}" required>
                            </div>

                            <div class="form-group-item">
                                <label for="edit_kapasitas_{{ $kamar['id'] }}" class="form-label-kamar">Kapasitas Tempat Tidur <span style="color:red">*</span></label>
                                <input type="number" id="edit_kapasitas_{{ $kamar['id'] }}" name="kapasitas" class="form-input-kamar" min="1" max="20" value="{{ $kamar['kapasitas'] }}" required>
                            </div>

                            <div class="form-group-item form-group-item--full">
                                <label for="edit_status_{{ $kamar['id'] }}" class="form-label-kamar">Status Ketersediaan <span style="color:red">*</span></label>
                                <select id="edit_status_{{ $kamar['id'] }}" name="status" class="form-select-kamar" required>
                                    <option value="Tersedia" {{ $kamar['status'] === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="Penuh" {{ $kamar['status'] === 'Penuh' ? 'selected' : '' }}>Penuh</option>
                                    <option value="Dalam Perbaikan" {{ $kamar['status'] === 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                                    <option value="Tidak Tersedia" {{ $kamar['status'] === 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
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

        @empty
            <div class="kamar-kosong" style="display:block">
                Belum ada data kamar Ma'had. Silakan klik tombol <strong>Tambah Kamar Baru</strong> di atas.
            </div>
        @endforelse

        <div class="kamar-kosong" id="pesanKamarKosong">
            Kamar yang dicari tidak ditemukan.
        </div>
    </section>

</div>

<!-- MODAL TAMBAH KAMAR BARU -->
<dialog class="kamar-modal" id="modalTambahKamar">
    <div class="kamar-modal__head">
        <h3 class="kamar-modal__title">Tambah Data Kamar Baru</h3>
        <button type="button" class="kamar-modal__close" data-close-modal>&times;</button>
    </div>
    <div class="kamar-modal__body">
        <form action="{{ route('musyrifah.data_kamar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid-2">
                <div class="form-group-item">
                    <label for="input_nomor_kamar" class="form-label-kamar">Nomor Kamar <span style="color:red">*</span></label>
                    <input type="text" id="input_nomor_kamar" name="nomor_kamar" class="form-input-kamar" placeholder="Contoh: 101 atau 1.1" required>
                </div>

                <div class="form-group-item">
                    <label for="input_gedung" class="form-label-kamar">Nama Gedung</label>
                    <input type="text" id="input_gedung" name="gedung" class="form-input-kamar" value="Gedung A" placeholder="Contoh: Gedung A">
                </div>

                <div class="form-group-item">
                    <label for="input_lantai" class="form-label-kamar">Lantai <span style="color:red">*</span></label>
                    <input type="number" id="input_lantai" name="lantai" class="form-input-kamar" min="1" max="20" value="1" required>
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
        }

        inputCari?.addEventListener('input', filterKamar);
        filterLantai?.addEventListener('change', filterKamar);
        filterStatus?.addEventListener('change', filterKamar);

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
