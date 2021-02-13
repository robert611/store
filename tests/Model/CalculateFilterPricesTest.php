<?php

namespace App\Tests\Model;

use App\Model\CalculateFilterPrices;
use PHPUnit\Framework\TestCase;
use App\Entity\Product;

class CalculateFilterPricesTest extends TestCase
{
    private $calculateFilterPrices;

    public function setUp()
    {
        $this->calculateFilterPrices =  new CalculateFilterPrices();
    }

    public function testGetFitlerPrices()
    {
        $products = $this->getProducts();

        $filteredPrices = $this->calculateFilterPrices->getFitlerPrices($products);

        $this->assertEquals($filteredPrices[0], 170);
        $this->assertEquals($filteredPrices[1], 330);
        $this->assertEquals($filteredPrices[2], 620);
        $this->assertEquals($filteredPrices[3], 1000000000);

        $this->assertEquals(count($filteredPrices), 4);
    }

    public function testGetProductsSummedPrice()
    {
        $products = $this->getProducts();

        $productsSummedPrice = $this->calculateFilterPrices->getProductsSummedPrice($products);

        $this->assertEquals($productsSummedPrice, 2050);
    }

    public function getProducts()
    {
        $products = array();

        $firstProduct = new Product();
        $firstProduct->setPrice(200);

        $secondProduct = new Product();
        $secondProduct->setPrice(500);

        $thirdProduct = new Product();
        $thirdProduct->setPrice(270);

        $fourthProduct = new Product();
        $fourthProduct->setPrice(80);

        $fifthProduct = new Product();
        $fifthProduct->setPrice(1000);

        $products[] = $firstProduct;
        $products[] = $secondProduct;
        $products[] = $thirdProduct;
        $products[] = $fourthProduct;
        $products[] = $fifthProduct;

        return $products; 
    }
}