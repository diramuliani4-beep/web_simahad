<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menyimpan isi laporan periodik (bulanan/tahunan) sebagai salinan tetap.
 *
 * Laporan kategori yang sudah ada tetap dibangkitkan dari data operasional.
 * Laporan periodik menyimpan potret isi (`konten`) saat dibuat agar laporan
 * yang dibuka kembali tidak berubah ketika data operasional diperbarui.
 * Migrasi ini hanya menambah kolom sehingga data lama tidak terpengaruh.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('laporan_mahads')) {
            return;
        }

        if (! Schema::hasColumn('laporan_mahads', 'tahun')) {
            Schema::table('laporan_mahads', function (Blueprint $table): void {
                $table->unsignedSmallInteger('tahun')->nullable()->after('periode');
            });
        }

        if (! Schema::hasColumn('laporan_mahads', 'bulan')) {
            Schema::table('laporan_mahads', function (Blueprint $table): void {
                $table->unsignedTinyInteger('bulan')->nullable()->after('tahun');
            });
        }

        if (! Schema::hasColumn('laporan_mahads', 'konten')) {
            Schema::table('laporan_mahads', function (Blueprint $table): void {
                $table->longText('konten')->nullable()->after('catatan');
            });
        }

        if (! Schema::hasIndex('laporan_mahads', 'laporan_mahads_tahun_bulan_index')) {
            Schema::table('laporan_mahads', function (Blueprint $table): void {
                $table->index(['tahun', 'bulan'], 'laporan_mahads_tahun_bulan_index');
            });
        }

        $this->lengkapiPeriodeLama();
    }

    /**
     * Laporan lama memakai periode "Y-m" sehingga tahun dan bulan diisikan
     * dari kolom periode tanpa mengubah nilai apa pun.
     */
    private function lengkapiPeriodeLama(): void
    {
        if (! Schema::hasColumn('laporan_mahads', 'tahun')) {
            return;
        }

        DB::table('laporan_mahads')
            ->whereNull('tahun')
            ->orderBy('id')
            ->chunkById(100, function ($baris): void {
                foreach ($baris as $laporan) {
                    $periode = (string) $laporan->periode;

                    if (preg_match('/^(\d{4})(?:-(\d{2}))?$/', $periode, $cocok) !== 1) {
                        continue;
                    }

                    DB::table('laporan_mahads')
                        ->where('id', $laporan->id)
                        ->update([
                            'tahun' => (int) $cocok[1],
                            'bulan' => isset($cocok[2]) ? (int) $cocok[2] : null,
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Kebijakan proyek hanya mengizinkan perubahan skema yang menambah.
    }
};
