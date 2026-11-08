
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Pendaftaran Mahasiswa Baru - Data Diri</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ==============================
           RESET DAN VARIABEL
        ============================== */

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

        /* ==============================
           BODY DAN BACKGROUND
        ============================== */

        body {
            font-family: 'Plus Jakarta Sans', -apple-system,
                BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;

            padding: 1rem;
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

            background: linear-gradient(
                135deg,
                rgba(230, 240, 255, 0.45) 0%,
                rgba(255, 255, 255, 0.65) 50%,
                rgba(255, 255, 255, 0.85) 100%
            );

            backdrop-filter: blur(3px);
            z-index: 1;
        }

        /* ==============================
           CARD UTAMA
        ============================== */

        .form-card {
            position: relative;
            z-index: 2;

            width: 100%;
            max-width: 900px;

            background-color: #ffffff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 12px;

            padding: 1.75rem 5.5rem;

            box-shadow: 0 12px 36px rgba(110, 0, 27, 0.08);
        }

        /* ==============================
           HEADER
        ============================== */

        .card-title {
            color: var(--primary-maroon);
            font-size: 1.3rem;
            font-weight: 800;

            text-align: center;
            margin-bottom: 0.3rem;
            letter-spacing: -0.01em;
        }

        .card-subtitle {
            color: #4b5563;
            font-size: 0.78rem;
            text-align: center;

            margin-bottom: 1.8rem;
            line-height: 1.5;
        }

        /* ==============================
           STEPPER
        ============================== */

        .stepper-container {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 1.5rem;
            position: relative;
        }

        .stepper-steps {
            display: flex;
            align-items: center;
            flex-grow: 1;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .step-circle {
            width: 43px;
            height: 43px;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 1.05rem;
            font-weight: 700;

            transition: all 0.2s;
        }

        .step-item.active .step-circle {
            background-color: var(--primary-maroon);
            color: #ffffff;

            border: 1px solid var(--primary-maroon);
            box-shadow: 0 2px 8px rgba(110, 0, 27, 0.25);
        }

        .step-item.inactive .step-circle {
            background-color: #ffffff;
            border: 1px solid var(--maroon-border);
            color: #111827;
        }

        .step-label {
            font-size: 0.76rem;
            margin-top: 0.4rem;
            white-space: nowrap;
        }

        .step-item.active .step-label {
            font-weight: 700;
            color: var(--primary-maroon);
        }

        .step-item.inactive .step-label {
            font-weight: 500;
            color: #111827;
        }

        .step-line {
            flex-grow: 1;
            height: 1px;

            background-color: var(--maroon-border);

            margin: -1.2rem 1.8rem 0;
        }

        /* ==============================
           KATEGORI MAHASISWA
        ============================== */

        .category-pills {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;

            margin-left: 2rem;
        }

        .btn-pill-filter {
            font-family: inherit;

            font-size: 0.65rem;
            font-weight: 700;

            padding: 0.4rem 1rem;
            min-width: 122px;

            border-radius: 9999px;
            border: none;

            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .btn-pill-filter.active {
            background-color: var(--primary-maroon);
            color: #ffffff;
        }

        .btn-pill-filter.inactive {
            background-color: #f4e6e9;
            color: #111827;
        }

        .btn-pill-filter:hover {
            opacity: 0.9;
        }

        /* ==============================
           HEADER BAGIAN DATA DIRI
        ============================== */

        .step-section-header {
            display: flex;
            align-items: center;
            gap: 0.55rem;

            margin-bottom: 1rem;
        }

        .step-badge-num {
            width: 43px;
            height: 43px;
            flex-shrink: 0;

            border-radius: 50%;
            background-color: var(--primary-maroon);
            color: #ffffff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 1.1rem;
            font-weight: 700;
        }

        .step-badge-text {
            display: flex;
            flex-direction: column;
        }

        .step-badge-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--primary-maroon);
        }

        .step-badge-sub {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* ==============================
           FORM INPUT
        ============================== */

        .form-group {
            margin-bottom: 0.65rem;
            min-width: 0;
        }

        .form-label {
            display: block;

            font-size: 0.68rem;
            font-weight: 600;

            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .form-input {
            width: 100%;
            height: 33px;

            border: 1px solid var(--maroon-border);
            border-radius: 10px;

            padding: 0 1rem;

            font-family: inherit;
            font-size: 0.78rem;

            color: #1f2937;
            background-color: #ffffff;

            outline: none;

            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input::placeholder {
            color: #9ca3af;
            font-size: 0.75rem;
        }

        .form-input:focus {
            border-color: var(--primary-maroon-hover);
            box-shadow: 0 0 0 3px rgba(110, 0, 27, 0.1);
        }

        /* ==============================
           FORM DUA KOLOM
        ============================== */

        .form-grid-two {
            display: grid;

            grid-template-columns: 1.5fr 1fr;

            gap: 0.9rem;
            align-items: start;
        }

        /* ==============================
           UPLOAD DOKUMEN
        ============================== */

        .file-input-wrapper {
            position: relative;

            width: 100%;
            height: 33px;

            border: 1px solid var(--maroon-border);
            border-radius: 10px;

            display: flex;
            align-items: center;

            overflow: hidden;
            background-color: #ffffff;

            padding: 0 1rem;
        }

        .file-input-wrapper span {
            display: block;

            width: 100%;

            color: #9ca3af;
            font-size: 0.75rem;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            inset: 0;

            width: 100%;
            height: 100%;

            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .file-input-wrapper:hover {
            background-color: #fffafb;
        }

        .file-input-wrapper:focus-within {
            border-color: var(--primary-maroon-hover);
            box-shadow: 0 0 0 3px rgba(110, 0, 27, 0.1);
        }

        /* ==============================
           PESAN ERROR
        ============================== */

        .error-message {
            display: block;
            color: #b91c1c;
            font-size: 0.65rem;
            margin-top: 0.25rem;
        }

        /* ==============================
           FOOTER DAN TOMBOL
        ============================== */

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-top: 0.8rem;
            padding-top: 0;
        }

        .col-required-notice {
            font-size: 0.65rem;
            color: var(--text-muted);
        }

        .btn-maroon-pill {
            background-color: var(--primary-maroon);
            color: #ffffff;

            border: none;
            border-radius: 9999px;

            height: 33px;
            min-width: 122px;

            padding: 0 1.5rem;

            font-family: inherit;
            font-size: 0.75rem;
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

        /* ==============================
           RESPONSIVE TABLET DAN HP
        ============================== */

        @media (max-width: 768px) {

            .form-card {
                padding: 1.5rem 2rem;
            }

            .stepper-container {
                gap: 1rem;
            }

            .step-line {
                margin-left: 0.7rem;
                margin-right: 0.7rem;
            }

            .category-pills {
                margin-left: 1rem;
            }

        }

        @media (max-width: 640px) {

            body {
                padding: 1rem 0.6rem;
                background-size: cover;
                background-position: center;
            }

            .form-card {
                padding: 1.5rem 1.1rem;
            }

            .card-title {
                font-size: 1.1rem;
            }

            .card-subtitle {
                font-size: 0.7rem;
            }

            .stepper-container {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .stepper-steps {
                width: 100%;
                justify-content: space-between;
            }

            .step-line {
                margin-left: 0.4rem;
                margin-right: 0.4rem;
            }

            .step-circle {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
            }

            .step-label {
                font-size: 0.65rem;
            }

            .category-pills {
                margin-left: 0;
                flex-direction: row;
                justify-content: center;
            }

            .btn-pill-filter {
                flex: 1;
                min-width: 0;
            }

            .form-grid-two {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-label {
                font-size: 0.72rem;
            }

            .form-input,
            .file-input-wrapper {
                height: 38px;
            }

            .step-badge-num {
                width: 38px;
                height: 38px;
            }

            .form-footer {
                gap: 0.5rem;
            }

            .btn-maroon-pill {
                min-width: 100px;
            }

            .file-input-wrapper {
                position: relative;
                width: 100%;
                height: 33px;

                border: 1px solid var(--maroon-border);
                border-radius: 10px;

                display: flex;
                align-items: center;
                justify-content: space-between;

                padding: 0 12px;

                background: #ffffff;
                overflow: hidden;
                cursor: pointer;
            }

            .file-input-wrapper span {
                display: block;
                flex: 1;
                width: 0;

                border: none;
                background: transparent;
                padding: 0;
                margin: 0;

                color: #9ca3af;
                font-size: 0.75rem;

                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .file-input-wrapper svg {
                flex-shrink: 0;
                margin-left: 8px;
            }

            .file-input-wrapper input[type="file"] {
                position: absolute;
                inset: 0;

                width: 100%;
                height: 100%;

                opacity: 0;
                cursor: pointer;
                z-index: 2;
            }

            .file-input-wrapper:focus-within {
                border-color: var(--primary-maroon);
                box-shadow: 0 0 0 3px rgba(110, 0, 27, 0.1);
            }
           

        }

    </style>
</head>

<body>

    <div class="page-overlay"></div>

    <div class="form-card">

        <!-- JUDUL HALAMAN -->
        <h1 class="card-title">
            Pendaftaran Mahasiswa Baru
        </h1>

        <p class="card-subtitle">
            Lengkapi data dan persyaratan untuk mendaftar sebagai penghuni Ma'had Al-Jami'ah Tuanku Imam Bonjol.
        </p>


        <!-- STEPPER PENDAFTARAN -->
        <div class="stepper-container">

            <div class="stepper-steps">

                <!-- STEP 1: DATA DIRI -->
                <a href="{{ route('pendaftaran.pribadi') }}"
                   class="step-item {{ request()->routeIs('pendaftaran.pribadi', 'pendaftaran.data_diri') ? 'active' : 'inactive' }}"
                   style="text-decoration: none;">

                    <div class="step-circle">1</div>
                    <span class="step-label">Data Diri</span>
                </a>

                <div class="step-line"></div>

                <!-- STEP 2: DATA AKADEMIK -->
                <a href="{{ route('pendaftaran.akademik') }}"
                   class="step-item {{ request()->routeIs('pendaftaran.akademik') ? 'active' : 'inactive' }}"
                   style="text-decoration: none;">

                    <div class="step-circle">2</div>
                    <span class="step-label">Data Akademik</span>
                </a>

                <div class="step-line"></div>

                <!-- STEP 3: PERSYARATAN -->
                <a href="{{ route('pendaftaran.persyaratan') }}"
                   class="step-item {{ request()->routeIs('pendaftaran.persyaratan', 'pendaftaran.dokumen') ? 'active' : 'inactive' }}"
                   style="text-decoration: none;">

                    <div class="step-circle">3</div>
                    <span class="step-label">Persyaratan</span>
                </a>

            </div>


            <!-- KATEGORI MAHASISWA -->
            <div class="category-pills">

                <button
                    type="button"
                    class="btn-pill-filter {{ old('kategori', $data['kategori'] ?? 'umum') === 'umum' ? 'active' : 'inactive' }}"
                    id="btnUmum"
                    onclick="setKategori('umum')">

                    Mahasiswa Umum

                </button>

                <button
                    type="button"
                    class="btn-pill-filter {{ old('kategori', $data['kategori'] ?? 'umum') === 'kip' ? 'active' : 'inactive' }}"
                    id="btnKip"
                    onclick="setKategori('kip')">

                    Mahasiswa KIP

                </button>

            </div>

        </div>


        <!-- HEADER DATA DIRI -->
        <div class="step-section-header">

            <div class="step-badge-num">1</div>

            <div class="step-badge-text">

                <span class="step-badge-title">
                    Data Diri
                </span>

                <span class="step-badge-sub">
                    Lengkapi identitas dan informasi kontak kamu.
                </span>

            </div>

        </div>


        <!-- FORM DATA DIRI -->
        <form action="{{ route('pendaftaran.simpanPribadi') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <!-- KATEGORI -->
            <input
                type="hidden"
                name="kategori"
                id="inputKategori"
                value="{{ old('kategori', $data['kategori'] ?? 'umum') }}">


            <!-- NAMA LENGKAP -->
            <div class="form-group">

                <label for="nama_lengkap" class="form-label">
                    Nama Lengkap*
                </label>

                <input
                    type="text"
                    id="nama_lengkap"
                    name="nama_lengkap"
                    class="form-input"
                    placeholder="Masukkan nama lengkap sesuai identitas"
                    value="{{ old('nama_lengkap', $data['nama_lengkap'] ?? Auth::user()->name ?? '') }}"
                    required>

                @error('nama_lengkap')
                    <small class="error-message">{{ $message }}</small>
                @enderror

            </div>


            <!-- JENIS KELAMIN -->
            <div class="form-group">

                <label for="jenis_kelamin" class="form-label">
                    Jenis Kelamin*
                </label>

                <select id="jenis_kelamin"
                        name="jenis_kelamin"
                        class="form-input"
                        required>
                    <option value="" disabled {{ old('jenis_kelamin', $data['jenis_kelamin'] ?? '') === '' ? 'selected' : '' }}>
                        Pilih jenis kelamin
                    </option>
                    <option value="Laki-laki" {{ old('jenis_kelamin', $data['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' }}>
                        Laki-laki
                    </option>
                    <option value="Perempuan" {{ old('jenis_kelamin', $data['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' }}>
                        Perempuan
                    </option>
                </select>

                @error('jenis_kelamin')
                    <small class="error-message">{{ $message }}</small>
                @enderror

            </div>


            <!-- EMAIL AKTIF -->
            <div class="form-group">

                <label for="email" class="form-label">
                    Email Aktif*
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="nama@email.com"
                    value="{{ old('email', $data['email'] ?? $data['email_aktif'] ?? Auth::user()->email ?? '') }}"
                    required>

                @error('email')
                    <small class="error-message">{{ $message }}</small>
                @enderror
                @error('email_aktif')
                    <small class="error-message">{{ $message }}</small>
                @enderror

            </div>


            <!-- NOMOR WHATSAPP -->
            <div class="form-group">

                <label for="no_wa" class="form-label">
                    Nomor WhatsApp Mahasiswa*
                </label>

                <input
                    type="tel"
                    id="no_wa"
                    name="no_wa"
                    class="form-input"
                    placeholder="08xxxxxxxxxx"
                    value="{{ old('no_wa', $data['no_wa'] ?? '') }}"
                    required>

                @error('no_wa')
                    <small class="error-message">{{ $message }}</small>
                @enderror

            </div>


            <!-- ASAL SEKOLAH DAN IJAZAH -->
            <div class="form-grid-two">

                <!-- ASAL SEKOLAH -->
                <div class="form-group">

                    <label for="asal_sekolah" class="form-label">
                        Asal Sekolah*
                    </label>

                    <input
                        type="text"
                        id="asal_sekolah"
                        name="asal_sekolah"
                        class="form-input"
                        placeholder="Nama MA/SMA/SMK atau sekolah asal"
                        value="{{ old('asal_sekolah', $data['asal_sekolah'] ?? '') }}"
                        required>

                    @error('asal_sekolah')
                        <small class="error-message">{{ $message }}</small>
                    @enderror

                </div>


                <!-- IJAZAH -->
                <div class="form-group">

                    <label for="ijazah_sma" class="form-label">
                        Unggah Ijazah*
                    </label>

                    <label class="file-input-wrapper" for="ijazah_sma">

                        <span id="namaIjazah">
                            {{ !empty($data['ijazah_sma']) ? 'Berkas ijazah tersimpan' : 'PDF, JPG, atau PNG' }}
                        </span>

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="20"
                             height="20"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="#6e001b"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round">

                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>

                        </svg>

                        <input
                            type="file"
                            id="ijazah_sma"
                            name="ijazah_sma"
                            accept=".pdf,.jpg,.jpeg,.png"
                            {{ empty($data['ijazah_sma']) ? 'required' : '' }}
                            onchange="tampilkanNamaFile(this, 'namaIjazah')">

                    </label>

                    @error('ijazah_sma')
                        <small class="error-message">{{ $message }}</small>
                    @enderror

                </div>

            </div>


            <!-- NAMA ORANG TUA DAN NOMOR HP -->
            <div class="form-grid-two">

                <!-- NAMA ORANG TUA -->
                <div class="form-group">

                    <label for="nama_ortu" class="form-label">
                        Nama Orang Tua*
                    </label>

                    <input
                        type="text"
                        id="nama_ortu"
                        name="nama_ortu"
                        class="form-input"
                        placeholder="Masukkan nama salah satu orang tua"
                        value="{{ old('nama_ortu', $data['nama_ortu'] ?? '') }}"
                        required>

                    @error('nama_ortu')
                        <small class="error-message">{{ $message }}</small>
                    @enderror

                </div>


                <!-- NOMOR HP ORANG TUA -->
                <div class="form-group">

                    <label for="no_hp_ortu" class="form-label">
                        Nomor Handphone Orang Tua*
                    </label>

                    <input
                        type="tel"
                        id="no_hp_ortu"
                        name="no_hp_ortu"
                        class="form-input"
                        placeholder="08xxxxxxxxxx"
                        value="{{ old('no_hp_ortu', $data['no_hp_ortu'] ?? '') }}"
                        required>

                    @error('no_hp_ortu')
                        <small class="error-message">{{ $message }}</small>
                    @enderror

                </div>

            </div>


            <!-- UPLOAD KTP DAN KK -->
            <div class="form-grid-two">

                <!-- KTP -->
                <div class="form-group">

                    <label for="ktp" class="form-label">
                        Unggah KTP*
                    </label>

                    <label class="file-input-wrapper" for="ktp">

                        <span id="namaktp">
                            {{ !empty($data['ktp']) ? 'Berkas KTP tersimpan' : 'PDF, JPG, atau PNG' }}
                        </span>

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="20"
                             height="20"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="#6e001b"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round">

                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>

                        </svg>

                        <input
                            type="file"
                            id="ktp"
                            name="ktp"
                            accept=".pdf,.jpg,.jpeg,.png"
                            {{ empty($data['ktp']) ? 'required' : '' }}
                            onchange="tampilkanNamaFile(this, 'namaktp')">

                    </label>

                    @error('ktp')
                        <small class="error-message">{{ $message }}</small>
                    @enderror

                </div>


                <!-- KK -->
                <div class="form-group">

                    <label for="kk" class="form-label">
                        Unggah KK*
                    </label>

                    <label class="file-input-wrapper" for="kk">

                        <span id="namaKk">
                            {{ !empty($data['kk']) ? 'Berkas KK tersimpan' : 'PDF, JPG, atau PNG' }}
                        </span>

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="20"
                             height="20"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="#6e001b"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round">

                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>

                        </svg>

                        <input
                            type="file"
                            id="kk"
                            name="kk"
                            accept=".pdf,.jpg,.jpeg,.png"
                            {{ empty($data['kk']) ? 'required' : '' }}
                            onchange="tampilkanNamaFile(this, 'namaKk')">

                    </label>

                    @error('kk')
                        <small class="error-message">{{ $message }}</small>
                    @enderror

                </div>

            </div>


            <!-- FOOTER FORM -->
            <div class="form-footer">

                <a href="{{ route('dashboard') }}" class="btn-maroon-pill" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                    Kembali
                </a>

                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <span class="col-required-notice">
                        * Kolom wajib diisi.
                    </span>

                    <button type="submit" class="btn-maroon-pill">
                        Lanjut
                    </button>
                </div>

            </div>

        </form>

    </div>


    <!-- JAVASCRIPT -->
    <script>

        // PILIH KATEGORI MAHASISWA
        function setKategori(val) {

            document.getElementById('inputKategori').value = val;

            const btnUmum = document.getElementById('btnUmum');
            const btnKip = document.getElementById('btnKip');

            if (val === 'umum') {

                btnUmum.className = 'btn-pill-filter active';
                btnKip.className = 'btn-pill-filter inactive';

            } else {

                btnUmum.className = 'btn-pill-filter inactive';
                btnKip.className = 'btn-pill-filter active';

            }

        }


        // MENAMPILKAN NAMA FILE YANG DIPILIH
        function tampilkanNamaFile(input, targetId) {

            const target = document.getElementById(targetId);

            if (input.files && input.files.length > 0) {

                target.textContent = input.files[0].name;
                target.style.color = '#6e001b';

            } else {

                target.textContent = 'Pilih dokumen';
                target.style.color = '#9ca3af';

            }

        }

    </script>

</body>
</html>