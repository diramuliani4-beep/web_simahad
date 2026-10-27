
@extends('layouts.musyrifah')

@section('title', 'Dashboard Musyrifah')

@section('content')

<style>
    .welcome {
        margin: 0 20px 120px;
    }

    .welcome h1 {
        font-size: 30px;
        font-weight: 700;
        color: #080808;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px 35px;
        margin: 0 20px 120px;
    }

    .stat-card {
        min-height: 190px;
        padding: 25px 15px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        background: #fff;
        border: 1px solid #8b1027;
        border-radius: 12px;
        box-shadow: 0 3px 5px rgba(0, 0, 0, 0.18);
    }

    .stat-card h2 {
        margin-bottom: 5px;
        color: #800d22;
        font-size: 24px;
        font-weight: 500;
    }

    .stat-card p {
        color: #800d22;
        font-size: 24px;
        line-height: 1.5;
    }

    .activity-section {
        margin: 0 35px;
        border: 1px solid #bbb;
        background: #fff;
    }

    .activity-title {
        padding: 7px 12px;
        text-align: center;
        color: #fff;
        background: #590018;
        font-size: 23px;
        font-weight: bold;
    }

    .activity-row {
        padding: 8px 35px;
        font-size: 18px;
        border-bottom: 1px solid #bbb;
    }

    .activity-row:nth-child(even) {
        background: #f3e5e8;
    }

    .activity-row:last-child {
        border-bottom: none;
    }

    @media (max-width: 1100px) {
        .welcome {
            margin-bottom: 50px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-bottom: 70px;
            gap: 15px;
        }

        .activity-section {
            margin: 0 20px;
        }
    }

    @media (max-width: 768px) {
        .welcome {
            margin: 0 5px 35px;
        }

        .welcome h1 {
            font-size: 23px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin: 0 5px 40px;
            gap: 15px;
        }

        .stat-card {
            min-height: 145px;
            padding: 18px 8px;
        }

        .stat-card h2,
        .stat-card p {
            font-size: 19px;
        }

        .activity-section {
            margin: 0 5px;
        }

        .activity-title {
            font-size: 19px;
        }

        .activity-row {
            padding: 10px 12px;
            font-size: 14px;
        }
    }

    @media (max-width: 420px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="welcome">
    <h1>← Selamat Datang, Pengelola</h1>
</section>

<section class="stats-grid">

    <article class="stat-card">
        <h2>Pendaftar</h2>
        <p>25</p>
    </article>

    <article class="stat-card">
        <h2>Penghuni</h2>
        <p>120</p>
    </article>

    <article class="stat-card">
        <h2>Kamar</h2>
        <p>30</p>
    </article>

    <article class="stat-card">
        <h2>Belum Lunas</h2>
        <p>15</p>
    </article>

    <article class="stat-card">
        <h2>Pengajuan Izin</h2>
        <p>8</p>
    </article>

</section>

<section class="activity-section">

    <div class="activity-title">
        Aktivitas Terbaru
    </div>

    <div class="activity-row">
        Ahmad mengajukan booking kamar
    </div>

    <div class="activity-row">
        Siti mengirim bukti pembayaran
    </div>

    <div class="activity-row">
        Siti mengirim bukti pembayaran
    </div>

    <div class="activity-row">
        Budi mengajukan izin
    </div>

</section>

@endsection
