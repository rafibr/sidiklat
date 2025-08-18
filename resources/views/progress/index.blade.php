@extends('layout.app')

@section('title', 'Progress JP - SIMPEG Auto SPA')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Progress Jam Pelajaran (JP) Pegawai</h2>

        <!-- Filters -->
        <form method="GET" class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pegawai</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama pegawai..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja</label>
                    <select name="unit_kerja"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Unit</option>
                        @foreach($unitKerjas as $unit)
                        <option value="{{ $unit }}" {{ request('unit_kerja')==$unit ? 'selected' : '' }}>
                            {{ $unit }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors mr-2">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('progress') }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Progress Cards -->
    <div class="grid gap-6">
        @foreach($pegawais as $pegawai)
        @php
        $progress = $pegawai->jp_target > 0 ? ($pegawai->jp_tercapai / $pegawai->jp_target) * 100 : 0;
        $progressColor = $progress >= 80 ? 'green' : ($progress >= 50 ? 'yellow' : 'red');
        @endphp

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                    <div class="mb-4 lg:mb-0">
                        <h3 class="text-xl font-semibold text-gray-800">{{ $pegawai->nama_lengkap }}</h3>
                        <div class="text-sm text-gray-600 mt-1">
                            <span class="mr-4"><strong>NIP:</strong> {{ $pegawai->nip ?? 'Tidak Ada' }}</span>
                            <span class="mr-4"><strong>Pangkat:</strong> {{ $pegawai->pangkat_golongan }}</span>
                            <span><strong>Unit:</strong> {{ $pegawai->unit_kerja }}</span>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">
                            {{ number_format($pegawai->jp_tercapai) }} / {{ number_format($pegawai->jp_target) }} JP
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ number_format($progress, 1) }}% tercapai
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Progress JP</span>
                        <span>{{ number_format($progress, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="h-4 rounded-full transition-all duration-300
                                {{ $progressColor == 'green' ? 'bg-green-500' : ($progressColor == 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }}"
                            style="width: {{ min(100, $progress) }}%">
                        </div>
                    </div>
                </div>

                <!-- Recent Pelatihan -->
                @if($pegawai->pelatihans->count() > 0)
                <div class="mt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Pelatihan Terakhir:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pegawai->pelatihans->take(3) as $pelatihan)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{
                                            $pelatihan->jenis_pelatihan == 'Diklat Struktural' ? 'bg-blue-100 text-blue-800' :
                                            ($pelatihan->jenis_pelatihan == 'Diklat Fungsional' ? 'bg-green-100 text-green-800' :
                                            ($pelatihan->jenis_pelatihan == 'Diklat Teknis' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))
                                        }}">
                            {{ $pelatihan->nama_pelatihan }} ({{ $pelatihan->jp }} JP)
                        </span>
                        @endforeach
                        @if($pegawai->pelatihans->count() > 3)
                        <span class="text-xs text-gray-500">
                            +{{ $pegawai->pelatihans->count() - 3 }} lainnya
                        </span>
                        @endif
                    </div>
                </div>
                @else
                <div class="mt-4 text-sm text-gray-500 italic">
                    Belum ada data pelatihan
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $pegawais->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any interactive features here if needed
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    document.querySelector('select[name="unit_kerja"]').addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endpush