<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumentasi_kegiatans', function (Blueprint $table) {
            $table->foreignId('pertemuan_id')->nullable()
                ->constrained('pertemuan_pembelajarans')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        // Relasi dokumentasi dipertahankan agar bukti pertemuan tidak terlepas saat rollback.
    }
};
