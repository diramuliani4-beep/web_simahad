<?php

namespace App\Http\Controllers;

use App\Models\DokumentasiKegiatan;
use App\Models\PertemuanPembelajaran;
use App\Models\User;
use App\Services\AuditTrailService;
use App\Services\PembelajaranGuruService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuruKegiatanDokumentasiController extends Controller
{
    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly PembelajaranGuruService $layanan,
    ) {}

    /** Daftar dokumentasi milik Guru yang sedang masuk. */
    public function index(Request $request): View
    {
        $guru = $this->layanan->guru($request);

        $dokumentasi = DokumentasiKegiatan::query()
            ->where('guru_id', $guru->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return view('guru.kegiatan_dokumentasi', [
            'dokumentasi' => $dokumentasi,
            'kelompokPilihan' => $this->kelompokPilihan($guru),
        ]);
    }

    /** Menyimpan dokumentasi kegiatan baru beserta fotonya. */
    public function store(Request $request): RedirectResponse
    {
        $guru = $this->layanan->guru($request);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:180'],
            'kelompok' => ['nullable', 'string', 'max:60'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'judul.required' => 'Judul kegiatan wajib diisi.',
            'foto.mimes' => 'Foto dokumentasi harus berupa JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $dokumentasi = new DokumentasiKegiatan($data);
        $dokumentasi->guru_id = $guru->id;

        if ($request->hasFile('foto')) {
            $berkas = $request->file('foto');
            $dokumentasi->foto_path = $berkas->store('dokumentasi/foto', 'local');
            $dokumentasi->foto_nama = $berkas->getClientOriginalName();
        }

        $dokumentasi->save();

        $this->audit->catat(
            'pembelajaran',
            'dokumentasi_ditambahkan',
            $dokumentasi->judul,
            $dokumentasi->id,
            "Guru {$guru->name} menambahkan dokumentasi \"{$dokumentasi->judul}\".",
            $guru,
            [],
            $dokumentasi->only(['judul', 'kelompok', 'tanggal', 'keterangan']),
        );

        return redirect()
            ->route('guru.kegiatan-dokumentasi')
            ->with('success', 'Dokumentasi "'.$dokumentasi->judul.'" berhasil disimpan.');
    }

    /** Memperbarui dokumentasi milik Guru yang sedang masuk. */
    public function update(Request $request, DokumentasiKegiatan $dokumentasi): RedirectResponse
    {
        $guru = $this->layanan->guru($request);
        $this->pastikanMilikGuru($guru, $dokumentasi);
        abort_if($dokumentasi->pertemuan_id !== null, 403, 'Kelola dokumentasi ini melalui pertemuan pembelajaran.');

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:180'],
            'kelompok' => ['nullable', 'string', 'max:60'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'judul.required' => 'Judul kegiatan wajib diisi.',
            'foto.mimes' => 'Foto dokumentasi harus berupa JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $sebelum = $dokumentasi->only(['judul', 'kelompok', 'tanggal', 'keterangan']);

        $dokumentasi->fill($data);

        if ($request->hasFile('foto')) {
            $this->hapusFoto($dokumentasi->foto_path);

            $berkas = $request->file('foto');
            $dokumentasi->foto_path = $berkas->store('dokumentasi/foto', 'local');
            $dokumentasi->foto_nama = $berkas->getClientOriginalName();
        }

        $dokumentasi->save();

        $this->audit->catat(
            'pembelajaran',
            'dokumentasi_diperbarui',
            $dokumentasi->judul,
            $dokumentasi->id,
            "Guru {$guru->name} memperbarui dokumentasi \"{$dokumentasi->judul}\".",
            $guru,
            $sebelum,
            $dokumentasi->only(['judul', 'kelompok', 'tanggal', 'keterangan']),
        );

        return redirect()
            ->route('guru.kegiatan-dokumentasi')
            ->with('success', 'Dokumentasi "'.$dokumentasi->judul.'" berhasil diperbarui.');
    }

    /** Menghapus dokumentasi beserta berkas fotonya. */
    public function destroy(Request $request, DokumentasiKegiatan $dokumentasi): RedirectResponse
    {
        $guru = $this->layanan->guru($request);
        $this->pastikanMilikGuru($guru, $dokumentasi);
        abort_if($dokumentasi->pertemuan_id !== null, 403, 'Kelola dokumentasi ini melalui pertemuan pembelajaran.');

        $sebelum = $dokumentasi->only(['judul', 'kelompok', 'tanggal', 'keterangan']);
        $judul = $dokumentasi->judul;

        $this->hapusFoto($dokumentasi->foto_path);
        $dokumentasi->delete();

        $this->audit->catat(
            'pembelajaran',
            'dokumentasi_dihapus',
            $judul,
            null,
            "Guru {$guru->name} menghapus dokumentasi \"{$judul}\".",
            $guru,
            $sebelum,
            [],
        );

        return redirect()
            ->route('guru.kegiatan-dokumentasi')
            ->with('success', 'Dokumentasi "'.$judul.'" berhasil dihapus.');
    }

    /**
     * Menyajikan foto dokumentasi.
     *
     * Diakses oleh pemilik Guru serta Musyrifah, Pimpinan, dan Admin
     * melalui halaman monitoring masing-masing.
     */
    public function foto(Request $request, DokumentasiKegiatan $dokumentasi): BinaryFileResponse
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));

        if ($dokumentasi->pertemuan_id !== null) {
            $this->pastikanAksesPertemuan($request, $dokumentasi->pertemuan);
        } else {
            $boleh = in_array($role, ['musyrifah', 'pimpinan', 'admin'], true)
                || ($role === 'guru' && (int) $dokumentasi->guru_id === (int) $user?->id);
            abort_unless($user && $boleh, 403);
        }

        abort_unless(filled($dokumentasi->foto_path)
            && Storage::disk('local')->exists($dokumentasi->foto_path), 404);

        return response()->file(Storage::disk('local')->path($dokumentasi->foto_path), [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ])->setPrivate();
    }

    public function pertemuan(Request $request, PertemuanPembelajaran $pertemuan): View
    {
        $this->pastikanAksesPertemuan($request, $pertemuan);
        $pertemuan->load('mataPelajaran');
        $role = strtolower(trim((string) $request->user()->role));
        $kembali = match ($role) {
            'guru' => route('guru.pembelajaran.pertemuan', $pertemuan).'#dokumentasi',
            'pimpinan' => route('pimpinan.monitoring.pembelajaran', ['periode' => $pertemuan->tanggal->format('Y-m')]),
            default => route('musyrifah.manajemen_pembelajaran', ['tab' => 'pemantauan']),
        };

        return view('dokumentasi-pertemuan', [
            'pertemuan' => $pertemuan,
            'dokumentasi' => $pertemuan->dokumentasi()->with('guru')->latest('id')->get(),
            'kembali' => $kembali,
        ]);
    }

    private function pastikanAksesPertemuan(Request $request, PertemuanPembelajaran $pertemuan): void
    {
        $role = strtolower(trim((string) $request->user()?->role));
        if ($role === 'guru') {
            $this->layanan->pastikanMilikGuru($this->layanan->guru($request), $pertemuan);

            return;
        }

        abort_unless(in_array($role, ['musyrifah', 'pengelola', 'pimpinan', 'admin'], true), 403);
    }

    private function pastikanMilikGuru(User $guru, DokumentasiKegiatan $dokumentasi): void
    {
        abort_unless((int) $dokumentasi->guru_id === (int) $guru->getKey(), 403, 'Dokumentasi ini bukan milik Anda.');
    }

    /**
     * Daftar kelompok dari penugasan Guru untuk pilihan isian kelompok.
     *
     * @return array<int, string>
     */
    private function kelompokPilihan(User $guru): array
    {
        try {
            return $this->layanan->penugasan($guru)
                ->pluck('kelompok')
                ->filter()
                ->map(fn ($kelompok): string => (string) $kelompok)
                ->unique()
                ->sort()
                ->values()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function hapusFoto(?string $path): void
    {
        if (filled($path) && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
