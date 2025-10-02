<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use App\Models\Pegawai;
use App\Models\JenisPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PelatihanController extends Controller
{
    public function comparison()
    {
        // Statistik untuk perbandingan
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        $pelatihanCurrentYear = Pelatihan::whereYear('created_at', $currentYear)->count();
        $pelatihanLastYear = Pelatihan::whereYear('created_at', $lastYear)->count();

        $jenisPelatihan = JenisPelatihan::orderBy('nama')->pluck('nama')->toArray();

        $comparisonData = [];
        foreach ($jenisPelatihan as $jenis) {
            $currentCount = Pelatihan::ofJenis($jenis)
                ->whereYear('created_at', $currentYear)
                ->count();
            $lastCount = Pelatihan::ofJenis($jenis)
                ->whereYear('created_at', $lastYear)
                ->count();

            $comparisonData[] = [
                'jenis' => $jenis,
                'current' => $currentCount,
                'last' => $lastCount,
                'change' => $lastCount > 0 ? (($currentCount - $lastCount) / $lastCount) * 100 : 0
            ];
        }

        // Data bulanan untuk trend
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $currentMonthCount = Pelatihan::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->count();
            $lastMonthCount = Pelatihan::whereYear('created_at', $lastYear)
                ->whereMonth('created_at', $i)
                ->count();

            $monthlyData[] = [
                'month' => $i,
                'current' => $currentMonthCount,
                'last' => $lastMonthCount
            ];
        }

        return view('pelatihan.comparison', compact(
            'currentYear',
            'lastYear',
            'pelatihanCurrentYear',
            'pelatihanLastYear',
            'comparisonData',
            'monthlyData'
        ));
    }

    public function index(Request $request)
    {
        $query = Pelatihan::with(['pegawai', 'jenisPelatihan']);

        // Filter by jenis pelatihan
        if ($request->filled('jenis')) {
            $query->ofJenis($request->jenis);
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
        $jenisPelatihan = JenisPelatihan::orderBy('nama')->pluck('nama');

        return view('pelatihan.index', compact('pelatihans', 'jenisPelatihan'));
    }

    public function create()
    {
        $pegawais = Pegawai::orderBy('nama_lengkap')->get();
        $jenisPelatihan = JenisPelatihan::orderBy('nama')->pluck('nama');

        return view('pelatihan.create', compact('pegawais', 'jenisPelatihan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'nama_pelatihan' => 'required|string|max:255',
            'jenis_pelatihan' => 'required|string|exists:jenis_pelatihans,nama',
            'penyelenggara' => 'required|string|max:255',
            'tempat' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
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
        return view('pelatihan.show', compact('pelatihan'));
    }

    public function edit(Pelatihan $pelatihan)
    {
        $pegawais = Pegawai::orderBy('nama_lengkap')->get();
        $jenisPelatihan = JenisPelatihan::orderBy('nama')->pluck('nama');

        return view('pelatihan.edit', compact('pelatihan', 'pegawais', 'jenisPelatihan'));
    }

    public function update(Request $request, Pelatihan $pelatihan)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'nama_pelatihan' => 'required|string|max:255',
            'jenis_pelatihan' => 'required|string|exists:jenis_pelatihans,nama',
            'penyelenggara' => 'required|string|max:255',
            'tempat' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
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
