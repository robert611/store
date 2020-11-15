<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use App\Repository\PurchaseProductRepository;
use App\Repository\UserAddressRepository;
use App\Repository\BasketRepository;
use App\Entity\Basket;

class PurchaseControllerTest extends WebTestCase
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

    /**
     * @dataProvider provideUrls
     */
    public function testIfPageIsSuccessfull($url)
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('GET', $url);
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider provideUrls
     */
    public function testIfUnloggedUserCannotSeePage($url)
    {
        $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->assertResponseRedirects('/login');
    }

    public function testIfPurchaseSummaryPageIsSuccessfull()
    {
        $this->client->loginUser($this->testCasualUser);

        $product = static::$container->get(ProductRepository::class)->findAll()[0];

        $crawler = $this->client->request('GET', "product/{$product->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Kup teraz');

        $form = $buttonCrawlerNode->form();
        $form['items-quantity'] = 5;

        $crawler = $this->client->submit($form);

        $this->assertSelectorTextContains('html', 'Podsumowanie');
        $this->assertSelectorTextContains('html', 'Wybierz sposób dostawy');
        $this->assertSelectorTextContains('html', 'Podsumowanie i dostawa');
        $this->assertSelectorTextContains('html', $product->getPrice());
        $this->assertSelectorTextContains('html', 'Klikając w ten przycisk potwierdzasz zakup. Sprzedawca otrzyma twoje zamówienie.');
        $this->assertSelectorTextNotContains('html', 'Exception');
    }

    public function testIfUnloggedUserCannotSeePurchaseSumarryPage()
    {
        $product = static::$container->get(ProductRepository::class)->findAll()[0];

        $crawler = $this->client->request('GET', "product/{$product->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Kup teraz');

        $form = $buttonCrawlerNode->form();
        $form['items-quantity'] = 5;

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/login');
    }

    public function testIfUserCannotBuyMoreProductsThenThereIs()
    {
        $this->client->loginUser($this->testCasualUser);

        $product = static::$container->get(ProductRepository::class)->findAll()[0];

        $tooBigQuantity = $product->getQuantity() + 1;

        $crawler = $this->client->request('GET', "/product/{$product->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Kup teraz');

        $form = $buttonCrawlerNode->form();
        $form['items-quantity'] = $tooBigQuantity;

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/product/{$product->getId()}");

        $crawler = $this->client->request('GET', "/product/{$product->getId()}");
        $this->assertSelectorTextContains('html', "Wystawione jest {$product->getQuantity()} sztuk tego przedmiotu, a ty próbujesz kupić {$tooBigQuantity} sztuk.");
    }

    public function testIfUserCanBuyProduct()
    {
        $this->client->loginUser($this->testCasualUser);

        $product = static::$container->get(ProductRepository::class)->findAll()[0];

        $crawler = $this->client->request('GET', "/product/{$product->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Kup teraz');

        $form = $buttonCrawlerNode->form();
        $form['items-quantity'] = $product->getQuantity();

        $crawler = $this->client->submit($form);

        $crawler = $this->client->request('GET', "/purchase/{$product->getId()}/summary");

        $deliveryTypeRadio = $crawler->filter('.purchase-summary-product-delivery-type')->first();
        $deliveryTypeAttributes = $deliveryTypeRadio->extract(['data-deliverytypeid', 'data-paymenttype', 'data-deliveryprice']);

        $deliveryTypeId = $deliveryTypeAttributes[0][0];
        $deliveryTypePayment = $deliveryTypeAttributes[0][1];
        $deliveryTypePrice = $deliveryTypeAttributes[0][2];

        $crawler = $this->client->request('GET', "/purchase/{$product->getId()}/{$deliveryTypeId}/{$product->getQuantity()}/buy");

        $purchases = static::$container->get(PurchaseRepository::class)->findAll();
        $purchase = $purchases[count($purchases) - 1];

        $purchaseProduct = static::$container->get(PurchaseProductRepository::class)->findOneBy(['purchase' => $purchase]);

        if ($deliveryTypePayment == "prepayment") {
            $isPaid = 0;
            $this->assertResponseRedirects("/purchase/{$purchase->getId()}/payment/view");
        }  else if('cash-on-delivery') {
            $isPaid = 2;
            $this->assertResponseRedirect("/purchase/after/buy/message");
        }

        $this->assertTrue(in_array($deliveryTypePayment, ['prepayment', 'cash-on-delivery']));
        $this->assertTrue($purchaseProduct->getProduct()->getId() == $product->getId());
        $this->assertTrue($purchaseProduct->getQuantity() == $product->getQuantity());
        $this->assertTrue($purchaseProduct->getProduct()->getIsSoldOut());
        $this->assertTrue($purchaseProduct->getProduct()->getQuantity() == 0);
        $this->assertTrue($purchaseProduct->getIsPaid() == $isPaid);
        $this->assertTrue($purchaseProduct->getDeliveryType()->getId() == $deliveryTypeId);
        $this->assertTrue($purchase->getUser()->getId() == $this->testCasualUser->getId());
        $this->assertTrue($purchase->getPrice() == ($product->getQuantity() * $product->getPrice()) + $deliveryTypePrice);
    }


    /**
     * @runInSeparateProcess
     */
    public function testIfUserCannotBuyHisOwnProduct()
    {
        $this->client->loginUser($this->testAdminUser);

        $product = static::$container->get(ProductRepository::class)->findOneBy(['owner' => $this->testAdminUser]);

        $crawler = $this->client->request('GET', "/product/{$product->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Kup teraz');

        $form = $buttonCrawlerNode->form();
        $form['items-quantity'] = 1;

        $crawler = $this->client->submit($form);

        $crawler = $this->client->request('GET', "/purchase/{$product->getId()}/summary");

        $deliveryTypeRadio = $crawler->filter('.purchase-summary-product-delivery-type')->first();
        $deliveryTypeId = $deliveryTypeRadio->extract(['data-deliverytypeid'])[0];

        $crawler = $this->client->request('GET', "/purchase/{$product->getId()}/{$deliveryTypeId}/{$product->getQuantity()}/buy");

        $this->assertResponseRedirects("/");

        $purchases = static::$container->get(PurchaseRepository::class)->findAll();
        $purchaseProduct = static::$container->get(PurchaseProductRepository::class)->findOneBy(['product' => $product]);

        $this->assertTrue(count($purchases) == 0);
        $this->assertTrue($purchaseProduct == null);
    }

    /**
     * @runInSeparateProcess
     */
    public function testiIfUserCannotMakePurchaseWithoutGivingDeliveryAddress()
    {
        $this->client->loginUser($this->testCasualUser);

        $product = static::$container->get(ProductRepository::class)->findAll()[0];

        $crawler = $this->client->request('GET', "/product/{$product->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Kup teraz');

        $form = $buttonCrawlerNode->form();
        $form['items-quantity'] = 1;

        $crawler = $this->client->submit($form);

        $crawler = $this->client->request('GET', "/purchase/{$product->getId()}/summary");

        $deliveryTypeRadio = $crawler->filter('.purchase-summary-product-delivery-type')->first();
        $deliveryTypeId = $deliveryTypeRadio->extract(['data-deliverytypeid'])[0];

        $userAddress = static::$container->get(UserAddressRepository::class)->findOneBy(['user' => $this->testCasualUser]);
        static::$container->get(UserAddressRepository::class)->remove($userAddress->getId());

        $crawler = $this->client->request('GET', "/purchase/{$product->getId()}/{$deliveryTypeId}/{$product->getQuantity()}/buy");
        
        $this->assertResponseRedirects("/");

        static::$container->get(UserAddressRepository::class)->save($userAddress);
    }

    public function testIfUserCanBuyProductUsingBasket()
    {
        $this->client->loginUser($this->testCasualUser);

        $productDeliveryType = array();

        $isProductWithPrepayment = false;

        $basketProducts = static::$container->get(BasketRepository::class)->findBy(['user' => $this->testCasualUser]);

        foreach ($basketProducts as $basketProduct) {
            $product = $basketProduct->getProduct();
            $deliveryType = $product->getDeliveryTypes()[0];

            $productDeliveryType[$product->getId()] = $deliveryType->getId();
        
            if ($deliveryType->getPayment() == "prepayment") {
                $isProductWithPrepayment = true;
            }
        }

        $crawler = $this->client->request('POST', "purchase/basket/buy", ['productDeliveryType' => $productDeliveryType]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = json_decode($this->client->getResponse()->getContent());

        $purchase = static::$container->get(PurchaseRepository::class)->findOneBy(['user' => $this->testCasualUser]);
        $purchaseProducts = static::$container->get(PurchaseProductRepository::class)->findBy(['purchase' => $purchase]);

        $this->assertEquals($response->purchase_id, $purchase->getId());
        $this->assertEquals($response->prepayment, $isProductWithPrepayment);
        $this->assertEquals(count($basketProducts), count($purchaseProducts));

        $entityManager = static::$container->get('doctrine.orm.entity_manager');

        foreach ($purchaseProducts as $purchaseProduct) {
            $entityManager->remove($purchaseProduct);
        }

        $entityManager->remove($purchase);
        $entityManager->flush();
    }

    /**
     * @runInSeparateProcess
     */
    public function testiIfUserCannotMakeBasketPurchaseWithoutGivingDeliveryAddress()
    {
        $this->client->loginUser($this->testCasualUser);

        $productDeliveryType = array();

        $basketProducts = static::$container->get(BasketRepository::class)->findBy(['user' => $this->testCasualUser]);

        foreach ($basketProducts as $basketProduct) {
            $product = $basketProduct->getProduct();
            $deliveryType = $product->getDeliveryTypes()[0];

            $productDeliveryType[$product->getId()] = $deliveryType->getId();
        }

        $userAddress = static::$container->get(UserAddressRepository::class)->findOneBy(['user' => $this->testCasualUser]);
        $this->testCasualUser->setUserAddress(null);

        $crawler = $this->client->request('POST', "purchase/basket/buy", ['productDeliveryType' => $productDeliveryType]);
        
        $this->assertResponseRedirects("/");

        $this->testCasualUser->setUserAddress($userAddress);
    }

    public function testIfUserCannnotBuyOwnProductUsingBasket()
    {
        $this->client->loginUser($this->testAdminUser);

        $productDeliveryType = array();

        $ownProduct = static::$container->get(ProductRepository::class)->findOneBy(['owner' => $this->testAdminUser]);

        $entityManager = static::$container->get('doctrine.orm.entity_manager');

        $basket = new Basket();
        $basket->setProduct($ownProduct);
        $basket->setUser($this->testCasualUser);
        $basket->setQuantity(1);

        $entityManager->persist($basket);
        $entityManager->flush();

        $basketProducts = static::$container->get(BasketRepository::class)->findBy(['user' => $this->testAdminUser]);

        foreach ($basketProducts as $basketProduct) {
            $product = $basketProduct->getProduct();
            $deliveryType = $product->getDeliveryTypes()[0];

            $productDeliveryType[$product->getId()] = $deliveryType->getId();
        }

        $crawler = $this->client->request('POST', "purchase/basket/buy", ['productDeliveryType' => $productDeliveryType]);

        $this->assertResponseRedirects("/");
    }


    public function provideUrls()
    {
        return [
            ['purchase/basket/summary'],
            ['purchase/after/buy/message'],
            ['purchase/payment/fail/message']
        ];
    }
}