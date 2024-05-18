<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\BaseLinkerService;
use App\Services\AtomStoreService;

class SyncProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $csvFilePath;
    protected $baseLinkerService;
    protected $atomStoreService;

    public function __construct($csvFilePath)
    {
        $this->csvFilePath = $csvFilePath;
        $this->baseLinkerService = new BaseLinkerService();
        $this->atomStoreService = new AtomStoreService();
    }

    public function handle(): void
    {
        $productsBaseLinker = $this->baseLinkerService->fetchProducts();
        $overriddenPrices = $this->readCsv($this->csvFilePath);

        foreach ($productsBaseLinker as $product) {

        }
    }

    private function readCsv($filePath)
    {
        $data = array_map('str_getcsv', file($filePath));
        $prices = [];
        foreach ($data as $row) {
            $prices[$row[0]] = $row[1];
        }
        return $prices;
    }
}
