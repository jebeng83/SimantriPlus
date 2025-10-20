Mobile JKN Debug & Retry Guide

This document describes how to simulate specific scenarios in the Mobile JKN integration for testing UI behavior and backend retry logic.

Available Debug Modes
- timeout: Simulates a connection timeout to BPJS services.
- skrining: Simulates a Mobile JKN response requiring the user to complete Skrining Kesehatan.
- retry-ok: For referensi endpoints, simulates a first attempt timeout and success on the retry.

How to Trigger Debug Modes
1) From the Mobile JKN UI
   - Timeout: open /pendaftaran-mobile-jkn?debug=timeout
   - Skrining: open /pendaftaran-mobile-jkn?debug=skrining

2) Calling APIs directly
   - FKTP ambil antrean (POST):
     /api/antrean?debug=timeout
     /api/antrean?debug=skrining
     Headers required: x-token, x-username

   - BPJS referensi poli (GET):
     /api/wsbpjs/referensi/poli/YYYY-MM-DD?debug=timeout
     /api/wsbpjs/referensi/poli/YYYY-MM-DD?debug=retry-ok

   - BPJS referensi dokter (GET):
     /api/wsbpjs/referensi/dokter/kodepoli/{KODE}/tanggal/YYYY-MM-DD?debug=timeout
     /api/wsbpjs/referensi/dokter/kodepoli/{KODE}/tanggal/YYYY-MM-DD?debug=retry-ok

Notes
- The MobileJknController forwards any ?debug query from the UI to FKTP via the x-debug header for antrean endpoints.
- WsBPJSController reads the ?debug query directly for referensi endpoints.

UI Behavior
- When metadata.code === 201 and the message contains connection keywords (e.g., "cURL error 28", "timed out", "timeout"), the UI shows the category: "Gangguan Koneksi BPJS FKTP".
- When metadata.code === 201 and the message contains "skrining kesehatan", the UI shows the category: "Skrining Kesehatan Diperlukan".
- Otherwise, metadata.code === 201 defaults to "Kuota Penuh".

Backend Retry Configuration
- Environment variable: BPJS_REFERENSI_RETRY (default: 2)
- Applies to WsBPJSController::getReferensiPoli and ::getReferensiDokter.
- Retry is skipped for authentication errors (401, 403). A 300ms delay is used between attempts.

Log Verification
Tail storage/logs/laravel-YYYY-MM-DD.log and look for entries like:
- "Simulating BPJS referensi-poli timeout" and then "Simulated BPJS referensi-poli success on retry"
- "Simulating BPJS referensi-dokter timeout" and then "Simulated BPJS referensi-dokter success on retry"