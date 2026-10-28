
@extends('layouts.guru')

@section('content')
<style>
    .dokumentasi-page {
        padding: 20px 28px;
        color: #222;
        font-family: inherit;
    }

    .dokumentasi-breadcrumb {
        color: #790d20;
        font-size: 22px;
        font-weight: 500;
        margin-bottom: 14px;
    }

    .dokumentasi-title {
        font-size: 32px;
        font-weight: 600;
        margin: 0 0 7px;
    }

    .dokumentasi-description {
        font-size: 20px;
        margin: 0;
    }

    .dokumentasi-content {
        border-top: 1px solid #e8dcd7;
        margin: 28px -28px 0;
        padding: 18px 35px 25px;
        background: #f8f7f6;
        min-height: 500px;
    }

    .dokumentasi-card {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        background: #fff;
        border: 1px solid #eee3de;
        border-radius: 11px;
        padding: 17px 15px;
        margin-bottom: 16px;
    }

    .dokumentasi-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 63px;
        height: 63px;
        background: #f8ecef;
        border-radius: 12px;
        color: #790d20;
    }

    .dokumentasi-icon svg {
        width: 27px;
        height: 27px;
    }

    .dokumentasi-card-content {
        flex: 1;
        min-width: 0;
    }

    .dokumentasi-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .dokumentasi-card-title {
        font-size: 19px;
        font-weight: 600;
        color: #414141;
        margin: 0;
    }

    .dokumentasi-meta {
        color: #85817f;
        font-size: 16px;
        margin-bottom: 14px;
    }

    .dokumentasi-description-text {
        color: #77716d;
        font-size: 17px;
        margin-bottom: 15px;
    }

    .dokumentasi-status {
        display: inline-block;
        background: #d8f5e3;
        color: #168447;
        padding: 5px 11px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
    }

    .dokumentasi-link {
        color: #790d20;
        text-decoration: none;
        font-size: 17px;
    }

    .dokumentasi-link:hover {
        text-decoration: underline;
    }

    .dokumentasi-add {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 65px;
        padding: 15px;
        border: 1px solid #790d20;
        border-radius: 11px;
        background: #790d20;
        color: #fff;
        text-decoration: none;
        font-size: 19px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }

    .dokumentasi-add:hover {
        background: #5f0a19;
        color: #fff;
    }

    @media (max-width: 768px) {
        .dokumentasi-page {
            padding: 16px;
        }

        .dokumentasi-breadcrumb {
            font-size: 18px;
        }

        .dokumentasi-title {
            font-size: 27px;
        }

        .dokumentasi-description {
            font-size: 16px;
        }

        .dokumentasi-content {
            margin-left: -16px;
            margin-right: -16px;
            padding: 16px;
        }

        .dokumentasi-card {
            gap: 12px;
            padding: 13px 11px;
        }

        .dokumentasi-icon {
            width: 45px;
            height: 45px;
        }

        .dokumentasi-icon svg {
            width: 23px;
            height: 23px;
        }

        .dokumentasi-card-header {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .dokumentasi-card-title {
            font-size: 17px;
        }

        .dokumentasi-meta,
        .dokumentasi-description-text {
            font-size: 14px;
        }

        .dokumentasi-status {
            font-size: 12px;
        }
    }
</style>

<div class="dokumentasi-page">

    {{-- JUDUL HALAMAN --}}
    <div class="dokumentasi-breadcrumb">
        Dashboard / Kegiatan &amp; Dokumentasi
    </div>

    <h1 class="dokumentasi-title">
        Kegiatan &amp; Dokumentasi
    </h1>

    <p class="dokumentasi-description">
        Dokumentasikan kegiatan pembelajaran dan aktivitas kelompok.
    </p>

    <div class="dokumentasi-content">

        {{-- KEGIATAN PERTAMA --}}
        <div class="dokumentasi-card">

            <div class="dokumentasi-icon">
                <svg viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                    <path d="M16 3v4M8 3v4M3 10h18"/>
                    <path d="M8 14h.01M12 14h.01M16 14h.01"/>
                    <path d="M8 17h.01M12 17h.01"/>
                </svg>
            </div>

            <div class="dokumentasi-card-content">
                <div class="dokumentasi-card-header">
                    <h2 class="dokumentasi-card-title">
                        Kegiatan Pembelajaran
                    </h2>

                    <span class="dokumentasi-status">
                        Tersimpan
                    </span>
                </div>

                <div class="dokumentasi-meta">
                    Kelompok A · 8 Oktober 2026
                </div>

                <div class="dokumentasi-description-text">
                    Dokumentasi kegiatan belajar bersama mahasiswa.
                </div>

                <a href="{{ url('/guru/kegiatan-dokumentasi') }}?kegiatan=pembelajaran"
                    class="dokumentasi-link">
                    Lihat Dokumentasi &rarr;
                </a>
            </div>

        </div>

        {{-- KEGIATAN KEDUA --}}
        <div class="dokumentasi-card">

            <div class="dokumentasi-icon">
                <svg viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="4" width="14" height="14" rx="2"
                        transform="rotate(-8 11 11)"/>
                    <path d="M8 16l3-3 2 2 3-4 3 3"/>
                    <circle cx="10" cy="9" r="1.5"/>
                    <path d="M7 20h12a2 2 0 0 0 2-2V8"/>
                </svg>
            </div>

            <div class="dokumentasi-card-content">
                <div class="dokumentasi-card-header">
                    <h2 class="dokumentasi-card-title">
                        Diskusi Kelompok
                    </h2>

                    <span class="dokumentasi-status">
                        Tersimpan
                    </span>
                </div>

                <div class="dokumentasi-meta">
                    Kelompok B · 6 Oktober 2026
                </div>

                <div class="dokumentasi-description-text">
                    Catatan dan dokumentasi aktivitas kelompok.
                </div>

                <a href="{{ url('/guru/kegiatan-dokumentasi') }}?kegiatan=diskusi"
                    class="dokumentasi-link">
                    Lihat Dokumentasi &rarr;
                </a>
            </div>

        </div>

        {{-- TOMBOL TAMBAH DOKUMENTASI --}}
        <button type="button"
            class="dokumentasi-add"
            onclick="alert('Form tambah dokumentasi belum diaktifkan. Fitur ini dapat dihubungkan ke form dan database berikutnya.')">
            + Tambah Dokumentasi
        </button>

    </div>
</div>
@endsection