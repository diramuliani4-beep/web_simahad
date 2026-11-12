{{--
    Gaya bersama komponen dashboard seluruh role.
    Warna mengikuti identitas SIMAHAD: maroon #6B0D1B, tint #F8EDEF, emas #C9A96A.
--}}
@once
<style>
    .dash {
        display: flex;
        flex-direction: column;
        gap: 16px;
        color: #2b2125;
        font-size: 13px;
        line-height: 1.5;
    }

    .dash *,
    .dash *::before,
    .dash *::after {
        box-sizing: border-box;
    }

    /* ---------------- Kepala halaman ---------------- */
    .dash-kepala {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .dash-sapaan h1 {
        margin: 0 0 4px;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.35;
        color: #6b0d1b;
        letter-spacing: -0.01em;
    }

    .dash-sapaan p {
        margin: 0;
        font-size: 12.5px;
        color: #7c7276;
    }

    .dash-peran {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 3px 10px;
        border-radius: 999px;
        background: #f8edef;
        color: #6b0d1b;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .dash-tanggal {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border: 1px solid #ecd9dd;
        border-radius: 10px;
        background: #fff;
        color: #6b0d1b;
        font-size: 11.5px;
        font-weight: 600;
    }

    .dash-tanggal svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    /* ---------------- Kartu statistik ---------------- */
    .dash-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(178px, 1fr));
        gap: 11px;
    }

    .dash-kartu {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
        padding: 12px 14px 12px 16px;
        border: 1px solid #ecd9dd;
        border-radius: 13px;
        background: #fff;
        color: inherit;
        text-align: left;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }

    a.dash-kartu:hover {
        border-color: #d9b4bc;
        box-shadow: 0 8px 20px rgba(107, 13, 27, 0.07);
        transform: translateY(-1px);
    }

    a.dash-kartu:focus-visible {
        outline: 2px solid #6b0d1b;
        outline-offset: 2px;
    }

    .dash-kartu::before {
        content: "";
        position: absolute;
        inset: 12px auto 12px 0;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: #6b0d1b;
    }

    .dash-kartu[data-nada="emas"]::before {
        background: #c9a96a;
    }

    .dash-kartu[data-nada="bahaya"]::before {
        background: #b02a34;
    }

    .dash-kartu[data-nada="sukses"]::before {
        background: #2f7d54;
    }

    .dash-kartu-kepala {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 4px;
    }

    .dash-kartu-ikon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 9px;
        background: #f8edef;
        color: #6b0d1b;
        flex-shrink: 0;
    }

    .dash-kartu[data-nada="emas"] .dash-kartu-ikon {
        background: #faf3e4;
        color: #96793c;
    }

    .dash-kartu[data-nada="bahaya"] .dash-kartu-ikon {
        background: #fdeef0;
        color: #b02a34;
    }

    .dash-kartu[data-nada="sukses"] .dash-kartu-ikon {
        background: #eaf6ef;
        color: #2f7d54;
    }

    .dash-kartu-ikon svg {
        width: 17px;
        height: 17px;
    }

    .dash-kartu-nilai {
        font-size: 22px;
        font-weight: 700;
        line-height: 1.25;
        color: #3a1119;
        font-variant-numeric: tabular-nums;
    }

    .dash-kartu-label {
        font-size: 12px;
        font-weight: 600;
        color: #5f5357;
    }

    .dash-kartu-ket {
        margin-top: 2px;
        font-size: 11px;
        color: #93868a;
    }

    /* ---------------- Dua kolom ---------------- */
    .dash-kolom {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 13px;
        align-items: start;
    }

    /* ---------------- Panel ---------------- */
    .dash-panel {
        border: 1px solid #ecd9dd;
        border-radius: 13px;
        background: #fff;
        overflow: hidden;
    }

    .dash-panel-kepala {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 14px;
        border-bottom: 1px solid #f3e6e8;
        background: linear-gradient(180deg, #fdf8f9 0%, #ffffff 100%);
    }

    .dash-panel-judul {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        min-width: 0;
        font-size: 12.5px;
        font-weight: 700;
        color: #6b0d1b;
    }

    .dash-panel-judul svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .dash-panel-tautan {
        font-size: 11.5px;
        font-weight: 700;
        color: #6b0d1b;
        white-space: nowrap;
    }

    .dash-panel-tautan:hover {
        text-decoration: underline;
    }

    .dash-panel-isi {
        padding: 10px 14px 12px;
    }

    .dash-panel-isi--rapat {
        padding: 4px 14px 8px;
    }

    /* ---------------- Baris daftar ---------------- */
    .dash-baris {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 8px 0;
        border-bottom: 1px dashed #f2e4e6;
    }

    .dash-baris:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .dash-baris-ikon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: #f8edef;
        color: #6b0d1b;
        flex-shrink: 0;
    }

    .dash-baris-ikon svg {
        width: 14px;
        height: 14px;
    }

    .dash-baris-isi {
        min-width: 0;
        flex: 1;
    }

    .dash-baris-utama {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #2b2125;
        overflow-wrap: anywhere;
    }

    .dash-baris-ket {
        display: block;
        font-size: 11px;
        color: #857a7e;
        overflow-wrap: anywhere;
    }

    .dash-baris-akhir {
        margin-left: auto;
        padding-left: 8px;
        font-size: 10.5px;
        color: #9a8b8e;
        white-space: nowrap;
        text-align: right;
    }

    /* ---------------- Kondisi kosong ---------------- */
    .dash-kosong {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        padding: 20px 12px;
        text-align: center;
        color: #93868a;
        font-size: 12px;
    }

    .dash-kosong svg {
        width: 22px;
        height: 22px;
        color: #c9a96a;
    }

    /* ---------------- Pil status ---------------- */
    .dash-pil {
        display: inline-flex;
        align-items: center;
        padding: 1px 8px;
        border-radius: 999px;
        background: #f8edef;
        color: #6b0d1b;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .dash-pil[data-nada="emas"] {
        background: #faf3e4;
        color: #8a6c2f;
    }

    .dash-pil[data-nada="bahaya"] {
        background: #fdeef0;
        color: #a51a2b;
    }

    .dash-pil[data-nada="sukses"] {
        background: #eaf6ef;
        color: #2f7d54;
    }

    /* ---------------- Bilah progres ---------------- */
    .dash-bilah {
        height: 7px;
        border-radius: 999px;
        background: #f3e7ea;
        overflow: hidden;
    }

    .dash-bilah-isi {
        height: 100%;
        border-radius: 999px;
        background: #6b0d1b;
    }

    .dash-bilah-isi[data-nada="emas"] {
        background: #c9a96a;
    }

    .dash-bilah-isi[data-nada="sukses"] {
        background: #2f7d54;
    }

    .dash-angka-besar {
        font-size: 24px;
        font-weight: 700;
        line-height: 1.2;
        color: #6b0d1b;
        font-variant-numeric: tabular-nums;
    }

    .dash-catatan {
        margin: 6px 0 0;
        font-size: 11.5px;
        color: #857a7e;
    }

    /* ---------------- Peringatan ---------------- */
    .dash-peringatan {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 9px 12px;
        border: 1px solid #f0c2c7;
        border-radius: 11px;
        background: #fdf1f2;
        color: #8a1024;
        font-size: 12px;
    }

    .dash-peringatan svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* ---------------- Tabel ringkas ---------------- */
    .dash-tabel {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .dash-tabel th {
        padding: 4px 6px;
        border-bottom: 1px solid #f0e2e4;
        color: #93868a;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        text-align: left;
    }

    .dash-tabel td {
        padding: 7px 6px;
        border-bottom: 1px solid #f8eef0;
        color: #3a2f33;
        vertical-align: top;
    }

    .dash-tabel tr:last-child td {
        border-bottom: none;
    }

    .dash-tabel td:last-child,
    .dash-tabel th:last-child {
        text-align: right;
    }

    /* ---------------- Grafik batang sederhana ---------------- */
    .dash-grafik {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        height: 118px;
        padding-top: 6px;
    }

    .dash-grafik-kolom {
        display: flex;
        flex: 1;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;
        height: 100%;
        min-width: 0;
    }

    .dash-grafik-batang {
        width: 100%;
        max-width: 34px;
        min-height: 4px;
        border-radius: 6px 6px 2px 2px;
        background: linear-gradient(180deg, #8a1626 0%, #6b0d1b 100%);
    }

    .dash-grafik-nilai {
        font-size: 10.5px;
        font-weight: 700;
        color: #6b0d1b;
    }

    .dash-grafik-label {
        font-size: 9.5px;
        color: #93868a;
        white-space: nowrap;
    }

    /* ---------------- Aksi cepat ---------------- */
    .dash-aksi {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(146px, 1fr));
        gap: 9px;
    }

    .dash-aksi a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 11px;
        border: 1px solid #ecd9dd;
        border-radius: 11px;
        background: #fff;
        color: #6b0d1b;
        font-size: 11.5px;
        font-weight: 600;
        transition: background 0.18s ease, border-color 0.18s ease;
    }

    .dash-aksi a:hover {
        background: #f8edef;
        border-color: #ddbfc5;
    }

    .dash-aksi a:focus-visible {
        outline: 2px solid #6b0d1b;
        outline-offset: 2px;
    }

    .dash-aksi svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    .dash-aksi-badge {
        margin-left: auto;
        padding: 0 6px;
        border-radius: 999px;
        background: #6b0d1b;
        color: #fff;
        font-size: 9.5px;
        font-weight: 700;
    }

    /* ---------------- Lantai (laporan gabungan) ---------------- */
    .dash-lantai {
        display: grid;
        gap: 9px;
    }

    .dash-lantai-item {
        padding: 9px 11px;
        border: 1px solid #f0e2e4;
        border-radius: 10px;
        background: #fffdfd;
    }

    .dash-lantai-kepala {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
        font-size: 11.5px;
        font-weight: 700;
        color: #4a2a30;
    }

    @media (max-width: 640px) {
        .dash-sapaan h1 {
            font-size: 18px;
        }

        .dash-grid {
            grid-template-columns: repeat(auto-fit, minmax(148px, 1fr));
        }

        .dash-kartu-nilai {
            font-size: 19px;
        }

        .dash-kolom {
            grid-template-columns: 1fr;
        }

        .dash-panel-isi {
            padding: 9px 11px 11px;
        }
    }
</style>
@endonce
