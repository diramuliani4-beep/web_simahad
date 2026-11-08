<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TugasMhsController extends Controller
{
    /**
     * Daftar tugas yang ditujukan kepada kelompok mahasiswa.
     */
    public function index(Request $request): View
    {
        $mahasiswa = $this->mahasiswa($request);
        $kelompok = $mahasiswa->kelompokPembelajaran();

        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(array_keys(Tugas::statusPengumpulanPilihan()))],
        ]);

        $daftar = Tugas::query()
            ->dipublikasikan()
            ->untukMahasiswa($kelompok)
            ->with(['mataPelajaran', 'guru'])
            ->with(['pengumpulan' => fn ($query) => $query->where('user_id', $mahasiswa->id)])
            ->cari($filter['cari'] ?? null)
            ->orderByDesc('batas_pengumpulan')
            ->orderByDesc('id')
            ->get()
            ->map(function (Tugas $tugas) {
                $pengumpulan = $tugas->pengumpulan->first();

                return [
                    'tugas' => $tugas,
                    'pengumpulan' => $pengumpulan,
                    'status' => $tugas->statusUntuk($pengumpulan),
                ];
            })
            ->when(filled($filter['status'] ?? null), fn ($daftar) => $daftar->where('status.status', $filter['status']))
            ->values();

        $semua = Tugas::query()
            ->dipublikasikan()
            ->untukMahasiswa($kelompok)
            ->with(['pengumpulan' => fn ($query) => $query->where('user_id', $mahasiswa->id)])
            ->get();

        $statistik = [
            'total' => $semua->count(),
            'belum' => $semua->filter(fn (Tugas $tugas) => $tugas->statusUntuk($tugas->pengumpulan->first())['status'] === Tugas::STATUS_BELUM_DIKUMPULKAN)->count(),
            'dinilai' => $semua->filter(fn (Tugas $tugas) => $tugas->statusUntuk($tugas->pengumpulan->first())['status'] === Tugas::STATUS_SUDAH_DINILAI)->count(),
        ];

        return view('Mahasiswa.tugas_mhs', [
            'mahasiswa' => $mahasiswa,
            'kelompok' => $kelompok,
            'daftarTugas' => $daftar,
            'filter' => $filter,
            'statistik' => $statistik,
            'statusPilihan' => Tugas::statusPengumpulanPilihan(),
        ]);
    }

    /**
     * Detail tugas, instruksi, lampiran, dan nilai mahasiswa.
     */
    public function show(Request $request, Tugas $tugas): View
    {
        $mahasiswa = $this->mahasiswa($request);

        abort_unless($tugas->isDipublikasikan(), 404);
        abort_unless($tugas->kelompok === $mahasiswa->kelompokPembelajaran(), 404);

        $pengumpulan = $tugas->pengumpulan()
            ->where('user_id', $mahasiswa->id)
            ->with('riwayatNilai')
            ->first();

        return view('Mahasiswa.detail_tugas_mhs', [
            'tugas' => $tugas->load(['mataPelajaran', 'guru']),
            'pengumpulan' => $pengumpulan,
            'status' => $tugas->statusUntuk($pengumpulan),
        ]);
    }

    /**
     * Mengunggah atau mengganti berkas pengumpulan tugas.
     */
    public function kumpulkan(Request $request, Tugas $tugas): RedirectResponse
    {
        $mahasiswa = $this->mahasiswa($request);

        abort_unless($tugas->isDipublikasikan(), 404);
        abort_unless($tugas->kelompok === $mahasiswa->kelompokPembelajaran(), 404);

        if (! $tugas->masihDapatDikumpulkan()) {
            return back()->withErrors([
                'file' => 'Batas pengumpulan tugas ini sudah berakhir pada '.$tugas->batasTeks().'.',
            ]);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], [
            'file.required' => 'Berkas tugas wajib dipilih.',
            'file.mimes' => 'Berkas harus berformat JPG, JPEG, PNG, atau PDF.',
            'file.max' => 'Ukuran berkas maksimal 5 MB.',
        ]);

        $pengumpulan = PengumpulanTugas::firstOrNew([
            'tugas_id' => $tugas->id,
            'user_id' => $mahasiswa->id,
        ]);

        $berkasLama = $pengumpulan->file_path;

        $berkas = $request->file('file');
        $pengumpulan->file_path = $berkas->store('tugas/pengumpulan', 'local');
        $pengumpulan->file_nama = $berkas->getClientOriginalName();
        $pengumpulan->dikumpulkan_pada = now();
        $pengumpulan->keterangan = $request->input('keterangan');
        $pengumpulan->save();

        if (filled($berkasLama) && $berkasLama !== $pengumpulan->file_path) {
            Storage::disk('local')->delete($berkasLama);
        }

        Notifikasi::kirim(
            $tugas->user_id,
            Notifikasi::TIPE_PENGUMPULAN_BARU,
            'Pengumpulan tugas: '.$mahasiswa->name,
            $mahasiswa->name.' mengumpulkan tugas "'.$tugas->judul.'" ('.$tugas->kelompok.').',
            route('guru.pengumpulan.index', $tugas)
        );

        Notifikasi::kirim(
            $mahasiswa,
            Notifikasi::TIPE_PENGUMPULAN_BARU,
            'Pengumpulan berhasil',
            'Tugas "'.$tugas->judul.'" berhasil dikumpulkan pada '.now()->locale('id')->translatedFormat('d F Y, H:i').' WIB.',
            route('tugas.mhs.show', $tugas),
            'kumpul-berhasil:'.$tugas->id
        );

        return redirect()
            ->route('tugas.mhs.show', $tugas)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Mengunduh lampiran tugas dari Guru.
     */
    public function lampiran(Request $request, Tugas $tugas): BinaryFileResponse
    {
        $mahasiswa = $this->mahasiswa($request);

        abort_unless($tugas->isDipublikasikan(), 404);
        abort_unless($tugas->kelompok === $mahasiswa->kelompokPembelajaran(), 404);
        abort_unless(
            $tugas->punyaLampiran() && Storage::disk('local')->exists($tugas->lampiran_path),
            404
        );

        return response()->file(Storage::disk('local')->path($tugas->lampiran_path));
    }

    /**
     * Mengunduh berkas pengumpulan milik mahasiswa sendiri.
     */
    public function berkas(Request $request, Tugas $tugas): BinaryFileResponse
    {
        $mahasiswa = $this->mahasiswa($request);

        abort_unless($tugas->isDipublikasikan(), 404);
        abort_unless($tugas->kelompok === $mahasiswa->kelompokPembelajaran(), 404);

        $pengumpulan = $tugas->pengumpulan()->where('user_id', $mahasiswa->id)->first();

        abort_unless(
            $pengumpulan && filled($pengumpulan->file_path) && Storage::disk('local')->exists($pengumpulan->file_path),
            404
        );

        return response()->file(Storage::disk('local')->path($pengumpulan->file_path));
    }

    /**
     * Memastikan pengguna adalah mahasiswa.
     */
    private function mahasiswa(Request $request): User
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));

        abort_unless($user && $role === 'mahasiswa', 403);

        return $user;
    }
}
