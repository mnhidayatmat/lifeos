/*
 * LifeOS service worker.
 *
 * Strategy (safe for an authenticated, server-rendered Laravel app):
 *  - Navigations (HTML): network-first. If offline, show the cached offline page.
 *    HTML responses are NEVER cached, so we never serve stale or cross-account pages.
 *  - Static assets (built JS/CSS, icons, fonts): stale-while-revalidate runtime cache.
 *  - Only same-origin GET requests are handled. POST/PUT/etc. always hit the network.
 */

const VERSION = 'v2';
const PRECACHE = `lifeos-precache-${VERSION}`;
const RUNTIME = `lifeos-runtime-${VERSION}`;

const PRECACHE_URLS = [
    '/offline.html',
    '/manifest.webmanifest',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(PRECACHE).then((cache) => cache.addAll(PRECACHE_URLS)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== PRECACHE && key !== RUNTIME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only handle same-origin GET requests.
    if (request.method !== 'GET' || new URL(request.url).origin !== self.location.origin) {
        return;
    }

    // Navigations → network-first, fall back to offline page.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
        return;
    }

    // Static assets → stale-while-revalidate.
    const dest = request.destination;
    const isAsset =
        dest === 'style' || dest === 'script' || dest === 'image' || dest === 'font' ||
        request.url.includes('/build/') || request.url.includes('/icons/');

    if (isAsset) {
        event.respondWith(
            caches.open(RUNTIME).then(async (cache) => {
                const cached = await cache.match(request);
                const network = fetch(request)
                    .then((response) => {
                        if (response && response.status === 200) {
                            cache.put(request, response.clone());
                        }
                        return response;
                    })
                    .catch(() => cached);
                return cached || network;
            })
        );
    }
});
