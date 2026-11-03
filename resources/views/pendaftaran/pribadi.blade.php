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
            justify-content: space-between;
            margin-bottom: 1.75rem;
            padding: 0 0.5rem;
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
            margin: -1.2rem 1rem 0 1rem;
        }

        /* Right Pill Badges */
        .category-pills {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            margin-left: 1.5rem;
        }

        .btn-pill-filter {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.3rem 0.85rem;
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
            background-color: var(--maroon-tint);
            color: var(--primary-maroon);
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

        /* Footer */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: 0.5rem;
        }

        .col-required-notice {
            font-size: 0.74rem;
            color: var(--text-muted);
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
                <!-- Step 1 (Active) -->
                <div class="step-item active">
                    <div class="step-circle">1</div>
                    <span class="step-label">Data Diri</span>
                </div>

                <div class="step-line"></div>

                <!-- Step 2 -->
                <div class="step-item inactive">
                    <div class="step-circle">2</div>
                    <span class="step-label">Data Akademik</span>
                </div>

                <div class="step-line"></div>

                <!-- Step 3 -->
                <div class="step-item inactive">
                    <div class="step-circle">3</div>
                    <span class="step-label">Persyaratan</span>
                </div>
            </div>

            <!-- Category Pills at Right -->
            <div class="category-pills">
                <button type="button" class="btn-pill-filter active" id="btnUmum" onclick="setKategori('umum')">Mahasiswa Umum</button>
                <button type="button" class="btn-pill-filter inactive" id="btnKip" onclick="setKategori('kip')">Mahasiswa KIP</button>
            </div>
        </div>

        <!-- Step Section Header -->
        <div class="step-section-header">
            <div class="step-badge-num">1</div>
            <div class="step-badge-text">
                <span class="step-badge-title">Data Diri</span>
                <span class="step-badge-sub">Lengkapi identitas dan informasi kontak kamu.</span>
            </div>
        </div>

        <!-- Form Form Pendaftaran Pribadi -->
        <form action="{{ route('pendaftaran.simpanPribadi') }}" method="POST" id="formPribadi">
            @csrf
            <input type="hidden" name="kategori" id="inputKategori" value="{{ old('kategori', $data['kategori'] ?? 'umum') }}">

            <!-- Nama Lengkap -->
            <div class="form-group">
                <label for="nama_lengkap" class="form-label">Nama Lengkap*</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-input" placeholder="Masukkan nama lengkap sesuai identitas" value="{{ old('nama_lengkap', $data['nama_lengkap'] ?? Auth::user()->name ?? '') }}" required>
            </div>

            <!-- Jenis Kelamin -->
            <div class="form-group">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin*</label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="form-input" required>
                    <option value="" disabled {{ old('jenis_kelamin', $data['jenis_kelamin'] ?? '') === '' ? 'selected' : '' }}>Pilih jenis kelamin</option>
                    <option value="Laki-laki" {{ old('jenis_kelamin', $data['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('jenis_kelamin', $data['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                    <small style="display:block; margin-top:0.35rem; color:#b91c1c;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Email Aktif -->
            <div class="form-group">
                <label for="email" class="form-label">Email Aktif*</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" value="{{ old('email', $data['email'] ?? Auth::user()->email ?? '') }}" required>
            </div>

            <!-- Nomor WhatsApp Mahasiswa -->
            <div class="form-group">
                <label for="no_wa" class="form-label">Nomor WhatsApp Mahasiswa*</label>
                <input type="tel" id="no_wa" name="no_wa" class="form-input" placeholder="08xxxxxxxxxx" value="{{ old('no_wa', $data['no_wa'] ?? '') }}" required>
            </div>

            <!-- Asal Sekolah -->
            <div class="form-group">
                <label for="asal_sekolah" class="form-label">Asal Sekolah*</label>
                <input type="text" id="asal_sekolah" name="asal_sekolah" class="form-input" placeholder="Nama MA/SMA/SMK atau sekolah asal" value="{{ old('asal_sekolah', $data['asal_sekolah'] ?? '') }}" required>
            </div>

            <!-- Nama Orang Tua -->
            <div class="form-group">
                <label for="nama_ortu" class="form-label">Nama Orang Tua*</label>
                <input type="text" id="nama_ortu" name="nama_ortu" class="form-input" placeholder="Masukkan nama salah satu orang tua" value="{{ old('nama_ortu', $data['nama_ortu'] ?? '') }}" required>
            </div>

            <!-- Nomor Handphone Orang Tua -->
            <div class="form-group">
                <label for="no_hp_ortu" class="form-label">Nomor Handphone Orang Tua*</label>
                <input type="tel" id="no_hp_ortu" name="no_hp_ortu" class="form-input" placeholder="08xxxxxxxxxx" value="{{ old('no_hp_ortu', $data['no_hp_ortu'] ?? '') }}" required>
            </div>

            <!-- Footer Buttons -->
            <div class="form-footer">
                <a href="{{ route('dashboard') }}" class="btn-maroon-pill">Kembali</a>
                <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
                    <span class="col-required-notice">* Kolom wajib diisi.</span>
                    <button type="submit" class="btn-maroon-pill">Lanjut</button>
                </div>
            </div>
        </form>
    </div>

    <script>
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
    </script>
</body>
</html>