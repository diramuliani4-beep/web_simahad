@props(['nama' => 'dashboard'])

{{-- Satu sumber ikon sidebar agar ukuran dan gaya seluruh role identik. --}}
<svg {{ $attributes->merge(['class' => 'ikon-menu', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
    @switch($nama)
        @case('dashboard')
            <rect x="3" y="3" width="7" height="7" rx="1.6" />
            <rect x="14" y="3" width="7" height="7" rx="1.6" />
            <rect x="14" y="14" width="7" height="7" rx="1.6" />
            <rect x="3" y="14" width="7" height="7" rx="1.6" />
            @break

        @case('pendaftaran')
            <path d="M11 4H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2v-6" />
            <path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4z" />
            @break

        @case('pembelajaran')
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
            @break

        @case('tugas')
            <rect x="4" y="3" width="16" height="18" rx="2" />
            <path d="M8 8h8M8 12h8M8 16h5" />
            @break

        @case('kamar')
            <path d="M3 4v16" />
            <path d="M3 9h15a3 3 0 0 1 3 3v8" />
            <path d="M3 17h18" />
            <path d="M7 9V6" />
            @break

        @case('pembayaran')
            <rect x="2" y="5" width="20" height="14" rx="2" />
            <path d="M2 10h20" />
            <path d="M6 15h4" />
            @break

        @case('absensi')
            <rect x="8" y="2" width="8" height="4" rx="1" />
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
            <path d="m9 14 2 2 4-4" />
            @break

        @case('surat')
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <path d="M14 2v6h6M16 13H8M16 17H8" />
            @break

        @case('pengumuman')
            <path d="m3 11 18-5v12L3 14z" />
            <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6" />
            @break

        @case('jadwal')
            <rect x="3" y="4" width="18" height="17" rx="2" />
            <path d="M16 2v4M8 2v4M3 10h18" />
            @break

        @case('kegiatan')
            <rect x="2" y="6" width="20" height="14" rx="2" />
            <circle cx="12" cy="13" r="3.5" />
            <path d="M8 6l1.5-2h5L16 6" />
            @break

        @case('pengguna')
            <circle cx="9" cy="8" r="3.2" />
            <path d="M3 20v-1.8A5.2 5.2 0 0 1 8.2 13h1.6A5.2 5.2 0 0 1 15 18.2V20" />
            <circle cx="18" cy="9" r="2.2" />
            <path d="M17 15.2A4.5 4.5 0 0 1 21 19.6V20" />
            @break

        @case('pegawai')
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <circle cx="9" cy="10" r="2.2" />
            <path d="M6 16c0-1.9 6-1.9 6 0M15 9h3M15 13h3" />
            @break

        @case('mahasiswa')
            <path d="m2 10 10-5 10 5-10 5z" />
            <path d="M6 12v5c4 3 8 3 12 0v-5" />
            <path d="M22 10v7" />
            @break

        @case('log')
            <path d="M3 12a9 9 0 1 0 3-6.7" />
            <path d="M3 4v5h5" />
            <path d="M12 7v5l3 2" />
            @break

        @case('laporan')
            <path d="M4 20V6M10 20V10M16 20V4M22 20H2" />
            @break

        @case('dokumentasi')
            <path d="M4 8h2.5l1.8-2.2A1.5 1.5 0 0 1 9.5 5h5a1.5 1.5 0 0 1 1.2.6L17.5 8H20a1.5 1.5 0 0 1 1.5 1.5V19A1.5 1.5 0 0 1 20 20.5H4A1.5 1.5 0 0 1 2.5 19V9.5A1.5 1.5 0 0 1 4 8z" />
            <circle cx="12" cy="13.5" r="3.4" />
            @break

        @case('monitoring')
            <path d="M22 12h-4l-3 8-4-16-3 8H2" />
            @break

        @case('penghuni')
            <path d="M3 21V8l9-5 9 5v13" />
            <path d="M9 21v-6h6v6" />
            <path d="M9 10h.01M15 10h.01" />
            @break

        @case('sistem')
            <circle cx="12" cy="12" r="3" />
            <path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1" />
            @break

        @case('pengaturan')
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 0 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 0 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 0 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 0 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 0 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z" />
            @break

        @case('keluar')
            <path d="M10 17l5-5-5-5" />
            <path d="M15 12H3" />
            <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7" />
            @break

        @case('profil')
            <circle cx="12" cy="8" r="4" />
            <path d="M4 21v-1.5A6.5 6.5 0 0 1 10.5 13h3A6.5 6.5 0 0 1 20 19.5V21" />
            @break

        @case('data')
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <path d="M3 10h18M9 10v10" />
            @break

        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>
