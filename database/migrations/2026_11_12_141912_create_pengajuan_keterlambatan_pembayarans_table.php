<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pengajuan keterlambatan pembayaran yang dibuat mahasiswa. Surat PDF
     * dibangkitkan dari data ini dan diperiksa Musyrifah.
     */
    public function up(): void
    {
        if (Schema::hasTable('pengajuan_keterlambatan_pembayarans')) {
            return;
        }

        Schema::create('pengajuan_keterlambatan_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pembayaran_id')->constrained('pembayarans')->cascadeOnDelete();
            $table->string('nomor_surat', 60)->unique();
            $table->text('alasan');
            $table->date('tanggal_rencana_bayar');

            $table->string('status', 20)->default('menunggu')->index();
            $table->text('catatan_musyrifah')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_pada')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_keterlambatan_pembayarans');
    }
};
