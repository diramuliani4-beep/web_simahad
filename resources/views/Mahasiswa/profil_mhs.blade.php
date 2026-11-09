<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil &amp; Pengaturan Akun - {{ $namaSistem ?? 'SIMAHAD' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --maroon: #6B0D1B;
            --maroon-dark: #560A15;
            --maroon-tint: #F9EDF0;
            --gold: #C9A96A;
            --gold-soft: #FBF4E6;
            --bg: #F8F5F1;
            --line: #E8DADD;
            --text: #2F2A2B;
            --muted: #7A6A6D;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ================= NAVBAR ================= */
        .navbar {
            height: 68px;
            background: #ffffff;
            border-bottom: 1.5px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 60;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--maroon);
            font-size: 21px;
            font-weight: 800;
            letter-spacing: .02em;
            text-decoration: none;
        }

        .brand img {
            height: 38px;
            width: auto;
        }

        .navbar-kanan {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .navbar-tautan {
            color: var(--maroon);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color .16s ease;
        }

        .navbar-tautan:hover {
            background: var(--maroon-tint);
        }

        /* ================= LAYOUT ================= */
        .container {
            max-width: 880px;
            margin: 34px auto 48px;
            padding: 0 20px;
        }

        .notif {
            border-radius: 9px;
            padding: 13px 15px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 14px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            line-height: 1.5;
        }

        .notif--sukses {
            background: #E8F5E9;
            color: #287D3C;
            border: 1px solid #BDE3C4;
        }

        .notif--gagal {
            background: #FCE8E6;
            color: #B42318;
            border: 1px solid #F3C6C1;
        }

        .notif ul {
            margin: 6px 0 0 18px;
        }

        .kartu {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(60, 10, 20, .06);
            overflow: hidden;
        }

        /* ================= HERO PROFIL ================= */
        .hero {
            position: relative;
            padding: 34px 30px 26px;
            text-align: center;
            background:
                radial-gradient(circle at 12% 0%, rgba(201, 169, 106, .18), transparent 55%),
                linear-gradient(140deg, #781023 0%, #6B0D1B 55%, #520A14 100%);
            color: #ffffff;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), #E5CE9D, var(--gold));
        }

        .avatar-wrap {
            position: relative;
            width: 128px;
            height: 128px;
            margin: 0 auto 14px;
        }

        .avatar {
            width: 128px;
            height: 128px;
            border-radius: 50%;
            overflow: hidden;
            background: var(--maroon-tint);
            border: 4px solid var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .22);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .avatar__inisial {
            font-size: 44px;
            font-weight: 800;
            color: var(--maroon);
        }

        .avatar-kamera {
            position: absolute;
            right: 2px;
            bottom: 2px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid #ffffff;
            background: var(--gold);
            color: #43110F;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform .16s ease, background-color .16s ease;
        }

        .avatar-kamera:hover {
            background: #DDBB7C;
            transform: scale(1.06);
        }

        .avatar-kamera svg {
            width: 19px;
            height: 19px;
        }

        .foto-menu {
            position: absolute;
            top: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
            width: 186px;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 16px 34px rgba(40, 5, 12, .24);
            padding: 6px;
            z-index: 30;
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .foto-menu[hidden] {
            display: none;
        }

        .foto-menu__item {
            border: none;
            background: transparent;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            color: var(--maroon);
            padding: 10px 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 9px;
            text-align: left;
        }

        .foto-menu__item svg {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            color: #9C7B34;
        }

        .foto-menu__item:hover:not(:disabled) {
            background: var(--maroon-tint);
        }

        .foto-menu__item:disabled {
            color: #B9AAAC;
            cursor: not-allowed;
        }

        .foto-menu__item--hapus {
            color: #B42318;
        }

        .foto-menu__item--hapus svg {
            color: #B42318;
        }

        .hero h1 {
            font-size: 23px;
            font-weight: 800;
            line-height: 1.25;
        }

        .hero__nim {
            margin-top: 6px;
            font-size: 13px;
            color: #F3DFD2;
            letter-spacing: .02em;
        }

        .hero__badges {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 11px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .badge--emas {
            background: var(--gold);
            color: #43110F;
        }

        .badge--sukses {
            background: rgba(232, 245, 233, .16);
            border: 1px solid rgba(200, 235, 205, .55);
            color: #E7F6E9;
        }

        .badge--menunggu {
            background: rgba(255, 226, 170, .16);
            border: 1px solid rgba(255, 226, 170, .55);
            color: #FFE7B8;
        }

        .badge--gagal {
            background: rgba(255, 205, 200, .16);
            border: 1px solid rgba(255, 205, 200, .55);
            color: #FFD9D4;
        }

        /* ================= ISI ================= */
        .isi {
            padding: 26px 30px 30px;
        }

        .judul-bagian {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 800;
            color: var(--maroon);
            letter-spacing: .01em;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .judul-bagian::before {
            content: "";
            width: 4px;
            height: 18px;
            border-radius: 4px;
            background: var(--gold);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px 22px;
        }

        .info-item {
            background: #FDFBFA;
            border: 1px solid #F0E6E4;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .info-item__label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 5px;
        }

        .info-item__nilai {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            overflow-wrap: anywhere;
            line-height: 1.45;
        }

        .info-item__nilai--kosong {
            color: #A99B9D;
            font-weight: 600;
        }

        .catatan {
            margin-top: 18px;
            background: var(--gold-soft);
            border: 1px solid #EFE0C4;
            border-left: 4px solid var(--gold);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 12px;
            line-height: 1.6;
            color: #6B5330;
        }

        .aksi {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }

        .tombol {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid transparent;
            border-radius: 9px;
            padding: 11px 18px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: background-color .16s ease, border-color .16s ease, color .16s ease;
        }

        .tombol svg {
            width: 16px;
            height: 16px;
        }

        .tombol--utama {
            background: var(--maroon);
            color: #ffffff;
        }

        .tombol--utama:hover {
            background: var(--maroon-dark);
        }

        .tombol--emas {
            background: var(--gold);
            color: #43110F;
        }

        .tombol--emas:hover {
            background: #DDBB7C;
        }

        .tombol--garis {
            background: #ffffff;
            color: var(--maroon);
            border-color: #D9C5C9;
        }

        .tombol--garis:hover {
            background: var(--maroon-tint);
            border-color: var(--maroon);
        }

        /* ================= FORM EDIT ================= */
        .form-edit {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px 20px;
        }

        .form-edit__grup {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-edit__grup--penuh {
            grid-column: 1 / -1;
        }

        .form-edit label {
            font-size: 12px;
            font-weight: 700;
            color: #55494B;
        }

        .form-edit input {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #D9C5C9;
            border-radius: 9px;
            font-family: inherit;
            font-size: 13px;
            color: var(--text);
            background: #ffffff;
            transition: border-color .16s ease, box-shadow .16s ease;
        }

        .form-edit input:focus {
            outline: none;
            border-color: var(--maroon);
            box-shadow: 0 0 0 3px rgba(107, 13, 27, .1);
        }

        .form-edit input[type="file"] {
            padding: 9px;
            background: #FDFBFA;
        }

        .form-edit__bantuan {
            font-size: 11px;
            color: var(--muted);
            line-height: 1.55;
        }

        .form-edit__error {
            font-size: 11.5px;
            font-weight: 600;
            color: #B42318;
        }

        .foto-lama {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--gold);
            display: block;
            margin-bottom: 8px;
        }

        .form-edit__aksi {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 6px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
        }

        /* ================= MODAL FOTO ================= */
        .modal-foto {
            position: fixed;
            inset: 0;
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .modal-foto[hidden] {
            display: none;
        }

        .modal-foto__latar {
            position: absolute;
            inset: 0;
            background: rgba(28, 4, 9, .78);
            backdrop-filter: blur(2px);
        }

        .modal-foto__isi {
            position: relative;
            z-index: 1;
            background: #ffffff;
            border-radius: 14px;
            padding: 12px;
            max-width: min(440px, 92vw);
            box-shadow: 0 24px 60px rgba(0, 0, 0, .4);
            text-align: center;
        }

        .modal-foto__isi img {
            display: block;
            width: 100%;
            max-height: 68vh;
            object-fit: contain;
            border-radius: 9px;
            background: #F4EFEA;
        }

        .modal-foto__isi figcaption {
            margin-top: 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--maroon);
        }

        .modal-foto__tutup {
            position: absolute;
            top: -12px;
            right: -12px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 2px solid #ffffff;
            background: var(--maroon);
            color: #ffffff;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pesan-foto {
            margin-top: 14px;
            background: #FCE8E6;
            color: #B42318;
            border: 1px solid #F3C6C1;
            border-radius: 9px;
            padding: 11px 13px;
            font-size: 12px;
            font-weight: 600;
        }

        .pesan-foto[hidden] {
            display: none;
        }

        /* ================= RESPONSIF ================= */
        @media (max-width: 720px) {
            .navbar {
                padding: 0 18px;
            }

            .navbar-tautan {
                display: none;
            }

            .hero {
                padding: 28px 18px 22px;
            }

            .isi {
                padding: 22px 18px 24px;
            }

            .info-grid,
            .form-edit {
                grid-template-columns: 1fr;
            }

            .aksi {
                flex-direction: column-reverse;
            }

            .aksi .tombol {
                width: 100%;
            }

            .form-edit__aksi {
                flex-direction: column-reverse;
            }

            .form-edit__aksi .tombol {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <header class="navbar">
        <a href="{{ route('dashboard') }}" class="brand">
            <img src="{{ asset('images/logo_uin.png') }}" alt="Logo SIMAHAD">
            <span>{{ $namaSistem ?? 'SIMAHAD' }}</span>
        </a>

        <div class="navbar-kanan">
            <a href="{{ route('dashboard') }}" class="navbar-tautan">Dashboard</a>
            <a href="{{ route('data.mahasiswa') }}" class="navbar-tautan">Data Mahasiswa</a>
            <x-mahasiswa.profile-dropdown />
        </div>
    </header>

    <main class="container">
        @if (session('success'))
            <div class="notif notif--sukses">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="notif notif--gagal">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="notif notif--gagal">
                <div>
                    <strong>Periksa kembali data yang kamu masukkan.</strong>
                    <ul>
                        @foreach ($errors->all() as $pesan)
                            <li>{{ $pesan }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php
            $adaFoto = filled($user->foto_profil);
            $urlFoto = $adaFoto ? asset('storage/'.$user->foto_profil) : null;
            $inisial = strtoupper(mb_substr($user->name ?? 'M', 0, 1));

            $statusAkun = strtolower((string) $user->status);
            $labelStatus = match ($statusAkun) {
                'aktif' => 'Akun Aktif',
                'pending', 'menunggu aktivasi' => 'Menunggu Verifikasi',
                'rejected', 'nonaktif' => 'Tidak Aktif',
                default => $user->status ? ucfirst($statusAkun) : 'Tidak Diketahui',
            };
            $kelasStatus = match ($statusAkun) {
                'aktif' => 'badge--sukses',
                'pending', 'menunggu aktivasi' => 'badge--menunggu',
                default => 'badge--gagal',
            };

            $labelKamar = $kamar
                ? trim('Kamar '.($kamar->nomor_kamar ?: $kamar->nama_kamar).($kamar->gedung ? ' · '.$kamar->gedung : ''))
                : null;
        @endphp

        <section class="kartu">
            <div class="hero">
                <div class="avatar-wrap">
                    <div class="avatar">
                        @if ($adaFoto)
                            <img src="{{ $urlFoto }}" alt="Foto profil {{ $user->name }}">
                        @else
                            <span class="avatar__inisial">{{ $inisial }}</span>
                        @endif
                    </div>

                    @unless ($modeEdit)
                        <button
                            type="button"
                            class="avatar-kamera"
                            id="btnMenuFoto"
                            aria-haspopup="true"
                            aria-expanded="false"
                            aria-controls="menuFoto"
                            title="Kelola foto profil"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                        </button>

                        <div class="foto-menu" id="menuFoto" hidden>
                            <button type="button" class="foto-menu__item" id="aksiLihatFoto" @disabled(! $adaFoto)>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>Lihat Foto</span>
                            </button>

                            <button type="button" class="foto-menu__item" id="aksiGantiFoto">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                                <span>Ganti Foto</span>
                            </button>

                            <button type="button" class="foto-menu__item foto-menu__item--hapus" id="aksiHapusFoto" @disabled(! $adaFoto)>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M3 6h18"></path>
                                    <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"></path>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                    <path d="M10 11v6M14 11v6"></path>
                                </svg>
                                <span>Hapus Foto</span>
                            </button>
                        </div>
                    @endunless
                </div>

                <h1>{{ $user->name }}</h1>
                <p class="hero__nim">{{ $user->nim ? 'NIM. '.$user->nim : 'NIM belum terdaftar' }}</p>

                <div class="hero__badges">
                    <span class="badge badge--emas">Mahasiswa Ma'had</span>
                    <span class="badge {{ $kelasStatus }}">{{ $labelStatus }}</span>
                </div>
            </div>

            <div class="isi">
                <div class="pesan-foto" id="pesanFoto" hidden></div>

                @if ($modeEdit)
                    <h2 class="judul-bagian">Edit Profil</h2>

                    <form action="{{ route('profil.mhs.update') }}" method="POST" enctype="multipart/form-data" class="form-edit">
                        @csrf
                        @method('PUT')

                        <div class="form-edit__grup form-edit__grup--penuh">
                            <label for="foto_profil">Foto Profil (opsional)</label>

                            @if ($adaFoto)
                                <img src="{{ $urlFoto }}" alt="Foto profil saat ini" class="foto-lama">
                            @endif

                            <input type="file" name="foto_profil" id="foto_profil" accept=".jpg,.jpeg,.png,.webp">
                            <p class="form-edit__bantuan">
                                Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                                Kosongkan jika tidak ingin mengganti foto.
                            </p>
                            @error('foto_profil')
                                <span class="form-edit__error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-edit__grup">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @error('name')
                                <span class="form-edit__error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-edit__grup">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                            @error('email')
                                <span class="form-edit__error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-edit__grup form-edit__grup--penuh">
                            <label for="no_hp">Nomor WhatsApp</label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                   inputmode="tel" placeholder="Contoh: 081234567890" autocomplete="tel">
                            @error('no_hp')
                                <span class="form-edit__error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-edit__grup form-edit__grup--penuh">
                            <div class="catatan">
                                NIM, status akun, role, dan data verifikasi tidak dapat diubah dari halaman ini.
                                Perubahan data akademik dan penerimaan dilakukan oleh Musyrifah atau Admin.
                            </div>
                        </div>

                        <div class="form-edit__aksi">
                            <a href="{{ route('profil.mhs') }}" class="tombol tombol--garis">Batal</a>
                            <button type="submit" class="tombol tombol--utama">Simpan Perubahan</button>
                        </div>
                    </form>
                @else
                    <h2 class="judul-bagian">Informasi Akun</h2>

                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-item__label">Nama Lengkap</span>
                            <span class="info-item__nilai">{{ $user->name ?: '-' }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-item__label">NIM</span>
                            <span class="info-item__nilai {{ $user->nim ? '' : 'info-item__nilai--kosong' }}">
                                {{ $user->nim ?: 'Belum terdaftar' }}
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-item__label">Email</span>
                            <span class="info-item__nilai">{{ $user->email ?: '-' }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-item__label">Nomor WhatsApp</span>
                            <span class="info-item__nilai {{ $user->no_hp ? '' : 'info-item__nilai--kosong' }}">
                                {{ $user->no_hp ?: 'Belum diisi' }}
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-item__label">Status Akun</span>
                            <span class="info-item__nilai">{{ $labelStatus }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-item__label">Kamar Ma'had</span>
                            <span class="info-item__nilai {{ $labelKamar ? '' : 'info-item__nilai--kosong' }}">
                                {{ $labelKamar ?: 'Belum ditempatkan' }}
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-item__label">Terdaftar Sejak</span>
                            <span class="info-item__nilai">
                                {{ optional($user->created_at)->translatedFormat('d F Y') ?: '-' }}
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-item__label">Verifikasi Email</span>
                            <span class="info-item__nilai {{ $user->email_verified_at ? '' : 'info-item__nilai--kosong' }}">
                                {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum terverifikasi' }}
                            </span>
                        </div>
                    </div>

                    <h2 class="judul-bagian">Keamanan Akun</h2>

                    <form action="{{ route('profil.mhs.password.update') }}" method="POST" class="form-edit" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div class="form-edit__grup form-edit__grup--penuh">
                            <label for="password_lama">Kata Sandi Saat Ini</label>
                            <input type="password" name="password_lama" id="password_lama" required autocomplete="current-password">
                            @error('password_lama')
                                <span class="form-edit__error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-edit__grup">
                            <label for="password">Kata Sandi Baru</label>
                            <input type="password" name="password" id="password" minlength="8" required autocomplete="new-password">
                            @error('password')
                                <span class="form-edit__error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-edit__grup">
                            <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" minlength="8" required autocomplete="new-password">
                        </div>

                        <div class="form-edit__grup form-edit__grup--penuh">
                            <p class="form-edit__bantuan">
                                Gunakan minimal 8 karakter. Kata sandi baru harus berbeda dari kata sandi saat ini.
                            </p>
                        </div>

                        <div class="form-edit__aksi">
                            <button type="submit" class="tombol tombol--utama">Perbarui Kata Sandi</button>
                        </div>
                    </form>

                    <div class="catatan">
                        Data akademik dan penerimaan (fakultas, program studi, angkatan, NIM, serta status)
                        hanya dapat diubah oleh Musyrifah atau Admin. Lihat rinciannya pada menu
                        <strong>Data Mahasiswa</strong>.
                    </div>

                    <div class="aksi">
                        <a href="{{ route('dashboard') }}" class="tombol tombol--garis">Kembali ke Dashboard</a>

                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="{{ route('data.mahasiswa') }}" class="tombol tombol--emas">Data Mahasiswa</a>
                            <a href="{{ route('profil.mhs', ['edit' => 1]) }}" class="tombol tombol--utama">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Edit Profil
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>

    @if ($adaFoto)
        <div class="modal-foto" id="modalFoto" hidden role="dialog" aria-modal="true" aria-label="Foto profil {{ $user->name }}">
            <div class="modal-foto__latar" data-tutup-foto></div>

            <figure class="modal-foto__isi">
                <button type="button" class="modal-foto__tutup" data-tutup-foto aria-label="Tutup">&times;</button>
                <img src="{{ $urlFoto }}" alt="Foto profil {{ $user->name }} ukuran penuh">
                <figcaption>{{ $user->name }}</figcaption>
            </figure>
        </div>
    @endif

    {{-- Form tersembunyi untuk unggah dan hapus foto profil --}}
    <form action="{{ route('profil.mhs.foto.update') }}" method="POST" enctype="multipart/form-data" id="formGantiFoto" hidden>
        @csrf
        <input type="file" name="foto_profil" id="inputFotoProfil" accept=".jpg,.jpeg,.png,.webp">
    </form>

    <form action="{{ route('profil.mhs.foto.destroy') }}" method="POST" id="formHapusFoto" hidden>
        @csrf
        @method('DELETE')
    </form>

    <script>
        (function () {
            var tombolMenu = document.getElementById('btnMenuFoto');
            var menuFoto = document.getElementById('menuFoto');
            var pesanFoto = document.getElementById('pesanFoto');

            function tampilkanPesanFoto(pesan) {
                if (!pesanFoto) {
                    return;
                }

                pesanFoto.textContent = pesan;
                pesanFoto.hidden = false;
            }

            function tutupMenuFoto() {
                if (!menuFoto || !tombolMenu) {
                    return;
                }

                menuFoto.hidden = true;
                tombolMenu.setAttribute('aria-expanded', 'false');
            }

            if (tombolMenu && menuFoto) {
                tombolMenu.addEventListener('click', function (event) {
                    event.stopPropagation();

                    var akanTampil = menuFoto.hidden;
                    menuFoto.hidden = !akanTampil;
                    tombolMenu.setAttribute('aria-expanded', akanTampil ? 'true' : 'false');
                });

                document.addEventListener('click', function (event) {
                    if (!menuFoto.hidden && !menuFoto.contains(event.target) && !tombolMenu.contains(event.target)) {
                        tutupMenuFoto();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        tutupMenuFoto();
                    }
                });
            }

            var modalFoto = document.getElementById('modalFoto');

            function bukaFoto() {
                if (modalFoto) {
                    modalFoto.hidden = false;
                }
            }

            function tutupFoto() {
                if (modalFoto) {
                    modalFoto.hidden = true;
                }
            }

            document.querySelectorAll('[data-tutup-foto]').forEach(function (tombol) {
                tombol.addEventListener('click', tutupFoto);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    tutupFoto();
                }
            });

            var aksiLihatFoto = document.getElementById('aksiLihatFoto');

            if (aksiLihatFoto) {
                aksiLihatFoto.addEventListener('click', function () {
                    tutupMenuFoto();
                    bukaFoto();
                });
            }

            var formGantiFoto = document.getElementById('formGantiFoto');
            var inputFotoProfil = document.getElementById('inputFotoProfil');
            var aksiGantiFoto = document.getElementById('aksiGantiFoto');

            if (aksiGantiFoto && inputFotoProfil) {
                aksiGantiFoto.addEventListener('click', function () {
                    tutupMenuFoto();
                    inputFotoProfil.click();
                });
            }

            if (inputFotoProfil && formGantiFoto) {
                inputFotoProfil.addEventListener('change', function () {
                    if (!this.files.length) {
                        return;
                    }

                    if (this.files[0].size > 2 * 1024 * 1024) {
                        tampilkanPesanFoto('Ukuran foto melebihi 2 MB. Silakan pilih foto lain.');
                        this.value = '';
                        tutupMenuFoto();
                        return;
                    }

                    formGantiFoto.submit();
                });
            }

            var aksiHapusFoto = document.getElementById('aksiHapusFoto');
            var formHapusFoto = document.getElementById('formHapusFoto');

            if (aksiHapusFoto && formHapusFoto) {
                aksiHapusFoto.addEventListener('click', function () {
                    tutupMenuFoto();

                    if (window.confirm('Hapus foto profil Anda? Avatar default akan digunakan kembali.')) {
                        formHapusFoto.submit();
                    }
                });
            }
        })();
    </script>
</body>
</html>
