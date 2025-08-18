@extends('layout.app')

@section('title', 'Perbandingan Data Pelatihan')

@section('content')
<div class="p-3 sm:p-4 md:p-6">
	<h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Perbandingan Data Pelatihan</h2>

	<!-- Summary Cards -->
	<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 md:gap-6 mb-6 md:mb-8">
		<div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 md:p-6 rounded-xl text-white">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-blue-100 text-xs sm:text-sm">Pelatihan {{ $currentYear }}</p>
					<p class="text-2xl md:text-3xl font-bold">{{ $pelatihanCurrentYear }}</p>
				</div>
				<div class="bg-white/20 p-2 md:p-3 rounded-lg">
					<i class="fas fa-calendar-alt text-lg md:text-2xl"></i>
				</div>
			</div>
		</div>

		<div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4 md:p-6 rounded-xl text-white">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-purple-100 text-xs sm:text-sm">Pelatihan {{ $lastYear }}</p>
					<p class="text-2xl md:text-3xl font-bold">{{ $pelatihanLastYear }}</p>
				</div>
				<div class="bg-white/20 p-2 md:p-3 rounded-lg">
					<i class="fas fa-history text-lg md:text-2xl"></i>
				</div>
			</div>
		</div>

		<div class="bg-gradient-to-r from-green-500 to-green-600 p-4 md:p-6 rounded-xl text-white">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-green-100 text-xs sm:text-sm">Perubahan</p>
					@php
					$change = $pelatihanLastYear > 0 ? (($pelatihanCurrentYear - $pelatihanLastYear) /
					$pelatihanLastYear) * 100 : 0;
					@endphp
					<p class="text-2xl md:text-3xl font-bold">{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1)
						}}%</p>
				</div>
				<div class="bg-white/20 p-2 md:p-3 rounded-lg">
					<i class="fas fa-chart-line text-lg md:text-2xl"></i>
				</div>
			</div>
		</div>
	</div>

	<!-- Charts Row -->
	<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
		<!-- Comparison by Type Chart -->
		<div class="bg-white p-3 sm:p-4 md:p-6 rounded-xl shadow-lg">
			<h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800">Perbandingan Per Jenis Pelatihan
			</h3>
			<div class="h-48 sm:h-60 md:h-80">
				<canvas id="comparisonChart"></canvas>
			</div>
		</div>

		<!-- Monthly Trend Chart -->
		<div class="bg-white p-3 sm:p-4 md:p-6 rounded-xl shadow-lg">
			<h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800">Tren Bulanan</h3>
			<div class="h-48 sm:h-60 md:h-80">
				<canvas id="trendChart"></canvas>
			</div>
		</div>
	</div>

	<!-- Detailed Comparison Table -->
	<div class="bg-white rounded-xl shadow-lg overflow-hidden">
		<div class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 border-b border-gray-200">
			<h3 class="text-base sm:text-lg font-semibold text-gray-800">Detail Perbandingan Per Jenis</h3>
		</div>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th
							class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Jenis Pelatihan
						</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							{{ $currentYear }}
						</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							{{ $lastYear }}
						</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Selisih
						</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Perubahan (%)
						</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					@foreach($comparisonData as $data)
					<tr>
						<td class="px-6 py-4 whitespace-nowrap">
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{
                                    $data['jenis'] == 'Diklat Struktural' ? 'bg-blue-100 text-blue-800' :
                                    ($data['jenis'] == 'Diklat Fungsional' ? 'bg-green-100 text-green-800' :
                                    ($data['jenis'] == 'Diklat Teknis' ? 'bg-purple-100 text-purple-800' :
                                    ($data['jenis'] == 'Workshop' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')))
                                }}">
								{{ $data['jenis'] }}
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
							{{ $data['current'] }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							{{ $data['last'] }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
							@php
							$diff = $data['current'] - $data['last'];
							@endphp
							<span class="{{ $diff >= 0 ? 'text-green-600' : 'text-red-600' }}">
								{{ $diff >= 0 ? '+' : '' }}{{ $diff }}
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							<span
								class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $data['change'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
								{{ $data['change'] >= 0 ? '+' : '' }}{{ number_format($data['change'], 1) }}%
							</span>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>

@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function() {
    // Comparison Chart
    const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
    const comparisonChart = new Chart(comparisonCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($comparisonData, 'jenis')) !!},
            datasets: [{
                label: '{{ $currentYear }}',
                data: {!! json_encode(array_column($comparisonData, 'current')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }, {
                label: '{{ $lastYear }}',
                data: {!! json_encode(array_column($comparisonData, 'last')) !!},
                backgroundColor: 'rgba(139, 92, 246, 0.6)',
                borderColor: 'rgba(139, 92, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });

    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

    const trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: monthNames,
            datasets: [{
                label: '{{ $currentYear }}',
                data: {!! json_encode(array_column($monthlyData, 'current')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: '{{ $lastYear }}',
                data: {!! json_encode(array_column($monthlyData, 'last')) !!},
                borderColor: 'rgb(139, 92, 246)',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection