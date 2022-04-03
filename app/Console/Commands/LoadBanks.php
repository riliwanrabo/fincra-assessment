<?php

namespace App\Console\Commands;

use App\Models\Bank;
use App\Services\Fincra\FincraService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class LoadBanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banks:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Banks from a third party provider';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $banks = (new FincraService)->fetchBanks();
            collect($banks)->each(fn ($bank) => Bank::updateOrCreate([
                'code' => $bank->code,
                'name' => $bank->name,
            ]));
        } catch (\Exception $e) {
            info($e->getMessage());
            return;
        }
    }
}
