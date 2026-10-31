
@extends('layouts.musyrifah')

@section('title', 'Manajemen Kamar')

@section('content')

<style>
    .data-kamar-page {
        font-family: 'Poppins', Arial, sans-serif;
        color: #111;
        width: 100%;
    }

    .data-kamar-page * {
        box-sizing: border-box;
    }

    /* JUDUL */

    .data-kamar-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 44px;
    }

    .data-kamar-back {
        color: #111;
        text-decoration: none;
        font-size: 34px;
        line-height: 1;
        flex-shrink: 0;
    }

    .data-kamar-back:hover {
        color: #800d22;
    }

    .data-kamar-title {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        line-height: 1.3;
    }

    /* FILTER KAMAR DAN LANTAI */

    .filter-kamar-area {
        display: flex;
        flex-direction: column;
        gap: 48px;
        margin: 0 auto 36px;
        max-width: 680px;
    }

    .filter-kamar-row {
        display: grid;
        grid-template-columns: 90px minmax(0, 430px);
        justify-content: center;
        align-items: center;
        gap: 15px;
    }

    .filter-kamar-row label {
        text-align: right;
        font-size: 20px;
        font-weight: 600;
    }

    .filter-select-wrap {
        position: relative;
        width: 100%;
    }

    .filter-select {
        width: 100%;
        height: 75px;
        padding: 0 60px 0 22px;
        appearance: none;
        -webkit-appearance: none;
        border: 1px solid #111;
        border-radius: 21px;
        background: #800020;
        color: #fff;
        font-family: inherit;
        font-size: 17px;
        cursor: pointer;
        box-shadow: 0 4px 4px rgba(0, 0, 0, .2);
    }

    .filter-select:focus {
        outline: 2px solid #bd8795;
        outline-offset: 3px;
    }

    .filter-select option {
        background: #fff;
        color: #111;
    }

    .filter-select-wrap::after {
        content: '';
        position: absolute;
        top: 50%;
        right: 48px;
        width: 0;
        height: 0;
        border-left: 9px solid transparent;
        border-right: 9px solid transparent;
        border-top: 12px solid #fff;
        transform: translateY(-30%);
        pointer-events: none;
    }

    /* RINGKASAN KAMAR */

    .ringkasan-kamar {
        width: 100%;
        margin-bottom: 36px;
        overflow: hidden;
        border: 1px solid #cdbfc0;
        border-radius: 21px;
        background: #f5e6e9;
        box-shadow: 0 4px 4px rgba(0, 0, 0, .2);
    }

    .ringkasan-item {
        min-height: 47px;
        display: flex;
        align-items: center;
        padding: 8px 28px;
        font-size: 20px;
        border-bottom: 1px solid #c3b9b9;
    }

    .ringkasan-item:last-child {
        border-bottom: none;
    }

    .ringkasan-item:nth-child(2) {
        background: #fff;
    }

    /* DAFTAR KARTU KAMAR */

    .daftar-kamar-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 24px;
        padding: 0 4%;
    }

    .kamar-card {
        min-height: 326px;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 35px 12px 18px;
        border: 1px solid #cdbfc0;
        border-radius: 22px;
        background: #f5e6e9;
        box-shadow: 0 4px 4px rgba(0, 0, 0, .2);
        text-align: center;
    }

    .kamar-nomor {
        font-size: 19px;
        margin-bottom: 20px;
    }

    .kamar-info {
        font-size: 19px;
        line-height: 1.5;
        margin-bottom: 17px;
    }

    .kamar-status {
        margin-top: auto;
        padding-top: 12px;
        font-size: 19px;
        font-weight: 700;
    }

    .kamar-status.tersedia {
        color: #111;
    }

    .kamar-status.penuh {
        color: #111;
    }

    .kamar-card[hidden] {
        display: none !important;
    }

    .kamar-kosong {
        display: none;
        grid-column: 1 / -1;
        padding: 30px;
        color: #777;
        text-align: center;
    }

    /* RESPONSIVE */

    @media (max-width: 1100px) {
        .daftar-kamar-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            padding: 0;
        }

        .kamar-card {
            min-height: 300px;
        }

        .filter-kamar-area {
            gap: 28px;
        }
    }

    @media (max-width: 768px) {
        .data-kamar-heading {
            margin-bottom: 35px;
        }

        .data-kamar-title {
            font-size: 25px;
        }

        .data-kamar-back {
            font-size: 28px;
        }

        .filter-kamar-area {
            gap: 20px;
            margin-bottom: 28px;
        }

        .filter-kamar-row {
            grid-template-columns: 75px minmax(0, 1fr);
            gap: 10px;
        }

        .filter-kamar-row label {
            font-size: 16px;
        }

        .filter-select {
            height: 58px;
            border-radius: 15px;
            padding-left: 15px;
        }

        .filter-select-wrap::after {
            right: 20px;
        }

        .ringkasan-item {
            min-height: 42px;
            padding: 8px 16px;
            font-size: 16px;
        }

        .daftar-kamar-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .kamar-card {
            min-height: 270px;
            padding-top: 25px;
        }

        .kamar-nomor,
        .kamar-info,
        .kamar-status {
            font-size: 16px;
        }
    }

    @media (max-width: 420px) {
        .data-kamar-title {
            font-size: 21px;
        }

        .daftar-kamar-grid {
            gap: 10px;
        }

        .kamar-card {
            min-height: 245px;
            padding: 22px 8px 15px;
        }

        .kamar-nomor,
        .kamar-info,
        .kamar-status {
            font-size: 14px;
        }

        .ringkasan-item {
            font-size: 14px;
        }
    }
</style>

<div class="data-kamar-page">

    <!-- JUDUL HALAMAN -->
    <div class="data-kamar-heading">

        <a
            href="{{ route('musyrifah.dashboard') }}"
            class="data-kamar-back"
            aria-label="Kembali ke Dashboard"
            title="Kembali ke Dashboard"
        >
            ←
        </a>

        <h1 class="data-kamar-title">
            Manajemen Kamar
        </h1>

    </div>

    <!-- FILTER KAMAR DAN LANTAI -->
    <section class="filter-kamar-area">

        <div class="filter-kamar-row">

            <label for="filterNomorKamar">Kamar</label>

            <div class="filter-select-wrap">
                <select id="filterNomorKamar" class="filter-select">
                    <option value="">Semua Kamar</option>

                    @foreach ($daftarKamar as $kamar)
                        <option value="{{ $kamar['nomor'] }}">
                            Kamar {{ $kamar['nomor'] }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="filter-kamar-row">

            <label for="filterLantai">Lantai</label>

            <div class="filter-select-wrap">
                <select id="filterLantai" class="filter-select">
                    <option value="">Semua Lantai</option>
                    <option value="1">Lantai 1</option>
                    <option value="2">Lantai 2</option>
                    <option value="3">Lantai 3</option>
                </select>
            </div>

        </div>

    </section>

    <!-- RINGKASAN JUMLAH KAMAR -->
    <section class="ringkasan-kamar">

        <div class="ringkasan-item">
            Jumlah {{ $jumlahKamar }} Kamar
        </div>

        <div class="ringkasan-item">
            Terisi {{ $kamarTerisi }} Kamar
        </div>

        <div class="ringkasan-item">
            Tersedia {{ $kamarTersedia }} Kamar
        </div>

    </section>

    <!-- DAFTAR KAMAR -->
    <section class="daftar-kamar-grid" id="daftarKamar">

        @forelse ($daftarKamar as $kamar)

            @php
                $statusKamar = $kamar['terisi'] >= $kamar['kapasitas']
                    ? 'Penuh'
                    : 'Tersedia';
            @endphp

            <article
                class="kamar-card"
                data-nomor="{{ $kamar['nomor'] }}"
                data-lantai="{{ $kamar['lantai'] }}"
            >

                <div class="kamar-nomor">
                    No: {{ $kamar['nomor'] }}
                </div>

                <div class="kamar-info">
                    Lantai {{ $kamar['lantai'] }}
                </div>

                <div class="kamar-info">
                    Kapasitas {{ $kamar['kapasitas'] }}
                </div>

                <div class="kamar-info">
                    Terisi {{ $kamar['terisi'] }}
                </div>

                <div class="kamar-status {{ strtolower($statusKamar) }}">
                    {{ $statusKamar }}
                </div>

            </article>

        @empty

            <div class="kamar-kosong" style="display:block">
                Belum ada data kamar.
            </div>

        @endforelse

        <div class="kamar-kosong" id="pesanKamarKosong">
            Kamar tidak ditemukan.
        </div>

    </section>

</div>

<!-- FILTER INTERAKTIF -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterNomor = document.getElementById('filterNomorKamar');
        const filterLantai = document.getElementById('filterLantai');
        const kartuKamar = document.querySelectorAll('.kamar-card');
        const pesanKosong = document.getElementById('pesanKamarKosong');

        function filterKamar() {
            const nomorDipilih = filterNomor.value;
            const lantaiDipilih = filterLantai.value;
            let jumlahTampil = 0;

            kartuKamar.forEach(function (kartu) {
                const cocokNomor =
                    nomorDipilih === '' ||
                    kartu.dataset.nomor === nomorDipilih;

                const cocokLantai =
                    lantaiDipilih === '' ||
                    kartu.dataset.lantai === lantaiDipilih;

                const tampil = cocokNomor && cocokLantai;

                kartu.hidden = !tampil;

                if (tampil) {
                    jumlahTampil++;
                }
            });

            pesanKosong.style.display =
                jumlahTampil === 0 ? 'block' : 'none';
        }

        filterNomor.addEventListener('change', function () {
            // Jika kamar tertentu dipilih, sesuaikan lantainya.
            if (this.value !== '') {
                const kamarTerpilih = Array.from(kartuKamar).find(
                    kartu => kartu.dataset.nomor === this.value
                );

                if (kamarTerpilih) {
                    filterLantai.value = kamarTerpilih.dataset.lantai;
                }
            }

            filterKamar();
        });

        filterLantai.addEventListener('change', function () {
            // Jika lantai berubah, reset pilihan kamar.
            filterNomor.value = '';
            filterKamar();
        });

        filterKamar();
    });
</script>

@endsection