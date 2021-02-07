<?php 

namespace App\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;

class ProductEntityTest extends WebTestCase
{   
    public $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }
    
    public function testGetHighestBiderId()
    {
        $product = static::$container->get(ProductRepository::class)->findOneBy(['name' => 'Auction Product']);

        $highestBiderId = $product->getHighestBiderId();

        $user = static::$container->get(UserRepository::class)->find($highestBiderId);

        $this->assertEquals($user->getUsername(), 'mario11');
    }
}