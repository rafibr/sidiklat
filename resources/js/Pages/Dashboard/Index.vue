<template>
    <AppLayout>
        <div class="relative p-4 md:p-6 lg:p-8 bg-gray-50 min-h-screen">
            <!-- Header Section -->
            <div class="mb-8" :class="{ 'animate-slide-up': animationsReady, 'opacity-0': !animationsReady }">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard Analytics</h1>
                    <p class="text-gray-600">Monitoring dan evaluasi pemenuhan target JP pegawai</p>
                </div>

                <!-- Loading overlay -->
                <div v-if="loading"
                    class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center">
                    <div class="bg-white p-6 rounded-xl shadow-xl">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin">
                            </div>
                            <span class="text-gray-700 font-medium">Memuat data...</span>
                        </div>
                    </div>
                </div>

                <!-- Year Filter -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <i class="fas fa-calendar-alt text-blue-600"></i>
                            </div>
                            <label class="text-sm font-semibold text-gray-700">Filter Tahun:</label>
                        </div>
                        <div class="relative">
                            <select :value="selectedYear" @change="changeYear($event)"
                                class="px-4 py-2.5 pr-10 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white shadow-sm hover:border-gray-300 transition-colors">
                                <option v-for="year in availableYears" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                            <i
                                class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-sm text-gray-600 font-medium">Data aktif untuk tahun {{ selectedYear
                                }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
                :class="{ 'animate-slide-up delay-100': animationsReady, 'opacity-0': !animationsReady }">
                <!-- Total Pegawai Card -->
                <div
                    class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-white/10 rounded-xl backdrop-blur-sm group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="w-2 h-2 bg-blue-200 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">Total Pegawai</p>
                        <p class="text-3xl font-bold mb-1">{{ stats.total_pegawai }}</p>
                        <p class="text-blue-200 text-xs">Pegawai aktif</p>
                    </div>
                    <div
                        class="absolute inset-0 bg-white/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>
                </div>

                <!-- Total Pelatihan Card -->
                <div
                    class="group relative bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-white/10 rounded-xl backdrop-blur-sm group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="w-2 h-2 bg-green-200 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">Total Pelatihan</p>
                        <p class="text-3xl font-bold mb-1">{{ stats.total_pelatihan }}</p>
                        <p class="text-green-200 text-xs">Program tersedia</p>
                    </div>
                    <div
                        class="absolute inset-0 bg-white/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>
                </div>

                <!-- Total JP Card -->
                <div
                    class="group relative bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-white/10 rounded-xl backdrop-blur-sm group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="w-2 h-2 bg-purple-200 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-purple-100 text-sm font-medium mb-1">Total JP</p>
                        <p class="text-3xl font-bold mb-1">{{ formatNumber(stats.total_jp) }}</p>
                        <p class="text-purple-200 text-xs">Jam pelajaran</p>
                    </div>
                    <div
                        class="absolute inset-0 bg-white/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>
                </div>

                <!-- Rata-rata Progress Card -->
                <div
                    class="group relative bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-white/10 rounded-xl backdrop-blur-sm group-hover:bg-white/20 transition-colors">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="w-2 h-2 bg-orange-200 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-orange-100 text-sm font-medium mb-1">Rata-rata Progress</p>
                        <p class="text-3xl font-bold mb-1">{{ Number(stats.rata_progress || 0).toFixed(1) }}%</p>
                        <p class="text-orange-200 text-xs">Pencapaian target</p>
                    </div>
                    <div
                        class="absolute inset-0 bg-white/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>
                </div>
            </div>

            <!-- JP Fulfillment Dashboard -->
            <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 mb-8 border border-gray-100"
                :class="{ 'animate-fade-scale delay-200': animationsReady, 'opacity-0': !animationsReady }">
                <!-- Header -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                    <div class="mb-4 lg:mb-0">
                        <div class="flex items-center mb-3">
                            <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl mr-4">
                                <i class="fas fa-chart-pie text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl lg:text-3xl font-bold text-gray-900">
                                    Pemenuhan JP {{ selectedYear }}
                                </h3>
                                <p class="text-gray-600 mt-1">Status pencapaian target JP seluruh pegawai</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-100">
                        <div class="text-center">
                            <div
                                class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                                {{ jpFulfillment?.totals?.overall_progress || 0 }}%
                            </div>
                            <div class="text-sm font-semibold text-gray-600">Progress Keseluruhan</div>
                            <div
                                class="mt-2 w-16 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full mx-auto">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Categories -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
                    :class="{ 'animate-slide-up delay-300': animationsReady, 'opacity-0': !animationsReady }">
                    <!-- Completed -->
                    <div
                        class="group relative bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-white/10 rounded-xl backdrop-blur-sm group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">{{ jpFulfillment?.categories?.completed || 0 }}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-green-100 text-sm font-semibold mb-1">Tercapai</div>
                            <div class="text-xs text-green-200">≥100% target</div>
                        </div>
                        <div class="pt-3 border-t border-green-400/30">
                            <div class="text-xs font-medium">
                                {{ jpFulfillment?.percentages?.completed || 0 }}% dari total pegawai
                            </div>
                        </div>
                        <div
                            class="absolute inset-0 bg-white/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                    </div>

                    <!-- On Track -->
                    <div
                        class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-white/10 rounded-xl backdrop-blur-sm group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-arrow-up text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">{{ jpFulfillment?.categories?.on_track || 0 }}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-blue-100 text-sm font-semibold mb-1">On Track</div>
                            <div class="text-xs text-blue-200">75-99% target</div>
                        </div>
                        <div class="pt-3 border-t border-blue-400/30">
                            <div class="text-xs font-medium">
                                {{ jpFulfillment?.percentages?.on_track || 0 }}% dari total pegawai
                            </div>
                        </div>
                        <div
                            class="absolute inset-0 bg-white/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                    </div>

                    <!-- Behind -->
                    <div
                        class="group relative bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-white/10 rounded-xl backdrop-blur-sm group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">{{ jpFulfillment?.categories?.behind || 0 }}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-yellow-100 text-sm font-semibold mb-1">Tertinggal</div>
                            <div class="text-xs text-yellow-200">50-74% target</div>
                        </div>
                        <div class="pt-3 border-t border-yellow-400/30">
                            <div class="text-xs font-medium">
                                {{ jpFulfillment?.percentages?.behind || 0 }}% dari total pegawai
                            </div>
                        </div>
                        <div
                            class="absolute inset-0 bg-white/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                    </div>

                    <!-- Critical -->
                    <div
                        class="group relative bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-white/10 rounded-xl backdrop-blur-sm group-hover:bg-white/20 transition-colors">
                                <i class="fas fa-exclamation-circle text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">{{ jpFulfillment?.categories?.critical || 0 }}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-red-100 text-sm font-semibold mb-1">Kritis</div>
                            <div class="text-xs text-red-200">&lt;50% target</div>
                        </div>
                        <div class="pt-3 border-t border-red-400/30">
                            <div class="text-xs font-medium">
                                {{ jpFulfillment?.percentages?.critical || 0 }}% dari total pegawai
                            </div>
                        </div>
                        <div
                            class="absolute inset-0 bg-white/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                    </div>
                </div>

                <!-- Progress Bar Overview - Glass Animation -->
                <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 mb-8 border border-gray-100"
                    :class="{ 'animate-slide-left delay-400': animationsReady, 'opacity-0': !animationsReady }">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <!-- Info Section -->
                        <div class="flex-1">
                            <div class="flex items-center mb-4">
                                <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl mr-4">
                                    <i class="fas fa-flask text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-xl lg:text-2xl font-bold text-gray-900">Total JP Progress</h4>
                                    <p class="text-gray-600">Tingkat pencapaian target keseluruhan</p>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4">
                                <div class="flex justify-between items-center text-sm font-semibold text-gray-700 mb-2">
                                    <span>JP Tercapai:</span>
                                    <span>{{ formatNumber(jpFulfillment?.totals?.jp_achieved || 0) }} JP</span>
                                </div>
                                <div class="flex justify-between items-center text-sm font-semibold text-gray-700 mb-2">
                                    <span>JP Target:</span>
                                    <span>{{ formatNumber(jpFulfillment?.totals?.jp_target || 0) }} JP</span>
                                </div>
                                <div class="flex justify-between items-center text-lg font-bold text-indigo-600">
                                    <span>Progress:</span>
                                    <span>{{ jpFulfillment?.totals?.overall_progress || 0 }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Glass Animation -->
                        <div class="flex justify-center lg:justify-end">
                            <div class="relative">
                                <!-- Glass Container -->
                                <div class="glass-container relative w-24 h-64 lg:w-28 lg:h-80">
                                    <!-- Glass Background -->
                                    <div
                                        class="absolute inset-0 bg-gradient-to-b from-blue-50/30 to-blue-100/40 rounded-t-lg border-4 border-gray-300 border-b-gray-400 backdrop-blur-sm shadow-2xl">
                                        <!-- Glass Shine Effect -->
                                        <div class="absolute top-2 left-2 w-4 h-16 bg-white/20 rounded-full blur-sm">
                                        </div>
                                        <div class="absolute top-6 right-2 w-2 h-8 bg-white/30 rounded-full blur-sm">
                                        </div>
                                    </div>

                                    <!-- Liquid (JP Progress) -->
                                    <div class="absolute bottom-0 left-0 right-0 transition-all duration-2000 ease-out rounded-b-lg overflow-hidden"
                                        :style="{ height: Math.min(100, jpFulfillment?.totals?.overall_progress || 0) + '%' }">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-indigo-600 via-blue-500 to-cyan-400 opacity-80">
                                            <!-- Liquid Animation Bubbles -->
                                            <div
                                                class="absolute bottom-2 left-2 w-2 h-2 bg-white/40 rounded-full opacity-60">
                                            </div>
                                            <div
                                                class="absolute bottom-4 right-2 w-1 h-1 bg-white/50 rounded-full opacity-70">
                                            </div>
                                            <div
                                                class="absolute bottom-6 left-3 w-1.5 h-1.5 bg-white/30 rounded-full opacity-50">
                                            </div>

                                            <!-- Liquid Wave Effect -->
                                            <div
                                                class="absolute top-0 left-0 right-0 h-4 bg-gradient-to-r from-cyan-300 to-blue-400 opacity-60">
                                                <div class="wave-animation absolute inset-0"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Glass Rim -->
                                    <div
                                        class="absolute -top-2 -left-1 -right-1 h-4 bg-gradient-to-b from-gray-200 to-gray-300 rounded-full shadow-md border border-gray-400">
                                    </div>
                                </div>

                                <!-- Progress Labels -->
                                <div
                                    class="absolute -right-16 top-0 h-full flex flex-col justify-between text-xs text-gray-500 font-medium">
                                    <span>100%</span>
                                    <span>75%</span>
                                    <span>50%</span>
                                    <span>25%</span>
                                    <span>0%</span>
                                </div>

                                <!-- Current Progress Indicator -->
                                <div class="absolute -left-12 flex items-center"
                                    :style="{ top: (100 - Math.min(100, jpFulfillment?.totals?.overall_progress || 0)) + '%' }">
                                    <div
                                        class="bg-indigo-600 text-white px-2 py-1 rounded-lg text-xs font-bold shadow-lg">
                                        {{ jpFulfillment?.totals?.overall_progress || 0 }}%
                                    </div>
                                    <div
                                        class="w-0 h-0 border-l-4 border-l-indigo-600 border-t-2 border-t-transparent border-b-2 border-b-transparent ml-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Performers & Need Attention -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                    :class="{ 'animate-slide-up delay-500': animationsReady, 'opacity-0': !animationsReady }">
                    <!-- Top Performers -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-6 border border-yellow-200">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl mr-3">
                                <i class="fas fa-trophy text-white text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900">Top Performers</h4>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(employee, index) in jpFulfillment?.top_performers || []" :key="employee.id"
                                class="group bg-white rounded-xl p-4 hover:shadow-md transition-all duration-200 border border-yellow-200/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 text-white rounded-xl flex items-center justify-center text-sm font-bold">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ employee.nama }}</div>
                                            <div class="text-sm text-gray-600">{{ employee.unit_kerja }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold"
                                            :class="getProgressColorClass(employee.progress_percentage)">
                                            {{ employee.progress_percentage }}%
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ employee.jp_achieved }}/{{ employee.jp_target }} JP
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Need Attention -->
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-6 border border-red-200">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-gradient-to-br from-red-500 to-pink-500 rounded-xl mr-3">
                                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900">Perlu Perhatian</h4>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(employee, index) in jpFulfillment?.needs_attention || []" :key="employee.id"
                                class="group bg-white rounded-xl p-4 hover:shadow-md transition-all duration-200 border border-red-200/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-red-400 to-pink-500 text-white rounded-xl flex items-center justify-center">
                                            <i class="fas fa-exclamation text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ employee.nama }}</div>
                                            <div class="text-sm text-gray-600">{{ employee.unit_kerja }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold"
                                            :class="getProgressColorClass(employee.progress_percentage)">
                                            {{ employee.progress_percentage }}%
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ employee.jp_achieved }}/{{ employee.jp_target }} JP
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 gap-6 mb-8"
                :class="{ 'animate-slide-right delay-600': animationsReady, 'opacity-0': !animationsReady }">
                <!-- Pie Chart -->
                <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl mr-4">
                            <i class="fas fa-chart-pie text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900">Distribusi Pelatihan</h3>
                            <p class="text-gray-600">Berdasarkan jenis pelatihan</p>
                        </div>
                    </div>
                    <div class="flex justify-center bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6">
                        <canvas ref="jenisChart" class="max-h-80 lg:max-h-96"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Progress Table -->
            <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100"
                :class="{ 'animate-fade-scale delay-700': animationsReady, 'opacity-0': !animationsReady }">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl mr-4">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-900">Progress Terbaru</h3>
                        <p class="text-gray-600">Perkembangan pencapaian JP pegawai</p>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th
                                        class="px-4 lg:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Nama Pegawai</th>
                                    <th
                                        class="px-4 lg:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden sm:table-cell">
                                        Unit Kerja</th>
                                    <th
                                        class="px-4 lg:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        JP Tercapai</th>
                                    <th
                                        class="px-4 lg:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        JP Target</th>
                                    <th
                                        class="px-4 lg:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Progress</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="pegawai in progressPegawai" :key="pegawai.id"
                                    class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 cursor-pointer transition-all duration-200"
                                    @click="goToPegawai(pegawai.id)" tabindex="0"
                                    @keydown.enter="goToPegawai(pegawai.id)">
                                    <td class="px-4 lg:px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-white text-sm font-bold">{{
                                                    pegawai.nama_lengkap.charAt(0) }}</span>
                                            </div>
                                            <div>
                                                <Link :href="route('pegawai.show', { pegawai: pegawai.id })" @click.stop
                                                    class="font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                                                {{ pegawai.nama_lengkap }}
                                                </Link>
                                                <div class="text-xs mt-1 sm:hidden">
                                                    <span :class="getUnitPillClass(pegawai.unit_kerja)">{{
                                                        pegawai.unit_kerja }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell">
                                        <span :class="getUnitPillClass(pegawai.unit_kerja)">{{ pegawai.unit_kerja
                                            }}</span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 font-semibold text-gray-900">
                                        {{ formatNumber(pegawai.jp_tercapai_filtered || pegawai.jp_tercapai || 0) }} JP
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 font-semibold text-gray-900">
                                        {{ formatNumber(pegawai.jp_target) }} JP
                                    </td>
                                    <td class="px-4 lg:px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-1 bg-gray-200 rounded-full h-3 shadow-inner">
                                                <div class="h-3 rounded-full transition-all duration-500"
                                                    :class="calculateProgress(pegawai) >= 80 ? 'bg-gradient-to-r from-green-400 to-green-600' : calculateProgress(pegawai) >= 50 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-red-400 to-red-600'"
                                                    :style="{ width: Math.min(100, calculateProgress(pegawai) || 0) + '%' }">
                                                </div>
                                            </div>
                                            <div class="bg-gray-100 px-2 py-1 rounded-lg">
                                                <span class="text-xs font-bold text-gray-700">{{
                                                    (calculateProgress(pegawai) || 0).toFixed(1) }}%</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
            animationsReady: false,
        };
    },
    mounted() {
        // ensure DOM refs are ready before creating charts
        this.loading = true;
        this.$nextTick(() => {
            this.initCharts();
            // Trigger animations after a short delay
            setTimeout(() => {
                this.animationsReady = true;
            }, 100);
        });
    },
    beforeUnmount() {
        // clean up Chart instances to avoid Chart.js trying to draw on removed canvas
        if (this.jenisChart) {
            try { this.jenisChart.destroy(); } catch (e) { }
            this.jenisChart = null;
        }
    },
    watch: {
        pelatihanByJenis() {
            // when props change from Inertia, update without animation to avoid flicker
            this.loading = true;
            this.updateCharts(false);
            this.$nextTick(() => { this.loading = false; });
        },
        progressPegawai() {
            this.loading = true;
            this.updateCharts(false);
            this.$nextTick(() => { this.loading = false; });
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
            this.loading = true;
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

            // finished initializing charts
            this.loading = false;
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
                // progressChart removed — nothing to update here
            } catch (e) {
                this.initCharts();
            }
        },

        getProgressColorClass(percentage) {
            if (percentage >= 100) return 'text-green-600';
            if (percentage >= 75) return 'text-blue-600';
            if (percentage >= 50) return 'text-yellow-600';
            return 'text-red-600';
        },
    }
};
</script>

<style scoped>
/* Custom animations and enhancements */
@keyframes shimmer {
    0% {
        background-position: -468px 0;
    }

    100% {
        background-position: 468px 0;
    }
}

.shimmer {
    animation: shimmer 2s infinite linear;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
    background-size: 400% 100%;
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:rotate-12 {
    transform: rotate(12deg);
}

/* Smooth progress bar animation */
.progress-bar {
    transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Gradient text animation */
@keyframes gradient {

    0%,
    100% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}

/* Card lift effect */
.card-lift {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-lift:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Pulse dot animation */
@keyframes pulse-dot {

    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: 0.5;
        transform: scale(1.1);
    }
}

.animate-pulse-dot {
    animation: pulse-dot 2s infinite;
}

/* Glass animation styles */
@keyframes wave {

    0%,
    100% {
        transform: translateX(-100%) skewX(15deg);
    }

    50% {
        transform: translateX(0%) skewX(-15deg);
    }
}

@keyframes glassReflection {

    0%,
    100% {
        opacity: 0.2;
        transform: translateY(-5px);
    }

    50% {
        opacity: 0.4;
        transform: translateY(5px);
    }
}

.wave-animation {
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: wave 3s infinite;
}

.glass-container {
    transform-style: preserve-3d;
}

.glass-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    border-radius: 0.5rem;
    animation: glassReflection 4s infinite;
    pointer-events: none;
}

/* Floating animation for cards */
@keyframes float {

    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-5px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Glass morphism effects */
.glass-morph {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}
</style>
