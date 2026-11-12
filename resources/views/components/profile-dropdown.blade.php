@auth
    @php
        $penggunaDropdown = auth()->user();
        $urlFotoDropdown = $penggunaDropdown->urlFotoProfil();
        $peranDropdown = \App\Support\MenuPeran::labelPeran($penggunaDropdown);
    @endphp

    @once
        <style>
            .pd-wrap { position: relative; }

            .pd-summary {
                list-style: none;
                display: flex;
                align-items: center;
                gap: 9px;
                padding: 4px 10px 4px 4px;
                border: 1px solid #e9d7db;
                border-radius: 999px;
                background: #fff;
                cursor: pointer;
                transition: background .18s ease, border-color .18s ease, box-shadow .18s ease;
            }

            .pd-summary::-webkit-details-marker { display: none; }

            .pd-summary:hover,
            .pd-wrap[open] .pd-summary {
                background: #F8EDEF;
                border-color: #C9A96A;
                box-shadow: 0 6px 18px rgba(107, 13, 27, .10);
            }

            .pd-avatar {
                width: 36px;
                height: 36px;
                flex-shrink: 0;
                display: grid;
                place-items: center;
                border-radius: 50%;
                overflow: hidden;
                background: #6B0D1B;
                color: #fff;
                font-size: 13px;
                font-weight: 800;
                letter-spacing: .04em;
                box-shadow: 0 0 0 2px #C9A96A inset;
            }

            .pd-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }

            .pd-identitas { display: none; line-height: 1.15; }

            .pd-nama {
                font-size: 13px;
                font-weight: 700;
                color: #2b2224;
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .pd-peran { font-size: 11px; font-weight: 600; color: #8a7d80; }

            .pd-caret { color: #8a7d80; display: grid; place-items: center; }

            .pd-panel {
                position: absolute;
                top: calc(100% + 10px);
                right: 0;
                width: 268px;
                padding: 8px;
                border-radius: 16px;
                background: #fff;
                border: 1px solid #ecd9dd;
                box-shadow: 0 22px 44px rgba(59, 7, 15, .18);
                z-index: 1200;
            }

            .pd-panel::before {
                content: "";
                position: absolute;
                top: -6px;
                right: 22px;
                width: 12px;
                height: 12px;
                background: #fff;
                border-left: 1px solid #ecd9dd;
                border-top: 1px solid #ecd9dd;
                transform: rotate(45deg);
            }

            .pd-head {
                display: flex;
                gap: 11px;
                align-items: center;
                padding: 10px 10px 12px;
                border-bottom: 1px dashed #eedfe2;
                margin-bottom: 6px;
            }

            .pd-head .pd-avatar { width: 42px; height: 42px; font-size: 15px; }

            .pd-head-teks { min-width: 0; }

            .pd-head-nama {
                font-size: 14px;
                font-weight: 800;
                color: #2b2224;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .pd-head-email {
                font-size: 12px;
                color: #857a7d;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .pd-badge {
                display: inline-block;
                margin-top: 4px;
                padding: 2px 8px;
                border-radius: 999px;
                background: #F8EDEF;
                color: #6B0D1B;
                font-size: 10.5px;
                font-weight: 800;
                letter-spacing: .04em;
                text-transform: uppercase;
            }

            .pd-item {
                display: flex;
                align-items: center;
                gap: 11px;
                padding: 10px 12px;
                border-radius: 11px;
                color: #3d3436;
                font-size: 13.5px;
                font-weight: 600;
                text-decoration: none;
                transition: background .15s ease, color .15s ease;
            }

            .pd-item:hover { background: #F8EDEF; color: #6B0D1B; }

            .pd-item svg { width: 18px; height: 18px; flex-shrink: 0; color: #6B0D1B; }

            .pd-item:hover svg { color: #C9A96A; }

            @media (min-width: 768px) {
                .pd-identitas { display: block; }
            }

            @media (max-width: 480px) {
                .pd-panel { width: 254px; right: -6px; }
            }
        </style>
    @endonce

    <details class="pd-wrap" id="profilDropdown">
        <summary class="pd-summary" aria-label="Menu profil {{ $penggunaDropdown->name }}">
            <span class="pd-avatar">
                @if ($urlFotoDropdown)
                    <img src="{{ $urlFotoDropdown }}" alt="Foto profil {{ $penggunaDropdown->name }}">
                @else
                    {{ $penggunaDropdown->inisial() }}
                @endif
            </span>

            <span class="pd-identitas">
                <span class="pd-nama">{{ $penggunaDropdown->name }}</span>
                <span class="pd-peran">{{ $peranDropdown }}</span>
            </span>

            <span class="pd-caret" aria-hidden="true">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </span>
        </summary>

        <div class="pd-panel">
            <div class="pd-head">
                <span class="pd-avatar">
                    @if ($urlFotoDropdown)
                        <img src="{{ $urlFotoDropdown }}" alt="Foto profil {{ $penggunaDropdown->name }}">
                    @else
                        {{ $penggunaDropdown->inisial() }}
                    @endif
                </span>

                <span class="pd-head-teks">
                    <span class="pd-head-nama">{{ $penggunaDropdown->name }}</span>
                    <span class="pd-head-email">{{ $penggunaDropdown->email ?: ($penggunaDropdown->nim ?: '-') }}</span>
                    <span class="pd-badge">{{ $peranDropdown }}</span>
                </span>
            </div>

            <a href="{{ route(\App\Support\MenuPeran::routeProfil($penggunaDropdown)) }}" class="pd-item">
                <x-panel.ikon nama="profil" />
                <span>Lihat Profil</span>
            </a>

            <a href="{{ route(\App\Support\MenuPeran::routeData($penggunaDropdown)) }}" class="pd-item">
                <x-panel.ikon nama="data" />
                <span>Data Saya</span>
            </a>

            <a href="{{ route('pengaturan-akun.index') }}" class="pd-item">
                <x-panel.ikon nama="pengaturan" />
                <span>Pengaturan Akun</span>
            </a>
        </div>
    </details>

    @once
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dropdown = document.getElementById('profilDropdown');

                if (!dropdown) {
                    return;
                }

                document.addEventListener('click', function (event) {
                    if (dropdown.open && !dropdown.contains(event.target)) {
                        dropdown.open = false;
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && dropdown.open) {
                        dropdown.open = false;
                    }
                });
            });
        </script>
    @endonce
@endauth
