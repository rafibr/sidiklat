<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelatihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'nama_pelatihan',
        'jenis_pelatihan',
        'jenis_pelatihan_id',
        'penyelenggara',
        'tempat',
        'tanggal_mulai',
        'tanggal_selesai',
        'jp',
        'status',
        'sertifikat_path',
        'deskripsi'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jp' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['jenis_pelatihan'];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisPelatihan(): BelongsTo
    {
        return $this->belongsTo(JenisPelatihan::class);
    }

    public function getJenisPelatihanAttribute(): ?string
    {
        if ($this->relationLoaded('jenisPelatihan')) {
            return $this->getRelation('jenisPelatihan')?->nama;
        }

        return $this->jenisPelatihan()->value('nama');
    }

    public function setJenisPelatihanAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['jenis_pelatihan_id'] = null;
            return;
        }

        if (is_numeric($value)) {
            $this->attributes['jenis_pelatihan_id'] = (int) $value;
            return;
        }

        $jenis = JenisPelatihan::firstOrCreate([
            'nama' => $value,
        ]);

        $this->attributes['jenis_pelatihan_id'] = $jenis->id;
    }

    public function scopeOfJenis($query, string $namaJenis)
    {
        return $query->whereHas('jenisPelatihan', function ($subQuery) use ($namaJenis) {
            $subQuery->where('nama', $namaJenis);
        });
    }
}
