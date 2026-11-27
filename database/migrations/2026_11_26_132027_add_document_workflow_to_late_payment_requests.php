<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan alur dokumen tanpa mengubah pengajuan yang sudah ada.
     */
    public function up(): void
    {
        Schema::table('pengajuan_keterlambatan_pembayarans', function (Blueprint $table): void {
            if (! Schema::hasColumn('pengajuan_keterlambatan_pembayarans', 'pdf_sistem')) {
                $table->string('pdf_sistem')->nullable()->after('nomor_surat');
            }

            if (! Schema::hasColumn('pengajuan_keterlambatan_pembayarans', 'dokumen_wajib')) {
                $table->boolean('dokumen_wajib')->default(false)->after('status');
            }
        });

        Schema::create('pengajuan_keterlambatan_dokumens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_keterlambatan_pembayarans')->cascadeOnDelete();
            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('path');
            $table->string('nama_asli');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('ukuran');
            $table->unsignedInteger('versi');
            $table->timestamps();
            $table->unique(['pengajuan_id', 'versi']);
        });

        Schema::create('pengajuan_keterlambatan_riwayats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_keterlambatan_pembayarans')->cascadeOnDelete();
            $table->foreignId('dokumen_id')->nullable()->constrained('pengajuan_keterlambatan_dokumens')->nullOnDelete();
            $table->string('status_sebelum', 20)->nullable();
            $table->string('status_sesudah', 20);
            $table->text('catatan')->nullable();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_keterlambatan_riwayats');
        Schema::dropIfExists('pengajuan_keterlambatan_dokumens');

        Schema::table('pengajuan_keterlambatan_pembayarans', function (Blueprint $table): void {
            $table->dropColumn(['pdf_sistem', 'dokumen_wajib']);
        });
    }
};
