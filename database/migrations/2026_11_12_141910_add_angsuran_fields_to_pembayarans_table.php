<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom tambahan untuk pembayaran berangsuran: tahap angsuran, jatuh
     * tempo per tagihan, hasil pemeriksaan bukti transfer, konfirmasi dana
     * dari Rektorat, dan penanda pengingat terakhir.
     */
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->unsignedTinyInteger('angsuran_ke')->nullable()->index();
            $table->date('jatuh_tempo')->nullable();
            $table->string('kategori_biaya', 30)->nullable();

            $table->string('status_verifikasi', 20)->nullable()->index();
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamp('tanggal_upload_bukti')->nullable();
            $table->timestamp('tanggal_konfirmasi_rektorat')->nullable();
            $table->timestamp('reminder_terakhir_pada')->nullable();

            $table->index(['user_id', 'periode', 'angsuran_ke']);
        });

        // Tagihan lama menjadi angsuran pertama agar tidak muncul ganda saat
        // jadwal tiga angsuran disinkronkan.
        DB::table('pembayarans')
            ->whereNull('angsuran_ke')
            ->update(['angsuran_ke' => 1]);
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'periode', 'angsuran_ke']);
            $table->dropIndex(['angsuran_ke']);
            $table->dropIndex(['status_verifikasi']);
            $table->dropConstrainedForeignId('diverifikasi_oleh');
            $table->dropColumn([
                'angsuran_ke',
                'jatuh_tempo',
                'kategori_biaya',
                'status_verifikasi',
                'catatan_verifikasi',
                'tanggal_verifikasi',
                'tanggal_upload_bukti',
                'tanggal_konfirmasi_rektorat',
                'reminder_terakhir_pada',
            ]);
        });
    }
};
