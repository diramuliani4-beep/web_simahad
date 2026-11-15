@extends('layouts.mahasiswa')

@section('title', 'Pengumuman Mahasiswa | SIMAHAD')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
.pengumuman-page {
    --primary-maroon: #6e001b;
    --primary-maroon-hover: #580015;
    --maroon-tint: #faedf0;
    --maroon-border: #7c1a2e;
    --text-dark: #111827;
    --text-secondary: #4b5563;
    --text-muted: #6b7280;
    --border: #e5ded9;
    font-family: 'Plus Jakarta Sans', Arial, sans-serif;
    background: transparent;
    color: var(--text-dark);
    min-width: 0;
    width: 100%;
}
.pengumuman-page, .pengumuman-page *, .pengumuman-page *::before, .pengumuman-page *::after { box-sizing: border-box; }
.pengumuman-page a { color: inherit; text-decoration: none; }
.pengumuman-page button, .pengumuman-page input { font: inherit; }
.pengumuman-page .page-heading {
            padding: 0 0 1rem;
            border-bottom: 1.5px solid var(--maroon-border);
            background: transparent;
        }
.pengumuman-page .breadcrumb {
            color: var(--primary-maroon);
            font-size: 1.15rem;
            font-weight: 500;
            margin-bottom: .75rem;
        }
.pengumuman-page .page-heading h1 {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: .35rem;
            color: #111;
        }
.pengumuman-page .subtitle {
            font-size: 1.05rem;
            color: #333;
            line-height: 1.5;
        }
.pengumuman-page .content {
            padding: 1.25rem 0 2.5rem;
        }
.pengumuman-page .pencarian {
            display: flex;
            gap: .65rem;
            margin-bottom: 1rem;
        }
.pengumuman-page .pencarian input {
            flex: 1;
            min-width: 0;
            padding: .8rem 1.1rem;
            border: 1px solid var(--border);
            border-radius: 30px;
            font-size: .95rem;
            color: var(--text-dark);
            outline: none;
        }
.pengumuman-page .pencarian input:focus {
            border-color: var(--primary-maroon);
        }
.pengumuman-page .pencarian button {
            padding: .8rem 1.6rem;
            border: 1px solid var(--primary-maroon);
            border-radius: 30px;
            background: var(--primary-maroon);
            color: #fff;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
        }
.pengumuman-page .pencarian button:hover {
            background: var(--primary-maroon-hover);
        }
.pengumuman-page .filters {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--border);
        }
.pengumuman-page .filter-btn {
            border: 1px solid var(--border);
            background: #fff;
            color: #555;
            border-radius: 30px;
            padding: .7rem 1rem;
            font-size: .95rem;
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            display: inline-block;
        }
.pengumuman-page .filter-btn:hover {
            border-color: var(--primary-maroon);
            color: var(--primary-maroon);
        }
.pengumuman-page .filter-btn.active {
            background: var(--primary-maroon);
            color: #fff;
            border-color: var(--primary-maroon);
        }
.pengumuman-page .announcement {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border);
        }
.pengumuman-page .announcement[hidden] {
            display: none !important;
        }
.pengumuman-page .announcement-icon {
            width: 92px;
            height: 76px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            background: #f8ecef;
            color: var(--primary-maroon);
            border-radius: 12px;
            overflow: hidden;
        }
.pengumuman-page .announcement-icon svg {
            width: 25px;
            height: 25px;
        }
.pengumuman-page .announcement-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
.pengumuman-page .announcement-body {
            flex: 1;
            min-width: 0;
        }
.pengumuman-page .tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }
.pengumuman-page .tag-purple {
            color: #8250c8;
            background: #eee1fa;
        }
.pengumuman-page .tag-orange {
            color: #e66b1d;
            background: #ffeadb;
        }
.pengumuman-page .tag-blue {
            color: #3185f6;
            background: #dcecff;
        }
.pengumuman-page .tag-green {
            color: #21865b;
            background: #dff5e8;
        }
.pengumuman-page .tag-pink {
            color: #be185d;
            background: #fce7f3;
        }
.pengumuman-page .announcement h2 {
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.45;
            margin: 0 0 .45rem;
            color: #171717;
        }
.pengumuman-page .description {
            font-size: .95rem;
            line-height: 1.6;
            color: #77716d;
            margin: 0 0 .4rem;
        }
.pengumuman-page .date {
            font-size: .85rem;
            color: #88817d;
            margin-bottom: .55rem;
        }
.pengumuman-page .read-more {
            color: var(--primary-maroon);
            font-size: .95rem;
            font-weight: 700;
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }
.pengumuman-page .read-more:hover {
            text-decoration: underline;
        }
.pengumuman-page .empty {
            display: none;
            padding: 2rem 0;
            color: var(--text-muted);
            line-height: 1.6;
        }
.pengumuman-page .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(2px);
            z-index: 1400;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
.pengumuman-page .modal-overlay.active {
            display: flex;
        }
.pengumuman-page .modal-box {
            background: #fff;
            border-radius: 16px;
            max-width: 520px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            padding: 1.85rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, .15);
            position: relative;
            border: 1.5px solid var(--maroon-border);
        }
.pengumuman-page .modal-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--primary-maroon);
            margin: 0 2rem .75rem 0;
        }
.pengumuman-page .modal-body {
            font-size: .95rem;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            overflow-wrap: anywhere;
        }
.pengumuman-page .btn-modal-x {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: transparent;
            border: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: #94a3b8;
        }
.pengumuman-page .btn-modal-action {
            width: 100%;
            background: var(--primary-maroon);
            color: #fff;
            padding: .8rem;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            cursor: pointer;
        }
.pengumuman-page .btn-modal-action:hover {
            background: var(--primary-maroon-hover);
        }
@media (max-width: 992px) {
.pengumuman-page .page-heading {
                padding: 0 0 1rem;
            }
.pengumuman-page .content {
                padding: 1.25rem 0 2.5rem;
            }
}
@media (max-width: 600px) {
.pengumuman-page .page-heading {
                padding: 0 0 1rem;
            }
.pengumuman-page .breadcrumb {
                font-size: .95rem;
            }
.pengumuman-page .page-heading h1 {
                font-size: 1.65rem;
            }
.pengumuman-page .subtitle {
                font-size: .9rem;
            }
.pengumuman-page .content {
                padding: 1.25rem 0 2.5rem;
            }
.pengumuman-page .filters {
                gap: .5rem;
            }
.pengumuman-page .filter-btn {
                padding: .6rem .8rem;
                font-size: .85rem;
            }
.pengumuman-page .announcement {
                gap: .75rem;
                padding: 1.15rem 0;
            }
.pengumuman-page .announcement-icon {
                width: 45px;
                height: 45px;
            }
.pengumuman-page .announcement-icon svg {
                width: 21px;
                height: 21px;
            }
.pengumuman-page .announcement h2 {
                font-size: .95rem;
            }
.pengumuman-page .description {
                font-size: .85rem;
            }
.pengumuman-page .date, .pengumuman-page .read-more {
                font-size: .8rem;
            }
.pengumuman-page .modal-box {
                padding: 1.4rem;
            }
}

/* ================================================================
   PENYEMPURNAAN TAMPILAN PENGUMUMAN (HANYA CSS)
   Seluruh selector dibatasi pada halaman Pengumuman.
   ================================================================ */
.pengumuman-page {
    max-width: 1480px;
    margin-inline: auto;
    background: transparent;
}

.pengumuman-page .page-heading {
    padding: 2px 0 24px;
    background: transparent;
    border-bottom: 0;
}

.pengumuman-page .breadcrumb {
    margin-bottom: 12px;
    font-size: .83rem;
    font-weight: 650;
    line-height: 1.5;
}

.pengumuman-page .page-heading h1 {
    margin-bottom: 10px;
    color: #171b2d;
    font-size: clamp(1.55rem, 1.2rem + 1.25vw, 2rem);
    font-weight: 800;
    letter-spacing: -.035em;
    line-height: 1.25;
}

.pengumuman-page .subtitle {
    color: #64748b;
    font-size: .93rem;
    line-height: 1.65;
}

.pengumuman-page .content {
    padding: 4px 0 48px;
}

.pengumuman-page .pencarian {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    align-items: stretch;
}

.pengumuman-page .pencarian input {
    min-height: 48px;
    padding: 12px 18px;
    border: 1px solid #e8dade;
    border-radius: 12px;
    background: #fff;
    color: #1f2937;
    box-shadow: 0 2px 10px rgba(30, 24, 29, .025);
    font-size: .89rem;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.pengumuman-page .pencarian input::placeholder {
    color: #8b93a4;
}

.pengumuman-page .pencarian input:focus {
    border-color: #9b4353;
    box-shadow: 0 0 0 3px rgba(110, 0, 27, .09);
}

.pengumuman-page .pencarian button {
    min-height: 48px;
    min-width: 100px;
    padding: 12px 22px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(110, 0, 27, .11);
    font-size: .88rem;
    transition: background .18s ease, transform .18s ease;
}

.pengumuman-page .pencarian button:hover {
    transform: translateY(-1px);
}

.pengumuman-page .filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    padding-bottom: 0;
    margin-bottom: 22px;
    border-bottom: 0;
}

.pengumuman-page .filter-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 37px;
    padding: 8px 14px;
    border: 1px solid #e8dade;
    border-radius: 10px;
    background: #fff;
    color: #596170;
    font-size: .82rem;
    font-weight: 600;
    line-height: 1.3;
    transition: background .18s ease, border-color .18s ease, color .18s ease;
}

.pengumuman-page .filter-btn.active {
    background: #6e001b;
    color: #fff;
    border-color: #6e001b;
    box-shadow: 0 3px 10px rgba(110, 0, 27, .10);
}

.pengumuman-page .filter-btn:focus-visible,
.pengumuman-page .pencarian button:focus-visible,
.pengumuman-page .read-more:focus-visible {
    outline: 3px solid rgba(184, 144, 76, .55);
    outline-offset: 3px;
}

.pengumuman-page #announcement-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
    min-width: 0;
}

.pengumuman-page .announcement {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 19px;
    min-width: 0;
    padding: 21px 23px;
    border: 1px solid #e8dfe1;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 4px 16px rgba(30, 30, 40, .035);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
}

.pengumuman-page .announcement:hover {
    transform: translateY(-2px);
    border-color: #d7b3be;
    box-shadow: 0 8px 26px rgba(60, 28, 39, .08);
}

.pengumuman-page .announcement-icon {
    width: 122px;
    height: 110px;
    flex: 0 0 122px;
    border-radius: 12px;
    background: #f8ecef;
}

.pengumuman-page .announcement-body {
    min-width: 0;
    padding: 1px 0;
    overflow-wrap: anywhere;
}

.pengumuman-page .tag {
    margin-bottom: 8px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: .72rem;
    letter-spacing: .01em;
}

.pengumuman-page .announcement h2 {
    margin-bottom: 6px;
    font-size: 1.03rem;
    font-weight: 800;
    line-height: 1.4;
    color: #1c2332;
}

.pengumuman-page .description {
    margin-bottom: 8px;
    color: #657083;
    font-size: .86rem;
    line-height: 1.7;
}

.pengumuman-page .date {
    margin-bottom: 10px;
    color: #87909e;
    font-size: .76rem;
    line-height: 1.5;
}

.pengumuman-page .read-more {
    display: inline-flex;
    align-items: center;
    color: #790c20;
    font-size: .83rem;
    font-weight: 750;
    line-height: 1.5;
}

.pengumuman-page .empty {
    padding: 34px 24px;
    border: 1px dashed #d8bec5;
    border-radius: 14px;
    background: rgba(255, 255, 255, .82);
    color: #637083;
    text-align: center;
    font-size: .86rem;
}

@media (max-width: 768px) {
    .pengumuman-page .page-heading {
        padding-bottom: 20px;
    }

    .pengumuman-page .filters {
        gap: 8px;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding: 0 1px 8px;
        margin-bottom: 16px;
        scroll-snap-type: x proximity;
        -webkit-overflow-scrolling: touch;
    }

    .pengumuman-page .filter-btn {
        flex: 0 0 auto;
        white-space: nowrap;
        scroll-snap-align: start;
    }

    .pengumuman-page .announcement {
        gap: 15px;
        padding: 17px;
    }

    .pengumuman-page .announcement-icon {
        width: 94px;
        height: 94px;
        flex-basis: 94px;
    }
}

@media (max-width: 480px) {
    .pengumuman-page .breadcrumb {
        margin-bottom: 9px;
        font-size: .78rem;
    }

    .pengumuman-page .page-heading h1 {
        font-size: 1.55rem;
    }

    .pengumuman-page .subtitle {
        font-size: .84rem;
    }

    .pengumuman-page .pencarian {
        gap: 8px;
    }

    .pengumuman-page .pencarian input {
        min-height: 44px;
        padding: 10px 12px;
        font-size: .8rem;
    }

    .pengumuman-page .pencarian button {
        min-height: 44px;
        min-width: 73px;
        padding: 10px 12px;
        font-size: .8rem;
    }

    .pengumuman-page .filter-btn {
        padding: 7px 11px;
        min-height: 35px;
        font-size: .77rem;
    }

    .pengumuman-page .announcement {
        gap: 12px;
        padding: 13px;
        border-radius: 13px;
    }

    .pengumuman-page .announcement-icon {
        width: 76px;
        height: 82px;
        flex-basis: 76px;
        border-radius: 10px;
    }

    .pengumuman-page .tag {
        font-size: .68rem;
        padding: 4px 8px;
    }

    .pengumuman-page .announcement h2 {
        font-size: .92rem;
    }

    .pengumuman-page .description {
        font-size: .78rem;
    }

    .pengumuman-page .date,
    .pengumuman-page .read-more {
        font-size: .74rem;
    }
}

@media (prefers-reduced-motion: reduce) {
    .pengumuman-page .announcement,
    .pengumuman-page .filter-btn,
    .pengumuman-page .pencarian input,
    .pengumuman-page .pencarian button {
        transition: none;
    }

    .pengumuman-page .announcement:hover,
    .pengumuman-page .pencarian button:hover {
        transform: none;
    }
}
</style>
@endpush

@section('content')
<div class="pengumuman-page">
<section class="page-heading">
                <div class="breadcrumb">Dashboard / Pengumuman</div>
                <h1>Pengumuman</h1>
                <p class="subtitle">
                    Informasi terbaru seputar kegiatan dan aktivitas Ma’had.
                </p>
            </section>

            <section class="content">

                <form method="GET" action="{{ route('pengumuman.mhs') }}" class="pencarian">
                    <input
                        type="text"
                        name="cari"
                        value="{{ $filter['cari'] ?? '' }}"
                        placeholder="Cari judul pengumuman"
                        autocomplete="off"
                    >

                    @if (filled($filter['kategori'] ?? null))
                        <input type="hidden" name="kategori" value="{{ $filter['kategori'] }}">
                    @endif

                    <button type="submit">Cari</button>
                </form>

                <div class="filters">
                    <a
                        href="{{ route('pengumuman.mhs', array_filter(['cari' => $filter['cari'] ?? null])) }}"
                        class="filter-btn {{ blank($filter['kategori'] ?? null) ? 'active' : '' }}"
                    >
                        Semua
                    </a>

                    @foreach ($kategoriPilihan as $kategori)
                        <a
                            href="{{ route('pengumuman.mhs', array_filter(['cari' => $filter['cari'] ?? null, 'kategori' => $kategori])) }}"
                            class="filter-btn {{ ($filter['kategori'] ?? '') === $kategori ? 'active' : '' }}"
                        >
                            {{ $kategori }}
                        </a>
                    @endforeach
                </div>

                <div id="announcement-list">

                    @forelse ($pengumuman as $item)
                        <article class="announcement" data-category="{{ $item->kategori }}">

                            <div class="announcement-icon">
                                @if ($item->punyaGambar())
                                    <img
                                        src="{{ route('pengumuman.mhs.berkas', [$item, 'gambar']) }}"
                                        alt="Gambar pengumuman {{ $item->judul }}"
                                    >
                                @else
                                    <svg viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m3 11 18-5v12L3 14v-3z"/>
                                        <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="announcement-body">

                                <span class="tag tag-purple">{{ $item->kategori }}</span>

                                <h2>{{ $item->judul }}</h2>

                                <p class="description">
                                    {{ $item->ringkasan(200) }}
                                </p>

                                <div class="date">
                                    Tanggal publikasi: {{ $item->tanggalTeks() }}
                                </div>

                                <a
                                    class="read-more"
                                    href="{{ route('pengumuman.mhs.show', $item) }}"
                                >
                                    Baca selengkapnya →
                                </a>

                            </div>
                        </article>
                    @empty
                        <div class="empty" style="display: block;">
                            Belum ada pengumuman yang sesuai dengan pencarian atau filter kategori.
                        </div>
                    @endforelse

                </div>

            </section>

<!-- ================= MODAL INFORMASI ================= -->

    <div class="modal-overlay" id="infoModal" role="dialog"
         aria-modal="true" aria-labelledby="modalTitle"
         onclick="closeModalOnBackdrop(event)">

        <div class="modal-box">
            <button type="button" class="btn-modal-x"
                    aria-label="Tutup" onclick="closeInfoModal()">
                &times;
            </button>

            <h2 class="modal-title" id="modalTitle">Informasi</h2>
            <div class="modal-body" id="modalBody"></div>

            <button type="button" class="btn-modal-action"
                    onclick="closeInfoModal()">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal informasi
        function openInfoModal(title, message) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalBody').innerHTML = message;
            document.getElementById('infoModal').classList.add('active');
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.remove('active');
        }

        function closeModalOnBackdrop(event) {
            if (event.target.id === 'infoModal') {
                closeInfoModal();
            }
        }

        // Tutup modal dengan tombol Escape
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closeInfoModal();
            }
        });
</script>
@endpush
