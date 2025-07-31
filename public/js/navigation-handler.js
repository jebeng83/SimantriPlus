/**
 * Navigation Handler - Menangani navigasi dan mencegah error saat perpindahan halaman
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Tangani transisi halaman
        applySmootherTransitions();

        // Tandai route aktif dan terapkan styling
        markActiveRoutes();

        // Aktifkan item menu sidebar yang sesuai
        activateSidebarItems();
    });

    // Fungsi untuk menerapkan transisi halaman yang lebih smooth
    function applySmootherTransitions() {
        // Tangani semua link navigasi
        var links = document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"]):not([href^="tel:"]):not([href^="mailto:"]):not([download])');
        
        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                var href = this.getAttribute('href');
                
                // Lewati jika link tidak valid atau khusus
                if (!href || href.startsWith('#') || 
                    href.startsWith('javascript:') || 
                    href.startsWith('tel:') || 
                    href.startsWith('mailto:') || 
                    this.hasAttribute('download') ||
                    this.classList.contains('no-transition')) {
                    return;
                }
                
                var sameOrigin = true;
                try {
                    var url = new URL(href, window.location.origin);
                    sameOrigin = (url.origin === window.location.origin);
                } catch(e) {
                    // URL relatif, dianggap same-origin
                }
                
                // Hanya terapkan transisi untuk navigasi internal
                if (sameOrigin) {
                    // Tambahkan animasi transisi
                    document.body.classList.add('page-transitioning');
                    
                    // Opsional: Simpan posisi scroll untuk back navigation
                    sessionStorage.setItem('scrollPosition_' + window.location.pathname, window.scrollY);
                }
            });
        });
        
        // Restore posisi scroll jika kembali ke halaman sebelumnya
        var savedScrollPosition = sessionStorage.getItem('scrollPosition_' + window.location.pathname);
        if (savedScrollPosition) {
            window.scrollTo(0, parseInt(savedScrollPosition));
        }
        
        // Hapus class transisi setelah halaman dimuat
        window.addEventListener('load', function() {
            document.body.classList.remove('page-transitioning');
        });
    }

    // Fungsi untuk menandai route aktif
    function markActiveRoutes() {
        // Tandai route aktif
        var path = window.location.pathname;
        document.body.setAttribute('data-route', path);
        
        // Terapkan sidebar hitam untuk semua halaman (ubah dari hanya premium routes)
        document.body.classList.add('premium-route');
    }

    // Fungsi untuk mengaktifkan menu sidebar yang sesuai
    function activateSidebarItems() {
        try {
            var path = window.location.pathname;
            var sidebarLinks = document.querySelectorAll('.nav-sidebar .nav-link');
            
            sidebarLinks.forEach(function(link) {
                var href = link.getAttribute('href');
                if (href) {
                    try {
                        // Ekstrak path dari URL
                        var url = new URL(href, window.location.origin);
                        var linkPath = url.pathname;

                        // Jika current path cocok dengan linkPath, aktifkan
                        if (path === linkPath || path.startsWith(linkPath + '/')) {
                            // Aktifkan link
                            link.classList.add('active');
                            
                            // Buka parent menu jika ada
                            var parentLi = link.closest('li.nav-item.has-treeview');
                            if (parentLi) {
                                parentLi.classList.add('menu-open');
                                var parentLink = parentLi.querySelector('.nav-link');
                                if (parentLink) parentLink.classList.add('active');
                            }
                        }
                    } catch(e) {
                        console.log('Error parsing URL:', href);
                    }
                }
            });
        } catch(e) {
            console.error('Error activating sidebar items:', e);
        }
    }

    // Deteksi error navigasi
    window.addEventListener('error', function(e) {
        // Cek apakah error terjadi saat navigasi
        if (document.body.classList.contains('page-transitioning')) {
            // Coba tangani error dengan lebih baik
            console.error('Navigation error detected:', e.message);
            
            // Hapus kelas transisi
            document.body.classList.remove('page-transitioning');
            
            // Reload halaman hanya jika diperlukan
            if (e.message.includes('script error') || e.message.includes('undefined') || e.message.includes('null')) {
                console.error('Reloading page due to critical navigation error');
                window.location.reload();
            }
        }
    });

    // Handle kesalahan AJAX
    if (window.jQuery) {
        jQuery(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
            console.error('AJAX error:', thrownError);
            
            // Hapus kelas transisi jika masih ada
            document.body.classList.remove('page-transitioning');
            
            // Jangan redirect otomatis untuk timeout atau error dari WhatsApp Node endpoints
            if (ajaxSettings.url && ajaxSettings.url.includes('whatsapp/node')) {
                console.log('WhatsApp Node endpoint error, tidak melakukan redirect');
                return;
            }
            
            if (jqXHR.status === 500) {
                // Redirect ke halaman error jika terjadi internal server error
                window.location.href = '/error';
            } else if (jqXHR.status === 404) {
                // Redirect ke halaman not found
                window.location.href = '/not-found';
            } else if (jqXHR.status === 403) {
                // Redirect ke halaman forbidden
                window.location.href = '/forbidden';
            }
        });
        
        // Tambahkan interceptor untuk semua permintaan AJAX
        jQuery(document).ajaxSend(function(event, jqXHR, settings) {
            // Tambahkan header CSRF token
            jqXHR.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        });
        
        // Deteksi selesai AJAX untuk handle transisi
        jQuery(document).ajaxComplete(function() {
            // Hapus kelas transisi setelah AJAX selesai
            document.body.classList.remove('page-transitioning');
        });
    }
})();