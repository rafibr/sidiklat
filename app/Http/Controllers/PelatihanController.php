<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use App\Models\Pegawai;
use App\Models\JenisPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PelatihanController extends Controller
{
	public function comparison(Request $request)
	{
		// Get available years from tanggal_mulai using YEAR()
		$availableYears = Pelatihan::selectRaw('YEAR(tanggal_mulai) as year')
			->groupBy('year')
			->orderBy('year', 'desc')
			->pluck('year')
			->filter()
			->values();

		// Get selected years from request or use default (current and last year)
		$selectedYears = $request->get('years', [date('Y'), date('Y') - 1]);
		if (!is_array($selectedYears)) {
			$selectedYears = explode(',', $selectedYears);
		}
		$selectedYears = array_filter($selectedYears); // Remove empty values

		// Get counts for each selected year using whereYear
		$yearlyData = [];
		foreach ($selectedYears as $year) {
			$yearlyData[$year] = Pelatihan::whereYear('tanggal_mulai', $year)->count();
		}

		// Data untuk chart perbandingan per jenis
		// Build comparison data grouped by jenis_pelatihans table
		$jenisList = \App\Models\JenisPelatihan::pluck('nama', 'id')->toArray();
		$comparisonData = [];
		foreach ($jenisList as $id => $nama) {
			$jenisData = ['jenis' => $nama];
			foreach ($selectedYears as $year) {
				$count = Pelatihan::where('jenis_pelatihan_id', $id)
					->whereYear('tanggal_mulai', $year)
					->count();
				$jenisData[$year] = $count;
			}
			$comparisonData[] = $jenisData;
		}

		// Data bulanan untuk trend (hanya untuk 2 tahun pertama untuk clarity)
		$monthlyData = [];
		$trendYears = array_slice($selectedYears, 0, 2); // Limit to 2 years for trend chart
		for ($i = 1; $i <= 12; $i++) {
			$monthNum = $i;
			$monthData = ['month' => $i];
			foreach ($trendYears as $year) {
				$count = Pelatihan::whereYear('tanggal_mulai', $year)
					->whereMonth('tanggal_mulai', $monthNum)
					->count();
				$monthData[$year] = $count;
			}
			$monthlyData[] = $monthData;
		}

		return Inertia::render('Pelatihan/Comparison', [
			'selectedYears' => $selectedYears,
			'yearlyData' => $yearlyData,
			'comparisonData' => $comparisonData,
			'monthlyData' => $monthlyData,
			'availableYears' => $availableYears,
		]);
	}

	public function index(Request $request)
	{
		$query = Pelatihan::with(['pegawai', 'jenisPelatihan']);

		// Filter by jenis pelatihan
		if ($request->filled('jenis')) {
			// Accept jenis nama (string) and find corresponding ID
			$jenisId = JenisPelatihan::where('nama', $request->jenis)->value('id');
			if ($jenisId) {
				$query->where('jenis_pelatihan_id', $jenisId);
			}
		}

		// Search
		if ($request->filled('search')) {
			$query->where(function ($q) use ($request) {
				$q->where('nama_pelatihan', 'like', '%' . $request->search . '%')
					->orWhereHas('pegawai', function ($subQ) use ($request) {
						$subQ->where('nama_lengkap', 'like', '%' . $request->search . '%');
					});
			});
		}

		$pelatihans = $query->latest()->paginate(10);
		// Provide jenis list from DB (id + nama) for filters/forms
		$jenisPelatihan = JenisPelatihan::select('id', 'nama')->orderBy('nama')->get();

		return Inertia::render('Pelatihan/Index', [
			'pelatihans' => $pelatihans,
			'jenisPelatihan' => $jenisPelatihan,
		]);
	}

	public function create()
	{
		$pegawais = Pegawai::orderBy('nama_lengkap')->get();
		$jenisPelatihan = JenisPelatihan::select('id', 'nama')->orderBy('nama')->get();

		return Inertia::render('Pelatihan/Create', [
			'pegawais' => $pegawais,
			'jenisPelatihan' => $jenisPelatihan,
		]);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'pegawai_id' => 'required|exists:pegawais,id',
			'nama_pelatihan' => 'required|string|max:255',
			'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
			'penyelenggara' => 'required|string|max:255',
			'tempat' => 'nullable|string|max:255',
			'tanggal_mulai' => 'required|date',
			'tanggal_selesai' => 'required|date',
			'jp' => 'required|integer|min:1',
			'status' => 'required|in:selesai,sedang_berjalan,akan_datang',
			'deskripsi' => 'nullable|string',
			'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
		]);

		if ($request->hasFile('sertifikat')) {
			$path = $request->file('sertifikat')->store('sertifikat', 'public');
			$validated['sertifikat_path'] = $path;
		}

		$pelatihan = Pelatihan::create($validated);

		// Update JP pegawai jika status selesai
		if ($pelatihan->status == 'selesai') {
			$pegawai = $pelatihan->pegawai;
			$pegawai->jp_tercapai += $pelatihan->jp;
			$pegawai->save();
		}

		return redirect()->route('pelatihan.index')->with('success', 'Data pelatihan berhasil ditambahkan.');
	}

	public function show(Pelatihan $pelatihan)
	{
		return Inertia::render('Pelatihan/Show', [
			'pelatihan' => $pelatihan,
		]);
	}

	public function edit(Pelatihan $pelatihan)
	{
		$pegawais = Pegawai::orderBy('nama_lengkap')->get();
		$jenisPelatihan = JenisPelatihan::select('id', 'nama')->orderBy('nama')->get();

		return Inertia::render('Pelatihan/Edit', [
			'pelatihan' => $pelatihan,
			'pegawais' => $pegawais,
			'jenisPelatihan' => $jenisPelatihan,
		]);
	}

	public function update(Request $request, Pelatihan $pelatihan)
	{
		$validated = $request->validate([
			'pegawai_id' => 'required|exists:pegawais,id',
			'nama_pelatihan' => 'required|string|max:255',
			'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
			'penyelenggara' => 'required|string|max:255',
			'tempat' => 'nullable|string|max:255',
			'tanggal_mulai' => 'required|date',
			'tanggal_selesai' => 'required|date',
			'jp' => 'required|integer|min:1',
			'status' => 'required|in:selesai,sedang_berjalan,akan_datang',
			'deskripsi' => 'nullable|string',
			'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
		]);

		$oldJp = $pelatihan->jp;
		$oldStatus = $pelatihan->status;

		if ($request->hasFile('sertifikat')) {
			// Delete old certificate
			if ($pelatihan->sertifikat_path) {
				Storage::disk('public')->delete($pelatihan->sertifikat_path);
			}
			$path = $request->file('sertifikat')->store('sertifikat', 'public');
			$validated['sertifikat_path'] = $path;
		}

		$pelatihan->update($validated);

		// Update JP pegawai
		$pegawai = $pelatihan->pegawai;

		// Kurangi JP lama jika sebelumnya selesai
		if ($oldStatus == 'selesai') {
			$pegawai->jp_tercapai -= $oldJp;
		}

		// Tambah JP baru jika sekarang selesai
		if ($pelatihan->status == 'selesai') {
			$pegawai->jp_tercapai += $pelatihan->jp;
		}

		$pegawai->save();

		return redirect()->route('pelatihan.index')->with('success', 'Data pelatihan berhasil diperbarui.');
	}

	public function destroy(Pelatihan $pelatihan)
	{
		// Update JP pegawai jika pelatihan selesai
		if ($pelatihan->status == 'selesai') {
			$pegawai = $pelatihan->pegawai;
			$pegawai->jp_tercapai -= $pelatihan->jp;
			$pegawai->save();
		}

		// Delete certificate file
		if ($pelatihan->sertifikat_path) {
			Storage::disk('public')->delete($pelatihan->sertifikat_path);
		}

		$pelatihan->delete();

		return redirect()->route('pelatihan.index')->with('success', 'Data pelatihan berhasil dihapus.');
	}
}
