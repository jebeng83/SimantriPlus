<?php
// URL langsung ke BPJS
$url = "https://kerjo.simkeskhanza.com/MjknKhanza/peserta";
$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJLaGFuemEgUkVTVCBBUEkiLCJhdWQiOiJDbGllbnQgS2hhbnphIFJFU1QgQVBJIiwiaWF0IjoxNzQzODEzODIyLCJleHAiOjM2NjAsImRhdGEiOnsidXNlcm5hbWUiOiJrZXJqbyJ9fQ.lIsXZWMim/l4QG3IH/X2X+8ZWcd672gv5iKnYoEL4Pg"; 
$username = "kerjo";

$data = [
    "nomorkartu" => "0001234567890",
    "nik" => "3212345678987654",
    "nomorkk" => "3212345678987654",
    "nama" => "Wati Suparman",
    "jeniskelamin" => "P",
    "tanggallahir" => "1990-05-15",
    "alamat" => "Jl. Contoh No. 123, RT 003 RW 002",
    "kodeprop" => "11",
    "namaprop" => "Jawa Barat",
    "kodedati2" => "0120",
    "namadati2" => "Kab. Bandung",
    "kodekec" => "1319",
    "namakec" => "Soreang",
    "kodekel" => "D2105",
    "namakel" => "Cingcin",
    "rw" => "002",
    "rt" => "003"
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-token: ' . $token,
    'x-username: ' . $username
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: " . $httpCode . "\n";
if ($error) {
    echo "Error: " . $error . "\n";
}
echo "Response:\n" . $response . "\n";
