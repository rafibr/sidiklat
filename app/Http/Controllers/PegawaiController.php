<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PegawaiController extends Controller
{
	public function show(Pegawai $pegawai)
	{
		// Load pelatihans
		$pegawai->load('pelatihans');

		// Group pelatihans by year (try to extract year from tanggal_mulai, fallback to created_at year)
		$pelatihansByYear = collect($pegawai->pelatihans)->groupBy(function ($pel) {
			// tanggal_mulai stored as string; attempt to parse year
			$year = null;
			if (!empty($pel->tanggal_mulai)) {
				if (preg_match('/(\d{4})/', $pel->tanggal_mulai, $m)) {
					$year = $m[1];
				}
			}
			if (!$year && $pel->created_at) {
				$year = $pel->created_at->format('Y');
			}
			return $year ?? 'Unknown';
		})->sortKeysDesc();

		return Inertia::render('Pegawai/Show', [
			'pegawai' => $pegawai,
			'pelatihansByYear' => $pelatihansByYear,
		]);
	}
}
