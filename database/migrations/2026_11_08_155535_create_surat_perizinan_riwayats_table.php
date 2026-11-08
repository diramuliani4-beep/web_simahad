<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat perubahan status setiap pengajuan surat & perizinan.
     */
    public function up(): void
    {
        if (Schema::hasTable('surat_perizinan_riwayats')) {
            return;
        }

        Schema::create('surat_perizinan_riwayats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_perizinan_id')
                ->constrained('surat_perizinans')
                ->cascadeOnDelete();

            $table->string('status', 30);
            $table->text('catatan')->nullable();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pengubah')->nullable();
            $table->timestamps();

            $table->index(['surat_perizinan_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_perizinan_riwayats');
    }
};
