<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfilMhsController extends Controller
{
    /**
     * Menampilkan profil akun milik mahasiswa yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $this->penggunaMahasiswa($request);

        return view('Mahasiswa.profil_mhs', [
            'user' => $user,
            'kamar' => $user->kamar,
            'modeEdit' => $request->boolean('edit'),
        ]);
    }

    /**
     * Memperbarui informasi profil yang diizinkan.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'foto_profil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email tersebut sudah digunakan akun lain.',
            'no_hp.max' => 'Nomor WhatsApp maksimal 20 karakter.',
            'no_hp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda +, - , atau kurung.',
            'foto_profil.image' => 'Berkas foto profil harus berupa gambar.',
            'foto_profil.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_profil.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        if ($request->hasFile('foto_profil')) {
            $pathBaru = $request->file('foto_profil')->store('foto-profil', 'public');

            if (! $pathBaru) {
                return back()
                    ->withErrors(['foto_profil' => 'Foto profil gagal disimpan. Silakan coba lagi.'])
                    ->withInput();
            }

            $pathLama = $user->foto_profil;
            $validated['foto_profil'] = $pathBaru;

            if ($pathLama && $pathLama !== $pathBaru) {
                Storage::disk('public')->delete($pathLama);
            }
        }

        // Nilai kosong disimpan sebagai null agar konsisten di database.
        $validated['name'] = trim($validated['name']);
        $validated['no_hp'] = filled($validated['no_hp'] ?? null) ? trim($validated['no_hp']) : null;

        $user->update($validated);

        // Nama dan email pada pendaftaran yang disetujui ikut diselaraskan.
        $user->pendaftarans()
            ->where('status', 'disetujui')
            ->latest('id')
            ->first()
            ?->update([
                'nama_lengkap' => $user->name,
                'email_aktif' => $user->email,
            ]);

        return redirect()
            ->route('profil.mhs')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Mengganti foto profil melalui menu kamera pada halaman profil.
     */
    public function updateFoto(Request $request): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);

        $request->validate([
            'foto_profil' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'foto_profil.required' => 'Silakan pilih foto profil yang ingin diunggah.',
            'foto_profil.image' => 'Berkas foto profil harus berupa gambar.',
            'foto_profil.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_profil.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        $pathBaru = $request->file('foto_profil')->store('foto-profil', 'public');

        if (! $pathBaru) {
            return redirect()
                ->route('profil.mhs')
                ->with('error', 'Foto profil gagal disimpan. Silakan coba lagi.');
        }

        $pathLama = $user->foto_profil;
        $user->update(['foto_profil' => $pathBaru]);

        if ($pathLama && $pathLama !== $pathBaru) {
            Storage::disk('public')->delete($pathLama);
        }

        return redirect()
            ->route('profil.mhs')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Menghapus foto profil dan mengembalikan tampilan ke avatar default.
     */
    public function hapusFoto(Request $request): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);

        $pathLama = $user->foto_profil;

        if (! filled($pathLama)) {
            return redirect()
                ->route('profil.mhs')
                ->with('error', 'Foto profil belum tersedia untuk dihapus.');
        }

        $user->update(['foto_profil' => null]);
        Storage::disk('public')->delete($pathLama);

        return redirect()
            ->route('profil.mhs')
            ->with('success', 'Foto profil berhasil dihapus. Avatar default digunakan kembali.');
    }

    /**
     * Memastikan hanya akun mahasiswa yang dapat mengelola profilnya sendiri.
     */
    private function penggunaMahasiswa(Request $request): User
    {
        $user = $request->user();

        abort_unless(
            $user && strtolower(trim((string) $user->role)) === 'mahasiswa',
            403,
            'Halaman ini hanya dapat diakses oleh mahasiswa.'
        );

        return $user;
    }
}
