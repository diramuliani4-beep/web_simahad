<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MusyrifahPengumumanController extends Controller
{
    /**
     * Daftar pengumuman beserta pencarian judul dan filter kategori/status.
     */
    public function index(Request $request): View
    {
        $this->pastikanPengelola($request);

        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:60'],
            'status' => ['nullable', Rule::in(array_keys(Pengumuman::statusPilihan()))],
        ], [
            'status.in' => 'Status pengumuman tidak dikenali.',
        ]);

        $pengumumans = Pengumuman::query()
            ->with('penulis')
            ->cari($filter['cari'] ?? null)
            ->when(filled($filter['kategori'] ?? null), fn ($query) => $query->where('kategori', $filter['kategori']))
            ->when(filled($filter['status'] ?? null), fn ($query) => $query->where('status', $filter['status']))
            ->orderByDesc('tanggal_publikasi')
            ->orderByDesc('id')
            ->get();

        return view('musyrifah.pengumuman', [
            'pengumumans' => $pengumumans,
            'filter' => $filter,
            'kategoriPilihan' => Pengumuman::kategoriTersedia(),
            'statusPilihan' => Pengumuman::statusPilihan(),
            'statistik' => [
                'total' => Pengumuman::query()->count(),
                'terbit' => Pengumuman::query()->dipublikasikan()->count(),
                'draft' => Pengumuman::query()->where('status', Pengumuman::STATUS_DRAFT)->count(),
            ],
        ]);
    }

    /**
     * Detail satu pengumuman.
     */
    public function show(Request $request, Pengumuman $pengumuman): View
    {
        $this->pastikanPengelola($request);

        return view('musyrifah.detail_pengumuman', [
            'pengumuman' => $pengumuman->load('penulis'),
            'mode' => 'lihat',
        ]);
    }

    /**
     * Menyimpan pengumuman baru sebagai draft maupun terbit.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->pastikanPengelola($request);

        $data = $this->validasiPengumuman($request);
        $musyrifah = $request->user();

        $pengumuman = Pengumuman::query()->create([
            'user_id' => $musyrifah->id,
            'penulis_nama' => $musyrifah->name,
            'judul' => $data['judul'],
            'kategori' => $data['kategori'],
            'isi' => $data['isi'],
            'tanggal_publikasi' => $data['tanggal_publikasi'],
            'status' => $data['status'],
            'dipublikasikan_pada' => $data['status'] === Pengumuman::STATUS_DIPUBLIKASIKAN ? now() : null,
            'gambar_path' => $this->simpanBerkas($request, 'gambar', 'pengumuman/gambar'),
            'gambar_nama' => $request->file('gambar')?->getClientOriginalName(),
            'dokumen_path' => $this->simpanBerkas($request, 'dokumen', 'pengumuman/dokumen'),
            'dokumen_nama' => $request->file('dokumen')?->getClientOriginalName(),
        ]);

        if ($pengumuman->isDipublikasikan()) {
            $this->kirimNotifikasiPengumuman($pengumuman);
        }

        return redirect()
            ->route('musyrifah.pengumuman.show', $pengumuman)
            ->with('success', $pengumuman->isDipublikasikan()
                ? 'Pengumuman berhasil dipublikasikan.'
                : 'Pengumuman berhasil disimpan sebagai draft.');
    }

    /**
     * Formulir edit pengumuman.
     */
    public function edit(Request $request, Pengumuman $pengumuman): View
    {
        $this->pastikanPengelola($request);

        return view('musyrifah.detail_pengumuman', [
            'pengumuman' => $pengumuman->load('penulis'),
            'mode' => 'edit',
        ]);
    }

    /**
     * Memperbarui pengumuman yang sama (tanpa membuat data baru).
     */
    public function update(Request $request, Pengumuman $pengumuman): RedirectResponse
    {
        $this->pastikanPengelola($request);

        $data = $this->validasiPengumuman($request);

        $atribut = [
            'judul' => $data['judul'],
            'kategori' => $data['kategori'],
            'isi' => $data['isi'],
            'tanggal_publikasi' => $data['tanggal_publikasi'],
            'status' => $data['status'],
            'dipublikasikan_pada' => $this->waktuPublikasi($pengumuman, $data['status']),
        ];

        if ($request->hasFile('gambar')) {
            $this->hapusBerkas($pengumuman->gambar_path);
            $atribut['gambar_path'] = $this->simpanBerkas($request, 'gambar', 'pengumuman/gambar');
            $atribut['gambar_nama'] = $request->file('gambar')->getClientOriginalName();
        }

        if ($request->hasFile('dokumen')) {
            $this->hapusBerkas($pengumuman->dokumen_path);
            $atribut['dokumen_path'] = $this->simpanBerkas($request, 'dokumen', 'pengumuman/dokumen');
            $atribut['dokumen_nama'] = $request->file('dokumen')->getClientOriginalName();
        }

        if ($request->boolean('hapus_gambar')) {
            $this->hapusBerkas($pengumuman->gambar_path);
            $atribut['gambar_path'] = null;
            $atribut['gambar_nama'] = null;
        }

        if ($request->boolean('hapus_dokumen')) {
            $this->hapusBerkas($pengumuman->dokumen_path);
            $atribut['dokumen_path'] = null;
            $atribut['dokumen_nama'] = null;
        }

        $pengumuman->update($atribut);

        if ($pengumuman->isDipublikasikan()) {
            $this->kirimNotifikasiPengumuman($pengumuman);
        }

        return redirect()
            ->route('musyrifah.pengumuman.show', $pengumuman)
            ->with('success', $pengumuman->isDipublikasikan()
                ? 'Pengumuman berhasil diperbarui dan sudah tampil pada halaman mahasiswa.'
                : 'Pengumuman berhasil diperbarui dan disimpan sebagai draft.');
    }

    /**
     * Menghapus pengumuman beserta berkas pendukungnya.
     */
    public function destroy(Request $request, Pengumuman $pengumuman): RedirectResponse
    {
        $this->pastikanPengelola($request);

        $this->hapusBerkas($pengumuman->gambar_path);
        $this->hapusBerkas($pengumuman->dokumen_path);

        $pengumuman->delete();

        return redirect()
            ->route('musyrifah.pengumuman')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Mengubah status Draft/Dipublikasikan secara langsung dari daftar.
     */
    public function ubahStatus(Request $request, Pengumuman $pengumuman): RedirectResponse
    {
        $this->pastikanPengelola($request);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Pengumuman::statusPilihan()))],
        ], [
            'status.required' => 'Status pengumuman wajib dipilih.',
            'status.in' => 'Status pengumuman tidak valid.',
        ]);

        $pengumuman->update([
            'status' => $validated['status'],
            'dipublikasikan_pada' => $this->waktuPublikasi($pengumuman, $validated['status']),
        ]);

        if ($pengumuman->isDipublikasikan()) {
            $this->kirimNotifikasiPengumuman($pengumuman);
        }

        return redirect()
            ->back()
            ->with('success', $pengumuman->isDipublikasikan()
                ? 'Pengumuman berhasil dipublikasikan.'
                : 'Pengumuman dikembalikan menjadi draft.');
    }

    /**
     * Mengirim gambar/dokumen pendukung kepada Musyrifah yang berwenang.
     */
    public function berkas(Request $request, Pengumuman $pengumuman, string $jenis): BinaryFileResponse
    {
        $this->pastikanPengelola($request);

        $path = match ($jenis) {
            'gambar' => $pengumuman->gambar_path,
            'dokumen' => $pengumuman->dokumen_path,
            default => null,
        };

        abort_unless(filled($path), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }

    /**
     * Validasi formulir pengumuman (tambah dan edit).
     *
     * @return array<string, string>
     */
    private function validasiPengumuman(Request $request): array
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', Rule::in(array_merge(Pengumuman::kategoriTersedia(), ['lainnya']))],
            'kategori_baru' => [
                'nullable',
                'string',
                'max:60',
                Rule::requiredIf($request->input('kategori') === 'lainnya'),
            ],
            'isi' => ['required', 'string', 'min:20', 'max:20000'],
            'tanggal_publikasi' => ['required', 'date'],
            'status' => ['required', Rule::in(array_keys(Pengumuman::statusPilihan()))],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'judul.max' => 'Judul pengumuman maksimal 150 karakter.',
            'kategori.required' => 'Kategori pengumuman wajib dipilih.',
            'kategori.in' => 'Kategori pengumuman tidak dikenali.',
            'kategori_baru.required' => 'Nama kategori baru wajib diisi.',
            'kategori_baru.max' => 'Nama kategori maksimal 60 karakter.',
            'isi.required' => 'Isi pengumuman wajib diisi.',
            'isi.min' => 'Isi pengumuman minimal 20 karakter.',
            'isi.max' => 'Isi pengumuman maksimal 20000 karakter.',
            'tanggal_publikasi.required' => 'Tanggal publikasi wajib diisi.',
            'tanggal_publikasi.date' => 'Tanggal publikasi tidak valid.',
            'status.required' => 'Status pengumuman wajib dipilih.',
            'status.in' => 'Status pengumuman tidak valid.',
            'gambar.image' => 'Berkas gambar harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus berformat JPG, JPEG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 5 MB.',
            'dokumen.mimes' => 'Dokumen harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen.max' => 'Ukuran dokumen maksimal 5 MB.',
        ]);

        if ($validated['kategori'] === 'lainnya') {
            $validated['kategori'] = trim($validated['kategori_baru']);
        }

        return $validated;
    }

    /**
     * Menyimpan berkas unggahan ke disk privat.
     */
    private function simpanBerkas(Request $request, string $field, string $folder): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store($folder, 'local');
    }

    private function hapusBerkas(?string $path): void
    {
        if (filled($path) && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }

    /**
     * Waktu publikasi: diisi saat terbit dan dikosongkan saat kembali ke draft.
     */
    private function waktuPublikasi(Pengumuman $pengumuman, string $status): ?string
    {
        if ($status !== Pengumuman::STATUS_DIPUBLIKASIKAN) {
            return null;
        }

        return ($pengumuman->dipublikasikan_pada ?? now())->toDateTimeString();
    }

    /**
     * Mengirim notifikasi pengumuman baru kepada seluruh mahasiswa.
     * Kunci unik mencegah notifikasi ganda untuk pengumuman yang sama.
     */
    private function kirimNotifikasiPengumuman(Pengumuman $pengumuman): void
    {
        $kunci = 'pengumuman:'.$pengumuman->id;
        $tautan = route('pengumuman.mhs.show', $pengumuman);
        $ringkasan = $pengumuman->ringkasan(120);

        User::query()
            ->whereRaw('LOWER(role) = ?', ['mahasiswa'])
            ->select('id')
            ->chunkById(200, function ($mahasiswas) use ($pengumuman, $kunci, $tautan, $ringkasan) {
                foreach ($mahasiswas as $mahasiswa) {
                    Notifikasi::kirim(
                        $mahasiswa->id,
                        Notifikasi::TIPE_PENGUMUMAN_BARU,
                        'Pengumuman baru: '.$pengumuman->judul,
                        $ringkasan,
                        $tautan,
                        $kunci
                    );
                }
            });
    }

    /**
     * Hanya Musyrifah/Pengelola yang boleh mengelola pengumuman.
     */
    private function pastikanPengelola(Request $request): void
    {
        $role = strtolower(trim((string) $request->user()?->role));
        abort_unless(in_array($role, ['musyrifah', 'pengelola'], true), 403);
    }
}
