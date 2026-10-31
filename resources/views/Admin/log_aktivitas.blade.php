
@extends('layouts.admin')

@section('content')

<style>
    .log-page {
        padding: 20px 25px;
        color: #222;
    }

    .log-page h2 {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .log-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        background: #fff;
        padding: 20px 15px;
        margin-bottom: 20px;
    }

    .summary-item {
        padding: 5px;
    }

    .summary-item i {
        color: #90002d;
        font-size: 17px;
        margin-bottom: 10px;
    }

    .summary-item .label {
        display: block;
        color: #777;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .summary-item h3 {
        font-size: 22px;
        margin: 0;
    }

    .filter-title {
        font-size: 17px;
        font-weight: 600;
        margin: 20px 5px 12px;
    }

    .log-filter label {
        display: block;
        margin: 0 15px 7px;
        font-size: 14px;
    }

    .log-filter select {
        width: 100%;
        height: 40px;
        border: 1px solid #aaa;
        border-radius: 14px;
        background: #f4e6e9;
        padding: 0 15px;
        margin-bottom: 18px;
        box-shadow: 0 2px 3px rgba(0,0,0,.15);
    }

    .btn-marun {
        background: #90002d;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 9px 25px;
    }

    .btn-marun:hover {
        background: #700020;
        color: white;
    }

    .log-table-wrapper {
        overflow-x: auto;
        margin-top: 25px;
        background: white;
        padding: 15px;
        border-radius: 8px;
    }

    .log-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .log-table th,
    .log-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    .log-table th {
        background: #f4e6e9;
        color: #700020;
    }

    .log-table tr:hover {
        background: #fafafa;
    }

    .log-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 12px;
        background: #f4e6e9;
        color: #800020;
        font-size: 12px;
    }

    .log-empty {
        text-align: center !important;
        padding: 25px !important;
        color: #888;
    }

    @media(max-width: 650px) {
        .log-summary {
            grid-template-columns: 1fr;
        }

        .log-page {
            padding: 15px 10px;
        }
    }
</style>

<div class="log-page">

    <h2>Log Aktivitas</h2>

    {{-- Ringkasan Aktivitas --}}
    <div class="log-summary">

        <div class="summary-item">
            <i class="fas fa-chart-line"></i>
            <span class="label">Total Aktivitas</span>
            <h3>{{ number_format($totalAktivitas) }}</h3>
        </div>

        <div class="summary-item">
            <i class="fas fa-users"></i>
            <span class="label">Pengguna Aktif</span>
            <h3>{{ number_format($penggunaAktif) }}</h3>
        </div>

        <div class="summary-item">
            <i class="far fa-calendar-alt"></i>
            <span class="label">Hari Ini</span>
            <h3>{{ number_format($aktivitasHariIni) }}</h3>
        </div>

    </div>

    {{-- Filter Aktivitas --}}
    <h4 class="filter-title">Filter Aktivitas</h4>

    <form action="{{ route('admin.log-aktivitas') }}"
          method="GET"
          class="log-filter">

        <div>
            <label>Periode</label>
            <select name="periode">
                <option value="">Semua Periode</option>
                <option value="hari_ini"
                    {{ request('periode') == 'hari_ini' ? 'selected' : '' }}>
                    Hari Ini
                </option>
                <option value="7_hari"
                    {{ request('periode') == '7_hari' ? 'selected' : '' }}>
                    7 Hari Terakhir
                </option>
                <option value="30_hari"
                    {{ request('periode') == '30_hari' ? 'selected' : '' }}>
                    30 Hari Terakhir
                </option>
            </select>
        </div>

        <div>
            <label>Peran Pengguna</label>
            <select name="role">
                <option value="">Semua Peran</option>
                @foreach (['admin', 'mahasiswa', 'guru', 'musyrifah', 'pimpinan'] as $role)
                    <option value="{{ $role }}"
                        {{ request('role') == $role ? 'selected' : '' }}>
                        {{ ucfirst($role) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Jenis Aktivitas</label>
            <select name="aktivitas">
                <option value="">Semua Aktivitas</option>
                <option value="login" {{ request('aktivitas') == 'login' ? 'selected' : '' }}>Login</option>
                <option value="logout" {{ request('aktivitas') == 'logout' ? 'selected' : '' }}>Logout</option>
                <option value="create_user" {{ request('aktivitas') == 'create_user' ? 'selected' : '' }}>Tambah Pengguna</option>
                <option value="update_user" {{ request('aktivitas') == 'update_user' ? 'selected' : '' }}>Ubah Pengguna</option>
                <option value="delete_user" {{ request('aktivitas') == 'delete_user' ? 'selected' : '' }}>Hapus Pengguna</option>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-marun">
                Terapkan Filter
            </button>
        </div>

    </form>

    {{-- Tabel Riwayat --}}
    <div class="log-table-wrapper">

        <h5 class="mb-3">Riwayat Aktivitas</h5>

        <table class="log-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pengguna</th>
                    <th>Peran</th>
                    <th>Jenis Aktivitas</th>
                    <th>Keterangan</th>
                    <th>Waktu</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($logs as $index => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $index }}</td>
                        <td>
                            {{ $log->user_name ?? 'Pengguna tidak diketahui' }}
                            <br>
                            <small>{{ $log->user_email }}</small>
                        </td>
                        <td>{{ ucfirst($log->user_role ?? '-') }}</td>
                        <td>
                            <span class="log-badge">
                                {{ ucwords(str_replace('_', ' ', $log->activity)) }}
                            </span>
                        </td>
                        <td>{{ $log->description ?? '-' }}</td>
                        <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="log-empty">
                            Belum ada aktivitas yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>

    </div>

</div>

@endsection
