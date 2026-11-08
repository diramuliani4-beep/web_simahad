<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MusyrifahDataKamarController extends Controller
{
    /**
     * Menampilkan halaman data kamar untuk Musyrifah.
     */
    public function index(Request $request): View
    {
        $this->pastikanPengelola($request);

        // Ambil data kamar asli dari database beserta penghuni mahasiswa
        $kamars = Kamar::with(['users' => function ($q) {
            $q->select('id', 'name', 'nim', 'email', 'no_hp', 'kamar_id')
                ->orderBy('name');
        }])
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get();

        // Rekapitulasi data kamar
        $jumlahKamar = $kamars->count();
        $totalKapasitas = (int) $kamars->sum('kapasitas');
        $kamarTerisi = (int) $kamars->sum(fn ($k) => $k->users->count());
        $kamarTersedia = $kamars->filter(fn ($k) => in_array($k->status, ['Tersedia', 'tersedia']) && ($k->kapasitas - $k->users->count()) > 0)->count();
        $kamarPenuh = $kamars->filter(fn ($k) => in_array($k->status, ['Penuh', 'penuh']) || ($k->kapasitas - $k->users->count()) <= 0)->count();
        $sisaTempatTidur = max(0, $totalKapasitas - $kamarTerisi);

        // Format data pendaftaran kamar untuk tampilan blade
        $daftarKamar = $kamars->map(function (Kamar $kamar): array {
            $penghuniCount = $kamar->users->count();
            $sisa = max(0, (int) $kamar->kapasitas - $penghuniCount);

            return [
                'id' => $kamar->id,
                'nama' => $kamar->nama_kamar,
                'nomor' => $kamar->nomor_kamar,
                'gedung' => $kamar->gedung ?: 'Gedung A',
                'lantai' => (int) $kamar->lantai,
                'kapasitas' => (int) $kamar->kapasitas,
                'terisi' => $penghuniCount,
                'sisa' => $sisa,
                'status' => $kamar->status ?: ($sisa <= 0 ? 'Penuh' : 'Tersedia'),
                'fasilitas' => $kamar->fasilitas,
                'foto' => $kamar->foto,
                'foto_url' => $kamar->foto ? Storage::url($kamar->foto) : null,
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
            'daftarKamar'
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
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'nomor_kamar.required' => 'Nomor kamar wajib diisi.',
            'nomor_kamar.unique' => 'Nomor kamar sudah terdaftar.',
            'lantai.required' => 'Lantai kamar wajib diisi.',
            'kapasitas.required' => 'Kapasitas kamar wajib diisi.',
            'foto.image' => 'File harus berupa gambar (JPG, JPEG, PNG, WEBP).',
            'foto.max' => 'Ukuran foto kamar maksimal 5 MB.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_kamar', 'public');
        }

        Kamar::create([
            'nama_kamar' => 'Kamar '.trim($validated['nomor_kamar']),
            'nomor_kamar' => trim($validated['nomor_kamar']),
            'gedung' => trim($validated['gedung'] ?? '') ?: 'Gedung A',
            'lantai' => (int) $validated['lantai'],
            'kapasitas' => (int) $validated['kapasitas'],
            'terisi' => 0,
            'status' => $validated['status'],
            'fasilitas' => filled($validated['fasilitas'] ?? null) ? trim($validated['fasilitas']) : null,
            'foto' => $fotoPath,
        ]);

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
            $fotoPath = $request->file('foto')->store('foto_kamar', 'public');
            if ($fotoPath) {
                if ($kamar->foto && Storage::disk('public')->exists($kamar->foto)) {
                    Storage::disk('public')->delete($kamar->foto);
                }
                $data['foto'] = $fotoPath;
            }
        }

        $kamar->update($data);

        return redirect()
            ->route('musyrifah.data_kamar')
            ->with('success', 'Data kamar berhasil diperbarui.');
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

        if ($kamar->foto && Storage::disk('public')->exists($kamar->foto)) {
            Storage::disk('public')->delete($kamar->foto);
        }

        $kamar->delete();

        return redirect()
            ->route('musyrifah.data_kamar')
            ->with('success', 'Kamar berhasil dihapus.');
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
