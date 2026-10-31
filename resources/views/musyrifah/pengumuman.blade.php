
@extends('layouts.musyrifah')

@section('content')
<style>
    .pengumuman-page {
        padding: 28px 30px;
        color: #171717;
        font-family: inherit;
    }

    .pengumuman-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin: 0 0 65px;
    }

    .pengumuman-header h1 {
        margin: 0 0 8px;
        font-size: 32px;
        font-weight: 700;
    }

    .pengumuman-header p {
        margin: 0;
        color: #858585;
        font-size: 20px;
    }

    .btn-tambah-pengumuman {
        background: #790d20;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 17px 45px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-tambah-pengumuman:hover {
        background: #5f0918;
    }

    .pengumuman-label {
        display: block;
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 9px 18px;
    }

    .pengumuman-search {
        display: flex;
        align-items: center;
        gap: 20px;
        background: #f5e9ec;
        border-radius: 20px;
        padding: 13px 16px;
        margin-bottom: 74px;
    }

    .pengumuman-search-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        background: white;
        border-radius: 50%;
        font-size: 24px;
    }

    .pengumuman-search input {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font-size: 19px;
        padding: 8px;
    }

    .pengumuman-search input::placeholder {
        color: #c9c0c2;
    }

    .daftar-pengumuman {
        background: #f4e6e9;
        border: 1px solid #333;
        border-radius: 20px;
        box-shadow: 0 3px 4px #00000025;
        padding: 0 7px;
        margin-bottom: 55px;
        overflow: hidden;
    }

    .pengumuman-item {
        padding: 8px 10px 24px;
        border-bottom: 1px solid #8a8082;
    }

    .pengumuman-item:last-child {
        border-bottom: none;
    }

    .pengumuman-item h3 {
        font-size: 20px;
        font-weight: 400;
        margin: 0 0 10px;
    }

    .pengumuman-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 28px;
        color: #858080;
        font-size: 18px;
    }

    .pengumuman-statistik {
        display: grid;
        grid-template-columns: 1fr 1.5fr 1.35fr;
        background: #f4e6e9;
        border: 1px solid #333;
        border-radius: 20px;
        box-shadow: 0 3px 4px #00000025;
        overflow: hidden;
    }

    .statistik-kolom {
        text-align: center;
        min-width: 0;
    }

    .statistik-kolom + .statistik-kolom {
        border-left: 1px solid #8a8082;
    }

    .statistik-judul {
        padding: 19px 8px;
        border-bottom: 1px solid #8a8082;
        font-size: 20px;
        font-weight: 700;
    }

    .statistik-angka {
        padding: 33px 8px;
        font-size: 18px;
        font-weight: 700;
    }

    .pengumuman-kosong {
        padding: 25px 15px;
        text-align: center;
        color: #777;
        display: none;
    }

    @media (max-width: 768px) {
        .pengumuman-page {
            padding: 20px 12px;
        }

        .pengumuman-header {
            margin-bottom: 40px;
            flex-direction: column;
        }

        .pengumuman-header h1 {
            font-size: 27px;
        }

        .pengumuman-header p {
            font-size: 16px;
        }

        .btn-tambah-pengumuman {
            padding: 12px 25px;
        }

        .pengumuman-statistik {
            grid-template-columns: 1fr;
        }

        .statistik-kolom + .statistik-kolom {
            border-left: none;
            border-top: 1px solid #8a8082;
        }

        .statistik-angka {
            padding: 20px 8px;
        }
    }
</style>

<div class="pengumuman-page">

<div class="pengumuman-header">
    <div>
        <h1>Pengumuman</h1>
        <p>Kelola Informasi Untuk Mahasantri</p>
    </div>

    <a href="{{ route('musyrifah.detail-pengumuman') }}"
       class="btn-tambah-pengumuman"
       style="display: inline-block; text-decoration: none; text-align: center;">
        Tambah
    </a>
</div>

    <div>
        <label for="cariPengumuman" class="pengumuman-label">
            Cari Pengumuman
        </label>

        <div class="pengumuman-search">
            <span class="pengumuman-search-icon">&#9906;</span>

            <input
                type="text"
                id="cariPengumuman"
                placeholder="Cari Judul Pengumuman"
                autocomplete="off"
            >
        </div>
    </div>

    <div>
        <h2 class="pengumuman-label">Daftar Pengumuman</h2>

        <div class="daftar-pengumuman" id="daftarPengumuman">

            <div class="pengumuman-item" data-judul="jadwal kegiatan mahasantri">
                <h3>Jadwal Kegiatan Mahasantri</h3>
                <div class="pengumuman-meta">
                    <span>28/Oktober/2026</span>
                    <span>Akademik</span>
                </div>
            </div>

            <div class="pengumuman-item" data-judul="informasi tata tertib asrama">
                <h3>Informasi Tata Tertib Asrama</h3>
                <div class="pengumuman-meta">
                    <span>28/Oktober/2026</span>
                    <span>Asrama</span>
                </div>
            </div>

            <div class="pengumuman-kosong" id="pengumumanKosong">
                Pengumuman tidak ditemukan.
            </div>

        </div>
    </div>

    <div class="pengumuman-statistik">

        <div class="statistik-kolom">
            <div class="statistik-judul">Total</div>
            <div class="statistik-angka">12 Pengumuman</div>
        </div>

        <div class="statistik-kolom">
            <div class="statistik-judul">Terbit</div>
            <div class="statistik-angka">9 Dipublikasikan</div>
        </div>

        <div class="statistik-kolom">
            <div class="statistik-judul">Draft</div>
            <div class="statistik-angka">3 Belum Terbit</div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('cariPengumuman');
        const items = document.querySelectorAll(
            '#daftarPengumuman .pengumuman-item'
        );
        const kosong = document.getElementById('pengumumanKosong');

        input.addEventListener('input', function () {
            const kataKunci = this.value.toLowerCase().trim();
            let jumlahTampil = 0;

            items.forEach(function (item) {
                const judul = item.dataset.judul.toLowerCase();
                const cocok = judul.includes(kataKunci);

                item.style.display = cocok ? 'block' : 'none';

                if (cocok) {
                    jumlahTampil++;
                }
            });

            kosong.style.display = jumlahTampil === 0 ? 'block' : 'none';
        });
    });
</script>
@endsection