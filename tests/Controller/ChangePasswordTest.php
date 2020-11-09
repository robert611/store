<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class ChangePasswordTest extends WebTestCase
{
    public $client = null;

    private $testCasualUser;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->testCasualUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'casual_user@interia.pl']);
    }

    public function testIfUserPasswordCanBeChanged()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('POST', '/account/change/password', ['new-password' => 'test_password', 'new-password-repeat' => 'test_password', 'current-password' => 'password']);
        
        $this->assertResponseRedirects('/account');

        $editedPassword = static::$container->get(UserRepository::class)->find($this->testCasualUser->getId())->getPassword();

        $this->assertTrue(password_verify('test_password', $editedPassword));

        $crawler = $this->client->request('GET', '/account');
        $this->assertSelectorTextContains('html', 'Twoje hasło zostało zmienione.');
    }

    public function testIfUserNeedsToGiveCorrectCurrentPassword()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('POST', '/account/change/password', ['new-password' => 'test_password', 'new-password-repeat' => 'test_password', 'current-password' => 'wrong_password']);
        
        $this->assertResponseRedirects('/account');

        $editedPassword = static::$container->get(UserRepository::class)->find($this->testCasualUser->getId())->getPassword();
        $this->assertTrue(!password_verify('test_password', $editedPassword));

        $crawler = $this->client->request('GET', '/account');
        $this->assertSelectorTextContains('html', 'Podano nieprawidłowe hasło.');
    }

    public function testIfUserNeedsToCorrectlyRepeatNewPassword()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('POST', '/account/change/password', ['new-password' => 'test_password', 'new-password-repeat' => 'wrong_test_password', 'current-password' => 'password']);
        
        $this->assertResponseRedirects('/account');

        $editedPassword = static::$container->get(UserRepository::class)->find($this->testCasualUser->getId())->getPassword();
        $this->assertTrue(!password_verify('test_password', $editedPassword));

        $crawler = $this->client->request('GET', '/account');
        $this->assertSelectorTextContains('html', 'Podane hasła nie są takie same.');
    }

    public function testIfNewPasswordMustHaveAtLeastSixCharacters()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('POST', '/account/change/password', ['new-password' => 'test', 'new-password-repeat' => 'test', 'current-password' => 'password']);
        
        $this->assertResponseRedirects('/account');

        $editedPassword = static::$container->get(UserRepository::class)->find($this->testCasualUser->getId())->getPassword();
        $this->assertTrue(!password_verify('test', $editedPassword));

        $crawler = $this->client->request('GET', '/account');
        $this->assertSelectorTextContains('html', 'Twoje hasło musi mieć przynajmniej 6 i nie więcej niż 32 znaki.');
    }

    public function testIfNewPasswordMustHaveAtBestThirtyTwoCharacters()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('POST', '/account/change/password', ['new-password' => 'test_test_test_test_test_test_test', 'new-password-repeat' => 'test_test_test_test_test_test_test', 'current-password' => 'password']);
        
        $this->assertResponseRedirects('/account');

        $editedPassword = static::$container->get(UserRepository::class)->find($this->testCasualUser->getId())->getPassword();
        $this->assertTrue(!password_verify('test_test_test_test_test_test_test', $editedPassword));

        $crawler = $this->client->request('GET', '/account');
        $this->assertSelectorTextContains('html', 'Twoje hasło musi mieć przynajmniej 6 i nie więcej niż 32 znaki.');
    }
}
