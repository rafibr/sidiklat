<template>
    <AppLayout>
        <div class="p-3 sm:p-4 md:p-6">
            <div class="mb-4 md:mb-6">
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 mb-3 md:mb-4">
                    Progress Jam Pelajaran (JP) Pegawai
                </h2>

                <!-- Filters -->
                <form method="GET" class="bg-gray-50 p-3 sm:p-4 rounded-lg mb-4 md:mb-6" @submit.prevent="submitFilter">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pegawai</label>
                            <input type="text" v-model="filters.search" placeholder="Nama pegawai..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja</label>
                            <select v-model="filters.unit_kerja"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Unit</option>
                                <option v-for="unit in unitKerjas" :key="unit" :value="unit">
                                    {{ unit }}
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="flex-1 sm:flex-none bg-blue-600 text-white px-3 sm:px-4 py-2 text-sm rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-search mr-1 sm:mr-2"></i>
                                <span class="hidden sm:inline">Filter</span>
                                <span class="sm:hidden">Cari</span>
                            </button>
                            <button type="button" @click="resetFilter"
                                class="flex-1 sm:flex-none bg-gray-500 text-white px-3 sm:px-4 py-2 text-sm rounded-md hover:bg-gray-600 transition-colors text-center">
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Progress Cards -->
            <div class="grid gap-4">
                <Link v-for="pegawai in pegawais.data" :key="pegawai.id" :href="route('pegawai.show', pegawai.id)"
                    class="block bg-white rounded-lg shadow-sm hover:shadow-md overflow-hidden transition-shadow">
                <div class="p-2 sm:p-3 md:p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-800 truncate">{{
                                pegawai.nama_lengkap }}</h3>
                            <div class="text-xs sm:text-sm text-gray-600 mt-1 flex flex-wrap gap-2">
                                <div class="mr-2"><strong>NIP:</strong> {{ pegawai.nip || 'Tidak Ada' }}</div>
                                <div class="mr-2"><strong>Pangkat:</strong> {{ pegawai.pangkat_golongan }}</div>
                                <div><strong>Unit:</strong> <span class="break-words">{{ pegawai.unit_kerja }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <div class="text-lg sm:text-xl font-bold text-gray-800">
                                {{ formatNumber(pegawai.jp_tercapai) }} / {{ formatNumber(pegawai.jp_target) }} JP
                            </div>
                            <div class="text-xs sm:text-sm text-gray-600">{{ calculateProgress(pegawai).toFixed(1) }}%
                                tercapai</div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="w-full bg-gray-200 rounded-full h-3">
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
                            <span v-if="pegawai.pelatihans.length > 3" class="text-xs text-gray-500">+{{
                                pegawai.pelatihans.length - 3 }} lainnya</span>
                        </div>
                        <div v-else class="text-sm text-gray-500 italic">Belum ada data pelatihan</div>
                    </div>
                </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="pegawais.links" class="mt-6">
                <div class="flex justify-center space-x-2">
                    <button v-for="link in pegawais.links" :key="link.label" @click="changePage(link.url)"
                        :disabled="!link.url" class="px-3 py-2 text-sm border rounded"
                        :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-600'"
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
    },
    data() {
        return {
            filters: {
                search: this.$page.url.split('?')[1] ? new URLSearchParams(this.$page.url.split('?')[1]).get('search') || '' : '',
                unit_kerja: this.$page.url.split('?')[1] ? new URLSearchParams(this.$page.url.split('?')[1]).get('unit_kerja') || '' : '',
            }
        };
    },
    methods: {
        calculateProgress(pegawai) {
            return pegawai.jp_target > 0 ? (pegawai.jp_tercapai / pegawai.jp_target) * 100 : 0;
        },
        getProgressColor(pegawai) {
            const progress = this.calculateProgress(pegawai);
            if (progress >= 80) return 'bg-green-500';
            if (progress >= 50) return 'bg-yellow-500';
            return 'bg-red-500';
        },
        getJenisColor(jenis) {
            const colorMap = {
                'Diklat Struktural': 'bg-blue-100 text-blue-800',
                'Diklat Fungsional': 'bg-green-100 text-green-800',
                'Diklat Teknis': 'bg-purple-100 text-purple-800',
                'Workshop': 'bg-orange-100 text-orange-800'
            };
            return colorMap[jenis] || 'bg-gray-100 text-gray-800';
        },
        formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        },
        submitFilter() {
            router.get(route('progress'), this.filters, { preserveState: true });
        },
        resetFilter() {
            this.filters = { search: '', unit_kerja: '' };
            router.get(route('progress'));
        },
        changePage(url) {
            if (url) {
                router.visit(url);
            }
        }
    }
};
</script>
