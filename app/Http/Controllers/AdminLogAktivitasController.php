<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminLogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        // Ringkasan aktivitas
        $totalAktivitas = ActivityLog::count();

        $penggunaAktif = ActivityLog::whereDate(
            'created_at',
            today()
        )->distinct('user_id')->count('user_id');

        $aktivitasHariIni = ActivityLog::whereDate(
            'created_at',
            today()
        )->count();

        // Query riwayat aktivitas
        $query = ActivityLog::query()
            ->latest();

        // Filter periode
        if ($request->periode === 'hari_ini') {
            $query->whereDate('created_at', today());
        } elseif ($request->periode === '7_hari') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($request->periode === '30_hari') {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        // Filter peran pengguna
        if ($request->filled('role')) {
            $query->where('user_role', $request->role);
        }

        // Filter jenis aktivitas
        if ($request->filled('aktivitas')) {
            $query->where('activity', $request->aktivitas);
        }

        $logs = $query->paginate(10)->withQueryString();

        return view('admin.log_aktivitas', compact(
            'totalAktivitas',
            'penggunaAktif',
            'aktivitasHariIni',
            'logs'
        ));
    }
}