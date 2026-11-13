@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@push('styles')
<style>
    /* Semua class diprefix ps- agar tidak mengubah halaman Admin lainnya. */
    .ps-page, .ps-page * { box-sizing: border-box; }
    .ps-page { width: 100%; min-width: 0; color: #292929; }
    .ps-header { padding-bottom: 22px; margin-bottom: 24px; border-bottom: 1px solid #e7cfd5; }
    .ps-breadcrumb { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; margin-bottom: 12px; font-size: 12px; color: #8d8587; }
    .ps-breadcrumb a { color: #8d8587; text-decoration: none; }
    .ps-breadcrumb a:hover, .ps-breadcrumb .ps-current { color: #800b22; }
    .ps-header h1 { margin: 0 0 7px; font-size: 25px; line-height: 1.3; font-weight: 700; }
    .ps-header p { margin: 0; font-size: 13px; color: #777; line-height: 1.7; }

    .ps-alert { margin-bottom: 18px; padding: 13px 15px; border: 1px solid #bfebcb; border-radius: 9px; background: #e8f7ed; color: #196a38; font-size: 12px; line-height: 1.6; }
    .ps-alert.error { border-color: #f0c5c5; background: #fff0f0; color: #ae3030; }
    .ps-alert ul { margin: 6px 0 0; padding-left: 18px; }

    .ps-panel { margin-bottom: 20px; overflow: hidden; background: #fff; border: 1px solid #efe2df; border-radius: 12px; box-shadow: 0 2px 8px rgba(50, 20, 20, .025); }
    .ps-panel-top { display: flex; align-items: center; flex-wrap: wrap; gap: 13px; padding: 18px 24px; border-bottom: 1px solid #f0e8e7; }
    .ps-panel-icon { display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; flex: none; border-radius: 10px; background: #f9eff1; color: #800b22; }
    .ps-panel-icon svg { width: 19px; height: 19px; }
    .ps-panel-head { min-width: 0; flex: 1 1 220px; }
    .ps-panel-head h2 { margin: 0; font-size: 15px; font-weight: 700; }
    .ps-panel-head p { margin: 4px 0 0; font-size: 11px; color: #8a8284; line-height: 1.6; }

    .ps-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 11px; border-radius: 999px; font-size: 10.5px; font-weight: 700; white-space: nowrap; }
    .ps-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .ps-pill.ok { background: #def8e8; color: #17763e; }
    .ps-pill.warn { background: #fdf3dc; color: #93650d; }
    .ps-pill.danger { background: #fee8eb; color: #ad3041; }
    .ps-pill.muted { background: #f1eff0; color: #62585c; }

    .ps-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 37px; padding: 9px 15px; border: 1px solid #800b22; border-radius: 8px; background: #800b22; color: #fff; font-family: inherit; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background .2s; }
    .ps-btn:hover { background: #65091b; }
    .ps-btn.ghost { background: #fff; color: #800b22; }
    .ps-btn.ghost:hover { background: #fdf3f5; }
    .ps-btn.muted { border-color: #e2d6d8; background: #fff; color: #6b6164; }
    .ps-btn.muted:hover { background: #f7f3f4; }
    .ps-btn.danger { border-color: #f0c5ca; background: #fff6f7; color: #b03044; }
    .ps-btn.danger:hover { background: #a6263e; border-color: #a6263e; color: #fff; }
    .ps-btn svg { width: 14px; height: 14px; }
    .ps-btn-sm { min-height: 31px; padding: 6px 10px; font-size: 11px; }

    .ps-view { padding: 20px 24px; }
    .ps-view[hidden], .ps-form[hidden] { display: none !important; }
    .ps-info-grid { display: grid; grid-template-columns: 140px minmax(0, 1fr); gap: 24px; align-items: start; }
    .ps-logo-preview { display: flex; flex-direction: column; align-items: center; gap: 9px; padding: 16px 12px; border: 1px dashed #e3cfd4; border-radius: 12px; background: #fdfafa; text-align: center; }
    .ps-logo-preview img { display: block; width: 86px; height: 86px; object-fit: contain; }
    .ps-logo-preview span { font-size: 10.5px; color: #8a8284; line-height: 1.5; }

    .ps-defs { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 22px; margin: 0; }
    .ps-def { min-width: 0; }
    .ps-def dt { margin-bottom: 5px; font-size: 10.5px; font-weight: 700; color: #8a8284; letter-spacing: .04em; text-transform: uppercase; }
    .ps-def dd { margin: 0; font-size: 12.5px; color: #3d3537; line-height: 1.65; overflow-wrap: anywhere; }
    .ps-def dd strong { font-weight: 700; }
    .ps-def .ps-mono { font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 11.5px; }
    .ps-muted { color: #9a9294; }

    .ps-form { padding: 20px 24px; background: #fdfbfb; border-top: 1px solid #f0e8e7; }
    .ps-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px 18px; }
    .ps-field { display: flex; flex-direction: column; min-width: 0; }
    .ps-field.full { grid-column: 1 / -1; }
    .ps-field label { margin-bottom: 6px; font-size: 10.5px; font-weight: 700; color: #6e5a5f; letter-spacing: .04em; text-transform: uppercase; }
    .ps-input, .ps-select { width: 100%; min-height: 40px; padding: 9px 12px; border: 1px solid #e7dbd9; border-radius: 8px; background: #fff; font: inherit; font-size: 12.5px; color: #333; }
    .ps-input:focus, .ps-select:focus { border-color: #800b22; outline: 0; box-shadow: 0 0 0 3px rgba(128, 11, 34, .07); }
    .ps-input.salah, .ps-select.salah { border-color: #c0392b; background: #fdf6f5; }
    .ps-error { margin-top: 5px; color: #b3261e; font-size: 11px; }
    .ps-hint { margin-top: 5px; color: #9a9294; font-size: 10.5px; line-height: 1.6; }
    .ps-check { display: flex; align-items: flex-start; gap: 8px; font-size: 11.5px; color: #565052; line-height: 1.5; }
    .ps-check input { margin-top: 2px; accent-color: #800b22; }

    .ps-radio-row { display: flex; flex-wrap: wrap; gap: 9px; }
    .ps-radio { position: relative; display: inline-flex; align-items: center; gap: 8px; padding: 9px 13px; border: 1px solid #e7dbd9; border-radius: 8px; background: #fff; font-size: 11.5px; font-weight: 600; color: #4a4244; cursor: pointer; }
    .ps-radio input { accent-color: #800b22; }
    .ps-radio:has(input:checked) { border-color: #800b22; background: #fdf3f5; color: #800b22; }
    .ps-smtp-fields { margin-top: 4px; padding-top: 15px; border-top: 1px dashed #e7dbd9; }
    .ps-smtp-fields[hidden] { display: none !important; }

    .ps-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; margin-top: 18px; padding-top: 15px; border-top: 1px solid #f0e8e7; }
    .ps-actions .ps-spacer { flex: 1 1 auto; }
    .ps-inline-form { display: inline-flex; }

    .ps-sub { padding: 18px 24px; }
    .ps-sub + .ps-sub { border-top: 1px solid #f0e8e7; }
    .ps-sub-head { display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .ps-sub-head h3 { margin: 0; font-size: 13px; font-weight: 700; }
    .ps-sub-head a { font-size: 11px; font-weight: 600; color: #800b22; text-decoration: none; }
    .ps-sub-head a:hover { text-decoration: underline; }
    .ps-sub-desc { margin: 5px 0 12px; font-size: 11px; color: #8a8284; line-height: 1.6; }

    .ps-table-wrap { width: 100%; overflow-x: auto; scrollbar-width: thin; }
    .ps-table { width: 100%; min-width: 640px; border-collapse: collapse; font-size: 11.5px; }
    .ps-table th { padding: 9px 12px; background: #f7f0f1; color: #6e5a5f; font-size: 10px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .ps-table td { padding: 11px 12px; border-bottom: 1px solid #f4eceb; color: #565052; vertical-align: top; line-height: 1.6; }
    .ps-table tr:last-child td { border-bottom: 0; }
    .ps-table .nowrap { white-space: nowrap; }
    .ps-table .strong { color: #2d2829; font-weight: 700; }
    .ps-role { display: inline-block; margin-left: 6px; padding: 3px 8px; border-radius: 999px; background: #f3f0f1; color: #6b6164; font-size: 9.5px; font-weight: 700; white-space: nowrap; }
    .ps-table-actions { display: flex; gap: 6px; flex-wrap: wrap; }

    .ps-empty { padding: 24px 16px; border: 1px dashed #e3cfd4; border-radius: 10px; background: #fdfafa; text-align: center; color: #8a8284; font-size: 11.5px; line-height: 1.7; }
    .ps-empty strong { display: block; margin-bottom: 4px; color: #4a4244; font-size: 12px; }
    .ps-note { display: flex; align-items: flex-start; gap: 9px; margin: 0; padding: 13px 24px 18px; color: #8a8284; font-size: 11px; line-height: 1.65; }
    .ps-note svg { width: 14px; height: 14px; flex: none; margin-top: 1px; color: #800b22; }

    @media (max-width: 860px) {
        .ps-info-grid { grid-template-columns: 1fr; }
        .ps-fields, .ps-defs { grid-template-columns: 1fr; }
        .ps-logo-preview { flex-direction: row; justify-content: flex-start; text-align: left; }
        .ps-logo-preview img { width: 62px; height: 62px; }
    }
    @media (max-width: 700px) {
        .ps-header h1 { font-size: 21px; }
        .ps-panel-top, .ps-view, .ps-form, .ps-sub, .ps-note { padding-left: 16px; padding-right: 16px; }
        .ps-btn { width: 100%; }
        .ps-actions .ps-btn, .ps-inline-form { width: 100%; }
        .ps-table-actions .ps-btn { width: auto; }
    }
</style>
@endpush

@section('content')
<div class="ps-page" data-buka="{{ $bukaBagian }}">

    <header class="ps-header">
        <nav class="ps-breadcrumb" aria-label="Navigasi halaman">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span aria-hidden="true">/</span>
            <span class="ps-current" aria-current="page">Pengaturan Sistem</span>
        </nav>
        <h1>Pengaturan Sistem</h1>
        <p>
            Identitas aplikasi, pengiriman email, keamanan, serta backup dan pemeliharaan {{ $pengaturan->nama_sistem }}.
            Setiap perubahan disimpan pada penyimpanan aplikasi dan langsung diterapkan.
        </p>
    </header>

    @if (session('success'))
        <div class="ps-alert" role="status">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="ps-alert error" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="ps-alert error" role="alert">
            <strong>Perubahan belum tersimpan. Periksa kembali isian yang ditandai.</strong>
            <ul>
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- BAGIAN 1: IDENTITAS APLIKASI --}}
    <section class="ps-panel" aria-labelledby="psJudulIdentitas">
        <div class="ps-panel-top">
            <span class="ps-panel-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M3 9h18"/>
                </svg>
            </span>
            <div class="ps-panel-head">
                <h2 id="psJudulIdentitas">Identitas Aplikasi</h2>
                <p>Nama aplikasi, nama institusi, dan logo yang dipakai pada sidebar, judul halaman, dan dokumen.</p>
            </div>
            <span class="ps-pill {{ $statusIdentitas['nada'] }}">{{ $statusIdentitas['label'] }}</span>
            <button type="button" class="ps-btn ghost ps-btn-sm" data-edit="identitas">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>
                </svg>
                Edit
            </button>
        </div>

        <div class="ps-view" data-bagian="identitas">
            <div class="ps-info-grid">
                <div class="ps-logo-preview">
                    <img src="{{ $pengaturan->urlLogo() }}" alt="Logo {{ $pengaturan->nama_sistem }}">
                    <span>{{ $pengaturan->logo_path ? basename($pengaturan->logo_path) : 'logo_uin.png (bawaan)' }}</span>
                </div>
                <dl class="ps-defs">
                    <div class="ps-def">
                        <dt>Nama Aplikasi</dt>
                        <dd><strong>{{ $pengaturan->nama_sistem }}</strong></dd>
                    </div>
                    <div class="ps-def">
                        <dt>Nama Institusi</dt>
                        <dd>{{ $pengaturan->nama_institusi }}</dd>
                    </div>
                    <div class="ps-def">
                        <dt>Versi Aplikasi</dt>
                        <dd>v{{ $versi }} <span class="ps-muted">(baca saja, dari APP_VERSION)</span></dd>
                    </div>
                    <div class="ps-def">
                        <dt>Status Identitas</dt>
                        <dd>{{ $statusIdentitas['keterangan'] }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pengaturan-sistem.update') }}" enctype="multipart/form-data"
            class="ps-form" data-bagian="identitas" hidden>
            @csrf
            @method('PUT')

            <div class="ps-fields">
                <div class="ps-field">
                    <label for="nama_sistem">Nama Aplikasi</label>
                    <input type="text" id="nama_sistem" name="nama_sistem"
                        class="ps-input @error('nama_sistem') salah @enderror"
                        value="{{ old('nama_sistem', $pengaturan->nama_sistem) }}" maxlength="60" required>
                    @error('nama_sistem')<span class="ps-error">{{ $message }}</span>@enderror
                </div>

                <div class="ps-field">
                    <label for="nama_institusi">Nama Institusi</label>
                    <input type="text" id="nama_institusi" name="nama_institusi"
                        class="ps-input @error('nama_institusi') salah @enderror"
                        value="{{ old('nama_institusi', $pengaturan->nama_institusi) }}" maxlength="150" required>
                    @error('nama_institusi')<span class="ps-error">{{ $message }}</span>@enderror
                </div>

                <div class="ps-field">
                    <label for="logo">Unggah Logo Baru</label>
                    <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp"
                        class="ps-input @error('logo') salah @enderror">
                    @error('logo')<span class="ps-error">{{ $message }}</span>@enderror
                    <span class="ps-hint">PNG, JPG, JPEG, atau WEBP. Maksimal 2 MB.</span>
                </div>

                <div class="ps-field">
                    <label>Pratinjau Logo</label>
                    <div class="ps-logo-preview" style="padding:10px;">
                        <img id="psPratinjauLogo" src="{{ $pengaturan->urlLogo() }}" data-asli="{{ $pengaturan->urlLogo() }}" alt="Pratinjau logo aplikasi">
                    </div>
                    @if ($pengaturan->logo_path)
                        <label class="ps-check" style="margin-top:8px;">
                            <input type="checkbox" name="hapus_logo" value="1">
                            <span>Hapus logo unggahan dan kembali ke logo bawaan.</span>
                        </label>
                    @endif
                </div>
            </div>

            <div class="ps-actions">
                <button type="submit" class="ps-btn">Simpan</button>
                <button type="button" class="ps-btn muted" data-batal="identitas">Batal</button>
                <span class="ps-spacer"></span>
                <span class="ps-hint">
                    Terakhir diperbarui:
                    {{ $pengaturan->updated_at?->locale('id')->translatedFormat('d F Y, H:i') ?? 'belum pernah' }} WIB
                </span>
            </div>
        </form>
    </section>

    {{-- BAGIAN 2: PENGATURAN EMAIL --}}
    <section class="ps-panel" aria-labelledby="psJudulEmail">
        <div class="ps-panel-top">
            <span class="ps-panel-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <path d="m22 7-10 6L2 7"/>
                </svg>
            </span>
            <div class="ps-panel-head">
                <h2 id="psJudulEmail">Pengaturan Email</h2>
                <p>Pengiriman email aktivasi. Mode lokal memakai Mailpit; mode SMTP kustom memakai server email sendiri.</p>
            </div>
            <span class="ps-pill {{ $statusEmail['nada'] }}">{{ $statusEmail['label'] }}</span>
            <button type="button" class="ps-btn ghost ps-btn-sm" data-edit="email">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>
                </svg>
                Edit
            </button>
        </div>

        <div class="ps-view" data-bagian="email">
            <dl class="ps-defs">
                <div class="ps-def">
                    <dt>Mode Pengiriman</dt>
                    <dd><strong>{{ $pengaturan->memakaiSmtpKustom() ? 'SMTP kustom' : 'Lokal (Mailpit)' }}</strong></dd>
                </div>
                <div class="ps-def">
                    <dt>Mailer Aktif</dt>
                    <dd class="ps-mono">{{ $statusEmail['mailer'] }}</dd>
                </div>
                <div class="ps-def">
                    <dt>Host dan Port Aktif</dt>
                    <dd class="ps-mono">{{ config('mail.mailers.smtp.host') }}:{{ config('mail.mailers.smtp.port') }}</dd>
                </div>
                <div class="ps-def">
                    <dt>Alamat Pengirim Aktif</dt>
                    <dd>{{ config('mail.from.address') ?: 'belum diatur' }}</dd>
                </div>
                <div class="ps-def">
                    <dt>Kata Sandi SMTP</dt>
                    <dd>{{ $pengaturan->mail_password ? 'Tersimpan (terenkripsi)' : 'Belum diatur' }}</dd>
                </div>
                <div class="ps-def">
                    <dt>Status Konfigurasi</dt>
                    <dd>{{ $statusEmail['keterangan'] }}</dd>
                </div>
            </dl>

            <div class="ps-actions">
                <form method="POST" action="{{ route('admin.pengaturan-sistem.email.tes') }}" class="ps-inline-form">
                    @csrf
                    <button type="submit" class="ps-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m22 2-7 20-4-9-9-4z"/>
                        </svg>
                        Tes Koneksi
                    </button>
                </form>
                <span class="ps-hint">
                    Email uji dikirim ke {{ auth()->user()?->email ?? 'email Admin' }}.
                    @if ($statusEmail['lokal'])
                        Hasilnya dapat dilihat di dashboard Mailpit http://127.0.0.1:8025.
                    @endif
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pengaturan-sistem.email') }}" class="ps-form" data-bagian="email" hidden>
            @csrf
            @method('PUT')

            <div class="ps-field full">
                <label>Mode Pengiriman Email</label>
                <div class="ps-radio-row">
                    <label class="ps-radio">
                        <input type="radio" name="mail_mode" value="lokal"
                            @checked(old('mail_mode', $pengaturan->mail_mode ?: 'lokal') === 'lokal')>
                        Lokal (Mailpit 127.0.0.1:1025)
                    </label>
                    <label class="ps-radio">
                        <input type="radio" name="mail_mode" value="smtp"
                            @checked(old('mail_mode', $pengaturan->mail_mode) === 'smtp')>
                        SMTP kustom
                    </label>
                </div>
                @error('mail_mode')<span class="ps-error">{{ $message }}</span>@enderror
                <span class="ps-hint">Mode lokal tidak memerlukan kredensial dan aman untuk pengujian.</span>
            </div>

            <div class="ps-smtp-fields" id="psSmtpFields" hidden>
                <div class="ps-fields">
                    <div class="ps-field">
                        <label for="mail_host">Host SMTP</label>
                        <input type="text" id="mail_host" name="mail_host"
                            class="ps-input @error('mail_host') salah @enderror"
                            value="{{ old('mail_host', $pengaturan->mail_host) }}" placeholder="mail.contoh.com">
                        @error('mail_host')<span class="ps-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="ps-field">
                        <label for="mail_port">Port</label>
                        <input type="number" id="mail_port" name="mail_port" min="1" max="65535"
                            class="ps-input @error('mail_port') salah @enderror"
                            value="{{ old('mail_port', $pengaturan->mail_port) }}" placeholder="587">
                        @error('mail_port')<span class="ps-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="ps-field">
                        <label for="mail_scheme">Jenis Koneksi</label>
                        <select id="mail_scheme" name="mail_scheme" class="ps-select @error('mail_scheme') salah @enderror">
                            <option value="" @selected(old('mail_scheme', $pengaturan->mail_scheme) === null || old('mail_scheme', $pengaturan->mail_scheme) === '')>STARTTLS (port 587)</option>
                            <option value="smtps" @selected(old('mail_scheme', $pengaturan->mail_scheme) === 'smtps')>TLS langsung / smtps (port 465)</option>
                        </select>
                        @error('mail_scheme')<span class="ps-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="ps-field">
                        <label for="mail_username">Username SMTP</label>
                        <input type="text" id="mail_username" name="mail_username"
                            class="ps-input @error('mail_username') salah @enderror"
                            value="{{ old('mail_username', $pengaturan->mail_username) }}">
                        @error('mail_username')<span class="ps-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="ps-field">
                        <label for="mail_password">Kata Sandi SMTP</label>
                        <input type="password" id="mail_password" name="mail_password" autocomplete="new-password"
                            class="ps-input @error('mail_password') salah @enderror"
                            placeholder="{{ $pengaturan->mail_password ? 'Kosongkan untuk mempertahankan kata sandi' : 'Belum diatur' }}">
                        @error('mail_password')<span class="ps-error">{{ $message }}</span>@enderror
                        @if ($pengaturan->mail_password)
                            <label class="ps-check" style="margin-top:8px;">
                                <input type="checkbox" name="hapus_password" value="1">
                                <span>Hapus kata sandi yang tersimpan.</span>
                            </label>
                        @endif
                    </div>

                    <div class="ps-field">
                        <label for="mail_from_address">Alamat Pengirim</label>
                        <input type="email" id="mail_from_address" name="mail_from_address"
                            class="ps-input @error('mail_from_address') salah @enderror"
                            value="{{ old('mail_from_address', $pengaturan->mail_from_address) }}"
                            placeholder="noreply@simahad.test">
                        @error('mail_from_address')<span class="ps-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="ps-field">
                        <label for="mail_from_name">Nama Pengirim</label>
                        <input type="text" id="mail_from_name" name="mail_from_name"
                            class="ps-input @error('mail_from_name') salah @enderror"
                            value="{{ old('mail_from_name', $pengaturan->mail_from_name) }}" maxlength="150">
                        @error('mail_from_name')<span class="ps-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="ps-actions">
                <button type="submit" class="ps-btn">Simpan</button>
                <button type="button" class="ps-btn muted" data-batal="email">Batal</button>
            </div>
        </form>
    </section>

    {{-- BAGIAN 3: KEAMANAN SISTEM --}}
    <section class="ps-panel" aria-labelledby="psJudulKeamanan">
        <div class="ps-panel-top">
            <span class="ps-panel-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </span>
            <div class="ps-panel-head">
                <h2 id="psJudulKeamanan">Keamanan Sistem</h2>
                <p>Durasi sesi login serta jejak audit aktivitas pengguna dan perubahan sistem.</p>
            </div>
            <span class="ps-pill {{ $statusKeamanan['nada'] }}">{{ $statusKeamanan['label'] }}</span>
            <button type="button" class="ps-btn ghost ps-btn-sm" data-edit="keamanan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>
                </svg>
                Edit
            </button>
        </div>

        <div class="ps-view" data-bagian="keamanan">
            <dl class="ps-defs">
                <div class="ps-def">
                    <dt>Durasi Sesi Login</dt>
                    <dd><strong>{{ $pengaturan->durasi_sesi }} menit</strong> tanpa aktivitas</dd>
                </div>
                <div class="ps-def">
                    <dt>Verifikasi Email Google</dt>
                    <dd>Selalu aktif <span class="ps-muted">(baca saja)</span></dd>
                </div>
                <div class="ps-def">
                    <dt>Aktivitas Tercatat</dt>
                    <dd>{{ number_format($totalAktivitas, 0, ',', '.') }} aktivitas</dd>
                </div>
                <div class="ps-def">
                    <dt>Keterangan</dt>
                    <dd>{{ $statusKeamanan['keterangan'] }}</dd>
                </div>
            </dl>
        </div>

        <form method="POST" action="{{ route('admin.pengaturan-sistem.keamanan') }}" class="ps-form" data-bagian="keamanan" hidden>
            @csrf
            @method('PUT')

            <div class="ps-fields">
                <div class="ps-field">
                    <label for="durasi_sesi">Durasi Sesi Login (menit)</label>
                    <input type="number" id="durasi_sesi" name="durasi_sesi" min="5" max="1440" step="1"
                        class="ps-input @error('durasi_sesi') salah @enderror"
                        value="{{ old('durasi_sesi', $pengaturan->durasi_sesi) }}" required>
                    @error('durasi_sesi')<span class="ps-error">{{ $message }}</span>@enderror
                    <span class="ps-hint">Pengguna diminta masuk kembali setelah tidak aktif selama durasi ini (5–1440 menit).</span>
                </div>
            </div>

            <div class="ps-actions">
                <button type="submit" class="ps-btn">Simpan</button>
                <button type="button" class="ps-btn muted" data-batal="keamanan">Batal</button>
            </div>
        </form>

        <div class="ps-sub">
            <div class="ps-sub-head">
                <h3>Riwayat Aktivitas Pengguna</h3>
                <a href="{{ route('admin.log-aktivitas') }}">Lihat seluruh riwayat</a>
            </div>
            <p class="ps-sub-desc">
                Menampilkan {{ $aktivitasPengguna->count() }} aktivitas terbaru dari seluruh peran.
            </p>

            @if ($aktivitasPengguna->isEmpty())
                <div class="ps-empty">
                    <strong>Belum ada aktivitas tercatat.</strong>
                    Aktivitas pengguna muncul setelah ada tindakan yang tersimpan.
                </div>
            @else
                <div class="ps-table-wrap">
                    <table class="ps-table">
                        <thead>
                            <tr>
                                <th scope="col">Waktu</th>
                                <th scope="col">Pengguna</th>
                                <th scope="col">Aktivitas</th>
                                <th scope="col">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aktivitasPengguna as $log)
                                <tr>
                                    <td class="nowrap">{{ $log->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>
                                        <span class="strong">{{ $log->user_name ?: 'Sistem' }}</span>
                                        <span class="ps-role">{{ ucfirst($log->user_role ?: 'sistem') }}</span>
                                    </td>
                                    <td>{{ $log->activity ?: '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit((string) ($log->description ?: '-'), 90) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="ps-sub">
            <div class="ps-sub-head">
                <h3>Riwayat Perubahan Sistem</h3>
            </div>
            <p class="ps-sub-desc">
                Catatan perubahan pengaturan, backup, dan pemeliharaan beserta admin yang melakukannya.
            </p>

            @if ($perubahanSistem->isEmpty())
                <div class="ps-empty">
                    <strong>Belum ada perubahan sistem.</strong>
                    Setiap penyimpanan pengaturan akan direkam otomatis di sini.
                </div>
            @else
                <div class="ps-table-wrap">
                    <table class="ps-table">
                        <thead>
                            <tr>
                                <th scope="col">Waktu</th>
                                <th scope="col">Admin</th>
                                <th scope="col">Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perubahanSistem as $log)
                                <tr>
                                    <td class="nowrap">{{ $log->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>
                                        <span class="strong">{{ $log->user_name ?: 'Sistem' }}</span>
                                        <span class="ps-role">{{ ucfirst($log->user_role ?: 'sistem') }}</span>
                                    </td>
                                    <td>{{ $log->description ?: ($log->activity ?: '-') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <p class="ps-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4M12 8h.01"/>
            </svg>
            <span>
                Riwayat audit bersifat tambah-saja sehingga tidak dapat diubah atau dihapus.
                Pengaturan akun dikelola melalui menu profil, sedangkan preferensi notifikasi dikelola pada fitur notifikasi.
            </span>
        </p>
    </section>

    {{-- BAGIAN 4: BACKUP DAN PEMELIHARAAN --}}
    <section class="ps-panel" aria-labelledby="psJudulBackup">
        <div class="ps-panel-top">
            <span class="ps-panel-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12a9 9 0 1 1-3-6.7L21 8"/>
                    <path d="M21 3v5h-5"/>
                </svg>
            </span>
            <div class="ps-panel-head">
                <h2 id="psJudulBackup">Backup dan Pemeliharaan</h2>
                <p>Cadangan basis data, retensi berkas, dan pembersihan cache aplikasi.</p>
            </div>
            <span class="ps-pill {{ $statusBackup['nada'] }}">{{ $statusBackup['label'] }}</span>
            <button type="button" class="ps-btn ghost ps-btn-sm" data-edit="backup">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>
                </svg>
                Edit
            </button>
        </div>

        <div class="ps-view" data-bagian="backup">
            <dl class="ps-defs">
                <div class="ps-def">
                    <dt>Retensi Backup</dt>
                    <dd><strong>{{ $pengaturan->retensiBackup() }} berkas</strong> terbaru disimpan</dd>
                </div>
                <div class="ps-def">
                    <dt>Berkas Tersimpan</dt>
                    <dd>{{ $daftarBackup->count() }} berkas &middot; {{ $totalUkuranBackup }}</dd>
                </div>
                <div class="ps-def">
                    <dt>Lingkungan</dt>
                    <dd class="ps-mono">
                        Laravel {{ app()->version() }} &middot; PHP {{ PHP_VERSION }}
                        &middot; {{ config('database.default') }}
                    </dd>
                </div>
                <div class="ps-def">
                    <dt>Keterangan</dt>
                    <dd>{{ $statusBackup['keterangan'] }}</dd>
                </div>
            </dl>

            <div class="ps-actions">
                <form method="POST" action="{{ route('admin.pengaturan-sistem.backup.buat') }}" class="ps-inline-form">
                    @csrf
                    <button type="submit" class="ps-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <path d="M17 21v-8H7v8M7 3v5h8"/>
                        </svg>
                        Buat Backup Sekarang
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.pengaturan-sistem.pemeliharaan') }}" class="ps-inline-form">
                    @csrf
                    <input type="hidden" name="tindakan" value="cache_aplikasi">
                    <button type="submit" class="ps-btn muted">Bersihkan Cache Aplikasi</button>
                </form>

                <form method="POST" action="{{ route('admin.pengaturan-sistem.pemeliharaan') }}" class="ps-inline-form">
                    @csrf
                    <input type="hidden" name="tindakan" value="cache_tampilan">
                    <button type="submit" class="ps-btn muted">Bersihkan Cache Tampilan</button>
                </form>

                <form method="POST" action="{{ route('admin.pengaturan-sistem.pemeliharaan') }}" class="ps-inline-form">
                    @csrf
                    <input type="hidden" name="tindakan" value="cache_konfigurasi">
                    <button type="submit" class="ps-btn muted">Bersihkan Cache Konfigurasi</button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pengaturan-sistem.backup') }}" class="ps-form" data-bagian="backup" hidden>
            @csrf
            @method('PUT')

            <div class="ps-fields">
                <div class="ps-field">
                    <label for="backup_retensi">Retensi Backup (jumlah berkas)</label>
                    <input type="number" id="backup_retensi" name="backup_retensi" min="1" max="30" step="1"
                        class="ps-input @error('backup_retensi') salah @enderror"
                        value="{{ old('backup_retensi', $pengaturan->retensiBackup()) }}" required>
                    @error('backup_retensi')<span class="ps-error">{{ $message }}</span>@enderror
                    <span class="ps-hint">Berkas backup tertua dihapus otomatis saat jumlah melebihi retensi (1–30).</span>
                </div>
            </div>

            <div class="ps-actions">
                <button type="submit" class="ps-btn">Simpan</button>
                <button type="button" class="ps-btn muted" data-batal="backup">Batal</button>
            </div>
        </form>

        <div class="ps-sub">
            <div class="ps-sub-head">
                <h3>Berkas Backup Tersimpan</h3>
            </div>
            <p class="ps-sub-desc">
                Berkas disimpan pada storage/app/private/backups dan hanya dapat diunduh Admin Sistem.
            </p>

            @if ($daftarBackup->isEmpty())
                <div class="ps-empty">
                    <strong>Belum ada berkas backup.</strong>
                    Gunakan tombol Buat Backup Sekarang untuk membuat cadangan basis data pertama.
                </div>
            @else
                <div class="ps-table-wrap">
                    <table class="ps-table">
                        <thead>
                            <tr>
                                <th scope="col">Berkas</th>
                                <th scope="col">Ukuran</th>
                                <th scope="col">Dibuat</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftarBackup as $berkas)
                                <tr>
                                    <td class="strong ps-mono">{{ $berkas['nama'] }}</td>
                                    <td class="nowrap">{{ $berkas['dibaca'] }}</td>
                                    <td class="nowrap">{{ date('d/m/Y H:i', $berkas['waktu']) }}</td>
                                    <td>
                                        <div class="ps-table-actions">
                                            <a class="ps-btn ghost ps-btn-sm"
                                                href="{{ route('admin.pengaturan-sistem.backup.unduh', ['berkas' => $berkas['nama']]) }}">
                                                Unduh
                                            </a>
                                            <form method="POST"
                                                action="{{ route('admin.pengaturan-sistem.backup.hapus', ['berkas' => $berkas['nama']]) }}"
                                                onsubmit="return confirm('Hapus berkas backup {{ $berkas['nama'] }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ps-btn danger ps-btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <p class="ps-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4M12 8h.01"/>
            </svg>
            <span>
                Backup berisi seluruh tabel basis data, termasuk data pengguna. Simpan berkas unduhan di tempat aman.
                Pembersihan cache tidak menghapus data.
            </span>
        </p>
    </section>

</div>
@endsection

@push('scripts')
<script>
    (function () {
        const page = document.querySelector('.ps-page');
        const pratinjau = document.getElementById('psPratinjauLogo');

        function buka(bagian) {
            const tampilan = document.querySelector('.ps-view[data-bagian="' + bagian + '"]');
            const form = document.querySelector('.ps-form[data-bagian="' + bagian + '"]');

            if (!tampilan || !form) {
                return;
            }

            tampilan.hidden = true;
            form.hidden = false;
            const pertama = form.querySelector('input:not([type="hidden"]):not([disabled]), select');
            if (pertama) {
                pertama.focus();
            }
        }

        function tutup(bagian) {
            const tampilan = document.querySelector('.ps-view[data-bagian="' + bagian + '"]');
            const form = document.querySelector('.ps-form[data-bagian="' + bagian + '"]');

            if (!tampilan || !form) {
                return;
            }

            form.hidden = true;
            tampilan.hidden = false;

            // Batal membuang perubahan yang belum disimpan.
            form.reset();

            if (pratinjau && pratinjau.dataset.asli) {
                pratinjau.src = pratinjau.dataset.asli;
            }
        }

        document.querySelectorAll('[data-edit]').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                buka(tombol.dataset.edit);
            });
        });

        document.querySelectorAll('[data-batal]').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                tutup(tombol.dataset.batal);
            });
        });

        if (page && page.dataset.buka) {
            buka(page.dataset.buka);
        }

        // Pratinjau logo sebelum disimpan.
        const inputLogo = document.getElementById('logo');
        if (inputLogo && pratinjau) {
            inputLogo.addEventListener('change', function () {
                const berkas = inputLogo.files && inputLogo.files[0];
                if (!berkas) {
                    return;
                }

                const pembaca = new FileReader();
                pembaca.onload = function (e) {
                    pratinjau.src = e.target.result;
                };
                pembaca.readAsDataURL(berkas);
            });
        }

        // Field SMTP hanya aktif pada mode SMTP kustom agar validasi konsisten.
        const modeEmail = document.querySelectorAll('input[name="mail_mode"]');
        const smtpFields = document.getElementById('psSmtpFields');
        if (smtpFields) {
            const sinkron = function () {
                const terpilih = document.querySelector('input[name="mail_mode"]:checked');
                const aktif = terpilih && terpilih.value === 'smtp';
                smtpFields.hidden = !aktif;
                smtpFields.querySelectorAll('input, select').forEach(function (el) {
                    el.disabled = !aktif;
                });
            };

            modeEmail.forEach(function (radio) {
                radio.addEventListener('change', sinkron);
            });
            sinkron();
        }
    })();
</script>
@endpush
