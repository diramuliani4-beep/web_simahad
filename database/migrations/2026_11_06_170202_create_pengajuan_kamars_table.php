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
        Schema::create('pengajuan_kamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kamar_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('menunggu');
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['kamar_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_kamars');
    }
};
