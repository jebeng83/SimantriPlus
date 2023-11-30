<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Traits\BpjsTraits;

class SendBPJS extends Command
{
    use BpjsTraits;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:bpjs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data['param'] = "0002613483279";
        $data['kodedokter'] = 14062;
        $response = $this->requestPostBpjs('api/rs/validate', $data);
        $this->info($response);
    }
}
