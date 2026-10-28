
@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

<style>
    .admin-dashboard {
        font-family: 'Poppins', sans-serif;
        color: #292929;
    }

    .admin-dashboard * {
        box-sizing: border-box;
    }

    .admin-dashboard .welcome h1 {
        font-size: 30px;
        font-weight: 600;
        line-height: 1.5;
        color: #080808;
        margin: 0 0 12px;
    }

    .admin-dashboard .welcome p {
        margin: 0 0 42px;
        font-size: 20px;
        color: #111;
    }

    .admin-dashboard .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin: 0 0 15px;
        padding: 0 8px;
    }

    .admin-dashboard .stat-card {
        min-height: 140px;
        padding: 15px 16px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
    }

    .admin-dashboard .stat-icon {
        width: 25px;
        height: 25px;
        color: #780d20;
        margin-bottom: 8px;
    }

    .admin-dashboard .stat-number {
        font-size: 29px;
        line-height: 1.4;
        font-weight: 500;
        color: #780d20;
    }

    .admin-dashboard .stat-label {
        margin-top: 7px;
        font-size: 14px;
        color: #77716e;
    }

    .admin-dashboard .activity-card {
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
        padding: 17px 16px 8px;
        margin: 0 8px;
    }

    .admin-dashboard .activity-title {
        color: #292929;
        font-size: 19px;
        font-weight: 600;
        margin: 0 0 16px;
    }

    .admin-dashboard .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 0 0 14px;
        margin-bottom: 2px;
    }

    .admin-dashboard .activity-icon {
        width: 19px;
        height: 19px;
        flex-shrink: 0;
        color: #780d20;
        margin-top: 2px;
    }

    .admin-dashboard .activity-name {
        font-size: 15px;
        font-weight: 500;
        color: #333;
    }

    .admin-dashboard .activity-description {
        font-size: 13px;
        color: #77716e;
        margin: 5px 0 0;
    }

    @media (max-width: 700px) {
        .admin-dashboard .welcome h1 {
            font-size: 22px;
        }

        .admin-dashboard .welcome p {
            font-size: 14px;
            margin-bottom: 25px;
        }

        .admin-dashboard .stats-grid {
            gap: 10px;
            padding: 0;
        }

        .admin-dashboard .stat-card {
            min-height: 120px;
            padding: 12px;
        }

        .admin-dashboard .stat-number {
            font-size: 24px;
        }

        .admin-dashboard .stat-label {
            font-size: 12px;
        }

        .admin-dashboard .activity-card {
            margin: 0;
        }

        .admin-dashboard .activity-title {
            font-size: 17px;
        }

        .admin-dashboard .activity-name {
            font-size: 13px;
        }

        .admin-dashboard .activity-description {
            font-size: 12px;
        }
    }

    @media (max-width: 360px) {
        .admin-dashboard .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-dashboard">

    {{-- SAPAAN ADMIN --}}
    <section class="welcome">
        <h1>Assalamu’alaikum, Admin! 👋</h1>
        <p>Selamat datang kembali, Admin!</p>
    </section>

    {{-- STATISTIK ADMIN --}}
    <section class="stats-grid">

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="10" cy="7" r="4"/>
                <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>

            <div class="stat-number">
                {{ $totalPengguna ?? 25 }}
            </div>
            <div class="stat-label">Total Pengguna</div>
        </article>

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="8" r="4"/>
                <path d="M3 21v-2a6 6 0 0 1 12 0v2"/>
                <path d="m16 11 2 2 4-4"/>
            </svg>

            <div class="stat-number">
                {{ $akunAktif ?? 21 }}
            </div>
            <div class="stat-label">Akun Aktif</div>
        </article>

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="10" cy="7" r="4"/>
                <path d="M3 21v-2a7 7 0 0 1 11-5.7"/>
                <path d="M19 14v6"/>
                <path d="M16 17h6"/>
            </svg>

            <div class="stat-number">
                {{ $menungguAktivasi ?? 3 }}
            </div>
            <div class="stat-label">Menunggu Aktivasi</div>
        </article>

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="10" cy="7" r="4"/>
                <path d="M3 21v-2a7 7 0 0 1 11-5.7"/>
                <path d="m17 16 5 5"/>
                <path d="m22 16-5 5"/>
            </svg>

            <div class="stat-number">
                {{ $akunNonaktif ?? 1 }}
            </div>
            <div class="stat-label">Akun Nonaktif</div>
        </article>

    </section>

    {{-- AKTIVITAS TERBARU --}}
    <section class="activity-card">

        <h2 class="activity-title">Aktivitas Terbaru</h2>

        @php
            $aktivitasAdmin = $aktivitasTerbaru ?? [
                [
                    'judul' => 'Akun baru ditambahkan',
                    'deskripsi' => 'Nadia Putri · 10 Oktober 2026',
                ],
                [
                    'judul' => 'Hak akses diperbarui',
                    'deskripsi' => 'Admin mengubah peran pengguna · 10 Oktober 2026',
                ],
                [
                    'judul' => 'Pengaturan sistem diperbarui',
                    'deskripsi' => 'Perubahan konfigurasi · 9 Oktober 2026',
                ],
            ];
        @endphp

        @forelse ($aktivitasAdmin as $aktivitas)
            <div class="activity-item">

                <svg class="activity-icon" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 1 0 3-6.7"/>
                    <path d="M3 4v5h5"/>
                    <path d="M12 7v5l3 2"/>
                </svg>

                <div>
                    <div class="activity-name">
                        {{ data_get($aktivitas, 'judul', data_get($aktivitas, 'nama', 'Aktivitas Admin')) }}
                    </div>

                    <p class="activity-description">
                        {{ data_get($aktivitas, 'deskripsi', data_get($aktivitas, 'keterangan', 'Aktivitas sistem')) }}
                    </p>
                </div>

            </div>
        @empty
            <p class="activity-description">
                Belum ada aktivitas terbaru.
            </p>
        @endforelse

    </section>

</div>

@endsection