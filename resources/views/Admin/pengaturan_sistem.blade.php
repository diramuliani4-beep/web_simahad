@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
<style>
    .pengaturan-page {
        padding: 20px 26px;
        color: #171717;
        font-family: inherit;
        max-width: 980px;
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

    .pengaturan-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        border-radius: 9px;
        padding: 14px 16px;
        font-size: 16px;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .pengaturan-alert--sukses {
        background: #e7f7ee;
        border: 1px solid #b6e6cc;
        color: #14683c;
    }

    .pengaturan-alert--gagal {
        background: #fdeceb;
        border: 1px solid #f3c3c0;
        color: #9c2018;
    }

    .pengaturan-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
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
        border: 1px solid transparent;
        border-radius: 9px;
        background: #f7f6f4;
        padding: 15px 11px;
        font: inherit;
        font-size: 18px;
        color: #444;
        outline-color: #790d20;
    }

    .pengaturan-input:focus {
        border-color: #790d20;
    }

    .pengaturan-input--salah {
        border-color: #c0392b;
        background: #fdf6f5;
    }

    .pengaturan-input--pendek {
        max-width: 180px;
    }

    .pengaturan-error {
        display: block;
        color: #b3261e;
        font-size: 14px;
        margin-top: 6px;
    }

    .pengaturan-hint {
        color: #858585;
        font-size: 14px;
        margin-top: 6px;
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

    /* Sakelar notifikasi */
    .pengaturan-switch {
        position: relative;
        display: inline-block;
        width: 54px;
        height: 30px;
        flex-shrink: 0;
        cursor: pointer;
    }

    .pengaturan-switch input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        cursor: pointer;
    }

    .pengaturan-slider {
        position: absolute;
        inset: 0;
        background: #d9d2cd;
        border-radius: 999px;
        transition: background .2s;
    }

    .pengaturan-slider::before {
        content: "";
        position: absolute;
        width: 24px;
        height: 24px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .25);
        transition: transform .2s;
    }

    .pengaturan-switch input:checked + .pengaturan-slider {
        background: #790d20;
    }

    .pengaturan-switch input:checked + .pengaturan-slider::before {
        transform: translateX(24px);
    }

    .pengaturan-switch input:focus-visible + .pengaturan-slider {
        outline: 2px solid #790d20;
        outline-offset: 2px;
    }

    .pengaturan-sr {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
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

    .pengaturan-catatan {
        color: #858585;
        font-size: 14px;
        margin: 12px 2px 0;
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
        Konfigurasi umum aplikasi {{ $namaSistem ?? 'SIMAHAD' }}. Perubahan langsung diterapkan setelah disimpan.
    </p>

    @if (session('success'))
        <div class="pengaturan-alert pengaturan-alert--sukses" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="pengaturan-alert pengaturan-alert--gagal" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="pengaturan-alert pengaturan-alert--gagal" role="alert">
            <div>
                <strong>Pengaturan belum tersimpan.</strong>
                <ul>
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.pengaturan-sistem.update') }}">
        @csrf
        @method('PUT')

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
                    class="pengaturan-input @error('nama_sistem') pengaturan-input--salah @enderror"
                    value="{{ old('nama_sistem', $pengaturan->nama_sistem) }}"
                    maxlength="60"
                    required
                >
                @error('nama_sistem')
                    <span class="pengaturan-error">{{ $message }}</span>
                @enderror
                <span class="pengaturan-hint">
                    Nama ini dipakai pada judul halaman dan tulisan SIMAHAD di header aplikasi.
                </span>
            </div>

            <div class="pengaturan-field">
                <label for="nama_institusi">Nama Institusi</label>
                <input
                    type="text"
                    id="nama_institusi"
                    name="nama_institusi"
                    class="pengaturan-input @error('nama_institusi') pengaturan-input--salah @enderror"
                    value="{{ old('nama_institusi', $pengaturan->nama_institusi) }}"
                    maxlength="150"
                    required
                >
                @error('nama_institusi')
                    <span class="pengaturan-error">{{ $message }}</span>
                @enderror
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

            @php
                $daftarNotifikasi = [
                    [
                        'kunci' => 'notifikasi_pengumuman',
                        'judul' => 'Notifikasi pengumuman',
                        'deskripsi' => 'Kirim pemberitahuan saat musyrifah menerbitkan pengumuman baru.',
                    ],
                    [
                        'kunci' => 'notifikasi_pembelajaran',
                        'judul' => 'Notifikasi pembelajaran',
                        'deskripsi' => 'Pemberitahuan tugas baru, tenggat, nilai, dan perubahan jadwal.',
                    ],
                    [
                        'kunci' => 'notifikasi_pembayaran',
                        'judul' => 'Notifikasi pembayaran',
                        'deskripsi' => 'Pengingat tagihan dan jatuh tempo pembayaran Ma’had.',
                    ],
                ];
            @endphp

            @foreach ($daftarNotifikasi as $notifikasi)
                <div class="pengaturan-row">
                    <div>
                        <div class="pengaturan-row-title">
                            {{ $notifikasi['judul'] }}
                        </div>
                        <div class="pengaturan-row-description">
                            {{ $notifikasi['deskripsi'] }}
                        </div>
                    </div>

                    <input type="hidden" name="{{ $notifikasi['kunci'] }}" value="0">

                    <label class="pengaturan-switch" for="{{ $notifikasi['kunci'] }}">
                        <input
                            type="checkbox"
                            id="{{ $notifikasi['kunci'] }}"
                            name="{{ $notifikasi['kunci'] }}"
                            value="1"
                            @checked(old($notifikasi['kunci'], $pengaturan->{$notifikasi['kunci']}))
                        >
                        <span class="pengaturan-slider" aria-hidden="true"></span>
                        <span class="pengaturan-sr">Aktifkan {{ $notifikasi['judul'] }}</span>
                    </label>
                </div>
            @endforeach
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

            <div class="pengaturan-field">
                <label for="durasi_sesi">Durasi Sesi Login (menit)</label>
                <input
                    type="number"
                    id="durasi_sesi"
                    name="durasi_sesi"
                    class="pengaturan-input pengaturan-input--pendek @error('durasi_sesi') pengaturan-input--salah @enderror"
                    value="{{ old('durasi_sesi', $pengaturan->durasi_sesi) }}"
                    min="5"
                    max="1440"
                    step="1"
                    required
                >
                @error('durasi_sesi')
                    <span class="pengaturan-error">{{ $message }}</span>
                @enderror
                <span class="pengaturan-hint">
                    Pengguna akan diminta masuk kembali setelah sesi tidak aktif selama durasi ini (5–1440 menit).
                </span>
            </div>

            <div class="pengaturan-row">
                <div>
                    <div class="pengaturan-row-title">Verifikasi email login Google</div>
                    <div class="pengaturan-row-description">
                        Selalu aktif: email Google harus terverifikasi sebelum dapat masuk.
                    </div>
                </div>
                <span class="pengaturan-status">Aktif</span>
            </div>
        </section>

        <button type="submit" class="pengaturan-save">
            Simpan Pengaturan
        </button>

        <p class="pengaturan-catatan">
            Terakhir diperbarui:
            {{ $pengaturan->updated_at?->locale('id')->translatedFormat('d F Y, H:i') ?? 'belum pernah' }} WIB
        </p>
    </form>

</div>
@endsection
