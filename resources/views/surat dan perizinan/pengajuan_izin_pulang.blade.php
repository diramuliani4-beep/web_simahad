
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Izin Pulang - SIMAHAD</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
    margin: 0;
    padding: 18px;
    font-family: 'Poppins', sans-serif;
    background-image: url('{{ asset("images/mahad_al_jamiah.jpeg") }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    color: #111;
}

        .form-container {
            width: 100%;
            min-height: calc(100vh - 36px);
            background: #fff;
            border: 1px solid #800d20;
            border-radius: 10px;
            padding: 38px 11% 20px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 27px;
        }

        .form-header h1 {
            margin: 0 0 3px;
            color: #800d20;
            font-size: 32px;
            font-weight: 600;
        }

        .form-header p {
            margin: 0;
            font-size: 19px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 9px;
            font-size: 15px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 21px;
            border: 1px solid #990d22;
            border-radius: 15px;
            font-family: inherit;
            font-size: 17px;
            outline: none;
            background: #fff;
        }

        .form-group input {
            height: 52px;
        }

        .form-group textarea {
            min-height: 85px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #70091a;
            box-shadow: 0 0 0 2px rgba(128, 13, 32, 0.1);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #888;
        }

        .required-note {
            margin-top: -15px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .alert {
            margin: 0 auto;
            max-width: 620px;
            padding: 12px 16px;
            border: 1px solid #800d20;
            border-radius: 10px;
            background: #fff0f1;
            font-size: 12px;
        }

        .alert strong {
            display: block;
            margin-bottom: 4px;
            color: #800d20;
        }

        .error-message {
            margin-top: 5px;
            color: #b00020;
            font-size: 12px;
        }

        .success-message {
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 8px;
            background: #e9f7ec;
            color: #17632b;
        }

        .form-footer {
            display: grid;
            grid-template-columns: 1fr 2.5fr 1fr;
            align-items: end;
            gap: 25px;
            margin-top: 20px;
        }

        .btn {
            min-height: 43px;
            padding: 9px 18px;
            border-radius: 15px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .btn-cancel {
            color: #800d20;
            border: 1px solid #800d20;
            background: #fff;
        }

        .btn-submit {
            color: #fff;
            border: 1px solid #800d20;
            background: #800d20;
        }

        .btn:hover {
            opacity: 0.88;
        }

        @media (max-width: 768px) {
            body {
                padding: 8px;
            }

            .form-container {
                padding: 28px 6% 20px;
            }

            .form-header h1 {
                font-size: 25px;
            }

            .form-header p {
                font-size: 14px;
            }

            .form-group input,
            .form-group textarea {
                font-size: 14px;
            }

            .form-footer {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .alert {
                grid-row: 1;
            }
        }
    </style>
</head>
<body>

<div class="form-container">

    <div class="form-header">
        <h1>Pengajuan Izin Pulang</h1>
        <p>Lengkapi formulir berikut untuk mengajukan izin pulang dari Ma'had.</p>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="success-message" style="background:#fff0f1;color:#800d20">
            Mohon periksa kembali data yang kamu masukkan.
        </div>
    @endif

    <form action="{{ route('surat.perizinan.mhs.pengajuan.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nama_mahasiswa">Nama Mahasiswa*</label>
            <input type="text" id="nama_mahasiswa" name="nama_mahasiswa"
                   value="{{ old('nama_mahasiswa') }}"
                   placeholder="Masukkan nama lengkap sesuai identitas" required>
            @error('nama_mahasiswa')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="nomor_whatsapp">Nomor WhatsApp Mahasiswa*</label>
            <input type="tel" id="nomor_whatsapp" name="nomor_whatsapp"
                   value="{{ old('nomor_whatsapp') }}"
                   placeholder="08xxxxxxxxxx" required>
            @error('nomor_whatsapp')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="tujuan_pulang">Tujuan Pulang*</label>
            <input type="text" id="tujuan_pulang" name="tujuan_pulang"
                   value="{{ old('tujuan_pulang') }}"
                   placeholder="Kota atau alamat tujuan pulang" required>
            @error('tujuan_pulang')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal_berangkat">Tanggal Berangkat*</label>
            <input type="date" id="tanggal_berangkat" name="tanggal_berangkat"
                   value="{{ old('tanggal_berangkat') }}" required>
            @error('tanggal_berangkat')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal_kembali">Tanggal Kembali ke Ma'had*</label>
            <input type="date" id="tanggal_kembali" name="tanggal_kembali"
                   value="{{ old('tanggal_kembali') }}" required>
            @error('tanggal_kembali')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="alasan_pulang">Alasan Pulang*</label>
            <textarea id="alasan_pulang" name="alasan_pulang"
                      placeholder="Tuliskan alasan pengajuan izin pulang..." required>{{ old('alasan_pulang') }}</textarea>
            @error('alasan_pulang')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="kontak_keluarga">Kontak Keluarga yang Bisa Dihubungi*</label>
            <input type="tel" id="kontak_keluarga" name="kontak_keluarga"
                   value="{{ old('kontak_keluarga') }}"
                   placeholder="08xxxxxxxxxx" required>
            @error('kontak_keluarga')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="required-note">* Kolom wajib diisi.</div>

        <div class="form-footer">
            <a href="{{ route('surat.perizinan.mhs') }}" class="btn btn-cancel">
                Batal
            </a>

            <div class="alert">
                <strong>ⓘ Perhatian</strong>
                Pengajuan izin pulang akan diperiksa oleh pengelola.
                Mahasiswa perlu menunggu keputusan sebelum izin dianggap disetujui.
            </div>

            <button type="submit" class="btn btn-submit">
                Kirim pengajuan
            </button>
        </div>
    </form>
</div>

<script>
    const tanggalBerangkat = document.getElementById('tanggal_berangkat');
    const tanggalKembali = document.getElementById('tanggal_kembali');

    tanggalBerangkat.addEventListener('change', function () {
        tanggalKembali.min = this.value;

        if (tanggalKembali.value && tanggalKembali.value < this.value) {
            tanggalKembali.value = '';
        }
    });
</script>

</body>
</html>