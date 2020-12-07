<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ProductRepository;

class IndexControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIfHomepageIsSuccessfull()
    {
        $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    public function testIfProductListingIsSuccessfull()
    {
        $this->client->request('GET', '/listing');
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }
    
    public function testIfProductListingPaginationWorksCorrectly()
    {
        $categoryId = static::$container->get(ProductRepository::class)->findOneBy(['name' => 'Toy'])->getCategory()->getId();

        $crawler = $this->client->request('GET', "/listing?page=1&product=toy&category={$categoryId}");

        $this->assertSelectorExists('ul.m-pagination');
        $this->assertSelectorExists('li.waves-effect');

        $link = $crawler->selectLink('1')->link();

        $this->client->click($link);

        $this->assertEquals($link->getUri(), "http://localhost/listing?page=1&product=toy&category={$categoryId}");
    }
}   