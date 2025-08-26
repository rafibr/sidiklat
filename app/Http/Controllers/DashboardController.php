<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pelatihan;
use App\Models\JenisPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
	public function index(Request $request)
	{
		// Get year filter, default to current year
		$year = $request->get('year', now()->year);

		$stats = [
			'total_pegawai' => Pegawai::count(),
			'total_pelatihan' => Pelatihan::whereYear('tanggal_mulai', $year)->count(),
			'total_jp' => Pelatihan::whereYear('tanggal_mulai', $year)->sum('jp'),
			// Calculate average progress based on filtered year data
			'rata_progress' => (float) $this->calculateAverageProgress($year),
		];

		// Aggregate counts grouped by jenis_pelatihans nama filtered by year
		$pelTable = (new Pelatihan)->getTable();
		$jenisTable = (new JenisPelatihan)->getTable();

		// use DB::table to return plain objects (avoid model accessors/appends overriding fields)
		$pelatihanByJenis = DB::table("{$pelTable} as p")
			->leftJoin("{$jenisTable} as j", 'p.jenis_pelatihan_id', '=', 'j.id')
			->select(DB::raw("COALESCE(j.nama, 'Lainnya') as jenis_pelatihan"), DB::raw('count(*) as total'))
			->whereYear('p.tanggal_mulai', $year)
			->groupBy(DB::raw("COALESCE(j.nama, 'Lainnya')"))
			->get();

		// Get top 10 employees with highest JP progress filtered by year
		$progressPegawai = Pegawai::select('pegawais.id', 'pegawais.nama_lengkap', 'pegawais.unit_kerja')
			->selectRaw('COALESCE(SUM(pelatihans.jp), 0) as jp_tercapai_filtered')
			->leftJoin('pelatihans', 'pegawais.id', '=', 'pelatihans.pegawai_id')
			->where(function ($query) use ($year) {
				$query->whereYear('pelatihans.tanggal_mulai', $year)
					->orWhereNull('pelatihans.id');
			})
			->groupBy('pegawais.id', 'pegawais.nama_lengkap', 'pegawais.unit_kerja')
			->orderByDesc('jp_tercapai_filtered')
			->limit(10)
			->get();

		// Add yearly JP targets to each pegawai in the progress list
		$progressPegawai->transform(function ($pegawai) use ($year) {
			$pegawai->jp_target = $pegawai->getJpTargetForYear($year);
			return $pegawai;
		});

		// Get available years from pelatihan data
		$availableYears = Pelatihan::selectRaw('DISTINCT YEAR(tanggal_mulai) as year')
			->whereNotNull('tanggal_mulai')
			->orderByDesc('year')
			->pluck('year')
			->toArray();

		// Ensure current year is in available years if not present
		if (!in_array($year, $availableYears)) {
			$availableYears[] = $year;
			rsort($availableYears);
		}

		return Inertia::render('Dashboard/Index', [
			'stats' => $stats,
			'pelatihanByJenis' => $pelatihanByJenis,
			'progressPegawai' => $progressPegawai,
			'availableYears' => $availableYears,
			'selectedYear' => $year,
		]);
	}

	private function calculateAverageProgress($year)
	{
		// Calculate average progress based on pelatihan data from specific year using yearly JP targets
		$pegawais = Pegawai::select('pegawais.id')
			->selectRaw('COALESCE(SUM(pelatihans.jp), 0) as jp_tercapai')
			->leftJoin('pelatihans', function ($join) use ($year) {
				$join->on('pegawais.id', '=', 'pelatihans.pegawai_id')
					->whereYear('pelatihans.tanggal_mulai', $year);
			})
			->groupBy('pegawais.id')
			->get();

		if ($pegawais->isEmpty()) {
			return 0;
		}

		$totalProgress = $pegawais->sum(function ($pegawai) use ($year) {
			$jpTarget = $pegawai->getJpTargetForYear($year);
			return $jpTarget > 0 ? ($pegawai->jp_tercapai / $jpTarget) * 100 : 0;
		});

		return $totalProgress / $pegawais->count();
	}
}
