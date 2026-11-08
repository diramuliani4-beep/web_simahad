<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MusyrifahPendaftaranController extends Controller
{
    /**
     * Menampilkan pendaftar asli beserta informasi validasi.
     */
    public function index(Request $request): View
    {
        $this->pastikanPengelola($request);

        $pendaftarans = Pendaftaran::with('user')
            ->latest('id')
            ->get();

        // Tetap kompatibel dengan variabel array Blade lama.
        $daftarPendaftar = $pendaftarans->map(function (Pendaftaran $pendaftaran): array {
            return [
                'id' => $pendaftaran->id,
                'nama' => $pendaftaran->nama_lengkap,
                'nim' => $pendaftaran->user?->nim ?: '-',
                'status' => match ($pendaftaran->status) {
                    'menunggu' => 'Menunggu',
                    'disetujui' => 'Disetujui',
                    'ditolak' => 'Ditolak',
                    'perlu_perbaikan' => 'Perlu Perbaikan',
                    default => ucfirst(str_replace('_', ' ', (string) $pendaftaran->status)),
                },
                'status_kode' => $pendaftaran->status,
                'kategori' => $pendaftaran->kategori ?: 'umum',
                'jenis_kelamin' => $pendaftaran->jenis_kelamin,
                'tahun_angkatan' => $pendaftaran->tahun_angkatan,
                'fakultas' => $pendaftaran->fakultas,
                'program_studi' => $pendaftaran->program_studi,
                'nominal_pembayaran' => $pendaftaran->nominal_pembayaran,
                'tanggal_daftar' => $pendaftaran->created_at?->format('d/m/Y H:i'),
                'validator_nama' => $pendaftaran->validator_nama,
                'divalidasi_pada' => $pendaftaran->divalidasi_pada?->format('d/m/Y H:i'),
                'catatan_validasi' => $pendaftaran->catatan_validasi,
            ];
        })->all();

        return view('musyrifah.pendaftaran', compact('daftarPendaftar', 'pendaftarans'));
    }

    /**
     * Menyetujui, meminta perbaikan, atau menolak satu pendaftaran.
     */
    public function updateStatus(Request $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $this->pastikanPengelola($request);

        if (! in_array($pendaftaran->status, ['menunggu', 'perlu_perbaikan'], true)) {
            return back()->with('error', 'Pendaftaran sudah diproses dan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['disetujui', 'ditolak', 'perlu_perbaikan'])],
            'catatan_validasi' => [
                'nullable',
                'string',
                'max:2000',
                Rule::requiredIf(in_array($request->input('status'), ['ditolak', 'perlu_perbaikan'], true)),
            ],
        ], [
            'status.required' => 'Keputusan validasi harus dipilih.',
            'status.in' => 'Keputusan validasi tidak valid.',
            'catatan_validasi.required' => 'Catatan wajib diisi saat menolak atau meminta perbaikan berkas.',
            'catatan_validasi.max' => 'Catatan maksimal 2000 karakter.',
        ]);

        if ($validated['status'] === 'disetujui') {
            // Kelengkapan dokumen bukan bukti bahwa transfer sudah sah.
            $dokumenLengkap = filled($pendaftaran->bukti_penerimaan)
                && filled($pendaftaran->bukti_pembayaran)
                && Storage::disk('public')->exists($pendaftaran->bukti_penerimaan)
                && Storage::disk('public')->exists($pendaftaran->bukti_pembayaran);

            if (
                (float) $pendaftaran->nominal_pembayaran < 1000000
                || ! $dokumenLengkap
                || ! $pendaftaran->konfirmasi_data
            ) {
                return back()->with(
                    'error',
                    'Pendaftaran belum dapat disetujui. Periksa nominal minimal Rp1.000.000, bukti penerimaan, bukti pembayaran, dan persetujuan data.'
                );
            }
        }

        $musyrifah = $request->user();

        // Update bersyarat mencegah konflik validasi.
        $berhasil = Pendaftaran::query()
            ->whereKey($pendaftaran->id)
            ->whereIn('status', ['menunggu', 'perlu_perbaikan'])
            ->update([
                'status' => $validated['status'],
                'validator_id' => $musyrifah->id,
                'validator_nama' => $musyrifah->name,
                'divalidasi_pada' => now(),
                'catatan_validasi' => filled($validated['catatan_validasi'] ?? null)
                    ? trim($validated['catatan_validasi'])
                    : null,
            ]);

        if (! $berhasil) {
            return back()->with('error', 'Pendaftaran sudah diproses Musyrifah lain. Muat ulang daftar.');
        }

        $pesanSukses = match ($validated['status']) {
            'disetujui' => 'Pendaftaran berhasil disetujui.',
            'perlu_perbaikan' => 'Permintaan perbaikan berhasil dikirim ke mahasiswa.',
            'ditolak' => 'Pendaftaran telah ditolak.',
            default => 'Validasi pendaftaran berhasil disimpan.',
        };

        return back()->with('success', $pesanSukses);
    }

    /**
     * Menampilkan berkas dari disk public untuk diperiksa Musyrifah.
     */
    public function berkas(Request $request, Pendaftaran $pendaftaran, string $jenis): BinaryFileResponse
    {
        $this->pastikanPengelola($request);

        $kolom = match ($jenis) {
            'penerimaan' => 'bukti_penerimaan',
            'pembayaran' => 'bukti_pembayaran',
            'ijazah' => 'ijazah_sma',
            'ktp' => 'ktp',
            'kk' => 'kk',
            default => abort(404),
        };

        $path = $pendaftaran->{$kolom};
        $disk = Storage::disk('public');

        abort_unless(filled($path) && $disk->exists($path), 404);

        return response()->file($disk->path($path));
    }

    /**
     * Hanya akun berperan Musyrifah/Pengelola yang boleh memvalidasi.
     */
    private function pastikanPengelola(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);
        abort_unless(in_array($role, ['musyrifah', 'pengelola'], true), 403);
    }
}
