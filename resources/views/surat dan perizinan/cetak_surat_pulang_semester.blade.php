<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pulang Semester - {{ $surat->nomor_surat }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #e9e9e9;
            color: #111;
            padding: 24px 12px 48px;
        }

        .lembar {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 22mm 20mm;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .18);
            font-size: 12pt;
            line-height: 1.65;
        }

        .kop {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 12px;
            border-bottom: 3px double #6B0D1B;
        }

        .kop img {
            height: 74px;
            width: auto;
        }

        .kop__teks {
            flex: 1;
            text-align: center;
        }

        .kop__teks h1 {
            font-size: 15pt;
            font-weight: 700;
            color: #6B0D1B;
            letter-spacing: .04em;
        }

        .kop__teks h2 {
            font-size: 12pt;
            font-weight: 600;
            margin-top: 2px;
        }

        .kop__teks p {
            font-size: 9.5pt;
            color: #4b5563;
            margin-top: 2px;
        }

        .judul {
            margin: 26px 0 4px;
            text-align: center;
        }

        .judul h3 {
            font-size: 13pt;
            font-weight: 700;
            text-decoration: underline;
            letter-spacing: .05em;
        }

        .judul span {
            display: block;
            margin-top: 3px;
            font-size: 11pt;
        }

        .identitas {
            margin: 20px 0;
        }

        .identitas table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5pt;
        }

        .identitas td {
            padding: 4px 0;
            vertical-align: top;
        }

        .identitas td:first-child {
            width: 34%;
        }

        .identitas td:nth-child(2) {
            width: 3%;
        }

        .isi p {
            margin-bottom: 10px;
            text-align: justify;
            text-indent: 32px;
        }

        .ttd {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 44px;
        }

        .ttd__kolom {
            width: 32%;
            text-align: center;
            font-size: 11pt;
        }

        .ttd__ruang {
            height: 74px;
        }

        .ttd__nama {
            font-weight: 700;
            text-decoration: underline;
        }

        .catatan {
            margin-top: 30px;
            padding: 10px 14px;
            border: 1px dashed #C9A96A;
            border-radius: 8px;
            font-size: 10pt;
            color: #4b5563;
            background: #fffdf7;
        }

        .toolbar {
            width: 210mm;
            margin: 0 auto 14px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .tombol {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border: 1px solid #6B0D1B;
            border-radius: 9px;
            background: #6B0D1B;
            color: #fff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .tombol--outline {
            background: #fff;
            color: #6B0D1B;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .lembar {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 18mm 20mm;
                box-shadow: none;
            }

            .toolbar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" class="tombol" onclick="window.print()">Cetak / Simpan PDF</button>
        <a href="{{ route('surat.perizinan.mhs.semester') }}" class="tombol tombol--outline">Kembali</a>
    </div>

    <div class="lembar">
        <div class="kop">
            <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol" onerror="this.style.display='none'">
            <div class="kop__teks">
                <h1>MA'HAD AL-JAMI'AH</h1>
                <h2>UIN IMAM BONJOL PADANG</h2>
                <p>Jl. Prof. Mahmud Yunus Lubuk Lintah, Padang &middot; Sumatera Barat</p>
            </div>
        </div>

        <div class="judul">
            <h3>SURAT IZIN PULANG SEMESTER</h3>
            <span>Nomor: {{ $surat->nomor_surat }}</span>
        </div>

        <div class="identitas">
            <table>
                <tr>
                    <td>Nama Mahasiswa</td>
                    <td>:</td>
                    <td><strong>{{ $user->name }}</strong></td>
                </tr>
                <tr>
                    <td>NIM</td>
                    <td>:</td>
                    <td>{{ $user->nim ?: ($pendaftaran?->nim ?: '-') }}</td>
                </tr>
                <tr>
                    <td>Fakultas</td>
                    <td>:</td>
                    <td>{{ $pendaftaran?->fakultas ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>:</td>
                    <td>{{ $pendaftaran?->program_studi ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Angkatan</td>
                    <td>:</td>
                    <td>{{ $pendaftaran?->tahun_angkatan ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Kamar Ma'had</td>
                    <td>:</td>
                    <td>
                        @if ($kamar)
                            {{ $kamar->nama_kamar }} ({{ $kamar->nomor_kamar }})
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="isi">
            <p>
                Dengan ini mengajukan izin pulang semester dari Ma'had Al-Jami'ah UIN Imam Bonjol Padang.
                Surat ini disampaikan kepada pengelola Ma'had sebagai permohonan resmi mahasiswa yang bersangkutan.
            </p>

            @if ($surat->alasan)
                <p>Keterangan: {{ $surat->alasan }}</p>
            @endif

            <p>
                Demikian surat permohonan ini dibuat. Mohon surat ini ditandatangani oleh orang tua/wali
                sebagai bentuk persetujuan, kemudian diunggah kembali melalui akun SIMAHAD mahasiswa
                untuk diperiksa oleh Musyrifah.
            </p>
        </div>

        <div class="ttd">
            <div class="ttd__kolom">
                <p>Mahasiswa,</p>
                <div class="ttd__ruang"></div>
                <p class="ttd__nama">{{ $user->name }}</p>
                <p>NIM. {{ $user->nim ?: '-' }}</p>
            </div>

            <div class="ttd__kolom">
                <p>Orang Tua/Wali,</p>
                <div class="ttd__ruang"></div>
                <p class="ttd__nama">................................</p>
            </div>

            <div class="ttd__kolom">
                <p>Musyrifah,</p>
                <div class="ttd__ruang"></div>
                <p class="ttd__nama">................................</p>
            </div>
        </div>

        <div class="catatan">
            Catatan: surat ini sah setelah ditandatangani orang tua/wali dan diverifikasi Musyrifah melalui SIMAHAD.
        </div>
    </div>
</body>
</html>
