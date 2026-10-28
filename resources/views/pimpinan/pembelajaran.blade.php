
@extends('layouts.pimpinan')

@section('title', 'Pembelajaran Pimpinan')

@section('content')

<style>
    .pembelajaran-page {
        font-family: 'Poppins', sans-serif;
        color: #292929;
    }

    .pembelajaran-page * {
        box-sizing: border-box;
    }

    .pembelajaran-page .breadcrumb {
        color: #780d20;
        font-size: 24px;
        font-weight: 500;
        margin: 0 0 18px;
    }

    .pembelajaran-page .page-title {
        font-size: 32px;
        line-height: 1.4;
        font-weight: 600;
        color: #111;
        margin: 0 0 8px;
    }

    .pembelajaran-page .page-description {
        font-size: 19px;
        line-height: 1.6;
        margin: 0 0 28px;
        color: #222;
    }

    /* KARTU STATISTIK */

    .pembelajaran-page .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 18px;
    }

    .pembelajaran-page .stat-card {
        min-height: 88px;
        padding: 14px 16px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .03);
    }

    .pembelajaran-page .stat-number {
        color: #780d20;
        font-size: 24px;
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 4px;
    }

    .pembelajaran-page .stat-label {
        font-size: 14px;
        color: #77716e;
    }

    /* DAFTAR KELOMPOK */

    .pembelajaran-page .group-section-title {
        font-size: 19px;
        font-weight: 600;
        margin: 0 0 15px;
        color: #383838;
    }

    .pembelajaran-page .group-list {
        display: flex;
        flex-direction: column;
        gap: 13px;
    }

    .pembelajaran-page .group-card {
        padding: 17px 15px 14px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 11px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .03);
    }

    .pembelajaran-page .group-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .pembelajaran-page .group-icon {
        width: 51px;
        height: 49px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: #f9edef;
        color: #780d20;
    }

    .pembelajaran-page .group-icon svg {
        width: 25px;
        height: 25px;
    }

    .pembelajaran-page .group-name {
        font-size: 18px;
        font-weight: 500;
        margin: 0 0 4px;
        color: #454545;
    }

    .pembelajaran-page .group-count {
        font-size: 13px;
        color: #888;
        margin: 0;
    }

    .pembelajaran-page .schedule-link {
        display: inline-block;
        color: #780d20;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
    }

    .pembelajaran-page .schedule-link:hover {
        color: #590018;
        text-decoration: underline;
    }

    /* RESPONSIVE */

    @media (max-width: 1000px) {
        .pembelajaran-page .breadcrumb {
            font-size: 21px;
        }

        .pembelajaran-page .page-title {
            font-size: 28px;
        }

        .pembelajaran-page .page-description {
            font-size: 16px;
        }
    }

    @media (max-width: 600px) {
        .pembelajaran-page .breadcrumb {
            font-size: 17px;
            margin-bottom: 12px;
        }

        .pembelajaran-page .page-title {
            font-size: 24px;
        }

        .pembelajaran-page .page-description {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .pembelajaran-page .stats-grid {
            gap: 9px;
        }

        .pembelajaran-page .stat-card {
            min-height: 78px;
            padding: 12px;
        }

        .pembelajaran-page .stat-number {
            font-size: 21px;
        }

        .pembelajaran-page .stat-label {
            font-size: 12px;
        }

        .pembelajaran-page .group-section-title {
            font-size: 17px;
        }

        .pembelajaran-page .group-card {
            padding: 13px 12px;
        }

        .pembelajaran-page .group-icon {
            width: 43px;
            height: 43px;
        }

        .pembelajaran-page .group-name {
            font-size: 15px;
        }

        .pembelajaran-page .schedule-link {
            font-size: 14px;
        }
    }
</style>

<div class="pembelajaran-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        Dashboard / Pembelajaran
    </div>

    <!-- JUDUL HALAMAN -->
    <section>
        <h1 class="page-title">Pembelajaran</h1>

        <p class="page-description">
            Pantau pelaksanaan pembelajaran dan kehadiran mahasiswa.
        </p>
    </section>

    <!-- STATISTIK PEMBELAJARAN -->
    <section class="stats-grid">

        <article class="stat-card">
            <div class="stat-number">
                {{ $mataPelajaran }}
            </div>
            <div class="stat-label">Mata Pelajaran</div>
        </article>

        <article class="stat-card">
            <div class="stat-number">
                {{ $pengajar }}
            </div>
            <div class="stat-label">Pengajar</div>
        </article>

        <article class="stat-card">
            <div class="stat-number">
                {{ $kelompokBelajar }}
            </div>
            <div class="stat-label">Kelompok Belajar</div>
        </article>

        <article class="stat-card">
            <div class="stat-number">
                {{ $targetPertemuan }}
            </div>
            <div class="stat-label">Target Pertemuan</div>
        </article>

    </section>

    <!-- DAFTAR KELOMPOK BELAJAR -->
    <section class="group-section">

        <h2 class="group-section-title">
            Daftar Kelompok Belajar
        </h2>

        <div class="group-list">

            @forelse ($daftarKelompok as $kelompok)

                <article class="group-card">

                    <div class="group-header">

                        <div class="group-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="10" cy="7" r="4"/>
                                <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>

                        <div>
                            <h3 class="group-name">
                                {{ $kelompok['nama'] }}
                            </h3>

                            <p class="group-count">
                                {{ $kelompok['jumlah'] }}
                            </p>
                        </div>

                    </div>

                    <a
                        href="#"
                        class="schedule-link"
                        aria-label="Lihat jadwal {{ $kelompok['nama'] }}"
                    >
                        Lihat Jadwal →
                    </a>

                </article>

            @empty

                <p>Belum ada kelompok belajar.</p>

            @endforelse

        </div>

    </section>

</div>

@endsection