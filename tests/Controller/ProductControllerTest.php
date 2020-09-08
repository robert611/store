<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class ProductControllerTest extends WebTestCase
{
    public $client = null;

    public $productId; 

    public $testCasualUser;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->productId = static::$container->get(ProductRepository::class)->findAll()[0]->getId();
        $this->userRepository = static::$container->get(UserRepository::class);
        $this->testCasualUser = $this->userRepository->findOneBy(['email' => 'casual_user@interia.pl']);
        $this->testAdminUser = $this->userRepository->findOneBy(['email' => 'admin@interia.pl']);
    }

    public function testIfIndexIsSuccessful()
    {
        $this->client->loginUser($this->testAdminUser);
        
        $this->client->request('GET', 'admin/product');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testIfShowAdminIsSuccessful()
    {
        $this->client->loginUser($this->testAdminUser);
    
        $this->client->request('GET', "product/{$this->productId}");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testIfShowIsSuccessful()
    {
        $this->client->loginUser($this->testCasualUser);
    
        $this->client->request('GET', "product/{$this->productId}");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }   

    public function testIfNewIsSuccessful()
    {
        $this->client->loginUser($this->testCasualUser);
    
        $this->client->request('GET', 'product/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testIfEditIsSuccessful()
    {
        $this->client->loginUser($this->testCasualUser);
    
        $this->client->request('GET', "product/{$this->productId}/edit");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testIfProductFormTypeWorks()
    {
        $this->client->loginUser($this->testCasualUser);

        $crawler = $this->client->request('GET', 'product/new');

        $buttonCrawlerNode = $crawler->selectButton('Wystaw');

        $form = $buttonCrawlerNode->form();

        $form['product[pictures][0]']->upload('../../public/uploads/pictures/test_picture.png');

        $this->client->submit($form, [
            'product[name]' => 'Produkt',
            'product[description]' => 'Opis',
            'product[price]' => '200',
            'product[state]' => 'nowy',
            'product[auction_type]' => 'auction',
            'product[category]' => static::$container->get(CategoryRepository::class)->findAll()[0]->getId(),
            'product[delivery_time]' => '24',
        ]);
    
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}