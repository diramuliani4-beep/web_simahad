
@extends('layouts.musyrifah')

@section('title', 'Pengaturan Akun Musyrifah')

@section('content')

<style>
    .akun-page {
        width: 100%;
        min-height: calc(100vh - 125px);
        color: #222;
    }

    /* JUDUL HALAMAN */
    .akun-header {
        margin: -35px -35px 22px;
        padding: 30px 45px 14px;
        border-bottom: 1px solid #800d22;
        background: #f8f8f8;
    }

    .akun-breadcrumb {
        margin-bottom: 12px;
        color: #800d22;
        font-size: 20px;
        font-weight: 500;
    }

    .akun-header h1 {
        margin-bottom: 5px;
        color: #080808;
        font-size: 32px;
        font-weight: 700;
    }

    .akun-subtitle {
        font-size: 20px;
        line-height: 1.5;
    }

    /* DAFTAR PENGATURAN */
    .akun-sections {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 0;
    }

    .akun-card {
        overflow: hidden;
        border: 1px solid #eadfd9;
        border-radius: 12px;
        background: #fff;
    }

    .akun-card-link {
        display: flex;
        align-items: center;
        gap: 16px;
        width: 100%;
        padding: 22px 16px;
        border: none;
        background: #fff;
        color: inherit;
        font-family: inherit;
        text-align: left;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s;
    }

    .akun-card-link:hover {
        background: #fffafb;
    }

    .akun-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 59px;
        height: 59px;
        flex-shrink: 0;
        border-radius: 13px;
        background: #f9edef;
        color: #800d22;
    }

    .akun-icon svg {
        width: 26px;
        height: 26px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .akun-info {
        flex: 1;
        min-width: 0;
    }

    .akun-info h2 {
        margin-bottom: 9px;
        color: #353535;
        font-size: 20px;
        font-weight: 600;
    }

    .akun-info p {
        color: #85807d;
        font-size: 18px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .akun-info .akun-email {
        margin-top: 5px;
        color: #444;
        font-size: 17px;
    }

    .akun-arrow {
        width: 23px;
        height: 23px;
        flex-shrink: 0;
        color: #800d22;
    }

    .akun-arrow svg {
        width: 100%;
        height: 100%;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* KEAMANAN AKUN */
    .keamanan-heading {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 16px;
    }

    .keamanan-heading .akun-info h2 {
        margin-bottom: 8px;
    }

    .keamanan-divider {
        margin: 0 16px;
        border: none;
        border-top: 1px solid #e4dcd8;
    }

    .ubah-password {
        display: flex;
        align-items: center;
        gap: 16px;
        width: 100%;
        padding: 23px 16px 18px;
        border: none;
        background: #fff;
        color: inherit;
        font-family: inherit;
        text-align: left;
        cursor: pointer;
    }

    .ubah-password:hover {
        background: #fffafb;
    }

    .password-icon {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
        color: #800d22;
    }

    .password-icon svg {
        width: 100%;
        height: 100%;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* TENTANG AKUN */
    .tentang-card .akun-card-link {
        padding-top: 20px;
        padding-bottom: 20px;
        cursor: default;
    }

    .tentang-card .akun-card-link:hover {
        background: #fff;
    }

    .tentang-card .akun-info h2 {
        margin-bottom: 8px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .akun-header {
            margin: -20px -15px 20px;
            padding: 23px 20px 15px;
        }

        .akun-breadcrumb {
            font-size: 16px;
        }

        .akun-header h1 {
            font-size: 25px;
        }

        .akun-subtitle {
            font-size: 15px;
        }

        .akun-sections {
            gap: 14px;
        }

        .akun-card-link {
            gap: 12px;
            padding: 17px 12px;
        }

        .akun-icon {
            width: 46px;
            height: 46px;
        }

        .akun-icon svg {
            width: 23px;
            height: 23px;
        }

        .akun-info h2 {
            font-size: 16px;
        }

        .akun-info p,
        .akun-info .akun-email {
            font-size: 14px;
        }

        .keamanan-heading {
            gap: 12px;
            padding: 17px 12px;
        }

        .ubah-password {
            gap: 12px;
            padding: 18px 12px;
        }
    }
</style>

<div class="akun-page">

    {{-- HEADER HALAMAN --}}
    <header class="akun-header">
        <div class="akun-breadcrumb">
            Dashboard / Pengaturan Akun
        </div>

        <h1>Pengaturan Akun</h1>

        <p class="akun-subtitle">
            Kelola informasi akun dan keamanan akun SIMAHAD kamu.
        </p>
    </header>

    <section class="akun-sections">

        {{-- INFORMASI AKUN --}}
        <article class="akun-card">
            <div class="akun-card-link">

                <div class="akun-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 21v-2a8 8 0 0 1 16 0v2"></path>
                    </svg>
                </div>

                <div class="akun-info">
                    <h2>Informasi Akun</h2>

                    <p>Email yang digunakan untuk masuk</p>

                    <p class="akun-email">
                        {{ $user->email ?? 'Email belum tersedia' }}
                    </p>
                </div>

                <span class="akun-arrow">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </span>

            </div>
        </article>

        {{-- KEAMANAN AKUN --}}
        <article class="akun-card">

            <div class="keamanan-heading">

                <div class="akun-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"></path>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                </div>

                <div class="akun-info">
                    <h2>Keamanan Akun</h2>
                    <p>Lindungi akun dan kata sandimu</p>
                </div>

            </div>

            <hr class="keamanan-divider">

            <button
                type="button"
                class="ubah-password"
                onclick="alert('Fitur ubah kata sandi akan dibuat pada tahap berikutnya.')"
            >

                <span class="password-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="8" cy="15" r="4"></circle>
                        <path d="m11 12 8-8 3 3-2 2 2 2-3 3-2-2-3 3"></path>
                    </svg>
                </span>

                <div class="akun-info">
                    <h2>Ubah Kata Sandi</h2>
                    <p>Perbarui kata sandi akunmu secara berkala.</p>
                </div>

                <span class="akun-arrow">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </span>

            </button>

        </article>

        {{-- TENTANG AKUN --}}
        <article class="akun-card tentang-card">

            <div class="akun-card-link">

                <div class="akun-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 11v5"></path>
                        <path d="M12 8h.01"></path>
                    </svg>
                </div>

                <div class="akun-info">
                    <h2>Tentang Akun</h2>

                    <p>
                        Sistem Informasi Manajemen Mahasiswa
                        Ma’had Al-Jami’ah Tuanku Imam Bonjol.
                    </p>
                </div>

            </div>

        </article>

    </section>

</div>

@endsection