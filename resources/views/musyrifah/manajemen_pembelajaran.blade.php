
@extends('layouts.musyrifah')

@section('title', 'Manajemen Pembelajaran')

@section('content')

<style>
    .musyrifah-pembelajaran {
        width: 100%;
        color: #111;
        font-family: 'Poppins', Arial, sans-serif;
    }

    .musyrifah-pembelajaran * {
        box-sizing: border-box;
    }

    /* JUDUL */

    .mp-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 120px;
    }

    .mp-back {
        color: #111;
        font-size: 34px;
        line-height: 1;
        text-decoration: none;
        flex-shrink: 0;
    }

    .mp-back:hover {
        color: #800d22;
    }

    .mp-title {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        line-height: 1.3;
    }

    /* KARTU RINGKASAN */

    .mp-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 292px));
        justify-content: start;
        gap: 12px 43px;
        margin-left: 7px;
        margin-bottom: 43px;
    }

    .mp-summary-card {
        min-height: 194px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 20px;
        background: #800020;
        color: #fff;
        border: 1px solid #e2b900;
        box-shadow: 0 3px 4px rgba(0, 0, 0, .2);
        text-align: center;
    }

    .mp-summary-card:nth-child(2),
    .mp-summary-card:nth-child(4) {
        background: #650019;
    }

    .mp-summary-label {
        font-size: 23px;
        font-weight: 600;
        line-height: 1.5;
    }

    .mp-summary-value {
        font-size: 24px;
        font-weight: 600;
        line-height: 1.4;
    }

    /* MENU DROPDOWN */

    .mp-menu-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 54px 60px;
    }

    .mp-menu-item {
        min-width: 0;
    }

    .mp-menu-button {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        width: 100%;
        min-height: 44px;
        padding: 8px 20px;
        border: 1px solid #e2b900;
        border-radius: 24px;
        background: #800020;
        color: #fff;
        box-shadow: 0 3px 4px rgba(0, 0, 0, .2);
        font-family: inherit;
        font-size: 19px;
        font-weight: 600;
        text-align: left;
        cursor: pointer;
        transition: background .2s ease;
    }

    .mp-menu-button:hover,
    .mp-menu-button[aria-expanded="true"] {
        background: #650019;
    }

    .mp-arrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 25px;
        height: 25px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #fff;
        color: #111;
        font-size: 15px;
        transition: transform .2s ease;
    }

    .mp-menu-button[aria-expanded="true"] .mp-arrow {
        transform: rotate(180deg);
    }

    .mp-dropdown-panel {
        display: none;
        margin-top: 12px;
        padding: 18px 20px;
        border: 1px solid #e4d2d5;
        border-radius: 14px;
        background: #f5e6e9;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .08);
        font-size: 15px;
        line-height: 1.6;
    }

    .mp-dropdown-panel.is-open {
        display: block;
    }

    .mp-dropdown-panel p {
        margin: 0;
    }

    .mp-dropdown-panel strong {
        display: block;
        margin-bottom: 5px;
        color: #650019;
    }

    /* RESPONSIVE */

    @media (max-width: 1100px) {
        .mp-heading {
            margin-bottom: 70px;
        }

        .mp-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 24px;
            margin-left: 0;
        }

        .mp-summary-card {
            min-height: 160px;
        }

        .mp-menu-grid {
            gap: 28px 25px;
        }
    }

    @media (max-width: 768px) {
        .mp-heading {
            margin-bottom: 45px;
        }

        .mp-title {
            font-size: 25px;
        }

        .mp-back {
            font-size: 28px;
        }

        .mp-summary-grid {
            gap: 12px;
            margin-bottom: 30px;
        }

        .mp-summary-card {
            min-height: 130px;
            padding: 12px;
        }

        .mp-summary-label,
        .mp-summary-value {
            font-size: 18px;
        }

        .mp-menu-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .mp-menu-button {
            font-size: 16px;
        }

        .mp-dropdown-panel {
            font-size: 14px;
        }
    }

    @media (max-width: 420px) {
        .mp-title {
            font-size: 21px;
        }

        .mp-summary-card {
            min-height: 110px;
        }

        .mp-summary-label,
        .mp-summary-value {
            font-size: 15px;
        }
    }
</style>

<div class="musyrifah-pembelajaran">

    <!-- JUDUL HALAMAN -->
    <div class="mp-heading">

        <a
            href="{{ route('musyrifah.dashboard') }}"
            class="mp-back"
            title="Kembali ke Dashboard"
            aria-label="Kembali ke Dashboard"
        >
            ←
        </a>

        <h1 class="mp-title">Manajemen Pembelajaran</h1>

    </div>

    <!-- KARTU RINGKASAN -->
    <section class="mp-summary-grid">

        <div class="mp-summary-card">
            <div class="mp-summary-label">Mata Pelajaran</div>
            <div class="mp-summary-value">
                {{ $jumlahMataPelajaran }}
            </div>
        </div>

        <div class="mp-summary-card">
            <div class="mp-summary-label">Pengajar</div>
            <div class="mp-summary-value">
                {{ $jumlahPengajar }}
            </div>
        </div>

        <div class="mp-summary-card" aria-label="Ringkasan pembelajaran">
            <!-- Mengikuti kartu ketiga pada desain referensi -->
        </div>

        <div class="mp-summary-card">
            <div class="mp-summary-label">Pertemuan</div>
            <div class="mp-summary-value">
                {{ $jumlahPertemuanTerlaksana }}/{{ $targetPertemuan }}
            </div>
        </div>

    </section>

    <!-- MENU MANAJEMEN PEMBELAJARAN -->
    <section class="mp-menu-grid">

        <!-- MATA PELAJARAN -->
        <div class="mp-menu-item">

            <button
                type="button"
                class="mp-menu-button"
                aria-expanded="false"
                aria-controls="panelMataPelajaran"
            >
                <span>Mata Pelajaran</span>
                <span class="mp-arrow" aria-hidden="true">▽</span>
            </button>

            <div
                class="mp-dropdown-panel"
                id="panelMataPelajaran"
            >
                <strong>Data Mata Pelajaran</strong>
                <p>
                    Jumlah mata pelajaran saat ini:
                    {{ $jumlahMataPelajaran }}.
                    Pengelolaan daftar mata pelajaran dapat dilanjutkan
                    pada halaman khusus.
                </p>
            </div>

        </div>

        <!-- JADWAL -->
        <div class="mp-menu-item">

            <button
                type="button"
                class="mp-menu-button"
                aria-expanded="false"
                aria-controls="panelJadwal"
            >
                <span>Jadwal</span>
                <span class="mp-arrow" aria-hidden="true">▽</span>
            </button>

            <div
                class="mp-dropdown-panel"
                id="panelJadwal"
            >
                <strong>Jadwal Pembelajaran</strong>
                <p>
                    Bagian ini disiapkan untuk melihat jadwal
                    pembelajaran dan waktu pelaksanaan setiap kelas.
                </p>
            </div>

        </div>

        <!-- PENGAJAR -->
        <div class="mp-menu-item">

            <button
                type="button"
                class="mp-menu-button"
                aria-expanded="false"
                aria-controls="panelPengajar"
            >
                <span>Pengajar</span>
                <span class="mp-arrow" aria-hidden="true">▽</span>
            </button>

            <div
                class="mp-dropdown-panel"
                id="panelPengajar"
            >
                <strong>Data Pengajar</strong>
                <p>
                    Jumlah pengajar saat ini:
                    {{ $jumlahPengajar }}.
                    Daftar pengajar dapat ditampilkan pada halaman
                    pengelolaan berikutnya.
                </p>
            </div>

        </div>

        <!-- KEHADIRAN -->
        <div class="mp-menu-item">

            <button
                type="button"
                class="mp-menu-button"
                aria-expanded="false"
                aria-controls="panelKehadiran"
            >
                <span>Kehadiran</span>
                <span class="mp-arrow" aria-hidden="true">▽</span>
            </button>

            <div
                class="mp-dropdown-panel"
                id="panelKehadiran"
            >
                <strong>Kehadiran Pembelajaran</strong>
                <p>
                    Bagian ini disiapkan untuk memantau kehadiran
                    mahasiswa dan pengajar pada setiap pertemuan.
                </p>
            </div>

        </div>

        <!-- KELOMPOK / KELAS -->
        <div class="mp-menu-item">

            <button
                type="button"
                class="mp-menu-button"
                aria-expanded="false"
                aria-controls="panelKelompok"
            >
                <span>Kelompok / Kelas</span>
                <span class="mp-arrow" aria-hidden="true">▽</span>
            </button>

            <div
                class="mp-dropdown-panel"
                id="panelKelompok"
            >
                <strong>Kelompok dan Kelas</strong>
                <p>
                    Bagian ini disiapkan untuk melihat pembagian
                    mahasiswa berdasarkan kelompok atau kelas belajar.
                </p>
            </div>

        </div>

        <!-- KELULUSAN -->
        <div class="mp-menu-item">

            <button
                type="button"
                class="mp-menu-button"
                aria-expanded="false"
                aria-controls="panelKelulusan"
            >
                <span>Kelulusan</span>
                <span class="mp-arrow" aria-hidden="true">▽</span>
            </button>

            <div
                class="mp-dropdown-panel"
                id="panelKelulusan"
            >
                <strong>Kelulusan Pembelajaran</strong>
                <p>
                    Bagian ini disiapkan untuk memantau hasil
                    pembelajaran dan status kelulusan mahasiswa.
                </p>
            </div>

        </div>

    </section>

</div>

<!-- INTERAKSI DROPDOWN -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tombolMenu = document.querySelectorAll(
            '.musyrifah-pembelajaran .mp-menu-button'
        );

        tombolMenu.forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                const panelId = this.getAttribute('aria-controls');
                const panel = document.getElementById(panelId);
                const sedangTerbuka =
                    this.getAttribute('aria-expanded') === 'true';

                // Tutup semua panel sebelum membuka pilihan baru.
                tombolMenu.forEach(function (tombolLain) {
                    tombolLain.setAttribute('aria-expanded', 'false');

                    const idPanelLain =
                        tombolLain.getAttribute('aria-controls');

                    const panelLain =
                        document.getElementById(idPanelLain);

                    if (panelLain) {
                        panelLain.classList.remove('is-open');
                    }
                });

                // Jika sebelumnya tertutup, buka panel yang diklik.
                if (!sedangTerbuka && panel) {
                    this.setAttribute('aria-expanded', 'true');
                    panel.classList.add('is-open');
                }
            });
        });
    });
</script>

@endsection