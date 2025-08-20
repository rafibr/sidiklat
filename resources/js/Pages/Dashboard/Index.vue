<template>
	<AppLayout>
		<div class="p-3 sm:p-4 md:p-6">
			<!-- Statistics Cards -->
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-6 md:mb-8">
				<div
					class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-3 sm:p-4 md:p-6 text-white border-compact card-compact">
					<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
						<div class="mb-2 sm:mb-0">
							<p class="text-white/90 text-xs sm:text-sm font-medium">Total Pegawai</p>
							<p class="text-xl sm:text-2xl md:text-3xl font-bold">{{ stats.total_pegawai }}</p>
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
							<p class="text-xl sm:text-2xl md:text-3xl font-bold">{{ stats.total_pelatihan }}</p>
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
							<p class="text-xl sm:text-2xl md:text-3xl font-bold">{{ formatNumber(stats.total_jp) }}</p>
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
							<p class="text-xl sm:text-2xl md:text-3xl font-bold">{{ Number(stats.rata_progress ||
								0).toFixed(1) }}%</p>
						</div>
						<div class="bg-orange-400 bg-opacity-50 rounded-full p-2 sm:p-3 self-end sm:self-auto">
							<i class="fas fa-chart-line text-sm sm:text-lg md:text-xl text-orange-700"></i>
						</div>
					</div>
				</div>
			</div>

			<!-- Charts Row -->
			<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
				<!-- Pie Chart -->
				<div class="bg-white p-3 sm:p-4 md:p-6 rounded-xl shadow-lg border-compact card-compact">
					<h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Distribusi Pelatihan by
						Jenis</h3>
					<div class="flex justify-center">
						<canvas ref="jenisChart" class="max-h-64 sm:max-h-80 md:max-h-96"></canvas>
					</div>
				</div>

				<!-- Bar Chart -->
				<div class="bg-white p-3 sm:p-4 md:p-6 rounded-xl shadow-lg border-compact card-compact">
					<h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Progress Jam Pelajaran
						(JP)</h3>
					<div class="flex justify-center">
						<canvas ref="progressChart" class="max-h-64 sm:max-h-80 md:max-h-96"></canvas>
					</div>
				</div>
			</div>

			<!-- Recent Progress Table -->
			<div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 md:p-6 border-compact card-compact">
				<h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Progress Terbaru Pegawai</h3>
				<div class="overflow-x-auto">
					<table class="min-w-full text-xs sm:text-sm">
						<thead class="bg-gray-50">
							<tr>
								<th
									class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Nama</th>
								<th
									class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
									Unit Kerja</th>
								<th
									class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									JP Tercapai</th>
								<th
									class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									JP Target</th>
								<th
									class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Progress</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200">
							<tr v-for="pegawai in progressPegawai" :key="pegawai.id" class="hover:bg-gray-50">
								<td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap">
									<div class="text-sm font-medium text-gray-900">{{ pegawai.nama_lengkap }}</div>
									<div class="text-xs text-gray-500 sm:hidden">{{ pegawai.unit_kerja }}</div>
								</td>
								<td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap hidden sm:table-cell">
									<div class="text-sm text-gray-900">{{ pegawai.unit_kerja }}</div>
								</td>
								<td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap text-sm text-gray-900">
									{{ formatNumber(pegawai.jp_tercapai) }} JP
								</td>
								<td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap text-sm text-gray-900">
									{{ formatNumber(pegawai.jp_target) }} JP
								</td>
								<td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap">
									<div class="flex items-center">
										<div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
											<div class="h-2 rounded-full transition-all duration-300"
												:class="calculateProgress(pegawai) >= 80 ? 'bg-green-500' : calculateProgress(pegawai) >= 50 ? 'bg-yellow-500' : 'bg-red-500'"
												:style="{ width: Math.min(100, calculateProgress(pegawai)) + '%' }">
											</div>
										</div>
										<span class="text-xs">{{ calculateProgress(pegawai).toFixed(1) }}%</span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
	components: {
		AppLayout,
	},
	props: {
		stats: Object,
		pelatihanByJenis: Array,
		progressPegawai: Array,
	},
	mounted() {
		this.initCharts();
	},
	methods: {
		formatNumber(num) {
			return new Intl.NumberFormat().format(num);
		},
		calculateProgress(pegawai) {
			return pegawai.jp_target > 0 ? (pegawai.jp_tercapai / pegawai.jp_target) * 100 : 0;
		},
		async initCharts() {
			// Import Chart.js dynamically to avoid SSR issues
			const Chart = (await import('chart.js/auto')).default;

			// Jenis Chart
			const jenisCtx = this.$refs.jenisChart.getContext('2d');
			new Chart(jenisCtx, {
				type: 'pie',
				data: {
					labels: this.pelatihanByJenis.map(item => item.jenis_pelatihan),
					datasets: [{
						data: this.pelatihanByJenis.map(item => item.total),
						backgroundColor: [
							'#3B82F6', // Blue
							'#10B981', // Green
							'#8B5CF6', // Purple
							'#F59E0B', // Amber
							'#EF4444', // Red
							'#6366F1'  // Indigo
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
			const progressCtx = this.$refs.progressChart.getContext('2d');
			new Chart(progressCtx, {
				type: 'bar',
				data: {
					labels: this.progressPegawai.map(item =>
						item.nama_lengkap.length > 15 ? item.nama_lengkap.substring(0, 15) + '...' : item.nama_lengkap
					),
					datasets: [{
						label: 'JP Tercapai',
						data: this.progressPegawai.map(item => item.jp_tercapai),
						backgroundColor: '#3B82F6',
						borderRadius: 4,
					}, {
						label: 'JP Target',
						data: this.progressPegawai.map(item => item.jp_target),
						backgroundColor: '#E5E7EB',
						borderRadius: 4,
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							position: 'top'
						}
					},
					scales: {
						y: {
							beginAtZero: true,
							ticks: {
								stepSize: 10
							}
						}
					}
				}
			});
		}
	}
};
</script>
