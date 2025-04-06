/**
 * Script untuk mendekripsi respons dari API BPJS
 * Digunakan oleh halaman Mobile JKN
 */

// Fungsi untuk mendekripsi data dari BPJS
function decryptBpjsResponse(encryptedData, consId, secretKey, timestamp) {
    try {
        // Validasi input
        if (!encryptedData || !consId || !secretKey || !timestamp) {
            console.error('Missing required parameters for decryption');
            return null;
        }
        
        // Buat kunci dekripsi
        const key = consId + secretKey + timestamp;
        
        // Buat key hash dengan SHA-256
        const keyHash = CryptoJS.SHA256(key).toString();
        
        // Ambil 16 byte pertama untuk IV
        const iv = CryptoJS.enc.Hex.parse(keyHash.substring(0, 32));
        
        // Parse key hash sebagai hex
        const keyHex = CryptoJS.enc.Hex.parse(keyHash);
        
        // Base64 decode string terlebih dahulu
        let ciphertext;
        try {
            ciphertext = CryptoJS.enc.Base64.parse(encryptedData);
        } catch (e) {
            console.error('Error parsing Base64 data', e);
            return null;
        }
        
        // Dekripsi menggunakan AES-256-CBC
        const decrypted = CryptoJS.AES.decrypt(
            { ciphertext: ciphertext },
            keyHex,
            {
                iv: iv,
                padding: CryptoJS.pad.Pkcs7,
                mode: CryptoJS.mode.CBC
            }
        );
        
        // Konversi hasil dekripsi ke string
        let decryptedString;
        try {
            decryptedString = decrypted.toString(CryptoJS.enc.Utf8);
        } catch (e) {
            console.error('Error converting decrypted data to string', e);
            return null;
        }
        
        // Jika hasil dekripsi kosong, kembalikan null
        if (!decryptedString || decryptedString === '') {
            console.error('Decryption resulted in empty string');
            return null;
        }
        
        // Coba dekompresi hasil dekripsi
        let decompressed;
        try {
            decompressed = LZString.decompressFromEncodedURIComponent(decryptedString);
        } catch (e) {
            console.error('Error decompressing decrypted string', e);
            return decryptedString; // Kembalikan hasil dekripsi jika dekompresi gagal
        }
        
        // Jika hasil dekompresi valid, kembalikan
        if (decompressed && decompressed !== '') {
            // Coba parse sebagai JSON jika mungkin
            try {
                return JSON.parse(decompressed);
            } catch (e) {
                return decompressed;
            }
        }
        
        // Jika dekompresi gagal, kembalikan hasil dekripsi
        return decryptedString;
    } catch (e) {
        console.error('Error decrypting BPJS response', e);
        return null;
    }
}

// Fungsi untuk mendekripsi respons dari API BPJS secara otomatis
function decryptBpjsResponseAuto(response) {
    // Periksa apakah respons mengandung data terenkripsi
    if (!response || typeof response !== 'object') {
        console.error('Invalid response format');
        return response;
    }
    
    // Periksa apakah respons memiliki timestamp
    if (!response.timestamp) {
        console.error('Response missing timestamp required for decryption');
        return response;
    }
    
    // Periksa apakah respons memiliki data terenkripsi
    if (!response.response || typeof response.response !== 'string') {
        return response;
    }
    
    // Ambil konsumer ID dan secret key dari konfigurasi global
    const consId = window.BPJS_CONFIG ? window.BPJS_CONFIG.consId : null;
    const secretKey = window.BPJS_CONFIG ? window.BPJS_CONFIG.secretKey : null;
    
    if (!consId || !secretKey) {
        console.error('BPJS configuration missing consId or secretKey');
        return response;
    }
    
    // Dekripsi data
    const decrypted = decryptBpjsResponse(
        response.response,
        consId,
        secretKey,
        response.timestamp
    );
    
    // Jika dekripsi berhasil, ganti data di respons
    if (decrypted !== null) {
        response.response = decrypted;
    }
    
    return response;
}

// Membuat fungsi untuk menampilkan debug info tentang response dari server BPJS
function debugBpjsResponse(response) {
    console.group('BPJS Response Debug Info');
    console.log('Response type:', typeof response);
    
    if (typeof response === 'object') {
        console.log('Has metadata:', !!response.metadata);
        console.log('Has response key:', !!response.response);
        console.log('Response key type:', typeof response.response);
        
        if (typeof response.response === 'string') {
            console.log('Response string length:', response.response.length);
            console.log('Response string preview:', response.response.substring(0, 50) + '...');
        }
        
        console.log('Has timestamp:', !!response.timestamp);
    }
    
    console.groupEnd();
    return response;
}

// Buat objek global untuk BPJS config
window.BPJS_CONFIG = {
    consId: '7925',
    secretKey: '2eF2C8E837'
};

// Tambahkan penanganan AJAX global untuk otomatis mendekripsi respons BPJS
$(document).ajaxSuccess(function(event, xhr, settings) {
    // Cek apakah respons adalah JSON
    let isJson = false;
    try {
        const contentType = xhr.getResponseHeader('Content-Type');
        isJson = contentType && contentType.indexOf('application/json') !== -1;
    } catch (e) {
        console.error('Error checking content type', e);
    }
    
    if (!isJson) {
        return;
    }
    
    try {
        // Parse respons sebagai JSON
        const response = JSON.parse(xhr.responseText);
        
        // Pastikan respon memiliki format yang sesuai
        if (response && typeof response === 'object' && (response.metadata || response.metaData)) {
            // Tambahkan timestamp 
            const timestamp = Math.floor(Date.now() / 1000).toString();
            response.timestamp = timestamp;
            
            // Jika respons berisi response yang berupa string (terenkripsi)
            if (response.response && typeof response.response === 'string' && 
                response.response.length > 100 && // Kemungkinan besar data terenkripsi
                !Array.isArray(response.response)) {
                
                console.log('Mencoba mendekripsi respons BPJS');
                // Dekripsi respons
                const decrypted = decryptBpjsResponse(
                    response.response,
                    window.BPJS_CONFIG.consId,
                    window.BPJS_CONFIG.secretKey,
                    timestamp
                );
                
                if (decrypted !== null) {
                    console.log('Berhasil mendekripsi respons BPJS');
                    response.response = decrypted;
                    
                    // Ganti responseText asli dengan yang sudah didekripsi
                    Object.defineProperty(xhr, 'responseText', {
                        writable: true,
                        value: JSON.stringify(response)
                    });
                } else {
                    console.error('Gagal mendekripsi respons BPJS');
                }
            }
        }
    } catch (e) {
        console.error('Error processing BPJS response in AJAX success handler', e);
    }
}); 