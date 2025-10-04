<template>
	<AppLayout>
                <div class="relative space-y-6 p-3 sm:p-4 md:p-6 animate-slide-up">
                        <!-- Loading overlay -->
                        <div v-if="loading" class="absolute inset-0 bg-white/60 z-50 flex items-center justify-center">
                                <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                        <div class="relative overflow-hidden rounded-3xl border border-indigo-200/60 bg-gradient-to-r from-indigo-600 via-sky-500 to-emerald-500 p-6 text-white shadow-xl animate-fade-scale">
                                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.3),_transparent_60%)]"></div>
                                <div class="relative space-y-3">
                                        <h2 class="text-lg sm:text-xl md:text-2xl font-semibold">Progress Jam Pelajaran (JP) Pegawai</h2>
                                        <p class="text-sm text-white/80 max-w-3xl">Pantau capaian Jam Pelajaran tiap pegawai berdasarkan unit kerja dan temukan siapa yang membutuhkan pendampingan tambahan.</p>
                                </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" class="rounded-2xl border border-indigo-100 bg-white/80 p-4 shadow-sm backdrop-blur animate-slide-left delay-100"
                                @submit.prevent="submitFilter">
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4">
                                                <div>
                                                        <label class="mb-2 block text-sm font-medium text-slate-700">Filter Tahun</label>
                                                        <select v-model="filters.year"
                                                                class="w-full rounded-xl border border-indigo-100 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40">
                                                                <option v-for="year in availableYears" :key="year" :value="year">
                                                                        {{ year }}
                                                                </option>
                                                        </select>
                                                </div>

                                                <div>
                                                        <label class="mb-2 block text-sm font-medium text-slate-700">Cari Pegawai</label>
                                                        <input type="text" v-model="filters.search" placeholder="Nama pegawai..."
                                                                class="w-full rounded-xl border border-indigo-100 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40" />
                                                </div>

                                                <div>
                                                        <label class="mb-2 block text-sm font-medium text-slate-700">Unit Kerja</label>
                                                        <select v-model="filters.unit_kerja"
                                                                class="w-full rounded-xl border border-indigo-100 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40">
                                                                <option value="">Semua Unit</option>
                                                                <option v-for="unit in unitKerjas" :key="unit" :value="unit">
                                                                        {{ unit }}
								</option>
							</select>
						</div>

						<div class="flex items-end gap-2">
                                                        <button type="submit"
                                                                class="flex-1 rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:shadow-lg sm:flex-none">
                                                                <i class="fas fa-search mr-1 sm:mr-2"></i>
                                                                <span class="hidden sm:inline">Filter</span>
                                                                <span class="sm:hidden">Cari</span>
                                                        </button>
                                                        <button type="button" @click="resetFilter"
                                                                class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 sm:flex-none">
                                                                Reset
                                                        </button>
                                                </div>
                                        </div>
                                </form>

                        <!-- Progress Cards -->
                        <div class="grid gap-4"
                                :class="{ 'animate-slide-up delay-200': animationsReady, 'opacity-0': !animationsReady }">
                                <Link v-for="pegawai in pegawais.data" :key="pegawai.id" :href="route('pegawai.show', pegawai.id)"
                                        class="block overflow-hidden rounded-2xl border border-indigo-100 bg-white/90 shadow-sm backdrop-blur transition-all hover:-translate-y-1 hover:shadow-xl">
                                <div class="p-3 sm:p-4 md:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                                <div class="flex-1 min-w-0">
                                                        <h3 class="truncate text-base font-semibold text-slate-900 sm:text-lg">{{
                                                                pegawai.nama_lengkap }}</h3>
                                                        <div class="mt-1 flex flex-wrap gap-2 text-xs text-slate-600 sm:text-sm">
                                                                <div class="mr-2"><strong>NIP:</strong> {{ pegawai.nip || 'Tidak Ada' }}</div>
                                                                <div class="mr-2"><strong>Pangkat:</strong> {{ pegawai.pangkat_golongan }}</div>
                                                                <div><strong>Unit:</strong> <span class="break-words">{{ pegawai.unit_kerja }}</span>
                                                                </div>
                                                        </div>
                                                </div>

                                                <div class="text-right flex-shrink-0">
                                                        <div class="text-lg font-bold text-slate-900 sm:text-xl">
                                                                {{ formatNumber(pegawai.jp_tercapai_filtered || 0) }} / {{
                                                                        formatNumber(pegawai.jp_target) }} JP
                                                        </div>
                                                        <div class="text-xs text-slate-600 sm:text-sm">{{ calculateProgress(pegawai).toFixed(1) }}%
                                                                tercapai</div>
                                                </div>
                                        </div>

                                        <div class="mt-3">
                                                <div class="h-3 w-full rounded-full bg-slate-100">
                                                        <div class="h-3 rounded-full transition-all duration-300" :class="getProgressColor(pegawai)"
                                                                :style="{ width: Math.min(100, calculateProgress(pegawai)) + '%' }"></div>
                                                </div>
                                        </div>

                                        <div class="mt-3">
                                                <div v-if="pegawai.pelatihans && pegawai.pelatihans.length > 0" class="flex flex-wrap gap-2">
                                                        <span v-for="pelatihan in pegawai.pelatihans.slice(0, 3)" :key="pelatihan.id"
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                                :class="getJenisColor(pelatihan.jenis_pelatihan)">
                                                                {{ pelatihan.nama_pelatihan }} ({{ pelatihan.jp }} JP)
                                                        </span>
                                                        <span v-if="pegawai.pelatihans.length > 3" class="text-xs text-slate-500">+{{
                                                                pegawai.pelatihans.length - 3 }} lainnya</span>
                                                </div>
                                                <div v-else class="text-sm italic text-slate-500">Belum ada data pelatihan</div>
                                        </div>
                                </div>
                                </Link>
                        </div>

                        <!-- Pagination -->
                        <div v-if="pegawais.links" class="mt-6">
                                <div class="flex justify-center space-x-2">
                                        <button v-for="link in pegawais.links" :key="link.label" @click="changePage(link.url)"
                                                :disabled="!link.url" class="rounded-lg border border-indigo-100 px-3 py-2 text-sm transition"
                                                :class="link.active ? 'bg-gradient-to-r from-indigo-600 to-sky-500 text-white shadow-md' : 'bg-white/80 text-slate-600 hover:bg-slate-100'"
                                                v-html="link.label"></button>
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
		pegawais: Object,
		unitKerjas: Array,
		availableYears: Array,
		selectedYear: Number,
	},
	data() {
		return {
			loading: false,
			animationsReady: false,
			filters: {
				year: this.selectedYear || new Date().getFullYear(),
				search: this.$page.url.split('?')[1] ? new URLSearchParams(this.$page.url.split('?')[1]).get('search') || '' : '',
				unit_kerja: this.$page.url.split('?')[1] ? new URLSearchParams(this.$page.url.split('?')[1]).get('unit_kerja') || '' : '',
			}
		};
	},
	mounted() {
		// Trigger animations after component is mounted
		setTimeout(() => {
			this.animationsReady = true;
		}, 100);
	},
	watch: {
		pegawais() {
			// clear loading when new data arrives
			this.$nextTick(() => { this.loading = false; });
		}
	},
	methods: {
		calculateProgress(pegawai) {
			const jpTercapai = pegawai.jp_tercapai_filtered || 0;
			return pegawai.jp_target > 0 ? (jpTercapai / pegawai.jp_target) * 100 : 0;
		},
		getProgressColor(pegawai) {
			const progress = this.calculateProgress(pegawai);
			if (progress >= 80) return 'bg-emerald-600';
			if (progress >= 50) return 'bg-amber-600';
			return 'bg-rose-600';
		},
		getJenisColor(jenis) {
			const colorMap = {
				'Diklat Struktural': 'bg-indigo-100 text-indigo-700',
				'Diklat Fungsional': 'bg-emerald-100 text-emerald-700',
				'Diklat Teknis': 'bg-indigo-100 text-indigo-700',
				'Workshop': 'bg-amber-100 text-amber-700'
			};
			return colorMap[jenis] || 'bg-gray-100 text-gray-800';
		},
		formatNumber(num) {
			return new Intl.NumberFormat().format(num);
		},
		submitFilter() {
			this.loading = true;
			router.get(route('progress'), this.filters, { preserveState: true });
		},
		resetFilter() {
			this.loading = true;
			this.filters = {
				year: this.selectedYear || new Date().getFullYear(),
				search: '',
				unit_kerja: ''
			};
			router.get(route('progress'));
		},
		changePage(url) {
			if (url) {
				this.loading = true;
				router.visit(url);
			}
		}
	}
};
</script>
