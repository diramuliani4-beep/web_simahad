
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa - SIMAHAD</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F8F7F4;
            color: #333;
            min-height: 100vh;
        }

        .navbar {
            height: 65px;
            background: white;
            border-bottom: 1px solid #E5D5D8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
        }

        .brand {
            color: #6B0D1B;
            font-size: 24px;
            font-weight: 800;
            text-decoration: none;
        }

        .back-link {
            color: #6B0D1B;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .container {
            max-width: 850px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .profile-card {
            background: white;
            border: 1px solid #B87883;
            border-radius: 7px;
            padding: 25px 45px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
        }

        .heading {
            text-align: center;
            margin-bottom: 15px;
        }

        .heading h1 {
            font-size: 21px;
            color: #6B0D1B;
            font-weight: 800;
        }

        .heading p {
            font-size: 11px;
            margin-top: 5px;
            color: #444;
        }

        .identity {
            text-align: center;
            margin: 15px 0 22px;
        }

        .avatar {
            width: 85px;
            height: 85px;
            background: #F8EDEF;
            border-radius: 50%;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6B0D1B;
            font-size: 32px;
            font-weight: 700;
            overflow: hidden;
            border: 2px solid #E8DADD;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .identity h2 {
            font-size: 14px;
            margin-top: 9px;
            color: #444;
        }

        .identity p {
            font-size: 11px;
            color: #6B0D1B;
            margin-top: 7px;
        }

        .role {
            display: inline-block;
            background: #E8F5E9;
            color: #278044;
            border-radius: 12px;
            padding: 4px 9px;
            font-size: 9px;
            font-weight: 700;
            margin-top: 6px;
        }

        .info-box {
            max-width: 520px;
            margin: auto;
            border: 1px solid #B87883;
            border-radius: 7px;
            padding: 18px;
        }

        .info-title {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #6B0D1B;
            font-size: 15px;
            font-weight: 700;
            padding-bottom: 15px;
            border-bottom: 1px solid #E8DADD;
            margin-bottom: 17px;
        }

        .info-item {
            margin-bottom: 16px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item label {
            display: block;
            color: #888;
            font-size: 12px;
            margin-bottom: 9px;
        }

        .info-item p {
            font-size: 14px;
            font-weight: 700;
            color: #444;
            overflow-wrap: anywhere;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
        }

        .btn {
            border: none;
            background: #6B0D1B;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
        }

        .btn:hover {
            background: #580015;
        }

        .success {
            background: #E8F5E9;
            color: #287D3C;
            padding: 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .error {
            color: #B42318;
            font-size: 11px;
            margin-top: -9px;
            margin-bottom: 12px;
        }

        .edit-form {
            max-width: 520px;
            margin: auto;
        }

        .edit-form label {
            display: block;
            font-size: 12px;
            color: #555;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .edit-form input {
            width: 100%;
            padding: 11px;
            border: 1px solid #D9C5C9;
            border-radius: 6px;
            font-family: inherit;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .edit-form input:focus {
            outline: none;
            border-color: #6B0D1B;
        }

        .current-photo {
            width: 85px;
            height: 85px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #E8DADD;
            margin-bottom: 12px;
        }

        .photo-help {
            font-size: 10px;
            color: #888;
            margin-top: -8px;
            margin-bottom: 15px;
        }

        .edit-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 5px;
        }

        .btn-cancel {
            background: #777;
        }

        .btn-cancel:hover {
            background: #555;
        }

        @media(max-width: 600px) {
            .navbar {
                padding: 0 18px;
            }

            .profile-card {
                padding: 22px 17px;
            }

            .actions {
                flex-direction: column;
            }

            .actions .btn {
                text-align: center;
            }

            .edit-actions {
                flex-direction: column-reverse;
            }

            .edit-actions .btn {
                text-align: center;
            }
        }
    </style>
</head>

<body>

<main class="container">

    <div class="profile-card">

        <div class="heading">
            <h1>Profil Mahasiswa</h1>
            <p>Lihat informasi diri yang terdaftar di SIMAHAD.</p>
        </div>

        <div class="identity">

            <div class="avatar">
                @if($user->foto_profil)
                    <img src="{{ asset('storage/' . $user->foto_profil) }}"
                         alt="Foto Profil">
                @else
                    {{ strtoupper(substr($user->name ?? 'M', 0, 1)) }}
                @endif
            </div>

            <h2>{{ $user->name }}</h2>

            <p>NIM: {{ $user->nim ?? '-' }}</p>

            <span class="role">Mahasiswa Ma'had</span>

        </div>

        @if(session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="success" style="background:#FCE8E6;color:#B42318;">
                Periksa kembali data yang kamu masukkan.
            </div>
        @endif

        @if(request()->query('edit') == '1')

            <form action="{{ route('profil.mhs.update') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="edit-form">

                @csrf
                @method('PUT')

                <h3 class="info-title">✎ Ubah Informasi Pribadi</h3>

                <label>Foto Profil</label>

                @if($user->foto_profil)
                    <div>
                        <img src="{{ asset('storage/' . $user->foto_profil) }}"
                             alt="Foto Profil Saat Ini"
                             class="current-photo">
                    </div>
                @endif

                <input type="file"
                       name="foto_profil"
                       accept=".jpg,.jpeg,.png,.webp">

                <p class="photo-help">
                    Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                    Kosongkan jika tidak ingin mengganti foto.
                </p>

                @error('foto_profil')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label>Nama Lengkap</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       required>

                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label>Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       required>

                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label>Nomor WhatsApp</label>
                <input type="text"
                       name="no_hp"
                       value="{{ old('no_hp', $user->no_hp) }}">

                @error('no_hp')
                    <div class="error">{{ $message }}</div>
                @enderror

                <div class="edit-actions">

                    <a href="{{ route('profil.mhs') }}"
                       class="btn btn-cancel">
                        Batal
                    </a>

                    <button type="submit" class="btn">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        @else

            <div class="info-box">

                <div class="info-title">
                    <span>♙</span>
                    Informasi Pribadi
                </div>

                <div class="info-item">
                    <label>Nama Lengkap</label>
                    <p>{{ $user->name ?? '-' }}</p>
                </div>

                <div class="info-item">
                    <label>Email</label>
                    <p>{{ $user->email ?? '-' }}</p>
                </div>

                <div class="info-item">
                    <label>Nomor WhatsApp</label>
                    <p>{{ $user->no_hp ?? '-' }}</p>
                </div>

            </div>

            <div class="actions">

                <a href="{{ route('dashboard') }}" class="btn">
                    Kembali ke Dashboard
                </a>

                <a href="{{ route('profil.mhs', ['edit' => 1]) }}"
                   class="btn">
                    ✎ Ubah Profil
                </a>

            </div>

        @endif

    </div>

</main>

</body>
</html>
