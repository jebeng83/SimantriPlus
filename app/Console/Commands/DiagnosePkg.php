<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiagnosePkg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --desa=NAME        Filter by desa/kelurahan name
     * --posyandu=NAME    Filter by posyandu name
     * --periode=NAME     One of: bulan_ini, 3_bulan, 6_bulan, tahun_ini
     */
    protected $signature = 'diagnose:pkg {--desa=} {--posyandu=} {--periode=bulan_ini}';

    /**
     * The console command description.
     */
    protected $description = 'Diagnose PKG summary counts and risk distribution based on filters';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $desa = $this->option('desa');
        $posyandu = $this->option('posyandu');
        $periode = $this->option('periode') ?: 'bulan_ini';

        // Tentukan rentang tanggal
        switch ($periode) {
            case '3_bulan':
                $tanggal_awal = Carbon::now()->subMonths(2)->startOfMonth()->toDateString();
                $tanggal_akhir = Carbon::now()->endOfMonth()->toDateString();
                break;
            case '6_bulan':
                $tanggal_awal = Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
                $tanggal_akhir = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'tahun_ini':
                $tanggal_awal = Carbon::now()->startOfYear()->toDateString();
                $tanggal_akhir = Carbon::now()->endOfYear()->toDateString();
                break;
            case 'bulan_ini':
            default:
                $tanggal_awal = Carbon::now()->startOfMonth()->toDateString();
                $tanggal_akhir = Carbon::now()->endOfMonth()->toDateString();
                break;
        }

        $q = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel');

        if ($posyandu) {
            $q->where('dp.nama_posyandu', $posyandu);
        }

        if ($desa) {
            $q->where(function ($qq) use ($desa) {
                $qq->where('dp.desa', $desa)->orWhere('k.nm_kel', $desa);
            });
        }

        $q->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal)
          ->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);

        $res = $q->selectRaw(
            'COUNT(sp.id_pkg) as total_skrining, '
            . 'SUM(CASE WHEN (sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90) '
            . 'OR (sp.gds >= 200 OR sp.gdp >= 126) '
            . 'OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30) '
            . 'OR ((sp.riwayat_hipertensi = "Ya" AND sp.riwayat_diabetes = "Ya") '
            . 'OR (sp.status_merokok = "Ya" AND sp.riwayat_hipertensi = "Ya") '
            . 'OR (sp.umur >= 60 AND sp.riwayat_diabetes = "Ya")) THEN 1 ELSE 0 END) as risiko_tinggi, '
            . 'SUM(CASE WHEN (sp.tekanan_sistolik BETWEEN 120 AND 139) '
            . 'OR (sp.tekanan_diastolik BETWEEN 80 AND 89) '
            . 'OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 25 '
            . 'AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) < 30) '
            . 'OR sp.status_merokok = "Ya" OR sp.riwayat_hipertensi = "Ya" OR sp.riwayat_diabetes = "Ya" THEN 1 ELSE 0 END) as risiko_sedang_temp'
        )->first();

        $tinggi = $res->risiko_tinggi ?? 0;
        $sedang = max(0, ($res->risiko_sedang_temp ?? 0) - $tinggi);
        $total = $res->total_skrining ?? 0;
        $rendah = max(0, $total - $tinggi - $sedang);

        $this->info('Diagnose PKG Summary');
        $this->line('Filter: desa=' . ($desa ?: 'semua') . ', posyandu=' . ($posyandu ?: 'semua') . ', periode=' . $periode);
        $this->line('Tanggal: ' . $tanggal_awal . ' s/d ' . $tanggal_akhir);
        $this->table(['Total', 'Tinggi', 'Sedang', 'Rendah'], [[(int)$total, (int)$tinggi, (int)$sedang, (int)$rendah]]);

        return self::SUCCESS;
    }
}