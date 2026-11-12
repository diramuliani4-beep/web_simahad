<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penanda kapan pemilik akun menentukan kata sandinya sendiri.
     * Akun yang dibuat Admin belum memilikinya sehingga Pengaturan Akun
     * menampilkan formulir "Buat Kata Sandi Pertama".
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'password_set_at')) {
                $table->timestamp('password_set_at')->nullable()->after('password');
            }
        });

        if (! Schema::hasColumn('users', 'password_set_at')) {
            return;
        }

        // Akun lama yang sudah aktif dianggap sudah memiliki kata sandi sendiri
        // agar verifikasi kata sandi saat ini tetap berlaku (tidak ada pelonggaran).
        DB::table('users')
            ->where(function ($query): void {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'menunggu aktivasi');
            })
            ->whereNull('password_set_at')
            ->update(['password_set_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'password_set_at')) {
                $table->dropColumn('password_set_at');
            }
        });
    }
};
