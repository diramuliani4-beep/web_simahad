
@extends('layouts.pimpinan')

@section('content')
<style>
    .data-penghuni {
        padding: 24px 28px;
        color: #171717;
        font-family: inherit;
    }

    .dp-breadcrumb {
        color: #790d20;
        font-size: 22px;
        margin-bottom: 14px;
    }

    .dp-title {
        font-size: 32px;
        font-weight: 600;
        margin: 0 0 10px;
    }

    .dp-subtitle {
        font-size: 20px;
        margin: 0 0 24px;
    }

    .dp-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .dp-stat,
    .dp-search,
    .dp-card {
        background: #fff;
        border: 1px solid #e9ded8;
        border-radius: 13px;
        box-shadow: 0 1px 2px rgba(0,0,0,.03);
    }

    .dp-stat {
        padding: 20px 16px;
    }

    .dp-stat-number {
        color: #790d20;
        font-size: 36px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .dp-stat-label {
        color: #85817e;
        font-size: 20px;
    }

    .dp-search {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 18px;
        margin-bottom: 22px;
    }

    .dp-search svg {
        width: 24px;
        height: 24px;
        color: #8b8b8b;
        flex-shrink: 0;
    }

    .dp-search input {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        padding: 20px 0;
        font-size: 19px;
        color: #333;
    }

    .dp-card {
        padding: 19px 16px 16px;
        margin-bottom: 22px;
    }

    .dp-name {
        font-size: 24px;
        font-weight: 500;
        margin: 0 0 12px;
    }

    .dp-info {
        color: #85817e;
        font-size: 19px;
        margin-bottom: 12px;
    }

    .dp-detail {
        display: inline-block;
        color: #790d20;
        font-size: 19px;
        font-weight: 600;
        text-decoration: none;
    }

    .dp-detail:hover {
        text-decoration: underline;
    }

    .dp-empty {
        padding: 25px 18px;
        color: #777;
        background: #fff;
        border: 1px solid #e9ded8;
        border-radius: 12px;
    }

    @media (max-width: 700px) {
        .data-penghuni {
            padding: 18px 14px;
        }

        .dp-breadcrumb {
            font-size: 18px;
        }

        .dp-title {
            font-size: 27px;
        }

        .dp-subtitle {
            font-size: 16px;
        }

        .dp-stats {
            gap: 10px;
        }

        .dp-stat {
            padding: 15px 12px;
        }

        .dp-stat-number {
            font-size: 29px;
        }

        .dp-stat-label {
            font-size: 15px;
        }

        .dp-name {
            font-size: 21px;
        }

        .dp-info,
        .dp-detail {
            font-size: 16px;
        }
    }
</style>

<div class="data-penghuni">
    <div class="dp-breadcrumb">
        Dashboard / Data Penghuni
    </div>

    <h1 class="dp-title">Data Penghuni</h1>
    <p class="dp-subtitle">
        Lihat informasi mahasiswa penghuni Ma’had.
    </p>

    <div class="dp-stats">
        <div class="dp-stat">
            <div class="dp-stat-number">{{ $totalPenghuni }}</div>
            <div class="dp-stat-label">Total Penghuni</div>
        </div>

        <div class="dp-stat">
            <div class="dp-stat-number">{{ $totalAngkatan }}</div>
            <div class="dp-stat-label">Kelompok Angkatan</div>
        </div>
    </div>

    <form action="{{ route('pimpinan.data-penghuni') }}"
          method="GET"
          class="dp-search">
        <svg viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2"
             stroke-linecap="round">
            <circle cx="11" cy="11" r="7"/>
            <path d="m16 16 4 4"/>
        </svg>

        <input type="text"
               name="cari"
               value="{{ $keyword }}"
               placeholder="Cari nama atau NIM..."
               aria-label="Cari nama atau NIM">
    </form>

    @forelse ($penghuni as $item)
        <div class="dp-card">
            <h2 class="dp-name">{{ $item['nama'] }}</h2>

            <div class="dp-info">
                NIM: {{ $item['nim'] }}
            </div>

            <div class="dp-info">
                {{ $item['kamar'] }}
            </div>

            <a href="{{ route('pimpinan.data-penghuni') }}"
               class="dp-detail">
                Lihat Detail &rarr;
            </a>
        </div>
    @empty
        <div class="dp-empty">
            Data penghuni tidak ditemukan.
            Coba periksa kembali nama atau NIM yang dimasukkan.
        </div>
    @endforelse
</div>
@endsection