
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('gedung_yang_dikelola', 150)
                  ->nullable();

            $table->unsignedSmallInteger('lantai_yang_dikelola')
                  ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn([
                'gedung_yang_dikelola',
                'lantai_yang_dikelola',
            ]);
        });
    }
};
