{{-- Gaya bersama halaman Profil dan Data agar tampil seragam dengan panel SIMAHAD. --}}
<style>
    .pr-page { display: flex; flex-direction: column; gap: 18px; }

    .pr-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 22px;
        padding: 24px;
        border-radius: 20px;
        background:
            radial-gradient(circle at 88% -20%, rgba(201, 169, 106, .35), transparent 55%),
            linear-gradient(135deg, #6B0D1B, #8a1a2b);
        color: #fff;
        box-shadow: 0 16px 36px rgba(107, 13, 27, .22);
    }

    .pr-hero-teks { min-width: 0; flex: 1 1 260px; }

    .pr-hero-eyebrow {
        display: inline-block;
        margin-bottom: 8px;
        padding: 3px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(201, 169, 106, .55);
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #f6e6c5;
    }

    .pr-hero h1 { margin: 0 0 4px; font-size: clamp(20px, 2.4vw, 26px); font-weight: 800; }
    .pr-hero p { margin: 0; font-size: 13.5px; color: rgba(255, 255, 255, .82); }

    .pr-hero-aksi { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }

    .pr-tombol {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 15px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, .3);
        background: rgba(255, 255, 255, .12);
        color: #fff;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        transition: background .16s ease, color .16s ease;
    }

    .pr-tombol:hover { background: #C9A96A; border-color: #C9A96A; color: #43280a; }
    .pr-tombol--emas { background: #C9A96A; border-color: #C9A96A; color: #43280a; }
    .pr-tombol--emas:hover { background: #fff; border-color: #fff; color: #6B0D1B; }
    .pr-tombol svg { width: 15px; height: 15px; }

    .pr-grid { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .pr-kolom { display: flex; flex-direction: column; gap: 16px; }

    .pr-card {
        padding: 20px;
        border: 1px solid #eee2e5;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(59, 7, 15, .04);
    }

    .pr-card h2 {
        margin: 0 0 4px;
        font-size: 15.5px;
        font-weight: 800;
        color: #2b2224;
    }

    .pr-card .pr-sub { margin: 0 0 14px; font-size: 12.5px; color: #8b7c80; line-height: 1.5; }

    .pr-form { display: grid; gap: 13px; }
    .pr-field { display: grid; gap: 5px; }
    .pr-field label { font-size: 12.5px; font-weight: 700; color: #4a3d40; }

    .pr-field input {
        width: 100%;
        min-height: 40px;
        padding: 9px 12px;
        border: 1px solid #e3d5d9;
        border-radius: 10px;
        font-family: inherit;
        font-size: 13.5px;
        color: #2b2224;
        background: #fff;
        transition: border-color .16s ease, box-shadow .16s ease;
    }

    .pr-field input:focus {
        outline: none;
        border-color: #6B0D1B;
        box-shadow: 0 0 0 3px rgba(107, 13, 27, .1);
    }

    .pr-field input:disabled { background: #f7f3f4; color: #8b7c80; }

    .pr-aksi { display: flex; justify-content: flex-end; gap: 8px; margin-top: 4px; }

    .pr-btn {
        min-height: 38px;
        padding: 9px 17px;
        border-radius: 10px;
        border: 1px solid transparent;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pr-btn--utama { background: #6B0D1B; color: #fff; }
    .pr-btn--utama:hover { background: #52070f; }
    .pr-btn--garis { background: #fff; border-color: #ddcdd1; color: #5b4d50; }
    .pr-btn--garis:hover { background: #F8EDEF; border-color: #C9A96A; color: #6B0D1B; }
    .pr-btn { gap: 8px; }
    .pr-btn svg { width: 15px; height: 15px; flex-shrink: 0; }

    /* Halaman Lihat Profil: foto dan identitas berada tepat di tengah. */
    .pr-profil {
        width: min(760px, 100%);
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 34px 28px 26px;
        border: 1px solid #eee2e5;
        border-radius: 22px;
        background:
            radial-gradient(circle at 50% -18%, rgba(201, 169, 106, .26), transparent 58%),
            #fff;
        box-shadow: 0 16px 36px rgba(59, 7, 15, .07);
    }

    .pr-profil-foto { display: flex; justify-content: center; width: 100%; }

    .pr-profil-eyebrow {
        display: inline-block;
        margin-top: 18px;
        padding: 4px 12px;
        border-radius: 999px;
        border: 1px solid #ecd9c0;
        background: #FBF3E4;
        color: #7a5a17;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .pr-profil h1 {
        margin: 10px 0 2px;
        font-size: clamp(21px, 2.6vw, 27px);
        font-weight: 800;
        color: #2b2224;
        text-align: center;
        word-break: break-word;
    }

    .pr-profil-jabatan {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #6B0D1B;
        text-align: center;
    }

    .pr-profil-petunjuk {
        margin: 12px 0 0;
        max-width: 430px;
        font-size: 12.5px;
        line-height: 1.6;
        color: #8b7c80;
        text-align: center;
    }

    .pr-profil-judul {
        margin: 24px 0 0;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #8b7c80;
        text-align: center;
    }

    .pr-identitas {
        width: 100%;
        max-width: 480px;
        margin: 12px auto 0;
        display: grid;
        gap: 4px;
    }

    .pr-identitas > div {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 11px 14px;
        border-radius: 11px;
        background: #fdfafb;
        border: 1px solid #f4eaec;
    }

    .pr-identitas dt {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin: 0;
        flex-shrink: 0;
        font-size: 12.5px;
        font-weight: 700;
        color: #8b7c80;
    }

    .pr-identitas dd {
        margin: 0;
        font-size: 13.5px;
        font-weight: 700;
        color: #33292b;
        text-align: right;
        word-break: break-word;
    }

    .pr-identitas-ikon {
        display: inline-grid;
        place-items: center;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: #F8EDEF;
        color: #6B0D1B;
    }

    .pr-identitas-ikon svg { width: 15px; height: 15px; }

    .pr-profil-aksi {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-top: 22px;
        width: 100%;
    }

    .pr-profil-catatan {
        margin: 16px 0 0;
        max-width: 460px;
        font-size: 12px;
        line-height: 1.6;
        color: #9a8b8f;
        text-align: center;
    }

    .pr-profil-catatan a { color: #6B0D1B; font-weight: 700; text-decoration: none; }
    .pr-profil-catatan a:hover { text-decoration: underline; }

    .pr-daftar { display: grid; gap: 0; margin: 0; }
    .pr-daftar > div { display: flex; justify-content: space-between; gap: 14px; padding: 9px 0; border-bottom: 1px dashed #f0e3e5; }
    .pr-daftar > div:last-child { border-bottom: 0; }
    .pr-daftar dt { margin: 0; font-size: 12.5px; color: #8b7c80; font-weight: 600; }
    .pr-daftar dd { margin: 0; font-size: 13px; font-weight: 700; color: #33292b; text-align: right; word-break: break-word; }

    .pr-badge {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .pr-badge--aktif { background: #eaf7ee; color: #1f6b3a; }
    .pr-badge--menunggu { background: #fdf3e2; color: #8a5a10; }
    .pr-badge--nonaktif { background: #fdeef0; color: #8a1224; }

    .pr-tabel { width: 100%; border-collapse: collapse; font-size: 13px; }
    .pr-tabel th, .pr-tabel td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f2e8ea; vertical-align: top; }
    .pr-tabel th { width: 38%; font-size: 12px; font-weight: 700; color: #8b7c80; text-transform: uppercase; letter-spacing: .05em; }
    .pr-tabel td { font-weight: 600; color: #33292b; word-break: break-word; }
    .pr-tabel tr:last-child th, .pr-tabel tr:last-child td { border-bottom: 0; }

    .pr-kosong {
        padding: 22px;
        border: 1px dashed #e3d5d9;
        border-radius: 12px;
        background: #fdfafb;
        text-align: center;
        font-size: 13px;
        color: #8b7c80;
        line-height: 1.55;
    }

    /* Notifikasi hasil simpan data pribadi. */
    .pr-alert {
        padding: 12px 15px;
        border-radius: 12px;
        border: 1px solid #f0d7dc;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.5;
    }

    .pr-alert--sukses { background: #eef8f1; border-color: #cfe8d8; color: #1f6b3a; }
    .pr-alert--gagal { background: #fdeef0; border-color: #f2ccd2; color: #8a1224; }
    .pr-alert ul { margin: 6px 0 0; padding-left: 18px; font-weight: 600; }

    .pr-hero-jabatan {
        margin: 0 0 2px !important;
        font-size: 13px !important;
        font-weight: 700;
        color: #f4dfb2 !important;
        letter-spacing: .01em;
    }

    .pr-field select,
    .pr-field textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e4d3d7;
        border-radius: 10px;
        background: #fff;
        color: #33292b;
        font-family: inherit;
        font-size: 13.5px;
        line-height: 1.5;
        outline: none;
        transition: border-color .16s ease, box-shadow .16s ease;
    }

    .pr-field select:focus,
    .pr-field textarea:focus {
        border-color: #C9A96A;
        box-shadow: 0 0 0 3px rgba(201, 169, 106, .18);
    }

    .pr-field textarea { min-height: 78px; resize: vertical; }

    /* Mode baca dan mode ubah pada halaman Data Saya. */
    .pr-baca[hidden],
    .pr-ubah[hidden] { display: none !important; }

    .pr-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 14px;
    }

    .pr-catatan {
        margin: 12px 0 0;
        font-size: 12.5px;
        color: #8b7c80;
        line-height: 1.6;
    }

    .pr-catatan a { color: #6B0D1B; font-weight: 700; text-decoration: none; }
    .pr-catatan a:hover { text-decoration: underline; }

    @media (max-width: 900px) {
        .pr-grid { grid-template-columns: minmax(0, 1fr); }
    }

    @media (max-width: 480px) {
        .pr-hero { padding: 18px; }
        .pr-card { padding: 16px; }
        .pr-daftar > div { flex-direction: column; gap: 2px; }
        .pr-daftar dd { text-align: left; }

        .pr-profil { padding: 26px 16px 22px; }
        .pr-identitas > div { flex-direction: column; align-items: flex-start; gap: 4px; padding: 10px 12px; }
        .pr-identitas dd { text-align: left; }
        .pr-profil-aksi .pr-btn { flex: 1 1 100%; }
    }
</style>
