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
 * Pengaturan akun mandiri untuk seluruh role: pembuatan kata sandi pertama,
 * penggantian kata sandi, penggantian email terverifikasi, foto profil, serta
 * ringkasan akses sesuai kebutuhan role.
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
            'halaman' => 'pengaturan-akun.index',
            'akses' => [
                'Pendaftaran', 'Data Kamar', 'Pembayaran', 'Manajemen Pembelajaran',
                'Surat & Perizinan', 'Pengumuman', 'Laporan',
            ],
            'tautan' => ['label' => 'Buka Data Kamar', 'route' => 'musyrifah.data_kamar'],
        ],
        'guru' => [
            'label' => 'Guru',
            'tampilan' => 'guru.pengaturan_akun',
            'halaman' => 'pengaturan-akun.index',
            'akses' => [
                'Jadwal Saya', 'Absensi', 'Kegiatan & Dokumentasi',
                'Tugas', 'Pengumpulan & Penilaian',
            ],
            'tautan' => ['label' => 'Buka Jadwal Saya', 'route' => 'guru.jadwal-saya'],
        ],
        'pimpinan' => [
            'label' => 'Pimpinan',
            'tampilan' => 'pimpinan.pengaturan_akun',
            'halaman' => 'pengaturan-akun.index',
            'akses' => ['Data Penghuni', 'Laporan Ma’had', 'Pembelajaran'],
            'tautan' => ['label' => 'Buka Laporan Ma’had', 'route' => 'pimpinan.laporan-mahad'],
        ],
        'admin' => [
            'label' => 'Admin',
            'tampilan' => 'Admin.pengaturan_akun',
            'halaman' => 'pengaturan-akun.index',
            'akses' => [
                'Manajemen Pengguna', 'Data Pegawai', 'Data Mahasiswa',
                'Log Aktivitas', 'Pengaturan Sistem',
            ],
            'tautan' => ['label' => 'Buka Pengaturan Sistem', 'route' => 'admin.pengaturan-sistem'],
        ],
    ];

    /** Role yang boleh memakai form kata sandi, email, dan foto profil bersama. */
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
     * Halaman Pengaturan Akun terpadu untuk seluruh role.
     * Form memakai route yang sesuai dengan hak masing-masing role,
     * sehingga akun mahasiswa tetap memakai alur halaman profilnya.
     */
    public function saya(Request $request): View
    {
        $user = $this->pengguna($request, self::PERAN_MANDIRI);
        $peran = strtolower(trim((string) $user->role));
        $konfigurasi = self::PERAN[$peran] ?? self::PERAN['mahasiswa'];
        $mahasiswa = $peran === 'mahasiswa';

        return view('pengaturan.index', [
            'user' => $user,
            'pegawai' => $user->pegawai,
            'labelPeran' => $konfigurasi['label'],
            'daftarAkses' => $konfigurasi['akses'],
            'tautanAkses' => $konfigurasi['tautan'],
            // Akun mahasiswa tetap memakai formulir profil lamanya, sedangkan
            // staf mengubah data pribadi dari halaman Data Saya.
            'aksiProfil' => $mahasiswa ? 'profil.mhs.update' : null,
            'aksiFoto' => $mahasiswa ? 'profil.mhs.foto.update' : 'pengaturan-akun.foto.update',
            'aksiHapusFoto' => $mahasiswa ? 'profil.mhs.foto.destroy' : 'pengaturan-akun.foto.destroy',
            'aksiPassword' => $mahasiswa ? 'profil.mhs.password.update' : 'pengaturan-akun.password.update',
            'aksiEmail' => 'pengaturan-akun.email.update',
        ]);
    }

    /**
     * Mengganti email akun dengan verifikasi kata sandi saat ini.
     * Email ikut diselaraskan ke biodata pegawai agar tidak ada data ganda.
     */
    public function updateEmail(Request $request): RedirectResponse
    {
        $user = $this->pengguna($request, self::PERAN_MANDIRI);

        abort_unless(
            $user->sudahPunyaKataSandi(),
            403,
            'Buat kata sandi terlebih dahulu sebelum mengganti email akun.'
        );

        $pegawai = $user->pegawai;

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->getKey()),
                Rule::unique('pegawais', 'email')->ignore($pegawai?->getKey()),
            ],
            'password_saat_ini' => ['required', 'string', 'current_password'],
        ], [
            'email.required' => 'Email baru wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email tersebut sudah digunakan akun atau data pegawai lain.',
            'password_saat_ini.required' => 'Kata sandi saat ini wajib diisi sebagai verifikasi.',
            'password_saat_ini.current_password' => 'Kata sandi saat ini tidak cocok. Email tidak diubah.',
        ]);

        $emailBaru = strtolower(trim($validated['email']));

        if ($emailBaru === strtolower((string) $user->email)) {
            return $this->kembali($user, 'Email baru sama dengan email yang sedang dipakai.', 'error');
        }

        DB::transaction(function () use ($user, $pegawai, $emailBaru): void {
            // Kepemilikan akun sudah diverifikasi dengan kata sandi saat ini,
            // sehingga email baru langsung ditandai terverifikasi.
            $user->update([
                'email' => $emailBaru,
                'email_verified_at' => now(),
            ]);

            $pegawai?->update(['email' => $emailBaru]);
        });

        $this->catat($request, 'Ubah Email Akun', 'Mengganti email akun melalui verifikasi kata sandi.');

        return $this->kembali($user, 'Email akun berhasil diperbarui.');
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

        return $this->kembali($user, 'Foto profil berhasil diperbarui.', 'success', $this->tujuanFoto($user));
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

        return $this->kembali(
            $user,
            'Foto profil berhasil dihapus. Avatar default digunakan kembali.',
            'success',
            $this->tujuanFoto($user)
        );
    }

    /**
     * Memperbarui kata sandi akun. Bila pemilik akun belum pernah menentukan
     * kata sandi sendiri (akun dibuat Admin), formulir berlaku sebagai
     * pembuatan kata sandi pertama dan identitas dikonfirmasi lewat email akun.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $this->pengguna($request, self::PERAN_MANDIRI);
        $kataSandiPertama = ! $user->sudahPunyaKataSandi();

        $aturan = [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $pesan = [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sama.',
        ];

        if ($kataSandiPertama) {
            $aturan['email_konfirmasi'] = ['required', 'email'];
            $pesan['email_konfirmasi.required'] = 'Ketik email akun Anda untuk konfirmasi pembuatan kata sandi.';
            $pesan['email_konfirmasi.email'] = 'Format email konfirmasi tidak valid.';
        } else {
            $aturan['password_lama'] = ['required', 'string', 'current_password'];
            $aturan['password'][] = 'different:password_lama';
            $pesan['password_lama.required'] = 'Kata sandi saat ini wajib diisi.';
            $pesan['password_lama.current_password'] = 'Kata sandi saat ini tidak cocok.';
            $pesan['password.different'] = 'Kata sandi baru harus berbeda dari kata sandi saat ini.';
        }

        $validated = $request->validate($aturan, $pesan);

        if ($kataSandiPertama
            && strcasecmp(trim((string) $validated['email_konfirmasi']), (string) $user->email) !== 0) {
            return $this->kembali(
                $user,
                'Email konfirmasi tidak sesuai dengan email akun Anda.',
                'error'
            );
        }

        // Cast 'hashed' pada model User mengamankan nilai kata sandi baru.
        $user->update([
            'password' => $validated['password'],
            'password_set_at' => now(),
        ]);

        $this->catat(
            $request,
            $kataSandiPertama ? 'Buat Kata Sandi' : 'Ubah Kata Sandi',
            $kataSandiPertama
                ? 'Membuat kata sandi pertama kali untuk akun sendiri.'
                : 'Memperbarui kata sandi akun.'
        );

        return $this->kembali(
            $user,
            $kataSandiPertama ? 'Kata sandi berhasil dibuat. Gunakan kata sandi baru saat masuk.' : 'Kata sandi berhasil diperbarui.'
        );
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
     * Halaman yang menerima hasil unggah atau penghapusan foto profil.
     * Menu kamera berada di halaman Lihat Profil, bukan di Pengaturan Akun.
     */
    private function tujuanFoto(User $user): string
    {
        return strtolower(trim((string) $user->role)) === 'mahasiswa' ? 'profil.mhs' : 'profil.pengguna';
    }

    /**
     * Mengarahkan kembali ke halaman pengaturan akun milik role pengguna,
     * atau ke halaman tujuan tertentu bila diisi.
     */
    private function kembali(
        User $user,
        string $pesan,
        string $jenis = 'success',
        ?string $tujuan = null
    ): RedirectResponse {
        $role = strtolower(trim((string) $user->role));
        $halaman = $tujuan ?? self::PERAN[$role]['halaman'] ?? 'dashboard';

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
