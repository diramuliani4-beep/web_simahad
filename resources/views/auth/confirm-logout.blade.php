
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
            max-width: 900px;
            min-height: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 55px 50px;
            background: #fff;
            border: 1px solid #800d22;
            border-radius: 9px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.18);
        }

        /* IKON KELUAR */
        .logout-icon {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            border-radius: 50%;
            background: #f9edef;
            color: #800d22;
        }

        .logout-icon svg {
            width: 38px;
            height: 38px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* JUDUL */
        .logout-card h1 {
            margin-bottom: 22px;
            color: #222;
            font-size: 30px;
            font-weight: 500;
            text-align: center;
        }

        /* DESKRIPSI */
        .logout-card p {
            margin-bottom: 65px;
            color: #888;
            font-size: 21px;
            line-height: 1.6;
            text-align: center;
        }

        /* AREA TOMBOL */
        .logout-actions {
            width: 100%;
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 25px;
            padding: 0 4%;
        }

        /* AREA BATAL DAN FORM LOGOUT SAMA BESAR */
        .logout-actions > .btn-cancel,
        .logout-actions > form {
            flex: 1 1 0;
            width: 0;
            min-width: 0;
            height: 60px;
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
            height: 60px;
            min-height: 60px;
            margin: 0;
            padding: 0 12px;
            border: 1px solid #800d22;
            border-radius: 14px;
            font-family: inherit;
            font-size: 20px;
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
                max-width: 480px;
                min-height: 0;
                padding: 38px 24px;
            }

            .logout-icon {
                width: 80px;
                height: 80px;
                margin-bottom: 22px;
            }

            .logout-icon svg {
                width: 32px;
                height: 32px;
            }

            .logout-card h1 {
                margin-bottom: 17px;
                font-size: 25px;
            }

            .logout-card p {
                margin-bottom: 35px;
                font-size: 16px;
            }

            /* TOMBOL TERSUSUN VERTIKAL DI HP */
            .logout-actions {
                flex-direction: column-reverse;
                align-items: stretch;
                gap: 13px;
                padding: 0;
            }

            .logout-actions > .btn-cancel,
            .logout-actions > form {
                flex: none;
                width: 100%;
                height: 48px;
                min-width: 0;
            }

            .logout-actions button {
                flex: none;
                width: 100%;
                height: 48px;
                min-height: 48px;
                font-size: 17px;
            }
        }

        /* RESPONSIVE HP KECIL */
        @media (max-width: 380px) {
            .logout-card {
                padding: 30px 17px;
            }

            .logout-card h1 {
                font-size: 22px;
            }

            .logout-card p {
                font-size: 14px;
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