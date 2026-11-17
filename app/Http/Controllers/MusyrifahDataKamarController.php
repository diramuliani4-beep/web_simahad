<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\KamarFoto;
use App\Models\PengajuanKamar;
use App\Models\User;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class MusyrifahDataKamarController extends Controller
{
    public function __construct(private readonly AuditTrailService $audit) {}

    /**
     * Menampilkan halaman data kamar untuk Musyrifah.
     */
    public function index(Request $request): View
    {
        $lokasi = $this->lokasiPengelola($request, false);

        // Daftar/ketersediaan semua kamar bersifat global. Identitas penghuni
        // hanya diambil dari database untuk kamar yang menjadi wilayahnya.
        // withCount memberi angka hunian tanpa memuat biodata kamar lain.
        $kamars = Kamar::query()
            ->with('fotos')
            ->withCount('users')
            ->orderBy('gedung')
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get();

        $kamarWilayah = $kamars->filter(
            fn (Kamar $kamar): bool => $this->kamarSesuaiPenempatan($lokasi, $kamar)
        );
        $kamarWilayah->load(['users' => function ($q): void {
            $q->select('id', 'name', 'nim', 'email', 'no_hp', 'kamar_id')
                ->orderBy('name');
        }]);

        // Daftar kerja hanya untuk penempatan Musyrifah yang sedang login.
        // Pindah lintas wilayah: tahap asal terlihat di wilayah asal,
        // tahap tujuan baru terlihat di wilayah tujuan setelah asal menyetujui.
        $pengajuanKamars = PengajuanKamar::with(['user', 'kamar', 'kamarAsal', 'verifikator', 'verifikatorAsal'])
            ->when($lokasi !== null, function (Builder $query) use ($lokasi): void {
                $query->where(function (Builder $q) use ($lokasi): void {
                    $q->where(function (Builder $penempatan) use ($lokasi): void {
                        $penempatan->where('jenis', PengajuanKamar::JENIS_PENEMPATAN)
                            ->whereHas('kamar', fn (Builder $k) => $this->batasiKamar($k, $lokasi));
                    })->orWhere(function (Builder $pindah) use ($lokasi): void {
                        $pindah->where('jenis', PengajuanKamar::JENIS_PINDAH)
                            ->where(function (Builder $tahap) use ($lokasi): void {
                                // Belum disetujui asal: hanya antrian Musyrifah asal.
                                $tahap->where(function (Builder $awal) use ($lokasi): void {
                                    $awal->where('status', PengajuanKamar::STATUS_MENUNGGU)
                                        ->whereNull('persetujuan_asal_oleh')
                                        ->whereHas('kamarAsal', fn (Builder $k) => $this->batasiKamar($k, $lokasi));
                                })->orWhere(function (Builder $tujuan) use ($lokasi): void {
                                    // Sudah disetujui asal: hanya antrian Musyrifah tujuan.
                                    $tujuan->where('status', PengajuanKamar::STATUS_MENUNGGU)
                                        ->whereNotNull('persetujuan_asal_oleh')
                                        ->whereHas('kamar', fn (Builder $k) => $this->batasiKamar($k, $lokasi));
                                })->orWhere(function (Builder $selesai) use ($lokasi): void {
                                    // Riwayat setelah selesai dapat dilihat pihak asal maupun tujuan.
                                    $selesai->whereIn('status', [PengajuanKamar::STATUS_DISETUJUI, PengajuanKamar::STATUS_DITOLAK])
                                        ->where(function (Builder $wilayah) use ($lokasi): void {
                                            $wilayah->whereHas('kamarAsal', fn (Builder $k) => $this->batasiKamar($k, $lokasi))
                                                ->orWhereHas('kamar', fn (Builder $k) => $this->batasiKamar($k, $lokasi));
                                        });
                                });
                            });
                    });
                });
            })
            ->latest('id')
            ->get();

        $jumlahPengajuanMenunggu = $pengajuanKamars
            ->where('status', PengajuanKamar::STATUS_MENUNGGU)
            ->count();

        // Format pengajuan kamar agar mudah ditampilkan pada blade
        $daftarPengajuan = $pengajuanKamars->map(function (PengajuanKamar $pengajuan): array {
            $sisaKamar = $pengajuan->kamar
                ? PengajuanKamar::slotTersedia($pengajuan->kamar, $pengajuan->user_id)
                : 0;

            return [
                'id' => $pengajuan->id,
                'nama' => $pengajuan->user?->name ?? 'Mahasiswa tidak ditemukan',
                'nim' => $pengajuan->user?->nim ?: '-',
                'no_hp' => $pengajuan->user?->no_hp ?: '-',
                'jenis' => $pengajuan->jenis,
                'jenis_label' => $pengajuan->labelJenis(),
                'is_pindah' => $pengajuan->jenis === PengajuanKamar::JENIS_PINDAH,
                'kamar_id' => $pengajuan->kamar_id,
                'kamar' => $pengajuan->kamar?->nomor_kamar ?? '-',
                'gedung' => $pengajuan->kamar?->gedung ?: '—',
                'lantai' => (int) ($pengajuan->kamar?->lantai ?? 0),
                'kapasitas' => (int) ($pengajuan->kamar?->kapasitas ?? 0),
                'sisa_kamar' => $sisaKamar,
                'kamar_asal' => $pengajuan->kamarAsal?->nomor_kamar ?? '-',
                'kamar_asal_gedung' => $pengajuan->kamarAsal?->gedung ?: '—',
                'kamar_asal_lantai' => (int) ($pengajuan->kamarAsal?->lantai ?? 0),
                'alasan' => $pengajuan->alasan,
                'catatan' => $pengajuan->catatan,
                'status' => $pengajuan->status,
                'status_label' => $pengajuan->labelStatus(),
                'persetujuan_asal_oleh' => $pengajuan->verifikatorAsal?->name,
                'persetujuan_asal_pada' => optional($pengajuan->persetujuan_asal_pada)->translatedFormat('d M Y H:i'),
                'tahap_persetujuan' => $pengajuan->menungguPersetujuanTujuan() ? 'tujuan' : 'asal',
                'lintas_wilayah' => $pengajuan->kamarAsal instanceof Kamar
                    && $pengajuan->kamar instanceof Kamar
                    && ! $this->lokasiSama($pengajuan->kamarAsal, $pengajuan->kamar),
                'diverifikasi_oleh' => $pengajuan->verifikator?->name,
                'diajukan_pada' => optional($pengajuan->created_at)->translatedFormat('d M Y H:i') ?? '-',
                'diproses_pada' => $pengajuan->status === PengajuanKamar::STATUS_MENUNGGU
                    ? '-'
                    : optional($pengajuan->updated_at)->translatedFormat('d M Y H:i'),
            ];
        })->all();

        // Sebaran pengajuan menunggu per kamar agar angka pada kartu kamar konsisten
        $pengajuanMenungguPerKamar = $pengajuanKamars
            ->where('status', PengajuanKamar::STATUS_MENUNGGU)
            ->groupBy('kamar_id')
            ->map(fn ($items) => $items->count());

        // Rekapitulasi data kamar
        $jumlahKamar = $kamars->count();
        $totalKapasitas = (int) $kamars->sum('kapasitas');
        $kamarTerisi = (int) $kamars->sum('users_count');
        $kamarTersedia = $kamars->filter(fn ($k) => in_array($k->status, ['Tersedia', 'tersedia']) && ($k->kapasitas - (int) $k->users_count) > 0)->count();
        $kamarPenuh = $kamars->filter(fn ($k) => in_array($k->status, ['Penuh', 'penuh']) || ($k->kapasitas - (int) $k->users_count) <= 0)->count();
        $sisaTempatTidur = max(0, $totalKapasitas - $kamarTerisi);

        // Format data pendaftaran kamar untuk tampilan blade
        $daftarKamar = $kamars->map(function (Kamar $kamar) use ($pengajuanMenungguPerKamar, $lokasi): array {
            $penghuniCount = (int) $kamar->users_count;
            $bolehKelola = $this->kamarSesuaiPenempatan($lokasi, $kamar);
            $sisa = max(0, (int) $kamar->kapasitas - $penghuniCount);
            $pengajuanMenunggu = (int) ($pengajuanMenungguPerKamar[$kamar->id] ?? 0);

            return [
                'id' => $kamar->id,
                // Blade dapat menyembunyikan tombol edit/hapus/foto di luar wilayah Admin.
                'boleh_kelola' => $bolehKelola,
                'boleh_lihat_penghuni' => $bolehKelola,
                'nama' => $kamar->nama_kamar,
                'nomor' => $kamar->nomor_kamar,
                'gedung' => $kamar->gedung ?: '—',
                'lantai' => (int) $kamar->lantai,
                'kapasitas' => (int) $kamar->kapasitas,
                'terisi' => $penghuniCount,
                'sisa' => $sisa,
                'pengajuan_menunggu' => $pengajuanMenunggu,
                'sisa_setelah_pengajuan' => max(0, $sisa - $pengajuanMenunggu),
                'status' => $kamar->status ?: ($sisa <= 0 ? 'Penuh' : 'Tersedia'),
                'fasilitas' => $kamar->fasilitas,
                'foto' => $kamar->foto,
                'foto_url' => $kamar->urlFotoUtama(),
                'fotos' => $kamar->fotos->map(fn (KamarFoto $foto) => [
                    'id' => $foto->id,
                    'url' => $foto->url,
                    'urutan' => (int) $foto->urutan,
                ])->values()->all(),
                'maks_foto' => Kamar::MAKS_FOTO,
                'sisa_slot_foto' => max(0, Kamar::MAKS_FOTO - $kamar->fotos->count()),
                // Jumlah penghuni boleh dilihat lintas wilayah; biodata detail
                // hanya untuk kamar yang menjadi tanggung jawab Musyrifah.
                'penghuni' => $bolehKelola
                    ? $kamar->users->map(fn ($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'nim' => $u->nim ?: '-',
                        'email' => $u->email,
                        'no_hp' => $u->no_hp ?: '-',
                    ])->all()
                    : [],
            ];
        })->all();

        return view('musyrifah.data_kamar', compact(
            'jumlahKamar',
            'kamarTerisi',
            'kamarTersedia',
            'kamarPenuh',
            'totalKapasitas',
            'sisaTempatTidur',
            'daftarKamar',
            'daftarPengajuan',
            'jumlahPengajuanMenunggu'
        ));
    }

    /**
     * Menyimpan data kamar baru beserta unggahan foto kamar.
     */
    public function store(Request $request): RedirectResponse
    {
        // Semua Musyrifah aktif boleh mendata gedung/lantai/kamar baru.
        // Penempatan oleh Admin tetap menjadi syarat untuk mengelola penghuni,
        // mengedit kamar, atau memvalidasi pengajuan.
        $this->lokasiPengelola($request, false);
        $penempatan = $this->validasiLokasiKamar($request, null);

        $request->merge(['nomor_kamar' => trim((string) $request->input('nomor_kamar'))]);
        $validated = $request->validate([
            // Nomor kamar boleh sama jika gedung/lantainya berbeda.
            'nomor_kamar' => [
                'required', 'string', 'max:50',
                Rule::unique('kamars', 'nomor_kamar')
                    ->where(fn ($q) => $q->where('gedung', $penempatan['gedung'])->where('lantai', $penempatan['lantai'])),
            ],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['required', 'string', Rule::in(['Tersedia', 'Penuh', 'Dalam Perbaikan', 'Tidak Tersedia'])],
            'fasilitas' => ['nullable', 'string', 'max:2000'],
        ], [
            'nomor_kamar.required' => 'Nomor kamar wajib diisi.',
            'nomor_kamar.unique' => 'Nomor kamar sudah terdaftar.',
            'lantai.required' => 'Lantai kamar wajib diisi.',
            'kapasitas.required' => 'Kapasitas kamar wajib diisi.',
        ]);

        // Mendukung unggahan satu foto (foto) maupun beberapa foto (foto[]) sekaligus.
        $berkasFoto = $this->kumpulkanBerkasFoto($request->file('foto'));
        $this->validasiBerkasFoto($berkasFoto, Kamar::MAKS_FOTO);

        $kamar = Kamar::create([
            'nama_kamar' => 'Kamar '.trim($validated['nomor_kamar']),
            'nomor_kamar' => trim($validated['nomor_kamar']),
            'gedung' => $penempatan['gedung'],
            'lantai' => $penempatan['lantai'],
            'kapasitas' => (int) $validated['kapasitas'],
            'terisi' => 0,
            'status' => $validated['status'],
            'fasilitas' => filled($validated['fasilitas'] ?? null) ? trim($validated['fasilitas']) : null,
        ]);

        $this->simpanBerkasFoto($kamar, $berkasFoto);

        $this->audit->catat(
            'kamar',
            'kamar_dibuat',
            'Kamar '.$kamar->nomor_kamar,
            (int) $kamar->getKey(),
            'Kamar baru ditambahkan pada lantai '.$kamar->lantai.'.',
            $request->user(),
            [],
            ['nomor_kamar' => $kamar->nomor_kamar, 'lantai' => $kamar->lantai, 'kapasitas' => $kamar->kapasitas, 'status' => $kamar->status],
        );

        return redirect()
            ->route('musyrifah.data_kamar')
            ->with('success', 'Data kamar berhasil ditambahkan.');
    }

    /**
     * Memperbarui informasi kamar dan foto kamar.
     */
    public function update(Request $request, Kamar $kamar): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesKamar($lokasi, $kamar);
        $penempatan = $this->validasiLokasiKamar($request, $lokasi);

        $sebelum = $kamar->getAttributes();

        $request->merge(['nomor_kamar' => trim((string) $request->input('nomor_kamar'))]);
        $validated = $request->validate([
            'nomor_kamar' => [
                'required', 'string', 'max:50',
                Rule::unique('kamars', 'nomor_kamar')
                    ->where(fn ($q) => $q->where('gedung', $penempatan['gedung'])->where('lantai', $penempatan['lantai']))
                    ->ignore($kamar->id),
            ],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['required', 'string', Rule::in(['Tersedia', 'Penuh', 'Dalam Perbaikan', 'Tidak Tersedia'])],
            'fasilitas' => ['nullable', 'string', 'max:2000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'nomor_kamar.required' => 'Nomor kamar wajib diisi.',
            'nomor_kamar.unique' => 'Nomor kamar sudah terdaftar.',
            'lantai.required' => 'Lantai kamar wajib diisi.',
            'kapasitas.required' => 'Kapasitas kamar wajib diisi.',
            'foto.image' => 'File harus berupa gambar (JPG, JPEG, PNG, WEBP).',
            'foto.max' => 'Ukuran foto kamar maksimal 5 MB.',
        ]);

        $data = [
            'nama_kamar' => 'Kamar '.trim($validated['nomor_kamar']),
            'nomor_kamar' => trim($validated['nomor_kamar']),
            'gedung' => $penempatan['gedung'],
            'lantai' => $penempatan['lantai'],
            'kapasitas' => (int) $validated['kapasitas'],
            'status' => $validated['status'],
            'fasilitas' => filled($validated['fasilitas'] ?? null) ? trim($validated['fasilitas']) : null,
        ];

        if ($request->hasFile('foto')) {
            $pathBaru = $request->file('foto')->store('foto_kamar', 'public');
            $pathLama = $kamar->pathFotoUtama();
            $fotoUtama = $kamar->fotoUtama();

            if ($fotoUtama) {
                $fotoUtama->update(['path' => $pathBaru]);
            } else {
                KamarFoto::create([
                    'kamar_id' => $kamar->id,
                    'path' => $pathBaru,
                    'urutan' => 1,
                ]);
            }

            $kamar->sinkronkanFotoUtama();

            if ($pathLama && $pathLama !== $pathBaru && Storage::disk('public')->exists($pathLama)) {
                Storage::disk('public')->delete($pathLama);
            }
        }

        $kamar->update($data);

        $this->audit->catat(
            'kamar',
            'kamar_diperbarui',
            'Kamar '.$kamar->nomor_kamar,
            (int) $kamar->getKey(),
            'Data kamar diperbarui.',
            $request->user(),
            $sebelum,
            $kamar->getAttributes(),
        );

        return redirect()
            ->route('musyrifah.data_kamar')
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    /**
     * Menambahkan satu atau beberapa foto baru ke galeri kamar.
     */
    public function tambahFoto(Request $request, Kamar $kamar): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesKamar($lokasi, $kamar);

        $sisaSlot = max(0, Kamar::MAKS_FOTO - $kamar->fotos()->count());

        if ($sisaSlot <= 0) {
            return $this->kembaliKeKamar($kamar, 'error', 'Kamar ini sudah memiliki '.Kamar::MAKS_FOTO.' foto. Hapus salah satu foto terlebih dahulu untuk menambah foto baru.');
        }

        $berkasFoto = $this->kumpulkanBerkasFoto($request->file('fotos') ?? $request->file('foto'));

        if ($berkasFoto === []) {
            return $this->kembaliKeKamar($kamar, 'error', 'Silakan pilih foto yang ingin diunggah.');
        }

        $this->validasiBerkasFoto($berkasFoto, $sisaSlot);
        $this->simpanBerkasFoto($kamar, $berkasFoto);

        return $this->kembaliKeKamar($kamar, 'success', 'Foto kamar berhasil ditambahkan.');
    }

    /**
     * Mengganti berkas salah satu foto tanpa mengubah urutannya pada galeri.
     */
    public function gantiFoto(Request $request, Kamar $kamar, KamarFoto $foto): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesKamar($lokasi, $kamar);
        $this->pastikanFotoMilikKamar($kamar, $foto);

        $berkasFoto = $this->kumpulkanBerkasFoto($request->file('foto'));

        if ($berkasFoto === []) {
            return $this->kembaliKeKamar($kamar, 'error', 'Silakan pilih foto pengganti.');
        }

        $this->validasiBerkasFoto($berkasFoto, 1);

        $pathLama = $foto->path;
        $pathBaru = $berkasFoto[0]->store('foto_kamar', 'public');

        $foto->update(['path' => $pathBaru]);
        $kamar->sinkronkanFotoUtama();

        if ($pathLama && $pathLama !== $pathBaru && Storage::disk('public')->exists($pathLama)) {
            Storage::disk('public')->delete($pathLama);
        }

        return $this->kembaliKeKamar($kamar, 'success', 'Foto kamar berhasil diganti.');
    }

    /**
     * Menghapus salah satu foto kamar beserta berkasnya.
     */
    public function hapusFoto(Request $request, Kamar $kamar, KamarFoto $foto): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesKamar($lokasi, $kamar);
        $this->pastikanFotoMilikKamar($kamar, $foto);

        $path = $foto->path;
        $foto->delete();

        // Urutan dirapikan kembali agar foto berikutnya menjadi foto utama.
        $kamar->fotos()->get()->values()->each(
            fn (KamarFoto $item, int $index) => $item->update(['urutan' => $index + 1])
        );

        $kamar->sinkronkanFotoUtama();

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return $this->kembaliKeKamar($kamar, 'success', 'Foto kamar berhasil dihapus.');
    }

    /**
     * Menghapus kamar (hanya diperbolehkan jika belum ada mahasiswa yang menempati).
     */
    public function destroy(Request $request, Kamar $kamar): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $this->pastikanAksesKamar($lokasi, $kamar);

        if ($kamar->users()->exists() || $kamar->terisi > 0) {
            return back()->with('error', 'Kamar tidak dapat dihapus karena masih memiliki penghuni mahasiswa.');
        }

        // Hapus seluruh berkas foto galeri kamar sebelum data kamar dihapus.
        $kamar->fotos()->get()->each(function (KamarFoto $foto): void {
            if ($foto->path && Storage::disk('public')->exists($foto->path)) {
                Storage::disk('public')->delete($foto->path);
            }
        });

        if ($kamar->foto && Storage::disk('public')->exists($kamar->foto)) {
            Storage::disk('public')->delete($kamar->foto);
        }

        $kamar->delete();

        $this->audit->catat(
            'kamar',
            'kamar_dihapus',
            'Kamar '.$kamar->nomor_kamar,
            (int) $kamar->getKey(),
            'Kamar dihapus.',
            $request->user(),
            ['nomor_kamar' => $kamar->nomor_kamar, 'lantai' => $kamar->lantai, 'kapasitas' => $kamar->kapasitas],
            [],
        );

        return redirect()
            ->route('musyrifah.data_kamar')
            ->with('success', 'Kamar berhasil dihapus.');
    }

    /**
     * Penempatan pertama: satu verifikasi Musyrifah tujuan.
     * Pindah wilayah yang sama: satu verifikasi Musyrifah wilayah itu.
     * Pindah lintas wilayah: Musyrifah asal dahulu, lalu Musyrifah tujuan.
     * Tidak ada perpindahan fisik sebelum persetujuan final dari tujuan.
     */
    public function approvePengajuan(Request $request, PengajuanKamar $pengajuan): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $tahap = 'final';
        $pindahKamar = $pengajuan->jenis === PengajuanKamar::JENIS_PINDAH;

        try {
            DB::transaction(function () use ($pengajuan, $request, $lokasi, &$tahap, $pindahKamar): void {
                $permohonan = PengajuanKamar::query()
                    ->lockForUpdate()->findOrFail($pengajuan->getKey());

                if ($permohonan->status !== PengajuanKamar::STATUS_MENUNGGU) {
                    throw new \RuntimeException('Pengajuan sudah diproses sebelumnya.');
                }

                $kamar = Kamar::query()->lockForUpdate()->findOrFail($permohonan->kamar_id);
                $santri = User::query()->lockForUpdate()->findOrFail($permohonan->user_id);
                $kamarAsalId = $santri->kamar_id;

                if (! $pindahKamar && $kamarAsalId) {
                    throw new \RuntimeException('Mahasiswa sudah memiliki kamar. Gunakan pengajuan pindah kamar.');
                }

                if ($pindahKamar) {
                    if (! $kamarAsalId || (int) $permohonan->kamar_asal_id !== (int) $kamarAsalId) {
                        throw new \RuntimeException('Kamar asal mahasiswa sudah berubah. Pengajuan harus diperiksa kembali.');
                    }
                    $kamarAsal = Kamar::query()->lockForUpdate()->findOrFail($kamarAsalId);
                    if ((int) $kamarAsal->getKey() === (int) $kamar->getKey()) {
                        throw new \RuntimeException('Kamar asal dan tujuan tidak boleh sama.');
                    }

                    if (! $this->lokasiSama($kamarAsal, $kamar)) {
                        if ($permohonan->persetujuan_asal_oleh === null) {
                            // Tahap 1: akses hanya Musyrifah asal.
                            $this->pastikanAksesKamar($lokasi, $kamarAsal);
                            if (! $kamar->bisaDiajukan() || PengajuanKamar::slotTersedia($kamar, $santri->id) <= 0) {
                                throw new \RuntimeException('Kamar tujuan tidak tersedia atau kuotanya sudah penuh.');
                            }
                            $permohonan->update([
                                'persetujuan_asal_oleh' => $request->user()?->id,
                                'persetujuan_asal_pada' => now(),
                                'catatan' => null,
                            ]);
                            $tahap = 'asal';
                            return; // Mahasiswa tetap di kamar asal.
                        }

                        // Tahap 2: akses hanya Musyrifah tujuan; tidak boleh
                        // memakai akun yang sama untuk menyetujui dua tahap.
                        $this->pastikanAksesKamar($lokasi, $kamar);
                        abort_if((int) $permohonan->persetujuan_asal_oleh === (int) $request->user()?->id, 403,
                            'Persetujuan tujuan harus dilakukan oleh Musyrifah yang berbeda.');
                    } else {
                        // Pindah kamar pada lantai sama: cukup satu Musyrifah.
                        $this->pastikanAksesKamar($lokasi, $kamarAsal);
                        $this->pastikanAksesKamar($lokasi, $kamar);
                        abort_if($permohonan->persetujuan_asal_oleh !== null, 409,
                            'Pengajuan memiliki persetujuan asal sebelumnya, periksa kembali lokasi kamar.');
                    }
                } else {
                    $this->pastikanAksesKamar($lokasi, $kamar);
                }

                if (! $kamar->bisaDiajukan()) {
                    throw new \RuntimeException('Kamar tujuan tidak tersedia untuk penempatan.');
                }

                $jumlahPenghuni = User::query()->where('kamar_id', $kamar->id)->count();
                if ($jumlahPenghuni >= (int) $kamar->kapasitas
                    || PengajuanKamar::slotTersedia($kamar, $santri->id) <= 0) {
                    throw new \RuntimeException('Kamar tujuan tidak memiliki kuota yang cukup.');
                }

                // Hanya langkah terakhir yang menetapkan kamar baru.
                $permohonan->update([
                    'status' => PengajuanKamar::STATUS_DISETUJUI,
                    'kamar_asal_id' => $permohonan->kamar_asal_id ?: $kamarAsalId,
                    'catatan' => null,
                    'diverifikasi_oleh' => $request->user()?->id,
                ]);
                $santri->update(['kamar_id' => $kamar->id]);

                collect([$kamar->id, $kamarAsalId, $permohonan->kamar_asal_id])
                    ->filter()->unique()->each(function ($kamarId): void {
                        Kamar::query()->find($kamarId)?->sinkronkanOkupansi();
                    });

                // Pengajuan pindah lain menjadi tidak relevan setelah pemindahan.
                PengajuanKamar::query()
                    ->where('user_id', $santri->id)
                    ->whereKeyNot($permohonan->id)
                    ->where('jenis', PengajuanKamar::JENIS_PINDAH)
                    ->where('status', PengajuanKamar::STATUS_MENUNGGU)
                    ->update([
                        'status' => PengajuanKamar::STATUS_DITOLAK,
                        'catatan' => 'Dibatalkan otomatis karena penempatan kamar sudah diperbarui.',
                        'diverifikasi_oleh' => $request->user()?->id,
                    ]);
            });
        } catch (HttpExceptionInterface $e) {
            throw $e; // Pelanggaran izin harus tetap HTTP 403/409.
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        $pengajuan->refresh()->load(['kamar', 'kamarAsal', 'user']);

        if ($tahap === 'asal') {
            $this->audit->catat(
                'kamar', 'persetujuan_pindah_asal', 'Pengajuan kamar '.$pengajuan->user?->name,
                (int) $pengajuan->getKey(),
                'Musyrifah asal menyetujui perpindahan. Menunggu persetujuan Musyrifah tujuan.',
                $request->user(),
                ['persetujuan_asal_oleh' => null],
                ['persetujuan_asal_oleh' => $pengajuan->persetujuan_asal_oleh],
            );

            return redirect()->route('musyrifah.data_kamar')
                ->with('success', 'Persetujuan keluar berhasil. Pengajuan diteruskan ke Musyrifah tujuan; mahasiswa masih di kamar asal.');
        }

        $this->audit->catat(
            'kamar',
            $pindahKamar ? 'pindah_kamar' : 'penempatan_kamar',
            'Pengajuan kamar '.$pengajuan->user?->name,
            (int) $pengajuan->getKey(),
            $pindahKamar
                ? 'Perpindahan kamar disetujui dari '.($pengajuan->kamarAsal?->nomor_kamar ?? '-').' ke '.($pengajuan->kamar?->nomor_kamar ?? '-').'.'
                : 'Penempatan kamar '.($pengajuan->kamar?->nomor_kamar ?? '-').' disetujui.',
            $request->user(),
            ['status' => PengajuanKamar::STATUS_MENUNGGU],
            ['status' => PengajuanKamar::STATUS_DISETUJUI],
        );

        return redirect()->route('musyrifah.data_kamar')
            ->with('success', $pindahKamar
                ? 'Persetujuan akhir berhasil. Mahasiswa sudah dipindahkan ke kamar tujuan.'
                : 'Penempatan mahasiswa pada kamar berhasil disetujui.');
    }

    /**
     * Menolak pengajuan kamar. Penempatan mahasiswa tidak diubah, sehingga
     * mahasiswa pindah kamar tetap menempati kamar lamanya.
     */
    public function rejectPengajuan(Request $request, PengajuanKamar $pengajuan): RedirectResponse
    {
        $lokasi = $this->lokasiPengelola($request);
        $validated = $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
        ], ['catatan.max' => 'Catatan penolakan maksimal 500 karakter.']);
        $catatan = filled($validated['catatan'] ?? null)
            ? trim($validated['catatan']) : 'Pengajuan kamar ditolak oleh Musyrifah.';

        DB::transaction(function () use ($pengajuan, $catatan, $request, $lokasi): void {
            $permohonan = PengajuanKamar::query()->lockForUpdate()->findOrFail($pengajuan->getKey());
            if ($permohonan->status !== PengajuanKamar::STATUS_MENUNGGU) {
                throw ValidationException::withMessages(['pengajuan' => 'Pengajuan sudah diproses sebelumnya.']);
            }

            $permohonan->load(['kamar', 'kamarAsal', 'user.kamar']);
            if ($permohonan->jenis === PengajuanKamar::JENIS_PINDAH) {
                // Penolakan awal di wilayah asal; penolakan setelah asal
                // menyetujui hanya di wilayah tujuan.
                $kamarBerwenang = $permohonan->persetujuan_asal_oleh === null
                    ? ($permohonan->kamarAsal ?: $permohonan->user?->kamar)
                    : $permohonan->kamar;
            } else {
                $kamarBerwenang = $permohonan->kamar;
            }
            abort_unless($kamarBerwenang instanceof Kamar, 403, 'Kamar pengajuan tidak ditemukan.');
            $this->pastikanAksesKamar($lokasi, $kamarBerwenang);
            $permohonan->update([
                'status' => PengajuanKamar::STATUS_DITOLAK,
                'catatan' => $catatan,
                'diverifikasi_oleh' => $request->user()?->id,
            ]);
        });

        $this->audit->catat(
            'kamar', 'pengajuan_kamar_ditolak', 'Pengajuan kamar '.$pengajuan->user?->name,
            (int) $pengajuan->getKey(),
            'Pengajuan kamar ditolak. Catatan: '.$catatan,
            $request->user(),
            ['status' => PengajuanKamar::STATUS_MENUNGGU],
            ['status' => PengajuanKamar::STATUS_DITOLAK, 'catatan' => $catatan],
        );

        return redirect()->route('musyrifah.data_kamar')
            ->with('success', 'Pengajuan ditolak. Mahasiswa tetap menempati kamar asal.');
    }

    /**
     * Memastikan foto benar-benar milik kamar yang sedang dikelola.
     */
    private function pastikanFotoMilikKamar(Kamar $kamar, KamarFoto $foto): void
    {
        abort_unless((int) $foto->kamar_id === (int) $kamar->id, 404);
    }

    /**
     * Mengumpulkan unggahan foto dari satu berkas tunggal maupun banyak berkas.
     *
     * @return array<int, UploadedFile>
     */
    private function kumpulkanBerkasFoto(mixed $berkas): array
    {
        if ($berkas instanceof UploadedFile) {
            return [$berkas];
        }

        if (! is_array($berkas)) {
            return [];
        }

        return array_values(array_filter($berkas, fn ($item) => $item instanceof UploadedFile));
    }

    /**
     * Memvalidasi berkas foto kamar beserta batas jumlahnya.
     *
     * @param  array<int, UploadedFile>  $berkasFoto
     */
    private function validasiBerkasFoto(array $berkasFoto, int $maksimal): void
    {
        Validator::make(
            ['foto' => $berkasFoto],
            [
                'foto' => ['array', 'max:'.$maksimal],
                'foto.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ],
            [
                'foto.max' => 'Jumlah foto yang diunggah melebihi batas. Maksimal '.$maksimal.' foto.',
                'foto.*.image' => 'File harus berupa gambar (JPG, JPEG, PNG, WEBP).',
                'foto.*.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
                'foto.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
            ]
        )->validate();
    }

    /**
     * Menyimpan berkas foto ke storage publik dan mencatatnya pada galeri kamar.
     *
     * @param  array<int, UploadedFile>  $berkasFoto
     */
    private function simpanBerkasFoto(Kamar $kamar, array $berkasFoto): void
    {
        $urutan = (int) $kamar->fotos()->max('urutan');

        // Foto lama pada kolom kamars.foto dipertahankan sebagai foto utama
        // bila galeri belum memiliki foto sama sekali.
        if ($urutan === 0 && filled($kamar->foto) && Storage::disk('public')->exists($kamar->foto)) {
            KamarFoto::create([
                'kamar_id' => $kamar->id,
                'path' => $kamar->foto,
                'urutan' => 1,
            ]);

            $urutan = 1;
        }

        foreach ($berkasFoto as $berkas) {
            $urutan++;

            KamarFoto::create([
                'kamar_id' => $kamar->id,
                'path' => $berkas->store('foto_kamar', 'public'),
                'urutan' => $urutan,
            ]);
        }

        $kamar->sinkronkanFotoUtama();
    }

    /**
     * Kembali ke halaman data kamar dengan menyorot kamar terkait.
     */
    private function kembaliKeKamar(Kamar $kamar, string $tipe, string $pesan): RedirectResponse
    {
        return redirect()
            ->to(route('musyrifah.data_kamar').'#kamar-'.$kamar->id)
            ->with($tipe, $pesan);
    }

    /**
     * Lokasi penempatan hanya berasal dari akun yang diatur Admin.
     * null berarti Admin (akses penuh).
     * Bila $wajibPenempatan=false, Musyrifah aktif yang belum ditempatkan
     * tetap dapat melihat ketersediaan dan membuat data kamar, tetapi tidak
     * memperoleh hak kelola/validasi apa pun (lokasi kosong tidak cocok).
     *
     * @return array{gedung: string, lantai: int}|null
     */
    private function lokasiPengelola(Request $request, bool $wajibPenempatan = true): ?array
    {
        $akun = $request->user();
        $role = strtolower(trim((string) $akun?->role));
        abort_unless(in_array($role, ['admin', 'musyrifah'], true), 403);

        if ($role === 'admin') {
            return null;
        }

        abort_unless(strtolower(trim((string) $akun->status)) === 'aktif', 403, 'Akun Musyrifah belum aktif.');
        $gedung = trim((string) $akun->gedung_penempatan);
        $lantai = filter_var($akun->lantai_penempatan, FILTER_VALIDATE_INT);

        if ($gedung === '' || $lantai === false || $lantai < 1) {
            abort_if($wajibPenempatan, 403, 'Penempatan gedung dan lantai belum ditentukan oleh Admin.');

            return ['gedung' => '', 'lantai' => 0];
        }

        return ['gedung' => $gedung, 'lantai' => (int) $lantai];
    }

    /** @param array{gedung: string, lantai: int} $lokasi */
    private function batasiKamar(Builder $query, array $lokasi): Builder
    {
        return $query->where('gedung', $lokasi['gedung'])->where('lantai', $lokasi['lantai']);
    }

    /** @param array{gedung: string, lantai: int}|null $lokasi */
    private function pastikanAksesKamar(?array $lokasi, Kamar $kamar): void
    {
        if ($lokasi === null) {
            return;
        }

        abort_unless(
            $this->kamarSesuaiPenempatan($lokasi, $kamar),
            403,
            'Kamar berada di luar gedung/lantai penempatan Musyrifah.'
        );
    }

    /**
     * Digunakan untuk menentukan tombol pengelolaan pada Blade.
     * Admin dapat mengelola seluruh lokasi; Musyrifah hanya penempatannya.
     *
     * @param array{gedung: string, lantai: int}|null $lokasi
     */
    private function kamarSesuaiPenempatan(?array $lokasi, Kamar $kamar): bool
    {
        return $lokasi === null || (
            mb_strtolower(trim((string) $kamar->gedung)) === mb_strtolower(trim($lokasi['gedung']))
                && (int) $kamar->lantai === (int) $lokasi['lantai']
        );
    }

    /**
     * Validasi gedung/lantai tanpa fallback diam-diam ke Gedung A.
     * Pada pembuatan kamar (store), null mengizinkan gedung/lantai mana saja.
     * Pada pengubahan (update), lokasi Admin tetap membatasi perubahan lokasi.
     *
     * @param array{gedung: string, lantai: int}|null $lokasi
     * @return array{gedung: string, lantai: int}
     */
    private function validasiLokasiKamar(Request $request, ?array $lokasi): array
    {
        $input = $request->validate([
            'gedung' => ['required', 'string', 'max:150'],
            'lantai' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $gedung = trim($input['gedung']);
        $lantai = (int) $input['lantai'];
        if ($gedung === '') {
            throw ValidationException::withMessages(['gedung' => 'Nama gedung wajib diisi.']);
        }

        if ($lokasi !== null && (
            mb_strtolower($gedung) !== mb_strtolower($lokasi['gedung'])
                || $lantai !== $lokasi['lantai']
        )) {
            throw ValidationException::withMessages([
                'gedung' => 'Gedung dan lantai harus sesuai penempatan yang ditentukan Admin: '.$lokasi['gedung'].', Lantai '.$lokasi['lantai'].'.',
            ]);
        }

        return $lokasi ?? ['gedung' => $gedung, 'lantai' => $lantai];
    }

    private function lokasiSama(Kamar $asal, Kamar $tujuan): bool
    {
        return mb_strtolower(trim((string) $asal->gedung)) === mb_strtolower(trim((string) $tujuan->gedung))
            && (int) $asal->lantai === (int) $tujuan->lantai;
    }
}
