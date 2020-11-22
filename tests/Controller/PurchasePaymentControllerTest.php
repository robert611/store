<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\PurchaseRepository;

class PurchasePaymentControllerTest extends WebTestCase
{
    public $client = null;

    public $testCasualUser;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->testCasualUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'casual_user@interia.pl']);
    }

    public function testIfPurchasePaymentViewIsSuccessfull()
    {
        $this->client->loginUser($this->testCasualUser);

        $purchase = static::$container->get(PurchaseRepository::class)->findOneBy(['user' => $this->testCasualUser]);

        $purchaseProduct = $purchase->getPurchaseProducts()[0];

        $product = $purchaseProduct->getProduct();
        $deliveryType = $purchaseProduct->getDeliveryType();

        $this->client->request('GET', "purchase/{$purchase->getId()}/payment/view");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
        $this->assertSelectorTextContains('html', 'Zapłać');
        $this->assertSelectorTextContains('h3', $product->getName());
        $this->assertSelectorTextContains('h5', ($product->getPrice() * $purchaseProduct->getQuantity()) + $deliveryType->getDefaultPrice());
    }

    public function testIfPurchaseProductPaymentViewIsSuccessfull()
    {
        $this->client->loginUser($this->testCasualUser);

        $purchase = static::$container->get(PurchaseRepository::class)->findOneBy(['user' => $this->testCasualUser]);

        $purchaseProduct = $purchase->getPurchaseProducts()[0];

        $product = $purchaseProduct->getProduct();
        $deliveryType = $purchaseProduct->getDeliveryType();

        $this->client->request('GET', "purchase/product/{$purchaseProduct->getId()}/payment/view");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
        $this->assertSelectorTextContains('html', 'Zapłać');
        $this->assertSelectorTextContains('h3', $product->getName());
        $this->assertSelectorTextContains('h5', ($product->getPrice() * $purchaseProduct->getQuantity()) + $deliveryType->getDefaultPrice());
    }

    public function testIfDataForPaymentCanBeFetched()
    {
        $this->client->loginUser($this->testCasualUser);

        $purchase = static::$container->get(PurchaseRepository::class)->findOneBy(['user' => $this->testCasualUser]);

        $purchaseProductId = $purchase->getPurchaseProducts()[0]->getId();

        $this->client->request('GET', "purchase/{$purchase->getId()}/payment/{$purchaseProductId}");

        $response = $this->client->getResponse()->getContent();

        $response = json_decode($response, true);

        $this->assertTrue(isset($response['id']));
        $this->assertTrue(is_string($response['id']));
    }

    public function testIfProductWithCashOnDeliveryMethodWillBeTurnedDown()
    {
        $this->client->loginUser($this->testCasualUser);

        $purchase = static::$container->get(PurchaseRepository::class)->findOneBy(['user' => $this->testCasualUser]);

        $purchaseProductId = $purchase->getPurchaseProducts()[1]->getId();

        $this->client->request('GET', "purchase/{$purchase->getId()}/payment/{$purchaseProductId}");

        $response = $this->client->getResponse()->getContent();

        $response = json_decode($response, true);

        $this->assertTrue(isset($response['error']));
        $this->assertTrue($response['error'] == "Nie możesz zapłacić za przedmiot z dostawą za pobraniem. Jeśli wyświetla ci się informacja o wcześniejszej płatności, proszę ją pominąć.");
    }

    public function testIfPaymentStatusCanBeSet()
    {
        $this->client->loginUser($this->testCasualUser);

        $purchase = static::$container->get(PurchaseRepository::class)->findOneBy(['user' => $this->testCasualUser]);

        $purchaseProduct = $purchase->getPurchaseProducts()[0];

        /* Make sure at first it is not equal to true */
        $isPaid = $purchaseProduct->getIsPaid();

        $this->assertTrue($isPaid == 0 or $isPaid == false);

        $this->client->request('GET', "set/purchase/product/payment/status/{$purchaseProduct->getId()}");

        $isPaid = $purchaseProduct->getIsPaid();

        $this->assertResponseRedirects('/purchase/after/buy/message');

        $this->assertTrue($isPaid == 1 or $isPaid == true);
    }
}