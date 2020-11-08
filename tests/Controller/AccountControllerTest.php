<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\ConversationRepository;
use App\Repository\ProductRepository;

class AccountControllerTest extends WebTestCase
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

    public function testIfShowConversationsPageIsSuccessfull()
    {
        $this->client->loginUser($this->testCasualUser);

        $conversationId = static::$container->get(ConversationRepository::class)->findAll()[0]->getId();

        $this->client->request('GET', "/account/user/conversation/{$conversationId}");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    public function testIfMessageAfterPostingProductPageIsSuccessfull()
    {
        $this->client->loginUser($this->testAdminUser);

        $productId = static::$container->get(ProductRepository::class)->findAll()[0]->getId();

        $this->client->request('GET', "/account/product/posting/message/{$productId}");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
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

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function provideUrls()
    {
        return [
            ['/account'],
            ['/account/user/auctions/list'],
            ['/account/user/products/bought'],
            ['/account/user/conversations'],
        ];
    }
}