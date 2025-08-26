<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
		'jp_target',
		'jp_tercapai',
		'email',
		'telepon'
	];

	protected $casts = [
		'tanggal_pengangkatan' => 'date',
	];

	public function pelatihans()
	{
		return $this->hasMany(Pelatihan::class);
	}

	public function getPersentaseProgressAttribute()
	{
		if ($this->jp_target == 0) return 0;
		return min(100, ($this->jp_tercapai / $this->jp_target) * 100);
	}
}
