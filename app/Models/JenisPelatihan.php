<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPelatihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'deskripsi',
        'target_peserta',
        'durasi_standar',
        'sertifikasi',
    ];

    protected $casts = [
        'sertifikasi' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pelatihans(): HasMany
    {
        return $this->hasMany(Pelatihan::class);
    }
}
