<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'nama_pelatihan',
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
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisPelatihan()
    {
        return $this->belongsTo(JenisPelatihan::class, 'jenis_pelatihan_id');
    }

    // Append an accessor to expose jenis_pelatihan string for frontend compatibility
    protected $appends = ['jenis_pelatihan'];

    public function getJenisPelatihanAttribute()
    {
        $jenis = $this->getRelationValue('jenisPelatihan');
        if ($jenis) {
            return $jenis->nama ?? null;
        }
        return null;
    }
}
