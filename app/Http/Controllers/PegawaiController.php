<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::query();

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

        // Sort by created_at desc by default
        $pegawais = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get JP default setting (you might want to store this in a settings table)
        $jpDefault = config('app.jp_default', 20); // fallback to 20

        return Inertia::render('Pegawai/Index', [
            'pegawais' => $pegawais,
            'jpDefault' => $jpDefault,
            'filters' => $request->only(['search'])
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

        // Set default JP target if not provided
        if (!isset($validated['jp_target']) || $validated['jp_target'] === null) {
            $validated['jp_target'] = config('app.jp_default', 20);
        }

        $pegawai = Pegawai::create($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai)
    {
        // Load pelatihans with jenis_pelatihan relation
        $pegawai->load(['pelatihans.jenisPelatihan']);

        // Group pelatihans by year (try to extract year from tanggal_mulai, fallback to created_at year)
        $pelatihansByYear = collect($pegawai->pelatihans)->groupBy(function ($pel) {
            // tanggal_mulai stored as string; attempt to parse year
            $year = null;
            if (!empty($pel->tanggal_mulai)) {
                if (preg_match('/(\d{4})/', $pel->tanggal_mulai, $m)) {
                    $year = $m[1];
                }
            }
            if (!$year && $pel->created_at) {
                $year = $pel->created_at->format('Y');
            }
            return $year ?? 'Unknown';
        })->map(function ($pelatihansInYear) {
            $totalJP = $pelatihansInYear->sum('jp');
            return [
                'pelatihan' => $pelatihansInYear->values(),
                'totalJP' => $totalJP
            ];
        })->sortKeysDesc();

        return Inertia::render('Pegawai/Show', [
            'pegawai' => $pegawai,
            'pelatihansByYear' => $pelatihansByYear,
        ]);
    }

    public function edit(Pegawai $pegawai)
    {
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

        $pegawai->update($validated);

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
            'apply_to_existing' => 'boolean'
        ]);

        // Here you might want to store this in a settings table
        // For now, we'll update config or use environment variable
        // This is a simple implementation - in production, use a proper settings system

        if ($validated['apply_to_existing']) {
            // Update all pegawais that don't have a custom jp_target
            Pegawai::whereNull('jp_target')->orWhere('jp_target', 0)->update(['jp_target' => $validated['jp_default']]);
        }

        // Store the new default (you might want to implement a proper settings system)
        // For now, this would need to be handled at the application level

        return redirect()->route('pegawai.index')->with('success', 'JP default berhasil diupdate.');
    }
}
