@extends('layouts.musyrifah')
@section('title', 'Pembayaran Mahasiswa')
@section('content')
<style>
    .mp {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--ink);
        min-width: 0;
    }
    .mp *,
    .mp *::before,
    .mp *::after {
        box-sizing: border-box;
    }
    .mp .page-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 14px;
    }
    .mp .page-head h1 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 800;
    }
    .mp .page-head p {
        margin: 4px 0 0;
        font-size: 0.8rem;
        line-height: 1.6;
        color: var(--muted);
    }
    .mp .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 38px;
        padding: 8px 14px;
        border: 1px solid var(--maroon);
        border-radius: 9px;
        background: var(--maroon);
        color: #fff;
        font-family: inherit;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
    }
    .mp .btn:hover {
        background: var(--maroon-dark);
    }
    .mp .btn--garis {
        background: #fff;
        color: var(--maroon);
    }
    .mp .btn--garis:hover {
        background: var(--maroon-tint);
    }
    .mp .btn--hijau {
        border-color: #15803d;
        background: #15803d;
    }
    .mp .btn--hijau:hover {
        background: #166534;
    }
    .mp .btn--merah {
        border-color: #b91c1c;
        background: #b91c1c;
    }
    .mp .btn--merah:hover {
        background: #991b1b;
    }
    .mp .btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }
    .mp .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }
    .mp .stat {
        min-width: 0;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
    }
    .mp .stat .label {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .mp .stat .nilai {
        display: block;
        margin-top: 4px;
        font-size: 1.1rem;
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }
    .mp .stat--maroon {
        border-color: #e3c9cf;
        background: linear-gradient(135deg, #fff, var(--maroon-tint));
    }
    .mp .stat--maroon .nilai {
        color: var(--maroon);
    }
    .mp .panel {
        min-width: 0;
        margin-bottom: 14px;
        padding: 14px 16px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #fff;
    }
    .mp .panel-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }
    .mp .panel-head h2 {
        margin: 0;
        font-size: 0.94rem;
        font-weight: 800;
    }
    .mp .panel-head span {
        font-size: 0.73rem;
        color: var(--muted);
    }
    .mp .toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }
    .mp .toolbar input,
    .mp .toolbar select {
        min-height: 38px;
        padding: 8px 11px;
        border: 1px solid #e0d5d0;
        border-radius: 9px;
        background: #fff;
        font-family: inherit;
        font-size: 0.8rem;
        color: var(--ink);
    }
    .mp .toolbar input {
        flex: 1 1 220px;
    }
    .mp .toolbar select {
        flex: 0 1 180px;
    }
    .mp .tabel-gulir {
        width: 100%;
        overflow-x: auto;
    }
    .mp table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
    }
    .mp thead th {
        padding: 8px 9px;
        border-bottom: 2px solid var(--line);
        text-align: left;
        font-size: 0.67rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
        white-space: nowrap;
    }
    .mp tbody td {
        padding: 9px;
        border-bottom: 1px solid #f3ebe8;
        vertical-align: top;
    }
    .mp tbody tr:last-child td {
        border-bottom: none;
    }
    .mp .nama {
        display: block;
        font-weight: 700;
        overflow-wrap: anywhere;
    }
    .mp .sub {
        display: block;
        margin-top: 2px;
        font-size: 0.7rem;
        color: var(--muted);
        overflow-wrap: anywhere;
    }
    .mp .nominal {
        font-weight: 700;
        white-space: nowrap;
    }
    .mp .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.66rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .mp .badge.lunas {
        background: #dcfce7;
        color: #15803d;
    }
    .mp .badge.menunggu {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .mp .badge.rektorat {
        background: #ede9fe;
        color: #5b21b6;
    }
    .mp .badge.ditolak,
    .mp .badge.terlambat {
        background: #fee2e2;
        color: #b91c1c;
    }
    .mp .badge.belum {
        background: #fef3c7;
        color: #92400e;
    }
    .mp .badge.verifikasi-menunggu {
        background: #e0f2fe;
        color: #075985;
    }
    .mp .badge.verifikasi-diterima {
        background: #dcfce7;
        color: #166534;
    }
    .mp .badge.verifikasi-ditolak {
        background: #fee2e2;
        color: #991b1b;
    }
    .mp .badge.verifikasi-kosong {
        background: #f3f4f6;
        color: #4b5563;
    }
    .mp .tautan-bukti {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--maroon);
    }
    .mp .tautan-bukti:hover {
        text-decoration: underline;
    }
    .mp .dua-kolom {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr);
        gap: 14px;
        align-items: start;
    }
    .mp .dua-kolom .panel {
        margin-bottom: 0;
    }
    .mp .jadwal-baris {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 9px 10px;
        padding: 10px 0;
        border-top: 1px dashed var(--line);
    }
    .mp .jadwal-baris:first-of-type {
        border-top: none;
        padding-top: 0;
    }
    .mp .field label {
        display: block;
        margin-bottom: 4px;
        font-size: 0.68rem;
        font-weight: 700;
        color: var(--text-secondary);
    }
    .mp .field input,
    .mp .field textarea {
        width: 100%;
        min-height: 38px;
        padding: 8px 10px;
        border: 1px solid #e0d5d0;
        border-radius: 8px;
        background: #fff;
        font-family: inherit;
        font-size: 0.8rem;
        color: var(--ink);
    }
    .mp .field textarea {
        min-height: 70px;
        resize: vertical;
    }
    .mp .field input:focus,
    .mp .field textarea:focus {
        outline: 2px solid rgba(107, 13, 27, .16);
        border-color: var(--maroon);
    }
    .mp .hint {
        margin-top: 6px;
        font-size: 0.7rem;
        line-height: 1.55;
        color: var(--muted);
    }
    .mp .pengajuan-item {
        padding: 10px 0;
        border-top: 1px dashed var(--line);
    }
    .mp .pengajuan-item:first-of-type {
        border-top: none;
        padding-top: 0;
    }
    .mp .pengajuan-atas {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }
    .mp .pengajuan-nomor {
        font-size: 0.76rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }
    .mp .pengajuan-teks {
        margin-top: 3px;
        font-size: 0.73rem;
        line-height: 1.55;
        color: var(--muted);
        overflow-wrap: anywhere;
    }
    .mp .pengajuan-aksi {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 7px;
    }
    .mp .pengajuan-aksi .btn {
        min-height: 34px;
        padding: 6px 11px;
        font-size: 0.72rem;
    }
    .mp .kosong {
        padding: 16px 4px;
        text-align: center;
        font-size: 0.76rem;
        color: var(--muted);
    }
    /* Panel periode pembayaran */
    .mp .periode-form {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr) auto;
        gap: 9px 10px;
        align-items: end;
        margin-bottom: 8px;
        padding: 10px 12px;
        border: 1px dashed var(--line);
        border-radius: 11px;
        background: #fdfbfa;
    }
    .mp .periode-list {
        display: grid;
        gap: 8px;
    }
    .mp .periode-item {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 9px 11px;
        border: 1px solid var(--border);
        border-radius: 11px;
        background: #fff;
    }
    .mp .periode-item--aktif {
        border-color: #e3c9cf;
        background: linear-gradient(135deg, #fff, var(--maroon-tint));
    }
    .mp .periode-info {
        min-width: 0;
        flex: 1 1 220px;
    }
    .mp .periode-nama {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }
    .mp .periode-aksi {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .mp .periode-aksi .btn {
        min-height: 34px;
        padding: 6px 11px;
        font-size: 0.72rem;
    }
    .mp .badge.dibuka {
        background: #dcfce7;
        color: #15803d;
    }
    .mp .badge.terjadwal {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .mp .badge.ditutup {
        background: #f3f4f6;
        color: #4b5563;
    }
    /* Modal */
    .mp-modal {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: none;
        align-items: flex-end;
        justify-content: center;
        padding: 0;
        background: rgba(33, 10, 14, .48);
    }
    .mp-modal.terbuka {
        display: flex;
    }
    .mp-modal .kotak {
        width: 100%;
        max-width: 640px;
        max-height: 92vh;
        overflow-y: auto;
        overscroll-behavior: contain;
        padding: 18px 18px 22px;
        border-radius: 18px 18px 0 0;
        background: #fff;
    }
    .mp-modal .tutup-baris {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }
    .mp-modal h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
    }
    .mp-modal .tutup {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border: 1px solid var(--line);
        border-radius: 9px;
        background: #fff;
        color: var(--muted);
        cursor: pointer;
        flex-shrink: 0;
    }
    .mp-modal .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 12px;
        margin-bottom: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #faf7f6;
    }
    .mp-modal .info-label {
        display: block;
        font-size: 0.63rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #9b8a8e;
    }
    .mp-modal .info-nilai {
        display: block;
        margin-top: 2px;
        font-size: 0.78rem;
        font-weight: 600;
        overflow-wrap: anywhere;
    }
    .mp-modal .seksi {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed var(--line);
    }
    .mp-modal .seksi h4 {
        margin: 0 0 7px;
        font-size: 0.8rem;
        font-weight: 800;
    }
    .mp-modal .aksi-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px;
    }
    .mp-modal .aksi-grid .btn {
        width: 100%;
    }
    .mp-modal .riwayat-baris {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        padding: 6px 0;
        border-bottom: 1px solid #f5eeeb;
        font-size: 0.75rem;
    }
    .mp-modal .riwayat-baris:last-child {
        border-bottom: none;
    }
    .mp-modal .aktivitas {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .mp-modal .aktivitas li {
        padding: 7px 0 7px 14px;
        border-left: 2px solid var(--line);
        font-size: 0.73rem;
        line-height: 1.5;
        color: var(--text-secondary);
        overflow-wrap: anywhere;
    }
    /*
     * FIX TAMPILAN MODAL DETAIL PEMBAYARAN
     * Modal berada di luar .mp, sehingga CSS .mp .field / .btn / .badge
     * tidak diterapkan. Semua aturan di bawah khusus untuk .mp-modal.
     * Tidak mengubah ID, route, atribut form, atau JavaScript pembayaran.
     */
    .mp-modal,
    .mp-modal *,
    .mp-modal *::before,
    .mp-modal *::after {
        box-sizing: border-box;
    }
    .mp-modal {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--ink, #111827);
    }
    .mp-modal .kotak {
        max-width: 680px;
        box-shadow: 0 24px 70px rgba(30, 10, 16, .22);
        scrollbar-width: thin;
        scrollbar-color: #d5c4c8 transparent;
    }
    .mp-modal .tutup-baris {
        margin-bottom: 14px;
    }
    .mp-modal h3 {
        font-size: 1.08rem;
        line-height: 1.45;
        color: #1f2937;
    }
    .mp-modal .tutup:hover {
        border-color: #e1bac4;
        background: #faf1f3;
        color: var(--maroon, #6e001b);
    }
    .mp-modal .info-grid {
        gap: 12px 16px;
        padding: 14px 16px;
        border: 1px solid #f0e6e8;
        background: #faf7f8;
    }
    .mp-modal .info-label {
        margin-bottom: 3px;
        line-height: 1.35;
    }
    .mp-modal .info-nilai {
        font-size: .79rem;
        line-height: 1.5;
    }
    .mp-modal .seksi {
        margin-top: 16px;
        padding-top: 15px;
        border-top: 1px solid #ede1e4;
    }
    .mp-modal .seksi h4 {
        margin-bottom: 11px;
        font-size: .85rem;
        color: #24212a;
    }
    /* Form nominal dan catatan sebelumnya tidak menerima styling .mp .field. */
    .mp-modal .field label {
        display: block;
        margin-bottom: 6px;
        color: #41434b;
        font-size: .74rem;
        font-weight: 700;
        line-height: 1.4;
    }
    .mp-modal .field input,
    .mp-modal .field textarea {
        display: block;
        width: 100%;
        min-width: 0;
        min-height: 40px;
        padding: 9px 12px;
        border: 1px solid #dac8ce;
        border-radius: 9px;
        background: #fff;
        color: #1f2937;
        font: 500 .81rem/1.5 'Plus Jakarta Sans', sans-serif;
        transition: border-color .16s ease, box-shadow .16s ease;
    }
    .mp-modal .field textarea {
        min-height: 82px;
        resize: vertical;
    }
    .mp-modal .field textarea::placeholder,
    .mp-modal .field input::placeholder {
        color: #96939a;
        font-weight: 400;
    }
    .mp-modal .field input:focus,
    .mp-modal .field textarea:focus {
        outline: none;
        border-color: var(--maroon, #6e001b);
        box-shadow: 0 0 0 3px rgba(107, 13, 27, .11);
    }
    .mp-modal .hint {
        margin-top: 9px;
        color: #6b7280;
        font-size: .72rem;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }
    .mp-modal .sub {
        display: block;
        margin-top: 3px;
        color: #6b7280;
        font-size: .74rem;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }
    .mp-modal .tautan-bukti {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: var(--maroon, #6e001b);
        font-size: .77rem;
        font-weight: 700;
        text-decoration: none;
    }
    .mp-modal .tautan-bukti:hover {
        text-decoration: underline;
    }
    /* Tombol modal: ukuran seragam, 2 kolom di desktop. */
    .mp-modal .aksi-grid {
        gap: 9px;
        margin-top: 14px;
    }
    .mp-modal .btn {
        display: inline-flex;
        width: 100%;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        padding: 9px 12px;
        border: 1px solid var(--maroon, #6e001b);
        border-radius: 9px;
        background: var(--maroon, #6e001b);
        color: #fff;
        font: 700 .76rem/1.4 'Plus Jakarta Sans', sans-serif;
        text-align: center;
        cursor: pointer;
        transition: background .15s ease, border-color .15s ease;
    }
    .mp-modal .btn:hover:not(:disabled) {
        background: var(--maroon-dark, #580015);
    }
    .mp-modal .btn--hijau {
        background: #15803d;
        border-color: #15803d;
    }
    .mp-modal .btn--hijau:hover:not(:disabled) {
        background: #166534;
        border-color: #166534;
    }
    .mp-modal .btn--merah {
        background: #b91c1c;
        border-color: #b91c1c;
    }
    .mp-modal .btn--merah:hover:not(:disabled) {
        background: #991b1b;
        border-color: #991b1b;
    }
    .mp-modal .btn--garis {
        background: #fff;
        color: var(--maroon, #6e001b);
    }
    .mp-modal .btn--garis:hover:not(:disabled) {
        background: #fcf0f3;
    }
    .mp-modal .btn:disabled {
        opacity: .44;
        cursor: not-allowed;
    }
    /* Label status yang dibuat dari data JSON secara dinamis. */
    .mp-modal .badge {
        display: inline-flex;
        align-items: center;
        max-width: 100%;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
        background: #f3f4f6;
        color: #4b5563;
    }
    .mp-modal .badge.lunas,
    .mp-modal .badge.verifikasi-diterima {
        background: #dcfce7;
        color: #166534;
    }
    .mp-modal .badge.menunggu,
    .mp-modal .badge.verifikasi-menunggu {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .mp-modal .badge.rektorat {
        background: #ede9fe;
        color: #5b21b6;
    }
    .mp-modal .badge.ditolak,
    .mp-modal .badge.terlambat,
    .mp-modal .badge.verifikasi-ditolak {
        background: #fee2e2;
        color: #991b1b;
    }
    .mp-modal .badge.belum {
        background: #fef3c7;
        color: #92400e;
    }
    /* Pesan berhasil/gagal di dalam modal tidak lagi kehilangan styling. */
    .mp-modal .pesan-json {
        display: none;
        margin: 0 0 12px;
        padding: 10px 12px;
        border-radius: 9px;
        font-size: .76rem;
        font-weight: 600;
        line-height: 1.55;
        overflow-wrap: anywhere;
    }
    .mp-modal .pesan-json.sukses {
        display: block;
        background: #eef7f0;
        border: 1px solid #cfe6d5;
        color: #22653a;
    }
    .mp-modal .pesan-json.gagal {
        display: block;
        background: #fdeef0;
        border: 1px solid #f2ccd2;
        color: #8a1224;
    }
    .mp-modal .riwayat-baris {
        gap: 10px;
        padding: 8px 0;
        font-size: .77rem;
    }
    .mp-modal .aktivitas li {
        font-size: .75rem;
    }
    @media (max-width: 640px) {
        .mp-modal .kotak {
            padding: 18px 16px 22px;
        }
        .mp-modal .info-grid {
            padding: 12px 13px;
        }
    }
    .mp .pesan-json {
        margin-top: 8px;
        padding: 9px 11px;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 600;
        line-height: 1.5;
        display: none;
    }
    .mp .pesan-json.sukses {
        display: block;
        background: #eef7f0;
        border: 1px solid #cfe6d5;
        color: #22653a;
    }
    .mp .pesan-json.gagal {
        display: block;
        background: #fdeef0;
        border: 1px solid #f2ccd2;
        color: #8a1224;
    }
    @media (min-width: 641px) {
        .mp-modal {
            align-items: center;
            padding: 20px;
        }
        .mp-modal .kotak {
            border-radius: 16px;
            padding: 20px 22px 24px;
        }
    }
    @media (max-width: 992px) {
        .mp .dua-kolom {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    @media (max-width: 780px) {
        .mp thead {
            display: none;
        }
        .mp table,
        .mp tbody,
        .mp tr,
        .mp td {
            display: block;
            width: 100%;
        }
        .mp tbody tr {
            margin-bottom: 9px;
            padding: 10px 11px;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: #fff;
        }
        .mp tbody td {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 4px 0;
            border-bottom: none;
            text-align: right;
        }
        .mp tbody td::before {
            content: attr(data-label);
            flex: 0 0 40%;
            text-align: left;
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .mp tbody td[data-label="Mahasiswa"] {
            display: block;
            text-align: left;
        }
        .mp tbody td[data-label="Mahasiswa"]::before {
            display: none;
        }
        .mp .tabel-gulir {
            overflow-x: visible;
        }
    }
    @media (max-width: 640px) {
        .mp .jadwal-baris,
        .mp .periode-form,
        .mp-modal .info-grid,
        .mp-modal .aksi-grid {
            grid-template-columns: minmax(0, 1fr);
        }
        .mp .toolbar select {
            flex: 1 1 100%;
        }
        .mp .stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* ==================================================================
       SIMAHAD — FINANCE DESK / MUSYRIFAH
       Penyempurnaan visual saja; form, data, route, JS, dan validasi tetap asli.
       Container query menjaga susunan saat sidebar dibuka atau ditutup.
       ================================================================== */
    .mp, .mp-modal {
        --ink:#24212a; --muted:#82767c; --text-secondary:#6f6369;
        --maroon:#800b22; --maroon-dark:#65071a; --maroon-tint:#fff4f5;
        --border:#eadde1; --line:#ece1e4;
        font-family:'Plus Jakarta Sans',Poppins,Arial,sans-serif;
    }
    .mp {
        display:block !important;
        clear:both;
        position:relative;
        width:100% !important;
        max-width:100% !important;
        min-width:0 !important;
        margin:0 !important;
        padding:0 0 26px !important;
        container-type:inline-size;
        overflow:visible;
        color:var(--ink);
    }
    .mp > * { min-width:0; }
    .mp-breadcrumb {
        display:flex; align-items:center; flex-wrap:wrap; gap:6px;
        margin:0 0 12px; color:#94878e; font-size:12px;
    }
    .mp-breadcrumb a { color:#800b22; text-decoration:none; font-weight:600; }
    .mp-breadcrumb a:hover { text-decoration:underline; }
    .mp .page-head {
        width:100%; align-items:center; gap:12px 20px;
        padding:0 0 20px; margin:0 0 24px;
        border-bottom:1px solid #e9cfd5;
    }
    .mp .page-head > div { flex:1 1 460px; min-width:0; }
    .mp .page-head h1 { color:#201820; font-size:clamp(21px,2.4vw,28px); font-weight:800; letter-spacing:-.035em; line-height:1.22; }
    .mp .page-head p { margin-top:7px; max-width:980px; font-size:12.5px; color:#867a80; line-height:1.7; }
    .mp .btn {
        display:inline-flex; align-items:center; justify-content:center;
        min-height:39px; gap:6px; padding:8px 14px;
        font-size:12px; line-height:1.35; font-weight:700;
        border:1px solid var(--maroon); border-radius:9px;
        background:var(--maroon); color:#fff;
        box-shadow:none; text-decoration:none; white-space:normal;
        transition:background-color .17s,border-color .17s,box-shadow .17s;
    }
    .mp .btn:hover:not(:disabled) { background:var(--maroon-dark); border-color:var(--maroon-dark); }
    .mp .btn:focus-visible, .mp-modal button:focus-visible, .mp-modal a:focus-visible {
        outline:3px solid rgba(201,169,106,.45); outline-offset:2px;
    }
    .mp .btn--garis { border-color:#e5cfd5; color:#800b22; background:#fff; }
    .mp .btn--garis:hover:not(:disabled) { border-color:#c79ca8; color:#800b22; background:#fff7f8; }
    .mp .btn--hijau { background:#137a47; border-color:#137a47; }
    .mp .btn--hijau:hover:not(:disabled) { background:#0e6339; border-color:#0e6339; }
    .mp .btn--merah { background:#b42332; border-color:#b42332; }
    .mp .btn--merah:hover:not(:disabled) { background:#951e2b; border-color:#951e2b; }
    .mp .page-head #btnMuatUlang { flex:0 0 auto; }

    /* Lima statistik ringkas tepat di atas daftar pembayaran. */
    .mp .stat-grid {
        width:100%; display:grid; grid-template-columns:repeat(3,minmax(0,1fr));
        gap:14px; margin:0 0 24px;
    }
    .mp .stat, .mp .stat--maroon {
        position:relative; display:flex; flex-direction:column; align-items:flex-start; justify-content:center;
        min-width:0; min-height:99px; padding:18px 16px 17px 70px;
        border:1px solid var(--border); border-radius:14px;
        background:#fff; box-shadow:0 3px 15px rgba(69,21,32,.025);
    }
    .mp .stat .stat-icon {
        position:absolute; top:21px; left:17px; display:grid; place-items:center;
        width:38px; height:38px; border-radius:11px;
        background:#f9ecef; color:#800b22;
    }
    .mp .stat:nth-child(2) .stat-icon { background:#f3f0fa; color:#72529e; }
    .mp .stat:nth-child(3) .stat-icon { background:#fff3e0; color:#ae6b12; }
    .mp .stat:nth-child(4) .stat-icon { background:#eaf8ee; color:#16804d; }
    .mp .stat:nth-child(5) .stat-icon { background:#f9ecef; color:#800b22; }
    .mp .stat-icon svg { width:20px; height:20px; }
    .mp .stat .label {
        color:#81777f; font-size:11.5px; font-weight:600;
        text-transform:none; letter-spacing:0; line-height:1.45;
    }
    .mp .stat .nilai, .mp .stat--maroon .nilai {
        margin-top:4px; font-size:clamp(17px,1.8vw,21px);
        font-weight:800; color:#261d23; letter-spacing:-.025em;
        line-height:1.35; overflow-wrap:anywhere;
    }
    .mp .stat:nth-child(4) .nilai { color:#146b3c; }
    .mp .stat:nth-child(3) .nilai { color:#93601d; }

    /* Panel-panel utama tertata penuh; tidak bergeser ke sisi kanan. */
    .mp .panel {
        width:100%; max-width:100%; min-width:0;
        float:none; position:relative; margin:0 0 22px;
        padding:0; border:1px solid var(--border); border-radius:16px;
        background:#fff; box-shadow:0 3px 16px rgba(55,24,32,.025);
        overflow:hidden;
    }
    .mp .panel > :not(.panel-head) { margin-left:18px; margin-right:18px; }
    .mp .panel > .tabel-gulir { margin-left:0; margin-right:0; }
    .mp .panel > .kosong { margin-bottom:17px; }
    .mp .panel-head {
        display:flex; align-items:center; flex-wrap:wrap;
        justify-content:space-between; gap:6px 14px;
        padding:20px 22px 17px; margin:0 0 17px;
        border-bottom:1px solid #f1e7e9;
    }
    .mp .panel-head h2 {
        color:#241e23; font-size:16px; font-weight:800;
        letter-spacing:-.015em; line-height:1.35;
    }
    .mp .panel-head span {
        color:#857a80; font-size:11.5px; line-height:1.6;
        overflow-wrap:anywhere;
    }
    .mp .toolbar {
        display:flex; flex-wrap:wrap; align-items:center;
        gap:9px; margin-bottom:16px !important;
    }
    .mp .toolbar input, .mp .toolbar select {
        min-width:0; height:42px; min-height:42px;
        border:1px solid #e2d3d7; border-radius:9px;
        background:#fff; font-size:12.5px; color:#2b2429;
        outline:none; box-shadow:none;
    }
    .mp .toolbar input { flex:1 1 260px; }
    .mp .toolbar select { flex:0 1 236px; }
    .mp .toolbar input:focus, .mp .toolbar select:focus {
        border-color:#bb8b98; box-shadow:0 0 0 3px rgba(128,11,34,.09);
    }
    .mp .tabel-gulir {
        width:100%; max-width:100%; min-width:0;
        overflow-x:auto; -webkit-overflow-scrolling:touch;
    }
    .mp table { width:100%; min-width:820px; border-collapse:collapse; table-layout:auto; }
    .mp thead th {
        padding:14px 15px; background:#800b22; color:#fff;
        border:0; font-size:11.5px; font-weight:700;
        text-transform:none; letter-spacing:0; text-align:left;
    }
    .mp tbody td {
        padding:15px 14px; border:0; border-bottom:1px solid #f0e7e9;
        color:#4f454a; font-size:12px; line-height:1.5;
        vertical-align:middle; overflow-wrap:anywhere;
    }
    .mp tbody tr:nth-child(even) td { background:#fcfafb; }
    .mp tbody tr:hover td { background:#fff7f8; }
    .mp tbody tr:last-child td { border-bottom:0; }
    .mp .nama { font-size:12.5px; font-weight:750; color:#261b23; }
    .mp .sub { font-size:11px; line-height:1.5; color:#93868d; }
    .mp .nominal { color:#2c2429; font-size:12.5px; font-weight:750; }
    .mp .badge, .mp-modal .badge {
        padding:4px 10px; border-radius:999px; font-size:10.5px;
        line-height:1.45; font-weight:700; letter-spacing:0;
    }
    .mp .tabel-gulir td[data-label="Aksi"] .btn { min-height:33px; padding:6px 12px; }
    .mp .tautan-bukti { text-decoration:none; font-size:11.5px; }

    /* Pembukaan periode — tampilan seperti form operasional Admin. */
    .mp .periode-form {
        grid-template-columns:minmax(0,1.3fr) minmax(0,.95fr) auto;
        gap:12px; padding:15px 16px; margin-bottom:12px;
        border:1px solid #f0e4e7; border-radius:11px; background:#fdfafb;
    }
    .mp .periode-item {
        min-height:67px; padding:13px 15px;
        border-color:#ece1e4; border-radius:10px;
    }
    .mp .periode-item--aktif { border-color:#e2c2cb; background:#fff8f9; }
    .mp .periode-nama { font-size:12.5px; color:#2c2026; }
    .mp .periode-list { padding-bottom:18px; }
    .mp .field label { font-size:11.5px; color:#5d545b; }
    .mp .field input, .mp .field textarea {
        min-width:0; min-height:41px; border:1px solid #dfd2d7; border-radius:9px;
        font-size:12.5px; color:#2e242a;
    }
    .mp .field input:focus, .mp .field textarea:focus {
        outline:none; border-color:#bd8e9a; box-shadow:0 0 0 3px rgba(128,11,34,.08);
    }
    .mp .hint { font-size:11.5px; color:#857880; line-height:1.6; }
    .mp .kosong { color:#90828a; font-size:12px; }
    .mp .periode-info, .mp .periode-item, .mp .periode-list { min-width:0; }

    /* Dua panel administrasi akhir memakai grid yang tidak memaksa overflow. */
    .mp .dua-kolom {
        display:grid; grid-template-columns:minmax(0,1.12fr) minmax(0,.88fr);
        align-items:start; width:100%; max-width:100%; gap:18px;
    }
    .mp .dua-kolom .panel { min-width:0; width:100%; margin:0 0 20px; }
    .mp .jadwal-baris { grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; padding:13px 0; }
    .mp .pengajuan-item { margin:0 18px; padding:15px 0; border-color:#eee3e6; }
    .mp .pengajuan-nomor { font-size:12px; color:#311e27; }
    .mp .pengajuan-teks { font-size:11.5px; line-height:1.65; }
    .mp .pengajuan-aksi { gap:8px; }

    /* Modal tetap berada di luar .mp, jadi aturan dibatasi sendiri. */
    .mp-modal { z-index:3000; background:rgba(24,10,19,.58); backdrop-filter:blur(3px); }
    .mp-modal .kotak {
        width:min(690px,100%); max-height:min(90dvh,900px);
        border:1px solid #ead9df; border-radius:17px;
        background:#fff; box-shadow:0 28px 75px rgba(20,10,20,.23);
        padding:0 !important;
    }
    .mp-modal .tutup-baris {
        position:sticky; top:0; z-index:2;
        display:flex; align-items:center; margin:0; padding:20px 22px 17px;
        border-bottom:1px solid #f0e4e7; background:#fff;
    }
    .mp-modal h3 { color:#251b22; font-size:17px; }
    .mp-modal .tutup { flex:0 0 34px; }
    .mp-modal .kotak > :not(.tutup-baris) { margin-left:22px; margin-right:22px; }
    .mp-modal .kotak > .seksi:last-child { margin-bottom:22px; }
    .mp-modal .kotak > .pesan-json { margin-top:15px; }
    .mp-modal .kotak > .info-grid { margin-top:17px; }
    .mp-modal .info-grid { background:#fdfafb; }
    .mp-modal .seksi h4 { font-size:13px; color:#6a0d22; }
    .mp-modal .info-label { font-size:10.5px; }
    .mp-modal .info-nilai { font-size:12px; }
    .mp-modal .field input, .mp-modal .field textarea { font-size:12.5px; }
    .mp-modal .btn { min-height:39px; font-size:11.5px; }

    /* Lebar container, bukan monitor, menentukan responsivitas sidebar. */
    @container (max-width:1060px) {
        .mp .stat-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .mp .dua-kolom { grid-template-columns:minmax(0,1fr); gap:0; }
    }
    @container (max-width:900px) {
        .mp .periode-form { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .mp .periode-form > :last-child { grid-column:1 / -1; }
        .mp .periode-form > :last-child .btn { width:100%; }
        .mp .tabel-gulir { overflow-x:visible; }
        .mp table, .mp tbody { display:block; min-width:0; width:100%; }
        .mp thead { display:none; }
        .mp tbody tr { display:block; width:100%; margin:0 0 10px; padding:14px 15px;
            border:1px solid #eadddf; border-radius:11px; background:#fff; }
        .mp tbody tr:last-child { margin-bottom:16px; }
        .mp tbody td, .mp tbody tr:nth-child(even) td, .mp tbody tr:hover td {
            display:flex; align-items:flex-start; justify-content:space-between;
            width:100%; min-width:0; padding:6px 0; gap:14px;
            text-align:right; background:transparent; border:0;
            font-size:12px; overflow-wrap:anywhere;
        }
        .mp tbody td::before {
            content:attr(data-label); flex:0 0 36%; min-width:0;
            color:#8d7d86; font-size:10.5px; font-weight:700;
            letter-spacing:0; text-transform:none; text-align:left;
        }
        .mp tbody td:not([data-label]) { display:block; text-align:center; }
        .mp tbody td:not([data-label])::before { content:none; }
        .mp tbody td[data-label="Mahasiswa"] { display:block; padding-bottom:10px;
            margin-bottom:6px; text-align:left; border-bottom:1px solid #f2e9eb; }
        .mp tbody td[data-label="Mahasiswa"]::before { display:none; }
        .mp tbody td[data-label="Aksi"] { align-items:center; }
    }
    @container (max-width:620px) {
        .mp .stat-grid { grid-template-columns:minmax(0,1fr); gap:10px; }
        .mp .stat, .mp .stat--maroon { min-height:82px; padding-top:14px; padding-bottom:14px; }
        .mp .stat .stat-icon { top:15px; }
        .mp .page-head { align-items:flex-start; }
        .mp .page-head #btnMuatUlang { width:100%; }
        .mp .panel-head { padding:17px 15px 14px; }
        .mp .panel > :not(.panel-head) { margin-left:14px; margin-right:14px; }
        .mp .panel > .tabel-gulir { margin-left:0; margin-right:0; }
        .mp .toolbar { flex-direction:column; align-items:stretch; }
        .mp .toolbar input,.mp .toolbar select { width:100%; flex:auto; }
        .mp .periode-form { grid-template-columns:minmax(0,1fr); }
        .mp .periode-form > :last-child { grid-column:auto; }
        .mp .jadwal-baris { grid-template-columns:minmax(0,1fr); }
        .mp .pengajuan-item { margin-left:14px; margin-right:14px; }
    }
    @media (max-width:640px) {
        .mp-modal { align-items:flex-end; padding:0; }
        .mp-modal .kotak { width:100%; max-height:92dvh; border-radius:16px 16px 0 0; }
        .mp-modal .tutup-baris { padding:17px 16px; }
        .mp-modal .kotak > :not(.tutup-baris) { margin-left:15px; margin-right:15px; }
        .mp-modal .info-grid,.mp-modal .aksi-grid { grid-template-columns:minmax(0,1fr); }
    }


    /* ============================================================
       Perapian final Pembayaran Musyrifah, 19 November 2026
       1. Lima ringkasan dalam satu baris desktop
       2. Daftar Pembayaran menjadi panel pertama setelah ringkasan
       3. Panel Periode ditampilkan setelah tabel
       4. Desain responsif relatif ke lebar konten/sidebar
       ============================================================ */
    .mp .page-head {
        padding-bottom: 13px;
        margin-bottom: 17px;
    }
    .mp .stat-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 11px;
        margin-bottom: 16px;
    }
    .mp .stat, .mp .stat--maroon {
        min-height: 88px;
        padding: 13px 11px 13px 55px;
        border-radius: 12px;
    }
    .mp .stat .stat-icon {
        width: 33px;
        height: 33px;
        top: 17px;
        left: 12px;
        border-radius: 9px;
    }
    .mp .stat-icon svg { width: 18px; height: 18px; }
    .mp .stat .label { font-size: 10.5px; line-height: 1.35; }
    .mp .stat .nilai, .mp .stat--maroon .nilai {
        font-size: clamp(14px, 1.25vw, 19px);
        line-height: 1.3;
        letter-spacing: -.03em;
    }
    .mp .panel { margin-bottom: 15px; }
    .mp .panel-head {
        padding: 13px 17px 11px;
        margin-bottom: 12px;
    }
    .mp .panel-head h2 { font-size: 15px; }
    .mp .panel-head span { max-width: 100%; }
    .mp .mp-panel-heading { min-width: 0; }
    .mp .mp-panel-heading p {
        margin: 3px 0 0;
        color: #877c81;
        font-size: 11.5px;
        line-height: 1.5;
    }
    .mp .toolbar { margin-bottom: 12px !important; }
    .mp .periode-form { padding: 11px 12px; margin-bottom: 8px; }
    .mp .periode-list { padding-bottom: 12px; }
    .mp .tabel-gulir { margin-top: 0; }
    .mp .tabel-gulir table { min-width: 780px; }
    .mp .tabel-gulir thead th { padding: 12px 14px; }
    .mp .tabel-gulir tbody td { padding: 11px 14px; }
    .mp #daftar-pembayaran { margin-top: 0; }
    .mp #periode-pembayaran { margin-top: 0; }
    @container (max-width: 1199px) {
        .mp .stat-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @container (max-width: 740px) {
        .mp .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .mp .stat-grid .stat:last-child { grid-column: 1 / -1; }
        .mp .stat, .mp .stat--maroon { padding-left: 53px; }
        .mp .stat .nilai, .mp .stat--maroon .nilai { font-size: 16px; }
        .mp .panel-head { padding: 13px 14px 10px; }
        .mp .tabel-gulir table { min-width: 0; }
        .mp .tabel-gulir tbody td { padding: 6px 0; }
    }
    @container (max-width: 350px) {
        .mp .stat-grid { grid-template-columns: 1fr; }
    }

    /* SIMAHAD | Revisi tata letak Pembayaran Musyrifah
       Urutan: Ringkasan -> Periode -> Jadwal/Pengajuan -> Daftar Pembayaran.
       Berlaku pada lebar konten, termasuk saat sidebar dibuka. */
    .mp .stat-grid {
        grid-template-columns:repeat(5,minmax(0,1fr));
        gap:9px;
        margin-bottom:13px;
    }
    .mp .stat, .mp .stat--maroon {
        min-height:72px;
        padding:10px 10px 10px 49px;
        border-radius:11px;
        box-shadow:0 2px 8px rgba(65,23,31,.025);
    }
    .mp .stat .stat-icon {
        top:50%; left:11px; transform:translateY(-50%);
        width:29px; height:29px; border-radius:8px;
    }
    .mp .stat-icon svg { width:16px; height:16px; }
    .mp .stat .label { font-size:10px; line-height:1.32; }
    .mp .stat .nilai, .mp .stat--maroon .nilai {
        font-size:clamp(13px,1.2vw,17px);
        margin-top:3px; line-height:1.22;
    }
    .mp .page-head { margin-bottom:14px; padding-bottom:12px; }
    .mp .page-head #btnMuatUlang {
        min-height:33px; padding:6px 11px; font-size:11px; width:auto;
        flex:0 0 auto;
    }
    .mp .panel { margin-bottom:12px; border-radius:13px; }
    .mp .panel-head { padding:11px 16px 10px; margin-bottom:9px; }
    .mp .panel-head h2 { font-size:14px; }
    .mp .panel-head span { font-size:10.5px; }
    .mp .periode-form {
        padding:10px 12px; margin-bottom:7px;
        gap:8px; border-radius:9px;
    }
    .mp .periode-form .field input { min-height:35px; height:35px; }
    .mp .periode-form .field .btn { min-height:35px; padding:6px 12px; }
    .mp .panel > .hint { line-height:1.4; }
    .mp .panel > .kosong { padding:8px 4px 15px; }
    .mp .dua-kolom { gap:12px; }
    .mp .dua-kolom .panel { margin-bottom:12px; }
    .mp .dua-kolom .panel > .kosong { padding:14px 10px 16px; }
    .mp .toolbar { margin-bottom:11px !important; }
    .mp #daftar-pembayaran { margin-top:0; }
    @container (max-width:1150px) {
        .mp .stat-grid { grid-template-columns:repeat(3,minmax(0,1fr)); }
    }
    @container (max-width:740px) {
        .mp .stat-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .mp .stat-grid .stat:last-child { grid-column:auto; }
        .mp .stat,.mp .stat--maroon { min-height:72px; padding-left:46px; }
        .mp .stat .stat-icon { left:9px; }
        .mp .stat .nilai,.mp .stat--maroon .nilai { font-size:14px; }
        .mp .panel-head { padding:11px 13px 9px; }
        .mp .page-head #btnMuatUlang { width:auto; }
    }
    @container (max-width:350px) {
        .mp .stat-grid { grid-template-columns:minmax(0,1fr); }
    }

    /* FIX: Panel Periode Pembayaran — satu area isi yang konsisten dan tidak berantakan. */
    .mp #periode-pembayaran {
        width:100%; min-width:0;
    }
    .mp #periode-pembayaran .panel-head {
        margin-bottom:0;
        padding:12px 18px;
    }
    .mp #periode-pembayaran > .periode-panel-body {
        width:100%; min-width:0;
        margin:0;
        padding:15px 18px 16px;
    }
    .mp #periode-pembayaran .periode-form {
        display:grid;
        grid-template-columns:minmax(0,1.35fr) minmax(0,1fr) auto;
        gap:12px;
        align-items:end;
        width:100%;
        margin:0;
        padding:0;
        border:0;
        border-radius:0;
        background:transparent;
    }
    .mp #periode-pembayaran .periode-form .field {
        min-width:0;
        margin:0;
        grid-column:auto;
    }
    .mp #periode-pembayaran .periode-form .field label {
        display:block;
        margin:0 0 7px;
        color:#63585e;
        font-size:11.5px;
        font-weight:700;
        line-height:1.35;
    }
    .mp #periode-pembayaran .periode-form .field input {
        display:block;
        width:100%;
        min-width:0;
        height:40px;
        min-height:40px;
        margin:0;
        padding:0 12px;
        border:1px solid #e2d2d6;
        border-radius:9px;
        background:#fff;
        font-size:12.5px;
        line-height:1.35;
    }
    .mp #periode-pembayaran .periode-form .field .btn {
        display:inline-flex;
        width:auto;
        min-height:40px;
        height:40px;
        margin:0;
        padding:0 17px;
        white-space:nowrap;
        font-size:12px;
        border-radius:9px;
    }
    .mp #periode-pembayaran .periode-note {
        display:flex;
        align-items:flex-start;
        gap:9px;
        min-width:0;
        margin:12px 0 0;
        padding:0;
        color:#81757b;
        font-size:11.5px;
        line-height:1.6;
        text-align:left;
    }
    .mp #periode-pembayaran .periode-note svg {
        flex:0 0 15px;
        width:15px;
        height:15px;
        margin-top:2px;
        color:#996477;
    }
    .mp #periode-pembayaran .periode-note span { min-width:0; }
    .mp #periode-pembayaran .periode-empty {
        display:flex;
        align-items:center;
        gap:11px;
        min-width:0;
        margin:13px 0 0;
        padding:11px 12px;
        border:1px solid #f0e4e7;
        border-radius:9px;
        background:#fcfafb;
        color:#877b82;
        font-size:11.5px;
        line-height:1.5;
        text-align:left;
    }
    .mp #periode-pembayaran .periode-empty svg {
        flex:0 0 19px;
        width:19px;
        height:19px;
        color:#9b8190;
    }
    .mp #periode-pembayaran .periode-list {
        display:grid;
        gap:9px;
        min-width:0;
        margin:13px 0 0;
        padding:0;
    }
    .mp #periode-pembayaran .periode-item { min-width:0; }
    @container (max-width:850px) {
        .mp #periode-pembayaran .periode-form {
            grid-template-columns:repeat(2,minmax(0,1fr));
        }
        .mp #periode-pembayaran .periode-form > .field:last-child {
            grid-column:1 / -1;
        }
        .mp #periode-pembayaran .periode-form > .field:last-child .btn {
            width:100%;
        }
    }
    @container (max-width:560px) {
        .mp #periode-pembayaran .panel-head { padding:12px 14px; }
        .mp #periode-pembayaran > .periode-panel-body { padding:14px; }
        .mp #periode-pembayaran .periode-form {
            grid-template-columns:minmax(0,1fr);
            gap:10px;
        }
        .mp #periode-pembayaran .periode-form > .field:last-child {
            grid-column:auto;
        }
        .mp #periode-pembayaran .periode-form > .field:last-child .btn { width:100%; }
        .mp #periode-pembayaran .periode-note { font-size:11px; }
        .mp #periode-pembayaran .periode-empty { align-items:flex-start; font-size:11px; }
    }
</style>
@php
    $pilihanStatus = [
        'semua' => 'Semua status',
        'menunggu' => 'Menunggu verifikasi bukti',
        'menunggu_konfirmasi' => 'Menunggu konfirmasi dana',
        'belum_lunas' => 'Belum lunas',
        'lunas' => 'Lunas',
    ];
    // Pembukaan dan pengaturan periode hanya untuk Musyrifah.
    $bolehAturPeriode = strtolower(trim((string) auth()->user()?->role)) === 'musyrifah';
@endphp
<div class="mp">
    <nav class="mp-breadcrumb" aria-label="Navigasi halaman">
        <a href="{{ route('musyrifah.dashboard') }}">Dashboard</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Pembayaran Mahasiswa</span>
    </nav>
    <div class="page-head">
        <div>
            <h1>Pembayaran Mahasiswa</h1>
            <p>
                Periksa bukti transfer,  atur jadwal angsuran, dan tindak lanjuti pengajuan keterlambatan.
                @if ($pengaturan && $pengaturan->sudahDibuka())
                    Periode aktif: <strong>{{ $pengaturan->periode }}</strong>.
                @elseif ($pengaturan)
                    Periode <strong>{{ $pengaturan->periode }}</strong> dijadwalkan dibuka {{ $pengaturan->periodeDibukaTeks() }}.
                @else
                    <strong>Belum ada periode pembayaran aktif.</strong>
                @endif
            </p>
        </div>
        <button type="button" class="btn btn--garis" id="btnMuatUlang" title="Ambil data pembayaran terbaru dari server" aria-label="Segarkan data pembayaran">Segarkan Data</button>
    </div>
    <div class="stat-grid">
        <div class="stat stat--maroon">
            <span class="stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M3 10h18M7 3h10"/><path d="M16 16h2"/></svg></span>
            <span class="label">Total Tagihan</span>
            <span class="nilai">{{ $totalNominalTagihanRupiah }}</span>
        </div>
        <div class="stat">
            <span class="stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3.5 20v-2a5.5 5.5 0 0 1 11 0v2M16 5a3 3 0 0 1 0 6M17 14a4 4 0 0 1 3.5 4v2"/></svg></span>
            <span class="label">Mahasiswa</span>
            <span class="nilai">{{ $totalMahasiswa }}</span>
        </div>
        <div class="stat">
            <span class="stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
            <span class="label">Menunggu Bukti</span>
            <span class="nilai">{{ $totalMenungguVerifikasi }}</span>
        </div>
        <div class="stat">
            <span class="stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span>
            <span class="label">Lunas</span>
            <span class="nilai">{{ $totalLunas }}</span>
        </div>
        <div class="stat">
            <span class="stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18M5 9V6l7-3 7 3v3M6 9v10M10 9v10M14 9v10M18 9v10M3 21h18"/></svg></span>
            <span class="label">Dana Terkonfirmasi</span>
            <span class="nilai">{{ $totalNominalTerkumpulRupiah }}</span>
        </div>
    </div>
    <div class="panel mp-panel-periode" id="periode-pembayaran">
        <div class="panel-head">
            <h2>Periode Pembayaran</h2>
            <span>Pembukaan dan pengaturan periode adalah kewenangan Musyrifah</span>
        </div>
        <div class="periode-panel-body">
        @if ($bolehAturPeriode)
            <form class="periode-form" action="{{ route('musyrifah.pembayaran.periode.simpan') }}" method="POST">
                @csrf
                <div class="field">
                    <label for="periodeBaru">Nama periode</label>
                    <input type="text" id="periodeBaru" name="periode" placeholder="Contoh: Januari 2027" maxlength="60" required>
                </div>
                <div class="field">
                    <label for="periodeMulai">Tanggal mulai</label>
                    <input type="date" id="periodeMulai" name="tanggal_mulai" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="field">
                    <button type="submit" class="btn">Buat Periode</button>
                </div>
            </form>
        @else
            <div class="periode-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
                <span>Pembukaan dan pengaturan periode pembayaran hanya dapat dilakukan Musyrifah.</span>
            </div>
        @endif
        @if (count($daftarPeriode) > 0)
            <div class="periode-list">
                @foreach ($daftarPeriode as $periode)
                    <div class="periode-item {{ $periode['aktif'] ? 'periode-item--aktif' : '' }}">
                        <div class="periode-info">
                            <span class="periode-nama">
                                {{ $periode['periode'] }}
                                <span class="badge {{ $periode['kelas_status'] }}">{{ $periode['status_label'] }}</span>
                            </span>
                            <span class="sub">
                                Mulai {{ $periode['tanggal_mulai_teks'] }} &middot; {{ $periode['jumlah_tagihan'] }} tagihan
                                @if ($periode['jatuh_tempo_teks'] !== '-')
                                    &middot; jatuh tempo tahap pertama {{ $periode['jatuh_tempo_teks'] }}
                                @endif
                            </span>
                        </div>
                        <div class="periode-aksi">
                            @if ($bolehAturPeriode)
                                @if ($periode['aktif'])
                                    <form action="{{ route('musyrifah.pembayaran.periode.tutup', $periode['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn--garis">Tutup Periode</button>
                                    </form>
                                @else
                                    <form action="{{ route('musyrifah.pembayaran.periode.buka', $periode['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn--hijau">Buka Periode</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="periode-empty" role="status">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18M8 15h8"/></svg>
                <span>Belum ada periode pembayaran. Isi formulir di atas untuk membuat periode pertama.</span>
            </div>
        @endif
        </div>
    </div>
    <div class="dua-kolom">
        <div class="panel">
            <div class="panel-head">
                <h2>Jadwal Angsuran</h2>
                <span>Total bawaan Rp5.000.000 per tahun</span>
            </div>
            @if ($bolehAturPeriode && $periodeKerja && count($jadwal) > 0)
                <form action="{{ route('musyrifah.pembayaran.jadwal') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pengaturan_id" value="{{ $periodeKerja->id }}">
                    <div class="field" style="margin-bottom:10px;">
                        <label for="jadwalTanggalMulai">Tanggal mulai periode {{ $periodeKerja->periode }}</label>
                        <input type="date"
                            id="jadwalTanggalMulai"
                            name="tanggal_mulai"
                            value="{{ $periodeKerja->periode_dibuka_pada?->toDateString() }}"
                            required>
                    </div>
                    @foreach ($jadwal as $indeks => $baris)
                        <div class="jadwal-baris">
                            <input type="hidden" name="angsuran[{{ $indeks }}][angsuran_ke]" value="{{ $baris['angsuran_ke'] }}">
                            <div class="field">
                                <label for="jadwal-nama-{{ $baris['angsuran_ke'] }}">Nama tahap {{ $baris['angsuran_ke'] }}</label>
                                <input type="text"
                                    id="jadwal-nama-{{ $baris['angsuran_ke'] }}"
                                    name="angsuran[{{ $indeks }}][nama_tahap]"
                                    value="{{ $baris['nama_tahap'] }}"
                                    maxlength="80">
                            </div>
                            <div class="field">
                                <label for="jadwal-nominal-{{ $baris['angsuran_ke'] }}">Nominal (Rp)</label>
                                <input type="number"
                                    id="jadwal-nominal-{{ $baris['angsuran_ke'] }}"
                                    name="angsuran[{{ $indeks }}][nominal]"
                                    value="{{ (int) $baris['nominal'] }}"
                                    min="{{ $baris['angsuran_ke'] === 1 ? 1000000 : 1 }}"
                                    step="1000"
                                    required>
                            </div>
                            <div class="field">
                                <label for="jadwal-tempo-{{ $baris['angsuran_ke'] }}">Jatuh tempo</label>
                                <input type="date"
                                    id="jadwal-tempo-{{ $baris['angsuran_ke'] }}"
                                    name="angsuran[{{ $indeks }}][jatuh_tempo]"
                                    value="{{ $baris['jatuh_tempo'] }}"
                                    required>
                            </div>
                            <div class="field">
                                <label for="jadwal-reminder-{{ $baris['angsuran_ke'] }}">Pengingat (hari sebelum)</label>
                                <input type="number"
                                    id="jadwal-reminder-{{ $baris['angsuran_ke'] }}"
                                    name="angsuran[{{ $indeks }}][reminder_hari]"
                                    value="{{ $baris['reminder_hari'] }}"
                                    min="0"
                                    max="30"
                                    required>
                            </div>
                        </div>
                    @endforeach
                    <div class="hint">
                        Angsuran pertama minimal Rp1.000.000. Total biaya umum Rp5.000.000 per tahun dan dapat berbeda sesuai kategori mahasiswa.
                        Perubahan jadwal diterapkan pada tagihan yang belum dibayar.
                    </div>
                    <div class="pengajuan-aksi" style="margin-top:10px;">
                        <button type="submit" class="btn">Simpan Periode &amp; Jadwal</button>
                    </div>
                </form>
            @elseif (count($jadwal) > 0)
                @foreach ($jadwal as $baris)
                    <div class="jadwal-baris" style="grid-template-columns: minmax(0, 1fr);">
                        <div class="sub">
                            <strong>{{ $baris['nama_tahap'] }}</strong> &middot; {{ $baris['nominal_rupiah'] }} &middot;
                            jatuh tempo {{ $baris['jatuh_tempo_teks'] }} &middot; pengingat {{ $baris['reminder_hari'] }} hari
                        </div>
                    </div>
                @endforeach
                <div class="hint">Jadwal angsuran hanya dapat diubah oleh Musyrifah.</div>
            @else
                <div class="kosong">Belum ada periode pembayaran. Buat periode pada panel Periode Pembayaran terlebih dahulu.</div>
            @endif
        </div>
        <div class="panel">
            <div class="panel-head">
                <h2>Pengajuan Keterlambatan</h2>
                <span>{{ $totalPengajuan }} menunggu keputusan</span>
            </div>
            <div class="pesan-json" id="pesanPengajuan" role="status"></div>
            @forelse ($pengajuan as $item)
                <div class="pengajuan-item" data-pengajuan="{{ $item['id'] }}">
                    <div class="pengajuan-atas">
                        <span class="pengajuan-nomor">{{ $item['nomor_surat'] }}</span>
                        <span class="badge {{ $item['kelas_status'] === 'disetujui' ? 'lunas' : ($item['kelas_status'] === 'ditolak' ? 'ditolak' : 'menunggu') }}">
                            {{ $item['status_label'] }}
                        </span>
                    </div>
                    <div class="pengajuan-teks">
                        {{ $item['nama'] }} ({{ $item['nim'] }}) &middot; {{ $item['tahap'] }} &middot; {{ $item['nominal_rupiah'] }}<br>
                        Rencana bayar <strong>{{ $item['tanggal_rencana'] }}</strong> &middot; {{ $item['alasan'] }}
                    </div>
                    @if (filled($item['catatan_musyrifah']))
                        <div class="pengajuan-teks">Catatan: {{ $item['catatan_musyrifah'] }}</div>
                    @endif
                    <div class="pengajuan-aksi">
                        <a class="btn btn--garis" href="{{ $item['surat_url'] }}" target="_blank" rel="noopener">Surat PDF</a>
                        @if ($item['menunggu'])
                            <button type="button" class="btn btn--hijau" data-putuskan="setuju" data-id="{{ $item['id'] }}">Setujui</button>
                            <button type="button" class="btn btn--merah" data-putuskan="tolak" data-id="{{ $item['id'] }}">Tolak</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="kosong">Belum ada pengajuan keterlambatan.</div>
            @endforelse
        </div>
    </div>
    <div class="panel mp-panel-pembayaran" id="daftar-pembayaran">
        <div class="panel-head">
            <div class="mp-panel-heading"><h2>Daftar Pembayaran</h2><p>Periksa tagihan dan bukti transfer mahasiswa secara langsung.</p></div>
            <span>Rekening resmi: {{ $rekening['bank'] }} {{ $rekening['nomor'] }} a.n. {{ $rekening['nama'] }}</span>
        </div>
        <div class="toolbar">
            <input type="search" id="cariPembayaran" placeholder="Cari nama atau NIM mahasiswa..." aria-label="Cari nama atau NIM mahasiswa">
            <select id="filterStatus" aria-label="Filter status pembayaran">
                @foreach ($pilihanStatus as $nilai => $label)
                    <option value="{{ $nilai }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="pesan-json" id="pesanDaftar" role="status"></div>
        <div class="tabel-gulir">
            <table>
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Tahap</th>
                        <th>Nominal</th>
                        <th>Jatuh Tempo</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabelPembayaran">
                    @forelse ($daftarPembayaran as $pembayaran)
                        <tr data-cari="{{ strtolower($pembayaran['nama'].' '.$pembayaran['nim']) }}"
                            data-status="{{ $pembayaran['raw_status'] }}">
                            <td data-label="Mahasiswa">
                                <span class="nama">{{ $pembayaran['nama'] }}</span>
                                <span class="sub">{{ $pembayaran['nim'] }} &middot; {{ $pembayaran['periode'] }}</span>
                            </td>
                            <td data-label="Tahap">{{ $pembayaran['tahap'] }}</td>
                            <td data-label="Nominal"><span class="nominal">{{ $pembayaran['nominal_rupiah'] }}</span></td>
                            <td data-label="Jatuh Tempo">
                                {{ $pembayaran['jatuh_tempo'] }}
                                @if ($pembayaran['kelas_status'] === 'terlambat')
                                    <span class="badge terlambat">Lewat</span>
                                @endif
                            </td>
                            <td data-label="Bukti">
                                @if ($pembayaran['bukti_url'])
                                    <a class="tautan-bukti" href="{{ $pembayaran['bukti_url'] }}" target="_blank" rel="noopener">Lihat</a>
                                    <span class="sub">{{ $pembayaran['verifikasi_label'] }}</span>
                                @else
                                    <span class="sub">Belum ada</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                <span class="badge {{ $pembayaran['kelas_status'] }}">{{ $pembayaran['raw_status'] === 'menunggu_konfirmasi' ? 'Menunggu konfirmasi dana' : $pembayaran['status'] }}</span>
                            </td>
                            <td data-label="Aksi">
                                <button type="button"
                                    class="btn btn--garis"
                                    data-detail="{{ route('musyrifah.pembayaran.detail', $pembayaran['id']) }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="kosong">Belum ada tagihan pembayaran.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mp-modal" id="modalDetail" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="judulModal">
    <div class="kotak">
        <div class="tutup-baris">
            <div>
                <h3 id="judulModal">Detail Pembayaran</h3>
                <span class="sub" id="mdMahasiswa">-</span>
            </div>
            <button type="button" class="tutup" data-tutup-modal aria-label="Tutup detail">&times;</button>
        </div>
        <div class="pesan-json" id="pesanModal" role="status"></div>
        <div class="info-grid">
            <div>
                <span class="info-label">NIM</span>
                <span class="info-nilai" id="mdNim">-</span>
            </div>
            <div>
                <span class="info-label">Kontak</span>
                <span class="info-nilai" id="mdKontak">-</span>
            </div>
            <div>
                <span class="info-label">Tahap</span>
                <span class="info-nilai" id="mdTahap">-</span>
            </div>
            <div>
                <span class="info-label">Jatuh Tempo</span>
                <span class="info-nilai" id="mdJatuhTempo">-</span>
            </div>
            <div>
                <span class="info-label">Status Tagihan</span>
                <span class="info-nilai" id="mdStatus">-</span>
            </div>
            <div>
                <span class="info-label">Pemeriksaan Bukti</span>
                <span class="info-nilai" id="mdVerifikasi">-</span>
            </div>
        </div>
        <div class="seksi">
            <h4>Nominal &amp; Bukti Transfer</h4>
            <div class="field" style="margin-bottom:8px;">
                <label for="mdNominal">Nominal tagihan (Rp)</label>
                <input type="number" id="mdNominal" min="1" step="1000">
                <div class="hint">Gunakan untuk menyesuaikan nominal sesuai kategori mahasiswa. Angsuran pertama minimal Rp1.000.000.</div>
            </div>
            <div id="mdBuktiBaris" style="margin-bottom:8px;"></div>
            <div class="field">
                <label for="mdCatatan">Catatan pemeriksaan (wajib saat menolak bukti)</label>
                <textarea id="mdCatatan" maxlength="500" placeholder="Contoh: nominal transfer tidak sesuai atau bukti tidak terbaca."></textarea>
            </div>
            <div class="aksi-grid">
                <button type="button" class="btn" id="btnTerimaBukti">Terima Bukti</button>
                <button type="button" class="btn btn--merah" id="btnTolakBukti">Tolak Bukti</button>
                <button type="button" class="btn btn--hijau" id="btnLunas">Konfirmasi Dana &rarr; Lunas</button>
                <button type="button" class="btn btn--garis" id="btnBelumLunas">Tandai Belum Lunas</button>
            </div>
            <div class="hint">
                Tandai Lunas hanya setelah dana benar-benar diterima dan diverifikasi oleh petugas pembayaran Ma'had. Menerima bukti tidak otomatis melunasi tagihan.
            </div>
        </div>
        <div class="seksi">
            <h4>Angsuran Mahasiswa</h4>
            <div id="mdRiwayatAngsuran"></div>
        </div>
        <div class="seksi">
            <h4>Pengajuan Keterlambatan</h4>
            <div id="mdPengajuan"></div>
        </div>
        <div class="seksi">
            <h4>Riwayat Aktivitas</h4>
            <ul class="aktivitas" id="mdAktivitas"></ul>
        </div>
    </div>
</div>
@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('modalDetail');
        const token = document.querySelector('meta[name="csrf-token"]');
        let idAktif = null;
        const rupiah = (nilai) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(Number(nilai) || 0));
        function pesan(sel, teks, sukses) {
            const kotak = document.getElementById(sel);
            if (!kotak) {
                return;
            }
            kotak.textContent = teks;
            kotak.classList.remove('sukses', 'gagal');
            kotak.classList.add(sukses ? 'sukses' : 'gagal');
        }
        async function kirim(url, data) {
            const respons = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token ? token.content : '',
                },
                body: JSON.stringify(data || {}),
            });
            let hasil = {};
            try {
                hasil = await respons.json();
            } catch (error) {
                hasil = { success: false, message: 'Respons server tidak dapat dibaca.' };
            }
            if (!respons.ok || hasil.success === false) {
                throw new Error(hasil.message || 'Permintaan gagal diproses.');
            }
            return hasil;
        }
        function muatUlangSetelah(jeda) {
            window.setTimeout(function () {
                window.location.reload();
            }, jeda || 900);
        }
        // Filter tabel
        const cari = document.getElementById('cariPembayaran');
        const filter = document.getElementById('filterStatus');
        const barisTabel = Array.from(document.querySelectorAll('#tabelPembayaran tr[data-cari]'));
        function saring() {
            const kata = (cari ? cari.value : '').toLowerCase().trim();
            const status = filter ? filter.value : 'semua';
            barisTabel.forEach(function (baris) {
                const cocokKata = kata === '' || baris.dataset.cari.indexOf(kata) !== -1;
                const cocokStatus = status === 'semua' || baris.dataset.status === status;
                baris.style.display = cocokKata && cocokStatus ? '' : 'none';
            });
        }
        if (cari) {
            cari.addEventListener('input', saring);
        }
        if (filter) {
            filter.addEventListener('change', saring);
        }
        const btnMuatUlang = document.getElementById('btnMuatUlang');
        if (btnMuatUlang) {
            btnMuatUlang.addEventListener('click', function () {
                window.location.reload();
            });
        }
        // Modal detail
        function bukaModal() {
            modal.classList.add('terbuka');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
        function tutupModal() {
            modal.classList.remove('terbuka');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            idAktif = null;
        }
        modal.querySelectorAll('[data-tutup-modal]').forEach(function (tombol) {
            tombol.addEventListener('click', tutupModal);
        });
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                tutupModal();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('terbuka')) {
                tutupModal();
            }
        });
        function isiBadge(elemen, kelas, teks) {
            elemen.innerHTML = '<span class="badge ' + kelas + '">' + teks + '</span>';
        }
        function tampilkanDetail(data) {
            const p = data.pembayaran;
            const m = data.mahasiswa;
            idAktif = p.id;
            document.getElementById('judulModal').textContent = 'Detail Pembayaran ' + p.tahap;
            document.getElementById('mdMahasiswa').textContent = m.name + ' - ' + m.nim;
            document.getElementById('mdNim').textContent = m.nim;
            document.getElementById('mdKontak').textContent = (m.no_hp || '-') + ' / ' + (m.email || '-');
            document.getElementById('mdTahap').textContent = p.tahap + ' (' + p.periode + ')';
            document.getElementById('mdJatuhTempo').textContent = p.jatuh_tempo;
            isiBadge(document.getElementById('mdStatus'), p.kelas_status, p.raw_status === 'menunggu_konfirmasi' ? 'Menunggu konfirmasi dana' : p.status_label);
            isiBadge(document.getElementById('mdVerifikasi'), 'verifikasi-' + (p.kelas_verifikasi || 'kosong'), p.verifikasi_label);
            document.getElementById('mdNominal').value = Math.round(Number(p.nominal) || 0);
            document.getElementById('mdCatatan').value = p.catatan_verifikasi || '';
            const buktiBaris = document.getElementById('mdBuktiBaris');
            buktiBaris.innerHTML = p.bukti_url
                ? '<a class="tautan-bukti" href="' + p.bukti_url + '" target="_blank" rel="noopener">Buka bukti transfer</a>' +
                    '<span class="sub">' + (p.bukti_nama || '') + (p.tanggal_upload ? ' - diunggah ' + p.tanggal_upload : '') + '</span>'
                : '<span class="sub">Belum ada bukti transfer yang diunggah.</span>';
            const riwayat = document.getElementById('mdRiwayatAngsuran');
            riwayat.innerHTML = (data.riwayat_angsuran || []).map(function (item) {
                return '<div class="riwayat-baris">' +
                    '<span>' + item.tahap + ' - ' + item.nominal_rupiah + '</span>' +
                    '<span class="badge ' + item.kelas_status + '">' + item.status_label + '</span>' +
                    '</div>';
            }).join('') || '<span class="sub">Tidak ada angsuran lain.</span>';
            const pengajuan = document.getElementById('mdPengajuan');
            pengajuan.innerHTML = (data.pengajuan || []).map(function (item) {
                return '<div class="riwayat-baris">' +
                    '<span>' + item.nomor_surat + ' - rencana ' + item.tanggal_rencana + '</span>' +
                    '<span class="badge ' + (item.status === 'disetujui' ? 'lunas' : (item.status === 'ditolak' ? 'ditolak' : 'menunggu')) + '">' + item.status_label + '</span>' +
                    '</div>';
            }).join('') || '<span class="sub">Belum ada pengajuan.</span>';
            const aktivitas = document.getElementById('mdAktivitas');
            aktivitas.innerHTML = (data.riwayat_aktivitas || []).map(function (log) {
                return '<li><strong>' + log.activity + '</strong><br>' + log.description + '<br><em>' + log.time_formatted + '</em></li>';
            }).join('') || '<li>Belum ada aktivitas.</li>';
            const menunggu = p.verifikasi === 'menunggu';
            const lunas = p.raw_status === 'lunas' || p.raw_status === 'paid' || p.raw_status === 'terkonfirmasi';
            document.getElementById('btnTerimaBukti').disabled = !menunggu;
            document.getElementById('btnTolakBukti').disabled = !menunggu;
            document.getElementById('btnLunas').disabled = lunas || menunggu || p.verifikasi === 'ditolak';
            document.getElementById('btnBelumLunas').disabled = !lunas;
            document.getElementById('pesanModal').classList.remove('sukses', 'gagal');
            document.getElementById('pesanModal').textContent = '';
        }
        document.querySelectorAll('[data-detail]').forEach(function (tombol) {
            tombol.addEventListener('click', async function () {
                bukaModal();
                document.getElementById('mdMahasiswa').textContent = 'Memuat data...';
                try {
                    const respons = await fetch(tombol.dataset.detail, {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (!respons.ok) {
                        throw new Error('Detail tidak dapat dimuat.');
                    }
                    tampilkanDetail(await respons.json());
                } catch (error) {
                    pesan('pesanModal', error.message, false);
                }
            });
        });
        async function aksiModal(url, data, suksesPesan) {
            try {
                const hasil = await kirim(url, data);
                pesan('pesanModal', hasil.message || suksesPesan, true);
                muatUlangSetelah();
            } catch (error) {
                pesan('pesanModal', error.message, false);
            }
        }
        const urlVerifikasi = (id) => "{{ url('/musyrifah/pembayaran') }}/" + id + "/verifikasi";
        const urlStatus = (id) => "{{ url('/musyrifah/pembayaran') }}/" + id + "/status";
        const urlNominal = (id) => "{{ url('/musyrifah/pembayaran') }}/" + id + "/nominal";
        document.getElementById('btnTerimaBukti').addEventListener('click', function () {
            if (!idAktif) {
                return;
            }
            aksiModal(urlVerifikasi(idAktif), {
                keputusan: 'terima',
                catatan: document.getElementById('mdCatatan').value,
            }, 'Bukti transfer diterima.');
        });
        document.getElementById('btnTolakBukti').addEventListener('click', function () {
            if (!idAktif) {
                return;
            }
            const catatan = (document.getElementById('mdCatatan').value || '').trim();
            if (catatan.length < 3) {
                pesan('pesanModal', 'Isi catatan/alasan penolakan terlebih dahulu.', false);
                return;
            }
            aksiModal(urlVerifikasi(idAktif), {
                keputusan: 'tolak',
                catatan: catatan,
            }, 'Bukti transfer ditolak.');
        });
        document.getElementById('btnLunas').addEventListener('click', function () {
            if (!idAktif) {
                return;
            }
            if (!window.confirm("Tetapkan Lunas? Pastikan dana benar-benar sudah diterima dan diverifikasi oleh petugas pembayaran Ma'had.")) {
                return;
            }
            aksiModal(urlStatus(idAktif), {
                status: 'lunas',
                catatan: document.getElementById('mdCatatan').value,
            }, 'Status tagihan menjadi Lunas.');
        });
        document.getElementById('btnBelumLunas').addEventListener('click', function () {
            if (!idAktif) {
                return;
            }
            aksiModal(urlStatus(idAktif), {
                status: 'belum_lunas',
                catatan: document.getElementById('mdCatatan').value,
            }, 'Status tagihan dikembalikan menjadi Belum Lunas.');
        });
        document.getElementById('mdNominal').addEventListener('change', function () {
            if (!idAktif) {
                return;
            }
            aksiModal(urlNominal(idAktif), {
                nominal: Number(this.value),
            }, 'Nominal tagihan diperbarui.');
        });
        // Keputusan pengajuan keterlambatan
        document.querySelectorAll('[data-putuskan]').forEach(function (tombol) {
            tombol.addEventListener('click', async function () {
                const id = tombol.dataset.id;
                const keputusan = tombol.dataset.putuskan;
                let catatan = '';
                if (keputusan === 'tolak') {
                    catatan = window.prompt('Alasan penolakan pengajuan keterlambatan:') || '';
                    if (catatan.trim().length < 3) {
                        pesan('pesanPengajuan', 'Alasan penolakan wajib diisi.', false);
                        return;
                    }
                } else if (!window.confirm('Setujui pengajuan? Jatuh tempo akan diperbarui dan status tagihan tetap belum Lunas.')) {
                    return;
                }
                try {
                    const hasil = await kirim("{{ url('/musyrifah/pembayaran/keterlambatan') }}/" + id, {
                        keputusan: keputusan,
                        catatan: catatan,
                    });
                    pesan('pesanPengajuan', hasil.message || 'Pengajuan diproses.', true);
                    muatUlangSetelah();
                } catch (error) {
                    pesan('pesanPengajuan', error.message, false);
                }
            });
        });
    })();
</script>
@endpush
@endsection
