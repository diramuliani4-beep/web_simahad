@extends('layouts.musyrifah')

@section('title', 'Manajemen Pembelajaran')

@section('content')

<style>
/* ==============================================================
   SIMAHAD — MANAJEMEN PEMBELAJARAN MUSYRIFAH
   Tampilan seragam Admin | CSS dibatasi ke modul ini & modalnya.
   Semua route, Blade, modal, dan fungsi JavaScript asli dipertahankan.
   ============================================================== */
.musyrifah-pembelajaran {
    --mp-maroon: #800b22;
    --mp-maroon-hover: #64081b;
    --mp-ink: #20242e;
    --mp-muted: #78808d;
    --mp-border: #eadcdf;
    --mp-surface: #fff;
    width: 100%; max-width: 100%; min-width: 0;
    padding: 0 0 36px;
    margin: 0;
    color: var(--mp-ink);
    font-family: 'Poppins', Arial, sans-serif;
    font-size: 13px;
    container-type: inline-size;
    isolation: isolate;
}
.musyrifah-pembelajaran *, .musyrifah-pembelajaran *::before, .musyrifah-pembelajaran *::after,
.modal-overlay *, .modal-overlay *::before, .modal-overlay *::after {box-sizing: border-box;}
.musyrifah-pembelajaran > *, .musyrifah-pembelajaran .mp-card, .musyrifah-pembelajaran .mp-tab-panel,
.musyrifah-pembelajaran .mp-table-wrap {min-width: 0; max-width: 100%;}

/* JUDUL & BREADCRUMB */
.musyrifah-pembelajaran .mp-heading {
    display: block; width: 100%; margin: 0 0 24px; padding: 0 0 20px;
    border-bottom: 1px solid #e8ced4;
}
.musyrifah-pembelajaran .mp-breadcrumb {
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    margin: 0 0 12px; color: #8c8b96; font-size: 12px;
}
.musyrifah-pembelajaran .mp-breadcrumb a {color: var(--mp-maroon); text-decoration: none;}
.musyrifah-pembelajaran .mp-breadcrumb a:hover {text-decoration: underline;}
.musyrifah-pembelajaran .mp-breadcrumb span[aria-hidden] {color: #b8a5aa;}
.musyrifah-pembelajaran .mp-heading-text {min-width: 0;}
.musyrifah-pembelajaran .mp-title {
    margin: 0 0 6px; color: #24232d; letter-spacing: -.65px;
    font-size: clamp(23px, 2.1vw, 29px); font-weight: 750; line-height: 1.3;
}
.musyrifah-pembelajaran .mp-subtitle {max-width: 850px; margin: 0; color: #7c808d; font-size: 13px; line-height: 1.7;}
.musyrifah-pembelajaran .mp-back {display: none;}

/* RINGKASAN 4 KARTU */
.musyrifah-pembelajaran .mp-summary-grid {
    width: 100%; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px; margin: 0 0 24px;
}
.musyrifah-pembelajaran .mp-summary-card {
    position: relative; display: grid; align-content: center; justify-items: start;
    grid-template-columns: 47px minmax(0,1fr); grid-template-rows: auto auto;
    column-gap: 13px; min-height: 101px; padding: 17px 16px;
    background: #fff; border: 1px solid #eadbdf; border-radius: 14px;
    box-shadow: 0 4px 14px rgba(45, 20, 34, .035); min-width: 0;
}
.musyrifah-pembelajaran .mp-summary-card:nth-child(n) {border-left: 1px solid #eadbdf;}
.musyrifah-pembelajaran .mp-summary-icon {
    grid-row: 1 / span 2; grid-column: 1; width: 45px; height: 45px;
    display: inline-flex; justify-content: center; align-items: center;
    border-radius: 11px; color: #8b1530; background: #f8e9ee; margin: 0;
}
.musyrifah-pembelajaran .mp-summary-card:nth-child(2) .mp-summary-icon {color: #9a6815; background: #fff2dc;}
.musyrifah-pembelajaran .mp-summary-card:nth-child(3) .mp-summary-icon {color: #1b7457; background: #e7f7ed;}
.musyrifah-pembelajaran .mp-summary-card:nth-child(4) .mp-summary-icon {color: #7e599c; background: #f1ebfa;}
.musyrifah-pembelajaran .mp-summary-icon svg {width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round;}
.musyrifah-pembelajaran .mp-summary-label {grid-column: 2; grid-row: 1; margin: 0; color: #7c808e; font-size: 11px; font-weight: 500; line-height: 1.5;}
.musyrifah-pembelajaran .mp-summary-value {grid-column: 2; grid-row: 2; margin: 2px 0 0; color: #20212c; font-size: 23px; font-weight: 750; line-height: 1.25;}

/* TABS -- tidak mendorong layout saat sidebar dibuka */
.musyrifah-pembelajaran .mp-tabs-nav {
    display: flex; align-items: center; flex-wrap: wrap; gap: 8px;
    width: 100%; margin: 0 0 18px; padding: 0; overflow: visible;
    border: 0;
}
.musyrifah-pembelajaran .mp-tab-btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 7px; min-height: 37px; padding: 9px 13px;
    color: #62616c; background: #fff; border: 1px solid #eadfe2;
    border-radius: 999px; font-family: inherit; font-size: 11.5px; font-weight: 600;
    white-space: normal; text-align: center; cursor: pointer;
    transition: background .16s, color .16s, border-color .16s;
}
.musyrifah-pembelajaran .mp-tab-btn:hover {color: var(--mp-maroon); background: #fcf5f6; border-color: #e2c3cd;}
.musyrifah-pembelajaran .mp-tab-btn.active {
    color: white; background: var(--mp-maroon); border-color: var(--mp-maroon);
    box-shadow: 0 3px 8px rgba(128, 11, 34, .10);
}
.musyrifah-pembelajaran .mp-tab-btn:focus-visible,
.musyrifah-pembelajaran button:focus-visible,
.modal-overlay button:focus-visible {outline: 3px solid rgba(201,169,106,.5); outline-offset: 2px;}
.musyrifah-pembelajaran .mp-tab-panel {display: none; width: 100%; min-width: 0;}
.musyrifah-pembelajaran .mp-tab-panel.active {display: block; animation: mpFadeIn .22s ease-out;}
@keyframes mpFadeIn {from {opacity: .5; transform: translateY(4px);} to {opacity: 1; transform: translateY(0);}}

/* KARTU PANEL DAN HEADER TABEL */
.musyrifah-pembelajaran .mp-card {
    width: 100%; min-width: 0; margin: 0 0 22px; padding: 0;
    border: 1px solid #e9dadf; border-radius: 14px;
    background: #fff; box-shadow: 0 4px 16px rgba(45,20,34,.035);
    overflow: hidden; display: block;
}
.musyrifah-pembelajaran .mp-card-header {
    display: flex; align-items: center; justify-content: space-between;
    gap: 14px; flex-wrap: wrap; margin: 0; padding: 20px 24px;
    background: #fff; border-bottom: 1px solid #f0e5e8;
}
.musyrifah-pembelajaran .mp-card-header > div {min-width: 0; flex: 1 1 350px;}
.musyrifah-pembelajaran .mp-card-title {
    margin: 0; display: flex; align-items: center; gap: 8px;
    color: #22232a; font-size: 17px; font-weight: 700; line-height: 1.35;
}
.musyrifah-pembelajaran .mp-card-header p {margin: 5px 0 0 !important; color: #83818c !important; font-size: 12px !important; line-height: 1.65;}
.musyrifah-pembelajaran .btn-action-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    min-height: 40px; padding: 10px 15px; border-radius: 9px;
    border: 1px solid var(--mp-maroon); background: var(--mp-maroon); color: #fff;
    font-family: inherit; font-size: 12px; font-weight: 600; text-decoration: none; white-space: nowrap;
    cursor: pointer; box-shadow: none; transition: background .16s;
}
.musyrifah-pembelajaran .btn-action-primary:hover, .modal-overlay .btn-action-primary:hover {background: #63091b; color: #fff;}
.musyrifah-pembelajaran .mp-table-wrap {
    width: 100%; min-width: 0; max-width: 100%; overflow-x: auto;
    border: 0; border-radius: 0; box-shadow: none;
    scrollbar-width: thin; scrollbar-color: #d4c0c7 #faf7f8;
    -webkit-overflow-scrolling: touch;
}
.musyrifah-pembelajaran .mp-table {width: 100%; min-width: 750px; border: 0; border-spacing: 0; border-collapse: collapse; table-layout: auto; background: #fff;}
.musyrifah-pembelajaran #tab-pemantauan .mp-table {min-width: 1160px;}
.musyrifah-pembelajaran #tab-jadwal .mp-table, .musyrifah-pembelajaran #tab-kelulusan .mp-table {min-width: 940px;}
.musyrifah-pembelajaran .mp-table thead th {
    padding: 14px 13px; background: var(--mp-maroon); color: #fff;
    font-size: 11px; font-weight: 700; text-align: left; line-height: 1.45;
    vertical-align: middle; border: 0; white-space: nowrap;
}
.musyrifah-pembelajaran .mp-table thead th:first-child,
.musyrifah-pembelajaran .mp-table thead th:last-child {border-radius: 0;}
.musyrifah-pembelajaran .mp-table tbody td {
    min-width: 0; padding: 13px 12px; font-size: 12px; line-height: 1.6;
    color: #4a4c58; background: #fff; border: 0; border-bottom: 1px solid #f1e8eb;
    vertical-align: middle; text-align: left; overflow-wrap: anywhere;
}
.musyrifah-pembelajaran .mp-table tbody tr:nth-child(even) td {background: #fcf9fa;}
.musyrifah-pembelajaran .mp-table tbody tr:hover td {background: #faf1f3;}
.musyrifah-pembelajaran .mp-table tbody tr:last-child td {border-bottom: 0;}
.musyrifah-pembelajaran .mp-table td:first-child {width: 45px; color: #7e7e88;}
.musyrifah-pembelajaran .mp-table td:nth-child(3) {min-width: 95px;}
.musyrifah-pembelajaran .mp-table td[style*="text-align: left"] {text-align: left !important;}
.musyrifah-pembelajaran .mp-table td[style*="padding-left: 18px"],
.musyrifah-pembelajaran .mp-table td[style*="padding-left: 16px"],
.musyrifah-pembelajaran .mp-table td[style*="padding-left: 14px"] {padding-left: 13px !important;}
.musyrifah-pembelajaran .mp-table tbody td > div[style*="display: flex"] {justify-content: flex-start !important; flex-wrap: wrap; gap: 6px;}
.musyrifah-pembelajaran .mp-table tbody td form {margin: 0;}
.musyrifah-pembelajaran .badge-pill,
.modal-overlay .badge-pill {
    display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px;
    border-radius: 999px; font-size: 11px; font-weight: 650;
    line-height: 1.4; white-space: normal;
}
.musyrifah-pembelajaran .badge-green, .modal-overlay .badge-green {background: #e2f7ea; color: #137449; border: 1px solid #c6ead4;}
.musyrifah-pembelajaran .badge-yellow, .modal-overlay .badge-yellow {background: #fff3dc; color: #97610f; border: 1px solid #f5e0b8;}
.musyrifah-pembelajaran .badge-red, .modal-overlay .badge-red {background: #fff0f1; color: #a62735; border: 1px solid #f3d3d7;}
.musyrifah-pembelajaran .badge-blue, .modal-overlay .badge-blue {background: #f3edf0; color: #8a1830; border: 1px solid #eadcdf;}
.musyrifah-pembelajaran .btn-table-edit, .musyrifah-pembelajaran .btn-table-del,
.musyrifah-pembelajaran .btn-table-detail {
    min-height: 33px; display: inline-flex; align-items: center; justify-content: center;
    padding: 7px 11px; border-radius: 8px; font-family: inherit; font-size: 11.5px; font-weight: 600;
    cursor: pointer; transition: background .16s, border-color .16s;
}
.musyrifah-pembelajaran .btn-table-edit {background: #fff; border: 1px solid #e6d3da; color: #782138;}
.musyrifah-pembelajaran .btn-table-edit:hover {background: #f9edf0;}
.musyrifah-pembelajaran .btn-table-del {background: #fff7f8; border: 1px solid #f0cbd2; color: #b33349;}
.musyrifah-pembelajaran .btn-table-del:hover {background: #ffebef;}
.musyrifah-pembelajaran .btn-table-detail {background: var(--mp-maroon); color: #fff; border: 1px solid var(--mp-maroon);}
.musyrifah-pembelajaran .btn-table-detail:hover {background: var(--mp-maroon-hover);}

/* FILTER BAR & PROGRESS */
.musyrifah-pembelajaran .filter-bar {
    display: flex; align-items: center; flex-wrap: wrap; gap: 10px;
    margin: 0; padding: 17px 22px; border: 0; border-bottom: 1px solid #eee2e6;
    background: #fff;
}
.musyrifah-pembelajaran .filter-input,
.musyrifah-pembelajaran .filter-select {
    display: block; max-width: 100%; height: 40px; padding: 0 12px;
    border: 1px solid #e6d7dd; border-radius: 9px; outline: none;
    font-family: inherit; font-size: 12px; font-weight: 400; color: #374151; background: #fff;
}
.musyrifah-pembelajaran .filter-input {min-width: 0; flex: 1 1 240px;}
.musyrifah-pembelajaran .filter-select {min-width: 150px; flex: 0 1 195px;}
.musyrifah-pembelajaran .filter-input:focus, .musyrifah-pembelajaran .filter-select:focus {border-color: #971531; box-shadow: 0 0 0 3px rgba(128,11,34,.08);}
.musyrifah-pembelajaran .progress-bar-wrap {width: 100%; height: 7px; margin-top: 7px; border-radius: 999px; background: #eee4e7; overflow: hidden;}
.musyrifah-pembelajaran .progress-bar-fill {height: 100%; border-radius: 999px; background: #8d1732; transition: width .3s;}
.musyrifah-pembelajaran .mp-dokumentasi {min-width: 100px; max-width: 190px; font-size: 11px; text-align: left;}
.musyrifah-pembelajaran .mp-dokumentasi summary {color: #800b22; font-weight: 650; cursor: pointer;}
.musyrifah-pembelajaran .mp-dokumentasi a, .musyrifah-pembelajaran .mp-dokumentasi-link {display: block; margin-top: 6px; color: #800b22; font-size: 11px; text-decoration: underline;}
.musyrifah-pembelajaran .mp-dokumentasi-empty {display: block; margin-top: 6px; color: #78808c; font-size: 11px;}

/* KARTU KPI KELULUSAN: menimpa gaya inline tanpa mengubah data */
.musyrifah-pembelajaran #tab-kelulusan .mp-kpi-grid {
    display: grid !important; grid-template-columns: repeat(3, minmax(0,1fr)) !important;
    gap: 12px !important; padding: 18px 22px 10px !important; margin: 0 !important;
}
.musyrifah-pembelajaran #tab-kelulusan .mp-kpi-grid > div {
    min-width: 0; padding: 15px 17px !important; border-radius: 11px !important;
    border: 1px solid #ece1e5 !important; background: #fff !important;
}
.musyrifah-pembelajaran #tab-kelulusan .mp-kpi-grid > div > div:first-child {font-size: 11px !important; color: #7d7b86 !important;}
.musyrifah-pembelajaran #tab-kelulusan .mp-kpi-grid > div > div:last-child {font-size: 20px !important; color: #272631 !important; line-height: 1.4; overflow-wrap: anywhere;}
.musyrifah-pembelajaran #tab-kelulusan .mp-kpi-grid > div:nth-child(2) {border-top: 3px solid #78b99a !important;}
.musyrifah-pembelajaran #tab-kelulusan .mp-kpi-grid > div:nth-child(3) {border-top: 3px solid #d895a1 !important;}

/* MODAL: di luar wrapper halaman, jadi memakai selector tersendiri */
.modal-overlay {
    position: fixed; inset: 0; z-index: 9999; display: none;
    align-items: center; justify-content: center; padding: 16px;
    background: rgba(21,12,25,.65); backdrop-filter: blur(3px);
    overflow-y: auto;
}
.modal-overlay.active {display: flex;}
.modal-overlay .modal-card {
    background: #fff; border: 1px solid #eddce1; border-radius: 15px;
    width: min(100%, 740px); max-width: 740px; min-width: 0;
    max-height: min(91dvh, 900px); overflow-y: auto; overflow-x: hidden;
    box-shadow: 0 24px 65px rgba(0,0,0,.23); display: flex; flex-direction: column;
    transform: none;
}
.modal-overlay .modal-top {
    position: sticky; top: 0; z-index: 2; display: flex;
    align-items: center; justify-content: space-between; gap: 12px;
    padding: 16px 22px; background: #800b22; border: 0; border-radius: 0;
    color: #fff;
}
.modal-overlay .modal-top-title {font-size: 16px; line-height: 1.4; font-weight: 700; color: #fff;}
.modal-overlay .modal-close-btn {
    flex: 0 0 auto; width: 31px; height: 31px; border-radius: 50%;
    border: 1px solid rgba(255,255,255,.42); background: transparent;
    color: #fff; font-size: 22px; cursor: pointer; line-height: 1;
}
.modal-overlay .modal-close-btn:hover {background: rgba(255,255,255,.13);}
.modal-overlay .modal-inner {padding: 19px 22px; min-width: 0;}
.modal-overlay .form-group {margin: 0 0 13px; min-width: 0;}
.modal-overlay .form-label {display: block; margin: 0 0 6px; font-size: 12px; font-weight: 600; color: #3c3944;}
.modal-overlay .form-control {
    display: block; min-width: 0; width: 100%; min-height: 40px;
    padding: 9px 12px; border-radius: 8px;
    border: 1px solid #e3d4da; background: #fff; color: #302f39;
    font-family: inherit; font-size: 12px; font-weight: 400; outline: none;
}
.modal-overlay textarea.form-control {height: auto; min-height: 70px; resize: vertical;}
.modal-overlay .form-control:focus {border-color: #800b22; box-shadow: 0 0 0 3px rgba(128,11,34,.09);}
.modal-overlay .modal-footer-btns {
    display: flex; justify-content: flex-end; align-items: center; gap: 9px; flex-wrap: wrap;
    margin: 0; padding: 14px 22px; border-top: 1px solid #eee3e6;
    background: #fcf9fa; border-radius: 0;
}
.modal-overlay .btn-modal-cancel {
    min-height: 38px; padding: 8px 15px; background: #fff; color: #6c525d;
    border: 1px solid #e4d4db; border-radius: 8px; font-weight: 600; cursor: pointer;
}
.modal-overlay .btn-action-primary {min-height: 38px; padding: 8px 15px; border: 1px solid #800b22; border-radius: 8px; color: #fff; background: #800b22; font-size: 12px; font-weight: 600; cursor: pointer;}
.modal-overlay .pertemuan-grid {display: grid; grid-template-columns: repeat(auto-fit,minmax(130px,1fr)); gap: 10px; margin-top: 14px; max-height: 450px; overflow-y: auto; padding: 4px;}
.modal-overlay .pertemuan-card-item {padding: 12px; background: #fbf8f9; border: 1px solid #eee0e4; border-radius: 10px; text-align: center;}
.modal-overlay .pertemuan-card-num {font-size: 13px; font-weight: 700; color: #800b22;}
.modal-overlay .pertemuan-card-date {font-size: 11px; color: #77808c; margin-top: 3px;}
.modal-overlay .mp-table-wrap {max-width: 100%; overflow-x: auto; border: 1px solid #eadcdf; border-radius: 9px;}
.modal-overlay .mp-table {width: 100%; min-width: 680px; border-collapse: collapse;}
.modal-overlay .mp-table th {padding: 11px 12px; text-align: left; background: #800b22; color: #fff; font-size: 11px;}
.modal-overlay .mp-table td {padding: 11px 12px; border-bottom: 1px solid #eee4e6; font-size: 12px; color: #4a4b55;}
.modal-overlay .badge-pill {font-size: 11px;}

/* RESPONSIVE BERDASARKAN LEBAR KONTEN (sidebar aman) */
@container (max-width: 1040px) {
.musyrifah-pembelajaran .mp-summary-grid {grid-template-columns: repeat(2, minmax(0,1fr));}
.musyrifah-pembelajaran .mp-tabs-nav {gap: 7px;}
.musyrifah-pembelajaran .mp-tab-btn {font-size: 11px;}
}
@container (max-width: 760px) {
.musyrifah-pembelajaran .mp-heading {margin-bottom: 18px; padding-bottom: 15px;}
.musyrifah-pembelajaran .mp-card-header {padding: 17px 16px;}
.musyrifah-pembelajaran .mp-card-header > div {flex: 1 1 100%;}
.musyrifah-pembelajaran .mp-card-header .btn-action-primary {width: 100%;}
.musyrifah-pembelajaran .mp-table-wrap {overflow: visible !important;}
.musyrifah-pembelajaran .mp-table, .musyrifah-pembelajaran .mp-table tbody {display: block; width: 100%; min-width: 0 !important;}
.musyrifah-pembelajaran .mp-table thead {display: none;}
.musyrifah-pembelajaran .mp-table tbody tr {
        display: grid; width: 100%; grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 11px 15px; margin: 0; padding: 17px 16px;
        border-bottom: 1px solid #f0e5e8; background: #fff;
    }
.musyrifah-pembelajaran .mp-table tbody tr:nth-child(even) {background: #fcf9fa;}
.musyrifah-pembelajaran .mp-table tbody tr td, .musyrifah-pembelajaran .mp-table tbody tr:nth-child(even) td {
        display: block; min-width: 0 !important; width: 100% !important;
        padding: 0 !important; border: 0 !important; background: transparent !important;
        text-align: left !important; overflow-wrap: anywhere;
    }
.musyrifah-pembelajaran .mp-table tbody tr td[data-label]::before {
        content: attr(data-label); display: block; margin: 0 0 5px;
        color: #94909a; font-size: 10px; font-weight: 700;
        line-height: 1.4; text-transform: uppercase; letter-spacing: .025em;
    }
.musyrifah-pembelajaran .mp-table tbody tr td:first-child {grid-column: 1 / -1; color: #978d92; font-size: 11px;}
.musyrifah-pembelajaran .mp-table tbody tr td:nth-child(2) {grid-column: 1 / -1;}
.musyrifah-pembelajaran .mp-table tbody tr td:last-child {grid-column: 1 / -1; padding-top: 8px !important; border-top: 1px solid #f0e8ea !important;}
.musyrifah-pembelajaran .mp-table tbody tr td[colspan] {grid-column: 1 / -1; padding: 14px 4px !important;}
.musyrifah-pembelajaran .mp-table tbody tr td[colspan]::before {display: none;}
.musyrifah-pembelajaran .mp-table tbody td > div[style*="display: flex"] {justify-content: flex-start !important;}
.musyrifah-pembelajaran #tab-pemantauan .mp-table tbody tr td:nth-child(6) {grid-column: 1 / -1;}
.musyrifah-pembelajaran #tab-pemantauan .mp-table tbody tr td:nth-child(8) {grid-column: 1 / -1;}
.musyrifah-pembelajaran #tab-kelulusan .mp-kpi-grid {grid-template-columns: 1fr !important;}
.musyrifah-pembelajaran .filter-bar {padding: 14px 16px;}
.musyrifah-pembelajaran .filter-input, .musyrifah-pembelajaran .filter-select {flex: 1 1 100%; width: 100%; min-width: 0;}
}
@container (max-width: 460px) {
.musyrifah-pembelajaran .mp-title {font-size: 21px;}
.musyrifah-pembelajaran .mp-subtitle {font-size: 12px;}
.musyrifah-pembelajaran .mp-summary-grid {grid-template-columns: repeat(2,minmax(0,1fr)); gap: 9px; margin-bottom: 18px;}
.musyrifah-pembelajaran .mp-summary-card {min-height: 87px; grid-template-columns: 38px minmax(0,1fr); column-gap: 9px; padding: 12px 11px;}
.musyrifah-pembelajaran .mp-summary-icon {width: 37px; height: 37px;}
.musyrifah-pembelajaran .mp-summary-icon svg {width: 20px; height: 20px;}
.musyrifah-pembelajaran .mp-summary-value {font-size: 19px;}
.musyrifah-pembelajaran .mp-summary-label {font-size: 10px;}
.musyrifah-pembelajaran .mp-tabs-nav {display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 7px;}
.musyrifah-pembelajaran .mp-tab-btn {white-space: normal; font-size: 10.5px; padding: 8px 7px; border-radius: 9px;}
.musyrifah-pembelajaran .mp-tab-btn:last-child {grid-column: 1 / -1;}

}
@media (max-width: 600px) {
    .modal-overlay {padding: 8px;}
    .modal-overlay .modal-card {width: 100%; max-height: calc(100dvh - 16px);}
    .modal-overlay .modal-top {padding: 13px 14px;}
    .modal-overlay .modal-top-title {font-size: 14px;}
    .modal-overlay .modal-inner {padding: 15px 14px;}
    .modal-overlay .modal-footer-btns {padding: 13px 14px;}
    .modal-overlay .modal-footer-btns button {flex: 1 1 auto;}
    .modal-overlay .form-control {font-size: 13px;}
    .modal-overlay .modal-inner > div[style*="grid-template-columns"] {grid-template-columns: 1fr !important;}
}
@media (prefers-reduced-motion: reduce) {
    .musyrifah-pembelajaran *, .modal-overlay * {animation-duration: .01ms !important; transition-duration: .01ms !important;}
}
</style>

<div class="musyrifah-pembelajaran">

    <!-- HEADER SERAGAM DENGAN MANAJEMEN PENGGUNA ADMIN -->
    <div class="mp-heading">
        <nav class="mp-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('musyrifah.dashboard') }}">Dashboard</a>
            <span aria-hidden="true">/</span>
            <span>Manajemen Pembelajaran</span>
        </nav>
        <div class="mp-heading-text">
            <h1 class="mp-title">Manajemen Pembelajaran</h1>
            <p class="mp-subtitle">Kelola mata pelajaran, kelompok belajar, jadwal, pemantauan 16 pertemuan, dan kelulusan mahasiswa.</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 500;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fdeef0; color: #8a1224; border: 1px solid #f2ccd2; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 500;">
            ⚠ {{ session('error') }}
        </div>
    @endif

    <!-- RINGKASAN ATAS -->
    <div class="mp-summary-grid" aria-label="Ringkasan pembelajaran">
        <div class="mp-summary-card">
            <span class="mp-summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21z"/><path d="M4 5.5v13A2.5 2.5 0 0 1 6.5 16H20M8 7h8M8 10h8"/></svg>
            </span>
            <p class="mp-summary-value">{{ $jumlahMataPelajaran ?? 5 }}</p>
            <div class="mp-summary-label">Mata Pelajaran</div>
        </div>

        <div class="mp-summary-card">
            <span class="mp-summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20"/><circle cx="10" cy="7" r="3.5"/><path d="M17 4.2a3.5 3.5 0 0 1 0 6.6M17 15h1.5a3.5 3.5 0 0 1 3.5 3.5V20"/></svg>
            </span>
            <p class="mp-summary-value">{{ $jumlahKelompokBelajar ?? 6 }}</p>
            <div class="mp-summary-label">Kelompok Belajar</div>
        </div>

        <div class="mp-summary-card">
            <span class="mp-summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M2.5 9 12 4l9.5 5-9.5 5z"/><path d="M6 11v5c3.5 3 8.5 3 12 0v-5M21.5 9v6"/></svg>
            </span>
            <p class="mp-summary-value">{{ $jumlahPengajar ?? 8 }}</p>
            <div class="mp-summary-label">Guru / Pengajar</div>
        </div>

        <div class="mp-summary-card">
            <span class="mp-summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18M12 13v3l2 1"/></svg>
            </span>
            <p class="mp-summary-value">{{ $targetPertemuan ?? 16 }} TM</p>
            <div class="mp-summary-label">Target Pertemuan</div>
        </div>
    </div>

    <!-- NAVIGASI 5 MENU UTAMA -->
    <nav class="mp-tabs-nav" aria-label="Menu Manajemen Pembelajaran">
        <button type="button" class="mp-tab-btn active" data-tab="tab-mapel" onclick="switchTab('tab-mapel', this)">
            📚 1. Mata Pelajaran ({{ count($daftarMataPelajaran) }})
        </button>
        <button type="button" class="mp-tab-btn" data-tab="tab-kelompok" onclick="switchTab('tab-kelompok', this)">
            👥 2. Kelompok Belajar
        </button>
        <button type="button" class="mp-tab-btn" data-tab="tab-jadwal" onclick="switchTab('tab-jadwal', this)">
            🗓️ 3. Jadwal Pembelajaran
        </button>
        <button type="button" class="mp-tab-btn" data-tab="tab-pemantauan" onclick="switchTab('tab-pemantauan', this)">
            📊 4. Pemantauan &amp; 16 Pertemuan
        </button>
        <button type="button" class="mp-tab-btn" data-tab="tab-kelulusan" onclick="switchTab('tab-kelulusan', this)">
            🎓 5. Kelulusan &amp; Sertifikat
        </button>
    </nav>

    <!-- ========================================== -->
    <!-- TAB 1: MATA PELAJARAN                      -->
    <!-- ========================================== -->
    <div class="mp-tab-panel active" id="tab-mapel">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Daftar Mata Pelajaran</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">{{ $jumlahMataPelajaran }} Program Pembelajaran Kurikulum Ma'had Al-Jami'ah (Dilengkapi fitur CRUD Tambah, Ubah, Hapus).</p>
                </div>
                <button type="button" class="btn-action-primary" onclick="openModalTambahMapel()">
                    + Tambah Mata Pelajaran
                </button>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Mata Pelajaran</th>
                            <th>Kategori Program</th>
                            <th>Target Pertemuan</th>
                            <th>Jadwal Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarMataPelajaran as $idx => $mapel)
                            <tr>
                                <td data-label="No">{{ $idx + 1 }}</td>
                                <td style="font-weight: 600; color: #800d22;" data-label="Kode">{{ $mapel->kode ?? '-' }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 18px;" data-label="Nama Mata Pelajaran">
                                    {{ $mapel->nama }}
                                    <div style="font-size: 12px; font-weight: 400; color: #64748b; margin-top: 2px;">
                                        {{ $mapel->deskripsi ?? '' }}
                                    </div>
                                </td>
                                <td data-label="Kategori Program">
                                    <span class="badge-pill badge-blue">{{ $mapel->kategori ?? 'Akademik' }}</span>
                                </td>
                                <td style="font-weight: 700;" data-label="Target Pertemuan">{{ $mapel->target_pertemuan }} Pertemuan</td>
                                <td data-label="Jadwal Aktif">
                                    <span class="badge-pill badge-green">{{ $mapel->jadwals_count ?? 2 }} Kelas Terjadwal</span>
                                </td>
                                <td data-label="Aksi">
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <button type="button" class="btn-table-edit"
                                                onclick="openModalEditMapel({{ $mapel->id }}, '{{ addslashes($mapel->kode ?? '') }}', '{{ addslashes($mapel->nama) }}', '{{ addslashes($mapel->kategori ?? '') }}', {{ (int) $mapel->target_pertemuan }}, '{{ addslashes($mapel->deskripsi ?? '') }}')">
                                            Ubah
                                        </button>
                                        <form action="{{ route('musyrifah.manajemen_pembelajaran.mapel.destroy', $mapel->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-table-del">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 25px;">Belum ada mata pelajaran terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: KELOMPOK BELAJAR (CRUD)             -->
    <!-- ========================================== -->
    <div class="mp-tab-panel" id="tab-kelompok">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Pengaturan 6 Kelompok Belajar &amp; Pengajar</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Daftar 8 pengajar pengampu kelompok belajar Ma'had Al-Jami'ah (Dilengkapi fitur CRUD Tambah, Ubah, Hapus).</p>
                </div>

                <button type="button" class="btn-action-primary" onclick="openModalTambahKelompok()">
                    + Tambah Pengajar Kelompok
                </button>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengajar</th>
                            <th>Program Pembelajaran</th>
                            <th>Kelompok Belajar</th>
                            <th>Beban Mengajar</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyKelompokPengajar">
                        @forelse($daftarPengajar as $idx => $p)
                            <tr>
                                <td data-label="No">{{ $idx + 1 }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 16px;" data-label="Nama Pengajar">
                                    {{ $p->nama_pengajar }}
                                </td>
                                <td style="text-align: left; padding-left: 16px;" data-label="Program Pembelajaran">
                                    {{ $p->program }}
                                </td>
                                <td data-label="Kelompok Belajar">
                                    <span class="badge-pill badge-blue" style="font-weight: 700;">
                                        Kelompok {{ $p->kelompok_teks }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: #800d22;" data-label="Beban Mengajar">
                                    {{ $p->jumlah_tm }}
                                </td>
                                <td style="font-size: 12px; color: #64748b;" data-label="Keterangan">
                                    {{ $p->keterangan ?? '-' }}
                                </td>
                                <td data-label="Aksi">
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <button type="button" class="btn-table-edit" 
                                                onclick="openModalEditKelompok({{ $p->id }}, '{{ addslashes($p->nama_pengajar) }}', '{{ addslashes($p->program) }}', '{{ addslashes($p->kelompok_teks) }}', '{{ addslashes($p->jumlah_tm) }}', '{{ addslashes($p->keterangan ?? '') }}')">
                                            Ubah
                                        </button>
                                        <form action="{{ route('musyrifah.manajemen_pembelajaran.kelompok.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-table-del">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 25px;">Belum ada data pengajar kelompok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: JADWAL PEMBELAJARAN (CRUD)          -->
    <!-- ========================================== -->
    <div class="mp-tab-panel" id="tab-jadwal">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Jadwal Pembelajaran Ma'had</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Pengaturan mata pelajaran, kelompok belajar, guru pengampu, periode, hari, dan jam mulai/selesai.</p>
                </div>

                <button type="button" class="btn-action-primary" onclick="openModalTambahJadwal()">
                    + Atur Jadwal Baru
                </button>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelompok</th>
                            <th>Guru Pengampu</th>
                            <th>Periode</th>
                            <th>Hari &amp; Waktu</th>
                            <th>Ruangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarJadwal as $idx => $j)
                            <tr>
                                <td data-label="No">{{ $idx + 1 }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 16px;" data-label="Mata Pelajaran">
                                    {{ $j->nama_mata_pelajaran }}
                                </td>
                                <td data-label="Kelompok">
                                    <span class="badge-pill badge-blue">{{ $j->kelompok }}</span>
                                </td>
                                <td style="text-align: left; padding-left: 14px;" data-label="Guru Pengampu">
                                    <div style="font-weight: 600; color: #1e293b;">{{ $j->namaGuruPengampu() }}</div>
                                    @if ($j->guru_id)
                                        <div style="font-size: 11px; color: #15803d; margin-top: 2px;">Akun Guru tertaut</div>
                                    @else
                                        <div style="font-size: 11px; color: #b45309; margin-top: 2px;">Belum tertaut akun Guru</div>
                                    @endif
                                </td>
                                <td style="font-size: 13px;" data-label="Periode">
                                    {{ $j->periode }}
                                </td>
                                <td data-label="Hari &amp; Waktu">
                                    <div style="font-weight: 600; color: #800d22;">{{ $j->hari }}</div>
                                    <div style="font-size: 12px; color: #64748b;">{{ $j->jam_mulai }} - {{ $j->jam_selesai }} WIB</div>
                                </td>
                                <td style="font-size: 13px;" data-label="Ruangan">{{ $j->ruangan }}</td>
                                <td data-label="Aksi">
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <button type="button" class="btn-table-edit" 
                                                onclick="openModalEditJadwal({{ $j->id }}, '{{ addslashes($j->nama_mata_pelajaran) }}', '{{ addslashes($j->kelompok) }}', {{ (int) $j->guru_id }}, '{{ addslashes($j->guru_pengampu) }}', '{{ addslashes($j->periode) }}', '{{ addslashes($j->hari) }}', '{{ addslashes($j->jam_mulai) }}', '{{ addslashes($j->jam_selesai) }}', '{{ addslashes($j->ruangan) }}')">
                                            Ubah
                                        </button>
                                        <form action="{{ route('musyrifah.manajemen_pembelajaran.jadwal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-table-del">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 25px;">Belum ada jadwal yang diatur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 4: PEMANTAUAN & 16 PERTEMUAN           -->
    <!-- ========================================== -->
    <div class="mp-tab-panel" id="tab-pemantauan">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Pemantauan Progres Pembelajaran</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Ringkasan progres pertemuan target 16 kali, dilengkapi pencarian dan filter dinamis periode &amp; kelompok.</p>
                </div>
            </div>

            <!-- FILTER BAR PEMANTAUAN -->
            <div class="filter-bar">
                <input type="search" id="cariPemantauan" class="filter-input" placeholder="🔍 Cari mata pelajaran atau pengajar..." oninput="filterPemantauan()">

                <select id="filterPeriodePemantauan" class="filter-select" onchange="filterPemantauan()">
                    <option value="">Semua Periode</option>
                    @foreach($listPeriode as $periode)
                        <option value="{{ strtolower($periode) }}">{{ $periode }}</option>
                    @endforeach
                </select>

                <select id="filterKelompokPemantauan" class="filter-select" onchange="filterPemantauan()">
                    <option value="">Semua Kelompok</option>
                    @foreach($listKelompokFilter as $k)
                        <option value="{{ strtolower($k) }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelompok</th>
                            <th>Pengajar</th>
                            <th>Periode</th>
                            <th>Progres Pertemuan (16 TM)</th>
                            <th>Kehadiran Rata-rata</th>
                            <th>Materi, Tugas &amp; Nilai</th>
                            <th>Dokumentasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPemantauan">
                        @forelse($daftarPemantauan as $idx => $pm)
                            <tr class="row-pemantauan" 
                                data-nama="{{ strtolower($pm['nama']) }}"
                                data-pengajar="{{ strtolower($pm['pengajar']) }}"
                                data-periode="{{ strtolower($pm['periode']) }}"
                                data-kelompok="{{ strtolower($pm['kelompok']) }}">
                                <td data-label="No">{{ $idx + 1 }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 16px;" data-label="Mata Pelajaran">
                                    {{ $pm['nama'] }}
                                    <div style="font-size: 11px; color: #800d22;">{{ $pm['kode'] }}</div>
                                </td>
                                <td data-label="Kelompok"><span class="badge-pill badge-blue">{{ $pm['kelompok'] }}</span></td>
                                <td style="text-align: left; padding-left: 14px;" data-label="Pengajar">{{ $pm['pengajar'] }}</td>
                                <td style="font-size: 13px;" data-label="Periode">{{ $pm['periode'] }}</td>
                                <td style="min-width: 170px;" data-label="Progres Pertemuan">
                                    <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600;">
                                        <span>{{ $pm['pertemuan_terlaksana'] }} dari 16 Pertemuan</span>
                                        <span>{{ round(($pm['pertemuan_terlaksana'] / 16) * 100) }}%</span>
                                    </div>
                                    <div class="progress-bar-wrap">
                                        <div class="progress-bar-fill" style="width: {{ ($pm['pertemuan_terlaksana'] / 16) * 100 }}%;"></div>
                                    </div>
                                </td>
                                <td data-label="Kehadiran">
                                    <span class="badge-pill badge-green">{{ $pm['persentase'] }}%</span>
                                </td>
                                <td style="font-size: 12px; color: #475569;" data-label="Materi, Tugas &amp; Nilai">
                                    {{ $pm['jumlah_materi'] }} materi &middot; {{ $pm['jumlah_tugas'] }} tugas
                                    <div style="font-size: 11px; color: #800d22; margin-top: 2px;">
                                        Nilai rata-rata: {{ $pm['nilai_rata_rata'] !== null ? $pm['nilai_rata_rata'] : '-' }}
                                    </div>
                                </td>
                                <td data-label="Dokumentasi">
                                    @if ($pm['jumlah_dokumentasi'] > 0)
                                        <details class="mp-dokumentasi">
                                            <summary>{{ $pm['jumlah_dokumentasi'] }} foto</summary>
                                            @foreach ($pm['dokumentasi_pertemuan'] as $dokPertemuan)
                                                <a href="{{ route('pembelajaran.dokumentasi', $dokPertemuan['id']) }}">
                                                    Pertemuan {{ $dokPertemuan['nomor'] }} · {{ $dokPertemuan['jumlah'] }} foto
                                                </a>
                                            @endforeach
                                        </details>
                                    @else
                                        <span class="mp-dokumentasi-empty">Belum ada</span>
                                    @endif
                                </td>
                                <td data-label="Aksi">
                                    <button type="button" class="btn-table-detail" onclick="openModalDetailPemantauan({{ $pm['id'] }}, '{{ addslashes($pm['nama']) }}', '{{ addslashes($pm['kelompok']) }}', '{{ addslashes($pm['periode']) }}')">
                                        Lihat Detail 16 TM
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="padding: 25px;">Belum ada data pemantauan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="emptyPemantauanMsg" style="display: none; text-align: center; padding: 30px; color: #64748b;">
                Tidak ada data pemantauan yang sesuai dengan filter yang dipilih.
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 5: KELULUSAN & SERTIFIKAT              -->
    <!-- ========================================== -->
    <div class="mp-tab-panel" id="tab-kelulusan">
        <div class="mp-card">
            <div class="mp-card-header">
                <div>
                    <h2 class="mp-card-title">Kelulusan &amp; Sertifikat Mahasiswa</h2>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">
                        <strong>Syarat Kelulusan:</strong> Mahasiswa wajib hadir <strong>16 kali pertemuan penuh</strong> dalam <strong>semua mata pelajaran</strong>.
                    </p>
                </div>
            </div>

            <!-- KPI KELULUSAN -->
            <div class="mp-kpi-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px;">
                    <div style="font-size: 13px; color: #64748b;">Total Mahasiswa Terdaftar</div>
                    <div style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ $dataKelulusan['totalMahasiswa'] }} Orang</div>
                </div>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 18px;">
                    <div style="font-size: 13px; color: #166534;">Memenuhi Syarat (Lulus)</div>
                    <div style="font-size: 24px; font-weight: 700; color: #15803d;">{{ $dataKelulusan['totalLulus'] }} Mahasiswa</div>
                </div>
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 18px;">
                    <div style="font-size: 13px; color: #991b1b;">Belum Memenuhi Syarat</div>
                    <div style="font-size: 24px; font-weight: 700; color: #b91c1c;">{{ $dataKelulusan['totalBelumLulus'] }} Mahasiswa</div>
                </div>
            </div>

            <!-- FILTER KELULUSAN -->
            <div class="filter-bar">
                <input type="search" id="cariKelulusan" class="filter-input" placeholder="🔍 Cari nama mahasiswa atau NIM..." oninput="filterKelulusan()">

                <select id="filterStatusKelulusan" class="filter-select" onchange="filterKelulusan()">
                    <option value="">Semua Status</option>
                    <option value="lulus">Memenuhi Syarat</option>
                    <option value="belum">Belum Memenuhi Syarat</option>
                </select>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Kelompok</th>
                            <th>Total Kehadiran</th>
                            <th>Status Kelulusan</th>
                            <th>Keterangan Syarat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyKelulusan">
                        @forelse($dataKelulusan['daftar'] as $idx => $mhs)
                            <tr class="row-kelulusan" 
                                data-nama="{{ strtolower($mhs['nama']) }}"
                                data-nim="{{ strtolower($mhs['nim']) }}"
                                data-status="{{ $mhs['is_lulus'] ? 'lulus' : 'belum' }}">
                                <td data-label="No">{{ $idx + 1 }}</td>
                                <td style="text-align: left; font-weight: 600; padding-left: 18px;" data-label="Nama Mahasiswa">
                                    {{ $mhs['nama'] }}
                                </td>
                                <td data-label="NIM">{{ $mhs['nim'] }}</td>
                                <td data-label="Kelompok"><span class="badge-pill badge-blue">{{ $mhs['kelompok'] }}</span></td>
                                <td style="font-weight: 700;" data-label="Total Kehadiran">
                                    {{ $mhs['total_hadir'] }} / {{ $mhs['target_hadir'] }} Sesi
                                </td>
                                <td data-label="Status Kelulusan">
                                    @if($mhs['is_lulus'])
                                        <span class="badge-pill badge-green">✓ Memenuhi Syarat</span>
                                    @else
                                        <span class="badge-pill badge-red">✕ Belum Memenuhi</span>
                                    @endif
                                </td>
                                <td style="font-size: 13px; color: {{ $mhs['is_lulus'] ? '#15803d' : '#b91c1c' }};" data-label="Keterangan Syarat">
                                    {{ $mhs['catatan_syarat'] }}
                                </td>
                                <td data-label="Aksi">
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <button type="button" class="btn-table-edit" onclick="openModalDetailKelulusan({{ $mhs['id'] }})">
                                            Rincian Syarat
                                        </button>
                                        @if($mhs['is_lulus'])
                                            <button type="button" class="btn-table-detail" style="background: #15803d;" onclick="openModalSertifikat({{ $mhs['id'] }}, '{{ addslashes($mhs['nama']) }}', '{{ addslashes($mhs['nim']) }}')">
                                                📜 Sertifikat
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 25px;">Belum ada data mahasiswa terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="emptyKelulusanMsg" style="display: none; text-align: center; padding: 30px; color: #64748b;">
                Tidak ada data mahasiswa yang sesuai dengan kriteria filter.
            </div>
        </div>
    </div>

</div>

<!-- ======================================================== -->
<!-- MODAL 0: TAMBAH / EDIT MATA PELAJARAN (CRUD)             -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalMapel" onclick="closeModalOnBackdrop(event, 'modalMapel')">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title" id="modalMapelTitle">Tambah Mata Pelajaran</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalMapel')">&times;</button>
        </div>
        <form id="formMapel" method="POST" action="{{ route('musyrifah.manajemen_pembelajaran.mapel.store') }}">
            @csrf
            <div id="methodMapelSpoof"></div>
            <div class="modal-inner">
                <div class="form-group">
                    <label class="form-label">Kode Mata Pelajaran</label>
                    <input type="text" name="kode" id="inputMapelKode" class="form-control" required maxlength="50" placeholder="Contoh: PKK-04">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Mata Pelajaran</label>
                    <input type="text" name="nama" id="inputMapelNama" class="form-control" required placeholder="Contoh: Penguatan Keterampilan Khusus">
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori Program</label>
                    <input type="text" name="kategori" id="inputMapelKategori" class="form-control" required placeholder="Contoh: Keislaman &amp; Turots">
                </div>
                <div class="form-group">
                    <label class="form-label">Target Pertemuan</label>
                    <input type="number" name="target_pertemuan" id="inputMapelTarget" class="form-control" required min="1" max="100" value="16">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi Singkat</label>
                    <textarea name="deskripsi" id="inputMapelDeskripsi" class="form-control" style="height: 70px; padding: 10px;" placeholder="Ringkasan cakupan materi mata pelajaran..."></textarea>
                </div>
            </div>
            <div class="modal-footer-btns">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalMapel')">Batal</button>
                <button type="submit" class="btn-action-primary" id="btnSubmitMapel">Simpan Mata Pelajaran</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 1: TAMBAH / EDIT KELOMPOK PENGAJAR                 -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalKelompok" onclick="closeModalOnBackdrop(event, 'modalKelompok')">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title" id="modalKelompokTitle">Tambah Pengajar Kelompok</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalKelompok')">&times;</button>
        </div>
        <form id="formKelompok" method="POST" action="{{ route('musyrifah.manajemen_pembelajaran.kelompok.store') }}">
            @csrf
            <div id="methodKelompokSpoof"></div>
            <div class="modal-inner">
                <div class="form-group">
                    <label class="form-label">Nama Pengajar / Dosen</label>
                    <input type="text" name="nama_pengajar" id="inputNamaPengajar" class="form-control" required placeholder="Contoh: Dr. Zulkifli, M.A.">
                </div>
                <div class="form-group">
                    <label class="form-label">Program Pembelajaran</label>
                    <select name="program" id="selectProgram" class="form-control" required>
                        @foreach($daftarMataPelajaran as $mp)
                            <option value="{{ $mp->nama }}">{{ $mp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelompok yang Diampu (Pilihan 6 Kelompok)</label>
                    <input type="text" name="kelompok_teks" id="inputKelompokTeks" class="form-control" required placeholder="Contoh: 1 dan 2 atau 4 dan 5">
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Beban TM</label>
                    <input type="text" name="jumlah_tm" id="inputJumlahTm" class="form-control" value="32 TM (2 kelas)" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan / Fokus Pembelajaran</label>
                    <textarea name="keterangan" id="inputKeteranganKelompok" class="form-control" style="height: 70px; padding: 10px;" placeholder="Catatan bidang materi..."></textarea>
                </div>
            </div>
            <div class="modal-footer-btns">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalKelompok')">Batal</button>
                <button type="submit" class="btn-action-primary" id="btnSubmitKelompok">Simpan Pengajar</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 2: TAMBAH / EDIT JADWAL PEMBELAJARAN               -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalJadwal" onclick="closeModalOnBackdrop(event, 'modalJadwal')">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title" id="modalJadwalTitle">Atur Jadwal Pembelajaran</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalJadwal')">&times;</button>
        </div>
        <form id="formJadwal" method="POST" action="{{ route('musyrifah.manajemen_pembelajaran.jadwal.store') }}">
            @csrf
            <div id="methodJadwalSpoof"></div>
            <div class="modal-inner">
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="nama_mata_pelajaran" id="inputJadwalMapel" class="form-control" required>
                        @foreach($daftarMataPelajaran as $mp)
                            <option value="{{ $mp->nama }}">{{ $mp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="form-group">
                        <label class="form-label">Kelompok Belajar</label>
                        <select name="kelompok" id="inputJadwalKelompok" class="form-control" required>
                            @foreach($daftarKelompok as $k)
                                <option value="{{ $k->nama_kelompok }}">{{ $k->nama_kelompok }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Periode</label>
                        <input type="text" name="periode" id="inputJadwalPeriode" class="form-control" value="Semester Ganjil 2026/2027" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Guru Pengampu</label>
                    @if ($daftarGuru->isNotEmpty())
                        <select name="guru_id" id="inputJadwalGuru" class="form-control" required>
                            <option value="">— Pilih akun Guru —</option>
                            @foreach ($daftarGuru as $akunGuru)
                                <option value="{{ $akunGuru->id }}">
                                    {{ $akunGuru->name }}@if (filled($akunGuru->pegawai?->nama_pegawai) && $akunGuru->pegawai->nama_pegawai !== $akunGuru->name) — {{ $akunGuru->pegawai->nama_pegawai }}@endif
                                </option>
                            @endforeach
                        </select>
                        <small style="display: block; margin-top: 5px; font-size: 11px; color: #64748b; line-height: 1.5;">
                            Pilih akun Guru penerima penugasan. Jadwal langsung tampil pada menu Pembelajaran akun tersebut.
                        </small>
                    @else
                        <input type="text" name="guru_pengampu" id="inputJadwalGuruTeks" class="form-control" placeholder="Nama guru pengampu" required>
                        <small style="display: block; margin-top: 5px; font-size: 11px; color: #b45309; line-height: 1.5;">
                            Belum ada akun Guru. Tambahkan akun Guru lebih dulu agar penugasan dapat dibuka Guru.
                        </small>
                    @endif
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                    <div class="form-group">
                        <label class="form-label">Hari</label>
                        <select name="hari" id="inputJadwalHari" class="form-control" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Mulai</label>
                        <input type="text" name="jam_mulai" id="inputJadwalJamMulai" class="form-control" placeholder="08:00" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Selesai</label>
                        <input type="text" name="jam_selesai" id="inputJadwalJamSelesai" class="form-control" placeholder="09:30" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Ruangan</label>
                    <input type="text" name="ruangan" id="inputJadwalRuangan" class="form-control" placeholder="Ruang Kelas 1 / Aula Ma'had" value="Ruang Kelas">
                </div>
            </div>
            <div class="modal-footer-btns">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalJadwal')">Batal</button>
                <button type="submit" class="btn-action-primary" id="btnSubmitJadwal">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 3: DETAIL PEMANTAUAN 16 PERTEMUAN                   -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalDetailPemantauan" onclick="closeModalOnBackdrop(event, 'modalDetailPemantauan')">
    <div class="modal-card" style="max-width: 760px;" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title">Detail 16 Pertemuan Pembelajaran</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailPemantauan')">&times;</button>
        </div>
        <div class="modal-inner">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; margin-bottom: 16px;">
                <div style="font-size: 17px; font-weight: 700; color: #800d22;" id="detailMapelTitle">—</div>
                <div style="font-size: 13px; color: #64748b; margin-top: 3px;" id="detailMapelSub">Target: 16 Pertemuan Tatap Muka</div>
            </div>

            <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Rincian Status Kehadiran per Pertemuan (Pertemuan 1 s.d. 16):
            </div>
            <div class="pertemuan-grid" id="gridPertemuanDetail">
                <div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #64748b;">Memuat data 16 pertemuan...</div>
            </div>
        </div>
        <div class="modal-footer-btns">
            <button type="button" class="btn-action-primary" onclick="closeModal('modalDetailPemantauan')">Tutup</button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 4: DETAIL SYARAT KELULUSAN MAHASISWA                -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalDetailKelulusan" onclick="closeModalOnBackdrop(event, 'modalDetailKelulusan')">
    <div class="modal-card" style="max-width: 740px;" onclick="event.stopPropagation()">
        <div class="modal-top">
            <div class="modal-top-title">Audit Syarat Kelulusan Mahasiswa</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailKelulusan')">&times;</button>
        </div>
        <div class="modal-inner">
            <div style="background: #fdf8f9; border: 1px solid #f1d5dc; border-radius: 12px; padding: 14px 18px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                    <div>
                        <div style="font-size: 18px; font-weight: 700; color: #800d22;" id="auditNamaMhs">—</div>
                        <div style="font-size: 13px; color: #64748b;" id="auditNimMhs">NIM: —</div>
                    </div>
                    <div id="auditBadgeKelulusan">
                        <!-- Badge status -->
                    </div>
                </div>
            </div>

            <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Capaian Kehadiran per Mata Pelajaran (Syarat: Wajib Hadir 16 Pertemuan):
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <th>Target</th>
                            <th>Hadir</th>
                            <th>Izin / Sakit</th>
                            <th>Alpa</th>
                            <th>Status Syarat</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyAuditSyarat">
                        <tr><td colspan="6" style="padding: 20px;">Memuat rincian syarat...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer-btns">
            <button type="button" class="btn-action-primary" onclick="closeModal('modalDetailKelulusan')">Tutup</button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 5: SERTIFIKAT KELULUSAN MA'HAD                      -->
<!-- ======================================================== -->
<div class="modal-overlay" id="modalSertifikat" onclick="closeModalOnBackdrop(event, 'modalSertifikat')">
    <div class="modal-card" style="max-width: 680px; text-align: center; border: 8px double #800d22; background: #fffcf8;" onclick="event.stopPropagation()">
        <div class="modal-top" style="background: transparent; border-bottom: none; justify-content: flex-end;">
            <button type="button" class="modal-close-btn" onclick="closeModal('modalSertifikat')">&times;</button>
        </div>
        <div style="padding: 10px 40px 30px;">
            <div style="color: #800d22; font-size: 26px; font-weight: 800; letter-spacing: 1px;">SERTIFIKAT KELULUSAN</div>
            <div style="font-size: 14px; font-weight: 600; color: #555; margin-top: 4px;">MA'HAD AL-JAMI'AH UIN IMAM BONJOL PADANG</div>
            <div style="font-size: 12px; color: #888;">No. Dokumen: SERT/SIMAHAD/2026/0892</div>

            <hr style="margin: 20px 0; border: none; border-top: 2px solid #800d22; width: 60%; margin-left: auto; margin-right: auto;">

            <p style="font-size: 14px; color: #444; margin-bottom: 6px;">Diberikan dengan hormat kepada:</p>
            <div style="font-size: 24px; font-weight: 700; color: #111; text-decoration: underline;" id="sertifikatNamaMhs">—</div>
            <div style="font-size: 15px; font-weight: 500; color: #666; margin-top: 4px;" id="sertifikatNimMhs">NIM: —</div>

            <p style="font-size: 14px; color: #444; margin-top: 20px; line-height: 1.6;">
                Telah <strong>MEMENUHI SYARAT KELULUSAN</strong> pada seluruh rangkaian program pembelajaran Ma'had Al-Jami'ah dengan tingkat kehadiran 100% (16 Pertemuan Penuh) di setiap mata pelajaran yang dipersyaratkan.
            </p>

            <div style="display: flex; justify-content: space-around; margin-top: 36px; padding-top: 10px;">
                <div>
                    <div style="font-size: 12px; color: #888;">Mudir Ma'had Al-Jami'ah</div>
                    <div style="height: 48px;"></div>
                    <div style="font-weight: 700; font-size: 14px; border-top: 1px solid #333; padding-top: 4px;">Dr. Zulkifli, M.A.</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #888;">Koordinator Musyrifah</div>
                    <div style="height: 48px;"></div>
                    <div style="font-weight: 700; font-size: 14px; border-top: 1px solid #333; padding-top: 4px;">Ustadzah Musyrifah</div>
                </div>
            </div>
        </div>
        <div class="modal-footer-btns" style="background: transparent;">
            <button type="button" class="btn-modal-cancel" onclick="closeModal('modalSertifikat')">Tutup</button>
            <button type="button" class="btn-action-primary" onclick="window.print()">🖨️ Cetak Sertifikat</button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- JAVASCRIPT LOGIC TAB, CRUD, FILTER, DETAIL               -->
<!-- ======================================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Cek parameter URL ?tab=...
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const mapTab = {
                'mapel': 'tab-mapel',
                'kelompok': 'tab-kelompok',
                'jadwal': 'tab-jadwal',
                'pemantauan': 'tab-pemantauan',
                'kelulusan': 'tab-kelulusan'
            };
            const targetId = mapTab[tab];
            if (targetId) {
                const btn = document.querySelector(`[data-tab="${targetId}"]`);
                if (btn) switchTab(targetId, btn);
            }
        }
    });

    // 1. SWITCH TAB
    function switchTab(tabId, btn) {
        document.querySelectorAll('.mp-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.mp-tab-panel').forEach(p => p.classList.remove('active'));

        btn.classList.add('active');
        const target = document.getElementById(tabId);
        if (target) target.classList.add('active');
    }

    // 2. MODAL HELPER
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('active');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('active');
    }

    function closeModalOnBackdrop(event, modalId) {
        if (event.target.id === modalId) {
            closeModal(modalId);
        }
    }

    // 2. MODAL MATA PELAJARAN (CRUD)
    function openModalTambahMapel() {
        document.getElementById('modalMapelTitle').textContent = 'Tambah Mata Pelajaran';
        document.getElementById('formMapel').action = "{{ route('musyrifah.manajemen_pembelajaran.mapel.store') }}";
        document.getElementById('methodMapelSpoof').innerHTML = '';
        document.getElementById('inputMapelKode').value = '';
        document.getElementById('inputMapelNama').value = '';
        document.getElementById('inputMapelKategori').value = '';
        document.getElementById('inputMapelTarget').value = '16';
        document.getElementById('inputMapelDeskripsi').value = '';
        document.getElementById('btnSubmitMapel').textContent = 'Simpan Mata Pelajaran';
        openModal('modalMapel');
    }

    function openModalEditMapel(id, kode, nama, kategori, target, deskripsi) {
        document.getElementById('modalMapelTitle').textContent = 'Ubah Mata Pelajaran';
        document.getElementById('formMapel').action = "{{ url('/musyrifah/manajemen-pembelajaran/mata-pelajaran') }}/" + id;
        document.getElementById('methodMapelSpoof').innerHTML = '@method("PUT")';
        document.getElementById('inputMapelKode').value = kode;
        document.getElementById('inputMapelNama').value = nama;
        document.getElementById('inputMapelKategori').value = kategori;
        document.getElementById('inputMapelTarget').value = target;
        document.getElementById('inputMapelDeskripsi').value = deskripsi;
        document.getElementById('btnSubmitMapel').textContent = 'Perbarui Mata Pelajaran';
        openModal('modalMapel');
    }

    // 3. MODAL KELOMPOK PENGAJAR (CRUD)
    function openModalTambahKelompok() {
        document.getElementById('modalKelompokTitle').textContent = 'Tambah Pengajar Kelompok Belajar';
        document.getElementById('formKelompok').action = "{{ route('musyrifah.manajemen_pembelajaran.kelompok.store') }}";
        document.getElementById('methodKelompokSpoof').innerHTML = '';
        document.getElementById('inputNamaPengajar').value = '';
        document.getElementById('inputKelompokTeks').value = '';
        document.getElementById('inputJumlahTm').value = '32 TM (2 kelas)';
        document.getElementById('inputKeteranganKelompok').value = '';
        document.getElementById('btnSubmitKelompok').textContent = 'Simpan Pengajar';
        openModal('modalKelompok');
    }

    function openModalEditKelompok(id, nama, program, kelompok, tm, ket) {
        document.getElementById('modalKelompokTitle').textContent = 'Ubah Pengajar Kelompok Belajar';
        document.getElementById('formKelompok').action = "{{ url('/musyrifah/manajemen-pembelajaran/kelompok') }}/" + id;
        document.getElementById('methodKelompokSpoof').innerHTML = '@method("PUT")';
        document.getElementById('inputNamaPengajar').value = nama;
        document.getElementById('selectProgram').value = program;
        document.getElementById('inputKelompokTeks').value = kelompok;
        document.getElementById('inputJumlahTm').value = tm;
        document.getElementById('inputKeteranganKelompok').value = ket;
        document.getElementById('btnSubmitKelompok').textContent = 'Perbarui Pengajar';
        openModal('modalKelompok');
    }

    // 4. MODAL JADWAL (CRUD)
    function openModalTambahJadwal() {
        document.getElementById('modalJadwalTitle').textContent = 'Atur Jadwal Pembelajaran Baru';
        document.getElementById('formJadwal').action = "{{ route('musyrifah.manajemen_pembelajaran.jadwal.store') }}";
        document.getElementById('methodJadwalSpoof').innerHTML = '';
        const pilihGuru = document.getElementById('inputJadwalGuru');
        if (pilihGuru) { pilihGuru.value = ''; }
        document.getElementById('inputJadwalJamMulai').value = '08:00';
        document.getElementById('inputJadwalJamSelesai').value = '09:30';
        document.getElementById('inputJadwalRuangan').value = 'Ruang Kelas';
        document.getElementById('btnSubmitJadwal').textContent = 'Simpan Jadwal';
        openModal('modalJadwal');
    }

    function openModalEditJadwal(id, mapel, kelompok, guruId, guruNama, periode, hari, jamMulai, jamSelesai, ruangan) {
        document.getElementById('modalJadwalTitle').textContent = 'Ubah Jadwal Pembelajaran';
        document.getElementById('formJadwal').action = "{{ url('/musyrifah/manajemen-pembelajaran/jadwal') }}/" + id;
        document.getElementById('methodJadwalSpoof').innerHTML = '@method("PUT")';
        document.getElementById('inputJadwalMapel').value = mapel;
        document.getElementById('inputJadwalKelompok').value = kelompok;

        const pilihGuru = document.getElementById('inputJadwalGuru');
        if (pilihGuru) {
            pilihGuru.value = guruId ? String(guruId) : '';
        }

        const teksGuru = document.getElementById('inputJadwalGuruTeks');
        if (teksGuru) {
            teksGuru.value = guruNama || '';
        }

        document.getElementById('inputJadwalPeriode').value = periode;
        document.getElementById('inputJadwalHari').value = hari;
        document.getElementById('inputJadwalJamMulai').value = jamMulai;
        document.getElementById('inputJadwalJamSelesai').value = jamSelesai;
        document.getElementById('inputJadwalRuangan').value = ruangan;
        document.getElementById('btnSubmitJadwal').textContent = 'Perbarui Jadwal';
        openModal('modalJadwal');
    }

    // 5. FILTER PEMANTAUAN (PERIODE & KELOMPOK & PENCARIAN)
    function filterPemantauan() {
        const query = document.getElementById('cariPemantauan').value.toLowerCase().trim();
        const periodeFilter = document.getElementById('filterPeriodePemantauan').value.toLowerCase();
        const kelompokFilter = document.getElementById('filterKelompokPemantauan').value.toLowerCase();

        const rows = document.querySelectorAll('.row-pemantauan');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const nama = row.dataset.nama || '';
            const pengajar = row.dataset.pengajar || '';
            const periode = row.dataset.periode || '';
            const kelompok = row.dataset.kelompok || '';

            const matchQuery = !query || nama.includes(query) || pengajar.includes(query);
            const matchPeriode = !periodeFilter || periode.includes(periodeFilter);
            const matchKelompok = !kelompokFilter || kelompok.includes(kelompokFilter);

            if (matchQuery && matchPeriode && matchKelompok) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyMsg = document.getElementById('emptyPemantauanMsg');
        if (emptyMsg) {
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // 6. DETAIL PEMANTAUAN 16 PERTEMUAN (AJAX)
    async function openModalDetailPemantauan(mapelId, mapelNama, kelompok, periode) {
        document.getElementById('detailMapelTitle').textContent = mapelNama;
        document.getElementById('detailMapelSub').textContent =
            [kelompok, periode].filter(Boolean).join(' · ') + ' · Target: 16 Pertemuan Tatap Muka';
        document.getElementById('gridPertemuanDetail').innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #64748b;">Memuat data 16 pertemuan...</div>';
        openModal('modalDetailPemantauan');

        try {
            const params = new URLSearchParams();
            if (kelompok) { params.set('kelompok', kelompok); }
            if (periode) { params.set('periode', periode); }

            const res = await fetch("{{ url('/musyrifah/manajemen-pembelajaran/detail-pemantauan') }}/" + mapelId
                + (params.toString() ? '?' + params.toString() : ''), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (data.success && data.pertemuan) {
                let html = '';
                data.pertemuan.forEach(function (p) {
                    const badgeClass = p.status_pertemuan === 'Terlaksana'
                        ? 'badge-green'
                        : (p.status_pertemuan === 'Berlangsung' ? 'badge-blue' : 'badge-yellow');
                    const materiTeks = p.materi ? 'Materi tersedia' : 'Materi belum';
                    const nilaiTeks = p.nilai_rata_rata !== null ? p.nilai_rata_rata : '-';
                    html += `
                        <div class="pertemuan-card-item">
                            <div class="pertemuan-card-num">Pertemuan ${p.pertemuan_ke}</div>
                            <div class="pertemuan-card-date">${p.tanggal}</div>
                            <span class="badge-pill ${badgeClass}" style="margin-top: 6px; font-size: 11px;">
                                ${p.status_pertemuan}
                            </span>
                            <div style="font-size: 11px; margin-top: 6px; color: #475569;">
                                <strong>${p.hadir}</strong> Hadir · <strong>${p.izin + p.sakit}</strong> Izin
                                · <strong>${p.alpa}</strong> Alpa
                            </div>
                            <div style="font-size: 11px; margin-top: 3px; color: #64748b;">
                                ${materiTeks} · ${p.jumlah_tugas} tugas · Nilai ${nilaiTeks}
                            </div>
                        </div>
                    `;
                });
                const grid = document.getElementById('gridPertemuanDetail');
                grid.innerHTML = html;
                data.pertemuan.forEach(function (p, index) {
                    const dokumentasi = document.createElement(p.dokumentasi_url ? 'a' : 'span');
                    dokumentasi.className = p.dokumentasi_url ? 'mp-dokumentasi-link' : 'mp-dokumentasi-empty';
                    dokumentasi.textContent = p.dokumentasi_url
                        ? 'Lihat dokumentasi · ' + p.jumlah_dokumentasi + ' foto'
                        : 'Dokumentasi belum tersedia';
                    if (p.dokumentasi_url) {
                        dokumentasi.href = p.dokumentasi_url;
                    }
                    grid.children[index].appendChild(dokumentasi);
                });
            }
        } catch (e) {
            console.error('Error fetching detail pemantauan:', e);
            document.getElementById('gridPertemuanDetail').innerHTML = '<div style="grid-column: 1/-1; color: red; text-align: center;">Gagal memuat detail pertemuan.</div>';
        }
    }

    // 7. FILTER KELULUSAN
    function filterKelulusan() {
        const query = document.getElementById('cariKelulusan').value.toLowerCase().trim();
        const statusFilter = document.getElementById('filterStatusKelulusan').value;

        const rows = document.querySelectorAll('.row-kelulusan');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const nama = row.dataset.nama || '';
            const nim = row.dataset.nim || '';
            const status = row.dataset.status || '';

            const matchQuery = !query || nama.includes(query) || nim.includes(query);
            const matchStatus = !statusFilter || status === statusFilter;

            if (matchQuery && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyMsg = document.getElementById('emptyKelulusanMsg');
        if (emptyMsg) {
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // 8. AUDIT SYARAT KELULUSAN MAHASISWA (AJAX)
    async function openModalDetailKelulusan(userId) {
        document.getElementById('auditNamaMhs').textContent = 'Memuat...';
        document.getElementById('auditNimMhs').textContent = 'NIM: -';
        document.getElementById('tbodyAuditSyarat').innerHTML = '<tr><td colspan="6" style="padding: 20px; text-align: center;">Memuat rincian syarat kelulusan...</td></tr>';
        openModal('modalDetailKelulusan');

        try {
            const res = await fetch("{{ url('/musyrifah/manajemen-pembelajaran/detail-kelulusan') }}/" + userId, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (data.success) {
                document.getElementById('auditNamaMhs').textContent = data.mahasiswa.name;
                document.getElementById('auditNimMhs').textContent = 'NIM: ' + data.mahasiswa.nim + ' · ' + data.mahasiswa.kamar;

                const badgeBox = document.getElementById('auditBadgeKelulusan');
                if (data.is_lulus) {
                    badgeBox.innerHTML = '<span class="badge-pill badge-green" style="font-size: 13px; font-weight: 700; padding: 6px 14px;">✓ Memenuhi Syarat Kelulusan (Lengkap 100%)</span>';
                } else {
                    badgeBox.innerHTML = '<span class="badge-pill badge-red" style="font-size: 13px; font-weight: 700; padding: 6px 14px;">✕ Belum Memenuhi Syarat (Wajib 16 TM di Semua Mapel)</span>';
                }

                let html = '';
                data.detail_syarat.forEach(function (ds) {
                    const statusClass = ds.is_lulus ? 'badge-green' : 'badge-red';
                    html += `
                        <tr>
                            <td style="text-align: left; font-weight: 600; padding-left: 14px;">${ds.mata_pelajaran}</td>
                            <td>${ds.target} Pertemuan</td>
                            <td style="font-weight: 700; color: ${ds.is_lulus ? '#15803d' : '#b91c1c'};">${ds.hadir} Hadir</td>
                            <td>${ds.izin + ds.sakit} Sesi</td>
                            <td>${ds.alpa} Sesi</td>
                            <td><span class="badge-pill ${statusClass}">${ds.status_mapel}</span></td>
                        </tr>
                    `;
                });
                document.getElementById('tbodyAuditSyarat').innerHTML = html;
            }
        } catch (e) {
            console.error('Error fetching detail kelulusan:', e);
            document.getElementById('tbodyAuditSyarat').innerHTML = '<tr><td colspan="6" style="color:red; text-align:center;">Gagal memuat rincian syarat kelulusan.</td></tr>';
        }
    }

    // 9. SERTIFIKAT MODAL
    function openModalSertifikat(userId, nama, nim) {
        document.getElementById('sertifikatNamaMhs').textContent = nama;
        document.getElementById('sertifikatNimMhs').textContent = 'NIM: ' + nim;
        openModal('modalSertifikat');
    }
</script>

@endsection
