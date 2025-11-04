// Simple, robust PWA service worker for production
// - Pre-caches essential assets including an offline page
// - Avoids intercepting external requests and Vite dev server ports
// - Always returns a valid Response in fallbacks to prevent TypeError

const staticCacheName = 'pwa-v' + new Date().getTime();
const filesToCache = [
  '/offline.html',
  '/css/app.css',
  '/js/app.js',
  '/images/icons/icon-72x72.png',
  '/images/icons/icon-96x96.png',
  '/images/icons/icon-128x128.png',
  '/images/icons/icon-144x144.png',
  '/images/icons/icon-152x152.png',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-384x384.png',
  '/images/icons/icon-512x512.png',
];

// Fallback makers
function handleCssRequest() {
  const emptyCss = `/* Offline fallback CSS */\nbody { font-family: Arial, sans-serif; }`;
  return new Response(emptyCss, {
    status: 200,
    headers: { 'Content-Type': 'text/css', 'Cache-Control': 'no-cache' },
  });
}

function handleAvatarRequest() {
  // Transparent 1x1 PNG as a lightweight placeholder
  const emptyImageBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
  const binary = atob(emptyImageBase64);
  const array = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) array[i] = binary.charCodeAt(i);
  return new Response(new Blob([array], { type: 'image/png' }), {
    status: 200,
    headers: { 'Content-Type': 'image/png', 'Cache-Control': 'no-cache' },
  });
}

function offlineFallback(request) {
  // Navigation requests -> offline page
  if (request.mode === 'navigate' || (request.headers.get('Accept') || '').includes('text/html')) {
    return caches.match('/offline.html').then((res) => {
      return (
        res ||
        new Response('<!doctype html><title>Offline</title><h1>Anda sedang offline</h1>', {
          status: 503,
          headers: { 'Content-Type': 'text/html' },
        })
      );
    });
  }
  // Styles
  if (request.destination === 'style') {
    return handleCssRequest();
  }
  // Scripts
  if (request.destination === 'script') {
    return new Response('// Offline fallback', {
      status: 200,
      headers: { 'Content-Type': 'application/javascript' },
    });
  }
  // Images
  if (request.destination === 'image') {
    return handleAvatarRequest();
  }
  // Generic fallback
  return new Response('Konten tidak tersedia saat offline', {
    status: 503,
    headers: { 'Content-Type': 'text/plain' },
  });
}

// Cache on install
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches
      .open(staticCacheName)
      .then((cache) => cache.addAll(filesToCache))
      .catch((err) => console.warn('[SW] cache.addAll error:', err))
  );
});

// Clear old caches and take control
self.addEventListener('activate', (event) => {
  event.waitUntil(
    Promise.all([
      caches.keys().then((cacheNames) => {
        return Promise.all(
          cacheNames
            .filter((cacheName) => cacheName.startsWith('pwa-'))
            .filter((cacheName) => cacheName !== staticCacheName)
            .map((cacheName) => caches.delete(cacheName))
        );
      }),
      self.clients.claim(),
    ])
  );
});

// Serve from cache with network fallback, and safe offline fallbacks
self.addEventListener('fetch', (event) => {
  const { request } = event;

  // Only handle GET
  if (request.method !== 'GET') return;

  const url = new URL(request.url);

  // Skip external requests and Vite dev server ports (517x)
  if (
    url.origin !== self.location.origin ||
    (url.port && url.port.startsWith('517')) ||
    url.hostname === '0.0.0.0'
  ) {
    // Let the browser handle it to avoid SW converting errors
    return;
  }

  event.respondWith(
    caches
      .match(request)
      .then((cached) => {
        const fetchPromise = fetch(request)
          .then((response) => {
            // Update cache with successful basic responses
            if (response && response.status === 200 && response.type === 'basic') {
              const respClone = response.clone();
              caches.open(staticCacheName).then((cache) => cache.put(request, respClone));
            }
            return response;
          })
          .catch(() => offlineFallback(request));

        return cached || fetchPromise;
      })
      .catch(() => offlineFallback(request))
  );
});