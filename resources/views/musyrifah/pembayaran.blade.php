@extends('layouts.musyrifah')

@section('title', 'Pembayaran')

@section('content')

<style>
    .musyrifah-pembayaran {
        width: 100%;
        font-family: 'Poppins', Arial, sans-serif;
        color: #111;
        padding-bottom: 40px;
    }

    .musyrifah-pembayaran * {
        box-sizing: border-box;
    }

    /* HEADER & SINKRONISASI */
    .pembayaran-heading-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 28px;
    }

    .pembayaran-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pembayaran-back {
        display: inline-flex;
        align-items: center;
        color: #111;
        font-size: 32px;
        line-height: 1;
        text-decoration: none;
        flex-shrink: 0;
        transition: transform 0.2s, color 0.2s;
    }

    .pembayaran-back:hover {
        color: #800d22;
        transform: translateX(-3px);
    }

    .pembayaran-title {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        line-height: 1.2;
    }

    .sync-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        font-size: 13px;
        color: #334155;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .pulse-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse 1.8s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .btn-sync {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #800d22;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-sync:hover {
        background: #5f0016;
    }

    .btn-sync svg {
        transition: transform 0.5s ease;
    }

    .btn-sync.spinning svg {
        transform: rotate(360deg);
    }

    /* KARTU STATISTIK */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #f1d5dc;
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        box-shadow: 0 2px 6px rgba(128, 13, 34, 0.05);
        border-left: 5px solid #800d22;
    }

    .stat-card.stat-lunas { border-left-color: #10b981; }
    .stat-card.stat-menunggu { border-left-color: #f59e0b; }
    .stat-card.stat-belum { border-left-color: #ef4444; }

    .stat-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
    }

    .stat-sub {
        font-size: 12px;
        color: #94a3b8;
    }

    /* FILTER DAN PENCARIAN */
    .filter-search-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 22px;
    }

    .status-filters {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 8px 16px;
        border-radius: 9999px;
        border: 1px solid #e2e8f0;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .filter-btn.active {
        background: #800d22;
        color: #fff;
        border-color: #800d22;
    }

    .pembayaran-search-box {
        display: flex;
        align-items: center;
        gap: 12px;
        height: 46px;
        width: 100%;
        max-width: 400px;
        padding: 0 16px;
        border-radius: 12px;
        background: #f5e6e9;
        border: 1px solid #eed6db;
    }

    .pembayaran-search-box svg {
        width: 20px;
        height: 20px;
        color: #650019;
        flex-shrink: 0;
    }

    .pembayaran-search-box input {
        width: 100%;
        height: 100%;
        border: none;
        outline: none;
        background: transparent;
        font-family: inherit;
        font-size: 15px;
        color: #111;
    }

    .pembayaran-search-box input::placeholder {
        color: #71717a;
    }

    /* TABEL PEMBAYARAN */
    .pembayaran-table-wrap {
        width: 100%;
        overflow-x: auto;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
        background: #fff;
        border: 1px solid #edd5db;
    }

    .pembayaran-table {
        width: 100%;
        min-width: 820px;
        border-collapse: separate;
        border-spacing: 0;
        background: #f5e6e9;
    }

    .pembayaran-table thead th {
        padding: 14px 14px;
        background: #650019;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
    }

    .pembayaran-table thead th:first-child {
        border-top-left-radius: 14px;
    }

    .pembayaran-table thead th:last-child {
        border-top-right-radius: 14px;
    }

    .pembayaran-table tbody td {
        padding: 14px 12px;
        text-align: center;
        vertical-align: middle;
        font-size: 15px;
        border-right: 1px solid rgba(80, 50, 50, .14);
        border-bottom: 1px solid rgba(80, 50, 50, .14);
        background: #fff;
        color: #1e293b;
    }

    .pembayaran-table tbody tr:hover td {
        background: #faf2f4;
    }

    .pembayaran-table tbody td:last-child {
        border-right: none;
    }

    .pembayaran-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* STATUS PILLS */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 13px;
        font-weight: 600;
    }

    .badge-lunas {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-menunggu {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-belum {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .badge-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .metode-info {
        font-size: 12px;
        color: #64748b;
        margin-top: 3px;
    }

    .btn-detail {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 8px;
        background: #800d22;
        color: #fff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background .2s, transform 0.1s;
    }

    .btn-detail:hover {
        background: #5f0016;
        transform: translateY(-1px);
    }

    .pembayaran-empty {
        display: none;
        padding: 40px 20px;
        text-align: center;
        color: #64748b;
        background: #fff;
        font-size: 15px;
    }

    /* MODAL DETAIL & RIWAYAT AKTIVITAS */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .modal-overlay.active {
        display: flex;
        opacity: 1;
    }

    .modal-card {
        background: #fff;
        border-radius: 18px;
        max-width: 680px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        transform: scale(0.96);
        transition: transform 0.25s ease;
    }

    .modal-overlay.active .modal-card {
        transform: scale(1);
    }

    .modal-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        background: #fdf8f9;
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
    }

    .modal-top-title {
        font-size: 20px;
        font-weight: 700;
        color: #800d22;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 26px;
        color: #64748b;
        cursor: pointer;
        line-height: 1;
        padding: 4px;
        border-radius: 6px;
    }

    .modal-close-btn:hover {
        color: #0f172a;
        background: #e2e8f0;
    }

    .modal-inner {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .info-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #800d22;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
    }

    .detail-item-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 2px;
    }

    .detail-item-val {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
    }

    /* RIWAYAT AKTIVITAS TIMELINE */
    .activity-timeline {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: 220px;
        overflow-y: auto;
        padding-right: 6px;
    }

    .timeline-item {
        display: flex;
        gap: 12px;
        position: relative;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 22px;
        bottom: -10px;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-dot {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #fde8ed;
        color: #800d22;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
        z-index: 1;
    }

    .timeline-content {
        flex: 1;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
    }

    .timeline-title {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        justify-content: space-between;
        gap: 8px;
    }

    .timeline-time {
        font-size: 11px;
        font-weight: 400;
        color: #64748b;
    }

    .timeline-desc {
        font-size: 13px;
        color: #334155;
        margin-top: 4px;
        line-height: 1.4;
    }

    .timeline-empty {
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 8px;
    }

    .modal-bottom {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-bottom-left-radius: 18px;
        border-bottom-right-radius: 18px;
        flex-wrap: wrap;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action-status {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: opacity 0.2s;
    }

    .btn-action-status:hover {
        opacity: 0.9;
    }

    .btn-mark-lunas { background: #15803d; color: #fff; }
    .btn-mark-menunggu { background: #d97706; color: #fff; }
    .btn-mark-belum { background: #b91c1c; color: #fff; }

    /* TOAST NOTIFIKASI */
    .toast-box {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 12px 20px;
        background: #1e293b;
        color: #fff;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
        z-index: 10000;
        display: none;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .detail-grid { grid-template-columns: 1fr; }
        .pembayaran-title { font-size: 24px; }
        .pembayaran-search-box { max-width: 100%; }
    }
</style>

<div class="musyrifah-pembayaran">

    <!-- HEADER DAN STATUS SINKRONISASI -->
    <div class="pembayaran-heading-row">
        <div class="pembayaran-heading">
            <a
                href="{{ route('musyrifah.dashboard') }}"
                class="pembayaran-back"
                aria-label="Kembali ke Dashboard"
                title="Kembali ke Dashboard"
            >
                ←
            </a>
            <h1 class="pembayaran-title">Pembayaran Mahasiswa</h1>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="sync-status-badge" title="Data diperbarui otomatis dari aktivitas pembayaran mahasiswa">
                <span class="pulse-dot"></span>
                <span>Sinkronisasi Otomatis Aktif</span>
                <span id="lastSyncText" style="color: #94a3b8; font-size: 11px;">(Live)</span>
            </div>

            <button type="button" class="btn-sync" id="btnManualSync" onclick="refreshData(true)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                <span>Segarkan</span>
            </button>
        </div>
    </div>

    <!-- KARTU RINGKASAN STATISTIK -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Mahasiswa</div>
            <div class="stat-value" id="statTotalMahasiswa">{{ $totalMahasiswa ?? count($daftarPembayaran) }}</div>
            <div class="stat-sub">Tagihan aktif terdaftar</div>
        </div>

        <div class="stat-card stat-lunas">
            <div class="stat-label">Sudah Lunas</div>
            <div class="stat-value" id="statTotalLunas" style="color: #10b981;">{{ $totalLunas ?? 0 }}</div>
            <div class="stat-sub">{{ $totalNominalTerkumpulRupiah ?? 'Rp 0' }} terkumpul</div>
        </div>

        <div class="stat-card stat-menunggu">
            <div class="stat-label">Menunggu Konfirmasi</div>
            <div class="stat-value" id="statTotalMenunggu" style="color: #f59e0b;">{{ $totalMenunggu ?? 0 }}</div>
            <div class="stat-sub">Perlu diverifikasi</div>
        </div>

        <div class="stat-card stat-belum">
            <div class="stat-label">Belum Lunas</div>
            <div class="stat-value" id="statTotalBelumLunas" style="color: #ef4444;">{{ $totalBelumLunas ?? 0 }}</div>
            <div class="stat-sub">Belum menyelesaikan bayar</div>
        </div>
    </div>

    <!-- FILTER STATUS DAN PENCARIAN -->
    <div class="filter-search-container">
        <div class="status-filters">
            <button type="button" class="filter-btn active" data-filter="all" onclick="setFilter('all', this)">
                Semua (<span id="countFilterAll">{{ count($daftarPembayaran) }}</span>)
            </button>
            <button type="button" class="filter-btn" data-filter="menunggu" onclick="setFilter('menunggu', this)">
                Menunggu (<span id="countFilterMenunggu">{{ $totalMenunggu ?? 0 }}</span>)
            </button>
            <button type="button" class="filter-btn" data-filter="lunas" onclick="setFilter('lunas', this)">
                Lunas (<span id="countFilterLunas">{{ $totalLunas ?? 0 }}</span>)
            </button>
            <button type="button" class="filter-btn" data-filter="belum_lunas" onclick="setFilter('belum_lunas', this)">
                Belum Lunas (<span id="countFilterBelum">{{ $totalBelumLunas ?? 0 }}</span>)
            </button>
        </div>

        <label class="pembayaran-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m16 16 4 4"></path>
            </svg>
            <input
                type="search"
                id="cariPembayaran"
                placeholder="Cari Nama, NIM, Tagihan..."
                aria-label="Cari Mahasiswa"
            >
        </label>
    </div>

    <!-- TABEL DATA PEMBAYARAN -->
    <div class="pembayaran-table-wrap">
        <table class="pembayaran-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Tagihan</th>
                    <th>Status</th>
                    <th>Metode & Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody id="daftarPembayaran">
                @forelse ($daftarPembayaran as $index => $item)
                    @php
                        $st = strtolower((string) ($item['raw_status'] ?? $item['status']));
                        $badgeClass = match($st) {
                            'lunas', 'paid', 'terkonfirmasi' => 'badge-lunas',
                            'menunggu', 'menunggu_konfirmasi', 'pending' => 'badge-menunggu',
                            default => 'badge-belum',
                        };
                        $statusLabel = match($st) {
                            'lunas', 'paid', 'terkonfirmasi' => 'Lunas',
                            'menunggu', 'menunggu_konfirmasi', 'pending' => 'Menunggu',
                            default => 'Belum Lunas',
                        };
                    @endphp
                    <tr class="baris-pembayaran" 
                        data-id="{{ $item['id'] ?? '' }}" 
                        data-status="{{ $st }}" 
                        data-nama="{{ strtolower($item['nama']) }}" 
                        data-nim="{{ strtolower($item['nim']) }}">
                        <td>{{ $item['no'] ?? ($index + 1) }}</td>
                        <td style="font-weight: 600; text-align: left; padding-left: 18px;">
                            {{ $item['nama'] }}
                        </td>
                        <td>{{ $item['nim'] }}</td>
                        <td style="font-weight: 600;">
                            {{ $item['tagihan_rupiah'] ?? ('Rp. ' . number_format($item['tagihan'], 0, ',', '.')) }}
                        </td>
                        <td>
                            <span class="badge-status {{ $badgeClass }}">
                                <span class="badge-dot"></span>
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td style="font-size: 13px;">
                            <div>{{ $item['metode'] ?? '—' }}</div>
                            <div class="metode-info">{{ $item['tanggal_bayar'] ?? '—' }}</div>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="btn-detail"
                                onclick="openDetailModal({{ $item['id'] ?? 0 }})"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Detail & Riwayat
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 30px; text-align: center; color: #64748b;">
                            Belum ada data tagihan pembayaran mahasiswa.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pembayaran-empty" id="pembayaranTidakDitemukan">
            Data pembayaran dengan kata kunci tersebut tidak ditemukan.
        </div>
    </div>

</div>

<!-- MODAL DETAIL PEMBAYARAN DAN RIWAYAT AKTIVITAS -->
<div class="modal-overlay" id="detailModal" onclick="closeDetailModalOnBackdrop(event)">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modalNamaMahasiswa" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                <span>Rincian Pembayaran & Riwayat</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeDetailModal()" aria-label="Tutup">&times;</button>
        </div>

        <div class="modal-inner">
            <!-- DATA MAHASISWA -->
            <div>
                <div class="info-section-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Informasi Mahasiswa
                </div>
                <div class="detail-grid">
                    <div>
                        <div class="detail-item-label">Nama Lengkap</div>
                        <div class="detail-item-val" id="modalNamaMhs">—</div>
                    </div>
                    <div>
                        <div class="detail-item-label">Nomor Induk Mahasiswa (NIM)</div>
                        <div class="detail-item-val" id="modalNimMhs">—</div>
                    </div>
                    <div>
                        <div class="detail-item-label">Email Mahasiswa</div>
                        <div class="detail-item-val" id="modalEmailMhs">—</div>
                    </div>
                    <div>
                        <div class="detail-item-label">Status Akun</div>
                        <div class="detail-item-val" id="modalStatusAkun">—</div>
                    </div>
                </div>
            </div>

            <!-- DETAIL TAGIHAN SAAT INI -->
            <div>
                <div class="info-section-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Detail Tagihan & Pembayaran
                </div>
                <div class="detail-grid">
                    <div>
                        <div class="detail-item-label">Jenis Tagihan</div>
                        <div class="detail-item-val" id="modalNamaTagihan">Pembayaran Asrama</div>
                    </div>
                    <div>
                        <div class="detail-item-label">Periode Tagihan</div>
                        <div class="detail-item-val" id="modalPeriodeTagihan">—</div>
                    </div>
                    <div>
                        <div class="detail-item-label">Nominal Tagihan</div>
                        <div class="detail-item-val" id="modalNominalTagihan" style="color: #800d22; font-size: 16px;">—</div>
                    </div>
                    <div>
                        <div class="detail-item-label">Status Pembayaran</div>
                        <div class="detail-item-val" id="modalStatusBayarBadge">—</div>
                    </div>
                    <div>
                        <div class="detail-item-label">Metode Pembayaran</div>
                        <div class="detail-item-val" id="modalMetodeBayar">—</div>
                    </div>
                    <div>
                        <div class="detail-item-label">Waktu Transaksi</div>
                        <div class="detail-item-val" id="modalWaktuBayar">—</div>
                    </div>
                </div>
            </div>

            <!-- RIWAYAT AKTIVITAS MAHASISWA -->
            <div>
                <div class="info-section-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Riwayat Aktivitas Mahasiswa Terkait Pembayaran
                </div>
                <div class="activity-timeline" id="modalTimelineList">
                    <div class="timeline-empty">Memuat riwayat aktivitas...</div>
                </div>
            </div>
        </div>

        <div class="modal-bottom">
            <span style="font-size: 13px; color: #64748b;">Perbarui Status:</span>
            <div class="action-group">
                <button type="button" class="btn-action-status btn-mark-lunas" onclick="updatePaymentStatus('lunas')">
                    ✓ Tandai Lunas
                </button>
                <button type="button" class="btn-action-status btn-mark-menunggu" onclick="updatePaymentStatus('menunggu')">
                    ◷ Tandai Menunggu
                </button>
                <button type="button" class="btn-action-status btn-mark-belum" onclick="updatePaymentStatus('belum_lunas')">
                    ✕ Tandai Belum Lunas
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TOAST MESSAGE -->
<div class="toast-box" id="toastBox">Notifikasi</div>

<!-- JAVASCRIPT REAL-TIME & SINKRONISASI -->
<script>
    let activeFilter = 'all';
    let currentDetailPaymentId = null;
    let isFetching = false;

    document.addEventListener('DOMContentLoaded', function () {
        initSearch();
        // Polling sinkronisasi otomatis setiap 8 detik
        setInterval(function () {
            refreshData(false);
        }, 8000);
    });

    // PENCARIAN
    function initSearch() {
        const inputCari = document.getElementById('cariPembayaran');
        if (!inputCari) return;

        inputCari.addEventListener('input', function () {
            applyFilters();
        });
    }

    // FILTER STATUS
    function setFilter(status, btnElement) {
        activeFilter = status;
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        if (btnElement) {
            btnElement.classList.add('active');
        }
        applyFilters();
    }

    // TERAPKAN FILTER DAN PENCARIAN
    function applyFilters() {
        const inputCari = document.getElementById('cariPembayaran');
        const kataKunci = inputCari ? inputCari.value.trim().toLowerCase() : '';
        const rows = document.querySelectorAll('.baris-pembayaran');
        const pesanKosong = document.getElementById('pembayaranTidakDitemukan');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const rowStatus = (row.dataset.status || '').toLowerCase();
            const textContent = row.textContent.toLowerCase();

            // Cek filter status
            let matchesFilter = false;
            if (activeFilter === 'all') {
                matchesFilter = true;
            } else if (activeFilter === 'lunas') {
                matchesFilter = (rowStatus === 'lunas' || rowStatus === 'paid' || rowStatus === 'terkonfirmasi');
            } else if (activeFilter === 'menunggu') {
                matchesFilter = (rowStatus === 'menunggu' || rowStatus === 'menunggu_konfirmasi' || rowStatus === 'pending');
            } else if (activeFilter === 'belum_lunas') {
                matchesFilter = (rowStatus === 'belum_lunas' || (!rowStatus.includes('lunas') && !rowStatus.includes('menunggu')));
            }

            // Cek kata kunci pencarian
            const matchesSearch = !kataKunci || textContent.includes(kataKunci);

            if (matchesFilter && matchesSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (pesanKosong) {
            pesanKosong.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // SINKRONISASI DATA REAL-TIME
    async function refreshData(showSpinner = false) {
        if (isFetching) return;
        isFetching = true;

        const btnSync = document.getElementById('btnManualSync');
        if (showSpinner && btnSync) {
            btnSync.classList.add('spinning');
        }

        try {
            const response = await fetch("{{ route('musyrifah.pembayaran.data') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Network error');
            const res = await response.json();

            if (res.success && res.data) {
                renderTable(res.data);
                updateSummary(res.summary);
                const syncText = document.getElementById('lastSyncText');
                if (syncText && res.last_sync) {
                    syncText.textContent = '(' + res.last_sync + ')';
                }
                if (showSpinner) {
                    showToast('Data pembayaran berhasil diperbarui');
                }
            }
        } catch (error) {
            console.error('Error syncing payment data:', error);
        } finally {
            isFetching = false;
            if (showSpinner && btnSync) {
                setTimeout(() => btnSync.classList.remove('spinning'), 500);
            }
        }
    }

    // UPDATE TABEL TANPA RELOAD
    function renderTable(dataList) {
        const tbody = document.getElementById('daftarPembayaran');
        if (!tbody) return;

        let html = '';
        dataList.forEach(function (item, index) {
            const rawStatus = (item.raw_status || item.status || '').toLowerCase();
            let badgeClass = 'badge-belum';
            let label = 'Belum Lunas';

            if (rawStatus === 'lunas' || rawStatus === 'paid' || rawStatus === 'terkonfirmasi') {
                badgeClass = 'badge-lunas';
                label = 'Lunas';
            } else if (rawStatus === 'menunggu' || rawStatus === 'menunggu_konfirmasi' || rawStatus === 'pending') {
                badgeClass = 'badge-menunggu';
                label = 'Menunggu';
            }

            html += `
                <tr class="baris-pembayaran" 
                    data-id="${item.id}" 
                    data-status="${rawStatus}" 
                    data-nama="${(item.nama || '').toLowerCase()}" 
                    data-nim="${(item.nim || '').toLowerCase()}">
                    <td>${item.no || (index + 1)}</td>
                    <td style="font-weight: 600; text-align: left; padding-left: 18px;">
                        ${escapeHtml(item.nama)}
                    </td>
                    <td>${escapeHtml(item.nim)}</td>
                    <td style="font-weight: 600;">
                        ${escapeHtml(item.tagihan_rupiah)}
                    </td>
                    <td>
                        <span class="badge-status ${badgeClass}">
                            <span class="badge-dot"></span>
                            ${label}
                        </span>
                    </td>
                    <td style="font-size: 13px;">
                        <div>${escapeHtml(item.metode || '—')}</div>
                        <div class="metode-info">${escapeHtml(item.tanggal_bayar || '—')}</div>
                    </td>
                    <td>
                        <button type="button" class="btn-detail" onclick="openDetailModal(${item.id})">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Detail & Riwayat
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        applyFilters();
    }

    // UPDATE KARTU STATISTIK
    function updateSummary(summary) {
        if (!summary) return;
        document.getElementById('statTotalMahasiswa').textContent = summary.totalMahasiswa || '0';
        document.getElementById('statTotalLunas').textContent = summary.totalLunas || '0';
        document.getElementById('statTotalMenunggu').textContent = summary.totalMenunggu || '0';
        document.getElementById('statTotalBelumLunas').textContent = summary.totalBelumLunas || '0';

        document.getElementById('countFilterAll').textContent = summary.totalMahasiswa || '0';
        document.getElementById('countFilterMenunggu').textContent = summary.totalMenunggu || '0';
        document.getElementById('countFilterLunas').textContent = summary.totalLunas || '0';
        document.getElementById('countFilterBelum').textContent = summary.totalBelumLunas || '0';
    }

    // BUKA MODAL DETAIL & RIWAYAT AKTIVITAS
    async function openDetailModal(id) {
        currentDetailPaymentId = id;
        const modal = document.getElementById('detailModal');
        modal.classList.add('active');

        document.getElementById('modalNamaMhs').textContent = 'Memuat...';
        document.getElementById('modalNimMhs').textContent = 'Memuat...';
        document.getElementById('modalEmailMhs').textContent = 'Memuat...';
        document.getElementById('modalStatusAkun').textContent = 'Memuat...';
        document.getElementById('modalNominalTagihan').textContent = 'Memuat...';
        document.getElementById('modalTimelineList').innerHTML = '<div class="timeline-empty">Memuat riwayat aktivitas...</div>';

        try {
            const url = "{{ url('/musyrifah/pembayaran') }}/" + id + "/detail";
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (data.success) {
                const mhs = data.mahasiswa;
                const p = data.pembayaran;

                document.getElementById('modalNamaMhs').textContent = mhs.name || 'Mahasiswa';
                document.getElementById('modalNimMhs').textContent = mhs.nim || '-';
                document.getElementById('modalEmailMhs').textContent = mhs.email || '-';
                document.getElementById('modalStatusAkun').textContent = (mhs.status || 'aktif').toUpperCase();

                document.getElementById('modalNamaTagihan').textContent = p.nama_tagihan || 'Pembayaran Asrama';
                document.getElementById('modalPeriodeTagihan').textContent = p.periode || '-';
                document.getElementById('modalNominalTagihan').textContent = p.nominal_rupiah || '-';
                document.getElementById('modalMetodeBayar').textContent = p.metode_pembayaran || 'Belum dipilih';
                document.getElementById('modalWaktuBayar').textContent = p.tanggal_bayar || 'Belum dibayar';

                let badgeHtml = '';
                if (p.status === 'lunas') {
                    badgeHtml = '<span class="badge-status badge-lunas"><span class="badge-dot"></span> Lunas</span>';
                } else if (p.status === 'menunggu') {
                    badgeHtml = '<span class="badge-status badge-menunggu"><span class="badge-dot"></span> Menunggu Konfirmasi</span>';
                } else {
                    badgeHtml = '<span class="badge-status badge-belum"><span class="badge-dot"></span> Belum Lunas</span>';
                }
                document.getElementById('modalStatusBayarBadge').innerHTML = badgeHtml;

                // Render Timeline Aktivitas Mahasiswa
                const timelineBox = document.getElementById('modalTimelineList');
                if (data.riwayat_aktivitas && data.riwayat_aktivitas.length > 0) {
                    let timelineHtml = '';
                    data.riwayat_aktivitas.forEach(function (log) {
                        timelineHtml += `
                            <div class="timeline-item">
                                <div class="timeline-dot">✓</div>
                                <div class="timeline-content">
                                    <div class="timeline-title">
                                        <span>${escapeHtml(log.activity)}</span>
                                        <span class="timeline-time">${escapeHtml(log.time_formatted)}</span>
                                    </div>
                                    <div class="timeline-desc">${escapeHtml(log.description || '-')}</div>
                                </div>
                            </div>
                        `;
                    });
                    timelineBox.innerHTML = timelineHtml;
                } else {
                    timelineBox.innerHTML = '<div class="timeline-empty">Belum ada catatan aktivitas pembayaran untuk mahasiswa ini.</div>';
                }
            }
        } catch (e) {
            console.error('Failed to load detail modal:', e);
            document.getElementById('modalTimelineList').innerHTML = '<div class="timeline-empty" style="color:red;">Gagal memuat detail data. Silakan coba lagi.</div>';
        }
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.remove('active');
        currentDetailPaymentId = null;
    }

    function closeDetailModalOnBackdrop(e) {
        if (e.target.id === 'detailModal') {
            closeDetailModal();
        }
    }

    // UPDATE STATUS OLEH MUSYRIFAH
    async function updatePaymentStatus(newStatus) {
        if (!currentDetailPaymentId) return;

        try {
            const url = "{{ url('/musyrifah/pembayaran') }}/" + currentDetailPaymentId + "/status";
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: newStatus })
            });

            const result = await response.json();
            if (result.success) {
                showToast(result.message);
                openDetailModal(currentDetailPaymentId); // Segarkan tampilan modal
                refreshData(false); // Segarkan tabel utama
            } else {
                showToast('Gagal memperbarui status');
            }
        } catch (error) {
            console.error('Error updating status:', error);
            showToast('Terjadi kesalahan koneksi.');
        }
    }

    // TOAST HELPER
    function showToast(msg) {
        const toast = document.getElementById('toastBox');
        if (!toast) return;
        toast.textContent = msg;
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3200);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>

@endsection
