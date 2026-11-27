<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('surat_perizinan_email_logs')) {
            return;
        }

        Schema::create('surat_perizinan_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_perizinan_id')
                ->constrained('surat_perizinans')
                ->cascadeOnDelete();
            $table->foreignId('surat_perizinan_riwayat_id')
                ->constrained('surat_perizinan_riwayats')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('peristiwa', 40);
            $table->string('email');
            $table->string('status', 20)->default('memproses');
            $table->timestamp('terkirim_pada')->nullable();
            $table->timestamp('gagal_pada')->nullable();
            $table->timestamps();

            $table->unique(
                ['surat_perizinan_riwayat_id', 'user_id'],
                'surat_perizinan_email_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_perizinan_email_logs');
    }
};
