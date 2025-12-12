/**
 * NextGen Admin PWA - Service Worker
 * Handles caching, offline support, and updates
 */

const CACHE_VERSION = 'nextgen-admin-v2.0';
const CACHE_NAME = `${CACHE_VERSION}`;

// Files to cache immediately on install
const CRITICAL_CACHE = [
  '/PROJET_WEB_NEXTGEN-main/public/backoffice/livraisons.php',
  '/PROJET_WEB_NEXTGEN-main/public/assets/css/app.min.css',
  '/PROJET_WEB_NEXTGEN-main/public/assets/css/nextgen-admin-theme.css',
  '/PROJET_WEB_NEXTGEN-main/public/assets/css/nextgen-enhancements.css',
  '/PROJET_WEB_NEXTGEN-main/public/assets/css/nextgen-additional.css',
  '/PROJET_WEB_NEXTGEN-main/public/assets/css/theme-toggle.css',
  '/PROJET_WEB_NEXTGEN-main/public/assets/js/vendor.min.js',
  '/PROJET_WEB_NEXTGEN-main/public/assets/js/app.js',
  '/PROJET_WEB_NEXTGEN-main/public/assets/js/theme-toggle.js',
  '/PROJET_WEB_NEXTGEN-main/public/assets/images/logo.png',
  '/PROJET_WEB_NEXTGEN-main/public/assets/images/users/admin-profile.png'
];

// Offline fallback page
const OFFLINE_PAGE = '/PROJET_WEB_NEXTGEN-main/public/offline.html';

/**
 * Install Event - Cache critical assets
 */
self.addEventListener('install', event => {
  console.log('📦 [SW] Installing service worker...');

  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('✅ [SW] Opened cache:', CACHE_NAME);
        // Add all critical files to cache
        return cache.addAll(CRITICAL_CACHE.concat([OFFLINE_PAGE]))
          .catch(err => {
            console.warn('⚠️ [SW] Some files failed to cache:', err);
            // Continue even if some files fail
          });
      })
      .then(() => {
        console.log('🚀 [SW] Installation complete');
        // Skip waiting to activate immediately
        return self.skipWaiting();
      })
  );
});

/**
 * Activate Event - Clean up old caches
 */
self.addEventListener('activate', event => {
  console.log('🔄 [SW] Activating new service worker...');

  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            // Delete old caches
            if (cacheName !== CACHE_NAME) {
              console.log('🗑️ [SW] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('✅ [SW] Activation complete');
        // Take control of all clients immediately
        return self.clients.claim();
      })
  );
});

/**
 * Fetch Event - Network first, fallback to cache
 * Strategy: Try network first for dynamic content, use cache as fallback
 */
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }

  // Skip chrome extensions and other protocols
  if (!url.protocol.startsWith('http')) {
    return;
  }

  event.respondWith(
    // Try network first
    fetch(request)
      .then(response => {
        // Clone the response before caching
        const responseToCache = response.clone();

        // Cache successful responses
        if (response.status === 200) {
          caches.open(CACHE_NAME).then(cache => {
            cache.put(request, responseToCache);
          });
        }

        return response;
      })
      .catch(() => {
        // Network failed, try cache
        return caches.match(request)
          .then(cachedResponse => {
            if (cachedResponse) {
              console.log('📦 [SW] Serving from cache:', request.url);
              return cachedResponse;
            }

            // If it's a navigation request, show offline page
            if (request.mode === 'navigate') {
              return caches.match(OFFLINE_PAGE);
            }

            // For other requests, return a generic offline response
            return new Response('Offline - No cached version available', {
              status: 503,
              statusText: 'Service Unavailable',
              headers: new Headers({
                'Content-Type': 'text/plain'
              })
            });
          });
      })
  );
});

/**
 * Message Event - Handle messages from clients
 */
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }

  if (event.data && event.data.type === 'CLEAR_CACHE') {
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => caches.delete(cacheName))
      );
    }).then(() => {
      console.log('🗑️ [SW] All caches cleared');
    });
  }
});

console.log('🎯 [SW] Service Worker loaded successfully');
