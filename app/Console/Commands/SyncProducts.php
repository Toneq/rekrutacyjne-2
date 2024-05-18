<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncProductsJob;

class SyncProducts extends Command
{
    protected $signature = 'sync:products {csvFilePath}';
    protected $description = 'Synchronize products from BaseLinker to AtomStore with overridden prices from CSV';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $csvFilePath = $this->argument('csvFilePath');
        SyncProductsJob::dispatch($csvFilePath);
        $this->info('Sync job dispatched!');
    }
}