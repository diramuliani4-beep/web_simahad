<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kamar_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kamar_id')->constrained('kamars')->cascadeOnDelete();
            $table->string('path');
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();

            $table->index(['kamar_id', 'urutan']);
        });

        // Foto tunggal lama (kamars.foto) dijadikan foto utama agar tetap dapat ditampilkan.
        $sekarang = now();

        DB::table('kamars')
            ->whereNotNull('foto')
            ->orderBy('id')
            ->get(['id', 'foto'])
            ->each(function (object $kamar) use ($sekarang): void {
                if (! filled($kamar->foto)) {
                    return;
                }

                $sudahAda = DB::table('kamar_fotos')->where('kamar_id', $kamar->id)->exists();

                if ($sudahAda) {
                    return;
                }

                DB::table('kamar_fotos')->insert([
                    'kamar_id' => $kamar->id,
                    'path' => $kamar->foto,
                    'urutan' => 1,
                    'created_at' => $sekarang,
                    'updated_at' => $sekarang,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamar_fotos');
    }
};
