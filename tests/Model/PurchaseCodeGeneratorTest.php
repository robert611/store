<?php

namespace App\Tests\Model;

use App\Model\PurchaseCodeGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\PurchaseRepository;

class PurchaseCodeGeneratorTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient();
    }
    
    public function testIfCodeCanBeGenerated()
    {
        $purchaseRepository = static::$container->get(PurchaseRepository::class);

        $purchaseCodeGenerator = new PurchaseCodeGenerator($purchaseRepository);

        $code = $purchaseCodeGenerator->generate();

        $this->assertTrue(strlen($code) == 12);
    }
}