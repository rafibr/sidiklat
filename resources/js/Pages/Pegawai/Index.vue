<template>
    <AppLayout>
        <div class="space-y-6 pb-12">
            <section class="rounded-2xl border border-slate-200/70 bg-white px-5 py-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-900">Data Pegawai</h1>
                        <p class="text-sm text-slate-500">Kelola informasi pegawai dan target JP tahunan secara terpusat.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button @click="showJpSettingsModal = true"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200/80 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:border-indigo-500 hover:text-indigo-600">
                            <i class="fas fa-sliders-h"></i>
                            Pengaturan JP
                        </button>
                        <Link :href="route('pegawai.create')"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-600">
                            <i class="fas fa-plus"></i>
                            Tambah pegawai
                        </Link>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200/70 bg-white px-5 py-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" v-model="searchForm.search" @input="search" placeholder="Cari nama, NIP, atau unit kerja"
                            class="w-full rounded-xl border border-slate-200 bg-white px-10 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <button v-if="searchForm.search" @click="clearSearch"
                        class="text-sm font-medium text-slate-500 hover:text-slate-700">Reset pencarian</button>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200/70 bg-white shadow-sm">
                <div class="flex flex-col gap-3 px-5 pb-4 pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">Menampilkan <span class="font-semibold text-slate-900">{{ pegawais.from }}-{{ pegawais.to }}</span> dari <span class="font-semibold text-slate-900">{{ pegawais.total }}</span> pegawai.</p>
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <label for="per-page" class="hidden sm:inline">Baris per halaman</label>
                        <select id="per-page" v-model.number="perPage" @change="updatePerPage"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Pegawai</th>
                                <th class="px-5 py-3">Unit & Jabatan</th>
                                <th class="px-5 py-3">Progress JP</th>
                                <th class="px-5 py-3">Kontak</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="pegawai in pegawais.data" :key="pegawai.id" class="hover:bg-slate-50/60">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ pegawai.nama_lengkap }}</p>
                                            <p class="text-xs text-slate-500">{{ pegawai.nip || 'NIP belum tersedia' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="space-y-1">
                                        <span :class="getUnitPillClass(pegawai.unit_kerja)" class="inline-flex max-w-[220px] items-center rounded-full px-3 py-1 text-xs font-medium">{{ pegawai.unit_kerja || 'Unit belum diatur' }}</span>
                                        <p class="text-xs text-slate-500">{{ pegawai.jabatan }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs text-slate-500">
                                            <span>{{ pegawai.jp_tercapai || 0 }}/{{ pegawai.jp_target_display || jpDefault }} JP</span>
                                            <span :class="getProgressColor(pegawai.jp_tercapai, pegawai.jp_target_display)">
                                                {{ Math.round(calculateProgress(pegawai)) }}%
                                            </span>
                                        </div>
                                        <div class="h-2 w-full rounded-full bg-slate-100">
                                            <div class="h-full rounded-full" :class="getProgressBgColor(pegawai.jp_tercapai, pegawai.jp_target_display)"
                                                :style="{ width: Math.min(100, calculateProgress(pegawai)) + '%' }"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500">
                                    <div class="space-y-1">
                                        <p v-if="pegawai.email" class="flex items-center gap-2">
                                            <i class="fas fa-envelope"></i>
                                            <span class="truncate">{{ pegawai.email }}</span>
                                        </p>
                                        <p v-if="pegawai.telepon" class="flex items-center gap-2">
                                            <i class="fas fa-phone"></i>
                                            <span>{{ pegawai.telepon }}</span>
                                        </p>
                                        <p v-if="!pegawai.email && !pegawai.telepon" class="italic text-slate-400">Kontak belum diatur</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2 text-sm">
                                        <Link :href="route('pegawai.show', pegawai.id)" class="rounded-lg border border-slate-200/70 px-3 py-2 text-slate-600 hover:border-indigo-500 hover:text-indigo-600">
                                            <i class="fas fa-eye"></i>
                                        </Link>
                                        <Link :href="route('pegawai.edit', pegawai.id)" class="rounded-lg border border-slate-200/70 px-3 py-2 text-slate-600 hover:border-indigo-500 hover:text-indigo-600">
                                            <i class="fas fa-pen"></i>
                                        </Link>
                                        <button @click="confirmDelete(pegawai)"
                                            class="rounded-lg border border-slate-200/70 px-3 py-2 text-slate-600 transition-colors hover:border-rose-500 hover:text-rose-600">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="pegawais.links && pegawais.links.length > 3" class="border-t border-slate-100 px-5 py-4">
                    <nav class="flex flex-wrap items-center justify-center gap-2 text-sm">
                        <template v-for="(link, index) in pegawais.links" :key="index">
                            <Link v-if="link.url && link.label !== '...'" :href="link.url"
                                class="rounded-lg border border-slate-200/70 px-3 py-2"
                                :class="link.active ? 'border-indigo-500 bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:border-indigo-400 hover:text-indigo-600'"
                                v-html="link.label" />
                            <span v-else class="px-3 py-2 text-slate-400">...</span>
                        </template>
                    </nav>
                </div>
            </section>

            <div v-if="pegawais.data.length === 0" class="rounded-2xl border border-dashed border-slate-200/70 bg-slate-50 py-12 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                    <i class="fas fa-users text-2xl text-slate-400"></i>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-800">Belum ada data pegawai</h3>
                <p class="mt-1 text-sm text-slate-500">Mulai dengan menambahkan pegawai baru untuk memonitor progres JP.</p>
            </div>
        </div>

        <transition name="fade">
            <div v-if="showJpSettingsModal" class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 px-4"
                @click.self="showJpSettingsModal = false">
                <div class="w-full max-w-md rounded-2xl border border-slate-200/70 bg-white p-6 shadow-2xl">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-slate-900">Pengaturan target JP tahunan</h2>
                        <button @click="showJpSettingsModal = false" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form @submit.prevent="submitJpSettings" class="mt-6 space-y-4">
                        <div>
                            <label for="tahun" class="block text-sm font-medium text-slate-600">Tahun</label>
                            <select id="tahun" v-model.number="jpSettingsForm.tahun"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
                                <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="jp_default" class="block text-sm font-medium text-slate-600">JP target default</label>
                            <input id="jp_default" type="number" min="0" v-model.number="jpSettingsForm.jp_default"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
                            <p class="mt-2 text-xs text-slate-500">Nilai ini akan diterapkan ke seluruh pegawai untuk tahun {{ jpSettingsForm.tahun }}.</p>
                        </div>
                        <div class="rounded-xl border border-amber-200 bg-amber-50/70 px-4 py-3 text-xs text-amber-700">
                            Penyesuaian akan diterapkan otomatis pada pegawai yang belum memiliki target khusus.
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showJpSettingsModal = false"
                                class="rounded-xl border border-slate-200/70 px-4 py-2 text-sm font-medium text-slate-600 hover:border-slate-400">Batal</button>
                            <button type="submit"
                                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-600">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>

        <transition name="fade">
            <div v-if="showDeleteModal" class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 px-4"
                @click.self="showDeleteModal = false">
                <div class="w-full max-w-md rounded-2xl border border-slate-200/70 bg-white p-6 shadow-2xl">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Hapus pegawai?</h3>
                    <p class="mt-2 text-sm text-slate-500">Pegawai <strong>{{ pegawaiToDelete?.nama_lengkap }}</strong> akan dihapus permanen beserta catatan progresnya.</p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="showDeleteModal = false"
                            class="rounded-xl border border-slate-200/70 px-4 py-2 text-sm font-medium text-slate-600 hover:border-slate-400">Batal</button>
                        <button @click="deletePegawai"
                            class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-600">Hapus</button>
                    </div>
                </div>
            </div>
        </transition>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { reactive, ref, watch } from 'vue';

export default {
    components: { AppLayout, Link },
    props: {
        pegawais: Object,
        jpDefault: { type: Number, default: 20 },
        currentYear: { type: Number, default: () => new Date().getFullYear() },
        filters: { type: Object, default: () => ({}) },
    },
    setup(props) {
        const showJpSettingsModal = ref(false);
        const showDeleteModal = ref(false);
        const pegawaiToDelete = ref(null);

        const searchForm = reactive({ search: props.filters.search || '' });
        const jpSettingsForm = reactive({ jp_default: props.jpDefault, tahun: props.currentYear });
        const perPage = ref(Number(props.filters.per_page) || 10);

        const availableYears = ref([]);
        const currentYearInt = parseInt(props.currentYear, 10);
        for (let i = -2; i <= 2; i++) {
            availableYears.value.push(currentYearInt + i);
        }

        watch(
            () => props.filters.per_page,
            (value) => {
                if (value) {
                    perPage.value = Number(value);
                }
            },
            { immediate: true }
        );

        const search = debounce(() => {
            router.get(route('pegawai.index'), { search: searchForm.search, per_page: perPage.value }, { preserveState: true, replace: true });
        }, 300);

        const clearSearch = () => {
            searchForm.search = '';
            router.get(route('pegawai.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
        };

        const confirmDelete = (pegawai) => {
            pegawaiToDelete.value = pegawai;
            showDeleteModal.value = true;
        };

        const deletePegawai = () => {
            if (!pegawaiToDelete.value) return;
            router.delete(route('pegawai.destroy', pegawaiToDelete.value.id), {
                onSuccess: () => {
                    showDeleteModal.value = false;
                    pegawaiToDelete.value = null;
                },
            });
        };

        const submitJpSettings = () => {
            router.post(route('pegawai.update-jp-default'), jpSettingsForm, {
                onSuccess: () => {
                    showJpSettingsModal.value = false;
                },
            });
        };

        const calculateProgress = (pegawai) => {
            const target = pegawai.jp_target_display || props.jpDefault;
            const achieved = pegawai.jp_tercapai || 0;
            return target > 0 ? (achieved / target) * 100 : 0;
        };

        const getProgressColor = (achieved, target) => {
            const progress = calculateProgress({ jp_tercapai: achieved, jp_target_display: target || props.jpDefault });
            if (progress >= 100) return 'text-emerald-600';
            if (progress >= 75) return 'text-indigo-600';
            if (progress >= 50) return 'text-amber-600';
            return 'text-rose-600';
        };

        const getProgressBgColor = (achieved, target) => {
            const progress = calculateProgress({ jp_tercapai: achieved, jp_target_display: target || props.jpDefault });
            if (progress >= 100) return 'bg-emerald-600';
            if (progress >= 75) return 'bg-indigo-600';
            if (progress >= 50) return 'bg-amber-600';
            return 'bg-rose-600';
        };

        const palette = [
            'bg-indigo-100 text-indigo-700',
            'bg-emerald-100 text-emerald-700',
            'bg-sky-100 text-sky-700',
            'bg-amber-100 text-amber-700',
            'bg-slate-200 text-slate-700',
        ];

        const hashString = (s) => {
            let hash = 0;
            for (let i = 0; i < s.length; i += 1) {
                hash = (hash << 5) - hash + s.charCodeAt(i);
                hash |= 0;
            }
            return Math.abs(hash);
        };

        const getUnitPillClass = (unit) => {
            if (!unit) {
                return 'bg-slate-200 text-slate-700';
            }
            const index = hashString(unit) % palette.length;
            return palette[index];
        };

        const updatePerPage = () => {
            router.get(route('pegawai.index'), { search: searchForm.search, per_page: perPage.value }, {
                preserveState: true,
                replace: true,
            });
        };

        return {
            showJpSettingsModal,
            showDeleteModal,
            pegawaiToDelete,
            searchForm,
            jpSettingsForm,
            availableYears,
            perPage,
            search,
            clearSearch,
            confirmDelete,
            deletePegawai,
            submitJpSettings,
            calculateProgress,
            getProgressColor,
            getProgressBgColor,
            getUnitPillClass,
            updatePerPage,
        };
    },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
