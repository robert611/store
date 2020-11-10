<?php 


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\BasketRepository;

class BasketControllerTest extends WebTestCase
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

    public function testIfBasketPageIsSuccessfull()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('GET', "/basket");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    public function testIfUserMustBeLoggedInToSeeBasketPage()
    {
        $this->client->request('GET', "/basket");
        $this->assertResponseRedirects("/login");
    }

    public function testIfUserCanAddProductToBasket()
    {
        $this->client->loginUser($this->testCasualUser);

        $product = static::$container->get(ProductRepository::class)->findAll()[0];

        $basket = static::$container->get(BasketRepository::class)->findOneBy(['product' => $product->getId()]);
        $crawler = $this->client->request('GET', "/product/{$product->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Dodaj do koszyka');

        $form = $buttonCrawlerNode->form();

        /* Quantity of the first product is 20, and user already has 10 of it in the basket */
        $form['items-quantity'] = 5;

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/product/{$product->getId()}");

        $this->client->request('GET', "/product/{$product->getId()}");
        $this->assertSelectorTextContains('html', 'Przedmiot został dodany do koszyka.');

        $editedBasket = static::$container->get(BasketRepository::class)->findOneBy(['product' => $product->getId()]);

        $previousQuantity = $basket ? $basket->getQuantity() : 0;
        $this->assertEquals($previousQuantity + 5, $editedBasket->getQuantity());
    }

    public function testIfUserCannotAddMoreProductItemsThenThereIs()
    {
        $this->client->loginUser($this->testCasualUser);

        $product = static::$container->get(ProductRepository::class)->findAll()[0];

        $basket = static::$container->get(BasketRepository::class)->findOneBy(['product' => $product->getId()]);
        $crawler = $this->client->request('GET', "/product/{$product->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Dodaj do koszyka');

        $form = $buttonCrawlerNode->form();

        /* Quantity of the first product is 20, and user already has 10 of it in the basket */
        $form['items-quantity'] = 15;

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/product/{$product->getId()}");

        $this->client->request('GET', "/product/{$product->getId()}");
        $this->assertSelectorTextContains('html', 'Nie możesz mieć w koszyku większej ilości tego produktu niż jest go w sprzedaży.');

        $editedBasket = static::$container->get(BasketRepository::class)->findOneBy(['product' => $product->getId()]);

        /* If user wanted to add more products then there is, it will add as many products as possible, not nothing at all*/
        $this->assertEquals($product->getQuantity(), $editedBasket->getQuantity());
    }

    public function testIfUserMustBeLoggedInToAddProduct()
    {
        $product = static::$container->get(ProductRepository::class)->findAll()[0];

        $crawler = $this->client->request('GET', "/product/{$product->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Dodaj do koszyka');

        $form = $buttonCrawlerNode->form();

        /* Quantity of the first product is 20, and user already has 10 of it in the basket */
        $form['items-quantity'] = 5;

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/login");
    }

    public function testIfBasketProductCanBeDeleted()
    {
        $this->client->loginUser($this->testCasualUser);

        $crawler = $this->client->request('GET', "/basket");

        $basket = static::$container->get(BasketRepository::class)->findBy(['user' => $this->testCasualUser])[0];

        $buttonCrawlerNode = $crawler->selectButton('delete_forever');

        $form = $buttonCrawlerNode->form();

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/basket");

        $deletedBasket = static::$container->get(BasketRepository::class)->find($basket->getId());

        $this->assertEquals($deletedBasket, false);
    }

    public function testIfUserMustBeLoggedInToDeleteBasket()
    {
        $basket = static::$container->get(BasketRepository::class)->findBy(['user' => $this->testCasualUser])[0];

        $crawler = $this->client->request('DELETE', "/account/basket/delete/product/{$basket->getId()}");

        $this->assertResponseRedirects("/login");

        $basket = static::$container->get(BasketRepository::class)->find($basket->getId());

        $this->assertTrue(is_object($basket));
    }
}