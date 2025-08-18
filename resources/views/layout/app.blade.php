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

<body class="min-h-screen bg-gradient-to-br from-blue-500 via-purple-600 to-purple-800">
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
					<a href="{{ route('pelatihan.index') }}"
						class="tab-button {{ request()->routeIs('pelatihan.*') ? 'active' : '' }} px-4 py-2 rounded-lg font-semibold text-sm">
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
		.tab-button {
			background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
			color: #475569;
			transition: all 0.3s ease;
		}

		.tab-button:hover {
			background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
			color: #334155;
			transform: translateY(-1px);
		}

		.tab-button.active {
			background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
			color: white;
			box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
		}
	</style>
</body>

</html>