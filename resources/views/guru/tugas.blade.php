@extends('layouts.guru')

@section('title', 'Tugas Pembelajaran')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .guru-tugas {
        font-family: 'Poppins', sans-serif;
        color: #292929;
        width: 100%;
        box-sizing: border-box;
    }

    .guru-tugas *,
    .guru-tugas *::before,
    .guru-tugas *::after {
        box-sizing: border-box;
    }

    .guru-tugas .page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .guru-tugas h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 600;
        color: #080808;
    }

    .guru-tugas .page-subtitle {
        margin: 6px 0 0;
        font-size: 14px;
        color: #5b5b5b;
    }

    .guru-tugas .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border: 1px solid var(--maroon);
        border-radius: 10px;
        background: #fff;
        color: var(--maroon);
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .guru-tugas .btn:hover {
        background: var(--maroon);
        color: #fff;
    }

    .guru-tugas .btn-primary {
        background: var(--maroon);
        color: #fff;
    }

    .guru-tugas .btn-primary:hover {
        background: var(--maroon-dark);
    }

    .guru-tugas .btn-small {
        padding: 6px 12px;
        font-size: 12.5px;
        border-radius: 8px;
    }

    .guru-tugas .btn-danger {
        border-color: #b91c1c;
        color: #b91c1c;
        background: #fff;
    }

    .guru-tugas .btn-danger:hover {
        background: #b91c1c;
        color: #fff;
    }

    .guru-tugas .alert {
        margin-bottom: 16px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .guru-tugas .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
        margin-bottom: 18px;
    }

    .guru-tugas .stat-card {
        padding: 15px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
    }

    .guru-tugas .stat-label {
        font-size: 13px;
        color: #6b6b6b;
        margin-bottom: 6px;
    }

    .guru-tugas .stat-value {
        font-size: 26px;
        font-weight: 700;
        color: var(--maroon);
    }

    .guru-tugas .filter-card {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 14px;
        margin-bottom: 18px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
    }

    .guru-tugas input[type="text"],
    .guru-tugas select {
        min-width: 180px;
        padding: 10px 12px;
        border: 1px solid #d9c9c4;
        border-radius: 9px;
        font-family: inherit;
        font-size: 14px;
        background: #fff;
        color: #292929;
    }

    .guru-tugas input[type="text"]:focus,
    .guru-tugas select:focus {
        outline: 2px solid rgba(128, 13, 34, 0.25);
        border-color: var(--maroon);
    }

    .guru-tugas .table-card {
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 12px;
        overflow-x: auto;
    }

    .guru-tugas table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .guru-tugas th {
        padding: 13px 14px;
        background: #faf4f2;
        border-bottom: 1px solid #eadfda;
        text-align: left;
        font-size: 12.5px;
        font-weight: 600;
        color: #6b4b45;
        white-space: nowrap;
    }

    .guru-tugas td {
        padding: 13px 14px;
        border-bottom: 1px solid #f2e9e5;
        font-size: 13.5px;
        vertical-align: top;
    }

    .guru-tugas tr:last-child td {
        border-bottom: none;
    }

    .guru-tugas .judul-cell {
        font-weight: 600;
        color: #1f1f1f;
    }

    .guru-tugas .meta {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        color: #7a7a7a;
    }

    .guru-tugas .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
    }

    .guru-tugas .badge-terbit {
        background: #dcfce7;
        color: #166534;
    }

    .guru-tugas .badge-draft {
        background: #f1f5f9;
        color: #475569;
    }

    .guru-tugas .badge-terlambat {
        background: #fee2e2;
        color: #b91c1c;
    }

    .guru-tugas .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .guru-tugas .empty {
        padding: 34px 18px;
        text-align: center;
        color: #7a7a7a;
        font-size: 14px;
    }

    @media (max-width: 900px) {
        .guru-tugas .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 520px) {
        .guru-tugas .stats-grid {
            grid-template-columns: 1fr;
        }

        .guru-tugas h1 {
            font-size: 21px;
        }

        .guru-tugas .filter-card input[type="text"],
        .guru-tugas .filter-card select {
            min-width: 100%;
            flex: 1 1 100%;
        }
    }
</style>

<div class="guru-tugas">
    <div class="page-header">
        <div>
            <h1>Tugas Pembelajaran</h1>
            <p class="page-subtitle">
                Kelola tugas untuk mata pelajaran dan kelompok yang menjadi tanggung jawab Anda.
            </p>
        </div>

        <a href="{{ route('guru.tugas.create') }}" class="btn btn-primary">+ Buat Tugas</a>
    </div>

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert" style="border-color:#fecaca;background:#fef2f2;color:#991b1b;">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Tugas</div>
            <div class="stat-value">{{ $statistik['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Dipublikasikan</div>
            <div class="stat-value">{{ $statistik['dipublikasikan'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Draft</div>
            <div class="stat-value">{{ $statistik['draft'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Pengumpulan</div>
            <div class="stat-value">{{ $statistik['pengumpulan'] }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('guru.tugas.index') }}" class="filter-card">
        <input
            type="text"
            name="cari"
            value="{{ $filter['cari'] ?? '' }}"
            placeholder="Cari judul tugas"
            autocomplete="off"
        >

        <select name="status">
            <option value="">Semua status</option>
            @foreach ($statusPilihan as $nilai => $label)
                <option value="{{ $nilai }}" @selected(($filter['status'] ?? '') === $nilai)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn">Terapkan</button>
        <a href="{{ route('guru.tugas.index') }}" class="btn">Reset</a>
    </form>

    <div class="table-card">
        @if ($daftarTugas->isEmpty())
            <div class="empty">
                Belum ada tugas yang sesuai. Klik "Buat Tugas" untuk menambahkan tugas baru.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Tugas</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelompok</th>
                        <th>Batas Pengumpulan</th>
                        <th>Mahasiswa</th>
                        <th>Pengumpulan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarTugas as $item)
                        @php
                            $jumlahMahasiswa = $item->jumlahAnggota();
                            $terkumpul = $item->pengumpulan_count;
                            $dinilai = $item->pengumpulan_dinilai_count;
                        @endphp
                        <tr>
                            <td>
                                <span class="judul-cell">{{ $item->judul }}</span>
                                <span class="meta">{{ \Illuminate\Support\Str::limit($item->deskripsi, 70) }}</span>
                            </td>
                            <td>{{ $item->namaMataPelajaran() }}</td>
                            <td>{{ $item->kelompok }}</td>
                            <td>
                                {{ $item->batasTeks() }}
                                @if ($item->sudahMelewatiBatas())
                                    <span class="badge badge-terlambat" style="margin-top:6px;">Ditutup</span>
                                @endif
                            </td>
                            <td>{{ $jumlahMahasiswa }}</td>
                            <td>
                                <span class="meta" style="margin-top:0;">
                                    Terkumpul: {{ $terkumpul }} /
                                    Belum: {{ max(0, $jumlahMahasiswa - $terkumpul) }} /
                                    Dinilai: {{ $dinilai }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $item->isDipublikasikan() ? 'badge-terbit' : 'badge-draft' }}">
                                    {{ $item->labelStatus() }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('guru.pengumpulan.index', $item) }}" class="btn btn-small">Pengumpulan</a>
                                    <a href="{{ route('guru.tugas.edit', $item) }}" class="btn btn-small">Edit</a>

                                    <form method="POST" action="{{ route('guru.tugas.status', $item) }}">
                                        @csrf
                                        <input
                                            type="hidden"
                                            name="status"
                                            value="{{ $item->isDipublikasikan() ? \App\Models\Tugas::STATUS_DRAFT : \App\Models\Tugas::STATUS_DIPUBLIKASIKAN }}"
                                        >
                                        <button type="submit" class="btn btn-small">
                                            {{ $item->isDipublikasikan() ? 'Jadikan Draft' : 'Publikasikan' }}
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('guru.tugas.destroy', $item) }}"
                                        onsubmit="return confirm('Hapus tugas {{ $item->judul }} beserta data pengumpulannya?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-small btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection
