
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Musyrifah - SIMAHAD</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f6f4;
            color: #24352c;
        }
        header {
            background: #176b45;
            color: white;
            padding: 22px 7%;
        }
        header h1 { margin: 0 0 6px; font-size: 24px; }
        main { max-width: 1000px; margin: 35px auto; padding: 0 20px; }
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #0000000d;
            margin-bottom: 20px;
        }
        .menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .menu .card { margin: 0; }
        a { color: #176b45; text-decoration: none; font-weight: bold; }
        button {
            background: #fff;
            color: #176b45;
            border: 0;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>SIMAHAD</h1>
        <p>Dashboard Musyrifah</p>
    </header>

    <main>
        <section class="card">
            <h2>Selamat Datang, Musyrifah!</h2>
            <p>Halaman utama untuk membantu pengawasan dan pengelolaan kegiatan asrama.</p>
        </section>

        <section class="menu">
            <div class="card">
                <h3>Data Mahasiswa</h3>
                <p>Informasi mahasiswa asrama.</p>
            </div>
            <div class="card">
                <h3>Data Kamar</h3>
                <p>Informasi kamar dan penghuni asrama.</p>
            </div>
            <div class="card">
                <h3>Absensi</h3>
                <p>Informasi kehadiran mahasiswa.</p>
            </div>
        </section>

        <form method="POST" action="{{ route('logout') }}" style="margin-top: 24px;">
            @csrf
            <button type="submit" style="background:#176b45;color:white;">Keluar</button>
        </form>
    </main>
</body>
</html>