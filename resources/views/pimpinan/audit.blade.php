{{-- Audit Trail digunakan bersama oleh Admin dan Pimpinan.
     URL/route tetap pimpinan.audit; tampilan mengikuti role yang sedang login. --}}
@extends(strtolower((string) auth()->user()?->role) === 'admin' ? 'layouts.admin' : 'layouts.pimpinan')

@section('title', 'Audit Trail SIMAHAD')

@section('content')
<style>
    /* SIMAHAD | Audit Trail — selaras dengan Manajemen Pengguna & Log Aktivitas */
    .audit-page, .audit-page * { box-sizing: border-box; }
    .audit-page {
        --audit-primary: #800b22;
        --audit-dark: #620819;
        --audit-text: #292427;
        --audit-muted: #81787d;
        --audit-line: #ecdee1;
        --audit-soft: #fcf8f9;
        width: 100%;
        min-width: 0;
        color: var(--audit-text);
        font-family: inherit;
        padding-bottom: 24px;
    }
    .audit-page a { text-decoration: none; }
    .audit-page button, .audit-page input, .audit-page select { font: inherit; }
    .audit-page a:focus-visible, .audit-page button:focus-visible,
    .audit-page input:focus-visible, .audit-page select:focus-visible,
    .audit-page summary:focus-visible {
        outline: 2px solid var(--audit-primary);
        outline-offset: 3px;
    }
    .audit-page svg { flex: none; }

    /* Heading */
    .audit-header {
        border-bottom: 1px solid #e8cfd6;
        padding-bottom: 19px;
        margin-bottom: 23px;
    }
    .audit-breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-bottom: 11px; font-size: 12px; color: #8e8487; }
    .audit-breadcrumb a { color: var(--audit-primary); font-weight: 600; }
    .audit-header h1 { margin: 0 0 6px; font-size: clamp(22px, 2.4vw, 27px); font-weight: 750; letter-spacing: -.4px; }
    .audit-header p { margin: 0; max-width: 730px; color: #787175; font-size: 13px; line-height: 1.7; }

    /* Ringkasan */
    .audit-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px; }
    .audit-stat {
        display: flex; align-items: center; gap: 14px;
        min-width: 0; padding: 18px 20px;
        border: 1px solid var(--audit-line); border-radius: 12px;
        background: #fff; box-shadow: 0 3px 14px rgba(62,10,25,.035);
    }
    .audit-stat-icon { display: grid; place-items: center; width: 44px; height: 44px; border-radius: 11px; color: var(--audit-primary); background: #f8eaee; }
    .audit-stat-icon svg { width: 21px; height: 21px; }
    .audit-stat-label { display: block; margin-bottom: 6px; color: var(--audit-muted); font-size: 12px; font-weight: 600; }
    .audit-stat-number { display: block; color: var(--audit-text); font-size: clamp(21px, 2.2vw, 27px); font-weight: 800; line-height: 1.1; font-variant-numeric: tabular-nums; }

    /* Panel & tab */
    .audit-card { min-width: 0; overflow: hidden; border: 1px solid #ece1e1; border-radius: 12px; background: #fff; box-shadow: 0 3px 14px rgba(62,10,25,.035); }
    .audit-tabbar { display: flex; gap: 7px; padding: 6px; margin-bottom: 18px; width: max-content; max-width: 100%; border: 1px solid var(--audit-line); border-radius: 10px; background: #fff; }
    .audit-tab { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 38px; padding: 9px 16px; border-radius: 7px; color: #69595f; font-size: 12px; font-weight: 700; white-space: nowrap; transition: background .18s, color .18s; }
    .audit-tab svg { width: 15px; height: 15px; }
    .audit-tab:hover { color: var(--audit-primary); background: #faf1f3; }
    .audit-tab.active { background: var(--audit-primary); color: #fff; }

    /* Filter */
    .audit-filter { padding: 18px 22px 20px; margin-bottom: 19px; }
    .audit-filter-heading { display: flex; align-items: center; gap: 9px; margin-bottom: 16px; }
    .audit-filter-heading svg { width: 17px; height: 17px; color: var(--audit-primary); }
    .audit-filter-heading h2 { margin: 0; font-size: 15px; font-weight: 750; }
    .audit-filter-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)) minmax(200px, 1.35fr); align-items: end; gap: 12px; }
    .audit-field { display: flex; flex-direction: column; min-width: 0; gap: 7px; }
    .audit-field label { font-size: 11.5px; font-weight: 700; color: #645b5f; }
    .audit-field select, .audit-field input {
        width: 100%; min-width: 0; height: 41px; padding: 0 12px;
        border: 1px solid #e4d8db; border-radius: 8px; outline: none;
        color: #30272b; background: #fff; font-size: 12px;
        transition: border-color .18s, box-shadow .18s;
    }
    .audit-field select:focus, .audit-field input:focus { border-color: #9f5264; box-shadow: 0 0 0 3px rgba(128,11,34,.07); }
    .audit-filter-actions { display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 9px; margin-top: 15px; }
    .audit-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 39px; padding: 9px 17px; border: 1px solid #e4d6d8; border-radius: 8px; background: #fff; color: #51454a; font-size: 12px; font-weight: 700; cursor: pointer; }
    .audit-btn svg { width: 15px; height: 15px; }
    .audit-btn:hover { background: #fcf1f4; color: var(--audit-primary); }
    .audit-btn-primary { border-color: var(--audit-primary); background: var(--audit-primary); color: #fff; }
    .audit-btn-primary:hover { background: var(--audit-dark); border-color: var(--audit-dark); color: #fff; }

    /* Tabel dan konten audit */
    .audit-list-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 20px 23px 17px; border-bottom: 1px solid #f0e8e9; }
    .audit-list-head h2 { margin: 0 0 5px; font-size: 16px; font-weight: 750; }
    .audit-list-head p { margin: 0; color: var(--audit-muted); font-size: 12px; }
    .audit-count { display: inline-flex; align-items: center; gap: 7px; padding: 7px 10px; border-radius: 8px; color: #704454; background: #faf1f4; font-size: 11px; font-weight: 700; }
    .audit-table-wrap { width: 100%; max-width: 100%; overflow-x: auto; }
    .audit-table { width: 100%; min-width: 1000px; border-collapse: collapse; font-size: 12px; }
    .audit-table thead th { padding: 14px 15px; background: var(--audit-primary); color: #fff; text-align: left; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .audit-table tbody td { padding: 15px; border-bottom: 1px solid #f0e8e9; vertical-align: top; color: #5a5155; line-height: 1.55; }
    .audit-table tbody tr:nth-child(even) { background: #fdfafb; }
    .audit-table tbody tr:hover { background: #fcf4f6; }
    .audit-date { color: #322a2d; font-weight: 700; white-space: nowrap; }
    .audit-time { margin-top: 4px; font-size: 11px; color: #94878c; }
    .audit-user { color: #31292d; font-weight: 700; }
    .audit-muted { color: #8a7c83; font-size: 11px; }
    .audit-object { color: #352d31; font-weight: 650; }
    .audit-text-break { overflow-wrap: anywhere; word-break: break-word; }
    .audit-report-link { color: var(--audit-primary); font-weight: 700; }
    .audit-report-link:hover { text-decoration: underline; }
    .audit-pill { display: inline-flex; align-items: center; max-width: 100%; padding: 6px 10px; border-radius: 7px; background: #f5eff1; color: #744252; font-size: 11px; font-weight: 700; overflow-wrap: anywhere; }
    .audit-pill.action { background: #f8e9ed; color: var(--audit-primary); }
    .audit-pill.action.is-create { background: #e8f7ed; color: #147439; }
    .audit-pill.action.is-delete { background: #ffe8ed; color: #a52540; }
    .audit-pill.action.is-update { background: #fff2dd; color: #8a5c12; }
    .audit-pill.version { background: #f1f3f7; color: #4a5361; }
    .audit-diff { max-width: 340px; }
    .audit-diff details { min-width: 0; }
    .audit-diff summary { width: max-content; max-width: 100%; cursor: pointer; color: var(--audit-primary); font-size: 11.5px; font-weight: 700; }
    .audit-diff summary:hover { text-decoration: underline; }
    .audit-diff-list { display: grid; gap: 10px; margin-top: 10px; }
    .audit-diff-item { padding: 10px 11px; border: 1px solid #f0e2e6; border-radius: 8px; background: #fffafb; overflow-wrap: anywhere; }
    .audit-diff-key { display: block; margin-bottom: 6px; color: #5d3644; font-weight: 750; font-size: 11px; }
    .audit-diff-values { display: grid; gap: 4px; color: #635a5f; font-size: 11px; }
    .audit-diff-values b { color: #3e3439; }
    .audit-empty { padding: 36px 18px !important; text-align: center; color: #8b8286 !important; }
    .audit-empty-icon { display: grid; place-items: center; width: 44px; height: 44px; margin: 0 auto 11px; border-radius: 12px; background: #f8edef; color: var(--audit-primary); }
    .audit-empty-icon svg { width: 22px; height: 22px; }
    .audit-empty strong { display: block; margin-bottom: 4px; color: #43383d; font-size: 13px; }
    .audit-empty span { font-size: 12px; }
    .audit-footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 15px; padding: 15px 22px 18px; color: var(--audit-muted); font-size: 11.5px; }
    .audit-pagination { min-width: 0; max-width: 100%; }
    .audit-pagination nav { max-width: 100%; }
    .audit-pagination svg { width: 18px; }

    @media (max-width: 1120px) {
        .audit-filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 800px) {
        .audit-summary { gap: 10px; }
        .audit-stat { padding: 15px; gap: 10px; }
        .audit-stat-icon { width: 38px; height: 38px; }
    }
    @media (max-width: 700px) {
        .audit-header { margin-bottom: 18px; }
        .audit-summary { grid-template-columns: 1fr 1fr; }
        .audit-summary .audit-stat:last-child { grid-column: 1 / -1; }
        .audit-filter { padding: 17px 16px; }
        .audit-list-head { padding: 17px 16px; }
        .audit-footer { padding: 15px 16px; }
        .audit-table-wrap { overflow: visible; }
        .audit-table { min-width: 0; display: block; }
        .audit-table thead { display: none; }
        .audit-table tbody { display: grid; gap: 12px; padding: 14px; }
        .audit-table tbody tr { display: block; border: 1px solid #eadfe2; border-radius: 10px; background: #fff !important; overflow: hidden; }
        .audit-table tbody tr:hover { background: #fff !important; }
        .audit-table tbody td { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 6px 12px; padding: 10px 13px; border-bottom: 1px solid #f3eced; font-size: 12px; }
        .audit-table tbody td:last-child { border-bottom: 0; }
        .audit-table tbody td::before { content: attr(data-label); flex: 0 0 90px; color: #8b7c82; font-size: 11px; font-weight: 700; }
        .audit-table tbody td .audit-cell-value { flex: 1 1 140px; min-width: 0; text-align: right; overflow-wrap: anywhere; }
        .audit-table tbody td .audit-diff { max-width: 100%; }
        .audit-table tbody td .audit-diff summary { margin-left: auto; }
        .audit-table tbody td .audit-diff-list { text-align: left; }
        .audit-table tbody tr.audit-empty-row { border: 0; }
        .audit-table tbody td.audit-empty { display: block; }
        .audit-table tbody td.audit-empty::before { display: none; }
    }
    @media (max-width: 520px) {
        .audit-header h1 { font-size: 22px; }
        .audit-summary { grid-template-columns: 1fr; }
        .audit-summary .audit-stat:last-child { grid-column: auto; }
        .audit-stat { padding: 13px 15px; }
        .audit-tabbar { width: 100%; }
        .audit-tab { flex: 1; padding: 9px 10px; font-size: 11px; }
        .audit-filter-grid { grid-template-columns: 1fr; }
        .audit-filter-actions { display: grid; grid-template-columns: 1fr 1fr; }
        .audit-filter-actions .audit-btn { padding: 10px 7px; }
        .audit-table tbody { padding: 10px; }
        .audit-footer { align-items: flex-start; flex-direction: column; }
        .audit-pagination { width: 100%; overflow-x: auto; }
    }
</style>

<div class="audit-page">
    <header class="audit-header">
        <nav class="audit-breadcrumb" aria-label="Navigasi halaman">
            @if (strtolower((string) auth()->user()?->role) === 'admin')
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('pimpinan.dashboard') }}">Dashboard</a>
            @endif
            <span aria-hidden="true">/</span>
            <span aria-current="page">Audit Trail</span>
        </nav>
        <h1>Audit Trail SIMAHAD</h1>
        <p>Riwayat perubahan data dan aktivitas penting yang tercatat di sistem. Informasi ditampilkan untuk kebutuhan pemantauan dan penelusuran.</p>
    </header>

    <section class="audit-summary" aria-label="Ringkasan audit">
        <article class="audit-stat">
            <span class="audit-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 19V5M4 19h16M8 15l4-5 3 2 4-6"/><path d="M17 6h2v2"/></svg></span>
            <div><span class="audit-stat-label">Total Aktivitas</span><strong class="audit-stat-number">{{ number_format((int) ($ringkasan['total'] ?? 0), 0, ',', '.') }}</strong></div>
        </article>
        <article class="audit-stat">
            <span class="audit-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18M8 15h3"/></svg></span>
            <div><span class="audit-stat-label">Aktivitas Hari Ini</span><strong class="audit-stat-number">{{ number_format((int) ($ringkasan['hari_ini'] ?? 0), 0, ',', '.') }}</strong></div>
        </article>
        <article class="audit-stat">
            <span class="audit-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M8 3h8l4 4v14H4V3h4Z"/><path d="M16 3v5h4M8 12h8M8 16h6"/></svg></span>
            <div><span class="audit-stat-label">Audit Laporan</span><strong class="audit-stat-number">{{ number_format((int) ($ringkasan['audit_laporan'] ?? 0), 0, ',', '.') }}</strong></div>
        </article>
    </section>

    <nav class="audit-tabbar" aria-label="Jenis riwayat audit">
        <a class="audit-tab {{ $tab === 'aktivitas' ? 'active' : '' }}"
           href="{{ route('pimpinan.audit', ['tab' => 'aktivitas']) }}"
           @if($tab === 'aktivitas') aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 12h4l3-7 4 14 3-7h4"/></svg>
            Aktivitas Sistem
        </a>
        <a class="audit-tab {{ $tab === 'laporan' ? 'active' : '' }}"
           href="{{ route('pimpinan.audit', ['tab' => 'laporan']) }}"
           @if($tab === 'laporan') aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
            Audit Laporan
        </a>
    </nav>

    <section class="audit-card audit-filter" aria-labelledby="auditFilterTitle">
        <div class="audit-filter-heading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h16l-6 7v6l-4 2v-8L4 5Z"/></svg>
            <h2 id="auditFilterTitle">Filter Riwayat Audit</h2>
        </div>
        <form action="{{ route('pimpinan.audit') }}" method="GET">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="audit-filter-grid">
                <div class="audit-field">
                    <label for="audit-modul">{{ $tab === 'aktivitas' ? 'Modul' : 'Jenis Laporan' }}</label>
                    <select id="audit-modul" name="modul">
                        <option value="">{{ $tab === 'aktivitas' ? 'Semua modul' : 'Semua jenis' }}</option>
                        @if ($tab === 'aktivitas')
                            @foreach ($daftarModul as $kunci => $label)
                                <option value="{{ $kunci }}" @selected(($filter['modul'] ?? '') === $kunci)>{{ $label }}</option>
                            @endforeach
                        @else
                            @foreach (\App\Services\LaporanRegisterService::JUDUL as $kunci => $label)
                                <option value="{{ $kunci }}" @selected(($filter['modul'] ?? '') === $kunci)>{{ $label }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="audit-field">
                    <label for="audit-role">Peran Pengguna</label>
                    <select name="role" id="audit-role">
                        <option value="">Semua peran</option>
                        @foreach (['pimpinan', 'musyrifah', 'guru', 'admin', 'mahasiswa', 'sistem'] as $peran)
                            <option value="{{ $peran }}" @selected(($filter['role'] ?? '') === $peran)>{{ ucfirst($peran) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="audit-field">
                    <label for="audit-periode">Periode</label>
                    <select name="periode" id="audit-periode">
                        <option value="">Semua waktu</option>
                        <option value="hari_ini" @selected(($filter['periode'] ?? '') === 'hari_ini')>Hari ini</option>
                        <option value="7_hari" @selected(($filter['periode'] ?? '') === '7_hari')>7 hari terakhir</option>
                        <option value="30_hari" @selected(($filter['periode'] ?? '') === '30_hari')>30 hari terakhir</option>
                    </select>
                </div>
                <div class="audit-field">
                    <label for="audit-cari">Pencarian</label>
                    <input type="search" id="audit-cari" name="cari" maxlength="150" value="{{ $filter['cari'] ?? '' }}" placeholder="Cari pengguna, objek, keterangan...">
                </div>
            </div>
            <div class="audit-filter-actions">
                <a class="audit-btn" href="{{ route('pimpinan.audit', ['tab' => $tab]) }}">Reset Filter</a>
                <button type="submit" class="audit-btn audit-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h16l-6 7v6l-4 2v-8L4 5Z"/></svg>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </section>

    <section class="audit-card" aria-labelledby="auditListTitle">
        <div class="audit-list-head">
            <div>
                <h2 id="auditListTitle">{{ $tab === 'aktivitas' ? 'Riwayat Aktivitas Sistem' : 'Riwayat Audit Laporan' }}</h2>
                <p>{{ $tab === 'aktivitas' ? 'Catatan perubahan berdasarkan pengguna, modul, dan waktu.' : 'Riwayat pembaruan dan penerbitan laporan periodik.' }}</p>
            </div>
            <span class="audit-count">{{ number_format((int) ($tab === 'aktivitas' ? $aktivitas->total() : $laporan->total()), 0, ',', '.') }} catatan</span>
        </div>

        <div class="audit-table-wrap" role="region" tabindex="0" aria-label="Tabel riwayat audit">
            @if ($tab === 'aktivitas')
                <table class="audit-table">
                    <thead><tr>
                        <th scope="col">Waktu</th><th scope="col">Pengguna</th><th scope="col">Peran</th>
                        <th scope="col">Modul</th><th scope="col">Aksi</th><th scope="col">Objek</th><th scope="col">Perubahan</th>
                    </tr></thead>
                    <tbody>
                    @forelse ($aktivitas as $log)
                        @php
                            $aksi = (string) ($log->aksi ?: ($log->activity ?: '-'));
                            $aksiKey = strtolower($aksi);
                            $aksiWarna = str_contains($aksiKey, 'hapus') || str_contains($aksiKey, 'delete') ? 'is-delete' :
                                (str_contains($aksiKey, 'tambah') || str_contains($aksiKey, 'create') || str_contains($aksiKey, 'buat') ? 'is-create' :
                                (str_contains($aksiKey, 'ubah') || str_contains($aksiKey, 'update') || str_contains($aksiKey, 'edit') ? 'is-update' : ''));
                            $sesudahLog = $log->data_sesudah ?? [];
                            $sebelumLog = $log->data_sebelum ?? [];
                            if (is_string($sesudahLog)) $sesudahLog = json_decode($sesudahLog, true) ?: [];
                            if (is_string($sebelumLog)) $sebelumLog = json_decode($sebelumLog, true) ?: [];
                            if (!is_array($sesudahLog)) $sesudahLog = [];
                            if (!is_array($sebelumLog)) $sebelumLog = [];
                        @endphp
                        <tr>
                            <td data-label="Waktu"><div class="audit-cell-value">
                                <div class="audit-date">{{ $log->created_at?->format('d/m/Y') ?? '-' }}</div>
                                <div class="audit-time">{{ $log->created_at?->format('H:i') ?? '-' }}</div>
                            </div></td>
                            <td data-label="Pengguna"><div class="audit-cell-value audit-user audit-text-break">{{ $log->user_name ?: 'Sistem' }}</div></td>
                            <td data-label="Peran"><div class="audit-cell-value"><span class="audit-pill">{{ ucfirst((string) ($log->user_role ?: '-')) }}</span></div></td>
                            <td data-label="Modul"><div class="audit-cell-value audit-text-break">{{ $daftarModul[$log->modul] ?? ($log->modul ?: '-') }}</div></td>
                            <td data-label="Aksi"><div class="audit-cell-value"><span class="audit-pill action {{ $aksiWarna }}">{{ $aksi }}</span></div></td>
                            <td data-label="Objek"><div class="audit-cell-value audit-text-break">
                                <div class="audit-object">{{ $log->objek ?: '-' }} @if($log->objek_id)<span class="audit-muted">#{{ $log->objek_id }}</span>@endif</div>
                                @if (filled($log->description) && $log->description !== $log->objek)
                                    <div class="audit-muted" style="margin-top:4px">{{ $log->description }}</div>
                                @endif
                            </div></td>
                            <td data-label="Perubahan"><div class="audit-cell-value audit-diff">
                                @if (count($sesudahLog))
                                    <details>
                                        <summary>Lihat perubahan ({{ count($sesudahLog) }})</summary>
                                        <div class="audit-diff-list">
                                            @foreach ($sesudahLog as $kunci => $nilai)
                                                @php
                                                    $rahasia = preg_match('/password|passwd|token|secret|otp|api.?key|private.?key|authorization|cookie/i', (string) $kunci);
                                                    $sebelumNilai = $sebelumLog[$kunci] ?? null;
                                                    $sebelumTeks = $rahasia ? '••••••' : ($sebelumNilai === null ? '—' : (is_scalar($sebelumNilai) ? (string) $sebelumNilai : json_encode($sebelumNilai, JSON_UNESCAPED_UNICODE)));
                                                    $sesudahTeks = $rahasia ? '••••••' : ($nilai === null ? '—' : (is_scalar($nilai) ? (string) $nilai : json_encode($nilai, JSON_UNESCAPED_UNICODE)));
                                                @endphp
                                                <div class="audit-diff-item">
                                                    <span class="audit-diff-key">{{ str_replace('_', ' ', (string) $kunci) }}</span>
                                                    <div class="audit-diff-values">
                                                        <span><b>Sebelum:</b> {{ $sebelumTeks }}</span>
                                                        <span><b>Sesudah:</b> {{ $sesudahTeks }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @else
                                    <span class="audit-muted">Tidak ada rincian</span>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr class="audit-empty-row"><td colspan="7" class="audit-empty">
                            <span class="audit-empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></span>
                            <strong>Belum ada aktivitas</strong><span>Tidak ada catatan yang sesuai dengan filter ini.</span>
                        </td></tr>
                    @endforelse
                    </tbody>
                </table>
            @else
                <table class="audit-table">
                    <thead><tr>
                        <th scope="col">Waktu</th><th scope="col">Pengguna</th><th scope="col">Peran</th>
                        <th scope="col">Laporan</th><th scope="col">Aksi</th><th scope="col">Versi</th><th scope="col">Keterangan</th>
                    </tr></thead>
                    <tbody>
                    @forelse ($laporan as $jejak)
                        @php
                            $aksi = (string) $jejak->labelAksi();
                            $aksiKey = strtolower($aksi);
                            $aksiWarna = str_contains($aksiKey, 'hapus') || str_contains($aksiKey, 'arsip') ? 'is-delete' :
                                (str_contains($aksiKey, 'terbit') || str_contains($aksiKey, 'buat') ? 'is-create' :
                                (str_contains($aksiKey, 'ubah') || str_contains($aksiKey, 'perbarui') ? 'is-update' : ''));
                        @endphp
                        <tr>
                            <td data-label="Waktu"><div class="audit-cell-value">
                                <div class="audit-date">{{ $jejak->created_at?->format('d/m/Y') ?? '-' }}</div>
                                <div class="audit-time">{{ $jejak->created_at?->format('H:i') ?? '-' }}</div>
                            </div></td>
                            <td data-label="Pengguna"><div class="audit-cell-value audit-user audit-text-break">{{ $jejak->pengguna_nama ?: 'Sistem' }}</div></td>
                            <td data-label="Peran"><div class="audit-cell-value"><span class="audit-pill">{{ ucfirst((string) ($jejak->pengguna_role ?: '-')) }}</span></div></td>
                            <td data-label="Laporan"><div class="audit-cell-value audit-text-break">
                                @if ($jejak->laporan)
                                    <a class="audit-report-link" href="{{ route('pimpinan.laporan.detail', $jejak->laporan) }}">{{ $jejak->laporan->judul }}</a>
                                @else
                                    <strong>{{ \App\Services\LaporanRegisterService::JUDUL[$jejak->jenis_laporan] ?? '-' }}</strong>
                                @endif
                                <div class="audit-muted" style="margin-top:5px">
                                    {{ $jejak->periode }} @if($jejak->lantai) · Lantai {{ $jejak->lantai }} @endif
                                </div>
                            </div></td>
                            <td data-label="Aksi"><div class="audit-cell-value"><span class="audit-pill action {{ $aksiWarna }}">{{ $aksi }}</span></div></td>
                            <td data-label="Versi"><div class="audit-cell-value"><span class="audit-pill version">v{{ (int) $jejak->versi }}</span></div></td>
                            <td data-label="Keterangan"><div class="audit-cell-value audit-text-break">{{ $jejak->keterangan ?: '-' }}</div></td>
                        </tr>
                    @empty
                        <tr class="audit-empty-row"><td colspan="7" class="audit-empty">
                            <span class="audit-empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></span>
                            <strong>Belum ada audit laporan</strong><span>Tidak ada catatan yang sesuai dengan filter ini.</span>
                        </td></tr>
                    @endforelse
                    </tbody>
                </table>
            @endif
        </div>
        <div class="audit-footer">
            <span>Menampilkan <strong>{{ $tab === 'aktivitas' ? $aktivitas->count() : $laporan->count() }}</strong> dari <strong>{{ $tab === 'aktivitas' ? $aktivitas->total() : $laporan->total() }}</strong> catatan</span>
            <div class="audit-pagination">{{ $tab === 'aktivitas' ? $aktivitas->appends(request()->except('page'))->links() : $laporan->appends(request()->except('page'))->links() }}</div>
        </div>
    </section>
</div>
@endsection
