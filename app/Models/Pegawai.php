<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nama_lengkap',
        'pangkat_golongan',
        'jabatan',
        'unit_kerja',
        'status',
        'tanggal_pengangkatan',
        'keterangan',
        'email',
        'telepon',
        'jp_target',
        'jp_tercapai'
    ];

    protected $casts = [
        'tanggal_pengangkatan' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'jp_target' => 'integer',
        'jp_tercapai' => 'integer',
    ];

    public function pelatihans(): HasMany
    {
        return $this->hasMany(Pelatihan::class);
    }

    public function jpTargets(): HasMany
    {
        return $this->hasMany(PegawaiJpTarget::class);
    }

    public function getPersentaseProgressAttribute()
    {
        if ($this->jp_target == 0) return 0;
        return min(100, ($this->jp_tercapai / $this->jp_target) * 100);
    }

    public function getKontakLengkapAttribute(): ?string
    {
        $parts = array_filter([$this->email, $this->telepon]);
        return empty($parts) ? null : implode(' / ', $parts);
    }
}
