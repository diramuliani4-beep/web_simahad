<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pendaftaran;
use App\Models\PengajuanKamar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    // =========================================================
    // TAHAP 1: DATA DIRI
    // =========================================================

    public function pribadi(Request $request): View|RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        $data = $this->dataDraft($request);

        return view('pendaftaran.data_diri', compact('data'));
    }

    public function simpanPribadi(Request $request): RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        if ($request->has('email_aktif') && ! $request->has('email')) {
            $request->merge(['email' => $request->input('email_aktif')]);
        }

        $dataLama = $this->dataDraft($request);

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
            'ijazah_sma' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'ktp' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'kk' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
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
        $validated['email_aktif'] = $validated['email'];

        foreach (['ijazah_sma', 'ktp', 'kk'] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $path = $request->file($fileKey)->store('dokumen_pendaftaran', 'public');
                if ($path) {
                    $validated[$fileKey] = $path;
                }
            } elseif (! empty($dataLama[$fileKey])) {
                $validated[$fileKey] = $dataLama[$fileKey];
            }
        }

        $data = array_merge($dataLama, $validated);

        session([
            'pendaftaran_user_id' => $request->user()->id,
            'pendaftaran' => $data,
        ]);

        return redirect()
            ->route('pendaftaran.akademik')
            ->with('success', 'Data diri berhasil disimpan.');
    }

    // =========================================================
    // TAHAP 2: DATA AKADEMIK
    // =========================================================

    public function akademik(Request $request): View|RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        $data = $this->dataDraft($request);

        if (empty($data['nama_lengkap']) || empty($data['jenis_kelamin'])) {
            return redirect()
                ->route('pendaftaran.pribadi')
                ->with('error', 'Lengkapi data diri terlebih dahulu.');
        }

        return view('pendaftaran.data_akademik', compact('data'));
    }

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
                'between:'.($tahunSekarang - 4).','.$tahunSekarang,
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
            $file = $request->file('bukti_penerimaan');

            $pathBaru = $file->store('dokumen_pendaftaran', 'public');

            if (! $pathBaru) {
                return back()->with('error', 'Gagal mengunggah bukti penerimaan.');
            }

            if (
                ! empty($data['bukti_penerimaan']) &&
                $data['bukti_penerimaan'] !== $pathBaru
            ) {
                Storage::disk('public')->delete($data['bukti_penerimaan']);
            }

            $data['bukti_penerimaan'] = $pathBaru;
            $data['bukti_penerimaan_nama'] = $file->getClientOriginalName();
        }

        session([
            'pendaftaran_user_id' => $request->user()->id,
            'pendaftaran' => $data,
        ]);

        return redirect()
            ->route('pendaftaran.persyaratan')
            ->with('success', 'Data akademik berhasil disimpan.');
    }

    // =========================================================
    // TAHAP 3: PERSYARATAN
    // =========================================================

    public function persyaratan(Request $request): View|RedirectResponse
    {
        if ($this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.konfirmasi');
        }

        $data = $this->dataDraft($request);

        if (empty($data['nama_lengkap']) || empty($data['jenis_kelamin'])) {
            return redirect()
                ->route('pendaftaran.pribadi')
                ->with('error', 'Lengkapi data diri terlebih dahulu.');
        }

        if (
            empty($data['tahun_angkatan']) ||
            empty($data['fakultas']) ||
            empty($data['program_studi']) ||
            empty($data['bukti_penerimaan'])
        ) {
            return redirect()
                ->route('pendaftaran.akademik')
                ->with('error', 'Lengkapi data akademik terlebih dahulu.');
        }

        return view('pendaftaran.persyaratan', compact('data'));
    }

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

        // Normalisasi nominal pembayaran.
        $nominal = trim((string) $request->input('nominal_pembayaran', ''));
        $nominal = preg_replace('/^rp\s*/iu', '', $nominal);
        $nominal = preg_replace('/[.\s]/u', '', $nominal);

        $request->merge([
            'nominal_pembayaran' => $nominal,
        ]);

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
            'alasan_daftar.required' => 'Alasan mendaftar Ma’had wajib diisi.',
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

        if (! $pathBuktiPembayaran) {
            return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
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
                'email_aktif' => $data['email_aktif'] ?? $data['email'] ?? null,
                'ijazah_sma' => $data['ijazah_sma'] ?? null,
                'ktp' => $data['ktp'] ?? null,
                'kk' => $data['kk'] ?? null,
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
            Storage::disk('public')->delete($pathBuktiPembayaran);
            throw $exception;
        }

        session()->forget([
            'pendaftaran',
            'pendaftaran_user_id',
        ]);

        return redirect()
            ->route('pendaftaran.konfirmasi')
            ->with('success', 'Pendaftaran berhasil dikirim.');
    }

    // =========================================================
    // TAHAP 4: KONFIRMASI
    // =========================================================

    public function konfirmasi(Request $request): View|RedirectResponse
    {
        if (! $this->sudahMendaftar($request)) {
            return redirect()->route('pendaftaran.pribadi');
        }

        $pendaftaran = Pendaftaran::where('user_id', $request->user()->id)
            ->latest('id')
            ->firstOrFail();

        return view('pendaftaran.konfirmasi', compact('pendaftaran'));
    }

    /**
     * Memproses pengiriman ulang perbaikan data atau berkas oleh mahasiswa.
     */
    public function simpanPerbaikan(Request $request): RedirectResponse
    {
        $pendaftaran = Pendaftaran::where('user_id', $request->user()->id)
            ->latest('id')
            ->firstOrFail();

        if ($pendaftaran->status !== 'perlu_perbaikan') {
            return redirect()
                ->route('pendaftaran.konfirmasi')
                ->with('error', 'Status pendaftaran Anda saat ini bukan perlu perbaikan.');
        }

        $validated = $request->validate([
            'no_wa' => ['nullable', 'string', 'max:20'],
            'asal_sekolah' => ['nullable', 'string', 'max:255'],
            'nama_ortu' => ['nullable', 'string', 'max:255'],
            'no_hp_ortu' => ['nullable', 'string', 'max:20'],
            'alasan_daftar' => ['nullable', 'string'],
            'bukti_penerimaan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'bukti_pembayaran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'ijazah_sma' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'ktp' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'kk' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'bukti_penerimaan.mimes' => 'Bukti penerimaan harus format PDF, JPG, JPEG, atau PNG.',
            'bukti_pembayaran.mimes' => 'Bukti pembayaran harus format PDF, JPG, JPEG, atau PNG.',
            'ijazah_sma.mimes' => 'Ijazah SMA harus format PDF, JPG, JPEG, atau PNG.',
            'ktp.mimes' => 'KTP harus format PDF, JPG, JPEG, atau PNG.',
            'kk.mimes' => 'KK harus format PDF, JPG, JPEG, atau PNG.',
        ]);

        $updateData = [];

        foreach (['no_wa', 'asal_sekolah', 'nama_ortu', 'no_hp_ortu', 'alasan_daftar'] as $field) {
            if ($request->filled($field)) {
                $updateData[$field] = $request->input($field);
            }
        }

        $filesToStore = [
            'bukti_penerimaan' => 'dokumen_pendaftaran',
            'bukti_pembayaran' => 'dokumen_pembayaran',
            'ijazah_sma' => 'dokumen_pendaftaran',
            'ktp' => 'dokumen_pendaftaran',
            'kk' => 'dokumen_pendaftaran',
        ];

        foreach ($filesToStore as $fileKey => $folder) {
            if ($request->hasFile($fileKey)) {
                $pathBaru = $request->file($fileKey)->store($folder, 'public');
                if ($pathBaru) {
                    if (! empty($pendaftaran->{$fileKey})) {
                        Storage::disk('public')->delete($pendaftaran->{$fileKey});
                    }
                    $updateData[$fileKey] = $pathBaru;
                }
            }
        }

        // Set status kembali menjadi 'menunggu' agar diperiksa ulang oleh Musyrifah
        $updateData['status'] = 'menunggu';

        $pendaftaran->update($updateData);

        return redirect()
            ->route('pendaftaran.konfirmasi')
            ->with('success', 'Perbaikan berkas pendaftaran berhasil dikirim. Menunggu verifikasi ulang Musyrifah.');
    }

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

    public function pilihKamarMhs(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->latest('id')
            ->first();

        if (! $pendaftaran) {
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
                ->route('kamar')
                ->with('success', 'Kamu sudah memiliki penempatan kamar. Informasi kamar dapat dilihat pada halaman Informasi Kamar.');
        }

        $kamars = $this->daftarKamarUntukMahasiswa();

        $pengajuanSaya = PengajuanKamar::with('kamar')
            ->where('user_id', $user->id)
            ->where('jenis', PengajuanKamar::JENIS_PENEMPATAN)
            ->first();

        return view('pendaftaran.pilih_kamar', compact(
            'kamars',
            'pendaftaran',
            'pengajuanSaya'
        ));
    }

    public function detailKamarMhs(
        Request $request,
        int $kamarId
    ): View|RedirectResponse {
        $user = $request->user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->latest('id')
            ->first();

        if (! $pendaftaran || $pendaftaran->status !== 'disetujui') {
            return redirect()
                ->route('pendaftaran.konfirmasi')
                ->with('error', 'Pendaftaran belum disetujui.');
        }

        if ($user->kamar_id) {
            return redirect()
                ->route('kamar')
                ->with('error', 'Kamu sudah memiliki penempatan kamar. Gunakan menu Pengajuan Pindah Kamar pada halaman Informasi Kamar.');
        }

        $kamarDetail = Kamar::query()->whereKey($kamarId)->first();

        abort_if(! $kamarDetail, 404);

        $kamarDetail->tempat_tersedia = PengajuanKamar::slotTersedia($kamarDetail);
        $kamarDetail->penuh = $kamarDetail->tempat_tersedia <= 0 || ! $kamarDetail->bisaDiajukan();

        $kamars = $this->daftarKamarUntukMahasiswa();

        $pengajuanSaya = PengajuanKamar::with('kamar')
            ->where('user_id', $user->id)
            ->where('jenis', PengajuanKamar::JENIS_PENEMPATAN)
            ->first();

        return view('pendaftaran.pilih_kamar', compact(
            'kamars',
            'pendaftaran',
            'pengajuanSaya',
            'kamarDetail'
        ));
    }

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

        $user = $request->user();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->latest('id')
            ->first();

        if (! $pendaftaran || $pendaftaran->status !== 'disetujui') {
            return redirect()
                ->route('pendaftaran.konfirmasi')
                ->with('error', 'Pendaftaran kamu belum disetujui.');
        }

        if ($user->kamar_id) {
            return redirect()
                ->route('kamar')
                ->with('error', 'Kamu sudah memiliki penempatan kamar. Gunakan menu Pengajuan Pindah Kamar pada halaman Informasi Kamar.');
        }

        try {
            PengajuanKamar::ajukan($user, Kamar::findOrFail($validated['kamar_id']));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('kamar')
            ->with(
                'success',
                'Pengajuan kamar berhasil disimpan. Silakan menunggu persetujuan pengelola Ma’had.'
            );
    }

    /**
     * Daftar kamar untuk mahasiswa dengan sisa kuota yang sama seperti
     * data kamar yang dikelola Musyrifah (penghuni + pengajuan menunggu).
     *
     * @return Collection<int, Kamar>
     */
    private function daftarKamarUntukMahasiswa(): Collection
    {
        return Kamar::query()
            ->withCount([
                'users',
                'pengajuanKamars as pengajuan_menunggu_count' => fn ($query) => $query
                    ->where('status', PengajuanKamar::STATUS_MENUNGGU),
            ])
            ->orderBy('nomor_kamar')
            ->get()
            ->each(function (Kamar $kamar): void {
                $kamar->tempat_tersedia = max(
                    0,
                    (int) $kamar->kapasitas - (int) $kamar->users_count - (int) $kamar->pengajuan_menunggu_count
                );

                $kamar->penuh = $kamar->tempat_tersedia <= 0 || ! $kamar->bisaDiajukan();
            });
    }

    // =========================================================
    // METHOD BANTU
    // =========================================================

    private function sudahMendaftar(Request $request): bool
    {
        return Pendaftaran::where('user_id', $request->user()->id)->exists();
    }

    private function dataDraft(Request $request): array
    {
        $pemilik = session('pendaftaran_user_id');

        if (
            ($pemilik === null && session()->has('pendaftaran')) ||
            ($pemilik !== null && (int) $pemilik !== (int) $request->user()->id)
        ) {
            session()->forget([
                'pendaftaran',
                'pendaftaran_user_id',
            ]);

            return [];
        }

        return session('pendaftaran', []);
    }
}
