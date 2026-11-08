<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pendaftaran Mahasiswa Baru - Data Akademik</title>

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
            font-family: 'Plus Jakarta Sans', sans-serif;
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

        .page-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(230,240,255,.45) 0%,
                rgba(255,255,255,.65) 50%,
                rgba(255,255,255,.85) 100%
            );
            backdrop-filter: blur(3px);
            z-index: 1;
        }

        .form-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 800px;
            background: #fff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 16px;
            padding: 2.25rem 2.75rem;
            box-shadow: 0 12px 36px rgba(110,0,27,.08);
        }

        .card-title {
            color: var(--primary-maroon);
            font-size: 1.45rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: .35rem;
        }

        .card-subtitle {
            color: #4b5563;
            font-size: .84rem;
            text-align: center;
            margin-bottom: 1.75rem;
            line-height: 1.5;
        }

        /* Stepper */

        .stepper-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
            padding: 0 .5rem;
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
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            font-weight: 700;
        }

        .step-item.active .step-circle {
            background: var(--primary-maroon);
            color: white;
            box-shadow: 0 2px 8px rgba(110,0,27,.25);
        }

        .step-item.inactive .step-circle {
            background: white;
            border: 1.5px solid #94a3b8;
            color: #64748b;
        }

        .step-label {
            font-size: .78rem;
            margin-top: .4rem;
            white-space: nowrap;
        }

        .step-item.active .step-label {
            font-weight: 700;
            color: var(--text-dark);
        }

        .step-item.inactive .step-label {
            color: #64748b;
        }

        .step-line {
            flex-grow: 1;
            height: 1.5px;
            background: #cbd5e1;
            margin: -1.2rem 1rem 0;
        }

        /* Kategori */

        .category-pills {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            margin-left: 1.5rem;
        }

        .btn-pill-filter {
            font-family: inherit;
            font-size: .72rem;
            font-weight: 700;
            padding: .3rem .85rem;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
        }

        .btn-pill-filter.active {
            background: var(--primary-maroon);
            color: white;
        }

        .btn-pill-filter.inactive {
            background: var(--maroon-tint);
            color: var(--primary-maroon);
        }

        /* Judul bagian */

        .step-section-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.35rem;
        }

        .step-badge-num {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--primary-maroon);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-badge-text {
            display: flex;
            flex-direction: column;
        }

        .step-badge-title {
            font-size: .96rem;
            font-weight: 700;
            color: var(--primary-maroon);
        }

        .step-badge-sub {
            font-size: .76rem;
            color: var(--text-muted);
        }

        /* Form */

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: .4rem;
        }

        .form-input {
            width: 100%;
            height: 42px;
            border: 1.2px solid var(--maroon-border);
            border-radius: 12px;
            padding: 0 .95rem;
            font-family: inherit;
            font-size: .85rem;
            color: #1f2937;
            background: white;
            outline: none;
            transition: .2s;
        }

        .form-input::placeholder {
            color: #9ca3af;
            font-size: .82rem;
        }

        .form-input:focus {
            border-color: var(--primary-maroon-hover);
            box-shadow: 0 0 0 3px rgba(110,0,27,.1);
        }

        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236e001b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 14px;
            cursor: pointer;
        }

        /* Upload */

        .upload-dropzone {
            position: relative;
            width: 100%;
            min-height: 50px;
            border: 1.2px solid var(--maroon-border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .65rem 1rem;
            background: white;
            cursor: pointer;
            transition: .2s;
        }

        .upload-dropzone:hover {
            background: #fff8f9;
        }

        .upload-icon {
            color: var(--primary-maroon);
            flex-shrink: 0;
        }

        .upload-text {
            font-size: .85rem;
            color: #9ca3af;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-hidden-input {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .upload-sub {
            display: block;
            font-size: .72rem;
            color: var(--text-muted);
            margin-top: .4rem;
        }

        .uploaded-note {
            font-size: .75rem;
            color: #526273;
            margin-top: .4rem;
            line-height: 1.5;
        }

        /* Validasi */

        .form-error {
            display: block;
            color: #b42318;
            font-size: .76rem;
            margin-top: .4rem;
        }

        .form-input.is-invalid,
        .upload-dropzone.is-invalid {
            border-color: #b42318;
        }

        .validation-summary {
            background: #fff1f2;
            border: 1px solid #fda4af;
            border-left: 4px solid #be123c;
            border-radius: 12px;
            color: #881337;
            padding: .9rem 1rem;
            margin-bottom: 1.25rem;
            font-size: .82rem;
            line-height: 1.6;
        }

        .validation-summary ul {
            margin: .35rem 0 0 1.2rem;
        }

        .validation-summary a {
            color: inherit;
        }

        [hidden] {
            display: none !important;
        }

        .form-required-note {
            color: var(--text-muted);
            font-size: .75rem;
            margin-top: .65rem;
        }

        /* Footer */

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: .5rem;
        }

        .btn-maroon-pill {
            background: var(--primary-maroon);
            color: white;
            border: none;
            border-radius: 9999px;
            height: 38px;
            padding: 0 1.85rem;
            font-family: inherit;
            font-size: .85rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .2s;
        }

        .btn-maroon-pill:hover {
            background: var(--primary-maroon-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(110,0,27,.2);
        }

        @media (max-width: 640px) {
            .form-card {
                padding: 1.5rem 1.25rem;
            }

            .stepper-container {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .stepper-steps {
                width: 100%;
            }

            .category-pills {
                margin-left: 0;
                flex-direction: row;
            }

            .step-line {
                margin-left: .5rem;
                margin-right: .5rem;
            }
        }
    </style>
</head>

<body>

<div class="page-overlay"></div>

<div class="form-card">

    <h1 class="card-title">Pendaftaran Mahasiswa Baru</h1>

    <p class="card-subtitle">
        Lengkapi data dan persyaratan untuk mendaftar sebagai penghuni
        Ma'had Al-Jami'ah Tuanku Imam Bonjol.
    </p>

    <!-- Stepper -->
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

        <!-- Kategori -->
        <div class="category-pills">

            <button type="button"
                class="btn-pill-filter {{ old('kategori', $data['kategori'] ?? 'umum') === 'umum' ? 'active' : 'inactive' }}"
                id="btnUmum"
                onclick="setKategori('umum')">
                Mahasiswa Umum
            </button>

            <button type="button"
                class="btn-pill-filter {{ old('kategori', $data['kategori'] ?? 'umum') === 'kip' ? 'active' : 'inactive' }}"
                id="btnKip"
                onclick="setKategori('kip')">
                Mahasiswa KIP
            </button>

        </div>
    </div>

    <!-- Judul Bagian -->
    <div class="step-section-header">

        <div class="step-badge-num">2</div>

        <div class="step-badge-text">
            <span class="step-badge-title">Data Akademik</span>
            <span class="step-badge-sub">
                Masukkan informasi penerimaan dan pendidikan di UIN.
            </span>
        </div>

    </div>

    <!-- Ringkasan kesalahan -->
    @if ($errors->any())
        <div class="validation-summary" role="alert">
            <strong>Periksa kembali Data Akademik.</strong>
            <p>Masih terdapat data yang perlu diperbaiki:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Akademik -->
        <form action="{{ route('pendaftaran.simpanAkademik') }}" method="POST" enctype="multipart/form-data">
         @csrf
        <input type="hidden"
               name="kategori"
               id="inputKategori"
               value="{{ old('kategori', $data['kategori'] ?? 'umum') }}">

        @php
            $tahunSekarang = now()->year;
            $tahunTerpilih = (string) old(
                'tahun_angkatan',
                $data['tahun_angkatan'] ?? ''
            );
        @endphp

        <!-- Tahun Angkatan -->
        <div class="form-group">

            <label for="tahun_angkatan" class="form-label">
                Tahun Angkatan*
            </label>

            <select id="tahun_angkatan"
                    name="tahun_angkatan"
                    class="form-input @error('tahun_angkatan') is-invalid @enderror"
                    required>

                <option value="" disabled
                    {{ $tahunTerpilih === '' ? 'selected' : '' }}>
                    Pilih tahun angkatan
                </option>

                @for ($tahun = $tahunSekarang; $tahun >= $tahunSekarang - 4; $tahun--)
                    <option value="{{ $tahun }}"
                        {{ $tahunTerpilih === (string) $tahun ? 'selected' : '' }}>
                        {{ $tahun }}
                    </option>
                @endfor

            </select>

            @error('tahun_angkatan')
                <span class="form-error">{{ $message }}</span>
            @enderror

        </div>

        <!-- Fakultas -->
        <div class="form-group">

            <label for="fakultas" class="form-label">
                Fakultas*
            </label>

            <input type="text"
                   id="fakultas"
                   name="fakultas"
                   class="form-input @error('fakultas') is-invalid @enderror"
                   placeholder="Masukkan nama fakultas"
                   value="{{ old('fakultas', $data['fakultas'] ?? '') }}"
                   maxlength="255"
                   required>

            @error('fakultas')
                <span class="form-error">{{ $message }}</span>
            @enderror

        </div>

        <!-- Program Studi -->
        <div class="form-group">

            <label for="program_studi" class="form-label">
                Program Studi*
            </label>

            <input type="text"
                   id="program_studi"
                   name="program_studi"
                   class="form-input @error('program_studi') is-invalid @enderror"
                   placeholder="Masukkan nama program studi"
                   value="{{ old('program_studi', $data['program_studi'] ?? '') }}"
                   maxlength="255"
                   required>

            @error('program_studi')
                <span class="form-error">{{ $message }}</span>
            @enderror

        </div>

        <!-- Bukti Penerimaan -->
        <div class="form-group">

            <label for="bukti_penerimaan" class="form-label">
                Bukti Penerimaan UIN IB Padang*
            </label>

            <div class="upload-dropzone @error('bukti_penerimaan') is-invalid @enderror"
                 id="uploadZone">

                <input type="file"
                       id="bukti_penerimaan"
                       name="bukti_penerimaan"
                       class="file-hidden-input"
                       accept=".pdf,.jpg,.jpeg,.png"
                       {{ empty($data['bukti_penerimaan']) ? 'required' : '' }}>

                <svg class="upload-icon"
                     width="22"
                     height="22"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">

                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>

                </svg>

                <span class="upload-text" id="fileNameDisplay">
                    {{ !empty($data['bukti_penerimaan'])
                        ? ($data['bukti_penerimaan_nama'] ?? 'Berkas sebelumnya tersimpan')
                        : 'PDF, JPG, atau PNG' }}
                </span>

            </div>

            <span class="upload-sub">
                Format PDF, JPG, JPEG, atau PNG. Maksimal 5 MB.
            </span>

            @if (!empty($data['bukti_penerimaan']))
                <p class="uploaded-note">
                    Berkas sebelumnya sudah tersimpan.
                    Kosongkan jika tidak ingin menggantinya.
                </p>
            @endif

            @error('bukti_penerimaan')
                <span class="form-error">{{ $message }}</span>
            @enderror

        </div>

        <p class="form-required-note">
            * Kolom wajib diisi.
        </p>

        <!-- Tombol -->
        <div class="form-footer">

            <a href="{{ route('pendaftaran.pribadi') }}"
               class="btn-maroon-pill">
                Kembali
            </a>

            <button type="submit" class="btn-maroon-pill">
                Lanjut
            </button>

        </div>

    </form>

</div>

<script>
    // Mengubah kategori mahasiswa
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

    // Menampilkan nama file yang dipilih
    const fileInput = document.getElementById('bukti_penerimaan');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const uploadZone = document.getElementById('uploadZone');

    fileInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {
            return;
        }

        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        const extension = file.name.split('.').pop().toLowerCase();
        const maxSize = 5 * 1024 * 1024;

        if (!allowedExtensions.includes(extension)) {
            alert('Format file tidak diperbolehkan. Gunakan PDF, JPG, JPEG, atau PNG.');
            this.value = '';
            return;
        }

        if (file.size > maxSize) {
            alert('Ukuran file maksimal adalah 5 MB.');
            this.value = '';
            return;
        }

        fileNameDisplay.textContent = file.name;
        fileNameDisplay.style.color = '#6e001b';
    });
</script>

</body>
</html>
