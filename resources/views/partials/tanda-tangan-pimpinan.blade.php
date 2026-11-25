<style>
    .laporan-ttd {
        width: 72mm;
        margin: 9mm 0 0 auto;
        color: #000;
        font-family: "Times New Roman", Times, serif;
        font-size: 10.5pt;
        line-height: 1.35;
        text-align: center;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .laporan-ttd p { margin: 0; }
    .laporan-ttd-ruang { height: 23mm; }
    .laporan-ttd-nama {
        display: inline-block;
        min-width: 58mm;
        border-bottom: 1px solid #000;
        font-weight: 700;
    }
    .laporan-ttd-nip { margin-top: 1mm !important; }

    @media screen and (max-width: 760px) {
        .laporan-ttd { width: 65mm; }
    }
</style>

<section class="laporan-ttd" aria-label="Tanda tangan pimpinan">
    <p>Padang, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
    <p>Kepala Ma’had Al-Jami’ah</p>
    <div class="laporan-ttd-ruang" aria-hidden="true"></div>
    <p class="laporan-ttd-nama">{{ $pimpinan['nama'] }}</p>
    <p class="laporan-ttd-nip">NIP. {{ $pimpinan['nip'] }}</p>
</section>
