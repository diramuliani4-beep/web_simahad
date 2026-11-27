{{--
    SIMAHAD | Layout formulir Surat & Perizinan Mahasiswa.
    Mengikuti header + sidebar global lewat layouts.mahasiswa.
    Kontrak untuk view anak tetap: title, judul, keterangan, konten-form.
    Jangan menambahkan <html>, <body>, atau <x-panel.sidebar> di sini.
--}}
@extends('layouts.mahasiswa')

@push('styles')
<style>
    .sp-perizinan-page {
        --maroon: #800b22;
        --maroon-dark: #64081b;
        --gold: #C9A96A;
        --gold-soft: #f3e3bf;
        --tint: #fdf1f3;
        --border: #e3cdd3;
        --muted: #6b7280;
        --text: #111827;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        color: var(--text);
        container: surat-perizinan / inline-size;
        font-family: 'Plus Jakarta Sans', 'Poppins', Arial, sans-serif;
        font-size: 13px;
        line-height: 1.6;
    }
    .sp-perizinan-page,
    .sp-perizinan-page *,
    .sp-perizinan-page *::before,
    .sp-perizinan-page *::after { box-sizing: border-box; }
    .sp-perizinan-page a { text-decoration: none; }
    .sp-perizinan-page button,
    .sp-perizinan-page input,
    .sp-perizinan-page select,
    .sp-perizinan-page textarea { font: inherit; }
    .sp-perizinan-page .sp-wrap { width: 100%; max-width: 1280px; margin: 0 auto; padding: 8px 0 40px; }
    .sp-perizinan-page .sp-head { padding-bottom: 19px; border-bottom: 1px solid #e8cfd6; }
    .sp-perizinan-page .sp-breadcrumb { display: flex; align-items: center; flex-wrap: wrap; gap: 7px; margin-bottom: 11px; color: #93858c; font-size: 12px; }
    .sp-perizinan-page .sp-breadcrumb a { color: var(--maroon); font-weight: 600; }
    .sp-perizinan-page .sp-breadcrumb a:hover { text-decoration: underline; }

.sp-perizinan-page .sp-head {
        margin-bottom: 22px;
}

.sp-perizinan-page .sp-head h1 {
        font-size: 26px;
        font-weight: 800;
        color: var(--maroon);
        letter-spacing: -.01em;
}

.sp-perizinan-page .sp-head p {
        margin-top: 6px;
        font-size: 14px;
        color: var(--muted);
        line-height: 1.6;
}

.sp-perizinan-page .sp-alert {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        margin-bottom: 18px;
        padding: 12px 16px;
        border: 1px solid;
        border-radius: 10px;
        font-size: 13.5px;
        overflow-wrap: anywhere;
        line-height: 1.55;
}

.sp-perizinan-page .sp-alert ul {
        margin: 6px 0 0 18px;
}

.sp-perizinan-page .sp-alert--sukses {
        background: #eaf6ee;
        border: 1px solid #2f7d4f;
        color: #1c5334;
}

.sp-perizinan-page .sp-alert--gagal {
        background: var(--tint);
        border: 1px solid var(--maroon);
        color: var(--maroon);
}

.sp-perizinan-page .sp-kuota {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px 20px;
        margin-bottom: 22px;
        padding: 18px 22px;
        border-radius: 16px;
        border: 1px solid var(--gold);
        background: linear-gradient(135deg, var(--maroon) 0%, #800d22 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(107, 13, 27, .22);
}

.sp-perizinan-page .sp-kuota > div:first-child { min-width: 0; flex: 1 1 320px; }
.sp-perizinan-page .sp-kuota__judul {
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px 9px;
        font-size: 16px;
        font-weight: 700;
        color: var(--gold-soft);
}

.sp-perizinan-page .sp-kuota p {
        margin-top: 5px;
        font-size: 13px;
        color: #f6e7ea;
}

.sp-perizinan-page .sp-kuota__angka {
        display: flex;
        flex: 0 0 auto;
        flex-direction: column;
        align-items: flex-end;
        gap: 2px;
}

.sp-perizinan-page .sp-kuota__angka span {
        font-size: 24px;
        font-weight: 800;
        color: var(--gold);
}

.sp-perizinan-page .sp-kuota__angka small {
        font-size: 12.5px;
        color: #f6e7ea;
}

.sp-perizinan-page .sp-card {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        margin-bottom: 24px;
        padding: 26px;
        border: 1px solid var(--border);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(107, 13, 27, .06);
    }

.sp-perizinan-page .sp-card h2 {
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.4;
        color: var(--maroon);
}

.sp-perizinan-page .sp-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: start;
        gap: 22px 24px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
}

.sp-perizinan-page .sp-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
}

.sp-perizinan-page .sp-field--full {
        grid-column: 1 / -1;
}

.sp-perizinan-page .sp-field label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.45;
        color: #374151;
}

.sp-perizinan-page .sp-field label span {
        color: #b00020;
}

.sp-perizinan-page .sp-field :is(input, select, textarea) {
        display: block;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        min-height: 48px;
        padding: 12px 14px;
        border: 1px solid #cdb4ba;
        border-radius: 10px;
        font-size: 15px;
        line-height: 1.5;
        background: #fff;
        color: var(--text);
}

.sp-perizinan-page .sp-field input[type="file"] {
        min-height: 52px;
        padding: 10px 12px;
        background: #fcf9fa;
        font-size: 14px;
        cursor: pointer;
}

.sp-perizinan-page .sp-field input[type="file"]::file-selector-button {
        margin-right: 10px;
        padding: 8px 12px;
        border: 0;
        border-radius: 7px;
        background: var(--tint);
        color: var(--maroon);
        font: inherit;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
}

.sp-perizinan-page .sp-field input:focus,
.sp-perizinan-page .sp-field select:focus,
.sp-perizinan-page .sp-field textarea:focus {
        outline: none;
        border-color: var(--maroon);
        box-shadow: 0 0 0 3px rgba(107, 13, 27, .12);
}

.sp-perizinan-page .sp-field textarea {
        min-height: 144px;
        resize: vertical;
}

.sp-perizinan-page .sp-hint {
        font-size: 12.5px;
        line-height: 1.55;
        color: var(--muted);
        overflow-wrap: anywhere;
}

.sp-perizinan-page .sp-error {
        font-size: 12.5px;
        color: #b00020;
}

.sp-perizinan-page .sp-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f0e2e6;
}

.sp-perizinan-page .sp-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        min-height: 48px;
        padding: 12px 22px;
        line-height: 1.4;
        text-align: center;
        border: 1px solid var(--maroon);
        border-radius: 10px;
        font-size: 14.5px;
        font-weight: 700;
        cursor: pointer;
        background: #fff;
        color: var(--maroon);
        transition: opacity .2s ease;
}

.sp-perizinan-page .sp-btn:hover {
        opacity: .88;
}

.sp-perizinan-page .sp-btn--primary {
        background: var(--maroon);
        color: #fff;
}

.sp-perizinan-page .sp-btn--gold {
        background: var(--gold);
        border-color: var(--gold);
        color: #4a1720;
}

.sp-perizinan-page .sp-btn--danger {
        border-color: #a01c2c;
        color: #a01c2c;
}

.sp-perizinan-page .sp-table-wrap {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: auto;
        overscroll-behavior-inline: contain;
        border: 1px solid var(--border);
        border-radius: 14px;
}

.sp-perizinan-page .sp-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        table-layout: auto;
}

.sp-perizinan-page .sp-table thead {
        background: var(--maroon);
        color: #fff;
}

.sp-perizinan-page .sp-table th,
.sp-perizinan-page .sp-table td {
        padding: 14px 16px;
        font-size: 13.5px;
        line-height: 1.55;
        overflow-wrap: anywhere;
        text-align: left;
        border-bottom: 1px solid #eddde1;
        vertical-align: middle;
}

.sp-perizinan-page .sp-table th {
        font-weight: 700;
        white-space: nowrap;
}

.sp-perizinan-page .sp-table tbody tr:last-child td {
        border-bottom: none;
}

.sp-perizinan-page .sp-table tbody tr:hover {
        background: #fdf6f7;
}

.sp-perizinan-page .sp-badge {
        display: inline-block;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 700;
        white-space: nowrap;
}

.sp-perizinan-page .sp-badge--menunggu {
        background: #fdf1d8; color: #8a5a00; border: 1px solid #e0b658;
}

.sp-perizinan-page .sp-badge--disetujui {
        background: #e7f6ec; color: #1e6b3a; border: 1px solid #7fbf95;
}

.sp-perizinan-page .sp-badge--ditolak {
        background: #fdeaea; color: #a01c2c; border: 1px solid #e2969f;
}

.sp-perizinan-page .sp-badge--perbaikan {
        background: #fdf1d8; color: #8a4b00; border: 1px solid #e0a558;
}

.sp-perizinan-page .sp-badge--selesai {
        background: #e8eefb; color: #22457f; border: 1px solid #93a9d6;
}

.sp-perizinan-page .sp-badge--dibatalkan {
        background: #eef0f3; color: #4b5563; border: 1px solid #b9c0ca;
}

.sp-perizinan-page .sp-empty {
        text-align: center;
        padding: 20px 12px;
        color: var(--muted);
}

.sp-perizinan-page .sp-inline-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-width: 0;
        max-width: 100%;
}

.sp-perizinan-page .sp-inline-form input[type="file"] {
        width: auto;
        max-width: 100%;
        min-width: 0;
        font-size: 12px;
}

.sp-perizinan-page .sp-inline-form input[type="file"]::file-selector-button {
        margin-right: 8px;
        padding: 6px 9px;
        border: 0;
        border-radius: 6px;
        background: var(--tint);
        color: var(--maroon);
        font: inherit;
        font-weight: 700;
        cursor: pointer;
}

.sp-perizinan-page .sp-mini {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-height: 40px;
        padding: 8px 13px;
        line-height: 1.4;
        text-align: center;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        border: 1px solid var(--maroon);
        background: #fff;
        color: var(--maroon);
        cursor: pointer;
        white-space: nowrap;
}

.sp-perizinan-page .sp-mini--gold {
        background: var(--gold);
        border-color: var(--gold);
        color: #4a1720;
}

.sp-perizinan-page .sp-mini--danger {
        border-color: #a01c2c;
        color: #a01c2c;
}

    @container surat-perizinan (max-width: 760px) {
        .sp-perizinan-page .sp-grid { grid-template-columns: minmax(0, 1fr); gap: 16px; }
        .sp-perizinan-page .sp-card { padding: 20px; }
        .sp-perizinan-page .sp-alert { padding: 11px 14px; font-size: 13px; }
        .sp-perizinan-page .sp-kuota__angka { align-items: flex-start; }
    }

    @container surat-perizinan (max-width: 700px) {
        .sp-perizinan-page .sp-head h1 { font-size: 23px; }
        .sp-perizinan-page .sp-head p { font-size: 13px; }
        .sp-perizinan-page .sp-card h2 { font-size: 16px; }
        .sp-perizinan-page .sp-table-wrap { overflow: visible; }
        .sp-perizinan-page .sp-table, .sp-perizinan-page .sp-table tbody { display: block; width: 100%; min-width: 0; }
        .sp-perizinan-page .sp-table thead { display: none; }
        .sp-perizinan-page .sp-table tbody tr:not(.sp-empty-row) { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px 14px; margin: 12px; padding: 14px; border: 1px solid #eadce0; border-radius: 11px; background: #fff !important; }
        .sp-perizinan-page .sp-table tbody tr:not(.sp-empty-row):hover { background: #fff !important; }
        .sp-perizinan-page .sp-table tbody tr:not(.sp-empty-row) td { display: block; width: auto; min-width: 0; padding: 0; border: 0; }
        .sp-perizinan-page .sp-table tbody tr:not(.sp-empty-row) td::before { display: block; margin-bottom: 4px; color: #93858c; font-size: 10.5px; font-weight: 700; content: attr(data-label); }
        .sp-perizinan-page .sp-table tbody tr.sp-empty-row { display: block; }
        .sp-perizinan-page .sp-table tbody tr.sp-empty-row td::before { content: none; }
        .sp-perizinan-page .sp-table--kegiatan tbody td:last-child,
        .sp-perizinan-page .sp-table--pulang tbody td:nth-child(2),
        .sp-perizinan-page .sp-table--pulang tbody td:nth-child(5),
        .sp-perizinan-page .sp-table--pulang tbody td:last-child,
        .sp-perizinan-page .sp-table--semester tbody td:nth-child(2),
        .sp-perizinan-page .sp-table--semester tbody td:nth-child(3),
        .sp-perizinan-page .sp-table--semester tbody td:nth-child(4),
        .sp-perizinan-page .sp-table--semester tbody td:nth-child(5),
        .sp-perizinan-page .sp-table--semester tbody td:last-child { grid-column: 1 / -1; }
        .sp-perizinan-page .sp-table--semester .sp-inline-form input[type="file"] { flex: 1 1 100%; }
    }

    @container surat-perizinan (max-width: 480px) {
        .sp-perizinan-page .sp-wrap { padding: 4px 0 28px; }
        .sp-perizinan-page .sp-head { margin-bottom: 18px; padding-bottom: 16px; }
        .sp-perizinan-page .sp-card { padding: 17px; }
        .sp-perizinan-page .sp-card h2 { margin-bottom: 16px; }
        .sp-perizinan-page .sp-field :is(input, select, textarea) { padding-right: 12px; padding-left: 12px; font-size: 16px; }
        .sp-perizinan-page .sp-field input[type="file"] { padding: 9px; font-size: 13px; }
        .sp-perizinan-page .sp-field input[type="file"]::file-selector-button { margin-right: 7px; padding: 7px 9px; }
        .sp-perizinan-page .sp-kuota { padding: 16px; }
        .sp-perizinan-page .sp-kuota__judul { font-size: 14.5px; }
        .sp-perizinan-page .sp-kuota__angka span { font-size: 22px; }
        .sp-perizinan-page .sp-actions > * { flex: 1 1 0; }
        .sp-perizinan-page .sp-table tbody tr:not(.sp-empty-row) { grid-template-columns: minmax(0, 1fr); margin: 10px; padding: 13px; }
        .sp-perizinan-page .sp-table tbody tr:not(.sp-empty-row) td { grid-column: 1 / -1; }
        .sp-perizinan-page .sp-inline-form input[type="file"] { flex: 1 1 100%; }
        .sp-perizinan-page .sp-inline-form .sp-mini { flex: 1 1 0; }
    }

    @media print {
        .sp-perizinan-page .sp-actions {
            display: none !important;
        }
}

    /* Konten mengikuti panel Mahasiswa; layout mandiri lama tidak dipakai lagi. */
    .sp-perizinan-page :is(input, select, textarea, button, a):focus-visible { outline: 2px solid rgba(128,11,34,.7); outline-offset: 2px; }
</style>
@endpush

@section('content')
<div class="sp-perizinan-page">
    <div class="sp-wrap">
        <header class="sp-head">
            <nav class="sp-breadcrumb" aria-label="Navigasi halaman">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('surat.perizinan.mhs') }}">Surat &amp; Perizinan</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">@yield('judul', 'Pengajuan')</span>
            </nav>
            <h1>@yield('judul', 'Surat &amp; Perizinan')</h1>
            <p>@yield('keterangan')</p>
        </header>


        @if ($errors->any())
            <div class="sp-alert sp-alert--gagal" role="alert">
                <strong>Periksa kembali data pengajuan:</strong>
                <ul>
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('konten-form')
    </div>
</div>

@endsection
