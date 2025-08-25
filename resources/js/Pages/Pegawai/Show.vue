<template>
    <AppLayout>
        <div class="p-4">
            <div class="mb-4">
                <Link :href="route('progress')" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Progress
                </Link>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Left Side - Basic Info -->
                    <div class="lg:w-1/3">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-2xl text-blue-600"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ pegawai.nama_lengkap }}</h1>
                                <p class="text-sm text-gray-500">{{ pegawai.nip || 'Tidak Ada NIP' }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">Pangkat/Golongan:</span>
                                <span class="text-sm text-gray-900">{{ pegawai.pangkat_golongan || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">Unit Kerja:</span>
                                <span class="text-sm text-gray-900">{{ pegawai.unit_kerja || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">Email:</span>
                                <span class="text-sm text-gray-900">{{ pegawai.email || '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">Telepon:</span>
                                <span class="text-sm text-gray-900">{{ pegawai.telepon || '-' }}</span>
                            </div>
                            <hr class="my-3">
                            <!-- JP Progress -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">Target JP:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ formatNumber(pegawai.jp_target
                                        || 0) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">JP Tercapai:</span>
                                    <span class="text-sm font-semibold"
                                        :class="getProgressColor(pegawai.jp_tercapai, pegawai.jp_target)">
                                        {{ formatNumber(pegawai.jp_tercapai || 0) }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300"
                                        :class="getProgressBgColor(pegawai.jp_tercapai, pegawai.jp_target)"
                                        :style="{ width: Math.min(100, calculateProgress(pegawai)) + '%' }"></div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-500">
                                        {{ Math.round(calculateProgress(pegawai)) }}% tercapai
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Training Stats -->
                    <div class="lg:w-2/3">
                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-blue-600">Total Pelatihan</p>
                                        <p class="text-2xl font-bold text-blue-900">{{ getTotalPelatihan() }}</p>
                                    </div>
                                    <i class="fas fa-graduation-cap text-blue-400 text-2xl"></i>
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-green-600">JP Tahun Ini</p>
                                        <p class="text-2xl font-bold text-green-900">{{ getJPThisByYear(new
                                            Date().getFullYear()) }}</p>
                                    </div>
                                    <i class="fas fa-chart-line text-green-400 text-2xl"></i>
                                </div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-purple-600">Jenis Terbanyak</p>
                                        <p class="text-sm font-bold text-purple-900">{{ getMostFrequentType() || '-' }}
                                        </p>
                                    </div>
                                    <i class="fas fa-medal text-purple-400 text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="hasPelatihan">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Riwayat Pelatihan</h2>
                        <div class="flex items-center gap-2">
                            <select v-model="selectedYear" @change="filterByYear"
                                class="text-sm border rounded-md px-2 py-1">
                                <option value="">Semua Tahun</option>
                                <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                            </select>
                            <button @click="toggleView"
                                class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded-md">
                                <i :class="viewMode === 'grid' ? 'fas fa-list' : 'fas fa-th-large'"></i>
                                {{ viewMode === 'grid' ? 'List' : 'Grid' }}
                            </button>
                        </div>
                    </div>

                    <!-- Grid View -->
                    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="pel in filteredPelatihan" :key="pel.id"
                            class="bg-white border rounded-lg p-4 hover:shadow-md transition-shadow" @dragover.prevent
                            @dragenter.prevent="addDragClass($event)" @dragleave.prevent="removeDragClass($event)"
                            @drop.prevent="handleFileDrop($event, pel)">
                            <!-- Training Card Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2">{{
                                        pel.nama_pelatihan }}</h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="getJenisColor(pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown')">
                                        {{ pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-blue-600">{{ pel.jp }} JP</div>
                                    <div class="text-xs text-gray-500">{{ formatDate(pel.tanggal_mulai) }}</div>
                                </div>
                            </div>

                            <!-- Training Details -->
                            <div class="space-y-2 text-xs text-gray-600 mb-4">
                                <div><i class="fas fa-building w-4"></i> {{ pel.penyelenggara }}</div>
                                <div><i class="fas fa-calendar w-4"></i> {{ formatDate(pel.tanggal_mulai) }} - {{
                                    formatDate(pel.tanggal_selesai) }}</div>
                                <div v-if="pel.tempat"><i class="fas fa-map-marker-alt w-4"></i> {{ pel.tempat }}</div>
                            </div>

                            <!-- Certificate Section -->
                            <div class="border-t pt-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <a v-if="pel.sertifikat_path" :href="`/storage/${pel.sertifikat_path}`"
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-800 inline-flex items-center gap-1 text-xs">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>{{ getFileName(pel.sertifikat_path) || 'Lihat' }}</span>
                                        </a>
                                        <span v-else class="text-xs text-gray-400">Tidak ada sertifikat</span>
                                    </div>

                                    <!-- Upload Area (smaller for grid) -->
                                    <div class="upload-area-small" @dragover.prevent
                                        @dragenter.prevent="addDragClass($event)"
                                        @dragleave.prevent="removeDragClass($event)"
                                        @drop.prevent="handleFileDrop($event, pel)"
                                        @click="$refs[`fileInput${pel.id}`] && $refs[`fileInput${pel.id}`][0] ? $refs[`fileInput${pel.id}`][0].click() : $refs[`fileInput${pel.id}`].click()">
                                        <div v-if="!editingFiles[pel.id] && !isUploading[pel.id]">
                                            <i class="fas fa-cloud-upload text-gray-400"></i>
                                        </div>
                                        <div v-else-if="isUploading[pel.id]">
                                            <i class="fas fa-spinner fa-spin text-blue-500"></i>
                                        </div>
                                        <div v-else>
                                            <i class="fas fa-file-pdf text-green-500"></i>
                                        </div>
                                        <input :ref="`fileInput${pel.id}`" type="file" accept=".pdf,.jpg,.jpeg,.png"
                                            style="display:none" @change="handleFileSelect($event, pel)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List View -->
                    <div v-else v-for="(pelList, year) in displayedPelatihansByYear" :key="year" class="mb-6">
                        <h3 class="text-lg font-medium text-gray-700 mb-3 flex items-center">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm mr-3">{{ year
                                }}</span>
                            <span class="text-sm text-gray-500">({{ pelList.length }} pelatihan)</span>
                        </h3>
                        <ul class="space-y-3">
                            <li v-for="pel in pelList" :key="pel.id"
                                class="bg-white border rounded-lg p-4 hover:shadow-md transition-shadow"
                                @dragover.prevent @dragenter.prevent="addDragClass($event)"
                                @dragleave.prevent="removeDragClass($event)"
                                @drop.prevent="handleFileDrop($event, pel)">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="font-semibold text-gray-900 flex-1">{{ pel.nama_pelatihan }}</h4>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-3"
                                                :class="getJenisColor(pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown')">
                                                {{ pel.jenis_pelatihan ? pel.jenis_pelatihan.nama : 'Unknown' }}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
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
                                        <div class="text-lg font-bold text-blue-600">{{ pel.jp }} JP</div>

                                        <!-- Certificate and Upload Section -->
                                        <div class="flex items-center gap-2">
                                            <a v-if="pel.sertifikat_path" :href="`/storage/${pel.sertifikat_path}`"
                                                target="_blank"
                                                class="text-blue-600 hover:text-blue-800 inline-flex items-center gap-2 text-sm">
                                                <i class="fas fa-file-pdf"></i>
                                                <span>{{ getFileName(pel.sertifikat_path) || 'Lihat Sertifikat'
                                                    }}</span>
                                            </a>
                                            <span v-else class="text-xs text-gray-400">Tidak ada sertifikat</span>

                                            <div class="upload-area" @dragover.prevent
                                                @dragenter.prevent="addDragClass($event)"
                                                @dragleave.prevent="removeDragClass($event)"
                                                @drop.prevent="handleFileDrop($event, pel)"
                                                @click="$refs[`fileInput${pel.id}`] && $refs[`fileInput${pel.id}`][0] ? $refs[`fileInput${pel.id}`][0].click() : $refs[`fileInput${pel.id}`].click()">
                                                <div v-if="!editingFiles[pel.id] && !isUploading[pel.id]"
                                                    class="text-xs text-center">
                                                    <div v-if="pel.sertifikat_path"
                                                        class="text-green-600 mb-1 truncate max-w-28">
                                                        <i class="fas fa-file-pdf mr-1"></i>
                                                        <span class="text-sm">{{ getFileName(pel.sertifikat_path)
                                                            }}</span>
                                                    </div>
                                                    <div class="text-gray-500">
                                                        <i class="fas fa-cloud-upload mr-1"></i>
                                                        <span class="text-xs">Drop/Click untuk {{ pel.sertifikat_path ?
                                                            'ganti' : 'upload' }}</span>
                                                    </div>
                                                </div>
                                                <div v-else-if="isUploading[pel.id]"
                                                    class="text-xs text-center text-blue-600">
                                                    <i class="fas fa-spinner fa-spin mr-1"></i>
                                                    <span>Uploading...</span>
                                                </div>
                                                <span v-else class="text-xs text-green-600">
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
                <div v-else class="text-sm text-gray-500 italic">Belum ada data pelatihan.</div>
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
        pelatihansByYear: Object,
    },
    data() {
        return {
            editingFiles: {},
            isUploading: {},
            selectedYear: '',
            viewMode: 'list' // 'list' or 'grid'
        };
    },
    computed: {
        hasPelatihan() {
            return Object.keys(this.pelatihansByYear || {}).length > 0;
        },

        availableYears() {
            return Object.keys(this.pelatihansByYear || {}).sort((a, b) => b - a);
        },

        displayedPelatihansByYear() {
            if (!this.selectedYear) return this.pelatihansByYear;
            const filtered = {};
            if (this.pelatihansByYear[this.selectedYear]) {
                filtered[this.selectedYear] = this.pelatihansByYear[this.selectedYear];
            }
            return filtered;
        },

        filteredPelatihan() {
            if (!this.selectedYear) {
                return this.getAllPelatihan();
            }
            return this.pelatihansByYear[this.selectedYear] || [];
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

        calculateProgress(pegawai) {
            const target = pegawai.jp_target || 0;
            const achieved = pegawai.jp_tercapai || 0;
            return target > 0 ? (achieved / target) * 100 : 0;
        },

        getProgressColor(achieved, target) {
            const progress = this.calculateProgress({ jp_tercapai: achieved, jp_target: target });
            if (progress >= 100) return 'text-green-600';
            if (progress >= 75) return 'text-blue-600';
            if (progress >= 50) return 'text-yellow-600';
            return 'text-red-600';
        },

        getProgressBgColor(achieved, target) {
            const progress = this.calculateProgress({ jp_tercapai: achieved, jp_target: target });
            if (progress >= 100) return 'bg-green-500';
            if (progress >= 75) return 'bg-blue-500';
            if (progress >= 50) return 'bg-yellow-500';
            return 'bg-red-500';
        },

        getJenisColor(jenis) {
            const colorMap = {
                'Diklat Struktural': 'bg-blue-100 text-blue-800',
                'Diklat Fungsional': 'bg-green-100 text-green-800',
                'Diklat Teknis': 'bg-purple-100 text-purple-800',
                'Workshop': 'bg-orange-100 text-orange-800',
                'Pelatihan Jarak Jauh': 'bg-indigo-100 text-indigo-800',
                'E-Learning': 'bg-teal-100 text-teal-800',
                'Seminar': 'bg-red-100 text-red-800'
            };
            return colorMap[jenis] || 'bg-gray-100 text-gray-800';
        },

        getTotalPelatihan() {
            return this.getAllPelatihan().length;
        },

        getJPThisByYear(year) {
            const pelatihans = this.pelatihansByYear[year] || [];
            return pelatihans.reduce((total, pel) => total + (pel.jp || 0), 0);
        },

        getMostFrequentType() {
            const allPelatihan = this.getAllPelatihan();
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

        getAllPelatihan() {
            const all = [];
            Object.values(this.pelatihansByYear || {}).forEach(yearPelatihans => {
                all.push(...yearPelatihans);
            });
            return all;
        },

        toggleView() {
            this.viewMode = this.viewMode === 'list' ? 'grid' : 'list';
        },

        filterByYear() {
            // Method is handled by computed property
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
            event.target.closest('.upload-area').classList.add('drag-over');
        },

        removeDragClass(event) {
            event.target.closest('.upload-area').classList.remove('drag-over');
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

                formData.append('sertifikat', file);

                const response = await fetch(route('pelatihan.update', pel.id), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                });

                if (response.ok) {
                    const result = await response.json();
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
            notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white ${alertClass === 'alert-success' ? 'bg-green-600' : alertClass === 'alert-error' ? 'bg-red-600' : 'bg-blue-600'}`;
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
