<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kelompok_belajar_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_belajar_id')->constrained('kelompok_belajars')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->string('status')->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Aturan ketat: Satu mahasiswa hanya memiliki satu kelompok aktif untuk setiap mata pelajaran.
            $table->unique(['user_id', 'mata_pelajaran_id'], 'user_mapel_kelompok_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_belajar_anggotas');
    }
};
