
@extends('layouts.pimpinan')

@section('title', 'Dashboard Pimpinan')

@section('content')

<style>
    .pimpinan-dashboard {
        font-family: 'Poppins', sans-serif;
        color: #292929;
    }

    .pimpinan-dashboard * {
        box-sizing: border-box;
    }

    .pimpinan-dashboard .welcome h1 {
        font-size: 30px;
        font-weight: 600;
        line-height: 1.5;
        color: #080808;
        margin: 0;
    }

    .pimpinan-dashboard .welcome p {
        margin: 10px 0 13px;
        font-size: 20px;
        color: #111;
    }

    .pimpinan-dashboard .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 23px;
    }

    .pimpinan-dashboard .stat-card {
        min-height: 178px;
        padding: 17px 15px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 14px;
        box-shadow: 0 1px 2px rgba(0,0,0,.03);
    }

    .pimpinan-dashboard .stat-icon {
        width: 31px;
        height: 31px;
        color: #780d20;
        margin-bottom: 15px;
    }

    .pimpinan-dashboard .stat-number {
        font-size: 34px;
        line-height: 1.4;
        font-weight: 500;
        color: #780d20;
    }

    .pimpinan-dashboard .stat-label {
        margin-top: 12px;
        font-size: 18px;
        color: #77716e;
    }

    .pimpinan-dashboard .payment-card,
    .pimpinan-dashboard .activity-card {
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 14px;
        padding: 25px 23px;
        margin-bottom: 22px;
    }

    .pimpinan-dashboard .section-title {
        color: #292929;
        font-size: 25px;
        font-weight: 600;
        margin: 0 0 21px;
    }

    .pimpinan-dashboard .payment-number {
        color: #780d20;
        font-size: 31px;
        font-weight: 500;
        margin-bottom: 17px;
    }

    .pimpinan-dashboard .progress-track {
        width: 100%;
        height: 12px;
        border-radius: 20px;
        background: #eae5e2;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .pimpinan-dashboard .progress-value {
        height: 100%;
        width: 85%;
        background: #780d20;
        border-radius: 20px;
    }

    .pimpinan-dashboard .payment-description {
        font-size: 17px;
        color: #77716e;
        margin: 0;
    }

    .pimpinan-dashboard .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 13px 0;
        border-bottom: 1px solid #eee7e4;
    }

    .pimpinan-dashboard .activity-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .pimpinan-dashboard .activity-icon {
        width: 30px;
        height: 30px;
        flex-shrink: 0;
        color: #780d20;
    }

    .pimpinan-dashboard .activity-name {
        font-size: 18px;
        font-weight: 500;
        color: #454545;
    }

    .pimpinan-dashboard .activity-description {
        font-size: 17px;
        color: #888;
        margin: 5px 0 0;
    }

    @media (max-width: 1000px) {
        .pimpinan-dashboard .welcome h1 {
            font-size: 25px;
        }

        .pimpinan-dashboard .stat-card {
            min-height: 150px;
        }

        .pimpinan-dashboard .stat-number {
            font-size: 29px;
        }

        .pimpinan-dashboard .stat-label {
            font-size: 15px;
        }
    }

    @media (max-width: 600px) {
        .pimpinan-dashboard .welcome h1 {
            font-size: 22px;
        }

        .pimpinan-dashboard .welcome p {
            font-size: 14px;
        }

        .pimpinan-dashboard .stats-grid {
            gap: 10px;
        }

        .pimpinan-dashboard .stat-card {
            min-height: 125px;
            padding: 12px;
            border-radius: 10px;
        }

        .pimpinan-dashboard .stat-icon {
            width: 25px;
            height: 25px;
            margin-bottom: 8px;
        }

        .pimpinan-dashboard .stat-number {
            font-size: 25px;
        }

        .pimpinan-dashboard .stat-label {
            font-size: 12px;
            margin-top: 5px;
        }

        .pimpinan-dashboard .payment-card,
        .pimpinan-dashboard .activity-card {
            padding: 17px 14px;
        }

        .pimpinan-dashboard .section-title {
            font-size: 19px;
        }

        .pimpinan-dashboard .payment-number {
            font-size: 26px;
        }

        .pimpinan-dashboard .payment-description,
        .pimpinan-dashboard .activity-description {
            font-size: 13px;
        }

        .pimpinan-dashboard .activity-name {
            font-size: 14px;
        }
    }

    @media (max-width: 360px) {
        .pimpinan-dashboard .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="pimpinan-dashboard">

    {{-- SAPAAN PIMPINAN --}}
    <section class="welcome">
        <h1>Assalamu’alaikum, Bapak/Ibu Pimpinan! 👋</h1>
        <p>Berikut ringkasan kondisi Ma’had.</p>
    </section>

    {{-- RINGKASAN STATISTIK --}}
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
            <div class="stat-number">{{ $totalPenghuni ?? 120 }}</div>
            <div class="stat-label">Total Penghuni</div>
        </article>

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="10" width="18" height="11" rx="2"/>
                <path d="M3 14h18M12 10V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v5"/>
                <path d="M12 10V5a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v5"/>
            </svg>
            <div class="stat-number">{{ $kamarTersedia ?? 8 }}</div>
            <div class="stat-label">Kamar Tersedia</div>
        </article>

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 0 4 22z"/>
                <path d="M4 5.5V22M8 7h8M8 11h8"/>
            </svg>
            <div class="stat-number">{{ $kelompokBelajar ?? 6 }}</div>
            <div class="stat-label">Kelompok Belajar</div>
        </article>

        <article class="stat-card">
            <svg class="stat-icon" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <path d="M14 2v6h6"/>
                <circle cx="12" cy="15" r="3"/>
                <path d="m14.2 17.2 2 2"/>
            </svg>
            <div class="stat-number">{{ $pengajuanMenunggu ?? 12 }}</div>
            <div class="stat-label">Pengajuan Menunggu</div>
        </article>

    </section>

    {{-- RINGKASAN PEMBAYARAN --}}
    <section class="payment-card">
        <h2 class="section-title">Ringkasan Pembayaran</h2>

        <div class="payment-number">{{ $persentasePembayaran ?? 85 }}%</div>

        <div class="progress-track"
             role="progressbar"
             aria-label="Persentase pembayaran"
             aria-valuemin="0"
             aria-valuemax="100"
             aria-valuenow="{{ $persentasePembayaran ?? 85 }}">
            <div class="progress-value"
                 style="width: {{ max(0, min(100, (float) ($persentasePembayaran ?? 85))) }}%;">
            </div>
        </div>

        <p class="payment-description">
            {{ $statusPembayaran ?? 'Status pembayaran terkonfirmasi' }}
        </p>
    </section>

    {{-- AKTIVITAS TERBARU --}}
    <section class="activity-card">
        <h2 class="section-title">Aktivitas Terbaru</h2>

        @forelse(($aktivitasTerbaru ?? [
            [
                'judul' => 'Pendaftaran mahasiswa',
                'keterangan' => 'Menunggu pemeriksaan',
                'jenis' => 'penghuni'
            ],
            [
                'judul' => 'Pembayaran Ma’had',
                'keterangan' => 'Pembaruan status pembayaran',
                'jenis' => 'pembayaran'
            ],
            [
                'judul' => 'Kegiatan pembelajaran',
                'keterangan' => 'Informasi kegiatan terbaru',
                'jenis' => 'pembelajaran'
            ]
        ]) as $aktivitas)

            <div class="activity-item">
                <svg class="activity-icon" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">

                    @if(data_get($aktivitas, 'jenis') === 'pembayaran')
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18M7 15h4"/>
                    @elseif(data_get($aktivitas, 'jenis') === 'pembelajaran')
                        <path d="M12 7v14"/>
                        <path d="M3 5a2 2 0 0 1 2-2h7v18H5a2 2 0 0 0-2 2z"/>
                        <path d="M21 5a2 2 0 0 0-2-2h-7v18h7a2 2 0 0 1 2 2z"/>
                    @else
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="10" cy="7" r="4"/>
                        <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    @endif
                </svg>

                <div>
                    <div class="activity-name">
                        {{ data_get($aktivitas, 'judul', 'Aktivitas Ma’had') }}
                    </div>
                    <p class="activity-description">
                        {{ data_get($aktivitas, 'keterangan', 'Informasi terbaru') }}
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