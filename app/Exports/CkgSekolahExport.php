<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CkgSekolahExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $tanggalAwal = $this->filters['tanggal_awal'] ?? null;
        $tanggalAkhir = $this->filters['tanggal_akhir'] ?? null;

        $latestSub = DB::table('skrining_siswa_sd as s1')
            ->select('s1.no_rkm_medis', DB::raw('MAX(s1.created_at) as latest_created_at'));
        if ($tanggalAwal && $tanggalAkhir) {
            $latestSub->whereBetween('s1.created_at', [$tanggalAwal, $tanggalAkhir]);
        }
        $latestSub->groupBy('s1.no_rkm_medis');

        $hasStatusGizi = Schema::hasColumn('skrining_siswa_sd', 'status_gizi');
        $hasKategoriStatusGizi = Schema::hasColumn('skrining_siswa_sd', 'kategori_status_gizi');
        $hasTekananDarah = Schema::hasColumn('skrining_siswa_sd', 'tekanan_darah');
        $hasSistole = Schema::hasColumn('skrining_siswa_sd', 'sistole');
        $hasDiastole = Schema::hasColumn('skrining_siswa_sd', 'diastole');
        $hasDenyutNadi = Schema::hasColumn('skrining_siswa_sd', 'denyut_nadi');
        $hasSuhuTubuh = Schema::hasColumn('skrining_siswa_sd', 'suhu_tubuh');
        $hasVisusOD = Schema::hasColumn('skrining_siswa_sd', 'visus_od') || Schema::hasColumn('skrining_siswa_sd', 'visus_mata_kanan');
        $hasVisusOS = Schema::hasColumn('skrining_siswa_sd', 'visus_os') || Schema::hasColumn('skrining_siswa_sd', 'visus_mata_kiri');
        $hasKelainanMata = Schema::hasColumn('skrining_siswa_sd', 'kelainan_mata') || Schema::hasColumn('skrining_siswa_sd', 'selaput_mata_kanan');
        $hasPendengaranKanan = Schema::hasColumn('skrining_siswa_sd', 'pendengaran_kanan') || Schema::hasColumn('skrining_siswa_sd', 'gangguan_telingga_kanan');
        $hasPendengaranKiri = Schema::hasColumn('skrining_siswa_sd', 'pendengaran_kiri') || Schema::hasColumn('skrining_siswa_sd', 'gangguan_telingga_kiri');
        $hasKelainanTelinga = Schema::hasColumn('skrining_siswa_sd', 'kelainan_telinga') || Schema::hasColumn('skrining_siswa_sd', 'infeksi_telingga_kanan');
        $hasGigiKaries = Schema::hasColumn('skrining_siswa_sd', 'gigi_karies');
        $hasGigiHilang = Schema::hasColumn('skrining_siswa_sd', 'gigi_hilang');
        $hasKelainanGigi = Schema::hasColumn('skrining_siswa_sd', 'kelainan_gigi');
        $hasRiwayatPenyakit = Schema::hasColumn('skrining_siswa_sd', 'riwayat_penyakit');
        $hasRiwayatAlergi = Schema::hasColumn('skrining_siswa_sd', 'riwayat_alergi');
        $hasObatDikonsumsi = Schema::hasColumn('skrining_siswa_sd', 'obat_dikonsumsi');
        $hasStatusImunisasi = Schema::hasColumn('skrining_siswa_sd', 'status_imunisasi');
        $hasKesimpulan = Schema::hasColumn('skrining_siswa_sd', 'kesimpulan') || Schema::hasColumn('skrining_siswa_sd', 'kebugaran_jantung');
        $hasTindakLanjut = Schema::hasColumn('skrining_siswa_sd', 'tindak_lanjut') || Schema::hasColumn('skrining_siswa_sd', 'kebugaran_jantung');
        $hasStatusSkrining = Schema::hasColumn('skrining_siswa_sd', 'status_skrining') || Schema::hasColumn('skrining_siswa_sd', 'kebugaran_jantung') || $hasKategoriStatusGizi;

        $statusGiziExpr = $hasStatusGizi
            ? 's.status_gizi'
            : ($hasKategoriStatusGizi ? 's.kategori_status_gizi' : 'NULL');
        $tekananDarahExpr = $hasTekananDarah
            ? 's.tekanan_darah'
            : (($hasSistole && $hasDiastole) ? "CONCAT(s.sistole,'/',s.diastole)" : 'NULL');
        $denyutNadiExpr = $hasDenyutNadi ? 's.denyut_nadi' : 'NULL';
        $suhuTubuhExpr = $hasSuhuTubuh ? 's.suhu_tubuh' : 'NULL';
        $visusOdExpr = Schema::hasColumn('skrining_siswa_sd', 'visus_od') ? 's.visus_od' : (Schema::hasColumn('skrining_siswa_sd', 'visus_mata_kanan') ? 's.visus_mata_kanan' : 'NULL');
        $visusOsExpr = Schema::hasColumn('skrining_siswa_sd', 'visus_os') ? 's.visus_os' : (Schema::hasColumn('skrining_siswa_sd', 'visus_mata_kiri') ? 's.visus_mata_kiri' : 'NULL');
        $kelainanMataExpr = Schema::hasColumn('skrining_siswa_sd', 'kelainan_mata') ? 's.kelainan_mata' : (Schema::hasColumn('skrining_siswa_sd', 'selaput_mata_kanan') ? 's.selaput_mata_kanan' : 'NULL');
        $pendengaranKananExpr = Schema::hasColumn('skrining_siswa_sd', 'pendengaran_kanan') ? 's.pendengaran_kanan' : (Schema::hasColumn('skrining_siswa_sd', 'gangguan_telingga_kanan') ? 's.gangguan_telingga_kanan' : 'NULL');
        $pendengaranKiriExpr = Schema::hasColumn('skrining_siswa_sd', 'pendengaran_kiri') ? 's.pendengaran_kiri' : (Schema::hasColumn('skrining_siswa_sd', 'gangguan_telingga_kiri') ? 's.gangguan_telingga_kiri' : 'NULL');
        $kelainanTelingaExpr = Schema::hasColumn('skrining_siswa_sd', 'kelainan_telinga') ? 's.kelainan_telinga' : (Schema::hasColumn('skrining_siswa_sd', 'infeksi_telingga_kanan') ? 's.infeksi_telingga_kanan' : 'NULL');

        $query = DB::table('skrining_siswa_sd as s')
            ->joinSub($latestSub, 'latest', function($join) {
                $join->on('s.no_rkm_medis', '=', 'latest.no_rkm_medis')
                     ->on('s.created_at', '=', 'latest.latest_created_at');
            })
            ->join('data_siswa_sekolah', 's.no_rkm_medis', '=', 'data_siswa_sekolah.no_rkm_medis')
            ->leftJoin('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->join('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
            ->select([
                'pasien.nm_pasien as nama_siswa',
                'data_sekolah.nama_sekolah',
                'data_kelas.kelas',
                DB::raw('COALESCE(s.kebugaran_jantung, s.kategori_status_gizi) as hasil_ckg'),
                's.berat_badan',
                's.tinggi_badan',
                's.imt',
                DB::raw($statusGiziExpr . ' as status_gizi'),
                DB::raw($tekananDarahExpr . ' as tekanan_darah'),
                DB::raw($denyutNadiExpr . ' as denyut_nadi'),
                DB::raw($suhuTubuhExpr . ' as suhu_tubuh'),
                DB::raw($visusOdExpr . ' as visus_od'),
                DB::raw($visusOsExpr . ' as visus_os'),
                DB::raw($kelainanMataExpr . ' as kelainan_mata'),
                DB::raw($pendengaranKananExpr . ' as pendengaran_kanan'),
                DB::raw($pendengaranKiriExpr . ' as pendengaran_kiri'),
                DB::raw($kelainanTelingaExpr . ' as kelainan_telinga'),
                $hasGigiKaries ? 's.gigi_karies' : DB::raw('NULL as gigi_karies'),
                $hasGigiHilang ? 's.gigi_hilang' : DB::raw('NULL as gigi_hilang'),
                $hasKelainanGigi ? 's.kelainan_gigi' : DB::raw('NULL as kelainan_gigi'),
                $hasRiwayatPenyakit ? 's.riwayat_penyakit' : DB::raw('NULL as riwayat_penyakit'),
                $hasRiwayatAlergi ? 's.riwayat_alergi' : DB::raw('NULL as riwayat_alergi'),
                $hasObatDikonsumsi ? 's.obat_dikonsumsi' : DB::raw('NULL as obat_dikonsumsi'),
                $hasStatusImunisasi ? 's.status_imunisasi' : DB::raw('NULL as status_imunisasi'),
                ($hasKesimpulan ? (Schema::hasColumn('skrining_siswa_sd', 'kesimpulan') ? 's.kesimpulan' : 's.kebugaran_jantung') : DB::raw('NULL as kesimpulan')),
                ($hasTindakLanjut ? (Schema::hasColumn('skrining_siswa_sd', 'tindak_lanjut') ? 's.tindak_lanjut' : 's.kebugaran_jantung') : DB::raw('NULL as tindak_lanjut')),
                ($hasStatusSkrining ? (Schema::hasColumn('skrining_siswa_sd', 'status_skrining') ? 's.status_skrining' : 's.kebugaran_jantung') : DB::raw('NULL as status_skrining')),
            ]);

        if (!empty($this->filters['sekolah'])) {
            $query->where('data_siswa_sekolah.id_sekolah', $this->filters['sekolah']);
        }
        if (!empty($this->filters['jenis_sekolah'])) {
            $query->where('data_sekolah.id_jenis_sekolah', $this->filters['jenis_sekolah']);
        }
        if (!empty($this->filters['kelas'])) {
            $query->where('data_siswa_sekolah.id_kelas', $this->filters['kelas']);
        }

        return $query->orderBy('pasien.nm_pasien')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Sekolah',
            'Kelas',
            'Hasil CKG',
            'Berat Badan',
            'Tinggi Badan',
            'IMT',
            'Status Gizi',
            'Tekanan Darah',
            'Denyut Nadi',
            'Suhu Tubuh',
            'Visus OD',
            'Visus OS',
            'Kelainan Mata',
            'Pendengaran Kanan',
            'Pendengaran Kiri',
            'Kelainan Telinga',
            'Gigi Karies',
            'Gigi Hilang',
            'Kelainan Gigi',
            'Riwayat Penyakit',
            'Riwayat Alergi',
            'Obat Dikonsumsi',
            'Status Imunisasi',
            'Kesimpulan',
            'Tindak Lanjut',
            'Status Skrining',
        ];
    }

    public function map($row): array
    {
        static $no = 1;
        return [
            $no++,
            $row->nama_siswa ?? '-',
            $row->nama_sekolah ?? '-',
            $row->kelas ?? '-',
            $row->hasil_ckg ?? '-',
            isset($row->berat_badan) ? (string)$row->berat_badan : '-',
            isset($row->tinggi_badan) ? (string)$row->tinggi_badan : '-',
            isset($row->imt) ? (string)$row->imt : '-',
            $row->status_gizi ?? '-',
            $row->tekanan_darah ?? '-',
            isset($row->denyut_nadi) ? (string)$row->denyut_nadi : '-',
            isset($row->suhu_tubuh) ? (string)$row->suhu_tubuh : '-',
            $row->visus_od ?? '-',
            $row->visus_os ?? '-',
            $row->kelainan_mata ?? '-',
            $row->pendengaran_kanan ?? '-',
            $row->pendengaran_kiri ?? '-',
            $row->kelainan_telinga ?? '-',
            isset($row->gigi_karies) ? (string)$row->gigi_karies : '-',
            isset($row->gigi_hilang) ? (string)$row->gigi_hilang : '-',
            $row->kelainan_gigi ?? '-',
            $row->riwayat_penyakit ?? '-',
            $row->riwayat_alergi ?? '-',
            $row->obat_dikonsumsi ?? '-',
            is_array($row->status_imunisasi ?? null) ? json_encode($row->status_imunisasi) : ($row->status_imunisasi ?? '-'),
            $row->kesimpulan ?? '-',
            $row->tindak_lanjut ?? '-',
            $row->status_skrining ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            'A2:' . $highestColumn . $highestRow => [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Hasil CKG Siswa';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 28,
            'C' => 28,
            'D' => 12,
            'E' => 16,
        ];
    }
}
