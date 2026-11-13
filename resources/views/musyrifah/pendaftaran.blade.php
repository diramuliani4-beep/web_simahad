@extends('layouts.musyrifah')

@section('title', 'Pendaftaran Mahasiswa')

@section('content')
<style>
    .pd {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        color: #322a2d;
        font-size: 13px;
    }

    .pd *,
    .pd *::before,
    .pd *::after {
        box-sizing: border-box;
    }

    /* ---------------- Kepala halaman ---------------- */
    .pd-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px 14px;
        margin-bottom: 10px;
    }

    .pd-judul {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .pd-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 26px;
        height: 26px;
        border: 1px solid #ecdde1;
        border-radius: 8px;
        background: #fff;
        color: #6B0D1B;
        font-size: 15px;
        line-height: 1;
        text-decoration: none;
    }

    .pd-back:hover {
        border-color: #C9A96A;
        background: #F8EDEF;
    }

    .pd-head h1 {
        margin: 0;
        color: #211a1c;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -.2px;
    }

    .pd-summary {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .pd-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border: 1px solid #efe3e6;
        border-radius: 999px;
        background: #fff;
        color: #6f636a;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
        white-space: nowrap;
    }

    .pd-chip b {
        color: #6B0D1B;
        font-weight: 700;
    }

    .pd-chip--tunggu {
        border-color: #e9d9b8;
        background: #fdf6e8;
        color: #8c621a;
    }

    .pd-chip--tunggu b {
        color: #8c621a;
    }

    .pd-chip--perbaikan {
        border-color: #f3ddc4;
        background: #fff7ed;
        color: #9a5a12;
    }

    .pd-chip--perbaikan b {
        color: #9a5a12;
    }

    /* ---------------- Notifikasi ---------------- */
    .pd-alert {
        margin-bottom: 12px;
        padding: 9px 12px;
        border: 1px solid #e6dde0;
        border-left: 3px solid #6B0D1B;
        border-radius: 9px;
        background: #fff;
        font-size: 12px;
        line-height: 1.55;
    }

    .pd-alert--success {
        border-color: #cbe9d4;
        border-left-color: #1f7a4a;
        background: #f2fbf4;
        color: #14532d;
    }

    .pd-alert--error {
        border-color: #f3cccc;
        border-left-color: #991b1b;
        background: #fef6f6;
        color: #8f1d1d;
    }

    .pd-alert ul {
        margin: 5px 0 0;
        padding-left: 16px;
    }

    /* ---------------- Kartu & pencarian ---------------- */
    .pd-kartu {
        margin-bottom: 12px;
        padding: 12px 14px;
        border: 1px solid #efe5e7;
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(59, 7, 15, .035);
    }

    .pd-cari {
        display: flex;
        align-items: center;
        gap: 8px;
        height: 36px;
        padding: 0 11px;
        border: 1px solid #e6dde0;
        border-radius: 9px;
        background: #fdfbfb;
        transition: border-color .16s, box-shadow .16s, background .16s;
    }

    .pd-cari:focus-within {
        border-color: #C9A96A;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(201, 169, 106, .16);
    }

    .pd-cari svg {
        flex: 0 0 16px;
        width: 16px;
        height: 16px;
        color: #8b7e84;
    }

    .pd-cari input {
        width: 100%;
        height: 100%;
        border: 0;
        background: transparent;
        color: #3f353a;
        font-family: inherit;
        font-size: 12.5px;
        outline: none;
    }

    .pd-cari input::placeholder {
        color: #a79ba0;
    }

    .pd-hasil {
        display: none;
        margin-top: 9px;
        padding: 10px;
        border: 1px dashed #e6d7dc;
        border-radius: 8px;
        background: #fdfbfb;
        color: #91858a;
        font-size: 11.5px;
        text-align: center;
    }

    /* ---------------- Tabel ---------------- */
    .pd-tabel-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #f1e9eb;
        border-radius: 10px;
        -webkit-overflow-scrolling: touch;
    }

    .pd-tabel {
        width: 100%;
        min-width: 700px;
        border-collapse: collapse;
        background: #fff;
    }

    .pd-tabel thead th {
        padding: 8px 10px;
        border-bottom: 1px solid #eee1e4;
        background: #FBF6EC;
        color: #6B0D1B;
        text-align: left;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .pd-tabel tbody td {
        padding: 8px 10px;
        border-bottom: 1px solid #f4eef0;
        color: #4f4549;
        font-size: 12px;
        vertical-align: middle;
    }

    .pd-tabel tbody tr:last-child td {
        border-bottom: 0;
    }

    .pd-tabel tbody tr:hover {
        background: #fffbfc;
    }

    .pd-tabel .pd-no {
        width: 44px;
        color: #91858a;
        font-size: 11px;
        text-align: center;
    }

    .pd-tabel .pd-aksi-kolom {
        width: 122px;
        text-align: right;
    }

    .pd-nama {
        color: #2c2226;
        font-size: 12.5px;
        font-weight: 700;
        line-height: 1.35;
    }

    .pd-kategori {
        display: inline-block;
        margin-top: 3px;
        padding: 1px 7px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .pd-kategori--umum {
        background: #F8EDEF;
        color: #6B0D1B;
    }

    .pd-kategori--kip {
        background: #fdf3e2;
        color: #96610a;
    }

    .pd-nim {
        color: #4f4549;
        font-size: 12px;
        white-space: nowrap;
    }

    .pd-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .pd-status--menunggu {
        background: #fef3c7;
        color: #92400e;
    }

    .pd-status--disetujui {
        background: #dcfce7;
        color: #166534;
    }

    .pd-status--ditolak {
        background: #fee2e2;
        color: #991b1b;
    }

    .pd-status--perbaikan {
        background: #ffedd5;
        color: #c2410c;
    }

    .pd-validator {
        display: block;
        margin-top: 3px;
        color: #8b7e84;
        font-size: 10.5px;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .pd-kosong {
        padding: 18px 10px;
        color: #91858a;
        font-size: 12px;
        text-align: center;
    }

    /* ---------------- Tombol ---------------- */
    .pd-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-height: 28px;
        padding: 4px 10px;
        border: 1px solid transparent;
        border-radius: 8px;
        background: #fff;
        font-family: inherit;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: background .16s, border-color .16s, color .16s;
    }

    .pd-btn--detail {
        border-color: #6B0D1B;
        background: #6B0D1B;
        color: #fff;
    }

    .pd-btn--detail:hover {
        border-color: #52070f;
        background: #52070f;
    }

    .pd-btn--outline {
        border-color: #e6d7dc;
        background: #fff;
        color: #6B0D1B;
    }

    .pd-btn--outline:hover {
        border-color: #C9A96A;
        background: #F8EDEF;
    }

    .pd-btn--approve {
        border-color: #166534;
        background: #166534;
        color: #fff;
    }

    .pd-btn--approve:hover {
        border-color: #14532d;
        background: #14532d;
    }

    .pd-btn--revise {
        border-color: #d97706;
        background: #d97706;
        color: #fff;
    }

    .pd-btn--revise:hover {
        border-color: #b45309;
        background: #b45309;
    }

    .pd-btn--reject {
        border-color: #991b1b;
        background: #991b1b;
        color: #fff;
    }

    .pd-btn--reject:hover {
        border-color: #7f1d1d;
        background: #7f1d1d;
    }

    .pd-btn--document {
        border-color: #e3c9a6;
        background: #fffaf1;
        color: #6B0D1B;
    }

    .pd-btn--document:hover {
        border-color: #C9A96A;
        background: #fdf3e2;
    }

    .pd-btn:focus-visible {
        outline: 3px solid rgba(201, 169, 106, .45);
        outline-offset: 1px;
    }

    /* ---------------- Modal detail ---------------- */
    .pd-modal {
        width: min(760px, calc(100% - 24px));
        max-height: min(88vh, 860px);
        margin: auto;
        padding: 0;
        border: none;
        border-radius: 13px;
        color: #322a2d;
        box-shadow: 0 20px 55px rgba(0, 0, 0, .28);
        overflow: auto;
    }

    .pd-modal::backdrop {
        background: rgba(16, 10, 12, .55);
    }

    .pd-modal-head {
        position: sticky;
        top: 0;
        z-index: 2;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px;
        background: #6B0D1B;
        color: #fff;
    }

    .pd-modal-head h2 {
        margin: 0 0 3px;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
    }

    .pd-modal-head p {
        margin: 0;
        font-size: 11.5px;
        line-height: 1.45;
        opacity: .9;
        overflow-wrap: anywhere;
    }

    .pd-modal-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 26px;
        width: 26px;
        height: 26px;
        border: 1px solid rgba(255, 255, 255, .55);
        border-radius: 50%;
        background: transparent;
        color: #fff;
        font-size: 17px;
        line-height: 1;
        cursor: pointer;
    }

    .pd-modal-close:hover {
        background: rgba(255, 255, 255, .16);
    }

    .pd-modal-body {
        padding: 12px 16px 16px;
    }

    .pd-section {
        margin-bottom: 14px;
    }

    .pd-section:last-of-type {
        margin-bottom: 10px;
    }

    .pd-section-title {
        position: relative;
        margin: 0 0 8px;
        padding-left: 8px;
        color: #6B0D1B;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.35;
    }

    .pd-section-title::before {
        content: "";
        position: absolute;
        top: 2px;
        bottom: 2px;
        left: 0;
        width: 3px;
        border-radius: 2px;
        background: #C9A96A;
    }

    .pd-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px;
    }

    .pd-detail {
        min-width: 0;
        padding: 7px 9px;
        border: 1px solid #f1e9eb;
        border-radius: 8px;
        background: #fffcfc;
    }

    .pd-detail--full {
        grid-column: 1 / -1;
    }

    .pd-detail-label {
        display: block;
        margin-bottom: 2px;
        color: #8b7e84;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .pd-detail-value {
        display: block;
        color: #2c2226;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.45;
        overflow-wrap: anywhere;
        white-space: pre-line;
    }

    .pd-documents {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
    }

    .pd-notice {
        padding: 9px 11px;
        border: 1px solid #f3ddc4;
        border-left: 3px solid #C9A96A;
        border-radius: 8px;
        background: #fff8ee;
        color: #8c621a;
        font-size: 11.5px;
        line-height: 1.55;
    }

    .pd-action-area {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 8px;
        margin-top: 9px;
    }

    .pd-action-form {
        display: flex;
        flex-direction: column;
        padding: 10px;
        border: 1px solid #f0e7e9;
        border-radius: 9px;
        background: #fffafb;
    }

    .pd-action-form h4 {
        margin: 0 0 3px;
        color: #4d4045;
        font-size: 12px;
        font-weight: 700;
    }

    .pd-action-form p {
        margin: 0 0 7px;
        color: #8b7e84;
        font-size: 10.5px;
        line-height: 1.5;
    }

    .pd-action-form label {
        display: block;
        margin-bottom: 3px;
        color: #7d7075;
        font-size: 10.5px;
        font-weight: 600;
    }

    .pd-action-form textarea {
        display: block;
        width: 100%;
        min-height: 58px;
        margin-bottom: 8px;
        padding: 6px 8px;
        border: 1px solid #e6dde0;
        border-radius: 7px;
        background: #fff;
        color: #3f353a;
        font-family: inherit;
        font-size: 11.5px;
        line-height: 1.5;
        resize: vertical;
        outline: none;
    }

    .pd-action-form textarea:focus {
        border-color: #C9A96A;
        box-shadow: 0 0 0 3px rgba(201, 169, 106, .16);
    }

    .pd-action-form .pd-btn {
        width: 100%;
        margin-top: auto;
        min-height: 30px;
    }

    .pd-finished {
        padding: 10px 11px;
        border: 1px solid #ece5e7;
        border-radius: 8px;
        background: #fbfafa;
        color: #6f636a;
        font-size: 11.5px;
        line-height: 1.55;
    }

    @media (max-width: 760px) {
        .pd-head h1 {
            font-size: 18px;
        }

        .pd-kartu {
            padding: 10px;
        }

        .pd-kartu .pd-btn--detail {
            width: 100%;
        }

        .pd-modal-head {
            padding: 11px 13px;
        }

        .pd-modal-body {
            padding: 11px 13px 14px;
        }

        .pd-detail-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .pd-detail--full {
            grid-column: auto;
        }

        .pd-action-area {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div class="pd">
    <div class="pd-head">
        <div class="pd-judul">
            <a href="{{ route('musyrifah.dashboard') }}" class="pd-back" aria-label="Kembali ke dashboard" title="Kembali ke Dashboard">&lsaquo;</a>
            <h1>Pendaftaran Mahasiswa</h1>
        </div>

        <div class="pd-summary">
            <span class="pd-chip">Total <b>{{ $pendaftarans->count() }}</b> pendaftar</span>
            <span class="pd-chip pd-chip--tunggu">
                <b>{{ $pendaftarans->where('status', 'menunggu')->count() }}</b> menunggu validasi
            </span>
            @if ($pendaftarans->where('status', 'perlu_perbaikan')->count() > 0)
                <span class="pd-chip pd-chip--perbaikan">
                    <b>{{ $pendaftarans->where('status', 'perlu_perbaikan')->count() }}</b> perlu perbaikan
                </span>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="pd-alert pd-alert--success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="pd-alert pd-alert--error" role="alert">
            @if (session('error'))
                <div>{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <strong>Periksa kembali data validasi:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="pd-kartu">
        <label class="pd-cari">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m16 16 4 4"></path>
            </svg>
            <input type="search" id="cariMahasiswa" placeholder="Cari mahasiswa berdasarkan nama, NIM, status, atau validator" aria-label="Cari mahasiswa berdasarkan nama, NIM, status atau validator">
        </label>

        <div class="pd-hasil" id="hasilTidakDitemukan" role="status">
            Mahasiswa yang dicari tidak ditemukan.
        </div>
    </div>

    <div class="pd-tabel-wrap">
        <table class="pd-tabel">
            <thead>
                <tr>
                    <th scope="col" class="pd-no">No</th>
                    <th scope="col">Nama &amp; Kategori</th>
                    <th scope="col">NIM</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="pd-aksi-kolom">Aksi</th>
                </tr>
            </thead>
            <tbody id="daftarPendaftar">
                @forelse ($pendaftarans as $index => $pendaftaran)
                    <tr class="baris-pendaftar">
                        <td class="pd-no">{{ $index + 1 }}</td>
                        <td>
                            <div class="pd-nama">{{ $pendaftaran->nama_lengkap ?: '-' }}</div>
                            <span class="pd-kategori pd-kategori--{{ ($pendaftaran->kategori ?? 'umum') === 'kip' ? 'kip' : 'umum' }}">
                                {{ ($pendaftaran->kategori ?? 'umum') === 'kip' ? 'Mahasiswa KIP' : 'Mahasiswa Umum' }}
                            </span>
                        </td>
                        <td>
                            <span class="pd-nim">{{ $pendaftaran->user?->nim ?: '-' }}</span>
                        </td>
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
                                <span class="pd-validator">
                                    Oleh: {{ $pendaftaran->validator_nama }}
                                </span>
                            @endif
                        </td>
                        <td class="pd-aksi-kolom">
                            <button type="button" class="pd-btn pd-btn--detail pendaftaran-open-detail"
                                data-target="detail-pendaftaran-{{ $pendaftaran->id }}"
                                aria-haspopup="dialog">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="pd-kosong">Belum ada data pendaftaran mahasiswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
                <section class="pd-section">
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
                </section>

                <section class="pd-section">
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
                </section>

                <section class="pd-section">
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
                </section>

                <section class="pd-section">
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
                </section>

                @if (in_array($pendaftaran->status, ['menunggu', 'perlu_perbaikan'], true))
                    <div class="pd-notice">
                        Periksa kelengkapan berkas fisik/digital dan pembayaran. Anda dapat <strong>Menyetujui</strong>, meminta <strong>Perbaikan</strong>, atau <strong>Menolak</strong> pengajuan pendaftaran ini.
                    </div>
                    <div class="pd-action-area">
                        <form class="pd-action-form" method="POST"
                            action="{{ route('musyrifah.pendaftaran.status', $pendaftaran->id) }}"
                            onsubmit="return confirm('Yakin menyetujui pendaftaran ini? Pastikan dokumen dan pembayaran sudah diverifikasi.');">
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
                            onsubmit="return confirm('Yakin meminta perbaikan untuk pendaftaran ini? Tuliskan instruksi perbaikan dengan jelas.');">
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
                            onsubmit="return confirm('Yakin menolak pendaftaran ini? Alasan penolakan akan dicatat.');">
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
        const barisPendaftar = document.querySelectorAll('.baris-pendaftar');
        const pesanKosong = document.getElementById('hasilTidakDitemukan');

        if (inputCari && pesanKosong) {
            inputCari.addEventListener('input', function () {
                const kataKunci = inputCari.value.trim().toLowerCase();
                let jumlahDitemukan = 0;

                barisPendaftar.forEach(function (baris) {
                    const cocok = baris.textContent.toLowerCase().includes(kataKunci);
                    baris.style.display = cocok ? '' : 'none';
                    if (cocok) jumlahDitemukan++;
                });

                pesanKosong.style.display = barisPendaftar.length > 0 && jumlahDitemukan === 0 ? 'block' : 'none';
            });
        }

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
        });
    });
</script>
@endsection
