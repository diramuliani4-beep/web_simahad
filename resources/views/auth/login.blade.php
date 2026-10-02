<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sistem Informasi Asrama Mahasiswa - Login</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary-maroon: #6e001b;
            --primary-maroon-hover: #560015;
            --primary-maroon-active: #450011;
            --text-dark: #1f2327;
            --text-serif: #222222;
            --bg-page: #fbfbfa;
            --shadow-maroon: 0 6px 14px rgba(0, 0, 0, 0.28);
            --shadow-button: 0 6px 15px rgba(0, 0, 0, 0.35);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            overflow-x: hidden;
        }

        .login-layout {
            display: flex;
            width: 100vw;
            min-height: 100vh;
        }

        /* Left Empty/Hero Section */
        .hero-section {
            flex: 1.15;
            display: none;
            background-color: var(--bg-page);
            position: relative;
        }

        @media (min-width: 900px) {
            .hero-section {
                display: block;
            }
        }

        /* Right Form Section */
        .form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 3rem 2rem;
            position: relative;
        }

        @media (min-width: 900px) {
            .form-section {
                padding: 4rem 5rem 4rem 2rem;
            }
        }

        .form-container {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
        }

        @media (min-width: 900px) {
            .form-container {
                margin: 0;
            }
        }

        /* Title Styling */
        .portal-title {
            font-family: 'Bebas Neue', 'Oswald', sans-serif;
            font-size: clamp(2.3rem, 4vw, 3.1rem);
            line-height: 1.08;
            letter-spacing: 0.03em;
            color: #1a1a1a;
            margin-bottom: 2.8rem;
            text-transform: uppercase;
        }

        .title-line {
            display: block;
            white-space: nowrap;
        }

        /* Stylized 'O' matching the design */
        .stylized-o {
            display: inline-block;
            vertical-align: 0.02em;
            width: 0.72em;
            height: 0.72em;
            margin: 0 0.04em;
        }

        .stylized-o svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.6rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-family: 'Lora', Georgia, serif;
            font-size: 1.05rem;
            font-weight: 500;
            color: var(--text-serif);
            margin-bottom: 0.45rem;
            letter-spacing: -0.01em;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .maroon-input {
            width: 100%;
            height: 52px;
            background-color: var(--primary-maroon);
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.02rem;
            padding: 0 1.25rem;
            border: none;
            border-radius: 14px;
            box-shadow: var(--shadow-maroon);
            outline: none;
            transition: all 0.25s ease;
        }

        .maroon-input::placeholder {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.95rem;
        }

        .maroon-input:focus {
            box-shadow: 0 0 0 3px rgba(110, 0, 27, 0.35), 0 8px 18px rgba(0, 0, 0, 0.35);
            transform: translateY(-1px);
        }

        .password-toggle-btn {
            position: absolute;
            right: 14px;
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.75);
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: color 0.2s, background-color 0.2s;
        }

        .password-toggle-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.15);
        }

        /* Action Section */
        .action-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 3.2rem;
        }

        .btn-login {
            background-color: var(--primary-maroon);
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 0.08em;
            padding: 0.75rem 3.2rem;
            min-width: 210px;
            height: 48px;
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-button);
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
        }

        .btn-login:hover {
            background-color: var(--primary-maroon-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .btn-login:active {
            transform: translateY(1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        }

        /* Links */
        .links-group {
            margin-top: 1.35rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.65rem;
            text-align: center;
        }

        .auth-link {
            font-family: 'Lora', Georgia, serif;
            font-size: 0.95rem;
            color: var(--text-serif);
            text-decoration: none;
            transition: color 0.2s, text-decoration 0.2s;
            cursor: pointer;
        }

        .auth-link:hover {
            color: var(--primary-maroon);
            text-decoration: underline;
        }

        /* Alerts */
        .alert-error {
            background-color: #fee2e2;
            border-left: 4px solid #b91c1c;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-status {
            background-color: #ecfdf5;
            border-left: 4px solid #059669;
            color: #065f46;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Quick Demo Account helper badge */
        .demo-badge-container {
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px dashed #e2e8f0;
            width: 100%;
            text-align: center;
        }

        .demo-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
        }

        .demo-badge:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* Modal styling for Lupa Password / Daftar */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #ffffff;
            border-radius: 16px;
            max-width: 420px;
            width: 100%;
            padding: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            position: relative;
            animation: modalFadeIn 0.25s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(12px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-title {
            font-family: 'Lora', Georgia, serif;
            font-size: 1.35rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }

        .modal-desc {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }

        .btn-modal-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            background: none;
            border: none;
            font-size: 1.4rem;
            cursor: pointer;
            color: #94a3b8;
            line-height: 1;
        }

        .btn-modal-close:hover {
            color: #1e293b;
        }

        .btn-modal-action {
            width: 100%;
            background-color: var(--primary-maroon);
            color: #ffffff;
            padding: 0.75rem;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-modal-action:hover {
            background-color: var(--primary-maroon-hover);
        }
    </style>
</head>
<body>
    <div class="login-layout">
        <!-- Left Side: Spacious Layout matching original design -->
        <div class="hero-section"></div>

        <!-- Right Side: Login Form -->
        <div class="form-section">
            <div class="form-container">
                <!-- Header Title -->
                <div class="portal-title">
                    <span class="title-line">SISTEM INF<span class="stylized-o" title="O">
                        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="50" cy="50" r="39" stroke="#1a1a1a" stroke-width="19" />
                        </svg>
                    </span>RMASI</span>
                    <span class="title-line">ASRAMA MAHASISWA</span>
                </div>

                <!-- Session / Status Flash -->
                @if (session('status'))
                    <div class="alert-status">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation Errors Flash -->
                @if ($errors->any())
                    <div class="alert-error">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login.post') }}" method="POST" id="loginForm" autocomplete="on">
                    @csrf

                    <!-- Email / NIM Field -->
                    <div class="form-group">
                        <label for="login" class="form-label">Email/NIM</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="login" 
                                name="login" 
                                class="maroon-input" 
                                value="{{ old('login') }}" 
                                required 
                                autofocus 
                                spellcheck="false"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="maroon-input" 
                                required
                            >
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Lihat Password">
                                <svg id="eyeIcon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeOffIcon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button & Links -->
                    <div class="action-area">
                        <button type="submit" class="btn-login" id="submitBtn">
                            LOGIN
                        </button>

                        <div class="links-group">
                            <a href="javascript:void(0)" class="auth-link" onclick="openModal('forgotPasswordModal')">
                                Lupa Password?
                            </a>
                            <a href="javascript:void(0)" class="auth-link" onclick="openModal('registerModal')">
                                Belum punya akun? Daftar!
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Demo helper -->
                <div class="demo-badge-container">
                    <button type="button" class="demo-badge" onclick="fillDemoCredentials()">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Gunakan Akun Demo (NIM: <strong>240101</strong>)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lupa Password -->
    <div id="forgotPasswordModal" class="modal-overlay" onclick="closeModalOnBackdrop(event, 'forgotPasswordModal')">
        <div class="modal-box">
            <button type="button" class="btn-modal-close" onclick="closeModal('forgotPasswordModal')">&times;</button>
            <h3 class="modal-title">Lupa Password</h3>
            <p class="modal-desc">Silakan hubungi administrator asrama atau masukkan Email/NIM Anda untuk mengajukan reset kata sandi akun.</p>
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; font-size: 0.85rem; color: #475569; margin-bottom: 0.4rem; font-weight: 500;">Email atau NIM Terdaftar</label>
                <input type="text" id="resetInput" placeholder="Contoh: 240101 / mahasiswa@asrama.ac.id" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-size: 0.95rem;">
            </div>
            <button type="button" class="btn-modal-action" onclick="handleResetPassword()">Kirim Permintaan Reset</button>
        </div>
    </div>

    <!-- Modal Pendaftaran -->
    <div id="registerModal" class="modal-overlay" onclick="closeModalOnBackdrop(event, 'registerModal')">
        <div class="modal-box">
            <button type="button" class="btn-modal-close" onclick="closeModal('registerModal')">&times;</button>
            <h3 class="modal-title">Pendaftaran Akun Asrama</h3>
            <p class="modal-desc">Akun mahasiswa asrama didaftarkan secara otomatis melalui bagian administrasi asrama kampus. Jika NIM Anda belum terdaftar, silakan hubungi pengelola asrama dengan membawa Kartu Tanda Mahasiswa (KTM).</p>
            <button type="button" class="btn-modal-action" onclick="closeModal('registerModal')">Mengerti</button>
        </div>
    </div>

    <script>
        // Password toggle visibility
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');

        togglePasswordBtn.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        });

        // Quick demo filler
        function fillDemoCredentials() {
            document.getElementById('login').value = '240101';
            document.getElementById('password').value = 'password123';
            document.getElementById('password').focus();
        }

        // Modal Helpers
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function closeModalOnBackdrop(event, modalId) {
            if (event.target === document.getElementById(modalId)) {
                closeModal(modalId);
            }
        }

        function handleResetPassword() {
            const val = document.getElementById('resetInput').value.trim();
            if (!val) {
                alert('Silakan masukkan Email atau NIM terlebih dahulu.');
                return;
            }
            alert('Permintaan reset password telah dicatat. Pengelola asrama akan memverifikasi data Anda.');
            closeModal('forgotPasswordModal');
        }

        // Form Submit Loading Feedback
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = 'Memproses...';
            btn.style.opacity = '0.85';
            btn.style.pointerEvents = 'none';
        });
    </script>
</body>
</html>
