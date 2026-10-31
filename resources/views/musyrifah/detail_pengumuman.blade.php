
@extends('layouts.musyrifah')

@section('content')
<style>
    .detail-pengumuman {
        padding: 26px 30px;
        color: #171717;
        font-family: inherit;
    }

    .detail-header {
        margin: 0 0 112px 12px;
    }

    .detail-header h1 {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .detail-header p {
        font-size: 20px;
        margin: 0 0 0 30px;
    }

    .form-pengumuman {
        width: 100%;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        margin: 0 0 6px 12px;
        font-size: 20px;
        color: #424242;
    }

    .form-control-pengumuman {
        width: 100%;
        min-height: 66px;
        padding: 17px 20px;
        background: #f5e7ea;
        border: 1px solid transparent;
        border-radius: 20px;
        outline: none;
        color: #333;
        font: inherit;
        font-size: 20px;
        box-sizing: border-box;
    }

    .form-control-pengumuman:focus {
        border-color: #790d20;
    }

    .form-control-pengumuman::placeholder {
        color: #81797b;
    }

    select.form-control-pengumuman {
        appearance: auto;
        cursor: pointer;
    }

    textarea.form-control-pengumuman {
        min-height: 144px;
        resize: vertical;
    }

    .lampiran-label {
        margin-bottom: 7px !important;
    }

    .upload-area {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70%;
        min-height: 144px;
        padding: 20px;
        box-sizing: border-box;
        background: #f5e7ea;
        border-radius: 20px;
        text-align: center;
        cursor: pointer;
        transition: background 0.2s;
    }

    .upload-area:hover {
        background: #f0dce1;
    }

    .upload-area strong {
        display: block;
        font-size: 16px;
        margin-bottom: 8px;
    }

    .upload-area span {
        color: #81797b;
        font-size: 15px;
        font-weight: 600;
    }

    .upload-area input {
        display: none;
    }

    .nama-file {
        display: block;
        margin-top: 8px;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .form-actions {
        display: flex;
        gap: 38px;
        margin-top: 27px;
    }

    .btn-form {
        width: 187px;
        min-height: 47px;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-batal {
        background: #cecece;
        color: #111;
    }

    .btn-simpan {
        background: #790d20;
        color: #fff;
    }

    .btn-batal:hover {
        background: #bdbdbd;
    }

    .btn-simpan:hover {
        background: #5f0918;
    }

    @media (max-width: 768px) {
        .detail-pengumuman {
            padding: 20px 14px;
        }

        .detail-header {
            margin: 0 0 55px;
        }

        .detail-header h1 {
            font-size: 27px;
        }

        .detail-header p {
            font-size: 16px;
            margin-left: 0;
        }

        .form-group label {
            font-size: 17px;
        }

        .form-control-pengumuman {
            font-size: 16px;
        }

        .upload-area {
            width: 100%;
        }

        .form-actions {
            gap: 12px;
        }

        .btn-form {
            width: 50%;
        }
    }
</style>

<div class="detail-pengumuman">

    <div class="detail-header">
        <h1>+ Pengumuman</h1>
        <p>Buat Pengumuman Baru Untuk Mahasantri</p>
    </div>

    <form class="form-pengumuman"
          id="formPengumuman"
          enctype="multipart/form-data">

        @csrf

        <div class="form-group">
            <label for="judul">Judul Pengumuman</label>
            <input
                type="text"
                id="judul"
                name="judul"
                class="form-control-pengumuman"
                placeholder="Masukan Judul Pengumuman"
                required
            >
        </div>

        <div class="form-group">
            <label for="kategori">Kategori</label>
            <select
                id="kategori"
                name="kategori"
                class="form-control-pengumuman"
                required
            >
                <option value="" selected disabled>
                    Pilih Kategori Pengumuman
                </option>
                <option value="Akademik">Akademik</option>
                <option value="Asrama">Asrama</option>
                <option value="Kegiatan">Kegiatan</option>
                <option value="Administrasi">Administrasi</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>

        <div class="form-group">
            <label for="isi">Isi Pengumuman</label>
            <textarea
                id="isi"
                name="isi"
                class="form-control-pengumuman"
                placeholder="Tuliskan Isi pengumuman Secara Lengkap"
                required
            ></textarea>
        </div>

        <div class="form-group">
            <label for="lampiran" class="lampiran-label">
                Lampiran (Opsional)
            </label>

            <label for="lampiran" class="upload-area">
                <div>
                    <strong>Unggah File atau Gambar</strong>
                    <span>PDF, JPG, PNG</span>
                    <span class="nama-file" id="namaFile"></span>
                </div>

                <input
                    type="file"
                    id="lampiran"
                    name="lampiran"
                    accept=".pdf,.jpg,.jpeg,.png"
                >
            </label>
        </div>

        <div class="form-actions">
            <button
                type="button"
                class="btn-form btn-batal"
                onclick="window.location.href='{{ route('musyrifah.pengumuman') }}'"
            >
                Batal
            </button>

            <button
                type="submit"
                class="btn-form btn-simpan"
            >
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputLampiran = document.getElementById('lampiran');
        const namaFile = document.getElementById('namaFile');
        const form = document.getElementById('formPengumuman');

        inputLampiran.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) {
                namaFile.textContent = '';
                return;
            }

            const tipeDiizinkan = [
                'application/pdf',
                'image/jpeg',
                'image/png'
            ];

            if (!tipeDiizinkan.includes(file.type)) {
                alert('Format lampiran harus PDF, JPG, atau PNG.');
                this.value = '';
                namaFile.textContent = '';
                return;
            }

            namaFile.textContent = file.name;
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            alert(
                'Tampilan formulir sudah siap. ' +
                'Fitur penyimpanan pengumuman perlu dihubungkan ' +
                'ke database terlebih dahulu.'
            );
        });
    });
</script>
@endsection