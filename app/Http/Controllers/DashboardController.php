<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pelatihan;
use App\Models\JenisPelatihan;
use App\Support\Database\DateQueryHelper;
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

		// Calculate JP fulfillment statistics for all employees
		$jpFulfillment = $this->calculateJpFulfillmentStats($year);

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
                $yearExpression = DateQueryHelper::yearExpression('tanggal_mulai');

                $availableYears = Pelatihan::selectRaw("DISTINCT {$yearExpression} as year")
                        ->whereNotNull('tanggal_mulai')
                        ->orderByDesc('year')
                        ->pluck('year')
                        ->map(fn ($value) => (int) $value)
                        ->filter()
                        ->values()
                        ->toArray();

		// Ensure current year is in available years if not present
		if (!in_array($year, $availableYears)) {
			$availableYears[] = $year;
			rsort($availableYears);
		}

		return Inertia::render('Dashboard/Index', [
			'stats' => $stats,
			'jpFulfillment' => $jpFulfillment,
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

        private function calculateJpFulfillmentStats($year)
        {
		// Get all employees with their JP progress for the year
		$pegawais = Pegawai::select('pegawais.id', 'pegawais.nama_lengkap', 'pegawais.unit_kerja')
			->selectRaw('COALESCE(SUM(pelatihans.jp), 0) as jp_tercapai')
			->leftJoin('pelatihans', function ($join) use ($year) {
				$join->on('pegawais.id', '=', 'pelatihans.pegawai_id')
					->whereYear('pelatihans.tanggal_mulai', $year);
			})
			->groupBy('pegawais.id', 'pegawais.nama_lengkap', 'pegawais.unit_kerja')
			->get();

		// Calculate fulfillment categories and stats
		$totalEmployees = $pegawais->count();
		$completed = 0; // 100% or more
		$onTrack = 0;   // 75-99%
		$behind = 0;    // 50-74%
		$critical = 0;  // Below 50%

		$totalJpTarget = 0;
		$totalJpAchieved = 0;

		$employeeDetails = [];

		foreach ($pegawais as $pegawai) {
			$jpTarget = $pegawai->getJpTargetForYear($year);
			$jpAchieved = $pegawai->jp_tercapai;
			$progressPercentage = $jpTarget > 0 ? ($jpAchieved / $jpTarget) * 100 : 0;

			$totalJpTarget += $jpTarget;
			$totalJpAchieved += $jpAchieved;

			// Categorize employee progress
			if ($progressPercentage >= 100) {
				$completed++;
				$status = 'completed';
			} elseif ($progressPercentage >= 75) {
				$onTrack++;
				$status = 'on-track';
			} elseif ($progressPercentage >= 50) {
				$behind++;
				$status = 'behind';
			} else {
				$critical++;
				$status = 'critical';
			}

			$employeeDetails[] = [
				'id' => $pegawai->id,
				'nama' => $pegawai->nama_lengkap,
				'unit_kerja' => $pegawai->unit_kerja,
				'jp_target' => $jpTarget,
				'jp_achieved' => $jpAchieved,
				'progress_percentage' => round($progressPercentage, 1),
				'status' => $status
			];
		}

		// Sort by progress percentage descending
		usort($employeeDetails, function ($a, $b) {
			return $b['progress_percentage'] <=> $a['progress_percentage'];
		});

                return [
                        'total_employees' => $totalEmployees,
                        'categories' => [
                                'completed' => $completed,
                                'on_track' => $onTrack,
                                'behind' => $behind,
                                'critical' => $critical
                        ],
                        'percentages' => [
                                'completed' => $totalEmployees > 0 ? round(($completed / $totalEmployees) * 100, 1) : 0,
                                'on_track' => $totalEmployees > 0 ? round(($onTrack / $totalEmployees) * 100, 1) : 0,
                                'behind' => $totalEmployees > 0 ? round(($behind / $totalEmployees) * 100, 1) : 0,
                                'critical' => $totalEmployees > 0 ? round(($critical / $totalEmployees) * 100, 1) : 0
                        ],
                        'totals' => [
                                'jp_target' => $totalJpTarget,
                                'jp_achieved' => $totalJpAchieved,
                                'overall_progress' => $totalJpTarget > 0 ? round(($totalJpAchieved / $totalJpTarget) * 100, 1) : 0
                        ],
                        'top_performers' => array_slice($employeeDetails, 0, 5), // Top 5
                        'needs_attention' => array_slice(array_reverse($employeeDetails), 0, 5) // Bottom 5
                ];
        }

        /**
         * Get mobile stats for quick display
         */
        public function mobileStats()
        {
                $currentYear = now()->year;

		$stats = [
			'total_pegawai' => Pegawai::count(),
			'active_training' => Pelatihan::where('status', 'Sedang Berjalan')
				->whereYear('tanggal_mulai', $currentYear)
				->count(),
			'completed_training' => Pelatihan::where('status', 'Selesai')
				->whereYear('tanggal_mulai', $currentYear)
				->count(),
			'total_jp_this_year' => Pelatihan::whereYear('tanggal_mulai', $currentYear)->sum('jp'),
			'avg_progress' => round($this->calculateAverageProgress($currentYear), 1)
		];

		return response()->json($stats);
	}
}
