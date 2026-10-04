<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Akademik</title>

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

        h1 {
            text-align: center;
            font-size: 42px;
            font-weight: 900;
            margin-bottom: 60px;
            color: #202020;
        }

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

        .btn-back {
            background: #d1a525;
        }

        .btn-back:hover {
            background: #b9901e;
        }

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

        @media (max-width: 768px) {

            .container {
                width: 90%;
            }

            h1 {
                font-size: 32px;
                margin-bottom: 40px;
            }

            .button-area {
                flex-direction: column;
                gap: 20px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>

</head>

<body>

<div class="container">

    <h1>DATA AKADEMIK</h1>

    <form
        action="{{ route('pendaftaran.selesai') }}"
        method="POST"
    >

        @csrf


        {{-- Program Studi --}}
        <div class="form-group">

            <label for="program_studi">
                Program Studi
            </label>

            <input
                type="text"
                id="program_studi"
                name="program_studi"
                value="{{ old('program_studi', $data['program_studi'] ?? '') }}"
            >

            @error('program_studi')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Fakultas --}}
        <div class="form-group">

            <label for="fakultas">
                Fakultas
            </label>

            <input
                type="text"
                id="fakultas"
                name="fakultas"
                value="{{ old('fakultas', $data['fakultas'] ?? '') }}"
            >

            @error('fakultas')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Semester --}}
        <div class="form-group">

            <label for="semester">
                Semester
            </label>

            <select
                id="semester"
                name="semester"
            >

                <option value="">
                    Pilih Semester
                </option>

                <option
                    value="1"
                    {{ old('semester', $data['semester'] ?? '') == '1' ? 'selected' : '' }}
                >
                    Semester 1
                </option>

                <option
                    value="2"
                    {{ old('semester', $data['semester'] ?? '') == '2' ? 'selected' : '' }}
                >
                    Semester 2
                </option>

                <option
                    value="3"
                    {{ old('semester', $data['semester'] ?? '') == '3' ? 'selected' : '' }}
                >
                    Semester 3
                </option>

                <option
                    value="4"
                    {{ old('semester', $data['semester'] ?? '') == '4' ? 'selected' : '' }}
                >
                    Semester 4
                </option>

                <option
                    value="5"
                    {{ old('semester', $data['semester'] ?? '') == '5' ? 'selected' : '' }}
                >
                    Semester 5
                </option>

                <option
                    value="6"
                    {{ old('semester', $data['semester'] ?? '') == '6' ? 'selected' : '' }}
                >
                    Semester 6
                </option>

                <option
                    value="7"
                    {{ old('semester', $data['semester'] ?? '') == '7' ? 'selected' : '' }}
                >
                    Semester 7
                </option>

                <option
                    value="8"
                    {{ old('semester', $data['semester'] ?? '') == '8' ? 'selected' : '' }}
                >
                    Semester 8
                </option>

            </select>

            @error('semester')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Tombol --}}
        <div class="button-area">

            <button
                type="button"
                class="btn btn-back"
                onclick="window.location.href='{{ route('pendaftaran.pribadi') }}'"
            >
                Kembali
            </button>


            <button
                type="submit"
                class="btn btn-next"
            >
                Selesai
            </button>

        </div>

    </form>

</div>

</body>

</html>