<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataSiswaSekolah;
use App\Models\DataSekolah;
use App\Models\JenisSekolah;
use App\Models\DataKelas;
use App\Models\Kelurahan;
use App\Models\SkriningPkg;
use App\Models\SkriningSiswaSD;
use App\Models\Setting;
use App\Exports\DataSiswaSekolahExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Session;
use Illuminate\Support\Facades\View;

class DataSiswaSekolahController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Set page title
        View::share('title', 'Data Siswa Sekolah');
        
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        
        // Filter parameters
        $sekolah_filter = $request->input('sekolah');
        $kelas_filter = $request->input('kelas');
        $status_filter = $request->input('status', 'Aktif');
        $search = $request->input('search');
        
        // Query data siswa dengan join ke tabel terkait
        $query = DataSiswaSekolah::select(
            'data_siswa_sekolah.id',
            'data_siswa_sekolah.nisn',
            'data_siswa_sekolah.no_rkm_medis',
            'data_siswa_sekolah.id_sekolah',
            'data_siswa_sekolah.id_kelas',
            'data_siswa_sekolah.jenis_disabilitas',
            'data_siswa_sekolah.nik_ortu',
            'data_siswa_sekolah.tanggal_lahir',
            'data_siswa_sekolah.nama_ortu',
            'data_siswa_sekolah.status',
            'data_siswa_sekolah.status_siswa',
            'data_siswa_sekolah.no_tlp',
            'data_siswa_sekolah.no_whatsapp',
            'data_sekolah.nama_sekolah',
            'jenis_sekolah.nama',
            'data_kelas.kelas',
            'pasien.nm_pasien',
            'pasien.no_ktp',
            'pasien.jk',
            'pasien.alamat as alamat_pasien',
            'pasien.tgl_lahir as tgl_lahir_pasien',
            'pasien.tmp_lahir as tmp_lahir_pasien'
        )
        ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
        ->join('jenis_sekolah', 'data_sekolah.id_jenis_sekolah', '=', 'jenis_sekolah.id')
        ->join('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
        ->leftJoin('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis');
        
        // Apply filters
        if ($sekolah_filter) {
            $query->where('data_siswa_sekolah.id_sekolah', $sekolah_filter);
        }
        
        if ($kelas_filter) {
            $query->where('data_siswa_sekolah.id_kelas', $kelas_filter);
        }
        
        if ($status_filter) {
            $query->where('data_siswa_sekolah.status_siswa', $status_filter);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('pasien.nm_pasien', 'like', '%' . $search . '%')
                  ->orWhere('data_siswa_sekolah.nama_ortu', 'like', '%' . $search . '%')
                  ->orWhere('data_siswa_sekolah.nisn', 'like', '%' . $search . '%')
                  ->orWhere('data_sekolah.nama_sekolah', 'like', '%' . $search . '%')
                  ->orWhere('data_siswa_sekolah.no_rkm_medis', 'like', '%' . $search . '%')
                  ->orWhere('pasien.no_ktp', 'like', '%' . $search . '%');
            });
        }
        
        $dataSiswa = $query->orderBy('pasien.nm_pasien', 'asc')
                          ->paginate(10);
        
        // Format data untuk tampilan
        $dataSiswa->getCollection()->transform(function($item, $index) use ($dataSiswa) {
            // Use patient data as primary source since nama_siswa doesn't exist
            $nama_lengkap = $item->nm_pasien ?? '-';
            $tempat_lahir = $item->tmp_lahir_pasien ?? '-';
            $tanggal_lahir = $item->tgl_lahir_pasien ?? $item->tanggal_lahir ?? null;
            $alamat = $item->alamat_pasien ?? '-';
            
            return [
                'no' => ($dataSiswa->currentPage() - 1) * $dataSiswa->perPage() + $index + 1,
                'id' => $item->id ?? null,
                'no_rkm_medis' => $item->no_rkm_medis ?? '-',
                'no_ktp' => $item->no_ktp ?? '-',
                'nisn' => $item->nisn ?? '-',
                'nama_siswa' => $nama_lengkap,
                'jenis_kelamin' => ($item->jk ?? 'L') == 'L' ? 'Laki-laki' : 'Perempuan',
                'tempat_tanggal_lahir' => $tempat_lahir . ', ' . ($tanggal_lahir ? date('d-m-Y', strtotime($tanggal_lahir)) : '-'),
                'umur' => $tanggal_lahir ? \Carbon\Carbon::parse($tanggal_lahir)->age . ' tahun' : '-',
                'nama_sekolah' => $item->nama_sekolah ?? '-',
                'jenis_sekolah' => $item->nama ?? '-',
                'kelas' => $item->kelas ?? '-',
                'nama_ortu' => $item->nama_ortu ?? '-',
                'nik_ortu' => $item->nik_ortu ?? '-',
                'no_whatsapp' => $item->no_whatsapp ?? '-',
                'nama_ibu' => '-',
                'status' => $item->status_siswa ?? 'Aktif',
                'alamat' => $alamat,
                'jenis_disabilitas' => $item->jenis_disabilitas ?? 'Non Disabilitas'
            ];
        });
        
        // Data untuk dropdown filter
        $daftarSekolah = DataSekolah::orderBy('nama_sekolah')->get();
        $daftarKelas = DataKelas::orderBy('kelas')->get();
        
        // Statistik
        $totalSiswa = DataSiswaSekolah::aktif()->count();
        $totalSekolah = DataSekolah::count();
        $totalKelas = DataKelas::count();
        
        return view('ilp.data-siswa-sekolah.index', [
            'dataSiswa' => $dataSiswa,
            'daftarSekolah' => $daftarSekolah,
            'daftarKelas' => $daftarKelas,
            'totalSiswa' => $totalSiswa,
            'totalSekolah' => $totalSekolah,
            'totalKelas' => $totalKelas,
            'filters' => [
                'sekolah' => $sekolah_filter,
                'kelas' => $kelas_filter,
                'status' => $status_filter,
                'search' => $search
            ]
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        View::share('title', 'Tambah Data Siswa');
        
        $daftarSekolah = DataSekolah::orderBy('nama_sekolah')->get();
        $daftarKelas = DataKelas::orderBy('kelas')->get();
        
        return view('ilp.data-siswa-sekolah.create', [
            'daftarSekolah' => $daftarSekolah,
            'daftarKelas' => $daftarKelas
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'nullable|string|max:20',
            'no_rkm_medis' => 'required|string|max:15',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'nama_ortu' => 'nullable|string|max:100',
            'nik_ortu' => 'nullable|string|max:16',
            'no_telepon_ortu' => 'nullable|string|max:20',
            'id_sekolah' => 'required|exists:data_sekolah,id_sekolah',
            'id_kelas' => 'required|exists:data_kelas,id_kelas',
            'jenis_disabilitas' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:100',
            'status_siswa' => 'required|in:Aktif,Pindah,Lulus,Drop Out'
        ]);
        
        $validatedData = $request->validated();
        // Note: no_telepon_ortu is automatically mapped to no_whatsapp via model mutator
        /** @var \App\Models\DataSiswaSekolah|null $siswa */
        $siswa = DataSiswaSekolah::create($validatedData);
        
        if ($siswa && $siswa->id) {
            return redirect()->route('ilp.data-siswa-sekolah.index')
                            ->with('success', 'Data siswa berhasil ditambahkan.');
        }
        
        return redirect()->back()->with('error', 'Gagal menambahkan data siswa.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id)
    {
        View::share('title', 'Detail Data Siswa');
        
        /** @var \App\Models\DataSiswaSekolah $siswa */
        $siswa = DataSiswaSekolah::with(['pasien'])
                                ->findOrFail($id);
        
        return view('ilp.data-siswa-sekolah.show', compact('siswa'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        View::share('title', 'Edit Data Siswa');
        
        // Ambil data siswa dengan join ke pasien dan sekolah
        $siswa = DataSiswaSekolah::with(['pasien', 'sekolah.jenisSekolah', 'sekolah.kelurahan'])->findOrFail($id);
        
        // Ambil daftar sekolah dengan jenis sekolah dan kelurahan untuk dropdown
        $daftarSekolah = DataSekolah::with(['jenisSekolah', 'kelurahan'])
                                   ->orderBy('nama_sekolah')
                                   ->get();
        
        // Ambil daftar kelas untuk dropdown
        $daftarKelas = DataKelas::orderBy('kelas')
                                ->get();
        
        // Ambil daftar jenis sekolah untuk dropdown
        $daftarJenisSekolah = JenisSekolah::orderBy('nama')->get();
        
        // Ambil daftar kelurahan untuk dropdown
        $daftarKelurahan = Kelurahan::orderBy('nm_kel')->get();
        
        return view('ilp.data-siswa-sekolah.edit', [
            'siswa' => $siswa,
            'daftarSekolah' => $daftarSekolah,
            'daftarKelas' => $daftarKelas,
            'daftarJenisSekolah' => $daftarJenisSekolah,
            'daftarKelurahan' => $daftarKelurahan
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $siswa = DataSiswaSekolah::findOrFail($id);
        
        $request->validate([
            'nik' => 'required|string|size:16',
            'nisn' => 'nullable|string|max:20',
            'nama_siswa' => 'required|string|max:100', // Validated for pasien.nm_pasien
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date|before:today',
            'alamat' => 'required|string',
            'nama_ayah' => 'nullable|string|max:100',
            'no_telepon_ortu' => 'nullable|string|max:20',
            'id_sekolah' => 'required|exists:data_sekolah,id_sekolah',
            'id_kelas' => 'required|exists:data_kelas,id_kelas',
            'status' => 'nullable|string|max:100',
            'status_siswa' => 'required|in:Aktif,Pindah,Lulus,Drop Out',
            'jenis_sekolah' => 'required|exists:jenis_sekolah,id',
            'kelurahan' => 'required|exists:kelurahan,kd_kel'
        ]);
        
        // Update data sekolah jika jenis_sekolah atau kelurahan berubah
        $sekolah = DataSekolah::find($request->id_sekolah);
        if ($sekolah && isset($sekolah->id_sekolah)) {
            $sekolah->update([
                'id_jenis_sekolah' => $request->jenis_sekolah,
                'kd_kel' => $request->kelurahan
            ]);
        }
        
        // Update data siswa sekolah
        $siswa->update([
            'nisn' => $request->nisn,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_telepon_ortu' => $request->no_telepon_ortu, // Uses mutator to map to no_whatsapp
            'id_sekolah' => $request->id_sekolah,
            'id_kelas' => $request->id_kelas,
            'status' => $request->status,
            'status_siswa' => $request->status_siswa
        ]);
        
        // Update data pasien jika ada relasi
        if ($siswa && isset($siswa->pasien) && $siswa->pasien && isset($siswa->pasien->no_rkm_medis)) {
            $siswa->pasien->update([
                'no_ktp' => $request->nik,
                'nm_pasien' => $request->nama_siswa,
                'jk' => $request->jenis_kelamin,
                'tmp_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat
            ]);
        } else if ($siswa && isset($siswa->no_rkm_medis) && $siswa->no_rkm_medis) {
            // Jika tidak ada relasi pasien, buat record pasien baru
            $pasien = new \App\Models\Pasien();
            $pasien->no_rkm_medis = $siswa->no_rkm_medis;
            $pasien->no_ktp = $request->nik;
            $pasien->nm_pasien = $request->nama_siswa;
            $pasien->jk = $request->jenis_kelamin;
            $pasien->tmp_lahir = $request->tempat_lahir;
            $pasien->tgl_lahir = $request->tanggal_lahir;
            $pasien->alamat = $request->alamat;
            $pasien->save();
        }
        
        return redirect()->route('ilp.data-siswa-sekolah.index')
                        ->with('success', 'Data siswa berhasil diperbarui.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        /** @var \App\Models\DataSiswaSekolah|null $siswa */
        $siswa = DataSiswaSekolah::find($id);
        
        if ($siswa && isset($siswa->id)) {
            $siswa->delete();
            return redirect()->route('ilp.data-siswa-sekolah.index')
                            ->with('success', 'Data siswa berhasil dihapus.');
        }
        
        return redirect()->route('ilp.data-siswa-sekolah.index')
                        ->with('error', 'Data siswa tidak ditemukan.');
    }
    
    /**
     * Export data siswa sekolah ke Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function exportExcel(Request $request)
    {
        try {
            // Ambil filter dari request
            $filters = [
                'sekolah' => $request->input('sekolah'),
                'kelas' => $request->input('kelas'),
                'status' => $request->input('status'),
                'search' => $request->input('search')
            ];
            
            $export = new DataSiswaSekolahExport($filters);
            $filename = 'data_siswa_sekolah_' . date('YmdHis') . '.xlsx';
            
            return Excel::download($export, $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat export data ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export data siswa ke PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'sekolah' => $request->input('sekolah'),
                'kelas' => $request->input('kelas'),
                'status' => $request->input('status')
            ];
            
            // Query data siswa dengan join ke tabel terkait
            $query = DataSiswaSekolah::select(
                'data_siswa_sekolah.id',
                'data_siswa_sekolah.nisn',
                'data_siswa_sekolah.no_rkm_medis',
                'data_siswa_sekolah.id_sekolah',
                'data_siswa_sekolah.id_kelas',
                'data_siswa_sekolah.jenis_disabilitas',
                'data_siswa_sekolah.nik_ortu',
                'data_siswa_sekolah.tanggal_lahir',
                'data_siswa_sekolah.nama_ortu',
                'data_siswa_sekolah.status',
                'data_siswa_sekolah.status_siswa',
                'data_siswa_sekolah.no_tlp',
                'data_siswa_sekolah.no_whatsapp',
                'data_sekolah.nama_sekolah',
                'jenis_sekolah.nama as nama_jenis_sekolah',
                'data_kelas.kelas',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.alamat as alamat_pasien',
                'pasien.tgl_lahir as tgl_lahir_pasien',
                'pasien.tmp_lahir as tmp_lahir_pasien',
                'pasien.jk'
            )
            ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->join('jenis_sekolah', 'data_sekolah.id_jenis_sekolah', '=', 'jenis_sekolah.id')
            ->join('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
            ->leftJoin('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis');
            
            // Apply filters
            if (!empty($filters['sekolah'])) {
            $query->where('data_siswa_sekolah.id_sekolah', $filters['sekolah']);
        }
        
        if (!empty($filters['kelas'])) {
            $query->where('data_siswa_sekolah.id_kelas', $filters['kelas']);
        }
            
            if (!empty($filters['status'])) {
                $query->where('data_siswa_sekolah.status_siswa', $filters['status']);
            }
            
            if (!empty($filters['search'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('pasien.nm_pasien', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('pasien.no_rkm_medis', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('data_siswa_sekolah.nisn', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('data_sekolah.nama_sekolah', 'like', '%' . $filters['search'] . '%');
                });
            }
            
            $data = $query->get();
            
            // Format data untuk tampilan PDF
            $formattedData = [];
            $no = 1;
            foreach ($data as $siswa) {
                $formattedData[] = [
                    'no' => $no++,
                    'no_ktp' => $siswa->no_ktp ?: '-',
                    'nama_siswa' => $siswa->nm_pasien ?: '-',
                    'nisn' => $siswa->nisn ?: '-',
                    'jenis_kelamin' => ($siswa->jk ?? 'L') == 'L' ? 'Laki-laki' : 'Perempuan',
                    'tempat_lahir' => $siswa->tmp_lahir_pasien ?: '-',
                    'tanggal_lahir' => $siswa->tgl_lahir_pasien ? date('d-m-Y', strtotime($siswa->tgl_lahir_pasien)) : '-',
                    'umur' => $siswa->tgl_lahir_pasien ? \Carbon\Carbon::parse($siswa->tgl_lahir_pasien)->age . ' tahun' : '-',
                    'nama_sekolah' => $siswa->nama_sekolah,
                    'jenis_sekolah' => $siswa->nama_jenis_sekolah,
                    'kelas' => $siswa->kelas,
                    'nama_ortu' => $siswa->nama_ortu ?: '-',
                    'nik_ortu' => $siswa->nik_ortu ?: '-',
                    'no_whatsapp' => $siswa->no_whatsapp ?: '-',
                    'jenis_disabilitas' => $siswa->jenis_disabilitas ?: 'Non Disabilitas',
                    'status' => $siswa->status_siswa ?: 'Aktif',
                    'alamat' => $siswa->alamat_pasien ?: '-'
                ];
            }
            
            // Get filter labels for display
            $filterLabels = [
                'sekolah' => 'Semua Sekolah',
                'kelas' => 'Semua Kelas',
                'status' => 'Semua Status',
                'search' => $filters['search'] ?: ''
            ];
            
            if (!empty($filters['sekolah'])) {
                $sekolah = DataSekolah::find($filters['sekolah']);
                $filterLabels['sekolah'] = $sekolah !== null ? $sekolah->nama_sekolah : 'Sekolah tidak ditemukan';
            }
            
            if (!empty($filters['kelas'])) {
                $kelas = DataKelas::find($filters['kelas']);
                $filterLabels['kelas'] = $kelas !== null ? $kelas->kelas : 'Kelas tidak ditemukan';
            }
            
            if (!empty($filters['status'])) {
                $filterLabels['status'] = $filters['status'];
            }
            
            $filename = 'data_siswa_sekolah_' . date('YmdHis') . '.pdf';
            
            // Get hospital information from settings
            $hospitalInfo = Setting::getHospitalInfo() ?? [];
            
            // Generate PDF
            $pdf = Pdf::loadView('exports.data-siswa-sekolah-pdf', [
                'data' => $formattedData,
                'filters' => $filterLabels,
                'tanggal_cetak' => date('d-m-Y H:i:s'),
                'total_data' => count($formattedData),
                'hospital_info' => $hospitalInfo
            ]);
            
            $pdf->setPaper('a4', 'landscape');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat export data ke PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get kelas by sekolah for AJAX request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKelasBySekolah(Request $request)
    {
        // Since sekolah_id column has been removed, return all available classes
        $kelas = DataKelas::orderBy('kelas')
            ->get(['id_kelas as id', 'kelas']); // Return only needed fields with proper naming
        
        return response()->json($kelas ?? []);
    }
}