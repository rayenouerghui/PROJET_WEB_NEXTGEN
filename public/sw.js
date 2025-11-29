const CACHE_NAME = 'nextgen-delivery-v1';
const urlsToCache = [
  '/PROJET_WEB_NEXTGEN-main/public/',
  '/PROJET_WEB_NEXTGEN-main/public/css/livraisons.css',
  '/PROJET_WEB_NEXTGEN-main/public/css/common.css',
  '/PROJET_WEB_NEXTGEN-main/public/images/delivery-truck.svg',
  '/PROJET_WEB_NEXTGEN-main/public/images/origin-marker.svg',
  '/PROJET_WEB_NEXTGEN-main/public/images/destination-marker.svg'
];

// Install event - cache files
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('✅ Cache opened');
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache hit - return response
        if (response) {
          return response;
        }
        return fetch(event.request);
      }
    )
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('🗑️ Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
