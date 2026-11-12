<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit trail khusus laporan.
     *
     * Setiap baris bersifat tambah-saja (append only): tidak ada route yang
     * mengubah atau menghapusnya, sehingga riwayat pembuatan, penerbitan,
     * pembaruan versi, dan pengarsipan laporan tetap dapat ditelusuri.
     */
    public function up(): void
    {
        if (Schema::hasTable('laporan_audits')) {
            return;
        }

        Schema::create('laporan_audits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laporan_mahad_id')
                ->nullable()
                ->constrained('laporan_mahads')
                ->cascadeOnDelete();

            // dibuat | diperbarui | diterbitkan | diarsipkan | dilihat | diunduh | dicetak
            $table->string('aksi', 30);
            $table->string('jenis_laporan', 40);
            $table->string('periode', 7);
            $table->unsignedTinyInteger('lantai')->nullable();
            $table->unsignedInteger('versi')->default(1);

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pengguna_nama')->nullable();
            $table->string('pengguna_role', 30)->nullable();

            $table->text('keterangan')->nullable();
            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();

            $table->timestamps();

            $table->index(['jenis_laporan', 'periode']);
            $table->index('aksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_audits');
    }
};
