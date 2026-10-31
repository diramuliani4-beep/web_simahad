
@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<style>
    .user-management {
        width: 100%;
        max-width: 100%;
        color: #243247;
    }

    /* HEADER HALAMAN */
    .user-heading {
        margin-bottom: 26px;
    }

    .user-breadcrumb {
        margin-bottom: 12px;
        color: #8792a2;
        font-size: 13px;
    }

    .user-heading h1 {
        margin: 0 0 8px;
        color: #1e293b;
        font-size: 28px;
        font-weight: 700;
    }

    .user-heading p {
        margin: 0;
        color: #718096;
        font-size: 14px;
        line-height: 1.6;
    }

    /* ALERT */
    .user-alert {
        padding: 13px 16px;
        margin-bottom: 18px;
        border-radius: 9px;
        font-size: 14px;
    }

    .user-alert-success {
        color: #166534;
        background: #dcfce7;
    }

    .user-alert-error {
        color: #991b1b;
        background: #fee2e2;
    }

    /* PANEL */
    .user-panel {
        overflow: hidden;
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(31, 45, 61, .04);
    }

    .user-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px;
        border-bottom: 1px solid #edf0f5;
    }

    .user-panel-header h2 {
        margin: 0 0 5px;
        color: #243247;
        font-size: 18px;
        font-weight: 700;
    }

    .user-panel-header p {
        margin: 0;
        color: #8a94a4;
        font-size: 13px;
    }

    .user-total {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        color: #1769aa;
        background: #eaf4ff;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
    }

    /* TOMBOL TAMBAH PENGGUNA */
    .user-panel-header .btn-tambah-pengguna {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 18px !important;
        color: #fff !important;
        background: #2563eb !important;
        border: 1px solid #2563eb !important;
        border-radius: 8px !important;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none !important;
        cursor: pointer;
        white-space: nowrap;
        box-sizing: border-box;
        transition: background-color .2s ease;
    }

    .user-panel-header .btn-tambah-pengguna:hover {
        color: #fff !important;
        background: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
    }

    .user-panel-header .btn-tambah-pengguna svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        stroke: #fff;
    }

    /* TOOLBAR */
    .user-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        padding: 18px 24px;
        background: #fff;
    }

    .user-search {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        max-width: 380px;
        margin: 0;
    }

    .user-search input {
        flex: 1;
        width: 100%;
        min-width: 0;
        height: 40px;
        padding: 0 13px;
        color: #334155;
        background: #fff;
        border: 1px solid #dce3ec;
        border-radius: 8px;
        outline: none;
        font-size: 13px;
        transition: border-color .2s, box-shadow .2s;
    }

    .user-search input:focus {
        border-color: #3992d6;
        box-shadow: 0 0 0 3px rgba(57, 146, 214, .12);
    }

    .user-search button {
        height: 40px;
        padding: 0 17px;
        color: #fff;
        background: #2185c5;
        border: 0;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
    }

    .user-search button:hover {
        background: #176da8;
    }

    /* TOTAL PENGGUNA */
    .user-total-box {
        display: flex;
        align-items: center;
        gap: 9px;
        color: #64748b;
        font-size: 12px;
    }

    .user-total {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        color: #1769aa;
        background: #eaf4ff;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
    }

    /* FILTER */
    .user-filters {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .user-filter {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 7px 12px;
        color: #64748b;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        transition: .2s;
    }

    .user-filter:hover {
        color: #1769aa;
        border-color: #91c7ee;
    }

    .user-filter.active {
        color: #1769aa;
        background: #eaf4ff;
        border-color: #c6e3fb;
        font-weight: 700;
    }

    /* TABEL */
    .user-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border-top: 1px solid #edf0f5;
    }

    .user-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        text-align: left;
    }

    .user-table thead th {
        padding: 14px 20px;
        color: #7b8798;
        background: #f8fafc;
        border-bottom: 1px solid #e9eef5;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .user-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #edf1f6;
        vertical-align: middle;
        font-size: 13px;
    }

    .user-table tbody tr:last-child td {
        border-bottom: none;
    }

    .user-table tbody tr {
        transition: background .15s;
    }

    .user-table tbody tr:hover {
        background: #f8fbff;
    }

    /* PROFIL PENGGUNA */
    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 220px;
    }

    .user-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        color: #237ab5;
        background: #eaf4ff;
        border-radius: 12px;
    }

    .user-avatar svg {
        width: 21px;
        height: 21px;
    }

    .user-name {
        margin-bottom: 5px;
        color: #263449;
        font-size: 13px;
        font-weight: 700;
    }

    .user-email {
        color: #8290a3;
        font-size: 12px;
        overflow-wrap: anywhere;
    }

    /* ROLE */
    .user-role {
        display: inline-block;
        padding: 6px 10px;
        color: #475569;
        background: #f1f5f9;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        white-space: nowrap;
    }

    /* STATUS */
    .user-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        color: #9a6700;
        background: #fff5d6;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .user-status::before {
        width: 6px;
        height: 6px;
        background: currentColor;
        border-radius: 50%;
        content: "";
    }

    .user-status.aktif {
        color: #16803d;
        background: #dcfce7;
    }

    .user-status.nonaktif {
        color: #b42332;
        background: #fee2e2;
    }

    .user-status.menunggu {
        color: #9a6700;
        background: #fff5d6;
    }

    /* AKSI */
    .user-actions {
        display: flex;
        align-items: center;
        gap: 7px;
        white-space: nowrap;
    }

    .user-actions form {
        display: inline-flex;
        margin: 0;
    }

    .user-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 8px 11px;
        border: 0;
        border-radius: 7px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
        transition: background .2s;
    }

    .user-btn-verify {
        color: #fff;
        background: #198754;
    }

    .user-btn-verify:hover {
        background: #146c43;
    }

    .user-btn-reject {
        color: #b42332;
        background: #fff;
        border: 1px solid #f1c4c8;
    }

    .user-btn-reject:hover {
        background: #fff0f1;
    }

    .user-verified-note {
        color: #16803d;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* DATA KOSONG */
    .user-empty {
        padding: 42px 20px;
        color: #8792a2;
        text-align: center;
        font-size: 14px;
    }

    /* PAGINATION */
    .user-pagination {
        display: flex;
        justify-content: flex-end;
        padding: 18px 24px;
        border-top: 1px solid #edf0f5;
    }

    .user-pagination nav p {
        margin-bottom: 10px;
        color: #718096;
        font-size: 12px;
    }

    .user-pagination nav a,
    .user-pagination nav span {
        font-size: 12px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .user-heading h1 {
            font-size: 23px;
        }

        .user-panel-header,
        .user-toolbar {
            padding: 16px;
        }

        .user-panel-header {
            align-items: flex-start;
        }

        .user-toolbar {
            align-items: stretch;
        }

        .user-search {
            max-width: none;
        }

        .user-filters {
            gap: 6px;
        }

        .user-filter {
            padding: 7px 9px;
            font-size: 11px;
        }

        .user-pagination {
            justify-content: center;
            padding: 16px 10px;
            overflow-x: auto;
        }
    }

    @media (max-width: 480px) {
        .user-panel-header {
            flex-direction: column;
            align-items: stretch;
        }

        .user-panel-header .btn-tambah-pengguna {
            align-self: flex-start;
        }

        .user-search {
            flex-wrap: wrap;
        }

        .user-total-box {
            align-self: flex-start;
        }
    }
</style>

<div class="user-management">

    {{-- JUDUL HALAMAN --}}
    <section class="user-heading">
        <div class="user-breadcrumb">
            Dashboard / Manajemen Pengguna
        </div>

        <h1>Manajemen Pengguna</h1>

        <p>
            Verifikasi akun pendaftar dan atur status pengguna SIMAHAD.
        </p>
    </section>

    {{-- PESAN NOTIFIKASI --}}
    @if (session('success'))
        <div class="user-alert user-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="user-alert user-alert-error">
            {{ session('error') }}
        </div>
    @endif

    @php
        $jumlahPengguna = $pengguna->total();
    @endphp

    <section class="user-panel">

        {{-- HEADER PANEL --}}
        <div class="user-panel-header">
            <div>
                <h2>Daftar Pengguna</h2>
                <p>Kelola akun dan verifikasi pendaftaran pengguna.</p>
            </div>

            <a href="{{ route('admin.manajemen-pengguna.create') }}"
               class="btn-tambah-pengguna">

                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <circle cx="9" cy="8" r="4"/>
                    <path d="M17 11v6"/>
                    <path d="M14 14h6"/>
                    <path d="M3 21v-2a6 6 0 0 1 12 0v2"/>
                </svg>

                <span>Tambah Pengguna</span>
            </a>
        </div>

        {{-- PENCARIAN, TOTAL, DAN FILTER --}}
        <div class="user-toolbar">

            <form method="GET"
                  action="{{ route('admin.manajemen-pengguna') }}"
                  class="user-search">

                <input
                    type="text"
                    name="search"
                    placeholder="Cari nama atau email..."
                    value="{{ request('search') }}"
                    aria-label="Cari nama atau email pengguna"
                >

                @if (request('status'))
                    <input type="hidden"
                           name="status"
                           value="{{ request('status') }}">
                @endif

                <button type="submit">Cari</button>
            </form>

            <div class="user-total-box">
                <span class="user-total">
                    {{ $jumlahPengguna }}
                </span>
                <span>Total Pengguna</span>
            </div>

            <div class="user-filters">
                @foreach ([
                    '' => 'Semua',
                    'menunggu' => 'Menunggu Verifikasi',
                    'aktif' => 'Aktif',
                    'nonaktif' => 'Nonaktif',
                ] as $nilai => $label)

                    <a
                        href="{{ route('admin.manajemen-pengguna', array_filter([
                            'search' => request('search'),
                            'status' => $nilai,
                        ], fn ($value) => $value !== '')) }}"
                        class="user-filter {{ request('status', '') === $nilai ? 'active' : '' }}"
                    >
                        {{ $label }}
                    </a>

                @endforeach
            </div>
        </div>

        {{-- TABEL PENGGUNA --}}
        <div class="user-table-wrapper">
            <table class="user-table">

                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengguna as $user)

                        @php
                            $statusUser = strtolower(
                                (string) ($user->status ?? 'menunggu')
                            );

                            if (in_array($statusUser, ['aktif', 'active', 'verified'])) {
                                $labelStatus = 'Aktif';
                                $classStatus = 'aktif';
                            } elseif (in_array($statusUser, ['nonaktif', 'non-aktif', 'inactive', 'ditolak'])) {
                                $labelStatus = 'Nonaktif';
                                $classStatus = 'nonaktif';
                            } else {
                                $labelStatus = 'Menunggu Verifikasi';
                                $classStatus = 'menunggu';
                            }

                            $peran = $user->role ?? $user->peran ?? 'Belum ditentukan';
                            $nama = $user->name ?? $user->username ?? 'Tanpa nama';
                        @endphp

                        <tr>
                            {{-- IDENTITAS PENGGUNA --}}
                            <td>
                                <div class="user-profile">

                                    <div class="user-avatar">
                                        <svg viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round"
                                             stroke-linejoin="round"
                                             aria-hidden="true">
                                            <circle cx="12" cy="8" r="4"/>
                                            <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                                        </svg>
                                    </div>

                                    <div>
                                        <div class="user-name">
                                            {{ $nama }}
                                        </div>

                                        <div class="user-email">
                                            {{ $user->email }}
                                        </div>
                                    </div>

                                </div>
                            </td>

                            {{-- ROLE --}}
                            <td>
                                <span class="user-role">
                                    {{ $peran }}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span class="user-status {{ $classStatus }}">
                                    {{ $labelStatus }}
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td>
                                <div class="user-actions">

                                    @if (in_array($statusUser, ['menunggu', 'pending']))

                                        <form method="POST"
                                              action="{{ route('admin.manajemen-pengguna.verifikasi', $user->id) }}"
                                              onsubmit="return confirm('Verifikasi dan aktifkan akun ini?')">

                                            @csrf

                                            <button type="submit"
                                                    class="user-btn user-btn-verify">
                                                Verifikasi
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('admin.manajemen-pengguna.tolak', $user->id) }}"
                                              onsubmit="return confirm('Tolak pendaftaran akun ini?')">

                                            @csrf

                                            <button type="submit"
                                                    class="user-btn user-btn-reject">
                                                Tolak
                                            </button>
                                        </form>

                                    @elseif (in_array($statusUser, ['aktif', 'active', 'verified']))

                                        <span class="user-verified-note">
                                            Sudah diverifikasi
                                        </span>

                                    @else

                                        <span style="color:#8792a2;font-size:12px;">
                                            Tidak ada aksi
                                        </span>

                                    @endif

                                </div>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">
                                <div class="user-empty">
                                    Tidak ada pengguna yang sesuai dengan pencarian atau filter.
                                </div>
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($pengguna->hasPages())
            <div class="user-pagination">
                {{ $pengguna->withQueryString()->links() }}
            </div>
        @endif

    </section>
</div>
@endsection
