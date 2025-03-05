// JavaScript Dasar untuk E-DOKTER

// Implementasi jQuery.once jika belum ada
(function($) {
    if (typeof $.once !== 'function') {
        // Menyimpan elemen yang sudah diproses
        var processedElements = {};
        
        // Implementasi $.once
        $.once = function(callback) {
            var uniqueId = 'once-' + Math.random().toString(36).substr(2, 9);
            return function() {
                var self = this;
                var $elements = $(self);
                
                $elements.each(function() {
                    var $el = $(this);
                    var id = $el.data('once-id') || {};
                    
                    if (!id[uniqueId]) {
                        id[uniqueId] = true;
                        $el.data('once-id', id);
                        callback.apply(this);
                    }
                });
                
                return $elements;
            };
        };
    }
})(jQuery);

// Fungsi untuk memeriksa status koneksi
function checkOnlineStatus() {
    if (navigator.onLine) {
        console.log('Aplikasi online');
        return true;
    } else {
        console.log('Aplikasi offline');
        return false;
    }
}

// Event listener untuk perubahan status koneksi
window.addEventListener('online', function() {
    console.log('Aplikasi kembali online');
});

window.addEventListener('offline', function() {
    console.log('Aplikasi offline');
});

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    console.log('E-DOKTER App loaded');
    checkOnlineStatus();
    
    // Tambahkan event listener untuk tombol refresh
    const refreshButtons = document.querySelectorAll('.btn-refresh');
    if (refreshButtons.length > 0) {
        refreshButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                window.location.reload();
            });
        });
    }
});

// Fungsi untuk menangani error AJAX
function handleAjaxError(xhr, status, error) {
    console.error('AJAX Error:', status, error);
    if (xhr.status === 0) {
        console.error('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
    } else if (xhr.status === 404) {
        console.error('Halaman yang diminta tidak ditemukan.');
    } else if (xhr.status === 500) {
        console.error('Terjadi kesalahan pada server.');
    } else {
        console.error('Terjadi kesalahan: ' + error);
    }
}

// Ekspos fungsi ke global scope jika diperlukan
window.appHelpers = {
    checkOnlineStatus: checkOnlineStatus,
    handleAjaxError: handleAjaxError
};
