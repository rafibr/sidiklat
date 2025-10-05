<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\PegawaiJpTarget;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::query();
        $currentYear = date('Y');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('unit_kerja', 'like', "%{$search}%")
                    ->orWhere('pangkat_golongan', 'like', "%{$search}%");
            });
        }

        // Get per_page from request or default to 10
        $perPage = $request->get('per_page', 10);

        // Sort by created_at desc by default
        $pegawais = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->only(['search', 'per_page']));

        // Calculate stats for each pegawai based on yearly targets
        $pegawais->getCollection()->transform(function ($pegawai) use ($currentYear) {
            $jpTarget = $pegawai->getJpTargetForYear($currentYear);
            $jpTercapai = $pegawai->pelatihans()
                ->whereYear('tanggal_mulai', $currentYear)
                ->sum('jp');

            $pegawai->jp_target_display = $jpTarget;
            $pegawai->jp_tercapai = $jpTercapai;
            $pegawai->progress_percentage = $jpTarget > 0 ? min(($jpTercapai / $jpTarget) * 100, 100) : 0;
            $pegawai->is_target_reached = $jpTercapai >= $jpTarget;

            return $pegawai;
        });

        // Get JP default setting (you might want to store this in a settings table)
        $jpDefault = config('app.jp_default', 20); // fallback to 20

        return Inertia::render('Pegawai/Index', [
            'pegawais' => $pegawais,
            'jpDefault' => $jpDefault,
            'currentYear' => $currentYear,
            'filters' => [
                'search' => $request->get('search'),
                'per_page' => $perPage,
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Pegawai/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:255|unique:pegawais',
            'nama_lengkap' => 'required|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'unit_kerja' => 'nullable|string|max:255',
            'status' => 'required|string|in:aktif,tidak_aktif,pensiun',
            'tanggal_pengangkatan' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'jp_target' => 'nullable|integer|min:0',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
        ]);

        // Remove jp_target from validated data since we'll handle it via yearly targets
        $jpTargetValue = $validated['jp_target'] ?? config('app.jp_default', 20);
        unset($validated['jp_target']);

        $pegawai = Pegawai::create($validated);

        // Create JP target for current year
        $currentYear = date('Y');
        PegawaiJpTarget::create([
            'pegawai_id' => $pegawai->id,
            'tahun' => $currentYear,
            'jp_target' => $jpTargetValue,
            'jp_tercapai' => 0
        ]);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai)
    {
        $currentYear = date('Y');

        // Load pelatihans untuk pegawai ini
        $pelatihans = $pegawai->pelatihans()
            ->with('jenisPelatihan')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Hitung statistik berdasarkan tahun
        $yearlyStats = [];
        $availableYears = $pelatihans->pluck('tanggal_mulai')
            ->map(fn($date) => date('Y', strtotime($date)))
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Pastikan tahun berjalan ada dalam daftar
        if (!in_array($currentYear, $availableYears)) {
            $availableYears[] = $currentYear;
            sort($availableYears);
        }

        foreach ($availableYears as $year) {
            $pelatihansTahunIni = $pelatihans->filter(function ($pelatihan) use ($year) {
                return date('Y', strtotime($pelatihan->tanggal_mulai)) == $year;
            });

            $jpTercapai = $pelatihansTahunIni->sum('jp');
            $jpTarget = $pegawai->getJpTargetForYear($year);

            $yearlyStats[$year] = [
                'jp_tercapai' => $jpTercapai,
                'jp_target' => $jpTarget,
                'progress_percentage' => $jpTarget > 0 ? min(($jpTercapai / $jpTarget) * 100, 100) : 0,
                'is_target_reached' => $jpTercapai >= $jpTarget,
                'total_pelatihan' => $pelatihansTahunIni->count()
            ];
        }

        return Inertia::render('Pegawai/Show', [
            'pegawai' => $pegawai,
            'pelatihans' => $pelatihans,
            'yearlyStats' => $yearlyStats,
            'availableYears' => $availableYears,
            'currentYear' => $currentYear
        ]);
    }

    public function edit(Pegawai $pegawai)
    {
        $currentYear = date('Y');

        // Add current year's JP target to pegawai data
        $pegawai->jp_target = $pegawai->getJpTargetForYear($currentYear);

        return Inertia::render('Pegawai/Edit', [
            'pegawai' => $pegawai
        ]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:255|unique:pegawais,nip,' . $pegawai->id,
            'nama_lengkap' => 'required|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'unit_kerja' => 'nullable|string|max:255',
            'status' => 'required|string|in:aktif,tidak_aktif,pensiun',
            'tanggal_pengangkatan' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'jp_target' => 'nullable|integer|min:0',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
        ]);

        // Handle JP target separately for current year
        $jpTargetValue = $validated['jp_target'] ?? null;
        unset($validated['jp_target']);

        $pegawai->update($validated);

        // Update or create JP target for current year if provided
        if ($jpTargetValue !== null) {
            $currentYear = date('Y');
            PegawaiJpTarget::updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'tahun' => $currentYear
                ],
                [
                    'jp_target' => $jpTargetValue
                ]
            );
        }

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diupdate.');
    }

    public function destroy(Pegawai $pegawai)
    {
        // Check if pegawai has pelatihans
        if ($pegawai->pelatihans()->count() > 0) {
            return redirect()->route('pegawai.index')->with('error', 'Pegawai tidak dapat dihapus karena memiliki data pelatihan.');
        }

        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }

    public function updateJpDefault(Request $request)
    {
        $validated = $request->validate([
            'jp_default' => 'required|integer|min:0|max:1000',
            'tahun' => 'required|integer|min:2020|max:2050'
        ]);

        $year = $validated['tahun'];
        $jpDefault = $validated['jp_default'];

        // Update JP targets for all pegawais for the specified year
        $pegawais = Pegawai::all();

        foreach ($pegawais as $pegawai) {
            PegawaiJpTarget::updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'tahun' => $year
                ],
                [
                    'jp_target' => $jpDefault
                ]
            );
        }

        return redirect()->route('pegawai.index')
            ->with('success', "JP target untuk tahun {$year} berhasil diupdate untuk semua pegawai.");
    }
}
