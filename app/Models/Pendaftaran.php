<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendaftaran extends Model
{
    // Kolom yang boleh disimpan melalui Eloquent.
    protected $fillable = [
        // Relasi mahasiswa.
        'user_id',

        // Data diri.
        'nama_lengkap',
        'nim',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_hp',
        'semester',
        'no_wa',
        'asal_sekolah',
        'nama_ortu',
        'no_hp_ortu',
        'kategori',
        'email_aktif',
        'ijazah_sma',
        'ktp',
        'kk',

        // Data akademik.
        'tahun_angkatan',
        'fakultas',
        'program_studi',
        'bukti_penerimaan',

        // Persyaratan dan pembayaran.
        'alasan_daftar',
        'nominal_pembayaran',
        'bukti_pembayaran',
        'konfirmasi_data',

        // Status pendaftaran.
        'status',

        // Data validasi oleh Musyrifah.
        'validator_id',
        'validator_nama',
        'divalidasi_pada',
        'catatan_validasi',
    ];

    // Konversi tipe data saat digunakan Laravel.
    protected function casts(): array
    {
        return [
            'nominal_pembayaran' => 'decimal:2',
            'konfirmasi_data' => 'boolean',
            'divalidasi_pada' => 'datetime',
        ];
    }

    /**
     * Relasi pendaftaran dengan akun mahasiswa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi pendaftaran dengan akun Musyrifah
     * yang melakukan validasi.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_id');
    }
}
