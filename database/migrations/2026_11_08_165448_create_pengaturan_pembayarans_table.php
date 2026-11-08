<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Konfigurasi periode pembayaran Ma'had: satu nomor Virtual Account
     * BSI dipakai bersama oleh seluruh mahasiswa, nominal tagihan,
     * tanggal periode dibuka, dan jatuh tempo.
     */
    public function up(): void
    {
        if (Schema::hasTable('pengaturan_pembayarans')) {
            return;
        }

        Schema::create('pengaturan_pembayarans', function (Blueprint $table) {
            $table->id();

            $table->string('periode', 60);
            $table->string('nama_tahap', 80)->default('Tahap 1');
            $table->decimal('nominal', 12, 2)->default(0);

            // Nomor Virtual Account BSI bersama (bukan per mahasiswa).
            $table->string('va_nomor', 40)->nullable();
            $table->string('va_bank', 40)->default('BSI');

            $table->date('periode_dibuka_pada')->nullable();
            $table->date('jatuh_tempo')->nullable();

            $table->boolean('is_aktif')->default(false);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->index(['is_aktif', 'periode_dibuka_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_pembayarans');
    }
};
