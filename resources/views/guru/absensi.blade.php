
@extends(request()->is('musyrifah/*') ? 'layouts.musyrifah' : 'layouts.guru')

@section('content')
<style>
    .absensi-page {
        padding: 20px 28px;
        color: #222;
        font-family: inherit;
    }

    .absensi-breadcrumb {
        color: #790d20;
        font-size: 22px;
        font-weight: 500;
        margin-bottom: 14px;
    }

    .absensi-title {
        font-size: 32px;
        font-weight: 600;
        margin: 0 0 7px;
    }

    .absensi-description {
        font-size: 20px;
        margin: 0;
    }

    .absensi-content {
        border-top: 1px solid #e8dcd7;
        margin: 28px -28px 0;
        padding: 14px 28px 25px;
        background: #f8f7f6;
    }

    .absensi-info-card,
    .absensi-card {
        background: #fff;
        border: 1px solid #eee3de;
        border-radius: 10px;
        margin-bottom: 14px;
    }

    .absensi-info-card {
        padding: 17px 15px;
    }

    .absensi-label {
        color: #85817f;
        font-size: 14px;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .absensi-mapel {
        font-size: 19px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .absensi-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .absensi-info-caption {
        color: #85817f;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .absensi-info-value {
        font-size: 18px;
        font-weight: 600;
    }

    .absensi-date {
        color: #85817f;
        font-size: 14px;
        margin-top: 17px;
    }

    .absensi-card {
        padding: 19px 15px;
    }

    .absensi-section-title {
        color: #790d20;
        font-size: 19px;
        font-weight: 600;
        margin: 0 0 14px;
    }

    .absensi-hint {
        color: #85817f;
        font-size: 14px;
        margin-bottom: 17px;
    }

    .absensi-table-wrapper {
        overflow-x: auto;
    }

    .absensi-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 450px;
    }

    .absensi-table th,
    .absensi-table td {
        text-align: left;
        padding: 12px 5px;
        border-bottom: 1px solid #eee;
        font-size: 15px;
    }

    .absensi-table th {
        font-size: 16px;
        font-weight: 600;
    }

    .absensi-select {
        min-width: 120px;
        padding: 7px 10px;
        border: 1px solid #e5ddd8;
        border-radius: 7px;
        background: #fff;
        font: inherit;
        font-size: 14px;
    }

    .absensi-rekap-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin: 22px 0;
        font-size: 17px;
    }

    .rekap-label {
        color: #85817f;
    }

    .rekap-hadir {
        color: #4d9c75;
    }

    .rekap-izin {
        color: #d59a35;
    }

    .rekap-alpa {
        color: #d56d6d;
    }

    .rekap-value {
        font-weight: 600;
        text-align: right;
    }

    .absensi-total-tidak-hadir {
        border-top: 1px solid #e8dcd7;
        margin-top: 25px;
        padding-top: 24px;
        color: #85817f;
        font-size: 16px;
    }

    .absensi-save {
        width: 100%;
        padding: 16px;
        border: 1px solid #790d20;
        border-radius: 9px;
        background: #790d20;
        color: #fff;
        font-size: 17px;
        font-weight: 600;
        cursor: pointer;
    }

    .absensi-save:hover {
        background: #5f0a19;
    }

    @media (max-width: 768px) {
        .absensi-page {
            padding: 16px;
        }

        .absensi-breadcrumb {
            font-size: 18px;
        }

        .absensi-title {
            font-size: 27px;
        }

        .absensi-description {
            font-size: 16px;
        }

        .absensi-content {
            margin-left: -16px;
            margin-right: -16px;
            padding: 14px 16px 22px;
        }

        .absensi-info-grid {
            gap: 10px;
        }

        .absensi-rekap-row {
            font-size: 15px;
        }
    }
</style>

<div class="absensi-page">
    <div class="absensi-breadcrumb">
        Dashboard / Absensi
    </div>

    <h1 class="absensi-title">Absensi</h1>

    <p class="absensi-description">
        Catat dan lihat riwayat kehadiran mahasiswa pada kelompok yang kamu ajar.
    </p>

    <div class="absensi-content">

        {{-- INFORMASI PEMBELAJARAN --}}
        <div class="absensi-info-card">
            <div class="absensi-label">Mata Pelajaran</div>

            <div class="absensi-mapel">Akhlak dan Adab</div>

            <div class="absensi-info-grid">
                <div>
                    <div class="absensi-info-caption">Kelompok</div>
                    <div class="absensi-info-value">Kelompok A</div>
                </div>

                <div>
                    <div class="absensi-info-caption">Pertemuan</div>
                    <div class="absensi-info-value">Ke-9 dari 16</div>
                </div>
            </div>

            <div class="absensi-date">
                Tanggal: 12 Oktober 2026 (contoh)
            </div>
        </div>

        {{-- DAFTAR KEHADIRAN --}}
        <div class="absensi-card">
            <h2 class="absensi-section-title">Daftar Kehadiran</h2>

            <p class="absensi-hint">
                Pilih status setiap mahasiswa.
            </p>

            <div class="absensi-table-wrapper">
                <table class="absensi-table">
                    <thead>
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody id="daftar-absensi">
                        <tr>
                            <td>1. Alya Putri</td>
                            <td>
                                <select class="absensi-select status-absensi">
                                    <option value="Hadir" selected>Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Alpa">Alpa</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>2. Aisyah Rahma</td>
                            <td>
                                <select class="absensi-select status-absensi">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Izin" selected>Izin</option>
                                    <option value="Alpa">Alpa</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>3. Nurul Fadillah</td>
                            <td>
                                <select class="absensi-select status-absensi">
                                    <option value="Hadir" selected>Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Alpa">Alpa</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>4. Siti Rahmi</td>
                            <td>
                                <select class="absensi-select status-absensi">
                                    <option value="Hadir" selected>Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Alpa">Alpa</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>5. Putri Amelia</td>
                            <td>
                                <select class="absensi-select status-absensi">
                                    <option value="Hadir" selected>Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Alpa">Alpa</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- REKAP KEHADIRAN --}}
        <div class="absensi-card">
            <h2 class="absensi-section-title">Rekap Kehadiran</h2>

            <div class="absensi-rekap-row">
                <span class="rekap-label">Total mahasiswa</span>
                <span class="rekap-value" id="total-mahasiswa">5 orang</span>
            </div>

            <div class="absensi-rekap-row">
                <span class="rekap-label rekap-hadir">Hadir</span>
                <span class="rekap-value" id="jumlah-hadir">4 mahasiswa</span>
            </div>

            <div class="absensi-rekap-row">
                <span class="rekap-label rekap-izin">Izin</span>
                <span class="rekap-value" id="jumlah-izin">1 mahasiswa</span>
            </div>

            <div class="absensi-rekap-row">
                <span class="rekap-label rekap-alpa">Alpa</span>
                <span class="rekap-value" id="jumlah-alpa">0 mahasiswa</span>
            </div>

            <div class="absensi-total-tidak-hadir" id="total-tidak-hadir">
                Total tidak hadir: 1 mahasiswa (1 izin dan 0 alpa).
            </div>
        </div>

        <button type="button" class="absensi-save" id="tombol-simpan-absensi">
            Simpan Absensi
        </button>

        <p id="pesan-absensi" style="display:none; margin-top:12px; color:#790d20;"></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const daftar = document.getElementById('daftar-absensi');
    const statusSelect = daftar.querySelectorAll('.status-absensi');

    function hitungRekap() {
        let hadir = 0;
        let izin = 0;
        let alpa = 0;

        statusSelect.forEach(function (select) {
            if (select.value === 'Hadir') hadir++;
            if (select.value === 'Izin') izin++;
            if (select.value === 'Alpa') alpa++;
        });

        const total = statusSelect.length;

        document.getElementById('total-mahasiswa').textContent =
            total + ' orang';

        document.getElementById('jumlah-hadir').textContent =
            hadir + ' mahasiswa';

        document.getElementById('jumlah-izin').textContent =
            izin + ' mahasiswa';

        document.getElementById('jumlah-alpa').textContent =
            alpa + ' mahasiswa';

        document.getElementById('total-tidak-hadir').textContent =
            'Total tidak hadir: ' + (izin + alpa) +
            ' mahasiswa (' + izin + ' izin dan ' + alpa + ' alpa).';
    }

    statusSelect.forEach(function (select) {
        select.addEventListener('change', hitungRekap);
    });

    hitungRekap();

    document.getElementById('tombol-simpan-absensi')
        .addEventListener('click', function () {
            const pesan = document.getElementById('pesan-absensi');
            pesan.style.display = 'block';
            pesan.textContent =
                'Pilihan absensi sudah dihitung. Penyimpanan permanen perlu dihubungkan ke database.';
        });
});
</script>
@endsection