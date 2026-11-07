<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pegawai;
use App\Models\PegawaiAkun;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminManajemenPenggunaController extends Controller
{
    private const ROLES = ['admin', 'guru', 'musyrifah', 'pimpinan', 'mahasiswa'];

    // Role adalah HAK AKSES, bukan bukti seseorang tercatat sebagai pegawai.
    // Akun Admin dan Mahasiswa yang dibuat manual TIDAK otomatis membuat Data Pegawai.
    // Jika Admin benar-benar pegawai Ma'had, tautkan secara eksplisit melalui pilihan Data Pegawai.
    private const ROLES_AUTO_PEGAWAI = ['guru', 'musyrifah', 'pimpinan'];

    private const STATUS_AKUN = ['aktif', 'nonaktif'];

    private const STATUS_MENUNGGU = ['menunggu aktivasi', 'menunggu', 'pending'];

    private function pastikanAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }

    private function catat(Request $request, string $aksi, string $deskripsi): void
    {
        $admin = $request->user();

        ActivityLog::create([
            'user_id' => $admin->getKey(),
            'user_name' => $admin->name,
            'user_email' => $admin->email,
            'user_role' => $admin->role,
            'activity' => $aksi,
            'description' => $deskripsi,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public function index(Request $request): View
    {
        $this->pastikanAdmin($request);

        $inputCari = $request->query('search', '');
        $search = is_string($inputCari) ? mb_substr(trim($inputCari), 0, 150) : '';
        $inputStatus = $request->query('status', '');
        $status = is_string($inputStatus) && in_array($inputStatus, ['aktif', 'nonaktif', 'menunggu'], true)
            ? $inputStatus : '';

        $pengguna = User::query()
            ->with('pegawaiAkun.pegawai')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                if ($status === 'menunggu') {
                    $query->whereIn('status', self::STATUS_MENUNGGU);
                } else {
                    $query->where('status', $status);
                }
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('Admin.manajemen_pengguna', compact('pengguna', 'search', 'status'));
    }

    public function create(Request $request): View
    {
        $this->pastikanAdmin($request);

        // Tidak menebak hubungan berdasarkan kecocokan nama/email.
        $pegawaiTersedia = Pegawai::query()
            ->whereDoesntHave('akunLink')
            ->orderBy('nama_pegawai')
            ->get(['id', 'nama_pegawai', 'email', 'no_hp']);

        return view('Admin.tambah_pengguna', compact('pegawaiTersedia'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);
        $this->normalisasiInput($request);

        $data = $request->validate([
            'pegawai_id' => ['nullable', 'integer', Rule::exists('pegawais', 'id')],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(self::STATUS_AKUN)],
        ]);

        $pegawaiId = isset($data['pegawai_id']) ? (int) $data['pegawai_id'] : null;

        if ($pegawaiId && $data['role'] === 'mahasiswa') {
            throw ValidationException::withMessages([
                'pegawai_id' => 'Data Pegawai tidak dapat digunakan untuk akun Mahasiswa.',
            ]);
        }

        if (! $pegawaiId) {
            $request->validate([
                'name' => ['required', 'string', 'min:2', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            ]);
        }

        DB::transaction(function () use ($request, $data, $pegawaiId): void {
            $pegawai = null;

            if ($pegawaiId) {
                $pegawai = Pegawai::query()->lockForUpdate()->findOrFail($pegawaiId);
                if (PegawaiAkun::query()->where('pegawai_id', $pegawai->getKey())->exists()) {
                    throw ValidationException::withMessages([
                        'pegawai_id' => 'Pegawai ini sudah memiliki akun. Perbarui halaman dan pilih pegawai lain.',
                    ]);
                }
                $nama = trim((string) $pegawai->nama_pegawai);
                $email = mb_strtolower(trim((string) $pegawai->email));
                $noHp = $pegawai->no_hp;

                if ($nama === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw ValidationException::withMessages([
                        'pegawai_id' => 'Nama atau email pegawai belum valid. Perbaiki di Data Pegawai dahulu.',
                    ]);
                }
                if (User::query()->where('email', $email)->exists()) {
                    throw ValidationException::withMessages([
                        'pegawai_id' => 'Email pegawai sudah dipakai akun lain. Periksa Manajemen Pengguna.',
                    ]);
                }
            } else {
                $nama = trim($data['name']);
                $email = $data['email'];
                $noHp = $data['no_hp'] ?? null;

                if (Pegawai::query()->where('email', $email)->exists()) {
                    throw ValidationException::withMessages([
                        'pegawai_id' => 'Email sudah tercatat di Data Pegawai. Pilih pegawainya agar tidak menjadi ganda.',
                    ]);
                }
            }

            // Model User sudah memiliki cast password => hashed.
            $akun = User::create([
                'name' => $nama,
                'email' => $email,
                'no_hp' => $noHp,
                'role' => $data['role'],
                'password' => $data['password'],
                'status' => $data['status'],
            ]);

            if (! $pegawai && in_array($akun->role, self::ROLES_AUTO_PEGAWAI, true)) {
                // Hanya role operasional Ma'had yang otomatis memperoleh biodata pegawai.
                // Admin bukan indikator kepegawaian; mahasiswa memakai modul tersendiri.
                $pegawai = Pegawai::create([
                    'nama_pegawai' => $akun->name,
                    'email' => $akun->email,
                    'no_hp' => $akun->no_hp,
                    'status_pegawai' => true,
                ]);
            }

            if ($pegawai) {
                PegawaiAkun::create([
                    'pegawai_id' => $pegawai->getKey(),
                    'user_id' => $akun->getKey(),
                ]);
            }

            $this->catat($request, 'create_user', 'Menambahkan pengguna baru: '.$akun->name);
        }, 3);

        return redirect()->route('admin.manajemen-pengguna')
            ->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->pastikanAdmin($request);
        $user->load('pegawaiAkun.pegawai');

        return view('Admin.edit_pengguna', ['akun' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->pastikanAdmin($request);
        $this->normalisasiInput($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey())],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(self::ROLES)],
            'status' => ['required', Rule::in(array_merge(self::STATUS_AKUN, self::STATUS_MENUNGGU))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $keluarDariAkunSendiri = DB::transaction(function () use ($request, $user, $data): bool {
            $akun = User::query()->lockForUpdate()->findOrFail($user->getKey());
            $link = PegawaiAkun::query()->where('user_id', $akun->getKey())->first();
            $pegawai = $link ? Pegawai::query()->lockForUpdate()->findOrFail($link->pegawai_id) : null;

            // Admin boleh mengedit akunnya sendiri. Jika perubahannya menghapus
            // akses Admin, sesi ini akan diakhiri setelah transaksi berhasil.
            $akunSendiri = (int) $akun->getKey() === (int) $request->user()->getKey();
            $hilangAksesAdmin = $akunSendiri && (
                $data['role'] !== 'admin' || $data['status'] !== 'aktif'
            );

            if ($akun->role === 'mahasiswa' && $data['role'] !== 'mahasiswa'
                && ($akun->nim !== null || $akun->pendaftarans()->exists())) {
                throw ValidationException::withMessages([
                    'role' => 'Akun mahasiswa dengan NIM atau riwayat pendaftaran tidak dapat dialihkan langsung menjadi akun pegawai.',
                ]);
            }

            if ($pegawai && $data['role'] === 'mahasiswa') {
                throw ValidationException::withMessages([
                    'role' => 'Akun terhubung dengan Data Pegawai. Role tidak dapat diubah menjadi Mahasiswa.',
                ]);
            }

            if ($akun->role === 'admin' &&
                ($data['role'] !== 'admin' || $data['status'] !== 'aktif') &&
                User::query()->where('role', 'admin')->where('status', 'aktif')->count() <= 1 &&
                $akun->status === 'aktif') {
                throw ValidationException::withMessages([
                    'status' => 'Admin aktif terakhir tidak boleh dinonaktifkan atau diubah rolenya.',
                ]);
            }

            if ($pegawai) {
                if (Pegawai::query()->where('email', $data['email'])
                    ->where('id', '!=', $pegawai->getKey())->exists()) {
                    throw ValidationException::withMessages([
                        'email' => 'Email tersebut sudah tercatat di Data Pegawai lain.',
                    ]);
                }
            } elseif (in_array($data['role'], self::ROLES_AUTO_PEGAWAI, true)
                && Pegawai::query()->where('email', $data['email'])->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Email sudah ada di Data Pegawai. Akun ini belum terhubung; jangan membuat biodata duplikat.',
                ]);
            }

            $akun->name = trim($data['name']);
            $akun->email = $data['email'];
            $akun->no_hp = $data['no_hp'] ?? null;
            $akun->role = $data['role'];
            $akun->status = $data['status'];
            if (! empty($data['password'])) {
                $akun->password = $data['password'];
            }
            $akun->save();

            if ($pegawai) {
                $pegawai->nama_pegawai = $akun->name;
                $pegawai->email = $akun->email;
                $pegawai->no_hp = $akun->no_hp;
                $pegawai->save();
            } elseif (in_array($akun->role, self::ROLES_AUTO_PEGAWAI, true)) {
                // Akun yang berganti ke role operasional Ma'had, misalnya Guru, memperoleh biodata dasar.
                // Perubahan menjadi Admin tidak otomatis membuat baris pegawai.
                $pegawai = Pegawai::create([
                    'nama_pegawai' => $akun->name,
                    'email' => $akun->email,
                    'no_hp' => $akun->no_hp,
                    'status_pegawai' => true,
                ]);
                PegawaiAkun::create([
                    'pegawai_id' => $pegawai->getKey(),
                    'user_id' => $akun->getKey(),
                ]);
            }

            $this->catat($request, 'update_user', 'Memperbarui akun pengguna: '.$akun->name);

            return $hilangAksesAdmin;
        }, 3);

        if ($keluarDariAkunSendiri) {
            // Hak akses baru harus berlaku sekarang, bukan setelah sesi berakhir.
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('success', 'Perubahan akun berhasil disimpan. Silakan masuk kembali dengan akun yang masih aktif.');
        }

        return redirect()->route('admin.manajemen-pengguna')
            ->with('success', 'Data akun pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->pastikanAdmin($request);

        if ($user->getKey() === $request->user()->getKey()) {
            return back()->with('error', 'Akun Admin yang sedang digunakan tidak bisa dihapus.');
        }

        try {
            DB::transaction(function () use ($request, $user): void {
                $akun = User::query()->lockForUpdate()->findOrFail($user->getKey());
                if ($akun->role === 'admin' && $akun->status === 'aktif'
                    && User::query()->where('role', 'admin')->where('status', 'aktif')->count() <= 1) {
                    throw ValidationException::withMessages([
                        'user' => 'Admin aktif terakhir tidak boleh dihapus.',
                    ]);
                }

                // Hapus HUBUNGAN saja, bukan biodata di pegawais.
                PegawaiAkun::query()->where('user_id', $akun->getKey())->delete();
                $nama = $akun->name;
                $akun->delete();
                $this->catat($request, 'delete_user', 'Menghapus akun pengguna: '.$nama.' (biodata pegawai dipertahankan)');
            }, 3);
        } catch (QueryException $e) {
            if (in_array((string) $e->getCode(), ['23000', '23503', '19'], true)) {
                return back()->with('error', 'Akun masih digunakan oleh data lain (misalnya pendaftaran, log, atau transaksi). Nonaktifkan akun daripada menghapusnya.');
            }
            throw $e;
        }

        return redirect()->route('admin.manajemen-pengguna')
            ->with('success', 'Akun pengguna berhasil dihapus. Biodata pegawai tetap tersimpan.');
    }

    public function verifikasi(Request $request, int $id): RedirectResponse
    {
        $this->pastikanAdmin($request);
        $user = User::query()->findOrFail($id);
        if (! in_array(strtolower((string) $user->status), self::STATUS_MENUNGGU, true)) {
            return back()->with('error', 'Akun tidak sedang menunggu verifikasi.');
        }

        DB::transaction(function () use ($request, $user): void {
            $user->status = 'aktif';
            $user->save();
            $this->catat($request, 'verify_user', 'Memverifikasi akun: '.$user->name);
        });

        return back()->with('success', 'Akun berhasil diverifikasi dan diaktifkan.');
    }

    public function tolak(Request $request, int $id): RedirectResponse
    {
        $this->pastikanAdmin($request);
        $user = User::query()->findOrFail($id);
        if (! in_array(strtolower((string) $user->status), self::STATUS_MENUNGGU, true)) {
            return back()->with('error', 'Akun tidak sedang menunggu verifikasi.');
        }

        DB::transaction(function () use ($request, $user): void {
            $user->status = 'nonaktif';
            $user->save();
            $this->catat($request, 'reject_user', 'Menolak permohonan aktivasi akun: '.$user->name);
        });

        return back()->with('success', 'Aktivasi akun ditolak; akun dinonaktifkan.');
    }

    private function normalisasiInput(Request $request): void
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => mb_strtolower(trim($request->input('email')))]);
        }
        if (is_string($request->input('name'))) {
            $request->merge(['name' => trim($request->input('name'))]);
        }
    }
}
