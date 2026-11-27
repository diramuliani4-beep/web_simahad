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
        Schema::table('penempatan_kelompok_belajars', function (Blueprint $table) {
            $table->unique('user_id', 'penempatan_kelompok_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penempatan_kelompok_belajars', function (Blueprint $table) {
            $table->dropUnique('penempatan_kelompok_user_unique');
        });
    }
};
