<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PCare\ReferensiPoliController;
use App\Http\Controllers\PCare\ReferensiDokterController;
use Illuminate\Http\Request;

class TestPcareData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pcare-data {type=poli} {--tanggal=} {--kodepoli=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PCare data (poli/dokter)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $tanggal = $this->option('tanggal') ?: date('Y-m-d');
        $kodepoli = $this->option('kodepoli');

        $this->info("Testing PCare {$type} data...");
        $this->info("Tanggal: {$tanggal}");
        if ($kodepoli) {
            $this->info("Kode Poli: {$kodepoli}");
        }
        $this->line('');

        try {
            if ($type === 'poli') {
                $this->testPoliData();
            } elseif ($type === 'dokter') {
                $this->testDokterData($tanggal, $kodepoli);
            } else {
                $this->error("Type tidak valid. Gunakan 'poli' atau 'dokter'");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function testPoliData()
    {
        $this->info("=== Testing Data Poli ===");
        
        $controller = new ReferensiPoliController();
        $request = new Request();
        
        $response = $controller->getPoli($request);
        $data = json_decode($response->getContent(), true);
        
        if (isset($data['success']) && $data['success']) {
            $this->info("✓ Data Poli berhasil dimuat");
            $this->info("Source: " . ($data['source'] ?? 'unknown'));
            $this->info("Total data: " . count($data['data'] ?? []));
            
            if (!empty($data['data'])) {
                $this->table(
                    ['No', 'Kode Poli', 'Nama Poli'],
                    collect($data['data'])->take(5)->map(function($item, $index) {
                        return [
                            $index + 1,
                            $item['kdPoli'] ?? '-',
                            $item['nmPoli'] ?? '-'
                        ];
                    })->toArray()
                );
            }
        } else {
            $this->error("✗ Gagal memuat data poli");
            $this->error("Message: " . ($data['message'] ?? 'Unknown error'));
        }
    }

    private function testDokterData($tanggal, $kodepoli)
    {
        $this->info("=== Testing Data Dokter ===");
        
        $controller = new ReferensiDokterController();
        $request = new Request();
        
        if ($kodepoli) {
            $response = $controller->getDokter($request, $kodepoli, $tanggal);
        } else {
            $response = $controller->getDokter($request, null, $tanggal);
        }
        
        $data = json_decode($response->getContent(), true);
        
        if (isset($data['success']) && $data['success']) {
            $this->info("✓ Data Dokter berhasil dimuat");
            $this->info("Source: " . ($data['source'] ?? 'unknown'));
            $this->info("Total data: " . count($data['data'] ?? []));
            
            if (!empty($data['data'])) {
                $this->table(
                    ['No', 'Kode Dokter', 'Nama Dokter', 'Kode Poli', 'Nama Poli'],
                    collect($data['data'])->take(5)->map(function($item, $index) {
                        return [
                            $index + 1,
                            $item['kdDokter'] ?? '-',
                            $item['nmDokter'] ?? '-',
                            $item['kdPoli'] ?? '-',
                            $item['nmPoli'] ?? '-'
                        ];
                    })->toArray()
                );
            }
        } else {
            $this->error("✗ Gagal memuat data dokter");
            $this->error("Message: " . ($data['message'] ?? 'Unknown error'));
        }
    }
}
