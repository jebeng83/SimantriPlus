// Konfigurasi global untuk Chart.js
Chart.defaults.global.defaultFontFamily = "'Nunito', 'Segoe UI', arial";
Chart.defaults.global.defaultFontColor = '#858796';
Chart.defaults.global.tooltips.backgroundColor = 'rgb(255,255,255)';
Chart.defaults.global.tooltips.bodyFontColor = '#858796';
Chart.defaults.global.tooltips.titleMarginBottom = 10;
Chart.defaults.global.tooltips.titleFontColor = '#6e707e';
Chart.defaults.global.tooltips.titleFontSize = 14;
Chart.defaults.global.tooltips.borderColor = '#dddfeb';
Chart.defaults.global.tooltips.borderWidth = 1;
Chart.defaults.global.tooltips.xPadding = 15;
Chart.defaults.global.tooltips.yPadding = 15;
Chart.defaults.global.legend.display = true;
Chart.defaults.global.legend.position = 'bottom';

// Fungsi untuk memastikan URL aman
function secureChartUrl(url) {
    return url.replace('http://', 'https://');
}

// Fungsi untuk menginisialisasi grafik dengan pengaturan aman
function initSecureChart(chartId, config) {
    // Pastikan semua URL menggunakan HTTPS
    if (config.options && config.options.elements) {
        Object.keys(config.options.elements).forEach(key => {
            if (config.options.elements[key].backgroundImage) {
                config.options.elements[key].backgroundImage = secureChartUrl(config.options.elements[key].backgroundImage);
            }
        });
    }
    
    return new Chart(document.getElementById(chartId), config);
} 