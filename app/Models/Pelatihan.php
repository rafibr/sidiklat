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
        'jenis_pelatihan',
        'penyelenggara',
        'tempat',
        'tanggal_mulai',
        'tanggal_selesai',
        'jp',
        'status',
        'sertifikat_path',
        'deskripsi'
    ];

    // Tidak menggunakan date casting karena format tanggal bervariasi dalam JSON
    // protected $casts = [
    //     'tanggal_mulai' => 'date',
    //     'tanggal_selesai' => 'date',
    // ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
