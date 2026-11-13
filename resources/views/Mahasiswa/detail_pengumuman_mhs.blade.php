<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $pengumuman->judul }} | Pengumuman SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary-maroon: #6B0D1B;
            --primary-maroon-hover: #4d0813;
            --gold: #C9A96A;
            --maroon-tint: #faedf0;
            --maroon-border: #7c1a2e;
            --text-dark: #111827;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --border: #e5ded9;
            --header-height: 66px;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #fff;
            color: var(--text-dark);
            min-height: 100vh;
        }

        a {
            color: var(--primary-maroon);
        }

        .top-navbar {
            height: var(--header-height);
            min-height: var(--header-height);
            background: #fff;
            border-bottom: 1.5px solid var(--maroon-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .navbar-brand-wrap {
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
            color: var(--text-dark);
        }

        .brand-logo-img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary-maroon);
            letter-spacing: .04em;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .halaman {
            max-width: 960px;
            margin: 0 auto;
            padding: 2rem 2.75rem 3rem;
        }

        .breadcrumb {
            color: var(--primary-maroon);
            font-size: 1.05rem;
            font-weight: 500;
            margin-bottom: .9rem;
        }

        .breadcrumb a {
            color: var(--primary-maroon);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .kartu-pengumuman {
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem 2.1rem 2.4rem;
            background: #fff;
            box-shadow: 0 6px 18px rgba(107, 13, 27, .06);
        }

        .ringkasan-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1rem;
        }

        .tag {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 700;
            color: var(--primary-maroon);
            background: #fff;
            border: 1px solid var(--gold);
        }

        .meta-teks {
            font-size: .9rem;
            color: var(--text-muted);
        }

        .judul {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.28;
            color: #111;
            margin-bottom: 1.4rem;
        }

        .gambar-pengumuman {
            display: block;
            width: 100%;
            max-height: 460px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid var(--border);
            margin-bottom: 1.6rem;
        }

        .isi-pengumuman {
            font-size: 1.02rem;
            line-height: 1.85;
            color: #333;
            overflow-wrap: anywhere;
        }

        .dokumen-pengumuman {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .9rem;
            margin-top: 1.9rem;
            padding: 1.1rem 1.25rem;
            border: 1px solid var(--gold);
            border-radius: 14px;
            background: #fdf8ef;
        }

        .dokumen-pengumuman span {
            font-size: .95rem;
            color: var(--text-secondary);
            overflow-wrap: anywhere;
        }

        .tombol {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            padding: .7rem 1.3rem;
            border-radius: 30px;
            font-size: .95rem;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid var(--primary-maroon);
            color: var(--primary-maroon);
            background: #fff;
            cursor: pointer;
        }

        .tombol:hover {
            background: var(--primary-maroon);
            color: #fff;
        }

        .tombol.utama {
            background: var(--primary-maroon);
            color: #fff;
        }

        .tombol.utama:hover {
            background: var(--primary-maroon-hover);
        }

        .aksi-bawah {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            margin-top: 2.2rem;
        }

        .lainnya {
            margin-top: 2.4rem;
        }

        .lainnya h2 {
            font-size: 1.2rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #171717;
        }

        .daftar-lainnya {
            display: grid;
            gap: .75rem;
        }

        .kartu-lainnya {
            display: block;
            padding: 1rem 1.2rem;
            border: 1px solid var(--border);
            border-radius: 14px;
            text-decoration: none;
            color: var(--text-dark);
            background: #fff;
            transition: all .18s ease;
        }

        .kartu-lainnya:hover {
            border-color: var(--primary-maroon);
            background: var(--maroon-tint);
        }

        .kartu-lainnya strong {
            display: block;
            font-size: .98rem;
            margin-bottom: .3rem;
        }

        .kartu-lainnya span {
            font-size: .82rem;
            color: var(--text-muted);
        }

        .kosong {
            padding: 1.4rem 0;
            color: var(--text-muted);
            font-size: .95rem;
        }

        .navbar-kiri {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .navbar-menu {
            display: none;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            padding: 0;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--primary-maroon);
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

        @media (max-width: 768px) {
            .top-navbar {
                padding: 0 1rem;
            }

            .halaman {
                padding: 1.4rem 1.1rem 2.4rem;
            }

            .kartu-pengumuman {
                padding: 1.4rem 1.15rem 1.7rem;
            }

            .judul {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body class="sb-mandiri">
    {{-- Sidebar bersama seluruh role SIMAHAD. --}}
    <x-panel.sidebar ragam="mandiri" />

    <header class="top-navbar">
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

            <a href="{{ route('pengumuman.mhs') }}" class="navbar-brand-wrap">
                <img
                    src="{{ asset('images/logo_uin.png') }}"
                    alt="Logo SIMAHAD"
                    class="brand-logo-img"
                    onerror="this.style.display='none'"
                >
                <span class="brand-title">SIMAHAD</span>
            </a>
        </div>

        <div class="navbar-actions">
            <a href="{{ route('pengumuman.mhs') }}" class="tombol">Kembali</a>
            <x-mahasiswa.profile-dropdown />
        </div>
    </header>

    <main class="halaman">

        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a> /
            <a href="{{ route('pengumuman.mhs') }}">Pengumuman</a> /
            Detail
        </div>

        <article class="kartu-pengumuman">

            <div class="ringkasan-meta">
                <span class="tag">{{ $pengumuman->kategori }}</span>
                <span class="meta-teks">
                    Tanggal publikasi: {{ $pengumuman->tanggalTeks() }}
                </span>
                <span class="meta-teks">
                    Penulis: {{ $pengumuman->penulisTeks() }}
                </span>
            </div>

            <h1 class="judul">{{ $pengumuman->judul }}</h1>

            @if ($pengumuman->punyaGambar())
                <img
                    src="{{ route('pengumuman.mhs.berkas', [$pengumuman, 'gambar']) }}"
                    alt="Gambar pengumuman {{ $pengumuman->judul }}"
                    class="gambar-pengumuman"
                >
            @endif

            <div class="isi-pengumuman">{!! nl2br(e($pengumuman->isi)) !!}</div>

            @if ($pengumuman->punyaDokumen())
                <div class="dokumen-pengumuman">
                    <span>Dokumen pendukung: {{ $pengumuman->namaBerkas('dokumen') }}</span>
                    <a
                        href="{{ route('pengumuman.mhs.berkas', [$pengumuman, 'dokumen']) }}"
                        class="tombol utama"
                    >
                        Unduh Dokumen
                    </a>
                </div>
            @endif

            <div class="aksi-bawah">
                <a href="{{ route('pengumuman.mhs') }}" class="tombol">
                    Kembali ke Daftar Pengumuman
                </a>
            </div>
        </article>

        <section class="lainnya">
            <h2>Pengumuman Lainnya</h2>

            <div class="daftar-lainnya">
                @forelse ($pengumumanLain as $item)
                    <a
                        href="{{ route('pengumuman.mhs.show', $item) }}"
                        class="kartu-lainnya"
                    >
                        <strong>{{ $item->judul }}</strong>
                        <span>{{ $item->kategori }} &middot; {{ $item->tanggalTeks() }}</span>
                    </a>
                @empty
                    <p class="kosong">Belum ada pengumuman lain yang dipublikasikan.</p>
                @endforelse
            </div>
        </section>

    </main>

</body>
</html>
