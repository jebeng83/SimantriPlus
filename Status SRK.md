# Status SRK

Panduan lengkap membuat pemeriksaan Status SRK (Skrining Kesehatan) melalui endpoint backend dan menampilkan hasil "Sudah SRK" / "Belum SRK" di frontend.

## Tujuan
- Backend melakukan panggilan terautentikasi ke layanan Mobile JKN untuk memeriksa apakah peserta sudah melakukan SRK.
- Frontend menampilkan status secara jelas dan real-time di halaman pendaftaran/reg_periksa.

## Ringkas Mekanisme
- Pemeriksaan SRK dilakukan dengan melakukan request simulasi pendaftaran antrean ke endpoint Mobile JKN `antreanfktp/antrean/add` menggunakan data minimal peserta.
- Jika layanan mengembalikan pesan yang mengindikasikan peserta belum skrining kesehatan, tampilkan "Belum SRK". Jika tidak ada indikasi tersebut atau berhasil, tampilkan "Sudah SRK".
- Endpoint harus diakses dengan metode POST. Pemakaian metode selain POST akan menghasilkan "Method Not Allowed" dari server.

## Desain Endpoint Backend
- **Route (contoh):** `POST /api/bpjs/cek-srk`
- **Request Body (JSON):**
  - `nomorkartu` (string, wajib)
  - `nik` (string, opsional tetapi dianjurkan)
  - `nohp` (string, opsional)
  - `tanggalperiksa` (string, format `YYYY-MM-DD`, wajib)
- **Response (JSON, disarankan):**
  - `status`: `SUDAH_SRK` | `BELUM_SRK` | `UNKNOWN`
  - `message`: penjelasan singkat yang aman ditampilkan ke pengguna
  - `metadata`: objek ringkas hasil dari layanan (boleh dihilangkan pada mode produksi)

### Header Autentikasi ke Mobile JKN
- `X-cons-id`: ID konsumen
- `X-timestamp`: epoch detik (UTC)
- `X-signature`: HMAC SHA256 dari `consId & timestamp` menggunakan secret key, hasil Base64
- `X-authorization`: skema otorisasi (mis. `Basic <token>` jika diwajibkan)
- `user_key`: kunci pengguna yang diberikan BPJS

Simpan seluruh kredensial di `.env` dan gunakan konfigurasi agar tidak pernah dikomit.

### Payload Simulasi Pendaftaran Antrean
Gunakan payload minimal untuk memicu verifikasi SRK tanpa benar-benar menambah antrean produksi:

```json
{
  "nomorkartu": "0001441909697",
  "nik": "3313161201830001",
  "nohp": "085229977208",
  "kodepoli": "INT",
  "namapoli": "Poli Internal",
  "norm": "000542.5",
  "tanggalperiksa": "2025-12-30",
  "kodedokter": 999999,
  "namadokter": "Dokter SRK Check",
  "jampraktek": "00:01-00:02",
  "nomorantrean": "000",
  "angkaantrean": 0,
  "keterangan": "SRK check only"
}
```

### Pseudokode Implementasi Controller (Laravel)
```php
public function cekSrk(Request $request)
{
    $data = $request->validate([
        'nomorkartu' => 'required|string',
        'nik' => 'nullable|string',
        'nohp' => 'nullable|string',
        'tanggalperiksa' => 'required|date_format:Y-m-d',
    ]);

    $payload = array_merge($data, [
        'kodepoli' => 'INT',
        'namapoli' => 'Poli Internal',
        'norm' => $request->input('norm', ''),
        'kodedokter' => 999999,
        'namadokter' => 'Dokter SRK Check',
        'jampraktek' => '00:01-00:02',
        'nomorantrean' => '000',
        'angkaantrean' => 0,
        'keterangan' => 'SRK check only',
    ]);

    $consId = config('services.bpjs.cons_id');
    $secret = config('services.bpjs.secret');
    $userKey = config('services.bpjs.user_key');
    $timestamp = (string) time();
    $signature = base64_encode(hash_hmac('sha256', $consId . '&' . $timestamp, $secret, true));

    $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-cons-id' => $consId,
        'X-timestamp' => $timestamp,
        'X-signature' => $signature,
        'X-authorization' => 'Basic ' . base64_encode('username:password'), // sesuaikan jika diwajibkan
        'user_key' => $userKey,
    ];

    $url = rtrim(config('services.bpjs.base_url'), '/') . '/antreanfktp/antrean/add';

    try {
        $client = new \GuzzleHttp\Client([ 'timeout' => 15 ]);
        $res = $client->post($url, [ 'headers' => $headers, 'json' => $payload ]);
        $body = json_decode((string) $res->getBody(), true);

        $message = strtolower($body['metadata']['message'] ?? '');
        $code = (int) ($body['metadata']['code'] ?? 0);

        // Heuristik penentuan status SRK
        $belum = (str_contains($message, 'skrining kesehatan') || str_contains($message, 'skrining'));
        $status = $belum ? 'BELUM_SRK' : 'SUDAH_SRK';

        return response()->json([
            'status' => $status,
            'message' => $belum ? 'Peserta belum melakukan Skrining Kesehatan' : 'Peserta sudah SRK atau tidak ada indikasi wajib SRK',
            'metadata' => [ 'code' => $code, 'message' => $body['metadata']['message'] ?? '' ],
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'UNKNOWN',
            'message' => 'Tidak dapat memeriksa status SRK saat ini',
        ], 503);
    }
}
```

> Catatan: Endpoint harus menggunakan POST. Pemanggilan langsung tanpa autentikasi atau dengan metode selain POST akan berujung pada `Method Not Allowed` dari server Mobile JKN.

## Integrasi Frontend
Lokasi integrasi: [index.jsx](file:///Users/mistermaster/Documents/trae_projects/edokter/resources/js/pages/reg_periksa/index.jsx)

### Alur
1. Setelah input `nomorkartu` dan `tanggalperiksa` valid, panggil `POST /api/bpjs/cek-srk`.
2. Tampilkan badge: hijau untuk "Sudah SRK", merah untuk "Belum SRK", kuning/abu untuk "Unknown".

### Contoh Kode (React)
```jsx
async function cekSRK({ nomorkartu, nik, nohp, tanggalperiksa }) {
  const res = await fetch('/api/bpjs/cek-srk', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ nomorkartu, nik, nohp, tanggalperiksa })
  });
  return res.json();
}

function StatusSRKBadge({ status, message }) {
  const map = {
    SUDAH_SRK: { text: 'Sudah SRK', className: 'badge badge-success' },
    BELUM_SRK: { text: 'Belum SRK', className: 'badge badge-danger' },
    UNKNOWN: { text: 'Tidak Dapat Memeriksa', className: 'badge badge-secondary' }
  };
  const { text, className } = map[status] || map.UNKNOWN;
  return (
    <div>
      <span className={className} style={{ fontSize: 14 }}>{text}</span>
      {message ? <div className="text-muted" style={{ fontSize: 12 }}>{message}</div> : null}
    </div>
  );
}

// Pemakaian di form pendaftaran
const [srkStatus, setSrkStatus] = React.useState({ status: 'UNKNOWN', message: '' });

async function onCekSRKClick() {
  const payload = { nomorkartu, nik, nohp, tanggalperiksa };
  try {
    const result = await cekSRK(payload);
    setSrkStatus({ status: result.status, message: result.message });
  } catch (e) {
    setSrkStatus({ status: 'UNKNOWN', message: 'Gagal menghubungi server' });
  }
}

// Render
<button className="btn btn-sm btn-outline-info" onClick={onCekSRKClick}>Cek SRK</button>
<StatusSRKBadge status={srkStatus.status} message={srkStatus.message} />
```

## Logging & Observabilitas
- Log setiap request POST ke backend dengan informasi waktu, URL, panjang respons dan status code.
- Contoh log:
```
local.INFO: BPJS MOBILEJKN Request POST Details {"url":"https://apijkn.bpjs-kesehatan.go.id/antreanfktp/antrean/add","timestamp":"1767048656","utc_time":"2025-12-29 22:50:56","data":{...}}
local.INFO: BPJS MOBILEJKN Request POST Headers {"headers":{...}}
local.INFO: BPJS MOBILEJKN Response POST Details {"status_code":200,"response_length":58}
```

## Penanganan Error
- `Method Not Allowed`: pastikan metode adalah POST dan path benar.
- Timeout/koneksi: tampilkan status `UNKNOWN` di frontend dan beri opsi coba lagi.
- Pesan berisi "skrining kesehatan": dianggap `BELUM_SRK`.

## Keamanan
- Jangan expose `X-cons-id`, `X-signature`, `user_key` di frontend.
- Simpan kredensial di `.env` dan akses via `config/services.php`.

## Uji Coba
1. Gunakan data uji pada payload untuk mencoba skenario.
2. Pastikan badge di frontend berubah sesuai respons.
3. Simulasikan gangguan jaringan untuk memverifikasi status `UNKNOWN`.

