<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class RegPeriksaController extends Controller
{
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    public function create($no_rkm_medis)
    {
        try {
            \Log::info('Membuka form registrasi periksa untuk pasien: ' . $no_rkm_medis);
            
            // Ambil data pasien dengan join ke penjab
            $pasien = DB::table('pasien')
                ->leftJoin('penjab', 'pasien.kd_pj', '=', 'penjab.kd_pj')
                ->leftJoin('data_posyandu', 'pasien.data_posyandu', '=', 'data_posyandu.nama_posyandu')
                ->select(
                    'pasien.*', 
                    'penjab.png_jawab as penjab_pasien',
                    'data_posyandu.nama_posyandu',
                    'data_posyandu.desa'
                )
                ->where('no_rkm_medis', $no_rkm_medis)
                ->first();

            if (!$pasien) {
                \Log::error('Pasien tidak ditemukan: ' . $no_rkm_medis);
                return redirect()->back()->with('error', 'Data pasien tidak ditemukan');
            }
            
            // Ambil data dokter
            $dokter = DB::table('dokter')->get();
            
            // Ambil data poliklinik
            $poliklinik = DB::table('poliklinik')->get();
            
            // Ambil data penjamin
            $penjab = DB::table('penjab')->get();

            // Ambil data posyandu
            $posyandu = DB::table('data_posyandu')
                ->select('nama_posyandu', 'desa')
                ->orderBy('nama_posyandu')
                ->get();
            
            return view('regperiksa.create', [
                'pasien' => $pasien,
                'dokter' => $dokter,
                'poliklinik' => $poliklinik,
                'penjab' => $penjab,
                'posyandu' => $posyandu
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada create registrasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor rawat (no_rawat) dengan format YYYY/MM/DD/000000
     * @param string $tgl_registrasi Tanggal registrasi dalam format Y-m-d
     * @return string Nomor rawat yang dihasilkan
     */
    private function generateNoRawat($tgl_registrasi)
    {
        // Format tanggal menjadi Y/m/d untuk nomor rawat
        $today = date('Y/m/d', strtotime($tgl_registrasi));
        
        // Ambil nomor urut terakhir
        $lastRawat = DB::table('reg_periksa')
            ->where('tgl_registrasi', $tgl_registrasi)
            ->orderByRaw('CONVERT(RIGHT(no_rawat, 6), UNSIGNED) DESC')
            ->first();

        if ($lastRawat) {
            \Log::info('Last rawat found: ' . $lastRawat->no_rawat);
            // Ambil 6 digit terakhir
            $lastNumber = (int) substr($lastRawat->no_rawat, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            \Log::info('No last rawat found, using 1');
            $nextNumber = 1;
        }

        $no_rawat = $today . '/' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        \Log::info('Nomor rawat yang dibuat: ' . $no_rawat);
        
        return $no_rawat;
    }

    public function store(Request $request)
    {
        \Log::info('Mencoba menyimpan registrasi periksa', $request->all());
        
        // Aktifkan query log untuk debugging
        DB::enableQueryLog();
        
        try {
            // Cek apakah request berupa JSON atau form-data
            $input = $request->json()->all();
            if (empty($input)) {
                $input = $request->all();
            }
            
            \Log::info('Data input yang diterima:', $input);
            
            // Validasi input yang diperlukan
            if (empty($input['no_reg']) || empty($input['kd_dokter']) || 
                empty($input['no_rkm_medis']) || empty($input['kd_poli']) || 
                empty($input['kd_pj'])) {
                
                \Log::error('Validasi gagal', $input);
                return response()->json([
                    'success' => false,
                    'message' => 'Data wajib tidak lengkap: nomor registrasi, dokter, pasien, poliklinik, dan cara bayar diperlukan'
                ], 422);
            }

            // Generate nomor rawat dengan format: tahun/bulan/tanggal/nomor urut
            $tgl_registrasi = $input['tgl_registrasi'] ?? date('Y-m-d');
            $no_rawat = $this->generateNoRawat($tgl_registrasi);
            
            // Siapkan data yang akan disimpan
            $data = [
                'no_reg' => $input['no_reg'],
                'no_rawat' => $no_rawat,
                'tgl_registrasi' => $tgl_registrasi,
                'jam_reg' => date('H:i:s'),
                'kd_dokter' => $input['kd_dokter'],
                'no_rkm_medis' => $input['no_rkm_medis'],
                'kd_poli' => $input['kd_poli'],
                'p_jawab' => $input['p_jawab'] ?? 'PASIEN',
                'almt_pj' => $input['almt_pj'] ?? '-',
                'hubunganpj' => $input['hubunganpj'] ?? 'DIRI SENDIRI',
                'biaya_reg' => $input['biaya_reg'] ?? 0,
                'stts' => $input['stts'] ?? 'Belum',
                'stts_daftar' => $input['stts_daftar'] ?? 'Lama',
                'status_lanjut' => $input['status_lanjut'] ?? 'Ralan',
                'kd_pj' => $input['kd_pj'],
                'umurdaftar' => $input['umurdaftar'] ?? 0,
                'sttsumur' => $input['sttsumur'] ?? 'Th',
                'status_bayar' => 'Belum Bayar',
                'status_poli' => 'Lama'
            ];

            \Log::info('Data yang akan disimpan:', $data);

            DB::beginTransaction();
            
            try {
                // Cek struktur tabel untuk debug
                $tableColumns = DB::getSchemaBuilder()->getColumnListing('reg_periksa');
                \Log::info('Struktur kolom tabel reg_periksa:', $tableColumns);
                
                DB::table('reg_periksa')->insert($data);
                
                // Log query yang dieksekusi
                \Log::info('Query yang dieksekusi:', DB::getQueryLog());
                
                DB::commit();
                
                \Log::info('Registrasi berhasil disimpan untuk no_rawat: ' . $no_rawat);
                
                // Update nomor registrasi di cache/database
                // Kita sekaligus menambahkan catatan untuk sinkronisasi nomor registrasi
                try {
                    $this->updateLastRegNumber($input['kd_dokter'], $tgl_registrasi, $input['no_reg'], $input['kd_poli']);
                } catch (\Exception $e) {
                    \Log::warning('Gagal update cache nomor registrasi: ' . $e->getMessage());
                }
                
                // Verifikasi data tersimpan
                $savedRecord = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
                if ($savedRecord) {
                    \Log::info('Verifikasi data tersimpan berhasil: ' . json_encode($savedRecord));
                } else {
                    \Log::warning('Verifikasi data tersimpan gagal, data tidak ditemukan dengan no_rawat: ' . $no_rawat);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi berhasil disimpan',
                    'no_rawat' => $no_rawat
                ]);
            } catch (\Exception $insertErr) {
                DB::rollBack();
                \Log::error('Error saat insert: ' . $insertErr->getMessage());
                \Log::error('SQL Error: ' . $insertErr->getMessage());
                \Log::error('Query yang dieksekusi:', DB::getQueryLog());
                throw $insertErr;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error menyimpan registrasi: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Query yang dieksekusi:', DB::getQueryLog());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update nomor registrasi terakhir untuk dokter dan tanggal tertentu
     */
    private function updateLastRegNumber($kd_dokter, $tgl_registrasi, $no_reg, $kd_poli = null)
    {
        \Log::info("Menyimpan nomor registrasi terakhir: $no_reg untuk dokter: $kd_dokter tanggal: $tgl_registrasi" . ($kd_poli ? " poli: $kd_poli" : ""));
        
        // Menyimpan ke cache atau pencatatan lain jika diperlukan di masa depan
        $cacheKey = $kd_poli 
            ? "last_reg_number_{$kd_dokter}_{$kd_poli}_{$tgl_registrasi}" 
            : "last_reg_number_{$kd_dokter}_{$tgl_registrasi}";
        \Cache::put($cacheKey, $no_reg, now()->addDay());
        
        return true;
    }

    /**
     * Generate nomor registrasi pasien berikutnya berdasarkan dokter dan poli
     */
    public function generateNoReg($kd_dokter = null, $tgl_registrasi = null)
    {
        try {
            \Log::info('Memulai generateNoReg');
            // Ambil dari request jika tidak disediakan
            if (!$kd_dokter) {
                $kd_dokter = request()->input('kd_dokter');
            }
            if (!$tgl_registrasi) {
                $tgl_registrasi = request()->input('tgl_registrasi', date('Y-m-d'));
            }
            
            $kd_poli = request()->input('kd_poli');
            
            \Log::info("Generating nomor registrasi untuk dokter: $kd_dokter, tanggal: $tgl_registrasi" . 
                ($kd_poli ? ", poli: $kd_poli" : ""));
            
            // Lock untuk mencegah race condition saat generate nomor
            $lockKey = "lock_no_reg_{$kd_dokter}_{$tgl_registrasi}" . ($kd_poli ? "_{$kd_poli}" : "");
            $isLocked = \Cache::add($lockKey, true, 30); // Lock selama 30 detik maksimal
            
            if (!$isLocked) {
                \Log::warning("Menunggu lock untuk generate nomor registrasi");
                // Tunggu sampai 3 detik untuk mendapatkan lock
                $startTime = microtime(true);
                while (!$isLocked && microtime(true) - $startTime < 3) {
                    usleep(100000); // Tunggu 100ms
                    $isLocked = \Cache::add($lockKey, true, 30);
                }
            }
            
            try {
                $maxRegNum = 0;
                
                // 1. Cek di history_noreg jika ada (ini biasanya yang paling update)
                if (Schema::hasTable('history_noreg')) {
                    $historyQuery = DB::table('history_noreg')
                        ->where('tgl_registrasi', $tgl_registrasi)
                        ->where('kd_dokter', $kd_dokter);
                        
                    if ($kd_poli) {
                        $historyQuery->where(function ($query) use ($kd_poli) {
                            $query->where('kd_poli', $kd_poli)
                                  ->orWhereNull('kd_poli'); // Untuk kompatibilitas dengan data lama
                        });
                    }
                    
                    $historyRegs = $historyQuery->orderBy('created_at', 'desc')
                        ->limit(100) // Batasi jumlah data yang diambil
                        ->get(['no_reg']);
                    
                    foreach ($historyRegs as $reg) {
                        $regNum = intval(ltrim($reg->no_reg, '0'));
                        if ($regNum > $maxRegNum) {
                            $maxRegNum = $regNum;
                        }
                    }
                    
                    if ($maxRegNum > 0) {
                        \Log::info("Max reg number dari history_noreg: $maxRegNum");
                    }
                }
                
                // 2. Cek di reg_periksa
                $regPeriksaQuery = DB::table('reg_periksa')
                    ->where('tgl_registrasi', $tgl_registrasi)
                    ->where('kd_dokter', $kd_dokter);
                    
                if ($kd_poli) {
                    $regPeriksaQuery->where('kd_poli', $kd_poli);
                    \Log::info("Filter tambahan dengan poli: $kd_poli");
                }
                    
                $regPeriksaData = $regPeriksaQuery->get(['no_reg']);
                foreach ($regPeriksaData as $item) {
                    $regNum = intval(ltrim($item->no_reg, '0'));
                    if ($regNum > $maxRegNum) {
                        $maxRegNum = $regNum;
                    }
                }
                
                if (count($regPeriksaData) > 0) {
                    \Log::info("Max reg number dari reg_periksa: $maxRegNum");
                }
                
                // 3. Cek di booking_registrasi jika ada
                if (Schema::hasTable('booking_registrasi')) {
                    $bookingQuery = DB::table('booking_registrasi')
                        ->where('tanggal_periksa', $tgl_registrasi)
                        ->where('kd_dokter', $kd_dokter);
                        
                    if ($kd_poli) {
                        $bookingQuery->where('kd_poli', $kd_poli);
                    }
                    
                    $bookingData = $bookingQuery->get(['no_reg']);
                    
                    foreach ($bookingData as $item) {
                        $regNum = intval(ltrim($item->no_reg, '0'));
                        if ($regNum > $maxRegNum) {
                            $maxRegNum = $regNum;
                        }
                    }
                    
                    if (count($bookingData) > 0) {
                        \Log::info("Max reg number termasuk booking_registrasi: $maxRegNum");
                    }
                }
                
                // 4. Tentukan nomor berikutnya
                $nextRegNum = $maxRegNum + 1;
                $no_reg = str_pad($nextRegNum, 3, '0', STR_PAD_LEFT);
                \Log::info("Nomor registrasi berikutnya: $no_reg");
                
                // 5. Verifikasi nomor registrasi tidak duplikat
                $attempts = 0;
                $isUnique = false;
                
                while (!$isUnique && $attempts < 10) {
                    $existingQuery = DB::table('reg_periksa')
                        ->where('tgl_registrasi', $tgl_registrasi)
                        ->where('kd_dokter', $kd_dokter)
                        ->where('no_reg', $no_reg);
                        
                    if ($kd_poli) {
                        $existingQuery->where('kd_poli', $kd_poli);
                    }
                    
                    $existingReg = $existingQuery->exists();
                    
                    if (!$existingReg) {
                        // Cek juga di booking_registrasi jika ada
                        if (Schema::hasTable('booking_registrasi')) {
                            $bookingExistQuery = DB::table('booking_registrasi')
                                ->where('tanggal_periksa', $tgl_registrasi)
                                ->where('kd_dokter', $kd_dokter)
                                ->where('no_reg', $no_reg);
                                
                            if ($kd_poli) {
                                $bookingExistQuery->where('kd_poli', $kd_poli);
                            }
                            
                            $existingBooking = $bookingExistQuery->exists();
                            
                            if (!$existingBooking) {
                                $isUnique = true;
                            } else {
                                $attempts++;
                                $nextRegNum++;
                                $no_reg = str_pad($nextRegNum, 3, '0', STR_PAD_LEFT);
                                \Log::info("Nomor registrasi konflik dengan booking, mencoba: $no_reg");
                            }
                        } else {
                            $isUnique = true;
                        }
                    } else {
                        $attempts++;
                        $nextRegNum++;
                        $no_reg = str_pad($nextRegNum, 3, '0', STR_PAD_LEFT);
                        \Log::info("Nomor registrasi konflik dengan reg_periksa, mencoba: $no_reg");
                    }
                }
                
                if (!$isUnique) {
                    \Log::warning("Gagal mendapatkan nomor registrasi unik setelah 10 percobaan. Menggunakan: $no_reg");
                }
                
                // 6. Update last reg number dan simpan dalam history
                $this->updateLastRegNumber($kd_dokter, $tgl_registrasi, $no_reg, $kd_poli);
                $this->logNoReg($kd_dokter, $tgl_registrasi, $no_reg, 'normal', $kd_poli);
                
                \Log::info("Nomor registrasi final: $no_reg");
                
                return response()->json([
                    'success' => true,
                    'no_reg' => $no_reg,
                    'message' => 'Nomor registrasi berhasil dibuat'
                ]);
            } finally {
                // Lepaskan lock setelah selesai
                if ($isLocked) {
                    \Cache::forget($lockKey);
                    \Log::info("Lock untuk generate nomor registrasi dilepaskan");
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error dalam generateNoReg: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat nomor registrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fungsi untuk logging nomor registrasi ke tabel history_noreg (jika ada)
     */
    private function logNoReg($kd_dokter, $tgl_registrasi, $no_reg, $method = 'normal', $kd_poli = null)
    {
        try {
            // Cek apakah tabel history_noreg ada
            $hasTable = Schema::hasTable('history_noreg');
            if (!$hasTable) {
                \Log::info('Tabel history_noreg tidak ada, mencoba membuat tabel');
                Schema::create('history_noreg', function ($table) {
                    $table->id();
                    $table->string('kd_dokter', 20);
                    $table->date('tgl_registrasi');
                    $table->string('no_reg', 10);
                    $table->string('kd_poli', 20)->nullable();
                    $table->string('method', 20)->default('normal');
                    $table->timestamp('created_at')->useCurrent();
                    $table->string('created_by', 50)->nullable();
                });
                \Log::info('Tabel history_noreg berhasil dibuat');
            } else {
                // Cek apakah kolom kd_poli sudah ada
                if (!Schema::hasColumn('history_noreg', 'kd_poli')) {
                    Schema::table('history_noreg', function ($table) {
                        $table->string('kd_poli', 20)->nullable()->after('no_reg');
                    });
                    \Log::info('Kolom kd_poli berhasil ditambahkan ke tabel history_noreg');
                }
            }
            
            // Insert data ke tabel history_noreg
            $data = [
                'kd_dokter' => $kd_dokter,
                'tgl_registrasi' => $tgl_registrasi,
                'no_reg' => $no_reg,
                'method' => $method,
                'created_by' => auth()->check() ? auth()->user()->username : 'system'
            ];
            
            if ($kd_poli) {
                $data['kd_poli'] = $kd_poli;
            }
            
            DB::table('history_noreg')->insert($data);
            
            \Log::info('Berhasil logging nomor registrasi ke history_noreg: ' . $no_reg . ($kd_poli ? " untuk poli: $kd_poli" : ""));
        } catch (\Exception $e) {
            \Log::warning('Gagal logging nomor registrasi ke history_noreg: ' . $e->getMessage());
        }
    }

    public function testNoReg()
    {
        try {
            $tgl_registrasi = date('Y-m-d');
            $kd_dokter = '103';
            
            // Lihat nomor registrasi yang ada
            $existing = DB::table('reg_periksa')
                ->where('tgl_registrasi', $tgl_registrasi)
                ->orderBy('no_reg', 'desc')
                ->select('no_reg', 'no_rawat', 'kd_dokter')
                ->limit(5)
                ->get();
                
            // Ambil nomor registrasi baru dengan algoritma yang sudah diperbaiki
            $maxReg = DB::table('reg_periksa')
                ->where('tgl_registrasi', $tgl_registrasi)
                ->selectRaw('MAX(CAST(no_reg AS UNSIGNED)) as max_reg')
                ->first();
            
            // Jika nilai yang dikembalikan adalah NULL, gunakan 0
            $maxRegNum = $maxReg && isset($maxReg->max_reg) ? (int)$maxReg->max_reg : 0;
            $nextRegNum = $maxRegNum + 1;
            $nextReg = str_pad($nextRegNum, 3, '0', STR_PAD_LEFT);
            
            // Double check dengan query raw SQL
            $rawCheck = DB::select("SELECT MAX(CAST(no_reg AS UNSIGNED)) as max_reg FROM reg_periksa WHERE tgl_registrasi = ?", [$tgl_registrasi]);
            $rawMaxRegNum = isset($rawCheck[0]->max_reg) ? (int)$rawCheck[0]->max_reg : 0;
            
            // Buat data pengujian jika parameter create=true
            $createdRecord = null;
            if (request()->has('create') && request()->get('create') == 'true') {
                // Generate nomor rawat dengan format yang sudah dibuat
                $no_rawat = $this->generateNoRawat($tgl_registrasi);
                
                // Siapkan data yang akan disimpan
                $data = [
                    'no_reg' => $nextReg,
                    'no_rawat' => $no_rawat,
                    'tgl_registrasi' => $tgl_registrasi,
                    'jam_reg' => date('H:i:s'),
                    'kd_dokter' => $kd_dokter,
                    'no_rkm_medis' => '900106150',
                    'kd_poli' => 'U0003',
                    'p_jawab' => 'PASIEN TEST',
                    'almt_pj' => 'ALAMAT TEST',
                    'hubunganpj' => 'DIRI SENDIRI',
                    'biaya_reg' => 0,
                    'stts' => 'Belum',
                    'stts_daftar' => 'Lama',
                    'status_lanjut' => 'Ralan',
                    'kd_pj' => '-',
                    'umurdaftar' => 0,
                    'sttsumur' => 'Th',
                    'status_bayar' => 'Belum Bayar',
                    'status_poli' => 'Lama'
                ];
                
                DB::table('reg_periksa')->insert($data);
                $createdRecord = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
            }
            
            return response()->json([
                'existing_registrations' => $existing,
                'max_reg_query_builder' => $maxRegNum,
                'max_reg_raw_sql' => $rawMaxRegNum,
                'next_reg_will_be' => $nextReg,
                'date_used' => $tgl_registrasi,
                'created_record' => $createdRecord
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    // Tambahkan metode di luar middleware untuk debugging
    public function testNoRegPublic()
    {
        return $this->testNoReg();
    }

    /**
     * Metode pengujian untuk generate nomor registrasi spesifik dokter
     */
    public function testDokterNoReg($kd_dokter = null)
    {
        try {
            if (!$kd_dokter) {
                $kd_dokter = request()->input('kd_dokter', '103');
            }
            
            $tgl_registrasi = request()->input('tgl_registrasi', date('Y-m-d'));
            $kd_poli = request()->input('kd_poli');
            
            \Log::info("Menguji nomor registrasi untuk dokter: $kd_dokter, tanggal: $tgl_registrasi" . 
                ($kd_poli ? ", poli: $kd_poli" : ""));
            
            // 1. Dapatkan semua nomor registrasi untuk dokter dan tanggal ini
            $existingRegsQuery = DB::table('reg_periksa')
                ->where('tgl_registrasi', $tgl_registrasi)
                ->where('kd_dokter', $kd_dokter);
                
            if ($kd_poli) {
                $existingRegsQuery->where('kd_poli', $kd_poli);
                \Log::info("Filter tambahan dengan poli: $kd_poli");
            }
                
            $existingRegs = $existingRegsQuery
                ->orderBy(DB::raw('CAST(no_reg AS UNSIGNED)'))
                ->select('no_reg', 'no_rawat', 'kd_dokter', 'tgl_registrasi', 'kd_poli')
                ->get();
            
            \Log::info("Registrasi yang ada: " . count($existingRegs));
            
            // 2. Hitung nomor registrasi yang seharusnya digunakan selanjutnya
            $maxReg = 0;
            foreach ($existingRegs as $reg) {
                $numReg = (int)ltrim($reg->no_reg, '0');
                if ($numReg > $maxReg) {
                    $maxReg = $numReg;
                }
            }
            
            // Cek juga di booking_registrasi jika ada
            if (Schema::hasTable('booking_registrasi')) {
                $bookingQuery = DB::table('booking_registrasi')
                    ->where('tanggal_periksa', $tgl_registrasi)
                    ->where('kd_dokter', $kd_dokter);
                    
                if ($kd_poli) {
                    $bookingQuery->where('kd_poli', $kd_poli);
                }
                
                $bookingRegs = $bookingQuery->select('no_reg')->get();
                
                foreach ($bookingRegs as $reg) {
                    $numReg = (int)ltrim($reg->no_reg, '0');
                    if ($numReg > $maxReg) {
                        $maxReg = $numReg;
                    }
                }
                
                \Log::info("Termasuk dari booking, nomor registrasi maksimum: $maxReg");
            }
            
            $nextReg = str_pad($maxReg + 1, 3, '0', STR_PAD_LEFT);
            \Log::info("Nomor registrasi berikutnya seharusnya: $nextReg");
            
            // 3. Cek lognya di history_noreg jika ada
            $historyLogsQuery = DB::table('history_noreg')
                ->where('tgl_registrasi', $tgl_registrasi)
                ->where('kd_dokter', $kd_dokter);
                
            if ($kd_poli) {
                $historyLogsQuery->where(function ($query) use ($kd_poli) {
                    $query->where('kd_poli', $kd_poli)
                          ->orWhereNull('kd_poli'); // Untuk kompatibilitas dengan data lama
                });
            }
                
            $historyLogs = [];
            if (Schema::hasTable('history_noreg')) {
                $historyLogs = $historyLogsQuery
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();
                    
                \Log::info("Jumlah log history: " . count($historyLogs));
            }
            
            // 4. Bikin baru jika diminta
            $newRecord = null;
            if (request()->has('create') && request()->get('create') == 'true') {
                // Gunakan generateNoReg untuk membuat nomor registrasi baru
                $requestParams = request()->all();
                
                // Jika generateNoReg membutuhkan request parameter
                if ($kd_poli && !isset($requestParams['kd_poli'])) {
                    request()->merge(['kd_poli' => $kd_poli]);
                }
                
                $response = $this->generateNoReg($kd_dokter, $tgl_registrasi);
                $responseData = json_decode($response->getContent(), true);
                
                if (isset($responseData['success']) && $responseData['success']) {
                    $no_reg = $responseData['no_reg'];
                    \Log::info("Berhasil membuat nomor registrasi baru: $no_reg");
                    
                    // Generate nomor rawat menggunakan metode yang sudah dibuat
                    $no_rawat = $this->generateNoRawat($tgl_registrasi);
                    
                    // Siapkan data
                    $data = [
                        'no_reg' => $no_reg,
                        'no_rawat' => $no_rawat,
                        'tgl_registrasi' => $tgl_registrasi,
                        'jam_reg' => date('H:i:s'),
                        'kd_dokter' => $kd_dokter,
                        'no_rkm_medis' => '900106150',
                        'kd_poli' => $kd_poli ?: 'U0003', // Default poli jika tidak ada
                        'p_jawab' => 'PASIEN TEST',
                        'almt_pj' => 'ALAMAT TEST',
                        'hubunganpj' => 'DIRI SENDIRI',
                        'biaya_reg' => 0,
                        'stts' => 'Belum',
                        'stts_daftar' => 'Lama',
                        'status_lanjut' => 'Ralan',
                        'kd_pj' => '-',
                        'umurdaftar' => 0,
                        'sttsumur' => 'Th',
                        'status_bayar' => 'Belum Bayar',
                        'status_poli' => 'Lama'
                    ];
                    
                    // Insert data
                    DB::table('reg_periksa')->insert($data);
                    $newRecord = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
                    \Log::info("Record registrasi baru berhasil dibuat dengan no_rawat: $no_rawat");
                } else {
                    \Log::error("Gagal membuat nomor registrasi: " . ($responseData['message'] ?? 'Tidak ada pesan error'));
                }
            }
            
            // 5. Kirim respons lengkap
            return response()->json([
                'dokter_id' => $kd_dokter,
                'tanggal' => $tgl_registrasi,
                'kd_poli' => $kd_poli,
                'existing_registrations' => $existingRegs,
                'max_reg' => $maxReg,
                'next_reg_should_be' => $nextReg,
                'history_logs' => $historyLogs,
                'new_record' => $newRecord,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            \Log::error("Error dalam testDokterNoReg: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Versi publik metode pengujian dokter
     */
    public function testDokterNoRegPublic($kd_dokter = null)
    {
        return $this->testDokterNoReg($kd_dokter);
    }
} 