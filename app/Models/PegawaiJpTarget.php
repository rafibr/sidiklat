<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiJpTarget extends Model
{
	use HasFactory;

	protected $fillable = [
		'pegawai_id',
		'tahun',
		'jp_target',
		'jp_tercapai'
	];

	protected $casts = [
		'tahun' => 'integer',
		'jp_target' => 'integer',
		'jp_tercapai' => 'integer',
	];

	public function pegawai()
	{
		return $this->belongsTo(Pegawai::class);
	}

	/**
	 * Get or create JP target for a specific year
	 */
	public static function getOrCreateForYear($pegawaiId, $year)
	{
		$target = self::where('pegawai_id', $pegawaiId)
			->where('tahun', $year)
			->first();

		if (!$target) {
			// Get previous year's target or use default
			$previousYear = $year - 1;
			$previousTarget = self::where('pegawai_id', $pegawaiId)
				->where('tahun', $previousYear)
				->first();

			$jpTarget = $previousTarget ? $previousTarget->jp_target : config('app.jp_default', 20);

			$target = self::create([
				'pegawai_id' => $pegawaiId,
				'tahun' => $year,
				'jp_target' => $jpTarget,
				'jp_tercapai' => 0
			]);
		}

		return $target;
	}
}
