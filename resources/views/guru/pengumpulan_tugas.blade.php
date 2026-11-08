@extends('layouts.guru')

@section('title', 'Pengumpulan Tugas')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .guru-pengumpulan {
        font-family: 'Poppins', sans-serif;
        color: #292929;
        width: 100%;
        box-sizing: border-box;
    }

    .guru-pengumpulan *,
    .guru-pengumpulan *::before,
    .guru-pengumpulan *::after {
        box-sizing: border-box;
    }

    .guru-pengumpulan .back-link {
        display: inline-block;
        margin-bottom: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--maroon);
        text-decoration: none;
    }

    .guru-pengumpulan h1 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
        color: #080808;
    }

    .guru-pengumpulan .page-subtitle {
        margin: 6px 0 0;
        font-size: 13.5px;
        color: #5b5b5b;
    }

    .guru-pengumpulan .info-card {
        margin: 16px 0 18px;
        padding: 16px 18px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 12px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .guru-pengumpulan .info-label {
        font-size: 12.5px;
        color: #6b6b6b;
        margin-bottom: 5px;
    }

    .guru-pengumpulan .info-value {
        font-size: 14px;
        font-weight: 600;
        color: #1f1f1f;
    }

    .guru-pengumpulan .alert {
        margin-bottom: 16px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13.5px;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .guru-pengumpulan .filter-card {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 14px;
        margin-bottom: 18px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
    }

    .guru-pengumpulan input[type="text"],
    .guru-pengumpulan input[type="number"],
    .guru-pengumpulan select {
        padding: 9px 11px;
        border: 1px solid #d9c9c4;
        border-radius: 9px;
        background: #fff;
        font-family: inherit;
        font-size: 13.5px;
        color: #292929;
    }

    .guru-pengumpulan input[type="number"] {
        width: 78px;
    }

    .guru-pengumpulan .table-card {
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 12px;
        overflow-x: auto;
    }

    .guru-pengumpulan table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1020px;
    }

    .guru-pengumpulan th {
        padding: 12px 14px;
        background: #faf4f2;
        border-bottom: 1px solid #eadfda;
        text-align: left;
        font-size: 12.5px;
        font-weight: 600;
        color: #6b4b45;
        white-space: nowrap;
    }

    .guru-pengumpulan td {
        padding: 12px 14px;
        border-bottom: 1px solid #f2e9e5;
        font-size: 13.5px;
        vertical-align: top;
    }

    .guru-pengumpulan tr:last-child td {
        border-bottom: none;
    }

    .guru-pengumpulan .nama {
        font-weight: 600;
        color: #1f1f1f;
    }

    .guru-pengumpulan .meta {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        color: #7a7a7a;
    }

    .guru-pengumpulan .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
    }

    .guru-pengumpulan .badge-dinilai {
        background: #dcfce7;
        color: #166534;
    }

    .guru-pengumpulan .badge-terkumpul {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .guru-pengumpulan .badge-terlambat {
        background: #fee2e2;
        color: #b91c1c;
    }

    .guru-pengumpulan .badge-belum {
        background: #f1f5f9;
        color: #475569;
    }

    .guru-pengumpulan .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 13px;
        border: 1px solid var(--maroon);
        border-radius: 8px;
        background: #fff;
        color: var(--maroon);
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .guru-pengumpulan .btn:hover {
        background: var(--maroon);
        color: #fff;
    }

    .guru-pengumpulan .btn-primary {
        background: var(--maroon);
        color: #fff;
    }

    .guru-pengumpulan .nilai-form {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 220px;
    }

    .guru-pengumpulan .nilai-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .guru-pengumpulan .komentar-input {
        width: 100%;
        min-width: 200px;
    }

    .guru-pengumpulan .riwayat {
        margin-top: 6px;
        font-size: 12px;
        color: #6b6b6b;
    }

    .guru-pengumpulan .riwayat summary {
        cursor: pointer;
        color: var(--maroon);
        font-weight: 600;
    }

    .guru-pengumpulan .riwayat ul {
        margin: 6px 0 0;
        padding-left: 16px;
    }

    .guru-pengumpulan .empty {
        padding: 32px 18px;
        text-align: center;
        color: #7a7a7a;
        font-size: 14px;
    }

    @media (max-width: 900px) {
        .guru-pengumpulan .info-card {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 520px) {
        .guru-pengumpulan .info-card {
            grid-template-columns: 1fr;
        }

        .guru-pengumpulan h1 {
            font-size: 20px;
        }
    }
</style>

<div class="guru-pengumpulan">
    <a href="{{ route('guru.tugas.index') }}" class="back-link">&larr; Kembali ke Daftar Tugas</a>

    <h1>Pengumpulan: {{ $tugas->judul }}</h1>
    <p class="page-subtitle">
        {{ $tugas->namaMataPelajaran() }} &middot; {{ $tugas->kelompok }} &middot;
        Batas pengumpulan {{ $tugas->batasTeks() }}
    </p>

    @if (session('success'))
        <div class="alert" style="margin-top:16px;">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert" style="margin-top:16px;border-color:#fecaca;background:#fef2f2;color:#991b1b;">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="info-card">
        <div>
            <div class="info-label">Mahasiswa Ditugaskan</div>
            <div class="info-value">{{ $statistik['mahasiswa'] }}</div>
        </div>
        <div>
            <div class="info-label">Sudah Mengumpulkan</div>
            <div class="info-value">{{ $statistik['terkumpul'] }}</div>
        </div>
        <div>
            <div class="info-label">Belum Mengumpulkan</div>
            <div class="info-value">{{ $statistik['belum'] }}</div>
        </div>
        <div>
            <div class="info-label">Sudah Dinilai</div>
            <div class="info-value">{{ $statistik['dinilai'] }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('guru.pengumpulan.index', $tugas) }}" class="filter-card">
        <input
            type="text"
            name="cari"
            value="{{ $filter['cari'] ?? '' }}"
            placeholder="Cari nama atau NIM mahasiswa"
            autocomplete="off"
        >

        <select name="status">
            <option value="">Semua status</option>
            @foreach ($statusPilihan as $nilai => $label)
                <option value="{{ $nilai }}" @selected(($filter['status'] ?? '') === $nilai)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn">Terapkan</button>
        <a href="{{ route('guru.pengumpulan.index', $tugas) }}" class="btn">Reset</a>
    </form>

    <div class="table-card">
        @if ($baris->isEmpty())
            <div class="empty">Belum ada mahasiswa pada kelompok ini.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Waktu Pengumpulan</th>
                        <th>Status</th>
                        <th>Berkas</th>
                        <th>Penilaian (0–100)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($baris as $item)
                        @php
                            $pengumpulan = $item['pengumpulan'];
                            $status = $item['status'];
                        @endphp
                        <tr>
                            <td>
                                <span class="nama">{{ $item['mahasiswa']->name }}</span>
                                <span class="meta">{{ $item['mahasiswa']->nim ?? '-' }}</span>
                            </td>
                            <td>
                                {{ $pengumpulan?->dikumpulkanTeks() ?? '-' }}
                                @if ($pengumpulan?->terlambat())
                                    <span class="meta" style="color:#b91c1c;">Melewati batas waktu</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $status['kelas'] }}">{{ $status['label'] }}</span>
                            </td>
                            <td>
                                @if ($pengumpulan)
                                    <a
                                        href="{{ route('guru.pengumpulan.berkas', [$tugas, $pengumpulan]) }}"
                                        class="btn"
                                    >Unduh</a>
                                    <span class="meta">{{ $pengumpulan->file_nama }}</span>
                                @else
                                    <span class="meta" style="margin-top:0;">Belum ada berkas</span>
                                @endif
                            </td>
                            <td>
                                @if ($pengumpulan)
                                    <form
                                        method="POST"
                                        action="{{ route('guru.pengumpulan.nilai', [$tugas, $pengumpulan]) }}"
                                        class="nilai-form"
                                    >
                                        @csrf
                                        <div class="nilai-row">
                                            <input
                                                type="number"
                                                name="nilai"
                                                min="0"
                                                max="100"
                                                step="1"
                                                value="{{ old('nilai', $pengumpulan->nilai) }}"
                                                placeholder="0-100"
                                                required
                                            >
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                        <input
                                            type="text"
                                            name="komentar"
                                            class="komentar-input"
                                            value="{{ old('komentar', $pengumpulan->komentar_guru) }}"
                                            placeholder="Komentar / umpan balik"
                                            maxlength="2000"
                                        >
                                    </form>

                                    @if ($pengumpulan->nilai !== null)
                                        <form
                                            method="POST"
                                            action="{{ route('guru.pengumpulan.publikasi', [$tugas, $pengumpulan]) }}"
                                            style="margin-top:6px;"
                                        >
                                            @csrf
                                            @if ($pengumpulan->nilaiDipublikasikan())
                                                <span class="meta" style="margin-top:0;color:#166534;">
                                                    Nilai sudah dipublikasikan
                                                </span>
                                            @else
                                                <button type="submit" class="btn">Publikasikan Nilai</button>
                                            @endif
                                        </form>
                                    @endif

                                    @if ($pengumpulan->riwayatNilai->isNotEmpty())
                                        <details class="riwayat">
                                            <summary>Riwayat nilai ({{ $pengumpulan->riwayatNilai->count() }})</summary>
                                            <ul>
                                                @foreach ($pengumpulan->riwayatNilai as $riwayat)
                                                    <li>
                                                        {{ $riwayat->created_at->locale('id')->translatedFormat('d/m/Y H:i') }}:
                                                        {{ $riwayat->nilai_lama ?? '—' }} &rarr; {{ $riwayat->nilai_baru ?? '—' }}
                                                        ({{ $riwayat->nama_pengubah ?? 'Guru' }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </details>
                                    @endif
                                @else
                                    <span class="meta" style="margin-top:0;">
                                        Nilai hanya dapat diberikan setelah mahasiswa mengumpulkan tugas.
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection
