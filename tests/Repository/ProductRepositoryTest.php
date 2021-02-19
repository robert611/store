<?php 

namespace App\Test\Repository;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindProductByNameAndCategoryMethod()
    {
        $products = $this->entityManager
            ->getRepository(Product::class)
            ->findProductByNameAndCategory(null, null, null);


        foreach ($products as $product) {
            $this->assertTrue($product->getQuantity() > 0);
            $this->assertTrue($product->getIsSoldOut() != 1);
            $this->assertTrue($product->getIsDeleted() != 1);
        }
    }

    public function testFindProductByName()
    {
        $lookingPhrase = 'Pral';

        $products = $this->entityManager
            ->getRepository(Product::class)
            ->findProductByNameAndCategory($lookingPhrase, null, null);


        foreach ($products as $product) {
            $this->assertTrue(is_int(strpos($product->getName(), $lookingPhrase)));
        }
    }

    public function testFindProductByCategory()
    {
        $lookingCategory = $this->entityManager->getRepository(Product::class)->findAll()[0]->getCategory()->getId();

        $products = $this->entityManager
            ->getRepository(Product::class)
            ->findProductByNameAndCategory(null, $lookingCategory, null);

        foreach ($products as $product) {
            $this->assertTrue($lookingCategory == $product->getCategory()->getId());
        }
    }

    
    public function testFindProductWithRandomCategoryName()
    {
        $lookingCategory = 'Kategorie';

        $foundProducts = $this->entityManager
            ->getRepository(Product::class)
            ->findProductByNameAndCategory(null, $lookingCategory, null);

        $this->assertTrue(count($foundProducts) > 0);
    }

    public function testFindProductByOwner()
    {
        $lookingOwner = $this->entityManager
            ->getRepository(Product::class)->findAll()[0]->getOwner()->getId();

        $products = $this->entityManager
            ->getRepository(Product::class)
            ->findProductByNameAndCategory(null, null, $lookingOwner);

        foreach ($products as $product) {
            $this->assertTrue($lookingOwner == $product->getOwner()->getId());
        }
    }

    public function testFindProductByNameAndCategory()
    {
        $lookingCategory = $this->entityManager->getRepository(Product::class)->findAll()[0]->getCategory()->getId();
        $lookingPhrase = $this->entityManager->getRepository(Product::class)->findAll()[0]->getName();

        $products = $this->entityManager
            ->getRepository(Product::class)
            ->findProductByNameAndCategory($lookingPhrase, $lookingCategory, null);
        
        foreach ($products as $product) {
            $this->assertTrue(is_int(strpos($product->getName(), $lookingPhrase)));
            $this->assertTrue($lookingCategory == $product->getCategory()->getId());
        }
    }

    public function testFindProductByNameAndOwner()
    {
        $lookingOwner = $this->entityManager->getRepository(Product::class)->findAll()[0]->getOwner()->getId();
        $lookingPhrase = $this->entityManager->getRepository(Product::class)->findAll()[0]->getName();

        $products = $this->entityManager
            ->getRepository(Product::class)
            ->findProductByNameAndCategory($lookingPhrase, null, $lookingOwner);
        
        foreach ($products as $product) {
            $this->assertTrue(is_int(strpos($product->getName(), $lookingPhrase)));
            $this->assertTrue($lookingOwner == $product->getOwner()->getId());
        }
    }

    public function testFindProductByCategoryAndOwner()
    {
        $lookingOwner = $this->entityManager->getRepository(Product::class)->findAll()[0]->getOwner()->getId();
        $lookingCategory = $this->entityManager->getRepository(Product::class)->findAll()[0]->getCategory()->getId();

        $products = $this->entityManager
            ->getRepository(Product::class)
            ->findProductByNameAndCategory(null, $lookingCategory, $lookingOwner);
        
        foreach ($products as $product) {
            $this->assertTrue($lookingCategory == $product->getCategory()->getId());
            $this->assertTrue($lookingOwner == $product->getOwner()->getId());
        }
    }

    public function testFindProductByCategoryNameAndOwner()
    {
        $lookingPhrase = $this->entityManager->getRepository(Product::class)->findAll()[0]->getName();
        $lookingOwner = $this->entityManager->getRepository(Product::class)->findAll()[0]->getOwner()->getId();
        $lookingCategory = $this->entityManager->getRepository(Product::class)->findAll()[0]->getCategory()->getId();

        $products = $this->entityManager
            ->getRepository(Product::class)
            ->findProductByNameAndCategory($lookingPhrase, $lookingCategory, $lookingOwner);
        
        foreach ($products as $product) {
            $this->assertTrue(is_int(strpos($product->getName(), $lookingPhrase)));
            $this->assertTrue($lookingCategory == $product->getCategory()->getId());
            $this->assertTrue($lookingOwner == $product->getOwner()->getId());
        }
    }

    public function testGetNumberOfAllProducts()
    {
        $productsAmount = $this->entityManager
            ->getRepository(Product::class)
            ->getNumberOfAllProducts();

        $productsAmountGotUsingBuiltInMethod = count($this->entityManager
            ->getRepository(Product::class)
            ->findAll());

        $this->assertEquals($productsAmount, $productsAmountGotUsingBuiltInMethod);
    }   
    
    public function testGetCategoriesWithGivenNumberOfProducts()
    {
        $minimalCategoryNumberOfProducts = 3;

        $categories = $this->entityManager
            ->getRepository(Product::class)
            ->getCategoriesWithGivenNumberOfProducts($minimalCategoryNumberOfProducts);

        foreach ($categories as $category)
        {
            $categoryProducts = $this->entityManager->getRepository(Product::class)->findBy(['category' => $category['category_id']]);

            $this->assertTrue(count($categoryProducts) >= $minimalCategoryNumberOfProducts);
        }
    }

    public function testGetRandomProducts()
    {
        $limit = 3;
        $excludedProducts = [];

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findAll()[1];

        $randomProductsIds = $this->entityManager
            ->getRepository(Product::class)
            ->getRandomCategoryProductsIds($category->getId(), $limit, $excludedProducts);

        $randomProducts = $this->entityManager
            ->getRepository(Product::class)
            ->getRandomProducts($limit, $randomProductsIds);


        $this->assertTrue(count($randomProducts) <= $limit);

        foreach ($randomProducts as $product)
        {
            $this->assertTrue(in_array(['id' => $product->getId()], $randomProductsIds));
        }
    }

    public function testGetRandomProductsIds()
    {
        $productsAmount = 5;

        $productsIds = $this->entityManager
            ->getRepository(Product::class)
            ->getRandomProductsIds($productsAmount);

        $this->assertTrue(count($productsIds) <= $productsAmount);
        $this->assertTrue(isset($productsIds[0]['id']));
    }

    public function testGetRandomCategoryProductsIds()
    {
        $limit = 5;
        $excludedProducts = [];
        
        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findAll()[1];

        $givenCategoryRandomProductsIds = $this->entityManager
            ->getRepository(Product::class)
            ->getRandomCategoryProductsIds($category->getId(), $limit, $excludedProducts);

        $this->assertTrue(count($givenCategoryRandomProductsIds) <= $limit);
        $this->assertTrue(isset($givenCategoryRandomProductsIds[0]['id']));
    }

    public function testGetRandomCategoryProducts()
    {
        $limit = 5;
        $excludedProducts = [];

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findAll()[1];

        $randomCategoryProducts = $this->entityManager
            ->getRepository(Product::class)
            ->getRandomCategoryProducts($category->getId(), $limit, $excludedProducts);

        $this->assertTrue(count($randomCategoryProducts) <= $limit);
        $this->assertTrue(is_string($randomCategoryProducts[0]->getName()));
    }
}