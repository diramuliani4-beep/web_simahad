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
use Illuminate\View\View;

class MusyrifahDataKamarController extends Controller
{
    public function __construct(private readonly AuditTrailService $audit) {}

    /**
     * Menampilkan halaman data kamar untuk Musyrifah.
     */
    public function index(Request $request): View
    {
        $this->pastikanPengelola($request);

        // Ambil data kamar asli dari database beserta penghuni mahasiswa
        $kamars = Kamar::with(['fotos', 'users' => function ($q) {
            $q->select('id', 'name', 'nim', 'email', 'no_hp', 'kamar_id')
                ->orderBy('name');
        }])
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get();

        // Pengajuan penempatan kamar dan pengajuan pindah kamar oleh santri
        $pengajuanKamars = PengajuanKamar::with(['user', 'kamar', 'kamarAsal', 'verifikator'])
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
                'gedung' => $pengajuan->kamar?->gedung ?: 'Gedung A',
                'lantai' => (int) ($pengajuan->kamar?->lantai ?? 0),
                'kapasitas' => (int) ($pengajuan->kamar?->kapasitas ?? 0),
                'sisa_kamar' => $sisaKamar,
                'kamar_asal' => $pengajuan->kamarAsal?->nomor_kamar ?? '-',
                'kamar_asal_gedung' => $pengajuan->kamarAsal?->gedung ?: 'Gedung A',
                'kamar_asal_lantai' => (int) ($pengajuan->kamarAsal?->lantai ?? 0),
                'alasan' => $pengajuan->alasan,
                'catatan' => $pengajuan->catatan,
                'status' => $pengajuan->status,
                'status_label' => $pengajuan->labelStatus(),
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
        $kamarTerisi = (int) $kamars->sum(fn ($k) => $k->users->count());
        $kamarTersedia = $kamars->filter(fn ($k) => in_array($k->status, ['Tersedia', 'tersedia']) && ($k->kapasitas - $k->users->count()) > 0)->count();
        $kamarPenuh = $kamars->filter(fn ($k) => in_array($k->status, ['Penuh', 'penuh']) || ($k->kapasitas - $k->users->count()) <= 0)->count();
        $sisaTempatTidur = max(0, $totalKapasitas - $kamarTerisi);

        // Format data pendaftaran kamar untuk tampilan blade
        $daftarKamar = $kamars->map(function (Kamar $kamar) use ($pengajuanMenungguPerKamar): array {
            $penghuniCount = $kamar->users->count();
            $sisa = max(0, (int) $kamar->kapasitas - $penghuniCount);
            $pengajuanMenunggu = (int) ($pengajuanMenungguPerKamar[$kamar->id] ?? 0);

            return [
                'id' => $kamar->id,
                'nama' => $kamar->nama_kamar,
                'nomor' => $kamar->nomor_kamar,
                'gedung' => $kamar->gedung ?: 'Gedung A',
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
                'penghuni' => $kamar->users->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'nim' => $u->nim ?: '-',
                    'email' => $u->email,
                    'no_hp' => $u->no_hp ?: '-',
                ])->all(),
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
        $this->pastikanPengelola($request);

        $validated = $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:50', 'unique:kamars,nomor_kamar'],
            'gedung' => ['nullable', 'string', 'max:100'],
            'lantai' => ['required', 'integer', 'min:1', 'max:20'],
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
            'gedung' => trim($validated['gedung'] ?? '') ?: 'Gedung A',
            'lantai' => (int) $validated['lantai'],
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
        $this->pastikanPengelola($request);

        $sebelum = $kamar->getAttributes();

        $validated = $request->validate([
            'nomor_kamar' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kamars', 'nomor_kamar')->ignore($kamar->id),
            ],
            'gedung' => ['nullable', 'string', 'max:100'],
            'lantai' => ['required', 'integer', 'min:1', 'max:20'],
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
            'gedung' => trim($validated['gedung'] ?? '') ?: 'Gedung A',
            'lantai' => (int) $validated['lantai'],
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
        $this->pastikanPengelola($request);

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
        $this->pastikanPengelola($request);
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
        $this->pastikanPengelola($request);
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
        $this->pastikanPengelola($request);

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
     * Menyetujui pengajuan penempatan maupun pengajuan pindah kamar.
     *
     * Penempatan baru hanya diterapkan pada tahap ini, setelah Musyrifah menyetujui.
     */
    public function approvePengajuan(Request $request, PengajuanKamar $pengajuan): RedirectResponse
    {
        $this->pastikanPengelola($request);

        if ($pengajuan->status !== PengajuanKamar::STATUS_MENUNGGU) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $pindahKamar = $pengajuan->jenis === PengajuanKamar::JENIS_PINDAH;

        try {
            DB::transaction(function () use ($pengajuan, $request): void {
                $kamar = Kamar::lockForUpdate()->findOrFail($pengajuan->kamar_id);
                $santri = User::lockForUpdate()->findOrFail($pengajuan->user_id);

                // Hitung santri yang sudah resmi menempati kamar tujuan
                $penghuniSaatIni = User::where('kamar_id', $kamar->id)->count();

                if ($penghuniSaatIni >= (int) $kamar->kapasitas) {
                    throw new \RuntimeException('Kamar '.$kamar->nomor_kamar.' sudah penuh kapasitasnya ('.$kamar->kapasitas.' orang).');
                }

                $kamarAsalId = $santri->kamar_id;

                $pengajuan->update([
                    'status' => PengajuanKamar::STATUS_DISETUJUI,
                    'kamar_asal_id' => $pengajuan->kamar_asal_id ?: $kamarAsalId,
                    'catatan' => null,
                    'diverifikasi_oleh' => $request->user()?->id,
                    'updated_at' => now(),
                ]);

                // Tetapkan penempatan kamar pada akun mahasiswa
                $santri->update(['kamar_id' => $kamar->id]);

                // Sinkronkan kapasitas kamar tujuan dan kamar asal
                $kamarTerkait = collect([$kamar->id, $kamarAsalId, $pengajuan->kamar_asal_id])
                    ->filter()
                    ->unique()
                    ->values();

                Kamar::whereKey($kamarTerkait)->get()
                    ->each(fn (Kamar $kamarItem) => $kamarItem->sinkronkanOkupansi());

                // Pengajuan pindah lain milik santri ini tidak relevan lagi
                PengajuanKamar::query()
                    ->where('user_id', $santri->id)
                    ->whereKeyNot($pengajuan->id)
                    ->where('jenis', PengajuanKamar::JENIS_PINDAH)
                    ->where('status', PengajuanKamar::STATUS_MENUNGGU)
                    ->update([
                        'status' => PengajuanKamar::STATUS_DITOLAK,
                        'catatan' => 'Dibatalkan otomatis karena penempatan kamar sudah diperbarui.',
                        'diverifikasi_oleh' => $request->user()?->id,
                        'updated_at' => now(),
                    ]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        $pengajuan->refresh()->load(['kamar', 'kamarAsal', 'user']);

        $this->audit->catat(
            'kamar',
            $pindahKamar ? 'pindah_kamar' : 'penempatan_kamar',
            'Pengajuan kamar '.$pengajuan->user?->name,
            (int) $pengajuan->getKey(),
            $pindahKamar
                ? 'Perpindahan kamar disetujui dari '.($pengajuan->kamarAsal?->nomor_kamar ?? '-').' ke '.($pengajuan->kamar?->nomor_kamar ?? '-').'.'
                : 'Penempatan kamar '.($pengajuan->kamar?->nomor_kamar ?? '-').' disetujui.',
            $request->user(),
            ['kamar' => $pengajuan->kamarAsal?->nomor_kamar, 'status' => PengajuanKamar::STATUS_MENUNGGU],
            ['kamar' => $pengajuan->kamar?->nomor_kamar, 'status' => PengajuanKamar::STATUS_DISETUJUI],
        );

        return redirect()
            ->route('musyrifah.data_kamar')
            ->with(
                'success',
                $pindahKamar
                    ? 'Pengajuan pindah kamar disetujui. Penempatan mahasiswa telah diperbarui ke kamar tujuan.'
                    : 'Penempatan santri pada kamar berhasil disetujui.'
            );
    }

    /**
     * Menolak pengajuan kamar. Penempatan mahasiswa tidak diubah, sehingga
     * mahasiswa pindah kamar tetap menempati kamar lamanya.
     */
    public function rejectPengajuan(Request $request, PengajuanKamar $pengajuan): RedirectResponse
    {
        $this->pastikanPengelola($request);

        if ($pengajuan->status !== PengajuanKamar::STATUS_MENUNGGU) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'catatan.max' => 'Catatan penolakan maksimal 500 karakter.',
        ]);

        $catatan = filled($validated['catatan'] ?? null)
            ? trim($validated['catatan'])
            : 'Pengajuan kamar ditolak oleh Musyrifah.';

        $pengajuan->update([
            'status' => PengajuanKamar::STATUS_DITOLAK,
            'catatan' => $catatan,
            'diverifikasi_oleh' => $request->user()?->id,
            'updated_at' => now(),
        ]);

        $this->audit->catat(
            'kamar',
            'pengajuan_kamar_ditolak',
            'Pengajuan kamar '.$pengajuan->user?->name,
            (int) $pengajuan->getKey(),
            'Pengajuan kamar ditolak. Catatan: '.$catatan,
            $request->user(),
            ['status' => PengajuanKamar::STATUS_MENUNGGU],
            ['status' => PengajuanKamar::STATUS_DITOLAK, 'catatan' => $catatan],
        );

        return redirect()
            ->route('musyrifah.data_kamar')
            ->with('success', 'Pengajuan kamar telah ditolak. Mahasiswa tetap menempati kamar saat ini dan dapat melihat catatan penolakan.');
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
     * Memastikan pengguna memiliki peran yang diizinkan mengelola kamar.
     */
    private function pastikanPengelola(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['musyrifah', 'pengelola', 'admin'], true), 403);
    }
}
