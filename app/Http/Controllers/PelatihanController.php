<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use App\Models\Pegawai;
use App\Models\JenisPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('tanggal_mulai', $request->year);
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

        // Allow client to request per-page via query param (default 25, max 200)
        $perPage = (int) $request->get('per_page', 25);
        if ($perPage <= 0) {
            $perPage = 25;
        }
        $perPage = min($perPage, 200);

        $pelatihans = $query->latest()->paginate($perPage)->withQueryString();
        // Provide jenis list from DB (id + nama) for filters/forms
        $jenisPelatihan = JenisPelatihan::select('id', 'nama')->orderBy('nama')->get();
        // Provide pegawai list for inline editing
        $pegawais = Pegawai::select('id', 'nama_lengkap')->orderBy('nama_lengkap')->get();

        // Get available years from pelatihan data
        $availableYears = Pelatihan::selectRaw('YEAR(tanggal_mulai) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->values();

        return Inertia::render('Pelatihan/Index', [
            'pelatihans' => $pelatihans,
            'jenisPelatihan' => $jenisPelatihan,
            'pegawais' => $pegawais,
            'availableYears' => $availableYears,
            // expose totals and current per-page so frontend can display them
            'total_pelatihans' => $pelatihans->total(),
            'per_page' => $perPage,
        ]);
    }

    /**
     * Export pelatihans to CSV/Excel/PDF
     * Query params: format=csv|xls|pdf and same filters as index
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $query = Pelatihan::with(['pegawai', 'jenisPelatihan']);

        if ($request->filled('jenis')) {
            $jenisId = JenisPelatihan::where('nama', $request->jenis)->value('id');
            if ($jenisId) $query->where('jenis_pelatihan_id', $jenisId);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal_mulai', $request->year);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pelatihan', 'like', '%' . $request->search . '%')
                    ->orWhereHas('pegawai', function ($subQ) use ($request) {
                        $subQ->where('nama_lengkap', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $items = $query->latest()->get();

        if ($format === 'pdf') {
            // Try using dompdf if installed
            if (class_exists('\Dompdf\Dompdf') || app()->bound('dompdf')) {
                $html = view('pelatihan.export_pdf', ['items' => $items])->render();
                $pdf = app()->make('dompdf.wrapper');
                $pdf->loadHTML($html);
                return $pdf->download('pelatihan_' . date('Ymd_His') . '.pdf');
            }

            // Fallback: return HTML file for printing
            $html = view('pelatihan.export_pdf', ['items' => $items])->render();
            return response($html, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'attachment; filename="pelatihan_' . date('Ymd_His') . '.html"'
            ]);
        }

        if ($format === 'xls') {
            // Generate Excel-compatible HTML for .xls format
            $html = view('pelatihan.export_xls', ['items' => $items])->render();

            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"pelatihan_" . date('Ymd_His') . ".xls\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ]);
        }

        // CSV format
        $filename = 'pelatihan_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($items) {
            $out = fopen('php://output', 'w');
            // Header row - menghapus kolom NIP terpisah
            // NOTE: omit Status and Sertifikat columns per request
            fputcsv($out, ['ID', 'Pegawai', 'Nama Pelatihan', 'Jenis', 'Penyelenggara', 'Tanggal Mulai', 'Tanggal Selesai', 'JP']);
            foreach ($items as $it) {
                // Gabungkan nama dan NIP dalam satu kolom (untuk CSV, ganti newline dengan space)
                $pegawaiInfo = optional($it->pegawai)->nama_lengkap;
                if (optional($it->pegawai)->nip) {
                    $pegawaiInfo .= " - " . optional($it->pegawai)->nip;
                }

                // Pastikan jenis pelatihan dimuat dengan benar
                $jenisNama = '';
                if ($it->jenisPelatihan) {
                    $jenisNama = $it->jenisPelatihan->nama;
                } else {
                    $jenisNama = $it->getJenisNama() ?: '';
                }

                // Format tanggal tanpa jam
                $tanggalMulai = $it->tanggal_mulai ? date('Y-m-d', strtotime($it->tanggal_mulai)) : '';
                $tanggalSelesai = $it->tanggal_selesai ? date('Y-m-d', strtotime($it->tanggal_selesai)) : '';

                // Do not include status or sertifikat_path in CSV export
                fputcsv($out, [
                    $it->id,
                    $pegawaiInfo,
                    $it->nama_pelatihan,
                    $jenisNama,
                    $it->penyelenggara,
                    $tanggalMulai,
                    $tanggalSelesai,
                    $it->jp,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
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
        // Debug request data
        Log::info('Store request data:', $request->all());

        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'nama_pelatihan' => 'required|string|max:255',
            'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
            'penyelenggara' => 'required|string|max:255',
            'tempat' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'jp' => 'required|integer|min:1',
            'status' => 'nullable|in:selesai,sedang_berjalan,akan_datang',
            'deskripsi' => 'nullable|string',
            'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = 'selesai';
        }

        if ($request->hasFile('sertifikat')) {
            $path = $request->file('sertifikat')->store('sertifikat', 'public');
            $validated['sertifikat_path'] = $path;
        }

        Log::info('Validated data:', $validated);

        $pelatihan = Pelatihan::create($validated);

        // Note: JP tercapai is now calculated dynamically from yearly targets
        // No need to manually update jp_tercapai field

        // For AJAX requests, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelatihan berhasil ditambahkan.',
                'data' => $pelatihan->load(['pegawai', 'jenisPelatihan'])
            ]);
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
            'status' => 'nullable|in:selesai,sedang_berjalan,akan_datang',
            'deskripsi' => 'nullable|string',
            'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $oldJp = $pelatihan->jp;
        $oldStatus = $pelatihan->status;

        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = $pelatihan->status ?? 'selesai';
        }

        if ($request->hasFile('sertifikat')) {
            // Delete old certificate
            if ($pelatihan->sertifikat_path) {
                Storage::disk('public')->delete($pelatihan->sertifikat_path);
            }
            $path = $request->file('sertifikat')->store('sertifikat', 'public');
            $validated['sertifikat_path'] = $path;
        }

        // If frontend requests to replace/remove the file without uploading a new one
        // (e.g. sends replace_file=true but no file), delete existing file and
        // clear the sertifikat_path so the DB reflects removal.
        if ($request->has('replace_file') && !$request->hasFile('sertifikat')) {
            if ($pelatihan->sertifikat_path) {
                Storage::disk('public')->delete($pelatihan->sertifikat_path);
            }
            $validated['sertifikat_path'] = null;
        }

        $pelatihan->update($validated);

        // Note: JP tercapai is now calculated dynamically from yearly targets
        // No need to manually update jp_tercapai field

        // For AJAX requests, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelatihan berhasil diperbarui.',
                'data' => $pelatihan->load(['pegawai', 'jenisPelatihan'])
            ]);
        }

        return redirect()->route('pelatihan.index')->with('success', 'Data pelatihan berhasil diperbarui.');
    }

    public function destroy(Request $request, Pelatihan $pelatihan)
    {
        // Note: JP tercapai is now calculated dynamically from yearly targets
        // No need to manually update jp_tercapai field

        // Delete certificate file
        if ($pelatihan->sertifikat_path) {
            Storage::disk('public')->delete($pelatihan->sertifikat_path);
        }

        $pelatihan->delete();

        // For AJAX requests, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelatihan berhasil dihapus.'
            ]);
        }

        return redirect()->route('pelatihan.index')->with('success', 'Data pelatihan berhasil dihapus.');
    }
}
