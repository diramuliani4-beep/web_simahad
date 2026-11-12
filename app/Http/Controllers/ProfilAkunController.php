<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Halaman profil dan data pribadi untuk akun staf (Guru, Musyrifah,
 * Pimpinan, Admin Sistem). Mahasiswa tetap memakai halaman profil
 * yang sudah berjalan (profil.mhs dan data.mahasiswa).
 */
class ProfilAkunController extends Controller
{
    /** Role staf yang memakai halaman profil ini. */
    private const PERAN_STAF = ['guru', 'musyrifah', 'pimpinan', 'admin'];

    /**
     * Menampilkan profil akun yang sedang login.
     */
    public function profil(Request $request): View
    {
        $user = $this->penggunaStaf($request);

        return view('profil.index', [
            'user' => $user,
            'pegawai' => $user->pegawai,
            'labelPeran' => $user->labelPeran(),
            'jabatan' => $user->labelPeran(),
        ]);
    }

    /**
     * Memperbarui data pribadi yang menjadi hak pemilik akun dari halaman
     * Data Saya: nama, nomor WhatsApp, serta biodata pribadi pada Data Pegawai.
     * Email tidak diubah di sini karena perubahan email memerlukan verifikasi
     * kata sandi pada halaman Pengaturan Akun.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $this->penggunaStaf($request);
        $pegawai = $user->pegawai;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'no_hp.max' => 'Nomor WhatsApp maksimal 20 karakter.',
            'no_hp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda +, -, atau kurung.',
            'jenis_kelamin.in' => 'Jenis kelamin harus dipilih antara Laki-laki atau Perempuan.',
            'pendidikan_terakhir.max' => 'Pendidikan terakhir maksimal 100 karakter.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',
        ]);

        $nama = trim($validated['name']);
        $noHp = filled($validated['no_hp'] ?? null) ? trim($validated['no_hp']) : null;
        $pendidikan = filled($validated['pendidikan_terakhir'] ?? null) ? trim($validated['pendidikan_terakhir']) : null;
        $alamat = filled($validated['alamat'] ?? null) ? trim($validated['alamat']) : null;

        DB::transaction(function () use ($user, $pegawai, $validated, $nama, $noHp, $pendidikan, $alamat): void {
            $user->update([
                'name' => $nama,
                'no_hp' => $noHp,
            ]);

            $pegawai?->update([
                'nama_pegawai' => $nama,
                'no_hp' => $noHp,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?: null,
                'pendidikan_terakhir' => $pendidikan,
                'alamat' => $alamat,
            ]);
        });

        ActivityLog::create([
            'user_id' => $user->getKey(),
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'activity' => 'Perbarui Data Pribadi',
            'description' => 'Memperbarui data pribadi dari halaman Data Saya.',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return redirect()
            ->route('data.pengguna')
            ->with('success', 'Data pribadi berhasil diperbarui.');
    }

    /**
     * Menampilkan data akun dan biodata kepegawaian (hanya baca).
     */
    public function data(Request $request): View
    {
        $user = $this->penggunaStaf($request);

        return view('profil.data', [
            'user' => $user,
            'pegawai' => $user->pegawai,
            'labelPeran' => $user->labelPeran(),
            'jabatan' => $user->labelPeran(),
            'jumlahTugas' => $user->role === 'guru' ? $user->tugasDibuat()->count() : null,
            'jumlahPengumuman' => $user->role === 'musyrifah' ? $user->pengumumans()->count() : null,
        ]);
    }

    /**
     * Memastikan hanya akun staf yang dapat membuka halaman ini.
     */
    private function penggunaStaf(Request $request): User
    {
        $user = $request->user();

        abort_unless(
            $user && in_array(strtolower(trim((string) $user->role)), self::PERAN_STAF, true),
            403,
            'Halaman ini hanya dapat diakses oleh akun staf.'
        );

        return $user;
    }
}
