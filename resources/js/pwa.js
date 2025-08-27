// PWA Registration and Management
class PWAHandler {
	constructor() {
		this.deferredPrompt = null;
		this.isOnline = navigator.onLine;
		this.init();
	}

	init() {
		// Register service worker
		if ('serviceWorker' in navigator) {
			this.registerServiceWorker();
		}

		// Handle install prompt
		this.handleInstallPrompt();

		// Handle online/offline status
		this.handleConnectionStatus();

		// Add touch optimizations
		this.optimizeForTouch();

		// Add PWA install button if not installed
		this.addInstallButton();

		// Prevent horizontal scroll on navigation
		this.preventNavigationScroll();

		// Add viewport constraints
		this.enforceViewportConstraints();
	}

	registerServiceWorker() {
		navigator.serviceWorker.register('/sw.js', {
			scope: '/'
		})
			.then((registration) => {
				console.log('Service Worker registered:', registration);

				// Handle updates
				registration.addEventListener('updatefound', () => {
					const newWorker = registration.installing;
					if (newWorker) {
						newWorker.addEventListener('statechange', () => {
							if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
								this.showUpdateNotification();
							}
						});
					}
				});
			})
			.catch((error) => {
				console.error('Service Worker registration failed:', error);
			});
	}

	handleInstallPrompt() {
		window.addEventListener('beforeinstallprompt', (e) => {
			e.preventDefault();
			this.deferredPrompt = e;

			// Show install button
			this.showInstallPrompt();
		});

		window.addEventListener('appinstalled', () => {
			this.deferredPrompt = null;
			this.hideInstallPrompt();
			this.showInstallSuccess();
		});
	}

	handleConnectionStatus() {
		window.addEventListener('online', () => {
			this.isOnline = true;
			this.showOnlineStatus();
		});

		window.addEventListener('offline', () => {
			this.isOnline = false;
			this.detectConnectionIssue();
		});

		// Monitor connection quality
		this.monitorConnectionQuality();
	}

	monitorConnectionQuality() {
		const connectionQuality = document.getElementById('connection-quality');

		setInterval(async () => {
			if (!navigator.onLine) {
				if (connectionQuality) {
					connectionQuality.style.display = 'flex';
					connectionQuality.classList.add('offline');
					connectionQuality.innerHTML = '<i class="fas fa-wifi-slash"></i>';
				}
				return;
			}

			try {
				const start = Date.now();
				const response = await fetch('/favicon.ico', {
					method: 'HEAD',
					cache: 'no-cache'
				});
				const end = Date.now();
				const responseTime = end - start;

				if (connectionQuality) {
					connectionQuality.style.display = 'flex';

					if (responseTime < 500) {
						connectionQuality.classList.remove('offline', 'slow');
						connectionQuality.innerHTML = '<i class="fas fa-wifi"></i>';
					} else if (responseTime < 2000) {
						connectionQuality.classList.remove('offline');
						connectionQuality.classList.add('slow');
						connectionQuality.innerHTML = '<i class="fas fa-wifi"></i>';
					} else {
						connectionQuality.classList.remove('offline');
						connectionQuality.classList.add('slow');
						connectionQuality.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
					}
				}
			} catch (error) {
				if (connectionQuality) {
					connectionQuality.style.display = 'flex';
					connectionQuality.classList.add('offline');
					connectionQuality.innerHTML = '<i class="fas fa-wifi-slash"></i>';
				}
			}
		}, 10000); // Check every 10 seconds
	}

	async detectConnectionIssue() {
		try {
			// Try to connect to a reliable endpoint
			const response = await fetch('/favicon.ico', {
				method: 'HEAD',
				cache: 'no-cache',
				signal: AbortSignal.timeout(5000)
			});

			if (response.ok) {
				// Connection is actually working
				this.showNotification('Koneksi internet normal', 'success');
				return;
			}
		} catch (error) {
			let issueType = 'unknown';
			let message = 'Koneksi internet terputus - mode offline aktif';

			if (error.name === 'TimeoutError') {
				issueType = 'timeout';
				message = 'Koneksi lambat atau tidak stabil - menggunakan mode offline';
			} else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
				issueType = 'network';
				message = 'Jaringan tidak tersedia - tidak ada koneksi internet';
			} else if (error.message.includes('AbortError')) {
				issueType = 'timeout';
				message = 'Waktu koneksi habis - menggunakan mode offline';
			}

			this.showOfflineStatus(message, issueType);
		}
	}

	showInstallPrompt() {
		const installBtn = document.createElement('button');
		installBtn.id = 'pwa-install-btn';
		installBtn.innerHTML = '<i class="fas fa-download"></i> Install App';
		installBtn.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 hover:bg-blue-700 transition-colors';
		installBtn.onclick = () => this.installPWA();

		document.body.appendChild(installBtn);
	}

	hideInstallPrompt() {
		const installBtn = document.getElementById('pwa-install-btn');
		if (installBtn) {
			installBtn.remove();
		}
	}

	async installPWA() {
		if (!this.deferredPrompt) return;

		this.deferredPrompt.prompt();
		const { outcome } = await this.deferredPrompt.userChoice;

		if (outcome === 'accepted') {
			console.log('User accepted the install prompt');
		}

		this.deferredPrompt = null;
	}

	showInstallSuccess() {
		this.showNotification('SIDIKLAT berhasil diinstall!', 'success');
	}

	showUpdateNotification() {
		const updateBtn = document.createElement('button');
		updateBtn.id = 'pwa-update-btn';
		updateBtn.innerHTML = '<i class="fas fa-sync"></i> Update Available';
		updateBtn.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 hover:bg-green-700 transition-colors';
		updateBtn.onclick = () => window.location.reload();

		document.body.appendChild(updateBtn);

		// Auto-hide after 10 seconds
		setTimeout(() => {
			if (updateBtn.parentNode) {
				updateBtn.remove();
			}
		}, 10000);
	}

	showOnlineStatus() {
		this.showNotification('✅ Koneksi internet tersambung kembali', 'success');
		this.hideOfflineBanner();
	}

	showOfflineStatus(message = 'Koneksi internet terputus - mode offline aktif', issueType = 'network') {
		this.showNotification(message, 'warning', 8000);
		this.showOfflineBanner(issueType);
		this.playOfflineSound();
	}

	showOfflineBanner(issueType = 'network') {
		// Remove existing banner if any
		this.hideOfflineBanner();

		const banner = document.createElement('div');
		banner.id = 'offline-banner';
		banner.className = 'fixed top-0 left-0 right-0 bg-red-600 text-white px-4 py-3 shadow-lg z-50 transform translate-y-0 transition-transform duration-300';

		let icon = 'wifi-slash';
		let title = 'Tidak Ada Koneksi Internet';
		let description = 'Anda dapat melanjutkan menggunakan data yang telah disimpan';
		let bgColor = 'bg-red-600';
		let actionButton = 'Coba Lagi';

		switch (issueType) {
			case 'timeout':
				icon = 'clock';
				title = 'Koneksi Lambat';
				description = 'Waktu koneksi habis - menggunakan data lokal';
				bgColor = 'bg-yellow-600';
				banner.className = banner.className.replace('bg-red-600', 'bg-yellow-600');
				actionButton = 'Refresh';
				break;
			case 'server':
				icon = 'server';
				title = 'Server Tidak Merespons';
				description = 'Server bermasalah - menggunakan mode offline';
				actionButton = 'Coba Server';
				break;
			default:
				// Default network issue
				break;
		}

		banner.innerHTML = `
			<div class="flex items-center justify-between max-w-7xl mx-auto">
				<div class="flex items-center">
					<i class="fas fa-${icon} mr-3 animate-pulse"></i>
					<div>
						<div class="font-semibold">${title}</div>
						<div class="text-sm opacity-90">${description}</div>
					</div>
				</div>
				<div class="flex items-center space-x-2">
					<button onclick="window.location.reload()" class="bg-${bgColor.replace('bg-', '')}-700 hover:bg-${bgColor.replace('bg-', '')}-800 px-3 py-1 rounded text-sm transition-colors">
						<i class="fas fa-redo mr-1"></i> ${actionButton}
					</button>
					<button onclick="showOfflineGuide()" class="bg-${bgColor.replace('bg-', '')}-700 hover:bg-${bgColor.replace('bg-', '')}-800 px-3 py-1 rounded text-sm transition-colors">
						<i class="fas fa-info-circle mr-1"></i> Panduan
					</button>
					<button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-red-200 p-1">
						<i class="fas fa-times"></i>
					</button>
				</div>
			</div>
		`;

		document.body.appendChild(banner);
		document.body.classList.add('has-offline-banner');
	}

	showOfflineGuide() {
		const guide = document.createElement('div');
		guide.id = 'offline-guide';
		guide.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
		guide.innerHTML = `
			<div class="bg-white rounded-lg p-6 max-w-md w-full max-h-96 overflow-y-auto">
				<div class="flex justify-between items-center mb-4">
					<h3 class="text-lg font-bold text-gray-800">Panduan Mode Offline</h3>
					<button onclick="this.closest('#offline-guide').remove()" class="text-gray-500 hover:text-gray-700">
						<i class="fas fa-times"></i>
					</button>
				</div>
				<div class="space-y-3 text-sm text-gray-600">
					<div class="flex items-start">
						<i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
						<span>Dashboard dengan data terakhir tersimpan</span>
					</div>
					<div class="flex items-start">
						<i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
						<span>Progress pegawai yang telah dimuat</span>
					</div>
					<div class="flex items-start">
						<i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
						<span>Daftar pelatihan yang tersimpan</span>
					</div>
					<div class="flex items-start">
						<i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
						<span>Navigasi halaman yang telah dikunjungi</span>
					</div>
					<div class="flex items-start">
						<i class="fas fa-exclamation-triangle text-yellow-500 mr-2 mt-1"></i>
						<span>Upload file memerlukan koneksi</span>
					</div>
					<div class="flex items-start">
						<i class="fas fa-exclamation-triangle text-yellow-500 mr-2 mt-1"></i>
						<span>Data real-time tidak tersedia</span>
					</div>
				</div>
				<div class="mt-4 pt-4 border-t border-gray-200">
					<p class="text-xs text-gray-500">
						Sistem akan otomatis kembali normal ketika koneksi internet tersedia.
					</p>
				</div>
			</div>
		`;

		document.body.appendChild(guide);
	}

	enforceViewportConstraints() {
		// Ensure viewport meta tag is properly set
		const viewport = document.querySelector('meta[name="viewport"]');
		if (viewport) {
			const content = viewport.getAttribute('content');
			if (!content.includes('user-scalable=no')) {
				viewport.setAttribute('content', content + ', user-scalable=no');
			}
		}

		// Prevent zoom on input focus (iOS)
		const inputs = document.querySelectorAll('input, textarea, select');
		inputs.forEach(input => {
			input.addEventListener('focus', () => {
				document.body.style.position = 'fixed';
				document.body.style.top = `-${window.scrollY}px`;
			});
			input.addEventListener('blur', () => {
				const scrollY = document.body.style.top;
				document.body.style.position = '';
				document.body.style.top = '';
				window.scrollTo(0, parseInt(scrollY || '0') * -1);
			});
		});

		// Prevent horizontal scroll on window
		window.addEventListener('scroll', () => {
			if (window.scrollX > 0) {
				window.scrollTo(0, window.scrollY);
			}
		});

		// Handle orientation change
		window.addEventListener('orientationchange', () => {
			setTimeout(() => {
				this.preventNavigationScroll();
			}, 100);
		});
	}

	preventNavigationScroll() {
		const navigationElements = [
			'.navigation-wrapper',
			'.navigation-bounds',
			'.navigation-container',
			'.desktop-nav'
		];

		navigationElements.forEach(selector => {
			const elements = document.querySelectorAll(selector);
			elements.forEach(element => {
				// Prevent touch scroll
				element.addEventListener('touchmove', (e) => {
					if (e.touches.length === 1) {
						const touch = e.touches[0];
						const startX = touch.clientX;
						const startY = touch.clientY;

						element.addEventListener('touchend', function handler() {
							const endX = touch.clientX;
							const endY = touch.clientY;
							const deltaX = Math.abs(endX - startX);
							const deltaY = Math.abs(endY - startY);

							// If horizontal movement is greater than vertical, prevent scroll
							if (deltaX > deltaY && deltaX > 10) {
								e.preventDefault();
							}
							element.removeEventListener('touchend', handler);
						});
					}
				}, { passive: false });

				// Prevent wheel scroll
				element.addEventListener('wheel', (e) => {
					if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
						e.preventDefault();
					}
				}, { passive: false });

				// Force element to stay within bounds
				const observer = new ResizeObserver(() => {
					const rect = element.getBoundingClientRect();
					if (rect.right > window.innerWidth) {
						element.style.maxWidth = '100vw';
						element.style.overflowX = 'hidden';
					}
				});
				observer.observe(element);
			});
		});

		// Add CSS class to prevent scroll on body
		document.body.classList.add('no-horizontal-scroll');

		// Handle window resize
		window.addEventListener('resize', () => {
			this.adjustNavigationForScreenSize();
		});

		// Initial adjustment
		this.adjustNavigationForScreenSize();
	}

	adjustNavigationForScreenSize() {
		const navContainer = document.querySelector('.navigation-container');
		if (!navContainer) return;

		const screenWidth = window.innerWidth;
		const navItems = document.querySelectorAll('.desktop-nav-item');

		if (screenWidth < 480) {
			// Very small screens: show only icons
			navItems.forEach(item => {
				const span = item.querySelector('span');
				if (span) span.style.display = 'none';

				const icon = item.querySelector('i');
				if (icon) {
					icon.style.marginRight = '0';
				}
			});
		} else if (screenWidth < 768) {
			// Small screens: show short text
			navItems.forEach(item => {
				const spans = item.querySelectorAll('span');
				spans.forEach(span => {
					if (span.classList.contains('lg:hidden')) {
						span.style.display = 'inline';
					} else if (!span.classList.contains('lg:hidden')) {
						span.style.display = 'none';
					}
				});
			});
		} else {
			// Larger screens: show full text
			navItems.forEach(item => {
				const spans = item.querySelectorAll('span');
				spans.forEach(span => {
					if (span.classList.contains('lg:hidden') || span.classList.contains('xl:hidden')) {
						span.style.display = 'none';
					} else {
						span.style.display = 'inline';
					}
				});
			});
		}
	}
}

// Initialize PWA when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
	new PWAHandler();
});

// Export for global use
window.PWAHandler = PWAHandler;
window.showOfflineGuide = () => {
	const pwa = new PWAHandler();
	pwa.showOfflineGuide();
};
