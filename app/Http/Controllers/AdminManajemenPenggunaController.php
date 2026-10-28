<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminManajemenPenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Pencarian berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter status akun jika kolom status tersedia di database
        if (
            $request->filled('status') &&
            in_array($request->status, ['aktif', 'nonaktif', 'menunggu'])
        ) {
            $status = $request->status;

            if ($status === 'aktif') {
                $query->where('status', 'aktif');
            } elseif ($status === 'nonaktif') {
                $query->where('status', 'nonaktif');
            } else {
                $query->where('status', 'menunggu aktivasi');
            }
        }

        $pengguna = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.manajemen_pengguna', compact('pengguna'));
    }
}