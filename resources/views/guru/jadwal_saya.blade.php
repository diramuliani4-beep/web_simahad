@extends(request()->is('musyrifah/*') ? 'layouts.musyrifah' : 'layouts.guru')

@section('content')
<style>
    .jadwal-page {
        padding: 20px 28px;
        color: #171717;
        font-family: inherit;
    }

    .jadwal-breadcrumb {
        color: #790d20;
        font-size: 22px;
        font-weight: 500;
        margin-bottom: 14px;
    }

    .jadwal-title {
        font-size: 32px;
        font-weight: 600;
        margin: 0 0 7px;
    }

    .jadwal-description {
        font-size: 20px;
        margin: 0;
    }

    .jadwal-content {
        border-top: 1px solid #e8dcd7;
        margin: 28px -28px 0;
        padding: 15px 28px 25px;
        background: #f8f7f6;
    }

    .jadwal-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .jadwal-stat-card {
        background: #fff;
        border: 1px solid #eee4df;
        border-radius: 11px;
        padding: 18px 16px;
    }

    .jadwal-stat-label {
        font-size: 16px;
        color: #85817f;
        margin-bottom: 14px;
    }

    .jadwal-stat-number {
        font-size: 25px;
        font-weight: 500;
        color: #444;
    }

    .jadwal-section-title {
        font-size: 19px;
        font-weight: 600;
        color: #790d20;
        margin: 20px 0 17px;
    }

    .jadwal-card {
        background: #fff;
        border: 1px solid #eee4df;
        border-radius: 11px;
        padding: 20px 15px 16px;
        margin-bottom: 14px;
    }

    .jadwal-mapel-title {
        font-size: 19px;
        font-weight: 600;
        margin-bottom: 16px;
        color: #414141;
    }

    .jadwal-detail {
        color: #85817f;
        font-size: 16px;
        margin-bottom: 13px;
    }

    .jadwal-progress-info {
        color: #85817f;
        font-size: 16px;
        margin-bottom: 12px;
    }

    .jadwal-progress {
        height: 8px;
        background: #e9e1dd;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 12px;
    }

    .jadwal-progress-bar {
        height: 100%;
        background: #790d20;
        border-radius: 10px;
    }

    .jadwal-detail-link {
        display: inline-block;
        color: #790d20;
        font-size: 16px;
        text-decoration: none;
    }

    .jadwal-detail-link:hover {
        text-decoration: underline;
    }

    .jadwal-mengajar-card {
        background: #fff;
        border: 1px solid #eee4df;
        border-radius: 11px;
        padding: 20px 15px;
        margin-bottom: 14px;
    }

    .jadwal-waktu {
        font-size: 17px;
        font-weight: 600;
        color: #414141;
        margin-bottom: 12px;
    }

    .jadwal-mengajar-info {
        color: #85817f;
        font-size: 16px;
        margin-bottom: 9px;
    }

    .jadwal-mengajar-info:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .jadwal-page {
            padding: 16px;
        }

        .jadwal-breadcrumb {
            font-size: 18px;
        }

        .jadwal-title {
            font-size: 27px;
        }

        .jadwal-description {
            font-size: 16px;
        }

        .jadwal-content {
            margin-left: -16px;
            margin-right: -16px;
            padding: 15px 16px 22px;
        }

        .jadwal-stats {
            gap: 10px;
        }

        .jadwal-stat-card {
            padding: 15px 12px;
        }

        .jadwal-stat-number {
            font-size: 22px;
        }
    }
</style>

<div class="jadwal-page">

    {{-- JUDUL HALAMAN --}}
    <div class="jadwal-breadcrumb">
        Dashboard / Jadwal Saya
    </div>

    <h1 class="jadwal-title">Jadwal Saya</h1>

    <p class="jadwal-description">
        Kelola informasi pembelajaran dan pantau jadwal mengajarmu.
    </p>

    <div class="jadwal-content">

        {{-- RINGKASAN --}}
        <div class="jadwal-stats">
            <div class="jadwal-stat-card">
                <div class="jadwal-stat-label">Mata Pelajaran</div>
                <div class="jadwal-stat-number">2</div>
            </div>

            <div class="jadwal-stat-card">
                <div class="jadwal-stat-label">Kelompok</div>
                <div class="jadwal-stat-number">2</div>
            </div>
        </div>

        {{-- MATA PELAJARAN --}}
        <h2 class="jadwal-section-title">
            Mata Pelajaran yang Diampu
        </h2>

        <div class="jadwal-card">
            <div class="jadwal-mapel-title">Akhlak dan Adab</div>

            <div class="jadwal-detail">Kelompok A</div>

            <div class="jadwal-progress-info">
                Pertemuan 8 dari 16
            </div>

            <div class="jadwal-progress">
                <div class="jadwal-progress-bar" style="width: 50%;"></div>
            </div>

            <a href="{{ url('/guru/jadwal-saya/detail/alquran') }}"
            class="jadwal-detail-link">
             Lihat Detail &rarr;
            </a>
        </div>

        <div class="jadwal-card">
            <div class="jadwal-mapel-title">Al-Qur'an</div>

            <div class="jadwal-detail">Kelompok B</div>

            <div class="jadwal-progress-info">
                Pertemuan 6 dari 16
            </div>

            <div class="jadwal-progress">
                <div class="jadwal-progress-bar" style="width: 37.5%;"></div>
            </div>

            <a href="{{ url('/guru/jadwal-saya/detail/alquran') }}"
   class="jadwal-detail-link">
    Lihat Detail &rarr;
</a>
        </div>

        {{-- JADWAL MENGAJAR --}}
        <h2 class="jadwal-section-title">Jadwal Mengajar</h2>

        <div class="jadwal-mengajar-card">
            <div class="jadwal-waktu">
                Senin · 09.00–10.00 WIB
            </div>

            <div class="jadwal-mengajar-info">
                Akhlak dan Adab · Kelompok A
            </div>

            <div class="jadwal-mengajar-info">
                Ruang Pembelajaran 1
            </div>
        </div>

    </div>
</div>
@endsection