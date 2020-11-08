<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\DeliveryTypeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class ProductControllerTest extends WebTestCase
{
    public $client = null;

    public $product; 

    public $testCasualUser;

    private $formData;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->product = static::$container->get(ProductRepository::class)->findAll()[0];
        $this->userRepository = static::$container->get(UserRepository::class);
        $this->testCasualUser = $this->userRepository->findOneBy(['email' => 'casual_user@interia.pl']);
        $this->testAdminUser = $this->userRepository->findOneBy(['email' => 'admin@interia.pl']);
        $this->formData = [
            'product[name]' => 'Produkt',
            'product[description]' => 'Opis',
            'product[price]' => 200,
            'product[state]' => 'nowy',
            'product[auction_type]' => 'auction',
            'product[category]' => static::$container->get(CategoryRepository::class)->findAll()[0]->getId(),
            'product[delivery_time]' => 24,
            'product[duration]' => 0,
            'product[quantity]' => 20,
            'product[delivery_types]' => [static::$container->get(DeliveryTypeRepository::class)->findAll()[0]->getId()]     
        ];
    }

    public function testIfIndexIsSuccessful()
    {
        $this->client->loginUser($this->testAdminUser);
        
        $this->client->request('GET', 'admin/product');
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    public function testIfShowAdminIsSuccessful()
    {
        $this->client->loginUser($this->testAdminUser);
    
        $this->client->request('GET', "admin/product/{$this->product->getId()}");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    public function testIfShowIsSuccessful()
    {
        $this->client->loginUser($this->testCasualUser);
    
        $this->client->request('GET', "product/{$this->product->getId()}");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }   

    public function testIfNewViewIsSuccessful()
    {
        $this->client->loginUser($this->testCasualUser);
    
        $this->client->request('GET', 'product/new');
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    public function testIfEditViewIsSuccessful()
    {
        $this->client->loginUser($this->testCasualUser);
    
        $this->client->request('GET', "product/{$this->product->getId()}/edit");
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    public function testIfUserCanEditProduct()
    {
        $this->client->loginUser($this->testCasualUser);

        $crawler = $this->client->request('GET', "product/{$this->product->getId()}/edit");

        $buttonCrawlerNode = $crawler->selectButton('Edytuj');

        $form = $buttonCrawlerNode->form();

        $formData = $this->formData;
        $formData['product[name]'] = 'edit_product_test_name';
        $formData['product[description]'] = 'edit_product_test_description';

        $product = static::$container->get(ProductRepository::class)->find($this->product->getId());
        $productPictureId = $product->getProductPictures()[0]->getId();

        $form["product[pictures_to_remove][$productPictureId]"]->tick();

        $crawler = $this->client->submit($form, $formData);

        $editedProduct = static::$container->get(ProductRepository::class)->find($this->product->getId());

        $this->assertEquals($editedProduct->getName(), $formData['product[name]']);
        $this->assertEquals($editedProduct->getDescription(), $formData['product[description]']);
        $this->assertEquals(1, $editedProduct->getProductPictures()->count());
    }

    public function testIfProductCanBeDeletedInEditPage()
    {
        $this->client->loginUser($this->testCasualUser);

        $productsNumber = count(static::$container->get(ProductRepository::class)->findAll());

        $crawler = $this->client->request('GET', "product/{$this->product->getId()}/edit");

        $buttonCrawlerNode = $crawler->selectButton('Usuń');

        $form = $buttonCrawlerNode->form();

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/account/user/auctions/list');

        $deletedProduct = static::$container->get(ProductRepository::class)->find($this->product->getId());
        $newProductsNumber = count(static::$container->get(ProductRepository::class)->findAll());

        $this->assertEquals($deletedProduct, NULL);
        $this->assertEquals($productsNumber - 1, $newProductsNumber);
    }

    public function testIfProductCanBeDeletedInListingPage()
    {
        $this->client->loginUser($this->testAdminUser);

        $productsNumber = count(static::$container->get(ProductRepository::class)->findAll());

        $crawler = $this->client->request('GET', "/account/user/auctions/list");

        $buttonCrawlerNode = $crawler->selectButton('Usuń');

        $form = $buttonCrawlerNode->form();

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/account/user/auctions/list');

        $newProductsNumber = count(static::$container->get(ProductRepository::class)->findAll());

        $this->assertEquals($productsNumber - 1, $newProductsNumber);
    }

    public function testIfUserCanAddPictureToProduct()
    {
        $this->client->loginUser($this->testCasualUser);

        $crawler = $this->client->request('GET', "product/{$this->product->getId()}/edit");

        $buttonCrawlerNode = $crawler->selectButton('Edytuj');

        $form = $buttonCrawlerNode->form();

        $form['product[pictures][0]']->upload('./public/uploads/pictures/tully.jpg');

        $crawler = $this->client->submit($form, $this->formData);

        $editedProduct = static::$container->get(ProductRepository::class)->find($this->product->getId());

        $this->assertEquals($this->product->getProductPictures()->count() + 1, $editedProduct->getProductPictures()->count());
    }

    public function testIfProductFormTypeWorks()
    {
        $this->client->loginUser($this->testCasualUser);

        $crawler = $this->client->request('GET', 'product/new');

        $buttonCrawlerNode = $crawler->selectButton('Wystaw');

        $form = $buttonCrawlerNode->form();

        $form['product[pictures][0]']->upload('./public/uploads/pictures/tully.jpg');

        $crawler = $this->client->submit($form, $this->formData);

        $products = static::$container->get(ProductRepository::class)->findAll();

        $lastProduct = $products[count($products) - 1];

        $this->assertResponseRedirects('/account/product/posting/message/' . $lastProduct->getId());
        
        $this->client->request('GET', '/account/product/posting/message/' . $lastProduct->getId());

        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());

        $this->assertEquals($lastProduct->getName(), $this->formData['product[name]']);
        $this->assertEquals($lastProduct->getDescription(), $this->formData['product[description]']);
    }
}