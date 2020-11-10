<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class AdminControlletTest extends WebTestCase
{
    public $client = null;

    private $testCasualUser;

    private $testAdminUser;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->testCasualUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'casual_user@interia.pl']);
        $this->testAdminUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'admin@interia.pl']);
    }

    public function testIfAdminPageIsSuccessfull()
    {
        $this->client->loginUser($this->testAdminUser);

        $this->client->request('GET', "/admin");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    public function testIfOnlyAdminCanSeeAdminPage()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('GET', "/admin");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->assertResponseRedirects("/");
    }
}