<!DOCTYPE html>
<html>
<head>
    <title>Generate Audio Files</title>
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=HmFXZnYe"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .status { margin: 20px 0; padding: 10px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        #progress { margin: 20px 0; }
        .record-btn { background-color: #dc3545; color: white; border: none; }
        .record-btn.recording { background-color: #28a745; }
        .instructions { 
            background: #fff3cd; 
            color: #856404; 
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Audio Files</h1>
        
        <div class="instructions">
            <h3>Petunjuk Penggunaan:</h3>
            <ol>
                <li>Klik salah satu tombol generate untuk memulai</li>
                <li>Suara akan diputar satu per satu</li>
                <li>Tunggu sampai semua suara selesai diputar</li>
                <li>Gunakan software perekam audio (seperti Audacity) untuk merekam suara dari komputer</li>
            </ol>
        </div>

        <div id="status"></div>
        <div id="progress"></div>
        
        <button onclick="generateCommonAudio()">Generate Common Audio</button>
        <button onclick="generateNumberAudio()">Generate Number Audio (001-100)</button>
        <button onclick="generatePoliAudio()">Generate Poli Audio</button>
        
        <script>
            // Daftar audio yang akan dibuat
            const commonAudio = {
                'bell': 'ding dong',
                'nomor-antrian': 'Nomor Antrian',
                'menuju': 'Menuju'
            };
            
            const numbers = Array.from({length: 100}, (_, i) => 
                String(i + 1).padStart(3, '0')
            );
            
            const poli = {
                'umum': 'Poli Umum',
                'gigi': 'Poli Gigi',
                'kia': 'Poli KIA',
                'mtbs': 'Poli MTBS',
                'lansia': 'Poli Lansia',
                'kb': 'Poli KB'
            };
            
            function updateStatus(message, isError = false) {
                $('#status').html(`
                    <div class="status ${isError ? 'error' : 'success'}">
                        ${message}
                    </div>
                `);
            }
            
            function updateProgress(current, total) {
                const percentage = Math.round((current / total) * 100);
                $('#progress').html(`Progress: ${percentage}% (${current}/${total})`);
            }
            
            function sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }
            
            async function generateAudio(text, filename) {
                return new Promise((resolve, reject) => {
                    try {
                        console.log(`Generating: ${filename} (${text})`);
                        responsiveVoice.speak(text, "Indonesian Female", {
                            pitch: 1,
                            rate: 0.8,
                            volume: 1,
                            onend: async () => {
                                await sleep(500); // Jeda setelah setiap audio
                                resolve();
                            }
                        });
                    } catch (error) {
                        reject(error);
                    }
                });
            }
            
            async function generateCommonAudio() {
                try {
                    updateStatus('Memulai generate audio umum...');
                    let current = 0;
                    
                    for (const [filename, text] of Object.entries(commonAudio)) {
                        await generateAudio(text, `public/assets/audio/${filename}.mp3`);
                        current++;
                        updateProgress(current, Object.keys(commonAudio).length);
                        await sleep(1000); // Jeda antar file
                    }
                    
                    updateStatus('Generate audio umum selesai!');
                } catch (error) {
                    updateStatus('Error: ' + error.message, true);
                }
            }
            
            async function generateNumberAudio() {
                try {
                    updateStatus('Memulai generate audio nomor...');
                    let current = 0;
                    
                    for (const number of numbers) {
                        await generateAudio(number, `public/assets/audio/antrian/${number}.mp3`);
                        current++;
                        updateProgress(current, numbers.length);
                        await sleep(1000);
                    }
                    
                    updateStatus('Generate audio nomor selesai!');
                } catch (error) {
                    updateStatus('Error: ' + error.message, true);
                }
            }
            
            async function generatePoliAudio() {
                try {
                    updateStatus('Memulai generate audio poli...');
                    let current = 0;
                    
                    for (const [filename, text] of Object.entries(poli)) {
                        await generateAudio(text, `public/assets/audio/poli/${filename}.mp3`);
                        current++;
                        updateProgress(current, Object.keys(poli).length);
                        await sleep(1000);
                    }
                    
                    updateStatus('Generate audio poli selesai!');
                } catch (error) {
                    updateStatus('Error: ' + error.message, true);
                }
            }
        </script>
    </div>
</body>
</html> 