DATA="{\"nik\":\"1234567890123456\",\"nama_lengkap\":\"John Doe\",\"tanggal_lahir\":\"1990-01-01\",\"jenis_kelamin\":\"Laki-laki\",\"no_handphone\":\"081234567890\",\"status_perkawinan\":\"Menikah\",\"rencana_menikah\":\"Tidak\",\"status_hamil\":\"Tidak\",\"status_disabilitas\":\"Non disabilitas\"}"
API_URL="http://localhost:8000/api/skrining/demografi"
curl -X POST "$API_URL" -H "Content-Type: application/json" -H "Accept: application/json" -d "$DATA" -v
