<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPembelajaran;
use App\Models\ActivityLog;
use App\Models\JadwalPembelajaran;
use App\Models\Notifikasi;
use App\Models\PertemuanPembelajaran;
use App\Models\Tugas;
use App\Services\AuditTrailService;
use App\Services\PembelajaranGuruService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Modul Pembelajaran Guru: memilih mata pelajaran, melihat progres 1-16,
 * memulai pertemuan, mengisi absensi, mengunggah materi dan tugas, lalu
 * menyelesaikan pertemuan. Seluruh data memakai tabel pembelajaran yang
 * sudah ada sehingga monitoring Musyrifah membaca sumber yang sama.
 */
class GuruPembelajaranController extends Controller
{
    public function __construct(
        private readonly PembelajaranGuruService $layanan,
        private readonly AuditTrailService $audit,
    ) {}

    /** Daftar penugasan Guru beserta progres 16 pertemuan. */
    public function index(Request $request): View
    {
        $guru = $this->layanan->guru($request);
        $penugasan = $this->layanan->penugasan($guru);

        $filter = $request->validate([
            'mata_pelajaran' => ['nullable', 'integer'],
            'kelompok' => ['nullable', 'string', 'max:60'],
            'periode' => ['nullable', 'string', 'max:100'],
        ]);

        $adaFilter = filled($filter['mata_pelajaran'] ?? null)
            || filled($filter['kelompok'] ?? null)
            || filled($filter['periode'] ?? null);

        $jadwal = $this->layanan->pilih(
            $penugasan,
            isset($filter['mata_pelajaran']) ? (int) $filter['mata_pelajaran'] : null,
            $filter['kelompok'] ?? null,
            $filter['periode'] ?? null,
        );

        // Filter yang tidak sesuai penugasan tidak boleh membuka data Guru lain.
        abort_if($adaFilter && ! $jadwal instanceof JadwalPembelajaran, 403);

        $progres = $jadwal instanceof JadwalPembelajaran ? $this->layanan->progres($jadwal) : null;

        $ringkasanPenugasan = $penugasan->map(function (JadwalPembelajaran $item) use ($jadwal) {
            $progresItem = $this->layanan->progres($item);

            return [
                'jadwal' => $item,
                'periode' => $this->layanan->periode($item),
                'selesai' => $progresItem['selesai'],
                'berlangsung' => $progresItem['berlangsung'],
                'berikutnya' => $progresItem['berikutnya'],
                'persentase' => $progresItem['persentase'],
                'aktif' => $jadwal instanceof JadwalPembelajaran && $jadwal->id === $item->id,
            ];
        });

        return view('guru.pembelajaran', [
            'guru' => $guru,
            'penugasan' => $penugasan,
            'ringkasanPenugasan' => $ringkasanPenugasan,
            'jadwal' => $jadwal,
            'progres' => $progres,
            'periodeAktif' => $jadwal instanceof JadwalPembelajaran ? $this->layanan->periode($jadwal) : null,
            'filter' => $filter,
            'pilihanMapel' => $this->layanan->pilihanMataPelajaran($penugasan),
            'pilihanKelompok' => $this->layanan->pilihanKelompok($penugasan),
            'pilihanPeriode' => $this->layanan->pilihanPeriode($penugasan),
            'targetPertemuan' => PembelajaranGuruService::TARGET_PERTEMUAN,
        ]);
    }

    /** Memulai pertemuan berikutnya (atau membuka pertemuan yang sedang berlangsung). */
    public function mulai(Request $request): RedirectResponse
    {
        $guru = $this->layanan->guru($request);

        $data = $request->validate([
            'mata_pelajaran_id' => ['required', 'integer'],
            'kelompok' => ['required', 'string', 'max:60'],
            'periode' => ['required', 'string', 'max:100'],
        ], [
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'kelompok.required' => 'Kelompok belajar wajib dipilih.',
            'periode.required' => 'Periode pembelajaran wajib dipilih.',
        ]);

        $jadwal = $this->layanan->verifikasiPenugasan(
            $guru,
            (int) $data['mata_pelajaran_id'],
            $data['kelompok'],
            $data['periode'],
        );

        $progres = $this->layanan->progres($jadwal);

        if ($progres['berlangsung'] !== null) {
            return redirect()
                ->route('guru.pembelajaran', $this->parameterJadwal($jadwal))
                ->with('info', 'Pertemuan '.$progres['berlangsung'].' masih berlangsung. Selesaikan terlebih dahulu.');
        }

        if ($progres['berikutnya'] === null) {
            return redirect()
                ->route('guru.pembelajaran', $this->parameterJadwal($jadwal))
                ->with('error', 'Seluruh 16 pertemuan mata pelajaran ini sudah selesai.');
        }

        $nomor = $progres['berikutnya'];

        $pertemuan = PertemuanPembelajaran::query()->create([
            'jadwal_id' => $jadwal->id,
            'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
            'guru_id' => $guru->id,
            'kelompok' => $jadwal->kelompok,
            'periode' => $this->layanan->periode($jadwal),
            'pertemuan_ke' => $nomor,
            'tanggal' => now()->toDateString(),
            'status' => PertemuanPembelajaran::STATUS_BERLANGSUNG,
            'dimulai_pada' => now(),
        ]);

        $this->audit->catat(
            'pembelajaran',
            'pertemuan_dimulai',
            $pertemuan->label(),
            (int) $pertemuan->getKey(),
            'Guru memulai pertemuan '.$nomor.' pada '.$pertemuan->tanggalTeks().'.',
            $guru,
            [],
            $pertemuan->getAttributes(),
        );

        return redirect()
            ->route('guru.pembelajaran.pertemuan', $pertemuan)
            ->with('success', 'Pertemuan '.$nomor.' dimulai. Isi absensi, materi, dan tugas, lalu selesaikan pertemuan.');
    }

    /** Detail pertemuan: absensi, materi, tugas, dan riwayat. */
    public function pertemuan(Request $request, PertemuanPembelajaran $pertemuan): View
    {
        $guru = $this->layanan->guru($request);
        $this->layanan->pastikanMilikGuru($guru, $pertemuan);

        $pertemuan->load(['mataPelajaran', 'jadwal']);

        $mahasiswa = $this->layanan->mahasiswa($pertemuan->kelompok);
        $absensi = $pertemuan->absensis()->get()->keyBy('user_id');
        $tugas = $pertemuan->tugas()
            ->withCount([
                'pengumpulan',
                'pengumpulan as pengumpulan_dinilai_count' => fn ($query) => $query->whereNotNull('nilai'),
            ])
            ->orderByDesc('id')
            ->get();

        $riwayat = ActivityLog::query()
            ->where('modul', 'pembelajaran')
            ->where('objek_id', $pertemuan->getKey())
            ->latest('id')
            ->limit(8)
            ->get();

        return view('guru.pertemuan', [
            'guru' => $guru,
            'pertemuan' => $pertemuan,
            'mahasiswa' => $mahasiswa,
            'absensi' => $absensi,
            'tugas' => $tugas,
            'ringkasanAbsensi' => $pertemuan->ringkasanAbsensi(),
            'statusAbsensi' => PertemuanPembelajaran::STATUS_ABSENSI,
            'riwayat' => $riwayat,
            'sudahSelesai' => $pertemuan->sudahSelesai(),
            'statusTugasPilihan' => Tugas::statusPilihan(),
        ]);
    }

    /** Menyimpan absensi Hadir/Izin/Sakit/Alpa untuk seluruh mahasiswa kelompok. */
    public function simpanAbsensi(Request $request, PertemuanPembelajaran $pertemuan): RedirectResponse
    {
        $guru = $this->layanan->guru($request);
        $this->layanan->pastikanMilikGuru($guru, $pertemuan);

        $data = $request->validate([
            'absensi' => ['required', 'array', 'min:1'],
            'absensi.*.status' => ['required', 'string', Rule::in(array_keys(PertemuanPembelajaran::STATUS_ABSENSI))],
            'absensi.*.catatan' => ['nullable', 'string', 'max:300'],
        ], [
            'absensi.required' => 'Absensi minimal harus berisi satu mahasiswa.',
            'absensi.*.status.required' => 'Status kehadiran setiap mahasiswa wajib dipilih.',
            'absensi.*.status.in' => 'Status absensi harus Hadir, Izin, Sakit, atau Alpa.',
        ]);

        $anggota = $this->layanan->mahasiswa($pertemuan->kelompok)->keyBy('id');
        abort_if($anggota->isEmpty(), 422, 'Belum ada mahasiswa terdaftar pada kelompok ini.');

        $sebelumnya = $pertemuan->absensis()->get()->keyBy('user_id');
        $perubahan = [];
        $sebelumData = [];
        $sesudahData = [];
        $adakahKoreksi = false;

        foreach ($data['absensi'] as $userId => $baris) {
            if (! $anggota->has($userId)) {
                continue;
            }

            $statusBaru = $baris['status'];
            $catatanBaru = $baris['catatan'] ?? null;
            $lama = $sebelumnya->get($userId);

            if ($lama && ($lama->status !== $statusBaru || $lama->catatan !== $catatanBaru)) {
                $adakahKoreksi = true;
                $sebelumData[$anggota[$userId]->name] = $lama->status.($lama->catatan ? ' · '.$lama->catatan : '');
                $sesudahData[$anggota[$userId]->name] = $statusBaru.($catatanBaru ? ' · '.$catatanBaru : '');
            }

            AbsensiPembelajaran::query()->updateOrCreate(
                [
                    'pertemuan_id' => $pertemuan->id,
                    'user_id' => $userId,
                ],
                [
                    'mata_pelajaran_id' => $pertemuan->mata_pelajaran_id,
                    'jadwal_id' => $pertemuan->jadwal_id,
                    'kelompok' => $pertemuan->kelompok,
                    'pertemuan_ke' => $pertemuan->pertemuan_ke,
                    'tanggal' => $pertemuan->tanggal,
                    'status' => $statusBaru,
                    'catatan' => $catatanBaru,
                ],
            );

            $perubahan[$anggota[$userId]->name] = $statusBaru;
        }

        abort_if($perubahan === [], 422, 'Tidak ada absensi yang dapat disimpan.');

        $this->audit->catat(
            'pembelajaran',
            $adakahKoreksi ? 'absensi_dikoreksi' : 'absensi_disimpan',
            $pertemuan->label(),
            (int) $pertemuan->getKey(),
            $adakahKoreksi
                ? 'Guru mengoreksi absensi pertemuan '.$pertemuan->pertemuan_ke.'.'
                : 'Guru mengisi absensi pertemuan '.$pertemuan->pertemuan_ke.'.',
            $guru,
            $sebelumData,
            $sesudahData ?: $perubahan,
        );

        return back()->with(
            'success',
            $adakahKoreksi
                ? 'Koreksi absensi tersimpan dan tercatat pada riwayat perubahan.'
                : 'Absensi pertemuan '.$pertemuan->pertemuan_ke.' berhasil disimpan.'
        );
    }

    /** Menyimpan materi pertemuan (teks dan/atau berkas). */
    public function unggahMateri(Request $request, PertemuanPembelajaran $pertemuan): RedirectResponse
    {
        $guru = $this->layanan->guru($request);
        $this->layanan->pastikanMilikGuru($guru, $pertemuan);

        $data = $request->validate([
            'materi' => ['nullable', 'string', 'max:5000'],
            'berkas' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png', 'max:10240'],
        ], [
            'berkas.mimes' => 'Materi harus berupa PDF, DOC, DOCX, PPT, PPTX, JPG, JPEG, atau PNG.',
            'berkas.max' => 'Ukuran materi maksimal 10 MB.',
            'materi.max' => 'Ringkasan materi maksimal 5000 karakter.',
        ]);

        abort_if(blank($data['materi'] ?? null) && ! $request->hasFile('berkas'), 422, 'Isi ringkasan materi atau pilih berkas materi.');

        $sebelum = $pertemuan->only(['materi', 'materi_path', 'materi_nama']);

        if ($request->hasFile('berkas')) {
            $this->hapusBerkas($pertemuan->materi_path);

            $berkas = $request->file('berkas');
            $pertemuan->materi_path = $berkas->store('pertemuan/materi', 'local');
            $pertemuan->materi_nama = $berkas->getClientOriginalName();
        }

        if (filled($data['materi'] ?? null)) {
            $pertemuan->materi = $data['materi'];
        }

        $pertemuan->save();

        $this->audit->catat(
            'pembelajaran',
            'materi_diunggah',
            $pertemuan->label(),
            (int) $pertemuan->getKey(),
            'Guru menyimpan materi pertemuan '.$pertemuan->pertemuan_ke.'.',
            $guru,
            $sebelum,
            $pertemuan->only(['materi', 'materi_path', 'materi_nama']),
        );

        return back()->with('success', 'Materi pertemuan '.$pertemuan->pertemuan_ke.' berhasil disimpan.');
    }

    /** Mengunduh berkas materi pertemuan. */
    public function unduhMateri(Request $request, PertemuanPembelajaran $pertemuan): BinaryFileResponse
    {
        $guru = $this->layanan->guru($request);
        $this->layanan->pastikanMilikGuru($guru, $pertemuan);

        abort_unless($pertemuan->punyaBerkasMateri(), 404);

        return response()->file(Storage::disk('local')->path($pertemuan->materi_path));
    }

    /** Menambahkan tugas pada pertemuan yang sedang dibuka. */
    public function simpanTugas(Request $request, PertemuanPembelajaran $pertemuan): RedirectResponse
    {
        $guru = $this->layanan->guru($request);
        $this->layanan->pastikanMilikGuru($guru, $pertemuan);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:180'],
            'deskripsi' => ['required', 'string', 'min:10', 'max:20000'],
            'batas_pengumpulan' => ['required', 'date', 'after_or_equal:'.now()->toDateString()],
            'status' => ['required', 'string', Rule::in(array_keys(Tugas::statusPilihan()))],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'judul.required' => 'Judul tugas wajib diisi.',
            'deskripsi.required' => 'Deskripsi tugas wajib diisi.',
            'deskripsi.min' => 'Deskripsi tugas minimal 10 karakter.',
            'batas_pengumpulan.after_or_equal' => 'Batas pengumpulan tidak boleh sebelum hari ini.',
            'lampiran.mimes' => 'Lampiran harus berupa PDF, JPG, JPEG, atau PNG.',
            'lampiran.max' => 'Ukuran lampiran maksimal 5 MB.',
        ]);

        if (strlen(trim((string) $data['batas_pengumpulan'])) <= 10) {
            $data['batas_pengumpulan'] = $data['batas_pengumpulan'].' 23:59:00';
        }

        $tugas = new Tugas($data);
        $tugas->user_id = $guru->id;
        $tugas->nama_guru = $guru->name;
        $tugas->mata_pelajaran_id = $pertemuan->mata_pelajaran_id;
        $tugas->nama_mata_pelajaran = $pertemuan->mataPelajaran?->nama;
        $tugas->kelompok = $pertemuan->kelompok;
        $tugas->pertemuan_id = $pertemuan->id;
        $tugas->tanggal_mulai = $pertemuan->tanggal;

        if ($request->hasFile('lampiran')) {
            $berkas = $request->file('lampiran');
            $tugas->lampiran_path = $berkas->store('tugas/lampiran', 'local');
            $tugas->lampiran_nama = $berkas->getClientOriginalName();
        }

        if ($tugas->status === Tugas::STATUS_DIPUBLIKASIKAN) {
            $tugas->dipublikasikan_pada = now();
        }

        $tugas->save();

        if ($tugas->isDipublikasikan()) {
            foreach ($this->layanan->mahasiswa($tugas->kelompok) as $mahasiswa) {
                Notifikasi::kirim(
                    $mahasiswa,
                    Notifikasi::TIPE_TUGAS_BARU,
                    'Tugas baru: '.$tugas->judul,
                    'Mata pelajaran '.$tugas->namaMataPelajaran().' pertemuan '.$pertemuan->pertemuan_ke
                        .'. Batas pengumpulan '.$tugas->batasTeks().'.',
                    route('tugas.mhs.show', $tugas),
                    'tugas:'.$tugas->id.':baru',
                );
            }
        }

        $this->audit->catat(
            'pembelajaran',
            'tugas_ditambahkan',
            $tugas->judul,
            (int) $tugas->getKey(),
            'Guru menambahkan tugas pada pertemuan '.$pertemuan->pertemuan_ke.'.',
            $guru,
            [],
            $tugas->getAttributes(),
        );

        return back()->with('success', 'Tugas "'.$tugas->judul.'" berhasil ditambahkan pada pertemuan ini.');
    }

    /** Menandai pertemuan selesai setelah absensi terisi. */
    public function selesaikan(Request $request, PertemuanPembelajaran $pertemuan): RedirectResponse
    {
        $guru = $this->layanan->guru($request);
        $this->layanan->pastikanMilikGuru($guru, $pertemuan);

        if ($pertemuan->sudahSelesai()) {
            return back()->with('info', 'Pertemuan '.$pertemuan->pertemuan_ke.' sudah selesai.');
        }

        $ringkasan = $pertemuan->ringkasanAbsensi();

        if ($ringkasan['total'] === 0) {
            return back()->with('error', 'Isi absensi minimal satu mahasiswa sebelum menyelesaikan pertemuan.');
        }

        $sebelum = $pertemuan->only(['status', 'selesai_pada']);
        $pertemuan->status = PertemuanPembelajaran::STATUS_SELESAI;
        $pertemuan->selesai_pada = now();
        $pertemuan->save();

        $this->audit->catat(
            'pembelajaran',
            'pertemuan_selesai',
            $pertemuan->label(),
            (int) $pertemuan->getKey(),
            'Guru menyelesaikan pertemuan '.$pertemuan->pertemuan_ke.' dengan '.$ringkasan['hadir'].' mahasiswa hadir.',
            $guru,
            $sebelum,
            $pertemuan->only(['status', 'selesai_pada']),
        );

        return redirect()
            ->route('guru.pembelajaran', $this->parameterJadwal($pertemuan))
            ->with('success', 'Pertemuan '.$pertemuan->pertemuan_ke.' selesai. Pertemuan tetap dapat dibuka untuk melihat detail dan riwayat absensi.');
    }

    /**
     * Parameter URL penugasan agar Guru kembali ke mata pelajaran yang sama.
     *
     * @return array<string, mixed>
     */
    private function parameterJadwal(JadwalPembelajaran|PertemuanPembelajaran $sumber): array
    {
        return [
            'mata_pelajaran' => $sumber->mata_pelajaran_id,
            'kelompok' => $sumber->kelompok,
            'periode' => $sumber instanceof PertemuanPembelajaran
                ? $sumber->periode
                : $this->layanan->periode($sumber),
        ];
    }

    private function hapusBerkas(?string $path): void
    {
        if (filled($path) && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
