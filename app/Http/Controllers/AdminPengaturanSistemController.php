<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PengaturanSistem;
use App\Services\AuditTrailService;
use App\Services\BackupDatabaseService;
use App\Support\KonfigurasiEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Pengaturan Sistem untuk role Admin Sistem.
 *
 * Empat bagian: Identitas Aplikasi, Pengaturan Email, Keamanan Sistem, serta
 * Backup dan Pemeliharaan. Setiap perubahan tersimpan pada baris pengaturan
 * sistem (kolom baru) atau berkas di storage, lalu diterapkan melalui
 * middleware TerapkanPengaturanSistem.
 */
class AdminPengaturanSistemController extends Controller
{
    /** Jumlah baris riwayat terbaru yang ditampilkan pada halaman. */
    private const BATAS_RIWAYAT = 5;

    /** Aktivitas lama yang belum memakai kolom modul. */
    private const AKTIVITAS_LAMA = 'Pengaturan Sistem';

    /** Modul audit yang termasuk riwayat perubahan sistem. */
    private const MODUL_PERUBAHAN = ['pengaturan', 'pemeliharaan'];

    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly BackupDatabaseService $backup,
    ) {}

    /**
     * Menampilkan empat bagian pengaturan beserta status konfigurasi.
     */
    public function index(Request $request): View
    {
        $this->pastikanAdmin($request);

        $pengaturan = PengaturanSistem::aktif();

        // Konfigurasi email dari basis data diterapkan agar status yang
        // ditampilkan sama dengan yang dipakai saat mengirim email.
        KonfigurasiEmail::terapkan($pengaturan);

        $daftarBackup = $this->backup->daftar();

        return view('Admin.pengaturan_sistem', [
            'pengaturan' => $pengaturan,
            'versi' => (string) config('app.version'),
            'aktivitasPengguna' => $this->riwayatAktivitas(),
            'perubahanSistem' => $this->riwayatPerubahanSistem(),
            'totalAktivitas' => ActivityLog::query()->count(),
            'daftarBackup' => $daftarBackup,
            'totalUkuranBackup' => $this->formatUkuran((int) $daftarBackup->sum('ukuran')),
            'statusIdentitas' => $this->statusIdentitas($pengaturan),
            'statusEmail' => $this->statusEmail(),
            'statusKeamanan' => $this->statusKeamanan($pengaturan),
            'statusBackup' => $this->statusBackup($daftarBackup->count(), $pengaturan),
            'bukaBagian' => $this->bukaBagian($request),
        ]);
    }

    /**
     * Bagian Identitas Aplikasi: nama aplikasi, institusi, dan logo.
     */
    public function updateIdentitas(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);

        $validated = $request->validate([
            'nama_sistem' => ['required', 'string', 'max:60'],
            'nama_institusi' => ['required', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'hapus_logo' => ['nullable', 'boolean'],
        ], [
            'nama_sistem.required' => 'Nama aplikasi wajib diisi.',
            'nama_sistem.max' => 'Nama aplikasi maksimal 60 karakter.',
            'nama_institusi.required' => 'Nama institusi wajib diisi.',
            'nama_institusi.max' => 'Nama institusi maksimal 150 karakter.',
            'logo.image' => 'Logo harus berupa berkas gambar.',
            'logo.mimes' => 'Logo harus berformat PNG, JPG, JPEG, atau WEBP.',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
        ]);

        $pengaturan = PengaturanSistem::aktif();
        $logoLama = $pengaturan->logo_path;

        $sebelum = [
            'nama_sistem' => (string) $pengaturan->nama_sistem,
            'nama_institusi' => (string) $pengaturan->nama_institusi,
            'logo' => $logoLama ? basename($logoLama) : 'logo bawaan',
        ];

        $data = [
            'nama_sistem' => $validated['nama_sistem'],
            'nama_institusi' => $validated['nama_institusi'],
        ];

        $logoBaru = null;

        if ($request->hasFile('logo')) {
            $logoBaru = $request->file('logo')->store('logo-aplikasi', 'public');
            $data['logo_path'] = $logoBaru;
        } elseif ($request->boolean('hapus_logo')) {
            $data['logo_path'] = null;
        }

        $pengaturan->update($data);

        if ($logoLama !== null && $logoLama !== $logoBaru && ($logoBaru !== null || $request->boolean('hapus_logo'))) {
            Storage::disk('public')->delete($logoLama);
        }

        $sesudah = [
            'nama_sistem' => (string) $pengaturan->nama_sistem,
            'nama_institusi' => (string) $pengaturan->nama_institusi,
            'logo' => $pengaturan->logo_path ? basename((string) $pengaturan->logo_path) : 'logo bawaan',
        ];

        if ($sebelum !== $sesudah) {
            $this->catat(
                $request,
                'pengaturan',
                'diperbarui',
                'Identitas Aplikasi',
                ($request->user()?->name ?? 'Admin')
                    .' memperbarui identitas aplikasi: nama aplikasi '.$sesudah['nama_sistem']
                    .', nama institusi '.$sesudah['nama_institusi']
                    .', logo '.$sesudah['logo'].'.',
                (int) $pengaturan->getKey(),
                $sebelum,
                $sesudah,
            );
        }

        return redirect()
            ->route('admin.pengaturan-sistem')
            ->with('success', 'Identitas aplikasi berhasil disimpan dan langsung diterapkan.');
    }

    /**
     * Bagian Pengaturan Email: mode lokal (Mailpit) atau SMTP kustom.
     */
    public function updateEmail(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);

        $validated = $request->validate([
            'mail_mode' => ['required', 'in:lokal,smtp'],
            'mail_host' => ['nullable', 'required_if:mail_mode,smtp', 'string', 'max:255'],
            'mail_port' => ['nullable', 'required_if:mail_mode,smtp', 'integer', 'between:1,65535'],
            'mail_scheme' => ['nullable', 'in:smtp,smtps'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'hapus_password' => ['nullable', 'boolean'],
            'mail_from_address' => ['nullable', 'required_if:mail_mode,smtp', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:150'],
        ], [
            'mail_mode.required' => 'Mode pengiriman email wajib dipilih.',
            'mail_mode.in' => 'Mode pengiriman email tidak dikenal.',
            'mail_host.required_if' => 'Host SMTP wajib diisi untuk mode SMTP kustom.',
            'mail_port.required_if' => 'Port SMTP wajib diisi untuk mode SMTP kustom.',
            'mail_port.integer' => 'Port SMTP harus berupa angka.',
            'mail_port.between' => 'Port SMTP harus antara 1 dan 65535.',
            'mail_from_address.required_if' => 'Alamat pengirim wajib diisi untuk mode SMTP kustom.',
            'mail_from_address.email' => 'Alamat pengirim harus berupa email yang valid.',
            'mail_scheme.in' => 'Jenis koneksi email tidak dikenal.',
        ]);

        $pengaturan = PengaturanSistem::aktif();

        $mode = (string) $validated['mail_mode'];
        $host = $mode === 'smtp' ? trim((string) ($validated['mail_host'] ?? '')) : null;
        $port = $mode === 'smtp' ? (int) $validated['mail_port'] : null;
        $username = $mode === 'smtp' ? trim((string) ($validated['mail_username'] ?? '')) : null;
        $passwordBaru = trim((string) ($validated['mail_password'] ?? ''));

        // SMTP non-lokal tetap membutuhkan kredensial. Kata sandi lama dipakai
        // bila kolom kata sandi dibiarkan kosong.
        $lokal = in_array(strtolower((string) $host), ['127.0.0.1', 'localhost', '::1', 'mailpit'], true);

        if ($mode === 'smtp' && ! $lokal
            && (blank($username) || (blank($passwordBaru) && blank($pengaturan->mail_password)))) {
            throw ValidationException::withMessages([
                'mail_username' => 'Username dan kata sandi SMTP wajib diisi untuk server email non-lokal.',
            ]);
        }

        $sebelum = [
            'mode' => (string) ($pengaturan->mail_mode ?: 'lokal'),
            'host' => (string) ($pengaturan->mail_host ?? '-'),
            'port' => (string) ($pengaturan->mail_port ?? '-'),
            'pengirim' => (string) ($pengaturan->mail_from_address ?? '-'),
            'kata_sandi' => $pengaturan->mail_password ? 'tersimpan' : 'kosong',
        ];

        $data = [
            'mail_mode' => $mode,
            'mail_host' => $host,
            'mail_port' => $port,
            'mail_scheme' => $mode === 'smtp' && filled($validated['mail_scheme'] ?? null)
                ? $validated['mail_scheme']
                : null,
            'mail_username' => $username,
            'mail_from_address' => $mode === 'smtp' ? trim((string) ($validated['mail_from_address'] ?? '')) : null,
            'mail_from_name' => $mode === 'smtp' ? trim((string) ($validated['mail_from_name'] ?? '')) : null,
        ];

        if ($request->boolean('hapus_password')) {
            $data['mail_password'] = null;
        } elseif ($passwordBaru !== '') {
            $data['mail_password'] = $passwordBaru;
        }

        $pengaturan->update($data);

        $sesudah = [
            'mode' => $mode,
            'host' => (string) ($pengaturan->mail_host ?? '-'),
            'port' => (string) ($pengaturan->mail_port ?? '-'),
            'pengirim' => (string) ($pengaturan->mail_from_address ?? '-'),
            'kata_sandi' => $pengaturan->mail_password ? 'tersimpan' : 'kosong',
        ];

        if ($sebelum !== $sesudah) {
            $this->catat(
                $request,
                'pengaturan',
                'diperbarui',
                'Pengaturan Email',
                ($request->user()?->name ?? 'Admin')
                    .' memperbarui pengaturan email: mode '.$mode
                    .($mode === 'smtp' ? ', host '.$pengaturan->mail_host.' port '.$pengaturan->mail_port : '')
                    .', kata sandi '.$sesudah['kata_sandi'].'.',
                (int) $pengaturan->getKey(),
                $sebelum,
                $sesudah,
            );
        }

        return redirect()
            ->route('admin.pengaturan-sistem')
            ->with('success', 'Pengaturan email berhasil disimpan. Gunakan tombol Tes Koneksi untuk memastikan pengiriman berjalan.');
    }

    /**
     * Mengirim email uji memakai konfigurasi email yang sedang aktif.
     */
    public function tesEmail(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);

        $admin = $request->user();
        $tujuan = trim((string) $admin?->email);

        if ($tujuan === '' || filter_var($tujuan, FILTER_VALIDATE_EMAIL) === false) {
            return redirect()
                ->route('admin.pengaturan-sistem')
                ->with('error', 'Akun Admin belum memiliki alamat email yang valid sehingga email uji tidak dapat dikirim.');
        }

        KonfigurasiEmail::terapkan(PengaturanSistem::aktif());

        $masalah = KonfigurasiEmail::masalah();

        if ($masalah !== null) {
            return redirect()
                ->route('admin.pengaturan-sistem')
                ->with('error', 'Tes koneksi belum dapat dijalankan: '.$masalah);
        }

        try {
            Mail::raw(
                "Ini email uji dari halaman Pengaturan Sistem SIMAHAD.\n\n"
                .'Bila pesan ini diterima, konfigurasi pengiriman email sudah benar.',
                fn ($pesan) => $pesan->to($tujuan)->subject('Uji Koneksi Email SIMAHAD')
            );
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.pengaturan-sistem')
                ->with('error', 'Koneksi email gagal ('.$e::class.'). Rincian sudah dicatat pada log aplikasi.');
        }

        $pesan = 'Email uji terkirim ke '.$tujuan.' melalui mailer "'.KonfigurasiEmail::mailer().'".';

        if (KonfigurasiEmail::smtpLokal()) {
            $pesan .= ' Periksa dashboard Mailpit di http://127.0.0.1:8025.';
        }

        return redirect()->route('admin.pengaturan-sistem')->with('success', $pesan);
    }

    /**
     * Bagian Keamanan Sistem: durasi sesi login.
     */
    public function updateKeamanan(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);

        $validated = $request->validate([
            'durasi_sesi' => ['required', 'integer', 'min:5', 'max:1440'],
        ], [
            'durasi_sesi.required' => 'Durasi sesi wajib diisi.',
            'durasi_sesi.integer' => 'Durasi sesi harus berupa angka.',
            'durasi_sesi.min' => 'Durasi sesi minimal 5 menit.',
            'durasi_sesi.max' => 'Durasi sesi maksimal 1440 menit (24 jam).',
        ]);

        $pengaturan = PengaturanSistem::aktif();
        $sebelum = (int) $pengaturan->durasi_sesi;

        $pengaturan->update(['durasi_sesi' => (int) $validated['durasi_sesi']]);

        if ($sebelum !== (int) $pengaturan->durasi_sesi) {
            $this->catat(
                $request,
                'pengaturan',
                'diperbarui',
                'Keamanan Sistem',
                ($request->user()?->name ?? 'Admin')
                    .' mengubah durasi sesi login dari '.$sebelum.' menjadi '.$pengaturan->durasi_sesi.' menit.',
                (int) $pengaturan->getKey(),
                ['durasi_sesi' => (string) $sebelum],
                ['durasi_sesi' => (string) $pengaturan->durasi_sesi],
            );
        }

        return redirect()
            ->route('admin.pengaturan-sistem')
            ->with('success', 'Durasi sesi login berhasil disimpan dan langsung diterapkan.');
    }

    /**
     * Bagian Backup dan Pemeliharaan: retensi berkas backup.
     */
    public function updateBackup(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);

        $validated = $request->validate([
            'backup_retensi' => ['required', 'integer', 'min:1', 'max:30'],
        ], [
            'backup_retensi.required' => 'Retensi backup wajib diisi.',
            'backup_retensi.integer' => 'Retensi backup harus berupa angka.',
            'backup_retensi.min' => 'Retensi backup minimal 1 berkas.',
            'backup_retensi.max' => 'Retensi backup maksimal 30 berkas.',
        ]);

        $pengaturan = PengaturanSistem::aktif();
        $sebelum = $pengaturan->retensiBackup();

        $pengaturan->update(['backup_retensi' => (int) $validated['backup_retensi']]);

        if ($sebelum !== $pengaturan->retensiBackup()) {
            $this->catat(
                $request,
                'pengaturan',
                'diperbarui',
                'Backup dan Pemeliharaan',
                ($request->user()?->name ?? 'Admin')
                    .' mengubah retensi backup dari '.$sebelum.' menjadi '.$pengaturan->retensiBackup().' berkas.',
                (int) $pengaturan->getKey(),
                ['backup_retensi' => (string) $sebelum],
                ['backup_retensi' => (string) $pengaturan->retensiBackup()],
            );
        }

        return redirect()
            ->route('admin.pengaturan-sistem')
            ->with('success', 'Retensi backup berhasil disimpan.');
    }

    /** Membuat berkas backup basis data baru. */
    public function buatBackup(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);

        try {
            $hasil = $this->backup->buat(PengaturanSistem::aktif()->retensiBackup());
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.pengaturan-sistem')
                ->with('error', 'Backup gagal dibuat: '.mb_substr($e->getMessage(), 0, 200));
        }

        $this->catat(
            $request,
            'pemeliharaan',
            'backup_dibuat',
            'Backup Basis Data',
            ($request->user()?->name ?? 'Admin').' membuat berkas backup '.$hasil['nama']
                .' ('.$hasil['dibaca'].', '.$hasil['tabel'].' tabel).',
        );

        return redirect()
            ->route('admin.pengaturan-sistem')
            ->with('success', 'Backup '.$hasil['nama'].' berhasil dibuat ('.$hasil['dibaca'].', '.$hasil['tabel'].' tabel).');
    }

    /** Mengunduh berkas backup yang tersimpan. */
    public function unduhBackup(Request $request, string $berkas): StreamedResponse|RedirectResponse
    {
        $this->pastikanAdmin($request);

        try {
            $path = $this->backup->pathUnduh($berkas);
        } catch (Throwable) {
            $path = null;
        }

        if ($path === null) {
            return redirect()
                ->route('admin.pengaturan-sistem')
                ->with('error', 'Berkas backup tidak ditemukan.');
        }

        $this->catat(
            $request,
            'pemeliharaan',
            'backup_diunduh',
            'Backup Basis Data',
            ($request->user()?->name ?? 'Admin').' mengunduh berkas backup '.$berkas.'.',
        );

        return Storage::disk(BackupDatabaseService::DISK)->download($path, $berkas);
    }

    /** Menghapus berkas backup tertentu. */
    public function hapusBackup(Request $request, string $berkas): RedirectResponse
    {
        $this->pastikanAdmin($request);

        try {
            $terhapus = $this->backup->hapus($berkas);
        } catch (Throwable) {
            $terhapus = false;
        }

        if (! $terhapus) {
            return redirect()
                ->route('admin.pengaturan-sistem')
                ->with('error', 'Berkas backup tidak ditemukan atau gagal dihapus.');
        }

        $this->catat(
            $request,
            'pemeliharaan',
            'backup_dihapus',
            'Backup Basis Data',
            ($request->user()?->name ?? 'Admin').' menghapus berkas backup '.$berkas.'.',
        );

        return redirect()
            ->route('admin.pengaturan-sistem')
            ->with('success', 'Berkas backup '.$berkas.' berhasil dihapus.');
    }

    /** Menjalankan tindakan pemeliharaan cache yang tersedia. */
    public function pemeliharaan(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);

        $validated = $request->validate([
            'tindakan' => ['required', 'in:cache_aplikasi,cache_tampilan,cache_konfigurasi'],
        ], [
            'tindakan.required' => 'Tindakan pemeliharaan wajib dipilih.',
            'tindakan.in' => 'Tindakan pemeliharaan tidak dikenal.',
        ]);

        $perintah = [
            'cache_aplikasi' => ['cache:clear', 'Cache aplikasi'],
            'cache_tampilan' => ['view:clear', 'Cache tampilan'],
            'cache_konfigurasi' => ['config:clear', 'Cache konfigurasi'],
        ][$validated['tindakan']];

        try {
            Artisan::call($perintah[0]);
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.pengaturan-sistem')
                ->with('error', 'Pemeliharaan gagal dijalankan: '.mb_substr($e->getMessage(), 0, 200));
        }

        $this->catat(
            $request,
            'pemeliharaan',
            'pemeliharaan_dijalankan',
            $perintah[1],
            ($request->user()?->name ?? 'Admin').' menjalankan pemeliharaan '.$perintah[1]
                .' melalui halaman Pengaturan Sistem.',
        );

        return redirect()
            ->route('admin.pengaturan-sistem')
            ->with('success', $perintah[1].' berhasil dibersihkan.');
    }

    /**
     * Riwayat aktivitas pengguna terbaru dari seluruh modul.
     *
     * @return Collection<int, ActivityLog>
     */
    private function riwayatAktivitas(): Collection
    {
        return ActivityLog::query()
            ->latest('created_at')
            ->latest('id')
            ->limit(self::BATAS_RIWAYAT)
            ->get();
    }

    /**
     * Riwayat perubahan konfigurasi dan pemeliharaan sistem.
     *
     * Baris baru memakai kolom modul, sedangkan catatan lama belum memiliki
     * kolom tersebut sehingga dikenali dari nilai aktivitasnya.
     *
     * @return Collection<int, ActivityLog>
     */
    private function riwayatPerubahanSistem(): Collection
    {
        return ActivityLog::query()
            ->where(function ($query) {
                $query->whereIn('modul', self::MODUL_PERUBAHAN)
                    ->orWhere('activity', self::AKTIVITAS_LAMA);
            })
            ->latest('created_at')
            ->latest('id')
            ->limit(self::BATAS_RIWAYAT)
            ->get();
    }

    /**
     * @return array{label: string, nada: string, keterangan: string}
     */
    private function statusIdentitas(PengaturanSistem $pengaturan): array
    {
        $lengkap = filled($pengaturan->nama_sistem) && filled($pengaturan->nama_institusi);

        return [
            'label' => $lengkap ? 'Lengkap' : 'Perlu dilengkapi',
            'nada' => $lengkap ? 'ok' : 'warn',
            'keterangan' => $lengkap
                ? 'Nama aplikasi dan institusi sudah dipakai di seluruh halaman.'
                : 'Lengkapi nama aplikasi dan nama institusi.',
        ];
    }

    /**
     * @return array{label: string, nada: string, keterangan: string, lokal: bool, mailer: string}
     */
    private function statusEmail(): array
    {
        $masalah = KonfigurasiEmail::masalah();
        $lokal = KonfigurasiEmail::smtpLokal();

        if ($masalah !== null) {
            return [
                'label' => 'Perlu perhatian',
                'nada' => 'danger',
                'keterangan' => $masalah,
                'lokal' => $lokal,
                'mailer' => KonfigurasiEmail::mailer(),
            ];
        }

        return [
            'label' => $lokal ? 'Mode lokal (Mailpit)' : 'SMTP aktif',
            'nada' => 'ok',
            'keterangan' => $lokal
                ? 'Email ditangkap Mailpit pada 127.0.0.1:1025. Dashboard: http://127.0.0.1:8025.'
                : 'Email dikirim melalui '.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port').'.',
            'lokal' => $lokal,
            'mailer' => KonfigurasiEmail::mailer(),
        ];
    }

    /**
     * @return array{label: string, nada: string, keterangan: string}
     */
    private function statusKeamanan(PengaturanSistem $pengaturan): array
    {
        return [
            'label' => 'Audit aktif',
            'nada' => 'ok',
            'keterangan' => 'Sesi login '.$pengaturan->durasi_sesi.' menit. Seluruh perubahan sistem direkam pada riwayat di bawah.',
        ];
    }

    /**
     * @return array{label: string, nada: string, keterangan: string}
     */
    private function statusBackup(int $jumlah, PengaturanSistem $pengaturan): array
    {
        return [
            'label' => $jumlah > 0 ? $jumlah.' berkas backup' : 'Belum ada backup',
            'nada' => $jumlah > 0 ? 'ok' : 'warn',
            'keterangan' => 'Menyimpan maksimal '.$pengaturan->retensiBackup().' berkas backup terbaru.',
        ];
    }

    /** Menentukan bagian yang formulirnya harus langsung terbuka setelah validasi gagal. */
    private function bukaBagian(Request $request): ?string
    {
        $errors = $request->session()->get('errors');

        if ($errors === null) {
            return null;
        }

        return match (true) {
            $errors->hasAny(['nama_sistem', 'nama_institusi', 'logo', 'hapus_logo']) => 'identitas',
            $errors->hasAny(['mail_mode', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_from_address', 'mail_from_name']) => 'email',
            $errors->hasAny(['durasi_sesi']) => 'keamanan',
            $errors->hasAny(['backup_retensi']) => 'backup',
            default => null,
        };
    }

    /**
     * Hanya Admin Sistem yang boleh membuka dan mengubah pengaturan.
     */
    private function pastikanAdmin(Request $request): void
    {
        abort_unless(
            strtolower((string) $request->user()?->role) === 'admin',
            403,
            'Hanya Admin Sistem yang dapat membuka Pengaturan Sistem.'
        );
    }

    /**
     * Mencatat perubahan pengaturan pada audit trail.
     *
     * Nilai rahasia (kata sandi SMTP) tidak pernah dikirim ke metode ini.
     *
     * @param  array<string, string>  $sebelum
     * @param  array<string, string>  $sesudah
     */
    private function catat(
        Request $request,
        string $modul,
        string $aksi,
        string $objek,
        string $deskripsi,
        ?int $objekId = null,
        array $sebelum = [],
        array $sesudah = [],
    ): void {
        $this->audit->catat(
            $modul,
            $aksi,
            $objek,
            $objekId,
            $deskripsi,
            $request->user(),
            $sebelum,
            $sesudah,
        );
    }

    private function formatUkuran(int $byte): string
    {
        if ($byte >= 1048576) {
            return number_format($byte / 1048576, 1, ',', '.').' MB';
        }

        return number_format(max($byte, 0) / 1024, 1, ',', '.').' KB';
    }
}
