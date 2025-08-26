<template>
    <v-app>
        <div class="min-h-screen p-2 sm:p-4 md:p-6 lg:p-8"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div
                    class="bg-white/95 backdrop-blur-sm p-3 sm:p-4 md:p-6 rounded-lg md:rounded-xl shadow-xl mb-3 md:mb-6 text-center">
                    <h1 class="text-slate-800 text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold mb-2">
                        <i class="fas fa-chart-bar text-blue-600 mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Dashboard Diklat ASN</span>
                        <span class="sm:hidden">Diklat ASN</span>
                    </h1>
                    <p class="text-slate-600 text-xs sm:text-sm md:text-base hidden sm:block">
                        Sistem Informasi Manajemen Pegawai - Dashboard Pelatihan dan Pengembangan
                    </p>
                    <p class="text-slate-600 text-xs sm:hidden">
                        SIMPEG - Dashboard Pelatihan
                    </p>
                </div>

                <!-- Navigation Tabs -->
                <div class="bg-white/95 backdrop-blur-sm p-1 sm:p-2 rounded-lg shadow-lg mb-3 md:mb-6 overflow-x-auto">
                    <div class="flex gap-1 sm:gap-2 justify-center px-2 sm:px-0">
                        <Link :href="route('dashboard')"
                            class="tab-button px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap flex-shrink-0"
                            :class="{ 'active': $page.component === 'Dashboard/Index' }">
                        <i class="fas fa-chart-bar mr-1 sm:mr-2"></i>
                        <span class="hidden xs:inline">Dashboard</span>
                        <span class="xs:hidden">Dash</span>
                        </Link>
                        <Link :href="route('progress')"
                            class="tab-button px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap flex-shrink-0"
                            :class="{ 'active': $page.component === 'Progress/Index' }">
                        <i class="fas fa-chart-line mr-1 sm:mr-2"></i>
                        <span class="hidden xs:inline">Progress JP</span>
                        <span class="xs:hidden">Progress</span>
                        </Link>

                        <Link :href="route('pelatihan.comparison')"
                            class="tab-button px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap flex-shrink-0"
                            :class="{ 'active': $page.component === 'Pelatihan/Comparison' }">
                        <i class="fas fa-balance-scale mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Perbandingan</span>
                        <span class="sm:hidden">Compare</span>
                        </Link>
                        <Link :href="route('pelatihan.index')"
                            class="tab-button px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap flex-shrink-0"
                            :class="{ 'active': $page.component.startsWith('Pelatihan/') && $page.component !== 'Pelatihan/Comparison' }">
                        <i class="fas fa-database mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Data Pelatihan</span>
                        <span class="sm:hidden">Data</span>
                        </Link>
                        <Link :is="Link" :href="route('pegawai.index')"
                            class="tab-button px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap flex-shrink-0"
                            :class="{ 'active': $page.component.startsWith('Pegawai/') }">
                        <i class="fas fa-users mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">List Pegawai</span>
                        <span class="sm:hidden">Pegawai</span>
                        </Link>
                    </div>
                </div>

                <!-- Content -->
                <div
                    class="bg-white/95 backdrop-blur-sm rounded-lg md:rounded-xl shadow-xl min-h-[400px] sm:min-h-[500px] md:min-h-[600px]">
                    <slot />
                </div>
            </div>
        </div>
    </v-app>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },
};
</script>

<style>
/* Tab button styles */
.tab-button {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.92));
    color: #0f172a;
    border: 1px solid rgba(15, 23, 42, 0.06);
    transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
    backdrop-filter: blur(6px);
    border-radius: 0.75rem;
    font-weight: 600;
}

.tab-button i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.6rem;
    height: 1.6rem;
    border-radius: 9999px;
    background: rgba(15, 23, 42, 0.04);
    color: #334155;
    transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
    font-size: 0.9rem;
}

.tab-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}

.tab-button.active {
    background: linear-gradient(90deg, #ffffff 0%, #f8fafc 100%);
    color: #06263b;
    border-color: rgba(15, 23, 42, 0.09);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
}

.tab-button.active i {
    background: linear-gradient(90deg, #3B82F6, #8B5CF6);
    color: #ffffff;
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.18);
}

.tab-button::after {
    content: '';
    position: absolute;
    left: 14%;
    right: 14%;
    bottom: -9px;
    height: 4px;
    background: transparent;
    border-radius: 9999px;
    transition: all 0.18s ease;
    opacity: 0;
}

.tab-button.active::after {
    background: linear-gradient(90deg, #3B82F6, #8B5CF6);
    opacity: 1;
    bottom: -10px;
    height: 5px;
}

/* Global styles */
html {
    scroll-behavior: smooth;
}

input:focus,
select:focus,
textarea:focus {
    outline: none;
    box-shadow: 0 0 0 2px #3B82F6;
    border-color: #3B82F6;
}

.btn-hover {
    transition: all 0.2s ease;
}

.btn-hover:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.progress-bar {
    transition: width 0.8s ease;
}

::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
}

::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

.bg-white,
.bg-white\/95,
.bg-gradient-to-r,
.card-hover {
    border: 1px solid rgba(15, 23, 42, 0.06);
}

.border-compact {
    border-width: 1px !important;
    border-style: solid !important;
    border-color: rgba(15, 23, 42, 0.09) !important;
}

.card-compact {
    padding: 0.5rem 0.75rem !important;
    border-radius: 0.5rem !important;
}

@media (max-width: 640px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .chart-container {
        height: 200px;
    }
}
</style>
