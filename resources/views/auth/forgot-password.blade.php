<!DOCTYPE html>
<html lang="id" data-sm-auth-page="forgot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $namaSistem ?? 'SIMAHAD' }} - Lupa Kata Sandi</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo_uin.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_uin.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@1,500;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- SIMAHAD: siapkan animasi halaman tujuan sebelum layout pertama digambar. -->
    <script>
        (function () {
            try {
                var saved = sessionStorage.getItem('simahad-auth-page-turn');
                if (!saved) return;
                sessionStorage.removeItem('simahad-auth-page-turn');
                var transition = JSON.parse(saved);
                if (transition && transition.to === document.documentElement.getAttribute('data-sm-auth-page') &&
                    Date.now() - transition.time < 8000 &&
                    !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    document.documentElement.classList.add('sm-auth-page-arriving');
                }
            } catch (error) { /* Storage tidak tersedia: navigasi tetap normal. */ }
        })();
    </script>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-maroon: #6B0D1B;
            --brand-maroon-hover: #560A16;
            --brand-maroon-dark: #3D0710;
            --brand-gold: #C9A96A;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --bg-canvas: #f3f4f6;
            --border-maroon: #6B0D1B;
        }

        body {
            font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
        }

        .forgot-card {
            width: 100%;
            max-width: 880px;
            background: #ffffff;
            border: 1.5px solid var(--border-maroon);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 860px) {
            .forgot-card {
                flex-direction: row;
                min-height: 500px;
            }
        }

        /* ================= LEFT COLUMN ================= */
        .hero-panel {
    position: relative;
    flex: 1.05;
    background-color: #d1d5db;
    background-image: url("{{ asset('images/mahad_al_jamiah.jpeg') }}");
    background-size: cover;
    background-position: center bottom;
    background-repeat: no-repeat;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1.6rem 1.6rem 1.5rem;
    min-height: 380px;
    overflow: hidden;
    border-bottom: 1.5px solid var(--border-maroon);
}

        @media (min-width: 860px) {
            .hero-panel {
                border-bottom: none;
                border-right: 1.5px solid var(--border-maroon);
                min-height: 500px;
            }
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg, 
                rgba(255, 255, 255, 0.35) 0%, 
                rgba(255, 255, 255, 0.15) 30%, 
                rgba(255, 255, 255, 0.5) 65%, 
                rgba(255, 255, 255, 0.2) 100%
            );
            pointer-events: none;
        }

        .hero-content-top {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo-img {
            width: 38px;
            height: 38px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .building-logo-img {
            width: 22px;
            height: 22px;
            object-fit: contain;
        }

        .center-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .hero-brand-text {
            display: flex;
            flex-direction: column;
        }

        .hero-brand-title {
            font-size: 1.12rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: #111827;
            line-height: 1.1;
        }

        .hero-brand-sub {
            font-size: 0.68rem;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.25;
            margin-top: 2px;
        }

        .hero-brand-inst {
            font-size: 0.64rem;
            font-weight: 500;
            color: #374151;
            line-height: 1.25;
        }

        .hero-content-middle {
            position: relative;
            z-index: 2;
            margin-top: 1.6rem;
            margin-bottom: auto;
            max-width: 360px;
        }

        .hero-welcome {
            font-family: "Lora", Georgia, serif;
            font-style: italic;
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--brand-maroon);
            margin-bottom: 2px;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        .hero-simahad {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--brand-maroon-dark);
            line-height: 1.05;
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        .hero-tagline {
            font-size: 0.8rem;
            font-weight: 500;
            color: #111827;
            line-height: 1.45;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.7);
        }

        .building-sign-overlay {
            position: relative;
            z-index: 2;
            align-self: center;
            text-align: center;
            margin-top: 0.6rem;
            padding: 0.45rem 0.95rem;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(2px);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .building-sign-logo {
            display: inline-flex;
            margin-bottom: 2px;
        }

        .building-sign-text {
            font-size: 0.76rem;
            font-weight: 800;
            color: #27303f;
            letter-spacing: 0.08em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .building-sign-sub {
            font-size: 0.68rem;
            font-weight: 800;
            color: #334155;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        /* ================= RIGHT COLUMN ================= */
        .form-panel {
            flex: 1.05;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.25rem 1.35rem 1.35rem;
        }

        @media (min-width: 600px) {
            .form-panel {
                padding: 1.4rem 1.9rem 1.6rem;
            }
        }

        .motto-bar {
            text-align: right;
            font-size: 0.7rem;
            font-weight: 600;
            color: #374151;
            letter-spacing: 0.02em;
            margin-bottom: 0.6rem;
        }

        .inner-frame {
            border: 1.5px solid var(--border-maroon);
            border-radius: 22px 22px 18px 18px;
            padding: 1.35rem 1.3rem 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        @media (min-width: 600px) {
            .inner-frame {
                padding: 1.5rem 1.6rem 1.4rem;
            }
        }

        .center-emblem {
            margin-bottom: 0.45rem;
        }

        .form-headline {
            color: var(--brand-maroon);
            font-size: 1.02rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.25rem;
        }

        .form-subheadline {
            color: #4b5563;
            font-size: 0.75rem;
            line-height: 1.35;
            text-align: center;
            max-width: 290px;
            margin-bottom: 1rem;
        }

        .alert-box {
            width: 100%;
            padding: 0.5rem 0.7rem;
            border-radius: 8px;
            font-size: 0.75rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 7px;
            line-height: 1.35;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .auth-form {
            width: 100%;
        }

        .form-row {
            margin-bottom: 0.75rem;
            width: 100%;
        }

        .field-label {
            display: block;
            font-size: 0.76rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.3rem;
        }

        .field-input {
            width: 100%;
            height: 36px;
            border: 1.5px solid var(--border-maroon);
            border-radius: 7px;
            padding: 0 11px;
            font-family: inherit;
            font-size: 0.8rem;
            color: #111827;
            background-color: #ffffff;
            outline: none;
            transition: all 0.2s ease;
        }

        .field-input::placeholder {
            color: #9ca3af;
            font-size: 0.78rem;
        }

        .field-input:focus {
            box-shadow: 0 0 0 3px rgba(120, 16, 35, 0.15);
            border-color: var(--brand-maroon);
        }

        .btn-submit {
            width: 100%;
            height: 36px;
            background-color: var(--brand-maroon);
            color: #ffffff;
            border: none;
            border-radius: 7px;
            font-family: inherit;
            font-weight: 700;
            font-size: 0.84rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(120, 16, 35, 0.2);
        }

        .btn-submit:hover {
            background-color: var(--brand-maroon-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(120, 16, 35, 0.25);
        }

        .register-footer {
            text-align: center;
            font-size: 0.74rem;
            color: #1f2937;
            margin-top: 0.85rem;
        }

        .register-link {
            color: var(--brand-maroon);
            font-weight: 700;
            text-decoration: none;
            margin-left: 2px;
        }

        .register-link:hover {
            text-decoration: underline;
        }
        /* ================================================================
           SIMAHAD | INTEGRITAS DIALOGIS • HEXAGON AURORA
           Dekorasi saja. Tidak menyentuh HTML form, route, atau interaksi.
           ================================================================ */
        html { min-width: 0; }
        body {
            overflow-x: hidden;
            align-items: safe center;
            background: #160d1b;
            isolation: isolate;
        }
        .simahad-scene {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
            isolation: isolate;
            background:
                radial-gradient(ellipse at 11% 18%, rgba(145, 20, 64, .50), transparent 43%),
                radial-gradient(ellipse at 89% 74%, rgba(135, 51, 94, .32), transparent 43%),
                radial-gradient(ellipse at 65% 5%, rgba(117, 32, 70, .27), transparent 42%),
                linear-gradient(118deg, #2d0c22 0%, #1e1227 48%, #111525 100%);
        }
        .sm-aurora,
        .sm-halo,
        .sm-stars,
        .sm-orbit,
        .sm-glimmer,
        .sm-hex {
            position: absolute;
            pointer-events: none;
        }
        .sm-aurora {
            width: 68vw;
            height: 74vh;
            min-width: 450px;
            border-radius: 50%;
            filter: blur(66px);
            opacity: .65;
            animation: smAuroraFloat 18s ease-in-out infinite alternate;
        }
        .sm-aurora--a {
            left: -31%; top: -40%;
            background: radial-gradient(ellipse, rgba(216, 45, 91, .52), rgba(112, 25, 58, .22) 53%, transparent 72%);
        }
        .sm-aurora--b {
            right: -31%; bottom: -43%;
            background: radial-gradient(ellipse, rgba(226, 129, 74, .39), rgba(150, 58, 96, .19) 54%, transparent 74%);
            animation-direction: alternate-reverse;
            animation-duration: 21s;
        }
        .sm-halo {
            left: 50%; top: 50%;
            width: min(1260px, 94vw);
            height: min(850px, 98vh);
            transform: translate(-50%, -50%);
            background: radial-gradient(ellipse, rgba(217, 91, 105, .21) 2%, rgba(249, 162, 102, .105) 44%, transparent 74%);
            filter: blur(28px);
            animation: smHaloPulse 9s ease-in-out infinite;
        }
        /* Hexagon besar DIAM: hanya sorotan lembut yang bergerak melintasi garis. */
        .sm-orbit {
            left: 50%; top: 49%;
            width: min(1230px, 98vw);
            height: min(890px, 104vh);
            transform: translate(-50%, -50%);
            opacity: .79;
            filter: drop-shadow(0 0 8px rgba(255, 183, 93, .17));
            animation: none;
        }
        .sm-orbit--inner {
            width: min(1020px, 87vw);
            height: min(730px, 92vh);
            opacity: .42;
            filter: drop-shadow(0 0 6px rgba(255, 190, 112, .13));
            animation: none;
        }
        .sm-orbit svg,
        .sm-hex svg { display: block; width: 100%; height: 100%; }
        .sm-orbit .sm-hex-path {
            stroke: #d6a365;
            stroke-width: 1.65;
            fill: rgba(242, 116, 91, .025);
        }
        .sm-orbit--inner .sm-hex-path { stroke: #a84f62; stroke-width: 1.2; }
        /* Satu siklus 11 detik: sapuan selesai di 70%; hexagon utuh bersinar;
           baru setelah itu kotak login menyala. Hexagon besar tidak berpindah. */
        .sm-orbit .sm-hex-path {
            animation: smHexagonFinished 11s ease-in-out infinite;
        }
        .sm-orbit--inner .sm-hex-path {
            animation: smHexagonInnerFinished 11s ease-in-out infinite;
        }
        /* Bukan garis putus-putus/ulat: satu sorotan gradasi lebar menyapu tepi utuh. */
        .sm-orbit .sm-orbit-sweep {
            fill: none;
            stroke-width: 4;
            stroke-linejoin: round;
            filter: drop-shadow(0 0 6px rgba(255, 206, 125, .65));
        }
        .sm-orbit--inner .sm-orbit-sweep { stroke-width: 3; opacity: .7; }

        .sm-hex {
            left: var(--x);
            top: var(--y);
            width: var(--size);
            aspect-ratio: 1 / 1;
            opacity: var(--op, .75);
            transform-origin: center center;
            filter: drop-shadow(0 0 7px rgba(253, 191, 111, .55));
            animation: smHexDrift var(--dur, 17s) ease-in-out var(--delay, 0s) infinite alternate,
                       smHexShine 5.5s ease-in-out var(--delay, 0s) infinite;
        }
        .sm-hex::after {
            content: '';
            position: absolute;
            inset: 16%;
            border-radius: 50%;
            background: rgba(249, 171, 92, .28);
            filter: blur(18px);
            opacity: .43;
        }
        .sm-hex polygon {
            fill: rgba(206, 89, 83, .10);
            stroke: rgba(253, 202, 121, .90);
            stroke-width: 1.7;
            vector-effect: non-scaling-stroke;
            stroke-linejoin: round;
        }
        .sm-hex polyline,
        .sm-hex line {
            fill: none;
            stroke: rgba(255, 214, 156, .20);
            stroke-width: 1.2;
            vector-effect: non-scaling-stroke;
        }
        .sm-hex--filled polygon { fill: rgba(230, 105, 86, .24); }
        .sm-hex--filled { filter: drop-shadow(0 0 11px rgba(255, 163, 88, .50)); }
        .sm-hex--muted polygon { stroke: rgba(237, 115, 134, .56); fill: rgba(163, 46, 94, .17); }
        .sm-hex--muted { filter: drop-shadow(0 0 8px rgba(238, 98, 125, .22)); }
        .sm-stars {
            inset: 0;
            background-image:
                radial-gradient(circle, rgba(255, 225, 176, .92) 1px, transparent 2px),
                radial-gradient(circle, rgba(255, 255, 255, .64) .8px, transparent 1.8px),
                radial-gradient(circle, rgba(248, 168, 174, .75) 1.15px, transparent 2.4px);
            background-size: 207px 187px, 341px 287px, 409px 359px;
            background-position: 34px 18px, 113px 86px, 231px 124px;
            opacity: .65;
            animation: smStarField 14s ease-in-out infinite alternate;
        }
        .sm-glimmer {
            left: var(--x); top: var(--y);
            width: var(--size, 3px);
            height: var(--size, 3px);
            border-radius: 50%;
            background: #fff1bd;
            box-shadow: 0 0 7px 2px rgba(255, 200, 109, .60), 0 0 23px 5px rgba(255, 178, 109, .16);
            opacity: .22;
            animation: smTwinkle var(--dur, 4.3s) ease-in-out var(--delay, 0s) infinite;
        }
        .sm-glimmer--cross::after,
        .sm-glimmer--cross::before {
            content: '';
            position: absolute;
            left: 50%; top: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(90deg, transparent, rgba(255, 232, 170, .8), transparent);
        }
        .sm-glimmer--cross::after { width: 22px; height: 1px; }
        .sm-glimmer--cross::before { width: 1px; height: 22px; }
        /* Cahaya tetap menyala SESUDAH hexagon selesai.
           Redup, hangat, hanya di LUAR kotak; tak menimpa foto atau form. */
        .forgot-card {
            position: relative;
            z-index: 2;
            border-color: rgba(130, 17, 40, .88);
            box-shadow:
                0 27px 73px rgba(10, 5, 19, .40),
                0 0 0 1px rgba(247, 194, 126, .42),
                0 0 19px 5px rgba(246, 219, 173, .22),
                0 0 49px 17px rgba(220, 149, 104, .11);
            animation: smCardStayLit 11s ease-in-out 1 both;
        }
        .form-panel {
            position: relative;
            isolation: isolate;
            background:
                radial-gradient(ellipse at 102% 101%, rgba(255, 217, 162, .13), transparent 31%),
                radial-gradient(ellipse at -20% -4%, rgba(153, 31, 76, .055), transparent 40%),
                #fff;
        }
        .form-panel::before,
        .form-panel::after {
            content: '';
            position: absolute;
            pointer-events: none;
            z-index: -1;
        }
        .form-panel::before {
            inset: 0 0 auto -8%;
            height: 76px;
            background: linear-gradient(115deg, rgba(131, 16, 45, .12), rgba(236, 148, 125, .085) 52%, transparent 80%);
            clip-path: polygon(0 0, 100% 0, 100% 16%, 70% 57%, 37% 85%, 0 45%);
        }
        .form-panel::after {
            bottom: -18px; right: -13px;
            width: 70%; height: 125px;
            background: linear-gradient(142deg, transparent 28%, rgba(249, 202, 129, .20) 72%, rgba(127, 9, 43, .13));
            clip-path: polygon(0 100%, 100% 9%, 100% 100%);
        }
        .form-panel > * { position: relative; z-index: 1; }
        .inner-frame {
            background: rgba(255, 255, 255, .90);
            box-shadow: 0 8px 30px rgba(110, 20, 51, .035), inset 0 0 15px rgba(255, 235, 220, .10);
            /* Tidak ada cahaya berkedip di dalam form. */
        }
        .forgot-card .hero-panel { background-blend-mode: normal; }
        @keyframes smAuroraFloat {
            0% { transform: translate3d(0, 0, 0) scale(1); }
            100% { transform: translate3d(85px, 50px, 0) scale(1.13); }
        }
        @keyframes smHaloPulse {
            0%,100% { opacity: .55; transform: translate(-50%, -50%) scale(.94); }
            50% { opacity: .95; transform: translate(-50%, -50%) scale(1.08); }
        }
        /* Jalur melayang organik: sedikit mengayun, berbelok dan kembali.
           Durasi/delay tiap hex berbeda sehingga tak terlihat serempak. */
        @keyframes smHexDrift {
            0%   { transform: translate3d(-5px, 5px, 0) rotate(-7deg) scale(.97); }
            23%  { transform: translate3d(calc(var(--dx, 13px) * .56), -17px, 0) rotate(1deg) scale(1.01); }
            49%  { transform: translate3d(var(--dx, 13px), -7px, 0) rotate(5deg) scale(1.035); }
            76%  { transform: translate3d(calc(var(--dx, 13px) * -.48), 17px, 0) rotate(-2deg) scale(.99); }
            100% { transform: translate3d(-5px, 5px, 0) rotate(-7deg) scale(.97); }
        }
        @keyframes smHexShine {
            0%,100% { opacity: calc(var(--op, .75) * .66); }
            50% { opacity: var(--op, .75); }
        }
        @keyframes smTwinkle {
            0%,100% { opacity: .14; transform: scale(.65); }
            42% { opacity: 1; transform: scale(1.7); }
            68% { opacity: .38; transform: scale(.9); }
        }
        @keyframes smStarField {
            0%,100% { opacity: .36; transform: translate3d(0,0,0); }
            50% { opacity: .8; transform: translate3d(-13px, 8px, 0); }
        }
        /* Tahapan TEGAS: 0–70% cahaya membentuk garis hexagon,
           70–73% garis hexagon selesai/menyala,
           76–88% barulah kotak login berpendar,
           90–100% kembali tenang sebelum siklus selanjutnya. */
        @keyframes smHexagonFinished {
            0%, 66%, 100% {
                stroke: #d6a365;
                filter: drop-shadow(0 0 2px rgba(241, 180, 111, .10));
            }
            71%, 78% {
                stroke: #ffe6b1;
                filter: drop-shadow(0 0 11px rgba(255, 205, 131, .68));
            }
            89% {
                stroke: #dcb987;
                filter: drop-shadow(0 0 4px rgba(255, 193, 124, .26));
            }
        }
        @keyframes smHexagonInnerFinished {
            0%, 66%, 100% {
                stroke: #a84f62;
                filter: drop-shadow(0 0 2px rgba(225, 131, 110, .08));
            }
            71%, 78% {
                stroke: #f7ca97;
                filter: drop-shadow(0 0 9px rgba(255, 197, 135, .52));
            }
            89% {
                stroke: #bd7c7e;
                filter: drop-shadow(0 0 3px rgba(255, 176, 137, .18));
            }
        }
        /* Urutan asli tetap: 0–72% tunggu hexagon, setelah itu
           cahaya card naik perlahan dan menetap (tanpa kedip/flash). */
        @keyframes smCardStayLit {
            0%, 72% {
                box-shadow:
                    0 27px 73px rgba(10, 5, 19, .40),
                    0 0 0 1px rgba(241, 171, 107, .24),
                    0 0 13px 3px rgba(239, 211, 165, .07),
                    0 0 35px 12px rgba(219, 151, 106, .045);
            }
            78% {
                box-shadow:
                    0 27px 73px rgba(10, 5, 19, .40),
                    0 0 0 1px rgba(245, 188, 122, .34),
                    0 0 17px 4px rgba(247, 219, 174, .14),
                    0 0 43px 14px rgba(221, 151, 106, .08);
            }
            86%, 100% {
                box-shadow:
                    0 27px 73px rgba(10, 5, 19, .40),
                    0 0 0 1px rgba(247, 194, 126, .42),
                    0 0 19px 5px rgba(246, 219, 173, .22),
                    0 0 49px 17px rgba(220, 149, 104, .11);
            }
        }
        @media (max-width: 859.98px) {
            .sm-orbit { width: 1050px; height: 820px; }
            .sm-orbit--inner { width: 800px; height: 630px; }
            .sm-hex--desktop { display: none; }
        }
        @media (max-width: 599.98px) {
            body { padding: 1rem .75rem; align-items: flex-start; }
            .forgot-card { width: 100%; }
            .sm-orbit { width: 790px; height: 620px; opacity: .46; }
            .sm-orbit--inner { width: 620px; height: 520px; }
            .sm-aurora { opacity: .48; filter: blur(50px); }
            .sm-hex { opacity: .49; }
            .sm-hex--mobile-hide, .sm-glimmer--mobile-hide { display: none; }
            .sm-halo { width: 100%; height: 100%; }
            .form-panel::before { opacity: .7; }
        }
        @media (prefers-reduced-motion: reduce) {
            .simahad-scene *, .forgot-card { animation: none !important; }
            .sm-orbit .sm-orbit-sweep { display: none !important; }
        }

        /* ============================================================
           DETAIL TAMBAHAN — HEXAGON MINI HIDUP (dekorasi murni)
           Bergerak mengikuti jalur lengkung pendek, berubah maroon→gold
           secara bergantian; tanpa partikel merayap pada pinggirannya.
           ============================================================ */
        /* Segi enam kecil: lintasan organik nyata, bukan berdenyut di tempat.
           Setiap mini punya amplitudo, tempo, dan fase tersendiri. */
        .sm-hex--mini {
            opacity: var(--op, .38);
            filter: drop-shadow(0 0 3px rgba(207, 147, 93, .13));
            animation:
                smMiniFloat var(--dur, 12s) cubic-bezier(.43,.04,.57,.96) var(--delay, 0s) infinite,
                smMiniVisibility var(--breath, 7s) ease-in-out var(--delay, 0s) infinite;
            will-change: transform;
            backface-visibility: hidden;
        }
        .sm-hex--mini::after {
            inset: 26%;
            background: rgba(224, 166, 88, .10);
            filter: blur(8px);
            opacity: .18;
        }
        .sm-hex--mini svg polygon {
            stroke: rgba(220, 132, 146, .45);
            fill: rgba(160, 65, 97, .07);
            stroke-width: 1.55;
            animation: smMiniToGold var(--gold, 9s) ease-in-out var(--color-delay, -2s) infinite;
        }
        .sm-hex--mini svg polyline,
        .sm-hex--mini svg line {
            stroke: rgba(225, 171, 143, .13);
            stroke-width: 1.05;
        }
        /* Lintasan tidak linear: melayang, berbelok, mengayun, kembali mulus.
           Transform di awal dan akhir identik supaya loop tak pernah meloncat. */
        @keyframes smMiniFloat {
            0%,100% {
                transform: translate3d(calc(var(--mx, 68px) * -.18), calc(var(--my, -50px) * -.12), 0)
                           rotate(-5deg) scale(.94);
            }
            17% {
                transform: translate3d(calc(var(--mx, 68px) * .25), calc(var(--my, -50px) * -.56), 0)
                           rotate(1deg) scale(.99);
            }
            38% {
                transform: translate3d(calc(var(--mx, 68px) * .82), calc(var(--my, -50px) * -.16), 0)
                           rotate(7deg) scale(1.035);
            }
            56% {
                transform: translate3d(var(--mx, 68px), calc(var(--my, -50px) * .60), 0)
                           rotate(11deg) scale(1.06);
            }
            76% {
                transform: translate3d(calc(var(--mx, 68px) * .25), var(--my, -50px), 0)
                           rotate(0deg) scale(1.01);
            }
            89% {
                transform: translate3d(calc(var(--mx, 68px) * -.34), calc(var(--my, -50px) * .42), 0)
                           rotate(-4deg) scale(.97);
            }
        }
        @keyframes smMiniVisibility {
            0%,100% { opacity: calc(var(--op, .38) * .75); }
            35% { opacity: var(--op, .38); }
            67% { opacity: calc(var(--op, .38) * .9); }
        }
        @keyframes smMiniToGold {
            0%, 22%, 100% {
                fill: rgba(160, 65, 97, .07);
                stroke: rgba(220, 132, 146, .45);
            }
            49% {
                fill: rgba(210, 148, 80, .18);
                stroke: rgba(230, 182, 117, .66);
            }
            62%, 76% {
                fill: rgba(218, 169, 83, .26);
                stroke: rgba(244, 199, 127, .79);
            }
            91% {
                fill: rgba(168, 89, 94, .10);
                stroke: rgba(226, 143, 140, .49);
            }
        }
        /* Pada HP cukup sebagian hexagon mini: layar tetap ringan. */
        @media (max-width: 859.98px) {
            .sm-mini--desktop { display: none; }
        }
        @media (max-width: 599.98px) {
            .sm-hex--mini { opacity: .30; filter: none; }
            .sm-hex--mini::after { display: none; }
            .sm-mini--mobile-hide { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            .sm-hex--mini, .sm-hex--mini svg polygon { animation: none !important; }
            .sm-hex--mini svg polygon {
                stroke: rgba(234, 185, 119, .48);
                fill: rgba(206, 151, 76, .12);
            }
        }

        /* ============================================================
           ENTRANCE FINAL — animasi pertama kali membuka halaman Lupa Kata Sandi
           Ringan, natural, dan tidak menimpa glow setelah hexagon selesai.
           Semua animasi masuk berlangsung SEKALI saja.
           ============================================================ */
        .forgot-card {
            /* Properti berbeda: entrance = opacity/transform;
               glow = box-shadow. Keduanya bisa berjalan tanpa konflik. */
            animation:
                smRegisterCardEnter .96s cubic-bezier(.18,.70,.25,1) .06s both,
                smCardStayLit 11s ease-in-out 1 both;
        }
        @keyframes smRegisterCardEnter {
            0%   { opacity: 0; transform: translate3d(0, 20px, 0) scale(.984); }
            46%  { opacity: .88; }
            74%  { opacity: 1; transform: translate3d(0, -2px, 0) scale(1.002); }
            100% { opacity: 1; transform: translate3d(0, 0, 0) scale(1); }
        }
        /* Konten menyusul secara singkat dan bertahap, tanpa mengubah layout. */
        .hero-content-top {
            animation: smRegisterDetailEnter .65s ease-out .16s both;
        }
        .hero-content-middle {
            animation: smRegisterDetailEnter .72s ease-out .26s both;
        }
        .building-sign-overlay {
            animation: smRegisterDetailEnter .70s ease-out .34s both;
        }
        .motto-bar {
            animation: smRegisterDetailEnter .65s ease-out .21s both;
        }
        .inner-frame {
            animation: smRegisterDetailEnter .78s cubic-bezier(.18,.70,.25,1) .30s both;
        }
        @keyframes smRegisterDetailEnter {
            0%   { opacity: 0; transform: translate3d(0, 9px, 0); }
            100% { opacity: 1; transform: translate3d(0, 0, 0); }
        }
        @media (prefers-reduced-motion: reduce) {
            .forgot-card, .hero-content-top, .hero-content-middle,
            .building-sign-overlay, .motto-bar, .inner-frame {
                animation: none !important;
            }
        }

        /* ================================================================
           SIMAHAD | SOFT BOOK PAGE TURN (Login <-> Register)
           - Efek hanya pada panel form sebelah kanan, bukan seluruh foto.
           - Animasi sekali saat mengklik tautan antarhalaman.
           - Form, action Laravel, ukuran dan tombol tidak dimodifikasi.
           ================================================================ */
        .login-card, .forgot-card {
            perspective: 1400px;
            perspective-origin: 52% 50%;
        }
        .login-card > .form-panel, .forgot-card > .form-panel {
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform-style: preserve-3d;
            transform-origin: left center;
        }
        /* Menutup lembar ketika pindah: Login -> Register. */
        body.sm-auth-page-leaving[data-sm-auth-target="forgot"] .login-card > .form-panel {
            animation: smAuthCloseForward .42s cubic-bezier(.42,.08,.72,.42) both !important;
        }
        /* Menutup lembar ke arah sebaliknya: Lupa Sandi -> Login. */
        body.sm-auth-page-leaving[data-sm-auth-target="login"] .forgot-card > .form-panel {
            transform-origin: right center;
            animation: smAuthCloseBackward .42s cubic-bezier(.42,.08,.72,.42) both !important;
        }
        /* Halaman tujuan dibuka dari sisi buku. */
        html.sm-auth-page-arriving[data-sm-auth-page="forgot"] .forgot-card > .form-panel {
            animation: smAuthOpenForward .59s cubic-bezier(.16,.76,.24,1) .045s both !important;
        }
        html.sm-auth-page-arriving[data-sm-auth-page="login"] .login-card > .form-panel {
            transform-origin: right center;
            animation: smAuthOpenBackward .59s cubic-bezier(.16,.76,.24,1) .045s both !important;
        }
        /* Pada halaman Login, entrance yang lama tidak diputar ulang saat kembali
           dari Register. Cahaya permanen tetap diatur oleh smCardStayLit. */
        html.sm-auth-page-arriving[data-sm-auth-page="login"] .login-card {
            animation: smCardStayLit 11s ease-in-out 1 both !important;
        }
        @keyframes smAuthCloseForward {
            0%   { opacity: 1; transform: rotateY(0deg); filter: brightness(1); }
            37%  { opacity: 1; transform: rotateY(-22deg); filter: brightness(.95); }
            100% { opacity: .04; transform: rotateY(-82deg); filter: brightness(.84); }
        }
        @keyframes smAuthCloseBackward {
            0%   { opacity: 1; transform: rotateY(0deg); filter: brightness(1); }
            37%  { opacity: 1; transform: rotateY(22deg); filter: brightness(.95); }
            100% { opacity: .04; transform: rotateY(82deg); filter: brightness(.84); }
        }
        @keyframes smAuthOpenForward {
            0%   { opacity: .12; transform: rotateY(67deg); filter: brightness(.93); }
            62%  { opacity: .95; transform: rotateY(-3deg); filter: brightness(1); }
            100% { opacity: 1; transform: rotateY(0deg); filter: brightness(1); }
        }
        @keyframes smAuthOpenBackward {
            0%   { opacity: .12; transform: rotateY(-67deg); filter: brightness(.93); }
            62%  { opacity: .95; transform: rotateY(3deg); filter: brightness(1); }
            100% { opacity: 1; transform: rotateY(0deg); filter: brightness(1); }
        }
        @media (max-width: 859.98px) {
            /* Form berada di bawah foto pada tablet/HP: transisi slide lebih
               nyaman daripada memutar lembar besar di layar sempit. */
            body.sm-auth-page-leaving .login-card > .form-panel,
            body.sm-auth-page-leaving .forgot-card > .form-panel {
                animation-name: smAuthMobileClose !important;
                transform-origin: center top;
            }
            html.sm-auth-page-arriving .login-card > .form-panel,
            html.sm-auth-page-arriving .forgot-card > .form-panel {
                animation-name: smAuthMobileOpen !important;
                transform-origin: center top;
            }
            @keyframes smAuthMobileClose {
                from { opacity: 1; transform: translateY(0) scale(1); }
                to   { opacity: 0; transform: translateY(16px) scale(.985); }
            }
            @keyframes smAuthMobileOpen {
                from { opacity: 0; transform: translateY(16px) scale(.985); }
                to   { opacity: 1; transform: translateY(0) scale(1); }
            }
        }
        @media (prefers-reduced-motion: reduce) {
            body.sm-auth-page-leaving .form-panel,
            html.sm-auth-page-arriving .form-panel {
                animation: none !important;
                transform: none !important;
                opacity: 1 !important;
                filter: none !important;
            }
        }


        /* Penyamaan dimensi dengan Register & Login: foto dan card tetap proporsional. */
        .forgot-card { min-width: 0; }
        .forgot-card .inner-frame { width: 100%; min-height: 566px; }
        .forgot-card .auth-form { margin-top: auto; margin-bottom: auto; }
        .forgot-card .field-input { font-size: 0.8rem; }
        .forgot-card .field-input:focus-visible,
        .forgot-card .btn-submit:focus-visible,
        .forgot-card .register-link:focus-visible { outline: 2px solid #800b22; outline-offset: 2px; }
        .forgot-card .alert-success {
            color: #176338; background: #ecfdf2; border: 1px solid #b6e6c5;
        }
        @media (min-width: 600px) {
            .forgot-card .inner-frame { min-height: 581px; }
        }
        @media (min-width: 860px) {
            .forgot-card { min-height: 658px; }
            .forgot-card .hero-panel { min-height: 658px; }
        }
        @media (max-width: 599.98px) {
            .forgot-card .inner-frame { min-height: 540px; }
            .forgot-card .auth-form { margin-top: 18px; margin-bottom: 0; }
        }
    </style>
</head>
<body>
    <!-- Background hexagon identik dengan halaman Register SIMAHAD. -->
    <div class="simahad-scene" aria-hidden="true">
        <div class="sm-aurora sm-aurora--a"></div>
        <div class="sm-aurora sm-aurora--b"></div>
        <div class="sm-halo"></div>
        <div class="sm-stars"></div>
        <div class="sm-orbit">
            <svg viewBox="0 0 1000 700" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="smGoldSweepOuter" gradientUnits="userSpaceOnUse" x1="-600" y1="0" x2="-230" y2="0">
                        <stop offset="0%" stop-color="#ffe1ae" stop-opacity="0"/>
                        <stop offset="30%" stop-color="#ffd69b" stop-opacity=".18"/>
                        <stop offset="50%" stop-color="#fff2d0" stop-opacity=".96"/>
                        <stop offset="70%" stop-color="#ffd69b" stop-opacity=".18"/>
                        <stop offset="100%" stop-color="#ffe1ae" stop-opacity="0"/>
                        <animate attributeName="x1" values="-600;1320;1320" keyTimes="0;0.70;1" calcMode="linear" dur="11s" repeatCount="indefinite"/>
                        <animate attributeName="x2" values="-230;1690;1690" keyTimes="0;0.70;1" calcMode="linear" dur="11s" repeatCount="indefinite"/>
                    </linearGradient>
                </defs>
                <path class="sm-hex-path" d="M500 5 L979 178 L979 522 L500 695 L21 522 L21 178 Z"/>
                <path class="sm-orbit-sweep" d="M500 5 L979 178 L979 522 L500 695 L21 522 L21 178 Z" stroke="url(#smGoldSweepOuter)"/>
            </svg>
        </div>
        <div class="sm-orbit sm-orbit--inner">
            <svg viewBox="0 0 1000 700" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="smGoldSweepInner" gradientUnits="userSpaceOnUse" x1="-600" y1="0" x2="-230" y2="0">
                        <stop offset="0%" stop-color="#ffe1ae" stop-opacity="0"/>
                        <stop offset="30%" stop-color="#ffd69b" stop-opacity=".18"/>
                        <stop offset="50%" stop-color="#fff2d0" stop-opacity=".96"/>
                        <stop offset="70%" stop-color="#ffd69b" stop-opacity=".18"/>
                        <stop offset="100%" stop-color="#ffe1ae" stop-opacity="0"/>
                        <animate attributeName="x1" values="-600;1320;1320" keyTimes="0;0.70;1" calcMode="linear" dur="11s" repeatCount="indefinite"/>
                        <animate attributeName="x2" values="-230;1690;1690" keyTimes="0;0.70;1" calcMode="linear" dur="11s" repeatCount="indefinite"/>
                    </linearGradient>
                </defs>
                <path class="sm-hex-path" d="M500 5 L979 178 L979 522 L500 695 L21 522 L21 178 Z"/>
                <path class="sm-orbit-sweep" d="M500 5 L979 178 L979 522 L500 695 L21 522 L21 178 Z" stroke="url(#smGoldSweepInner)" opacity=".72"/>
            </svg>
        </div>
        <span class="sm-hex sm-hex--filled sm-hex--desktop" style="--x:12%;--y:16%;--size:118px;--delay:-.7s;--dur:12.2s;--dx:23px;--op:.75">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--filled" style="--x:82%;--y:20%;--size:105px;--delay:-3s;--dur:9.8s;--dx:-18px;--op:.92">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--filled sm-hex--mobile-hide" style="--x:5%;--y:58%;--size:98px;--delay:-1.6s;--dur:11.0s;--dx:21px;--op:.84">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--filled" style="--x:86%;--y:74%;--size:96px;--delay:-4s;--dur:12.2s;--dx:-25px;--op:.86">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--muted sm-hex--desktop" style="--x:22%;--y:75%;--size:68px;--delay:-5s;--dur:9.8s;--dx:11px;--op:.70">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--muted sm-hex--mobile-hide" style="--x:93%;--y:42%;--size:52px;--delay:-1s;--dur:8.5s;--dx:-12px;--op:.71">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--muted sm-hex--mobile-hide" style="--x:5%;--y:33%;--size:48px;--delay:-6s;--dur:12.2s;--dx:15px;--op:.60">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--muted" style="--x:78%;--y:8%;--size:58px;--delay:-3.5s;--dur:11.0s;--dx:18px;--op:.68">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--desktop sm-hex--mobile-hide" style="--x:26%;--y:4%;--size:43px;--delay:-2s;--dur:10.4s;--dx:-8px;--op:.62">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--desktop" style="--x:68%;--y:92%;--size:63px;--delay:-4.8s;--dur:9.2s;--dx:19px;--op:.62">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <i class="sm-glimmer sm-glimmer--cross sm-glimmer--mobile-hide" style="--x:8%;--y:12%;--size:2px;--delay:-0.00s;--dur:3.10s"></i>
        <i class="sm-glimmer" style="--x:21%;--y:35%;--size:3px;--delay:-1.19s;--dur:3.71s"></i>
        <i class="sm-glimmer" style="--x:17%;--y:78%;--size:2.5px;--delay:-2.38s;--dur:4.32s"></i>
        <i class="sm-glimmer sm-glimmer--mobile-hide" style="--x:31%;--y:10%;--size:3.5px;--delay:-3.57s;--dur:4.93s"></i>
        <i class="sm-glimmer" style="--x:76%;--y:13%;--size:2px;--delay:-4.76s;--dur:5.54s"></i>
        <i class="sm-glimmer" style="--x:89%;--y:36%;--size:3px;--delay:-0.95s;--dur:6.15s"></i>
        <i class="sm-glimmer sm-glimmer--cross sm-glimmer--mobile-hide" style="--x:94%;--y:78%;--size:2.5px;--delay:-2.14s;--dur:6.76s"></i>
        <i class="sm-glimmer" style="--x:79%;--y:88%;--size:3.5px;--delay:-3.33s;--dur:3.10s"></i>
        <i class="sm-glimmer" style="--x:51%;--y:5%;--size:2px;--delay:-4.52s;--dur:3.71s"></i>
        <i class="sm-glimmer sm-glimmer--mobile-hide" style="--x:60%;--y:94%;--size:3px;--delay:-0.71s;--dur:4.32s"></i>
        <i class="sm-glimmer" style="--x:5%;--y:90%;--size:2.5px;--delay:-1.90s;--dur:4.93s"></i>
        <i class="sm-glimmer" style="--x:94%;--y:9%;--size:3.5px;--delay:-3.09s;--dur:5.54s"></i>
        <i class="sm-glimmer sm-glimmer--cross sm-glimmer--mobile-hide" style="--x:35%;--y:78%;--size:2px;--delay:-4.28s;--dur:6.15s"></i>
        <i class="sm-glimmer" style="--x:71%;--y:61%;--size:3px;--delay:-0.47s;--dur:6.76s"></i>
        <i class="sm-glimmer" style="--x:14%;--y:49%;--size:2.5px;--delay:-1.66s;--dur:3.10s"></i>
        <i class="sm-glimmer sm-glimmer--mobile-hide" style="--x:87%;--y:58%;--size:3.5px;--delay:-2.85s;--dur:3.71s"></i>
        <i class="sm-glimmer" style="--x:41%;--y:18%;--size:2px;--delay:-4.04s;--dur:4.32s"></i>
        <i class="sm-glimmer" style="--x:53%;--y:82%;--size:3px;--delay:-0.23s;--dur:4.93s"></i>
        <i class="sm-glimmer sm-glimmer--cross sm-glimmer--mobile-hide" style="--x:3%;--y:67%;--size:2.5px;--delay:-1.42s;--dur:5.54s"></i>
        <i class="sm-glimmer" style="--x:98%;--y:32%;--size:3.5px;--delay:-2.61s;--dur:6.15s"></i>
        <i class="sm-glimmer" style="--x:27%;--y:92%;--size:2px;--delay:-3.80s;--dur:6.76s"></i>
        <i class="sm-glimmer sm-glimmer--mobile-hide" style="--x:68%;--y:29%;--size:3px;--delay:-4.99s;--dur:3.10s"></i>
        <i class="sm-glimmer" style="--x:40%;--y:5%;--size:2.5px;--delay:-1.18s;--dur:3.71s"></i>
        <i class="sm-glimmer" style="--x:73%;--y:8%;--size:3.5px;--delay:-2.37s;--dur:4.32s"></i>
        <!-- Hexagon mini tambahan: fase bergerak dan berubah emas secara asinkron. -->
        <span class="sm-hex sm-hex--mini sm-mini--mobile-hide" style="--x:3%;--y:9%;--size:31px;--mx:66px;--my:-55px;--dur:12.7s;--breath:7.2s;--gold:9.6s;--delay:-3.1s;--color-delay:-4.7s;--op:0.39">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--desktop" style="--x:21%;--y:7%;--size:25px;--mx:-58px;--my:-67px;--dur:14.4s;--breath:6.5s;--gold:8.8s;--delay:-5.3s;--color-delay:-1.1s;--op:0.43">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini" style="--x:2%;--y:39%;--size:24px;--mx:59px;--my:-62px;--dur:11.9s;--breath:8.1s;--gold:9.5s;--delay:-2.4s;--color-delay:-5.2s;--op:0.37">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--mobile-hide" style="--x:12%;--y:53%;--size:33px;--mx:-73px;--my:58px;--dur:15.1s;--breath:7.4s;--gold:8.3s;--delay:-6.4s;--color-delay:-3.6s;--op:0.43">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini" style="--x:5%;--y:84%;--size:28px;--mx:55px;--my:-79px;--dur:13.8s;--breath:7.9s;--gold:10.2s;--delay:-4.7s;--color-delay:-7.8s;--op:0.49">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--desktop" style="--x:26%;--y:91%;--size:24px;--mx:86px;--my:-49px;--dur:12.3s;--breath:6.3s;--gold:9.7s;--delay:-8.1s;--color-delay:-1.7s;--op:0.37">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--desktop" style="--x:34%;--y:5%;--size:24px;--mx:-70px;--my:63px;--dur:15.7s;--breath:9.3s;--gold:11.1s;--delay:-4.2s;--color-delay:-8.3s;--op:0.38">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--desktop" style="--x:75%;--y:7%;--size:27px;--mx:65px;--my:-61px;--dur:11.8s;--breath:8.3s;--gold:8.9s;--delay:-7.0s;--color-delay:-4.0s;--op:0.38">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--mobile-hide" style="--x:89%;--y:11%;--size:38px;--mx:-80px;--my:53px;--dur:16.2s;--breath:7.7s;--gold:10.8s;--delay:-2.8s;--color-delay:-8.0s;--op:0.43">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini" style="--x:97%;--y:26%;--size:26px;--mx:-69px;--my:-57px;--dur:12.9s;--breath:6.4s;--gold:9.8s;--delay:-6.3s;--color-delay:-1.4s;--op:0.45">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--mobile-hide" style="--x:91%;--y:41%;--size:22px;--mx:74px;--my:61px;--dur:14.9s;--breath:8.5s;--gold:9.3s;--delay:-4.1s;--color-delay:-5.6s;--op:0.39">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--mobile-hide" style="--x:97%;--y:62%;--size:34px;--mx:-57px;--my:-75px;--dur:13.4s;--breath:7.2s;--gold:8.7s;--delay:-8.4s;--color-delay:-6.9s;--op:0.40">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini" style="--x:86%;--y:81%;--size:30px;--mx:-86px;--my:52px;--dur:15.3s;--breath:6.9s;--gold:10.6s;--delay:-3.8s;--color-delay:-3.4s;--op:0.41">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--desktop" style="--x:73%;--y:91%;--size:25px;--mx:72px;--my:-67px;--dur:12.1s;--breath:8.6s;--gold:9.1s;--delay:-5.7s;--color-delay:-2.4s;--op:0.38">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--desktop" style="--x:40%;--y:95%;--size:21px;--mx:64px;--my:-53px;--dur:14.5s;--breath:7.1s;--gold:10.0s;--delay:-4.9s;--color-delay:-6.6s;--op:0.34">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--mobile-hide" style="--x:7%;--y:22%;--size:22px;--mx:61px;--my:61px;--dur:13.2s;--breath:8.2s;--gold:8.4s;--delay:-1.8s;--color-delay:-5.9s;--op:0.40">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--mobile-hide" style="--x:94%;--y:89%;--size:20px;--mx:-65px;--my:-55px;--dur:15.9s;--breath:7.0s;--gold:9.9s;--delay:-6.2s;--color-delay:-3.3s;--op:0.38">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
        <span class="sm-hex sm-hex--mini sm-mini--desktop" style="--x:79%;--y:53%;--size:21px;--mx:-75px;--my:59px;--dur:11.7s;--breath:6.8s;--gold:9.1s;--delay:-1.9s;--color-delay:-7.1s;--op:0.32">
            <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="50,3 92,26 92,74 50,97 8,74 8,26"/><polyline points="8,26 50,48 92,26"/><line x1="50" y1="48" x2="50" y2="97"/></svg>
        </span>
    </div>
    <div class="forgot-card">
        <!-- ================= LEFT HERO PANEL ================= -->
        <div class="hero-panel">
            <div class="hero-overlay"></div>

            <div class="hero-content-top">
                <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol" class="brand-logo-img">
                <div class="hero-brand-text">
                    <span class="hero-brand-title">SIMAHAD</span>
                    <span class="hero-brand-sub">Sistem Informasi Manajemen Ma'had</span>
                    <span class="hero-brand-inst">Al-Jami'ah Tuanku Imam Bonjol</span>
                </div>
            </div>

            <div class="hero-content-middle">
                <div class="hero-welcome">Selamat Datang di</div>
                <div class="hero-simahad">SIMAHAD</div>
                <p class="hero-tagline">
                    Akses informasi, kelola data,<br>
                    wujudkan layanan Ma'had yang lebih baik.
                </p>
            </div>

            <div class="building-sign-overlay">
                <div class="building-sign-logo">
                    <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol" class="building-logo-img">
                </div>
                <div class="building-sign-text">MA'HAD AL-JAMI'AH</div>
                <div class="building-sign-sub">TUANKU IMAM BONJOL</div>
            </div>
        </div>

        <!-- ================= RIGHT FORM PANEL ================= -->
        <div class="form-panel">
            <div class="motto-bar">
                Berakhlak | Berilmu | Berprestasi
            </div>

            <div class="inner-frame">
                <div class="center-emblem">
                    <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol" class="center-logo-img">
                </div>

                <h1 class="form-headline">Lupa Kata Sandi</h1>
                <p class="form-subheadline">
                    Masukkan Email atau NIM terdaftar dan tentukan kata sandi baru.
                </p>

                @if (session('status'))
                    <div class="alert-box alert-success" role="status">{{ session('status') }}</div>
                @endif
                @if (session('success'))
                    <div class="alert-box alert-success" role="status">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert-box alert-error" role="alert">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert-box alert-error" role="alert">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('password.update') }}" method="POST" class="auth-form" autocomplete="on">
                    @csrf

                    <div class="form-row">
                        <label for="identifier" class="field-label">Email atau NIM Terdaftar</label>
                        <input type="text" id="identifier" name="identifier" class="field-input"
                               placeholder="Masukkan email atau NIM" value="{{ old('identifier') }}"
                               autocomplete="username" spellcheck="false" required autofocus>
                    </div>

                    <div class="form-row">
                        <label for="password" class="field-label">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" class="field-input"
                               placeholder="Minimal 6 karakter" autocomplete="new-password" required>
                    </div>

                    <div class="form-row">
                        <label for="password_confirmation" class="field-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="field-input" placeholder="Ulangi kata sandi baru"
                               autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="btn-submit">Perbarui Kata Sandi</button>

                    <div class="register-footer">
                        Sudah ingat kata sandi?
                        <a href="{{ route('login') }}" class="register-link">Masuk di sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- SIMAHAD: transisi link saja, tidak mengintersepsi submit form. -->
    <script>
        (function () {
            var page = document.documentElement.getAttribute('data-sm-auth-page');
            var destination = page === 'login' ? 'register' : 'login';
            var moving = false;
            document.addEventListener('click', function (event) {
                var anchor = event.target.closest && event.target.closest('a.register-link');
                if (!anchor || moving || event.defaultPrevented || event.button !== 0 ||
                    event.ctrlKey || event.metaKey || event.shiftKey || event.altKey ||
                    (anchor.target && anchor.target !== '_self') || anchor.hasAttribute('download')) return;
                var next;
                try { next = new URL(anchor.href, window.location.href); } catch (error) { return; }
                if (next.origin !== window.location.origin ||
                    next.pathname === window.location.pathname ||
                    window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                event.preventDefault();
                moving = true;
                try {
                    sessionStorage.setItem('simahad-auth-page-turn', JSON.stringify({
                        to: destination, time: Date.now()
                    }));
                } catch (error) { /* Animasi keluar tetap berjalan. */ }
                document.body.setAttribute('data-sm-auth-target', destination);
                document.body.classList.add('sm-auth-page-leaving');
                window.setTimeout(function () { window.location.assign(next.href); }, 420);
            });
            /* Ketika browser Back/Forward merestore dokumen dari bfcache,
               jangan biarkan panel tetap tertutup. */
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    moving = false;
                    document.body.classList.remove('sm-auth-page-leaving');
                    document.body.removeAttribute('data-sm-auth-target');
                    document.documentElement.classList.remove('sm-auth-page-arriving');
                }
            });
        })();
    </script>

</body>
</html>