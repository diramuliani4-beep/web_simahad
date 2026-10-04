<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Pribadi</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f8f8f8;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 80%;
            max-width: 1150px;
            margin: 40px auto;
        }

        /* Judul */
        h1 {
            text-align: center;
            font-size: 42px;
            font-weight: 900;
            margin-bottom: 60px;
            color: #202020;
            letter-spacing: -1px;
        }

        /* Form */
        .form-group {
            margin-bottom: 60px;
        }

        label {
            display: block;
            margin-left: 15px;
            margin-bottom: 8px;

            font-size: 24px;
            font-weight: 700;
            color: #69717e;
        }

        input,
        select {
            width: 100%;
            height: 76px;

            padding: 0 25px;

            border: none;
            border-radius: 20px;

            background: #920025;

            color: white;
            font-size: 20px;

            outline: none;

            box-shadow: 0 4px 5px rgba(0, 0, 0, 0.35);
        }

        input:focus,
        select:focus {
            box-shadow:
                0 0 0 3px rgba(146, 0, 37, 0.25),
                0 4px 5px rgba(0, 0, 0, 0.35);
        }

        input::placeholder {
            color: transparent;
        }

        /* Dua kolom */
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
        }

        /* Tombol */
        .button-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .btn {
            width: 215px;
            height: 62px;

            border: none;
            border-radius: 12px;

            color: white;

            font-size: 22px;
            font-weight: bold;

            cursor: pointer;

            box-shadow: 0 4px 5px rgba(0, 0, 0, 0.3);
        }

        /* Tombol Kembali */
        .btn-back {
            background: #d1a525;
        }

        .btn-back:hover {
            background: #b9901e;
        }

        /* Tombol Lanjut */
        .btn-next {
            background: #70001d;
        }

        .btn-next:hover {
            background: #5d0018;
        }

        /* Error */
        .error {
            margin-top: 7px;
            margin-left: 15px;

            color: #d00000;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {

            .container {
                width: 90%;
            }

            h1 {
                font-size: 32px;
                margin-bottom: 40px;
            }

            .row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-group {
                margin-bottom: 35px;
            }

            label {
                font-size: 20px;
            }

            input,
            select {
                height: 60px;
            }

            .button-area {
                gap: 20px;
            }

            .btn {
                width: 180px;
            }
        }
    </style>

</head>

<body>

<div class="container">

    <h1>DATA PRIBADI</h1>

    <form
        action="{{ route('pendaftaran.simpanPribadi') }}"
        method="POST"
    >

        @csrf


        {{-- Nama Lengkap --}}
        <div class="form-group">

            <label for="nama_lengkap">
                Nama Lengkap
            </label>

            <input
                type="text"
                id="nama_lengkap"
                name="nama_lengkap"
                value="{{ old('nama_lengkap', $data['nama_lengkap'] ?? '') }}"
            >

            @error('nama_lengkap')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- NIM --}}
        <div class="form-group">

            <label for="nim">
                NIM
            </label>

            <input
                type="text"
                id="nim"
                name="nim"
                value="{{ old('nim', $data['nim'] ?? '') }}"
            >

            @error('nim')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Tempat dan Tanggal Lahir --}}
        <div class="row">

            <div class="form-group">

                <label for="tempat_lahir">
                    Tempat
                </label>

                <input
                    type="text"
                    id="tempat_lahir"
                    name="tempat_lahir"
                    value="{{ old('tempat_lahir', $data['tempat_lahir'] ?? '') }}"
                >

                @error('tempat_lahir')
                    <div class="error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="form-group">

                <label for="tanggal_lahir">
                    Tanggal Lahir
                </label>

                <input
                    type="date"
                    id="tanggal_lahir"
                    name="tanggal_lahir"
                    value="{{ old('tanggal_lahir', $data['tanggal_lahir'] ?? '') }}"
                >

                @error('tanggal_lahir')
                    <div class="error">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>


        {{-- Jenis Kelamin --}}
        <div class="form-group">

            <label for="jenis_kelamin">
                Jenis Kelamin
            </label>

            <select
                id="jenis_kelamin"
                name="jenis_kelamin"
            >

                <option value="">
                    Pilih Jenis Kelamin
                </option>

                <option
                    value="Laki-laki"
                    {{ old('jenis_kelamin', $data['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : '' }}
                >
                    Laki-laki
                </option>

                <option
                    value="Perempuan"
                    {{ old('jenis_kelamin', $data['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : '' }}
                >
                    Perempuan
                </option>

            </select>

            @error('jenis_kelamin')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- No HP --}}
        <div class="form-group">

            <label for="no_hp">
                No-HP
            </label>

            <input
                type="text"
                id="no_hp"
                name="no_hp"
                value="{{ old('no_hp', $data['no_hp'] ?? '') }}"
            >

            @error('no_hp')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Tombol Kembali dan Lanjut --}}
        <div class="button-area">

            {{-- Kembali ke Dashboard --}}
            <button
                type="button"
                class="btn btn-back"
                onclick="window.location.href='{{ route('dashboard') }}'"
            >
                Kembali
            </button>


            {{-- Lanjut ke Data Akademik --}}
            <button
                type="submit"
                class="btn btn-next"
            >
                Lanjut
            </button>

        </div>

    </form>

</div>

</body>

</html>