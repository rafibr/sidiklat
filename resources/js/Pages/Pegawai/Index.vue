<template>
    <AppLayout>
        <div class="p-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Data Pegawai</h1>
                    <p class="text-sm text-gray-600 mt-1">Kelola data pegawai dan pengaturan JP</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button @click="showJpSettingsModal = true"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-cog mr-2"></i>Pengaturan JP
                    </button>
                    <Link :href="route('pegawai.create')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-plus mr-2"></i>Tambah Pegawai
                    </Link>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="relative flex-1">
                        <input type="text" v-model="searchForm.search" @input="search" placeholder="Cari pegawai..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button v-if="searchForm.search" @click="clearSearch"
                        class="text-gray-600 hover:text-gray-800 px-3 py-2">
                        <i class="fas fa-times mr-1"></i>Clear
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <div v-if="$page.props.flash?.success" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 mt-0.5 mr-3"></i>
                    <p class="text-green-700">{{ $page.props.flash.success }}</p>
                </div>
            </div>

            <div v-if="$page.props.flash?.error" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-400 mt-0.5 mr-3"></i>
                    <p class="text-red-700">{{ $page.props.flash.error }}</p>
                </div>
            </div> <!-- Data Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="w-3/12 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pegawai</th>
                                <th
                                    class="w-2/12 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Unit Kerja</th>
                                <th
                                    class="w-3/12 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    JP Progress</th>
                                <th
                                    class="w-2/12 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kontak</th>
                                <th
                                    class="w-2/12 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="pegawai in pegawais.data" :key="pegawai.id"
                                class="hover:bg-gray-50 cursor-pointer" @click="goToDetail(pegawai)">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 truncate max-w-[220px]">{{
                                                pegawai.nama_lengkap }}</div>
                                            <div class="text-sm text-gray-500 truncate max-w-[220px]">{{ pegawai.nip ||
                                                'Tidak ada NIP' }}</div>
                                            <div v-if="pegawai.pangkat_golongan"
                                                class="text-xs text-gray-400 truncate max-w-[220px]">{{
                                                pegawai.pangkat_golongan }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div v-if="pegawai.unit_kerja">
                                            <span
                                                :class="getUnitPillClass(pegawai.unit_kerja) + ' inline-block max-w-[160px] truncate'"
                                                @click.stop>
                                                {{ pegawai.unit_kerja }}
                                            </span>
                                        </div>
                                        <div v-else class="text-sm text-gray-900">-</div>
                                        <div v-if="pegawai.jabatan" class="text-sm text-gray-500 mt-1">{{
                                            pegawai.jabatan }}
                                        </div>
                                    </div>
                                </td>
                                <!-- Status column removed per request -->
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-gray-600">{{ pegawai.jp_tercapai || 0 }}/{{
                                                pegawai.jp_target_display || jpDefault }} ({{ currentYear }})</span>
                                            <span class="text-xs font-medium"
                                                :class="getProgressColor(pegawai.jp_tercapai, pegawai.jp_target_display)">
                                                {{ Math.round(calculateProgress(pegawai)) }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-300"
                                                :class="getProgressBgColor(pegawai.jp_tercapai, pegawai.jp_target_display)"
                                                :style="{ width: Math.min(100, calculateProgress(pegawai)) + '%' }">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div v-if="pegawai.email" class="text-gray-600 mb-1 truncate max-w-[180px]">
                                            <i class="fas fa-envelope w-4 mr-1"></i>{{ pegawai.email }}
                                        </div>
                                        <div v-if="pegawai.telepon" class="text-gray-600 truncate max-w-[180px]">
                                            <i class="fas fa-phone w-4 mr-1"></i>{{ pegawai.telepon }}
                                        </div>
                                        <div v-if="!pegawai.email && !pegawai.telepon" class="text-gray-400 text-xs">
                                            Tidak ada kontak
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link :href="route('pegawai.show', pegawai.id)"
                                            class="text-blue-600 hover:text-blue-900 p-1" @click.stop>
                                        <i class="fas fa-eye"></i>
                                        </Link>
                                        <Link :href="route('pegawai.edit', pegawai.id)"
                                            class="text-green-600 hover:text-green-900 p-1" @click.stop>
                                        <i class="fas fa-edit"></i>
                                        </Link>
                                        <button @click="confirmDelete(pegawai)"
                                            class="text-red-600 hover:text-red-900 p-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="pegawais.links && pegawais.links.length > 3"
                    class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="pegawais.prev_page_url" :href="pegawais.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                            </Link>
                            <Link v-if="pegawais.next_page_url" :href="pegawais.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Menampilkan {{ pegawais.from }} sampai {{ pegawais.to }} dari {{ pegawais.total }}
                                    data
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="(link, index) in pegawais.links" :key="index">
                                        <Link v-if="link.url && link.label !== '...'" :href="link.url"
                                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                            :class="{
                                                'z-10 bg-blue-50 border-blue-500 text-blue-600': link.active,
                                                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active,
                                                'rounded-l-md': index === 0,
                                                'rounded-r-md': index === pegawais.links.length - 1
                                            }" v-html="link.label">
                                        </Link>
                                        <span v-else-if="link.label === '...'"
                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                            ...
                                        </span>
                                    </template>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="pegawais.data.length === 0" class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    {{ searchForm.search ? 'Tidak Ada Hasil' : 'Belum Ada Data Pegawai' }}
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    {{ searchForm.search ? 'Coba ubah kata kunci pencarian' : 'Mulai dengan menambahkan pegawai baru' }}
                </p>
                <Link v-if="!searchForm.search" :href="route('pegawai.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Tambah Pegawai
                </Link>
            </div>
        </div>

        <!-- JP Settings Modal -->
        <div v-if="showJpSettingsModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click.self="showJpSettingsModal = false">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Pengaturan JP Target Tahunan</h3>
                        <button @click="showJpSettingsModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form @submit.prevent="submitJpSettings">
                        <div class="mb-4">
                            <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun
                            </label>
                            <select id="tahun" v-model.number="jpSettingsForm.tahun" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih tahun untuk mengatur JP target</p>
                        </div>

                        <div class="mb-4">
                            <label for="jp_default" class="block text-sm font-medium text-gray-700 mb-2">
                                JP Target untuk Semua Pegawai
                            </label>
                            <input type="number" id="jp_default" v-model.number="jpSettingsForm.jp_default" min="0"
                                max="1000" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Nilai ini akan diterapkan ke semua pegawai untuk tahun
                                {{
                                    jpSettingsForm.tahun }}</p>
                        </div>

                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-2"></i>
                                <div class="text-sm text-yellow-800">
                                    <p><strong>Catatan:</strong></p>
                                    <p>• Pengaturan ini akan memengaruhi semua pegawai</p>
                                    <p>• Target yang sudah diatur manual tidak akan berubah</p>
                                    <p>• Jika tahun dipilih belum memiliki target, akan menggunakan target tahun
                                        sebelumnya</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="showJpSettingsModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click.self="showDeleteModal = false">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Apakah Anda yakin ingin menghapus pegawai <strong>{{ pegawaiToDelete?.nama_lengkap }}</strong>?
                        <br>Data yang sudah dihapus tidak dapat dikembalikan.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <button @click="showDeleteModal = false"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Batal
                        </button>
                        <button @click="deletePegawai"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import { debounce } from 'lodash';

export default {
    components: { AppLayout, Link },
    props: {
        pegawais: Object,
        jpDefault: {
            type: Number,
            default: 20
        },
        currentYear: {
            type: Number,
            default: () => new Date().getFullYear()
        },
        filters: Object
    },
    setup(props) {
        const showJpSettingsModal = ref(false);
        const showDeleteModal = ref(false);
        const pegawaiToDelete = ref(null);

        const searchForm = reactive({
            search: props.filters.search || ''
        });

        const jpSettingsForm = reactive({
            jp_default: props.jpDefault,
            tahun: props.currentYear
        });

        // Generate available years (current year + 2 previous + 2 next)
        const availableYears = ref([]);
        const currentYearInt = parseInt(props.currentYear);
        for (let i = -2; i <= 2; i++) {
            availableYears.value.push(currentYearInt + i);
        }

        const search = debounce(() => {
            router.get(route('pegawai.index'), { search: searchForm.search }, {
                preserveState: true,
                replace: true
            });
        }, 300);

        const clearSearch = () => {
            searchForm.search = '';
            router.get(route('pegawai.index'));
        };

        const confirmDelete = (pegawai) => {
            pegawaiToDelete.value = pegawai;
            showDeleteModal.value = true;
        };

        const deletePegawai = () => {
            router.delete(route('pegawai.destroy', pegawaiToDelete.value.id), {
                onSuccess: () => {
                    showDeleteModal.value = false;
                    pegawaiToDelete.value = null;
                }
            });
        };

        const submitJpSettings = () => {
            router.post(route('pegawai.update-jp-default'), jpSettingsForm, {
                onSuccess: () => {
                    showJpSettingsModal.value = false;
                }
            });
        };

        // navigate to pegawai detail
        const goToDetail = (pegawai) => {
            router.visit(route('pegawai.show', pegawai.id));
        };

        const calculateProgress = (pegawai) => {
            const target = pegawai.jp_target_display || props.jpDefault;
            const achieved = pegawai.jp_tercapai || 0;
            return target > 0 ? (achieved / target) * 100 : 0;
        };

        const getProgressColor = (achieved, target) => {
            const progress = calculateProgress({ jp_tercapai: achieved, jp_target_display: target || props.jpDefault });
            if (progress >= 100) return 'text-green-600';
            if (progress >= 75) return 'text-blue-600';
            if (progress >= 50) return 'text-yellow-600';
            return 'text-red-600';
        };

        const getProgressBgColor = (achieved, target) => {
            const progress = calculateProgress({ jp_tercapai: achieved, jp_target_display: target || props.jpDefault });
            if (progress >= 100) return 'bg-green-500';
            if (progress >= 75) return 'bg-blue-500';
            if (progress >= 50) return 'bg-yellow-500';
            return 'bg-red-500';
        };

        // deterministic color pill for unit_kerja
        const palette = [
            'bg-indigo-100 text-indigo-800',
            'bg-green-100 text-green-800',
            'bg-blue-100 text-blue-800',
            'bg-yellow-100 text-yellow-800',
            'bg-purple-100 text-purple-800',
            'bg-pink-100 text-pink-800',
            'bg-orange-100 text-orange-800',
            'bg-teal-100 text-teal-800'
        ];

        const hashString = (s) => {
            let h = 0;
            for (let i = 0; i < s.length; i++) {
                h = (h << 5) - h + s.charCodeAt(i);
                h |= 0; // convert to 32bit integer
            }
            return Math.abs(h);
        };

        const getUnitPillClass = (unit) => {
            if (!unit) return 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
            const idx = hashString(unit) % palette.length;
            return `inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${palette[idx]}`;
        };

        return {
            showJpSettingsModal,
            showDeleteModal,
            pegawaiToDelete,
            searchForm,
            jpSettingsForm,
            availableYears,
            search,
            clearSearch,
            confirmDelete,
            deletePegawai,
            submitJpSettings,
            goToDetail,
            getUnitPillClass,
            calculateProgress,
            getProgressColor,
            getProgressBgColor
        };
    }
};
</script>
