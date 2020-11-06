<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
}