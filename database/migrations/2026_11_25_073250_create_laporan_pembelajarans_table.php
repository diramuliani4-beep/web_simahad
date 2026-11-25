<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('laporan_pembelajarans')) {
            Schema::create('laporan_pembelajarans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
                $table->foreignId('pertemuan_id')->nullable()->constrained('pertemuan_pembelajarans')->cascadeOnDelete();
                $table->date('tanggal');
                $table->string('kelompok', 60)->nullable();
                $table->string('periode_semester', 100)->nullable();
                $table->text('materi');
                $table->string('instruktur', 255)->nullable();
                $table->unsignedSmallInteger('jumlah_hadir')->nullable();
                $table->unsignedSmallInteger('jumlah_izin')->nullable();
                $table->unsignedSmallInteger('jumlah_sakit')->nullable();
                $table->unsignedSmallInteger('jumlah_alpa')->nullable();
                $table->text('tanda_tangan')->nullable();
                $table->text('catatan')->nullable();
                $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique('pertemuan_id', 'laporan_pembelajaran_pertemuan_unik');
                $table->index(['mata_pelajaran_id', 'tanggal'], 'laporan_pembelajaran_mapel_tanggal_idx');
            });
        }

        $this->pastikanMataPelajaranWajib();
    }

    /**
     * Ensure the six report subjects exist on both new and existing installs.
     */
    private function pastikanMataPelajaranWajib(): void
    {
        $mataPelajaran = [
            ['Pembelajaran Kitab Kuning', 'PKK-01'],
            ['Penguatan Keterampilan Khusus Wawasan Interdisipliner dan Moderasi Beragama', 'PKK-WIMB'],
            ['Penguatan Keterampilan Khusus', 'PKK-02'],
            ['Penguatan Bahasa Asing', 'PBA-01'],
            ['Penguatan Keterampilan Khusus (Karakter & Dakwah)', 'PKK-03'],
            ['Bahasa Arab', 'BA-01'],
        ];

        foreach ($mataPelajaran as [$nama, $kode]) {
            if (DB::table('mata_pelajarans')->where('nama', $nama)->exists()) {
                continue;
            }

            DB::table('mata_pelajarans')->insert([
                'nama' => $nama,
                'kode' => $kode,
                'target_pertemuan' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pembelajarans');
    }
};
