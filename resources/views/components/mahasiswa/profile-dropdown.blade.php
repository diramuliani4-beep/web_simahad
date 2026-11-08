@php
    // Data pemilik akun yang sedang login untuk ditampilkan pada navbar.
    $penggunaNavbar = auth()->user();
    $fotoNavbar = $penggunaNavbar?->foto_profil;
    $urlFotoNavbar = $fotoNavbar ? asset('storage/'.$fotoNavbar) : null;
    $inisialNavbar = strtoupper(mb_substr($penggunaNavbar->name ?? 'M', 0, 1));
    $metaNavbar = filled($penggunaNavbar?->nim)
        ? 'NIM. '.$penggunaNavbar->nim
        : ($penggunaNavbar?->email ?? '-');
@endphp

@if ($penggunaNavbar)
    <details class="mhs-profil-dropdown" data-mhs-profil-dropdown>
        <summary
            class="mhs-profil-dropdown__pemicu"
            title="Menu profil {{ $penggunaNavbar->name }}"
            aria-label="Menu profil {{ $penggunaNavbar->name }}"
        >
            @if ($urlFotoNavbar)
                <img
                    src="{{ $urlFotoNavbar }}"
                    alt="Foto profil {{ $penggunaNavbar->name }}"
                    class="mhs-profil-dropdown__foto"
                >
            @else
                <span class="mhs-profil-dropdown__inisial">{{ $inisialNavbar }}</span>
            @endif
        </summary>

        <div class="mhs-profil-dropdown__panel" role="menu">
            {{-- Foto dan nama mahasiswa pemilik akun --}}
            <div class="mhs-profil-dropdown__identitas">
                <span class="mhs-profil-dropdown__avatar">
                    @if ($urlFotoNavbar)
                        <img
                            src="{{ $urlFotoNavbar }}"
                            alt="Foto profil {{ $penggunaNavbar->name }}"
                            class="mhs-profil-dropdown__foto"
                        >
                    @else
                        <span class="mhs-profil-dropdown__inisial mhs-profil-dropdown__inisial--besar">
                            {{ $inisialNavbar }}
                        </span>
                    @endif
                </span>

                <span class="mhs-profil-dropdown__teks">
                    <span class="mhs-profil-dropdown__nama">{{ $penggunaNavbar->name }}</span>
                    <span class="mhs-profil-dropdown__meta">{{ $metaNavbar }}</span>
                </span>
            </div>

            <div class="mhs-profil-dropdown__menu">
                <a href="{{ route('profil.mhs') }}" class="mhs-profil-dropdown__item" role="menuitem">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Lihat Profil</span>
                </a>

                <a href="{{ route('data.mahasiswa') }}" class="mhs-profil-dropdown__item" role="menuitem">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                        <circle cx="9" cy="10" r="2"></circle>
                        <path d="M6 16c.7-1.6 1.8-2.4 3-2.4s2.3.8 3 2.4"></path>
                        <path d="M15 9h3M15 13h3"></path>
                    </svg>
                    <span>Data Mahasiswa</span>
                </a>
            </div>
        </div>
    </details>

    @once
        <style>
            .mhs-profil-dropdown {
                position: relative;
                display: inline-flex;
                align-items: center;
                font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            .mhs-profil-dropdown__pemicu {
                list-style: none;
                width: 40px;
                height: 40px;
                padding: 0;
                border-radius: 50%;
                border: 2px solid #C9A96A;
                background: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                cursor: pointer;
                transition: box-shadow 0.18s ease, transform 0.18s ease;
            }

            .mhs-profil-dropdown__pemicu::-webkit-details-marker {
                display: none;
            }

            .mhs-profil-dropdown__pemicu:hover,
            .mhs-profil-dropdown[open] > .mhs-profil-dropdown__pemicu {
                box-shadow: 0 0 0 4px rgba(201, 169, 106, 0.25);
            }

            .mhs-profil-dropdown__pemicu:focus-visible {
                outline: 2px solid #6B0D1B;
                outline-offset: 2px;
            }

            .mhs-profil-dropdown__foto {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
                display: block;
            }

            .mhs-profil-dropdown__inisial {
                font-size: 15px;
                font-weight: 800;
                line-height: 1;
                color: #6B0D1B;
            }

            .mhs-profil-dropdown__inisial--besar {
                font-size: 19px;
            }

            .mhs-profil-dropdown__panel {
                position: absolute;
                top: calc(100% + 10px);
                right: 0;
                width: min(272px, calc(100vw - 32px));
                background: #ffffff;
                border: 1px solid #E8DADD;
                border-top: 3px solid #C9A96A;
                border-radius: 12px;
                box-shadow: 0 18px 38px rgba(60, 10, 20, 0.18);
                overflow: hidden;
                z-index: 9999;
            }

            .mhs-profil-dropdown__identitas {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 15px 16px;
                background: #FDF6F0;
                border-bottom: 1px solid #F0E2D6;
            }

            .mhs-profil-dropdown__avatar {
                width: 46px;
                height: 46px;
                flex: 0 0 46px;
                border-radius: 50%;
                overflow: hidden;
                background: #FAEDF0;
                border: 2px solid #C9A96A;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .mhs-profil-dropdown__teks {
                min-width: 0;
                display: flex;
                flex-direction: column;
                gap: 3px;
            }

            .mhs-profil-dropdown__nama {
                font-size: 13px;
                font-weight: 800;
                color: #6B0D1B;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mhs-profil-dropdown__meta {
                font-size: 11px;
                color: #7A6A6D;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mhs-profil-dropdown__menu {
                display: flex;
                flex-direction: column;
            }

            .mhs-profil-dropdown__item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 16px;
                font-size: 13px;
                font-weight: 600;
                color: #6B0D1B;
                text-decoration: none;
                transition: background-color 0.16s ease;
            }

            .mhs-profil-dropdown__item + .mhs-profil-dropdown__item {
                border-top: 1px solid #F4E8EA;
            }

            .mhs-profil-dropdown__item:hover,
            .mhs-profil-dropdown__item:focus-visible {
                background: #FAEDF0;
                outline: none;
            }

            .mhs-profil-dropdown__item svg {
                width: 17px;
                height: 17px;
                flex: 0 0 17px;
                color: #9C7B34;
            }

            @media (max-width: 480px) {
                .mhs-profil-dropdown__pemicu {
                    width: 36px;
                    height: 36px;
                }
            }
        </style>

        <script>
            (function () {
                if (window.mhsProfilDropdownSiap) {
                    return;
                }

                window.mhsProfilDropdownSiap = true;

                // Tutup dropdown saat pengguna mengklik di luar atau menekan Escape.
                document.addEventListener('click', function (event) {
                    document.querySelectorAll('details.mhs-profil-dropdown[open]').forEach(function (dropdown) {
                        if (! dropdown.contains(event.target)) {
                            dropdown.removeAttribute('open');
                        }
                    });
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key !== 'Escape') {
                        return;
                    }

                    document.querySelectorAll('details.mhs-profil-dropdown[open]').forEach(function (dropdown) {
                        dropdown.removeAttribute('open');
                    });
                });
            })();
        </script>
    @endonce
@endif
