
@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<style>
    .user-management {
        font-family: 'Poppins', sans-serif;
        color: #111;
    }

    .user-management * {
        box-sizing: border-box;
    }

    .user-heading {
        padding: 0 0 28px;
        border-bottom: 1px solid #780d20;
        margin: 0 -2.5% 26px;
        padding-left: 2.5%;
        padding-right: 2.5%;
    }

    .user-breadcrumb {
        color: #780d20;
        font-size: 21px;
        font-weight: 500;
        margin-bottom: 10px;
    }

    .user-heading h1 {
        font-size: 31px;
        font-weight: 600;
        margin: 0;
        line-height: 1.3;
    }

    .user-heading p {
        font-size: 18px;
        margin: 0;
    }

    .user-section-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin: 0 0 30px;
    }

    .user-section-heading h2 {
        font-size: 22px;
        font-weight: 600;
        margin: 0;
    }

    .add-user-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 15px 18px;
        background: #780d20;
        color: #fff;
        border: 0;
        border-radius: 10px;
        text-decoration: none;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .add-user-button:hover {
        background: #650919;
        color: #fff;
    }

    .user-search {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
        padding: 0 13px;
        margin: 0 7px 12px;
        min-height: 54px;
    }

    .user-search svg {
        width: 19px;
        height: 19px;
        color: #888;
        flex-shrink: 0;
    }

    .user-search input {
        width: 100%;
        border: 0;
        outline: none;
        background: transparent;
        font-family: inherit;
        font-size: 15px;
        color: #292929;
    }

    .user-filters {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin: 0 7px 15px;
    }

    .user-filter {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        background: #eaeaea;
        color: #333;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
    }

    .user-filter.active {
        background: #f5e6e9;
        color: #780d20;
        font-weight: 600;
    }

    .user-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 0 7px;
    }

    .user-card {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 10px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 12px;
        min-height: 130px;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #f8ecef;
        color: #780d20;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-avatar svg {
        width: 24px;
        height: 24px;
    }

    .user-information {
        min-width: 0;
        padding-top: 4px;
        padding-right: 25px;
    }

    .user-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 7px;
        overflow-wrap: anywhere;
    }

    .user-email,
    .user-role {
        color: #777;
        font-size: 13px;
        margin-bottom: 5px;
        overflow-wrap: anywhere;
    }

    .user-status {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        background: #d8f5e2;
        color: #159447;
    }

    .user-status.nonaktif {
        background: #eee;
        color: #666;
    }

    .user-status.menunggu {
        background: #ffeadb;
        color: #d65b17;
    }

    .user-empty {
        padding: 30px 15px;
        text-align: center;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 12px;
        color: #777;
    }

    .user-pagination {
        margin: 20px 7px;
    }

    @media (max-width: 700px) {
        .user-heading {
            margin-left: 0;
            margin-right: 0;
            padding-left: 0;
            padding-right: 0;
        }

        .user-breadcrumb {
            font-size: 16px;
        }

        .user-heading h1 {
            font-size: 24px;
        }

        .user-heading p {
            font-size: 14px;
        }

        .user-section-heading {
            align-items: flex-start;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .user-section-heading h2 {
            font-size: 19px;
        }

        .add-user-button {
            padding: 12px 14px;
        }

        .user-search,
        .user-filters,
        .user-list {
            margin-left: 0;
            margin-right: 0;
            padding-left: 0;
            padding-right: 0;
        }

        .user-card {
            gap: 9px;
        }

        .user-name {
            font-size: 16px;
        }
    }
</style>

<div class="user-management">
    <section class="user-heading">
        <div class="user-breadcrumb">Dashboard / Manajemen Pengguna</div>
        <h1>Manajemen Pengguna</h1>
        <p>Kelola akun dan status pengguna SIMAHAD.</p>
    </section>

    <section class="user-section-heading">
        <h2>Daftar Pengguna</h2>

        {{-- Sesuaikan tujuan tombol ini jika halaman tambah pengguna sudah tersedia --}}
        <a href="{{ url('/admin/manajemen-pengguna/tambah') }}"
           class="add-user-button">
            <span>+</span> Tambah Pengguna
        </a>
    </section>

    <form method="GET" action="{{ route('admin.manajemen-pengguna') }}"
          class="user-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2.2" stroke-linecap="round">
            <circle cx="10.8" cy="10.8" r="6.8"/>
            <path d="m16 16 5 5"/>
        </svg>

        <input type="text" name="search"
               placeholder="Cari nama atau email..."
               value="{{ request('search') }}">
    </form>

    <div class="user-filters">
        <a href="{{ route('admin.manajemen-pengguna', ['search' => request('search')]) }}"
           class="user-filter {{ !request('status') ? 'active' : '' }}">
            Semua
        </a>

        <a href="{{ route('admin.manajemen-pengguna', ['search' => request('search'), 'status' => 'aktif']) }}"
           class="user-filter {{ request('status') === 'aktif' ? 'active' : '' }}">
            Aktif
        </a>

        <a href="{{ route('admin.manajemen-pengguna', ['search' => request('search'), 'status' => 'nonaktif']) }}"
           class="user-filter {{ request('status') === 'nonaktif' ? 'active' : '' }}">
            Nonaktif
        </a>
    </div>

    <section class="user-list">
        @forelse ($pengguna as $user)
            @php
                $statusUser = strtolower((string) data_get($user, 'status', 'aktif'));

                if (in_array($statusUser, ['nonaktif', 'non-aktif', 'inactive'])) {
                    $labelStatus = 'Nonaktif';
                    $classStatus = 'nonaktif';
                } elseif (in_array($statusUser, ['menunggu', 'pending', 'menunggu aktivasi'])) {
                    $labelStatus = 'Menunggu Aktivasi';
                    $classStatus = 'menunggu';
                } else {
                    $labelStatus = 'Aktif';
                    $classStatus = '';
                }

                $peran = data_get($user, 'role')
                    ?? data_get($user, 'peran')
                    ?? data_get($user, 'user_role')
                    ?? 'Pengguna';
            @endphp

            <article class="user-card">
                <div class="user-avatar">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                    </svg>
                </div>

                <div class="user-information">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                    <div class="user-role">Peran: {{ $peran }}</div>
                    <span class="user-status {{ $classStatus }}">
                        {{ $labelStatus }}
                    </span>
                </div>
            </article>
        @empty
            <div class="user-empty">
                Tidak ada pengguna yang sesuai dengan pencarian.
            </div>
        @endforelse
    </section>

    <div class="user-pagination">
        {{ $pengguna->links() }}
    </div>
</div>
@endsection