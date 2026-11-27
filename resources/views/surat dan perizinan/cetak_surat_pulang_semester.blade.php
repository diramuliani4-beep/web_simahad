
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pulang Semester - {{ $surat->nomor_surat }}</title>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: #e9e9e9;
            color: #111;
            padding: 24px 12px 48px;
        }

        .toolbar {
            width: 210mm;
            max-width: 100%;
            margin: 0 auto 14px;
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
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
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .tombol--outline {
            background: #fff;
            color: #6B0D1B;
        }

        .lembar {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 17mm 20mm 15mm;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .18);
            font-size: 11pt;
            line-height: 1.45;
        }

        .kop { text-align: center; }
        .kop h1 { font-size: 11pt; font-weight: 700; line-height: 1.45; }
        .kop h2 { font-size: 12pt; font-weight: 700; line-height: 1.45; }
        .kop p { margin-top: 2px; font-size: 9pt; }

        .garis-kop {
            margin-top: 7px;
            border-top: 1.4px solid #111;
            border-bottom: .6px solid #111;
            height: 3px;
        }

        .tanggal {
            margin-top: 18px;
            text-align: right;
        }

        .judul {
            margin-top: 13px;
            text-align: center;
        }

        .judul h3 {
            font-size: 13pt;
            font-weight: 700;
            text-decoration: underline;
        }

        .judul p {
            margin-top: 3px;
            font-size: 10.5pt;
        }

        .pembuka {
            margin-top: 18px;
            text-align: justify;
            text-indent: 0;
        }

        .identitas {
            width: 100%;
            margin: 12px 0 16px;
            border-collapse: collapse;
            font-size: 11pt;
        }

        .identitas td {
            padding: 3px 0;
            vertical-align: top;
        }

        .identitas td:first-child { width: 27%; }
        .identitas td:nth-child(2) { width: 3%; }
        .identitas .nilai {
            width: 70%;
            border-bottom: 1px dotted #555;
        }

        .isi p {
            margin-bottom: 9px;
            text-align: justify;
            text-indent: 0;
        }

        /* BAGIAN TANDA TANGAN */
        .ttd {
            margin-top: 20px;
            font-size: 11pt;
        }

        .ttd__tanggal {
            text-align: right;
            margin-bottom: 8px;
        }

        .ttd__area {
            width: 70%;
            margin-left: auto;
        }

        .ttd__mengetahui {
            text-align: center;
            margin-bottom: 8px;
        }

        .ttd__container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            text-align: center;
        }

        .ttd__kolom {
            width: 50%;
        }

        .ttd__nama {
            font-weight: 700;
            min-height: 22px;
        }

        .ttd__ruang {
            height: 65px;
        }

        .catatan-keluar {
            width: 100%;
            margin-top: 28px;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .catatan-keluar th,
        .catatan-keluar td {
            border: 1px solid #111;
            padding: 5px 4px;
            text-align: center;
        }

        .catatan-keluar thead th { font-weight: 700; }
        .catatan-keluar tbody th {
            text-align: left;
            font-weight: 700;
        }

        .catatan-keluar .kosong { height: 24px; }

        @page {
            size: A4;
            margin: 0;
        }

        @media (max-width: 800px) {
            body { padding: 12px 8px 24px; }

            .lembar {
                width: 100%;
                min-height: auto;
                padding: 24px 18px;
            }

            .ttd__area {
                width: 80%;
            }
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
                padding: 17mm 20mm 15mm;
                box-shadow: none;
            }

            .toolbar { display: none; }
        }
    </style>
</head>
<body>
    @php
        $nama = $user->name ?: ($pendaftaran?->nama_lengkap ?: '-');
        $nim = $user->nim ?: ($pendaftaran?->nim ?: '-');
        $prodi = $pendaftaran?->program_studi ?: '-';
        $namaKamar = $kamar ? trim($kamar->nama_kamar.' '.$kamar->nomor_kamar) : '-';
        $tanggalSurat = $surat->created_at?->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y');
    @endphp

    <div class="toolbar">
        <button type="button" class="tombol" id="cetakSurat">Cetak / Simpan PDF</button>
        <a href="{{ route('surat.perizinan.mhs.semester.pdf', $surat) }}" class="tombol">Unduh PDF Resmi</a>
        <a href="{{ route('surat.perizinan.mhs.semester') }}" class="tombol tombol--outline">Kembali</a>
    </div>

    <main class="lembar">
        <header class="kop">
            <h1>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h1>
            <h1>UNIVERSITAS ISLAM NEGERI IMAM BONJOL PADANG</h1>
            <h2>MA'HAD AL-JAMI'AH TUANKU IMAM BONJOL</h2>
            <p><i>Kampus III Sungai Bangek, Balai Gadang, Kec. Koto Tangah, Kota Padang, Sumatera Barat</i></p>
            <div class="garis-kop"></div>
        </header>

        <section class="judul">
            <h3>SURAT IZIN PULANG MAHASANTRI</h3>
            <p>Nomor: {{ $surat->nomor_surat }} &nbsp;&nbsp;&nbsp;&nbsp;</p>
        </section>

        <p class="pembuka">
            Yang bertanda tangan di bawah ini, Pengurus / Musyrif(ah) Ma'had Al-Jami'ah Tuanku Imam Bonjol UIN Imam Bonjol Padang,
            memberikan izin pulang kepada:
        </p>

        <table class="identitas">
            <tr><td>Nama Lengkap</td><td>:</td><td class="nilai"><strong>{{ $nama }}</strong></td></tr>
            <tr><td>Prodi</td><td>:</td><td class="nilai">{{ $prodi }}</td></tr>
            <tr><td>Kamar</td><td>:</td><td class="nilai">{{ $namaKamar }}</td></tr>
            <tr><td>Tujuan</td><td>:</td><td class="nilai">{{ $surat->lokasi ?: '-' }}</td></tr>
            <tr><td>Tanggal Pulang</td><td>:</td><td class="nilai">{{ $surat->tanggal_mulai?->locale('id')->translatedFormat('d F Y') ?: '-' }}</td></tr>
            <tr><td>Tanggal Kembali</td><td>:</td><td class="nilai">{{ $surat->tanggal_selesai?->locale('id')->translatedFormat('d F Y') ?: '-' }}</td></tr>
            <tr><td>Alasan Pulang</td><td>:</td><td class="nilai">{{ $surat->alasan ?: '-' }}</td></tr>
            <tr><td>Keterangan</td><td>:</td><td class="nilai">{{ $surat->keterangan ?: '-' }}</td></tr>
            <tr><td>Penjemputan/Transportasi</td><td>:</td><td class="nilai">{{ $surat->transportasi ?: '-' }}</td></tr>
        </table>

        <section class="isi">
            <p>
                Mahasantri yang bersangkutan wajib menjaga nama baik Ma'had Al-Jami'ah Tuanku UIN Imam Bonjol Padang
                selama berada di luar lingkungan asrama, kembali ke Ma'had tepat waktu sesuai jadwal yang tertera diatas, serta melapor kembali kepada Musyrif/Musyrifah setalah tiba.
            </p>
            <p>Demikian surat ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </section>

        <section class="ttd">
            <p class="ttd__tanggal">
                Padang, {{ $tanggalSurat }}
            </p>

            <div class="ttd__area">
                <p class="ttd__mengetahui">Mengetahui,</p>

                <div class="ttd__container">
                    <div class="ttd__kolom">
                        <p class="ttd__nama">Musyrifah</p>
                        <div class="ttd__ruang"></div>
                        <p>( {{ $namaMusyrifah ?? '............................' }} )</p>
                        <p>NIP. {{$nipMusyrifah ?? '............................'}}</p>
                    </div>

                    <div class="ttd__kolom">
                        <p class="ttd__nama">Mahasantri Pemohon</p>
                        <div class="ttd__ruang"></div>
                        <p>( {{ $nama }} )</p>
                        <p>NIM. {{ $nim }}</p>
                    </div>
                </div>
            </div>
        </section>

        <table class="catatan-keluar">
            <colgroup>
                <col style="width:18%">
                <col style="width:11%">
                <col style="width:18%">
                <col style="width:8%">
                <col style="width:12%">
                <col style="width:8%">
                <col style="width:25%">
            </colgroup>
            <thead>
                <tr>
                    <th colspan="7">Catatan Keluar &amp; Kembali ke Ma'had (Diisi Petugas/Musyrifah)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Keluar Ma'had</th>
                    <td>Hari/Tgl</td>
                    <td class="kosong"></td>
                    <td>Pukul</td>
                    <td class="kosong"></td>
                    <td>WIB</td>
                    <td class="kosong">Paraf</td>
                </tr>
                <tr>
                    <th>Kembali ke Ma'had</th>
                    <td>Hari/Tgl</td>
                    <td class="kosong"></td>
                    <td>Pukul</td>
                    <td class="kosong"></td>
                    <td>WIB</td>
                    <td class="kosong">Paraf</td>
                </tr>
            </tbody>
        </table>
    </main>

    <script>
        document.getElementById('cetakSurat').addEventListener('click', function () {
            window.print();
        });
    </script>
</body>
</html>
