<template>
	<AppLayout>
		<div class="relative p-3 sm:p-4 md:p-6 animate-slide-up">
			<!-- Loading overlay -->
			<div v-if="loading" class="absolute inset-0 bg-white/60 z-50 flex items-center justify-center">
				<div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
			</div>
			<h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6 animate-fade-scale">
				Perbandingan Data Pelatihan
			</h2>

			<!-- Year Selection -->
			<div class="bg-gray-50 p-3 sm:p-4 rounded-lg mb-4 md:mb-6 animate-slide-left delay-100">
				<h3 class="text-sm font-medium text-gray-700 mb-3">Pilih Tahun untuk Perbandingan</h3>

				<div class="flex flex-wrap gap-2 mb-3">
					<span v-for="year in selectedYears" :key="year"
						class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
						{{ year }}
						<button @click="removeYear(year)" class="ml-2 text-blue-600 hover:text-blue-800">
							<i class="fas fa-times text-xs"></i>
						</button>
					</span>
					<span v-if="selectedYears.length === 0" class="text-sm text-gray-500">
						Belum ada tahun dipilih
					</span>
				</div>

				<div class="flex gap-2">
					<select v-model="yearToAdd"
						class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
						<option value="">Pilih tahun...</option>
						<option v-for="year in availableYearsToAdd" :key="year" :value="year">
							{{ year }}
						</option>
					</select>
					<button @click="addYear" :disabled="!yearToAdd"
						class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
						<i class="fas fa-plus mr-1"></i>
						Tambah
					</button>
					<button @click="updateComparison" :disabled="selectedYears.length < 1"
						class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
						<i class="fas fa-sync mr-1"></i>
						Update
					</button>
				</div>
			</div>

			<!-- Summary Cards -->
			<div
				class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 mb-6 md:mb-8 animate-slide-up delay-200">
				<div v-for="(count, year) in yearlyData" :key="year"
					class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-xl text-white">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-white/90 text-sm">Pelatihan {{ year }}</p>
							<p class="text-2xl font-bold">{{ count }}</p>
						</div>
						<div class="bg-white/20 p-2 rounded-lg">
							<i class="fas fa-calendar-alt text-lg"></i>
						</div>
					</div>
				</div>
			</div>

			<!-- Charts Row -->
			<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
				<!-- Comparison by Type Chart -->
				<div class="bg-white p-3 sm:p-4 md:p-6 rounded-xl shadow-lg border-compact card-compact">
					<h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800">Perbandingan Per Jenis
						Pelatihan</h3>
					<div class="h-48 sm:h-60 md:h-80">
						<template v-if="hasComparisonData">
							<canvas ref="comparisonChart"></canvas>
						</template>
						<template v-else>
							<div class="h-full flex items-center justify-center text-sm text-gray-500">Belum ada data
								untuk perbandingan per jenis.</div>
						</template>
					</div>
				</div>

				<!-- Monthly Trend Chart -->
				<div class="bg-white p-3 sm:p-4 md:p-6 rounded-xl shadow-lg border-compact card-compact">
					<h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800">Tren Bulanan</h3>
					<div class="h-48 sm:h-60 md:h-80">
						<template v-if="hasMonthlyData">
							<canvas ref="trendChart"></canvas>
						</template>
						<template v-else>
							<div class="h-full flex items-center justify-center text-sm text-gray-500">Belum ada data
								tren bulanan.</div>
						</template>
					</div>
				</div>
			</div>

			<!-- Detailed Comparison Table -->
			<div class="bg-white rounded-xl shadow-lg overflow-hidden border-compact card-compact">
				<div class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 border-b border-gray-200">
					<h3 class="text-base sm:text-lg font-semibold text-gray-800">Detail Perbandingan Per Jenis</h3>
				</div>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th
									class="px-3 sm:px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Jenis Pelatihan
								</th>
								<th v-for="year in selectedYears" :key="year"
									class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									{{ year }}
								</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200">
							<tr v-for="data in comparisonData" :key="data.jenis">
								<td class="px-6 py-4 whitespace-nowrap">
									<span
										class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
										:class="getJenisColor(data.jenis)">
										{{ data.jenis }}
									</span>
								</td>
								<td v-for="year in selectedYears" :key="year"
									class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
									{{ data[year] || 0 }}
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
import { router } from '@inertiajs/vue3';

export default {
	components: {
		AppLayout,
	},
	props: {
		selectedYears: Array,
		yearlyData: Object,
		comparisonData: Array,
		monthlyData: Array,
		availableYears: Array,
	},
	data() {
		return {
			yearToAdd: '',
			chartComparison: null,
			chartTrend: null,
			loading: false,
		};
	},
	computed: {
		availableYearsToAdd() {
			// ensure type-safe comparison (selectedYears may be strings)
			const selectedSet = new Set((this.selectedYears || []).map(y => String(y)));
			return (this.availableYears || []).filter(year => !selectedSet.has(String(year)));
		},
		hasComparisonData() {
			return Array.isArray(this.comparisonData) && this.comparisonData.length > 0;
		},
		hasMonthlyData() {
			return Array.isArray(this.monthlyData) && this.monthlyData.length > 0;
		}
	},
	mounted() {
		// ensure DOM refs are ready before creating charts
		this.loading = true;
		this.$nextTick(() => this.initCharts());
	},
	beforeUnmount() {
		// clean up Chart instances to avoid Chart.js trying to draw on removed canvas
		if (this.chartComparison) {
			try { this.chartComparison.destroy(); } catch (e) { }
			this.chartComparison = null;
		}
		if (this.chartTrend) {
			try { this.chartTrend.destroy(); } catch (e) { }
			this.chartTrend = null;
		}
	},
	watch: {
		selectedYears() {
			this.loading = true;
			this.initCharts();
			this.$nextTick(() => { this.loading = false; });
		},
		comparisonData() {
			this.loading = true;
			this.initCharts();
			this.$nextTick(() => { this.loading = false; });
		},
		monthlyData() {
			this.loading = true;
			this.initCharts();
			this.$nextTick(() => { this.loading = false; });
		}
	},
	methods: {
		addYear() {
			if (this.yearToAdd && !this.selectedYears.includes(this.yearToAdd)) {
				const newYears = [...this.selectedYears, this.yearToAdd];
				this.yearToAdd = '';
				this.updateComparison(newYears);
			}
		},
		removeYear(yearToRemove) {
			const newYears = this.selectedYears.filter(year => year !== yearToRemove);
			this.updateComparison(newYears);
		},
		updateComparison(years = null) {
			const yearsToCompare = years || this.selectedYears;
			if (yearsToCompare.length === 0) return;

			this.loading = true;
			router.get(route('pelatihan.comparison'), {
				years: yearsToCompare,
			}, { preserveState: true });
		},
		// Deprecated: keep for compatibility if needed
		getJenisColor(jenis) {
			return this.getJenisColorStyle(jenis);
		},

		// Return inline style object for a jenis badge with varied colors
		getJenisColorStyle(jenis) {
			if (!this._jenisColorCache) this._jenisColorCache = {};
			const key = String(jenis || '');
			if (this._jenisColorCache[key]) return this._jenisColorCache[key];

			// stable hash from string -> hue
			let hash = 0;
			for (let i = 0; i < key.length; i++) {
				hash = ((hash << 5) - hash) + key.charCodeAt(i);
				hash |= 0;
			}
			const hue = Math.abs(hash) % 360;

			// background: light pastel, text: darker for contrast
			const bg = `hsl(${hue} 70% 92%)`;
			const text = `hsl(${hue} 70% 25%)`;

			const styleObj = { backgroundColor: bg, color: text };
			this._jenisColorCache[key] = styleObj;
			return styleObj;
		},
		getDiffColor(diff) {
			return diff >= 0 ? 'text-green-600' : 'text-red-600';
		},
		getChangeColor(change) {
			return change >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
		},
		async initCharts() {
			// Import Chart.js dynamically to avoid SSR issues
			const Chart = (await import('chart.js/auto')).default;

			// destroy previous if exist
			if (this.chartComparison) {
				try { this.chartComparison.destroy(); } catch (e) { }
				this.chartComparison = null;
			}
			if (this.chartTrend) {
				try { this.chartTrend.destroy(); } catch (e) { }
				this.chartTrend = null;
			}

			// Comparison Chart
			if (this.hasComparisonData && this.$refs.comparisonChart && typeof this.$refs.comparisonChart.getContext === 'function') {
				const comparisonCanvas = this.$refs.comparisonChart;
				if (comparisonCanvas) {
					const labels = this.comparisonData.map(item => item.jenis);

					const jenisColors = labels.map(j => {
						const colorMap = {
							'Diklat Struktural': 'rgba(59,130,246,0.85)',
							'Diklat Fungsional': 'rgba(16,185,129,0.85)',
							'Diklat Teknis': 'rgba(139,92,246,0.85)',
							'Workshop': 'rgba(245,158,11,0.85)',
							'Seminar': 'rgba(239,68,68,0.85)',
							'Pelatihan Jarak Jauh': 'rgba(99,102,241,0.85)',
							'E-Learning': 'rgba(6,182,212,0.85)'
						};
						if (colorMap[j]) return colorMap[j];
						let hash = 0;
						for (let i = 0; i < j.length; i++) {
							hash = ((hash << 5) - hash) + j.charCodeAt(i);
							hash |= 0;
						}
						const hue = Math.abs(hash) % 360;
						return `hsla(${hue},70%,50%,0.85)`;
					});

					const datasets = this.selectedYears.map(year => ({
						label: String(year),
						data: this.comparisonData.map(item => item[year] || 0),
						backgroundColor: jenisColors,
						borderColor: jenisColors.map(color => color.replace('0.85', '1')),
						borderWidth: 1
					}));

					this.chartComparison = new Chart(comparisonCanvas, {
						type: 'bar',
						data: { labels, datasets },
						options: {
							animation: false,
							responsive: true,
							maintainAspectRatio: false,
							plugins: {
								legend: { position: 'top' },
								title: {
									display: true,
									text: 'Perbandingan Per Jenis Pelatihan'
								}
							},
							scales: {
								y: { beginAtZero: true, ticks: { stepSize: 1 } },
								x: { ticks: { maxRotation: 45, minRotation: 45 } }
							}
						}
					});
				}
			}

			// Trend Chart
			if (this.hasMonthlyData && this.$refs.trendChart && typeof this.$refs.trendChart.getContext === 'function') {
				const trendCanvas = this.$refs.trendChart;
				if (trendCanvas) {
					const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
					const trendYears = this.selectedYears.slice(0, 2);
					const trendColors = ['rgb(59,130,246)', 'rgb(139,92,246)'];

					const datasets = trendYears.map((year, idx) => ({
						label: String(year),
						data: this.monthlyData.map(item => item[year] || 0),
						borderColor: trendColors[idx % trendColors.length],
						backgroundColor: trendColors[idx % trendColors.length].replace('rgb', 'rgba').replace(')', ', 0.12)'),
						tension: 0.4,
						fill: true
					}));

					this.chartTrend = new Chart(trendCanvas, {
						type: 'line',
						data: { labels: monthNames, datasets },
						options: {
							animation: false,
							responsive: true,
							maintainAspectRatio: false,
							plugins: {
								legend: { position: 'top' },
								title: {
									display: true,
									text: 'Tren Bulanan'
								}
							},
							scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
						}
					});
				}
			}

			// finished initializing charts
			this.loading = false;
		}
	}
};
</script>
