@extends('layout.app')

@section('title', 'Dashboard - SIMPEG Auto SPA')

@section('content')
<div class="p-3 sm:p-4 md:p-6">
	<!-- Statistics Cards -->
	<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-6 md:mb-8">
		<div
			class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-3 sm:p-4 md:p-6 text-white border-compact card-compact">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
				<div class="mb-2 sm:mb-0">
					<p class="text-white/90 text-xs sm:text-sm font-medium">Total Pegawai</p>
					<p class="text-xl sm:text-2xl md:text-3xl font-bold">{{ $stats['total_pegawai'] }}</p>
				</div>
				<div class="bg-blue-400 bg-opacity-50 rounded-full p-2 sm:p-3 self-end sm:self-auto">
					<i class="fas fa-users text-sm sm:text-lg md:text-xl text-blue-700"></i>
				</div>
			</div>
		</div>

		<div
			class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-3 sm:p-4 md:p-6 text-white border-compact card-compact">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
				<div class="mb-2 sm:mb-0">
					<p class="text-white/90 text-xs sm:text-sm font-medium">Total Pelatihan</p>
					<p class="text-xl sm:text-2xl md:text-3xl font-bold">{{ $stats['total_pelatihan'] }}</p>
				</div>
				<div class="bg-green-400 bg-opacity-50 rounded-full p-2 sm:p-3 self-end sm:self-auto">
					<i class="fas fa-graduation-cap text-sm sm:text-lg md:text-xl text-green-700"></i>
				</div>
			</div>
		</div>

		<div
			class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-3 sm:p-4 md:p-6 text-white border-compact card-compact">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
				<div class="mb-2 sm:mb-0">
					<p class="text-white/90 text-xs sm:text-sm font-medium">Total JP</p>
					<p class="text-xl sm:text-2xl md:text-3xl font-bold">{{ number_format($stats['total_jp']) }}</p>
				</div>
				<div class="bg-purple-400 bg-opacity-50 rounded-full p-2 sm:p-3 self-end sm:self-auto">
					<i class="fas fa-clock text-sm sm:text-lg md:text-xl text-purple-700"></i>
				</div>
			</div>
		</div>

		<div
			class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-3 sm:p-4 md:p-6 text-white border-compact card-compact">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
				<div class="mb-2 sm:mb-0">
					<p class="text-white/90 text-xs sm:text-sm font-medium">Rata-rata Progress</p>
					<p class="text-xl sm:text-2xl md:text-3xl font-bold">{{ number_format($stats['rata_progress'], 1)
						}}%</p>
				</div>
				<div class="bg-orange-400 bg-opacity-50 rounded-full p-2 sm:p-3 self-end sm:self-auto">
					<i class="fas fa-chart-line text-sm sm:text-lg md:text-xl text-orange-700"></i>
				</div>
			</div>
		</div>
	</div>

	<!-- Charts Row -->
	<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
		<!-- Pelatihan by Jenis Chart -->
		<div class="bg-white rounded-lg shadow-lg p-3 sm:p-4 md:p-6 border-compact card-compact">
			<h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Distribusi Jenis Pelatihan</h3>
			<div class="relative h-48 sm:h-56 md:h-64">
				<canvas id="jenisChart"></canvas>
			</div>
		</div>

		<!-- Progress Chart -->
		<div class="bg-white rounded-lg shadow-lg p-3 sm:p-4 md:p-6 border-compact card-compact">
			<h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Top 10 Progress Pegawai</h3>
			<div class="relative h-48 sm:h-56 md:h-64">
				<canvas id="progressChart"></canvas>
			</div>
		</div>
	</div>

	<!-- Recent Activities Table -->
	<div class="bg-white rounded-lg shadow-lg overflow-hidden border-compact card-compact">
		<div class="px-3 sm:px-4 md:px-6 py-4 border-b border-gray-200">
			<h3 class="text-base sm:text-lg font-semibold text-gray-800">Top Performer</h3>
		</div>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th
							class="px-3 sm:px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							<span class="hidden sm:inline">Nama Pegawai</span>
							<span class="sm:hidden">Nama</span>
						</th>
						<th
							class="px-3 sm:px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							<span class="hidden sm:inline">Target JP</span>
							<span class="sm:hidden">Target</span>
						</th>
						<th
							class="px-3 sm:px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							<span class="hidden sm:inline">JP Tercapai</span>
							<span class="sm:hidden">Tercapai</span>
						</th>
						<th
							class="px-3 sm:px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Progress
						</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					@foreach($progressPegawai as $pegawai)
					<tr>
						<td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
							<div class="truncate max-w-[120px] sm:max-w-none" title="{{ $pegawai->nama_lengkap }}">
								{{ $pegawai->nama_lengkap }}
							</div>
						</td>
						<td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
							{{ number_format($pegawai->jp_target) }}
						</td>
						<td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
							{{ number_format($pegawai->jp_tercapai) }}
						</td>
						<td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
							@php
							$progress = $pegawai->jp_target > 0 ? ($pegawai->jp_tercapai / $pegawai->jp_target) * 100 :
							0;
							@endphp
							<div class="flex items-center">
								<div class="w-12 sm:w-16 bg-gray-200 rounded-full h-2 mr-2">
									<div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $progress) }}%">
									</div>
								</div>
								<span class="text-xs">{{ number_format($progress, 1) }}%</span>
							</div>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	// Jenis Pelatihan Chart
const jenisCtx = document.getElementById('jenisChart').getContext('2d');
const jenisData = @json($pelatihanByJenis);

new Chart(jenisCtx, {
    type: 'doughnut',
    data: {
        labels: jenisData.map(item => item.jenis_pelatihan),
        datasets: [{
            data: jenisData.map(item => item.total),
            backgroundColor: [
                '#3B82F6', '#10B981', '#8B5CF6', '#F59E0B',
                '#EF4444', '#06B6D4', '#6366F1'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Progress Chart
const progressCtx = document.getElementById('progressChart').getContext('2d');
const progressData = @json($progressPegawai);

new Chart(progressCtx, {
    type: 'bar',
    data: {
        labels: progressData.map(item => item.nama_lengkap.length > 15 ? item.nama_lengkap.substring(0, 15) + '...' : item.nama_lengkap),
        datasets: [{
            label: 'JP Tercapai',
            data: progressData.map(item => item.jp_tercapai),
            backgroundColor: '#3B82F6',
            borderRadius: 4,
        }, {
            label: 'JP Target',
            data: progressData.map(item => item.jp_target),
            backgroundColor: '#E5E7EB',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});
</script>
@endpush