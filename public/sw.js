const preLoad = function () {
    return caches.open("offline").then(function (cache) {
        // Coba cache file satu per satu untuk menghindari kegagalan total
        const filesToCache = [
            '/', 
            '/offline.html', 
            '/favicon.ico', 
            '/favicon.png',
            '/favicons/favicon-16x16.png',
            '/favicons/favicon-32x32.png',
            '/favicons/favicon-96x96.png',
            '/favicons/android-icon-192x192.png',
            '/css/app.css',
            '/js/app.js'
        ];
        
        return Promise.allSettled(
            filesToCache.map(url => {
                return cache.add(url).catch(error => {
                    console.error('Gagal menyimpan ke cache:', url, error);
                    return null; // Lanjutkan meskipun ada error
                });
            })
        );
    });
};

// Penanganan khusus untuk file CSS yang tidak ditemukan
const handleCssRequest = function(request) {
    // CSS kosong sebagai fallback
    const emptyCss = `/* Fallback CSS */
    body { font-family: Arial, sans-serif; }
    `;
    
    return new Response(emptyCss, {
        status: 200,
        headers: {
            'Content-Type': 'text/css',
            'Cache-Control': 'no-cache'
        }
    });
};

// Penanganan khusus untuk file avatar yang tidak ditemukan
const handleAvatarRequest = function(request) {
    // Gambar placeholder 1x1 pixel sebagai fallback
    const emptyImageBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
    
    return new Response(
        new Blob(
            [atob(emptyImageBase64)],
            {type: 'image/png'}
        ),
        {
            status: 200,
            headers: {
                'Content-Type': 'image/png',
                'Cache-Control': 'no-cache'
            }
        }
    );
};

// Fungsi untuk memeriksa apakah permintaan dapat di-cache
const canCacheRequest = function(request) {
    // Hanya cache permintaan GET
    if (request.method !== 'GET') {
        return false;
    }
    
    // Jangan cache permintaan ke /ilp/dewasa/
    if (request.url.includes('/ilp/dewasa/')) {
        return false;
    }
    
    // Jangan cache permintaan ke /customlogin
    if (request.url.includes('/customlogin')) {
        return false;
    }
    
    return true;
};

const checkResponse = function (request) {
    return new Promise(function (fulfill, reject) {
        fetch(request).then(function (response) {
            if (response.status !== 404) {
                fulfill(response);
            } else {
                reject(new Error('Respons tidak ditemukan (404)'));
            }
        }, reject);
    });
};

const addToCache = function (request) {
    // Hanya cache permintaan GET
    if (request.method !== 'GET') {
        return Promise.resolve();
    }
    
    // Jangan cache permintaan ke /ilp/dewasa/ atau /customlogin
    if (request.url.includes('/ilp/dewasa/') || request.url.includes('/customlogin')) {
        return Promise.resolve();
    }
    
    return caches.open("offline").then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request, response);
        }).catch(error => {
            console.error('Gagal menambahkan ke cache:', request.url, error);
        });
    });
};

const returnFromCache = function (request) {
    return caches.open("offline").then(function (cache) {
        return cache.match(request).then(function (matching) {
            if (!matching || matching.status === 404) {
                // Jika permintaan adalah untuk favicon.png dan tidak ditemukan, coba favicon.ico
                if (request.url.includes('/favicon.png')) {
                    return cache.match('/favicon.ico');
                }
                
                // Jika permintaan adalah untuk halaman HTML, kembalikan halaman offline
                if (request.headers.get('Accept') && request.headers.get('Accept').includes('text/html')) {
                    return cache.match('/offline.html');
                }
                
                return Promise.reject("no-match");
            }
            return matching;
        });
    });
};

self.addEventListener("install", function (event) {
    event.waitUntil(preLoad());
});

self.addEventListener("fetch", function (event) {
    // Jangan tangani permintaan non-GET
    if (event.request.method !== 'GET') {
        return;
    }
    
    // Jangan tangani permintaan ke /ilp/dewasa/ atau /customlogin
    if (event.request.url.includes('/ilp/dewasa/') || event.request.url.includes('/customlogin')) {
        return;
    }
    
    // Tangani permintaan CSS secara khusus
    if (event.request.url.endsWith('.css') || event.request.url.includes('/css/')) {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    console.log('CSS tidak ditemukan, mengembalikan CSS kosong:', event.request.url);
                    return handleCssRequest(event.request);
                })
        );
        return;
    }
    
    // Tangani permintaan avatar secara khusus
    if (event.request.url.includes('/avatar.png') || event.request.url.includes('/webapps/photopasien/avatar.png')) {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    console.log('Avatar tidak ditemukan, mengembalikan placeholder:', event.request.url);
                    return handleAvatarRequest(event.request);
                })
        );
        return;
    }
    
    // Tangani permintaan favicon secara khusus
    if (event.request.url.includes('/favicon.png') || event.request.url.includes('/favicon.ico')) {
        event.respondWith(
            caches.match(event.request)
                .then(response => {
                    if (response) {
                        return response;
                    }
                    
                    // Jika favicon.png tidak ditemukan, coba favicon.ico
                    if (event.request.url.includes('/favicon.png')) {
                        return caches.match('/favicon.ico')
                            .then(icoResponse => {
                                if (icoResponse) {
                                    return icoResponse;
                                }
                                // Jika masih tidak ditemukan, coba fetch
                                return fetch(event.request);
                            });
                    }
                    
                    return fetch(event.request);
                })
                .catch(() => {
                    // Jika gagal, kembalikan gambar kosong 1x1 pixel
                    return handleAvatarRequest(event.request);
                })
        );
        return;
    }
    
    // Tangani permintaan ikon secara khusus
    if (event.request.url.includes('/images/icons/')) {
        event.respondWith(
            caches.match(event.request)
                .then(response => {
                    if (response) {
                        return response;
                    }
                    return fetch(event.request);
                })
                .catch(() => {
                    // Jika gagal, kembalikan gambar kosong 1x1 pixel
                    return new Response(
                        new Blob([
                            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
                        ], {type: 'image/png'})
                    );
                })
        );
        return;
    }
    
    // Hanya tangani permintaan yang berasal dari domain yang sama
    // atau permintaan HTTPS untuk menghindari masalah SSL
    if (event.request.url.startsWith(self.location.origin) || 
        (event.request.url.startsWith('https://') && !event.request.url.includes('faskesku.com/sw.js'))) {
        
        // Jangan cache permintaan ke /ilp/dewasa/ atau /customlogin
        if (event.request.url.includes('/ilp/dewasa/') || event.request.url.includes('/customlogin')) {
            return;
        }
        
        // Jangan cache permintaan media (gambar besar, video, audio)
        if (event.request.url.match(/\.(mp4|webm|ogg|mp3|avi|mov|wmv)$/i)) {
            event.respondWith(checkResponse(event.request).catch(function () {
                return returnFromCache(event.request);
            }));
            return;
        }
        
        // Untuk permintaan non-media, coba cache
        event.respondWith(
            checkResponse(event.request).then(function (response) {
                // Hanya cache permintaan GET
                if (event.request.method === 'GET') {
                    event.waitUntil(addToCache(event.request));
                }
                return response;
            }).catch(function () {
                return returnFromCache(event.request).catch(function() {
                    // Jika permintaan adalah untuk gambar, kembalikan gambar placeholder
                    if (event.request.url.match(/\.(jpg|jpeg|png|gif|bmp|webp|svg)$/i) || 
                        event.request.destination === 'image') {
                        return handleAvatarRequest(event.request);
                    }
                    
                    // Untuk permintaan CSS, kembalikan CSS kosong
                    if (event.request.url.endsWith('.css') || event.request.destination === 'style') {
                        return handleCssRequest(event.request);
                    }
                    
                    // Untuk permintaan JS, kembalikan JS kosong
                    if (event.request.url.endsWith('.js') || event.request.destination === 'script') {
                        return new Response('// Fallback empty JavaScript', {
                            status: 200, 
                            headers: {'Content-Type': 'application/javascript'}
                        });
                    }
                    
                    // Untuk permintaan lainnya, kembalikan pesan error
                    return new Response('Konten tidak tersedia saat offline', {
                        status: 503,
                        headers: {'Content-Type': 'text/plain'}
                    });
                });
            })
        );
    }
});
