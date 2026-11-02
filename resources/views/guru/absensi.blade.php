
@extends(request()->is('musyrifah/*') ? 'layouts.musyrifah' : 'layouts.guru')

@section('title', 'Absensi Guru')

@section('content')

<style>
    .absensi-home {
        width: 100%;
        padding: 12px 10px 25px;
        font-family: 'Poppins', sans-serif;
        color: #292929;
        box-sizing: border-box;
    }

    .absensi-home *,
    .absensi-home *::before,
    .absensi-home *::after {
        box-sizing: border-box;
    }

    /* HEADER HALAMAN */
    .absensi-home .breadcrumb {
        font-size: 13px;
        color: #790d20;
        margin-bottom: 7px;
    }

    .absensi-home .page-title {
        font-size: 23px;
        font-weight: 600;
        margin: 0 0 4px;
        color: #171717;
    }

    .absensi-home .page-description {
        font-size: 13px;
        color: #444;
        line-height: 1.6;
        margin: 0 0 14px;
    }

    /* RINGKASAN ABSENSI */
    .absensi-home .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 13px;
    }

    .absensi-home .summary-card {
        min-height: 60px;
        padding: 11px 12px;
        background: #fff;
        border: 1px solid #e9e5e3;
        border-radius: 7px;
    }

    .absensi-home .summary-card.pending {
        background: #f9edef;
        border-color: #f3e4e7;
    }

    .absensi-home .summary-label {
        font-size: 10px;
        color: #777;
        margin-bottom: 7px;
    }

    .absensi-home .summary-card.pending .summary-label {
        color: #790d20;
    }

    .absensi-home .summary-number {
        font-size: 17px;
        font-weight: 600;
        color: #292929;
    }

    .absensi-home .summary-card.pending .summary-number {
        color: #790d20;
    }

    /* PILIH PERTEMUAN */
    .absensi-home .section-title {
        font-size: 13px;
        font-weight: 600;
        margin: 0 0 7px;
        color: #222;
    }

    .absensi-home .meeting-card {
        background: #f8f7f6;
        border: 1px solid #f0ebe8;
        border-radius: 7px;
        padding: 11px 12px 9px;
        margin-bottom: 12px;
    }

    .absensi-home .meeting-field {
        padding: 0 0 11px;
        margin-bottom: 11px;
        border-bottom: 1px solid #e9e5e2;
    }

    .absensi-home .meeting-field:last-of-type {
        border-bottom: none;
        margin-bottom: 5px;
        padding-bottom: 5px;
    }

    .absensi-home .field-label {
        display: block;
        font-size: 10px;
        color: #85817f;
        margin-bottom: 7px;
    }

    .absensi-home .field-value {
        font-size: 12px;
        font-weight: 600;
        color: #333;
        line-height: 1.5;
    }

    /* TOMBOL MULAI ABSENSI */
    .absensi-home .btn-start {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 39px;
        padding: 10px 12px;
        background: #790d20;
        color: #fff;
        border: 1px solid #790d20;
        border-radius: 6px;
        text-decoration: none;
        text-align: center;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        transition: background 0.2s ease;
    }

    .absensi-home .btn-start:hover {
        background: #5e0a19;
    }

    .absensi-home .btn-start:focus-visible {
        outline: 2px solid #790d20;
        outline-offset: 3px;
    }

    /* RESPONSIVE */
    @media (max-width: 480px) {
        .absensi-home {
            padding: 10px 4px 20px;
        }

        .absensi-home .page-title {
            font-size: 21px;
        }

        .absensi-home .page-description {
            font-size: 12px;
        }

        .absensi-home .summary-card {
            padding: 10px;
        }
    }
</style>

<div class="absensi-home">

    {{-- JUDUL DAN DESKRIPSI HALAMAN --}}
    <div class="breadcrumb">
        Dashboard / Absensi
    </div>

    <h1 class="page-title">
        Absensi
    </h1>

    <p class="page-description">
        Pilih mata pelajaran dan pertemuan untuk mencatat kehadiran.
    </p>

    {{-- RINGKASAN ABSENSI --}}
    <section class="summary-grid">

        <div class="summary-card">
            <div class="summary-label">
                Absensi Selesai
            </div>

            <div class="summary-number">
                12
            </div>
        </div>

        <div class="summary-card pending">
            <div class="summary-label">
                Belum Diisi
            </div>

            <div class="summary-number">
                1
            </div>
        </div>

    </section>

    {{-- DAFTAR PERTEMUAN YANG BISA DIISI --}}
    <section>

        <h2 class="section-title">
            Pilih Pertemuan
        </h2>

        <article class="meeting-card">

            {{-- INFORMASI MATA PELAJARAN --}}
            <div class="meeting-field">
                <span class="field-label">
                    Mata Pelajaran
                </span>

                <div class="field-value">
                    Akhlak dan Adab
                </div>
            </div>

            {{-- INFORMASI KELOMPOK --}}
            <div class="meeting-field">
                <span class="field-label">
                    Kelompok
                </span>

                <div class="field-value">
                    Kelompok A
                </div>
            </div>

            {{-- NOMOR PERTEMUAN --}}
            <div class="meeting-field">
                <span class="field-label">
                    Pertemuan
                </span>

                <div class="field-value">
                    Pertemuan ke-8
                </div>
            </div>

            {{-- TANGGAL PERTEMUAN --}}
            <div class="meeting-field">
                <span class="field-label">
                    Tanggal
                </span>

                <div class="field-value">
                    26 Oktober 2026
                </div>
            </div>

            {{-- NAVIGASI KE HALAMAN ISI ABSENSI --}}
            <a href="{{ url('/guru/absensi/isi') }}"
               class="btn-start">
                Mulai Isi Absensi
            </a>

        </article>

    </section>

</div>

@endsection
