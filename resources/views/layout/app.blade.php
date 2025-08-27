<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="theme-color" content="#3B82F6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SIDIKLAT">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-TileColor" content="#3B82F6">
    <meta name="msapplication-config" content="/browserconfig.xml">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/favicon.ico">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <title>@yield('title', 'SIDIKLAT - Sistem Informasi Data Pelatihan')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/pwa.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>

<body class="min-h-screen p-2 sm:p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div
            class="bg-white/95 backdrop-blur-sm p-3 sm:p-4 md:p-6 rounded-lg md:rounded-xl shadow-xl mb-3 md:mb-6 text-center relative overflow-hidden">
            <!-- Network Status Indicator -->
            <div class="network-status" id="network-status"></div>

            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute inset-0"
                    style="background-image: radial-gradient(circle at 25% 25%, rgb(59, 130, 246) 2px, transparent 2px); background-size: 20px 20px;">
                </div>
            </div>

            <div class="relative z-10">
                <h1
                    class="text-slate-800 text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold mb-2 flex items-center justify-center flex-wrap">
                    <i class="fas fa-chart-bar text-blue-600 mr-2 sm:mr-3 animate-pulse"></i>
                    <span class="hidden sm:inline lg:inline xl:inline">Dashboard Diklat ASN</span>
                    <span class="sm:hidden">Diklat ASN</span>
                </h1>

                <p class="text-slate-600 text-xs sm:text-sm md:text-base hidden sm:block leading-relaxed">
                    Sistem Informasi Manajemen Pegawai - Dashboard Pelatihan dan Pengembangan
                </p>
                <p class="text-slate-600 text-xs sm:hidden leading-relaxed">
                    SIMPEG - Dashboard Pelatihan
                </p>

                <!-- Quick Stats (Mobile Only) -->
                <div class="sm:hidden mt-3 pt-3 border-t border-slate-200 mobile-quick-stats">
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-blue-50 rounded-lg p-2 mobile-stat-card">
                            <div class="mobile-stat-label text-slate-600">Total</div>
                            <div class="mobile-stat-number text-blue-600" id="mobile-total-pegawai">--</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-2 mobile-stat-card">
                            <div class="mobile-stat-label text-slate-600">Aktif</div>
                            <div class="mobile-stat-number text-green-600" id="mobile-active-training">--</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-2 mobile-stat-card">
                            <div class="mobile-stat-label text-slate-600">Selesai</div>
                            <div class="mobile-stat-number text-purple-600" id="mobile-completed-training">--</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white/95 backdrop-blur-sm rounded-lg shadow-lg mb-3 md:mb-6 relative navigation-wrapper">
            <!-- Desktop Navigation -->
            <div class="hidden md:block navigation-bounds">
                <div class="p-2 navigation-container">
                    <div class="desktop-nav">
                        <a href="{{ route('dashboard') }}"
                            class="desktop-nav-item tab-button {{ request()->routeIs('dashboard') ? 'active' : '' }} px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap transition-all duration-200 hover:scale-105">
                            <i class="fas fa-chart-bar mr-1 sm:mr-2"></i>
                            <span class="hidden lg:inline">Dashboard</span>
                            <span class="lg:hidden">Dash</span>
                        </a>
                        <a href="{{ route('progress') }}"
                            class="desktop-nav-item tab-button {{ request()->routeIs('progress') ? 'active' : '' }} px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap transition-all duration-200 hover:scale-105">
                            <i class="fas fa-chart-line mr-1 sm:mr-2"></i>
                            <span class="hidden lg:inline">Progress JP</span>
                            <span class="lg:hidden">Progress</span>
                        </a>
                        <a href="{{ route('pelatihan.comparison') }}"
                            class="desktop-nav-item tab-button {{ request()->routeIs('pelatihan.comparison') ? 'active' : '' }} px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap transition-all duration-200 hover:scale-105">
                            <i class="fas fa-balance-scale mr-1 sm:mr-2"></i>
                            <span class="hidden xl:inline">Perbandingan</span>
                            <span class="xl:hidden">Compare</span>
                        </a>
                        <a href="{{ route('pelatihan.index') }}"
                            class="desktop-nav-item tab-button {{ request()->routeIs('pelatihan.*') && !request()->routeIs('pelatihan.comparison') ? 'active' : '' }} px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap transition-all duration-200 hover:scale-105">
                            <i class="fas fa-database mr-1 sm:mr-2"></i>
                            <span class="hidden lg:inline">Data Pelatihan</span>
                            <span class="lg:hidden">Data</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div class="md:hidden">
                <div class="flex items-center justify-between p-2">
                    <!-- Current Page Indicator -->
                    <div class="flex items-center text-sm font-medium text-slate-700">
                        <i class="fas fa-circle text-xs text-blue-500 mr-2"></i>
                        <span id="current-page-mobile" class="truncate">
                            @if(request()->routeIs('dashboard')) Dashboard
                            @elseif(request()->routeIs('progress')) Progress JP
                            @elseif(request()->routeIs('pelatihan.comparison')) Perbandingan
                            @elseif(request()->routeIs('pelatihan.*')) Data Pelatihan
                            @else Dashboard
                            @endif
                        </span>
                    </div>

                    <!-- Hamburger Menu Button -->
                    <button id="mobile-menu-toggle"
                        class="p-2 rounded-lg hover:bg-slate-100 transition-colors duration-200">
                        <i class="fas fa-bars text-slate-600"></i>
                    </button>
                </div>

                <!-- Mobile Menu Dropdown -->
                <div id="mobile-menu" class="hidden bg-white border-t border-slate-200">
                    <div class="py-2">
                        <a href="{{ route('dashboard') }}"
                            class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-chart-bar mr-3 w-5 text-center"></i>
                            <span>Dashboard</span>
                            @if(request()->routeIs('dashboard'))
                            <i class="fas fa-check ml-auto text-blue-500"></i>
                            @endif
                        </a>
                        <a href="{{ route('progress') }}"
                            class="mobile-nav-item {{ request()->routeIs('progress') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-chart-line mr-3 w-5 text-center"></i>
                            <span>Progress JP</span>
                            @if(request()->routeIs('progress'))
                            <i class="fas fa-check ml-auto text-blue-500"></i>
                            @endif
                        </a>
                        <a href="{{ route('pelatihan.comparison') }}"
                            class="mobile-nav-item {{ request()->routeIs('pelatihan.comparison') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-balance-scale mr-3 w-5 text-center"></i>
                            <span>Perbandingan</span>
                            @if(request()->routeIs('pelatihan.comparison'))
                            <i class="fas fa-check ml-auto text-blue-500"></i>
                            @endif
                        </a>
                        <a href="{{ route('pelatihan.index') }}"
                            class="mobile-nav-item {{ request()->routeIs('pelatihan.*') && !request()->routeIs('pelatihan.comparison') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-database mr-3 w-5 text-center"></i>
                            <span>Data Pelatihan</span>
                            @if(request()->routeIs('pelatihan.*') && !request()->routeIs('pelatihan.comparison'))
                            <i class="fas fa-check ml-auto text-blue-500"></i>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div
            class="bg-white/95 backdrop-blur-sm rounded-lg md:rounded-xl shadow-xl min-h-[400px] sm:min-h-[500px] md:min-h-[600px]">
            @yield('content')
        </div>
    </div>
    </div>

    @stack('scripts')

    <!-- PWA Offline Indicator -->
    <div id="connection-status" class="offline-indicator" style="display: none;">
        <i class="fas fa-wifi-slash mr-2"></i>
        <span id="connection-message">Anda sedang offline</span>
    </div>

    <!-- Connection Quality Indicator -->
    <div class="connection-quality" id="connection-quality" style="display: none;">
        <i class="fas fa-wifi"></i>
    </div>

    <script>
        // Add loading state for forms
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
                    }
                });
            });

            // Add hover effects to cards
            const cards = document.querySelectorAll('.bg-white, .bg-gradient-to-r');
            cards.forEach(card => {
                if (!card.classList.contains('no-hover')) {
                    card.classList.add('card-hover');
                }
            });

            // Add button hover effects
            const buttons = document.querySelectorAll('button, .btn, a[href]');
            buttons.forEach(btn => {
                if (!btn.classList.contains('no-hover')) {
                    btn.classList.add('btn-hover');
                }
            });

            // Handle online/offline status
            const connectionStatus = document.getElementById('connection-status');
            const connectionMessage = document.getElementById('connection-message');
            const networkStatus = document.getElementById('network-status');
            const connectionQuality = document.getElementById('connection-quality');

            function updateConnectionStatus() {
                if (navigator.onLine) {
                    connectionStatus.style.display = 'none';
                    connectionStatus.classList.remove('offline-indicator');
                    connectionStatus.classList.add('online-indicator');
                    connectionMessage.textContent = 'Koneksi tersambung';
                    if (networkStatus) {
                        networkStatus.classList.remove('offline');
                    }
                    setTimeout(() => {
                        connectionStatus.style.display = 'none';
                    }, 3000);
                } else {
                    connectionStatus.style.display = 'block';
                    connectionStatus.classList.remove('online-indicator');
                    connectionStatus.classList.add('offline-indicator');
                    connectionMessage.innerHTML = `
                        <strong>Tidak Ada Koneksi Internet</strong><br>
                        <small>Anda dapat melanjutkan menggunakan data yang telah disimpan di cache</small>
                    `;
                    if (networkStatus) {
                        networkStatus.classList.add('offline');
                    }
                }
            }

            window.addEventListener('online', updateConnectionStatus);
            window.addEventListener('offline', updateConnectionStatus);

            // Initial check
            updateConnectionStatus();

            // Add touch device class
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
            }

            // Add PWA install prompt handling
            let deferredPrompt;
            const installButton = document.getElementById('pwa-install-btn');

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;

                if (installButton) {
                    installButton.style.display = 'block';
                }
            });

            if (installButton) {
                installButton.addEventListener('click', () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('User accepted the install prompt');
                            }
                            deferredPrompt = null;
                            installButton.style.display = 'none';
                        });
                    }
                });
            }

            // Hide install button if already installed
            if (window.matchMedia('(display-mode: standalone)').matches) {
                if (installButton) {
                    installButton.style.display = 'none';
                }
            }

			// Add mobile menu toggle functionality
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', function() {
                    const isHidden = mobileMenu.classList.contains('hidden');

                    if (isHidden) {
                        mobileMenu.classList.remove('hidden');
                        mobileMenu.classList.add('animate-slide-down');
                        this.innerHTML = '<i class="fas fa-times text-slate-600"></i>';
                    } else {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-slide-down');
                        this.innerHTML = '<i class="fas fa-bars text-slate-600"></i>';
                    }
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-slide-down');
                        mobileMenuToggle.innerHTML = '<i class="fas fa-bars text-slate-600"></i>';
                    }
                });

                // Close menu when clicking on menu items
                const mobileNavItems = document.querySelectorAll('.mobile-nav-item');
                mobileNavItems.forEach(item => {
                    item.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-slide-down');
                        mobileMenuToggle.innerHTML = '<i class="fas fa-bars text-slate-600"></i>';
                    });
                });
            }

            // Load mobile quick stats
            function loadMobileStats() {
                const mobileTotalPegawai = document.getElementById('mobile-total-pegawai');
                const mobileActiveTraining = document.getElementById('mobile-active-training');
                const mobileCompletedTraining = document.getElementById('mobile-completed-training');

                if (mobileTotalPegawai && mobileActiveTraining && mobileCompletedTraining) {
                    // Add loading animation
                    [mobileTotalPegawai, mobileActiveTraining, mobileCompletedTraining].forEach(el => {
                        el.parentElement.classList.add('mobile-stat-loading');
                    });

                    // Try to get data from localStorage first (for offline support)
                    const cachedStats = localStorage.getItem('sidiklat-mobile-stats');
                    if (cachedStats) {
                        const stats = JSON.parse(cachedStats);
                        mobileTotalPegawai.textContent = stats.totalPegawai || '--';
                        mobileActiveTraining.textContent = stats.activeTraining || '--';
                        mobileCompletedTraining.textContent = stats.completedTraining || '--';

                        // Remove loading animation
                        [mobileTotalPegawai, mobileActiveTraining, mobileCompletedTraining].forEach(el => {
                            el.parentElement.classList.remove('mobile-stat-loading');
                        });
                    }

                    // Fetch fresh data if online
                    if (navigator.onLine) {
                        fetch('/api/mobile-stats', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            mobileTotalPegawai.textContent = data.total_pegawai || '--';
                            mobileActiveTraining.textContent = data.active_training || '--';
                            mobileCompletedTraining.textContent = data.completed_training || '--';

                            // Remove loading animation
                            [mobileTotalPegawai, mobileActiveTraining, mobileCompletedTraining].forEach(el => {
                                el.parentElement.classList.remove('mobile-stat-loading');
                            });

                            // Cache the data
                            localStorage.setItem('sidiklat-mobile-stats', JSON.stringify({
                                totalPegawai: data.total_pegawai,
                                activeTraining: data.active_training,
                                completedTraining: data.completed_training,
                                timestamp: Date.now()
                            }));
                        })
                        .catch(error => {
                            console.log('Could not fetch mobile stats:', error);
                            // Remove loading animation on error
                            [mobileTotalPegawai, mobileActiveTraining, mobileCompletedTraining].forEach(el => {
                                el.parentElement.classList.remove('mobile-stat-loading');
                            });
                        });
                    } else {
                        // Remove loading animation if offline
                        setTimeout(() => {
                            [mobileTotalPegawai, mobileActiveTraining, mobileCompletedTraining].forEach(el => {
                                el.parentElement.classList.remove('mobile-stat-loading');
                            });
                        }, 1000);
                    }
                }
            }

            // Load mobile stats on page load
            loadMobileStats();

            // Add smooth navigation transitions
            const navLinks = document.querySelectorAll('.tab-button, .mobile-nav-item');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Add loading state
                    this.classList.add('nav-loading');

                    // Remove loading state after navigation
                    setTimeout(() => {
                        this.classList.remove('nav-loading');
                    }, 1000);
                });
            });

            // Add keyboard navigation support
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('animate-slide-down');
                    if (mobileMenuToggle) {
                        mobileMenuToggle.innerHTML = '<i class="fas fa-bars text-slate-600"></i>';
                    }
                }
            });

            // Add swipe gesture support for mobile menu
            if ('ontouchstart' in window) {
                let touchStartX = 0;
                let touchStartY = 0;

                document.addEventListener('touchstart', function(e) {
                    touchStartX = e.touches[0].clientX;
                    touchStartY = e.touches[0].clientY;
                });

                document.addEventListener('touchend', function(e) {
                    if (!mobileMenu || mobileMenu.classList.contains('hidden')) return;

                    const touchEndX = e.changedTouches[0].clientX;
                    const touchEndY = e.changedTouches[0].clientY;
                    const diffX = touchStartX - touchEndX;
                    const diffY = touchStartY - touchEndY;

                    // Swipe down to close menu
                    if (Math.abs(diffY) > Math.abs(diffX) && diffY < -50) {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-slide-down');
                        if (mobileMenuToggle) {
                            mobileMenuToggle.innerHTML = '<i class="fas fa-bars text-slate-600"></i>';
                        }
                    }
                });
            }
		});
    </script>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .tab-button {
            background: rgba(255, 255, 255, 0.75);
            /* light but opaque so text remains readable */
            color: #334155;
            /* dark text for contrast on light header */
            border: 1px solid rgba(15, 23, 42, 0.06);
            transition: all 0.18s ease;
            backdrop-filter: blur(6px);
        }

        /* Enhanced pill-style tabs */
        .tab-button {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.92));
            color: #0f172a;
            border: 1px solid rgba(15, 23, 42, 0.06);
            padding: 0.45rem 0.9rem;
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

        /* Active indicator bar under the active tab */
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

        html {
            scroll-behavior: smooth;
        }

        /* Form focus states */
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            ring-width: 2px;
            ring-color: #3B82F6;
            border-color: #3B82F6;
        }

        /* Button hover effects */
        .btn-hover {
            transition: all 0.2s ease;
        }

        .btn-hover:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Progress bar animation */
        .progress-bar {
            transition: width 0.8s ease;
        }

        /* Mobile optimizations */
        @media (max-width: 640px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .chart-container {
                height: 200px;
            }
        }

        /* Network status indicator styles */
        .network-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 2rem;
            height: 2rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .network-status.online {
            background: #3B82F6;
            transform: scale(1.1);
        }

        .network-status.offline {
            background: #ef4444;
            transform: scale(1);
        }

        /* Connection quality indicator styles */
        .connection-quality {
            position: absolute;
            top: 1rem;
            right: 4rem;
            width: 2rem;
            height: 2rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .connection-quality i {
            color: #3B82F6;
            font-size: 1.2rem;
        }

        /* Show/hide based on connection quality */
        .connection-quality.good {
            display: flex;
        }

        .connection-quality.fair {
            display: flex;
            animation: pulse 2s infinite;
        }

        .connection-quality.poor {
            display: none;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }
    </style>

    <style>
        /* Custom scrollbar */
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

        /* Clearer borders across panels, cards and gradients */
        .bg-white,
        .bg-white\/95,
        .bg-gradient-to-r,
        .card-hover {
            border: 1px solid rgba(15, 23, 42, 0.06);
        }

        /* Slightly stronger border for main containers */
        .max-w-7xl>.bg-white\/95,
        .max-w-7xl>.bg-white {
            border-color: rgba(15, 23, 42, 0.08);
        }

        /* Compact border & card utilities for tighter look */
        .border-compact {
            border-width: 1px !important;
            border-style: solid !important;
            border-color: rgba(15, 23, 42, 0.09) !important;
        }

        .card-compact {
            padding: 0.5rem 0.75rem !important;
            /* tighter padding */
            border-radius: 0.5rem !important;
        }
    </style>
</body>

</html>