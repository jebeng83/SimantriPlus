<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PcarePendaftaranExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
{
    protected $tanggal;
    protected $status;

    /**
     * @param string|null $tanggal
     * @param string|null $status
     */
    public function __construct($tanggal = null, $status = null)
    {
        $this->tanggal = $tanggal;
        $this->status = $status;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = DB::table('pcare_pendaftaran');

        // Filter berdasarkan tanggal
        if (!empty($this->tanggal)) {
            $query->whereDate('tglDaftar', $this->tanggal);
        }

        // Filter berdasarkan status
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No. Rawat',
            'Tanggal Daftar',
            'No. Rekam Medis',
            'Nama Pasien',
            'No. Kartu BPJS',
            'Provider Peserta',
            'Kode Poli',
            'Nama Poli',
            'Keluhan',
            'Kunjungan Sakit',
            'Sistole',
            'Diastole',
            'Berat Badan',
            'Tinggi Badan',
            'Respiratory Rate',
            'Lingkar Perut',
            'Heart Rate',
            'Rujuk Balik',
            'Tempat Kunjungan',
            'No. Urut',
            'Status'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        // Format tanggal dari database (YYYY-MM-DD) menjadi format DD-MM-YYYY
        $parts = explode('-', $row->tglDaftar);
        $tglDaftar = count($parts) === 3 ? $parts[2] . '-' . $parts[1] . '-' . $parts[0] : $row->tglDaftar;
        
        // Format tempat kunjungan
        $tkp = '';
        switch ($row->kdTkp) {
            case '10':
                $tkp = 'Rawat Jalan (RJTP)';
                break;
            case '20':
                $tkp = 'Rawat Inap (RITP)';
                break;
            case '50':
                $tkp = 'Promotif Preventif';
                break;
            default:
                $tkp = $row->kdTkp;
                break;
        }
        
        return [
            $row->no_rawat,
            $tglDaftar,
            $row->no_rkm_medis,
            $row->nm_pasien,
            $row->noKartu,
            $row->kdProviderPeserta,
            $row->kdPoli,
            $row->nmPoli,
            $row->keluhan,
            $row->kunjSakit === 'true' ? 'Ya' : 'Tidak',
            $row->sistole,
            $row->diastole,
            $row->beratBadan,
            $row->tinggiBadan,
            $row->respRate,
            $row->lingkar_perut,
            $row->heartRate,
            $row->rujukBalik,
            $tkp,
            $row->noUrut,
            $row->status
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:U1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style untuk seluruh cell
        $sheet->getStyle('A1:U' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Tambahkan informasi filter yang digunakan
        $row = $sheet->getHighestRow() + 2;
        $sheet->setCellValue('A' . $row, 'Informasi Export:');
        $sheet->setCellValue('A' . ($row + 1), 'Tanggal Export:');
        $sheet->setCellValue('B' . ($row + 1), date('d-m-Y H:i:s'));
        
        if (!empty($this->tanggal)) {
            $sheet->setCellValue('A' . ($row + 2), 'Filter Tanggal:');
            $sheet->setCellValue('B' . ($row + 2), date('d-m-Y', strtotime($this->tanggal)));
        }
        
        if (!empty($this->status)) {
            $sheet->setCellValue('A' . ($row + 3), 'Filter Status:');
            $sheet->setCellValue('B' . ($row + 3), $this->status);
        }
        
        // Style untuk informasi export
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Data Pendaftaran PCare';
    }
} 