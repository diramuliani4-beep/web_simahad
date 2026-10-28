
@extends('layouts.admin')

@section('content')
<style>
    .pengaturan-page {
        padding: 20px 26px;
        color: #171717;
        font-family: inherit;
    }

    .pengaturan-breadcrumb {
        color: #790d20;
        font-size: 22px;
        font-weight: 500;
        margin-bottom: 30px;
    }

    .pengaturan-title {
        font-size: 32px;
        font-weight: 600;
        margin: 0 0 5px;
    }

    .pengaturan-description {
        font-size: 20px;
        margin: 0 0 25px;
    }

    .pengaturan-card {
        background: #fff;
        border: 1px solid #e7ded8;
        border-radius: 10px;
        padding: 20px 16px;
        margin-bottom: 14px;
    }

    .pengaturan-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 19px;
        font-weight: 600;
        color: #444;
        margin-bottom: 20px;
    }

    .pengaturan-card-title svg {
        width: 22px;
        height: 22px;
        color: #790d20;
        flex-shrink: 0;
    }

    .pengaturan-field {
        margin-bottom: 18px;
    }

    .pengaturan-field:last-child {
        margin-bottom: 0;
    }

    .pengaturan-field label {
        display: block;
        color: #666;
        font-size: 17px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .pengaturan-input {
        display: block;
        width: 100%;
        box-sizing: border-box;
        border: none;
        border-radius: 9px;
        background: #f7f6f4;
        padding: 15px 11px;
        font: inherit;
        font-size: 18px;
        color: #444;
        outline-color: #790d20;
    }

    .pengaturan-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .pengaturan-row:last-child {
        margin-bottom: 0;
    }

    .pengaturan-row-title {
        font-size: 17px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .pengaturan-row-description {
        font-size: 14px;
        color: #858585;
    }

    .pengaturan-status {
        display: inline-block;
        padding: 5px 11px;
        border-radius: 20px;
        background: #d8f6e4;
        color: #168447;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
    }

    .pengaturan-value {
        color: #790d20;
        font-size: 16px;
        font-weight: 500;
        white-space: nowrap;
    }

    .pengaturan-save {
        display: block;
        width: 100%;
        padding: 19px 15px;
        border: 1px solid #790d20;
        border-radius: 9px;
        background: #790d20;
        color: white;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }

    .pengaturan-save:hover {
        background: #5f0a19;
    }

    @media (max-width: 768px) {
        .pengaturan-page {
            padding: 16px;
        }

        .pengaturan-breadcrumb {
            font-size: 18px;
            margin-bottom: 24px;
        }

        .pengaturan-title {
            font-size: 27px;
        }

        .pengaturan-description {
            font-size: 16px;
        }

        .pengaturan-card {
            padding: 17px 13px;
        }

        .pengaturan-row-title {
            font-size: 15px;
        }

        .pengaturan-row-description {
            font-size: 12px;
        }
    }
</style>

<div class="pengaturan-page">

    <div class="pengaturan-breadcrumb">
        Dashboard / Pengaturan Sistem
    </div>

    <h1 class="pengaturan-title">Pengaturan Sistem</h1>

    <p class="pengaturan-description">
        Konfigurasi umum aplikasi SIMAHAD.
    </p>

    {{-- INFORMASI APLIKASI --}}
    <section class="pengaturan-card">
        <div class="pengaturan-card-title">
            <svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="16" rx="2"/>
                <path d="M3 9h18"/>
            </svg>
            <span>Informasi Aplikasi</span>
        </div>

        <div class="pengaturan-field">
            <label for="nama_sistem">Nama Sistem</label>
            <input
                type="text"
                id="nama_sistem"
                name="nama_sistem"
                class="pengaturan-input"
                value="SIMAHAD"
            >
        </div>

        <div class="pengaturan-field">
            <label for="nama_institusi">Nama Institusi</label>
            <input
                type="text"
                id="nama_institusi"
                name="nama_institusi"
                class="pengaturan-input"
                value="Ma’had Al-Jami’ah Tuanku Imam Bonjol"
            >
        </div>
    </section>

    {{-- NOTIFIKASI --}}
    <section class="pengaturan-card">
        <div class="pengaturan-card-title">
            <svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                <path d="M10 21h4"/>
            </svg>
            <span>Notifikasi</span>
        </div>

        <div class="pengaturan-row">
            <div>
                <div class="pengaturan-row-title">
                    Notifikasi pengumuman sistem
                </div>
                <div class="pengaturan-row-description">
                    Aktifkan notifikasi untuk aktivitas penting.
                </div>
            </div>
            <span class="pengaturan-status">Aktif</span>
        </div>

        <div class="pengaturan-row">
            <div>
                <div class="pengaturan-row-title">
                    Pemberitahuan keamanan
                </div>
                <div class="pengaturan-row-description">
                    Pemberitahuan aktivitas akun yang penting.
                </div>
            </div>
            <span class="pengaturan-status">Aktif</span>
        </div>
    </section>

    {{-- KEAMANAN SISTEM --}}
    <section class="pengaturan-card">
        <div class="pengaturan-card-title">
            <svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
            <span>Keamanan Sistem</span>
        </div>

        <p style="color:#777; font-size:17px; margin:0 0 20px;">
            Pengaturan keamanan akun dan sesi masuk.
        </p>

        <div class="pengaturan-row">
            <div class="pengaturan-row-title">
                Durasi sesi
            </div>
            <span class="pengaturan-value">30 menit (contoh)</span>
        </div>

        <div class="pengaturan-row">
            <div class="pengaturan-row-title">
                Verifikasi email
            </div>
            <span class="pengaturan-status">Aktif</span>
        </div>
    </section>

    <button type="button" class="pengaturan-save"
        onclick="alert('Tampilan pengaturan sudah siap. Fitur penyimpanan perlu dihubungkan ke controller dan database.')">
        Simpan Pengaturan
    </button>

</div>
@endsection