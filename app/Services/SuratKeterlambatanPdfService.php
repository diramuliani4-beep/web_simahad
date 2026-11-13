<?php

namespace App\Services;

use App\Models\PengajuanKeterlambatanPembayaran;

/**
 * Membuat surat pengajuan keterlambatan pembayaran dalam format PDF.
 *
 * Penulisan berkas memakai LaporanPdfService agar tidak ada dependensi
 * tambahan dan seluruh dokumen SIMAHAD memakai mesin PDF yang sama.
 */
class SuratKeterlambatanPdfService
{
    public function __construct(private readonly LaporanPdfService $laporanPdf) {}

    public function render(PengajuanKeterlambatanPembayaran $pengajuan): string
    {
        $pengajuan->loadMissing(['user', 'pembayaran', 'pemroses']);

        $mahasiswa = $pengajuan->user;
        $tagihan = $pengajuan->pembayaran;

        $seksi = [
            [
                'judul' => 'Identitas Mahasiswa',
                'tabel' => [[
                    'kolom' => [
                        ['key' => 'label', 'label' => 'Keterangan'],
                        ['key' => 'nilai', 'label' => 'Data'],
                    ],
                    'baris' => [
                        ['label' => 'Nama', 'nilai' => $mahasiswa?->name ?? '-'],
                        ['label' => 'NIM', 'nilai' => $mahasiswa?->nim ?? '-'],
                        ['label' => 'Email', 'nilai' => $mahasiswa?->email ?? '-'],
                        ['label' => 'Nomor HP', 'nilai' => $mahasiswa?->no_hp ?? '-'],
                    ],
                ]],
            ],
            [
                'judul' => 'Rincian Tagihan',
                'tabel' => [[
                    'kolom' => [
                        ['key' => 'label', 'label' => 'Keterangan'],
                        ['key' => 'nilai', 'label' => 'Data'],
                    ],
                    'baris' => [
                        ['label' => 'Tahap', 'nilai' => $tagihan?->labelTahap() ?? '-'],
                        ['label' => 'Periode', 'nilai' => $tagihan?->periode ?? '-'],
                        ['label' => 'Nominal', 'nilai' => $tagihan?->nominal_rupiah ?? '-'],
                        ['label' => 'Jatuh Tempo', 'nilai' => $tagihan?->jatuhTempoTeks() ?? '-'],
                        ['label' => 'Status Tagihan', 'nilai' => $tagihan?->status_label ?? '-'],
                    ],
                ]],
            ],
            [
                'judul' => 'Isi Pengajuan',
                'teks' => [
                    'Alasan Keterlambatan' => $pengajuan->alasan,
                    'Rencana Pembayaran' => 'Mahasiswa berencana melakukan pembayaran pada '.$pengajuan->tanggalRencanaTeks().'.',
                ],
                'catatan' => [
                    'Surat ini dibuat otomatis oleh SIMAHAD dan tidak mengubah status tagihan menjadi Lunas.',
                    'Dana pembayaran ditransfer langsung ke rekening resmi kampus; SIMAHAD tidak menerima atau mengelola dana.',
                ],
            ],
            [
                'judul' => 'Pemeriksaan Musyrifah',
                'ringkasan' => [
                    ['label' => 'Status Pengajuan', 'nilai' => $pengajuan->statusLabel()],
                    ['label' => 'Diproses Oleh', 'nilai' => $pengajuan->pemroses?->name ?? 'Belum diproses'],
                    ['label' => 'Tanggal Proses', 'nilai' => $pengajuan->diproses_pada
                        ? $pengajuan->diproses_pada->locale('id')->translatedFormat('d F Y, H:i').' WIB'
                        : '-'],
                    ['label' => 'Catatan Musyrifah', 'nilai' => $pengajuan->catatan_musyrifah ?: '-'],
                ],
            ],
        ];

        return $this->laporanPdf->render([
            'judul' => 'Surat Pengajuan Keterlambatan Pembayaran',
            'jenis_label' => 'SURAT PENGAJUAN KETERLAMBATAN PEMBAYARAN',
            'periode_label' => $tagihan?->periode ?? '-',
            'cakupan' => $mahasiswa?->name ?? 'Mahasiswa',
            'dibangkitkan_label' => now()->locale('id')->translatedFormat('d F Y, H:i').' WIB',
            'seksi' => $seksi,
        ], [
            'Nomor Surat' => $pengajuan->nomor_surat,
            'Status' => $pengajuan->statusLabel(),
        ]);
    }
}
