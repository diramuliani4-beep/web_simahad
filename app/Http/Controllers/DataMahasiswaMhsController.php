<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DataMahasiswaMhsController extends Controller
{
    public function __construct(private readonly AuditTrailService $audit) {}

    /**
     * Menampilkan data mahasiswa milik akun yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $this->penggunaMahasiswa($request);

        $pendaftaran = $this->pendaftaranDisetujui($user);
        $pendaftaranTerbaru = $pendaftaran ?: $this->pendaftaranTerbaru($user);

        return view('Mahasiswa.data_mahasiswa', [
            'user' => $user,
            'pendaftaran' => $pendaftaran,
            'kamar' => $user->kamar,
            'modeEdit' => $request->boolean('edit') && $pendaftaran !== null,
            'statusPendaftaran' => $this->labelStatus($pendaftaranTerbaru?->status),
        ]);
    }

    /**
     * Memperbarui data mahasiswa yang diizinkan untuk diubah sendiri.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);
        $pendaftaran = $this->pendaftaranDisetujui($user);

        if (! $pendaftaran) {
            return redirect()
                ->route('data.mahasiswa')
                ->with('error', 'Data pendaftaran belum disetujui Musyrifah sehingga belum dapat diperbarui.');
        }

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'no_wa' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'asal_sekolah' => ['nullable', 'string', 'max:255'],
            'nama_ortu' => ['nullable', 'string', 'max:255'],
            'no_hp_ortu' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, spasi, tanda +, - , atau kurung.',
            'no_wa.max' => 'Nomor WhatsApp maksimal 20 karakter.',
            'no_wa.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda +, - , atau kurung.',
            'asal_sekolah.max' => 'Asal sekolah maksimal 255 karakter.',
            'nama_ortu.max' => 'Nama orang tua/wali maksimal 255 karakter.',
            'no_hp_ortu.max' => 'Nomor HP orang tua/wali maksimal 20 karakter.',
            'no_hp_ortu.regex' => 'Nomor HP orang tua/wali hanya boleh berisi angka, spasi, tanda +, - , atau kurung.',
        ]);

        // Hanya kolom data diri yang diizinkan; NIM, status, dan data verifikasi tetap milik Musyrifah/Admin.
        $sebelum = $pendaftaran->getAttributes();

        $pendaftaran->update([
            'nama_lengkap' => trim($validated['nama_lengkap']),
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'no_wa' => $this->nilaiTeks($validated['no_wa'] ?? null),
            'asal_sekolah' => $this->nilaiTeks($validated['asal_sekolah'] ?? null),
            'nama_ortu' => $this->nilaiTeks($validated['nama_ortu'] ?? null),
            'no_hp_ortu' => $this->nilaiTeks($validated['no_hp_ortu'] ?? null),
        ]);

        // Nama dan nomor HP akun mengikuti data mahasiswa agar navbar dan profil tetap sinkron.
        $user->update([
            'name' => trim($validated['nama_lengkap']),
            'no_hp' => $this->nilaiTeks($validated['no_hp'] ?? null),
        ]);

        $this->audit->catat(
            'mahasiswa',
            'data_diperbarui',
            'Data mahasiswa '.$pendaftaran->nama_lengkap,
            (int) $pendaftaran->getKey(),
            'Mahasiswa memperbarui data dirinya.',
            $request->user(),
            ['nama_lengkap' => $sebelum['nama_lengkap'] ?? null, 'jenis_kelamin' => $sebelum['jenis_kelamin'] ?? null, 'no_wa' => $sebelum['no_wa'] ?? null],
            ['nama_lengkap' => $pendaftaran->nama_lengkap, 'jenis_kelamin' => $pendaftaran->jenis_kelamin, 'no_wa' => $pendaftaran->no_wa],
        );

        return redirect()
            ->route('data.mahasiswa')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    /**
     * Memastikan hanya akun mahasiswa yang dapat mengakses datanya sendiri.
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

    /**
     * Pendaftaran terakhir yang sudah disetujui Musyrifah.
     */
    private function pendaftaranDisetujui(User $user): ?Pendaftaran
    {
        return $user->pendaftarans()
            ->where('status', 'disetujui')
            ->latest('id')
            ->first();
    }

    /**
     * Pendaftaran terakhir tanpa memandang status, untuk pesan status pada tampilan.
     */
    private function pendaftaranTerbaru(User $user): ?Pendaftaran
    {
        return $user->pendaftarans()->latest('id')->first();
    }

    /**
     * Mengubah teks kosong menjadi null agar konsisten di database.
     */
    private function nilaiTeks(?string $nilai): ?string
    {
        return filled($nilai) ? trim($nilai) : null;
    }

    /**
     * Label status pendaftaran yang ramah dibaca.
     */
    private function labelStatus(?string $status): string
    {
        return match ($status) {
            'disetujui' => 'Disetujui',
            'menunggu' => 'Menunggu Verifikasi',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            'ditolak' => 'Ditolak',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : 'Belum Mendaftar',
        };
    }
}
