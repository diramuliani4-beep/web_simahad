
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Konfirmasi Keluar - {{ $namaSistem ?? 'SIMAHAD' }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            background: #f3f3f3;
            font-family: Arial, Helvetica, sans-serif;
            overflow-x: hidden;
        }

        /* BACKGROUND BLUR */
        .background-blur {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                linear-gradient(
                    rgba(0, 0, 0, 0.50),
                    rgba(0, 0, 0, 0.50)
                ),
                url("{{ asset('images/mahad.jpeg') }}")
                center / cover no-repeat;
            filter: blur(8px);
            transform: scale(1.04);
            pointer-events: none;
        }

        /* LAPISAN GELAP BACKGROUND */
        .background-overlay {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: rgba(0, 0, 0, 0.30);
            pointer-events: none;
        }

        /* KOTAK KONFIRMASI */
        .logout-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            min-height: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 28px 28px;
            background: #fff;
            border: 1px solid #800d22;
            border-radius: 11px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.18);
        }

        /* IKON KELUAR */
        .logout-icon {
            width: 58px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            border-radius: 50%;
            background: #f9edef;
            color: #800d22;
        }

        .logout-icon svg {
            width: 26px;
            height: 26px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* JUDUL */
        .logout-card h1 {
            margin-bottom: 9px;
            color: #222;
            font-size: 19px;
            font-weight: 500;
            line-height: 1.35;
            text-align: center;
        }

        /* DESKRIPSI */
        .logout-card p {
            margin-bottom: 24px;
            color: #888;
            font-size: 13px;
            line-height: 1.55;
            text-align: center;
        }

        /* AREA TOMBOL */
        .logout-actions {
            width: 100%;
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 12px;
            padding: 0;
        }

        /* AREA BATAL DAN FORM LOGOUT SAMA BESAR */
        .logout-actions > .btn-cancel,
        .logout-actions > form {
            flex: 1 1 0;
            width: 0;
            min-width: 0;
            height: 42px;
            margin: 0;
        }

        /* FORM LOGOUT */
        .logout-actions form {
            display: flex;
            padding: 0;
        }

        /* KEDUA TOMBOL SAMA LEBAR DAN TINGGI */
        .logout-actions button {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1 1 auto;
            width: 100%;
            height: 42px;
            min-height: 42px;
            margin: 0;
            padding: 0 12px;
            border: 1px solid #800d22;
            border-radius: 9px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.2;
            cursor: pointer;
            transition:
                background 0.2s,
                color 0.2s,
                transform 0.2s;
        }

        .logout-actions button:hover {
            transform: translateY(-1px);
        }

        /* TOMBOL BATAL */
        .btn-cancel {
            background: #fff;
            color: #800d22;
        }

        .btn-cancel:hover {
            background: #f9edef;
        }

        /* TOMBOL YA, KELUAR */
        .btn-confirm {
            background: #700b20;
            color: #fff;
        }

        .btn-confirm:hover {
            background: #590018;
        }

        /* RESPONSIVE TABLET DAN HP */
        @media (max-width: 768px) {
            body {
                padding: 18px;
            }

            .logout-card {
                width: 100%;
                max-width: 400px;
                min-height: 0;
                padding: 24px 18px;
            }

            .logout-icon {
                width: 50px;
                height: 50px;
                margin-bottom: 12px;
            }

            .logout-icon svg {
                width: 22px;
                height: 22px;
            }

            .logout-card h1 {
                margin-bottom: 8px;
                font-size: 17px;
            }

            .logout-card p {
                margin-bottom: 20px;
                font-size: 12.5px;
            }

            /* TOMBOL TERSUSUN VERTIKAL DI HP */
            .logout-actions {
                flex-direction: column-reverse;
                align-items: stretch;
                gap: 9px;
                padding: 0;
            }

            .logout-actions > .btn-cancel,
            .logout-actions > form {
                flex: none;
                width: 100%;
                height: 40px;
                min-width: 0;
            }

            .logout-actions button {
                flex: none;
                width: 100%;
                height: 40px;
                min-height: 40px;
                font-size: 12.5px;
            }
        }

        /* RESPONSIVE HP KECIL */
        @media (max-width: 380px) {
            .logout-card {
                padding: 20px 15px;
            }

            .logout-card h1 {
                font-size: 16px;
            }

            .logout-card p {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    {{-- BACKGROUND BLUR --}}
    <div class="background-blur"></div>
    <div class="background-overlay"></div>

    {{-- KOTAK KONFIRMASI --}}
    <main class="logout-card">

        <div class="logout-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M10 17l5-5-5-5"></path>
                <path d="M15 12H3"></path>
                <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"></path>
            </svg>
        </div>

        <h1>Keluar dari Akun?</h1>

        <p>
            Apakah kamu yakin ingin keluar dari akun SIMAHAD?
        </p>

        <div class="logout-actions">

            <button
                type="button"
                class="btn-cancel"
                onclick="kembaliKeHalaman()"
            >
                Batal
            </button>

            <form
                method="POST"
                action="{{ route('logout') }}"
                id="logoutForm"
            >
                @csrf

                <button
                    type="submit"
                    class="btn-confirm"
                >
                    Ya, Keluar
                </button>
            </form>

        </div>

    </main>

    <script>
        function kembaliKeHalaman() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '/';
            }
        }
    </script>

</body>
</html>