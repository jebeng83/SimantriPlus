const CACHE_NAME = 'simantri-cache-v1';
const urlsToCache = [
    '/',
    '/offline.html',
    '/css/app.css',
    '/js/app.js',
    '/js/chart-config.js',
    '/favicon.ico',
    '/favicon.png',
    '/favicons/favicon-16x16.png',
    '/favicons/favicon-32x32.png',
    '/favicons/favicon-96x96.png',
    '/favicons/android-icon-192x192.png'
];

// Install Service Worker
self.addEventListener('install', event => {
    console.log('Service Worker installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache).catch(err => {
                    console.error('Error caching initial files:', err);
                    // Lanjutkan instalasi meskipun ada error
                    return Promise.resolve();
                });
            })
            .catch(err => {
                console.error('Error opening cache:', err);
            })
    );
});

// Fetch Event
self.addEventListener('fetch', event => {
    // Hanya tangani permintaan GET
    if (event.request.method !== 'GET') {
        return;
    }

    // Daftar URL yang harus diabaikan (tidak di-cache)
    const ignoredUrls = [
        '/ilp/dewasa/',
        '/customlogin',
        'serviceworker.js'
    ];

    // Periksa apakah URL harus diabaikan
    const shouldIgnore = ignoredUrls.some(url => event.request.url.includes(url));
    if (shouldIgnore) {
        return fetch(event.request).catch(() => {
            // Jika gagal fetch, tampilkan halaman offline untuk navigasi
            if (event.request.mode === 'navigate') {
                return caches.match('/offline.html');
            }
            return new Response('Network error', {
                status: 503,
                headers: { 'Content-Type': 'text/plain' }
            });
        });
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Cache hit - return response
                if (response) {
                    return response;
                }

                // Clone the request
                const fetchRequest = event.request.clone();

                return fetch(fetchRequest)
                    .then(response => {
                        // Check if valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // Clone the response
                        const responseToCache = response.clone();

                        // Simpan ke cache secara asinkron
                        caches.open(CACHE_NAME)
                            .then(cache => {
                                try {
                                    cache.put(event.request, responseToCache);
                                } catch (error) {
                                    console.error('Error caching response:', error);
                                }
                            })
                            .catch(err => {
                                console.error('Error opening cache:', err);
                            });

                        return response;
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        // Jika terjadi error, coba tampilkan halaman offline
                        if (event.request.mode === 'navigate') {
                            return caches.match('/offline.html');
                        }
                        
                        // Untuk request lain yang gagal
                        return new Response('Network error happened', {
                            status: 503,
                            headers: { 'Content-Type': 'text/plain' }
                        });
                    });
            })
            .catch(error => {
                console.error('Cache match error:', error);
                return caches.match('/offline.html');
            })
    );
});

// Activate Event - Clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker activating...');
    const cacheWhitelist = [CACHE_NAME];

    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                    return Promise.resolve();
                })
            );
        })
    );
    
    // Klaim klien langsung setelah aktivasi
    return self.clients.claim();
});