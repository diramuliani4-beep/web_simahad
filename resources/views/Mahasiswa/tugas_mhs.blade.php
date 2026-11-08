@extends('layouts.mahasiswa')

@section('title', 'Tugas')

@section('content')

<style>
    .tugas-page {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #111827;
    }

    .tugas-page * ,
    .tugas-page *::before,
    .tugas-page *::after {
        box-sizing: border-box;
    }

    /* STATISTIK */
    .tugas-page .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .tugas-page .stat-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border: 1px solid #eadfda;
        border-radius: 12px;
        background: #fff;
    }

    .tugas-page .stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 11px;
        background: #faedf0;
        color: var(--primary-maroon);
        flex-shrink: 0;
    }

    .tugas-page .stat-card.is-warning .stat-icon {
        background: #fef3c7;
        color: #b45309;
    }

    .tugas-page .stat-card.is-success .stat-icon {
        background: #dcfce7;
        color: #15803d;
    }

    .tugas-page .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #6b7280;
    }

    .tugas-page .stat-value {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.2;
        color: #111827;
    }

    /* FILTER */
    .tugas-page .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        padding: 12px 14px;
        margin-bottom: 18px;
        border: 1px solid #eadfda;
        border-radius: 12px;
        background: #fff;
    }

    .tugas-page .filter-bar input[type="search"],
    .tugas-page .filter-bar select {
        height: 40px;
        padding: 0 12px;
        border: 1px solid #e0d5d0;
        border-radius: 9px;
        background: #fff;
        font-family: inherit;
        font-size: 0.85rem;
        color: #111827;
    }

    .tugas-page .filter-bar input[type="search"] {
        flex: 1 1 240px;
        min-width: 0;
    }

    .tugas-page .filter-bar select {
        flex: 0 1 200px;
    }

    .tugas-page .btn-filter {
        height: 40px;
        padding: 0 18px;
        border: none;
        border-radius: 9px;
        background: var(--primary-maroon);
        color: #fff;
        font-family: inherit;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
    }

    .tugas-page .btn-filter:hover {
        background: var(--maroon-dark);
    }

    .tugas-page .btn-reset {
        height: 40px;
        display: inline-flex;
        align-items: center;
        padding: 0 14px;
        border: 1px solid #e0d5d0;
        border-radius: 9px;
        background: #fff;
        color: #4b5563;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
    }

    .tugas-page .btn-reset:hover {
        background: #faf7f5;
    }

    /* DAFTAR TUGAS */
    .tugas-page .task-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .tugas-page .task-card {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 16px 18px;
        border: 1px solid #eadfda;
        border-left: 4px solid var(--primary-maroon);
        border-radius: 12px;
        background: #fff;
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }

    .tugas-page .task-card:hover {
        box-shadow: 0 8px 22px rgba(107, 13, 27, 0.09);
        transform: translateY(-1px);
    }

    .tugas-page .task-card.is-terlambat {
        border-left-color: #dc2626;
    }

    .tugas-page .task-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .tugas-page .task-mapel {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--primary-maroon);
    }

    .tugas-page .task-title {
        margin: 2px 0 0;
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.4;
        color: #111827;
    }

    .tugas-page .task-title a {
        color: inherit;
        text-decoration: none;
    }

    .tugas-page .task-title a:hover {
        color: var(--primary-maroon);
    }

    .tugas-page .task-desc {
        font-size: 0.84rem;
        line-height: 1.55;
        color: #4b5563;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .tugas-page .task-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 16px;
        font-size: 0.78rem;
        color: #6b7280;
    }

    .tugas-page .task-meta strong {
        color: #374151;
        font-weight: 600;
    }

    .tugas-page .task-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: auto;
        padding-top: 10px;
        border-top: 1px dashed #eee4df;
    }

    .tugas-page .task-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .tugas-page .link-detail {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 13px;
        border-radius: 8px;
        background: var(--primary-maroon);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
    }

    .tugas-page .link-detail:hover {
        background: var(--maroon-dark);
    }

    .tugas-page .link-lampiran {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 11px;
        border: 1px solid #e0d5d0;
        border-radius: 8px;
        color: #4b5563;
        font-size: 0.76rem;
        font-weight: 600;
        text-decoration: none;
    }

    .tugas-page .link-lampiran:hover {
        background: #faf7f5;
        color: var(--primary-maroon);
    }

    .tugas-page .nilai-chip {
        display: inline-flex;
        align-items: baseline;
        gap: 4px;
        padding: 5px 11px;
        border-radius: 999px;
        background: #dcfce7;
        color: #15803d;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .tugas-page .nilai-chip span {
        font-size: 0.95rem;
    }

    /* BADGE STATUS */
    .tugas-page .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.71rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .tugas-page .badge.belum {
        background: #fef3c7;
        color: #92400e;
    }

    .tugas-page .badge.terkumpul {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .tugas-page .badge.terlambat {
        background: #fee2e2;
        color: #b91c1c;
    }

    .tugas-page .badge.dinilai {
        background: #dcfce7;
        color: #15803d;
    }

    /* KOSONG */
    .tugas-page .empty-state {
        padding: 50px 22px;
        border: 1px dashed #e0d5d0;
        border-radius: 14px;
        background: #fffdfc;
        text-align: center;
    }

    .tugas-page .empty-state svg {
        color: #c9a96a;
        margin-bottom: 10px;
    }

    .tugas-page .empty-state h3 {
        margin: 0 0 6px;
        font-size: 1rem;
        font-weight: 700;
        color: #374151;
    }

    .tugas-page .empty-state p {
        margin: 0;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .tugas-page .kelompok-note {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 14px;
        padding: 6px 12px;
        border-radius: 999px;
        background: var(--maroon-tint);
        color: var(--primary-maroon);
        font-size: 0.78rem;
        font-weight: 700;
    }

    @media (max-width: 992px) {
        .tugas-page .task-list {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 640px) {
        .tugas-page .stat-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .tugas-page .filter-bar select,
        .tugas-page .btn-filter,
        .tugas-page .btn-reset {
            flex: 1 1 100%;
        }

        .tugas-page .task-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="tugas-page">
    <div class="page-heading">
        <h1>Tugas Mahasiswa</h1>
        <p>Daftar tugas dari Guru yang ditujukan kepada kelompok pembelajaran Anda.</p>
    </div>

    <span class="kelompok-note">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        {{ $kelompok }}
    </span>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                    <path d="M8 8h8M8 12h8M8 16h5"></path>
                </svg>
            </span>
            <div>
                <div class="stat-label">Total Tugas</div>
                <div class="stat-value">{{ $statistik['total'] }}</div>
            </div>
        </div>

        <div class="stat-card is-warning">
            <span class="stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v5l3 2"></path>
                </svg>
            </span>
            <div>
                <div class="stat-label">Belum Dikumpulkan</div>
                <div class="stat-value">{{ $statistik['belum'] }}</div>
            </div>
        </div>

        <div class="stat-card is-success">
            <span class="stat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.1V12a10 10 0 1 1-5.9-9.1"></path>
                    <path d="m9 11 3 3L22 4"></path>
                </svg>
            </span>
            <div>
                <div class="stat-label">Sudah Dinilai</div>
                <div class="stat-value">{{ $statistik['dinilai'] }}</div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('tugas.mhs.index') }}" class="filter-bar">
        <input type="search" name="cari" value="{{ $filter['cari'] ?? '' }}" placeholder="Cari judul tugas...">

        <select name="status" aria-label="Filter status tugas">
            <option value="">Semua Status</option>
            @foreach ($statusPilihan as $nilai => $label)
                <option value="{{ $nilai }}" @selected(($filter['status'] ?? '') === $nilai)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn-filter">Terapkan</button>

        @if (filled($filter['cari'] ?? null) || filled($filter['status'] ?? null))
            <a href="{{ route('tugas.mhs.index') }}" class="btn-reset">Reset</a>
        @endif
    </form>

    @if ($daftarTugas->isEmpty())
        <div class="empty-state">
            <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
            <h3>Belum ada tugas</h3>
            <p>
                @if (filled($filter['cari'] ?? null) || filled($filter['status'] ?? null))
                    Tidak ada tugas yang cocok dengan pencarian atau filter Anda.
                @else
                    Tugas dari Guru untuk kelompok {{ $kelompok }} akan tampil di halaman ini.
                @endif
            </p>
        </div>
    @else
        <div class="task-list">
            @foreach ($daftarTugas as $item)
                @php
                    $tugas = $item['tugas'];
                    $pengumpulan = $item['pengumpulan'];
                    $status = $item['status'];
                @endphp

                <article class="task-card {{ $status['kelas'] === 'terlambat' ? 'is-terlambat' : '' }}">
                    <div class="task-top">
                        <div>
                            <div class="task-mapel">{{ $tugas->namaMataPelajaran() }}</div>
                            <h3 class="task-title">
                                <a href="{{ route('tugas.mhs.show', $tugas) }}">{{ $tugas->judul }}</a>
                            </h3>
                        </div>
                        <span class="badge {{ $status['kelas'] }}">{{ $status['label'] }}</span>
                    </div>

                    <p class="task-desc">{{ \Illuminate\Support\Str::limit(strip_tags($tugas->deskripsi), 140) }}</p>

                    <div class="task-meta">
                        <span>Guru: <strong>{{ $tugas->namaGuru() }}</strong></span>
                        <span>Diberikan: <strong>{{ $tugas->tanggalMulaiTeks() }}</strong></span>
                        <span>Batas: <strong>{{ $tugas->batasTeks() }}</strong></span>
                    </div>

                    <div class="task-footer">
                        <div class="task-actions">
                            <a href="{{ route('tugas.mhs.show', $tugas) }}" class="link-detail">
                                Lihat Detail
                            </a>

                            @if ($tugas->punyaLampiran())
                                <a href="{{ route('tugas.mhs.lampiran', $tugas) }}" class="link-lampiran">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    Lampiran
                                </a>
                            @endif
                        </div>

                        @if ($pengumpulan && $pengumpulan->nilaiDipublikasikan())
                            <span class="nilai-chip">Nilai <span>{{ $pengumpulan->nilai }}</span></span>
                        @elseif ($pengumpulan)
                            <span class="task-meta">Dikumpulkan {{ $pengumpulan->dikumpulkanTeks() }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>

@endsection
