<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMPEG Auto SPA')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>

<body class="min-h-screen p-2 sm:p-4 md:p-6 lg:p-8">
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
            <div class="flex gap-1 sm:gap-2 justify-start sm:justify-center min-w-max sm:min-w-0 px-2 sm:px-0">
                <a href="{{ route('dashboard') }}"
                    class="tab-button {{ request()->routeIs('dashboard') ? 'active' : '' }} px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap flex-shrink-0">
                    <i class="fas fa-chart-bar mr-1 sm:mr-2"></i>
                    <span class="hidden xs:inline">Dashboard</span>
                    <span class="xs:hidden">Dash</span>
                </a>
                <a href="{{ route('progress') }}"
                    class="tab-button {{ request()->routeIs('progress') ? 'active' : '' }} px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap flex-shrink-0">
                    <i class="fas fa-chart-line mr-1 sm:mr-2"></i>
                    <span class="hidden xs:inline">Progress JP</span>
                    <span class="xs:hidden">Progress</span>
                </a>
                <a href="{{ route('pelatihan.comparison') }}"
                    class="tab-button {{ request()->routeIs('pelatihan.comparison') ? 'active' : '' }} px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap flex-shrink-0">
                    <i class="fas fa-balance-scale mr-1 sm:mr-2"></i>
                    <span class="hidden sm:inline">Perbandingan</span>
                    <span class="sm:hidden">Compare</span>
                </a>
                <a href="{{ route('pelatihan.index') }}"
                    class="tab-button {{ request()->routeIs('pelatihan.*') && !request()->routeIs('pelatihan.comparison') ? 'active' : '' }} px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap flex-shrink-0">
                    <i class="fas fa-database mr-1 sm:mr-2"></i>
                    <span class="hidden sm:inline">Data Pelatihan</span>
                    <span class="sm:hidden">Data</span>
                </a>
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

            // Show success message for form submissions
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success')) {
                showNotification('Berhasil! Data telah disimpan.', 'success');
            }

            // Auto-hide notifications
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);
        });

        // Notification function
        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                warning: 'bg-yellow-500'
            };

            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info-circle'} mr-2"></i>
                    ${message}
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        // Confirm delete
        function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
            return confirm(message);
        }
    </script>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .tab-button {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .tab-button:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-1px);
        }

        .tab-button.active {
            background: white;
            color: #334155;
            border-color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Loading animations */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Smooth scroll */
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
    </style>
    }
    </style>
</body>

</html>