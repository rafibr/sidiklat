<template>
    <AppLayout>
        <div class="p-3 sm:p-4 md:p-6">
            <!-- Year Filter -->
            <div class="mb-4 md:mb-6">
                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <label class="block text-sm font-medium text-gray-700">Filter Tahun:</label>
                        <select :value="selectedYear" @change="changeYear($event)"
                            class="w-full sm:w-auto px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option v-for="year in availableYears" :key="year" :value="year">
                                {{ year }}
                            </option>
                        </select>
                        <span class="text-sm text-gray-600">Menampilkan data untuk tahun {{ selectedYear }}</span>
                    </div>
                </div>
            </div>

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
                            <tr v-for="pegawai in progressPegawai" :key="pegawai.id"
                                class="hover:bg-gray-50 cursor-pointer" @click="goToPegawai(pegawai.id)" tabindex="0"
                                @keydown.enter="goToPegawai(pegawai.id)">
                                <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <Link :href="route('pegawai.show', { pegawai: pegawai.id })" @click.stop>{{
                                            pegawai.nama_lengkap }}</Link>
                                    </div>
                                    <div class="text-xs mt-1 sm:hidden">
                                        <span :class="getUnitPillClass(pegawai.unit_kerja)">{{ pegawai.unit_kerja
                                            }}</span>
                                    </div>
                                </td>
                                <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap hidden sm:table-cell">
                                    <div class="text-sm">
                                        <span :class="getUnitPillClass(pegawai.unit_kerja)">{{ pegawai.unit_kerja
                                        }}</span>
                                    </div>
                                </td>
                                <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatNumber(pegawai.jp_tercapai_filtered || pegawai.jp_tercapai || 0) }} JP
                                </td>
                                <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatNumber(pegawai.jp_target) }} JP
                                </td>
                                <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="h-2 rounded-full transition-all duration-300"
                                                :class="calculateProgress(pegawai) >= 80 ? 'bg-green-500' : calculateProgress(pegawai) >= 50 ? 'bg-yellow-500' : 'bg-red-500'"
                                                :style="{ width: Math.min(100, calculateProgress(pegawai) || 0) + '%' }">
                                            </div>
                                        </div>
                                        <span class="text-xs">{{ (calculateProgress(pegawai) || 0).toFixed(1) }}%</span>
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
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    components: {
        AppLayout,
        Link,
    },
    props: {
        stats: Object,
        pelatihanByJenis: Array,
        progressPegawai: Array,
        availableYears: Array,
        selectedYear: Number,
    },
    data() {
        return {
            jenisChart: null,
            progressChart: null,
        };
    },
    mounted() {
        // ensure DOM refs are ready before creating charts
        this.$nextTick(() => this.initCharts());
    },
    beforeUnmount() {
        // clean up Chart instances to avoid Chart.js trying to draw on removed canvas
        if (this.jenisChart) {
            try { this.jenisChart.destroy(); } catch (e) { }
            this.jenisChart = null;
        }
        if (this.progressChart) {
            try { this.progressChart.destroy(); } catch (e) { }
            this.progressChart = null;
        }
    },
    watch: {
        pelatihanByJenis() {
            // when props change from Inertia, update without animation to avoid flicker
            this.updateCharts(false);
        },
        progressPegawai() {
            this.updateCharts(false);
        }
    },
    methods: {
        formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        },
        calculateProgress(pegawai) {
            return pegawai.jp_target > 0 ? (pegawai.jp_tercapai_filtered / pegawai.jp_target) * 100 : 0;
        },
        // Return Tailwind classes for a colored pill based on unit name (deterministic)
        getUnitPillClass(unit) {
            if (!unit) return 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
            const palette = [
                'bg-blue-100 text-blue-800',
                'bg-green-100 text-green-800',
                'bg-purple-100 text-purple-800',
                'bg-amber-100 text-amber-800',
                'bg-red-100 text-red-800',
                'bg-indigo-100 text-indigo-800',
                'bg-teal-100 text-teal-800',
                'bg-pink-100 text-pink-800',
            ];
            // simple hash: sum char codes
            let sum = 0;
            for (let i = 0; i < unit.length; i++) sum += unit.charCodeAt(i);
            const idx = sum % palette.length;
            return `inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${palette[idx]}`;
        },
        changeYear(event) {
            const year = parseInt(event.target.value);
            router.get(route('dashboard'), { year: year }, { preserveState: true });
        },

        goToPegawai(id) {
            if (!id) return;
            // use Inertia router to navigate to pegawai detail
            router.get(route('pegawai.show', { pegawai: id }));
        },
        // Generic helpers adapted from Chart.js docs
        addData(chart, label, newData) {
            if (!chart || !chart.ctx) return;
            chart.data.labels.push(label);
            chart.data.datasets.forEach((dataset, idx) => {
                // if newData is array, use element per dataset, otherwise use same value
                if (Array.isArray(newData)) dataset.data.push(newData[idx] || 0);
                else dataset.data.push(newData);
            });
            chart.update();
        },
        removeData(chart) {
            if (!chart || !chart.ctx) return;
            chart.data.labels.pop();
            chart.data.datasets.forEach((dataset) => {
                dataset.data.pop();
            });
            chart.update();
        },
        updateConfigByMutating(chart, title) {
            if (!chart || !chart.ctx) return;
            chart.options.plugins = chart.options.plugins || {};
            chart.options.plugins.title = chart.options.plugins.title || { display: true, text: '' };
            chart.options.plugins.title.text = title;
            chart.update();
        },
        updateConfigAsNewObject(chart, options) {
            if (!chart || !chart.ctx) return;
            chart.options = options;
            chart.update();
        },
        updateScales(chart, scalesObj) {
            if (!chart || !chart.ctx) return;
            chart.options.scales = scalesObj;
            chart.update();
        },
        async initCharts() {
            // Import Chart.js dynamically to avoid SSR issues
            const Chart = (await import('chart.js/auto')).default;

            // destroy previous if exist
            if (this.jenisChart) {
                try { this.jenisChart.destroy(); } catch (e) { }
                this.jenisChart = null;
            }
            if (this.progressChart) {
                try { this.progressChart.destroy(); } catch (e) { }
                this.progressChart = null;
            }

            // Jenis Chart
            if (!this.$refs.jenisChart || typeof this.$refs.jenisChart.getContext !== 'function') {
                // refs not available yet; try again on next tick
                this.$nextTick(() => this.initCharts());
                return;
            }
            // pass the canvas element itself to Chart constructor (matches provided format)
            const jenisCanvas = this.$refs.jenisChart;
            if (!jenisCanvas) {
                this.$nextTick(() => this.initCharts());
                return;
            }
            this.jenisChart = new Chart(jenisCanvas, {
                type: 'pie',
                data: {
                    labels: this.pelatihanByJenis.map(item => item.jenis_pelatihan || 'Lainnya'),
                    datasets: [{
                        data: this.pelatihanByJenis.map(item => Number(item.total) || 0),
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
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: `Distribusi Pelatihan — ${this.selectedYear}`
                        },
                        legend: { display: true, position: 'bottom' },
                        tooltip: { enabled: true }
                    }
                }
            });

            // Progress Chart
            if (!this.$refs.progressChart || typeof this.$refs.progressChart.getContext !== 'function') {
                this.$nextTick(() => this.initCharts());
                return;
            }
            const progressCanvas = this.$refs.progressChart;
            if (!progressCanvas) {
                this.$nextTick(() => this.initCharts());
                return;
            }
            this.progressChart = new Chart(progressCanvas, {
                type: 'bar',
                data: {
                    labels: this.progressPegawai.map(item =>
                        item.nama_lengkap.length > 15 ? item.nama_lengkap.substring(0, 15) + '...' : item.nama_lengkap
                    ),
                    datasets: [{
                        label: 'JP Tercapai',
                        data: this.progressPegawai.map(item => Number(item.jp_tercapai_filtered || item.jp_tercapai || 0)),
                        backgroundColor: '#3B82F6',
                        borderRadius: 4,
                    }, {
                        label: 'JP Target',
                        data: this.progressPegawai.map(item => Number(item.jp_target || 0)),
                        backgroundColor: '#E5E7EB',
                        borderRadius: 4,
                    }]
                },
                options: {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        title: {
                            display: true,
                            text: `Progress JP — ${this.selectedYear}`
                        },
                        legend: { display: true, position: 'top' },
                        tooltip: { enabled: true }
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
        },

        updateCharts(animate = true) {
            // Update pie
            try {
                // if chart instance is missing or its drawing context is gone, recreate
                if (!this.jenisChart || !this.jenisChart.ctx) return this.initCharts();
                this.jenisChart.data.labels = this.pelatihanByJenis.map(item => item.jenis_pelatihan || 'Lainnya');
                this.jenisChart.data.datasets[0].data = this.pelatihanByJenis.map(item => Number(item.total) || 0);
                // safe mutate title
                this.jenisChart.options.plugins = this.jenisChart.options.plugins || {};
                this.jenisChart.options.plugins.title = this.jenisChart.options.plugins.title || { display: true, text: '' };
                this.jenisChart.options.plugins.title.text = `Distribusi Pelatihan — ${this.selectedYear}`;
                this.jenisChart.update(animate ? undefined : 'none');
            } catch (e) {
                // recreate if anything wrong
                this.initCharts();
            }

            // Update progress bar chart
            try {
                if (!this.progressChart || !this.progressChart.ctx) return this.initCharts();
                this.progressChart.data.labels = this.progressPegawai.map(item =>
                    item.nama_lengkap.length > 15 ? item.nama_lengkap.substring(0, 15) + '...' : item.nama_lengkap
                );
                this.progressChart.data.datasets[0].data = this.progressPegawai.map(item => Number(item.jp_tercapai_filtered || item.jp_tercapai || 0));
                this.progressChart.data.datasets[1].data = this.progressPegawai.map(item => Number(item.jp_target || 0));
                // safe mutate title
                this.progressChart.options.plugins = this.progressChart.options.plugins || {};
                this.progressChart.options.plugins.title = this.progressChart.options.plugins.title || { display: true, text: '' };
                this.progressChart.options.plugins.title.text = `Progress JP — ${this.selectedYear}`;
                this.progressChart.update(animate ? undefined : 'none');
            } catch (e) {
                this.initCharts();
            }
        },
    }
};
</script>
