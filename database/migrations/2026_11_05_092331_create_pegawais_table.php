<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {

            $table->id();

            // Hubungan dengan akun Manajemen Pengguna
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->restrictOnDelete();

            // Disalin otomatis dari tabel users
            $table->string('nama_pegawai');
            $table->string('email');

            // Biodata khusus pegawai
            $table->string('foto')->nullable();

            $table->enum('jenis_kelamin', [
                'L',
                'P'
            ])->nullable();

            $table->string('pendidikan_terakhir', 100)
                ->nullable();

            $table->text('alamat')->nullable();

            $table->boolean('status_pegawai')
                ->default(true);

            $table->timestamps();
        });

        // Memasukkan akun pegawai yang sudah ada
        // sebelum tabel pegawais dibuat.
        DB::table('users')
            ->whereIn('role', [
                'admin',
                'guru',
                'musyrifah',
                'pengelola',
                'pimpinan',
            ])
            ->orderBy('id')
            ->chunkById(100, function ($users) {

                $waktu = now();
                $data = [];

                foreach ($users as $user) {
                    $data[] = [
                        'user_id' => $user->id,
                        'nama_pegawai' => $user->name,
                        'email' => $user->email,
                        'status_pegawai' => true,
                        'created_at' => $waktu,
                        'updated_at' => $waktu,
                    ];
                }

                if (!empty($data)) {
                    DB::table('pegawais')->insert($data);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
