<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun SIMAHAD</title>
</head>
<body style="margin:0;padding:0;background:#f7f5f4;font-family:'Segoe UI',Arial,sans-serif;color:#2b2528;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:28px 14px;background:#f7f5f4;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:540px;background:#ffffff;border:1px solid #ecd9dd;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 26px;background:#6B0D1B;color:#ffffff;">
                            <div style="font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:#C9A96A;font-weight:700;">SIMAHAD</div>
                            <div style="margin-top:5px;font-size:18px;font-weight:700;">Aktivasi Akun {{ $pengguna->labelPeran() }}</div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:26px;">
                            <p style="margin:0 0 12px;font-size:14px;line-height:1.7;">Assalamu&rsquo;alaikum, <strong>{{ $pengguna->name }}</strong>.</p>
                            <p style="margin:0 0 12px;font-size:14px;line-height:1.7;">
                                Akun SIMAHAD Anda sudah dibuat oleh Admin Sistem. Demi keamanan, Admin tidak membuatkan
                                kata sandi. Silakan tentukan kata sandi Anda sendiri melalui tombol berikut.
                            </p>

                            <p style="margin:22px 0;text-align:center;">
                                <a href="{{ $tautan }}"
                                   style="display:inline-block;padding:13px 26px;border-radius:9px;background:#6B0D1B;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;">
                                    Tentukan Kata Sandi
                                </a>
                            </p>

                            <p style="margin:0 0 10px;font-size:13px;line-height:1.7;color:#6d6266;">
                                Tautan ini berlaku selama <strong>{{ $berlakuJam }} jam</strong> dan hanya dapat dipakai satu kali.
                                Bila sudah kedaluwarsa, minta Admin Sistem mengirim ulang tautan aktivasi.
                            </p>

                            <p style="margin:0;padding:12px 14px;border:1px solid #ecd9dd;border-radius:9px;background:#F8EDEF;font-size:12px;line-height:1.7;color:#6B0D1B;">
                                Bila tombol tidak berfungsi, salin tautan berikut ke browser:<br>
                                <span style="word-break:break-all;">{{ $tautan }}</span>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 26px;border-top:1px solid #f0e6e8;background:#fdfbfb;font-size:11px;line-height:1.7;color:#8a8085;">
                            Email ini dikirim otomatis oleh SIMAHAD. Jangan membagikan tautan aktivasi kepada siapa pun,
                            termasuk kepada Admin Sistem.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
