<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom aktivasi akun bertoken (Guru, Musyrifah, Pimpinan) dan
     * pencatatan login terakhir untuk Dashboard Admin.
     * Semua kolom nullable sehingga akun lama tidak terpengaruh.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'activation_token')) {
                $table->string('activation_token', 64)->nullable()->after('password');
            }

            if (! Schema::hasColumn('users', 'activation_expires_at')) {
                $table->timestamp('activation_expires_at')->nullable()->after('activation_token');
            }

            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('activation_expires_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['activation_token', 'activation_expires_at', 'last_login_at']);
        });
    }
};
