
@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .guru-dashboard {
        font-family: 'Poppins', sans-serif;
        color: #292929;
        width: 100%;
        box-sizing: border-box;
    }

    .guru-dashboard *,
    .guru-dashboard *::before,
    .guru-dashboard *::after {
        box-sizing: border-box;
    }

    /* SAPAAN GURU */
    .guru-dashboard .welcome h1 {
        font-size: 30px;
        font-weight: 600;
        line-height: 1.4;
        color: #080808;
        margin: 0 0 8px;
    }

    .guru-dashboard .welcome p {
        margin: 0 0 17px 3px;
        font-size: 20px;
        line-height: 1.5;
        color: #111;
    }

    /* RINGKASAN DASHBOARD */
    .guru-dashboard .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 13px;
        margin: 0 0 19px 3px;
    }

    .guru-dashboard .stat-card {
        min-width: 0;
        min-height: 105px;
        padding: 14px 15px 12px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
    }

    .guru-dashboard .stat-icon {
        display: block;
        width: 25px;
        height: 25px;
        color: #780d20;
        margin-bottom: 2px;
    }

    .guru-dashboard .stat-number {
        font-size: 27px;
        line-height: 1.35;
        font-weight: 500;
        color: #292929;
    }

    .guru-dashboard .stat-label {
        margin-top: 4px;
        font-size: 15px;
        line-height: 1.4;
        color: #77716e;
    }

    /* KARTU JADWAL */
    .guru-dashboard .schedule-card {
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
        padding: 14px 14px 15px;
        margin: 0 0 20px 3px;
    }

    .guru-dashboard .section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        color: #780d20;
        font-size: 17px;
        font-weight: 600;
        line-height: 1.5;
        margin: 0 0 12px;
    }

    .guru-dashboard .section-heading svg {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
    }

    /* DAFTAR JADWAL */
    .guru-dashboard .schedule-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .guru-dashboard .schedule-item {
        min-width: 0;
        background: #fff;
        border: 1px solid #e8e5e3;
        border-radius: 11px;
        padding: 13px 15px 12px;
        transition: border-color 0.2s ease;
    }

    .guru-dashboard .schedule-item:hover {
        border-color: #c8a1a8;
    }

    .guru-dashboard .schedule-time {
        font-size: 16px;
        font-weight: 600;
        line-height: 1.5;
        color: #252525;
        margin-bottom: 5px;
    }

    .guru-dashboard .schedule-subject {
        font-size: 16px;
        font-weight: 500;
        line-height: 1.5;
        color: #292929;
        margin: 0 0 4px;
    }

    .guru-dashboard .schedule-detail {
        color: #85817f;
        font-size: 14px;
        line-height: 1.5;
        margin: 0 0 5px;
    }

    .guru-dashboard .schedule-link {
        display: inline-block;
        color: #780d20;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        margin-top: 2px;
        transition: color 0.2s ease;
    }

    .guru-dashboard .schedule-link:hover {
        color: #a51a35;
        text-decoration: underline;
    }

    .guru-dashboard .schedule-link:focus-visible {
        outline: 2px solid #780d20;
        outline-offset: 3px;
        border-radius: 3px;
    }

    /* AKTIVITAS TERBARU */
    .guru-dashboard .activity-card {
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 10px;
        padding: 15px 14px 8px;
        margin: 0 0 20px 3px;
    }

    .guru-dashboard .activity-title {
        color: #780d20;
        font-size: 17px;
        font-weight: 600;
        line-height: 1.5;
        margin: 0 0 9px;
    }

    .guru-dashboard .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 0 15px;
        border-bottom: 1px solid #eadfda;
    }

    .guru-dashboard .activity-item:last-child {
        border-bottom: none;
    }

    .guru-dashboard .activity-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .guru-dashboard .activity-icon.success {
        color: #368c5d;
    }

    .guru-dashboard .activity-icon.normal {
        color: #780d20;
    }

    .guru-dashboard .activity-name {
        font-size: 14px;
        line-height: 1.5;
        color: #555;
        margin: 0;
    }

    .guru-dashboard .activity-description {
        font-size: 12px;
        line-height: 1.6;
        color: #77716e;
        margin: 3px 0 0;
    }

    /* RESPONSIVE TABLET DAN HP */
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
            min-height: 100px;
            padding: 12px 11px;
        }

        .guru-dashboard .stat-icon {
            width: 22px;
            height: 22px;
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

        .guru-dashboard .schedule-item {
            padding: 12px;
        }

        .guru-dashboard .schedule-time,
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
        <h1>Assalamu’alaikum, {{ $guru?->name ?? 'Ustazah' }}! 👋</h1>

        <p>
            Semoga kegiatan mengajar hari ini berjalan lancar.
        </p>
    </section>

    {{-- RINGKASAN DASHBOARD --}}
    <section class="stats-grid">

        {{-- MATA PELAJARAN --}}
        <article class="stat-card">
            <svg class="stat-icon"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M12 7v14"/>
                <path d="M3 5.5A2.5 2.5 0 0 1 5.5 3H12v18H5.5A2.5 2.5 0 0 0 3 23z"/>
                <path d="M21 5.5A2.5 2.5 0 0 0 18.5 3H12v18h6.5A2.5 2.5 0 0 1 21 23z"/>
            </svg>

            <div class="stat-number">
                {{ $jumlahMapel ?? 0 }}
            </div>

            <div class="stat-label">Mata Pelajaran</div>
        </article>

        {{-- KELOMPOK DIAJAR --}}
        <article class="stat-card">
            <svg class="stat-icon"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="10" cy="7" r="4"/>
                <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>

            <div class="stat-number">
                {{ $jumlahKelompok ?? 0 }}
            </div>

            <div class="stat-label">Kelompok Diajar</div>
        </article>

        {{-- JADWAL MINGGU INI --}}
        <article class="stat-card">
            <svg class="stat-icon"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <rect x="3" y="4" width="18" height="17" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>

            <div class="stat-number">
                {{ $jadwalMingguIni ?? 0 }}
            </div>

            <div class="stat-label">Jadwal Minggu Ini</div>
        </article>

        {{-- ABSENSI PERLU DIISI --}}
        <article class="stat-card">
            <svg class="stat-icon"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <rect x="5" y="4" width="14" height="18" rx="2"/>
                <path d="M9 4V2h6v2M9 13l2 2 4-4"/>
            </svg>

            <div class="stat-number">
                {{ $absensiPerluDiisi ?? 0 }}
            </div>

            <div class="stat-label">Absensi Perlu Diisi</div>
        </article>

    </section>

    {{-- JADWAL MENGAJAR BERIKUTNYA --}}
    <section class="schedule-card">

        <div class="section-heading">
            <span>Jadwal Mengajar Berikutnya</span>

            <svg viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 10h18"/>
                <path d="M12 13v4l3 2"/>
            </svg>
        </div>

        <div class="schedule-list">

            @forelse ($jadwalMendatang ?? collect() as $jadwal)
                <article class="schedule-item">

                    <div class="schedule-time">
                        {{ $jadwal->hari }}, {{ $jadwal->jamTeks() }}
                    </div>

                    <h3 class="schedule-subject">
                        {{ $jadwal->namaMataPelajaran() }} · {{ $jadwal->kelompok }}
                    </h3>

                    <p class="schedule-detail">
                        {{ $jadwal->ruangan ?: 'Ruang Kelas' }}
                    </p>

                    <a href="{{ route('guru.jadwal-saya') }}"
                       class="schedule-link">
                        Buka jadwal &rarr;
                    </a>

                </article>
            @empty
                <article class="schedule-item">
                    <h3 class="schedule-subject">Belum ada jadwal mengajar</h3>
                    <p class="schedule-detail">
                        Jadwal akan tampil setelah Musyrifah menetapkan penugasan pembelajaran.
                    </p>
                </article>
            @endforelse

        </div>

    </section>

    {{-- AKTIVITAS TERBARU --}}
    <section class="activity-card">

        <h2 class="activity-title">
            Aktivitas Terbaru
        </h2>

        {{-- TUGAS --}}
        <div class="activity-item">

            <svg class="activity-icon success"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <rect x="4" y="3" width="16" height="18" rx="2"/>
                <path d="M8 8h8M8 12h8M8 16h5"/>
            </svg>

            <div>
                <p class="activity-name">
                    {{ $jumlahTugas ?? 0 }} tugas telah dibuat
                </p>

                <p class="activity-description">
                    Kelola tugas, pengumpulan, dan penilaian mahasiswa pada menu Tugas.
                </p>
            </div>

        </div>

        {{-- PENGUMPULAN --}}
        <div class="activity-item">

            <svg class="activity-icon normal"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M12 3v12"/>
                <path d="m7 10 5 5 5-5"/>
                <path d="M5 21h14"/>
            </svg>

            <div>
                <p class="activity-name">
                    Pengumpulan tugas mahasiswa
                </p>

                <p class="activity-description">
                    Pengumpulan terbaru otomatis masuk ke halaman Pengumpulan Tugas.
                </p>
            </div>

        </div>

    </section>

</div>

@endsection
