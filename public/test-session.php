<?php
// Memuat autoloader
require __DIR__.'/../vendor/autoload.php';

// Memuat aplikasi Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Mendapatkan kernel HTTP
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Membuat request
$request = Illuminate\Http\Request::capture();

// Menjalankan aplikasi untuk memproses request
$response = $kernel->handle($request);

// Tampilkan informasi session
echo "<h1>Session Info</h1>";
echo "<pre>";
echo "Session ID: " . session()->getId() . "\n";
echo "Session Data: \n";
print_r(session()->all());
echo "</pre>";

// Tambahkan form untuk set session
echo "<h2>Set Session</h2>";
echo "<form method='post'>";
echo "<input type='text' name='username' placeholder='Username' value='test_user'><br>";
echo "<input type='password' name='password' placeholder='Password' value='test_password'><br>";
echo "<input type='text' name='kd_poli' placeholder='Kode Poli' value='IGDK'><br>";
echo "<button type='submit'>Set Session</button>";
echo "</form>";

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session(['username' => $_POST['username']]);
    session(['password' => $_POST['password']]);
    session(['kd_poli' => $_POST['kd_poli'] ?? 'UMUM']);
    
    // Simpan session
    session()->save();
    
    echo "<h3>Session Updated!</h3>";
    echo "<pre>";
    echo "New Session Data: \n";
    print_r(session()->all());
    echo "</pre>";
    
    echo "<p><a href='/kyc'>Try accessing /kyc now</a></p>";
}

// Terminate the application
$kernel->terminate($request, $response);
?> 