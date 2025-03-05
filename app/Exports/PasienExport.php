<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PasienExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = DB::table('pasien');
        
        // Jika ada parameter pencarian
        if ($this->search) {
            if (!empty($this->search['name'])) {
                $query->where('nm_pasien', 'like', '%' . $this->search['name'] . '%');
            }
            
            if (!empty($this->search['rm'])) {
                $query->where('no_rkm_medis', 'like', '%' . $this->search['rm'] . '%');
            }
            
            if (!empty($this->search['address'])) {
                $query->where('alamat', 'like', '%' . $this->search['address'] . '%');
            }
        }
        
        return $query->orderBy('tgl_daftar', 'desc')->limit(1000)->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No. RM',
            'Nama Pasien',
            'No. KTP',
            'No. KK',
            'No. Peserta',
            'No. Telepon',
            'Tanggal Lahir',
            'Umur',
            'Alamat',
            'Status Nikah',
            'Status',
            'Tanggal Daftar'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->no_rkm_medis,
            $row->nm_pasien,
            $row->no_ktp,
            $row->no_kk,
            $row->no_peserta,
            $row->no_tlp,
            $row->tgl_lahir,
            $row->umur,
            $row->alamat,
            $row->stts_nikah,
            $row->status,
            $row->tgl_daftar
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:L1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF4F81BD']
                    ],
                    'font' => [
                        'color' => ['argb' => 'FFFFFFFF']
                    ]
                ]);
            },
        ];
    }
} 