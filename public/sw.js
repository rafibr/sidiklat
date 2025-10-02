// Service Worker for PWA functionality
const CACHE_NAME = 'sidiklat-v1.0.0';
const urlsToCache = [
	'/',
	'/dashboard',
	'/progress',
	'/pelatihan',
	'/offline.html',
	'/build/manifest.json',
	'/build/assets/app.css',
	'/build/assets/app.js',
	'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
	'https://cdn.jsdelivr.net/npm/chart.js'
];

// Install event - cache resources
self.addEventListener('install', (event) => {
	event.waitUntil(
		caches.open(CACHE_NAME)
			.then((cache) => {
				return cache.addAll(urlsToCache);
			})
	);
	self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
	event.waitUntil(
		caches.keys().then((cacheNames) => {
			return Promise.all(
				cacheNames.map((cacheName) => {
					if (cacheName !== CACHE_NAME) {
						return caches.delete(cacheName);
					}
				})
			);
		})
	);
	self.clients.claim();
});

// Fetch event - serve from cache when offline
self.addEventListener('fetch', (event) => {
	event.respondWith(
		caches.match(event.request)
			.then((response) => {
				// Return cached version or fetch from network
				if (response) {
					return response;
				}

				return fetch(event.request)
					.then((response) => {
						// Don't cache non-GET requests or external resources
						if (!event.request.url.startsWith(self.location.origin) ||
							event.request.method !== 'GET') {
							return response;
						}

						// Cache successful responses
						if (response.status === 200) {
							const responseClone = response.clone();
							caches.open(CACHE_NAME)
								.then((cache) => {
									cache.put(event.request, responseClone);
								});
						}

						return response;
					})
					.catch(() => {
						// Return offline page for navigation requests
						if (event.request.mode === 'navigate') {
							return caches.match('/offline.html');
						}
					});
			})
	);
});

// Background sync for offline actions
self.addEventListener('sync', (event) => {
	if (event.tag === 'background-sync') {
		event.waitUntil(doBackgroundSync());
	}
});

function doBackgroundSync() {
	// Implement background sync logic here
	// This would typically sync offline actions when connection is restored
	console.log('Background sync triggered');
}

// Push notifications (if implemented later)
self.addEventListener('push', (event) => {
	const options = {
		body: event.data ? event.data.text() : 'Notifikasi baru dari SIDIKLAT',
		icon: '/favicon.ico',
		badge: '/favicon.ico',
		vibrate: [100, 50, 100],
		data: {
			dateOfArrival: Date.now(),
			primaryKey: 1
		},
		actions: [
			{
				action: 'explore',
				title: 'Lihat Detail',
				icon: '/favicon.ico'
			},
			{
				action: 'close',
				title: 'Tutup',
				icon: '/favicon.ico'
			}
		]
	};

	event.waitUntil(
		self.registration.showNotification('SIDIKLAT', options)
	);
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
	event.notification.close();

	if (event.action === 'explore') {
		event.waitUntil(
			clients.openWindow('/')
		);
	}
});
