
@extends('layouts.guru')

@section('title', 'Detail Mata Pembelajaran')

@section('content')

<style>
    .detail-jadwal-page {
        width: 100%;
        padding: 15px 10px 25px;
        color: #292929;
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    .detail-jadwal-page *,
    .detail-jadwal-page *::before,
    .detail-jadwal-page *::after {
        box-sizing: border-box;
    }

    /* HEADER HALAMAN */
    .detail-jadwal-page .detail-breadcrumb {
        color: #790d20;
        font-size: 15px;
        font-weight: 500;
        margin-bottom: 8px;
        line-height: 1.5;
    }

    .detail-jadwal-page .detail-title {
        color: #111;
        font-size: 23px;
        font-weight: 600;
        line-height: 1.4;
        margin: 0 0 4px;
    }

    .detail-jadwal-page .detail-subtitle {
        color: #333;
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }

    .detail-jadwal-page .detail-header {
        padding-bottom: 16px;
        border-bottom: 1px solid #e8dcd7;
        margin-bottom: 5px;
    }

    /* KARTU INFORMASI */
    .detail-jadwal-page .info-card {
        background: #fff;
        border: 1px solid #eae7e5;
        border-radius: 12px;
        padding: 13px 12px;
        margin: 0 3px 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
    }

    .detail-jadwal-page .info-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 10px;
        color: #333;
    }

    .detail-jadwal-page .info-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.25fr);
        column-gap: 12px;
        row-gap: 8px;
    }

    .detail-jadwal-page .info-label {
        font-size: 12px;
        line-height: 1.5;
        color: #85817f;
        overflow-wrap: anywhere;
    }

    .detail-jadwal-page .info-value {
        font-size: 12px;
        line-height: 1.5;
        color: #454545;
        overflow-wrap: anywhere;
    }

    /* DAFTAR PERTEMUAN */
    .detail-jadwal-page .meeting-section-title {
        color: #292929;
        font-size: 14px;
        font-weight: 600;
        margin: 8px 10px 10px;
    }

    .detail-jadwal-page .meeting-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 0 3px;
    }

    .detail-jadwal-page .meeting-card {
        background: #fff;
        border: 1px solid #eae7e5;
        border-radius: 12px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
    }

    .detail-jadwal-page .meeting-main {
        display: grid;
        grid-template-columns: 38px minmax(0, 1fr) auto;
        align-items: center;
        gap: 8px;
        margin-bottom: 7px;
    }

    .detail-jadwal-page .meeting-number {
        min-height: 57px;
        border-radius: 8px;
        background: #f8edef;
        color: #790d20;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 5px 2px;
    }

    .detail-jadwal-page .meeting-number span {
        font-size: 9px;
        line-height: 1.3;
        margin-bottom: 4px;
    }

    .detail-jadwal-page .meeting-number strong {
        font-size: 16px;
        line-height: 1.2;
        font-weight: 600;
    }

    .detail-jadwal-page .meeting-info {
        min-width: 0;
    }

    .detail-jadwal-page .meeting-name {
        font-size: 13px;
        font-weight: 600;
        line-height: 1.5;
        color: #333;
        margin: 0 0 3px;
        overflow-wrap: anywhere;
    }

    .detail-jadwal-page .meeting-date {
        font-size: 10px;
        line-height: 1.5;
        color: #85817f;
        margin: 0;
    }

    .detail-jadwal-page .meeting-status {
        font-size: 10px;
        white-space: nowrap;
        font-weight: 500;
        text-align: right;
    }

    .detail-jadwal-page .status-complete {
        color: #368451;
    }

    .detail-jadwal-page .status-pending {
        color: #b47722;
    }

    /* TOMBOL ABSENSI */
    .detail-jadwal-page .meeting-action {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 31px;
        padding: 6px 10px;
        border: 1px solid #dedede;
        border-radius: 20px;
        background: #fff;
        color: #666;
        text-decoration: none;
        font-family: inherit;
        font-size: 11px;
        line-height: 1.4;
        transition: background 0.2s, border-color 0.2s;
    }

    .detail-jadwal-page .meeting-action:hover {
        background: #faf3f4;
        border-color: #cdaab0;
        color: #790d20;
    }

    .detail-jadwal-page .meeting-action:focus-visible {
        outline: 2px solid #790d20;
        outline-offset: 2px;
    }

    /* RESPONSIVE */
    @media (max-width: 480px) {
        .detail-jadwal-page {
            padding: 12px 5px 20px;
        }

        .detail-jadwal-page .detail-title {
            font-size: 21px;
        }

        .detail-jadwal-page .detail-breadcrumb {
            font-size: 13px;
        }

        .detail-jadwal-page .info-grid {
            grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
            column-gap: 8px;
        }

        .detail-jadwal-page .meeting-main {
            grid-template-columns: 35px minmax(0, 1fr);
            gap: 8px;
        }

        .detail-jadwal-page .meeting-status {
            grid-column: 2;
            text-align: left;
            margin-top: -2px;
        }

        .detail-jadwal-page .meeting-number {
            min-height: 54px;
        }
    }
</style>

<div class="detail-jadwal-page">

    {{-- HEADER DAN BREADCRUMB --}}
    <header class="detail-header">

        <div class="detail-breadcrumb">
            <a href="{{ url('/guru/jadwal-saya') }}"
               style="color: inherit; text-decoration: none;">
                Jadwal Saya
            </a>
            / Detail Mata Pembelajaran
        </div>

        <h1 class="detail-title">
            {{ $mataPelajaran ?? 'Akhlak dan Adab' }}
        </h1>

        <p class="detail-subtitle">
            {{ $kelompok ?? 'Kelompok A' }}
            · Periode pembelajaran berjalan
        </p>

    </header>

    {{-- INFORMASI PEMBELAJARAN --}}
    <section class="info-card">

        <h2 class="info-title">
            Informasi Pembelajaran
        </h2>

        <div class="info-grid">

            <div class="info-label">Instruktur</div>
            <div class="info-value">
                {{ $instruktur ?? 'Nama Guru' }}
            </div>

            <div class="info-label">Jadwal</div>
            <div class="info-value">
                {{ $jadwal ?? 'Senin, 08.00–09.00' }}
            </div>

            <div class="info-label">Total pertemuan</div>
            <div class="info-value">
                {{ $totalPertemuan ?? 16 }} pertemuan
            </div>

            <div class="info-label">Absensi terisi</div>
            <div class="info-value">
                {{ $absensiTerisi ?? 8 }} dari {{ $totalPertemuan ?? 16 }}
            </div>

        </div>

    </section>

    {{-- DAFTAR PERTEMUAN --}}
    <h2 class="meeting-section-title">
        Daftar Pertemuan
    </h2>

    <section class="meeting-list">

        @php
            $daftarPertemuan = $pertemuan ?? [
                [
                    'nomor' => 1,
                    'materi' => 'Pengenalan Akhlak',
                    'tanggal' => 'Tanggal: sesuai jadwal',
                    'status' => 'Selesai',
                    'selesai' => true,
                ],
                [
                    'nomor' => 2,
                    'materi' => 'Adab kepada Guru',
                    'tanggal' => 'Tanggal: sesuai jadwal',
                    'status' => 'Selesai',
                    'selesai' => true,
                ],
                [
                    'nomor' => 9,
                    'materi' => 'Materi pertemuan berikutnya',
                    'tanggal' => 'Tanggal: sesuai jadwal',
                    'status' => 'Belum diisi',
                    'selesai' => false,
                ],
            ];
        @endphp

        @foreach ($daftarPertemuan as $item)

            <article class="meeting-card">

                <div class="meeting-main">

                    {{-- NOMOR PERTEMUAN --}}
                    <div class="meeting-number">
                        <span>Pert.</span>

                        <strong>
                            {{ $item['nomor'] }}
                        </strong>
                    </div>

                    {{-- MATERI DAN TANGGAL --}}
                    <div class="meeting-info">

                        <h3 class="meeting-name">
                            {{ $item['materi'] }}
                        </h3>

                        <p class="meeting-date">
                            {{ $item['tanggal'] ?? 'Tanggal: sesuai jadwal' }}
                        </p>

                    </div>

                    {{-- STATUS ABSENSI --}}
                    <div class="meeting-status {{ $item['selesai'] ? 'status-complete' : 'status-pending' }}">
                        {{ $item['status'] }}
                    </div>

                </div>

                {{-- TOMBOL ABSENSI --}}
                <a href="{{ url('/guru/absensi') }}"
                   class="meeting-action">

                    @if ($item['selesai'])
                        Lihat Absensi &rarr;
                    @else
                        Isi Absensi &rarr;
                    @endif

                </a>

            </article>

        @endforeach

    </section>

</div>

@endsection
