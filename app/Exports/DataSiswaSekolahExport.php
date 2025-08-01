<?php

namespace App\Exports;

use App\Models\DataSiswaSekolah;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class DataSiswaSekolahExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithColumnFormatting, WithColumnWidths
{
    protected $filters;

    /**
     * @param array $filters
     */
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
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
        if (!empty($this->filters['sekolah'])) {
            $query->where('data_siswa_sekolah.id_sekolah', $this->filters['sekolah']);
        }
        
        if (!empty($this->filters['jenis_sekolah'])) {
            $query->where('data_sekolah.id_jenis_sekolah', $this->filters['jenis_sekolah']);
        }
        
        if (!empty($this->filters['kelas'])) {
            $query->where('data_siswa_sekolah.id_kelas', $this->filters['kelas']);
        }
        
        if (!empty($this->filters['status'])) {
            $query->where('data_siswa_sekolah.status_siswa', $this->filters['status']);
        }
        
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('pasien.nm_pasien', 'like', '%' . $search . '%')
                  ->orWhere('data_siswa_sekolah.nama_ortu', 'like', '%' . $search . '%')
                  ->orWhere('data_siswa_sekolah.nisn', 'like', '%' . $search . '%')
                  ->orWhere('data_sekolah.nama_sekolah', 'like', '%' . $search . '%')
                  ->orWhere('data_siswa_sekolah.no_rkm_medis', 'like', '%' . $search . '%')
                  ->orWhere('pasien.no_ktp', 'like', '%' . $search . '%');
            });
        }
        
        return $query->orderBy('pasien.nm_pasien', 'asc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'No. KTP',
            'NISN',
            'No. RM',
            'Nama Siswa',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Umur',
            'Alamat',
            'Nama Sekolah',
            'Jenis Sekolah',
            'Kelas',
            'Nama Orang Tua',
            'NIK Orang Tua',
            'No. Telepon',
            'No. WhatsApp',
            'Jenis Disabilitas',
            'Status Siswa'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        static $no = 1;
        
        // Use patient data as primary source
        $nama_lengkap = $row->nm_pasien ?? '-';
        $tempat_lahir = $row->tmp_lahir_pasien ?? '-';
        $tanggal_lahir = $row->tgl_lahir_pasien ?? $row->tanggal_lahir ?? null;
        $alamat = $row->alamat_pasien ?? '-';
        
        // Format nomor dengan prefix apostrof untuk memaksa sebagai teks
        $no_ktp = $row->no_ktp ? "'" . $row->no_ktp : '-';
        $nisn = $row->nisn ? "'" . $row->nisn : '-';
        $no_rm = $row->no_rkm_medis ? "'" . $row->no_rkm_medis : '-';
        $nik_ortu = $row->nik_ortu ? "'" . $row->nik_ortu : '-';
        $no_tlp = $row->no_tlp ? "'" . $row->no_tlp : '-';
        $no_wa = $row->no_whatsapp ? "'" . $row->no_whatsapp : '-';
        
        return [
            $no++,
            $no_ktp,
            $nisn,
            $no_rm,
            $nama_lengkap,
            ($row->jk ?? 'L') == 'L' ? 'Laki-laki' : 'Perempuan',
            $tempat_lahir,
            $tanggal_lahir ? date('d-m-Y', strtotime($tanggal_lahir)) : '-',
            $tanggal_lahir ? Carbon::parse($tanggal_lahir)->age . ' tahun' : '-',
            $alamat,
            $row->nama_sekolah ?? '-',
            $row->nama_jenis_sekolah ?? '-', // Jenis Sekolah
            $row->kelas ?? '-',
            $row->nama_ortu ?? '-',
            $nik_ortu,
            $no_tlp,
            $no_wa,
            $row->jenis_disabilitas ?? 'Non Disabilitas',
            $row->status_siswa ?? 'Aktif'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Get the highest row and column
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        return [
            // Style header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                     'fillType' => Fill::FILL_SOLID,
                     'startColor' => ['rgb' => '4472C4']
                 ],
                 'alignment' => [
                     'horizontal' => Alignment::HORIZONTAL_CENTER,
                     'vertical' => Alignment::VERTICAL_CENTER
                 ]
            ],
            // Style data rows with alternating colors
            'A2:' . $highestColumn . $highestRow => [
                'borders' => [
                     'allBorders' => [
                         'borderStyle' => Border::BORDER_THIN,
                         'color' => ['rgb' => 'CCCCCC']
                     ]
                 ],
                 'alignment' => [
                     'vertical' => Alignment::VERTICAL_CENTER
                 ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Data Siswa Sekolah';
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // No. KTP
            'C' => NumberFormat::FORMAT_TEXT, // NISN
            'D' => NumberFormat::FORMAT_TEXT, // No. RM
            'O' => NumberFormat::FORMAT_TEXT, // NIK Orang Tua
            'P' => NumberFormat::FORMAT_TEXT, // No. Telepon
            'Q' => NumberFormat::FORMAT_TEXT, // No. WhatsApp
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 18,  // No. KTP
            'C' => 15,  // NISN
            'D' => 12,  // No. RM
            'E' => 25,  // Nama Siswa
            'F' => 12,  // Jenis Kelamin
            'G' => 15,  // Tempat Lahir
            'H' => 12,  // Tanggal Lahir
            'I' => 10,  // Umur
            'J' => 30,  // Alamat
            'K' => 25,  // Nama Sekolah
            'L' => 15,  // Jenis Sekolah
            'M' => 10,  // Kelas
            'N' => 20,  // Nama Orang Tua
            'O' => 18,  // NIK Orang Tua
            'P' => 15,  // No. Telepon
            'Q' => 15,  // No. WhatsApp
            'R' => 15,  // Jenis Disabilitas
            'S' => 12,  // Status Siswa
        ];
    }
}