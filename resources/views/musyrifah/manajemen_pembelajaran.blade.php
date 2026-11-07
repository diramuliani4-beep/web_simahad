@extends('layouts.musyrifah')

@section('title', 'Manajemen Pembelajaran')

@section('content')

<style>
    .musyrifah-pembelajaran {
        width: 100%;
        color: #171717;
        font-family: 'Poppins', Arial, sans-serif;
        padding-bottom: 50px;
    }

    .musyrifah-pembelajaran * {
        box-sizing: border-box;
    }

    .mp-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 24px;
    }

    .mp-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #111;
        font-size: 30px;
        line-height: 1;
        text-decoration: none;
        flex-shrink: 0;
        transition: transform 0.2s, color 0.2s;
    }

    .mp-back:hover {
        color: #800d22;
        transform: translateX(-3px);
    }

    .mp-heading-text {
        min-width: 0;
    }

    .mp-title {
        margin: 0;
        font-size: 30px;
        font-weight: 700;
        line-height: 1.25;
        color: #080808;
    }

    .mp-subtitle {
        margin: 4px 0 0;
        color: #555;
        font-size: 15px;
    }

    /* KARTU RINGKASAN ATAS */
    .mp-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin: 0 0 24px;
    }

    .mp-summary-card {
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        padding: 16px 20px;
        border: 1px solid #edd5db;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(128, 13, 34, 0.04);
        border-left: 5px solid #800d22;
    }

    .mp-summary-card:nth-child(2) { border-left-color: #2563eb; }
    .mp-summary-card:nth-child(3) { border-left-color: #10b981; }
    .mp-summary-card:nth-child(4) { border-left-color: #f59e0b; }

    .mp-summary-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        margin-bottom: 6px;
        color: #800d22;
    }

    .mp-summary-icon svg {
        width: 24px;
        height: 24px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .mp-summary-value {
        margin: 0;
        color: #111;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
    }

    .mp-summary-label {
        margin-top: 4px;
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }

    /* TAB MENU / AKSES CEPAT */
    .mp-tabs-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 22px;
        overflow-x: auto;
        padding-bottom: 6px;
        border-bottom: 2px solid #e2e8f0;
    }

    .mp-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        border: none;
        border-bottom: 3px solid transparent;
        background: transparent;
        color: #64748b;
        font-family: inherit;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
        border-radius: 8px 8px 0 0;
    }

    .mp-tab-btn:hover {
        color: #800d22;
        background: #fbf1f3;
    }

    .mp-tab-btn.active {
        color: #800d22;
        border-bottom-color: #800d22;
        background: #fbf1f3;
    }

    .mp-tab-panel {
        display: none;
        animation: fadeIn 0.25s ease;
    }

    .mp-tab-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* KONTEN TAB & CARD */
    .mp-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        margin-bottom: 20px;
    }

    .mp-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 18px;
    }

    .mp-card-title {
        font-size: 19px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-action-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #800d22;
        color: #fff;
        padding: 9px 18px;
        border-radius: 10px;
        border: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
    }

    .btn-action-primary:hover {
        background: #5f0016;
    }

    /* TABEL UMUM */
    .mp-table-wrap {
        width: 100%;
        overflow-x: auto;
        border-radius: 14px;
        border: 1px solid #edd5db;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }

    .mp-table {
        width: 100%;
        min-width: 780px;
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
    }

    .mp-table thead th {
        padding: 13px 14px;
        background: #650019;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
    }

    .mp-table thead th:first-child { border-top-left-radius: 13px; }
    .mp-table thead th:last-child { border-top-right-radius: 13px; }

    .mp-table tbody td {
        padding: 14px 12px;
        text-align: center;
        vertical-align: middle;
        font-size: 14px;
        color: #1e293b;
        border-right: 1px solid rgba(80, 50, 50, .12);
        border-bottom: 1px solid rgba(80, 50, 50, .12);
    }

    .mp-table tbody tr:hover td {
        background: #fbf4f6;
    }

    .mp-table tbody td:last-child { border-right: none; }
    .mp-table tbody tr:last-child td { border-bottom: none; }

    /* BADGE & PILL */
    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-green { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-yellow { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-red { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .badge-blue { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }

    /* TOMBOL AKSI TABEL */
    .btn-table-edit {
        padding: 6px 12px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-table-edit:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .btn-table-del {
        padding: 6px 12px;
        border-radius: 6px;
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-table-del:hover {
        background: #fee2e2;
    }

    .btn-table-detail {
        padding: 6px 14px;
        border-radius: 6px;
        background: #800d22;
        color: #fff;
        border: none;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-table-detail:hover {
        background: #5f0016;
    }

    /* FILTER BAR PEMANTAUAN & KELULUSAN */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
        padding: 14px 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .filter-input {
        flex: 1;
        min-width: 220px;
        height: 42px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        outline: none;
        background: #fff;
    }

    .filter-select {
        height: 42px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        outline: none;
        background: #fff;
        min-width: 170px;
    }

    /* PROGRES BAR */
    .progress-bar-wrap {
        width: 100%;
        height: 8px;
        background: #e2e8f0;
        border-radius: 9999px;
        overflow: hidden;
        margin-top: 6px;
    }

    .progress-bar-fill {
        height: 100%;
        background: #800d22;
        border-radius: 9999px;
        transition: width 0.3s ease;
    }

    /* MODAL */
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
        max-width: 720px;
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
        font-size: 19px;
        font-weight: 700;
        color: #800d22;
    }

    .modal-close-btn {
        background: none;
        border: none;
        font-size: 26px;
        color: #64748b;
        cursor: pointer;
        line-height: 1;
    }

    .modal-inner {
        padding: 22px 24px;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 5px;
    }

    .form-control {
        width: 100%;
        height: 42px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        outline: none;
    }

    .form-control:focus {
        border-color: #800d22;
    }

    .modal-footer-btns {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        border-bottom-left-radius: 18px;
        border-bottom-right-radius: 18px;
    }

    .btn-modal-cancel {
        padding: 9px 18px;
        background: #e2e8f0;
        color: #334155;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }

    /* GRID PERTEMUAN 1-16 */
    .pertemuan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(135px, 1fr));
        gap: 10px;
        margin-top: 14px;
        max-height: 380px;
        overflow-y: auto;
        padding: 4px;
    }

    .pertemuan-card-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        text-align: center;
    }

    .pertemuan-card-num {
        font-size: 14px;
        font-weight: 700;
        color: #800d22;
    }

    .pertemuan-card-date {
        font-size: 11px;
        color: #64748b;
        margin-top: 2px;
    }

    .pertemuan-card-badge {
        display: inline-block;
        margin-top: 6px;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 9999px;
        font-weight: 600;
    }

    @media (max-width: 900px) {
        .mp-summary-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 600px) {
        .mp-summary-grid { grid-template-columns: 1fr; }
        .mp-title { font-size: 24px; }
    }
</style>

<div class="musyrifah-pembelajaran">

    <!-- HEADER -->
    <div class="mp-heading">
        <a href="{{ route('musyrifah.dashboard') }}"
           class="mp-back"
           title="Kembali ke Dashboard"
           aria-label="Kembali ke Dashboard">←</a>
        <div class="mp-heading-text">
            <h1 class="mp-title">Manajemen Pembelajaran</h1>
            <p class="mp-subtitle">Kelola mata pelajaran, 6 kelompok belajar, jadwal, pemantauan 16 pertemuan, dan kelulusan mahasiswa.</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 500;">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- RINGKASAN ATAS -->
    <section class="mp-summary-grid" aria-label="Ringkasan pembelajaran">
        <div class="mp-summary-card">
            <span class="mp-summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21z"/><path d="M4 5.5v13A2.5 2.5 0 0 1 6.5 16H20M8 7h8M8 10h8"/></svg>
            </span>
            <p class="mp-summary-value">{{ $jumlahMataPelajaran ?? 5 }}</p>
            <div class="mp-summary-label">Mata Pelajaran</div>
        </div>

        <div class="mp-summary-card">
            <span class="mp-summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20"/><circle cx="10" cy="7" r="3.5"/><path d="M17 4.2a3.5 3.5 0 0 1 0 6.6M17 15h1.5a3.5 3.5 0 0 1 3.5 3.5V20"/></svg>
            </span>
            <p class="mp-summary-value">{{ $jumlahKelompokBelajar ?? 6 }}</p>
            <div class="mp-summary-label">Kelompok Belajar</div>
        </div>

        <div class="mp-summary-card">
            <span class="mp-summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M2.5 9 12 4l9.5 5-9.5 5z"/><path d="M6 11v5c3.5 3 8.5 3 12 0v-5M21.5 9v6"/></svg>
            </span>
            <p class="mp-summary-value">{{ $jumlahPengajar ?? 8 }}</p>
            <div class="mp-summary-label">Guru / Pengajar</div>
        </div>

        <div class="mp-summary-card">
            <span class="mp-summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18M12 13v3l2 1"/></svg>
            </span>
            <p class="mp-summary-value">{{ $targetPertemuan ?? 16 }} TM</p>
            <div class="mp-summary-label">Target Pertemuan</div>
        </div>
    </section>

    <!-- NAVIGASI 5 MENU UTAMA -->
    <nav class="mp-tabs-nav" aria-label="Menu Manajemen Pembelajaran">
        <button type="button" class="mp-tab-btn active" data-tab="tab-mapel" onclick="switchTab('tab-mapel', this)">
            📚 1. Mata Pelajaran ({{ count($daftarMataPelajaran) }})
        </button>
        <button type="button" class="mp-tab-btn" data-tab="tab-kelompok" onclick="switchTab('tab-kelompok', this)">
            👥 2. Kelompok Belajar (CRUD)
        </button>
        <button type="button" class="mp-tab-btn" data-tab="tab-jadwal" onclick="switchTab('tab-jadwal', this)">
            🗓️ 3. Jadwal Pembelajaran
        </button>
        <button type="button" class="mp-tab-btn" data-tab="tab-pemantauan" onclick="switchTab('tab-pemantauan', this)">
            📊 4. Pemantauan &amp; 16 Pertemuan
        </button>
        <button type="button" class="mp-tab-btn" data-tab="tab-kelulusan" onclick="switchTab('tab-kelulusan', this)">
            🎓 5. Kelulusan &amp; Sertifikat
        </button>
    </nav>

    <!-- ========================================== -->
    <!-- TAB 1: MATA PELAJARAN                      -->
    <!-- ========================================== -->
    <div class="mp-tab-panel active" id="tab-mapel">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Daftar Mata Pelajaran</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">5 Program Pembelajaran Kurikulum Ma'had Al-Jami'ah (Masing-masing 16 Pertemuan).</p>
                </div>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Mata Pelajaran</th>
                            <th>Kategori Program</th>
                            <th>Target Pertemuan</th>
                            <th>Jadwal Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarMataPelajaran as $idx => $mapel)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="font-weight: 600; color: #800d22;">{{ $mapel->kode ?? '-' }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 18px;">
                                    {{ $mapel->nama }}
                                    <div style="font-size: 12px; font-weight: 400; color: #64748b; margin-top: 2px;">
                                        {{ $mapel->deskripsi ?? '' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-pill badge-blue">{{ $mapel->kategori ?? 'Akademik' }}</span>
                                </td>
                                <td style="font-weight: 700;">{{ $mapel->target_pertemuan }} Pertemuan</td>
                                <td>
                                    <span class="badge-pill badge-green">{{ $mapel->jadwals_count ?? 2 }} Kelas Terjadwal</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 25px;">Belum ada mata pelajaran terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: KELOMPOK BELAJAR (CRUD)             -->
    <!-- ========================================== -->
    <div class="mp-tab-panel" id="tab-kelompok">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Pengaturan 6 Kelompok Belajar &amp; Pengajar</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Daftar 8 pengajar pengampu kelompok belajar Ma'had Al-Jami'ah (Dilengkapi fitur CRUD Tambah, Ubah, Hapus).</p>
                </div>

                <button type="button" class="btn-action-primary" onclick="openModalTambahKelompok()">
                    + Tambah Pengajar Kelompok
                </button>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengajar</th>
                            <th>Program Pembelajaran</th>
                            <th>Kelompok Belajar</th>
                            <th>Beban Mengajar</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyKelompokPengajar">
                        @forelse($daftarPengajar as $idx => $p)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 16px;">
                                    {{ $p->nama_pengajar }}
                                </td>
                                <td style="text-align: left; padding-left: 16px;">
                                    {{ $p->program }}
                                </td>
                                <td>
                                    <span class="badge-pill badge-blue" style="font-weight: 700;">
                                        Kelompok {{ $p->kelompok_teks }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: #800d22;">
                                    {{ $p->jumlah_tm }}
                                </td>
                                <td style="font-size: 12px; color: #64748b;">
                                    {{ $p->keterangan ?? '-' }}
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <button type="button" class="btn-table-edit" 
                                                onclick="openModalEditKelompok({{ $p->id }}, '{{ addslashes($p->nama_pengajar) }}', '{{ addslashes($p->program) }}', '{{ addslashes($p->kelompok_teks) }}', '{{ addslashes($p->jumlah_tm) }}', '{{ addslashes($p->keterangan ?? '') }}')">
                                            Ubah
                                        </button>
                                        <form action="{{ route('musyrifah.manajemen_pembelajaran.kelompok.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-table-del">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 25px;">Belum ada data pengajar kelompok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: JADWAL PEMBELAJARAN (CRUD)          -->
    <!-- ========================================== -->
    <div class="mp-tab-panel" id="tab-jadwal">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Jadwal Pembelajaran Ma'had</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Pengaturan mata pelajaran, kelompok belajar, guru pengampu, periode, hari, dan jam mulai/selesai.</p>
                </div>

                <button type="button" class="btn-action-primary" onclick="openModalTambahJadwal()">
                    + Atur Jadwal Baru
                </button>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelompok</th>
                            <th>Guru Pengampu</th>
                            <th>Periode</th>
                            <th>Hari &amp; Waktu</th>
                            <th>Ruangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarJadwal as $idx => $j)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 16px;">
                                    {{ $j->nama_mata_pelajaran }}
                                </td>
                                <td>
                                    <span class="badge-pill badge-blue">{{ $j->kelompok }}</span>
                                </td>
                                <td style="text-align: left; padding-left: 14px;">
                                    {{ $j->guru_pengampu }}
                                </td>
                                <td style="font-size: 13px;">
                                    {{ $j->periode }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #800d22;">{{ $j->hari }}</div>
                                    <div style="font-size: 12px; color: #64748b;">{{ $j->jam_mulai }} - {{ $j->jam_selesai }} WIB</div>
                                </td>
                                <td style="font-size: 13px;">{{ $j->ruangan }}</td>
                                <td>
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <button type="button" class="btn-table-edit" 
                                                onclick="openModalEditJadwal({{ $j->id }}, '{{ addslashes($j->nama_mata_pelajaran) }}', '{{ addslashes($j->kelompok) }}', '{{ addslashes($j->guru_pengampu) }}', '{{ addslashes($j->periode) }}', '{{ addslashes($j->hari) }}', '{{ addslashes($j->jam_mulai) }}', '{{ addslashes($j->jam_selesai) }}', '{{ addslashes($j->ruangan) }}')">
                                            Ubah
                                        </button>
                                        <form action="{{ route('musyrifah.manajemen_pembelajaran.jadwal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-table-del">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 25px;">Belum ada jadwal yang diatur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 4: PEMANTAUAN & 16 PERTEMUAN           -->
    <!-- ========================================== -->
    <div class="mp-tab-panel" id="tab-pemantauan">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Pemantauan Progres Pembelajaran</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Ringkasan progres pertemuan target 16 kali, dilengkapi pencarian dan filter dinamis periode &amp; kelompok.</p>
                </div>
            </div>

            <!-- FILTER BAR PEMANTAUAN -->
            <div class="filter-bar">
                <input type="search" id="cariPemantauan" class="filter-input" placeholder="🔍 Cari mata pelajaran atau pengajar..." oninput="filterPemantauan()">

                <select id="filterPeriodePemantauan" class="filter-select" onchange="filterPemantauan()">
                    <option value="">Semua Periode</option>
                    @foreach($listPeriode as $periode)
                        <option value="{{ strtolower($periode) }}">{{ $periode }}</option>
                    @endforeach
                </select>

                <select id="filterKelompokPemantauan" class="filter-select" onchange="filterPemantauan()">
                    <option value="">Semua Kelompok</option>
                    @foreach($listKelompokFilter as $k)
                        <option value="{{ strtolower($k) }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelompok</th>
                            <th>Pengajar</th>
                            <th>Periode</th>
                            <th>Progres Pertemuan (16 TM)</th>
                            <th>Kehadiran Rata-rata</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPemantauan">
                        @forelse($daftarPemantauan as $idx => $pm)
                            <tr class="row-pemantauan" 
                                data-nama="{{ strtolower($pm['nama']) }}"
                                data-pengajar="{{ strtolower($pm['pengajar']) }}"
                                data-periode="{{ strtolower($pm['periode']) }}"
                                data-kelompok="{{ strtolower($pm['kelompok']) }}">
                                <td>{{ $idx + 1 }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 16px;">
                                    {{ $pm['nama'] }}
                                    <div style="font-size: 11px; color: #800d22;">{{ $pm['kode'] }}</div>
                                </td>
                                <td><span class="badge-pill badge-blue">{{ $pm['kelompok'] }}</span></td>
                                <td style="text-align: left; padding-left: 14px;">{{ $pm['pengajar'] }}</td>
                                <td style="font-size: 13px;">{{ $pm['periode'] }}</td>
                                <td style="min-width: 170px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600;">
                                        <span>{{ $pm['pertemuan_terlaksana'] }} dari 16 Pertemuan</span>
                                        <span>{{ round(($pm['pertemuan_terlaksana'] / 16) * 100) }}%</span>
                                    </div>
                                    <div class="progress-bar-wrap">
                                        <div class="progress-bar-fill" style="width: {{ ($pm['pertemuan_terlaksana'] / 16) * 100 }}%;"></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-pill badge-green">{{ $pm['persentase'] }}%</span>
                                </td>
                                <td>
                                    <button type="button" class="btn-table-detail" onclick="openModalDetailPemantauan({{ $pm['id'] }}, '{{ addslashes($pm['nama']) }}')">
                                        Lihat Detail 16 TM
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 25px;">Belum ada data pemantauan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="emptyPemantauanMsg" style="display: none; text-align: center; padding: 30px; color: #64748b;">
                Tidak ada data pemantauan yang sesuai dengan filter yang dipilih.
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 5: KELULUSAN & SERTIFIKAT              -->
    <!-- ========================================== -->
    <div class="mp-tab-panel" id="tab-kelulusan">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Kelulusan &amp; Sertifikat Mahasiswa</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">
                        <strong>Syarat Kelulusan:</strong> Mahasiswa wajib hadir <strong>16 kali pertemuan penuh</strong> dalam <strong>semua mata pelajaran</strong>.
                    </p>
                </div>
            </div>

            <!-- KPI KELULUSAN -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px;">
                    <div style="font-size: 13px; color: #64748b;">Total Mahasiswa Terdaftar</div>
                    <div style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ $dataKelulusan['totalMahasiswa'] }} Orang</div>
                </div>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 18px;">
                    <div style="font-size: 13px; color: #166534;">Memenuhi Syarat (Lulus)</div>
                    <div style="font-size: 24px; font-weight: 700; color: #15803d;">{{ $dataKelulusan['totalLulus'] }} Mahasiswa</div>
                </div>
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 18px;">
                    <div style="font-size: 13px; color: #991b1b;">Belum Memenuhi Syarat</div>
                    <div style="font-size: 24px; font-weight: 700; color: #b91c1c;">{{ $dataKelulusan['totalBelumLulus'] }} Mahasiswa</div>
                </div>
            </div>

            <!-- FILTER KELULUSAN -->
            <div class="filter-bar">
                <input type="search" id="cariKelulusan" class="filter-input" placeholder="🔍 Cari nama mahasiswa atau NIM..." oninput="filterKelulusan()">

                <select id="filterStatusKelulusan" class="filter-select" onchange="filterKelulusan()">
                    <option value="">Semua Status</option>
                    <option value="lulus">Memenuhi Syarat</option>
                    <option value="belum">Belum Memenuhi Syarat</option>
                </select>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Kelompok</th>
                            <th>Total Kehadiran</th>
                            <th>Status Kelulusan</th>
                            <th>Keterangan Syarat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyKelulusan">
                        @forelse($dataKelulusan['daftar'] as $idx => $mhs)
                            <tr class="row-kelulusan" 
                                data-nama="{{ strtolower($mhs['nama']) }}"
                                data-nim="{{ strtolower($mhs['nim']) }}"
                                data-status="{{ $mhs['is_lulus'] ? 'lulus' : 'belum' }}">
                                <td>{{ $idx + 1 }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 18px;">
                                    {{ $mhs['nama'] }}
                                </td>
                                <td>{{ $mhs['nim'] }}</td>
                                <td><span class="badge-pill badge-blue">{{ $mhs['kelompok'] }}</span></td>
                                <td style="font-weight: 700;">
                                    {{ $mhs['total_hadir'] }} / {{ $mhs['target_hadir'] }} Sesi
                                </td>
                                <td>
                                    @if($mhs['is_lulus'])
                                        <span class="badge-pill badge-green">✓ Memenuhi Syarat</span>
                                    @else
                                        <span class="badge-pill badge-red">✕ Belum Memenuhi</span>
                                    @endif
                                </td>
                                <td style="font-size: 13px; color: {{ $mhs['is_lulus'] ? '#15803d' : '#b91c1c' }};">
                                    {{ $mhs['catatan_syarat'] }}
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <button type="button" class="btn-table-edit" onclick="openModalDetailKelulusan({{ $mhs['id'] }})">
                                            Rincian Syarat
                                        </button>
                                        @if($mhs['is_lulus'])
                                            <button type="button" class="btn-table-detail" style="background: #15803d;" onclick="openModalSertifikat({{ $mhs['id'] }}, '{{ addslashes($mhs['nama']) }}', '{{ addslashes($mhs['nim']) }}')">
                                                📜 Sertifikat
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 25px;">Belum ada data mahasiswa terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="emptyKelulusanMsg" style="display: none; text-align: center; padding: 30px; color: #64748b;">
                Tidak ada data mahasiswa yang sesuai dengan kriteria filter.
            </div>
        </div>
    </div>

</div>

<!-- ======================================================== -->
<!-- MODAL 1: TAMBAH / EDIT KELOMPOK PENGAJAR                 -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalKelompok" onclick="closeModalOnBackdrop(event, 'modalKelompok')">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title" id="modalKelompokTitle">Tambah Pengajar Kelompok</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalKelompok')">&times;</button>
        </div>
        <form id="formKelompok" method="POST" action="{{ route('musyrifah.manajemen_pembelajaran.kelompok.store') }}">
            @csrf
            <div id="methodKelompokSpoof"></div>
            <div class="modal-inner">
                <div class="form-group">
                    <label class="form-label">Nama Pengajar / Dosen</label>
                    <input type="text" name="nama_pengajar" id="inputNamaPengajar" class="form-control" required placeholder="Contoh: Dr. Zulkifli, M.A.">
                </div>
                <div class="form-group">
                    <label class="form-label">Program Pembelajaran</label>
                    <select name="program" id="selectProgram" class="form-control" required>
                        @foreach($daftarMataPelajaran as $mp)
                            <option value="{{ $mp->nama }}">{{ $mp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelompok yang Diampu (Pilihan 6 Kelompok)</label>
                    <input type="text" name="kelompok_teks" id="inputKelompokTeks" class="form-control" required placeholder="Contoh: 1 dan 2 atau 4 dan 5">
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Beban TM</label>
                    <input type="text" name="jumlah_tm" id="inputJumlahTm" class="form-control" value="32 TM (2 kelas)" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan / Fokus Pembelajaran</label>
                    <textarea name="keterangan" id="inputKeteranganKelompok" class="form-control" style="height: 70px; padding: 10px;" placeholder="Catatan bidang materi..."></textarea>
                </div>
            </div>
            <div class="modal-footer-btns">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalKelompok')">Batal</button>
                <button type="submit" class="btn-action-primary" id="btnSubmitKelompok">Simpan Pengajar</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 2: TAMBAH / EDIT JADWAL PEMBELAJARAN               -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalJadwal" onclick="closeModalOnBackdrop(event, 'modalJadwal')">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title" id="modalJadwalTitle">Atur Jadwal Pembelajaran</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalJadwal')">&times;</button>
        </div>
        <form id="formJadwal" method="POST" action="{{ route('musyrifah.manajemen_pembelajaran.jadwal.store') }}">
            @csrf
            <div id="methodJadwalSpoof"></div>
            <div class="modal-inner">
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="nama_mata_pelajaran" id="inputJadwalMapel" class="form-control" required>
                        @foreach($daftarMataPelajaran as $mp)
                            <option value="{{ $mp->nama }}">{{ $mp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="form-group">
                        <label class="form-label">Kelompok Belajar</label>
                        <select name="kelompok" id="inputJadwalKelompok" class="form-control" required>
                            @foreach($daftarKelompok as $k)
                                <option value="{{ $k->nama_kelompok }}">{{ $k->nama_kelompok }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Periode</label>
                        <input type="text" name="periode" id="inputJadwalPeriode" class="form-control" value="Semester Ganjil 2026/2027" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Guru Pengampu</label>
                    <select name="guru_pengampu" id="inputJadwalGuru" class="form-control" required>
                        @foreach($daftarPengajar as $peng)
                            <option value="{{ $peng->nama_pengajar }}">{{ $peng->nama_pengajar }} ({{ $peng->program }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                    <div class="form-group">
                        <label class="form-label">Hari</label>
                        <select name="hari" id="inputJadwalHari" class="form-control" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Mulai</label>
                        <input type="text" name="jam_mulai" id="inputJadwalJamMulai" class="form-control" placeholder="08:00" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Selesai</label>
                        <input type="text" name="jam_selesai" id="inputJadwalJamSelesai" class="form-control" placeholder="09:30" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Ruangan</label>
                    <input type="text" name="ruangan" id="inputJadwalRuangan" class="form-control" placeholder="Ruang Kelas 1 / Aula Ma'had" value="Ruang Kelas">
                </div>
            </div>
            <div class="modal-footer-btns">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalJadwal')">Batal</button>
                <button type="submit" class="btn-action-primary" id="btnSubmitJadwal">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 3: DETAIL PEMANTAUAN 16 PERTEMUAN                   -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalDetailPemantauan" onclick="closeModalOnBackdrop(event, 'modalDetailPemantauan')">
    <div class="modal-card" style="max-width: 760px;" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title">Detail 16 Pertemuan Pembelajaran</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailPemantauan')">&times;</button>
        </div>
        <div class="modal-inner">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; margin-bottom: 16px;">
                <div style="font-size: 17px; font-weight: 700; color: #800d22;" id="detailMapelTitle">—</div>
                <div style="font-size: 13px; color: #64748b; margin-top: 3px;" id="detailMapelSub">Target: 16 Pertemuan Tatap Muka</div>
            </div>

            <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Rincian Status Kehadiran per Pertemuan (Pertemuan 1 s.d. 16):
            </div>
            <div class="pertemuan-grid" id="gridPertemuanDetail">
                <div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #64748b;">Memuat data 16 pertemuan...</div>
            </div>
        </div>
        <div class="modal-footer-btns">
            <button type="button" class="btn-action-primary" onclick="closeModal('modalDetailPemantauan')">Tutup</button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 4: DETAIL SYARAT KELULUSAN MAHASISWA                -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalDetailKelulusan" onclick="closeModalOnBackdrop(event, 'modalDetailKelulusan')">
    <div class="modal-card" style="max-width: 740px;" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title">Audit Syarat Kelulusan Mahasiswa</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailKelulusan')">&times;</button>
        </div>
        <div class="modal-inner">
            <div style="background: #fdf8f9; border: 1px solid #f1d5dc; border-radius: 12px; padding: 14px 18px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                    <div>
                        <div style="font-size: 18px; font-weight: 700; color: #800d22;" id="auditNamaMhs">—</div>
                        <div style="font-size: 13px; color: #64748b;" id="auditNimMhs">NIM: —</div>
                    </div>
                    <div id="auditBadgeKelulusan">
                        <!-- Badge status -->
                    </div>
                </div>
            </div>

            <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Capaian Kehadiran per Mata Pelajaran (Syarat: Wajib Hadir 16 Pertemuan):
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <th>Target</th>
                            <th>Hadir</th>
                            <th>Izin / Sakit</th>
                            <th>Alpa</th>
                            <th>Status Syarat</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyAuditSyarat">
                        <tr><td colspan="6" style="padding: 20px;">Memuat rincian syarat...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer-btns">
            <button type="button" class="btn-action-primary" onclick="closeModal('modalDetailKelulusan')">Tutup</button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 5: SERTIFIKAT KELULUSAN MA'HAD                      -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalSertifikat" onclick="closeModalOnBackdrop(event, 'modalSertifikat')">
    <div class="modal-card" style="max-width: 680px; text-align: center; border: 8px double #800d22; background: #fffcf8;" onclick="event.stopPropagation()">
        <div class="modal-top" style="background: transparent; border-bottom: none; justify-content: flex-end;">
            <button type="button" class="modal-close-btn" onclick="closeModal('modalSertifikat')">&times;</button>
        </div>
        <div style="padding: 10px 40px 30px;">
            <div style="color: #800d22; font-size: 26px; font-weight: 800; letter-spacing: 1px;">SERTIFIKAT KELULUSAN</div>
            <div style="font-size: 14px; font-weight: 600; color: #555; margin-top: 4px;">MA'HAD AL-JAMI'AH UIN IMAM BONJOL PADANG</div>
            <div style="font-size: 12px; color: #888;">No. Dokumen: SERT/SIMAHAD/2026/0892</div>

            <hr style="margin: 20px 0; border: none; border-top: 2px solid #800d22; width: 60%; margin-left: auto; margin-right: auto;">

            <p style="font-size: 14px; color: #444; margin-bottom: 6px;">Diberikan dengan hormat kepada:</p>
            <div style="font-size: 24px; font-weight: 700; color: #111; text-decoration: underline;" id="sertifikatNamaMhs">—</div>
            <div style="font-size: 15px; font-weight: 500; color: #666; margin-top: 4px;" id="sertifikatNimMhs">NIM: —</div>

            <p style="font-size: 14px; color: #444; margin-top: 20px; line-height: 1.6;">
                Telah <strong>MEMENUHI SYARAT KELULUSAN</strong> pada seluruh rangkaian program pembelajaran Ma'had Al-Jami'ah dengan tingkat kehadiran 100% (16 Pertemuan Penuh) di setiap mata pelajaran yang dipersyaratkan.
            </p>

            <div style="display: flex; justify-content: space-around; margin-top: 36px; padding-top: 10px;">
                <div>
                    <div style="font-size: 12px; color: #888;">Mudir Ma'had Al-Jami'ah</div>
                    <div style="height: 48px;"></div>
                    <div style="font-weight: 700; font-size: 14px; border-top: 1px solid #333; padding-top: 4px;">Dr. Zulkifli, M.A.</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #888;">Koordinator Musyrifah</div>
                    <div style="height: 48px;"></div>
                    <div style="font-weight: 700; font-size: 14px; border-top: 1px solid #333; padding-top: 4px;">Ustadzah Musyrifah</div>
                </div>
            </div>
        </div>
        <div class="modal-footer-btns" style="background: transparent;">
            <button type="button" class="btn-modal-cancel" onclick="closeModal('modalSertifikat')">Tutup</button>
            <button type="button" class="btn-action-primary" onclick="window.print()">🖨️ Cetak Sertifikat</button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- JAVASCRIPT LOGIC TAB, CRUD, FILTER, DETAIL               -->
<!-- ======================================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Cek parameter URL ?tab=...
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const mapTab = {
                'mapel': 'tab-mapel',
                'kelompok': 'tab-kelompok',
                'jadwal': 'tab-jadwal',
                'pemantauan': 'tab-pemantauan',
                'kelulusan': 'tab-kelulusan'
            };
            const targetId = mapTab[tab];
            if (targetId) {
                const btn = document.querySelector(`[data-tab="${targetId}"]`);
                if (btn) switchTab(targetId, btn);
            }
        }
    });

    // 1. SWITCH TAB
    function switchTab(tabId, btn) {
        document.querySelectorAll('.mp-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.mp-tab-panel').forEach(p => p.classList.remove('active'));

        btn.classList.add('active');
        const target = document.getElementById(tabId);
        if (target) target.classList.add('active');
    }

    // 2. MODAL HELPER
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('active');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('active');
    }

    function closeModalOnBackdrop(event, modalId) {
        if (event.target.id === modalId) {
            closeModal(modalId);
        }
    }

    // 3. MODAL KELOMPOK PENGAJAR (CRUD)
    function openModalTambahKelompok() {
        document.getElementById('modalKelompokTitle').textContent = 'Tambah Pengajar Kelompok Belajar';
        document.getElementById('formKelompok').action = "{{ route('musyrifah.manajemen_pembelajaran.kelompok.store') }}";
        document.getElementById('methodKelompokSpoof').innerHTML = '';
        document.getElementById('inputNamaPengajar').value = '';
        document.getElementById('inputKelompokTeks').value = '';
        document.getElementById('inputJumlahTm').value = '32 TM (2 kelas)';
        document.getElementById('inputKeteranganKelompok').value = '';
        document.getElementById('btnSubmitKelompok').textContent = 'Simpan Pengajar';
        openModal('modalKelompok');
    }

    function openModalEditKelompok(id, nama, program, kelompok, tm, ket) {
        document.getElementById('modalKelompokTitle').textContent = 'Ubah Pengajar Kelompok Belajar';
        document.getElementById('formKelompok').action = "{{ url('/musyrifah/manajemen-pembelajaran/kelompok') }}/" + id;
        document.getElementById('methodKelompokSpoof').innerHTML = '@method("PUT")';
        document.getElementById('inputNamaPengajar').value = nama;
        document.getElementById('selectProgram').value = program;
        document.getElementById('inputKelompokTeks').value = kelompok;
        document.getElementById('inputJumlahTm').value = tm;
        document.getElementById('inputKeteranganKelompok').value = ket;
        document.getElementById('btnSubmitKelompok').textContent = 'Perbarui Pengajar';
        openModal('modalKelompok');
    }

    // 4. MODAL JADWAL (CRUD)
    function openModalTambahJadwal() {
        document.getElementById('modalJadwalTitle').textContent = 'Atur Jadwal Pembelajaran Baru';
        document.getElementById('formJadwal').action = "{{ route('musyrifah.manajemen_pembelajaran.jadwal.store') }}";
        document.getElementById('methodJadwalSpoof').innerHTML = '';
        document.getElementById('inputJadwalJamMulai').value = '08:00';
        document.getElementById('inputJadwalJamSelesai').value = '09:30';
        document.getElementById('inputJadwalRuangan').value = 'Ruang Kelas';
        document.getElementById('btnSubmitJadwal').textContent = 'Simpan Jadwal';
        openModal('modalJadwal');
    }

    function openModalEditJadwal(id, mapel, kelompok, guru, periode, hari, jamMulai, jamSelesai, ruangan) {
        document.getElementById('modalJadwalTitle').textContent = 'Ubah Jadwal Pembelajaran';
        document.getElementById('formJadwal').action = "{{ url('/musyrifah/manajemen-pembelajaran/jadwal') }}/" + id;
        document.getElementById('methodJadwalSpoof').innerHTML = '@method("PUT")';
        document.getElementById('inputJadwalMapel').value = mapel;
        document.getElementById('inputJadwalKelompok').value = kelompok;
        document.getElementById('inputJadwalGuru').value = guru;
        document.getElementById('inputJadwalPeriode').value = periode;
        document.getElementById('inputJadwalHari').value = hari;
        document.getElementById('inputJadwalJamMulai').value = jamMulai;
        document.getElementById('inputJadwalJamSelesai').value = jamSelesai;
        document.getElementById('inputJadwalRuangan').value = ruangan;
        document.getElementById('btnSubmitJadwal').textContent = 'Perbarui Jadwal';
        openModal('modalJadwal');
    }

    // 5. FILTER PEMANTAUAN (PERIODE & KELOMPOK & PENCARIAN)
    function filterPemantauan() {
        const query = document.getElementById('cariPemantauan').value.toLowerCase().trim();
        const periodeFilter = document.getElementById('filterPeriodePemantauan').value.toLowerCase();
        const kelompokFilter = document.getElementById('filterKelompokPemantauan').value.toLowerCase();

        const rows = document.querySelectorAll('.row-pemantauan');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const nama = row.dataset.nama || '';
            const pengajar = row.dataset.pengajar || '';
            const periode = row.dataset.periode || '';
            const kelompok = row.dataset.kelompok || '';

            const matchQuery = !query || nama.includes(query) || pengajar.includes(query);
            const matchPeriode = !periodeFilter || periode.includes(periodeFilter);
            const matchKelompok = !kelompokFilter || kelompok.includes(kelompokFilter);

            if (matchQuery && matchPeriode && matchKelompok) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyMsg = document.getElementById('emptyPemantauanMsg');
        if (emptyMsg) {
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // 6. DETAIL PEMANTAUAN 16 PERTEMUAN (AJAX)
    async function openModalDetailPemantauan(mapelId, mapelNama) {
        document.getElementById('detailMapelTitle').textContent = mapelNama;
        document.getElementById('gridPertemuanDetail').innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #64748b;">Memuat data 16 pertemuan...</div>';
        openModal('modalDetailPemantauan');

        try {
            const res = await fetch("{{ url('/musyrifah/manajemen-pembelajaran/detail-pemantauan') }}/" + mapelId, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (data.success && data.pertemuan) {
                let html = '';
                data.pertemuan.forEach(function (p) {
                    const isDone = p.status_pertemuan === 'Terlaksana';
                    const badgeClass = isDone ? 'badge-green' : 'badge-yellow';
                    html += `
                        <div class="pertemuan-card-item">
                            <div class="pertemuan-card-num">Pertemuan ${p.pertemuan_ke}</div>
                            <div class="pertemuan-card-date">${p.tanggal}</div>
                            <span class="badge-pill ${badgeClass}" style="margin-top: 6px; font-size: 11px;">
                                ${p.status_pertemuan}
                            </span>
                            <div style="font-size: 11px; margin-top: 6px; color: #475569;">
                                <strong>${p.hadir}</strong> Hadir · <strong>${p.izin + p.sakit}</strong> Izin
                            </div>
                        </div>
                    `;
                });
                document.getElementById('gridPertemuanDetail').innerHTML = html;
            }
        } catch (e) {
            console.error('Error fetching detail pemantauan:', e);
            document.getElementById('gridPertemuanDetail').innerHTML = '<div style="grid-column: 1/-1; color: red; text-align: center;">Gagal memuat detail pertemuan.</div>';
        }
    }

    // 7. FILTER KELULUSAN
    function filterKelulusan() {
        const query = document.getElementById('cariKelulusan').value.toLowerCase().trim();
        const statusFilter = document.getElementById('filterStatusKelulusan').value;

        const rows = document.querySelectorAll('.row-kelulusan');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const nama = row.dataset.nama || '';
            const nim = row.dataset.nim || '';
            const status = row.dataset.status || '';

            const matchQuery = !query || nama.includes(query) || nim.includes(query);
            const matchStatus = !statusFilter || status === statusFilter;

            if (matchQuery && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyMsg = document.getElementById('emptyKelulusanMsg');
        if (emptyMsg) {
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // 8. AUDIT SYARAT KELULUSAN MAHASISWA (AJAX)
    async function openModalDetailKelulusan(userId) {
        document.getElementById('auditNamaMhs').textContent = 'Memuat...';
        document.getElementById('auditNimMhs').textContent = 'NIM: -';
        document.getElementById('tbodyAuditSyarat').innerHTML = '<tr><td colspan="6" style="padding: 20px; text-align: center;">Memuat rincian syarat kelulusan...</td></tr>';
        openModal('modalDetailKelulusan');

        try {
            const res = await fetch("{{ url('/musyrifah/manajemen-pembelajaran/detail-kelulusan') }}/" + userId, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (data.success) {
                document.getElementById('auditNamaMhs').textContent = data.mahasiswa.name;
                document.getElementById('auditNimMhs').textContent = 'NIM: ' + data.mahasiswa.nim + ' · ' + data.mahasiswa.kamar;

                const badgeBox = document.getElementById('auditBadgeKelulusan');
                if (data.is_lulus) {
                    badgeBox.innerHTML = '<span class="badge-pill badge-green" style="font-size: 13px; font-weight: 700; padding: 6px 14px;">✓ Memenuhi Syarat Kelulusan (Lengkap 100%)</span>';
                } else {
                    badgeBox.innerHTML = '<span class="badge-pill badge-red" style="font-size: 13px; font-weight: 700; padding: 6px 14px;">✕ Belum Memenuhi Syarat (Wajib 16 TM di Semua Mapel)</span>';
                }

                let html = '';
                data.detail_syarat.forEach(function (ds) {
                    const statusClass = ds.is_lulus ? 'badge-green' : 'badge-red';
                    html += `
                        <tr>
                            <td style="text-align: left; font-weight: 600; padding-left: 14px;">${ds.mata_pelajaran}</td>
                            <td>${ds.target} Pertemuan</td>
                            <td style="font-weight: 700; color: ${ds.is_lulus ? '#15803d' : '#b91c1c'};">${ds.hadir} Hadir</td>
                            <td>${ds.izin + ds.sakit} Sesi</td>
                            <td>${ds.alpa} Sesi</td>
                            <td><span class="badge-pill ${statusClass}">${ds.status_mapel}</span></td>
                        </tr>
                    `;
                });
                document.getElementById('tbodyAuditSyarat').innerHTML = html;
            }
        } catch (e) {
            console.error('Error fetching detail kelulusan:', e);
            document.getElementById('tbodyAuditSyarat').innerHTML = '<tr><td colspan="6" style="color:red; text-align:center;">Gagal memuat rincian syarat kelulusan.</td></tr>';
        }
    }

    // 9. SERTIFIKAT MODAL
    function openModalSertifikat(userId, nama, nim) {
        document.getElementById('sertifikatNamaMhs').textContent = nama;
        document.getElementById('sertifikatNimMhs').textContent = 'NIM: ' + nim;
        openModal('modalSertifikat');
    }
</script>

@endsection
