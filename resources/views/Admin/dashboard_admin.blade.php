
@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

<style>
    .dashboard-admin {
        padding: 25px 20px;
        color: #333;
    }

    /* Sapaan admin */
    .welcome-admin {
        margin-bottom: 25px;
    }

    .welcome-admin h2 {
        font-size: 22px;
        font-weight: 600;
        color: #171717;
        margin-bottom: 8px;
    }

    .welcome-admin p {
        font-size: 13px;
        color: #555;
        margin: 0;
    }

    /* Statistik */
    .statistik-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 10px;
    }

    .statistik-card {
        background: #fff;
        border: 1px solid #eee5e5;
        border-radius: 9px;
        padding: 12px;
        min-height: 80px;
    }

    .statistik-icon {
        color: #850027;
        font-size: 15px;
        margin-bottom: 5px;
    }

    .statistik-card h3 {
        font-size: 20px;
        color: #850027;
        font-weight: 600;
        margin: 0 0 4px;
    }

    .statistik-card p {
        color: #888;
        font-size: 11px;
        margin: 0;
    }

    /* Aktivitas terbaru */
    .aktivitas-box {
        background: #fff;
        border: 1px solid #eee5e5;
        border-radius: 9px;
        padding: 14px;
        margin-top: 8px;
    }

    .aktivitas-box h3 {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 12px;
        color: #444;
    }

    .aktivitas-item {
        display: flex;
        gap: 10px;
        margin-bottom: 13px;
        align-items: flex-start;
    }

    .aktivitas-item:last-child {
        margin-bottom: 0;
    }

    .aktivitas-icon {
        color: #850027;
        font-size: 13px;
        padding-top: 2px;
    }

    .aktivitas-detail {
        flex: 1;
    }

    .aktivitas-detail h4 {
        font-size: 12px;
        font-weight: 600;
        color: #444;
        margin: 0 0 3px;
    }

    .aktivitas-detail p {
        font-size: 10px;
        color: #999;
        margin: 0;
    }

    .aktivitas-kosong {
        font-size: 12px;
        color: #999;
        padding: 10px 0;
    }

    /* Tampilan responsif */
    @media (max-width: 600px) {
        .dashboard-admin {
            padding: 18px 12px;
        }

        .statistik-grid {
            grid-template-columns: 1fr;
        }

        .welcome-admin h2 {
            font-size: 18px;
        }
    }
</style>

<div class="dashboard-admin">

    {{-- Sapaan Admin --}}
    <div class="welcome-admin">
        <h2>
            Assalamu’alaikum, {{ $user->name }}! 👋
        </h2>

        <p>Selamat datang kembali, Admin!</p>
    </div>

    {{-- Statistik Pengguna --}}
    <div class="statistik-grid">

        <div class="statistik-card">
            <div class="statistik-icon">
                <i class="fas fa-users"></i>
            </div>

            <h3>{{ $totalPengguna }}</h3>
            <p>Total Pengguna</p>
        </div>

        <div class="statistik-card">
            <div class="statistik-icon">
                <i class="fas fa-user-check"></i>
            </div>

            <h3>{{ $akunAktif }}</h3>
            <p>Akun Aktif</p>
        </div>

        <div class="statistik-card">
            <div class="statistik-icon">
                <i class="fas fa-user-clock"></i>
            </div>

            <h3>{{ $menungguAktivasi }}</h3>
            <p>Menunggu Aktivasi</p>
        </div>

        <div class="statistik-card">
            <div class="statistik-icon">
                <i class="fas fa-user-times"></i>
            </div>

            <h3>{{ $akunNonaktif }}</h3>
            <p>Akun Nonaktif</p>
        </div>

    </div>

    {{-- Aktivitas Terbaru --}}
    <div class="aktivitas-box">

        <h3>Aktivitas Terbaru</h3>

        @forelse ($aktivitasTerbaru as $aktivitas)

            <div class="aktivitas-item">

                <div class="aktivitas-icon">
                    <i class="fas fa-history"></i>
                </div>

                <div class="aktivitas-detail">

                    <h4>
                        {{ $aktivitas->description }}
                    </h4>

                    <p>
                        {{ $aktivitas->user_name ?? 'Pengguna' }}
                        ·
                        {{ $aktivitas->created_at->translatedFormat('d F Y H:i') }}
                    </p>

                </div>

            </div>

        @empty

            <div class="aktivitas-kosong">
                Belum ada aktivitas yang tercatat.
            </div>

        @endforelse

    </div>

</div>

@endsection
