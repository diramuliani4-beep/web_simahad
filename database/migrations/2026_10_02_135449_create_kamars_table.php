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
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kamar');
            $table->string('nomor_kamar')->nullable();
            $table->string('gedung')->default('Gedung A');
            $table->integer('lantai')->default(1);
            $table->integer('kapasitas')->default(4);
            $table->integer('terisi')->default(0);
            $table->string('status')->default('Tersedia'); // 'Tersedia', 'Penuh', 'Perbaikan'
            $table->text('fasilitas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
