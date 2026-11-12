<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LaporanAudit;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Audit trail SIMAHAD untuk Pimpinan dan Admin Sistem.
 *
 * Hanya menampilkan riwayat. Tidak tersedia aksi ubah maupun hapus karena
 * pencatatan audit bersifat tambah-saja.
 */
class PimpinanAuditController extends Controller
{
    /** Modul audit yang dikenal. */
    public const MODUL = [
        'mahasiswa' => 'Data Mahasiswa',
        'kamar' => 'Kamar & Penempatan',
        'pembayaran' => 'Pembayaran',
        'perizinan' => 'Perizinan',
        'pembelajaran' => 'Pembelajaran',
        'pendaftaran' => 'Pendaftaran',
        'laporan' => 'Laporan',
        'pengguna' => 'Akun Pengguna',
        'sistem' => 'Pengaturan Sistem',
    ];

    public function index(Request $request): View
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['pimpinan', 'admin'], true), 403);

        $tab = $request->query('tab') === 'laporan' ? 'laporan' : 'aktivitas';
        $filter = [
            'modul' => trim((string) $request->query('modul', '')),
            'role' => trim((string) $request->query('role', '')),
            'periode' => trim((string) $request->query('periode', '')),
            'cari' => trim((string) $request->query('cari', '')),
        ];

        $aktivitas = ActivityLog::query()
            ->when($filter['modul'] !== '', fn ($query) => $query->where('modul', $filter['modul']))
            ->when($filter['role'] !== '', fn ($query) => $query->where('user_role', $filter['role']))
            ->when($filter['periode'] === 'hari_ini', fn ($query) => $query->whereDate('created_at', today()))
            ->when($filter['periode'] === '7_hari', fn ($query) => $query->where('created_at', '>=', now()->subDays(7)))
            ->when($filter['periode'] === '30_hari', fn ($query) => $query->where('created_at', '>=', now()->subDays(30)))
            ->when($filter['cari'] !== '', function ($query) use ($filter): void {
                $kataKunci = '%'.str_replace(['%', '_'], ['\%', '\_'], $filter['cari']).'%';

                $query->where(function ($sub) use ($kataKunci): void {
                    $sub->where('objek', 'like', $kataKunci)
                        ->orWhere('deskripsi', 'like', $kataKunci)
                        ->orWhere('description', 'like', $kataKunci)
                        ->orWhere('user_name', 'like', $kataKunci);
                });
            })
            ->latest('id')
            ->paginate(15, ['*'], 'aktivitas_page')
            ->withQueryString();

        $laporan = LaporanAudit::query()
            ->with('laporan:id,judul,jenis,periode,lantai,unit,status,versi')
            ->when($filter['modul'] !== '', fn ($query) => $query->where('jenis_laporan', $filter['modul']))
            ->when($filter['role'] !== '', fn ($query) => $query->where('pengguna_role', $filter['role']))
            ->when($filter['periode'] === 'hari_ini', fn ($query) => $query->whereDate('created_at', today()))
            ->when($filter['periode'] === '7_hari', fn ($query) => $query->where('created_at', '>=', now()->subDays(7)))
            ->when($filter['periode'] === '30_hari', fn ($query) => $query->where('created_at', '>=', now()->subDays(30)))
            ->when($filter['cari'] !== '', function ($query) use ($filter): void {
                $kataKunci = '%'.str_replace(['%', '_'], ['\%', '\_'], $filter['cari']).'%';

                $query->where(function ($sub) use ($kataKunci): void {
                    $sub->where('keterangan', 'like', $kataKunci)
                        ->orWhere('pengguna_nama', 'like', $kataKunci);
                });
            })
            ->latest('id')
            ->paginate(15, ['*'], 'laporan_page')
            ->withQueryString();

        return view('pimpinan.audit', [
            'tab' => $tab,
            'filter' => $filter,
            'aktivitas' => $aktivitas,
            'laporan' => $laporan,
            'daftarModul' => self::MODUL,
            'ringkasan' => [
                'total' => ActivityLog::query()->count(),
                'hari_ini' => ActivityLog::query()->whereDate('created_at', today())->count(),
                'audit_laporan' => LaporanAudit::query()->count(),
            ],
        ]);
    }
}
