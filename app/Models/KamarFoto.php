<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class KamarFoto extends Model
{
    protected $fillable = [
        'kamar_id',
        'path',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Kamar yang memiliki foto ini.
     */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    /**
     * URL publik berkas foto kamar.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
