@php
    $adaFoto = filled($user->foto_profil);
    $urlFoto = $adaFoto ? asset('storage/'.$user->foto_profil) : null;
    $inisial = strtoupper(mb_substr($user->name ?? 'P', 0, 1));
    $rolePengguna = strtolower(trim((string) $user->role));

    $statusAkun = strtolower(trim((string) $user->status));
    $labelStatus = match ($statusAkun) {
        'aktif' => 'Akun Aktif',
        'pending', 'menunggu aktivasi' => 'Menunggu Verifikasi',
        'rejected', 'nonaktif' => 'Tidak Aktif',
        default => $user->status ? ucfirst($statusAkun) : 'Tidak Diketahui',
    };
    $kelasStatus = match ($statusAkun) {
        'aktif' => 'pa-badge--sukses',
        'pending', 'menunggu aktivasi' => 'pa-badge--menunggu',
        default => 'pa-badge--gagal',
    };
@endphp

<style>
    .pa-page {
        width: 100%;
        color: #27272a;
        font-size: 15px;
    }

    /* JUDUL HALAMAN */
    .pa-header {
        margin-bottom: 20px;
    }

    .pa-breadcrumb {
        margin-bottom: 6px;
        color: #800d22;
        font-size: 15px;
        font-weight: 600;
    }

    .pa-header h1 {
        margin-bottom: 4px;
        color: #111;
        font-size: 30px;
        font-weight: 700;
    }

    .pa-subtitle {
        color: #6b7280;
        font-size: 14.5px;
        line-height: 1.5;
    }

    /* PESAN */
    .pa-alert {
        margin-bottom: 16px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.5;
    }

    .pa-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .pa-alert--sukses {
        border: 1px solid #abefc6;
        background: #ecfdf3;
        color: #067647;
    }

    .pa-alert--gagal {
        border: 1px solid #fecdca;
        background: #fef3f2;
        color: #b42318;
    }

    /* TATA LETAK */
    .pa-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }

    .pa-kolom {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .pa-card {
        padding: 22px;
        border: 1px solid #e6e0dc;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(17, 17, 17, 0.04);
    }

    /* PROFIL & FOTO */
    .pa-profil-head {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }

    .pa-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 88px;
        height: 88px;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid #f1d5db;
        border-radius: 50%;
        background: #f6e7ea;
    }

    .pa-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pa-inisial {
        color: #800d22;
        font-size: 32px;
        font-weight: 700;
    }

    .pa-profil-identitas {
        flex: 1;
        min-width: 0;
    }

    .pa-profil-identitas h2 {
        margin-bottom: 2px;
        color: #111;
        font-size: 20px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .pa-profil-identitas p {
        color: #6b7280;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .pa-foto-aksi {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .pa-foto-aksi form {
        margin: 0;
    }

    .pa-divider {
        margin: 20px 0;
        border: none;
        border-top: 1px solid #eee7e3;
    }

    /* FORMULIR */
    .pa-judul {
        margin-bottom: 4px;
        color: #111;
        font-size: 17px;
        font-weight: 700;
    }

    .pa-muted {
        margin-bottom: 14px;
        color: #6b7280;
        font-size: 13.5px;
        line-height: 1.55;
    }

    .pa-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .pa-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .pa-field label {
        color: #3f3f46;
        font-size: 13.5px;
        font-weight: 600;
    }

    .pa-wajib {
        color: #b42318;
    }

    .pa-field input {
        padding: 10px 12px;
        border: 1px solid #d8d2ce;
        border-radius: 9px;
        background: #fff;
        color: #18181b;
        font-family: inherit;
        font-size: 14px;
    }

    .pa-field input:focus {
        border-color: #800d22;
        outline: none;
        box-shadow: 0 0 0 3px rgba(128, 13, 34, 0.12);
    }

    .pa-aksi {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 4px;
    }

    .pa-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 18px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s, color 0.2s;
    }

    .pa-btn--utama {
        background: #800d22;
        color: #fff;
    }

    .pa-btn--utama:hover {
        background: #590018;
    }

    .pa-btn--garis {
        border-color: #800d22;
        background: #fff;
        color: #800d22;
    }

    .pa-btn--garis:hover {
        background: #fdf2f4;
    }

    .pa-btn--bahaya {
        border-color: #d8d2ce;
        background: #fff;
        color: #b42318;
    }

    .pa-btn--bahaya:hover {
        border-color: #fecdca;
        background: #fef3f2;
    }

    /* DAFTAR INFORMASI */
    .pa-daftar {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 6px 0 0;
    }

    .pa-daftar > div {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #ece6e2;
    }

    .pa-daftar > div:last-child {
        padding-bottom: 0;
        border-bottom: none;
    }

    .pa-daftar dt {
        color: #6b7280;
        font-size: 13.5px;
    }

    .pa-daftar dd {
        margin: 0;
        color: #18181b;
        font-size: 13.5px;
        font-weight: 600;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .pa-badge {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .pa-badge--sukses {
        background: #ecfdf3;
        color: #067647;
    }

    .pa-badge--menunggu {
        background: #fffaeb;
        color: #b54708;
    }

    .pa-badge--gagal {
        background: #fef3f2;
        color: #b42318;
    }

    /* DAFTAR AKSES */
    .pa-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 2px 0 14px;
        padding: 0;
        list-style: none;
    }

    .pa-chips li {
        padding: 5px 11px;
        border-radius: 999px;
        background: #f9edef;
        color: #800d22;
        font-size: 12.5px;
        font-weight: 600;
    }

    .pa-catatan {
        margin-top: 12px;
        padding: 10px 12px;
        border-radius: 9px;
        background: #faf7f5;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
    }

    /* RESPONSIVE */
    @media (max-width: 1000px) {
        .pa-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .pa-header h1 {
            font-size: 24px;
        }

        .pa-card {
            padding: 18px 16px;
        }

        .pa-avatar {
            width: 72px;
            height: 72px;
        }

        .pa-aksi .pa-btn {
            width: 100%;
        }
    }
</style>

<div class="pa-page">

    {{-- HEADER HALAMAN --}}
    <header class="pa-header">
        <div class="pa-breadcrumb">Dashboard / Pengaturan Akun</div>
        <h1>Pengaturan Akun</h1>
        <p class="pa-subtitle">
            Kelola profil, foto, dan keamanan akun {{ $labelPeran }} SIMAHAD.
        </p>
    </header>

    @if (session('success'))
        <div class="pa-alert pa-alert--sukses" role="status">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="pa-alert pa-alert--gagal" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="pa-alert pa-alert--gagal" role="alert">
            <strong>Periksa kembali data yang dimasukkan.</strong>
            <ul>
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pa-grid">

        {{-- PROFIL DAN FOTO --}}
        <section class="pa-card">
            <div class="pa-profil-head">
                <div class="pa-avatar">
                    @if ($adaFoto)
                        <img src="{{ $urlFoto }}" alt="Foto profil {{ $user->name }}">
                    @else
                        <span class="pa-inisial">{{ $inisial }}</span>
                    @endif
                </div>

                <div class="pa-profil-identitas">
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $labelPeran }} · {{ $user->email ?: 'Email belum tersedia' }}</p>

                    <div class="pa-foto-aksi">
                        <form action="{{ route('pengaturan-akun.foto.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input
                                type="file"
                                name="foto_profil"
                                id="paInputFoto"
                                accept=".jpg,.jpeg,.png,.webp"
                                hidden
                                onchange="this.form.submit()"
                            >
                            <label for="paInputFoto" class="pa-btn pa-btn--garis">
                                {{ $adaFoto ? 'Ganti Foto' : 'Unggah Foto' }}
                            </label>
                        </form>

                        @if ($adaFoto)
                            <form
                                action="{{ route('pengaturan-akun.foto.destroy') }}"
                                method="POST"
                                onsubmit="return confirm('Hapus foto profil akun ini?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="pa-btn pa-btn--bahaya">Hapus Foto</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <p class="pa-muted" style="margin-top: 14px; margin-bottom: 0;">
                Format JPG, JPEG, PNG, atau WEBP. Ukuran maksimal 2 MB.
            </p>

            <hr class="pa-divider">

            <h2 class="pa-judul">Informasi Profil</h2>
            <p class="pa-muted">
                Nama dan email ini dipakai untuk masuk serta tampil di seluruh halaman SIMAHAD.
            </p>

            <form class="pa-form" action="{{ route('pengaturan-akun.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="pa-field">
                    <label for="paNama">Nama Lengkap <span class="pa-wajib">*</span></label>
                    <input
                        type="text"
                        id="paNama"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        maxlength="255"
                        required
                        autocomplete="name"
                    >
                </div>

                <div class="pa-field">
                    <label for="paEmail">Email <span class="pa-wajib">*</span></label>
                    <input
                        type="email"
                        id="paEmail"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        maxlength="255"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="pa-field">
                    <label for="paNoHp">Nomor WhatsApp</label>
                    <input
                        type="text"
                        id="paNoHp"
                        name="no_hp"
                        value="{{ old('no_hp', $user->no_hp) }}"
                        maxlength="20"
                        inputmode="tel"
                        placeholder="Contoh: 081234567890"
                        autocomplete="tel"
                    >
                </div>

                <div class="pa-aksi">
                    <button type="submit" class="pa-btn pa-btn--utama">Simpan Perubahan</button>
                </div>
            </form>
        </section>

        <div class="pa-kolom">

            {{-- KEAMANAN AKUN --}}
            <section class="pa-card">
                <h2 class="pa-judul">Keamanan Akun</h2>
                <p class="pa-muted">
                    Gunakan kata sandi yang kuat dan berbeda dari akun lain. Minimal 8 karakter.
                </p>

                <form class="pa-form" action="{{ route('pengaturan-akun.password.update') }}" method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="pa-field">
                        <label for="paPasswordLama">Kata Sandi Saat Ini <span class="pa-wajib">*</span></label>
                        <input
                            type="password"
                            id="paPasswordLama"
                            name="password_lama"
                            required
                            autocomplete="current-password"
                        >
                    </div>

                    <div class="pa-field">
                        <label for="paPasswordBaru">Kata Sandi Baru <span class="pa-wajib">*</span></label>
                        <input
                            type="password"
                            id="paPasswordBaru"
                            name="password"
                            minlength="8"
                            required
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="pa-field">
                        <label for="paPasswordKonfirmasi">Konfirmasi Kata Sandi Baru <span class="pa-wajib">*</span></label>
                        <input
                            type="password"
                            id="paPasswordKonfirmasi"
                            name="password_confirmation"
                            minlength="8"
                            required
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="pa-aksi">
                        <button type="submit" class="pa-btn pa-btn--utama">Perbarui Kata Sandi</button>
                    </div>
                </form>
            </section>

            {{-- INFORMASI AKUN --}}
            <section class="pa-card">
                <h2 class="pa-judul">Informasi Akun</h2>

                <dl class="pa-daftar">
                    <div>
                        <dt>Peran</dt>
                        <dd>{{ $labelPeran }}</dd>
                    </div>
                    <div>
                        <dt>Status Akun</dt>
                        <dd><span class="pa-badge {{ $kelasStatus }}">{{ $labelStatus }}</span></dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $user->email ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt>Nomor WhatsApp</dt>
                        <dd>{{ $user->no_hp ?: 'Belum diisi' }}</dd>
                    </div>
                    <div>
                        <dt>Verifikasi Email</dt>
                        <dd>{{ $user->email_verified_at ? 'Terverifikasi' : 'Belum terverifikasi' }}</dd>
                    </div>
                    <div>
                        <dt>Terdaftar Sejak</dt>
                        <dd>{{ optional($user->created_at)->translatedFormat('d F Y') ?: '-' }}</dd>
                    </div>
                </dl>
            </section>

            {{-- AKSES SESUAI ROLE --}}
            <section class="pa-card">
                <h2 class="pa-judul">Akses {{ $labelPeran }}</h2>
                <p class="pa-muted">
                    Menu yang dapat dibuka oleh akun dengan peran {{ $labelPeran }}.
                </p>

                <ul class="pa-chips">
                    @foreach ($daftarAkses as $akses)
                        <li>{{ $akses }}</li>
                    @endforeach
                </ul>

                @if ($tautanAkses)
                    <a class="pa-btn pa-btn--garis" href="{{ route($tautanAkses['route']) }}">
                        {{ $tautanAkses['label'] }}
                    </a>
                @endif
            </section>

            {{-- BIODATA KEPEGAWAIAN --}}
            @if ($pegawai)
                <section class="pa-card">
                    <h2 class="pa-judul">Biodata Kepegawaian</h2>

                    <dl class="pa-daftar">
                        <div>
                            <dt>Jenis Kelamin</dt>
                            <dd>
                                {{ $pegawai->jenis_kelamin === 'L' ? 'Laki-laki' : ($pegawai->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                            </dd>
                        </div>
                        <div>
                            <dt>Pendidikan Terakhir</dt>
                            <dd>{{ $pegawai->pendidikan_terakhir ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt>Alamat</dt>
                            <dd>{{ $pegawai->alamat ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt>Status Kepegawaian</dt>
                            <dd>{{ $pegawai->status_pegawai ? 'Aktif' : 'Tidak Aktif' }}</dd>
                        </div>
                    </dl>

                    <p class="pa-catatan">
                        Biodata kepegawaian hanya dapat diubah Admin melalui menu
                        <strong>Data Pegawai</strong>.
                    </p>
                </section>
            @elseif ($rolePengguna === 'admin')
                <section class="pa-card">
                    <h2 class="pa-judul">Biodata Kepegawaian</h2>
                    <p class="pa-muted" style="margin-bottom: 0;">
                        Akun ini tidak tertaut dengan Data Pegawai. Jika akun Admin ini juga
                        merupakan pegawai Ma’had, penautannya dilakukan melalui menu
                        <strong>Manajemen Pengguna</strong>.
                    </p>
                </section>
            @endif

        </div>
    </div>

</div>
