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
        // Jangan gunakan relasi langsung dalam accessor untuk menghindari infinite loop
        if ($this->jenis_pelatihan_id) {
            static $cache = [];
            $key = $this->jenis_pelatihan_id;

            if (!isset($cache[$key])) {
                $jenis = JenisPelatihan::find($this->jenis_pelatihan_id);
                $cache[$key] = $jenis ? (object)['nama' => $jenis->nama] : null;
            }

            return $cache[$key];
        }

        return null;
    }

    // Helper method untuk mendapatkan nama jenis pelatihan
    public function getJenisNama()
    {
        if ($this->relationLoaded('jenisPelatihan') && $this->jenisPelatihan) {
            return $this->jenisPelatihan->nama;
        }

        if ($this->jenis_pelatihan_id) {
            $jenis = JenisPelatihan::find($this->jenis_pelatihan_id);
            return $jenis ? $jenis->nama : null;
        }

        return null;
    }
}
