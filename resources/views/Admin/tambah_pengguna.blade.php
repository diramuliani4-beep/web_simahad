@extends('layouts.admin')

@section('title', 'Tambah Pengguna')

@push('styles')
<style>
/* Seluruh style dibatasi agar tidak memengaruhi halaman modul lain. */
.um-create, .um-create * { box-sizing: border-box; }
.um-create { --wine:#800b22; --wine-dark:#620819; --ink:#2b2528; --muted:#80767b; --border:#eadde1; width:100%; min-width:0; color:var(--ink); font-family:inherit; }
.um-create a { text-decoration:none; }
.um-create .um-head { margin-bottom:25px; padding-bottom:19px; border-bottom:1px solid #e8cfd6; }
.um-create .um-crumb { margin-bottom:11px; color:#8e8487; font-size:12px; }
.um-create .um-crumb a { color:var(--wine); }
.um-create .um-head h1 { margin:0 0 7px; font-size:clamp(22px,2.3vw,27px); font-weight:700; }
.um-create .um-head p { margin:0; color:var(--muted); font-size:13px; line-height:1.6; }
.um-create .um-card { overflow:hidden; background:#fff; border:1px solid var(--border); border-radius:13px; box-shadow:0 4px 16px rgba(70,15,33,.04); }
.um-create .um-card-title { padding:21px 25px; border-bottom:1px solid #f0e8e9; }
.um-create .um-card-title h2 { margin:0 0 5px; font-size:16px; font-weight:700; }
.um-create .um-card-title p { margin:0; font-size:12px; color:var(--muted); }
.um-create .um-section { padding:24px 25px 4px; }
.um-create .um-section h3 { display:flex; gap:10px; align-items:center; margin:0 0 18px; padding-bottom:13px; border-bottom:1px solid #f0e6e9; color:var(--wine); font-size:14px; font-weight:700; }
.um-create .um-step { display:grid; place-items:center; width:27px; height:27px; flex-shrink:0; border-radius:8px; background:#f8edf0; color:var(--wine); font-size:12px; }
.um-create .um-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:19px 20px; margin-bottom:23px; }
.um-create .um-field { min-width:0; }
.um-create .um-wide { grid-column:1/-1; }
.um-create .um-label { display:block; margin:0 0 8px; font-size:12px; font-weight:600; color:#41363c; }
.um-create .um-required { color:#b92744; }
.um-create .um-control { display:block; width:100%; min-height:46px; padding:11px 13px; border:1px solid #dfd2d6; border-radius:8px; background:#fff; color:#2a2629; font-family:inherit; font-size:13px; outline:none; }
.um-create .um-control:focus { border-color:#9e5265; box-shadow:0 0 0 3px rgba(128,11,34,.08); }
.um-create .um-control[readonly] { background:#f7f4f5; color:#686066; cursor:not-allowed; }
.um-create .um-control.is-invalid { border-color:#b52b48; }
.um-create .um-help { margin:7px 0 0; color:#887d82; font-size:11px; line-height:1.6; }
.um-create .um-error { display:block; margin:6px 0 0; font-size:11px; color:#b62742; }
.um-create .um-note { margin-bottom:19px; padding:13px 15px; border:1px solid #edd2d9; border-radius:9px; background:#fcf4f6; color:#714454; font-size:12px; line-height:1.7; }
.um-create .um-note.neutral { border-color:#e0e4eb; background:#f7f8fa; color:#536174; }
.um-create .um-alert { margin:0 0 18px; padding:14px 16px; border:1px solid #ecc3cc; border-radius:9px; background:#fff1f3; color:#9c253d; font-size:12px; }
.um-create .um-alert ul { margin:8px 0 0; padding-left:19px; }

.um-create .um-form-foot { display:flex; justify-content:flex-end; flex-wrap:wrap; gap:11px; padding:20px 25px; border-top:1px solid #f0e8e9; }
.um-create .um-btn { display:inline-flex; justify-content:center; align-items:center; min-height:43px; padding:10px 20px; border:1px solid #e1d5d9; border-radius:8px; background:#fff; color:#564a50; font-family:inherit; font-size:12px; font-weight:600; cursor:pointer; }
.um-create .um-btn:hover { background:#f8eef1; }
.um-create .um-btn-primary { background:var(--wine); color:#fff; border-color:var(--wine); }
.um-create .um-btn-primary:hover { background:var(--wine-dark); color:#fff; }
.um-create .um-btn:disabled { opacity:.65; cursor:wait; }
.um-create :is(.um-btn,.um-control,.um-source input):focus-visible { outline:2px solid var(--wine); outline-offset:2px; }
@media(max-width:700px) {
  .um-create .um-grid { grid-template-columns:1fr; }
  .um-create .um-wide { grid-column:auto; }
  .um-create :is(.um-section,.um-card-title,.um-form-foot) { padding-left:16px; padding-right:16px; }
  .um-create .um-form-foot .um-btn { flex:1; }
}

/* Pencarian pegawai langsung di kolom Nama: tanpa pilihan mode terpisah. */
.um-create .um-name-search { position:relative; }
.um-create .um-name-search input { padding-right:46px; }
.um-create .um-name-icon { position:absolute; right:15px; top:13px; width:19px; height:19px; pointer-events:none; color:#9c8790; }
.um-create .um-suggestions[hidden],.um-create .um-linked[hidden] { display:none!important; }
.um-create .um-suggestions { position:absolute; top:calc(100% + 5px); left:0; right:0; z-index:100; background:#fff; border:1px solid #d9b5c0; border-radius:10px; box-shadow:0 15px 30px rgba(52,17,27,.13); max-height:292px; overflow-y:auto; padding:6px; }
.um-create .um-suggest-title { padding:8px 11px 9px; color:#8a7880; font-size:11px; font-weight:600; letter-spacing:.01em; }
.um-create .um-suggest-option { width:100%; display:flex; justify-content:space-between; align-items:center; gap:12px; padding:11px 12px; border:0; border-radius:8px; background:#fff; color:#362b30; text-align:left; cursor:pointer; font:inherit; }
.um-create .um-suggest-option[hidden] { display:none!important; }
.um-create .um-suggest-option:hover,.um-create .um-suggest-option.um-active { background:#f9edf0; }
.um-create .um-suggest-identity { display:flex; flex-direction:column; gap:3px; min-width:0; }
.um-create .um-suggest-identity strong { font-size:12px; font-weight:700; overflow-wrap:anywhere; }
.um-create .um-suggest-identity small { color:#82767c; font-size:11px; overflow-wrap:anywhere; }
.um-create .um-suggest-tag { flex:none; background:#f5e7eb; border-radius:20px; padding:5px 8px; color:#800b22; font-size:10px; font-weight:600; }
.um-create .um-suggest-empty { padding:12px; font-size:12px; color:#81737a; line-height:1.5; }
.um-create .um-linked { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin-top:11px; padding:11px 13px; background:#edf8f0; color:#266a3b; border:1px solid #cbe9d2; border-radius:9px; font-size:12px; line-height:1.5; }
.um-create .um-linked button { flex:none; font-family:inherit; font-size:11px; font-weight:600; background:#fff; border:1px solid #acd6b9; border-radius:6px; padding:5px 10px; color:#246637; cursor:pointer; }
.um-create .um-linked button:hover { background:#dcf5e4; }
.um-create .um-field .um-lookup-note { padding-top:5px; }
.um-create .um-form-foot { position:relative; }
@media(max-width:700px) { .um-create .um-linked { flex-direction:column; } }
</style>
@endpush

@section('content')
<div class="um-create">
    <header class="um-head">
        <nav class="um-crumb" aria-label="Breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <a href="{{ route('admin.manajemen-pengguna') }}">Manajemen Pengguna</a> / Tambah Pengguna
        </nav>
        <h1>Tambah Pengguna</h1>
        <p>Masukkan nama pengguna. Jika sudah tercatat di Data Pegawai dan belum punya akun, pilih namanya dari saran yang muncul.</p>
    </header>

    @if ($errors->any())
        <div class="um-alert" role="alert">
            <strong>Data belum berhasil disimpan. Periksa isian berikut:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form id="umFormCreate" method="POST" action="{{ route('admin.manajemen-pengguna.store') }}">
        @csrf
        {{-- Hanya diisi bila Admin benar-benar memilih saran pegawai. --}}
        <input type="hidden" name="pegawai_id" id="pegawai_id" value="{{ old('pegawai_id', '') }}">

        <div class="um-card">
            <div class="um-card-title">
                <h2>Formulir Akun Pengguna</h2>
                <p>Identitas, email, dan role ditentukan Admin. Kata sandi dibuat sendiri oleh pemilik akun melalui tautan aktivasi.</p>
            </div>

            <section class="um-section" aria-labelledby="infoPengguna">
                <h3 id="infoPengguna"><span class="um-step">01</span> Informasi Pengguna</h3>
                <div class="um-note">
                    <strong>Cari pegawai atau langsung ketik manual.</strong>
                    Saran nama hanya menampilkan pegawai yang belum memiliki akun. Jika tidak memilih salah satunya, formulir tetap membuat akun baru secara manual.
                </div>
                <div class="um-grid">
                    <div class="um-field um-wide">
                        <label class="um-label" for="name">Nama Lengkap <span class="um-required">*</span></label>
                        <div class="um-name-search" id="umNameSearch">
                            <input id="name" name="name" type="text" maxlength="255" minlength="2" required
                                class="um-control @error('name') is-invalid @enderror"
                                placeholder="Ketik nama lengkap pengguna..." autocomplete="off"
                                role="combobox" aria-autocomplete="list" aria-haspopup="listbox"
                                aria-controls="umSuggestions" aria-expanded="false"
                                value="{{ old('name') }}">
                            <svg class="um-name-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                            <div id="umSuggestions" class="um-suggestions" role="listbox" aria-label="Saran nama pegawai yang belum punya akun" hidden>
                                <div class="um-suggest-title">Data Pegawai yang belum memiliki akun</div>
                                @foreach ($pegawaiTersedia as $p)
                                    <button type="button" class="um-suggest-option" role="option"
                                        id="umPegawaiOption{{ $p->id }}"
                                        data-id="{{ $p->id }}"
                                        data-nama="{{ $p->nama_pegawai }}"
                                        data-email="{{ $p->email }}"
                                        data-hp="{{ $p->no_hp ?? '' }}"
                                        aria-selected="false" hidden>
                                        <span class="um-suggest-identity"><strong>{{ $p->nama_pegawai }}</strong><small>{{ $p->email }}</small></span>
                                        <span class="um-suggest-tag">Pilih</span>
                                    </button>
                                @endforeach
                                <p id="umSuggestEmpty" class="um-suggest-empty" hidden>Tidak ada pegawai yang cocok. Lanjutkan input manual.</p>
                            </div>
                        </div>
                        <p class="um-help um-lookup-note">Mulai ketik minimal 2 huruf untuk melihat saran. Saran tidak wajib dipilih.</p>
                        <div id="umLinked" class="um-linked" hidden role="status">
                            <span><strong id="umLinkedName"></strong> dipilih dari Data Pegawai. Email dan nomor HP mengikuti biodata tersebut.</span>
                            <button type="button" id="umClearChoice">Batalkan pilihan</button>
                        </div>
                        @error('name')<span class="um-error">{{ $message }}</span>@enderror
                        @error('pegawai_id')<span class="um-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="um-field">
                        <label class="um-label" for="email">Email <span class="um-required">*</span></label>
                        <input id="email" name="email" type="email" maxlength="255" required
                            class="um-control @error('email') is-invalid @enderror"
                            placeholder="nama@contoh.com" autocomplete="email" value="{{ old('email') }}">
                        <p class="um-help">Jika memilih pegawai, email otomatis mengikuti Data Pegawai dan tidak dapat diedit di sini.</p>
                        @error('email')<span class="um-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="um-field">
                        <label class="um-label" for="no_hp">Nomor HP</label>
                        <input id="no_hp" name="no_hp" type="tel" maxlength="20"
                            class="um-control @error('no_hp') is-invalid @enderror"
                            placeholder="08xxxxxxxxxx" autocomplete="tel" value="{{ old('no_hp') }}">
                        @error('no_hp')<span class="um-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="um-field">
                        <label class="um-label" for="role">Role Pengguna <span class="um-required">*</span></label>
                        <select id="role" name="role" class="um-control @error('role') is-invalid @enderror" required>
                            <option value="">-- Pilih role --</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            <option value="guru" @selected(old('role') === 'guru')>Guru</option>
                            <option value="musyrifah" @selected(old('role') === 'musyrifah')>Musyrifah</option>
                            <option value="pimpinan" @selected(old('role') === 'pimpinan')>Pimpinan</option>
                            <option value="mahasiswa" @selected(old('role') === 'mahasiswa')>Mahasiswa</option>
                        </select>
                        <p class="um-help">Role ditentukan Admin. Akun Admin atau Mahasiswa manual tidak otomatis menjadi Data Pegawai.</p>
                        @error('role')<span class="um-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="um-note neutral" role="status" id="umSourceNote">Ketik data secara manual atau pilih pegawai dari saran nama. Admin tidak membuat kata sandi; tautan aktivasi dikirim ke email pengguna.</div>
            </section>

            <section class="um-section" aria-labelledby="keamananPengguna">
                <h3 id="keamananPengguna"><span class="um-step">02</span> Aktivasi Akun</h3>
                <div class="um-note">
                    <strong>Admin tidak membuat dan tidak mengetahui kata sandi pengguna.</strong>
                    Setelah akun disimpan, SIMAHAD mengirim tautan aktivasi ke email pengguna. Melalui tautan tersebut
                    pemilik akun membuat kata sandi sendiri. Tautan berlaku 48 jam dan hanya dapat dipakai satu kali.
                    Akun berstatus <em>Menunggu Aktivasi</em> sampai pemiliknya menyelesaikan proses ini.
                </div>
            </section>
            <div class="um-form-foot">
                <a class="um-btn" href="{{ route('admin.manajemen-pengguna') }}">Batal</a>
                <button class="um-btn um-btn-primary" type="submit" id="umSubmit">Simpan &amp; Kirim Aktivasi</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const form = document.getElementById('umFormCreate');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const hpInput = document.getElementById('no_hp');
    const pegawaiIdInput = document.getElementById('pegawai_id');
    const roleInput = document.getElementById('role');
    const suggestions = document.getElementById('umSuggestions');
    const searchBox = document.getElementById('umNameSearch');
    const emptyState = document.getElementById('umSuggestEmpty');
    const options = Array.from(document.querySelectorAll('.um-suggest-option'));
    const linkedNote = document.getElementById('umLinked');
    const linkedName = document.getElementById('umLinkedName');
    const clearButton = document.getElementById('umClearChoice');
    const sourceNote = document.getElementById('umSourceNote');
    const submitButton = document.getElementById('umSubmit');
    if (!form || !nameInput || !emailInput || !hpInput || !pegawaiIdInput || !roleInput || !suggestions || !searchBox || !emptyState || !linkedNote || !linkedName || !clearButton || !sourceNote || !submitButton) return;

    let selectedPegawai = null;
    let manualDraft = null;
    let visibleOptions = [];
    let activeIndex = -1;

    function cleanText(value) {
        return String(value || '').normalize('NFKD').toLocaleLowerCase('id-ID').trim();
    }
    function setDropdown(open) {
        suggestions.hidden = !open;
        nameInput.setAttribute('aria-expanded', String(open));
        if (!open) {
            activeIndex = -1;
            nameInput.removeAttribute('aria-activedescendant');
        }
    }
    function activate(index) {
        activeIndex = index;
        visibleOptions.forEach((option, i) => {
            const active = i === index;
            option.classList.toggle('um-active', active);
            option.setAttribute('aria-selected', String(active));
        });
        if (index >= 0 && visibleOptions[index]) {
            nameInput.setAttribute('aria-activedescendant', visibleOptions[index].id);
            visibleOptions[index].scrollIntoView({ block: 'nearest' });
        } else {
            nameInput.removeAttribute('aria-activedescendant');
        }
    }
    function filterSuggestions() {
        const search = cleanText(nameInput.value);
        if (selectedPegawai || search.length < 2 || roleInput.value === 'mahasiswa') {
            setDropdown(false);
            return;
        }
        const matches = options.filter(option => cleanText(option.dataset.nama).includes(search)).slice(0, 8);
        visibleOptions = matches;
        options.forEach(option => { option.hidden = !matches.includes(option); option.classList.remove('um-active'); option.setAttribute('aria-selected', 'false'); });
        emptyState.hidden = matches.length > 0;
        setDropdown(true);
        activeIndex = -1;
    }
    function updateRoleGuard() {
        roleInput.setCustomValidity(selectedPegawai && roleInput.value === 'mahasiswa'
            ? 'Akun Mahasiswa tidak dapat dihubungkan dengan Data Pegawai. Batalkan pilihan nama atau pilih role lain.'
            : '');
        if (selectedPegawai && roleInput.value === 'mahasiswa') {
            sourceNote.textContent = 'Data Pegawai tidak dapat dipakai untuk role Mahasiswa. Batalkan pilihan pegawai terlebih dahulu.';
        } else if (selectedPegawai) {
            sourceNote.textContent = 'Identitas diambil dari Data Pegawai. Role tetap ditentukan Admin; kata sandi dibuat pemilik akun lewat tautan aktivasi.';
        } else {
            sourceNote.textContent = 'Input manual aktif. Jika akun dibuat untuk Guru, Musyrifah (Pengelola), atau Pimpinan, biodata dasar pegawai akan dibuat otomatis. Tautan aktivasi dikirim ke email pengguna.';
        }
    }
    function applySelection(option, restoring = false) {
        if (!restoring) manualDraft = { name: nameInput.value, email: emailInput.value, hp: hpInput.value };
        selectedPegawai = option;
        pegawaiIdInput.value = option.dataset.id;
        nameInput.value = option.dataset.nama || '';
        emailInput.value = option.dataset.email || '';
        hpInput.value = option.dataset.hp || '';
        emailInput.readOnly = true;
        hpInput.readOnly = true;
        linkedName.textContent = option.dataset.nama || 'Pegawai';
        linkedNote.hidden = false;
        setDropdown(false);
        updateRoleGuard();
    }
    function clearSelection(keepTypedName) {
        selectedPegawai = null;
        pegawaiIdInput.value = '';
        const draft = manualDraft;
        emailInput.readOnly = false;
        hpInput.readOnly = false;
        emailInput.value = draft ? draft.email : '';
        hpInput.value = draft ? draft.hp : '';
        if (!keepTypedName) nameInput.value = draft ? draft.name : '';
        manualDraft = null;
        linkedNote.hidden = true;
        updateRoleGuard();
    }

    nameInput.addEventListener('input', function () {
        if (selectedPegawai) clearSelection(true);
        filterSuggestions();
    });
    nameInput.addEventListener('focus', filterSuggestions);
    nameInput.addEventListener('keydown', function (event) {
        if (suggestions.hidden) return;
        if (event.key === 'Escape') { event.preventDefault(); setDropdown(false); return; }
        if (event.key === 'ArrowDown' && visibleOptions.length) {
            event.preventDefault(); activate((activeIndex + 1) % visibleOptions.length); return;
        }
        if (event.key === 'ArrowUp' && visibleOptions.length) {
            event.preventDefault(); activate((activeIndex - 1 + visibleOptions.length) % visibleOptions.length); return;
        }
        if (event.key === 'Enter' && activeIndex >= 0 && visibleOptions[activeIndex]) {
            event.preventDefault(); applySelection(visibleOptions[activeIndex]);
        }
    });
    options.forEach(option => {
        option.addEventListener('click', function () { applySelection(option); nameInput.focus(); });
    });
    document.addEventListener('pointerdown', event => {
        if (!searchBox.contains(event.target)) setDropdown(false);
    });
    clearButton.addEventListener('click', function () {
        clearSelection(false);
        nameInput.focus();
    });
    roleInput.addEventListener('change', function () {
        updateRoleGuard();
        if (!selectedPegawai) filterSuggestions();
    });

    // Pemulihan setelah validasi Laravel gagal: pegawai dipilih secara eksplisit sebelumnya.
    const previousSelection = options.find(option => option.dataset.id === pegawaiIdInput.value);
    if (previousSelection) applySelection(previousSelection, true);
    else {
        pegawaiIdInput.value = '';
        emailInput.readOnly = false;
        hpInput.readOnly = false;
    }
    updateRoleGuard();

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
            return;
        }
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';
    });
});
</script>
@endpush
