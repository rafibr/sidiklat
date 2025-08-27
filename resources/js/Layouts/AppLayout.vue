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
                <div class="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg mb-3 md:mb-6 relative">
                    <!-- Desktop Navigation -->
                    <nav
                        class="hidden md:flex items-center justify-center space-x-1 bg-white/80 backdrop-blur-sm rounded-lg p-1 shadow-sm border border-slate-200/50 mx-2">
                        <Link v-for="tab in navigation" :key="tab.name" :href="tab.href" :class="[
                            'nav-tab px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 whitespace-nowrap',
                            $page.url === tab.href
                                ? 'nav-tab-active text-white'
                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                        ]">
                        <i :class="tab.icon" class="mr-2"></i>
                        {{ tab.name }}
                        </Link>
                    </nav>

                    <!-- Mobile Navigation -->
                    <div class="md:hidden">
                        <div class="flex items-center justify-between p-3">
                            <!-- Current Page Indicator -->
                            <div class="flex items-center text-sm font-medium text-slate-700">
                                <i class="fas fa-circle text-xs text-blue-500 mr-2"></i>
                                <span id="current-page-mobile" class="truncate">
                                    <template v-for="tab in navigation" :key="tab.name">
                                        <span v-if="$page.url === tab.href">{{ tab.name }}</span>
                                    </template>
                                </span>
                            </div>

                            <!-- Mobile Navigation Toggle -->
                            <button id="mobile-menu-toggle"
                                class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors duration-200"
                                aria-label="Toggle mobile menu">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>

                        <!-- Mobile Navigation Menu -->
                        <div id="mobile-menu"
                            class="mobile-menu hidden bg-white rounded-lg shadow-lg border border-slate-200 mx-2 mb-2">
                            <nav class="py-2">
                                <Link v-for="tab in navigation" :key="tab.name" :href="tab.href" :class="[
                                    'mobile-nav-item block px-4 py-3 text-sm font-medium transition-colors duration-200 border-b border-slate-100 last:border-b-0',
                                    $page.url === tab.href
                                        ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white'
                                        : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900'
                                ]">
                                <i :class="tab.icon" class="mr-3"></i>
                                {{ tab.name }}
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div
                    class="bg-white/95 backdrop-blur-sm rounded-lg md:rounded-xl shadow-xl min-h-[400px] sm:min-h-[500px] md:min-h-[600px] overflow-hidden">
                    <transition name="page" enter-active-class="page-enter-active" enter-from-class="page-enter-from"
                        enter-to-class="page-enter-to" leave-active-class="page-leave-active"
                        leave-from-class="page-leave-from" leave-to-class="page-leave-to" mode="out-in">
                        <div :key="$page.component" class="page-content">
                            <slot />
                        </div>
                    </transition>
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
    data() {
        return {
            navigation: [
                {
                    name: 'Dashboard',
                    href: '/',
                    icon: 'fas fa-tachometer-alt'
                },
                {
                    name: 'Data Pegawai',
                    href: '/pegawai',
                    icon: 'fas fa-users'
                },
                {
                    name: 'Data Pelatihan',
                    href: '/pelatihan',
                    icon: 'fas fa-graduation-cap'
                },
                {
                    name: 'Progress JP',
                    href: '/progress',
                    icon: 'fas fa-chart-line'
                },
                {
                    name: 'Perbandingan',
                    href: '/comparison',
                    icon: 'fas fa-balance-scale'
                }
            ]
        };
    },
    mounted() {
        this.initMobileMenu();
    },
    methods: {
        initMobileMenu() {
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', () => {
                    const isHidden = mobileMenu.classList.contains('hidden');

                    if (isHidden) {
                        mobileMenu.classList.remove('hidden');
                        mobileMenu.classList.add('animate-slide-down');
                        mobileMenuToggle.innerHTML = '<i class="fas fa-times text-slate-600"></i>';
                    } else {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-slide-down');
                        mobileMenuToggle.innerHTML = '<i class="fas fa-bars text-slate-600"></i>';
                    }
                });

                // Close menu when clicking outside
                document.addEventListener('click', (event) => {
                    if (!mobileMenuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-slide-down');
                        mobileMenuToggle.innerHTML = '<i class="fas fa-bars text-slate-600"></i>';
                    }
                });

                // Close menu when clicking on menu items
                const mobileNavItems = document.querySelectorAll('.mobile-nav-item');
                mobileNavItems.forEach(item => {
                    item.addEventListener('click', () => {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-slide-down');
                        mobileMenuToggle.innerHTML = '<i class="fas fa-bars text-slate-600"></i>';
                    });
                });

                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-slide-down');
                        mobileMenuToggle.innerHTML = '<i class="fas fa-bars text-slate-600"></i>';
                    }
                });
            }
        }
    }
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

/* Page Transition Animations */
.page-content {
    width: 100%;
    height: 100%;
}

.page-enter-active {
    transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.page-leave-active {
    transition: all 0.4s cubic-bezier(0.55, 0.085, 0.68, 0.53);
}

.page-enter-from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    filter: blur(5px);
}

.page-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0);
}

.page-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0);
}

.page-leave-to {
    opacity: 0;
    transform: translateY(-20px) scale(1.05);
    filter: blur(3px);
}

/* Staggered Animation for Cards */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-40px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(40px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.9);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-slide-up {
    animation: slideInUp 0.6s ease-out forwards;
}

.animate-slide-left {
    animation: slideInLeft 0.6s ease-out forwards;
}

.animate-slide-right {
    animation: slideInRight 0.6s ease-out forwards;
}

.animate-fade-scale {
    animation: fadeInScale 0.6s ease-out forwards;
}

/* Delay classes for staggered animations */
.delay-100 {
    animation-delay: 0.1s;
    opacity: 0;
}

.delay-200 {
    animation-delay: 0.2s;
    opacity: 0;
}

.delay-300 {
    animation-delay: 0.3s;
    opacity: 0;
}

.delay-400 {
    animation-delay: 0.4s;
    opacity: 0;
}

.delay-500 {
    animation-delay: 0.5s;
    opacity: 0;
}

.delay-600 {
    animation-delay: 0.6s;
    opacity: 0;
}

.delay-700 {
    animation-delay: 0.7s;
    opacity: 0;
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

<style scoped>
/* Custom animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-down {
    animation: slideDown 0.3s ease-out;
}

/* Mobile menu styling */
.mobile-menu {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(148, 163, 184, 0.1);
}

/* Navigation tabs styling */
.nav-tabs {
    display: flex;
    overflow-x: hidden;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.nav-tabs::-webkit-scrollbar {
    display: none;
}

/* Active tab styling */
.nav-tab-active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(102, 126, 234, 0.1);
}

/* Hover effects */
.nav-tab:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .nav-tabs {
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .nav-tab {
        flex: 1;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

/* Desktop navigation */
@media (min-width: 769px) {
    .nav-tabs {
        justify-content: center;
    }
}
</style>
