<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class ChangeEmailTest extends WebTestCase
{
    public $client = null;

    private $testCasualUser;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->testCasualUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'casual_user@interia.pl']);
    }

    public function testIfUserEmailCanBeChanged()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('POST', '/account/change/email', ['new-email' => 'new_test_email@interia.pl', 'new-email-repeat' => 'new_test_email@interia.pl', 'password' => 'password']);
        
        $this->assertResponseRedirects('/account');

        $editedEmail = static::$container->get(UserRepository::class)->find($this->testCasualUser->getId())->getEmail();

        $this->assertEquals($editedEmail, 'new_test_email@interia.pl');

        $crawler = $this->client->request('GET', '/account');
        $this->assertSelectorTextContains('html', 'Twój adres email został zmieniony.');
    }

    public function testIfUserNeedsToGiveCorrectCurrentPassword()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('POST', '/account/change/email', ['new-email' => 'new_test_email@interia.pl', 'new-email-repeat' => 'new_test_email@interia.pl', 'password' => 'wrong_password']);
        
        $this->assertResponseRedirects('/account');

        $editedEmail = static::$container->get(UserRepository::class)->find($this->testCasualUser->getId())->getEmail();
        $this->assertNotEquals($editedEmail, 'new_test_email@interia.pl');

        $crawler = $this->client->request('GET', '/account');
        $this->assertSelectorTextContains('html', 'Podano nieprawidłowe hasło.');
    }

    public function testIfUserNeedsToCorrectlyRepeatNewEmail()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('POST', '/account/change/email', ['new-email' => 'new_test_email@interia.pl', 'new-email-repeat' => 'wrong_new_test_email@interia.pl', 'password' => 'wrong_password']);
        
        $this->assertResponseRedirects('/account');

        $editedEmail = static::$container->get(UserRepository::class)->find($this->testCasualUser->getId())->getEmail();
        $this->assertNotEquals($editedEmail, 'new_test_email@interia.pl');

        $crawler = $this->client->request('GET', '/account');
        $this->assertSelectorTextContains('html', 'Musisz podać dwa takie same adresy email.');
    }
}