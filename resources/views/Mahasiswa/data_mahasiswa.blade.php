<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - SIMAHAD</title>

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
            max-width: 920px;
            margin: 34px auto 48px;
            padding: 0 20px;
        }

        .notif {
            border-radius: 9px;
            padding: 13px 15px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 14px;
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

        /* ================= HERO ================= */
        .hero {
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            padding: 28px 30px 26px;
            color: #ffffff;
            background:
                radial-gradient(circle at 90% 0%, rgba(201, 169, 106, .22), transparent 55%),
                linear-gradient(140deg, #781023 0%, #6B0D1B 55%, #520A14 100%);
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), #E5CE9D, var(--gold));
        }

        .hero__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 8px;
        }

        .hero h1 {
            font-size: 24px;
            font-weight: 800;
            line-height: 1.25;
        }

        .hero__meta {
            margin-top: 7px;
            font-size: 13px;
            color: #F3DFD2;
            line-height: 1.6;
        }

        .hero__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 12px;
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            background: rgba(232, 245, 233, .16);
            border: 1px solid rgba(200, 235, 205, .55);
            color: #E7F6E9;
        }

        .hero__badge--menunggu {
            background: rgba(255, 226, 170, .16);
            border-color: rgba(255, 226, 170, .55);
            color: #FFE7B8;
        }

        /* ================= ISI ================= */
        .isi {
            padding: 26px 30px 30px;
        }

        .bagian + .bagian {
            margin-top: 26px;
        }

        .judul-bagian {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 800;
            color: var(--maroon);
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

        .info-item__kunci {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-left: 6px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #9C7B34;
            background: var(--gold-soft);
            border: 1px solid #EFE0C4;
            border-radius: 999px;
            padding: 2px 7px;
            vertical-align: middle;
        }

        .catatan {
            margin-top: 20px;
            background: var(--gold-soft);
            border: 1px solid #EFE0C4;
            border-left: 4px solid var(--gold);
            border-radius: 10px;
            padding: 13px 15px;
            font-size: 12px;
            line-height: 1.65;
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

        /* ================= KOSONG ================= */
        .kosong {
            text-align: center;
            padding: 40px 20px 34px;
        }

        .kosong__ikon {
            width: 76px;
            height: 76px;
            margin: 0 auto 16px;
            border-radius: 50%;
            background: var(--maroon-tint);
            border: 2px solid var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--maroon);
        }

        .kosong__ikon svg {
            width: 34px;
            height: 34px;
        }

        .kosong h2 {
            font-size: 17px;
            font-weight: 800;
            color: var(--maroon);
        }

        .kosong p {
            max-width: 460px;
            margin: 10px auto 0;
            font-size: 13px;
            line-height: 1.65;
            color: var(--muted);
        }

        .kosong__status {
            display: inline-block;
            margin-top: 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #6B5330;
            background: var(--gold-soft);
            border: 1px solid #EFE0C4;
            border-radius: 999px;
            padding: 5px 12px;
        }

        .kosong__aksi {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
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

        .form-edit input,
        .form-edit select {
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

        .form-edit input:focus,
        .form-edit select:focus {
            outline: none;
            border-color: var(--maroon);
            box-shadow: 0 0 0 3px rgba(107, 13, 27, .1);
        }

        .form-edit input[readonly] {
            background: #F5EFEA;
            color: #6B5F61;
        }

        .form-edit__error {
            font-size: 11.5px;
            font-weight: 600;
            color: #B42318;
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

        .terkunci {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            background: #F7F2ED;
            border: 1px dashed #D9C5C9;
            border-radius: 10px;
            padding: 14px;
        }

        .terkunci__item span {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #9A8B8D;
            margin-bottom: 3px;
        }

        .terkunci__item strong {
            font-size: 12.5px;
            color: #574A4C;
            overflow-wrap: anywhere;
        }

        /* ================= RESPONSIF ================= */
        .navbar-kiri {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .navbar-menu {
            display: none;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            padding: 0;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #ffffff;
            color: var(--maroon);
            cursor: pointer;
        }

        .navbar-menu svg {
            width: 19px;
            height: 19px;
        }

        @media (max-width: 991px) {
            .navbar-menu {
                display: grid;
            }
        }

        @media (max-width: 720px) {
            .navbar {
                padding: 0 18px;
            }

            .navbar-tautan {
                display: none;
            }

            .hero {
                padding: 24px 18px 22px;
            }

            .isi {
                padding: 22px 18px 24px;
            }

            .info-grid,
            .form-edit,
            .terkunci {
                grid-template-columns: 1fr;
            }

            .terkunci {
                gap: 10px;
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

<body class="sb-mandiri">
    {{-- Sidebar bersama seluruh role SIMAHAD. --}}
    <x-panel.sidebar ragam="mandiri" />

    <header class="navbar">
        <div class="navbar-kiri">
            <button type="button"
                class="navbar-menu"
                data-sb-buka
                aria-label="Buka menu navigasi"
                aria-controls="sidebar"
                aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="brand">
                <img src="{{ asset('images/logo_uin.png') }}" alt="Logo SIMAHAD">
                <span>SIMAHAD</span>
            </a>
        </div>

        <div class="navbar-kanan">
            <a href="{{ route('dashboard') }}" class="navbar-tautan">Dashboard</a>
            <a href="{{ route('profil.mhs') }}" class="navbar-tautan">Lihat Profil</a>
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
            $labelKamar = $kamar
                ? trim('Kamar '.($kamar->nomor_kamar ?: $kamar->nama_kamar).($kamar->gedung ? ' · '.$kamar->gedung : ''))
                : null;

            $labelKategori = match (strtolower((string) $pendaftaran?->kategori)) {
                'kip' => 'KIP',
                'umum' => 'Umum',
                default => $pendaftaran?->kategori ? ucfirst((string) $pendaftaran->kategori) : '-',
            };

            $noHpMahasiswa = $user->no_hp ?: $pendaftaran?->no_wa;
        @endphp

        <section class="kartu">
            <div class="hero">
                <div>
                    <span class="hero__eyebrow">Data Mahasiswa</span>
                    <h1>{{ $user->name }}</h1>
                    <p class="hero__meta">
                        {{ $user->nim ? 'NIM. '.$user->nim : 'NIM belum terdaftar' }}
                        @if ($pendaftaran && filled($pendaftaran->program_studi))
                            &middot; {{ $pendaftaran->program_studi }}
                        @endif
                        @if ($pendaftaran && filled($pendaftaran->fakultas))
                            &middot; {{ $pendaftaran->fakultas }}
                        @endif
                    </p>

                    <span class="hero__badge {{ $statusPendaftaran === 'Disetujui' ? '' : 'hero__badge--menunggu' }}">
                        Pendaftaran: {{ $statusPendaftaran }}
                    </span>
                </div>

                <a href="{{ route('profil.mhs') }}" class="tombol tombol--emas">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Lihat Profil
                </a>
            </div>

            <div class="isi">
                @if (! $pendaftaran)
                    <div class="kosong">
                        <div class="kosong__ikon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <path d="M14 2v6h6"></path>
                                <path d="M9 14h6M9 18h4"></path>
                            </svg>
                        </div>

                        <h2>Data pendaftaran belum disetujui</h2>
                        <p>
                            Data mahasiswa akan tampil pada halaman ini setelah pendaftaran Anda
                            disetujui oleh Musyrifah. Pastikan seluruh berkas pendaftaran sudah lengkap
                            dan terverifikasi.
                        </p>

                        <span class="kosong__status">Status: {{ $statusPendaftaran }}</span>

                        <div class="kosong__aksi">
                            <a href="{{ route('pendaftaran.pribadi') }}" class="tombol tombol--utama">
                                Buka Pendaftaran
                            </a>
                            <a href="{{ route('dashboard') }}" class="tombol tombol--garis">
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                @elseif ($modeEdit)
                    <div class="bagian">
                        <h2 class="judul-bagian">Edit Data Mahasiswa</h2>

                        <form action="{{ route('data.mahasiswa.update') }}" method="POST" class="form-edit">
                            @csrf
                            @method('PUT')

                            <div class="terkunci">
                                <div class="terkunci__item">
                                    <span>NIM</span>
                                    <strong>{{ $user->nim ?: '-' }}</strong>
                                </div>
                                <div class="terkunci__item">
                                    <span>Fakultas</span>
                                    <strong>{{ $pendaftaran->fakultas ?: '-' }}</strong>
                                </div>
                                <div class="terkunci__item">
                                    <span>Program Studi</span>
                                    <strong>{{ $pendaftaran->program_studi ?: '-' }}</strong>
                                </div>
                            </div>

                            <div class="form-edit__grup form-edit__grup--penuh">
                                <label for="nama_lengkap">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap"
                                       value="{{ old('nama_lengkap', $pendaftaran->nama_lengkap ?: $user->name) }}" required>
                                @error('nama_lengkap')
                                    <span class="form-edit__error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-edit__grup">
                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="jenis_kelamin" required>
                                    <option value="" disabled @selected(! old('jenis_kelamin', $pendaftaran->jenis_kelamin))>Pilih jenis kelamin</option>
                                    <option value="Laki-laki" @selected(old('jenis_kelamin', $pendaftaran->jenis_kelamin) === 'Laki-laki')>Laki-laki</option>
                                    <option value="Perempuan" @selected(old('jenis_kelamin', $pendaftaran->jenis_kelamin) === 'Perempuan')>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <span class="form-edit__error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-edit__grup">
                                <label for="no_hp">Nomor HP Mahasiswa</label>
                                <input type="text" name="no_hp" id="no_hp" inputmode="tel"
                                       value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 081234567890">
                                @error('no_hp')
                                    <span class="form-edit__error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-edit__grup">
                                <label for="no_wa">Nomor WhatsApp</label>
                                <input type="text" name="no_wa" id="no_wa" inputmode="tel"
                                       value="{{ old('no_wa', $pendaftaran->no_wa) }}" placeholder="Contoh: 081234567890">
                                @error('no_wa')
                                    <span class="form-edit__error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-edit__grup">
                                <label for="asal_sekolah">Asal Sekolah</label>
                                <input type="text" name="asal_sekolah" id="asal_sekolah"
                                       value="{{ old('asal_sekolah', $pendaftaran->asal_sekolah) }}" placeholder="Contoh: MAN 1 Padang">
                                @error('asal_sekolah')
                                    <span class="form-edit__error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-edit__grup">
                                <label for="nama_ortu">Nama Orang Tua / Wali</label>
                                <input type="text" name="nama_ortu" id="nama_ortu"
                                       value="{{ old('nama_ortu', $pendaftaran->nama_ortu) }}">
                                @error('nama_ortu')
                                    <span class="form-edit__error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-edit__grup">
                                <label for="no_hp_ortu">Nomor HP Orang Tua / Wali</label>
                                <input type="text" name="no_hp_ortu" id="no_hp_ortu" inputmode="tel"
                                       value="{{ old('no_hp_ortu', $pendaftaran->no_hp_ortu) }}" placeholder="Contoh: 081234567890">
                                @error('no_hp_ortu')
                                    <span class="form-edit__error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-edit__grup form-edit__grup--penuh">
                                <div class="catatan">
                                    NIM, status penerimaan, status mahasiswa, role, serta data verifikasi
                                    Musyrifah/Admin tidak dapat diubah dari halaman ini. Perubahan data
                                    akademik (fakultas, program studi, angkatan) juga dilakukan oleh
                                    Musyrifah atau Admin.
                                </div>
                            </div>

                            <div class="form-edit__aksi">
                                <a href="{{ route('data.mahasiswa') }}" class="tombol tombol--garis">Batal</a>
                                <button type="submit" class="tombol tombol--utama">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bagian">
                        <h2 class="judul-bagian">Data Akademik</h2>

                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-item__label">NIM <span class="info-item__kunci">Terkunci</span></span>
                                <span class="info-item__nilai {{ $user->nim ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $user->nim ?: 'Belum terdaftar' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Nama Lengkap</span>
                                <span class="info-item__nilai">{{ $pendaftaran->nama_lengkap ?: $user->name }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Fakultas <span class="info-item__kunci">Terkunci</span></span>
                                <span class="info-item__nilai {{ filled($pendaftaran->fakultas) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->fakultas ?: 'Belum diisi' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Program Studi <span class="info-item__kunci">Terkunci</span></span>
                                <span class="info-item__nilai {{ filled($pendaftaran->program_studi) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->program_studi ?: 'Belum diisi' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Angkatan <span class="info-item__kunci">Terkunci</span></span>
                                <span class="info-item__nilai {{ filled($pendaftaran->tahun_angkatan) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->tahun_angkatan ?: 'Belum diisi' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Kategori Pendaftaran</span>
                                <span class="info-item__nilai">{{ $labelKategori }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Status Penerimaan <span class="info-item__kunci">Terkunci</span></span>
                                <span class="info-item__nilai">{{ $statusPendaftaran }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Kamar Ma'had</span>
                                <span class="info-item__nilai {{ $labelKamar ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $labelKamar ?: 'Belum ditempatkan' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bagian">
                        <h2 class="judul-bagian">Data Diri &amp; Kontak</h2>

                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-item__label">Nomor HP Mahasiswa</span>
                                <span class="info-item__nilai {{ $noHpMahasiswa ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $noHpMahasiswa ?: 'Belum diisi' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Nomor WhatsApp</span>
                                <span class="info-item__nilai {{ filled($pendaftaran->no_wa) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->no_wa ?: 'Belum diisi' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Email Aktif</span>
                                <span class="info-item__nilai {{ filled($pendaftaran->email_aktif) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->email_aktif ?: $user->email ?: 'Belum diisi' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Jenis Kelamin</span>
                                <span class="info-item__nilai {{ filled($pendaftaran->jenis_kelamin) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->jenis_kelamin ?: 'Belum diisi' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Asal Sekolah</span>
                                <span class="info-item__nilai {{ filled($pendaftaran->asal_sekolah) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->asal_sekolah ?: 'Belum diisi' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bagian">
                        <h2 class="judul-bagian">Orang Tua / Wali</h2>

                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-item__label">Nama Orang Tua / Wali</span>
                                <span class="info-item__nilai {{ filled($pendaftaran->nama_ortu) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->nama_ortu ?: 'Belum diisi' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Nomor HP Orang Tua / Wali</span>
                                <span class="info-item__nilai {{ filled($pendaftaran->no_hp_ortu) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->no_hp_ortu ?: 'Belum diisi' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bagian">
                        <h2 class="judul-bagian">Verifikasi Pendaftaran</h2>

                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-item__label">Divalidasi Oleh <span class="info-item__kunci">Terkunci</span></span>
                                <span class="info-item__nilai {{ filled($pendaftaran->validator_nama) ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->validator_nama ?: 'Belum divalidasi' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-item__label">Tanggal Validasi <span class="info-item__kunci">Terkunci</span></span>
                                <span class="info-item__nilai {{ $pendaftaran->divalidasi_pada ? '' : 'info-item__nilai--kosong' }}">
                                    {{ $pendaftaran->divalidasi_pada ? $pendaftaran->divalidasi_pada->translatedFormat('d F Y H:i') : 'Belum divalidasi' }}
                                </span>
                            </div>

                            @if (filled($pendaftaran->catatan_validasi))
                                <div class="info-item" style="grid-column: 1 / -1;">
                                    <span class="info-item__label">Catatan Musyrifah</span>
                                    <span class="info-item__nilai">{{ $pendaftaran->catatan_validasi }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="catatan">
                        Data di atas berasal dari pendaftaran yang telah disetujui Musyrifah dan terhubung
                        dengan akun {{ $user->email }}. Perubahan NIM, status, dan data verifikasi hanya
                        dapat dilakukan oleh Musyrifah atau Admin.
                    </div>

                    <div class="aksi">
                        <a href="{{ route('dashboard') }}" class="tombol tombol--garis">Kembali ke Dashboard</a>

                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="{{ route('profil.mhs') }}" class="tombol tombol--emas">Lihat Profil</a>
                            <a href="{{ route('data.mahasiswa', ['edit' => 1]) }}" class="tombol tombol--utama">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Edit Data
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
