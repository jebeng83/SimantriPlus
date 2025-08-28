{{--
    Date Helper Partial
    Helper untuk format tanggal yang digunakan di modul ILP
--}}

@php
    // Helper function untuk format tanggal Indonesia
    if (!function_exists('formatTanggalIndonesia')) {
        function formatTanggalIndonesia($tanggal, $format = 'd/m/Y') {
            if (empty($tanggal) || $tanggal == '0000-00-00' || $tanggal == '0000-00-00 00:00:00') {
                return '-';
            }
            
            try {
                $date = new DateTime($tanggal);
                return $date->format($format);
            } catch (Exception $e) {
                return '-';
            }
        }
    }
    
    // Helper function untuk format tanggal dengan nama bulan Indonesia
    if (!function_exists('formatTanggalLengkap')) {
        function formatTanggalLengkap($tanggal) {
            if (empty($tanggal) || $tanggal == '0000-00-00' || $tanggal == '0000-00-00 00:00:00') {
                return '-';
            }
            
            $bulan = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            try {
                $date = new DateTime($tanggal);
                $hari = $date->format('d');
                $bulanNama = $bulan[(int)$date->format('m')];
                $tahun = $date->format('Y');
                
                return $hari . ' ' . $bulanNama . ' ' . $tahun;
            } catch (Exception $e) {
                return '-';
            }
        }
    }
    
    // Helper function untuk format waktu
    if (!function_exists('formatWaktu')) {
        function formatWaktu($waktu) {
            if (empty($waktu) || $waktu == '0000-00-00 00:00:00') {
                return '-';
            }
            
            try {
                $date = new DateTime($waktu);
                return $date->format('H:i:s');
            } catch (Exception $e) {
                return '-';
            }
        }
    }
    
    // Helper function untuk format tanggal dan waktu
    if (!function_exists('formatTanggalWaktu')) {
        function formatTanggalWaktu($datetime, $format = 'd/m/Y H:i') {
            if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
                return '-';
            }
            
            try {
                $date = new DateTime($datetime);
                return $date->format($format);
            } catch (Exception $e) {
                return '-';
            }
        }
    }
@endphp