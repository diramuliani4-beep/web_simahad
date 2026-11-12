@props(['pesan' => 'Belum ada data.', 'petunjuk' => null])

{{-- Kondisi kosong informatif, tanpa angka contoh. --}}
<div class="dash-kosong" role="status">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M3 15h4l2 3h6l2-3h4" />
        <path d="M5.5 5h13l2.5 10v3.5A1.5 1.5 0 0 1 19.5 20h-15A1.5 1.5 0 0 1 3 18.5V15z" />
    </svg>

    <span>{{ $pesan }}</span>

    @if ($petunjuk)
        <span class="dash-baris-ket">{{ $petunjuk }}</span>
    @endif
</div>
