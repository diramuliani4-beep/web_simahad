@extends('layouts.admin')

@section('title', 'Edit Data Pegawai')

@push('styles')
<style>
    .pgform, .pgform * { box-sizing: border-box; }
    .pgform { --wine:#780d20; --line:#e9dfe1; --soft:#fdf7f8; --muted:#777077; color:#252125; width:100%; min-width:0; }
    .pgform a { text-decoration:none; }
    .pgform-head { margin-bottom:24px; padding-bottom:17px; border-bottom:1px solid #e9d4d8; }
    .pgform-crumb { color:#8c8587; font-size:12px; margin-bottom:11px; }
    .pgform-crumb a { color:var(--wine); }
    .pgform-title { margin:0 0 6px; color:#282428; font-weight:700; font-size:clamp(22px,2.2vw,27px); line-height:1.35; }
    .pgform-desc { margin:0; color:var(--muted); font-size:13px; line-height:1.7; }
    .pgform-card { overflow:hidden; background:#fff; border:1px solid var(--line); border-radius:14px; box-shadow:0 7px 22px rgba(61,14,28,.04); }
    .pgform-card-header { display:flex; align-items:center; gap:13px; padding:21px 25px; border-bottom:1px solid #f0e8eb; }
    .pgform-head-icon { display:grid; place-items:center; width:45px; height:45px; border-radius:11px; background:#f8edef; color:var(--wine); flex-shrink:0; }
    .pgform-head-icon svg { width:24px; height:24px; }
    .pgform-card-header h2 { margin:0 0 4px; font-size:16px; font-weight:700; }
    .pgform-card-header p { margin:0; color:#847c80; font-size:12px; line-height:1.55; }
    .pgform-body { padding:24px 25px 8px; }
    .pgform-section { margin-bottom:29px; }
    .pgform-section h3 { display:flex; align-items:center; gap:10px; padding:0 0 13px; margin:0 0 18px; border-bottom:1px solid #f0e7e9; font-size:14px; font-weight:700; color:var(--wine); }
    .pgform-step { flex-shrink:0; width:26px; height:26px; display:grid; place-items:center; border-radius:8px; background:#f8edef; font-size:12px; }
    .pgform-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:19px 21px; }
    .pgform-field { min-width:0; }
    .pgform-field.full { grid-column:1 / -1; }
    .pgform-label { display:block; margin:0 0 7px; font-size:12px; font-weight:600; color:#3b3438; }
    .pgform-required { color:#bd2946; }
    .pgform-input { width:100%; min-height:46px; padding:11px 13px; border:1px solid #e1d6da; border-radius:8px; background:#fff; font:13px 'Poppins',sans-serif; color:#2a2729; outline:none; transition:border-color .15s, box-shadow .15s; }
    .pgform-input:focus { border-color:#9d5263; box-shadow:0 0 0 3px rgba(120,13,32,.08); }
    .pgform-input[readonly] { background:#f7f5f6; color:#615d60; }
    .pgform-input.invalid { border-color:#bc3046; }
    textarea.pgform-input { resize:vertical; min-height:110px; }
    .pgform-help { margin:7px 0 0; color:#8a8185; font-size:11px; line-height:1.55; }
    .pgform-error { display:block; margin:5px 0 0; color:#b72741; font-size:11px; }
    .pgform-note { display:flex; gap:10px; align-items:flex-start; margin:0 0 21px; padding:13px 15px; border:1px solid #edccd3; border-radius:9px; background:#fbf2f4; color:#6f414d; font-size:12px; line-height:1.75; }
    .pgform-note svg { width:18px; height:18px; flex-shrink:0; margin-top:2px; }
    .pgform-alert { margin-bottom:19px; padding:14px 16px; border-radius:9px; background:#fff0f3; color:#a3233e; border:1px solid #edc2ca; font-size:12px; }
    .pgform-alert ul { padding-left:18px; margin:8px 0 0; }
    .pgform-photo-row { display:flex; align-items:center; gap:20px; flex-wrap:wrap; }
    .pgform-preview { width:110px; height:110px; display:grid; place-items:center; border-radius:12px; border:1px solid #e9cad2; background:#f9edf0; color:var(--wine); overflow:hidden; flex-shrink:0; }
    .pgform-preview img { width:100%; height:100%; object-fit:cover; }
    .pgform-preview svg { width:48px; height:48px; }
    .pgform-photo-upload { min-width:190px; flex:1; }
    .pgform-photo-upload input[type=file] { width:min(100%,460px); padding:11px; background:#fffbfc; border:1px dashed #d8b2bc; border-radius:9px; font-size:12px; font-family:inherit; }
    .pgform-foot { display:flex; justify-content:flex-end; flex-wrap:wrap; gap:10px; padding:19px 25px; border-top:1px solid #f0e7e9; background:#fff; }
    .pgform-button { min-height:43px; padding:10px 20px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; font:600 12px 'Poppins',sans-serif; cursor:pointer; text-decoration:none; }
    .pgform-cancel { border:1px solid #e2d9db; color:#544b4e; background:#fff; }
    .pgform-cancel:hover { background:#f6f0f2; color:var(--wine); }
    .pgform-save { border:1px solid var(--wine); color:#fff; background:var(--wine); }
    .pgform-save:hover { background:#5a0b1b; }
    .pgform-save:disabled { opacity:.65; cursor:wait; }
    .pgform-inline-action { display:inline-flex; align-items:center; margin-top:7px; border:0; padding:0; color:var(--wine); background:transparent; font:600 12px 'Poppins',sans-serif; text-decoration:underline; text-underline-offset:3px; cursor:pointer; }
    .pgform-inline-action:hover { color:#a52b44; }
    .pgform-modal { width:min(440px,calc(100% - 32px)); padding:0; border:1px solid #ead6db; border-radius:15px; color:#30272c; box-shadow:0 24px 80px rgba(30,8,14,.25); }
    .pgform-modal::backdrop { background:rgba(28,13,17,.55); }
    .pgform-modal-inner { padding:26px; text-align:center; }
    .pgform-modal-icon { width:48px; height:48px; display:grid; place-items:center; margin:0 auto 12px; border-radius:50%; background:#f9edf0; color:var(--wine); }
    .pgform-modal-icon svg { width:24px; height:24px; }
    .pgform-modal h2 { margin:0 0 9px; font-size:18px; font-weight:700; }
    .pgform-modal p { margin:0 0 20px; color:#73696e; font-size:13px; line-height:1.7; }
    .pgform-modal button { width:100%; min-height:42px; border:0; border-radius:8px; background:var(--wine); color:#fff; font:600 12px 'Poppins',sans-serif; cursor:pointer; }
    @media (max-width:700px) {
        .pgform-grid { grid-template-columns:1fr; }
        .pgform-field.full { grid-column:auto; }
        .pgform-card-header,.pgform-body,.pgform-foot { padding-left:16px; padding-right:16px; }
        .pgform-button { flex:1; }
    }
</style>
@endpush

@section('content')
<div class="pgform">
    <header class="pgform-head">
        <nav class="pgform-crumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.data-pegawai.index') }}">Data Pegawai</a> / Edit Data Pegawai
        </nav>
        <h1 class="pgform-title">Edit Data Pegawai</h1>
        <p class="pgform-desc">Perbarui biodata pegawai yang sudah tersimpan. Tidak perlu memilih akun atau role.</p>
    </header>

    @php
        // Cek tautan yang sesungguhnya di tabel pegawai_akun, bukan kolom user_id lama.
        // Controller mengunci email di server dengan aturan yang sama.
        $emailTerhubung = $pegawai->akunLink()->exists();
    @endphp

    @if($errors->any())
        <div class="pgform-alert" role="alert">
            <strong>Data belum berhasil disimpan. Periksa isian berikut:</strong>
            <ul>@foreach($errors->all() as $pesan)<li>{{ $pesan }}</li>@endforeach</ul>
        </div>
    @endif

    <form id="formPegawai" action="{{ route('admin.data-pegawai.update', $pegawai->getKey()) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="pgform-card">
            <div class="pgform-card-header">
                <div class="pgform-head-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 4H6a2 2 0 0 0-2 2v14h16V9z"/><path d="M14 4v5h6M8 13h8M8 17h6"/>
                    </svg>
                </div>
                <div>
                    <h2>Formulir Biodata Pegawai</h2>
                    <p>Informasi kepegawaian tersimpan pada tabel pegawais dan tidak menentukan izin login SIMAHAD.</p>
                </div>
            </div>

            <div class="pgform-body">
                <section class="pgform-section">
                    <h3><span class="pgform-step">01</span> Identitas Pegawai</h3>
                    <div class="pgform-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="10"/><path d="M12 11v6M12 7h.01"/></svg>
                        <div><strong>Nama dapat diubah.</strong> Jika pegawai sudah memiliki akun, nama akan ikut diperbarui pada Manajemen Pengguna. <strong>Email dapat diubah selama belum terhubung dengan akun login.</strong> Status pegawai tidak memengaruhi akses login.</div>
                    </div>
                    <div class="pgform-grid">
                        <div class="pgform-field full">
                            <label class="pgform-label" for="nama_pegawai">Nama lengkap <span class="pgform-required">*</span></label>
                            <input id="nama_pegawai" name="nama_pegawai" type="text" required maxlength="150" minlength="2"
                                   class="pgform-input @error('nama_pegawai') invalid @enderror"
                                   value="{{ old('nama_pegawai', $pegawai->nama_pegawai) }}" placeholder="Contoh: Siti Aminah" autocomplete="name">
                            @error('nama_pegawai')<span class="pgform-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pgform-field">
                            <label class="pgform-label" for="email">Email <span class="pgform-required">*</span></label>
                            @if ($emailTerhubung)
                                <input id="email" type="email" readonly aria-readonly="true" class="pgform-input"
                                       value="{{ $pegawai->user?->email ?? $pegawai->email }}">
                                <p class="pgform-help">Email sudah menjadi identitas akun SIMAHAD dan tidak dapat diubah dari Data Pegawai.</p>
                                <button type="button" class="pgform-inline-action" id="mintaUbahEmail"
                                        aria-haspopup="dialog" aria-controls="dialogEmail">Ingin mengubah email?</button>
                            @else
                                <input id="email" name="email" type="email" required maxlength="255"
                                       class="pgform-input @error('email') invalid @enderror"
                                       value="{{ old('email', $pegawai->email) }}" placeholder="pegawai@contoh.com" autocomplete="email">
                                <p class="pgform-help">Belum punya akun login, jadi email biodata masih dapat diubah.</p>
                            @endif
                            @error('email')<span class="pgform-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pgform-field">
                            <label class="pgform-label" for="no_hp">Nomor HP</label>
                            <input id="no_hp" name="no_hp" type="tel" maxlength="20" class="pgform-input @error('no_hp') invalid @enderror"
                                   value="{{ old('no_hp', $pegawai->no_hp ?? $pegawai->user?->no_hp) }}" placeholder="08xxxxxxxxxx" autocomplete="tel">
                            @error('no_hp')<span class="pgform-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pgform-field">
                            <label class="pgform-label" for="jenis_kelamin">Jenis kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="pgform-input @error('jenis_kelamin') invalid @enderror">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="L" @selected(old('jenis_kelamin', $pegawai->jenis_kelamin) === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('jenis_kelamin', $pegawai->jenis_kelamin) === 'P')>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')<span class="pgform-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pgform-field">
                            <label class="pgform-label" for="pendidikan_terakhir">Pendidikan terakhir</label>
                            <input id="pendidikan_terakhir" name="pendidikan_terakhir" type="text" maxlength="100"
                                   class="pgform-input @error('pendidikan_terakhir') invalid @enderror"
                                   value="{{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) }}" placeholder="Contoh: S1 Pendidikan Agama Islam">
                            @error('pendidikan_terakhir')<span class="pgform-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pgform-field full">
                            <label class="pgform-label" for="alamat">Alamat lengkap</label>
                            <textarea id="alamat" name="alamat" rows="4" maxlength="3000" class="pgform-input @error('alamat') invalid @enderror"
                                      placeholder="Masukkan alamat tempat tinggal pegawai">{{ old('alamat', $pegawai->alamat) }}</textarea>
                            @error('alamat')<span class="pgform-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </section>

                <section class="pgform-section">
                    <h3><span class="pgform-step">02</span> Foto Pegawai</h3>
                    <div class="pgform-photo-row">
                        <div class="pgform-preview" id="previewFoto" aria-label="Pratinjau foto pegawai">
                            @if ($pegawai->foto && \Illuminate\Support\Facades\Route::has('admin.data-pegawai.foto'))
                                <img src="{{ route('admin.data-pegawai.foto', $pegawai->getKey()) }}" alt="Foto {{ $pegawai->nama_pegawai }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <span style="display:none"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-5 3-8 8-8s8 3 8 8"/></svg></span>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-5 3-8 8-8s8 3 8 8"/></svg>
                            @endif
                        </div>
                        <div class="pgform-photo-upload">
                            <label class="pgform-label" for="foto">Ganti foto (opsional)</label>
                            <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp">
                            <p class="pgform-help">JPG, PNG, atau WEBP. Maksimal 2 MB. Kosongkan untuk mempertahankan foto lama.</p>
                            @error('foto')<span class="pgform-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </section>

                <section class="pgform-section">
                    <h3><span class="pgform-step">03</span> Status Kepegawaian</h3>
                    <div class="pgform-grid">
                        <div class="pgform-field">
                            <label class="pgform-label" for="status_pegawai">Status pegawai <span class="pgform-required">*</span></label>
                            <select id="status_pegawai" name="status_pegawai" required class="pgform-input @error('status_pegawai') invalid @enderror">
                                <option value="1" @selected((string) old('status_pegawai', (int) $pegawai->status_pegawai) === '1')>Aktif</option>
                                <option value="0" @selected((string) old('status_pegawai', (int) $pegawai->status_pegawai) === '0')>Nonaktif</option>
                            </select>
                            <p class="pgform-help">Status data pegawai saja. Tidak memberi atau mencabut akses login.</p>
                            @error('status_pegawai')<span class="pgform-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pgform-field">
                            <div class="pgform-note" style="height:100%;margin:0">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="10"/><path d="M12 11v6M12 7h.01"/></svg>
                                <div>Role, password, dan status akun login dikelola secara terpisah di <strong>Manajemen Pengguna</strong>.</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="pgform-foot">
                <a class="pgform-button pgform-cancel" href="{{ route('admin.data-pegawai.index') }}">Batal</a>
                <button class="pgform-button pgform-save" type="submit" id="tombolSimpan">Simpan Perubahan</button>
            </div>
        </div>
    </form>

    @if ($emailTerhubung)
        <dialog class="pgform-modal" id="dialogEmail" aria-labelledby="dialogEmailJudul" aria-describedby="dialogEmailPesan">
            <div class="pgform-modal-inner">
                <div class="pgform-modal-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                </div>
                <h2 id="dialogEmailJudul">Perubahan Email Akun</h2>
                <p id="dialogEmailPesan">Email pegawai sudah terhubung dengan akun SIMAHAD. <strong>Untuk mengubah email, silakan hubungi Admin.</strong></p>
                <form method="dialog"><button type="submit">Mengerti</button></form>
            </div>
        </dialog>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fotoInput = document.getElementById('foto');
    const preview = document.getElementById('previewFoto');
    const form = document.getElementById('formPegawai');
    const tombolSimpan = document.getElementById('tombolSimpan');
    const tombolEmail = document.getElementById('mintaUbahEmail');
    const dialogEmail = document.getElementById('dialogEmail');
    if (tombolEmail && dialogEmail) {
        tombolEmail.addEventListener('click', function () {
            dialogEmail.showModal();
        });
    }
    if (!fotoInput || !preview) return;

    const fotoAwal = preview.innerHTML;
    let previewUrl = null;

    fotoInput.addEventListener('change', function () {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = null;
        const file = fotoInput.files && fotoInput.files[0];
        if (!file) { preview.innerHTML = fotoAwal; return; }
        const formats = ['image/jpeg', 'image/png', 'image/webp'];
        if (!formats.includes(file.type) || file.size > 2 * 1024 * 1024) {
            fotoInput.value = '';
            preview.innerHTML = fotoAwal;
            alert('Gunakan foto JPG, PNG, atau WEBP berukuran maksimal 2 MB.');
            return;
        }
        previewUrl = URL.createObjectURL(file);
        const img = document.createElement('img');
        img.src = previewUrl;
        img.alt = 'Pratinjau foto pegawai';
        preview.replaceChildren(img);
    });

    if (form && tombolSimpan) {
        form.addEventListener('submit', function () {
            if (!form.checkValidity()) return;
            tombolSimpan.disabled = true;
            tombolSimpan.textContent = 'Menyimpan...';
        });
    }
    window.addEventListener('pagehide', function () {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
    });
});
</script>
@endpush
