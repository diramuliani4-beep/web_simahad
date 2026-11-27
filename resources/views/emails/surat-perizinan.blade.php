<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul }} - SIMAHAD</title>
</head>
<body style="margin:0;padding:0;background:#f7f5f4;font-family:'Segoe UI',Arial,sans-serif;color:#2b2528;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:28px 14px;background:#f7f5f4;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:580px;background:#ffffff;border:1px solid #ecd9dd;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 26px;background:#6B0D1B;color:#ffffff;">
                            <div style="font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:#C9A96A;font-weight:700;">SIMAHAD</div>
                            <div style="margin-top:5px;font-size:18px;font-weight:700;">{{ $judul }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:26px;">
                            <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Assalamu&rsquo;alaikum, <strong>{{ $penerima->name }}</strong>.</p>
                            <p style="margin:0 0 20px;font-size:14px;line-height:1.7;">{{ $pesan }}</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #ecd9dd;border-radius:10px;background:#fdfbfb;">
                                <tr>
                                    <td style="padding:10px 14px;border-bottom:1px solid #f0e6e8;font-size:12px;color:#6d6266;width:34%;">Jenis Pengajuan</td>
                                    <td style="padding:10px 14px;border-bottom:1px solid #f0e6e8;font-size:13px;font-weight:700;">{{ $pengajuan->labelJenis() }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px;border-bottom:1px solid #f0e6e8;font-size:12px;color:#6d6266;">Status</td>
                                    <td style="padding:10px 14px;border-bottom:1px solid #f0e6e8;font-size:13px;font-weight:700;color:#6B0D1B;">{{ $pengajuan->labelStatus() }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px;border-bottom:1px solid #f0e6e8;font-size:12px;color:#6d6266;">Periode</td>
                                    <td style="padding:10px 14px;border-bottom:1px solid #f0e6e8;font-size:13px;">{{ $pengajuan->periodeTeks() }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px;font-size:12px;color:#6d6266;">Tujuan / Lokasi</td>
                                    <td style="padding:10px 14px;font-size:13px;">{{ $pengajuan->tujuanTeks() }}</td>
                                </tr>
                                @if ($pengajuan->nomor_surat)
                                    <tr>
                                        <td style="padding:10px 14px;border-top:1px solid #f0e6e8;font-size:12px;color:#6d6266;">Nomor Surat</td>
                                        <td style="padding:10px 14px;border-top:1px solid #f0e6e8;font-size:13px;">{{ $pengajuan->nomor_surat }}</td>
                                    </tr>
                                @endif
                            </table>

                            @if ($catatan)
                                <div style="margin-top:18px;padding:14px 16px;border:1px solid #ecd9dd;border-radius:10px;background:#F8EDEF;font-size:13px;line-height:1.7;color:#6B0D1B;">
                                    <strong>Catatan:</strong><br>{{ $catatan }}
                                </div>
                            @endif

                            <p style="margin:24px 0 18px;text-align:center;">
                                <a href="{{ $url }}"
                                   style="display:inline-block;padding:13px 26px;border-radius:9px;background:#6B0D1B;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;">
                                    {{ $labelTombol }}
                                </a>
                            </p>
                            <p style="margin:0;font-size:12px;line-height:1.7;color:#6d6266;">
                                Bila tombol tidak berfungsi, masuk ke SIMAHAD dan buka menu Surat &amp; Perizinan.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 26px;border-top:1px solid #f0e6e8;background:#fdfbfb;font-size:11px;line-height:1.7;color:#8a8085;">
                            Email ini dikirim otomatis oleh SIMAHAD. Silakan jangan membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
