<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Http\Livewire\ILP\Pendaftaran;
use App\Exports\PasienExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PasienController extends Controller
{
    public function index()
    {
        // Mengambil data dari database untuk menghindari kueri langsung di tampilan
        $totalPasien = DB::table('pasien')->count();
        $pasienBaru = DB::table('pasien')->orderBy('no_rkm_medis', 'desc')->limit(10)->count();
        $kunjunganHariIni = DB::table('reg_periksa')->whereDate('tgl_registrasi', date('Y-m-d'))->count();
        $pasienBPJS = DB::table('pasien')->where('kd_pj', 'BPJ')->count();

        return view('pasien.index', [
            'totalPasien' => $totalPasien,
            'pasienBaru' => $pasienBaru,
            'kunjunganHariIni' => $kunjunganHariIni,
            'pasienBPJS' => $pasienBPJS
        ]);
    }

    public function edit($no_rkm_medis)
    {
        // Memastikan query langsung ke database tanpa cache
        DB::connection()->disableQueryLog();
        $pasien = DB::table('pasien')
                  ->where('no_rkm_medis', $no_rkm_medis)
                  ->useWritePdo() // Menggunakan koneksi write untuk memastikan data terbaru
                  ->first();
        
        // Ambil data posyandu langsung dari database
        $posyandu = DB::table('data_posyandu')
                    ->useWritePdo()
                    ->get();

        return view('pasien.edit', ['pasien' => $pasien, 'posyandu' => $posyandu]);
    }

    public function searchPasien(Request $request)
    {
        // Ambil parameter pencarian dari request
        $search = $request->get('term') ?? $request->get('q');
        
        // Debug log untuk melihat query yang diterima
        \Log::info('PasienController - searchPasien: Query menerima: ' . $search);
        
        // Gunakan metode searchPasien dari Livewire\ILP\Pendaftaran
        $results = Pendaftaran::searchPasien($search);
        
        // Debug jumlah hasil yang ditemukan
        \Log::info('PasienController - searchPasien: Jumlah hasil ditemukan: ' . count($results));
        
        // Jika request menggunakan parameter 'term' (untuk modal detail)
        if ($request->has('term')) {
            return response()->json($results);
        }
        
        // Format untuk Select2 jika menggunakan parameter 'q'
        $formattedResults = [];
        foreach ($results as $pasien) {
            // Format nomor KTP (tampilkan 4 digit pertama dan 1 digit terakhir)
            $maskedKtp = '-';
            if (!empty($pasien->no_ktp)) {
                $ktpLength = strlen($pasien->no_ktp);
                if ($ktpLength > 5) {
                    $firstFour = substr($pasien->no_ktp, 0, 4);
                    $lastOne = substr($pasien->no_ktp, -1);
                    $masked = str_repeat('x', $ktpLength - 5);
                    $maskedKtp = $firstFour . $masked . $lastOne;
                } else {
                    $maskedKtp = $pasien->no_ktp;
                }
            }
            
            // Format data untuk Select2
            $formattedResults[] = [
                'id' => $pasien->no_rkm_medis,
                'text' => $pasien->no_rkm_medis . ' - ' . $pasien->nm_pasien,
                'no_rkm_medis' => $pasien->no_rkm_medis,
                'nm_pasien' => $pasien->nm_pasien,
                'alamat' => $pasien->alamat,
                'tgl_lahir' => $pasien->tgl_lahir,
                'namakeluarga' => $pasien->namakeluarga,
                'keluarga' => $pasien->keluarga,
                'alamatpj' => $pasien->alamatpj,
                'kd_pj' => $pasien->kd_pj,
                'no_ktp' => $pasien->no_ktp,
                'masked_ktp' => $maskedKtp,
                'kelurahanpj' => $pasien->kelurahanpj,
            ];
        }
        
        // Tambahkan contoh hasil pertama untuk debugging
        if (count($formattedResults) > 0) {
            \Log::info('PasienController - searchPasien: Contoh hasil pertama: ', ['item' => $formattedResults[0]]);
        }
        
        $response = [
            'items' => $formattedResults,
            'total_count' => count($formattedResults)
        ];
        
        return response()->json($response);
    }

    public function storeFromSkrining(Request $request)
    {
        $validator = $this->makeSkriningPasienValidator($request);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $existingPasien = DB::table('pasien')
            ->where('no_ktp', trim((string) $request->no_ktp))
            ->first();

        if ($existingPasien) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIK sudah terdaftar. Gunakan tombol Cari untuk memuat data pasien yang ada.',
                'data' => $this->getPasienLookupData($existingPasien->no_rkm_medis),
            ], 422);
        }

        $lockAcquired = false;

        try {
            DB::beginTransaction();

            $lock = DB::select('SELECT GET_LOCK("pasien_no_rkm_medis_lock", 10) AS l');
            $lockAcquired = $lock && (int)($lock[0]->l ?? 0) === 1;

            if (!$lockAcquired) {
                throw new \Exception('Gagal mendapatkan lock penomoran pasien.');
            }

            $noRkmMedis = $this->generateNoRekamMedis();
            $referenceDefaults = $this->resolvePasienReferenceDefaults($request);
            $data = $this->buildSkriningPasienPayload(
                $request,
                $referenceDefaults,
                now()->format('Y-m-d')
            );
            $data['no_rkm_medis'] = $noRkmMedis;

            DB::table('pasien')->insert($data);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data pasien baru berhasil disimpan.',
                'data' => $this->getPasienLookupData($noRkmMedis),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('PasienController - storeFromSkrining: Gagal menyimpan pasien baru', [
                'error' => $e->getMessage(),
                'payload' => $request->except('_token'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data pasien: ' . $e->getMessage(),
            ], 500);
        } finally {
            if ($lockAcquired) {
                DB::select('SELECT RELEASE_LOCK("pasien_no_rkm_medis_lock") AS r');
            }
        }
    }

    public function updateFromSkrining(Request $request, $noRkmMedis)
    {
        $validator = $this->makeSkriningPasienValidator($request);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $pasien = DB::table('pasien')
            ->where('no_rkm_medis', $noRkmMedis)
            ->first();

        if (!$pasien) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pasien tidak ditemukan.',
            ], 404);
        }

        $duplicateNik = DB::table('pasien')
            ->where('no_ktp', trim((string) $request->no_ktp))
            ->where('no_rkm_medis', '!=', $noRkmMedis)
            ->exists();

        if ($duplicateNik) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIK sudah dipakai pasien lain.',
            ], 422);
        }

        try {
            $referenceDefaults = $this->resolvePasienReferenceDefaults($request);
            $data = $this->buildSkriningPasienPayload(
                $request,
                $referenceDefaults,
                $pasien->tgl_daftar ?: now()->format('Y-m-d')
            );

            DB::table('pasien')
                ->where('no_rkm_medis', $noRkmMedis)
                ->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data pasien berhasil diperbarui.',
                'data' => $this->getPasienLookupData($noRkmMedis),
            ]);
        } catch (\Exception $e) {
            \Log::error('PasienController - updateFromSkrining: Gagal memperbarui pasien', [
                'no_rkm_medis' => $noRkmMedis,
                'error' => $e->getMessage(),
                'payload' => $request->except('_token'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data pasien: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $no_rkm_medis)
    {
        // Log request untuk debugging
        \Log::info('PasienController - update: Menerima permintaan update', [
            'no_rkm_medis' => $no_rkm_medis,
            'method' => $request->method()
        ]);
        
        // Panggil metode simpan untuk menangani pembaruan data
        return $this->simpan($request, $no_rkm_medis);
    }

    public function simpan(Request $request, $no_rkm_medis)
    {
        // dd($request->all());
        $this->validate($request, [
            'nm_pasien' => 'required',
            'no_ktp' => 'required',
            'no_peserta' => 'required',
            'no_tlp' => 'required',
            'tgl_lahir' => 'required',
            'alamat' => 'required',
            'stts_nikah' => 'required',
            'status' => 'required',
            'data_posyandu' => 'required',
            'no_kk' => 'required',
        ], [
            'nm_pasien.required' => 'Nama Pasien tidak boleh kosong',
            'no_ktp.required' => 'No. KTP/SIM tidak boleh kosong',
            'no_peserta.required' => 'No. Peserta tidak boleh kosong',
            'no_tlp.required' => 'No. Telepon tidak boleh kosong',
            'tgl_lahir.required' => 'Tanggal Lahir tidak boleh kosong',
            'alamat.required' => 'Alamat tidak boleh kosong',
            'stts_nikah.required' => 'Status Nikah tidak boleh kosong',
            'status.required' => 'Status tidak boleh kosong',
            'data_posyandu.required' => 'Posyandu tidak boleh kosong',
            'no_kk.required' => 'No. KK tidak boleh kosong',
        ]);

        try {
            // Log data sebelum update
            \Log::info('PasienController - simpan: Memperbarui data pasien', [
                'no_rkm_medis' => $no_rkm_medis,
                'data' => $request->except('_token', '_method')
            ]);
            
            DB::table('pasien')->where('no_rkm_medis', $no_rkm_medis)->update([
                'nm_pasien' => $request->nm_pasien,
                'no_ktp' => $request->no_ktp,
                'no_peserta' => $request->no_peserta,
                'no_tlp' => $request->no_tlp,
                'tgl_lahir' => $request->tgl_lahir,
                'umur' => $this->rubahUmur($request->tgl_lahir),
                'alamat' => $request->alamat,
                'stts_nikah' => $request->stts_nikah,
                'status' => $request->status,
                'data_posyandu' => $request->data_posyandu,
                'no_kk' => $request->no_kk,
            ]);

            // Log sukses update
            \Log::info('PasienController - simpan: Berhasil memperbarui data pasien', [
                'no_rkm_medis' => $no_rkm_medis
            ]);

            return redirect('/data-pasien')->with('success', 'Data Pasien berhasil diperbarui');
        } catch (\Exception $e) {
            // Log error
            \Log::error('PasienController - simpan: Gagal memperbarui data pasien', [
                'no_rkm_medis' => $no_rkm_medis,
                'error' => $e->getMessage()
            ]);
            
            return redirect('/data-pasien')->with('error', 'Data Pasien gagal diperbarui: ' . $e->getMessage());
        }
    }

    public function rubahUmur($tgl_lahir)
    {
        $tgl_lahir = Carbon::parse($tgl_lahir);
        return $tgl_lahir->diff(Carbon::now())->format('%y Th %m Bl %d Hr');
    }

    private function generateNoRekamMedis(): string
    {
        $lastRecord = DB::table('pasien')
            ->orderByRaw('CAST(no_rkm_medis AS UNSIGNED) DESC')
            ->first();

        $lastNumber = $lastRecord ? (int) $lastRecord->no_rkm_medis : 0;

        return str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    private function makeSkriningPasienValidator(Request $request)
    {
        return Validator::make($request->all(), [
            'no_ktp' => 'required|string|max:20',
            'nm_pasien' => 'required|string|max:40',
            'jk' => 'required|in:L,P',
            'tgl_lahir' => 'required|date',
            'nm_ibu' => 'required|string|max:40',
            'tmp_lahir' => 'nullable|string|max:15',
            'no_tlp' => 'nullable|string|max:40',
            'alamat' => 'nullable|string|max:200',
            'stts_nikah' => 'nullable|string|max:20',
            'agama' => 'nullable|string|max:12',
            'pnd' => 'nullable|string|max:20',
            'keluarga' => 'nullable|string|max:20',
            'namakeluarga' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:20',
            'data_posyandu' => 'nullable|string|max:70',
            'kd_pj' => 'nullable|string|max:3',
            'no_peserta' => 'nullable|string|max:25',
            'perusahaan_pasien' => 'nullable|string|max:8',
            'pekerjaanpj' => 'nullable|string|max:35',
            'alamatpj' => 'nullable|string|max:100',
            'kelurahanpj' => 'nullable|string|max:60',
            'kecamatanpj' => 'nullable|string|max:60',
            'kabupatenpj' => 'nullable|string|max:60',
            'propinsipj' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:50',
            'pekerjaan' => 'nullable|string|max:60',
            'nip' => 'nullable|string|max:30',
            'status' => 'nullable|string|max:20',
        ], [
            'no_ktp.required' => 'NIK wajib diisi.',
            'nm_pasien.required' => 'Nama lengkap wajib diisi.',
            'jk.required' => 'Jenis kelamin wajib dipilih.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'nm_ibu.required' => 'Nama ibu wajib diisi.',
        ]);
    }

    private function buildSkriningPasienPayload(Request $request, array $referenceDefaults, string $tglDaftar): array
    {
        return [
            'nm_pasien' => strtoupper(trim((string) $request->nm_pasien)),
            'no_ktp' => trim((string) $request->no_ktp),
            'jk' => $request->jk,
            'tmp_lahir' => strtoupper(trim((string) ($request->tmp_lahir ?: '-'))),
            'tgl_lahir' => $request->tgl_lahir,
            'nm_ibu' => strtoupper(trim((string) $request->nm_ibu)),
            'alamat' => strtoupper(trim((string) ($request->alamat ?: '-'))),
            'gol_darah' => $request->gol_darah ?: '-',
            'pekerjaan' => strtoupper(trim((string) ($request->pekerjaan ?: 'SWASTA'))),
            'stts_nikah' => $request->stts_nikah ?: 'MENIKAH',
            'agama' => strtoupper(trim((string) ($request->agama ?: 'ISLAM'))),
            'tgl_daftar' => $tglDaftar,
            'no_tlp' => trim((string) ($request->no_tlp ?: '081')),
            'umur' => $this->rubahUmur($request->tgl_lahir),
            'pnd' => $request->pnd ?: '-',
            'keluarga' => $request->keluarga ?: 'DIRI SENDIRI',
            'namakeluarga' => strtoupper(trim((string) ($request->namakeluarga ?: $request->nm_pasien))),
            'kd_pj' => $referenceDefaults['kd_pj'],
            'no_peserta' => trim((string) ($request->no_peserta ?: '0000')),
            'kd_kel' => $referenceDefaults['kd_kel'],
            'kd_kec' => $referenceDefaults['kd_kec'],
            'kd_kab' => $referenceDefaults['kd_kab'],
            'pekerjaanpj' => strtoupper(trim((string) ($request->pekerjaanpj ?: 'SWASTA'))),
            'alamatpj' => strtoupper(trim((string) ($request->alamatpj ?: $request->alamat ?: '-'))),
            'kelurahanpj' => strtoupper(trim((string) ($request->kelurahanpj ?: 'KELURAHAN'))),
            'kecamatanpj' => strtoupper(trim((string) ($request->kecamatanpj ?: 'KECAMATAN'))),
            'kabupatenpj' => strtoupper(trim((string) ($request->kabupatenpj ?: 'KABUPATEN'))),
            'perusahaan_pasien' => $referenceDefaults['perusahaan_pasien'],
            'suku_bangsa' => $referenceDefaults['suku_bangsa'],
            'bahasa_pasien' => $referenceDefaults['bahasa_pasien'],
            'cacat_fisik' => $referenceDefaults['cacat_fisik'],
            'email' => strtolower(trim((string) ($request->email ?: 'puskesmaskerjo@gmail.com'))),
            'nip' => trim((string) ($request->nip ?: '0')),
            'kd_prop' => $referenceDefaults['kd_prop'],
            'propinsipj' => strtoupper(trim((string) ($request->propinsipj ?: 'PROPINSI'))),
            'no_kk' => trim((string) ($request->no_kk ?: '0')),
            'data_posyandu' => trim((string) ($request->data_posyandu ?: '')) ?: null,
            'status' => $request->status ?: 'Kepala Keluarga',
        ];
    }

    private function resolvePasienReferenceDefaults(Request $request): array
    {
        return [
            'kd_prop' => $this->resolveReferenceValue($request->kd_prop, 'propinsi', 'kd_prop'),
            'kd_kab' => $this->resolveReferenceValue($request->kd_kab, 'kabupaten', 'kd_kab'),
            'kd_kec' => $this->resolveReferenceValue($request->kd_kec, 'kecamatan', 'kd_kec'),
            'kd_kel' => $this->resolveReferenceValue($request->kd_kel, 'kelurahan', 'kd_kel'),
            'kd_pj' => $this->resolveReferenceValue($request->kd_pj, 'penjab', 'kd_pj'),
            'perusahaan_pasien' => $this->resolveReferenceValue($request->perusahaan_pasien, 'perusahaan_pasien', 'kode_perusahaan', '-'),
            'suku_bangsa' => $this->resolveReferenceValue($request->suku_bangsa, 'suku_bangsa', 'id', 5),
            'bahasa_pasien' => $this->resolveReferenceValue($request->bahasa_pasien, 'bahasa_pasien', 'id', 11),
            'cacat_fisik' => $this->resolveReferenceValue($request->cacat_fisik, 'cacat_fisik', 'id', 5),
        ];
    }

    private function resolveReferenceValue($requestedValue, string $table, string $column, $preferredDefault = null)
    {
        $requestedValue = is_string($requestedValue) ? trim($requestedValue) : $requestedValue;

        if ($requestedValue !== null && $requestedValue !== '') {
            $exists = DB::table($table)
                ->where($column, $requestedValue)
                ->exists();

            if ($exists) {
                return $requestedValue;
            }
        }

        if ($preferredDefault !== null) {
            $preferredExists = DB::table($table)
                ->where($column, $preferredDefault)
                ->exists();

            if ($preferredExists) {
                return $preferredDefault;
            }
        }

        return DB::table($table)
            ->orderBy($column)
            ->value($column);
    }

    private function getPasienLookupData(string $noRkmMedis)
    {
        return DB::table('pasien')
            ->leftJoin('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
            ->leftJoin('data_posyandu', 'pasien.data_posyandu', '=', 'data_posyandu.kode_posyandu')
            ->where('pasien.no_rkm_medis', $noRkmMedis)
            ->select(
                'pasien.*',
                DB::raw('kelurahan.nm_kel as nm_kel'),
                DB::raw('data_posyandu.nama_posyandu as nama_posyandu'),
                DB::raw('pasien.data_posyandu as kode_posyandu')
            )
            ->first();
    }

    /**
     * Mendapatkan detail pasien untuk PCare BPJS
     * Digunakan oleh form PCare BPJS
     */
    public function getDetailByRekamMedis($no_rkm_medis, Request $request)
    {
        try {
            // Bersihkan input untuk keamanan
            $no_rkm_medis = trim($no_rkm_medis);
            
            // Catat parameter lain yang mungkin dikirim
            $timestamp = $request->input('ts', '');
            $clearCache = $request->input('clear_cache', 'false');
            $userAgent = $request->header('User-Agent', 'Unknown');
            
            // Log lengkap untuk debugging
            \Log::info('PasienController - getDetailByRekamMedis: Request diterima', [
                'no_rkm_medis' => $no_rkm_medis,
                'timestamp' => $timestamp,
                'clear_cache' => $clearCache,
                'ip' => $request->ip(),
                'user_agent' => $userAgent,
                'referrer' => $request->header('Referer', 'Unknown'),
                'request_id' => uniqid('pasien_req_')
            ]);
            
            // Cek apakah ada pasien yang registrasi hari ini
            $today = date('Y-m-d');
            
            // Query pasien yang registrasi hari ini dulu untuk data terbaru
            $registrasiHariIni = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
                ->where('reg_periksa.tgl_registrasi', $today)
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.no_ktp',
                    'pasien.jk',
                    'pasien.tmp_lahir',
                    'pasien.tgl_lahir',
                    'pasien.no_peserta', // Nomor BPJS dari tabel pasien
                    'reg_periksa.kd_pj',
                    'penjab.png_jawab',
                    'pasien.no_tlp',
                    'pasien.alamat'
                )
                ->orderBy('reg_periksa.jam_reg', 'desc')
                ->first();
                
            // Jika ada registrasi hari ini, gunakan data tersebut
            if ($registrasiHariIni) {
                \Log::info('PasienController - getDetailByRekamMedis: Ditemukan registrasi hari ini', [
                    'no_rkm_medis' => $no_rkm_medis,
                    'no_rawat' => $registrasiHariIni->no_rawat,
                    'kd_pj' => $registrasiHariIni->kd_pj,
                    'png_jawab' => $registrasiHariIni->png_jawab,
                    'timestamp' => now()->format('Y-m-d H:i:s.u')
                ]);
                
                $pasien = $registrasiHariIni;
            } else {
                // Jika tidak ada registrasi hari ini, cek registrasi terakhir
                $registrasiTerakhir = DB::table('reg_periksa')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                    ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
                    ->select(
                        'reg_periksa.no_rawat',
                        'reg_periksa.no_rkm_medis',
                        'pasien.nm_pasien',
                        'pasien.no_ktp',
                        'pasien.jk',
                        'pasien.tmp_lahir',
                        'pasien.tgl_lahir',
                        'pasien.no_peserta', // Nomor BPJS dari tabel pasien
                        'reg_periksa.kd_pj',
                        'penjab.png_jawab',
                        'pasien.no_tlp',
                        'pasien.alamat',
                        'reg_periksa.tgl_registrasi'
                    )
                    ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                    ->orderBy('reg_periksa.jam_reg', 'desc')
                    ->first();
                
                // Jika ada registrasi terakhir, gunakan data tersebut
                if ($registrasiTerakhir) {
                    \Log::info('PasienController - getDetailByRekamMedis: Ditemukan registrasi terakhir', [
                        'no_rkm_medis' => $no_rkm_medis,
                        'no_rawat' => $registrasiTerakhir->no_rawat,
                        'tgl_registrasi' => $registrasiTerakhir->tgl_registrasi,
                        'kd_pj' => $registrasiTerakhir->kd_pj,
                        'png_jawab' => $registrasiTerakhir->png_jawab
                    ]);
                    
                    $pasien = $registrasiTerakhir;
                } else {
                    // Jika tidak ada registrasi sama sekali, ambil dari data pasien saja
                    \Log::info('PasienController - getDetailByRekamMedis: Tidak ditemukan registrasi, menggunakan data master pasien');
                    
                    $pasien = DB::table('pasien')
                        ->select(
                            DB::raw("'' as no_rawat"),
                            'no_rkm_medis',
                            'nm_pasien',
                            'no_ktp',
                            'jk',
                            'tmp_lahir',
                            'tgl_lahir',
                            'no_peserta',
                            DB::raw("'' as kd_pj"),
                            DB::raw("'' as png_jawab"),
                            'no_tlp',
                            'alamat'
                        )
                        ->where('no_rkm_medis', $no_rkm_medis)
                        ->first();
                }
            }
            
            if (!$pasien) {
                \Log::warning('PasienController - getDetailByRekamMedis: Pasien tidak ditemukan dengan no_rkm_medis: ' . $no_rkm_medis);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pasien tidak ditemukan',
                    'data' => null
                ], 404);
            }
            
            // Verifikasi ulang nomor rekam medis untuk memastikan kecocokan data
            if ($pasien->no_rkm_medis !== $no_rkm_medis) {
                \Log::error('PasienController - getDetailByRekamMedis: Ketidakcocokan data', [
                    'requested_rm' => $no_rkm_medis,
                    'returned_rm' => $pasien->no_rkm_medis,
                    'timestamp' => now()->format('Y-m-d H:i:s.u')
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ketidakcocokan data pasien terdeteksi',
                    'data' => null
                ], 409);
            }
            
            // Hitung umur
            $tglLahir = Carbon::parse($pasien->tgl_lahir);
            $now = Carbon::now();
            $umurTahun = $tglLahir->diffInYears($now);
            $umurBulan = $tglLahir->copy()->addYears($umurTahun)->diffInMonths($now);
            $umurHari = $tglLahir->copy()->addYears($umurTahun)->addMonths($umurBulan)->diffInDays($now);
            
            // Tambahkan umur ke objek pasien
            $pasien->umur = "$umurTahun tahun, $umurBulan bulan, $umurHari hari";
            
            \Log::info('PasienController - getDetailByRekamMedis: Pasien ditemukan', [
                'no_rkm_medis' => $pasien->no_rkm_medis,
                'nama' => $pasien->nm_pasien,
                'no_peserta' => $pasien->no_peserta,
                'jenis_peserta' => $pasien->png_jawab ?? 'Tidak ada',
                'timestamp' => now()->format('Y-m-d H:i:s.u'),
                'response_id' => uniqid('pasien_res_')
            ]);
            
            // Set header anti-cache yang lebih kuat untuk memastikan data selalu fresh
            return response()->json([
                'status' => 'success',
                'message' => 'Data pasien ditemukan',
                'data' => $pasien,
                'timestamp' => now()->timestamp, // Tambahkan timestamp di respons
                'request_rm' => $no_rkm_medis // Echo back requested RM untuk verifikasi client
            ])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Response-Time', now()->format('Y-m-d H:i:s.u'))
            ->header('X-Patient-RM', $no_rkm_medis);
            
        } catch (\Exception $e) {
            \Log::error('PasienController - getDetailByRekamMedis: Error', [
                'no_rkm_medis' => $no_rkm_medis,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $search = [
            'name' => $request->input('name'),
            'rm' => $request->input('rm'),
            'address' => $request->input('address')
        ];
        
        return Excel::download(new PasienExport($search), 'data-pasien-' . date('Y-m-d') . '.xlsx');
    }
    
    public function cetak(Request $request)
    {
        $search = [
            'name' => $request->input('name'),
            'rm' => $request->input('rm'),
            'address' => $request->input('address')
        ];
        
        $query = DB::table('pasien');
        
        if (!empty($search['name'])) {
            $query->where('nm_pasien', 'like', '%' . $search['name'] . '%');
        }
        
        if (!empty($search['rm'])) {
            $query->where('no_rkm_medis', 'like', '%' . $search['rm'] . '%');
        }
        
        if (!empty($search['address'])) {
            $query->where('alamat', 'like', '%' . $search['address'] . '%');
        }
        
        // Batasi jumlah data yang diambil untuk menghindari memory exhausted
        $pasien = $query->orderBy('tgl_daftar', 'desc')->limit(100)->get();
        
        // Hanya ambil kolom yang diperlukan untuk mengurangi penggunaan memori
        $pasienData = $pasien->map(function($item) {
            return [
                'no_rkm_medis' => $item->no_rkm_medis,
                'nm_pasien' => $item->nm_pasien,
                'no_ktp' => $item->no_ktp,
                'tgl_lahir' => $item->tgl_lahir,
                'alamat' => $item->alamat,
                'status' => $item->status
            ];
        });
        
        // Tingkatkan batas memori untuk proses PDF
        ini_set('memory_limit', '512M');
        
        $pdf = PDF::loadView('pasien.cetak', [
            'pasien' => $pasienData,
            'tanggal' => date('d-m-Y'),
            'filter' => $search,
            'user' => auth()->user() ?? (object)['name' => 'Admin'] // Memberikan fallback jika user tidak ada
        ]);
        
        // Mengatur ukuran kertas dan orientasi
        $pdf->setPaper('a4', 'landscape');
        
        // Opsi untuk mengoptimalkan PDF
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);
        
        return $pdf->stream('data-pasien-' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Menampilkan detail pasien berdasarkan nomor rekam medis
     * 
     * @param string $no_rkm_medis
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($no_rkm_medis)
    {
        try {
            // Log request untuk debugging
            \Log::info('PasienController - show: Mengambil detail pasien', [
                'no_rkm_medis' => $no_rkm_medis
            ]);
            
            // Memastikan query langsung ke database tanpa cache
            DB::connection()->disableQueryLog();
            $pasien = DB::table('pasien')
                     ->where('no_rkm_medis', $no_rkm_medis)
                     ->useWritePdo() // Menggunakan koneksi write untuk memastikan data terbaru
                     ->first();
            
            if (!$pasien) {
                // Log jika pasien tidak ditemukan
                \Log::warning('PasienController - show: Pasien tidak ditemukan', [
                    'no_rkm_medis' => $no_rkm_medis
                ]);
                
                return response()->json([
                    'error' => true,
                    'message' => 'Data pasien tidak ditemukan'
                ], 404);
            }
            
            // Log sukses
            \Log::info('PasienController - show: Berhasil mengambil detail pasien', [
                'no_rkm_medis' => $no_rkm_medis
            ]);
            
            // Menambahkan header no-cache
            return response()->json($pasien)
                   ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                   ->header('Pragma', 'no-cache')
                   ->header('Expires', '0');
        } catch (\Exception $e) {
            // Log error
            \Log::error('PasienController - show: Gagal mengambil detail pasien', [
                'no_rkm_medis' => $no_rkm_medis,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Gagal mengambil data pasien: ' . $e->getMessage()
            ], 500);
        }
    }
}
