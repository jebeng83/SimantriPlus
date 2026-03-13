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

class PendaftaranCKGExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $tanggal_awal = $this->filters['tanggal_awal'] ?? null;
        $tanggal_akhir = $this->filters['tanggal_akhir'] ?? null;
        $status = array_key_exists('status', $this->filters) ? $this->filters['status'] : '0';
        $nama_sekolah = $this->filters['nama_sekolah'] ?? null;
        $kelas = $this->filters['kelas'] ?? null;
        $kd_kel = $this->filters['kelurahan'] ?? null;
        $kode_posyandu = $this->filters['posyandu'] ?? null;

        $query = DB::table('skrining_pkg as s')
            ->leftJoin('pasien as p', 's.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('data_siswa_sekolah as dss', 's.no_rkm_medis', '=', 'dss.no_rkm_medis')
            ->leftJoin('data_sekolah as ds', 'dss.id_sekolah', '=', 'ds.id_sekolah')
            ->leftJoin('data_kelas as dk', 'dss.id_kelas', '=', 'dk.id_kelas')
            ->leftJoin('kelurahan as kel', 's.kd_kel', '=', 'kel.kd_kel')
            ->leftJoin('kecamatan as kec', 'kel.kd_kec', '=', 'kec.kd_kec')
            ->leftJoin('kabupaten as kab', 'kec.kd_kab', '=', 'kab.kd_kab')
            ->leftJoin('propinsi as prop', 'kab.kd_prop', '=', 'prop.kd_prop')
            ->select([
                's.nik',
                's.nama_lengkap',
                's.tanggal_lahir',
                's.jenis_kelamin',
                's.no_handphone',
                DB::raw('COALESCE(p.alamat, p.alamatpj) as alamat'),
                'p.pekerjaan',
                'prop.nm_prop as propinsi',
                'kab.nm_kab as kabupaten',
                'kec.nm_kec as kecamatan',
                'kel.nm_kel as kelurahan',
                'dss.nik_ortu as nik_wali',
                'dss.nama_ortu as nama_wali',
                'dss.tanggal_lahir as tgl_lahir_wali',
                'dss.jenis_kelamin as kelamin_wali',
                'p.no_tlp as no_tlp_pasien',
            ]);

        if ($tanggal_awal) {
            $query->whereDate('s.tanggal_skrining', '>=', $tanggal_awal);
        }
        if ($tanggal_akhir) {
            $query->whereDate('s.tanggal_skrining', '<=', $tanggal_akhir);
        }
        if ($status !== null && $status !== '') {
            $query->where('s.status', $status);
        }
        if ($nama_sekolah !== null && $nama_sekolah !== '') {
            $query->where('ds.id_sekolah', $nama_sekolah);
        }
        if ($kelas !== null && $kelas !== '') {
            $query->where('dk.id_kelas', $kelas);
        }
        if ($kd_kel !== null && $kd_kel !== '') {
            $query->where('s.kd_kel', $kd_kel);
        }
        if ($kode_posyandu !== null && $kode_posyandu !== '') {
            $query->where('s.kode_posyandu', $kode_posyandu);
        }

        return $query->orderBy('s.tanggal_skrining', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Nik',
            'Nama',
            'Tanggal Lahir (mm/dd/yyyy)',
            'JK',
            'No HP',
            'Alamat',
            'Pekerjaan',
            'Propinsi',
            'Kabupaten',
            'Kecamatan',
            'Kelurahan',
            'NIK Wali',
            'Nama Wali',
            'Tgl Lahir Wali',
            'Kelamin Wali',
        ];
    }

    public function map($row): array
    {
        $tglLahir = $row->tanggal_lahir ? date('m/d/Y', strtotime($row->tanggal_lahir)) : '';
        $tglLahirWali = $row->tgl_lahir_wali ? date('m/d/Y', strtotime($row->tgl_lahir_wali)) : '';
        $noHp = $row->no_handphone ?: ($row->no_tlp_pasien ?? '');
        return [
            $row->nik ? ("'".$row->nik) : '',
            $row->nama_lengkap ?? '',
            $tglLahir,
            $row->jenis_kelamin ?? '',
            $noHp,
            $row->alamat ?? '',
            $row->pekerjaan ?? '',
            $row->propinsi ?? '',
            $row->kabupaten ?? '',
            $row->kecamatan ?? '',
            $row->kelurahan ?? '',
            $row->nik_wali ? ("'".$row->nik_wali) : '',
            $row->nama_wali ?? '',
            $tglLahirWali,
            $row->kelamin_wali ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '007BFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A1:O' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
    }

    public function title(): string
    {
        return 'Data Pendaftaran CKG';
    }
}
