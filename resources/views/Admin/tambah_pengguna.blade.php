
@extends('layouts.admin')

@section('content')

<style>
    .form-pengguna {
        padding: 15px 25px 25px;
        font-family: inherit;
    }

    .form-pengguna h2 {
        font-size: 22px;
        font-weight: 600;
        color: #151515;
        margin: 0 0 18px;
        padding-bottom: 8px;
        border-bottom: 1px solid #a00030;
    }

    .form-pengguna .judul-bagian {
        font-size: 15px;
        font-weight: 600;
        margin: 18px 10px 10px;
    }

    .form-pengguna .input-pengguna {
        width: 100%;
        height: 38px;
        padding: 8px 48px;
        margin-bottom: 18px;
        border: 1px solid #aaa;
        border-radius: 14px;
        background: #f4e6e9;
        box-shadow: 0 2px 3px rgba(0,0,0,.2);
        font-size: 14px;
        outline: none;
    }

    .form-pengguna .input-pengguna:focus {
        border-color: #800020;
        box-shadow: 0 0 0 2px rgba(128,0,32,.12);
    }

    .form-pengguna .tombol-form {
    min-width: 120px;
    height: 38px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    line-height: 1;
    border-radius: 14px;
    border: 1px solid #aaa;
    background: white;
    box-shadow: 0 2px 3px rgba(0,0,0,.2);
    font-size: 14px;
    cursor: pointer;
    text-decoration: none;
}

    .form-pengguna .tombol-simpan {
        background: #90002d;
        color: white;
        border-color: #800020;
    }

    .form-pengguna .tombol-simpan:hover {
        background: #700020;
    }

    .form-pengguna .aksi-form {
        display: flex;
        justify-content: space-between;
        padding: 0 35px;
        margin-top: 5px;
    }

    @media(max-width: 600px) {
        .form-pengguna {
            padding: 12px;
        }

        .form-pengguna .input-pengguna {
            padding-left: 18px;
        }

        .form-pengguna .aksi-form {
            padding: 0;
        }
    }
</style>

<div class="form-pengguna">

    <h2>Tambah Pengguna</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.manajemen-pengguna.store') }}"
          method="POST">
        @csrf

        <div class="judul-bagian">Informasi Pengguna</div>

        <input type="text"
               name="name"
               class="input-pengguna"
               placeholder="Nama Lengkap"
               value="{{ old('name') }}"
               required>

        <input type="email"
               name="email"
               class="input-pengguna"
               placeholder="Email"
               value="{{ old('email') }}"
               required>

        <input type="text"
               name="no_hp"
               class="input-pengguna"
               placeholder="No-hp"
               value="{{ old('no_hp') }}">

        <select name="role" class="input-pengguna" required>
            <option value="">Peran Pengguna</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
            <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
            <option value="musyrifah" {{ old('role') == 'musyrifah' ? 'selected' : '' }}>Musyrifah</option>
            <option value="pimpinan" {{ old('role') == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
        </select>

        <div class="judul-bagian">Informasi Akun</div>

        <input type="password"
               name="password"
               class="input-pengguna"
               placeholder="Kata Sandi"
               required>

        <input type="password"
               name="password_confirmation"
               class="input-pengguna"
               placeholder="Konfirmasi Kata Sandi"
               required>

        <select name="status" class="input-pengguna" required>
            <option value="">Status Akun</option>
            <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <div class="aksi-form">
            <a href="{{ route('admin.manajemen-pengguna') }}"class="btn tombol-form">
                Batal
            </a>

            <button type="submit"
                    class="tombol-form tombol-simpan">
                Simpan
            </button>
        </div>

    </form>
</div>

@endsection
