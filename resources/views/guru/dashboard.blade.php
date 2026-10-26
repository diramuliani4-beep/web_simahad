
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - SIMAHAD</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f3f6f4; color: #24352c; }
        header { background: #176b45; color: white; padding: 22px 7%; }
        header h1 { margin: 0 0 6px; font-size: 24px; }
        main { max-width: 1000px; margin: 35px auto; padding: 0 20px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 3px 12px #0000000d; margin-bottom: 20px; }
        .menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .menu .card { margin: 0; }
        button { background: #176b45; color: white; border: 0; padding: 10px 16px; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <header>
        <h1>SIMAHAD</h1>
        <p>Dashboard Guru</p>
    </header>

    <main>
        <section class="card">
            <h2>Selamat Datang, Guru!</h2>
            <p>Halaman utama untuk mendukung kegiatan pembelajaran asrama.</p>
        </section>

        <section class="menu">
            <div class="card">
                <h3>Pembelajaran</h3>
                <p>Informasi kegiatan pembelajaran.</p>
            </div>
            <div class="card">
                <h3>Absensi</h3>
                <p>Informasi kehadiran mahasiswa.</p>
            </div>
            <div class="card">
                <h3>Pengumuman</h3>
                <p>Informasi dan pengumuman pembelajaran.</p>
            </div>
        </section>

        <form method="POST" action="{{ route('logout') }}" style="margin-top:24px;">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </main>
</body>
</html>