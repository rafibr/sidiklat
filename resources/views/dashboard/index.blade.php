@extends('layout.app')

@section('title', 'Dashboard - SIMPEG Auto SPA')

@section('content')
<div class="p-6">
	<!-- Statistics Cards -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
		<div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-blue-100 text-sm font-medium">Total Pegawai</p>
					<p class="text-3xl font-bold">{{ $stats['total_pegawai'] }}</p>
				</div>
				<div class="bg-blue-400 bg-opacity-50 rounded-full p-3">
					<i class="fas fa-users text-xl"></i>
				</div>
			</div>
		</div>

		<div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-green-100 text-sm font-medium">Total Pelatihan</p>
					<p class="text-3xl font-bold">{{ $stats['total_pelatihan'] }}</p>
				</div>
				<div class="bg-green-400 bg-opacity-50 rounded-full p-3">
					<i class="fas fa-graduation-cap text-xl"></i>
				</div>
			</div>
		</div>

		<div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-purple-100 text-sm font-medium">Total JP</p>
					<p class="text-3xl font-bold">{{ number_format($stats['total_jp']) }}</p>
				</div>
				<div class="bg-purple-400 bg-opacity-50 rounded-full p-3">
					<i class="fas fa-clock text-xl"></i>
				</div>
			</div>
		</div>

		<div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-orange-100 text-sm font-medium">Rata-rata Progress</p>
					<p class="text-3xl font-bold">{{ number_format($stats['rata_progress'], 1) }}%</p>
				</div>
				<div class="bg-orange-400 bg-opacity-50 rounded-full p-3">
					<i class="fas fa-chart-line text-xl"></i>
				</div>
			</div>
		</div>
	</div>

	<!-- Charts Row -->
	<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
		<!-- Pelatihan by Jenis Chart -->
		<div class="bg-white rounded-lg shadow-lg p-6">
			<h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Jenis Pelatihan</h3>
			<div class="relative h-64">
				<canvas id="jenisChart"></canvas>
			</div>
		</div>

		<!-- Progress Chart -->
		<div class="bg-white rounded-lg shadow-lg p-6">
			<h3 class="text-lg font-semibold text-gray-800 mb-4">Top 10 Progress Pegawai</h3>
			<div class="relative h-64">
				<canvas id="progressChart"></canvas>
			</div>
		</div>
	</div>

	<!-- Recent Activities Table -->
	<div class="bg-white rounded-lg shadow-lg p-6">
		<h3 class="text-lg font-semibold text-gray-800 mb-4">Top Performer</h3>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
						</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Target JP</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JP
							Tercapai</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Progress</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					@foreach($progressPegawai as $pegawai)
					<tr>
						<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
							{{ $pegawai->nama_lengkap }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							{{ number_format($pegawai->jp_target) }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							{{ number_format($pegawai->jp_tercapai) }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							@php
							$progress = $pegawai->jp_target > 0 ? ($pegawai->jp_tercapai / $pegawai->jp_target) * 100 :
							0;
							@endphp
							<div class="flex items-center">
								<div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
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