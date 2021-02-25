<?php 

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;

class CalculateFilterPrices
{
    public function getFitlerPrices(array $products): array 
    {
        $filterPrices = array();

        $productsSummedPrice = $this->getProductsSummedPrice($products);

        $productsAveragePrice = count($products) > 0 ? (int) ($productsSummedPrice / count($products)) : 0;  
        
        $filterPrices[0] = (int) (ceil(($productsAveragePrice * 0.4) / 10)) * 10;
        $filterPrices[1] = (int) (ceil(($productsAveragePrice * 0.8) / 10)) * 10;
        $filterPrices[2] = (int) (ceil(($productsAveragePrice * 1.5) / 10)) * 10;
        $filterPrices[3] = 1000000000;

        return $filterPrices;
    }

    public function getProductsSummedPrice(array $products): float
    {
        $productsSummedPrice = 0;

        (new ArrayCollection($products))->map(function($product) use (&$productsSummedPrice) {
            $productsSummedPrice += $product->getPrice();
        });

        return $productsSummedPrice;
    }
}