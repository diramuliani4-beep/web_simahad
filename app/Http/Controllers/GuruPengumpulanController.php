<?php

namespace App\Http\Controllers;

use App\Models\JadwalPembelajaran;
use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\RiwayatNilaiTugas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuruPengumpulanController extends Controller
{
    /**
     * Daftar pengumpulan tugas dari mahasiswa pada kelompok tujuan.
     */
    public function index(Request $request, Tugas $tugas): View
    {
        $guru = $this->guru($request);
        $this->pastikanMilikGuru($tugas, $guru);

        $filter = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(array_keys(Tugas::statusPengumpulanPilihan()))],
        ]);

        $pengumpulan = $tugas->pengumpulan()
            ->with(['mahasiswa', 'riwayatNilai'])
            ->get()
            ->keyBy('user_id');

        $baris = $tugas->anggotaKelompok()
            ->map(function (User $mahasiswa) use ($tugas, $pengumpulan) {
                $pengumpulanMahasiswa = $pengumpulan->get($mahasiswa->id);

                return [
                    'mahasiswa' => $mahasiswa,
                    'pengumpulan' => $pengumpulanMahasiswa,
                    'status' => $tugas->statusUntuk($pengumpulanMahasiswa),
                ];
            })
            ->when(filled($filter['cari'] ?? null), function ($baris) use ($filter) {
                $kataKunci = mb_strtolower(trim($filter['cari']));

                return $baris->filter(fn (array $item) => str_contains(
                    mb_strtolower($item['mahasiswa']->name.' '.($item['mahasiswa']->nim ?? '')),
                    $kataKunci
                ));
            })
            ->when(filled($filter['status'] ?? null), fn ($baris) => $baris->where('status.status', $filter['status']))
            ->values();

        $statistik = [
            'mahasiswa' => $tugas->anggotaKelompok()->count(),
            'terkumpul' => $pengumpulan->count(),
            'dinilai' => $pengumpulan->whereNotNull('nilai')->count(),
            'belum' => max(0, $tugas->anggotaKelompok()->count() - $pengumpulan->count()),
        ];

        return view('guru.pengumpulan_tugas', [
            'tugas' => $tugas,
            'baris' => $baris,
            'filter' => $filter,
            'statistik' => $statistik,
            'statusPilihan' => Tugas::statusPengumpulanPilihan(),
        ]);
    }

    /**
     * Mengunduh berkas pengumpulan mahasiswa.
     */
    public function berkas(Request $request, Tugas $tugas, PengumpulanTugas $pengumpulan): BinaryFileResponse
    {
        $guru = $this->guru($request);
        $this->pastikanMilikGuru($tugas, $guru);

        abort_unless($pengumpulan->tugas_id === $tugas->id, 404);
        $this->pastikanBerkasAda($pengumpulan);

        return response()->file(Storage::disk('local')->path($pengumpulan->file_path));
    }

    /**
     * Memberikan atau memperbarui nilai 0-100 beserta komentar Guru.
     */
    public function nilai(Request $request, Tugas $tugas, PengumpulanTugas $pengumpulan): RedirectResponse
    {
        $guru = $this->guru($request);
        $this->pastikanMilikGuru($tugas, $guru);

        abort_unless($pengumpulan->tugas_id === $tugas->id, 404);

        $data = $request->validate([
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
            'komentar' => ['nullable', 'string', 'max:2000'],
        ], [
            'nilai.required' => 'Nilai wajib diisi.',
            'nilai.integer' => 'Nilai harus berupa angka bulat.',
            'nilai.min' => 'Nilai tidak boleh kurang dari 0.',
            'nilai.max' => 'Nilai tidak boleh lebih dari 100.',
            'komentar.max' => 'Komentar maksimal 2000 karakter.',
        ]);

        $nilaiLama = $pengumpulan->nilai;
        $komentarLama = $pengumpulan->komentar_guru;
        $komentarBaru = $data['komentar'] ?? null;

        if ($nilaiLama !== $data['nilai'] || $komentarLama !== $komentarBaru) {
            RiwayatNilaiTugas::create([
                'pengumpulan_tugas_id' => $pengumpulan->id,
                'nilai_lama' => $nilaiLama,
                'nilai_baru' => $data['nilai'],
                'komentar_lama' => $komentarLama,
                'komentar_baru' => $komentarBaru,
                'diubah_oleh' => $guru->id,
                'nama_pengubah' => $guru->name,
            ]);
        }

        $pengumpulan->nilai = $data['nilai'];
        $pengumpulan->komentar_guru = $komentarBaru;
        $pengumpulan->dinilai_oleh = $guru->id;
        $pengumpulan->dinilai_pada = now();
        $pengumpulan->save();

        return back()->with(
            'success',
            'Nilai '.$data['nilai'].' untuk '.$pengumpulan->mahasiswa?->name.' berhasil disimpan.'
        );
    }

    /**
     * Memublikasikan nilai agar terlihat mahasiswa.
     */
    public function publikasiNilai(Request $request, Tugas $tugas, PengumpulanTugas $pengumpulan): RedirectResponse
    {
        $guru = $this->guru($request);
        $this->pastikanMilikGuru($tugas, $guru);

        abort_unless($pengumpulan->tugas_id === $tugas->id, 404);
        abort_if($pengumpulan->nilai === null, 422, 'Nilai belum diisi.');

        $pengumpulan->nilai_dipublikasikan_pada = $pengumpulan->nilai_dipublikasikan_pada ?? now();
        $pengumpulan->save();

        Notifikasi::kirim(
            $pengumpulan->user_id,
            Notifikasi::TIPE_NILAI_DIPUBLIKASIKAN,
            'Nilai dipublikasikan: '.$tugas->judul,
            'Nilai '.$pengumpulan->nilai.' untuk tugas '.$tugas->judul.' sudah dipublikasikan.',
            route('tugas.mhs.show', $tugas),
            'nilai:'.$pengumpulan->id
        );

        return back()->with('success', 'Nilai berhasil dipublikasikan untuk mahasiswa.');
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
     * Menolak akses tugas milik Guru lain.
     */
    private function pastikanMilikGuru(Tugas $tugas, User $guru): void
    {
        $milikGuru = $tugas->user_id === $guru->id
            || JadwalPembelajaran::kunciPenugasan($guru)->contains(
                $tugas->mata_pelajaran_id.'|'.$tugas->kelompok
            );

        abort_unless($milikGuru, 403);
    }

    private function pastikanBerkasAda(PengumpulanTugas $pengumpulan): void
    {
        abort_unless(
            filled($pengumpulan->file_path) && Storage::disk('local')->exists($pengumpulan->file_path),
            404
        );
    }
}
