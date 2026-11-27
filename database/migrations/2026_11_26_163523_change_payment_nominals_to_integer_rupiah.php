<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simpan nominal Rupiah sebagai bilangan bulat tanpa mengubah nilainya.
     */
    public function up(): void
    {
        $this->pastikanNominalBulat('angsuran_pembayarans');
        $this->pastikanNominalBulat('pembayarans');

        Schema::table('angsuran_pembayarans', function (Blueprint $table): void {
            $table->unsignedBigInteger('nominal')->change();
        });

        Schema::table('pembayarans', function (Blueprint $table): void {
            $table->unsignedBigInteger('nominal')->default(2500000)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('angsuran_pembayarans', function (Blueprint $table): void {
            $table->decimal('nominal', 15, 2)->change();
        });

        Schema::table('pembayarans', function (Blueprint $table): void {
            $table->decimal('nominal', 15, 2)->default(2500000)->change();
        });
    }

    private function pastikanNominalBulat(string $tabel): void
    {
        DB::table($tabel)
            ->select(['id', 'nominal'])
            ->chunkById(1000, function (Collection $baris) use ($tabel): void {
                foreach ($baris as $barisNominal) {
                    $nominal = (float) $barisNominal->nominal;

                    if (floor($nominal) !== $nominal) {
                        throw new RuntimeException(
                            'Nominal pecahan ditemukan pada tabel '.$tabel.
                            '. Perbaiki data sebelum mengubah kolom menjadi bilangan bulat.'
                        );
                    }
                }
            });
    }
};
