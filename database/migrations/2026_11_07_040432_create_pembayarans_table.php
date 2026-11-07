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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('kode_transaksi')->nullable()->index();
            $table->string('nama_tagihan')->default('Pembayaran Asrama');
            $table->string('periode')->default('Januari 2027');
            $table->decimal('nominal', 15, 2)->default(2500000.00);
            $table->string('metode_pembayaran')->nullable();
            $table->string('status')->default('belum_lunas')->index();
            $table->timestamp('tanggal_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
