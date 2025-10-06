<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ILPProbeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ilp:probe-data {--desa=} {--posyandu=} {--start=} {--end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uji coba via terminal untuk menampilkan data ILP per kriteria (desa, posyandu, periode)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $desa = $this->option('desa');
        $posyandu = $this->option('posyandu');
        $start = $this->option('start');
        $end = $this->option('end');

        $this->info('== Uji Coba Data ILP ==');
        $this->line('Filter: desa=' . ($desa ?: 'SEMUA') . ', posyandu=' . ($posyandu ?: 'SEMUA') . ', start=' . ($start ?: '-') . ', end=' . ($end ?: '-'));

        // Daftar Desa dari data_posyandu
        $daftarDesa = DB::table('data_posyandu')
            ->whereNotNull('desa')->where('desa', '!=', '')->where('desa', '!=', '-')
            ->distinct()->orderBy('desa', 'asc')->pluck('desa');
        $this->info('Jumlah Desa (data_posyandu): ' . $daftarDesa->count());
        $this->line('Contoh 5 Desa: ' . $daftarDesa->take(5)->implode(', '));

        // Daftar Posyandu berdasar desa (data_posyandu)
        $posQuery = DB::table('data_posyandu')->whereNotNull('nama_posyandu')->where('nama_posyandu', '!=', '')->where('nama_posyandu', '!=', '-');
        if ($desa) { $posQuery->where('desa', $desa); }
        $daftarPosyandu = $posQuery->distinct()->orderBy('nama_posyandu', 'asc')->pluck('nama_posyandu');
        $this->info('Jumlah Posyandu (filter desa): ' . $daftarPosyandu->count());
        $this->line('Contoh 5 Posyandu: ' . $daftarPosyandu->take(5)->implode(', '));

        // Summary skrining dari skrining_pkg
        $sumQuery = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->selectRaw('COUNT(sp.id_pkg) as total')
            ->selectRaw('COUNT(CASE WHEN sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90 OR sp.gds >= 200 OR sp.gdp >= 126 OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30) THEN 1 END) as risiko_tinggi')
            ->selectRaw('COUNT(CASE WHEN (sp.tekanan_sistolik BETWEEN 120 AND 139) OR (sp.tekanan_diastolik BETWEEN 80 AND 89) OR sp.status_merokok = "Ya" OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) BETWEEN 25 AND 29.9) THEN 1 END) as risiko_sedang_temp');
        if ($desa) { $sumQuery->where('dp.desa', $desa); }
        if ($posyandu) { $sumQuery->where('dp.nama_posyandu', $posyandu); }
        if ($start) { $sumQuery->whereDate('sp.tanggal_skrining', '>=', $start); }
        if ($end) { $sumQuery->whereDate('sp.tanggal_skrining', '<=', $end); }
        $sum = $sumQuery->first();
        $risSedang = max(0, ($sum->risiko_sedang_temp ?? 0) - ($sum->risiko_tinggi ?? 0));
        $risRendah = max(0, ($sum->total ?? 0) - ($sum->risiko_tinggi ?? 0) - $risSedang);
        $this->info('Summary Skrining: total=' . ($sum->total ?? 0) . ', tinggi=' . ($sum->risiko_tinggi ?? 0) . ', sedang=' . $risSedang . ', rendah=' . $risRendah);

        // Faktor risiko
        $frQuery = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->selectRaw('SUM(CASE WHEN sp.riwayat_hipertensi = "Ya" THEN 1 ELSE 0 END) as hipertensi')
            ->selectRaw('SUM(CASE WHEN sp.riwayat_diabetes = "Ya" THEN 1 ELSE 0 END) as diabetes')
            ->selectRaw('SUM(CASE WHEN sp.status_merokok = "Ya" THEN 1 ELSE 0 END) as merokok')
            ->selectRaw('SUM(CASE WHEN sp.umur >= 60 THEN 1 ELSE 0 END) as lansia');
        if ($desa) { $frQuery->where('dp.desa', $desa); }
        if ($posyandu) { $frQuery->where('dp.nama_posyandu', $posyandu); }
        if ($start) { $frQuery->whereDate('sp.tanggal_skrining', '>=', $start); }
        if ($end) { $frQuery->whereDate('sp.tanggal_skrining', '<=', $end); }
        $fr = $frQuery->first();
        $this->info('Faktor Risiko: hipertensi=' . ($fr->hipertensi ?? 0) . ', diabetes=' . ($fr->diabetes ?? 0) . ', merokok=' . ($fr->merokok ?? 0) . ', lansia=' . ($fr->lansia ?? 0));

        // Distribusi umur
        $duQuery = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->selectRaw('SUM(CASE WHEN sp.umur < 30 THEN 1 ELSE 0 END) as umur_20_29')
            ->selectRaw('SUM(CASE WHEN sp.umur >= 30 AND sp.umur < 40 THEN 1 ELSE 0 END) as umur_30_39')
            ->selectRaw('SUM(CASE WHEN sp.umur >= 40 AND sp.umur < 50 THEN 1 ELSE 0 END) as umur_40_49')
            ->selectRaw('SUM(CASE WHEN sp.umur >= 50 AND sp.umur < 60 THEN 1 ELSE 0 END) as umur_50_59')
            ->selectRaw('SUM(CASE WHEN sp.umur >= 60 THEN 1 ELSE 0 END) as umur_60_plus');
        if ($desa) { $duQuery->where('dp.desa', $desa); }
        if ($posyandu) { $duQuery->where('dp.nama_posyandu', $posyandu); }
        if ($start) { $duQuery->whereDate('sp.tanggal_skrining', '>=', $start); }
        if ($end) { $duQuery->whereDate('sp.tanggal_skrining', '<=', $end); }
        $du = $duQuery->first();
        $this->info('Distribusi Umur: 20-29=' . ($du->umur_20_29 ?? 0) . ', 30-39=' . ($du->umur_30_39 ?? 0) . ', 40-49=' . ($du->umur_40_49 ?? 0) . ', 50-59=' . ($du->umur_50_59 ?? 0) . ', ≥60=' . ($du->umur_60_plus ?? 0));

        // Trend bulanan (6 bulan terakhir)
        $this->info('Trend Skrining (6 bulan terakhir):');
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth()->format('Y-m-d');
            $monthEnd = now()->subMonths($i)->endOfMonth()->format('Y-m-d');
            $tQuery = DB::table('skrining_pkg as sp')->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu');
            if ($desa) { $tQuery->where('dp.desa', $desa); }
            if ($posyandu) { $tQuery->where('dp.nama_posyandu', $posyandu); }
            $tQuery->whereDate('sp.tanggal_skrining', '>=', $monthStart)->whereDate('sp.tanggal_skrining', '<=', $monthEnd);
            $total = $tQuery->count('sp.id_pkg');
            $this->line('- ' . now()->subMonths($i)->format('M Y') . ': ' . $total);
        }

        $this->info('Selesai.');
        return 0;
    }
}