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

<body class="min-h-screen p-4 md:p-6 lg:p-8">
    <div class="min-h-screen p-4 md:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white/95 backdrop-blur-sm p-4 md:p-6 rounded-xl shadow-xl mb-4 md:mb-6 text-center">
                <h1 class="text-slate-800 text-2xl md:text-3xl lg:text-4xl font-bold mb-2">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>Dashboard Diklat ASN
                </h1>
                <p class="text-slate-600 text-sm md:text-base">
                    Sistem Informasi Manajemen Pegawai - Dashboard Pelatihan dan Pengembangan
                </p>
            </div>

            <!-- Navigation Tabs -->
            <div class="bg-white/95 backdrop-blur-sm p-2 rounded-lg shadow-lg mb-6">
                <div class="flex flex-wrap gap-2 justify-center">
                    <a href="{{ route('dashboard') }}"
                        class="tab-button {{ request()->routeIs('dashboard') ? 'active' : '' }} px-4 py-2 rounded-lg font-semibold text-sm">
                        <i class="fas fa-chart-bar mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('progress') }}"
                        class="tab-button {{ request()->routeIs('progress') ? 'active' : '' }} px-4 py-2 rounded-lg font-semibold text-sm">
                        <i class="fas fa-chart-line mr-2"></i>Progress JP
                    </a>
                    <a href="{{ route('pelatihan.comparison') }}"
                        class="tab-button {{ request()->routeIs('pelatihan.comparison') ? 'active' : '' }} px-4 py-2 rounded-lg font-semibold text-sm">
                        <i class="fas fa-balance-scale mr-2"></i>Perbandingan
                    </a>
                    <a href="{{ route('pelatihan.index') }}"
                        class="tab-button {{ request()->routeIs('pelatihan.*') && !request()->routeIs('pelatihan.comparison') ? 'active' : '' }} px-4 py-2 rounded-lg font-semibold text-sm">
                        <i class="fas fa-database mr-2"></i>Data Pelatihan
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-xl min-h-[600px]">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .tab-button {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .tab-button.active {
            background: white;
            color: #334155;
            border-color: white;
        }
    </style>
</body>

</html>