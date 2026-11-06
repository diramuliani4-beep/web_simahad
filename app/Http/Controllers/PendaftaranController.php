<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    /** Tahap 1: Form data diri. */
    public function pribadi(Request $request): View|RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        $data = $this->dataDraft($request);

        return view('pendaftaran.pribadi', compact('data'));
    }

    /** Validasi dan simpan data diri sementara. */
    public function simpanPribadi(Request $request): RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        $data = $this->dataDraft($request);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::in([$request->user()->email]),
            ],
            'jenis_kelamin' => [
                'required',
                Rule::in(['Laki-laki', 'Perempuan']),
            ],
            'no_wa' => ['required', 'string', 'max:20'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'nama_ortu' => ['required', 'string', 'max:255'],
            'no_hp_ortu' => ['required', 'string', 'max:20'],
            'kategori' => ['nullable', Rule::in(['umum', 'kip'])],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email aktif wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.in' => 'Email harus sama dengan email akun yang sedang login.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
            'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
            'nama_ortu.required' => 'Nama orang tua wajib diisi.',
            'no_hp_ortu.required' => 'Nomor HP orang tua wajib diisi.',
        ]);

        $validated['kategori'] = $validated['kategori'] ?? 'umum';

        session([
            'pendaftaran_user_id' => $request->user()->id,
            'pendaftaran' => array_merge($data, $validated),
        ]);

        return redirect()->route('pendaftaran.akademik');
    }

    /** Tahap 2: Form data akademik. */
    public function akademik(Request $request): View|RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        $data = $this->dataDraft($request);

        if (empty($data['nama_lengkap']) || empty($data['jenis_kelamin'])) {
            return redirect()->route('pendaftaran.pribadi');
        }

        return view('pendaftaran.akademik', compact('data'));
    }

    /** Validasi akademik, termasuk angkatan lima tahun terakhir. */
    public function simpanAkademik(Request $request): RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        $data = $this->dataDraft($request);

        if (empty($data['nama_lengkap']) || empty($data['jenis_kelamin'])) {
            return redirect()->route('pendaftaran.pribadi');
        }

        $tahunSekarang = now()->year;

        $validated = $request->validate([
            'tahun_angkatan' => [
                'required',
                'integer',
                'digits:4',
                'between:' . ($tahunSekarang - 4) . ',' . $tahunSekarang,
            ],
            'fakultas' => ['required', 'string', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'bukti_penerimaan' => [
                empty($data['bukti_penerimaan']) ? 'required' : 'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ], [
            'tahun_angkatan.required' => 'Tahun angkatan wajib dipilih.',
            'tahun_angkatan.integer' => 'Tahun angkatan tidak valid.',
            'tahun_angkatan.digits' => 'Tahun angkatan harus 4 digit.',
            'tahun_angkatan.between' => 'Pilih angkatan dari lima tahun terakhir.',
            'fakultas.required' => 'Fakultas wajib diisi.',
            'program_studi.required' => 'Program studi wajib diisi.',
            'bukti_penerimaan.required' => 'Bukti penerimaan UIN wajib diunggah.',
            'bukti_penerimaan.mimes' => 'Bukti penerimaan harus PDF, JPG, JPEG, atau PNG.',
            'bukti_penerimaan.max' => 'Ukuran bukti penerimaan maksimal 5 MB.',
        ]);

        $data = array_merge($data, [
            'tahun_angkatan' => $validated['tahun_angkatan'],
            'fakultas' => $validated['fakultas'],
            'program_studi' => $validated['program_studi'],
        ]);

        if ($request->hasFile('bukti_penerimaan')) {
            $path = $request->file('bukti_penerimaan')
                ->store('dokumen_pendaftaran', 'public');

            if ($path === false) {
                throw new \RuntimeException('Gagal mengunggah bukti penerimaan.');
            }

            // Hapus berkas lama yang hanya dipakai oleh draft ini.
            if (!empty($data['bukti_penerimaan']) && $data['bukti_penerimaan'] !== $path) {
                Storage::disk('public')->delete($data['bukti_penerimaan']);
            }

            $data['bukti_penerimaan'] = $path;
            $data['bukti_penerimaan_nama'] = $request->file('bukti_penerimaan')
                ->getClientOriginalName();
        }

        session(['pendaftaran' => $data]);

        return redirect()->route('pendaftaran.dokumen');
    }

    /** Tahap 3: Form dokumen dan pembayaran. */
    public function dokumen(Request $request): View|RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        $data = $this->dataDraft($request);

        if (empty($data['nama_lengkap']) || empty($data['jenis_kelamin'])) {
            return redirect()->route('pendaftaran.pribadi');
        }

        if (empty($data['program_studi']) || empty($data['bukti_penerimaan'])) {
            return redirect()->route('pendaftaran.akademik');
        }

        return view('pendaftaran.dokumen', compact('data'));
    }

    /** Validasi pembayaran, lalu simpan pendaftaran ke database. */
    public function simpanDokumen(Request $request): RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        $data = $this->dataDraft($request);

        if (empty($data['nama_lengkap']) || empty($data['jenis_kelamin'])) {
            return redirect()->route('pendaftaran.pribadi');
        }

        if (
            empty($data['tahun_angkatan']) ||
            empty($data['fakultas']) ||
            empty($data['program_studi']) ||
            empty($data['bukti_penerimaan'])
        ) {
            return redirect()->route('pendaftaran.akademik');
        }

        // Terima nominal 1000000, 1.000.000, atau Rp1.000.000.
        $nominal = trim((string) $request->input('nominal_pembayaran', ''));
        $nominal = preg_replace('/^Rp\s*/iu', '', $nominal);

        if (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $nominal)) {
            $nominal = str_replace('.', '', $nominal);
        }

        $request->merge(['nominal_pembayaran' => $nominal]);

        $validated = $request->validate([
            'alasan_daftar' => ['required', 'string'],
            'nominal_pembayaran' => [
                'required',
                'integer',
                'min:1000000',
                'max:9999999999999',
            ],
            'bukti_pembayaran' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
            'konfirmasi_data' => ['accepted'],
        ], [
            'alasan_daftar.required' => 'Alasan mendaftar Ma\'had wajib diisi.',
            'nominal_pembayaran.required' => 'Nominal pembayaran awal wajib diisi.',
            'nominal_pembayaran.integer' => 'Masukkan nominal angka yang valid.',
            'nominal_pembayaran.min' => 'Pembayaran awal minimal Rp1.000.000.',
            'nominal_pembayaran.max' => 'Nominal pembayaran terlalu besar.',
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.mimes' => 'Bukti pembayaran harus PDF, JPG, JPEG, atau PNG.',
            'bukti_pembayaran.max' => 'Ukuran bukti pembayaran maksimal 5 MB.',
            'konfirmasi_data.accepted' => 'Centang persetujuan kebenaran data.',
        ]);

        $pathBuktiPembayaran = $request->file('bukti_pembayaran')
            ->store('dokumen_pembayaran', 'public');

        if ($pathBuktiPembayaran === false) {
            throw new \RuntimeException('Gagal mengunggah bukti pembayaran.');
        }

        try {
            Pendaftaran::create([
                'user_id' => $request->user()->id,
                'nama_lengkap' => $data['nama_lengkap'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'no_wa' => $data['no_wa'],
                'asal_sekolah' => $data['asal_sekolah'],
                'nama_ortu' => $data['nama_ortu'],
                'no_hp_ortu' => $data['no_hp_ortu'],
                'kategori' => $data['kategori'] ?? 'umum',
                'tahun_angkatan' => $data['tahun_angkatan'],
                'fakultas' => $data['fakultas'],
                'program_studi' => $data['program_studi'],
                'bukti_penerimaan' => $data['bukti_penerimaan'],
                'alasan_daftar' => $validated['alasan_daftar'],
                'nominal_pembayaran' => $validated['nominal_pembayaran'],
                'bukti_pembayaran' => $pathBuktiPembayaran,
                'konfirmasi_data' => true,
                'status' => 'menunggu',
            ]);
        } catch (\Throwable $exception) {
            // Jangan tinggalkan file baru jika penyimpanan database gagal.
            Storage::disk('public')->delete($pathBuktiPembayaran);
            throw $exception;
        }

        session()->forget(['pendaftaran', 'pendaftaran_user_id']);
        session(['pendaftaran_selesai' => true]);

        return redirect()->route('pendaftaran.konfirmasi');
    }

    /** Tahap 4: Konfirmasi hanya jika data sudah tersimpan. */
    public function konfirmasi(Request $request): View|RedirectResponse
    {
        if (!$this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.pribadi');
        }

        // Ambil status terbaru milik pengguna yang sedang login.
        $pendaftaran = Pendaftaran::where('user_id', $request->user()->id)
            ->latest('id')
            ->firstOrFail();

        return view('pendaftaran.konfirmasi', compact('pendaftaran'));
    }

    /** Menjaga route lama tetap berfungsi. */
    public function selesai(Request $request): RedirectResponse
    {
        return redirect()->route(
            $this->sudahMendaftar($request)
                ? 'pendaftaran.konfirmasi'
                : 'pendaftaran.pribadi'
        );
    }

    // =========================================================
    // FITUR PILIH KAMAR MAHASISWA
    // =========================================================

    /**
     * Menampilkan daftar kamar untuk mahasiswa.
     */
    public function pilihKamarMhs(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->latest('id')
            ->first();

        if (!$pendaftaran) {
            return redirect()
                ->route('pendaftaran.pribadi')
                ->with('error', 'Silakan selesaikan pendaftaran terlebih dahulu.');
        }

        if ($pendaftaran->status !== 'disetujui') {
            return redirect()
                ->route('pendaftaran.konfirmasi')
                ->with('error', 'Pendaftaran kamu belum disetujui.');
        }

        if ($user->kamar_id) {
            return redirect()
                ->route('pendaftaran.konfirmasi')
                ->with('success', 'Kamu sudah memiliki penempatan kamar.');
        }

        $kamars = DB::table('kamars')
            ->orderBy('nomor_kamar')
            ->get()
            ->map(function ($kamar) {
                $pengajuanMenunggu = DB::table('pengajuan_kamars')
                    ->where('kamar_id', $kamar->id)
                    ->where('status', 'menunggu')
                    ->count();

                $kamar->tempat_tersedia = max(
                    0,
                    (int) $kamar->kapasitas
                    - (int) $kamar->terisi
                    - $pengajuanMenunggu
                );

                $kamar->penuh = $kamar->tempat_tersedia <= 0;

                return $kamar;
            });

        $pengajuanSaya = DB::table('pengajuan_kamars')
            ->where('user_id', $user->id)
            ->first();

        return view('pendaftaran.pilih_kamar', compact(
            'kamars',
            'pendaftaran',
            'pengajuanSaya'
        ));
    }

    /**
     * Menampilkan detail kamar untuk mahasiswa.
     */
    public function detailKamarMhs(
        Request $request,
        int $kamarId
    ): View|RedirectResponse {
        $user = $request->user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->latest('id')
            ->first();

        if (!$pendaftaran || $pendaftaran->status !== 'disetujui') {
            return redirect()
                ->route('pendaftaran.konfirmasi')
                ->with('error', 'Pendaftaran belum disetujui.');
        }

        if ($user->kamar_id) {
            return redirect()
                ->route('pendaftaran.konfirmasi')
                ->with('error', 'Kamu sudah memiliki penempatan kamar.');
        }

        $kamarDetail = DB::table('kamars')
            ->where('id', $kamarId)
            ->first();

        abort_if(!$kamarDetail, 404);

        $pengajuanMenunggu = DB::table('pengajuan_kamars')
            ->where('kamar_id', $kamarId)
            ->where('status', 'menunggu')
            ->count();

        $kamarDetail->tempat_tersedia = max(
            0,
            (int) $kamarDetail->kapasitas
            - (int) $kamarDetail->terisi
            - $pengajuanMenunggu
        );

        $kamarDetail->penuh = $kamarDetail->tempat_tersedia <= 0;

        $kamars = DB::table('kamars')
            ->orderBy('nomor_kamar')
            ->get()
            ->map(function ($kamar) {
                $jumlahPengajuan = DB::table('pengajuan_kamars')
                    ->where('kamar_id', $kamar->id)
                    ->where('status', 'menunggu')
                    ->count();

                $kamar->tempat_tersedia = max(
                    0,
                    (int) $kamar->kapasitas
                    - (int) $kamar->terisi
                    - $jumlahPengajuan
                );

                $kamar->penuh = $kamar->tempat_tersedia <= 0;

                return $kamar;
            });

        $pengajuanSaya = DB::table('pengajuan_kamars')
            ->where('user_id', $user->id)
            ->first();

        return view('pendaftaran.pilih_kamar', compact(
            'kamars',
            'pendaftaran',
            'pengajuanSaya',
            'kamarDetail'
        ));
    }

    /**
     * Menyimpan pengajuan pilihan kamar mahasiswa.
     */
    public function simpanPilihanKamarMhs(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kamar_id' => [
                'required',
                'integer',
                'exists:kamars,id',
            ],
        ], [
            'kamar_id.required' => 'Silakan pilih kamar terlebih dahulu.',
            'kamar_id.exists' => 'Kamar yang dipilih tidak ditemukan.',
        ]);

        $userId = $request->user()->id;

        $pendaftaran = Pendaftaran::where('user_id', $userId)
            ->latest('id')
            ->first();

        if (!$pendaftaran || $pendaftaran->status !== 'disetujui') {
            return redirect()
                ->route('pendaftaran.konfirmasi')
                ->with('error', 'Pendaftaran kamu belum disetujui.');
        }

        if ($request->user()->kamar_id) {
            return redirect()
                ->route('pendaftaran.konfirmasi')
                ->with('error', 'Kamu sudah memiliki penempatan kamar.');
        }

        try {
            DB::transaction(function () use (
                $validated,
                $userId,
                $pendaftaran
            ) {
                // Kunci baris kamar untuk mengurangi risiko
                // pengajuan bersamaan melebihi kapasitas.
                $kamar = DB::table('kamars')
                    ->where('id', $validated['kamar_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$kamar) {
                    throw new \RuntimeException('Kamar tidak ditemukan.');
                }

                // Kunci pengajuan milik mahasiswa.
                $pengajuanLama = DB::table('pengajuan_kamars')
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if (
                    $pengajuanLama &&
                    $pengajuanLama->status === 'disetujui'
                ) {
                    throw new \RuntimeException(
                        'Pengajuan kamar kamu sudah disetujui.'
                    );
                }

                // Hitung pengajuan menunggu dari mahasiswa lain.
                $pengajuanLain = DB::table('pengajuan_kamars')
                    ->where('kamar_id', $kamar->id)
                    ->where('status', 'menunggu')
                    ->where('user_id', '!=', $userId)
                    ->count();

                $tersedia = (int) $kamar->kapasitas
                    - (int) $kamar->terisi
                    - $pengajuanLain;

                if ($tersedia <= 0) {
                    throw new \RuntimeException(
                        'Maaf, kamar ini sudah penuh atau sedang dalam proses pengajuan.'
                    );
                }

                // Simpan pengajuan baru atau perbarui pengajuan sebelumnya.
                DB::table('pengajuan_kamars')->updateOrInsert(
                    ['user_id' => $userId],
                    [
                        'pendaftaran_id' => $pendaftaran->id,
                        'kamar_id' => $kamar->id,
                        'status' => 'menunggu',
                        'created_at' => $pengajuanLama
                            ? $pengajuanLama->created_at
                            : now(),
                        'updated_at' => now(),
                    ]
                );
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('pendaftaran.pilihKamarMhs')
            ->with(
                'success',
                'Pengajuan kamar berhasil disimpan. Silakan menunggu persetujuan pengelola Ma’had.'
            );
    }

    // =========================================================
    // METHOD LAMA
    // =========================================================

    /** Periksa pendaftaran milik akun yang sedang login. */
    private function sudahMendaftar(Request $request): bool
    {
        return Pendaftaran::where('user_id', $request->user()->id)->exists();
    }

    /** Hindari penggunaan draft akun lain pada sesi browser yang sama. */
    private function dataDraft(Request $request): array
    {
        $pemilik = session('pendaftaran_user_id');

        if (
            ($pemilik === null && session()->has('pendaftaran')) ||
            ($pemilik !== null && (int) $pemilik !== (int) $request->user()->id)
        ) {
            session()->forget(['pendaftaran', 'pendaftaran_user_id']);

            return [];
        }

        return session('pendaftaran', []);
    }
}
