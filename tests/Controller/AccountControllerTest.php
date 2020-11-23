<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\ConversationRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use App\Entity\Purchase;
use App\Entity\PurchaseProduct;

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

    public function testIfPaginationInBoughtProductsPageWorks()
    {
        $this->client->loginUser($this->testCasualUser);

        $entityManager = static::$container->get('doctrine.orm.entity_manager');

        $purchase = new Purchase();
        $purchase->setUser($this->testCasualUser);
        $purchase->setPrice(100);
        $purchase->setCreatedAt(new \DateTime());

        $product = static::$container->get(ProductRepository::class)->findAll()[0];

        for($i = 1; $i <= 21; $i++) {
            $purchaseProduct = new PurchaseProduct();
            $purchaseProduct->setPurchase($purchase);
            $purchaseProduct->setQuantity(1);
            $purchaseProduct->setProduct($product);
            $purchaseProduct->setDeliveryType($product->getDeliveryTypes()[0]);
            $purchaseProduct->setIsPaid(1);

            $purchase->addPurchaseProduct($purchaseProduct);

            $entityManager->persist($purchaseProduct);
        }

        $entityManager->persist($purchase);

        $entityManager->flush();

        $crawler = $this->client->request('GET', "/account/user/products/bought");

        $this->assertSelectorExists('ul.m-pagination');
        $this->assertSelectorExists('li.waves-effect');

        $link = $crawler->selectLink('3')->link();

        $this->client->click($link);

        $this->assertEquals($link->getUri(), "http://localhost/account/user/products/bought?page=3");
    }

    public function testIfCorrectMessageIsShownIfThereAreNoBoughtProducts()
    {
        $this->client->loginUser($this->testCasualUser);

        $purchases = static::$container->get(PurchaseRepository::class)->findBy(['user' => $this->testCasualUser]);

        $entityManager = static::$container->get('doctrine.orm.entity_manager');

        foreach ($purchases as $purchase) {
            $entityManager->remove($purchase);
        }

        $entityManager->flush();

        $crawler = $this->client->request('GET', "/account/user/products/bought");

        $this->assertSelectorTextContains('html', 'Nie kupiłeś żadnych przedmiotów.');
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