<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProgressController extends Controller
{
	public function index(Request $request)
	{
		// Get year filter, default to current year
		$year = $request->get('year', now()->year);

		$query = Pegawai::select('pegawais.*')
			->selectRaw('COALESCE(SUM(pelatihans.jp), 0) as jp_tercapai_filtered')
			->leftJoin('pelatihans', function ($join) use ($year) {
				$join->on('pegawais.id', '=', 'pelatihans.pegawai_id')
					->whereYear('pelatihans.tanggal_mulai', $year);
			})
			->groupBy('pegawais.id');

		// Filter by unit kerja
		if ($request->filled('unit_kerja')) {
			$query->where('pegawais.unit_kerja', $request->unit_kerja);
		}

		// Search by name
		if ($request->filled('search')) {
			$query->where('pegawais.nama_lengkap', 'like', '%' . $request->search . '%');
		}

		$pegawais = $query->paginate(10);

		// Add pelatihan relationship data for each pegawai
		$pegawais->getCollection()->transform(function ($pegawai) use ($year) {
			$pegawai->pelatihans = Pelatihan::where('pegawai_id', $pegawai->id)
				->whereYear('tanggal_mulai', $year)
				->with('jenisPelatihan')
				->get()
				->map(function ($pelatihan) {
					return [
						'id' => $pelatihan->id,
						'nama_pelatihan' => $pelatihan->nama_pelatihan,
						'jp' => $pelatihan->jp,
						'jenis_pelatihan' => $pelatihan->jenisPelatihan->nama ?? 'Unknown',
					];
				});
			return $pegawai;
		});

		$unitKerjas = Pegawai::distinct('unit_kerja')->pluck('unit_kerja');

		// Get available years from pelatihan data
		$availableYears = Pelatihan::selectRaw('DISTINCT YEAR(tanggal_mulai) as year')
			->whereNotNull('tanggal_mulai')
			->orderByDesc('year')
			->pluck('year')
			->toArray();

		return Inertia::render('Progress/Index', [
			'pegawais' => $pegawais,
			'unitKerjas' => $unitKerjas,
			'availableYears' => $availableYears,
			'selectedYear' => $year,
		]);
	}
}
