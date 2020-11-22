<?php 

namespace App\Test\Repository;

use App\Entity\Product;
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
}