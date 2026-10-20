<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pendaftaran Berhasil Dikirim - SIMAHAD</title>

    <!-- Google Fonts -->
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
            --primary-maroon: #6e001b;
            --primary-maroon-hover: #580015;
            --maroon-border: #7c1a2e;
            --text-dark: #111827;
            --text-muted: #6b7280;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            background-image: url('{{ asset("images/mahad_al_jamiah.jpeg") }}');
            background-size: 100% auto;
            background-position: 0% 100%;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* Overlay */
        .page-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(230, 240, 255, 0.45) 0%, rgba(255, 255, 255, 0.65) 50%, rgba(255, 255, 255, 0.85) 100%);
            backdrop-filter: blur(3px);
            z-index: 1;
        }

        .confirmation-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 740px;
            background-color: #ffffff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 16px;
            padding: 2.75rem 3rem;
            box-shadow: 0 12px 36px rgba(110, 0, 27, 0.08);
            text-align: center;
        }

        /* Success Green Icon */
        .success-circle-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #dcfce7;
            border: 2px solid #86efac;
            color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem auto;
        }

        .success-title {
            color: var(--primary-maroon);
            font-size: 1.35rem;
            font-weight: 800;
            margin-bottom: 0.45rem;
            letter-spacing: -0.01em;
        }

        .success-desc {
            color: #4b5563;
            font-size: 0.86rem;
            line-height: 1.45;
            max-width: 580px;
            margin: 0 auto;
        }

        .divider {
            border-top: 1px solid #e5e7eb;
            margin: 1.5rem 0;
        }

        /* Alert Status Banner */
        .status-alert-box {
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.65rem;
            text-align: left;
        }

        .status-alert-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #92400e;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .status-alert-desc {
            color: #78350f;
            font-size: 0.82rem;
            margin-top: 0.3rem;
            line-height: 1.4;
            padding-left: 1.6rem;
        }

        /* Next Steps Section */
        .next-steps-section {
            text-align: left;
            margin-bottom: 1.75rem;
        }

        .next-steps-heading {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.85rem;
        }

        .steps-list {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            list-style: none;
        }

        .step-list-item {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            font-size: 0.84rem;
            color: #374151;
            line-height: 1.4;
        }

        .step-check-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #dcfce7;
            color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* Action Buttons */
        .btn-status-action {
            width: 100%;
            height: 44px;
            background-color: var(--primary-maroon);
            color: #ffffff;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(110, 0, 27, 0.2);
        }

        .btn-status-action:hover {
            background-color: var(--primary-maroon-hover);
            transform: translateY(-1px);
        }

        .link-back-login {
            display: inline-block;
            margin-top: 0.95rem;
            color: #4b5563;
            font-size: 0.82rem;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .link-back-login:hover {
            color: var(--primary-maroon);
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .confirmation-card {
                padding: 1.75rem 1.25rem;
            }
        }
    </style>
</head>
<body>

    <div class="page-overlay"></div>

    <div class="confirmation-card">
        <!-- Top Green Success Check Icon -->
        <div class="success-circle-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <!-- Success Title -->
        <h1 class="success-title">Pendaftaran Berhasil Dikirim!</h1>
        <p class="success-desc">Terima kasih telah mendaftar sebagai calon penghuni Ma'had Al-Jami'ah Tuanku Imam Bonjol.</p>

        <!-- Divider Line -->
        <div class="divider"></div>

        <!-- Alert Banner: Menunggu Verifikasi -->
        <div class="status-alert-box">
            <div class="status-alert-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Menunggu Verifikasi</span>
            </div>
            <p class="status-alert-desc">Data pendaftaran kamu sedang menunggu pemeriksaan oleh pengelola Ma'had.</p>
        </div>

        <!-- Section: Yang perlu kamu lakukan -->
        <div class="next-steps-section">
            <h2 class="next-steps-heading">Yang perlu kamu lakukan</h2>
            <ul class="steps-list">
                <li class="step-list-item">
                    <span class="step-check-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                    <span>Pastikan data dan dokumen yang dikirim sudah benar.</span>
                </li>
                <li class="step-list-item">
                    <span class="step-check-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                    <span>Pantau status pendaftaran untuk mengetahui hasil verifikasi.</span>
                </li>
                <li class="step-list-item">
                    <span class="step-check-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                    <span>Siapkan dokumen fisik yang perlu dibawa ke Ma'had sesuai ketentuan.</span>
                </li>
            </ul>
        </div>

        <!-- Button: Lihat Status Pendaftaran -->
        <a href="{{ route('dashboard') }}" class="btn-status-action">Lihat Status Pendaftaran</a>

        <!-- Link: Kembali ke halaman login -->
        <a href="{{ route('login') }}" class="link-back-login">Kembali ke halaman login</a>
    </div>

</body>
</html>
