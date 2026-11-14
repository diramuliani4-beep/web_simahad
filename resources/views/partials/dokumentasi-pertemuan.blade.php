@pushOnce('styles', 'dokumentasi-pertemuan')
<style>
    .dk-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr)); gap: 16px; }
    .dk-item { margin: 0; overflow: hidden; border: 1px solid #ecedf2; border-radius: 12px; background: #fff; }
    .dk-photo { display: block; background: #f7f5f4; }
    .dk-photo img { display: block; width: 100%; height: 190px; object-fit: contain; }
    .dk-caption { padding: 14px; font-size: 13px; line-height: 1.7; color: #4f5767; overflow-wrap: anywhere; }
    .dk-caption p { margin: 0 0 8px; white-space: pre-line; }
    .dk-meta { display: block; color: #6b7280; font-size: 12px; }
    .dk-actions { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-top: 12px; }
    .dk-link, .dk-delete { font: inherit; font-size: 12px; font-weight: 600; color: #6b0d1b; text-decoration: underline; }
    .dk-delete { padding: 0; border: 0; background: none; cursor: pointer; }
    .dk-empty { padding: 24px; border: 1px dashed #ecd9dd; border-radius: 10px; color: #4f5767; font-size: 13px; line-height: 1.7; text-align: center; }
</style>
@endPushOnce

<div class="dk-grid">
    @forelse ($dokumentasi as $foto)
        <figure class="dk-item">
            <a class="dk-photo" href="{{ route('dokumentasi.foto', $foto) }}" target="_blank" rel="noopener" aria-label="Buka foto dokumentasi pertemuan {{ $pertemuan->pertemuan_ke }}">
                <img src="{{ route('dokumentasi.foto', $foto) }}" alt="{{ $foto->keterangan ?: 'Dokumentasi pertemuan '.$pertemuan->pertemuan_ke }}" loading="lazy">
            </a>
            <figcaption class="dk-caption">
                <p>{{ $foto->keterangan ?: 'Tanpa keterangan tambahan.' }}</p>
                <span class="dk-meta">Diunggah {{ $foto->guru?->name }} · {{ $foto->created_at?->format('d/m/Y H:i') }}</span>
                <div class="dk-actions">
                    <a class="dk-link" href="{{ route('dokumentasi.foto', $foto) }}" target="_blank" rel="noopener">Lihat foto penuh</a>
                    @if (($bolehKelola ?? false) && (int) $foto->guru_id === (int) auth()->id())
                        <form method="POST" action="{{ route('guru.pembelajaran.dokumentasi.destroy', [$pertemuan, $foto]) }}" onsubmit="return confirm('Hapus foto dokumentasi ini? Foto lain dan data pertemuan tetap tersimpan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dk-delete">Hapus foto</button>
                        </form>
                    @endif
                </div>
            </figcaption>
        </figure>
    @empty
        <div class="dk-empty">Belum ada foto dokumentasi pada pertemuan ini.</div>
    @endforelse
</div>
