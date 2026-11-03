<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pendaftaran Mahasiswa Baru - Data Akademik</title>

    <!-- Font dan desain asli SIMAHAD tetap digunakan. -->
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
            margin-bottom: 1rem;
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
        /* Select Styling */
        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236e001b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 14px;
            cursor: pointer;
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

        /* Pesan kesalahan validasi */
        .form-error {
            display: block;
            margin-top: 0.4rem;
            font-size: 0.76rem;
            line-height: 1.4;
            color: #b42318;
        }
        .form-input.is-invalid,
        .upload-dropzone.is-invalid {
            border-color: #b42318;
        }
        .uploaded-note {
            color: #526273;
            font-size: 0.75rem;
            line-height: 1.5;
            margin-top: 0.4rem;
        }
        .form-required-note {
            color: var(--text-muted);
            font-size: 0.75rem;
            margin: 0.65rem 0 0;
        }
        .upload-dropzone:focus-within {
            border-color: var(--primary-maroon-hover);
            box-shadow: 0 0 0 3px rgba(110, 0, 27, 0.1);
        }


        /* Ringkasan kesalahan yang mudah ditemukan. */
        [hidden] { display: none !important; }
        .validation-summary {
            background: #fff1f2;
            border: 1px solid #fda4af;
            border-left: 4px solid #be123c;
            border-radius: 12px;
            color: #881337;
            padding: 0.9rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.82rem;
            line-height: 1.6;
        }
        .validation-summary strong { font-weight: 800; }
        .validation-summary ul { margin: 0.35rem 0 0 1.2rem; }
        .validation-summary a { color: inherit; text-decoration: underline; }
        .validation-summary p { margin-top: 0.3rem; }
        .form-error:not([hidden]) { display: block; }
        .upload-dropzone.is-invalid { background: #fff6f6; }

    </style>
</head>
<body>
    <div class="page-overlay"></div>

    <div class="form-card">
        <h1 class="card-title">Pendaftaran Mahasiswa Baru</h1>
        <p class="card-subtitle">Lengkapi data dan persyaratan untuk mendaftar sebagai penghuni Ma'had Al-Jami'ah Tuanku Imam Bonjol.</p>

        <!-- Indikator tahapan pendaftaran. -->
        <div class="stepper-container">
            <div class="stepper-steps">
                <div class="step-item inactive">
                    <div class="step-circle">1</div>
                    <span class="step-label">Data Diri</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item active">
                    <div class="step-circle">2</div>
                    <span class="step-label">Data Akademik</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item inactive">
                    <div class="step-circle">3</div>
                    <span class="step-label">Persyaratan</span>
                </div>
            </div>
        </div>

        <div class="step-section-header">
            <div class="step-badge-num">2</div>
            <div class="step-badge-text">
                <span class="step-badge-title">Data Akademik</span>
                <span class="step-badge-sub">Masukkan informasi penerimaan dan pendidikan di UIN.</span>
            </div>
        </div>

        <!-- Ringkasan kesalahan dari validasi Laravel dan browser. -->
        <div class="validation-summary" id="validationSummary" role="alert" tabindex="-1"
             @if (!$errors->any()) hidden @endif>
            <strong>Periksa kembali Data Akademik</strong>
            @if ($errors->any())
                <p>Belum dapat melanjutkan karena:</p>
                <ul id="serverErrorList">
                    @foreach (['tahun_angkatan' => 'Tahun Angkatan', 'fakultas' => 'Fakultas', 'program_studi' => 'Program Studi', 'bukti_penerimaan' => 'Bukti Penerimaan UIN'] as $field => $label)
                        @if ($errors->has($field))
                            <li><a href="#{{ $field }}"><strong>{{ $label }}:</strong> {{ $errors->first($field) }}</a></li>
                        @endif
                    @endforeach
                </ul>
            @endif
            <ul id="clientErrorList" hidden></ul>
        </div>

        <form action="{{ route('pendaftaran.simpanAkademik') }}"
              method="POST" enctype="multipart/form-data" id="formAkademik" novalidate>
            @csrf

            <!-- Dropdown tahun angkatan: tahun sekarang sampai empat tahun sebelumnya. -->
            @php
                $tahunSekarang = now()->year;
                $tahunTerpilih = (string) old('tahun_angkatan', $data['tahun_angkatan'] ?? '');
            @endphp
            <div class="form-group">
                <label for="tahun_angkatan" class="form-label">Tahun Angkatan*</label>
                <select id="tahun_angkatan" name="tahun_angkatan"
                        class="form-input @error('tahun_angkatan') is-invalid @enderror"
                        required aria-invalid="{{ $errors->has('tahun_angkatan') ? 'true' : 'false' }}">
                    <option value="" disabled {{ $tahunTerpilih === '' ? 'selected' : '' }}>Pilih tahun angkatan</option>
                    @for ($tahun = $tahunSekarang; $tahun >= $tahunSekarang - 4; $tahun--)
                        <option value="{{ $tahun }}" {{ $tahunTerpilih === (string) $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endfor
                </select>
                @error('tahun_angkatan')
                    <span class="form-error" role="alert">{{ $message }}</span>
                @enderror
                <span class="form-error" id="tahun_angkatanClientError" role="alert" hidden></span>
            </div>

            <!-- Fakultas. -->
            <div class="form-group">
                <label for="fakultas" class="form-label">Fakultas*</label>
                <input type="text" id="fakultas" name="fakultas"
                       class="form-input @error('fakultas') is-invalid @enderror"
                       placeholder="Masukkan nama fakultas"
                       value="{{ old('fakultas', $data['fakultas'] ?? '') }}" maxlength="255" required>
                @error('fakultas')
                    <span class="form-error" role="alert">{{ $message }}</span>
                @enderror
                <span class="form-error" id="fakultasClientError" role="alert" hidden></span>
            </div>

            <!-- Program studi. -->
            <div class="form-group">
                <label for="program_studi" class="form-label">Program Studi*</label>
                <input type="text" id="program_studi" name="program_studi"
                       class="form-input @error('program_studi') is-invalid @enderror"
                       placeholder="Masukkan nama program studi"
                       value="{{ old('program_studi', $data['program_studi'] ?? '') }}" maxlength="255" required>
                @error('program_studi')
                    <span class="form-error" role="alert">{{ $message }}</span>
                @enderror
                <span class="form-error" id="program_studiClientError" role="alert" hidden></span>
            </div>

            <!-- Bukti penerimaan wajib saat pertama mengunggah; boleh tetap gunakan file tersimpan di session. -->
            <div class="form-group">
                <label for="bukti_penerimaan" class="form-label">Bukti Penerimaan UIN*</label>
                <div class="upload-dropzone @error('bukti_penerimaan') is-invalid @enderror" id="uploadZone">
                    <input type="file" id="bukti_penerimaan" name="bukti_penerimaan"
                           class="file-hidden-input" accept=".pdf,.jpg,.jpeg,.png"
                           @if (empty($data['bukti_penerimaan'])) required @endif
                           onchange="handleFileSelected(this, 'fileNameDisplay')">
                    <div class="upload-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                            <path d="M12 12v9"></path>
                            <path d="m8 16 4-4 4 4"></path>
                        </svg>
                    </div>
                    <span class="upload-text" id="fileNameDisplay">
                        {{ !empty($data['bukti_penerimaan']) ? ($data['bukti_penerimaan_nama'] ?? 'Berkas sebelumnya tersimpan') : 'Pilih berkas' }}
                    </span>
                    <span class="upload-sub">PDF, JPG, atau PNG — maksimal 5 MB</span>
                </div>
                @if (!empty($data['bukti_penerimaan']))
                    <p class="uploaded-note">Berkas sudah tersimpan sementara. Biarkan kosong jika tidak ingin menggantinya.</p>
                @endif
                @error('bukti_penerimaan')
                    <span class="form-error" role="alert">{{ $message }}</span>
                @enderror
                <span class="form-error" id="bukti_penerimaanClientError" role="alert" hidden></span>
            </div>

            <p class="form-required-note">* Kolom wajib diisi.</p>
            <div class="form-footer">
                <a href="{{ route('pendaftaran.pribadi') }}" class="btn-maroon-pill">Kembali</a>
                <button type="submit" class="btn-maroon-pill">Lanjut</button>
            </div>
        </form>
    </div>

    <script>
        // Validasi yang menjelaskan kolom, penyebab, dan cara memperbaikinya.
        const formAkademik = document.getElementById('formAkademik');
        const summary = document.getElementById('validationSummary');
        const clientErrorList = document.getElementById('clientErrorList');
        const fieldLabels = {
            tahun_angkatan: 'Tahun Angkatan',
            fakultas: 'Fakultas',
            program_studi: 'Program Studi',
            bukti_penerimaan: 'Bukti Penerimaan UIN'
        };
        const fileInput = document.getElementById('bukti_penerimaan');
        const uploadText = document.getElementById('fileNameDisplay');
        const permittedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        const maxFileBytes = 5 * 1024 * 1024;

        function setFieldError(name, message) {
            const field = document.getElementById(name);
            const feedback = document.getElementById(name + 'ClientError');
            feedback.textContent = message;
            feedback.hidden = !message;
            field.classList.toggle('is-invalid', Boolean(message));
            field.setAttribute('aria-invalid', message ? 'true' : 'false');
            if (name === 'bukti_penerimaan') {
                document.getElementById('uploadZone').classList.toggle('is-invalid', Boolean(message));
            }
            refreshErrorSummary();
            return !message;
        }

        function refreshErrorSummary() {
            clientErrorList.replaceChildren();
            for (const [name, label] of Object.entries(fieldLabels)) {
                const feedback = document.getElementById(name + 'ClientError');
                if (feedback.hidden) continue;
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = '#' + name;
                const strong = document.createElement('strong');
                strong.textContent = label + ': ';
                a.appendChild(strong);
                a.appendChild(document.createTextNode(feedback.textContent));
                li.appendChild(a);
                clientErrorList.appendChild(li);
            }
            clientErrorList.hidden = clientErrorList.children.length === 0;
            // Kesalahan dari Laravel tetap ditampilkan sampai server menerima data.
            const hasServerErrors = Boolean(document.getElementById('serverErrorList'));
            summary.hidden = clientErrorList.hidden && !hasServerErrors;
        }

        function validateField(name) {
            const field = document.getElementById(name);
            const value = field.value.trim();
            let message = '';
            if (name === 'tahun_angkatan') {
                const pilihan = Array.from(field.options)
                    .filter(option => !option.disabled)
                    .map(option => option.value);
                if (!value) message = 'Silakan pilih tahun angkatan.';
                else if (!pilihan.includes(value)) message = 'Tahun harus dipilih dari 5 angkatan terakhir.';
            } else if (name === 'fakultas' || name === 'program_studi') {
                if (!value) message = fieldLabels[name] + ' wajib diisi.';
                else if (value.length > 255) message = fieldLabels[name] + ' maksimal 255 karakter.';
            } else if (name === 'bukti_penerimaan') {
                const file = field.files && field.files[0];
                if (!file) {
                    if (field.required) message = 'Bukti Penerimaan UIN wajib diunggah. Pilih PDF, JPG, JPEG, atau PNG (maksimal 5 MB).';
                } else {
                    const extension = file.name.split('.').pop().toLowerCase();
                    if (!permittedExtensions.includes(extension)) {
                        message = 'Format .' + extension + ' tidak diperbolehkan. Gunakan PDF, JPG, JPEG, atau PNG.';
                    } else if (file.size > maxFileBytes) {
                        message = 'Ukuran berkas ' + (file.size / 1024 / 1024).toFixed(2) + ' MB melebihi batas 5 MB. Pilih berkas yang lebih kecil.';
                    }
                }
            }
            return setFieldError(name, message);
        }

        // Nama berkas ditampilkan saat dipilih, dan format/ukuran dicek langsung.
        function handleFileSelected(input, displayId) {
            const file = input.files && input.files[0];
            const text = document.getElementById(displayId);
            text.textContent = file ? file.name : 'Pilih berkas';
            if (!validateField('bukti_penerimaan')) {
                // Jika salah format/ukuran, kosongkan agar tidak terkirim.
                input.value = '';
                text.textContent = 'Pilih berkas lain';
                summary.hidden = false;
                summary.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        for (const name of ['tahun_angkatan', 'fakultas', 'program_studi']) {
            const field = document.getElementById(name);
            field.addEventListener('change', () => validateField(name));
            field.addEventListener('input', () => {
                if (!document.getElementById(name + 'ClientError').hidden) validateField(name);
            });
        }

        // Jangan biarkan browser menggagalkan submit tanpa penjelasan.
        formAkademik.addEventListener('submit', function (event) {
            let semuaValid = true;
            for (const name of Object.keys(fieldLabels)) {
                if (!validateField(name)) semuaValid = false;
            }
            if (!semuaValid) {
                event.preventDefault();
                summary.focus({ preventScroll: true });
                summary.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            // Jika lolos pemeriksaan browser, biarkan Laravel memvalidasi ulang.
        });

        // Saat Laravel mengembalikan validasi gagal, tampilkan ringkasan kesalahan.
        document.addEventListener('DOMContentLoaded', function () {
            if (!summary.hidden) {
                summary.focus({ preventScroll: true });
                summary.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    </script>
</body>
</html>
