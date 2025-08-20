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
			// avg may return null; cast to float to ensure frontend numeric usage
			'rata_progress' => (float) (Pegawai::avg('jp_tercapai') ?? 0),
		];

		// Aggregate counts grouped by jenis_pelatihans nama
		$pelatihanByJenis = Pelatihan::leftJoin('jenis_pelatihans', 'pelatihans.jenis_pelatihan_id', '=', 'jenis_pelatihans.id')
			->select('jenis_pelatihans.nama as jenis_pelatihan', DB::raw('count(*) as total'))
			->groupBy('jenis_pelatihans.nama')
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
