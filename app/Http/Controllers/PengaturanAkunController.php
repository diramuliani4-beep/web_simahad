<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Pengaturan akun mandiri untuk seluruh role:
 * profil, foto, kata sandi, serta ringkasan akses sesuai kebutuhan role.
 */
class PengaturanAkunController extends Controller
{
    /**
     * Ringkasan tiap role: label, halaman pengaturan, menu yang diakses, dan tautan cepat.
     *
     * @var array<string, array{
     *     label: string,
     *     tampilan: string,
     *     halaman: string,
     *     akses: list<string>,
     *     tautan: array{label: string, route: string}|null
     * }>
     */
    private const PERAN = [
        'mahasiswa' => [
            'label' => 'Mahasiswa',
            'tampilan' => 'Mahasiswa.profil_mhs',
            'halaman' => 'profil.mhs',
            'akses' => [
                'Pendaftaran', 'Pembelajaran', 'Tugas', 'Informasi Kamar',
                'Pembayaran', 'Absensi', 'Surat & Perizinan', 'Pengumuman',
            ],
            'tautan' => ['label' => 'Buka Data Mahasiswa', 'route' => 'data.mahasiswa'],
        ],
        'musyrifah' => [
            'label' => 'Musyrifah',
            'tampilan' => 'musyrifah.pengaturan_akun',
            'halaman' => 'musyrifah.pengaturan_akun',
            'akses' => [
                'Pendaftaran', 'Data Kamar', 'Pembayaran', 'Manajemen Pembelajaran',
                'Surat & Perizinan', 'Pengumuman', 'Laporan',
            ],
            'tautan' => ['label' => 'Buka Data Kamar', 'route' => 'musyrifah.data_kamar'],
        ],
        'guru' => [
            'label' => 'Guru',
            'tampilan' => 'guru.pengaturan_akun',
            'halaman' => 'guru.pengaturan-akun',
            'akses' => [
                'Jadwal Saya', 'Absensi', 'Kegiatan & Dokumentasi',
                'Tugas', 'Pengumpulan & Penilaian',
            ],
            'tautan' => ['label' => 'Buka Jadwal Saya', 'route' => 'guru.jadwal-saya'],
        ],
        'pimpinan' => [
            'label' => 'Pimpinan',
            'tampilan' => 'pimpinan.pengaturan_akun',
            'halaman' => 'pimpinan.pengaturan-akun',
            'akses' => ['Data Penghuni', 'Laporan Ma’had', 'Pembelajaran'],
            'tautan' => ['label' => 'Buka Laporan Ma’had', 'route' => 'pimpinan.laporan-mahad'],
        ],
        'admin' => [
            'label' => 'Admin',
            'tampilan' => 'Admin.pengaturan_akun',
            'halaman' => 'admin.pengaturan-akun',
            'akses' => [
                'Manajemen Pengguna', 'Data Pegawai', 'Data Mahasiswa',
                'Log Aktivitas', 'Pengaturan Sistem',
            ],
            'tautan' => ['label' => 'Buka Pengaturan Sistem', 'route' => 'admin.pengaturan-sistem'],
        ],
    ];

    /** Role yang mengelola profilnya melalui halaman pengaturan akun (bukan halaman profil mahasiswa). */
    private const PERAN_STAF = ['musyrifah', 'guru', 'pimpinan', 'admin'];

    /** Role yang boleh memakai form perubahan kata sandi dan foto profil bersama. */
    private const PERAN_MANDIRI = ['mahasiswa', 'musyrifah', 'guru', 'pimpinan', 'admin'];

    public function musyrifah(Request $request): View
    {
        return $this->halaman($request, 'musyrifah');
    }

    public function guru(Request $request): View
    {
        return $this->halaman($request, 'guru');
    }

    public function pimpinan(Request $request): View
    {
        return $this->halaman($request, 'pimpinan');
    }

    public function admin(Request $request): View
    {
        return $this->halaman($request, 'admin');
    }

    /**
     * Menyimpan perubahan nama, email, dan nomor WhatsApp milik akun staf.
     * Perubahan diselaraskan ke biodata pegawai yang tertaut agar tidak ada data ganda.
     */
    public function updateProfil(Request $request): RedirectResponse
    {
        $user = $this->pengguna($request, self::PERAN_STAF);
        $pegawai = $user->pegawai;

        $aturanEmail = [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->getKey()),
            Rule::unique('pegawais', 'email')->ignore($pegawai?->getKey()),
        ];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => $aturanEmail,
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email tersebut sudah digunakan akun atau data pegawai lain.',
            'no_hp.max' => 'Nomor WhatsApp maksimal 20 karakter.',
            'no_hp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda +, -, atau kurung.',
        ]);

        // Nilai kosong disimpan sebagai null agar konsisten di database.
        $validated['name'] = trim($validated['name']);
        $validated['no_hp'] = filled($validated['no_hp'] ?? null) ? trim($validated['no_hp']) : null;

        DB::transaction(function () use ($user, $pegawai, $validated): void {
            $user->update($validated);

            $pegawai?->update([
                'nama_pegawai' => $user->name,
                'email' => $user->email,
                'no_hp' => $user->no_hp,
            ]);
        });

        $this->catat($request, 'Perbarui Profil', 'Memperbarui nama, email, dan nomor WhatsApp akun.');

        return $this->kembali($user, 'Informasi akun berhasil diperbarui.');
    }

    /**
     * Mengganti foto profil akun (users.foto_profil).
     */
    public function updateFoto(Request $request): RedirectResponse
    {
        $user = $this->pengguna($request, self::PERAN_MANDIRI);

        $request->validate([
            'foto_profil' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'foto_profil.required' => 'Silakan pilih foto profil yang ingin diunggah.',
            'foto_profil.image' => 'Berkas foto profil harus berupa gambar.',
            'foto_profil.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_profil.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        $pathBaru = $request->file('foto_profil')->store('foto-profil', 'public');

        if (! $pathBaru) {
            return $this->kembali($user, 'Foto profil gagal disimpan. Silakan coba lagi.', 'error');
        }

        $pathLama = $user->foto_profil;
        $user->update(['foto_profil' => $pathBaru]);

        if ($pathLama && $pathLama !== $pathBaru) {
            Storage::disk('public')->delete($pathLama);
        }

        $this->catat($request, 'Ganti Foto Profil', 'Mengganti foto profil akun.');

        return $this->kembali($user, 'Foto profil berhasil diperbarui.');
    }

    /**
     * Menghapus foto profil dan mengembalikan tampilan ke avatar default.
     */
    public function hapusFoto(Request $request): RedirectResponse
    {
        $user = $this->pengguna($request, self::PERAN_MANDIRI);

        $pathLama = $user->foto_profil;

        if (! filled($pathLama)) {
            return $this->kembali($user, 'Foto profil belum tersedia untuk dihapus.', 'error');
        }

        $user->update(['foto_profil' => null]);
        Storage::disk('public')->delete($pathLama);

        $this->catat($request, 'Hapus Foto Profil', 'Menghapus foto profil akun.');

        return $this->kembali($user, 'Foto profil berhasil dihapus. Avatar default digunakan kembali.');
    }

    /**
     * Memperbarui kata sandi akun setelah memverifikasi kata sandi saat ini.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $this->pengguna($request, self::PERAN_MANDIRI);

        $validated = $request->validate([
            'password_lama' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:password_lama'],
        ], [
            'password_lama.required' => 'Kata sandi saat ini wajib diisi.',
            'password_lama.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sama.',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        // Cast 'hashed' pada model User mengamankan nilai kata sandi baru.
        $user->update(['password' => $validated['password']]);

        $this->catat($request, 'Ubah Kata Sandi', 'Memperbarui kata sandi akun.');

        return $this->kembali($user, 'Kata sandi berhasil diperbarui.');
    }

    /**
     * Menampilkan halaman pengaturan akun sesuai role pemilik akun.
     */
    private function halaman(Request $request, string $peran): View
    {
        $user = $this->pengguna($request, [$peran]);
        $user->load('pegawai');

        $konfigurasi = self::PERAN[$peran];

        return view($konfigurasi['tampilan'], [
            'user' => $user,
            'pegawai' => $user->pegawai,
            'labelPeran' => $konfigurasi['label'],
            'daftarAkses' => $konfigurasi['akses'],
            'tautanAkses' => $konfigurasi['tautan'],
        ]);
    }

    /**
     * Memastikan pengguna yang sedang login memiliki salah satu role yang diizinkan.
     *
     * @param  list<string>  $peran
     */
    private function pengguna(Request $request, array $peran): User
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));

        abort_unless(
            $user && in_array($role, $peran, true),
            403,
            'Halaman pengaturan akun ini tidak tersedia untuk peran Anda.'
        );

        return $user;
    }

    /**
     * Mengarahkan kembali ke halaman pengaturan akun milik role pengguna.
     */
    private function kembali(User $user, string $pesan, string $jenis = 'success'): RedirectResponse
    {
        $role = strtolower(trim((string) $user->role));
        $halaman = self::PERAN[$role]['halaman'] ?? 'dashboard';

        return redirect()->route($halaman)->with($jenis, $pesan);
    }

    private function catat(Request $request, string $aksi, string $deskripsi): void
    {
        $user = $request->user();

        if (! $user) {
            return;
        }

        ActivityLog::create([
            'user_id' => $user->getKey(),
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'activity' => $aksi,
            'description' => $deskripsi,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
