<template>
    <AppLayout>
        <div class="space-y-6 pb-12">
            <div>
                <Link :href="route('pegawai.index')"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700 rounded-xl border border-indigo-200 hover:border-indigo-300 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i>
                Kembali ke data pegawai
                </Link>
            </div>

            <div class="rounded-2xl border border-slate-200/70 bg-white px-6 py-6 shadow-sm">
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Left Side - Basic Info -->
                    <div class="lg:w-1/3">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-2xl text-indigo-600"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-slate-900">{{ pegawai.nama_lengkap }}</h1>
                                <p class="text-sm text-slate-500">{{ pegawai.nip || 'Tidak Ada NIP' }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 bg-slate-50 p-4 rounded-2xl">
                            <div v-if="pegawai.pangkat_golongan" class="flex justify-between items-center">
                                <span class="text-sm font-medium text-slate-600">Pangkat/Golongan:</span>
                                <span class="text-sm text-slate-900">{{ pegawai.pangkat_golongan }}</span>
                            </div>
                            <div v-if="pegawai.unit_kerja" class="flex justify-between items-center">
                                <span class="text-sm font-medium text-slate-600">Unit Kerja:</span>
                                <span class="text-sm text-slate-900">{{ pegawai.unit_kerja }}</span>
                            </div>
                            <div v-if="pegawai.email" class="flex justify-between items-center">
                                <span class="text-sm font-medium text-slate-600">Email:</span>
                                <span class="text-sm text-slate-900">{{ pegawai.email }}</span>
                            </div>
                            <div v-if="pegawai.telepon" class="flex justify-between items-center">
                                <span class="text-sm font-medium text-slate-600">Telepon:</span>
                                <span class="text-sm text-slate-900">{{ pegawai.telepon }}</span>
                            </div>

                            <!-- Show a message if no additional info is available -->
                            <div v-if="!pegawai.pangkat_golongan && !pegawai.unit_kerja && !pegawai.email && !pegawai.telepon"
                                class="text-sm text-slate-500 italic text-center py-2">
                                Informasi tambahan belum tersedia
                            </div>

                            <hr v-if="yearlyStats[currentYear]" class="my-3">

                            <!-- JP Progress for Current Year - Only show if there's current year data -->
                            <div v-if="yearlyStats[currentYear]" class="space-y-2">
                                <h4 class="text-sm font-semibold text-slate-700 mb-2">Progress JP {{ currentYear }}</h4>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-slate-600">Target JP:</span>
                                    <span class="text-sm font-semibold text-slate-900">{{
                                        formatNumber(yearlyStats[currentYear].jp_target || 0) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-slate-600">JP Tercapai:</span>
                                    <span class="text-sm font-semibold"
                                        :class="getProgressColor(yearlyStats[currentYear].jp_tercapai, yearlyStats[currentYear].jp_target)">
                                        {{ formatNumber(yearlyStats[currentYear].jp_tercapai || 0) }}
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300"
                                        :class="getProgressBgColor(yearlyStats[currentYear].jp_tercapai, yearlyStats[currentYear].jp_target)"
                                        :style="{ width: Math.min(100, yearlyStats[currentYear].progress_percentage || 0) + '%' }">
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-slate-500">
                                        {{ Math.round(yearlyStats[currentYear].progress_percentage || 0) }}% tercapai
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Training Stats -->
                    <div class="lg:w-2/3">
                        <!-- Statistics Cards - Only show if there are trainings -->
                        <div v-if="hasPelatihan" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div class="bg-indigo-50 p-4 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-indigo-600">Total Pelatihan</p>
                                        <p class="text-2xl font-bold text-indigo-700">{{ getTotalPelatihan() }}</p>
                                    </div>
                                    <i class="fas fa-graduation-cap text-indigo-600 text-2xl"></i>
                                </div>
                            </div>
                            <div class="bg-emerald-50 p-4 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-emerald-600">JP Tahun Ini</p>
                                        <p class="text-2xl font-bold text-emerald-700">{{ getJPThisByYear(new
                                            Date().getFullYear()) }}</p>
                                    </div>
                                    <i class="fas fa-chart-line text-emerald-600 text-2xl"></i>
                                </div>
                            </div>
                            <div class="bg-indigo-50 p-4 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-indigo-600">Sertifikat</p>
                                        <p class="text-2xl font-bold text-indigo-700">{{ getTrainingWithCertificates()
                                            }}</p>
                                        <p class="text-xs text-indigo-600">dari {{ getTotalPelatihan() }}</p>
                                    </div>
                                    <i class="fas fa-certificate text-indigo-600 text-2xl"></i>
                                </div>
                            </div>
                            <div class="bg-amber-50 p-4 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-amber-600">Rata-rata JP/Tahun</p>
                                        <p class="text-2xl font-bold text-amber-700">{{ getAverageJPPerYear() }}</p>
                                    </div>
                                    <i class="fas fa-chart-bar text-amber-600 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Statistics Row - Only show if there are trainings with meaningful data -->
                        <div v-if="hasPelatihan && getTotalPelatihan() > 0"
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div v-if="getMostFrequentType()" class="bg-indigo-50 p-4 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-indigo-600">Jenis Terbanyak</p>
                                        <p class="text-sm font-bold text-indigo-700">{{ getMostFrequentType() }}</p>
                                        <p class="text-xs text-indigo-600">{{ getMostFrequentTypeCount() }} pelatihan
                                        </p>
                                    </div>
                                    <i class="fas fa-medal text-indigo-600 text-2xl"></i>
                                </div>
                            </div>
                            <div v-if="getMostActiveYear()" class="bg-emerald-50 p-4 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-emerald-600">Tahun Teraktif</p>
                                        <p class="text-2xl font-bold text-emerald-700">{{ getMostActiveYear() }}</p>
                                        <p class="text-xs text-emerald-600">{{ getMostActiveYearCount() }} pelatihan</p>
                                    </div>
                                    <i class="fas fa-fire text-emerald-600 text-2xl"></i>
                                </div>
                            </div>
                            <div v-if="getHighestJPInYear() > 0" class="bg-rose-50 p-4 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-rose-600">JP Terbanyak</p>
                                        <p class="text-2xl font-bold text-rose-700">{{ getHighestJPInYear() }}</p>
                                        <p class="text-xs text-rose-600">pada {{ getHighestJPYear() }}</p>
                                    </div>
                                    <i class="fas fa-trophy text-rose-600 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- JP Progress per Year -->
                        <!-- Yearly JP Achievement Overview - Only show if there are trainings -->
                        <div v-if="hasPelatihan" class="mb-6">
                            <h3 class="text-lg font-semibold text-slate-900 mb-4">Capaian JP Per Tahun</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div v-for="(data, year) in filteredPelatihansByYear" :key="year"
                                    class="bg-white px-5 py-4 border border-slate-200/70 rounded-2xl shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-slate-900">{{ year }}</h4>
                                        <span class="text-sm font-medium"
                                            :class="getYearProgressPercentage(data.totalJP) >= 100 ? 'text-emerald-600' : 'text-amber-600'">
                                            {{ data.totalJP }} JP ({{ getYearProgressPercentage(data.totalJP) }}%)
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 mb-2">
                                        <div class="h-2 rounded-full transition-all duration-300"
                                            :class="getYearProgressBgColor(data.totalJP)"
                                            :style="`width: ${Math.min(getYearProgressPercentage(data.totalJP), 100)}%`">
                                        </div>
                                    </div>
                                    <div class="text-sm text-slate-600">
                                        {{ (data.pelatihan && data.pelatihan.length) || 0 }} pelatihan • Target: {{
                                            pegawai.jp_target || 20 }}
                                        JP/tahun
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State when no trainings -->
                        <div v-if="!hasPelatihan" class="text-center py-12">
                            <div
                                class="mx-auto w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-graduation-cap text-slate-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900 mb-2">Belum Ada Data Pelatihan</h3>
                            <p class="text-sm text-slate-500">Pegawai ini belum memiliki riwayat pelatihan yang tercatat
                                dalam sistem.</p>
                        </div>
                    </div>
                </div>

                <div v-if="hasPelatihan">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                        <h2 class="text-lg font-semibold text-slate-900">Riwayat Pelatihan</h2>

                        <!-- Search and Filters -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                            <!-- Search Input -->
                            <div class="relative">
                                <input type="text" v-model="searchQuery" placeholder="Cari pelatihan, penyelenggara..."
                                    class="form-input sm:w-64 pr-10" />
                                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>

                            <!-- Year Filter -->
                            <select v-model="selectedYear" @change="filterByYear" class="form-input sm:w-40">
                                <option value="">Semua Tahun</option>
                                <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                            </select>

                            <!-- View Toggle -->
                            <button @click="toggleView"
                                class="rounded-xl border border-slate-200/70 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:border-indigo-400 hover:text-indigo-600">
                                <i :class="viewMode === 'grid' ? 'fas fa-list' : 'fas fa-th-large'"></i>
                                {{ viewMode === 'grid' ? 'List' : 'Grid' }}
                            </button>
                        </div>
                    </div>

                    <!-- Search Results Count -->
                    <div v-if="searchQuery" class="mb-4">
                        <p class="text-sm text-slate-600">
                            Menampilkan {{ searchResults.length }} pelatihan dari pencarian "<strong>{{ searchQuery
                                }}</strong>"
                            <button @click="clearSearch" class="text-indigo-600 hover:text-indigo-700 ml-2">
                                <i class="fas fa-times"></i> Hapus filter
                            </button>
                        </p>
                    </div>

                    <!-- Grid View -->
                    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="pel in filteredPelatihan" :key="pel.id"
                            class="bg-white border border-slate-200/70 rounded-2xl p-4 hover:shadow-md transition-shadow"
                            @dragover.prevent @dragenter.prevent="addDragClass($event)"
                            @dragleave.prevent="removeDragClass($event)" @drop.prevent="handleFileDrop($event, pel)">
                            <!-- Training Card Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-slate-900 text-sm mb-1 line-clamp-2">{{
                                        pel.nama_pelatihan }}</h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="getJenisColor(pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown')">
                                        {{ pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-indigo-600">{{ pel.jp }} JP</div>
                                    <div class="text-xs text-slate-500">{{ formatDate(pel.tanggal_mulai) }}</div>
                                </div>
                            </div>

                            <!-- Training Details -->
                            <div class="space-y-2 text-xs text-slate-600 mb-4">
                                <div><i class="fas fa-building w-4"></i> {{ pel.penyelenggara }}</div>
                                <div><i class="fas fa-calendar w-4"></i> {{ formatDate(pel.tanggal_mulai) }} - {{
                                    formatDate(pel.tanggal_selesai) }}</div>
                                <div v-if="pel.tempat"><i class="fas fa-map-marker-alt w-4"></i> {{ pel.tempat }}</div>
                            </div>

                            <!-- Certificate Section -->
                            <div class="border-t pt-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <a v-if="pel.file_sertifikat" :href="`/storage/${pel.file_sertifikat}`"
                                            target="_blank"
                                            class="text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 text-xs">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>{{ getFileName(pel.file_sertifikat) || 'Lihat' }}</span>
                                        </a>
                                        <span v-else class="text-xs text-slate-400">Tidak ada sertifikat</span>
                                    </div>

                                    <!-- Upload Area (smaller for grid) -->
                                    <div class="upload-area-small" @dragover.prevent
                                        @dragenter.prevent="addDragClass($event)"
                                        @dragleave.prevent="removeDragClass($event)"
                                        @drop.prevent="handleFileDrop($event, pel)"
                                        @click="$refs[`fileInput${pel.id}`] && $refs[`fileInput${pel.id}`][0] ? $refs[`fileInput${pel.id}`][0].click() : $refs[`fileInput${pel.id}`].click()">
                                        <div v-if="!editingFiles[pel.id] && !isUploading[pel.id]">
                                            <i class="fas fa-cloud-upload text-slate-400"></i>
                                        </div>
                                        <div v-else-if="isUploading[pel.id]">
                                            <i class="fas fa-spinner fa-spin text-indigo-600"></i>
                                        </div>
                                        <div v-else>
                                            <i class="fas fa-file-pdf text-emerald-600"></i>
                                        </div>
                                        <input :ref="`fileInput${pel.id}`" type="file" accept=".pdf,.jpg,.jpeg,.png"
                                            style="display:none" @change="handleFileSelect($event, pel)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List View -->
                    <div v-else v-for="(data, year) in displayedPelatihansByYear" :key="year" class="mb-6">
                        <h3 class="text-lg font-medium text-slate-700 mb-3 flex items-center">
                            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm mr-3">{{ year
                            }}</span>
                            <span class="text-sm text-slate-500">({{ (data.pelatihan && data.pelatihan.length) || 0 }}
                                pelatihan)</span>
                        </h3>
                        <ul class="space-y-3">
                            <li v-for="pel in data.pelatihan || []" :key="pel.id"
                                class="bg-white border border-slate-200/70 rounded-2xl p-4 hover:shadow-md transition-shadow"
                                @dragover.prevent @dragenter.prevent="addDragClass($event)"
                                @dragleave.prevent="removeDragClass($event)"
                                @drop.prevent="handleFileDrop($event, pel)">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="font-semibold text-slate-900 flex-1">{{ pel.nama_pelatihan }}
                                            </h4>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-3"
                                                :class="getJenisColor(pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown')">
                                                {{ pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown' }}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-slate-600 mb-3">
                                            <div><i class="fas fa-building w-4 mr-1"></i>{{ pel.penyelenggara }}</div>
                                            <div><i class="fas fa-calendar w-4 mr-1"></i>{{
                                                formatDate(pel.tanggal_mulai) }} - {{ formatDate(pel.tanggal_selesai) }}
                                            </div>
                                            <div v-if="pel.tempat"><i class="fas fa-map-marker-alt w-4 mr-1"></i>{{
                                                pel.tempat }}</div>
                                            <div><i class="fas fa-clock w-4 mr-1"></i>{{ pel.jp }} JP</div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-2">
                                        <div class="text-lg font-bold text-indigo-600">{{ pel.jp }} JP</div>

                                        <!-- Certificate and Upload Section -->
                                        <div class="flex items-center gap-2">
                                            <a v-if="pel.file_sertifikat" :href="`/storage/${pel.file_sertifikat}`"
                                                target="_blank"
                                                class="text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-2 text-sm">
                                                <i class="fas fa-file-pdf"></i>
                                                <span>{{ getFileName(pel.file_sertifikat) || 'Lihat Sertifikat'
                                                }}</span>
                                            </a>
                                            <span v-else class="text-xs text-slate-400">Tidak ada sertifikat</span>

                                            <div class="upload-area" @dragover.prevent
                                                @dragenter.prevent="addDragClass($event)"
                                                @dragleave.prevent="removeDragClass($event)"
                                                @drop.prevent="handleFileDrop($event, pel)"
                                                @click="$refs[`fileInput${pel.id}`] && $refs[`fileInput${pel.id}`][0] ? $refs[`fileInput${pel.id}`][0].click() : $refs[`fileInput${pel.id}`].click()">
                                                <div v-if="!editingFiles[pel.id] && !isUploading[pel.id]"
                                                    class="text-xs text-center">
                                                    <div v-if="pel.file_sertifikat"
                                                        class="text-emerald-600 mb-1 truncate max-w-28">
                                                        <i class="fas fa-file-pdf mr-1"></i>
                                                        <span class="text-sm">{{ getFileName(pel.file_sertifikat)
                                                        }}</span>
                                                    </div>
                                                    <div class="text-slate-500">
                                                        <i class="fas fa-cloud-upload mr-1"></i>
                                                        <span class="text-xs">Drop/Click untuk {{ pel.file_sertifikat ?
                                                            'ganti' : 'upload' }}</span>
                                                    </div>
                                                </div>
                                                <div v-else-if="isUploading[pel.id]"
                                                    class="text-xs text-center text-indigo-600">
                                                    <i class="fas fa-spinner fa-spin mr-1"></i>
                                                    <span>Uploading...</span>
                                                </div>
                                                <span v-else class="text-xs text-emerald-600">
                                                    <i class="fas fa-file-pdf"></i> {{ editingFiles[pel.id].name }}
                                                </span>
                                                <input :ref="`fileInput${pel.id}`" type="file"
                                                    accept=".pdf,.jpg,.jpeg,.png" style="display:none"
                                                    @change="handleFileSelect($event, pel)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div v-else class="text-sm text-slate-500 italic">Belum ada data pelatihan.</div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    components: { AppLayout, Link },
    props: {
        pegawai: Object,
        pelatihans: Array,
        yearlyStats: Object,
        availableYears: Array,
        currentYear: Number
    },
    data() {
        return {
            editingFiles: {},
            isUploading: {},
            uploading: {},
            dragover: {},
            selectedYear: '',
            viewMode: 'grid', // 'list' or 'grid'
            searchQuery: '',
            searchResults: []
        };
    },
    computed: {
        hasPelatihan() {
            return this.pelatihans && this.pelatihans.length > 0;
        },

        sortedAvailableYears() {
            return [...(this.availableYears || [])].sort((a, b) => b - a);
        },

        pelatihansByYear() {
            const result = {};
            if (!this.pelatihans) return result;

            this.pelatihans.forEach(pelatihan => {
                const year = new Date(pelatihan.tanggal_mulai).getFullYear();
                if (!result[year]) {
                    result[year] = [];
                }
                result[year].push(pelatihan);
            });

            return result;
        },

        displayedPelatihansByYear() {
            const result = {};
            const years = this.selectedYear ? [this.selectedYear] : this.sortedAvailableYears;

            years.forEach(year => {
                const pelList = this.pelatihansByYear[year] || [];
                const stats = this.yearlyStats[year] || {
                    jp_tercapai: 0,
                    jp_target: 20,
                    total_pelatihan: 0,
                    progress_percentage: 0,
                    is_target_reached: false
                };

                result[year] = {
                    pelatihan: pelList,
                    totalJP: stats.jp_tercapai,
                    targetJP: stats.jp_target,
                    totalPelatihan: stats.total_pelatihan,
                    progressPercentage: stats.progress_percentage,
                    isTargetReached: stats.is_target_reached
                };
            });

            return result;
        },

        filteredPelatihan() {
            if (!this.selectedYear) {
                return this.getAllPelatihan();
            }
            const y = this.pelatihansByYear[this.selectedYear] || {};
            return Array.isArray(y.pelatihan) ? y.pelatihan : [];
        },

        filteredPelatihansByYear() {
            if (!this.searchQuery) {
                return this.displayedPelatihansByYear;
            }

            const filtered = {};
            const query = this.searchQuery.toLowerCase();

            Object.keys(this.displayedPelatihansByYear).forEach(year => {
                const data = this.displayedPelatihansByYear[year] || { pelatihan: [] };
                const pelList = Array.isArray(data.pelatihan) ? data.pelatihan : [];

                const filteredPelatihan = pelList.filter(pelatihan => {
                    const nama = (pelatihan.nama_pelatihan || '').toString().toLowerCase();
                    const peny = (pelatihan.penyelenggara || '').toString().toLowerCase();
                    const jenis = pelatihan.jenis_pelatihan && pelatihan.jenis_pelatihan.nama ? pelatihan.jenis_pelatihan.nama.toString().toLowerCase() : '';
                    return nama.includes(query) || peny.includes(query) || jenis.includes(query);
                });

                if (filteredPelatihan.length > 0) {
                    filtered[year] = {
                        ...data,
                        pelatihan: filteredPelatihan,
                        totalJP: filteredPelatihan.reduce((sum, p) => sum + (p.jp || 0), 0)
                    };
                }
            });

            // Update search results
            this.searchResults = Object.values(filtered).flatMap(data => data.pelatihan);

            return filtered;
        }
    },
    methods: {
        formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        },

        // Helper to extract filename from storage path
        getFileName(filePath) {
            if (!filePath) return '';
            return filePath.split('/').pop().split('\\').pop();
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            try {
                const d = new Date(dateStr);
                return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(d);
            } catch (e) {
                return dateStr;
            }
        },

        // Alias for template compatibility (some templates call formatTanggal)
        formatTanggal(dateStr) {
            return this.formatDate(dateStr);
        },

        viewCertificate(filePath) {
            if (!filePath) return;
            // Open certificate in new window/tab
            const url = filePath.startsWith('/storage/') ? filePath : `/storage/${filePath}`;
            window.open(url, '_blank');
        },

        calculateProgress(pegawai) {
            const target = pegawai.jp_target || 0;
            const achieved = pegawai.jp_tercapai || 0;
            return target > 0 ? (achieved / target) * 100 : 0;
        },

        getProgressColor(achieved, target) {
            const progress = this.calculateProgress({ jp_tercapai: achieved, jp_target: target });
            if (progress >= 100) return 'text-emerald-600';
            if (progress >= 75) return 'text-indigo-600';
            if (progress >= 50) return 'text-amber-600';
            return 'text-rose-600';
        },

        getProgressBgColor(achieved, target) {
            const progress = this.calculateProgress({ jp_tercapai: achieved, jp_target: target });
            if (progress >= 100) return 'bg-emerald-600';
            if (progress >= 75) return 'bg-indigo-600';
            if (progress >= 50) return 'bg-amber-600';
            return 'bg-rose-600';
        },

        getJenisColor(jenis) {
            const colorMap = {
                'Diklat Struktural': 'bg-indigo-100 text-indigo-700',
                'Diklat Fungsional': 'bg-emerald-100 text-emerald-700',
                'Diklat Teknis': 'bg-sky-100 text-sky-700',
                'Workshop': 'bg-amber-100 text-amber-700',
                'Pelatihan Jarak Jauh': 'bg-indigo-100 text-indigo-700',
                'E-Learning': 'bg-slate-200 text-slate-700',
                'Seminar': 'bg-rose-100 text-rose-700'
            };
            return colorMap[jenis] || 'bg-slate-100 text-slate-700';
        },

        getTotalPelatihan() {
            return this.pelatihans ? this.pelatihans.length : 0;
        },

        getJPThisByYear(year) {
            const stats = this.yearlyStats[year];
            return stats ? stats.jp_tercapai : 0;
        },

        getMostFrequentType() {
            const allPelatihan = this.pelatihans || [];
            const typeCount = {};

            allPelatihan.forEach(pel => {
                const type = pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown';
                typeCount[type] = (typeCount[type] || 0) + 1;
            });

            let maxCount = 0;
            let mostFrequent = '';

            Object.entries(typeCount).forEach(([type, count]) => {
                if (count > maxCount) {
                    maxCount = count;
                    mostFrequent = type;
                }
            });

            return mostFrequent;
        },

        getMostFrequentTypeCount() {
            const allPelatihan = this.getAllPelatihan();
            const typeCount = {};

            allPelatihan.forEach(pel => {
                const type = pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown';
                typeCount[type] = (typeCount[type] || 0) + 1;
            });

            let maxCount = 0;

            Object.entries(typeCount).forEach(([type, count]) => {
                if (count > maxCount) {
                    maxCount = count;
                }
            });

            return maxCount;
        },

        getAllPelatihan() {
            return this.pelatihans || [];
        },

        toggleView() {
            this.viewMode = this.viewMode === 'list' ? 'grid' : 'list';
        },

        filterByYear() {
            // Method is handled by computed property
        },

        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
        },

        // Statistics methods for the enhanced cards
        getTrainingWithCertificates() {
            return this.pelatihans ? this.pelatihans.filter(p => p.file_sertifikat).length : 0;
        },

        getAverageJPPerYear() {
            const years = this.availableYears || [];
            if (years.length === 0) return 0;

            const totalJP = years.reduce((sum, year) => {
                const stats = this.yearlyStats[year];
                return sum + (stats ? stats.jp_tercapai : 0);
            }, 0);

            return Math.round(totalJP / years.length);
        },

        getMostActiveYear() {
            let maxCount = 0;
            let mostActiveYear = '';

            Object.entries(this.pelatihansByYear || {}).forEach(([year, data]) => {
                const list = data && Array.isArray(data.pelatihan) ? data.pelatihan : [];
                if (list.length > maxCount) {
                    maxCount = list.length;
                    mostActiveYear = year;
                }
            });

            return mostActiveYear;
        },

        getMostActiveYearCount() {
            let maxCount = 0;

            Object.entries(this.pelatihansByYear || {}).forEach(([year, data]) => {
                const list = data && Array.isArray(data.pelatihan) ? data.pelatihan : [];
                if (list.length > maxCount) {
                    maxCount = list.length;
                }
            });

            return maxCount;
        },

        getHighestJPAchievement() {
            let maxJP = 0;
            let year = '';

            Object.entries(this.pelatihansByYear || {}).forEach(([y, data]) => {
                if (data.totalJP > maxJP) {
                    maxJP = data.totalJP;
                    year = y;
                }
            });

            return { jp: maxJP, year };
        },

        getHighestJPInYear() {
            const result = this.getHighestJPAchievement();
            return result.jp;
        },

        getHighestJPYear() {
            const result = this.getHighestJPAchievement();
            return result.year;
        },

        getYearProgressPercentage(jp) {
            const target = this.pegawai.jp_target || 20;
            return Math.round((jp / target) * 100);
        },

        getYearProgressBgColor(jp) {
            const percentage = this.getYearProgressPercentage(jp);
            if (percentage >= 100) return 'bg-emerald-600';
            if (percentage >= 75) return 'bg-indigo-600';
            if (percentage >= 50) return 'bg-amber-600';
            return 'bg-rose-600';
        },
        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        handleFileSelect(event, pel) {
            const file = event.target.files ? event.target.files[0] : null;
            if (!file) return;
            // show selected file name in UI
            this.editingFiles = { ...this.editingFiles, [pel.id]: file };
            this.uploadFileForPel(pel, file);
        },

        handleFileDrop(event, pel) {
            this.removeDragClass(event);
            const file = event.dataTransfer && event.dataTransfer.files ? event.dataTransfer.files[0] : null;
            if (!file) return;
            this.editingFiles = { ...this.editingFiles, [pel.id]: file };
            this.uploadFileForPel(pel, file);
        },

        addDragClass(event) {
            const uploadArea = event.target.closest('.upload-area, .upload-area-small');
            if (uploadArea) {
                uploadArea.classList.add('drag-over');
            }
        },

        removeDragClass(event) {
            const uploadArea = event.target.closest('.upload-area, .upload-area-small');
            if (uploadArea) {
                uploadArea.classList.remove('drag-over');
            }
        },

        async uploadFileForPel(pel, file) {
            try {
                // Set uploading state
                this.isUploading = { ...this.isUploading, [pel.id]: true };

                const formData = new FormData();

                // Append required fields for update (use existing pel values)
                formData.append('pegawai_id', pel.pegawai_id || this.pegawai.id || '');
                formData.append('nama_pelatihan', pel.nama_pelatihan || '');
                formData.append('jenis_pelatihan_id', pel.jenis_pelatihan_id || (pel.jenis_pelatihan ? pel.jenis_pelatihan.id : ''));
                formData.append('penyelenggara', pel.penyelenggara || '');
                formData.append('tempat', pel.tempat || '');
                formData.append('tanggal_mulai', pel.tanggal_mulai || '');
                formData.append('tanggal_selesai', pel.tanggal_selesai || '');
                formData.append('jp', pel.jp || 0);
                formData.append('status', pel.status || 'selesai');
                formData.append('_method', 'PUT');

                // Use 'sertifikat' as the field name to match backend expectations
                formData.append('sertifikat', file);

                console.log('Uploading file for pelatihan:', pel.id, 'File:', file.name);

                const response = await fetch(route('pelatihan.update', pel.id), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                });

                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (response.ok) {
                    const result = await response.json();
                    console.log('Upload success result:', result);
                    // update local pel object (server returns loaded model)
                    if (result && result.data) {
                        // copy relevant fields into pel
                        Object.assign(pel, result.data);
                    }
                    // Show success feedback
                    this.showNotification('Sertifikat berhasil diupload', 'success');
                } else {
                    const err = await response.json().catch(() => ({}));
                    console.error('Upload failed', err);
                    this.showNotification('Gagal mengupload sertifikat: ' + (err.message || 'Unknown error'), 'error');
                }
            } catch (e) {
                console.error('Upload error', e);
                this.showNotification('Terjadi kesalahan saat mengupload', 'error');
            } finally {
                // Clear states
                const { [pel.id]: removedFile, ...remainingFiles } = this.editingFiles;
                const { [pel.id]: removedUpload, ...remainingUploads } = this.isUploading;
                this.editingFiles = remainingFiles;
                this.isUploading = remainingUploads;
            }
        },

        showNotification(message, type = 'info') {
            // Simple notification - you can replace with a more sophisticated toast system
            const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-error' : 'alert-info';

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-2xl text-white ${alertClass === 'alert-success' ? 'bg-emerald-600' : alertClass === 'alert-error' ? 'bg-rose-600' : 'bg-indigo-600'}`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }
    }
};
</script>

<style scoped>
/* File upload styles */
.upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 6px;
    padding: 6px;
    text-align: center;
    cursor: pointer;
    background-color: #f9fafb;
    transition: all 0.2s;
    min-height: 36px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: #6b7280;
    line-height: 1.2;
    min-width: 80px;
}

.upload-area:hover {
    border-color: #3b82f6;
    background-color: #eff6ff;
    color: #3b82f6;
}

.upload-area.drag-over {
    border-color: #10b981;
    background-color: #ecfdf5;
    color: #10b981;
}

.upload-area-small {
    border: 2px dashed #d1d5db;
    border-radius: 4px;
    padding: 4px;
    text-align: center;
    cursor: pointer;
    background-color: #f9fafb;
    transition: all 0.2s;
    min-height: 24px;
    min-width: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.upload-area-small:hover {
    border-color: #3b82f6;
    background-color: #eff6ff;
    color: #3b82f6;
}

.upload-area-small.drag-over {
    border-color: #10b981;
    background-color: #ecfdf5;
    color: #10b981;
}

/* Utility classes */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.max-w-28 {
    max-width: 7rem;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .upload-area {
        min-width: 60px;
        font-size: 9px;
        padding: 4px;
        min-height: 28px;
    }

    .grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3 {
        grid-template-columns: 1fr;
    }
}
</style>
