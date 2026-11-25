<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman Ma'had</title>
</head>
<body style="margin:0;padding:0;background:#f7f5f4;font-family:'Segoe UI',Arial,sans-serif;color:#2b2528;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:28px 14px;background:#f7f5f4;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:540px;background:#ffffff;border:1px solid #ecd9dd;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 26px;background:#6B0D1B;color:#ffffff;">
                            <div style="font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:#C9A96A;font-weight:700;">SIMAHAD</div>
                            <div style="margin-top:5px;font-size:18px;font-weight:700;">Pengumuman Ma&rsquo;had</div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:26px 26px 8px;">
                            <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Assalamu&rsquo;alaikum, <strong>{{ $penerima->name }}</strong>.</p>

                            <p style="margin:0 0 6px;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#8a8085;">{{ $pengumuman->kategori }}</p>
                            <h1 style="margin:0 0 8px;font-size:19px;line-height:1.4;color:#6B0D1B;">{{ $pengumuman->judul }}</h1>
                            <p style="margin:0 0 18px;font-size:12px;color:#6d6266;">
                                Tanggal publikasi: <strong>{{ $pengumuman->tanggalTeks() }}</strong>
                            </p>

                            <div style="margin:0 0 20px;padding:16px 18px;border:1px solid #ecd9dd;border-radius:10px;background:#fdfbfb;font-size:14px;line-height:1.8;color:#2b2528;">
                                {!! nl2br(e($pengumuman->isi)) !!}
                            </div>

                            <p style="margin:0 0 6px;font-size:12px;line-height:1.7;color:#6d6266;">
                                Disampaikan oleh: <strong>{{ $pengumuman->penulisTeks() }}</strong>
                            </p>

                            <p style="margin:22px 0;text-align:center;">
                                <a href="{{ route('pengumuman.mhs.show', $pengumuman) }}"
                                   style="display:inline-block;padding:13px 26px;border-radius:9px;background:#6B0D1B;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;">
                                    Lihat Pengumuman
                                </a>
                            </p>

                            <p style="margin:0;padding:12px 14px;border:1px solid #ecd9dd;border-radius:9px;background:#F8EDEF;font-size:12px;line-height:1.7;color:#6B0D1B;">
                                Bila tombol tidak berfungsi, buka SIMAHAD lalu pilih menu Pengumuman.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 26px;border-top:1px solid #f0e6e8;background:#fdfbfb;font-size:11px;line-height:1.7;color:#8a8085;">
                            Email ini dikirim otomatis oleh SIMAHAD kepada mahasiswa yang terdaftar
                            setiap kali Musyrifah memublikasikan pengumuman.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
