<?php

namespace App\Http\Controllers;

use App\Models\JadwalPembelajaran;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuruTugasController extends Controller
{
    /**
     * Daftar tugas milik Guru beserta rekap pengumpulan.
     */
    public function index(Request $request): View
    {
        $guru = $this->guru($request);

        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(array_keys(Tugas::statusPilihan()))],
        ]);

        $daftarTugas = Tugas::query()
            ->with(['mataPelajaran'])
            ->withCount([
                'pengumpulan',
                'pengumpulan as pengumpulan_dinilai_count' => fn (Builder $query) => $query->whereNotNull('nilai'),
            ])
            ->cari($filter['cari'] ?? null)
            ->when(filled($filter['status'] ?? null), fn (Builder $query) => $query->where('status', $filter['status']))
            ->orderByDesc('batas_pengumpulan')
            ->orderByDesc('id');

        $this->batasiTugasGuru($daftarTugas, $guru);

        $daftarTugas = $daftarTugas->get();

        $statistik = [
            'total' => $daftarTugas->count(),
            'dipublikasikan' => $daftarTugas->where('status', Tugas::STATUS_DIPUBLIKASIKAN)->count(),
            'draft' => $daftarTugas->where('status', Tugas::STATUS_DRAFT)->count(),
            'pengumpulan' => $daftarTugas->sum('pengumpulan_count'),
        ];

        return view('guru.tugas', [
            'guru' => $guru,
            'daftarTugas' => $daftarTugas,
            'filter' => $filter,
            'statistik' => $statistik,
            'statusPilihan' => Tugas::statusPilihan(),
        ]);
    }

    /**
     * Formulir pembuatan tugas baru.
     */
    public function create(Request $request): View
    {
        $guru = $this->guru($request);

        return view('guru.form_tugas', [
            'tugas' => null,
            'mode' => 'tambah',
            'penugasan' => $this->penugasanGuru($guru),
            'statusPilihan' => Tugas::statusPilihan(),
        ]);
    }

    /**
     * Menyimpan tugas baru sesuai penugasan Guru.
     */
    public function store(Request $request): RedirectResponse
    {
        $guru = $this->guru($request);

        $data = $this->validasiTugas($request, $guru);

        $tugas = new Tugas($data);
        $tugas->user_id = $guru->id;
        $tugas->nama_guru = $guru->name;
        $tugas->nama_mata_pelajaran = MataPelajaran::query()->find($data['mata_pelajaran_id'])?->nama;

        if ($request->hasFile('lampiran')) {
            $berkas = $request->file('lampiran');
            $tugas->lampiran_path = $berkas->store('tugas/lampiran', 'local');
            $tugas->lampiran_nama = $berkas->getClientOriginalName();
        }

        if ($tugas->status === Tugas::STATUS_DIPUBLIKASIKAN && ! $tugas->dipublikasikan_pada) {
            $tugas->dipublikasikan_pada = now();
        }

        $tugas->save();

        $this->kirimNotifikasiTugasBaru($tugas);

        return redirect()
            ->route('guru.tugas.index')
            ->with('success', 'Tugas "'.$tugas->judul.'" berhasil disimpan.');
    }

    /**
     * Formulir penyuntingan tugas milik Guru.
     */
    public function edit(Request $request, Tugas $tugas): View
    {
        $guru = $this->guru($request);
        $this->pastikanMilikGuru($tugas, $guru);

        return view('guru.form_tugas', [
            'tugas' => $tugas,
            'mode' => 'edit',
            'penugasan' => $this->penugasanGuru($guru),
            'statusPilihan' => Tugas::statusPilihan(),
        ]);
    }

    /**
     * Memperbarui tugas tanpa membuat data baru.
     */
    public function update(Request $request, Tugas $tugas): RedirectResponse
    {
        $guru = $this->guru($request);
        $this->pastikanMilikGuru($tugas, $guru);

        $data = $this->validasiTugas($request, $guru);

        $tugas->fill($data);
        $tugas->nama_guru = $tugas->nama_guru ?: $guru->name;
        $tugas->nama_mata_pelajaran = MataPelajaran::query()->find($data['mata_pelajaran_id'])?->nama;

        if ($request->hasFile('lampiran')) {
            $this->hapusBerkas($tugas->lampiran_path);

            $berkas = $request->file('lampiran');
            $tugas->lampiran_path = $berkas->store('tugas/lampiran', 'local');
            $tugas->lampiran_nama = $berkas->getClientOriginalName();
        }

        if ($tugas->status === Tugas::STATUS_DIPUBLIKASIKAN && ! $tugas->dipublikasikan_pada) {
            $tugas->dipublikasikan_pada = now();
        }

        if ($tugas->status === Tugas::STATUS_DRAFT) {
            $tugas->dipublikasikan_pada = null;
        }

        $tugas->save();

        $this->kirimNotifikasiTugasBaru($tugas);

        return redirect()
            ->route('guru.tugas.index')
            ->with('success', 'Tugas "'.$tugas->judul.'" berhasil diperbarui.');
    }

    /**
     * Menghapus tugas beserta berkas lampiran dan pengumpulannya.
     */
    public function destroy(Request $request, Tugas $tugas): RedirectResponse
    {
        $guru = $this->guru($request);
        $this->pastikanMilikGuru($tugas, $guru);

        $judul = $tugas->judul;

        foreach ($tugas->pengumpulan as $pengumpulan) {
            $this->hapusBerkas($pengumpulan->file_path);
        }

        $this->hapusBerkas($tugas->lampiran_path);
        $tugas->delete();

        return redirect()
            ->route('guru.tugas.index')
            ->with('success', 'Tugas "'.$judul.'" berhasil dihapus.');
    }

    /**
     * Mengubah status Draft / Dipublikasikan.
     */
    public function ubahStatus(Request $request, Tugas $tugas): RedirectResponse
    {
        $guru = $this->guru($request);
        $this->pastikanMilikGuru($tugas, $guru);

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys(Tugas::statusPilihan()))],
        ]);

        $tugas->status = $data['status'];
        $tugas->dipublikasikan_pada = $data['status'] === Tugas::STATUS_DIPUBLIKASIKAN
            ? ($tugas->dipublikasikan_pada ?? now())
            : null;
        $tugas->save();

        $this->kirimNotifikasiTugasBaru($tugas);

        return back()->with(
            'success',
            'Status tugas "'.$tugas->judul.'" diubah menjadi '.$tugas->labelStatus().'.'
        );
    }

    /**
     * Mengunduh lampiran tugas yang diberikan Guru.
     */
    public function lampiran(Request $request, Tugas $tugas): BinaryFileResponse
    {
        $guru = $this->guru($request);
        $this->pastikanMilikGuru($tugas, $guru);

        abort_unless($tugas->punyaLampiran() && Storage::disk('local')->exists($tugas->lampiran_path), 404);

        return response()->file(Storage::disk('local')->path($tugas->lampiran_path));
    }

    /**
     * Memastikan pengguna adalah Guru.
     */
    private function guru(Request $request): User
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));

        abort_unless($user && $role === 'guru', 403);

        return $user;
    }

    /**
     * Penugasan (mata pelajaran + kelompok) milik Guru.
     *
     * @return Collection<int, array{mata_pelajaran_id: int|null, nama_mata_pelajaran: string, kelompok: string}>
     */
    private function penugasanGuru(User $guru): Collection
    {
        return JadwalPembelajaran::penugasanUntuk($guru);
    }

    /**
     * Membatasi query tugas pada tugas milik Guru.
     *
     * @param  Builder<Tugas>  $query
     */
    private function batasiTugasGuru(Builder $query, User $guru): void
    {
        $query->where(function (Builder $inner) use ($guru) {
            $inner->where('user_id', $guru->id);

            foreach ($this->penugasanGuru($guru) as $penugasan) {
                $inner->orWhere(function (Builder $w) use ($penugasan) {
                    $w->where('kelompok', $penugasan['kelompok'])
                        ->where('mata_pelajaran_id', $penugasan['mata_pelajaran_id']);
                });
            }
        });
    }

    /**
     * Menolak akses tugas milik Guru lain di luar penugasannya.
     */
    private function pastikanMilikGuru(Tugas $tugas, User $guru): void
    {
        $kunci = $tugas->mata_pelajaran_id.'|'.$tugas->kelompok;

        $milikGuru = $tugas->user_id === $guru->id
            || $this->penugasanGuru($guru)->contains(
                fn (array $item) => $kunci === $item['mata_pelajaran_id'].'|'.$item['kelompok']
            );

        abort_unless($milikGuru, 403);
    }

    /**
     * Validasi form tugas sekaligus memastikan penugasan Guru.
     *
     * @return array<string, mixed>
     */
    private function validasiTugas(Request $request, User $guru): array
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:180'],
            'mata_pelajaran_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'kelompok' => ['required', 'string', 'max:60'],
            'deskripsi' => ['required', 'string', 'min:10', 'max:20000'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'tanggal_mulai' => ['required', 'date'],
            'batas_pengumpulan' => ['required', 'date', 'after:tanggal_mulai'],
            'status' => ['required', 'string', Rule::in(array_keys(Tugas::statusPilihan()))],
        ], [
            'judul.required' => 'Judul tugas wajib diisi.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'kelompok.required' => 'Kelompok tujuan wajib dipilih.',
            'deskripsi.required' => 'Deskripsi tugas wajib diisi.',
            'deskripsi.min' => 'Deskripsi tugas minimal 10 karakter.',
            'batas_pengumpulan.after' => 'Batas pengumpulan harus setelah tanggal mulai.',
            'lampiran.mimes' => 'Lampiran harus berupa PDF, JPG, JPEG, atau PNG.',
            'lampiran.max' => 'Ukuran lampiran maksimal 5 MB.',
        ]);

        $kunci = $data['mata_pelajaran_id'].'|'.$data['kelompok'];

        abort_unless(
            $this->penugasanGuru($guru)->contains(
                fn (array $item) => $kunci === $item['mata_pelajaran_id'].'|'.$item['kelompok']
            ),
            403,
            'Anda tidak ditugaskan pada mata pelajaran dan kelompok tersebut.'
        );

        // Bila hanya tanggal yang dikirim, batas pengumpulan berlaku sampai akhir hari.
        if (strlen(trim((string) $data['batas_pengumpulan'])) <= 10) {
            $data['batas_pengumpulan'] = $data['batas_pengumpulan'].' 23:59:00';
        }

        return $data;
    }

    /**
     * Notifikasi tugas baru untuk mahasiswa pada kelompok tujuan.
     */
    private function kirimNotifikasiTugasBaru(Tugas $tugas): void
    {
        if (! $tugas->isDipublikasikan()) {
            return;
        }

        foreach ($tugas->anggotaKelompok() as $mahasiswa) {
            Notifikasi::kirim(
                $mahasiswa,
                Notifikasi::TIPE_TUGAS_BARU,
                'Tugas baru: '.$tugas->judul,
                'Mata pelajaran '.$tugas->namaMataPelajaran().' untuk '.$tugas->kelompok
                    .'. Batas pengumpulan '.$tugas->batasTeks().'.',
                route('tugas.mhs.show', $tugas),
                'tugas:'.$tugas->id.':baru'
            );
        }
    }

    /** Menghapus berkas privat bila tersedia. */
    private function hapusBerkas(?string $path): void
    {
        if (filled($path) && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
