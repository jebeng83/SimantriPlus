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
        $response = $this->requestPostBpjs('/ihs/api/rs/validate');
        $this->info($response);
    }
}
