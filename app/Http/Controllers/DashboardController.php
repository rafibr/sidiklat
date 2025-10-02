<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
	public function index()
	{
        $stats = [
                'total_pegawai' => Pegawai::count(),
                'total_pelatihan' => Pelatihan::count(),
                'total_jp' => Pelatihan::sum('jp'),
                'rata_progress' => (float) Pegawai::selectRaw('AVG(CASE WHEN jp_target > 0 THEN (jp_tercapai / jp_target) * 100 ELSE 0 END) as rata')->value('rata') ?? 0,
        ];

        $pelatihanByJenis = Pelatihan::leftJoin('jenis_pelatihans', 'pelatihans.jenis_pelatihan_id', '=', 'jenis_pelatihans.id')
                ->selectRaw('COALESCE(jenis_pelatihans.nama, "Tidak Terdefinisi") as jenis_pelatihan, COUNT(pelatihans.id) as total')
                ->groupBy('jenis_pelatihan')
                ->orderBy('jenis_pelatihan')
                ->get();

		$progressPegawai = Pegawai::select('nama_lengkap', 'jp_target', 'jp_tercapai')
			->orderByDesc('jp_tercapai')
			->limit(10)
			->get();
		return view('dashboard.index', compact('stats', 'pelatihanByJenis', 'progressPegawai'));
	}
}
