<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pendaftaran Mahasiswa Baru - Dokumen dan Pembayaran</title>

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
            --maroon-tint: #faedf0;
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
            background-size: cover;
            background-position: center top;
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

        .form-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 800px;
            background-color: #ffffff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 16px;
            padding: 2.25rem 2.75rem;
            box-shadow: 0 12px 36px rgba(110, 0, 27, 0.08);
        }

        /* Header */
        .card-title {
            color: var(--primary-maroon);
            font-size: 1.45rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 0.35rem;
            letter-spacing: -0.01em;
        }

        .card-subtitle {
            color: #4b5563;
            font-size: 0.84rem;
            text-align: center;
            margin-bottom: 1.75rem;
            line-height: 1.4;
        }

        /* Stepper */
        .stepper-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.75rem;
            padding: 0 2rem;
            position: relative;
        }

        .stepper-steps {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 500px;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            font-weight: 700;
            transition: all 0.2s;
        }

        .step-item.active .step-circle {
            background-color: var(--primary-maroon);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(110, 0, 27, 0.25);
        }

        .step-item.inactive .step-circle {
            background-color: #ffffff;
            border: 1.5px solid #94a3b8;
            color: #64748b;
        }

        .step-label {
            font-size: 0.78rem;
            margin-top: 0.4rem;
            white-space: nowrap;
        }

        .step-item.active .step-label {
            font-weight: 700;
            color: var(--text-dark);
        }

        .step-item.inactive .step-label {
            font-weight: 500;
            color: #64748b;
        }

        .step-line {
            flex-grow: 1;
            height: 1.5px;
            background-color: #cbd5e1;
            margin: -1.2rem 1.25rem 0 1.25rem;
        }

        /* Step Section Header */
        .step-section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.35rem;
        }

        .step-badge-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--primary-maroon);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-badge-text {
            display: flex;
            flex-direction: column;
        }

        .step-badge-title {
            font-size: 0.96rem;
            font-weight: 700;
            color: var(--primary-maroon);
        }

        .step-badge-sub {
            font-size: 0.76rem;
            color: var(--text-muted);
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.15rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.35rem;
        }

        .form-input {
            width: 100%;
            height: 42px;
            border: 1.2px solid var(--maroon-border);
            border-radius: 12px;
            padding: 0 0.95rem;
            font-size: 0.85rem;
            color: #1f2937;
            background-color: #ffffff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input::placeholder {
            color: #9ca3af;
            font-size: 0.82rem;
        }

        .form-input:focus {
            border-color: var(--primary-maroon-hover);
            box-shadow: 0 0 0 3px rgba(110, 0, 27, 0.1);
        }

        /* Textarea */
        textarea.form-input {
            height: 95px;
            padding: 0.75rem 0.95rem;
            font-family: inherit;
            resize: vertical;
        }

        .form-subtext {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        /* File Upload Box */
        .upload-dropzone {
            border: 1.2px solid var(--maroon-border);
            border-radius: 12px;
            min-height: 86px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            cursor: pointer;
            padding: 1rem;
            text-align: center;
            transition: all 0.2s;
            position: relative;
        }

        .upload-dropzone:hover {
            background-color: #fff8f9;
        }

        .upload-icon {
            color: var(--primary-maroon);
            margin-bottom: 0.3rem;
        }

        .upload-text {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary-maroon);
        }

        .upload-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        .file-hidden-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        /* Checkbox confirmation */
        .confirm-wrap {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-top: 0.75rem;
            cursor: pointer;
        }

        .confirm-checkbox {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-maroon);
            cursor: pointer;
            border-radius: 4px;
        }

        .confirm-label {
            font-size: 0.82rem;
            color: #374151;
            user-select: none;
            cursor: pointer;
        }

        /* Footer */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: 0.5rem;
        }

        .btn-maroon-pill {
            background-color: var(--primary-maroon);
            color: #ffffff;
            border: none;
            border-radius: 9999px;
            height: 38px;
            padding: 0 1.85rem;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-maroon-pill:hover {
            background-color: var(--primary-maroon-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(110, 0, 27, 0.2);
        }

        @media (max-width: 640px) {
            .form-card {
                padding: 1.5rem 1.25rem;
            }

            .stepper-container {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="page-overlay"></div>

    <div class="form-card">
        <!-- Title -->
        <h1 class="card-title">Pendaftaran Mahasiswa Baru</h1>
        <p class="card-subtitle">Lengkapi data dan persyaratan untuk mendaftar sebagai penghuni Ma'had Al-Jami'ah Tuanku Imam Bonjol.</p>

        <!-- Stepper -->
        <div class="stepper-container">
            <div class="stepper-steps">
                <!-- Step 1 (Completed) -->
                <div class="step-item inactive">
                    <div class="step-circle">1</div>
                    <span class="step-label">Data Diri</span>
                </div>

                <div class="step-line"></div>

                <!-- Step 2 (Completed) -->
                <div class="step-item inactive">
                    <div class="step-circle">2</div>
                    <span class="step-label">Data Akademik</span>
                </div>

                <div class="step-line"></div>

                <!-- Step 3 (Active) -->
                <div class="step-item active">
                    <div class="step-circle">3</div>
                    <span class="step-label">Persyaratan</span>
                </div>
            </div>
        </div>

        <!-- Step Section Header -->
        <div class="step-section-header">
            <div class="step-badge-num">3</div>
            <div class="step-badge-text">
                <span class="step-badge-title">Dokumen dan Pembayaran</span>
                <span class="step-badge-sub">Lengkapi persyaratan sebelum mengirim pendaftaran.</span>
            </div>
        </div>

        <!-- Form Form Pendaftaran Dokumen -->
        <form action="{{ route('pendaftaran.simpanDokumen') }}" method="POST" enctype="multipart/form-data" id="formDokumen">
            @csrf

            <!-- Alasan daftar ke Ma'had -->
            <div class="form-group">
                <label for="alasan_daftar" class="form-label">Alasan daftar ke Ma'had*</label>
                <textarea id="alasan_daftar" name="alasan_daftar" class="form-input" placeholder="Tuliskan alasan kamu ingin tinggal di Ma'had..." required>{{ old('alasan_daftar', $data['alasan_daftar'] ?? '') }}</textarea>
            </div>

            <!-- Nominal Pembayaran Awal -->
            <div class="form-group">
                <label for="nominal_pembayaran" class="form-label">Nominal Pembayaran Awal*</label>
                <input type="text" id="nominal_pembayaran" name="nominal_pembayaran" class="form-input" placeholder="Contoh: Rp1.000.000" value="{{ old('nominal_pembayaran', $data['nominal_pembayaran'] ?? '') }}" required>
                <div class="form-subtext">Pembayaran awal minimal Rp1.000.000</div>
            </div>

            <!-- Bukti Pembayaran Awal -->
            <div class="form-group">
                <label class="form-label">Bukti Pembayaran Awal*</label>
                <div class="upload-dropzone" id="uploadZone">
                    <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" class="file-hidden-input" accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelected(this, 'fileNameDisplay')">
                    <div class="upload-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                            <path d="M12 12v9"></path>
                            <path d="m8 16 4-4 4 4"></path>
                        </svg>
                    </div>
                    <span class="upload-text" id="fileNameDisplay">
                        {{ !empty($data['bukti_pembayaran_nama']) ? $data['bukti_pembayaran_nama'] : 'Unggah bukti pembayaran' }}
                    </span>
                    <span class="upload-sub">PDF, JPG, atau PNG</span>
                </div>
            </div>

            <!-- Konfirmasi Data -->
            <div class="form-group">
                <label class="form-label">Konfirmasi Data*</label>
                <label class="confirm-wrap" for="konfirmasi_data">
                    <input type="checkbox" id="konfirmasi_data" name="konfirmasi_data" value="1" class="confirm-checkbox" required {{ !empty($data['konfirmasi_data']) ? 'checked' : '' }}>
                    <span class="confirm-label">Saya menyatakan bahwa data yang saya isi sudah benar.</span>
                </label>
            </div>

            <!-- Footer Buttons -->
            <div class="form-footer">
                <a href="{{ route('pendaftaran.akademik') }}" class="btn-maroon-pill">Kembali</a>
                <button type="submit" class="btn-maroon-pill">Kirim Pendaftaran</button>
            </div>
        </form>
    </div>

    <script>
        function handleFileSelected(input, displayId) {
            if (input.files && input.files[0]) {
                document.getElementById(displayId).innerText = input.files[0].name;
            }
        }
    </script>
</body>
</html>
