<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
	public function index()
	{
		$stats = [
			'total_pegawai' => Pegawai::count(),
			'total_pelatihan' => Pelatihan::count(),
			'total_jp' => Pelatihan::sum('jp'),
			'rata_progress' => Pegawai::avg('jp_tercapai'),
		];

		$pelatihanByJenis = Pelatihan::select('jenis_pelatihan', DB::raw('count(*) as total'))
			->groupBy('jenis_pelatihan')
			->get();

		$progressPegawai = Pegawai::select('nama_lengkap', 'jp_target', 'jp_tercapai')
			->orderByDesc('jp_tercapai')
			->limit(10)
			->get();

		return Inertia::render('Dashboard/Index', [
			'stats' => $stats,
			'pelatihanByJenis' => $pelatihanByJenis,
			'progressPegawai' => $progressPegawai,
		]);
	}
}
