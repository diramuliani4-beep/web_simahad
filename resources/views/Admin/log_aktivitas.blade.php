@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@push('styles')
<style>
    /* SIMAHAD | Admin Log Aktivitas — selaras dengan Manajemen Pengguna */
    .la-page,
    .la-page * { box-sizing: border-box; }

    .la-page {
        --la-maroon: #800b22;
        --la-maroon-dark: #620819;
        --la-ink: #292427;
        --la-muted: #81787d;
        --la-border: #ecdee1;
        --la-soft: #fcf8f9;
        width: 100%;
        min-width: 0;
        color: var(--la-ink);
        font-family: inherit;
    }

    .la-page a { text-decoration: none; }
    .la-page button,
    .la-page select { font: inherit; }

    /* Header halaman */
    .la-header {
        padding-bottom: 19px;
        margin-bottom: 24px;
        border-bottom: 1px solid #e8cfd6;
    }
    .la-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
        margin-bottom: 11px;
        font-size: 12px;
        color: #8e8487;
    }
    .la-breadcrumb a { color: var(--la-maroon); }
    .la-breadcrumb a:hover { text-decoration: underline; }
    .la-header h1 {
        margin: 0 0 6px;
        font-size: clamp(22px, 2.4vw, 27px);
        font-weight: 700;
        line-height: 1.3;
    }
    .la-header p {
        margin: 0;
        color: #787175;
        font-size: 13px;
        line-height: 1.7;
    }

    /* Kartu statistik */
    .la-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }
    .la-stat {
        display: flex;
        align-items: center;
        gap: 15px;
        min-width: 0;
        padding: 20px;
        border: 1px solid #ece1e1;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 3px 14px rgba(62, 10, 25, .035);
    }
    .la-stat-icon {
        flex: 0 0 46px;
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #f8e9ed;
        color: var(--la-maroon);
    }
    .la-stat-icon svg { width: 23px; height: 23px; }
    .la-stat-icon--green { background: #e9f6ec; color: #217848; }
    .la-stat-icon--gold { background: #f9f2e6; color: #a3732d; }
    .la-stat-info { min-width: 0; }
    .la-stat-label {
        display: block;
        margin-bottom: 5px;
        color: var(--la-muted);
        font-size: 12px;
        line-height: 1.4;
    }
    .la-stat-number {
        margin: 0;
        color: #30282c;
        font-size: clamp(22px, 2vw, 27px);
        font-weight: 750;
        line-height: 1.2;
        font-variant-numeric: tabular-nums;
    }

    /* Panel seragam dengan Manajemen Pengguna */
    .la-card {
        min-width: 0;
        margin-bottom: 22px;
        overflow: hidden;
        border: 1px solid #ece1e1;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 3px 14px rgba(62, 10, 25, .035);
    }
    .la-card-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 19px 24px;
        border-bottom: 1px solid #f0e8e9;
    }
    .la-card-header h2 {
        margin: 0 0 5px;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.35;
    }
    .la-card-header p {
        margin: 0;
        font-size: 12px;
        line-height: 1.55;
        color: var(--la-muted);
    }
    .la-count {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border: 1px solid #ebdce0;
        border-radius: 20px;
        background: #faf4f6;
        color: #733a4a;
        font-size: 11px;
        font-weight: 650;
        white-space: nowrap;
    }
    .la-count::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--la-maroon);
    }

    /* Filter */
    .la-filter-form { padding: 20px 24px 22px; }
    .la-filter-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }
    .la-field { min-width: 0; }
    .la-field label {
        display: block;
        margin-bottom: 8px;
        color: #51484d;
        font-size: 12px;
        font-weight: 650;
    }
    .la-select-wrap { position: relative; }
    .la-select-wrap::after {
        content: '';
        position: absolute;
        right: 16px;
        top: 50%;
        width: 8px;
        height: 8px;
        border-right: 1.8px solid #846a72;
        border-bottom: 1.8px solid #846a72;
        transform: translateY(-70%) rotate(45deg);
        pointer-events: none;
    }
    .la-field select {
        display: block;
        width: 100%;
        height: 44px;
        min-width: 0;
        padding: 0 37px 0 13px;
        appearance: none;
        -webkit-appearance: none;
        border: 1px solid #e5d8db;
        border-radius: 9px;
        background: #fff;
        color: #3f3439;
        font-size: 12px;
        cursor: pointer;
        transition: border-color .16s ease, box-shadow .16s ease;
    }
    .la-field select:focus-visible {
        outline: none;
        border-color: #a56070;
        box-shadow: 0 0 0 3px rgba(128, 11, 34, .09);
    }
    .la-filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 9px;
        margin-top: 16px;
    }
    .la-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 9px 17px;
        border: 1px solid #e4d6d8;
        border-radius: 8px;
        background: #fff;
        color: #55484e;
        cursor: pointer;
        font-size: 12px;
        font-weight: 650;
        line-height: 1.3;
        transition: background .16s ease, border-color .16s ease;
    }
    .la-btn svg { width: 16px; height: 16px; }
    .la-btn:hover { background: #fcf1f4; border-color: #d9bbc3; }
    .la-btn--primary {
        background: var(--la-maroon);
        border-color: var(--la-maroon);
        color: #fff;
    }
    .la-btn--primary:hover {
        background: var(--la-maroon-dark);
        border-color: var(--la-maroon-dark);
        color: #fff;
    }
    .la-btn:focus-visible,
    .la-page a:focus-visible {
        outline: 2px solid var(--la-maroon);
        outline-offset: 3px;
    }

    /* Tabel log */
    .la-table-wrap {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }
    .la-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
        font-size: 12px;
        line-height: 1.5;
    }
    .la-table th {
        padding: 14px 16px;
        background: var(--la-maroon);
        color: #fff;
        text-align: left;
        white-space: nowrap;
        font-size: 11px;
        font-weight: 650;
    }
    .la-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0e8e9;
        color: #574f53;
        vertical-align: middle;
    }
    .la-table tbody tr:last-child td { border-bottom: 0; }
    .la-table tbody tr:nth-child(even) { background: #fdfafb; }
    .la-table tbody tr:hover { background: #fcf4f6; }
    .la-table th:first-child,
    .la-table td:first-child { width: 54px; }

    .la-user {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 190px;
    }
    .la-user-avatar {
        display: grid;
        flex: 0 0 39px;
        place-items: center;
        width: 39px;
        height: 39px;
        border-radius: 10px;
        background: #f8e9ed;
        color: var(--la-maroon);
    }
    .la-user-avatar svg { width: 21px; height: 21px; }
    .la-user-details { min-width: 0; }
    .la-user-name {
        color: #292427;
        font-weight: 700;
        overflow-wrap: anywhere;
    }
    .la-user-email {
        margin-top: 2px;
        color: #90878b;
        font-size: 11px;
        overflow-wrap: anywhere;
    }
    .la-role {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 7px;
        background: #f5eff1;
        color: #744252;
        font-size: 11px;
        font-weight: 650;
        white-space: nowrap;
    }
    .la-activity {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 20px;
        background: #f7edf0;
        color: #7b263e;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .la-activity::before {
        content: '';
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .la-activity--login { background: #e4f6eb; color: #167545; }
    .la-activity--logout { background: #edf0f6; color: #596581; }
    .la-activity--create { background: #eaf1fe; color: #2d5da2; }
    .la-activity--update { background: #fff3db; color: #906116; }
    .la-activity--delete { background: #fff0f0; color: #a12a40; }

    .la-desc {
        max-width: 420px;
        min-width: 170px;
        overflow-wrap: anywhere;
        line-height: 1.6;
    }
    .la-time { white-space: nowrap; font-variant-numeric: tabular-nums; }
    .la-time-date { display: block; color: #40373b; font-weight: 650; }
    .la-time-clock { display: block; margin-top: 3px; color: #90868a; font-size: 11px; }
    .la-empty {
        padding: 42px 16px !important;
        text-align: center;
        color: #81787d;
    }
    .la-empty-icon {
        display: grid;
        place-items: center;
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;
        border-radius: 14px;
        background: #f8edef;
        color: var(--la-maroon);
    }
    .la-empty-icon svg { width: 25px; height: 25px; }
    .la-empty strong { display: block; color: #45393e; margin-bottom: 4px; }

    /* Footer dan pagination */
    .la-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        min-height: 66px;
        padding: 16px 24px;
        border-top: 1px solid #f0e8e9;
        color: var(--la-muted);
        font-size: 11px;
    }
    .la-footer strong { color: #4e4248; }
    .la-pager {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .la-page-btn,
    .la-page-current {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 7px 11px;
        border: 1px solid #eadce0;
        border-radius: 7px;
        background: #fff;
        color: var(--la-maroon);
        font-size: 11px;
        font-weight: 650;
    }
    .la-page-btn:hover { background: #f9eef1; }
    .la-page-btn.is-disabled { color: #b4aab0; background: #faf8f9; cursor: default; }
    .la-page-current { background: #f7edf0; color: #6e3446; }

    /* Tablet */
    @media (max-width: 1024px) {
        .la-stats { gap: 11px; }
        .la-stat { padding: 17px 15px; gap: 11px; }
        .la-stat-icon { flex-basis: 42px; width: 42px; height: 42px; }
    }
    @media (max-width: 820px) {
        .la-filter-grid { grid-template-columns: 1fr 1fr; }
        .la-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .la-stats .la-stat:last-child { grid-column: 1 / -1; }
    }

    /* HP: tabel jadi kartu per aktivitas agar tidak perlu geser horizontal */
    @media (max-width: 650px) {
        .la-header { margin-bottom: 18px; padding-bottom: 15px; }
        .la-header h1 { font-size: 22px; }
        .la-stats { grid-template-columns: 1fr; gap: 9px; margin-bottom: 16px; }
        .la-stats .la-stat:last-child { grid-column: auto; }
        .la-stat { padding: 14px 16px; }
        .la-stat-number { font-size: 23px; }
        .la-card { margin-bottom: 16px; }
        .la-card-header { padding: 16px; }
        .la-filter-form { padding: 16px; }
        .la-filter-grid { grid-template-columns: 1fr; gap: 13px; }
        .la-filter-actions { justify-content: stretch; }
        .la-filter-actions .la-btn { flex: 1; }
        .la-table-wrap { overflow: visible; padding: 2px 0 8px; }
        .la-table,
        .la-table tbody,
        .la-table tr,
        .la-table td { display: block; width: 100%; min-width: 0; }
        .la-table thead {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
        }
        .la-table tbody tr {
            width: auto;
            margin: 12px 13px;
            padding: 5px 14px;
            border: 1px solid #ece1e1;
            border-radius: 10px;
            background: #fff !important;
            box-shadow: 0 2px 8px rgba(62,10,25,.025);
        }
        .la-table tbody tr:hover { background: #fcf8f9 !important; }
        .la-table tbody td {
            display: grid;
            grid-template-columns: 92px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1e9eb;
            font-size: 12px;
        }
        .la-table tbody td:last-child { border-bottom: 0; }
        .la-table tbody td::before {
            content: attr(data-label);
            color: #91868b;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.4;
        }
        .la-table tbody .la-empty {
            display: block;
            width: 100%;
            padding: 28px 12px !important;
        }
        .la-table tbody .la-empty::before { display: none; }
        .la-user { min-width: 0; }
        .la-desc { min-width: 0; max-width: none; }
        .la-time { white-space: normal; }
        .la-footer { padding: 15px 16px; }
    }
    @media (max-width: 380px) {
        .la-table tbody td { grid-template-columns: 78px minmax(0, 1fr); gap: 9px; }
        .la-table tbody tr { margin: 10px; padding: 4px 11px; }
        .la-user-avatar { flex-basis: 34px; width: 34px; height: 34px; }
        .la-user { gap: 8px; }
        .la-footer { gap: 10px; }
    }
</style>
@endpush

@section('content')
<div class="la-page">
    <header class="la-header">
        <nav class="la-breadcrumb" aria-label="Navigasi halaman">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">Log Aktivitas</span>
        </nav>
        <h1>Log Aktivitas</h1>
        <p>Pantau riwayat aktivitas pengguna dan perubahan data dalam sistem SIMAHAD.</p>
    </header>

    {{-- Statistik tetap menggunakan nilai dari controller yang sudah ada. --}}
    <section class="la-stats" aria-label="Ringkasan aktivitas sistem">
        <div class="la-stat">
            <div class="la-stat-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18"/><path d="m7 15 4-5 3 3 5-7"/><path d="M16 6h3v3"/>
                </svg>
            </div>
            <div class="la-stat-info">
                <span class="la-stat-label">Total Aktivitas</span>
                <p class="la-stat-number">{{ number_format($totalAktivitas ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="la-stat">
            <div class="la-stat-icon la-stat-icon--green" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2"/><path d="M17 8a3 3 0 0 1 0 6"/><path d="M18 17c1.7.6 3 1.9 3 3"/>
                </svg>
            </div>
            <div class="la-stat-info">
                <span class="la-stat-label">Pengguna Aktif</span>
                <p class="la-stat-number">{{ number_format($penggunaAktif ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="la-stat">
            <div class="la-stat-icon la-stat-icon--gold" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/><path d="m9 16 2 2 4-4"/>
                </svg>
            </div>
            <div class="la-stat-info">
                <span class="la-stat-label">Aktivitas Hari Ini</span>
                <p class="la-stat-number">{{ number_format($aktivitasHariIni ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </section>

    <section class="la-card" aria-labelledby="laFilterTitle">
        <div class="la-card-header">
            <div>
                <h2 id="laFilterTitle">Filter Aktivitas</h2>
                <p>Pilih periode, peran pengguna, dan jenis aktivitas untuk mempersempit riwayat.</p>
            </div>
        </div>

        <form action="{{ route('admin.log-aktivitas') }}" method="GET" class="la-filter-form">
            <div class="la-filter-grid">
                <div class="la-field">
                    <label for="laPeriode">Periode</label>
                    <div class="la-select-wrap">
                        <select id="laPeriode" name="periode">
                            <option value="">Semua Periode</option>
                            <option value="hari_ini" @selected(request('periode') === 'hari_ini')>Hari Ini</option>
                            <option value="7_hari" @selected(request('periode') === '7_hari')>7 Hari Terakhir</option>
                            <option value="30_hari" @selected(request('periode') === '30_hari')>30 Hari Terakhir</option>
                        </select>
                    </div>
                </div>

                <div class="la-field">
                    <label for="laRole">Peran Pengguna</label>
                    <div class="la-select-wrap">
                        <select id="laRole" name="role">
                            <option value="">Semua Peran</option>
                            @foreach (['admin', 'mahasiswa', 'guru', 'musyrifah', 'pimpinan'] as $role)
                                <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="la-field">
                    <label for="laAktivitas">Jenis Aktivitas</label>
                    <div class="la-select-wrap">
                        <select id="laAktivitas" name="aktivitas">
                            <option value="">Semua Aktivitas</option>
                            <option value="login" @selected(request('aktivitas') === 'login')>Login</option>
                            <option value="logout" @selected(request('aktivitas') === 'logout')>Logout</option>
                            <option value="create_user" @selected(request('aktivitas') === 'create_user')>Tambah Pengguna</option>
                            <option value="update_user" @selected(request('aktivitas') === 'update_user')>Ubah Pengguna</option>
                            <option value="delete_user" @selected(request('aktivitas') === 'delete_user')>Hapus Pengguna</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="la-filter-actions">
                @if (request()->filled('periode') || request()->filled('role') || request()->filled('aktivitas'))
                    <a class="la-btn" href="{{ route('admin.log-aktivitas') }}">Reset Filter</a>
                @endif
                <button type="submit" class="la-btn la-btn--primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 7h16M7 12h10M10 17h4"/>
                    </svg>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </section>

    <section class="la-card" aria-labelledby="laHistoryTitle">
        <div class="la-card-header">
            <div>
                <h2 id="laHistoryTitle">Riwayat Aktivitas</h2>
                <p>Daftar kejadian yang tercatat dalam sistem.</p>
            </div>
            <span class="la-count">{{ number_format($logs->total(), 0, ',', '.') }} catatan</span>
        </div>

        <div class="la-table-wrap" role="region" tabindex="0" aria-label="Tabel riwayat aktivitas">
            <table class="la-table">
                <caption class="la-sr-only" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;">Riwayat aktivitas pengguna SIMAHAD</caption>
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Pengguna</th>
                        <th scope="col">Peran</th>
                        <th scope="col">Jenis Aktivitas</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $index => $log)
                        @php
                            $activityKey = strtolower((string) ($log->activity ?? ''));
                            $activityLabel = match ($activityKey) {
                                'login' => 'Login',
                                'logout' => 'Logout',
                                'create_user' => 'Tambah Pengguna',
                                'update_user' => 'Ubah Pengguna',
                                'delete_user' => 'Hapus Pengguna',
                                default => ucwords(str_replace('_', ' ', $activityKey ?: '-')),
                            };
                            $activityTone = match ($activityKey) {
                                'login' => 'login',
                                'logout' => 'logout',
                                'create_user' => 'create',
                                'update_user' => 'update',
                                'delete_user' => 'delete',
                                default => 'other',
                            };
                        @endphp
                        <tr>
                            <td data-label="No.">{{ $logs->firstItem() + $index }}</td>
                            <td data-label="Pengguna">
                                <div class="la-user">
                                    <span class="la-user-avatar" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="8" r="3.8"/><path d="M4 21c0-4.5 3-7 8-7s8 2.5 8 7"/>
                                        </svg>
                                    </span>
                                    <div class="la-user-details">
                                        <div class="la-user-name">{{ $log->user_name ?: 'Pengguna tidak diketahui' }}</div>
                                        @if (!empty($log->user_email))
                                            <div class="la-user-email">{{ $log->user_email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td data-label="Peran"><span class="la-role">{{ ucfirst($log->user_role ?? '-') }}</span></td>
                            <td data-label="Aktivitas"><span class="la-activity la-activity--{{ $activityTone }}">{{ $activityLabel }}</span></td>
                            <td data-label="Keterangan"><div class="la-desc">{{ $log->description ?: '—' }}</div></td>
                            <td data-label="Waktu">
                                <time class="la-time" @if ($log->created_at) datetime="{{ $log->created_at->toIso8601String() }}" @endif>
                                    <span class="la-time-date">{{ $log->created_at?->format('d/m/Y') ?? '—' }}</span>
                                    <span class="la-time-clock">{{ $log->created_at?->format('H:i') ?? '—' }}</span>
                                </time>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="la-empty" data-label="">
                                <div class="la-empty-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="4" y="3" width="16" height="18" rx="2"/>
                                        <path d="M8 8h8M8 12h8M8 16h4"/>
                                    </svg>
                                </div>
                                <strong>Belum ada aktivitas yang tercatat</strong>
                                Silakan ubah filter atau periksa kembali setelah aktivitas sistem berlangsung.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="la-footer">
            <span>
                Menampilkan <strong>{{ $logs->count() }}</strong> dari
                <strong>{{ number_format($logs->total(), 0, ',', '.') }}</strong> aktivitas
            </span>
            <nav class="la-pager" aria-label="Navigasi halaman log aktivitas">
                @if ($logs->onFirstPage())
                    <span class="la-page-btn is-disabled" aria-disabled="true">Sebelumnya</span>
                @else
                    <a class="la-page-btn" href="{{ $logs->appends(request()->only(['periode', 'role', 'aktivitas']))->previousPageUrl() }}">Sebelumnya</a>
                @endif

                <span class="la-page-current">{{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>

                @if ($logs->hasMorePages())
                    <a class="la-page-btn" href="{{ $logs->appends(request()->only(['periode', 'role', 'aktivitas']))->nextPageUrl() }}">Berikutnya</a>
                @else
                    <span class="la-page-btn is-disabled" aria-disabled="true">Berikutnya</span>
                @endif
            </nav>
        </div>
    </section>
</div>
@endsection
