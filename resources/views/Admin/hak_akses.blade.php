
@extends('layouts.admin')

@section('content')
<style>
    .hakakses-page {
        padding: 20px 26px;
        color: #171717;
        font-family: inherit;
    }

    .hakakses-breadcrumb {
        color: #790d20;
        font-size: 22px;
        margin-bottom: 30px;
        font-weight: 500;
    }

    .hakakses-title {
        font-size: 32px;
        font-weight: 600;
        margin: 0 0 5px;
    }

    .hakakses-description {
        font-size: 20px;
        margin: 0 0 14px;
    }

    .hakakses-section-title {
        font-size: 20px;
        font-weight: 600;
        color: #444;
        margin-bottom: 17px;
    }

    .hakakses-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .hakakses-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: #fff;
        border: 1px solid #e5ddd8;
        border-radius: 9px;
        padding: 21px 17px;
        color: #333;
        text-decoration: none;
        transition: background .2s, border-color .2s;
    }

    .hakakses-card:hover {
        background: #fffafa;
        border-color: #790d20;
        color: #333;
    }

    .hakakses-role {
        font-size: 19px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .hakakses-info {
        font-size: 17px;
        color: #777;
        margin-bottom: 6px;
    }

    .hakakses-label {
        font-size: 14px;
        color: #790d20;
    }

    .hakakses-arrow {
        font-size: 29px;
        color: #790d20;
        line-height: 1;
    }

    @media (max-width: 768px) {
        .hakakses-page {
            padding: 16px;
        }

        .hakakses-breadcrumb {
            font-size: 18px;
            margin-bottom: 24px;
        }

        .hakakses-title {
            font-size: 27px;
        }

        .hakakses-description {
            font-size: 16px;
        }

        .hakakses-card {
            padding: 18px 14px;
        }

        .hakakses-role {
            font-size: 17px;
        }

        .hakakses-info {
            font-size: 14px;
        }
    }
</style>

<div class="hakakses-page">
    <div class="hakakses-breadcrumb">
        Dashboard / Hak Akses
    </div>

    <h1 class="hakakses-title">Hak Akses</h1>

    <p class="hakakses-description">
        Atur izin berdasarkan peran pengguna.
    </p>

    <div class="hakakses-section-title">
        Daftar Peran
    </div>

    <div class="hakakses-list">
        <a href="{{ url('/admin/hak-akses?peran=admin') }}"
           class="hakakses-card">
            <div>
                <div class="hakakses-role">Admin</div>
                <div class="hakakses-info">
                    Pengelolaan akun dan konfigurasi sistem
                </div>
                <div class="hakakses-label">4 izin utama</div>
            </div>
            <span class="hakakses-arrow">&rsaquo;</span>
        </a>

        <a href="{{ url('/admin/hak-akses?peran=musyrifah') }}"
           class="hakakses-card">
            <div>
                <div class="hakakses-role">Musyrifah/Pengelola</div>
                <div class="hakakses-info">
                    Operasional dan data Ma’had
                </div>
                <div class="hakakses-label">Izin operasional</div>
            </div>
            <span class="hakakses-arrow">&rsaquo;</span>
        </a>

        <a href="{{ url('/admin/hak-akses?peran=guru') }}"
           class="hakakses-card">
            <div>
                <div class="hakakses-role">Guru</div>
                <div class="hakakses-info">
                    Pembelajaran dan absensi sesuai penugasan
                </div>
                <div class="hakakses-label">Izin pengajaran</div>
            </div>
            <span class="hakakses-arrow">&rsaquo;</span>
        </a>

        <a href="{{ url('/admin/hak-akses?peran=mahasiswa') }}"
           class="hakakses-card">
            <div>
                <div class="hakakses-role">Mahasiswa</div>
                <div class="hakakses-info">
                    Akses layanan dan data milik sendiri
                </div>
                <div class="hakakses-label">Izin mahasiswa</div>
            </div>
            <span class="hakakses-arrow">&rsaquo;</span>
        </a>
    </div>
</div>
@endsection