<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $laporan['judul'] ?? 'Laporan' }} - {{ $laporan['periode_label'] ?? '' }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            background: #f4f1f2;
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .cetak-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            max-width: 1080px;
            margin: 0 auto 14px;
        }

        .cetak-toolbar span {
            color: #6f636a;
            font-size: 12.5px;
        }

        .cetak-toolbar div {
            display: flex;
            gap: 8px;
        }

        .cetak-btn {
            padding: 9px 15px;
            border: 1px solid #780d20;
            border-radius: 8px;
            background: #780d20;
            color: #fff;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .cetak-btn.cetak-btn-outline {
            background: #fff;
            color: #780d20;
        }

        .cetak-kertas {
            max-width: 1080px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            box-shadow: 0 4px 18px rgba(40, 20, 30, .08);
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .cetak-toolbar {
                display: none !important;
            }

            .cetak-kertas {
                max-width: none;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="cetak-toolbar">
        <span>
            {{ $laporan['judul'] ?? 'Laporan' }} · Periode {{ $laporan['periode_label'] ?? '-' }}
        </span>

        <div>
            <button type="button" class="cetak-btn" onclick="window.print()">Cetak Dokumen</button>
            <a href="javascript:history.back()" class="cetak-btn cetak-btn-outline">Kembali</a>
        </div>
    </div>

    <div class="cetak-kertas">
        @include('partials.laporan-periodik', [
            'laporan' => $laporan,
            'meta' => $meta ?? [],
        ])
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
