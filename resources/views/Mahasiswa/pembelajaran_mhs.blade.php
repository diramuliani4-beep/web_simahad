@extends('layouts.mahasiswa')

@section('title', 'Pembelajaran Mahasiswa | SIMAHAD')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* SIMAHAD | Pembelajaran Mahasiswa
   Hanya mengatur konten, bukan header/sidebar dari layouts.mahasiswa. */
.pembelajaran-page {
  --maroon:#800b22; --maroon-dark:#65091a; --maroon-soft:#fff3f6;
  --line:#eadfe3; --ink:#211d25; --muted:#776e7b;
  --green:#137a4a; --amber:#a36a13; --red:#ba354d;
  width:100% !important; max-width:100% !important; min-width:0 !important;
  display:block !important; clear:both; margin:0 !important; padding:0 0 30px !important;
  color:var(--ink); background:transparent; font-family:'Plus Jakarta Sans',Poppins,Arial,sans-serif;
  font-size:13px; line-height:1.55; container-type:inline-size;
}
.pembelajaran-page,.pembelajaran-page *,.pembelajaran-page *::before,.pembelajaran-page *::after {box-sizing:border-box}
.pembelajaran-page > *, .pembelajaran-page section, .pembelajaran-page div, .pembelajaran-page article {min-width:0}
.pembelajaran-page a {color:inherit;text-decoration:none}
.pembelajaran-page button,.pembelajaran-page input,.pembelajaran-page select {font:inherit}
.pembelajaran-page button {cursor:pointer}
.pembelajaran-page a:focus-visible,.pembelajaran-page button:focus-visible {outline:3px solid rgba(201,169,106,.5);outline-offset:2px}
/* Header halaman: hanya breadcrumb menuju dashboard, tanpa tombol kembali tambahan */
.pembelajaran-page .page-header-row {
  display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;
  gap:12px;margin:0 0 18px;padding:0 0 17px;border-bottom:1px solid #ead5dc;
}
.pembelajaran-page .page-header-row > div:first-child {flex:1 1 580px;min-width:0}
.pembelajaran-page .breadcrumb {display:flex;align-items:center;flex-wrap:wrap;gap:7px;margin:0 0 10px;color:#8f8290;font-size:12px;font-weight:550}
.pembelajaran-page .breadcrumb a {color:var(--maroon);font-weight:750}
.pembelajaran-page .breadcrumb a:hover {text-decoration:underline}
.pembelajaran-page .page-title {margin:0;color:#251b26;font-size:clamp(21px,2.1vw,28px);font-weight:800;line-height:1.28;letter-spacing:-.028em}
.pembelajaran-page .page-description {margin:6px 0 0;color:var(--muted);font-size:12.5px;line-height:1.7;max-width:950px}
.pembelajaran-page .sync-badge {display:none}
.pembelajaran-page .sync-dot {display:none}
/* Statistik ringkas */
.pembelajaran-page .stats-grid {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:0 0 19px}
.pembelajaran-page .stat-card {
  position:relative;display:flex;flex-direction:column;justify-content:space-between;gap:9px;
  padding:15px 16px 14px;background:#fff;border:1px solid var(--line);border-radius:13px;
  box-shadow:0 2px 9px rgba(44,11,27,.025);overflow:hidden;
}
.pembelajaran-page .stat-card::before {content:'';position:absolute;top:0;bottom:0;left:0;width:3px;background:var(--maroon)}
.pembelajaran-page .stat-card:nth-child(2)::before{background:#7654b3}
.pembelajaran-page .stat-card:nth-child(3)::before{background:#238658}
.pembelajaran-page .stat-card:nth-child(4)::before{background:#cc9140}
.pembelajaran-page .stat-card > div:first-child {display:grid;grid-template-columns:36px minmax(0,1fr);grid-template-rows:auto auto;align-items:center;column-gap:10px}
.pembelajaran-page .stat-icon {grid-row:1 / 3;display:grid;place-items:center;width:36px;height:36px;border-radius:10px;background:#fbeaf0;color:var(--maroon)}
.pembelajaran-page .stat-card:nth-child(2) .stat-icon{background:#f2ecfc}
.pembelajaran-page .stat-card:nth-child(3) .stat-icon{background:#e9f7ee}
.pembelajaran-page .stat-card:nth-child(4) .stat-icon{background:#fff3df}
.pembelajaran-page .stat-icon svg {width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}
.pembelajaran-page .stat-number {grid-column:2;font-size:clamp(17px,1.65vw,23px);font-weight:800;color:#241b26;line-height:1.25;letter-spacing:-.025em;overflow-wrap:anywhere}
.pembelajaran-page .stat-label {grid-column:2;color:#6e6472;font-size:11px;font-weight:650;line-height:1.45}
.pembelajaran-page .stat-subtext {margin:0;padding-top:8px;border-top:1px dashed #eee4e8;font-size:10.7px;color:#8b808a;line-height:1.5;overflow-wrap:anywhere}
/* Tab: horizontal di dalam panel, bukan melebar keluar halaman */
.pembelajaran-page .tabs-header-wrap {display:flex;flex-direction:column;align-items:stretch;gap:10px;min-width:0;margin:0 0 16px;padding:10px 12px;background:#fff;border:1px solid var(--line);border-radius:13px;overflow:hidden}
.pembelajaran-page .tabs-nav {display:flex;gap:7px;width:100%;max-width:100%;min-width:0;overflow-x:auto;overscroll-behavior-x:contain;padding:1px 1px 6px;scrollbar-width:thin;scrollbar-color:#e2c6cf transparent}
.pembelajaran-page .tab-btn {display:inline-flex;flex:0 0 auto;align-items:center;justify-content:center;gap:5px;min-height:37px;padding:8px 12px;color:#695f6c;background:#fff;border:1px solid #eee4e7;border-radius:8px;font-size:11.5px;font-weight:700;white-space:nowrap;transition:background .15s,color .15s,border-color .15s}
.pembelajaran-page .tab-btn:hover {background:#fff5f7;color:var(--maroon);border-color:#e3bdc6}
.pembelajaran-page .tab-btn.active {background:var(--maroon);border-color:var(--maroon);color:#fff;box-shadow:0 3px 9px rgba(128,11,34,.1)}
.pembelajaran-page .kelompok-select-form {display:flex;align-items:center;align-self:flex-end;flex-wrap:wrap;gap:7px;padding:2px 4px;color:#695b64}
.pembelajaran-page .kelompok-select-label {font-size:11px;font-weight:650}
.pembelajaran-page .kelompok-select-form strong {font-size:11.5px !important;color:var(--maroon)!important}
.pembelajaran-page .tab-pane{display:none;min-width:0}
.pembelajaran-page .tab-pane.active{display:block}
/* Kontainer utama di tiap tab */
.pembelajaran-page .card {width:100%;min-width:0;padding:0;margin:0 0 16px;border:1px solid var(--line);border-radius:14px;background:#fff;box-shadow:0 2px 11px rgba(36,15,28,.024);overflow:hidden}
.pembelajaran-page .card-header {display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px 12px;margin:0 0 14px;padding:15px 18px;border-bottom:1px solid #f1e9eb}
.pembelajaran-page .card-header > div:first-child {flex:1 1 360px;min-width:0}
.pembelajaran-page .card-title{display:flex;align-items:center;gap:7px;margin:0;color:#251e27;font-size:15px;font-weight:800;line-height:1.45}
.pembelajaran-page .card-desc{margin:4px 0 0;color:#807581;font-size:11.5px;line-height:1.6}
.pembelajaran-page .badge-pill {display:inline-flex;align-items:center;gap:4px;max-width:100%;padding:4px 10px;border:1px solid transparent;border-radius:999px;font-size:10.5px;font-weight:750;line-height:1.4;white-space:normal;overflow-wrap:anywhere;text-align:center}
.pembelajaran-page .badge-maroon{background:#fbeaf0;color:var(--maroon);border-color:#f0d8de}
.pembelajaran-page .badge-blue{background:#eaf2ff;color:#315ca3;border-color:#dae8fa}
.pembelajaran-page .badge-green{background:#e7f7ed;color:#18794b;border-color:#d5eddc}
.pembelajaran-page .badge-amber{background:#fff3dc;color:#936014;border-color:#f4e4c4}
.pembelajaran-page .badge-gray{background:#f4f5f7;color:#687180;border-color:#e7e9ec}
/* Mata pelajaran dua kolom desktop, turun ke bawah saat konten sempit */
.pembelajaran-page .mapel-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;padding:0 18px 18px}
.pembelajaran-page .mapel-card {display:flex;flex-direction:column;justify-content:space-between;gap:13px;min-width:0;padding:16px;border:1px solid #eadce1;border-radius:12px;background:#fff;transition:box-shadow .15s,border-color .15s}
.pembelajaran-page .mapel-card:hover{border-color:#d8afbb;box-shadow:0 5px 16px rgba(70,17,37,.07)}
.pembelajaran-page .mapel-card-top{display:flex;flex-wrap:wrap;align-items:center;gap:5px;margin-bottom:10px}
.pembelajaran-page .mapel-title{margin:0 0 5px;color:#241e26;font-size:15px;font-weight:800;line-height:1.45;overflow-wrap:anywhere}
.pembelajaran-page .mapel-desc{margin:0 0 12px;color:#7e7580;font-size:11.5px;line-height:1.65}
.pembelajaran-page .mapel-meta-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px 10px;margin:0 0 12px}
.pembelajaran-page .mapel-meta-item{display:flex;align-items:flex-start;gap:5px;min-width:0;color:#605562;font-size:11px;line-height:1.55;overflow-wrap:anywhere}
.pembelajaran-page .mapel-meta-item strong{color:#312b34}
.pembelajaran-page .progress-block{padding:12px;margin:0;border:1px solid #f0e7eb;border-radius:10px;background:#fcf9fa}
.pembelajaran-page .progress-header{display:flex;flex-wrap:wrap;justify-content:space-between;gap:6px;font-size:11px;color:#766975;margin-bottom:8px}
.pembelajaran-page .progress-header strong{color:#33262e;font-weight:800}
.pembelajaran-page .progress-bar-wrap{width:100%;height:7px;margin-bottom:9px;border-radius:999px;background:#ebdfe4;overflow:hidden}
.pembelajaran-page .progress-bar-fill{width:0;height:100%;border-radius:inherit;background:linear-gradient(90deg,#800b22,#ba3651);transition:width .2s}
.pembelajaran-page .progress-bar-fill.complete{background:linear-gradient(90deg,#18794b,#41ac73)}
.pembelajaran-page .progress-pills{display:flex;flex-wrap:wrap;align-items:center;gap:5px}
.pembelajaran-page .pill-stat{padding:3px 7px;border-radius:6px;font-size:10px;font-weight:750}
.pembelajaran-page .pill-stat.hadir{background:#e7f7ed;color:#176944}
.pembelajaran-page .pill-stat.izin{background:#e8f3ff;color:#2c6697}
.pembelajaran-page .pill-stat.sakit{background:#fff2da;color:#a06716}
.pembelajaran-page .pill-stat.alpa{background:#fde9ed;color:#a72640}
.pembelajaran-page .btn-view-detail{display:inline-flex;align-items:center;justify-content:center;width:100%;min-height:38px;padding:9px 12px;border:1px solid #dfc4cc;border-radius:9px;color:var(--maroon);background:#fff;font-size:11.5px;font-weight:750}
.pembelajaran-page .btn-view-detail:hover{background:#fff3f6;border-color:#c597a4}
/* Tabel aman untuk desktop; pada layar HP menjadi kartu dengan label kolom */
.pembelajaran-page .table-responsive{width:calc(100% - 36px);max-width:100%;margin:0 18px 18px;overflow-x:auto;overscroll-behavior-x:contain;border:1px solid #eadde2;border-radius:10px;scrollbar-width:thin}
.pembelajaran-page .custom-table{width:100%;min-width:760px;border-collapse:collapse;background:#fff}
.pembelajaran-page .custom-table thead th{padding:12px 11px;background:var(--maroon);color:#fff;font-size:11px;font-weight:750;text-align:left;white-space:nowrap}
.pembelajaran-page .custom-table tbody td{padding:11px;border-bottom:1px solid #f1e8eb;color:#433943;font-size:11.5px;line-height:1.55;vertical-align:middle;text-align:left;overflow-wrap:anywhere}
.pembelajaran-page .custom-table tbody tr:nth-child(even){background:#fffbfc}
.pembelajaran-page .custom-table tbody tr:hover{background:#fff5f7}
.pembelajaran-page .custom-table tbody tr:last-child td{border-bottom:0}
.pembelajaran-page .custom-table tbody tr.kelompok-saya{background:#fffdf8}
/* Jadwal */
.pembelajaran-page .schedule-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:0 18px 18px;margin:0}
.pembelajaran-page .schedule-card-item{display:flex;align-items:center;gap:12px;min-width:0;padding:12px 14px;border:1px solid #eee1e5;border-radius:11px;background:#fff}
.pembelajaran-page .schedule-time-box{display:flex;flex:0 0 76px;flex-direction:column;align-items:center;justify-content:center;width:76px;min-height:77px;padding:9px;border-radius:10px;background:#fff2f5;color:var(--maroon);text-align:center}
.pembelajaran-page .schedule-day-label{font-size:10px;font-weight:750;line-height:1.4}
.pembelajaran-page .schedule-date-num{font-size:19px;font-weight:800;line-height:1.2}
.pembelajaran-page .schedule-details{flex:1;min-width:0}
.pembelajaran-page .schedule-details h4{margin:0 0 4px;color:#251d25;font-size:12.5px;font-weight:800;line-height:1.5;overflow-wrap:anywhere}
.pembelajaran-page .schedule-details p{margin:3px 0 0;color:#796f79;font-size:11px;line-height:1.5;overflow-wrap:anywhere}
/* Audit dan kelulusan */
.pembelajaran-page .abs-ringkasan{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:9px;margin:0 0 14px}
.pembelajaran-page #tab-tugas .abs-ringkasan{grid-template-columns:repeat(4,minmax(0,1fr));margin:0 18px 14px}
.pembelajaran-page .abs-stat{min-width:0;padding:10px;border:1px solid #eadfe4;border-radius:9px;background:#fff;text-align:center}
.pembelajaran-page .abs-stat-label{display:block;color:#8c808c;font-size:10px;font-weight:700}
.pembelajaran-page .abs-stat strong{display:block;margin-top:3px;color:var(--maroon);font-size:19px;font-weight:800;line-height:1.2}
.pembelajaran-page .abs-stat.hadir strong{color:#15803d}
.pembelajaran-page .abs-stat.izin strong{color:#1d4ed8}
.pembelajaran-page .abs-stat.sakit strong{color:#a56b16}
.pembelajaran-page .abs-stat.alpa strong{color:#b91c1c}
.pembelajaran-page .abs-stat--persen{background:#fffdf7;border-color:#ecd9b8}
.pembelajaran-page .abs-stat-note{display:block;margin-top:4px;color:#8c808c;font-size:10px;line-height:1.35}
.pembelajaran-page .graduation-banner{display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin:0 0 16px;padding:19px 20px;border:1px solid #efdba7;border-radius:13px;background:#fffaf0}
.pembelajaran-page .graduation-banner.lulus{background:#f1fbf5;border-color:#c6e7d0}
.pembelajaran-page .grad-icon-wrap{display:grid;flex:0 0 48px;place-items:center;width:48px;height:48px;border-radius:12px;background:#fff0cc;font-size:24px}
.pembelajaran-page .graduation-banner.lulus .grad-icon-wrap{background:#e0f5e8}
.pembelajaran-page .grad-content{flex:1 1 360px;min-width:0}
.pembelajaran-page .grad-title{color:#774a13;font-size:15px;font-weight:800;line-height:1.4}
.pembelajaran-page .graduation-banner.lulus .grad-title{color:#176640}
.pembelajaran-page .grad-desc{margin:5px 0 0;color:#845f32;font-size:11.5px;line-height:1.7}
.pembelajaran-page .graduation-banner.lulus .grad-desc{color:#3c7955}
.pembelajaran-page .btn-certificate{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:40px;padding:9px 15px;border:0;border-radius:9px;background:#21804f;color:#fff;font-size:11.5px;font-weight:750}
.pembelajaran-page .btn-certificate:hover:not(:disabled){background:#17673f}
.pembelajaran-page .btn-certificate:disabled{background:#e3e4e6;color:#929aa2;cursor:not-allowed}
/* Kalender */
.pembelajaran-page #tab-kalender > .card{display:block!important;padding:18px!important}
.pembelajaran-page .calendar-card{width:100%;max-width:760px;min-width:0;margin:0 auto;padding:16px;border:1px solid #eee2e5;border-radius:12px;background:#fff}
.pembelajaran-page .calendar-heading{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-bottom:13px}
.pembelajaran-page .calendar-heading h3{margin:0;font-size:14px;font-weight:800;color:#29212a}
.pembelajaran-page .calendar-toolbar{display:grid;grid-template-columns:36px minmax(0,1fr) 36px;align-items:center;gap:8px;margin:0 0 10px;text-align:center}
.pembelajaran-page .calendar-toolbar strong{font-size:13px;color:var(--maroon)}
.pembelajaran-page .calendar-toolbar button{display:grid;place-items:center;min-height:34px;padding:0;border:1px solid #e4d6db;border-radius:8px;color:var(--maroon);background:#fff;font-size:18px}
.pembelajaran-page .calendar-toolbar button:hover{background:#fff2f6}
.pembelajaran-page .calendar-grid{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:4px}
.pembelajaran-page .day-name{text-align:center;padding:6px 0;color:#8f8690;font-size:10px;font-weight:700}
.pembelajaran-page .calendar-day{position:relative;display:flex;align-items:center;justify-content:center;min-width:0;min-height:42px;border:1px solid transparent;border-radius:8px;color:#413b43;font-size:12px;cursor:pointer}
.pembelajaran-page .calendar-day:hover{background:#f8f3f4}
.pembelajaran-page .calendar-day.muted{color:#c2bcc3;cursor:default}
.pembelajaran-page .calendar-day.today{border-color:#be93a0;font-weight:800}
.pembelajaran-page .calendar-day.has-event{background:#faebef;border-color:#efdae0;color:var(--maroon);font-weight:800}
.pembelajaran-page .calendar-day.has-event::after{content:'';position:absolute;bottom:4px;width:5px;height:5px;border-radius:50%;background:var(--maroon)}
.pembelajaran-page .calendar-legend{display:flex;align-items:center;gap:7px;margin-top:12px;padding-top:11px;border-top:1px solid #f0e8ea;color:#7a717d;font-size:11px}
.pembelajaran-page .legend-dot{flex:0 0 8px;width:8px;height:8px;border-radius:50%;background:var(--maroon)}
.pembelajaran-page .calendar-info-box{margin-top:11px;padding:12px;border:1px solid #f0e0e5;border-radius:9px;background:#fffbfc;color:#645966;font-size:11.5px;line-height:1.7;overflow-wrap:anywhere}
/* Modal di atas layout bersama, tombol dan konten tetap berfungsi */
.pembelajaran-page .modal-overlay{position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;padding:15px;background:rgba(22,12,20,.58);overflow-y:auto}
.pembelajaran-page .modal-overlay.active{display:flex}
.pembelajaran-page .modal-card{display:flex;flex-direction:column;width:min(100%,740px);max-height:min(90vh,900px);min-width:0;margin:auto;overflow-y:auto;border:1px solid #ebdce1;border-radius:15px;background:#fff;box-shadow:0 25px 65px rgba(23,11,23,.22)}
.pembelajaran-page .modal-top{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:17px 20px;border-bottom:1px solid #eee3e7;background:#fff8fa}
.pembelajaran-page .modal-top-title{font-size:15px;font-weight:800;color:var(--maroon)}
.pembelajaran-page .modal-close-btn{flex:0 0 auto;border:0;background:transparent;color:#77707a;font-size:25px;line-height:1;cursor:pointer}
.pembelajaran-page .modal-inner{padding:17px 20px}
.pembelajaran-page .modal-footer-btns{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;padding:13px 20px;border-top:1px solid #eee5e8;background:#fffafb}
.pembelajaran-page .btn-modal-cancel,.pembelajaran-page .btn-modal-action{padding:9px 16px;border:1px solid #e6d5db;border-radius:8px;color:#6c5360;background:#fff;font-size:11.5px;font-weight:750}
.pembelajaran-page .btn-modal-action{color:#fff;background:var(--maroon);border-color:var(--maroon)}
.pembelajaran-page .pertemuan-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px;max-height:340px;margin-top:11px;padding:2px;overflow-y:auto}
.pembelajaran-page .pertemuan-card-item{min-width:0;padding:10px;border:1px solid #ece1e5;border-radius:9px;background:#fffafa;text-align:center}
.pembelajaran-page .pertemuan-card-num{font-size:11.5px;font-weight:750;color:var(--maroon)}
.pembelajaran-page .pertemuan-card-date{margin-top:3px;color:#827883;font-size:10.5px}
.pembelajaran-page .pertemuan-card-badge{display:inline-flex;margin-top:6px;padding:3px 9px;border-radius:999px;font-size:10.5px;font-weight:700}
.pembelajaran-page .abs-periode{margin:0 0 14px;border:1px solid #eee4e7;border-radius:10px;overflow:hidden}
.pembelajaran-page .abs-periode-item{display:flex;align-items:center;flex-wrap:wrap;gap:6px 11px;padding:8px 12px;border-bottom:1px solid #f3ebed;font-size:11px}
.pembelajaran-page .abs-periode-item:last-child{border-bottom:0}
.pembelajaran-page .abs-periode-nama{flex:1 1 140px;min-width:0;font-weight:700;overflow-wrap:anywhere}
.pembelajaran-page .abs-periode-detail{color:#827883}
.pembelajaran-page .abs-periode-persen{padding:3px 9px;border-radius:999px;color:var(--maroon);background:#fff5e5;font-weight:700}
.pembelajaran-page .certificate-wrapper{width:min(100%,740px);max-width:740px;padding:25px;border:7px double #800b22;background:#fffcf8;text-align:center;overflow-wrap:anywhere}
/* Responsif berdasarkan lebar KONTEN, bukan lebar browser (sidebar bisa terbuka). */
@container (max-width:1050px){
 .pembelajaran-page .stats-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
 .pembelajaran-page .mapel-grid,.pembelajaran-page .schedule-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
 .pembelajaran-page .mapel-meta-row{grid-template-columns:1fr}
}
@container (max-width:800px){
 .pembelajaran-page .mapel-grid,.pembelajaran-page .schedule-grid{grid-template-columns:minmax(0,1fr)}
 .pembelajaran-page .card-header{align-items:flex-start}
 .pembelajaran-page .tabs-header-wrap{padding:9px}
}
@container (max-width:650px){
 .pembelajaran-page .page-description{font-size:12px}
 .pembelajaran-page .stat-card{padding:12px}
 .pembelajaran-page .stat-number{font-size:18px}
 .pembelajaran-page .card-header{padding:13px 14px}
 .pembelajaran-page .mapel-grid,.pembelajaran-page .schedule-grid{padding:0 13px 14px}
 .pembelajaran-page .mapel-card{padding:13px}
 .pembelajaran-page .table-responsive{width:calc(100% - 26px);margin:0 13px 14px;overflow:visible;border:0;border-radius:0}
 .pembelajaran-page .custom-table{width:100%;min-width:0;display:block;background:transparent}
 .pembelajaran-page .custom-table thead{display:none}
 .pembelajaran-page .custom-table tbody{display:grid;gap:10px}
 .pembelajaran-page .custom-table tbody tr,.pembelajaran-page .custom-table tbody tr.kelompok-saya{display:block;padding:10px 12px;border:1px solid #eadde1;border-radius:10px;background:#fff;overflow:hidden}
 .pembelajaran-page .custom-table tbody td{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;width:100%;padding:7px 0;border-bottom:1px dashed #f0e5e9;text-align:right !important;min-width:0}
 .pembelajaran-page .custom-table tbody td:not([colspan])::before{content:attr(data-label);flex:0 0 42%;color:#897b85;font-size:10.5px;font-weight:700;text-align:left}
 .pembelajaran-page .custom-table tbody td:last-child{border-bottom:0}
 .pembelajaran-page .custom-table tbody td[colspan]{display:block;text-align:center!important}
 .pembelajaran-page #tab-tugas .abs-ringkasan{grid-template-columns:repeat(2,minmax(0,1fr));margin:0 13px 13px}
 .pembelajaran-page .calendar-card{padding:10px}
 .pembelajaran-page .calendar-day{min-height:34px;font-size:11px}
 .pembelajaran-page .graduation-banner{padding:15px;gap:11px}
 .pembelajaran-page .pertemuan-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
 .pembelajaran-page .modal-top{padding:13px 14px}
 .pembelajaran-page .modal-inner{padding:13px 14px}
 .pembelajaran-page .modal-footer-btns{padding:12px 14px}
 .pembelajaran-page .abs-ringkasan{grid-template-columns:repeat(2,minmax(0,1fr))}
 .pembelajaran-page .certificate-wrapper{padding:15px;border-width:5px}
}
@container (max-width:390px){
 .pembelajaran-page .stats-grid{gap:8px}
 .pembelajaran-page .stat-card > div:first-child{grid-template-columns:1fr;gap:6px}
 .pembelajaran-page .stat-icon{grid-row:auto;width:30px;height:30px}
 .pembelajaran-page .stat-number,.pembelajaran-page .stat-label{grid-column:1}
 .pembelajaran-page .tab-btn{font-size:11px;padding:8px 10px}
 .pembelajaran-page .calendar-grid{gap:2px}
 .pembelajaran-page .calendar-day{min-height:31px}
}
@media (prefers-reduced-motion:reduce){
 .pembelajaran-page *{transition:none!important;animation:none!important}
}
/* Sertifikat: ketika dicetak, sembunyikan panel lainnya */
@media print{
 body *{visibility:hidden!important}
 #printableCertificate,#printableCertificate *{visibility:visible!important}
 #printableCertificate{position:fixed!important;left:0!important;top:0!important;width:100%!important;max-width:none!important;max-height:none!important;overflow:visible!important;border:8px double #800b22!important;background:#fffcf8!important;padding:35px!important;box-shadow:none!important}
 #printableCertificate .modal-footer-btns,#printableCertificate .modal-close-btn{display:none!important}
}
</style>
@endpush

@section('content')
<div class="pembelajaran-page">
        <div class="page-header-row">
            <div>
                <nav class="breadcrumb" aria-label="Navigasi halaman"><a href="{{ route('dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><span aria-current="page">Pembelajaran Mahasiswa</span></nav>
                <h1 class="page-title">Pembelajaran Mahasiswa</h1>
                <p class="page-description">
                    Mata pelajaran, jadwal tatap muka, materi, dan tugas yang ditetapkan Musyrifah untuk kelompok belajar Anda di Ma'had Al-Jami'ah, beserta progres pertemuan dan status sertifikat kelulusan.
                </p>
            </div>
</div>

        @if($belumAdaKelompok)
            {{-- Mahasiswa belum ditempatkan Musyrifah pada kelompok belajar mana pun --}}
            <div class="card" style="text-align: center; padding: 48px 24px;">
                <div style="font-size: 44px; line-height: 1;">👥</div>
                <h2 class="card-title" style="justify-content: center; margin-top: 14px;">Kelompok Belajar Belum Ditetapkan</h2>
                <p class="card-desc" style="max-width: 640px; margin: 10px auto 0;">
                    Anda belum ditempatkan pada kelompok belajar mata pelajaran Ma'had Al-Jami'ah. Data pembelajaran
                    (mata pelajaran, jadwal, materi, dan tugas) baru akan tampil setelah Musyrifah menetapkan kelompok
                    belajar Anda pada setiap mata pelajaran.
                </p>
                <div style="margin-top: 18px;">
                    <span class="badge-pill badge-amber">Menunggu penetapan kelompok oleh Musyrifah</span>
                </div>
            </div>
        @else
        <!-- STATISTIK RINGKASAN PEMBELAJARAN SAYA -->
        <section class="stats-grid" aria-label="Statistik Pembelajaran">
            <div class="stat-card">
                <div>
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21z"/><path d="M4 5.5v13A2.5 2.5 0 0 1 6.5 16H20M8 7h8M8 10h8"/></svg>
                    </div>
                    <div class="stat-number">{{ $totalMataPelajaran }}</div>
                    <div class="stat-label">Mata Pelajaran Kurikulum</div>
                </div>
                <div class="stat-subtext">{{ $totalMataPelajaran }} Program Pembelajaran Sesuai Kelompok</div>
            </div>

            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="color: #2563eb;">
                        <svg viewBox="0 0 24 24"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20"/><circle cx="10" cy="7" r="3.5"/><path d="M17 4.2a3.5 3.5 0 0 1 0 6.6M17 15h1.5a3.5 3.5 0 0 1 3.5 3.5V20"/></svg>
                    </div>
                    <div class="stat-number">{{ $jumlahKelompokSaya }}</div>
                    <div class="stat-label">Kelompok Belajar Saya</div>
                </div>
                <div class="stat-subtext">{{ $kelompokSaya }}</div>
            </div>

            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="color: #10b981;">
                        <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18M12 13v3l2 1"/></svg>
                    </div>
                    <div class="stat-number">{{ $targetPertemuan }} TM</div>
                    <div class="stat-label">Target per Mata Pelajaran</div>
                </div>
                <div class="stat-subtext">Total Target: {{ $totalTargetKehadiran }} Pertemuan</div>
            </div>

            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="color: #f59e0b;">
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <div class="stat-number">{{ $totalHadirSeluruh }} / {{ $totalTargetKehadiran }}</div>
                    <div class="stat-label">Total Kehadiran Saya</div>
                </div>
                <div class="stat-subtext">
                    @if($isLulus)
                        <span style="color: #15803d; font-weight: 700;">✓ Memenuhi Syarat Kelulusan</span>
                    @else
                        <span style="color: #b45309; font-weight: 700;">⏳ Dalam Proses (Kurang {{ $mapelBelumLulus }} Mapel)</span>
                    @endif
                </div>
            </div>
        </section>

        <!-- TABS NAVIGASI FITUR PEMBELAJARAN SINKRON -->
        <div class="tabs-header-wrap">
            <nav class="tabs-nav" aria-label="Tab Menu Pembelajaran">
                <button type="button" class="tab-btn active" data-tab="tab-mapel" onclick="switchTab('tab-mapel', this)">
                    📚 1. Mata Pelajaran &amp; Progres
                </button>
                <button type="button" class="tab-btn" data-tab="tab-jadwal" onclick="switchTab('tab-jadwal', this)">
                    🗓️ 2. Jadwal Pembelajaran Saya
                </button>
                <button type="button" class="tab-btn" data-tab="tab-materi" onclick="switchTab('tab-materi', this)">
                    📖 3. Materi Pembelajaran
                </button>
                <button type="button" class="tab-btn" data-tab="tab-tugas" onclick="switchTab('tab-tugas', this)">
                    📝 4. Tugas Saya
                </button>
                <button type="button" class="tab-btn" data-tab="tab-kelompok" onclick="switchTab('tab-kelompok', this)">
                    👥 5. Kelompok &amp; Pengajar
                </button>
                <button type="button" class="tab-btn" data-tab="tab-kelulusan" onclick="switchTab('tab-kelulusan', this)">
                    🎓 6. Kelulusan &amp; Sertifikat
                </button>
                <button type="button" class="tab-btn" data-tab="tab-kalender" onclick="switchTab('tab-kalender', this)">
                    📅 7. Kalender Pembelajaran
                </button>
            </nav>

            {{-- Penetapan kelompok hanya dapat diubah oleh Musyrifah, bukan oleh mahasiswa. --}}
            <div class="kelompok-select-form">
                <span class="kelompok-select-label">Penetapan Musyrifah:</span>
                <strong style="font-size: 13px; color: var(--maroon);">{{ $kelompokSaya }}</strong>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- TAB 1: MATA PELAJARAN KELOMPOK & PROGRES            -->
        <!-- ==================================================== -->
        <section class="tab-pane active" id="tab-mapel">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Mata Pelajaran Kelompok Belajar Saya</h2>
                        <p class="card-desc">Mata pelajaran beserta kelompok belajar yang ditetapkan Musyrifah dan capaian pertemuan tatap muka Anda.</p>
                    </div>
                    <span class="badge-pill badge-maroon" style="font-size: 13px;">{{ $kelompokSaya }}</span>
                </div>

                <div class="mapel-grid">
                    @forelse($daftarMataPelajaran as $m)
                        <div class="mapel-card">
                            <div>
                                <div class="mapel-card-top">
                                    <span class="badge-pill badge-blue">{{ $m['kategori'] }}</span>
                                    <span class="badge-pill badge-green">{{ $m['kelompok'] }}</span>
                                    <span class="badge-pill badge-maroon" style="font-weight: 800;">{{ $m['kode'] }}</span>
                                </div>

                                <h3 class="mapel-title">{{ $m['nama'] }}</h3>
                                <p class="mapel-desc">{{ $m['deskripsi'] ?: 'Program pembelajaran penguatan keilmuan ma\'had al-jami\'ah.' }}</p>

                                <div class="mapel-meta-row">
                                    <div class="mapel-meta-item">
                                        <span>👤 <strong>{{ $m['guru_pengampu'] }}</strong></span>
                                    </div>
                                    <div class="mapel-meta-item">
                                        <span>🗓️ {{ $m['ada_jadwal'] ? $m['hari'].', '.$m['jam'] : 'Jadwal belum ditetapkan' }}</span>
                                    </div>
                                    <div class="mapel-meta-item">
                                        <span>📍 {{ $m['ruangan'] }}</span>
                                    </div>
                                    <div class="mapel-meta-item">
                                        <span>📖 Materi: {{ $m['jumlah_materi'] }} · 📝 Tugas: {{ $m['tugas_terkumpul'] }}/{{ $m['jumlah_tugas'] }}</span>
                                    </div>
                                </div>

                                <div class="progress-block">
                                    <div class="progress-header">
                                        <span>Progres Kehadiran {{ $m['target_pertemuan'] }} TM:</span>
                                        <strong>{{ $m['hadir'] }} / {{ $m['target_pertemuan'] }} TM ({{ $m['persentase'] }}%)</strong>
                                    </div>
                                    <div class="progress-bar-wrap">
                                        <div class="progress-bar-fill {{ $m['is_lulus'] ? 'complete' : '' }}" data-progress="{{ $m['persentase'] }}"></div>
                                    </div>
                                    <div class="progress-pills">
                                        <span class="pill-stat hadir">Hadir: {{ $m['hadir'] }}</span>
                                        <span class="pill-stat izin">Izin: {{ $m['izin'] }}</span>
                                        <span class="pill-stat sakit">Sakit: {{ $m['sakit'] }}</span>
                                        <span class="pill-stat alpa">Alpa: {{ $m['alpa'] }}</span>
                                        @if($m['is_lulus'])
                                            <span class="badge-pill badge-green" style="margin-left: auto;">✓ Memenuhi Syarat</span>
                                        @else
                                            <span class="badge-pill badge-amber" style="margin-left: auto;">Kurang {{ $m['sisa_pertemuan'] }} TM</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn-view-detail" data-mapel-id="{{ $m['id'] }}" data-mapel-nama="{{ $m['nama'] }}" data-mapel-kode="{{ $m['kode'] }}" onclick="openDetailPertemuan(this.dataset.mapelId, this.dataset.mapelNama, this.dataset.mapelKode)">
                                Lihat Kehadiran &amp; Pertemuan
                            </button>
                        </div>
                    @empty
                        <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">
                            Musyrifah belum menetapkan mata pelajaran untuk kelompok belajar Anda.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 2: JADWAL PEMBELAJARAN SAYA & MINGGUAN           -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-jadwal">
            <!-- JADWAL TERDEKAT / HARI INI -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Jadwal Tatap Muka Terdekat (Hari Ini &amp; Mendatang)</h2>
                        <p class="card-desc">Jadwal pembelajaran aktif untuk {{ $kelompokSaya }} yang telah ditetapkan Musyrifah.</p>
                    </div>
                </div>

                <div class="schedule-grid">
                    @forelse($jadwalTerdekat as $jdw)
                        <div class="schedule-card-item">
                            <div class="schedule-time-box">
                                <span class="schedule-day-label">{{ $jdw['status_hari'] }}</span>
                                <span class="schedule-date-num">{{ $jdw['tanggal'] }}</span>
                                <span style="font-size: 10px; font-weight: 600;">{{ $jdw['bulan'] }}</span>
                            </div>

                            <div class="schedule-details">
                                <h4>{{ $jdw['nama'] }}</h4>
                                <p>⏰ <strong>{{ $jdw['waktu'] }}</strong></p>
                                <p>👤 Guru: {{ $jdw['guru'] }}</p>
                                <p>📍 Ruangan: {{ $jdw['lokasi'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1/-1; text-align: center; padding: 30px; color: #888;">
                            Belum ada jadwal tatap muka untuk kelompok belajar Anda.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- JADWAL LENGKAP KELOMPOK SAYA -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Daftar Jadwal Lengkap: {{ $kelompokSaya }}</h2>
                        <p class="card-desc">Jadwal mingguan mata pelajaran sesuai kelompok belajar yang ditetapkan Musyrifah.</p>
                    </div>
                    <span class="badge-pill badge-green">{{ $jadwalSaya->count() }} Sesi Terjadwal</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelompok</th>
                                <th>Guru Pengampu</th>
                                <th>Ruangan</th>
                                <th>Periode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalSaya as $idx => $js)
                                <tr>
                                    <td data-label="No">{{ $idx + 1 }}</td>
                                    <td style="font-weight: 700; color: var(--maroon);" data-label="Hari">{{ $js->hari }}</td>
                                    <td style="font-weight: 600;" data-label="Waktu">{{ $js->jamTeks() }}</td>
                                    <td style="text-align: left; font-weight: 600;" data-label="Mata Pelajaran">{{ $js->namaMataPelajaran() }}</td>
                                    <td data-label="Kelompok"><span class="badge-pill badge-blue">{{ $js->kelompok }}</span></td>
                                    <td style="text-align: left;" data-label="Guru Pengampu">{{ $js->namaGuruPengampu() }}</td>
                                    <td data-label="Ruangan">{{ $js->ruangan ?: '-' }}</td>
                                    <td style="font-size: 12px; color: #64748b;" data-label="Periode">{{ $js->periode ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding: 30px; color: #888;">Belum ada jadwal untuk kelompok belajar Anda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 3: MATERI PEMBELAJARAN KELOMPOK SAYA             -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-materi">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Materi Pembelajaran Kelompok Saya</h2>
                        <p class="card-desc">Materi pertemuan yang dibagikan pengajar untuk {{ $kelompokSaya }}.</p>
                    </div>
                    <span class="badge-pill badge-green">{{ $materiPembelajaran->count() }} Materi</span>
                </div>

                @if($materiPembelajaran->isEmpty())
                    <div style="text-align: center; padding: 36px 18px; color: #888;">
                        Belum ada materi pembelajaran yang dibagikan untuk kelompok belajar Anda.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelompok</th>
                                    <th>Pertemuan</th>
                                    <th>Tanggal</th>
                                    <th>Judul Materi</th>
                                    <th>Ringkasan</th>
                                    <th>Pengajar</th>
                                    <th>Berkas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materiPembelajaran as $idx => $materi)
                                    <tr>
                                        <td data-label="No">{{ $idx + 1 }}</td>
                                        <td style="text-align: left; font-weight: 600;" data-label="Mata Pelajaran">{{ $materi['mapel'] }}</td>
                                        <td data-label="Kelompok"><span class="badge-pill badge-blue">{{ $materi['kelompok'] }}</span></td>
                                        <td data-label="Pertemuan">Ke-{{ $materi['pertemuan_ke'] }}</td>
                                        <td style="font-size: 12px; color: #64748b;" data-label="Tanggal">{{ $materi['tanggal'] }}</td>
                                        <td style="text-align: left;" data-label="Judul Materi">{{ $materi['judul'] }}</td>
                                        <td style="text-align: left; font-size: 12px; color: #475569;" data-label="Ringkasan">{{ \Illuminate\Support\Str::limit($materi['ringkasan'], 90) ?: '-' }}</td>
                                        <td style="text-align: left;" data-label="Pengajar">{{ $materi['guru'] }}</td>
                                        <td data-label="Berkas">
                                            @if($materi['ada_berkas'])
                                                <span class="badge-pill badge-green">Berkas tersedia</span>
                                            @else
                                                <span class="badge-pill badge-gray">Tanpa berkas</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 4: TUGAS KELOMPOK BELAJAR SAYA                   -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-tugas">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Tugas Kelompok Saya</h2>
                        <p class="card-desc">Tugas yang dipublikasikan pengajar untuk {{ $kelompokSaya }} beserta status pengumpulan Anda.</p>
                    </div>
                    <span class="badge-pill badge-maroon">{{ $statistikTugas['total'] }} Tugas</span>
                </div>

                <div class="abs-ringkasan" aria-label="Ringkasan Tugas">
                    <div class="abs-stat">
                        <span class="abs-stat-label">Total</span>
                        <strong>{{ $statistikTugas['total'] }}</strong>
                    </div>
                    <div class="abs-stat">
                        <span class="abs-stat-label">Belum</span>
                        <strong>{{ $statistikTugas['belum'] }}</strong>
                    </div>
                    <div class="abs-stat">
                        <span class="abs-stat-label">Terkumpul</span>
                        <strong>{{ $statistikTugas['terkumpul'] }}</strong>
                    </div>
                    <div class="abs-stat">
                        <span class="abs-stat-label">Dinilai</span>
                        <strong>{{ $statistikTugas['dinilai'] }}</strong>
                    </div>
                </div>

                @if($daftarTugas->isEmpty())
                    <div style="text-align: center; padding: 36px 18px; color: #888;">
                        Belum ada tugas yang dipublikasikan untuk kelompok belajar Anda.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelompok</th>
                                    <th>Judul Tugas</th>
                                    <th>Pengajar</th>
                                    <th>Batas Pengumpulan</th>
                                    <th>Status</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($daftarTugas as $idx => $item)
                                    @php($tugas = $item['tugas'])
                                    <tr>
                                        <td data-label="No">{{ $idx + 1 }}</td>
                                        <td style="text-align: left; font-weight: 600;" data-label="Mata Pelajaran">{{ $tugas->namaMataPelajaran() }}</td>
                                        <td data-label="Kelompok"><span class="badge-pill badge-blue">{{ $tugas->kelompok }}</span></td>
                                        <td style="text-align: left;" data-label="Judul Tugas">{{ $tugas->judul }}</td>
                                        <td style="text-align: left;" data-label="Pengajar">{{ $tugas->namaGuru() }}</td>
                                        <td style="font-size: 12px; color: #64748b;" data-label="Batas Pengumpulan">{{ $tugas->batasTeks() }}</td>
                                        <td data-label="Status">
                                            @if($item['status']['kelas'] === 'dinilai')
                                                <span class="badge-pill badge-green">{{ $item['status']['label'] }}</span>
                                            @elseif($item['status']['kelas'] === 'terkumpul')
                                                <span class="badge-pill badge-blue">{{ $item['status']['label'] }}</span>
                                            @elseif($item['status']['kelas'] === 'terlambat')
                                                <span class="badge-pill badge-amber">{{ $item['status']['label'] }}</span>
                                            @else
                                                <span class="badge-pill badge-gray">{{ $item['status']['label'] }}</span>
                                            @endif
                                        </td>
                                        <td style="font-weight: 700; color: var(--maroon);" data-label="Nilai">
                                            {{ $item['pengumpulan']?->nilai ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 5: KELOMPOK BELAJAR & PENGAJAR                   -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-kelompok">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Pengajar Kelompok Belajar Saya</h2>
                        <p class="card-desc">Pengajar yang ditetapkan Musyrifah pada jadwal {{ $kelompokSaya }}.</p>
                    </div>
                    <span class="badge-pill badge-maroon">{{ $kelompokSaya }}</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pengajar</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelompok</th>
                                <th>Jadwal</th>
                                <th>Ruangan</th>
                                <th>Beban Mengajar</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($daftarPengajarKelompok as $idx => $p)
                                <tr class="kelompok-saya">
                                    <td data-label="No">{{ $idx + 1 }}</td>
                                    <td style="text-align: left; font-weight: 700;" data-label="Nama Pengajar">
                                        {{ $p['nama'] }}
                                    </td>
                                    <td style="text-align: left;" data-label="Mata Pelajaran">{{ $p['program'] }}</td>
                                    <td data-label="Kelompok">
                                        <span class="badge-pill badge-blue">{{ $p['kelompok'] }}</span>
                                    </td>
                                    <td style="font-weight: 600;" data-label="Jadwal">{{ $p['jadwal'] }}</td>
                                    <td data-label="Ruangan">{{ $p['ruangan'] }}</td>
                                    <td style="font-weight: 600; color: var(--maroon);" data-label="Beban Mengajar">{{ $p['beban'] ?: '-' }}</td>
                                    <td style="font-size: 12px; color: #64748b; text-align: left;" data-label="Keterangan">{{ $p['keterangan'] ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding: 30px; color: #888;">Belum ada pengajar pada jadwal kelompok belajar Anda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 6: KELULUSAN & SERTIFIKAT PEMBELAJARAN           -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-kelulusan">
            <div class="graduation-banner {{ $isLulus ? 'lulus' : 'proses' }}">
                <div class="grad-icon-wrap">
                    @if($isLulus)
                        🎓
                    @else
                        ⏳
                    @endif
                </div>

                <div class="grad-content">
                    <div class="grad-title">
                        @if($isLulus)
                            Selamat! Anda Memenuhi Syarat Kelulusan Pembelajaran Ma'had Al-Jami'ah
                        @else
                            Status Kelulusan: Belum Memenuhi Syarat (Dalam Proses)
                        @endif
                    </div>
                    <p class="grad-desc">
                        @if($isLulus)
                            Anda telah hadir penuh pada seluruh pertemuan mata pelajaran kelompok belajar Anda. Sertifikat kelulusan Anda telah diterbitkan dan siap diunduh atau dicetak.
                        @else
                            Syarat Kelulusan: <strong>Wajib hadir pada seluruh pertemuan tatap muka di SELURUH mata pelajaran kelompok Anda</strong>. Saat ini capaian Anda: <strong>{{ $totalHadirSeluruh }} / {{ $totalTargetKehadiran }} Pertemuan</strong> (Masih kurang di {{ $mapelBelumLulus }} mata pelajaran).
                        @endif
                    </p>
                </div>

                <div>
                    @if($isLulus)
                        <button type="button" class="btn-certificate" onclick="openModalSertifikat()">
                            🎓 Lihat &amp; Cetak Sertifikat
                        </button>
                    @else
                        <button type="button" class="btn-certificate" disabled title="Sertifikat akan aktif setelah seluruh mata pelajaran kelompok Anda memenuhi target kehadiran">
                            🔒 Sertifikat Terkunci
                        </button>
                    @endif
                </div>
            </div>

            <!-- AUDIT KEHADIRAN PER MATA PELAJARAN -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Audit Capaian Kelulusan per Mata Pelajaran</h2>
                        <p class="card-desc">Rincian pemenuhan syarat kehadiran per mata pelajaran pada kelompok belajar Anda.</p>
                    </div>
                    <span class="badge-pill {{ $isLulus ? 'badge-green' : 'badge-amber' }}">
                        {{ $isLulus
                            ? 'Lulus Penuh (' . $totalMataPelajaran . '/' . $totalMataPelajaran . ' Mapel)'
                            : 'Progres: ' . ($totalMataPelajaran - $mapelBelumLulus) . '/' . $totalMataPelajaran . ' Mapel Lengkap' }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Mata Pelajaran</th>
                                <th>Target Wajib</th>
                                <th>Kehadiran Saya</th>
                                <th>Izin / Sakit</th>
                                <th>Alpa</th>
                                <th>Progres (%)</th>
                                <th>Status Syarat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daftarMataPelajaran as $idx => $m)
                                <tr>
                                    <td data-label="No">{{ $idx + 1 }}</td>
                                    <td style="font-weight: 700; color: var(--maroon);" data-label="Kode">{{ $m['kode'] }}</td>
                                    <td style="text-align: left; font-weight: 600;" data-label="Mata Pelajaran">{{ $m['nama'] }}</td>
                                    <td data-label="Target Wajib">{{ $m['target_pertemuan'] }} Pertemuan</td>
                                    <td style="font-weight: 700; color: #15803d;" data-label="Kehadiran Saya">{{ $m['hadir'] }} TM</td>
                                    <td data-label="Izin / Sakit">{{ $m['izin'] + $m['sakit'] }} TM</td>
                                    <td data-label="Alpa">{{ $m['alpa'] }} TM</td>
                                    <td style="font-weight: 700;" data-label="Progres (%)">{{ $m['persentase'] }}%</td>
                                    <td data-label="Status Syarat">
                                        @if($m['is_lulus'])
                                            <span class="badge-pill badge-green">✓ Memenuhi Syarat</span>
                                        @else
                                            <span class="badge-pill badge-amber">Kurang {{ $m['sisa_pertemuan'] }} Pertemuan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 7: KALENDER PEMBELAJARAN DINAMIS                -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-kalender">
            <div class="card" style="display: flex; flex-direction: column; align-items: center;">
                <div class="calendar-card">
                    <div class="calendar-heading">
                        <h3>Kalender Jadwal Pembelajaran Mahasiswa</h3>
                        <span class="badge-pill badge-maroon">{{ $kelompokSaya }}</span>
                    </div>

                    <div class="calendar-toolbar">
                        <button type="button" id="prevMonth" aria-label="Bulan sebelumnya">‹</button>
                        <strong id="calendarTitle">Memuat...</strong>
                        <button type="button" id="nextMonth" aria-label="Bulan berikutnya">›</button>
                    </div>

                    <div class="calendar-grid" id="calendarGrid" data-calendar-events='@json($calendarEvents ?? [])'></div>

                    <div class="calendar-legend">
                        <span class="legend-dot"></span>
                        <span>Hari dengan jadwal tatap muka kelompok belajar Anda ({{ $kelompokSaya }})</span>
                    </div>

                    <div class="calendar-info-box" id="calendarDayInfo">
                        <em>Klik tanggal yang memiliki titik untuk melihat detail mata pelajaran dan waktu pembelajaran.</em>
                    </div>
                </div>
            </div>
        </section>

    <!-- ======================================================== -->
    <!-- MODAL 1: KEHADIRAN & RINCIAN PERTEMUAN MAHASISWA         -->
    <!-- ======================================================== -->
    <div class="modal-overlay" id="modalDetailPertemuan" onclick="closeModalOnBackdrop(event, 'modalDetailPertemuan')">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-top">
                <div class="modal-top-title">Kehadiran &amp; Pertemuan Pembelajaran</div>
                <button type="button" class="modal-close-btn" aria-label="Tutup modal" onclick="closeModal('modalDetailPertemuan')">&times;</button>
            </div>
            <div class="modal-inner">
                <div style="background: #fdf8f9; border: 1px solid #f1d5dc; border-radius: 12px; padding: 14px 18px; margin-bottom: 12px;">
                    <div style="font-size: 17px; font-weight: 700; color: var(--maroon);" id="detailMapelTitle">—</div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 3px;" id="detailMapelSub">Memuat target pertemuan...</div>
                </div>

                {{-- Ringkasan kehadiran: total per status dan persentase periode berjalan --}}
                <div class="abs-ringkasan" aria-live="polite">
                    <div class="abs-stat hadir">
                        <span class="abs-stat-label">Hadir</span>
                        <strong id="absHadir">0</strong>
                    </div>
                    <div class="abs-stat izin">
                        <span class="abs-stat-label">Izin</span>
                        <strong id="absIzin">0</strong>
                    </div>
                    <div class="abs-stat sakit">
                        <span class="abs-stat-label">Sakit</span>
                        <strong id="absSakit">0</strong>
                    </div>
                    <div class="abs-stat alpa">
                        <span class="abs-stat-label">Alpa</span>
                        <strong id="absAlpa">0</strong>
                    </div>
                    <div class="abs-stat abs-stat--persen">
                        <span class="abs-stat-label">Persentase</span>
                        <strong id="absPersen">0%</strong>
                        <span class="abs-stat-note" id="absTotal">0 pertemuan tercatat</span>
                    </div>
                </div>

                {{-- Rekap persentase per periode pembelajaran --}}
                <div class="abs-periode" id="periodeKehadiran"></div>

                <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                    Riwayat Kehadiran Setiap Pertemuan:
                </div>
                <div class="pertemuan-grid" id="gridPertemuanDetail">
                    <div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #64748b;">Memuat data pertemuan...</div>
                </div>
            </div>
            <div class="modal-footer-btns">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalDetailPertemuan')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 2: SERTIFIKAT KELULUSAN MA'HAD AL-JAMI'AH          -->
    <!-- ======================================================== -->
    <div class="modal-overlay" id="modalSertifikat" onclick="closeModalOnBackdrop(event, 'modalSertifikat')">
        <div class="modal-card certificate-wrapper" id="printableCertificate" onclick="event.stopPropagation()">
            <div style="display: flex; justify-content: flex-end;">
                <button type="button" class="modal-close-btn" aria-label="Tutup modal" onclick="closeModal('modalSertifikat')">&times;</button>
            </div>

            <div style="color: #790c20; font-size: 26px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
                SERTIFIKAT KELULUSAN
            </div>
            <div style="font-size: 14px; font-weight: 700; color: #333; margin-top: 4px; letter-spacing: 0.5px;">
                MA'HAD AL-JAMI'AH UIN IMAM BONJOL PADANG
            </div>
            <div style="font-size: 12px; color: #777; margin-top: 2px;">
                Nomor: {{ $nomorSertifikat }}
            </div>

            <hr style="margin: 22px auto; border: none; border-top: 2px solid #790c20; width: 60%;">

            <p style="font-size: 14px; color: #444; margin-bottom: 8px;">Diberikan dengan penuh kehormatan kepada:</p>
            <div style="font-size: 24px; font-weight: 800; color: #111; text-decoration: underline;">
                {{ $user->name ?? 'Mahasiswa Ma\'had' }}
            </div>
            <div style="font-size: 15px; font-weight: 600; color: #555; margin-top: 4px;">
                NIM: {{ $user->nim ?? '-' }} | {{ $kelompokSaya }}
            </div>

            <p style="font-size: 14px; color: #333; margin-top: 24px; line-height: 1.7; max-width: 580px; margin-left: auto; margin-right: auto;">
                Telah <strong>MEMENUHI SYARAT KELULUSAN</strong> pada seluruh program pembelajaran Ma'had Al-Jami'ah yang menjadi kelompok belajarnya dengan kehadiran penuh pada setiap pertemuan mata pelajaran.
            </p>

            <div style="display: flex; justify-content: space-around; margin-top: 40px; padding-top: 10px;">
                <div>
                    <div style="font-size: 12px; color: #777;">Mudir Ma'had Al-Jami'ah</div>
                    <div style="height: 50px;"></div>
                    <div style="font-weight: 700; font-size: 14px; border-top: 1px solid #333; padding-top: 4px;">
                        Dr. Zulkifli, M.A.
                    </div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #777;">Koordinator Musyrifah</div>
                    <div style="height: 50px;"></div>
                    <div style="font-weight: 700; font-size: 14px; border-top: 1px solid #333; padding-top: 4px;">
                        Ustadzah Musyrifah
                    </div>
                </div>
            </div>

            <div class="modal-footer-btns" style="background: transparent; border-top: none; justify-content: center; margin-top: 26px;">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalSertifikat')">Tutup</button>
                <button type="button" class="btn-certificate" onclick="window.print()">🖨️ Cetak Sertifikat</button>
            </div>
        </div>
    </div>

        @endif

</div>
@endsection

@push('scripts')
    <script>
        // TAB SWITCHER
        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            const target = document.getElementById(tabId);
            if (target) {
                target.classList.add('active');
            }

            if (tabId === 'tab-kalender') {
                renderCalendar();
            }
        }

        // Encode nilai dari database sebelum masuk ke konten HTML dinamis.
        function escapeHTML(value) {
            return String(value ?? '').replace(/[&<>"']/g, ch => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[ch]));
        }

        // MODAL HELPERS
        function openModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.add('active');
        }

        function closeModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.remove('active');
        }

        function closeModalOnBackdrop(event, id) {
            if (event.target.id === id) {
                closeModal(id);
            }
        }

        function openModalSertifikat() {
            openModal('modalSertifikat');
        }

        // KEHADIRAN PER MATA PELAJARAN (AJAX/FETCH)
        function formatPersen(nilai) {
            return String(Number(nilai || 0)).replace('.', ',') + '%';
        }

        function resetRingkasanKehadiran() {
            ['absHadir', 'absIzin', 'absSakit', 'absAlpa'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.textContent = '0';
            });

            const persen = document.getElementById('absPersen');
            if (persen) persen.textContent = '0%';

            const total = document.getElementById('absTotal');
            if (total) total.textContent = 'Memuat data kehadiran...';

            const periode = document.getElementById('periodeKehadiran');
            if (periode) periode.innerHTML = '';
        }

        function tampilkanRingkasanKehadiran(data) {
            const ringkasan = data.ringkasan || {};

            const jumlah = {
                absHadir: ringkasan.hadir || 0,
                absIzin: ringkasan.izin || 0,
                absSakit: ringkasan.sakit || 0,
                absAlpa: ringkasan.alpa || 0,
            };

            Object.keys(jumlah).forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.textContent = jumlah[id];
            });

            const persen = document.getElementById('absPersen');
            if (persen) persen.textContent = formatPersen(ringkasan.persentase);

            const total = document.getElementById('absTotal');
            if (total) {
                total.textContent = (ringkasan.total || 0) + ' dari ' + (ringkasan.target || 16) + ' pertemuan';
            }

            const wadah = document.getElementById('periodeKehadiran');
            if (!wadah) return;

            wadah.innerHTML = '';
            const daftar = data.periode || [];

            if (!daftar.length) {
                wadah.style.display = 'none';
                return;
            }

            wadah.style.display = 'block';

            daftar.forEach(function (item) {
                const baris = document.createElement('div');
                baris.className = 'abs-periode-item';

                const nama = document.createElement('span');
                nama.className = 'abs-periode-nama';
                nama.textContent = item.periode || 'Periode Pembelajaran';

                const detail = document.createElement('span');
                detail.className = 'abs-periode-detail';
                detail.textContent = 'H ' + (item.hadir || 0) + ' · I ' + (item.izin || 0) +
                    ' · S ' + (item.sakit || 0) + ' · A ' + (item.alpa || 0);

                const persenPeriode = document.createElement('span');
                persenPeriode.className = 'abs-periode-persen';
                persenPeriode.textContent = formatPersen(item.persentase);

                baris.appendChild(nama);
                baris.appendChild(detail);
                baris.appendChild(persenPeriode);
                wadah.appendChild(baris);
            });
        }

        function openDetailPertemuan(mapelId, namaMapel, kodeMapel) {
            document.getElementById('detailMapelTitle').textContent = namaMapel + ' (' + kodeMapel + ')';
            document.getElementById('detailMapelSub').textContent = 'Target: 16 Pertemuan Tatap Muka Ma\'had';

            const grid = document.getElementById('gridPertemuanDetail');
            grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 25px; color: #64748b;">Memuat data kehadiran...</div>';

            resetRingkasanKehadiran();
            openModal('modalDetailPertemuan');

            fetch(`{{ url('/pembelajaran-mhs/detail-pertemuan') }}/${mapelId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.pertemuan) {
                        tampilkanRingkasanKehadiran(data);

                        const mapel = data.mapel || {};
                        document.getElementById('detailMapelSub').textContent =
                            (mapel.periode ? 'Periode ' + mapel.periode + ' · ' : '') +
                            'Target ' + (mapel.target_pertemuan || 16) + ' Pertemuan Tatap Muka';

                        grid.innerHTML = '';
                        data.pertemuan.forEach(p => {
                            let badgeStyle = 'background: #e2e8f0; color: #334155;';
                            let statusText = 'Terjadwal';

                            if (p.status === 'hadir') {
                                badgeStyle = 'background: #dcfce7; color: #15803d;';
                                statusText = 'Hadir';
                            } else if (p.status === 'izin') {
                                badgeStyle = 'background: #dbeafe; color: #1d4ed8;';
                                statusText = 'Izin';
                            } else if (p.status === 'sakit') {
                                badgeStyle = 'background: #fef3c7; color: #b45309;';
                                statusText = 'Sakit';
                            } else if (p.status === 'alpa') {
                                badgeStyle = 'background: #fee2e2; color: #b91c1c;';
                                statusText = 'Alpa';
                            }

                            const card = document.createElement('div');
                            card.className = 'pertemuan-card-item';
                            card.innerHTML = `
                                <div class="pertemuan-card-num">Pertemuan ${escapeHTML(p.pertemuan_ke)}</div>
                                <div class="pertemuan-card-date">${escapeHTML(p.tanggal)}</div>
                                <span class="pertemuan-card-badge" style="${badgeStyle}">
                                    ${statusText}
                                </span>
                            `;
                            grid.appendChild(card);
                        });
                    } else {
                        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #ef4444; padding: 20px;">Gagal memuat data kehadiran.</div>';
                    }
                })
                .catch(() => {
                    grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #ef4444; padding: 20px;">Terjadi kesalahan saat memuat data.</div>';
                });
        }

        // ============================================
        // KALENDER DINAMIS
        // ============================================
        // Data dari Blade dibaca sebagai JSON, bukan disisipkan sebagai sintaks JavaScript.
        const calendarEvents = JSON.parse(document.getElementById('calendarGrid')?.dataset.calendarEvents || '[]');

        // Lebar progres tetap mengikuti persentase dari data pembelajaran.
        document.querySelectorAll('.pembelajaran-page .progress-bar-fill[data-progress]').forEach(function (bar) {
            bar.style.width = bar.dataset.progress + '%';
        });

        let shownDate = new Date();
        let shownYear = shownDate.getFullYear();
        let shownMonth = shownDate.getMonth();

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April',
            'Mei', 'Juni', 'Juli', 'Agustus',
            'September', 'Oktober', 'November', 'Desember'
        ];

        function renderCalendar() {
            const grid = document.getElementById('calendarGrid');
            const title = document.getElementById('calendarTitle');
            if (!grid || !title) return;

            const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

            title.textContent = monthNames[shownMonth] + ' ' + shownYear;
            grid.innerHTML = '';

            // Header nama hari
            days.forEach(day => {
                const el = document.createElement('div');
                el.className = 'day-name';
                el.textContent = day;
                grid.appendChild(el);
            });

            const firstDay = new Date(shownYear, shownMonth, 1).getDay();
            const daysInMonth = new Date(shownYear, shownMonth + 1, 0).getDate();
            const daysInPrevMonth = new Date(shownYear, shownMonth, 0).getDate();

            const today = new Date();
            const cells = Math.ceil((firstDay + daysInMonth) / 7) * 7;

            for (let i = 0; i < cells; i++) {
                const element = document.createElement('div');
                element.className = 'calendar-day';

                let dayNum;
                let dateKey = '';

                if (i < firstDay) {
                    dayNum = daysInPrevMonth - firstDay + i + 1;
                    element.classList.add('muted');
                } else if (i >= firstDay + daysInMonth) {
                    dayNum = i - firstDay - daysInMonth + 1;
                    element.classList.add('muted');
                } else {
                    dayNum = i - firstDay + 1;
                    const mm = String(shownMonth + 1).padStart(2, '0');
                    const dd = String(dayNum).padStart(2, '0');
                    dateKey = `${shownYear}-${mm}-${dd}`;

                    if (
                        today.getFullYear() === shownYear &&
                        today.getMonth() === shownMonth &&
                        today.getDate() === dayNum
                    ) {
                        element.classList.add('today');
                    }

                    if (calendarEvents[dateKey] && calendarEvents[dateKey].length > 0) {
                        element.classList.add('has-event');
                        element.title = `${calendarEvents[dateKey].length} kelas terjadwal`;
                        element.addEventListener('click', function () {
                            showDaySchedule(dateKey, calendarEvents[dateKey]);
                        });
                    }
                }

                element.textContent = dayNum;
                grid.appendChild(element);
            }
        }

        function showDaySchedule(dateKey, classes) {
            const box = document.getElementById('calendarDayInfo');
            if (!box) return;

            const parts = dateKey.split('-');
            const formatted = `${parts[2]} ${monthNames[parseInt(parts[1], 10) - 1]} ${parts[0]}`;

            let html = `<strong>Jadwal Pembelajaran pada ${formatted}:</strong><ul style="margin: 8px 0 0 18px; line-height: 1.6;">`;
            classes.forEach(c => {
                html += `<li><strong>${escapeHTML(c.nama)}</strong> (${escapeHTML(c.waktu)}) — Guru: ${escapeHTML(c.guru)} | Ruang: ${escapeHTML(c.ruangan)}</li>`;
            });
            html += `</ul>`;
            box.innerHTML = html;
        }

        document.getElementById('prevMonth')?.addEventListener('click', function () {
            shownMonth--;
            if (shownMonth < 0) {
                shownMonth = 11;
                shownYear--;
            }
            renderCalendar();
        });

        document.getElementById('nextMonth')?.addEventListener('click', function () {
            shownMonth++;
            if (shownMonth > 11) {
                shownMonth = 0;
                shownYear++;
            }
            renderCalendar();
        });

        renderCalendar();
    </script>
@endpush
