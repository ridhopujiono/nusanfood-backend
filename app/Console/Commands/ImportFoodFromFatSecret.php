<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ImportFoodFromFatSecretJob;

class ImportFoodFromFatSecret extends Command
{
    protected $signature = 'fatsecret:import {search_expression}';
    protected $description = 'Import food & nutrition data from FatSecret';

    public function handle()
    {
        $search = $this->argument('search_expression');

        ImportFoodFromFatSecretJob::dispatch($search);

        $this->info("Job dispatched for search: {$search}");
    }
}
