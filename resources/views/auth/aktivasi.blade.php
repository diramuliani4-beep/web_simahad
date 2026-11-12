<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun - SIMAHAD</title>

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
            --maroon: #6B0D1B;
            --maroon-dark: #52070f;
            --maroon-tint: #F8EDEF;
            --gold: #C9A96A;
            --ink: #2b2528;
            --muted: #7a6f72;
            --line: #ecd9dd;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 22px 16px;
            background: radial-gradient(circle at 12% 18%, #fbf1f2 0%, #f7f5f4 46%, #f4f2f1 100%);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--ink);
        }

        .aktivasi-kartu {
            width: 100%;
            max-width: 430px;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 20px 44px -20px rgba(82, 7, 15, .28);
        }

        .aktivasi-kepala {
            padding: 20px 24px;
            background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
            color: #fff;
        }

        .aktivasi-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .aktivasi-brand img {
            width: 34px;
            height: 34px;
            object-fit: contain;
            padding: 3px;
            border-radius: 9px;
            background: rgba(255, 255, 255, .14);
        }

        .aktivasi-brand strong {
            display: block;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .06em;
        }

        .aktivasi-brand span {
            display: block;
            font-size: 10.5px;
            color: var(--gold);
            font-weight: 600;
            letter-spacing: .04em;
        }

        .aktivasi-kepala h1 {
            margin: 16px 0 5px;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.35;
        }

        .aktivasi-kepala p {
            font-size: 12px;
            line-height: 1.6;
            color: rgba(255, 255, 255, .82);
        }

        .aktivasi-isi {
            padding: 22px 24px 24px;
        }

        .aktivasi-info {
            margin-bottom: 16px;
            padding: 11px 13px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--maroon-tint);
            font-size: 11.5px;
            line-height: 1.65;
            color: #6f3a44;
        }

        .aktivasi-info b {
            color: var(--maroon);
        }

        .aktivasi-pesan {
            margin-bottom: 14px;
            padding: 10px 12px;
            border: 1px solid #f0c4cd;
            border-radius: 9px;
            background: #fdf1f3;
            color: #9c2337;
            font-size: 11.5px;
            line-height: 1.6;
        }

        .aktivasi-field {
            margin-bottom: 13px;
        }

        .aktivasi-field label {
            display: block;
            margin-bottom: 6px;
            font-size: 11.5px;
            font-weight: 700;
            color: #443a3d;
        }

        .aktivasi-sandi {
            position: relative;
        }

        .aktivasi-sandi input {
            width: 100%;
            height: 40px;
            padding: 0 72px 0 12px;
            border: 1px solid #dfcfd3;
            border-radius: 9px;
            background: #fff;
            font-family: inherit;
            font-size: 12.5px;
            color: #241f21;
            outline: none;
            transition: border-color .16s ease, box-shadow .16s ease;
        }

        .aktivasi-sandi input:focus {
            border-color: var(--maroon);
            box-shadow: 0 0 0 3px rgba(107, 13, 27, .1);
        }

        .aktivasi-toggle {
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            padding: 4px 8px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: var(--maroon);
            font-family: inherit;
            font-size: 10.5px;
            font-weight: 700;
            cursor: pointer;
        }

        .aktivasi-toggle:hover {
            background: var(--maroon-tint);
        }

        .aktivasi-bantuan {
            margin-top: 5px;
            font-size: 10.5px;
            color: var(--muted);
        }

        .aktivasi-aksi {
            display: flex;
            gap: 9px;
            margin-top: 18px;
        }

        .aktivasi-btn {
            display: inline-flex;
            flex: 1;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 9px 14px;
            border: 1px solid #e2d3d7;
            border-radius: 9px;
            background: #fff;
            color: #5a4d51;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .aktivasi-btn:hover {
            background: var(--maroon-tint);
            color: var(--maroon);
        }

        .aktivasi-btn--utama {
            border-color: var(--maroon);
            background: var(--maroon);
            color: #fff;
        }

        .aktivasi-btn--utama:hover {
            border-color: var(--maroon-dark);
            background: var(--maroon-dark);
            color: #fff;
        }

        .aktivasi-btn:disabled {
            opacity: .65;
            cursor: wait;
        }

        .aktivasi-kosong {
            text-align: center;
        }

        .aktivasi-kosong svg {
            width: 42px;
            height: 42px;
            margin-bottom: 12px;
            color: var(--maroon);
        }

        .aktivasi-kosong h2 {
            margin-bottom: 8px;
            font-size: 15px;
            font-weight: 700;
        }

        .aktivasi-kosong p {
            margin-bottom: 18px;
            font-size: 11.5px;
            line-height: 1.7;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <div class="aktivasi-kartu">
        <header class="aktivasi-kepala">
            <div class="aktivasi-brand">
                <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol">
                <div>
                    <strong>SIMAHAD</strong>
                    <span>Ma'had Al-Jami'ah</span>
                </div>
            </div>

            <h1>Aktivasi Akun</h1>
            <p>Buat kata sandi Anda sendiri untuk mengaktifkan akses SIMAHAD.</p>
        </header>

        <div class="aktivasi-isi">
            @if (! $pengguna)
                <div class="aktivasi-kosong">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 8v4m0 4h.01" stroke-linecap="round" />
                    </svg>
                    <h2>Tautan tidak berlaku</h2>
                    <p>
                        Tautan aktivasi sudah kedaluwarsa, sudah pernah dipakai, atau tidak dikenali.
                        Silakan minta Admin Sistem mengirim ulang tautan aktivasi akun Anda.
                    </p>
                    <a href="{{ route('login') }}" class="aktivasi-btn aktivasi-btn--utama">Kembali ke Halaman Masuk</a>
                </div>
            @else
                <div class="aktivasi-info">
                    Akun <b>{{ $pengguna->name }}</b> ({{ $pengguna->labelPeran() }}) belum memiliki kata sandi.
                    Kata sandi yang Anda buat di sini tidak dapat dilihat oleh Admin Sistem.
                    Tautan berlaku <b>{{ $berlakuJam }} jam</b> sejak dikirim.
                </div>

                @if (session('error'))
                    <div class="aktivasi-pesan" role="alert">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="aktivasi-pesan" role="alert">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('aktivasi.simpan', ['token' => $token]) }}" id="formAktivasi">
                    @csrf

                    <div class="aktivasi-field">
                        <label for="password">Kata Sandi Baru</label>
                        <div class="aktivasi-sandi">
                            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter">
                            <button type="button" class="aktivasi-toggle" data-sasaran="password">Lihat</button>
                        </div>
                        <p class="aktivasi-bantuan">Minimal 8 karakter. Gunakan kombinasi huruf dan angka.</p>
                    </div>

                    <div class="aktivasi-field">
                        <label for="password_confirmation">Ulangi Kata Sandi Baru</label>
                        <div class="aktivasi-sandi">
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Ulangi kata sandi">
                            <button type="button" class="aktivasi-toggle" data-sasaran="password_confirmation">Lihat</button>
                        </div>
                    </div>

                    <div class="aktivasi-aksi">
                        <a href="{{ route('login') }}" class="aktivasi-btn">Batal</a>
                        <button type="submit" class="aktivasi-btn aktivasi-btn--utama" id="tombolAktivasi">Simpan &amp; Masuk</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <script>
        document.querySelectorAll('.aktivasi-toggle').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                const input = document.getElementById(tombol.dataset.sasaran);

                if (!input) {
                    return;
                }

                const tampil = input.type === 'password';
                input.type = tampil ? 'text' : 'password';
                tombol.textContent = tampil ? 'Sembunyi' : 'Lihat';
            });
        });

        const form = document.getElementById('formAktivasi');

        if (form) {
            form.addEventListener('submit', function () {
                const tombol = document.getElementById('tombolAktivasi');
                tombol.disabled = true;
                tombol.textContent = 'Menyimpan...';
            });
        }
    </script>
</body>
</html>
