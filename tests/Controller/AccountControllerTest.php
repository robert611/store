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

    /**
     * @runInSeparateProcess
     */
    public function testIfThirdPersonCannotSeeConversation()
    {
        $conversation = static::$container->get(ConversationRepository::class)->findAll()[0];

        $authorId = $conversation->getAuthor()->getId();
        $recipientId = $conversation->getRecipient()->getId();

        $users = static::$container->get(UserRepository::class)->findAll();

        $i = 1;
        do {
            $user = $users[$i];

            $i++;
        } while(is_object($user) && ($user->getId() == $authorId || $user->getId() == $recipientId));

        $this->client->loginUser($user);

        $this->client->request('GET', "/account/user/conversation/{$conversation->getId()}");
        
        $this->assertResponseRedirects('/');
    }

    public function testIfMessageAfterPostingProductPageIsSuccessfull()
    {
        $this->client->loginUser($this->testAdminUser);

        $productId = static::$container->get(ProductRepository::class)->findAll()[0]->getId();

        $this->client->request('GET', "/account/product/posting/message/{$productId}");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
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

    /**
     * @dataProvider provideUrlsForAnonymousUser
     */
    public function testIfAnonymousUserCannotSeePage($url)
    {
        $this->client->request('GET', $url);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testIfAnonymousUserCannotSeeMessageAfterPosting()
    {
        $productId = static::$container->get(ProductRepository::class)->findAll()[0]->getId();

        $this->client->request('GET', "/account/product/posting/message/{$productId}");

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function provideUrls()
    {
        return [
            ['/account'],
            ['/account/user/auctions/list'],
            ['/account/user/products/bought'],
            ['/account/user/conversations']
        ];
    }

    public function provideUrlsForAnonymousUser()
    {
        return array_merge($this->provideUrls(), [
            ['/account/change/email'],
            ['/account/change/password']
        ]);
    }
}