const CACHE_NAME = 'tienda-v1';
const OFFLINE_URL = '/offline';

// Archivos que se cachean al instalar
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
    'https://cdn.tailwindcss.com',
    'https://cdn.jsdelivr.net/npm/chart.js',
];

// Instalar — cachear assets estáticos
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(() => {
                // Si falla algún asset externo, continuar igual
            });
        })
    );
    self.skipWaiting();
});

// Activar — limpiar caches viejos
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// Fetch — estrategia: Network first, cache fallback
self.addEventListener('fetch', event => {
    // Solo interceptar GETs
    if (event.request.method !== 'GET') return;

    // No interceptar requests de API o admin
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/api/')) return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Si la respuesta es válida, guardarla en cache
                if (response && response.status === 200 && response.type === 'basic') {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, clone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Sin internet — buscar en cache
                return caches.match(event.request).then(cached => {
                    if (cached) return cached;
                    // Si es navegación, mostrar página offline
                    if (event.request.mode === 'navigate') {
                        return caches.match(OFFLINE_URL);
                    }
                });
            })
    );
});

// Sincronización en background cuando vuelve el internet
self.addEventListener('sync', event => {
    if (event.tag === 'sync-pending-sales') {
        event.waitUntil(syncPendingSales());
    }
});

async function syncPendingSales() {
    // Esta función se llama desde el cliente via postMessage
    const clients = await self.clients.matchAll();
    clients.forEach(client => {
        client.postMessage({ type: 'SYNC_SALES' });
    });
}