<template>
    <AppLayout>
        <div class="space-y-6 pb-24">
            <div v-if="loading" class="fixed inset-0 z-50 flex items-center justify-center bg-white/70 backdrop-blur-sm">
                <div class="flex items-center space-x-3 rounded-xl border border-slate-200/70 bg-white px-6 py-4 shadow-lg">
                    <span class="loading-spinner-small"></span>
                    <span class="text-sm font-medium text-slate-600">Memuat data...</span>
                </div>
            </div>

            <section ref="yearFilterCard" class="relative overflow-hidden rounded-3xl border border-indigo-200/60 bg-gradient-to-br from-indigo-600 via-sky-500 to-emerald-500 px-5 py-6 text-white shadow-xl">
                <div class="pointer-events-none absolute inset-x-0 -top-32 h-40 bg-gradient-to-b from-white/20 via-transparent to-transparent"></div>
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="space-y-2">
                        <h1 class="text-2xl font-semibold">Dashboard Kinerja Diklat</h1>
                        <p class="max-w-xl text-sm text-white/80">Pemantauan real-time terhadap pencapaian jam pelatihan pegawai dan efektivitas program belajar sepanjang tahun {{ selectedYear }}.</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="rounded-xl border border-white/30 bg-white/10 px-4 py-3 text-sm text-white/80 shadow-lg backdrop-blur">
                            <p class="font-medium text-white/90">Tahun aktif</p>
                            <p class="text-lg font-semibold text-white">{{ selectedYear }}</p>
                        </div>
                        <div class="relative">
                            <label class="sr-only" for="year-filter">Filter tahun</label>
                            <select id="year-filter" :value="selectedYear" @change="changeYear($event.target.value)"
                                class="w-48 rounded-xl border border-white/30 bg-white/20 px-4 py-3 text-sm text-white focus:border-white focus:ring-2 focus:ring-white/60">
                                <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div v-for="card in summaryCards" :key="card.label"
                    :class="['flex flex-col justify-between rounded-2xl p-5 text-white shadow-xl ring-1 ring-black/5', card.surface]">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl" :class="card.iconBackground">
                            <i :class="card.icon" class="text-base"></i>
                        </div>
                        <span class="text-xs font-medium uppercase tracking-wider text-white/70">{{ card.caption }}</span>
                    </div>
                    <div class="mt-6 space-y-1">
                        <p class="text-sm font-medium text-white/80">{{ card.label }}</p>
                        <p class="text-2xl font-semibold text-white">{{ card.value }}</p>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-200/70 bg-white/90 p-6 shadow-sm backdrop-blur lg:col-span-2">
                    <div class="flex flex-col gap-4 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Ringkasan Pemenuhan JP</h2>
                            <p class="text-sm text-slate-500">Rasio jam pelajaran tercapai dibandingkan target selama tahun {{ selectedYear }}.</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-2 text-sm text-slate-500">
                            <span class="font-semibold text-indigo-600 text-lg">{{ jpFulfillment?.totals?.overall_progress || 0 }}%</span>
                            <span class="ml-2">progres keseluruhan</span>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm text-slate-600">
                                <span>JP tercapai</span>
                                <span class="font-semibold text-slate-900">{{ formatNumber(jpFulfillment?.totals?.jp_achieved || 0) }} JP</span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-slate-600">
                                <span>JP target</span>
                                <span class="font-semibold text-slate-900">{{ formatNumber(jpFulfillment?.totals?.jp_target || 0) }} JP</span>
                            </div>
                            <div class="h-3 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600 transition-all duration-500"
                                    :style="{ width: Math.min(100, jpFulfillment?.totals?.overall_progress || 0) + '%' }"></div>
                            </div>
                            <p class="text-xs text-slate-500">Target tahunan akan tercapai jika progres mencapai 100%.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200/70 bg-slate-50 p-4">
                            <ul class="space-y-3 text-sm text-slate-600">
                                <li class="flex items-center justify-between">
                                    <span class="flex items-center gap-2"><span class="status-dot bg-emerald-600"></span>Tercapai</span>
                                    <span class="font-semibold text-slate-900">{{ jpFulfillment?.categories?.completed || 0 }} pegawai</span>
                                </li>
                                <li class="flex items-center justify-between">
                                    <span class="flex items-center gap-2"><span class="status-dot bg-indigo-600"></span>On track</span>
                                    <span class="font-semibold text-slate-900">{{ jpFulfillment?.categories?.on_track || 0 }} pegawai</span>
                                </li>
                                <li class="flex items-center justify-between">
                                    <span class="flex items-center gap-2"><span class="status-dot bg-amber-600"></span>Tertinggal</span>
                                    <span class="font-semibold text-slate-900">{{ jpFulfillment?.categories?.behind || 0 }} pegawai</span>
                                </li>
                                <li class="flex items-center justify-between">
                                    <span class="flex items-center gap-2"><span class="status-dot bg-rose-600"></span>Kritis</span>
                                    <span class="font-semibold text-slate-900">{{ jpFulfillment?.categories?.critical || 0 }} pegawai</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200/70 bg-white/90 p-6 shadow-sm backdrop-blur">
                    <h3 class="text-lg font-semibold text-slate-900">Sebaran Kategori</h3>
                    <p class="mt-2 text-sm text-slate-500">Persentase pegawai berdasarkan status pemenuhan target.</p>
                    <ul class="mt-5 space-y-4">
                        <li v-for="item in categoryBreakdown" :key="item.label" class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <span class="status-dot" :class="item.color"></span>
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ item.label }}</p>
                                    <p class="text-xs text-slate-500">{{ item.subtitle }}</p>
                                </div>
                            </div>
                            <div class="text-right text-sm text-slate-600">
                                <p class="font-semibold text-slate-900">{{ item.total }} pegawai</p>
                                <p>{{ item.percentage }}%</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-200/70 bg-white/90 p-6 shadow-sm backdrop-blur">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Top performer</h3>
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">Pencapaian tertinggi</span>
                    </div>
                    <div class="mt-4 space-y-4">
                        <div v-for="employee in jpFulfillment?.top_performers || []" :key="employee.id"
                            class="rounded-xl border border-slate-200/70 bg-slate-50 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ employee.nama }}</p>
                                    <p class="text-xs text-slate-500">{{ employee.unit_kerja }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-emerald-600">{{ employee.progress_percentage }}%</p>
                                    <p class="text-xs text-slate-500">{{ employee.jp_achieved }}/{{ employee.jp_target }} JP</p>
                                </div>
                            </div>
                        </div>
                        <p v-if="(jpFulfillment?.top_performers || []).length === 0" class="text-sm text-slate-500">Belum ada data top performer untuk tahun ini.</p>
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200/70 bg-white/90 p-6 shadow-sm backdrop-blur">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Perlu perhatian</h3>
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">Prioritas pendampingan</span>
                    </div>
                    <div class="mt-4 space-y-4">
                        <div v-for="employee in jpFulfillment?.needs_attention || []" :key="employee.id"
                            class="rounded-xl border border-rose-100 bg-rose-50/60 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ employee.nama }}</p>
                                    <p class="text-xs text-slate-500">{{ employee.unit_kerja }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-rose-600">{{ employee.progress_percentage }}%</p>
                                    <p class="text-xs text-slate-500">{{ employee.jp_achieved }}/{{ employee.jp_target }} JP</p>
                                </div>
                            </div>
                        </div>
                        <p v-if="(jpFulfillment?.needs_attention || []).length === 0" class="text-sm text-slate-500">Seluruh pegawai berada pada jalur yang aman.</p>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-200/70 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Distribusi Jenis Pelatihan</h3>
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ selectedYear }}</span>
                    </div>
                    <div class="mt-4 flex items-center justify-center">
                        <canvas ref="jenisChart" class="max-h-80 w-full"></canvas>
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200/70 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Progress terbaru pegawai</h3>
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">7 entri terbaru</span>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200/70 bg-white">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Pegawai</th>
                                    <th class="px-4 py-3">Unit</th>
                                    <th class="px-4 py-3">Progress</th>
                                    <th class="px-4 py-3">JP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in progressPegawai" :key="item.id" class="border-t border-slate-100">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-800">{{ item.nama }}</p>
                                        <p class="text-xs text-slate-500">{{ item.jabatan }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ item.unit_kerja }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-between text-xs text-slate-500">
                                            <span>{{ item.jp_tercapai }}/{{ item.jp_target }} JP</span>
                                            <span :class="getProgressColorClass(item.persentase)">
                                                {{ item.persentase }}%
                                            </span>
                                        </div>
                                        <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full bg-indigo-600" :style="{ width: Math.min(100, item.persentase) + '%' }"></div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ item.jp_tercapai }}</td>
                                </tr>
                                <tr v-if="!progressPegawai || progressPegawai.length === 0">
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada riwayat progress terbaru.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <FloatingYearFilter :selected-year="selectedYear" :years="availableYears" :visible="floatingYearVisible" @change="changeYear" />
        </div>
    </AppLayout>
</template>

<script>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FloatingYearFilter from '@/Components/FloatingYearFilter.vue';

export default {
    components: {
        AppLayout,
        FloatingYearFilter
    },
    props: {
        stats: Object,
        jpFulfillment: Object,
        pelatihanByJenis: Array,
        progressPegawai: Array,
        availableYears: Array,
        selectedYear: Number,
    },
    data() {
        return {
            jenisChart: null,
            loading: false,
            yearFilterObserver: null,
            floatingYearVisible: false,
        };
    },
    computed: {
        summaryCards() {
            return [
                {
                    label: 'Total pegawai',
                    caption: 'Aktif terdaftar',
                    value: this.formatNumber(this.stats?.total_pegawai || 0),
                    icon: 'fas fa-users',
                    iconBackground: 'bg-white/20 text-white',
                    surface: 'bg-gradient-to-br from-indigo-600 via-sky-500 to-emerald-500',
                },
                {
                    label: 'Program pelatihan',
                    caption: 'Seluruh jenis',
                    value: this.formatNumber(this.stats?.total_pelatihan || 0),
                    icon: 'fas fa-briefcase',
                    iconBackground: 'bg-white/20 text-white',
                    surface: 'bg-gradient-to-br from-sky-500 via-cyan-500 to-emerald-500',
                },
                {
                    label: 'Total JP terserap',
                    caption: 'Kumulatif',
                    value: this.formatNumber(this.stats?.total_jp || 0),
                    icon: 'fas fa-clock',
                    iconBackground: 'bg-white/20 text-white',
                    surface: 'bg-gradient-to-br from-indigo-700 via-violet-600 to-fuchsia-500',
                },
                {
                    label: 'Rata-rata progress',
                    caption: 'Per pegawai',
                    value: `${Number(this.stats?.rata_progress || 0).toFixed(1)}%`,
                    icon: 'fas fa-chart-line',
                    iconBackground: 'bg-white/20 text-white',
                    surface: 'bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500',
                },
            ];
        },
        categoryBreakdown() {
            return [
                {
                    label: 'Tercapai',
                    subtitle: '≥ 100% target',
                    color: 'bg-emerald-600',
                    total: this.jpFulfillment?.categories?.completed || 0,
                    percentage: this.jpFulfillment?.percentages?.completed || 0,
                },
                {
                    label: 'On track',
                    subtitle: '75-99% target',
                    color: 'bg-indigo-600',
                    total: this.jpFulfillment?.categories?.on_track || 0,
                    percentage: this.jpFulfillment?.percentages?.on_track || 0,
                },
                {
                    label: 'Tertinggal',
                    subtitle: '50-74% target',
                    color: 'bg-amber-600',
                    total: this.jpFulfillment?.categories?.behind || 0,
                    percentage: this.jpFulfillment?.percentages?.behind || 0,
                },
                {
                    label: 'Kritis',
                    subtitle: '< 50% target',
                    color: 'bg-rose-600',
                    total: this.jpFulfillment?.categories?.critical || 0,
                    percentage: this.jpFulfillment?.percentages?.critical || 0,
                },
            ];
        },
    },
    mounted() {
        this.loading = true;
        this.$nextTick(() => {
            this.initCharts();
            this.observeYearFilter();
        });
    },
    beforeUnmount() {
        if (this.jenisChart) {
            try { this.jenisChart.destroy(); } catch (e) { /* noop */ }
            this.jenisChart = null;
        }
        if (this.yearFilterObserver && this.$refs.yearFilterCard) {
            try { this.yearFilterObserver.unobserve(this.$refs.yearFilterCard); } catch (e) { /* noop */ }
        }
        if (this.yearFilterObserver) {
            try { this.yearFilterObserver.disconnect(); } catch (e) { /* noop */ }
            this.yearFilterObserver = null;
        }
    },
    watch: {
        pelatihanByJenis() {
            this.loading = true;
            this.updateCharts(false);
            this.$nextTick(() => { this.loading = false; });
        },
        progressPegawai() {
            this.$forceUpdate();
        },
    },
    methods: {
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        },
        observeYearFilter() {
            if (!this.$refs.yearFilterCard) {
                return;
            }

            const observerOptions = {
                threshold: 0.15,
                rootMargin: '-80px 0px 0px 0px',
            };

            this.yearFilterObserver = new IntersectionObserver((entries) => {
                const [entry] = entries;
                if (!entry) {
                    return;
                }

                const isVisible = entry.isIntersecting;
                const top = entry.boundingClientRect?.top ?? 0;
                this.floatingYearVisible = !isVisible && top < 0;
            }, observerOptions);

            this.yearFilterObserver.observe(this.$refs.yearFilterCard);
        },
        changeYear(yearValue) {
            const year = parseInt(yearValue, 10);
            if (Number.isNaN(year) || year === this.selectedYear) {
                return;
            }
            this.loading = true;
            router.get(route('dashboard'), { year }, { preserveState: true, preserveScroll: true });
        },
        getProgressColorClass(percentage) {
            if (percentage >= 100) return 'text-emerald-600';
            if (percentage >= 75) return 'text-indigo-600';
            if (percentage >= 50) return 'text-amber-600';
            return 'text-rose-600';
        },
        async initCharts() {
            const Chart = (await import('chart.js/auto')).default;

            if (this.jenisChart) {
                try { this.jenisChart.destroy(); } catch (e) { /* noop */ }
                this.jenisChart = null;
            }

            if (!this.$refs.jenisChart) {
                this.$nextTick(() => this.initCharts());
                return;
            }

            this.jenisChart = new Chart(this.$refs.jenisChart, {
                type: 'doughnut',
                data: {
                    labels: this.pelatihanByJenis.map(item => item.jenis_pelatihan || 'Lainnya'),
                    datasets: [{
                        data: this.pelatihanByJenis.map(item => Number(item.total) || 0),
                        backgroundColor: ['#4338CA', '#6366F1', '#818CF8', '#A5B4FC', '#C7D2FE', '#E0E7FF'],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                    }],
                },
                options: {
                    cutout: '65%',
                    plugins: {
                        legend: { display: true, position: 'bottom' },
                        tooltip: { enabled: true },
                    },
                },
            });

            this.loading = false;
        },
        updateCharts(animate = true) {
            if (!this.jenisChart || !this.jenisChart.ctx) {
                this.initCharts();
                return;
            }

            this.jenisChart.data.labels = this.pelatihanByJenis.map(item => item.jenis_pelatihan || 'Lainnya');
            this.jenisChart.data.datasets[0].data = this.pelatihanByJenis.map(item => Number(item.total) || 0);
            this.jenisChart.update(animate ? undefined : 'none');
            this.loading = false;
        },
    },
};
</script>

<style scoped>
.loading-spinner-small {
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 9999px;
    border: 3px solid rgba(79, 70, 229, 0.2);
    border-top-color: #4f46e5;
    animation: spin 0.8s linear infinite;
}

.status-dot {
    display: inline-flex;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 9999px;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
