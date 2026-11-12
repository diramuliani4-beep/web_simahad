@props([
    'pengguna' => null,
    'aksiSimpan' => 'pengaturan-akun.foto.update',
    'aksiHapus' => 'pengaturan-akun.foto.destroy',
    'ukuran' => 96,
])

@php
    $penggunaFoto = $pengguna ?? auth()->user();
    $idFoto = 'fp'.substr(md5((string) ($penggunaFoto?->id ?? 0).$aksiSimpan), 0, 8);
    $urlFoto = $penggunaFoto?->urlFotoProfil();
    $adaFoto = filled($urlFoto);
    $maksKb = 2048;
@endphp

@once
    <style>
        .fp-wrap { display: inline-flex; flex-direction: column; align-items: center; gap: 10px; }
        .fp-avatar-box { position: relative; display: inline-block; }

        .fp-avatar {
            display: grid;
            place-items: center;
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(140deg, #6B0D1B, #8c2130);
            color: #fff;
            font-weight: 800;
            letter-spacing: .04em;
            border: 3px solid #C9A96A;
            box-shadow: 0 10px 24px rgba(107, 13, 27, .18);
        }

        .fp-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }

        /* Ikon kamera berada tepat di tengah bagian bawah foto profil. */
        .fp-kamera {
            position: absolute;
            left: 50%;
            bottom: -7px;
            transform: translateX(-50%);
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            padding: 0;
            border: 2px solid #fff;
            border-radius: 50%;
            background: #6B0D1B;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 6px 14px rgba(107, 13, 27, .28);
            transition: background .16s ease, transform .16s ease;
        }

        .fp-kamera:hover { background: #C9A96A; color: #4a2d0c; transform: translateX(-50%) scale(1.06); }
        .fp-kamera svg { width: 16px; height: 16px; }

        .fp-menu {
            position: absolute;
            top: calc(100% + 12px);
            left: 50%;
            transform: translateX(-50%);
            z-index: 60;
            width: 188px;
            max-width: calc(100vw - 28px);
            padding: 6px;
            border: 1px solid #ecd9dd;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 18px 36px rgba(59, 7, 15, .18);
        }

        .fp-menu[hidden] { display: none; }

        .fp-menu button,
        .fp-menu a {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            padding: 8px 10px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #3d3436;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
        }

        .fp-menu button:hover,
        .fp-menu a:hover { background: #F8EDEF; color: #6B0D1B; }
        .fp-menu button:disabled { opacity: .45; cursor: not-allowed; }
        .fp-menu svg { width: 16px; height: 16px; color: #6B0D1B; flex-shrink: 0; }
        .fp-menu .fp-menu-hapus { color: #a02334; }

        .fp-modal {
            position: fixed;
            inset: 0;
            z-index: 3000;
            display: grid;
            place-items: center;
            padding: 18px;
            background: rgba(30, 8, 12, .55);
            backdrop-filter: blur(2px);
        }

        .fp-modal[hidden] { display: none; }

        .fp-dialog {
            width: min(560px, 100%);
            max-height: 92vh;
            overflow: auto;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 30px 70px rgba(20, 3, 7, .4);
        }

        .fp-dialog-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 15px 18px;
            border-bottom: 1px solid #f0e3e5;
        }

        .fp-dialog-head h3 { margin: 0; font-size: 16px; font-weight: 800; color: #2b2224; }

        .fp-tutup {
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            padding: 0;
            border: 0;
            border-radius: 9px;
            background: #F8EDEF;
            color: #6B0D1B;
            cursor: pointer;
        }

        .fp-tutup:hover { background: #f2dde1; }
        .fp-dialog-body { padding: 16px 18px; }

        .fp-pilih {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .fp-label-pilih {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border: 1px dashed #d8bcc2;
            border-radius: 10px;
            background: #fdf7f8;
            color: #6B0D1B;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .fp-label-pilih:hover { border-color: #C9A96A; background: #F8EDEF; }

        .fp-info { font-size: 12px; color: #8b7c80; }

        .fp-editor { display: grid; gap: 14px; }

        /* Area crop selalu persegi dan berada di tengah agar pratinjau tidak gepeng. */
        .fp-stage {
            position: relative;
            width: min(100%, 340px);
            aspect-ratio: 1 / 1;
            margin: 0 auto;
            display: grid;
            place-items: center;
            overflow: hidden;
            border-radius: 14px;
            background:
                repeating-conic-gradient(#f3eef0 0% 25%, #fff 0% 50%) 50% / 20px 20px;
            border: 1px solid #efe1e4;
            touch-action: none;
            cursor: grab;
        }

        .fp-stage:active { cursor: grabbing; }
        .fp-stage canvas { display: block; width: 100%; height: 100%; }

        .fp-lingkaran {
            position: absolute;
            inset: 8%;
            border-radius: 50%;
            border: 2px dashed rgba(107, 13, 27, .55);
            box-shadow: 0 0 0 9999px rgba(24, 8, 11, .28);
            pointer-events: none;
        }

        .fp-kontrol {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            justify-content: center;
        }

        /* Penggeser zoom membantu pengaturan ukuran dan posisi foto. */
        .fp-geser {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            border: 1px solid #f0e3e5;
            background: #fdfafb;
        }

        .fp-geser label {
            flex-shrink: 0;
            font-size: 12.5px;
            font-weight: 700;
            color: #4a3d40;
        }

        .fp-geser input[type="range"] {
            flex: 1 1 auto;
            min-width: 0;
            accent-color: #6B0D1B;
            cursor: pointer;
        }

        .fp-geser-nilai {
            flex-shrink: 0;
            min-width: 42px;
            text-align: right;
            font-size: 12px;
            font-weight: 700;
            color: #6B0D1B;
        }

        .fp-kontrol button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 34px;
            padding: 7px 11px;
            border: 1px solid #e7d6da;
            border-radius: 9px;
            background: #fff;
            color: #4a3d40;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
        }

        .fp-kontrol button:hover { background: #F8EDEF; border-color: #C9A96A; color: #6B0D1B; }
        .fp-kontrol svg { width: 15px; height: 15px; }

        .fp-pratinjau {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #fbf7f8;
            border: 1px solid #f0e3e5;
        }

        .fp-pratinjau-bulat {
            width: 64px;
            height: 64px;
            flex-shrink: 0;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #C9A96A;
            background: #fff;
        }

        .fp-pratinjau-bulat canvas { width: 100% !important; height: 100% !important; display: block; }
        .fp-pratinjau-teks { font-size: 12.5px; color: #7c6d71; line-height: 1.4; }

        .fp-pesan {
            display: none;
            margin-bottom: 10px;
            padding: 9px 12px;
            border-radius: 9px;
            background: #fdeef0;
            border: 1px solid #f2ccd2;
            color: #8a1224;
            font-size: 12.5px;
            font-weight: 600;
        }

        .fp-pesan[data-tampil="1"] { display: block; }

        /* Batal di kiri, Simpan di kanan. */
        .fp-aksi {
            display: flex;
            justify-content: space-between;
            gap: 9px;
            padding: 13px 18px 17px;
            border-top: 1px solid #f0e3e5;
        }

        .fp-btn {
            min-height: 38px;
            padding: 9px 18px;
            border-radius: 10px;
            border: 1px solid transparent;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
        }

        .fp-btn--batal { background: #fff; border-color: #dccdd0; color: #5b4d50; }
        .fp-btn--batal:hover { background: #f7f0f1; }
        .fp-btn--simpan { background: #6B0D1B; color: #fff; }
        .fp-btn--simpan:hover { background: #52070f; }
        .fp-btn--simpan:disabled { opacity: .55; cursor: not-allowed; }

        .fp-lihat {
            display: grid;
            place-items: center;
            padding: 10px;
        }

        .fp-lihat img {
            width: min(320px, 78vw);
            height: min(320px, 78vw);
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #C9A96A;
            box-shadow: 0 18px 40px rgba(30, 8, 12, .22);
        }

        .fp-lihat-kosong {
            padding: 26px;
            text-align: center;
            color: #8b7c80;
            font-size: 13.5px;
        }

        @media (max-width: 520px) {
            .fp-dialog-head { padding: 13px 14px; }
            .fp-dialog-body { padding: 14px; }
            .fp-aksi { padding: 12px 14px 14px; }
            .fp-aksi .fp-btn { flex: 1 1 0; padding: 9px 12px; }
            .fp-kontrol button { padding: 8px 9px; font-size: 12px; }
        }
    </style>
@endonce

<div class="fp-wrap" id="{{ $idFoto }}">
    <div class="fp-avatar-box">
        <div class="fp-avatar" style="width: {{ $ukuran }}px; height: {{ $ukuran }}px; font-size: {{ max(16, (int) round($ukuran / 3)) }}px;">
            <span data-fp-avatar>
                @if ($adaFoto)
                    <img src="{{ $urlFoto }}" alt="Foto profil {{ $penggunaFoto->name }}">
                @else
                    {{ $penggunaFoto->inisial() }}
                @endif
            </span>
        </div>

        <button type="button"
            class="fp-kamera"
            data-fp-toggle
            aria-label="Menu foto profil"
            aria-expanded="false"
            title="Menu foto profil">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                <circle cx="12" cy="13" r="4" />
            </svg>
        </button>

        <div class="fp-menu" data-fp-menu hidden>
            <button type="button" data-fp-lihat @disabled(! $adaFoto)>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <span>Lihat Foto</span>
            </button>

            <button type="button" data-fp-edit>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9" />
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z" />
                </svg>
                <span>Edit Foto</span>
            </button>

            <form action="{{ route($aksiHapus) }}" method="POST" data-fp-hapus>
                @csrf
                @method('DELETE')
                <button type="submit" class="fp-menu-hapus" @disabled(! $adaFoto)>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18" />
                        <path d="M8 6V4h8v2" />
                        <path d="M6 6l1 14h10l1-14" />
                    </svg>
                    <span>Hapus Foto</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Form unggah tersembunyi: hanya dikirim saat tombol Simpan Foto ditekan. --}}
    <form id="{{ $idFoto }}-form"
        action="{{ route($aksiSimpan) }}"
        method="POST"
        enctype="multipart/form-data"
        hidden>
        @csrf
        <input type="file" name="foto_profil" id="{{ $idFoto }}-berkas" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
    </form>

    {{-- Modal Lihat Foto --}}
    <div class="fp-modal" data-fp-modal-lihat hidden>
        <div class="fp-dialog" role="dialog" aria-modal="true" aria-label="Lihat foto profil">
            <div class="fp-dialog-head">
                <h3>Foto Profil</h3>
                <button type="button" class="fp-tutup" data-fp-tutup aria-label="Tutup">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="fp-dialog-body">
                <div class="fp-lihat">
                    @if ($adaFoto)
                        <img src="{{ $urlFoto }}" alt="Foto profil {{ $penggunaFoto->name }}">
                    @else
                        <p class="fp-lihat-kosong">
                            Belum ada foto profil. Pilih <strong>Edit Foto</strong> untuk mengunggah foto.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Foto (crop, geser, zoom, rotasi, pratinjau bulat) --}}
    <div class="fp-modal" data-fp-modal-edit hidden>
        <div class="fp-dialog" role="dialog" aria-modal="true" aria-label="Edit foto profil">
            <div class="fp-dialog-head">
                <h3>Edit Foto Profil</h3>
                <button type="button" class="fp-tutup" data-fp-tutup aria-label="Batal">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="fp-dialog-body">
                <div class="fp-pesan" data-fp-pesan></div>

                <div class="fp-pilih">
                    <label class="fp-label-pilih" for="{{ $idFoto }}-pilih">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path d="m21 15-5-5L5 21" />
                        </svg>
                        <span>Pilih Gambar</span>
                    </label>

                    <input type="file" id="{{ $idFoto }}-pilih" accept=".jpg,.jpeg,.png,image/jpeg,image/png" hidden data-fp-input>

                    <span class="fp-info">JPG, JPEG, atau PNG. Maksimal 2 MB.</span>
                </div>

                <div class="fp-editor">
                    <div class="fp-stage" data-fp-stage>
                        <canvas width="360" height="360" data-fp-canvas></canvas>
                        <div class="fp-lingkaran" aria-hidden="true"></div>
                    </div>

                    <div class="fp-geser">
                        <label for="{{ $idFoto }}-zoom">Zoom</label>
                        <input type="range"
                            id="{{ $idFoto }}-zoom"
                            min="0.4"
                            max="4"
                            step="0.05"
                            value="1"
                            data-fp-zoom
                            aria-label="Atur ukuran foto">
                        <span class="fp-geser-nilai" data-fp-zoom-nilai>100%</span>
                    </div>

                    <div class="fp-kontrol">
                        <button type="button" data-fp-zoom-in title="Perbesar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7" />
                                <path d="M21 21l-4.3-4.3M11 8v6M8 11h6" />
                            </svg>
                            <span>Zoom In</span>
                        </button>

                        <button type="button" data-fp-zoom-out title="Perkecil">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7" />
                                <path d="M21 21l-4.3-4.3M8 11h6" />
                            </svg>
                            <span>Zoom Out</span>
                        </button>

                        <button type="button" data-fp-putar-kiri title="Putar ke kiri">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12a9 9 0 1 0 3-6.7" />
                                <path d="M3 4v5h5" />
                            </svg>
                            <span>Rotasi Kiri</span>
                        </button>

                        <button type="button" data-fp-putar-kanan title="Putar ke kanan">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12a9 9 0 1 1-3-6.7" />
                                <path d="M21 4v5h-5" />
                            </svg>
                            <span>Rotasi Kanan</span>
                        </button>

                        <button type="button" data-fp-reset title="Kembalikan posisi awal">
                            <span>Reset</span>
                        </button>
                    </div>

                    <div class="fp-pratinjau">
                        <div class="fp-pratinjau-bulat">
                            <canvas width="128" height="128" data-fp-pratinjau></canvas>
                        </div>
                        <div class="fp-pratinjau-teks">
                            Area di dalam lingkaran adalah hasil crop foto profil.
                            Geser gambar untuk mengatur posisi, lalu gunakan Zoom dan
                            Rotasi agar hasilnya pas di seluruh halaman SIMAHAD.
                        </div>
                    </div>
                </div>
            </div>

            <div class="fp-aksi">
                <button type="button" class="fp-btn fp-btn--batal" data-fp-batal>Batal</button>
                <button type="button" class="fp-btn fp-btn--simpan" data-fp-simpan disabled>Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const root = document.getElementById(@json($idFoto));

        if (!root) {
            return;
        }

        const modalLihat = root.querySelector('[data-fp-modal-lihat]');
        const modalEdit = root.querySelector('[data-fp-modal-edit]');
        const menu = root.querySelector('[data-fp-menu]');
        const toggle = root.querySelector('[data-fp-toggle]');
        const input = root.querySelector('[data-fp-input]');
        const canvas = root.querySelector('[data-fp-canvas]');
        const pratinjau = root.querySelector('[data-fp-pratinjau]');
        const stage = root.querySelector('[data-fp-stage]');
        const pesan = root.querySelector('[data-fp-pesan]');
        const tombolSimpan = root.querySelector('[data-fp-simpan]');
        const geserZoom = root.querySelector('[data-fp-zoom]');
        const nilaiZoom = root.querySelector('[data-fp-zoom-nilai]');
        const formUnggah = document.getElementById(@json($idFoto.'-form'));
        const berkasTujuan = document.getElementById(@json($idFoto.'-berkas'));
        const konteks = canvas.getContext('2d');
        const konteksPratinjau = pratinjau.getContext('2d');

        const MAKS_BYTE = 2 * 1024 * 1024;
        const TIPE_DIIZINKAN = ['image/jpeg', 'image/png'];

        let gambar = null;
        let skala = 1;
        let rotasi = 0;
        let geserX = 0;
        let geserY = 0;
        let mulaiX = 0;
        let mulaiY = 0;
        let menyeret = false;

        function tampilkanPesan(teks) {
            pesan.textContent = teks || '';
            pesan.dataset.tampil = teks ? '1' : '0';
        }

        function tutupSemua() {
            menu.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
            modalLihat.hidden = true;
            modalEdit.hidden = true;
        }

        function bacaUlangFotoLama() {
            @if ($adaFoto)
                const imgLama = new Image();
                imgLama.onload = function () {
                    gambar = imgLama;
                    skala = 1;
                    rotasi = 0;
                    geserX = 0;
                    geserY = 0;
                    tombolSimpan.disabled = false;
                    selaraskanZoom();
                    gambarDanPratinjau();
                };
                imgLama.src = @json($urlFoto);
            @else
                gambar = null;
                tombolSimpan.disabled = true;
                konteks.clearRect(0, 0, canvas.width, canvas.height);
                konteksPratinjau.clearRect(0, 0, pratinjau.width, pratinjau.height);
            @endif
        }

        function selaraskanZoom() {
            if (geserZoom) {
                geserZoom.value = skala;
            }

            if (nilaiZoom) {
                nilaiZoom.textContent = Math.round(skala * 100) + '%';
            }
        }

        function gambarDanPratinjau() {
            if (!gambar) {
                return;
            }

            selaraskanZoom();

            const sisi = Math.min(canvas.width, canvas.height);
            const dasar = Math.max(sisi / gambar.width, sisi / gambar.height);
            const skalaAkhir = dasar * skala;

            konteks.clearRect(0, 0, canvas.width, canvas.height);
            konteks.save();
            konteks.translate(canvas.width / 2 + geserX, canvas.height / 2 + geserY);
            konteks.rotate((rotasi * Math.PI) / 180);
            konteks.drawImage(
                gambar,
                -(gambar.width * skalaAkhir) / 2,
                -(gambar.height * skalaAkhir) / 2,
                gambar.width * skalaAkhir,
                gambar.height * skalaAkhir
            );
            konteks.restore();

            konteksPratinjau.clearRect(0, 0, pratinjau.width, pratinjau.height);
            konteksPratinjau.save();
            konteksPratinjau.beginPath();
            konteksPratinjau.arc(pratinjau.width / 2, pratinjau.height / 2, pratinjau.width / 2, 0, Math.PI * 2);
            konteksPratinjau.clip();
            konteksPratinjau.drawImage(canvas, 0, 0, canvas.width, canvas.height, 0, 0, pratinjau.width, pratinjau.height);
            konteksPratinjau.restore();
        }

        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            const akanBuka = menu.hidden;
            menu.hidden = !akanBuka;
            toggle.setAttribute('aria-expanded', akanBuka ? 'true' : 'false');
        });

        document.addEventListener('click', function (event) {
            if (!menu.hidden && !root.contains(event.target)) {
                menu.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        root.querySelector('[data-fp-lihat]').addEventListener('click', function () {
            menu.hidden = true;
            modalLihat.hidden = false;
        });

        root.querySelector('[data-fp-edit]').addEventListener('click', function () {
            menu.hidden = true;
            tampilkanPesan('');
            modalEdit.hidden = false;
            bacaUlangFotoLama();
        });

        root.querySelectorAll('[data-fp-tutup]').forEach(function (tombol) {
            tombol.addEventListener('click', tutupSemua);
        });

        root.querySelector('[data-fp-batal]').addEventListener('click', function () {
            tutupSemua();
            // Foto lama tidak diubah karena unggahan hanya terjadi saat Simpan Foto.
        });

        modalEdit.addEventListener('click', function (event) {
            if (event.target === modalEdit) {
                tutupSemua();
            }
        });

        modalLihat.addEventListener('click', function (event) {
            if (event.target === modalLihat) {
                tutupSemua();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                tutupSemua();
            }
        });

        input.addEventListener('change', function () {
            const berkas = input.files && input.files[0];

            if (!berkas) {
                return;
            }

            if (!TIPE_DIIZINKAN.includes(berkas.type)) {
                tampilkanPesan('Format foto harus JPG, JPEG, atau PNG.');
                input.value = '';
                return;
            }

            if (berkas.size > MAKS_BYTE) {
                tampilkanPesan('Ukuran foto maksimal 2 MB.');
                input.value = '';
                return;
            }

            tampilkanPesan('');

            const pembaca = new FileReader();
            pembaca.onload = function (hasil) {
                const img = new Image();
                img.onload = function () {
                    gambar = img;
                    skala = 1;
                    rotasi = 0;
                    geserX = 0;
                    geserY = 0;
                    tombolSimpan.disabled = false;
                    selaraskanZoom();
                    gambarDanPratinjau();
                };
                img.src = hasil.target.result;
            };
            pembaca.readAsDataURL(berkas);
        });

        geserZoom.addEventListener('input', function () {
            skala = Math.min(4, Math.max(0.4, parseFloat(geserZoom.value) || 1));
            gambarDanPratinjau();
        });

        root.querySelector('[data-fp-zoom-in]').addEventListener('click', function () {
            skala = Math.min(4, +(skala + 0.15).toFixed(2));
            gambarDanPratinjau();
        });

        root.querySelector('[data-fp-zoom-out]').addEventListener('click', function () {
            skala = Math.max(0.4, +(skala - 0.15).toFixed(2));
            gambarDanPratinjau();
        });

        root.querySelector('[data-fp-putar-kiri]').addEventListener('click', function () {
            rotasi -= 90;
            gambarDanPratinjau();
        });

        root.querySelector('[data-fp-putar-kanan]').addEventListener('click', function () {
            rotasi += 90;
            gambarDanPratinjau();
        });

        root.querySelector('[data-fp-reset]').addEventListener('click', function () {
            skala = 1;
            rotasi = 0;
            geserX = 0;
            geserY = 0;
            gambarDanPratinjau();
        });

        stage.addEventListener('pointerdown', function (event) {
            if (!gambar) {
                return;
            }

            menyeret = true;
            mulaiX = event.clientX - geserX;
            mulaiY = event.clientY - geserY;
            stage.setPointerCapture(event.pointerId);
        });

        stage.addEventListener('pointermove', function (event) {
            if (!menyeret) {
                return;
            }

            geserX = event.clientX - mulaiX;
            geserY = event.clientY - mulaiY;
            gambarDanPratinjau();
        });

        stage.addEventListener('pointerup', function () {
            menyeret = false;
        });

        stage.addEventListener('pointercancel', function () {
            menyeret = false;
        });

        stage.addEventListener('wheel', function (event) {
            if (!gambar) {
                return;
            }

            event.preventDefault();
            skala = Math.min(4, Math.max(0.4, +(skala + (event.deltaY < 0 ? 0.12 : -0.12)).toFixed(2)));
            gambarDanPratinjau();
        }, { passive: false });

        tombolSimpan.addEventListener('click', function () {
            if (!gambar || !berkasTujuan) {
                tampilkanPesan('Silakan pilih gambar terlebih dahulu.');
                return;
            }

            const keluaran = document.createElement('canvas');
            keluaran.width = 512;
            keluaran.height = 512;
            const konteksKeluar = keluaran.getContext('2d');

            konteksKeluar.drawImage(canvas, 0, 0, keluaran.width, keluaran.height);

            tombolSimpan.disabled = true;
            tombolSimpan.textContent = 'Menyimpan...';

            keluaran.toBlob(function (blob) {
                if (!blob) {
                    tampilkanPesan('Foto gagal diproses. Silakan coba lagi.');
                    tombolSimpan.disabled = false;
                    tombolSimpan.textContent = 'Simpan';
                    return;
                }

                const berkas = new File([blob], 'foto-profil.jpg', { type: 'image/jpeg' });
                const wadah = new DataTransfer();
                wadah.items.add(berkas);
                berkasTujuan.files = wadah.files;
                formUnggah.submit();
            }, 'image/jpeg', 0.92);
        });
    });
</script>
