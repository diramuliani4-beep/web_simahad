@extends('layouts.admin')
@section('title', 'Edit Pengguna')
@push('styles')
<style>
/* Semua aturan khusus menu ini diawali um- untuk menjaga halaman tim lain. */
.um-page,.um-page *{box-sizing:border-box}
.um-page{--um-wine:#800b22;--um-deep:#620819;--um-border:#ecdee1;--um-muted:#837b80;--um-ink:#292427;max-width:100%;min-width:0;color:var(--um-ink);font-family:inherit}
.um-page a{text-decoration:none}
.um-head{padding-bottom:19px;margin:0 0 25px;border-bottom:1px solid #e8cfd6}
.um-crumb{font-size:12px;color:#8e8487;margin:0 0 11px}
.um-crumb a{color:var(--um-wine)}
.um-head h1{margin:0 0 6px;font-size:clamp(22px,2.4vw,27px);font-weight:700}
.um-head p{font-size:13px;line-height:1.7;color:#787175;margin:0}
.um-card{background:white;border:1px solid #ece1e1;border-radius:12px;box-shadow:0 3px 14px rgba(62,10,25,.035);overflow:hidden}
.um-card-top{display:flex;gap:14px;align-items:center;justify-content:space-between;flex-wrap:wrap;padding:20px 24px;border-bottom:1px solid #f0e8e9}
.um-card-top h2{font-size:16px;margin:0 0 5px;font-weight:700}.um-card-top p{font-size:12px;color:var(--um-muted);margin:0}
.um-btn{display:inline-flex;gap:7px;justify-content:center;align-items:center;border-radius:8px;padding:10px 17px;min-height:41px;border:1px solid #e4d6d8;font-family:inherit;font-size:12px;font-weight:600;cursor:pointer;background:white;color:#52454a;text-decoration:none}
.um-btn:hover{background:#fcf1f4;color:var(--um-wine)}
.um-btn:focus-visible,.um-control:focus-visible,.um-pill:focus-visible{outline:2px solid var(--um-wine);outline-offset:2px}
.um-btn-primary{background:var(--um-wine);color:white;border-color:var(--um-wine)}.um-btn-primary:hover{background:var(--um-deep);color:white;border-color:var(--um-deep)}
.um-btn-warn{border-color:#e9bac5;color:#a52540;background:#fff6f7}.um-btn-warn:hover{color:white;background:#a52540}
.um-btn svg{height:15px;width:15px}
.um-alert{border-radius:9px;padding:13px 16px;font-size:12px;margin:0 0 16px;border:1px solid #bce2ca;background:#e8f8ed;color:#1c7138}
.um-alert-error{border-color:#f1c2cc;background:#fff0f2;color:#a21e3c}
.um-toolbar{padding:18px 24px 12px}.um-search{display:flex;align-items:center;gap:10px;border:1px solid #e5d8db;border-radius:10px;padding:5px 5px 5px 13px;min-height:53px}
.um-search:focus-within{border-color:#a56070;box-shadow:0 0 0 3px rgba(128,11,34,.07)}.um-search input{flex:1;min-width:0;border:0;outline:0;font-size:12px;background:transparent}
.um-tabs{display:flex;gap:7px;flex-wrap:wrap;padding:13px 0 6px}.um-pill{border-radius:22px;padding:7px 14px;font-size:12px;color:#494047;background:#f0edef;font-weight:600}.um-pill:hover{background:#eedfe3;color:var(--um-wine)}.um-pill.current{color:#fff;background:var(--um-wine)}
.um-hint{font-size:11px;color:#999095;padding:3px 24px 14px;margin:0}
.um-table-wrap{max-width:100%;width:100%;overflow-x:auto}.um-table{width:100%;min-width:870px;border-collapse:collapse;font-size:12px}
.um-table th{padding:14px 16px;white-space:nowrap;text-align:left;color:#fff;background:var(--um-wine);font-size:11px;font-weight:600}
.um-table td{padding:14px 16px;border-bottom:1px solid #f0e8e9;vertical-align:middle;color:#574f53}.um-table tr:nth-child(even){background:#fdfafb}.um-table tr:hover{background:#fcf4f6}
.um-profile{display:flex;gap:11px;align-items:center;min-width:220px}.um-avatar{display:grid;place-items:center;flex:none;width:42px;height:42px;border-radius:11px;background:#f8e9ed;color:var(--um-wine);overflow:hidden}
.um-avatar svg{height:23px;width:23px}.um-avatar img{height:100%;width:100%;object-fit:cover}
.um-name{font-weight:700;color:#2b2528;margin:0 0 3px}.um-email{font-size:11px;color:#8f8589;overflow-wrap:anywhere;max-width:265px}
.um-role{padding:6px 10px;border-radius:7px;background:#f5eff1;color:#744252;font-weight:600;text-transform:capitalize;white-space:nowrap}
.um-status{display:inline-flex;align-items:center;gap:6px;padding:6px 11px;border-radius:20px;font-weight:700;font-size:11px;white-space:nowrap}
.um-status::before{content:"";background:currentColor;width:6px;height:6px;border-radius:50%}
.um-status.active{background:#e0f8e8;color:#147439}.um-status.inactive{background:#ffe9eb;color:#a52a40}.um-status.pending{background:#fff4db;color:#976817}
.um-actions{display:flex;gap:6px;align-items:center;white-space:nowrap}.um-actions form{margin:0}.um-actions .um-btn{padding:8px 10px;min-height:34px;font-size:11px}
.um-footer{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center;padding:17px 24px;color:#827a7e;font-size:11px}
.um-footer a{color:var(--um-wine);font-weight:600}.um-footer nav{display:flex;gap:10px;align-items:center}
.um-section{padding:23px 25px 3px}.um-section h3{display:flex;gap:10px;align-items:center;margin:0 0 18px;padding:0 0 13px;border-bottom:1px solid #f1e6e9;color:var(--um-wine);font-size:14px;font-weight:700}
.um-step{display:grid;place-items:center;flex:none;width:26px;height:26px;border-radius:8px;background:#f8ecf0;font-size:12px}
.um-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:19px 20px;margin-bottom:25px}.um-field{min-width:0}.um-wide{grid-column:1/-1}
.um-label{display:block;margin:0 0 8px;color:#40343a;font-size:12px;font-weight:600}.um-required{color:#bb2541}
.um-control{display:block;width:100%;min-height:45px;padding:10px 13px;border:1px solid #e1d6da;border-radius:8px;background:white;color:#292529;font-family:inherit;font-size:13px;outline:none}
.um-control:focus{border-color:#a15365;box-shadow:0 0 0 3px rgba(128,11,34,.07)}.um-control[readonly]{background:#f7f4f5;color:#62595e;cursor:default}.um-control.is-invalid{border-color:#ba2a45}
.um-help{margin:6px 0 0;font-size:11px;color:#8a8086;line-height:1.6}.um-error{color:#b82642;font-size:11px;margin-top:5px;display:block}
.um-note{border:1px solid #ebd2d8;background:#fcf3f5;padding:12px 14px;border-radius:9px;color:#714151;font-size:12px;line-height:1.7;margin:0 0 20px}
.um-note.info{background:#f6f7f9;border-color:#e0e4eb;color:#526076}
.um-form-foot{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;border-top:1px solid #f1e8eb;padding:19px 25px}
.um-modal[hidden]{display:none!important}.um-modal{position:fixed;inset:0;z-index:9999;background:rgba(12,20,32,.72);backdrop-filter:blur(5px);display:flex;align-items:center;justify-content:center;padding:18px}
.um-dialog{background:#fff;border:1px solid var(--um-wine);border-radius:11px;width:min(100%,710px);max-height:calc(100dvh - 36px);overflow-y:auto;padding:40px 44px;text-align:center;box-shadow:0 20px 70px #0003}
.um-dialog-icon{display:grid;place-items:center;background:#f8edef;border-radius:50%;color:var(--um-wine);height:90px;width:90px;margin:0 auto 23px}
.um-dialog-icon svg{width:40px;height:40px}.um-dialog h2{font-size:25px;margin:0 0 14px}.um-dialog p{font-size:15px;color:#797377;line-height:1.65}.um-dialog strong{color:#333;overflow-wrap:anywhere}
.um-dialog-actions{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-top:33px}.um-dialog-actions .um-btn{min-height:54px;font-size:15px}
@media(max-width:720px){.um-card-top,.um-toolbar,.um-footer,.um-section,.um-form-foot{padding-left:16px;padding-right:16px}.um-grid{grid-template-columns:1fr}.um-wide{grid-column:auto}.um-head h1{font-size:22px}.um-dialog{padding:26px 18px}.um-dialog-icon{width:74px;height:74px}.um-dialog h2{font-size:21px}.um-dialog p{font-size:13px}.um-dialog-actions{gap:10px}.um-dialog-actions .um-btn{min-height:46px;font-size:13px}}
</style>
@endpush
@section('content')
<div class="um-page">
<header class="um-head"><div class="um-crumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.manajemen-pengguna') }}">Manajemen Pengguna</a> / Edit Pengguna</div><h1>Edit Pengguna</h1><p>Admin dapat memperbarui identitas, role, status login, dan kata sandi akun. Biodata pegawai yang terhubung tetap dipertahankan.</p></header>
@if ($errors->any())<div class="um-alert um-alert-error" role="alert"><strong>Periksa kembali isian berikut:</strong><ul style="margin:8px 0 0;padding-left:20px">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form id="umFormEdit" method="POST" action="{{ route('admin.manajemen-pengguna.update', $akun->getKey()) }}" autocomplete="off">@csrf @method('PUT')
<section class="um-card">
  <div class="um-card-top"><div><h2>Formulir Edit Akun</h2><p>Perbarui identitas, role, akses login, atau kata sandi.</p></div><span class="um-role">{{ ucfirst($akun->role) }}</span></div>
  <div class="um-section"><h3><span class="um-step">01</span> Informasi Pengguna</h3>
    @if ((int) auth()->id() === (int) $akun->getKey())
      <div class="um-note info" role="status">
        <strong>Anda sedang mengedit akun sendiri.</strong> Nama, email, nomor HP, dan kata sandi bisa diperbarui. Role dan status juga bisa diubah selama masih ada Admin aktif lain. Jika perubahan menghilangkan akses Admin, Anda akan keluar otomatis setelah menyimpan.
      </div>
    @endif
    @if ($akun->pegawaiAkun?->pegawai)
      <div class="um-note"><strong>Data Pegawai terhubung:</strong> {{ $akun->pegawaiAkun->pegawai->nama_pegawai }}. Perubahan nama, email, dan nomor HP di sini ikut disinkronkan ke biodata pegawai. Status kepegawaian tetap terpisah.</div>
    @elseif (in_array($akun->role, ['guru', 'musyrifah', 'pimpinan'], true))
      <div class="um-note info">Akun ini belum terhubung dengan Data Pegawai. Jika disimpan dengan role operasional Ma'had, sistem akan membuat biodata dasar selama tidak ada data yang bertabrakan.</div>
    @endif
    <div class="um-grid">
      <div class="um-field um-wide"><label class="um-label" for="name">Nama Lengkap <span class="um-required">*</span></label><input id="name" name="name" type="text" class="um-control @error('name') is-invalid @enderror" placeholder="Nama pengguna" maxlength="255" value="{{ old('name', $akun->name) }}" required ><span class="um-error">@error('name'){{ $message }}@enderror</span></div>
      <div class="um-field"><label class="um-label" for="email">Email Akun <span class="um-required">*</span></label><input id="email" name="email" type="email" class="um-control @error('email') is-invalid @enderror" placeholder="nama@contoh.com" maxlength="255" value="{{ old('email', $akun->email) }}" required ><span class="um-error">@error('email'){{ $message }}@enderror</span><p class="um-help">Perubahan email untuk akun terkait akan diperbarui juga di Data Pegawai.</p></div>
      <div class="um-field"><label class="um-label" for="no_hp">Nomor HP</label><input id="no_hp" name="no_hp" type="tel" class="um-control @error('no_hp') is-invalid @enderror" placeholder="08xxxxxxxxxx" maxlength="20" value="{{ old('no_hp', $akun->no_hp) }}" ><span class="um-error">@error('no_hp'){{ $message }}@enderror</span></div>
      <div class="um-field"><label class="um-label" for="role">Role Pengguna <span class="um-required">*</span></label><select id="role" name="role" required class="um-control @error('role') is-invalid @enderror"><option value="">-- Pilih role --</option>
<option value="admin" @selected(old('role', $akun->role) === 'admin')>Admin</option>
<option value="guru" @selected(old('role', $akun->role) === 'guru')>Guru</option>
<option value="musyrifah" @selected(old('role', $akun->role) === 'musyrifah')>Musyrifah</option>
<option value="pimpinan" @selected(old('role', $akun->role) === 'pimpinan')>Pimpinan</option>
<option value="mahasiswa" @selected(old('role', $akun->role) === 'mahasiswa')>Mahasiswa</option></select>
        @if ($akun->role === 'pengelola')
          <p class="um-help" style="color:#a52540"><strong>Role lama terdeteksi:</strong> Pengelola. Pilih <strong>Musyrifah (Pengelola)</strong> sebelum menyimpan. Sistem tidak mengubah role akun lama secara diam-diam.</p>
        @endif
        <p class="um-help">Akun yang terhubung dengan Data Pegawai tidak boleh diubah menjadi Mahasiswa.</p>@error('role')<span class="um-error">{{ $message }}</span>@enderror
      </div>
    </div>
  </div>
  <div class="um-section"><h3><span class="um-step">02</span> Status & Kata Sandi</h3>
    <div class="um-grid"><div class="um-field"><label class="um-label" for="status">Status Akun <span class="um-required">*</span></label><select name="status" id="status" required class="um-control">@if(in_array($akun->status, ['menunggu aktivasi','menunggu','pending'], true))<option value="{{ $akun->status }}" @selected(old('status', $akun->status) === $akun->status)>Menunggu Verifikasi</option>@endif<option value="aktif" @selected(old('status', $akun->status) === 'aktif')>Aktif</option>
<option value="nonaktif" @selected(old('status', $akun->status) === 'nonaktif')>Nonaktif</option></select><p class="um-help">Hanya status login. Tidak mengubah status pegawai.</p></div></div>
    <div class="um-note info">Biarkan Kata Sandi kosong jika tidak ingin mengubahnya. Jika diisi, pengguna akan memakai kata sandi baru.</div>
    <div class="um-grid">
      <div class="um-field"><label class="um-label" for="password">Kata Sandi Baru</label><input id="password" name="password" type="password" class="um-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak diganti" maxlength="255" ><span class="um-error">@error('password'){{ $message }}@enderror</span></div><div class="um-field"><label class="um-label" for="password_confirmation">Konfirmasi Kata Sandi</label><input id="password_confirmation" name="password_confirmation" type="password" class="um-control @error('password_confirmation') is-invalid @enderror" placeholder="Ulangi kata sandi baru" maxlength="255" ><span class="um-error">@error('password_confirmation'){{ $message }}@enderror</span></div>
    </div>
  </div>
  <div class="um-form-foot"><a class="um-btn" href="{{ route('admin.manajemen-pengguna') }}">Batal</a><button class="um-btn um-btn-primary" id="umSubmitEdit" type="submit">Simpan Perubahan</button></div>
</section>
</form></div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
 const form=document.getElementById('umFormEdit');
 const button=document.getElementById('umSubmitEdit');
 if(!form||!button)return;
 form.addEventListener('submit',function(){
   if(!form.checkValidity())return;
   button.disabled=true;button.textContent='Menyimpan...';
 });
});
</script>
@endpush