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
            $productCode = $product['ean'];
            if (isset($overriddenPrices[$productCode])) {
                $product['price_brutto'] = $overriddenPrices[$productCode];
            }

            $stock = 0;
            foreach ($product["data"]['stock'] as $key => $value) {
                $stock += $value;
            }

            $payload = [
                'code' => $product['ean'],
                'name' => $product['name'],
                'vat_rate' => $product["data"]['tax_rate'],
                'price' => $product['price_brutto'],
                'stock' => $stock,
                'attributes' => [
                    'weight' => $product["data"]['weight'],
                    'height' => $product["data"]['height'],
                    'width' => $product["data"]['width'],
                    'length' => $product["data"]['length'],
                    'star' => $product["data"]['star'],
                ],
            ];

            $this->atomStoreService->setProduct($payload);
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
