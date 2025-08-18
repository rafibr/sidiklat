<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with('pelatihans');

        // Filter by unit kerja
        if ($request->filled('unit_kerja')) {
            $query->where('unit_kerja', $request->unit_kerja);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }
        $pegawais = $query->paginate(10);
        $unitKerjas = Pegawai::distinct('unit_kerja')->pluck('unit_kerja');

        return view('progress.index', compact('pegawais', 'unitKerjas'));
    }
}
