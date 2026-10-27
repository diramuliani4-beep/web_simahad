
@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .guru-dashboard {
        font-family: 'Poppins', sans-serif;
        color: #292929;
    }

    .guru-dashboard * {
        box-sizing: border-box;
    }

    .guru-dashboard .welcome h1 {
        font-size: 30px;
        font-weight: 600;
        line-height: 1.5;
        color: #080808;
    }

    .guru-dashboard .welcome p {
        margin: 5px 0 17px 3px;
        font-size: 20px;
        color: #111;
    }

    .guru-dashboard .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin: 0 0 19px 3px;
    }

    .guru-dashboard .stat-card {
        min-height: 128px;
        padding: 17px 15px 12px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
    }

    .guru-dashboard .stat-icon {
        width: 25px;
        height: 25px;
        color: #780d20;
        margin-bottom: 1px;
    }

    .guru-dashboard .stat-number {
        font-size: 28px;
        line-height: 1.4;
        font-weight: 500;
    }

    .guru-dashboard .stat-label {
        margin-top: 3px;
        font-size: 15px;
        color: #77716e;
    }

    .guru-dashboard .schedule-card {
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
        padding: 18px 14px 15px;
        margin: 0 0 21px 3px;
    }

    .guru-dashboard .section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        color: #780d20;
        font-size: 17px;
        font-weight: 600;
        margin-bottom: 19px;
    }

    .guru-dashboard .section-heading svg {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
    }

    .guru-dashboard .schedule-subject {
        font-size: 17px;
        font-weight: 600;
        margin-bottom: 17px;
    }

    .guru-dashboard .schedule-detail {
        color: #77716e;
        font-size: 15px;
        margin-bottom: 14px;
    }

    .guru-dashboard .primary-button {
        width: 100%;
        min-height: 55px;
        border: none;
        border-radius: 7px;
        background: #780d20;
        color: #fff;
        font-weight: 600;
        font-size: 16px;
        margin-top: 1px;
        cursor: pointer;
        transition: background .2s;
    }

    .guru-dashboard .primary-button:hover {
        background: #680a1b;
    }

    .guru-dashboard .activity-card {
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 10px;
        padding: 17px 14px 8px;
        margin: 0 0 0 8px;
    }

    .guru-dashboard .activity-title {
        color: #780d20;
        font-size: 17px;
        font-weight: 600;
        margin-bottom: 9px;
    }

    .guru-dashboard .activity-item {
        display: flex;
        gap: 12px;
        padding: 10px 0 18px;
        border-bottom: 1px solid #eadfda;
    }

    .guru-dashboard .activity-item:last-child {
        border-bottom: none;
    }

    .guru-dashboard .activity-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .guru-dashboard .activity-icon.success {
        color: #368c5d;
    }

    .guru-dashboard .activity-icon.normal {
        color: #780d20;
    }

    .guru-dashboard .activity-name {
        font-size: 14px;
        color: #555;
    }

    .guru-dashboard .activity-description {
        font-size: 12px;
        color: #77716e;
        margin-top: 3px;
    }

    @media (max-width: 768px) {
        .guru-dashboard .welcome h1 {
            font-size: 22px;
        }

        .guru-dashboard .welcome p {
            font-size: 14px;
            margin-left: 0;
        }

        .guru-dashboard .stats-grid {
            gap: 10px;
            margin-left: 0;
        }

        .guru-dashboard .stat-card {
            min-height: 115px;
            padding: 13px 11px;
        }

        .guru-dashboard .stat-number {
            font-size: 24px;
        }

        .guru-dashboard .stat-label {
            font-size: 12px;
        }

        .guru-dashboard .schedule-card,
        .guru-dashboard .activity-card {
            margin-left: 0;
        }

        .guru-dashboard .section-heading,
        .guru-dashboard .activity-title {
            font-size: 15px;
        }

        .guru-dashboard .schedule-subject {
            font-size: 15px;
        }

        .guru-dashboard .schedule-detail {
            font-size: 13px;
        }
    }

    @media (max-width: 380px) {
        .guru-dashboard .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="guru-dashboard">

    {{-- SAPAAN GURU --}}
    <section class="welcome">
        <h1>Assalamu’alaikum, Ustazah! 👋</h1>
        <p>Semoga kegiatan mengajar hari ini berjalan lancar.</p>
    </section>

    {{-- RINGKASAN --}}
    <section class="stats-grid">

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 7v14"/>
                <path d="M3 5.5A2.5 2.5 0 0 1 5.5 3H12v18H5.5A2.5 2.5 0 0 0 3 23z"/>
                <path d="M21 5.5A2.5 2.5 0 0 0 18.5 3H12v18h6.5A2.5 2.5 0 0 1 21 23z"/>
            </svg>
            <div class="stat-number">{{ $jumlahMapel ?? 2 }}</div>
            <div class="stat-label">Mata Pelajaran</div>
        </article>

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="10" cy="7" r="4"/>
                <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <div class="stat-number">{{ $jumlahKelompok ?? 2 }}</div>
            <div class="stat-label">Kelompok Diajar</div>
        </article>

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="17" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
            <div class="stat-number">{{ $jadwalMingguIni ?? 3 }}</div>
            <div class="stat-label">Jadwal Minggu Ini</div>
        </article>

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="4" width="14" height="18" rx="2"/>
                <path d="M9 4V2h6v2M9 13l2 2 4-4"/>
            </svg>
            <div class="stat-number">{{ $absensiPerluDiisi ?? 2 }}</div>
            <div class="stat-label">Absensi Perlu Diisi</div>
        </article>

    </section>

    {{-- JADWAL BERIKUTNYA --}}
    <section class="schedule-card">

        <div class="section-heading">
            <span>Jadwal Mengajar Berikutnya</span>
            <svg viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 10h18"/>
                <path d="M12 13v4l3 2"/>
                <circle cx="12" cy="16" r="7"/>
            </svg>
        </div>

        <div class="schedule-subject">
            {{ data_get($jadwalBerikutnya ?? null, 'mata_pelajaran', 'Akhlak dan Adab') }}
        </div>

        <p class="schedule-detail">
            {{ data_get($jadwalBerikutnya ?? null, 'kelompok', 'Kelompok A') }}
            ·
            {{ data_get($jadwalBerikutnya ?? null, 'jam', '09.00–10.00 WIB') }}
        </p>

        <p class="schedule-detail">
            {{ data_get($jadwalBerikutnya ?? null, 'ruangan', 'Ruang Pembelajaran 1') }}
        </p>

        <button type="button" class="primary-button"
            onclick="window.location.href='{{ url('/guru/jadwal') }}'">
            Lihat Jadwal
        </button>

    </section>

    {{-- AKTIVITAS TERBARU --}}
    <section class="activity-card">

        <h2 class="activity-title">Aktivitas Terbaru</h2>

        <div class="activity-item">
            <svg class="activity-icon success"
                 viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/>
                <path d="m8 12 3 3 5-6"/>
            </svg>
            <div>
                <div class="activity-name">Absensi Kelompok A</div>
                <p class="activity-description">
                    Pertemuan sebelumnya telah dicatat.
                </p>
            </div>
        </div>

        <div class="activity-item">
            <svg class="activity-icon normal"
                 viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 10h18"/>
            </svg>
            <div>
                <div class="activity-name">
                    Jadwal pembelajaran diperbarui
                </div>
                <p class="activity-description">
                    Periksa jadwal terbaru dari pengelola.
                </p>
            </div>
        </div>

    </section>

</div>

@endsection