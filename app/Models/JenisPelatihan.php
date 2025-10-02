<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPelatihan extends Model
{
	use HasFactory;

	protected $table = 'jenis_pelatihans';

	protected $fillable = [
		'kode',
		'nama',
		'kategori',
		'deskripsi',
		'target_peserta',
		'durasi_standar',
		'sertifikasi'
	];
}
