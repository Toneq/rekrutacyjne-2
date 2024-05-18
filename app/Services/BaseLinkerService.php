<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BaseLinkerService
{
    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url = env('BASELINKER_API_URL');
        $this->token = env('BASELINKER_API_TOKEN');
    }

    public function fetchProducts()
    {
        $response1 = Http::withHeaders([
            'X-BLToken' => $this->token,
        ])->post($this->url, [
            'method' => 'getProductsList',
        ]);
    
        if (!$response1->successful()) {
            return response()->json(['error' => 'Failed to fetch products'], 500);
        }
    
        $endpoint1Data = $response1->json();
        $products = collect($endpoint1Data['products']);
        $productIds = $products->pluck('product_id');
    
        $methodParams = [
            "inventory_id" => "307",
            "products" => $productIds->toArray()
        ];
    
        $response2 = Http::withHeaders([
            'X-BLToken' => $this->token,
        ])->asForm()->post($this->url, [
            "method" => "getInventoryProductsData",
            "parameters" => json_encode($methodParams)    
        ]);
    
        if (!$response2->successful()) {
            return response()->json(['error' => 'Failed to fetch inventory products data'], 500);
        }

        $endpoint2Data = $response2->json();
    
        $mergedData = [];
        foreach ($endpoint1Data['products'] as $product) {
            $productId = $product['product_id'];
            if (isset($endpoint2Data['products'][$productId])) {
                $mergedData[] = [
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'ean' => $product['ean'],
                    'quantity' => $product['quantity'],
                    'price_brutto' => $product['price_brutto'],
                    'data' => $endpoint2Data['products'][$productId]
                ];
            }
        }
    
        return response()->json($mergedData);
    }
}
