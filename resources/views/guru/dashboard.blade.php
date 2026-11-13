@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@push('styles')
<style>
    /*
        Dashboard Guru memakai gaya sendiri (prefiks gd-) agar tampilan
        dashboard role lain yang memakai partials.dashboard-gaya tidak berubah.
        Palet: maroon #6B0D1B, putih, dan aksen emas #C9A96A.
    */
    .gd {
        --gd-maroon: #6b0d1b;
        --gd-maroon-tua: #52070f;
        --gd-tint: #f8edef;
        --gd-garis: #ecd9dd;
        --gd-garis-lembut: #f3e6e8;
        --gd-emas: #c9a96a;
        --gd-emas-lembut: #faf3e4;
        --gd-teks: #2b2125;
        --gd-lembut: #857a7e;
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
        max-width: 100%;
        color: var(--gd-teks);
        font-size: 13px;
        line-height: 1.5;
    }

    .gd *,
    .gd *::before,
    .gd *::after {
        box-sizing: border-box;
    }

    .gd svg {
        flex-shrink: 0;
    }

    .gd a {
        color: inherit;
    }

    /* ---------------- Kepala (hero) ---------------- */
    .gd-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr);
        gap: 18px;
        padding: 24px 26px;
        border-radius: 18px;
        background:
            radial-gradient(900px 260px at 108% -30%, rgba(201, 169, 106, 0.34), transparent 62%),
            linear-gradient(135deg, #6b0d1b 0%, #7f1a2a 55%, #53080f 100%);
        color: #fff;
        overflow: hidden;
        box-shadow: 0 14px 32px rgba(82, 7, 15, 0.16);
    }

    .gd-hero::after {
        content: "";
        position: absolute;
        right: -70px;
        bottom: -110px;
        width: 260px;
        height: 260px;
        border: 1px solid rgba(201, 169, 106, 0.35);
        border-radius: 50%;
        pointer-events: none;
    }

    .gd-hero-teks {
        position: relative;
        z-index: 1;
        min-width: 0;
    }

    .gd-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .gd-salam {
        margin: 10px 0 6px;
        font-size: clamp(20px, 2.4vw, 27px);
        font-weight: 800;
        line-height: 1.3;
        letter-spacing: -0.01em;
        overflow-wrap: anywhere;
    }

    .gd-sapaan-sub {
        margin: 0;
        max-width: 52ch;
        font-size: 12.5px;
        color: rgba(255, 255, 255, 0.82);
    }

    .gd-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 14px;
    }

    .gd-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        font-size: 11px;
        font-weight: 600;
        color: #fff;
    }

    .gd-meta-item svg {
        width: 13px;
        height: 13px;
        color: #e6cf9f;
    }

    .gd-hero-aksi {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 16px;
    }

    .gd-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 9px 15px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
        cursor: pointer;
        transition: transform 0.16s ease, box-shadow 0.16s ease, background 0.16s ease, border-color 0.16s ease;
    }

    .gd-btn:focus-visible {
        outline: 2px solid #fff;
        outline-offset: 2px;
    }

    .gd-btn svg {
        width: 15px;
        height: 15px;
    }

    .gd-btn--emas {
        background: linear-gradient(180deg, #ddc189 0%, #c9a96a 100%);
        border-color: #c9a96a;
        color: #3b290f;
    }

    .gd-btn--emas:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.22);
    }

    .gd-btn--garis {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.4);
        color: #fff;
    }

    .gd-btn--garis:hover {
        background: rgba(255, 255, 255, 0.18);
    }

    .gd-btn--maroon {
        background: var(--gd-maroon);
        color: #fff;
    }

    .gd-btn--maroon:hover {
        background: var(--gd-maroon-tua);
    }

    .gd-btn--kecil {
        padding: 5px 10px;
        border-color: var(--gd-garis);
        background: #fff;
        color: var(--gd-maroon);
        font-size: 11px;
        white-space: nowrap;
    }

    .gd-btn--kecil:hover {
        background: var(--gd-tint);
        border-color: #d9b4bc;
    }

    /* ---------------- Jadwal berikutnya (hero) ---------------- */
    .gd-hero-jadwal {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 0;
        padding: 15px 16px;
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 14px;
        background: rgba(33, 4, 10, 0.28);
    }

    .gd-hero-jadwal-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #e6cf9f;
    }

    .gd-hero-jadwal-label svg {
        width: 13px;
        height: 13px;
    }

    .gd-hero-jadwal-utama {
        font-size: 14.5px;
        font-weight: 700;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .gd-hero-jadwal-ket {
        font-size: 11.5px;
        color: rgba(255, 255, 255, 0.8);
        overflow-wrap: anywhere;
    }

    .gd-hero-jadwal-waktu {
        display: inline-flex;
        align-items: center;
        align-self: flex-start;
        margin-top: 4px;
        padding: 3px 9px;
        border-radius: 999px;
        background: rgba(201, 169, 106, 0.22);
        color: #f2e2c0;
        font-size: 10.5px;
        font-weight: 700;
    }

    .gd-hero-jadwal-tautan {
        margin-top: 4px;
        font-size: 11px;
        font-weight: 700;
        color: #f2e2c0;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .gd-hero-jadwal-tautan:hover {
        color: #fff;
    }

    /* ---------------- Kartu statistik ---------------- */
    .gd-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(158px, 1fr));
        gap: 11px;
    }

    .gd-stat {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        min-width: 0;
        padding: 13px 14px;
        border: 1px solid var(--gd-garis);
        border-radius: 14px;
        background: #fff;
        color: inherit;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }

    a.gd-stat:hover {
        border-color: #d9b4bc;
        box-shadow: 0 10px 22px rgba(107, 13, 27, 0.08);
        transform: translateY(-2px);
    }

    a.gd-stat:focus-visible {
        outline: 2px solid var(--gd-maroon);
        outline-offset: 2px;
    }

    .gd-stat-ikon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--gd-tint);
        color: var(--gd-maroon);
    }

    .gd-stat-ikon svg {
        width: 18px;
        height: 18px;
    }

    .gd-stat[data-nada="emas"] .gd-stat-ikon {
        background: var(--gd-emas-lembut);
        color: #96793c;
    }

    .gd-stat[data-nada="bahaya"] .gd-stat-ikon {
        background: #fdeef0;
        color: #b02a34;
    }

    .gd-stat-isi {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .gd-stat-nilai {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
        color: #3a1119;
        font-variant-numeric: tabular-nums;
    }

    .gd-stat[data-nada="bahaya"] .gd-stat-nilai {
        color: #a51a2b;
    }

    .gd-stat-label {
        font-size: 11.5px;
        font-weight: 600;
        color: #5f5357;
    }

    .gd-stat-ket {
        margin-top: 1px;
        font-size: 10.5px;
        color: var(--gd-lembut);
        overflow-wrap: anywhere;
    }

    /* ---------------- Aksi cepat ---------------- */
    .gd-aksi {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(148px, 1fr));
        gap: 9px;
    }

    .gd-aksi a {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        padding: 9px 12px;
        border: 1px solid var(--gd-garis);
        border-radius: 11px;
        background: #fff;
        color: var(--gd-maroon);
        font-size: 11.5px;
        font-weight: 600;
        transition: background 0.16s ease, border-color 0.16s ease;
    }

    .gd-aksi a:hover {
        background: var(--gd-tint);
        border-color: #ddbfc5;
    }

    .gd-aksi a:focus-visible {
        outline: 2px solid var(--gd-maroon);
        outline-offset: 2px;
    }

    .gd-aksi svg {
        width: 16px;
        height: 16px;
    }

    .gd-aksi span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .gd-aksi-badge {
        margin-left: auto;
        padding: 0 6px;
        border-radius: 999px;
        background: var(--gd-maroon);
        color: #fff;
        font-size: 9.5px;
        font-weight: 700;
    }

    /* ---------------- Tata letak panel ---------------- */
    .gd-kolom {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr);
        gap: 13px;
        align-items: start;
    }

    .gd-kolom-utama,
    .gd-kolom-sisi {
        display: flex;
        flex-direction: column;
        gap: 13px;
        min-width: 0;
    }

    .gd-panel {
        min-width: 0;
        border: 1px solid var(--gd-garis);
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
    }

    .gd-panel-kepala {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 11px 14px;
        border-bottom: 1px solid var(--gd-garis-lembut);
        background: linear-gradient(180deg, #fdf8f9 0%, #ffffff 100%);
    }

    .gd-panel-judul {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        min-width: 0;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--gd-maroon);
    }

    .gd-panel-judul svg {
        width: 16px;
        height: 16px;
    }

    .gd-panel-tautan {
        font-size: 11px;
        font-weight: 700;
        color: var(--gd-maroon);
        white-space: nowrap;
    }

    .gd-panel-tautan:hover {
        text-decoration: underline;
    }

    .gd-panel-isi {
        padding: 10px 14px 12px;
        min-width: 0;
    }

    .gd-panel-isi--rapat {
        padding: 4px 14px 8px;
    }

    /* ---------------- Jadwal hari ini ---------------- */
    .gd-jadwal {
        display: grid;
        grid-template-columns: 58px minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;
        padding: 9px 0;
        border-bottom: 1px dashed #f2e4e6;
    }

    .gd-jadwal:last-child {
        border-bottom: none;
        padding-bottom: 2px;
    }

    .gd-jadwal-jam {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1px;
        padding: 5px 4px;
        border-radius: 9px;
        background: var(--gd-tint);
        color: var(--gd-maroon);
        font-size: 11.5px;
        font-weight: 800;
        line-height: 1.2;
        font-variant-numeric: tabular-nums;
    }

    .gd-jadwal-jam small {
        font-size: 9.5px;
        font-weight: 600;
        color: #9a8b8e;
    }

    .gd-jadwal-isi {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .gd-jadwal-isi strong {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--gd-teks);
        overflow-wrap: anywhere;
    }

    .gd-jadwal-isi span {
        font-size: 11px;
        color: var(--gd-lembut);
        overflow-wrap: anywhere;
    }

    /* ---------------- Tabel progres ---------------- */
    .gd-tabel-bungkus {
        width: 100%;
        min-width: 0;
        overflow-x: auto;
    }

    .gd-tabel {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 12px;
    }

    .gd-tabel th {
        padding: 7px 8px;
        border-bottom: 1px solid var(--gd-garis-lembut);
        color: #93868a;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        text-align: left;
    }

    .gd-tabel td {
        padding: 10px 8px;
        border-bottom: 1px solid #f8eef0;
        vertical-align: middle;
        overflow-wrap: anywhere;
    }

    .gd-tabel tbody tr:last-child td {
        border-bottom: none;
    }

    .gd-tabel th:nth-child(1),
    .gd-tabel td:nth-child(1) {
        width: 27%;
    }

    .gd-tabel th:nth-child(2),
    .gd-tabel td:nth-child(2) {
        width: 15%;
    }

    .gd-tabel th:nth-child(3),
    .gd-tabel td:nth-child(3) {
        width: 21%;
    }

    .gd-tabel th:nth-child(4),
    .gd-tabel td:nth-child(4) {
        width: 26%;
    }

    .gd-tabel th:nth-child(5),
    .gd-tabel td:nth-child(5) {
        width: 11%;
        text-align: right;
    }

    .gd-tabel-utama {
        display: block;
        font-weight: 700;
        color: var(--gd-teks);
    }

    .gd-tabel-ket {
        display: block;
        font-size: 10.5px;
        color: var(--gd-lembut);
    }

    .gd-progres {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .gd-progres-angka {
        font-size: 11px;
        font-weight: 800;
        color: var(--gd-maroon);
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .gd-bilah {
        display: block;
        flex: 1;
        min-width: 34px;
        height: 7px;
        border-radius: 999px;
        background: #f3e7ea;
        overflow: hidden;
    }

    .gd-bilah-isi {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #8a1626 0%, #6b0d1b 100%);
    }

    .gd-progres-ket {
        display: block;
        margin-top: 2px;
        font-size: 10px;
        color: var(--gd-lembut);
    }

    /* ---------------- Baris daftar ---------------- */
    .gd-baris {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 8px 0;
        border-bottom: 1px dashed #f2e4e6;
    }

    .gd-baris:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .gd-baris-ikon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 27px;
        height: 27px;
        border-radius: 8px;
        background: var(--gd-tint);
        color: var(--gd-maroon);
    }

    .gd-baris-ikon svg {
        width: 14px;
        height: 14px;
    }

    .gd-baris-isi {
        flex: 1;
        min-width: 0;
    }

    .gd-baris-utama {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--gd-teks);
        overflow-wrap: anywhere;
    }

    .gd-baris-ket {
        display: block;
        font-size: 10.5px;
        color: var(--gd-lembut);
        overflow-wrap: anywhere;
    }

    .gd-baris-akhir {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        margin-left: auto;
        padding-left: 8px;
        text-align: right;
    }

    .gd-pil {
        display: inline-flex;
        align-items: center;
        padding: 1px 8px;
        border-radius: 999px;
        background: var(--gd-tint);
        color: var(--gd-maroon);
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .gd-pil[data-nada="emas"] {
        background: var(--gd-emas-lembut);
        color: #8a6c2f;
    }

    .gd-pil[data-nada="bahaya"] {
        background: #fdeef0;
        color: #a51a2b;
    }

    .gd-pil[data-nada="sukses"] {
        background: #eaf6ef;
        color: #2f7d54;
    }

    /* ---------------- Ringkasan absensi ---------------- */
    .gd-absen-baris {
        display: grid;
        grid-template-columns: 52px minmax(0, 1fr) 32px;
        align-items: center;
        gap: 8px;
        padding: 5px 0;
        font-size: 11px;
    }

    .gd-absen-label {
        font-weight: 600;
        color: #5f5357;
    }

    .gd-absen-nilai {
        font-weight: 700;
        color: var(--gd-teks);
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .gd-bilah-isi[data-nada="hadir"] {
        background: #2f7d54;
    }

    .gd-bilah-isi[data-nada="izin"] {
        background: var(--gd-emas);
    }

    .gd-bilah-isi[data-nada="sakit"] {
        background: #a9705f;
    }

    .gd-bilah-isi[data-nada="alpa"] {
        background: #b02a34;
    }

    .gd-absen-kaki {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px dashed #f2e4e6;
        font-size: 11px;
        color: var(--gd-lembut);
    }

    .gd-absen-kaki strong {
        color: var(--gd-maroon);
        font-size: 12px;
    }

    /* ---------------- Kondisi kosong ---------------- */
    .gd-kosong {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        padding: 20px 12px;
        text-align: center;
        color: var(--gd-lembut);
        font-size: 12px;
    }

    .gd-kosong-ikon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--gd-emas-lembut);
        color: #96793c;
    }

    .gd-kosong-ikon svg {
        width: 19px;
        height: 19px;
    }

    .gd-kosong strong {
        color: #5f5357;
        font-size: 12.5px;
    }

    .gd-kosong p {
        margin: 0;
        max-width: 40ch;
        font-size: 11.5px;
    }

    .gd-kosong .gd-btn {
        margin-top: 6px;
    }

    /* ---------------- Pengumuman ---------------- */
    .gd-pengumuman {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px;
    }

    .gd-pengumuman-item {
        min-width: 0;
        padding: 10px 12px;
        border: 1px solid var(--gd-garis-lembut);
        border-radius: 11px;
        background: #fffdfd;
    }

    .gd-pengumuman-item strong {
        display: block;
        font-size: 12px;
        color: var(--gd-maroon);
        overflow-wrap: anywhere;
    }

    .gd-pengumuman-item span {
        display: block;
        margin-top: 3px;
        font-size: 11px;
        color: var(--gd-lembut);
        overflow-wrap: anywhere;
    }

    .gd-pengumuman-item small {
        display: block;
        margin-top: 5px;
        font-size: 10px;
        font-weight: 600;
        color: #b09a9e;
    }

    /* ---------------- Transisi masuk ---------------- */
    @media (prefers-reduced-motion: no-preference) {
        .gd > .gd-hero,
        .gd > .gd-stats,
        .gd > .gd-aksi,
        .gd > .gd-kolom,
        .gd > .gd-panel {
            animation: gd-naik 0.35s ease both;
        }

        .gd > .gd-stats {
            animation-delay: 0.04s;
        }

        .gd > .gd-aksi {
            animation-delay: 0.08s;
        }

        .gd > .gd-kolom {
            animation-delay: 0.12s;
        }
    }

    @keyframes gd-naik {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    /* ---------------- Responsif ---------------- */
    @media (max-width: 1080px) {
        .gd-kolom {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 900px) {
        .gd-hero {
            grid-template-columns: minmax(0, 1fr);
            padding: 20px;
        }
    }

    @media (max-width: 720px) {
        .gd-tabel--progres thead {
            display: none;
        }

        .gd-tabel--progres,
        .gd-tabel--progres tbody,
        .gd-tabel--progres tr,
        .gd-tabel--progres td {
            display: block;
            width: 100%;
        }

        .gd-tabel--progres tr {
            margin-bottom: 9px;
            padding: 9px 11px;
            border: 1px solid var(--gd-garis);
            border-radius: 11px;
            background: #fffdfd;
        }

        .gd-tabel--progres tr:last-child {
            margin-bottom: 0;
        }

        .gd-tabel--progres td {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 4px 0;
            border-bottom: none;
            text-align: right;
        }

        .gd-tabel--progres td::before {
            content: attr(data-label);
            flex-shrink: 0;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #9a8b8e;
            text-align: left;
        }

        .gd-tabel--progres td.gd-sel-progres {
            display: block;
            padding-top: 7px;
            text-align: left;
        }

        .gd-tabel--progres td.gd-sel-progres::before {
            display: block;
            margin-bottom: 4px;
        }

        .gd-tabel--progres td.gd-sel-aksi {
            justify-content: flex-end;
            padding-top: 8px;
        }

        .gd-tabel--progres td.gd-sel-aksi::before {
            display: none;
        }

        .gd-tabel--progres td.gd-sel-utama {
            display: block;
            padding-top: 0;
            text-align: left;
        }

        .gd-tabel--progres td.gd-sel-utama::before {
            display: none;
        }

        .gd-tabel--progres td.gd-sel-utama .gd-tabel-utama {
            font-size: 13px;
        }
    }

    @media (max-width: 560px) {
        .gd-salam {
            font-size: 19px;
        }

        .gd-stat {
            padding: 11px 12px;
        }

        .gd-stat-nilai {
            font-size: 19px;
        }

        .gd-panel-isi {
            padding: 9px 11px 11px;
        }

        .gd-panel-isi--rapat {
            padding: 4px 11px 8px;
        }

        .gd-jadwal {
            grid-template-columns: 52px minmax(0, 1fr);
        }

        .gd-jadwal .gd-btn {
            grid-column: 2;
            justify-self: start;
        }

        .gd-baris {
            flex-wrap: wrap;
        }

        .gd-baris-akhir {
            width: 100%;
            margin-left: 0;
            padding-left: 36px;
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
            text-align: left;
        }
    }
</style>
@endpush

@section('content')
    @php
        $berikut = $ringkasan['jadwalBerikutnya'];
        $rekapAbsensi = $ringkasan['absensi'];
        $totalAbsensi = max(1, (int) $rekapAbsensi['total']);
        $adaPenugasan = $ringkasan['progresPenugasan']->isNotEmpty();
    @endphp

    <div class="gd">

        {{-- Sapaan, tanggal, dan jadwal berikutnya --}}
        <section class="gd-hero">
            <div class="gd-hero-teks">
                <span class="gd-eyebrow">Dashboard Guru</span>
                <h1 class="gd-salam">Assalamu’alaikum, {{ $guru?->name ?? 'Ustazah' }}</h1>
                <p class="gd-sapaan-sub">
                    Ringkasan penugasan, jadwal mengajar, progres pertemuan, absensi, dan tugas
                    yang menjadi tanggung jawab Anda pada Ma’had.
                </p>

                <div class="gd-meta">
                    <span class="gd-meta-item">
                        <x-panel.ikon nama="jadwal" />
                        {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                    </span>
                    <span class="gd-meta-item">
                        <x-panel.ikon nama="pembelajaran" />
                        {{ $ringkasan['jumlahMapel'] }} mata pelajaran
                    </span>
                    <span class="gd-meta-item">
                        <x-panel.ikon nama="pengguna" />
                        {{ $ringkasan['jumlahKelompok'] }} kelompok
                    </span>
                </div>

                <div class="gd-hero-aksi">
                    <a class="gd-btn gd-btn--emas" href="{{ route('guru.pembelajaran') }}">
                        <x-panel.ikon nama="pembelajaran" />
                        Buka Pembelajaran
                    </a>
                    <a class="gd-btn gd-btn--garis" href="{{ route('guru.absensi') }}">
                        <x-panel.ikon nama="absensi" />
                        Rekap Absensi
                    </a>
                </div>
            </div>

            <aside class="gd-hero-jadwal" aria-label="Jadwal berikutnya">
                <span class="gd-hero-jadwal-label">
                    <x-panel.ikon nama="jadwal" />
                    Jadwal berikutnya
                </span>

                @if ($berikut)
                    <span class="gd-hero-jadwal-utama">{{ $berikut['mapel'] }}</span>
                    <span class="gd-hero-jadwal-ket">
                        {{ $berikut['kelompok'] }} · {{ $berikut['ruangan'] }}
                    </span>
                    <span class="gd-hero-jadwal-waktu">
                        {{ $berikut['kapan'] }}, {{ $berikut['tanggal'] }} · {{ $berikut['jam'] }}
                    </span>
                    <a class="gd-hero-jadwal-tautan"
                        href="{{ route('guru.pembelajaran', [
                            'mata_pelajaran' => $berikut['jadwal']->mata_pelajaran_id,
                            'kelompok' => $berikut['jadwal']->kelompok,
                            'periode' => $berikut['jadwal']->periode,
                        ]) }}">
                        Siapkan pertemuan berikutnya
                    </a>
                @else
                    <span class="gd-hero-jadwal-utama">Belum ada jadwal berikutnya</span>
                    <span class="gd-hero-jadwal-ket">
                        Jadwal yang ditetapkan Musyrifah akan tampil di sini secara otomatis.
                    </span>
                @endif
            </aside>
        </section>

        {{-- Ringkasan angka dari penugasan sebenarnya --}}
        <section class="gd-stats" aria-label="Ringkasan penugasan">
            <a class="gd-stat" href="{{ route('guru.pembelajaran') }}">
                <span class="gd-stat-ikon"><x-panel.ikon nama="pembelajaran" /></span>
                <span class="gd-stat-isi">
                    <span class="gd-stat-nilai">{{ $ringkasan['jumlahMapel'] }}</span>
                    <span class="gd-stat-label">Mata Pelajaran</span>
                    <span class="gd-stat-ket">{{ $ringkasan['jumlahKelompok'] }} kelompok diajar</span>
                </span>
            </a>

            <a class="gd-stat" href="{{ route('guru.pembelajaran') }}">
                <span class="gd-stat-ikon"><x-panel.ikon nama="pengguna" /></span>
                <span class="gd-stat-isi">
                    <span class="gd-stat-nilai">{{ $ringkasan['jumlahKelompok'] }}</span>
                    <span class="gd-stat-label">Kelompok</span>
                    <span class="gd-stat-ket">{{ $ringkasan['jumlahJadwal'] }} jadwal mengajar</span>
                </span>
            </a>

            <a class="gd-stat" href="{{ route('guru.pembelajaran') }}" data-nada="emas">
                <span class="gd-stat-ikon"><x-panel.ikon nama="laporan" /></span>
                <span class="gd-stat-isi">
                    <span class="gd-stat-nilai">{{ $ringkasan['pertemuanSelesai'] }}</span>
                    <span class="gd-stat-label">Pertemuan Selesai</span>
                    <span class="gd-stat-ket">
                        @if ($ringkasan['targetPertemuan'] > 0)
                            dari {{ $ringkasan['targetPertemuan'] }} pertemuan (1–16)
                        @else
                            belum ada penugasan
                        @endif
                    </span>
                </span>
            </a>

            <a class="gd-stat" href="{{ route('guru.penilaian') }}"
                data-nada="{{ $ringkasan['belumDinilai'] > 0 ? 'bahaya' : 'sukses' }}">
                <span class="gd-stat-ikon"><x-panel.ikon nama="tugas" /></span>
                <span class="gd-stat-isi">
                    <span class="gd-stat-nilai">{{ $ringkasan['belumDinilai'] }}</span>
                    <span class="gd-stat-label">Perlu Diperiksa</span>
                    <span class="gd-stat-ket">
                        {{ $ringkasan['perluDiperiksa']->count() }} tugas menunggu penilaian
                    </span>
                </span>
            </a>
        </section>

        {{-- Aksi cepat --}}
        <nav class="gd-aksi" aria-label="Aksi cepat">
            <a href="{{ route('guru.pembelajaran') }}">
                <x-panel.ikon nama="pembelajaran" />
                <span>Pembelajaran</span>
            </a>
            <a href="{{ route('guru.absensi') }}">
                <x-panel.ikon nama="absensi" />
                <span>Absensi</span>
                @if ($ringkasan['absensiBelumDiisi'] > 0)
                    <span class="gd-aksi-badge">{{ $ringkasan['absensiBelumDiisi'] }}</span>
                @endif
            </a>
            <a href="{{ route('guru.penilaian') }}">
                <x-panel.ikon nama="laporan" />
                <span>Penilaian</span>
                @if ($ringkasan['belumDinilai'] > 0)
                    <span class="gd-aksi-badge">{{ $ringkasan['belumDinilai'] }}</span>
                @endif
            </a>
            <a href="{{ route('guru.tugas.create') }}">
                <x-panel.ikon nama="tugas" />
                <span>Buat Tugas</span>
            </a>
        </nav>

        <div class="gd-kolom">
            <div class="gd-kolom-utama">

                {{-- Jadwal hari ini --}}
                <section class="gd-panel">
                    <header class="gd-panel-kepala">
                        <h2 class="gd-panel-judul">
                            <x-panel.ikon nama="jadwal" />
                            <span>Jadwal Mengajar Hari Ini</span>
                        </h2>
                        <a class="gd-panel-tautan" href="{{ route('guru.pembelajaran') }}">Jadwal Saya</a>
                    </header>
                    <div class="gd-panel-isi gd-panel-isi--rapat">
                        @forelse ($ringkasan['jadwalHariIni'] as $jadwal)
                            <div class="gd-jadwal">
                                <span class="gd-jadwal-jam">
                                    {{ substr((string) $jadwal->jam_mulai, 0, 5) }}
                                    <small>{{ substr((string) $jadwal->jam_selesai, 0, 5) }}</small>
                                </span>
                                <span class="gd-jadwal-isi">
                                    <strong>{{ $jadwal->namaMataPelajaran() }}</strong>
                                    <span>{{ $jadwal->kelompok ?: '-' }} · {{ $jadwal->ruangan ?: 'Ruang Kelas' }}</span>
                                </span>
                                <a class="gd-btn gd-btn--kecil"
                                    href="{{ route('guru.pembelajaran', [
                                        'mata_pelajaran' => $jadwal->mata_pelajaran_id,
                                        'kelompok' => $jadwal->kelompok,
                                        'periode' => $jadwal->periode,
                                    ]) }}">
                                    Buka
                                </a>
                            </div>
                        @empty
                            <div class="gd-kosong">
                                <span class="gd-kosong-ikon"><x-panel.ikon nama="jadwal" /></span>
                                <strong>Tidak ada jadwal mengajar hari ini.</strong>
                                <p>Jadwal ditetapkan oleh Musyrifah melalui Manajemen Pembelajaran.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- Progres 16 pertemuan per mapel dan periode --}}
                <section class="gd-panel">
                    <header class="gd-panel-kepala">
                        <h2 class="gd-panel-judul">
                            <x-panel.ikon nama="laporan" />
                            <span>Progres Pertemuan (1–16)</span>
                        </h2>
                        <a class="gd-panel-tautan" href="{{ route('guru.pembelajaran') }}">Mulai Pembelajaran</a>
                    </header>

                    @if ($adaPenugasan)
                        <div class="gd-tabel-bungkus">
                            <table class="gd-tabel gd-tabel--progres">
                                <thead>
                                    <tr>
                                        <th scope="col">Mata Pelajaran</th>
                                        <th scope="col">Kelompok</th>
                                        <th scope="col">Periode</th>
                                        <th scope="col">Pertemuan</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ringkasan['progresPenugasan'] as $baris)
                                        <tr>
                                            <td class="gd-sel-utama" data-label="Mata Pelajaran">
                                                <span class="gd-tabel-utama">{{ $baris['mapel'] }}</span>
                                            </td>
                                            <td data-label="Kelompok">{{ $baris['kelompok'] }}</td>
                                            <td data-label="Periode">{{ $baris['periode'] }}</td>
                                            <td class="gd-sel-progres" data-label="Pertemuan">
                                                <span class="gd-progres">
                                                    <span class="gd-progres-angka">
                                                        {{ $baris['selesai'] }}/{{ $baris['target'] }}
                                                    </span>
                                                    <span class="gd-bilah">
                                                        <span class="gd-bilah-isi"
                                                            style="width: {{ $baris['persentase'] }}%"></span>
                                                    </span>
                                                </span>
                                                <span class="gd-progres-ket">
                                                    @if ($baris['berlangsung'] !== null)
                                                        Pertemuan {{ $baris['berlangsung'] }} sedang berlangsung
                                                    @elseif ($baris['berikutnya'] !== null)
                                                        Berikutnya pertemuan {{ $baris['berikutnya'] }}
                                                    @else
                                                        Seluruh 16 pertemuan selesai
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="gd-sel-aksi" data-label="Aksi">
                                                <a class="gd-btn gd-btn--kecil"
                                                    href="{{ route('guru.pembelajaran', [
                                                        'mata_pelajaran' => $baris['jadwal']->mata_pelajaran_id,
                                                        'kelompok' => $baris['jadwal']->kelompok,
                                                        'periode' => $baris['periode'],
                                                    ]) }}">
                                                    Lanjut
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="gd-panel-isi">
                            <div class="gd-kosong">
                                <span class="gd-kosong-ikon"><x-panel.ikon nama="pembelajaran" /></span>
                                <strong>Belum ada kelompok yang ditugaskan.</strong>
                                <p>
                                    Mata pelajaran, kelompok, dan jadwal ditetapkan Musyrifah. Bila
                                    penugasan belum muncul, hubungi Musyrifah agar akun Anda dipilih
                                    sebagai Guru Pengampu.
                                </p>
                                <a class="gd-btn gd-btn--maroon" href="{{ route('guru.pembelajaran') }}">
                                    <x-panel.ikon nama="pembelajaran" />
                                    Buka Pembelajaran
                                </a>
                            </div>
                        </div>
                    @endif
                </section>
            </div>

            <aside class="gd-kolom-sisi">

                {{-- Tugas yang menunggu pemeriksaan --}}
                <section class="gd-panel">
                    <header class="gd-panel-kepala">
                        <h2 class="gd-panel-judul">
                            <x-panel.ikon nama="tugas" />
                            <span>Perlu Diperiksa</span>
                        </h2>
                        <a class="gd-panel-tautan" href="{{ route('guru.penilaian') }}">Penilaian</a>
                    </header>
                    <div class="gd-panel-isi gd-panel-isi--rapat">
                        @forelse ($ringkasan['perluDiperiksa'] as $item)
                            <div class="gd-baris">
                                <span class="gd-baris-ikon"><x-panel.ikon nama="tugas" /></span>
                                <span class="gd-baris-isi">
                                    <span class="gd-baris-utama">{{ $item['tugas']->judul }}</span>
                                    <span class="gd-baris-ket">
                                        {{ $item['tugas']->kelompok ?: '-' }}
                                        @if ($item['tugas']->nama_mata_pelajaran)
                                            · {{ $item['tugas']->nama_mata_pelajaran }}
                                        @endif
                                    </span>
                                </span>
                                <span class="gd-baris-akhir">
                                    <span class="gd-pil" data-nada="bahaya">{{ $item['jumlah'] }} belum dinilai</span>
                                    <a class="gd-btn gd-btn--kecil"
                                        href="{{ route('guru.pengumpulan.index', $item['tugas']) }}">
                                        Periksa
                                    </a>
                                </span>
                            </div>
                        @empty
                            <div class="gd-kosong">
                                <span class="gd-kosong-ikon"><x-panel.ikon nama="tugas" /></span>
                                <strong>Tidak ada pengumpulan yang menunggu.</strong>
                                <p>Pengumpulan baru akan tampil di sini untuk Anda nilai.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- Ringkasan absensi --}}
                <section class="gd-panel">
                    <header class="gd-panel-kepala">
                        <h2 class="gd-panel-judul">
                            <x-panel.ikon nama="absensi" />
                            <span>Ringkasan Absensi</span>
                        </h2>
                        <a class="gd-panel-tautan" href="{{ route('guru.absensi') }}">Rekap</a>
                    </header>
                    <div class="gd-panel-isi">
                        @if ($rekapAbsensi['total'] > 0)
                            @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'] as $kunci => $label)
                                <div class="gd-absen-baris">
                                    <span class="gd-absen-label">{{ $label }}</span>
                                    <span class="gd-bilah">
                                        <span class="gd-bilah-isi" data-nada="{{ $kunci }}"
                                            style="width: {{ round(((int) $rekapAbsensi[$kunci] / $totalAbsensi) * 100) }}%"></span>
                                    </span>
                                    <span class="gd-absen-nilai">{{ $rekapAbsensi[$kunci] }}</span>
                                </div>
                            @endforeach

                            <div class="gd-absen-kaki">
                                <span>{{ $rekapAbsensi['total'] }} catatan absensi</span>
                                <span><strong>{{ $rekapAbsensi['persentaseHadir'] }}%</strong> hadir</span>
                            </div>
                        @else
                            <div class="gd-kosong">
                                <span class="gd-kosong-ikon"><x-panel.ikon nama="absensi" /></span>
                                <strong>Belum ada absensi tercatat.</strong>
                                <p>Absensi diisi saat pertemuan pembelajaran berlangsung.</p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Tugas terbaru milik Guru --}}
                <section class="gd-panel">
                    <header class="gd-panel-kepala">
                        <h2 class="gd-panel-judul">
                            <x-panel.ikon nama="tugas" />
                            <span>Tugas Terbaru</span>
                        </h2>
                        <a class="gd-panel-tautan" href="{{ route('guru.tugas.index') }}">Semua Tugas</a>
                    </header>
                    <div class="gd-panel-isi gd-panel-isi--rapat">
                        @forelse ($ringkasan['tugasDaftar'] as $tugas)
                            <div class="gd-baris">
                                <span class="gd-baris-ikon"><x-panel.ikon nama="tugas" /></span>
                                <span class="gd-baris-isi">
                                    <span class="gd-baris-utama">{{ $tugas->judul }}</span>
                                    <span class="gd-baris-ket">
                                        {{ $tugas->kelompok ?: '-' }} · {{ $tugas->pengumpulan_count }} pengumpulan
                                        @if ($tugas->batas_pengumpulan)
                                            · batas {{ $tugas->batas_pengumpulan->locale('id')->translatedFormat('d M H:i') }}
                                        @endif
                                    </span>
                                </span>
                                <span class="gd-baris-akhir">
                                    <span class="gd-pil"
                                        data-nada="{{ $tugas->status === \App\Models\Tugas::STATUS_DIPUBLIKASIKAN ? 'sukses' : 'emas' }}">
                                        {{ $tugas->status === \App\Models\Tugas::STATUS_DIPUBLIKASIKAN ? 'Aktif' : 'Draft' }}
                                    </span>
                                </span>
                            </div>
                        @empty
                            <div class="gd-kosong">
                                <span class="gd-kosong-ikon"><x-panel.ikon nama="tugas" /></span>
                                <strong>Belum ada tugas yang dibuat.</strong>
                                <p>Gunakan tombol Buat Tugas untuk memulai.</p>
                                <a class="gd-btn gd-btn--maroon" href="{{ route('guru.tugas.create') }}">
                                    <x-panel.ikon nama="tugas" />
                                    Buat Tugas
                                </a>
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>

        {{-- Pengumuman ma'had --}}
        @if ($ringkasan['pengumuman']->isNotEmpty())
            <section class="gd-panel">
                <header class="gd-panel-kepala">
                    <h2 class="gd-panel-judul">
                        <x-panel.ikon nama="pengumuman" />
                        <span>Pengumuman Ma’had</span>
                    </h2>
                </header>
                <div class="gd-panel-isi">
                    <div class="gd-pengumuman">
                        @foreach ($ringkasan['pengumuman'] as $pengumuman)
                            <article class="gd-pengumuman-item">
                                <strong>{{ $pengumuman->judul }}</strong>
                                <span>{{ $pengumuman->ringkasan(110) }}</span>
                                <small>{{ $pengumuman->tanggalTeks() }}</small>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
