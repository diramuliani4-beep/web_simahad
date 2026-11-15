@extends('layouts.mahasiswa')

@section('title', 'Surat & Perizinan - SIMAHAD')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
.surat-perizinan-page {
    --primary-maroon: #6e001b;
    --primary-maroon-hover: #580015;
    --maroon-tint: #faedf0;
    --maroon-border: #7c1a2e;
    --text-dark: #111827;
    --text-secondary: #4b5563;
    --text-muted: #6b7280;
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: transparent;
    color: var(--text-dark);
    min-width: 0;
    width: 100%;
}
.surat-perizinan-page, .surat-perizinan-page *, .surat-perizinan-page *::before, .surat-perizinan-page *::after { box-sizing: border-box; }
.surat-perizinan-page a { color: inherit; text-decoration: none; }
.surat-perizinan-page button, .surat-perizinan-page input, .surat-perizinan-page select, .surat-perizinan-page textarea { font: inherit; }
.surat-perizinan-page .surat-content { min-width:0; padding:0; display:flex; flex-direction:column; background:transparent; }
.surat-perizinan-page .page-header {
            margin-bottom: 1.5rem;
        }
.surat-perizinan-page .breadcrumb {
            color: var(--primary-maroon);
            font-size: .9rem;
            font-weight: 600;
            margin-bottom: .55rem;
        }
.surat-perizinan-page .page-header h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: .4rem;
        }
.surat-perizinan-page .page-header p {
            font-size: .95rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }
.surat-perizinan-page .section-title {
            font-size: 1rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: .85rem;
        }
.surat-perizinan-page .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
.surat-perizinan-page .summary-card {
            background: #fff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 14px;
            min-height: 115px;
            padding: 1rem 1.15rem;
        }
.surat-perizinan-page .summary-icon {
            font-size: 1.2rem;
            margin-bottom: .35rem;
        }
.surat-perizinan-page .waiting { color: #c9912d; }
.surat-perizinan-page .approved { color: #40956c; }
.surat-perizinan-page .rejected { color: #d65f5f; }
.surat-perizinan-page .summary-number {
            font-size: 1.5rem;
            font-weight: 800;
        }
.surat-perizinan-page .summary-label {
            color: var(--text-muted);
            font-size: .8rem;
            margin-top: .2rem;
        }
.surat-perizinan-page .request-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.6rem;
        }
.surat-perizinan-page .request-card {
            border: 1.5px solid var(--maroon-border);
            border-radius: 14px;
            background: #fff;
            min-height: 185px;
            padding: 1.1rem 1.25rem;
            transition: .2s;
        }
.surat-perizinan-page .request-card:hover {
            box-shadow: 0 4px 14px rgba(110,0,27,.06);
            transform: translateY(-2px);
        }
.surat-perizinan-page .request-icon {
            width: 46px;
            height: 46px;
            background: #fdf2f4;
            color: var(--primary-maroon);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: .8rem;
        }
.surat-perizinan-page .request-card h3 {
            font-size: .95rem;
            font-weight: 800;
            margin-bottom: .45rem;
        }
.surat-perizinan-page .request-card p {
            font-size: .82rem;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: .65rem;
        }
.surat-perizinan-page .request-link {
            border: none;
            background: none;
            color: var(--primary-maroon);
            font-size: .84rem;
            font-weight: 800;
            cursor: pointer;
        }
.surat-perizinan-page .request-link:hover {
            text-decoration: underline;
        }
.surat-perizinan-page .history-card {
            background: #fff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 14px;
            overflow: hidden;
        }
.surat-perizinan-page .history-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background .2s;
        }
.surat-perizinan-page .history-item:last-child {
            border-bottom: none;
        }
.surat-perizinan-page .history-item:hover {
            background: #fff9fa;
        }
.surat-perizinan-page .history-icon {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            background: #fdf2f4;
            color: var(--primary-maroon);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.15rem;
        }
.surat-perizinan-page .history-info {
            flex: 1;
            min-width: 0;
        }
.surat-perizinan-page .history-info h3 {
            font-size: .9rem;
            font-weight: 800;
            margin-bottom: .3rem;
        }
.surat-perizinan-page .history-date {
            font-size: .78rem;
            color: var(--text-muted);
        }
.surat-perizinan-page .status {
            font-size: .78rem;
            font-weight: 700;
            margin-top: .25rem;
        }
.surat-perizinan-page .status.pending { color: #c9912d; }
.surat-perizinan-page .status.accepted { color: #40956c; }
.surat-perizinan-page .status.denied { color: #d65f5f; }
.surat-perizinan-page .history-arrow {
            color: #888;
            font-size: .9rem;
        }
.surat-perizinan-page .empty-history {
            padding: 2rem;
            text-align: center;
            color: #888;
            font-size: .85rem;
        }
.surat-perizinan-page .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1400;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(2px);
            padding: 20px;
            align-items: center;
            justify-content: center;
        }
.surat-perizinan-page .modal.show {
            display: flex;
        }
.surat-perizinan-page .modal-box {
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1.6rem;
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid var(--maroon-border);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,.15);
        }
.surat-perizinan-page .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 1.2rem;
        }
.surat-perizinan-page .modal-header h2 {
            color: var(--primary-maroon);
            font-size: 1.25rem;
            font-weight: 800;
        }
.surat-perizinan-page .close-modal {
            background: none;
            border: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: #777;
        }
.surat-perizinan-page .form-group {
            margin-bottom: 1rem;
        }
.surat-perizinan-page .form-group label {
            display: block;
            font-size: .85rem;
            font-weight: 700;
            margin-bottom: .45rem;
        }
.surat-perizinan-page .form-group input, .surat-perizinan-page .form-group select, .surat-perizinan-page .form-group textarea {
            width: 100%;
            padding: .7rem .75rem;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: .85rem;
            outline: none;
            background: #fff;
        }
.surat-perizinan-page .form-group input:focus, .surat-perizinan-page .form-group select:focus, .surat-perizinan-page .form-group textarea:focus {
            border-color: var(--primary-maroon);
            box-shadow: 0 0 0 3px rgba(110,0,27,.08);
        }
.surat-perizinan-page .form-group textarea {
            resize: vertical;
            min-height: 90px;
        }
.surat-perizinan-page .form-note {
            font-size: .75rem;
            color: #888;
            margin-top: .35rem;
        }
.surat-perizinan-page .btn-submit {
            width: 100%;
            padding: .8rem;
            background: var(--primary-maroon);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 800;
            cursor: pointer;
        }
.surat-perizinan-page .btn-submit:hover {
            background: var(--primary-maroon-hover);
        }
.surat-perizinan-page .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: .8rem 0;
            border-bottom: 1px solid #eee;
            font-size: .85rem;
        }
.surat-perizinan-page .detail-row span:first-child {
            color: #777;
            flex-shrink: 0;
        }
.surat-perizinan-page .detail-row span:last-child {
            text-align: right;
            font-weight: 600;
            overflow-wrap: anywhere;
        }
.surat-perizinan-page .alert {
            display: none;
            margin-bottom: 1rem;
            border-radius: 9px;
            padding: .75rem;
            font-size: .82rem;
        }
.surat-perizinan-page .alert.show { display: block; }
.surat-perizinan-page .alert.success {
            color: #246b46;
            background: #e9f7ef;
            border: 1px solid #b7e3c8;
        }
.surat-perizinan-page .alert.error {
            color: #a21c2e;
            background: #fff0f0;
            border: 1px solid #f2c0c0;
        }
.surat-perizinan-page .info-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(2px);
            z-index: 1410;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
.surat-perizinan-page .info-modal-overlay.active {
            display: flex;
        }
.surat-perizinan-page .info-modal-box {
            background: #fff;
            border-radius: 16px;
            max-width: 480px;
            width: 100%;
            padding: 1.85rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,.15);
            position: relative;
            border: 1.5px solid var(--maroon-border);
        }
.surat-perizinan-page .info-modal-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary-maroon);
            margin-bottom: .75rem;
        }
.surat-perizinan-page .info-modal-body {
            font-size: .9rem;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 1.25rem;
            overflow-wrap: anywhere;
        }
.surat-perizinan-page .btn-info-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #94a3b8;
        }
.surat-perizinan-page .btn-info-action {
            width: 100%;
            background: var(--primary-maroon);
            color: #fff;
            padding: .75rem;
            border-radius: 10px;
            border: none;
            font-weight: 800;
            font-size: .9rem;
            cursor: pointer;
        }
.surat-perizinan-page .btn-info-action:hover {
            background: var(--primary-maroon-hover);
        }
@media (max-width: 1100px) {
.surat-perizinan-page .surat-content { min-width:0; padding:0; display:flex; flex-direction:column; background:transparent; }
.surat-perizinan-page .summary-grid {
                gap: .75rem;
            }
.surat-perizinan-page .request-grid {
                gap: .75rem;
            }
}
@media (max-width: 992px) {
.surat-perizinan-page .surat-content { min-width:0; padding:0; display:flex; flex-direction:column; background:transparent; }
}
@media (max-width: 650px) {
.surat-perizinan-page .page-header h1 {
                font-size: 1.5rem;
            }
.surat-perizinan-page .summary-grid {
                grid-template-columns: 1fr;
            }
.surat-perizinan-page .summary-card {
                min-height: 95px;
            }
.surat-perizinan-page .request-grid {
                grid-template-columns: 1fr;
            }
.surat-perizinan-page .history-item {
                padding: .85rem;
                gap: .7rem;
            }
.surat-perizinan-page .history-icon {
                width: 40px;
                height: 40px;
            }
.surat-perizinan-page .detail-row {
                gap: 10px;
                font-size: .8rem;
            }
}
.surat-perizinan-page .sp-alert {
                margin: 0 20px 18px;
                padding: 12px 16px;
                border-radius: 10px;
                font-size: 14px;
                line-height: 1.55;
            }
.surat-perizinan-page .sp-alert ul {
                margin: 6px 0 0 18px;
            }
.surat-perizinan-page .sp-alert--sukses {
                background: #eaf6ee;
                border: 1px solid #2f7d4f;
                color: #1c5334;
            }
.surat-perizinan-page .sp-alert--gagal {
                background: #fdeff1;
                border: 1px solid #800d22;
                color: #800d22;
            }
.surat-perizinan-page .sp-kuota {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin: 0 20px 26px;
                padding: 18px 22px;
                border-radius: 16px;
                border: 1px solid #C9A96A;
                background: linear-gradient(135deg, #6B0D1B 0%, #800d22 100%);
                color: #fff;
                box-shadow: 0 4px 10px rgba(107, 13, 27, 0.25);
            }
.surat-perizinan-page .sp-kuota__judul {
                display: inline-flex;
                align-items: center;
                gap: 9px;
                font-size: 17px;
                font-weight: 700;
                color: #F3E3BF;
            }
.surat-perizinan-page .sp-kuota__info p {
                margin: 5px 0 0;
                font-size: 13.5px;
                color: #f6e7ea;
            }
.surat-perizinan-page .sp-kuota__angka {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                gap: 2px;
            }
.surat-perizinan-page .sp-kuota__angka span {
                font-size: 24px;
                font-weight: 800;
                color: #C9A96A;
            }
.surat-perizinan-page .sp-kuota__angka small {
                font-size: 12.5px;
                color: #f6e7ea;
            }
.surat-perizinan-page .sp-table-wrap {
                margin: 0 20px 26px;
                overflow-x: auto;
                border: 1px solid #d9c2c8;
                border-radius: 14px;
                background: #fff;
            }
.surat-perizinan-page .sp-table {
                width: 100%;
                min-width: 860px;
                border-collapse: collapse;
            }
.surat-perizinan-page .sp-table thead {
                background: #6B0D1B;
                color: #fff;
            }
.surat-perizinan-page .sp-table th, .surat-perizinan-page .sp-table td {
                padding: 11px 13px;
                font-size: 13.5px;
                text-align: left;
                border-bottom: 1px solid #eddde1;
                vertical-align: middle;
            }
.surat-perizinan-page .sp-table th {
                font-weight: 700;
                white-space: nowrap;
            }
.surat-perizinan-page .sp-table tbody tr:hover {
                background: #fdf6f7;
            }
.surat-perizinan-page .sp-table tbody tr:last-child td {
                border-bottom: none;
            }
.surat-perizinan-page .sp-empty {
                text-align: center !important;
                color: #6b7280;
                padding: 22px 12px !important;
            }
.surat-perizinan-page .sp-badge {
                display: inline-block;
                padding: 4px 11px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 700;
                white-space: nowrap;
            }
.surat-perizinan-page .sp-badge--menunggu { background: #fdf1d8; color: #8a5a00; border: 1px solid #e0b658; }
.surat-perizinan-page .sp-badge--disetujui { background: #e7f6ec; color: #1e6b3a; border: 1px solid #7fbf95; }
.surat-perizinan-page .sp-badge--ditolak { background: #fdeaea; color: #a01c2c; border: 1px solid #e2969f; }
.surat-perizinan-page .sp-badge--perbaikan { background: #fdf1d8; color: #8a4b00; border: 1px solid #e0a558; }
.surat-perizinan-page .sp-badge--selesai { background: #e8eefb; color: #22457f; border: 1px solid #93a9d6; }
.surat-perizinan-page .sp-badge--dibatalkan { background: #eef0f3; color: #4b5563; border: 1px solid #b9c0ca; }
.surat-perizinan-page .sp-aksi {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                align-items: center;
            }
.surat-perizinan-page .sp-aksi form {
                display: inline;
            }
.surat-perizinan-page .sp-link {
                border: 1px solid #6B0D1B;
                background: #fff;
                color: #6B0D1B;
                font-size: 12.5px;
                font-weight: 600;
                padding: 5px 11px;
                border-radius: 8px;
                cursor: pointer;
                white-space: nowrap;
            }
.surat-perizinan-page .sp-link:hover {
                background: #6B0D1B;
                color: #fff;
            }
.surat-perizinan-page .sp-link--emas {
                border-color: #C9A96A;
                background: #C9A96A;
                color: #4a1720;
            }
.surat-perizinan-page .sp-link--danger {
                border-color: #a01c2c;
                color: #a01c2c;
            }
.surat-perizinan-page .sp-link--danger:hover {
                background: #a01c2c;
                color: #fff;
            }
.surat-perizinan-page .sp-detail-group {
                margin-bottom: 18px;
                padding-bottom: 14px;
                border-bottom: 1px dashed #e3cdd3;
            }
.surat-perizinan-page .sp-detail-group:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }
.surat-perizinan-page .sp-detail-group h4 {
                margin: 0 0 10px;
                font-size: 14px;
                color: #6B0D1B;
                text-transform: uppercase;
                letter-spacing: .04em;
            }
.surat-perizinan-page .sp-detail-row {
                display: flex;
                justify-content: space-between;
                gap: 18px;
                padding: 5px 0;
                font-size: 14px;
            }
.surat-perizinan-page .sp-detail-row span:first-child {
                color: #6b7280;
                flex-shrink: 0;
            }
.surat-perizinan-page .sp-detail-row span:last-child {
                text-align: right;
                color: #111827;
                font-weight: 600;
                word-break: break-word;
            }
@media (max-width: 768px) {
.surat-perizinan-page .sp-kuota, .surat-perizinan-page .sp-table-wrap, .surat-perizinan-page .sp-alert {
                    margin-left: 6px;
                    margin-right: 6px;
                }
.surat-perizinan-page .sp-kuota__angka {
                    align-items: flex-start;
                }
}
</style>
@endpush

@section('content')
<div class="surat-perizinan-page">
    <div class="surat-content">
@if (session('success'))
            <div class="sp-alert sp-alert--sukses">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="sp-alert sp-alert--gagal">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="sp-alert sp-alert--gagal">
                <strong>Periksa kembali data pengajuan:</strong>
                <ul>
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <section class="page-header">
            <div class="breadcrumb">Dashboard / Surat &amp; Perizinan</div>
            <h1>Surat &amp; Perizinan</h1>
            <p>Ajukan surat dan perizinan serta pantau status pengajuanmu.</p>
        </section>
        <section>
            <h2 class="section-title">Ringkasan Pengajuan</h2>
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-icon waiting"><i class="fa-regular fa-clock"></i></div>
                    <div class="summary-number" id="countPending">{{ $jumlahMenunggu }}</div>
                    <div class="summary-label">Menunggu</div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon approved"><i class="fa-regular fa-circle-check"></i></div>
                    <div class="summary-number" id="countApproved">{{ $jumlahDisetujui }}</div>
                    <div class="summary-label">Disetujui</div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon rejected"><i class="fa-regular fa-circle-xmark"></i></div>
                    <div class="summary-number" id="countRejected">{{ $jumlahDitolak }}</div>
                    <div class="summary-label">Ditolak</div>
                </div>
            </div>
            <div class="sp-kuota">
                <div class="sp-kuota__info">
                    <span class="sp-kuota__judul">
                        <i class="fa-solid fa-house"></i>
                        Kuota Izin Pulang Mingguan
                    </span>
                    <p>Periode {{ $kuotaPulang['bulan'] }} &middot; maksimal {{ $kuotaPulang['maksimal'] }} kali per bulan</p>
                </div>
                <div class="sp-kuota__angka">
                    <span>{{ $kuotaPulang['terpakai'] }} / {{ $kuotaPulang['maksimal'] }}</span>
                    <small>Sisa {{ $kuotaPulang['sisa'] }} slot bulan ini</small>
                </div>
            </div>
            <h2 class="section-title">Buat Pengajuan</h2>
            <div class="request-grid">
                <div class="request-card">
                    <div class="request-icon"><i class="fa-solid fa-building-columns"></i></div>
                    <h3>Izin Kegiatan Fakultas/Prodi</h3>
                    <p>Kegiatan resmi fakultas atau program studi di luar Ma’had.</p>
                    <a href="{{ route('surat.perizinan.mhs.kegiatan', ['jenis' => 'kegiatan_fakultas']) }}" class="request-link">
                        Buat Pengajuan &rarr;
                    </a>
                </div>
                <div class="request-card">
                    <div class="request-icon"><i class="fa-solid fa-calendar-check"></i></div>
                    <h3>Izin Kegiatan Kampus</h3>
                    <p>Kegiatan kampus dengan surat resmi penyelenggara.</p>
                    <a href="{{ route('surat.perizinan.mhs.kegiatan', ['jenis' => 'kegiatan_kampus']) }}" class="request-link">
                        Buat Pengajuan &rarr;
                    </a>
                </div>
                <div class="request-card">
                    <div class="request-icon"><i class="fa-solid fa-file-signature"></i></div>
                    <h3>Surat Pulang Semester</h3>
                    <p>Unduh surat, minta tanda tangan orang tua, lalu unggah kembali.</p>
                    <a href="{{ route('surat.perizinan.mhs.semester') }}" class="request-link">
                        Buka Surat Semester &rarr;
                    </a>
                </div>
                <div class="request-card">
                    <div class="request-icon"><i class="fa-solid fa-house"></i></div>
                    <h3>Izin Pulang</h3>
                    <p>Ajukan izin pulang dan isi rencana keberangkatan serta kepulangan.</p>
                    <a href="{{ route('surat.perizinan.mhs.pengajuan') }}" class="request-link">
                        Buat Pengajuan &rarr;
                    </a>
                </div>
                <div class="request-card">
                    <div class="request-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <h3>Riwayat Pengajuan</h3>
                    <p>Lihat pengajuan sebelumnya dan statusnya.</p>
                    <button class="request-link" type="button" onclick="showHistory()">
                        Lihat Riwayat &rarr;
                    </button>
                </div>
            </div>
            <h2 class="section-title">Riwayat Pengajuan</h2>
            <div class="sp-table-wrap">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th>Tanggal Ajuan</th>
                            <th>Jenis Izin</th>
                            <th>Periode</th>
                            <th>Tujuan / Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuans as $pengajuan)
                            <tr>
                                <td>{{ $pengajuan->created_at?->locale('id')->translatedFormat('d M Y') }}</td>
                                <td>{{ $pengajuan->labelJenis() }}</td>
                                <td>{{ $pengajuan->periodeTeks() }}</td>
                                <td>{{ $pengajuan->tujuanTeks() }}</td>
                                <td>
                                    <span class="sp-badge sp-badge--{{ $pengajuan->kelasStatus() }}">
                                        {{ $pengajuan->labelStatus() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="sp-aksi">
                                        <button
                                            type="button"
                                            class="sp-link"
                                            data-detail="{{ json_encode([
                                                'jenis' => $pengajuan->labelJenis(),
                                                'status' => $pengajuan->labelStatus(),
                                                'diajukan' => $pengajuan->created_at?->locale('id')->translatedFormat('d M Y H:i') ?? '-',
                                                'periode' => $pengajuan->periodeTeks(),
                                                'durasi' => $pengajuan->durasi_hari ? $pengajuan->durasi_hari.' hari' : '-',
                                                'lokasi' => $pengajuan->tujuanTeks(),
                                                'kegiatan' => $pengajuan->nama_kegiatan ?: '-',
                                                'penyelenggara' => $pengajuan->penyelenggara ?: '-',
                                                'waktu' => $pengajuan->waktuTeks(),
                                                'alasan' => $pengajuan->alasan ?: '-',
                                                'verifikator' => $pengajuan->keputusan_nama ?: '-',
                                                'diverifikasi' => $pengajuan->keputusan_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-',
                                                'catatan' => $pengajuan->catatan_musyrifah ?: '-',
                                                'nomor_surat' => $pengajuan->nomor_surat ?: '-',
                                                'dokumen' => $pengajuan->punyaDokumen() ? route('surat.perizinan.mhs.dokumen', [$pengajuan, 'pendukung']) : null,
                                                'tertanda' => $pengajuan->punyaSuratTertanda() ? route('surat.perizinan.mhs.dokumen', [$pengajuan, 'tertanda']) : null,
                                                'cetak' => $pengajuan->suratSemester() && $pengajuan->nomor_surat ? route('surat.perizinan.mhs.semester.cetak', $pengajuan) : null,
                                            ]) }}"
                                            onclick="bukaDetail(this)"
                                        >
                                            Detail
                                        </button>
                                        @if (in_array($pengajuan->status, ['menunggu', 'perlu_perbaikan'], true))
                                            <form
                                                method="POST"
                                                action="{{ route('surat.perizinan.mhs.batalkan', $pengajuan) }}"
                                                onsubmit="return confirm('Batalkan pengajuan ini? Slot kuota akan dikembalikan.')"
                                            >
                                                @csrf
                                                <button type="submit" class="sp-link sp-link--danger">Batalkan</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="sp-empty">
                                    Belum ada pengajuan. Silakan buat pengajuan melalui menu di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
<!-- ================= MODAL PENGAJUAN ================= -->
<div class="modal" id="formModal">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="formTitle">Buat Pengajuan</h2>
            <button type="button" class="close-modal" onclick="closeModal('formModal')">&times;</button>
        </div>
        <div id="formAlert" class="alert"></div>
        <form id="permissionForm" onsubmit="submitForm(event)">
            <div class="form-group">
                <label for="jenis">Jenis Pengajuan</label>
                <select id="jenis" name="jenis" required onchange="updateFormFields()">
                    <option value="">Pilih jenis pengajuan</option>
                    <option value="Pengajuan Surat">Pengajuan Surat</option>
                    <option value="Izin Kegiatan">Izin Kegiatan</option>
                    <option value="Izin Pulang">Izin Pulang</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggalMulai">Tanggal Mulai / Keberangkatan</label>
                <input type="date" id="tanggalMulai" name="tanggalMulai" required>
            </div>
            <div class="form-group" id="tanggalKembaliGroup">
                <label for="tanggalKembali">Tanggal Kembali</label>
                <input type="date" id="tanggalKembali" name="tanggalKembali">
            </div>
            <div class="form-group">
                <label for="keperluan">Keperluan / Alasan</label>
                <textarea id="keperluan" name="keperluan" placeholder="Jelaskan keperluan pengajuan..." required></textarea>
            </div>
            <div class="form-group">
                <label for="lampiran">Lampiran (opsional)</label>
                <input type="file" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png">
                <div class="form-note">Format yang diperbolehkan: PDF, JPG, JPEG, PNG. Maksimal 5 MB.</div>
            </div>
            <button type="submit" class="btn-submit">Kirim Pengajuan</button>
        </form>
    </div>
</div>
<!-- ================= MODAL DETAIL ================= -->
<div class="modal" id="detailModal">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Detail Pengajuan</h2>
            <button type="button" class="close-modal" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div id="detailContent"></div>
    </div>
</div>
<!-- ================= MODAL INFORMASI ================= -->
<div class="info-modal-overlay" id="infoModal" onclick="closeInfoModalOnBackdrop(event)">
    <div class="info-modal-box">
        <button type="button" class="btn-info-close" onclick="closeInfoModal()">&times;</button>
        <h3 class="info-modal-title" id="infoModalTitle">Informasi</h3>
        <div class="info-modal-body" id="infoModalBody">Konten</div>
        <button type="button" class="btn-info-action" onclick="closeInfoModal()">Tutup</button>
    </div>
</div>
<script type="text/plain" data-nonaktif="skrip-contoh-lama">
    {{-- Skrip contoh lama dinonaktifkan agar tidak menimpa data dari database. --}}
    // Contoh data tampilan. Belum terhubung ke database.
    let submissions = [
        {
            jenis: 'Izin Pulang',
            tanggal: '12 Oktober 2026',
            mulai: '2026-10-12',
            kembali: '',
            keperluan: 'Keperluan keluarga',
            status: 'Menunggu Persetujuan'
        },
        {
            jenis: 'Pengajuan Surat',
            tanggal: '10 Oktober 2026',
            mulai: '2026-10-10',
            kembali: '',
            keperluan: 'Keperluan administrasi',
            status: 'Disetujui'
        },
        {
            jenis: 'Izin Kegiatan',
            tanggal: '8 Oktober 2026',
            mulai: '2026-10-08',
            kembali: '',
            keperluan: 'Mengikuti kegiatan di luar Ma’had',
            status: 'Ditolak'
        }
    ];
    function openForm(jenis) {
        document.getElementById('permissionForm').reset();
        document.getElementById('formTitle').textContent = jenis;
        document.getElementById('jenis').value = jenis;
        document.getElementById('formAlert').className = 'alert';
        updateFormFields();
        document.getElementById('formModal').classList.add('show');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }
    function updateFormFields() {
        const jenis = document.getElementById('jenis').value;
        const kembaliGroup = document.getElementById('tanggalKembaliGroup');
        const kembaliInput = document.getElementById('tanggalKembali');
        if (jenis === 'Izin Pulang') {
            kembaliGroup.style.display = 'block';
            kembaliInput.required = true;
        } else {
            kembaliGroup.style.display = 'none';
            kembaliInput.required = false;
            kembaliInput.value = '';
        }
    }
    function submitForm(event) {
        event.preventDefault();
        const jenis = document.getElementById('jenis').value;
        const mulai = document.getElementById('tanggalMulai').value;
        const kembali = document.getElementById('tanggalKembali').value;
        const keperluan = document.getElementById('keperluan').value.trim();
        const lampiran = document.getElementById('lampiran').files[0];
        if (!jenis || !mulai || !keperluan) {
            showFormAlert('Mohon lengkapi data pengajuan.', 'error');
            return;
        }
        if (jenis === 'Izin Pulang' && kembali && kembali < mulai) {
            showFormAlert('Tanggal kembali tidak boleh sebelum tanggal keberangkatan.', 'error');
            return;
        }
        if (lampiran && lampiran.size > 5 * 1024 * 1024) {
            showFormAlert('Ukuran lampiran maksimal 5 MB.', 'error');
            return;
        }
        const date = new Date(mulai + 'T00:00:00');
        const formattedDate = date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        submissions.unshift({
            jenis: jenis,
            tanggal: formattedDate,
            mulai: mulai,
            kembali: kembali,
            keperluan: keperluan,
            status: 'Menunggu Persetujuan'
        });
        renderHistory();
        updateSummary();
        closeModal('formModal');
        alert('Pengajuan ditambahkan pada tampilan ini. Data belum disimpan ke database.');
    }
    function showFormAlert(message, type) {
        const alertBox = document.getElementById('formAlert');
        alertBox.textContent = message;
        alertBox.className = 'alert show ' + type;
    }
    function updateSummary() {
        const pending = submissions.filter(item => item.status === 'Menunggu Persetujuan').length;
        const approved = submissions.filter(item => item.status === 'Disetujui').length;
        const rejected = submissions.filter(item => item.status === 'Ditolak').length;
        document.getElementById('countPending').textContent = pending;
        document.getElementById('countApproved').textContent = approved;
        document.getElementById('countRejected').textContent = rejected;
    }
    function renderHistory() {
        const list = document.getElementById('historyList');
        if (submissions.length === 0) {
            list.innerHTML = '<div class="empty-history">Belum ada pengajuan.</div>';
            return;
        }
        list.innerHTML = submissions.map((item, index) => {
            let statusClass = 'pending';
            if (item.status === 'Disetujui') statusClass = 'accepted';
            if (item.status === 'Ditolak') statusClass = 'denied';
            let icon = 'fa-file-lines';
            if (item.jenis === 'Izin Pulang') icon = 'fa-house';
            if (item.jenis === 'Izin Kegiatan') icon = 'fa-calendar-check';
            return `
                <div class="history-item" onclick="showDetail(${index})">
                    <div class="history-icon">
                        <i class="fa-regular ${icon}"></i>
                    </div>
                    <div class="history-info">
                        <h3>${escapeHTML(item.jenis)}</h3>
                        <div class="history-date">${escapeHTML(item.tanggal)}</div>
                        <div class="status ${statusClass}">${escapeHTML(item.status)}</div>
                    </div>
                    <i class="fa-solid fa-chevron-right history-arrow"></i>
                </div>
            `;
        }).join('');
    }
    function showDetail(index) {
        const item = submissions[index];
        if (!item) return;
        document.getElementById('detailContent').innerHTML = `
            <div class="detail-row">
                <span>Jenis Pengajuan</span>
                <span>${escapeHTML(item.jenis)}</span>
            </div>
            <div class="detail-row">
                <span>Tanggal Pengajuan</span>
                <span>${escapeHTML(item.tanggal)}</span>
            </div>
            <div class="detail-row">
                <span>Tanggal Mulai</span>
                <span>${escapeHTML(item.mulai || '-')}</span>
            </div>
            <div class="detail-row">
                <span>Tanggal Kembali</span>
                <span>${escapeHTML(item.kembali || '-')}</span>
            </div>
            <div class="detail-row">
                <span>Keperluan</span>
                <span>${escapeHTML(item.keperluan)}</span>
            </div>
            <div class="detail-row">
                <span>Status</span>
                <span>${escapeHTML(item.status)}</span>
            </div>
        `;
        document.getElementById('detailModal').classList.add('show');
    }
    function showHistory() {
        document.getElementById('historyList').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
    function openInfoModal(title, content) {
        document.getElementById('infoModalTitle').textContent = title;
        document.getElementById('infoModalBody').innerHTML = content;
        document.getElementById('infoModal').classList.add('active');
    }
    function closeInfoModal() {
        document.getElementById('infoModal').classList.remove('active');
    }
    function closeInfoModalOnBackdrop(event) {
        if (event.target === document.getElementById('infoModal')) {
            closeInfoModal();
        }
    }
    function escapeHTML(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(char) {
            const entities = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return entities[char];
        });
    }
    // Tutup modal jika pengguna menekan area di luar kotak modal.
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.remove('show');
            }
        });
    });
    // Tutup modal dengan tombol Escape.
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal.show').forEach(modal => {
                modal.classList.remove('show');
            });
            closeInfoModal();
        }
    });
    // Menu sidebar pada layar kecil.
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarBackdrop.classList.toggle('active');
        });
        sidebarBackdrop.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarBackdrop.classList.remove('active');
        });
        sidebar.querySelectorAll('a.nav-pill').forEach(link => {
            link.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarBackdrop.classList.remove('active');
            });
        });
    }
    // Tampilkan contoh riwayat dan hitung ringkasan saat halaman dibuka.
    updateFormFields();
    renderHistory();
    updateSummary();
</script>
</div>
@endsection

@push('scripts')
<script>
function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('show');
        }
    }
    function showHistory() {
        const tabel = document.querySelector('.sp-table-wrap');
        if (tabel) {
            tabel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    function bukaDetail(tombol) {
        let data = {};
        try {
            data = JSON.parse(tombol.dataset.detail || '{}');
        } catch (error) {
            return;
        }
        const baris = (label, nilai) => `
            <div class="sp-detail-row">
                <span>${label}</span>
                <span>${escapeHTML(nilai ?? '-')}</span>
            </div>`;
        const berkas = [];
        if (data.dokumen) {
            berkas.push(`<a class="sp-link" href="${data.dokumen}" target="_blank" rel="noopener">Surat Pendukung</a>`);
        }
        if (data.tertanda) {
            berkas.push(`<a class="sp-link" href="${data.tertanda}" target="_blank" rel="noopener">Surat Bertanda Tangan</a>`);
        }
        if (data.cetak) {
            berkas.push(`<a class="sp-link sp-link--emas" href="${data.cetak}" target="_blank" rel="noopener">Cetak Surat Semester</a>`);
        }
        const isi = `
            <div class="sp-detail-group">
                <h4>Informasi Pengajuan</h4>
                ${baris('Jenis Izin', data.jenis)}
                ${baris('Status', data.status)}
                ${baris('Tanggal Ajuan', data.diajukan)}
                ${baris('Periode', data.periode)}
                ${baris('Durasi', data.durasi)}
            </div>
            <div class="sp-detail-group">
                <h4>Rincian</h4>
                ${baris('Nama Kegiatan', data.kegiatan)}
                ${baris('Penyelenggara', data.penyelenggara)}
                ${baris('Waktu Kegiatan', data.waktu)}
                ${baris('Tujuan / Lokasi', data.lokasi)}
                ${baris('Nomor Surat', data.nomor_surat)}
                ${baris('Alasan', data.alasan)}
            </div>
            <div class="sp-detail-group">
                <h4>Keputusan Musyrifah</h4>
                ${baris('Diverifikasi Oleh', data.verifikator)}
                ${baris('Waktu Verifikasi', data.diverifikasi)}
                ${baris('Catatan', data.catatan)}
            </div>
            ${berkas.length > 0 ? `<div class="sp-detail-group"><h4>Berkas</h4><div class="sp-aksi">${berkas.join(' ')}</div></div>` : ''}
        `;
        document.getElementById('detailContent').innerHTML = isi;
        document.getElementById('detailModal').classList.add('show');
    }
    function escapeHTML(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            const entities = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return entities[char];
        });
    }
    // Tutup modal saat area luar atau tombol Escape ditekan.
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.classList.remove('show');
            }
        });
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal.show').forEach(function (modal) {
                modal.classList.remove('show');
            });
        }
    });
</script>
@endpush
