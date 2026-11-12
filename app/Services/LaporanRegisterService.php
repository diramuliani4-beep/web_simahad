<?php

namespace App\Services;

use App\Models\LaporanAudit;
use App\Models\LaporanMahad;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Pengelola register laporan Ma'had.
 *
 * Isi laporan selalu dibangkitkan dari data operasional oleh
 * LaporanMahadService; kelas ini hanya mengurus identitas laporan, periode,
 * sumber, status, versi, serta riwayat aktivitas yang tidak dapat diubah.
 */
class LaporanRegisterService
{
    /** Judul baku untuk setiap jenis laporan. */
    public const JUDUL = [
        'penghuni' => 'Laporan Penghuni',
        'kamar' => 'Laporan Kamar',
        'kehadiran' => 'Laporan Kehadiran',
        'pembayaran' => 'Laporan Pembayaran',
        'pendaftaran' => 'Laporan Pendaftaran',
        'pembelajaran' => 'Laporan Pembelajaran',
        'perizinan' => 'Laporan Perizinan',
    ];

    /** Jenis laporan rekap otomatis yang selalu tersedia. */
    public const JENIS_SISTEM = [
        'penghuni',
        'kamar',
        'pembelajaran',
        'pembayaran',
        'perizinan',
        'kehadiran',
        'pendaftaran',
    ];

    public function __construct(
        private readonly AuditTrailService $audit,
        private readonly LaporanMahadService $laporanMahad,
    ) {}

    public function labelJenis(string $jenis): string
    {
        return self::JUDUL[$jenis] ?? ucfirst(str_replace('_', ' ', $jenis));
    }

    /**
     * Memastikan rekap otomatis sistem tercatat untuk satu periode.
     *
     * Idempoten: pemanggilan berulang tidak membuat baris maupun audit ganda.
     *
     * @param  array<int, string>|null  $jenis
     */
    public function pastikanRekapSistem(?string $periode = null, ?array $jenis = null): int
    {
        $periode = $this->laporanMahad->normalisasiPeriode($periode);
        $daftarJenis = $jenis ?? self::JENIS_SISTEM;
        $dibuat = 0;

        foreach ($daftarJenis as $jenisLaporan) {
            if (! in_array($jenisLaporan, self::JENIS_SISTEM, true)) {
                continue;
            }

            $laporan = LaporanMahad::query()->firstOrCreate(
                [
                    'jenis' => $jenisLaporan,
                    'periode' => $periode,
                    'lantai' => null,
                    'sumber' => LaporanMahad::SUMBER_SISTEM,
                ],
                [
                    'judul' => $this->labelJenis($jenisLaporan),
                    'unit' => 'Seluruh Ma’had',
                    'status' => LaporanMahad::STATUS_DITERBITKAN,
                    'versi' => 1,
                    'pembuat_role' => 'sistem',
                    'diterbitkan_pada' => now(),
                ],
            );

            if ($laporan->wasRecentlyCreated) {
                $dibuat++;

                $this->catatAudit(
                    $laporan,
                    'dibuat',
                    'Rekap otomatis sistem dibuat untuk periode '.$laporan->labelPeriode().'.',
                );
            }
        }

        return $dibuat;
    }

    /**
     * Daftar laporan dengan filter jenis, periode, lantai, sumber, status.
     *
     * @param  array<string, mixed>  $filter
     * @return LengthAwarePaginator<int, LaporanMahad>
     */
    public function daftar(array $filter = [], int $perHalaman = 15): LengthAwarePaginator
    {
        $cari = trim((string) ($filter['cari'] ?? ''));

        return LaporanMahad::query()
            ->with([
                'pembuat' => fn ($query) => $query->select('id', 'name', 'role'),
                'penerbit' => fn ($query) => $query->select('id', 'name'),
            ])
            ->when(filled($filter['jenis'] ?? null), fn ($query) => $query->where('jenis', $filter['jenis']))
            ->when(filled($filter['periode'] ?? null), fn ($query) => $query->where('periode', $filter['periode']))
            ->when(filled($filter['lantai'] ?? null), fn ($query) => $query->where('lantai', (int) $filter['lantai']))
            ->when(filled($filter['sumber'] ?? null), fn ($query) => $query->where('sumber', $filter['sumber']))
            ->when(filled($filter['status'] ?? null), fn ($query) => $query->where('status', $filter['status']))
            ->when($cari !== '', function ($query) use ($cari): void {
                $kataKunci = '%'.str_replace(['%', '_'], ['\%', '\_'], $cari).'%';

                $query->where(function ($sub) use ($kataKunci): void {
                    $sub->where('judul', 'like', $kataKunci)
                        ->orWhere('pembuat_nama', 'like', $kataKunci)
                        ->orWhere('unit', 'like', $kataKunci);
                });
            })
            ->orderByDesc('periode')
            ->orderByDesc('id')
            ->paginate($perHalaman)
            ->withQueryString();
    }

    /**
     * Laporan buatan Musyrifah untuk lantai/unit tertentu.
     */
    public function buatLaporanMusyrifah(
        User $musyrifah,
        string $jenis,
        string $periode,
        ?int $lantai = null,
        ?string $unit = null,
        ?string $catatan = null,
    ): LaporanMahad {
        $periode = $this->laporanMahad->normalisasiPeriode($periode);

        $laporan = LaporanMahad::query()->create([
            'jenis' => $jenis,
            'judul' => $this->labelJenis($jenis),
            'periode' => $periode,
            'lantai' => $lantai,
            'unit' => $unit ?: ($lantai ? 'Lantai '.$lantai : 'Seluruh Ma’had'),
            'sumber' => LaporanMahad::SUMBER_MUSYRIFAH,
            'status' => LaporanMahad::STATUS_DRAFT,
            'versi' => 1,
            'dibuat_oleh' => $musyrifah->getKey(),
            'pembuat_nama' => $musyrifah->name,
            'pembuat_role' => strtolower((string) $musyrifah->role),
            'catatan' => $catatan,
        ]);

        $this->catatAudit($laporan, 'dibuat', 'Laporan dibuat oleh '.$musyrifah->name.'.', $musyrifah, [], [
            'status' => $laporan->status,
            'lantai' => $laporan->lantai,
            'unit' => $laporan->unit,
        ]);

        return $laporan;
    }

    /**
     * Menerbitkan laporan draft atau menandai pembaruan laporan terbit.
     */
    public function terbitkan(LaporanMahad $laporan, User $oleh, ?string $catatan = null): LaporanMahad
    {
        $sebelum = $this->ringkasanLaporan($laporan);
        $sudahTerbit = $laporan->status === LaporanMahad::STATUS_DITERBITKAN;

        $laporan->status = LaporanMahad::STATUS_DITERBITKAN;
        $laporan->diterbitkan_pada = $laporan->diterbitkan_pada ?? now();
        $laporan->diterbitkan_oleh = $oleh->getKey();
        $laporan->diarsipkan_pada = null;

        if ($sudahTerbit) {
            // Laporan terbit yang diperbarui menyimpan riwayat versi.
            $laporan->versi = (int) $laporan->versi + 1;
        }

        if (filled($catatan)) {
            $laporan->catatan = $catatan;
        }

        $laporan->save();

        $this->catatAudit(
            $laporan,
            $sudahTerbit ? 'diperbarui' : 'diterbitkan',
            $sudahTerbit
                ? 'Laporan terbit diperbarui menjadi versi '.$laporan->versi.'.'
                : 'Laporan diterbitkan.',
            $oleh,
            $sebelum,
            $this->ringkasanLaporan($laporan),
        );

        return $laporan;
    }

    public function arsipkan(LaporanMahad $laporan, User $oleh, ?string $catatan = null): LaporanMahad
    {
        $sebelum = $this->ringkasanLaporan($laporan);

        $laporan->status = LaporanMahad::STATUS_DIARSIPKAN;
        $laporan->diarsipkan_pada = now();

        if (filled($catatan)) {
            $laporan->catatan = $catatan;
        }

        $laporan->save();

        $this->catatAudit(
            $laporan,
            'diarsipkan',
            'Laporan diarsipkan.',
            $oleh,
            $sebelum,
            $this->ringkasanLaporan($laporan),
        );

        return $laporan;
    }

    /**
     * Riwayat aktivitas satu laporan (tidak dapat diubah).
     *
     * @return Collection<int, LaporanAudit>
     */
    public function riwayat(LaporanMahad $laporan)
    {
        return $laporan->audits()->with('pengguna:id,name,role')->get();
    }

    /**
     * @param  array<string, mixed>  $sebelum
     * @param  array<string, mixed>  $sesudah
     */
    public function catatAudit(
        LaporanMahad $laporan,
        string $aksi,
        ?string $keterangan = null,
        ?User $oleh = null,
        array $sebelum = [],
        array $sesudah = [],
    ): LaporanAudit {
        $oleh ??= auth()->user();

        return LaporanAudit::query()->create([
            'laporan_mahad_id' => $laporan->getKey(),
            'aksi' => $aksi,
            'jenis_laporan' => $laporan->jenis,
            'periode' => $laporan->periode,
            'lantai' => $laporan->lantai,
            'versi' => (int) $laporan->versi,
            'user_id' => $oleh?->getKey(),
            'pengguna_nama' => $oleh?->name ?? ($laporan->pembuat_nama ?: 'Sistem'),
            'pengguna_role' => $oleh ? strtolower((string) $oleh->role) : 'sistem',
            'keterangan' => $keterangan,
            'data_sebelum' => $sebelum ?: null,
            'data_sesudah' => $sesudah ?: null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function ringkasanLaporan(LaporanMahad $laporan): array
    {
        return [
            'status' => $laporan->status,
            'versi' => (int) $laporan->versi,
            'diterbitkan_pada' => $laporan->diterbitkan_pada?->format('Y-m-d H:i:s'),
            'diarsipkan_pada' => $laporan->diarsipkan_pada?->format('Y-m-d H:i:s'),
        ];
    }
}
