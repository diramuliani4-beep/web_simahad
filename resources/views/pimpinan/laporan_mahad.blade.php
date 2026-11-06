
@extends('layouts.pimpinan')

@section('title', 'Laporan Ma’had')

@section('content')
<style>
    .laporan-page {
        font-family: 'Poppins', sans-serif;
        color: #29252a;
        padding: 8px 0 28px;
    }

    .laporan-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 26px;
    }

    .laporan-heading h1 {
        margin: 0 0 8px;
        font-size: 27px;
        font-weight: 700;
        color: #780d20;
    }

    .laporan-heading p {
        margin: 0;
        color: #777078;
        font-size: 14px;
        line-height: 1.7;
    }

    .laporan-date {
        padding: 10px 14px;
        border: 1px solid #eadde0;
        border-radius: 10px;
        background: #fff;
        color: #780d20;
        font-size: 13px;
        font-weight: 600;
    }

    .laporan-info {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 18px;
        margin-bottom: 26px;
        border: 1px solid #f0dfe3;
        border-left: 4px solid #780d20;
        border-radius: 12px;
        background: #fff9fa;
    }

    .laporan-info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #f5e5e9;
        color: #780d20;
    }

    .laporan-info h3 {
        margin: 0 0 5px;
        font-size: 15px;
        font-weight: 700;
    }

    .laporan-info p {
        margin: 0;
        color: #716970;
        font-size: 13px;
        line-height: 1.7;
    }

    .laporan-section-title {
        margin: 0 0 16px;
        font-size: 18px;
        font-weight: 700;
    }

    .laporan-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .laporan-card {
        display: flex;
        flex-direction: column;
        min-height: 220px;
        padding: 22px;
        border: 1px solid #eee7e9;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(50, 20, 30, .04);
        transition: transform .2s ease, box-shadow .2s ease,
                    border-color .2s ease;
    }

    .laporan-card:hover {
        transform: translateY(-4px);
        border-color: #d9b5bd;
        box-shadow: 0 10px 25px rgba(120, 13, 32, .09);
    }

    .laporan-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .laporan-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f7e9ec;
        color: #780d20;
    }

    .laporan-icon svg,
    .laporan-info-icon svg {
        width: 23px;
        height: 23px;
    }

    .laporan-number {
        color: #c7b7bb;
        font-size: 13px;
        font-weight: 700;
    }

    .laporan-card h3 {
        margin: 0 0 8px;
        color: #30272b;
        font-size: 16px;
        font-weight: 700;
    }

    .laporan-card p {
        flex: 1;
        margin: 0 0 20px;
        color: #81777d;
        font-size: 13px;
        line-height: 1.7;
    }

    .laporan-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        color: #780d20;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .laporan-link:hover {
        color: #a31c36;
    }

    .laporan-link svg {
        width: 16px;
        height: 16px;
        transition: transform .2s ease;
    }

    .laporan-link:hover svg {
        transform: translateX(4px);
    }

    @media (max-width: 1000px) {
        .laporan-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .laporan-heading h1 {
            font-size: 23px;
        }

        .laporan-grid {
            grid-template-columns: 1fr;
        }

        .laporan-card {
            min-height: 190px;
        }

        .laporan-date {
            width: 100%;
            box-sizing: border-box;
        }
    }
</style>

<div class="laporan-page">
    <div class="laporan-heading">
        <div>
            <h1>Laporan Ma’had</h1>
            <p>
                Informasi dan ringkasan data operasional Ma’had
                dalam satu halaman.
            </p>
        </div>

        <div class="laporan-date">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 style="vertical-align: -3px; margin-right: 5px;">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 11h18"/>
            </svg>
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    

    </div>

    <h2 class="laporan-section-title">Kategori Laporan</h2>

    <div class="laporan-grid">
        @foreach ($laporan as $item)
            <article class="laporan-card">
                <div class="laporan-card-top">
                    <div class="laporan-icon">
                        @if ($item['ikon'] === 'users')
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="10" cy="7" r="4"/>
                                <path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        @elseif ($item['ikon'] === 'bed')
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7v13M21 7v13M3 12h18M5 12V9a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3"/>
                                <path d="M12 12V9a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3"/>
                            </svg>
                        @elseif ($item['ikon'] === 'book-open')
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 7v14M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3z"/>
                            </svg>
                        @elseif ($item['ikon'] === 'wallet')
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="15" rx="2"/>
                                <path d="M3 9h18M16 14h.01"/>
                                <path d="M7 5V3h11"/>
                            </svg>
                        @elseif ($item['ikon'] === 'file-check')
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <path d="M14 2v6h6M8 15l2.5 2.5L16 12"/>
                            </svg>
                        @endif
                    </div>

                    <span class="laporan-number">
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <h3>{{ $item['judul'] }}</h3>
                <p>{{ $item['deskripsi'] }}</p>
                <a href="{{ $item['url'] }}" class="laporan-link">
                    Lihat laporan
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </article>
        @endforeach
    </div>
</div>
@endsection