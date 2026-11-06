<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminManajemenPenggunaController extends Controller
{
    // Menampilkan daftar pengguna
    public function index(Request $request)
    {
        $query = User::query();

        // Pencarian nama atau email
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter status pengguna
        if (
            $request->filled('status') &&
            in_array($request->status, ['aktif', 'nonaktif', 'menunggu'])
        ) {
            if ($request->status === 'menunggu') {
                $query->where('status', 'menunggu aktivasi');
            } else {
                $query->where('status', $request->status);
            }
        }

        $pengguna = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.manajemen_pengguna', compact('pengguna'));
    }

    // Menampilkan halaman tambah pengguna
    public function create()
    {
        return view('Admin.tambah_pengguna');
    }

    // Menyimpan pengguna baru dan mencatat aktivitas
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'nullable|string|max:20',
            'role' => 'required|in:admin,guru,mahasiswa,musyrifah,pimpinan',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Simpan pengguna baru
        $penggunaBaru = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'role' => $request->role,
            'password' => $request->password,
            'status' => $request->status,
        ]);

        // Catat aktivitas admin
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'user_email' => auth()->user()->email,
            'user_role' => auth()->user()->role,
            'activity' => 'create_user',
            'description' => 'Menambahkan pengguna baru: ' . $penggunaBaru->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Kembali ke halaman manajemen pengguna
        return redirect()
            ->route('admin.manajemen-pengguna')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function verifikasi($id)
{
    // Cari user berdasarkan ID
    $user = User::findOrFail($id);

    // Ubah status akun menjadi disetujui
    $user->status = 'approved';
    $user->save();

    // Kembali ke halaman manajemen pengguna
    return redirect()->back()->with(
        'success',
        'Akun ' . $user->name . ' berhasil diverifikasi.'
    );
}
}
