<?php

namespace App\Tests\Model;

use App\Model\ChooseHomepageProducts;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ProductRepository;

class ChooseHomepageProductsTest extends WebTestCase
{
    private $productRepository;

    private $homepageProducts;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->productRepository = static::$container->get(ProductRepository::class);

        $this->homepageProducts = (new ChooseHomepageProducts($this->productRepository))->getHomepageProducts();
    }

    public function testGetUnfetchedProductsIds()
    {
        $chooseHomepageProducts = new ChooseHomepageProducts($this->productRepository);

        $products = $this->productRepository->findAll();
        
        $productsToSave = [$products[0], $products[1], $products[2], $products[3]];

        $savedProductsIds = [$products[0]->getId(), $products[1]->getId(), $products[2]->getId(), $products[3]->getId()];

        $chooseHomepageProducts->saveFetchedProductsIds($productsToSave);

        $productsIdsToLookThrough = array();
        
        foreach ($products as $product)
        {
            $productsIdsToLookThrough[] = ['id' => $product->getId()];
        }

        $unfetchedProductsIds = $chooseHomepageProducts->getUnfetchedProductsIds($productsIdsToLookThrough);
        
        foreach ($unfetchedProductsIds as $productId)
        {
            $this->assertTrue(!in_array($productId[0]['id'], $savedProductsIds));
        }
    }

    public function testIfGetHomepageProductsReturnsArrayWithProperIndexes()
    {
        $chooseHomepageProducts = new ChooseHomepageProducts($this->productRepository);

        $homepageProducts = $chooseHomepageProducts->getHomepageProducts();

        $this->assertTrue(isset($homepageProducts['six_products_row']));
        $this->assertTrue(isset($homepageProducts['second_six_products_row']));
        $this->assertTrue(isset($homepageProducts['three_products_row']));
        $this->assertTrue(isset($homepageProducts['two_products_row']));
        $this->assertTrue(isset($homepageProducts['second_two_products_row']));

        $this->assertEquals(count($homepageProducts), 5);
    }

    public function testIfFirstRowWithSixProductsConsistsOfTheSameCategory()
    {
        $firstRowWithSixProducts = $this->homepageProducts['six_products_row'];

        $isRowConsistent = true;

        $categoryId = $firstRowWithSixProducts[0]->getCategory()->getId();

        foreach ($firstRowWithSixProducts as $product)
        {
            if ($product->getCategory()->getId() !== $categoryId) $isRowConsistent = false;
        }
        
        $this->assertTrue($isRowConsistent);
    }

    public function testIfSecondRowWithSixProductsConsistsOfTheSameCategory()
    {
        $secondRowWithSixProducts = $this->homepageProducts['second_six_products_row'];

        $isRowConsistent = true;

        $categoryId = $secondRowWithSixProducts[0]->getCategory()->getId();

        foreach ($secondRowWithSixProducts as $product)
        {
            if ($product->getCategory()->getId() !== $categoryId) $isRowConsistent = false;
        }
        
        $this->assertTrue($isRowConsistent);
    }

    public function testIfFirstTwoRowsConsistsOfProductsOfDiffrentCategoryThanOtherRow()
    {
        $firstRowCategory = $this->homepageProducts['six_products_row'][0]->getCategory()->getId();
        $secondRowCategory = $this->homepageProducts['second_six_products_row'][0]->getCategory()->getId();

        $this->assertNotEquals($firstRowCategory, $secondRowCategory);
    }
}