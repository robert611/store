<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;

class SecurityControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIfLoginPageIsSuccessfull()
    {
        $this->client->request('GET', '/login');
        
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
        $this->assertSelectorTextContains('html h1', 'Logowanie');
        $this->assertSelectorTextContains('html', 'Login');
    }

    public function testIfUserCanLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $form['email'] = 'casual_user@interia.pl';
        $form['password'] = 'password';

        $crawler = $this->client->submitForm('Login', $form);

        $this->assertResponseRedirects('/');

        $crawler = $this->client->request('GET', '/');
        $this->assertSelectorTextNotContains('html', 'Exception');

        $crawler = $this->client->clickLink('Wystaw przedmiot');
    }
}