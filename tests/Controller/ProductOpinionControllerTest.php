<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductOpinionRepository;

class ProductOpinionControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->testUserWithoutOpinions = static::$container->get(UserRepository::class)->findOneBy(['email' => 'user_without_opinions@interia.pl']);
        $this->testUserWithOpinions = static::$container->get(UserRepository::class)->findOneBy(['email' => 'opinion_user@interia.pl']);
    }

    public function testIfUserCanAddOpinion()
    {
        $this->client->loginUser($this->testUserWithoutOpinions);

        $productId = static::$container->get(ProductRepository::class)->findOneBy(['name' => 'Opinion Product'])->getId();

        $crawler = $this->client->request('GET', "/product/opinion/{$productId}");
        
        $buttonCrawlerNode = $crawler->selectButton('Dodaj ocenę');

        $form = $buttonCrawlerNode->form();

        $formData['mark'] = '2';
        $formData['product_opinion[opinion]'] = 'Not so Great Product, just a test.';
        $formData['product_opinion[advantages]'] = 'Very usuful, test, just a test';
        $formData['product_opinion[flaws]'] = 'it is easy to brake';

        $crawler = $this->client->submit($form, $formData);

        $productOpinion = static::$container->get(ProductOpinionRepository::class)->findOneBy(['opinion' => 'Not so Great Product, just a test.']);

        $this->client->request('GET', "/account/user/products/bought");

        $this->assertSelectorTextContains('html', 'Opinia została dodana');
        $this->assertEquals($productOpinion->getMark(), $formData['mark']);
        $this->assertEquals($productOpinion->getOpinion(), $formData['product_opinion[opinion]']);
        $this->assertEquals($productOpinion->getAdvantages(), $formData['product_opinion[advantages]']);
        $this->assertEquals($productOpinion->getFlaws(), $formData['product_opinion[flaws]']);
    }

    public function testIfUserCanAddOpinionOnlyGivingMark()
    {
        $this->client->loginUser($this->testUserWithoutOpinions);

        $productId = static::$container->get(ProductRepository::class)->findOneBy(['name' => 'Opinion Product'])->getId();

        $crawler = $this->client->request('GET', "/product/opinion/{$productId}");
        
        $buttonCrawlerNode = $crawler->selectButton('Dodaj ocenę');

        $form = $buttonCrawlerNode->form();

        $formData['mark'] = '2';
        $formData['product_opinion[opinion]'] = null;
        $formData['product_opinion[advantages]'] = null;
        $formData['product_opinion[flaws]'] = null;

        $crawler = $this->client->submit($form, $formData);

        $productOpinion = static::$container->get(ProductOpinionRepository::class)->findAll();
        $productOpinion = $productOpinion[count($productOpinion) - 1];

        $this->client->request('GET', "/account/user/products/bought");

        $this->assertSelectorTextContains('html', 'Opinia została dodana');
        $this->assertEquals($productOpinion->getMark(), $formData['mark']);
        $this->assertEquals($productOpinion->getOpinion(), $formData['product_opinion[opinion]']);
        $this->assertEquals($productOpinion->getAdvantages(), $formData['product_opinion[advantages]']);
        $this->assertEquals($productOpinion->getFlaws(), $formData['product_opinion[flaws]']);
    }

    public function testIfMarkMustBeEqualOrLessThanFive()
    {
        $this->client->loginUser($this->testUserWithoutOpinions);

        $productId = static::$container->get(ProductRepository::class)->findOneBy(['name' => 'Opinion Product'])->getId();

        $crawler = $this->client->request('GET', "/product/opinion/{$productId}");
        
        $buttonCrawlerNode = $crawler->selectButton('Dodaj ocenę');

        $form = $buttonCrawlerNode->form();

        $formData['mark'] = '6';

        $crawler = $this->client->submit($form, $formData);

        $wrongMarkOpinion = static::$container->get(ProductOpinionRepository::class)->findOneBy(['mark' => 6]);

        $this->assertSelectorTextContains('html', 'Ocena musi znajdować się w przedziale od 1 do 5 gwiazdek');

        $this->assertTrue($wrongMarkOpinion == null);
    }

    public function testIfMarkMustBeEqualOrMoneThanOne()
    {
        $this->client->loginUser($this->testUserWithoutOpinions);

        $productId = static::$container->get(ProductRepository::class)->findOneBy(['name' => 'Opinion Product'])->getId();

        $crawler = $this->client->request('GET', "/product/opinion/{$productId}");
        
        $buttonCrawlerNode = $crawler->selectButton('Dodaj ocenę');

        $form = $buttonCrawlerNode->form();

        $formData['mark'] = '-1';

        $crawler = $this->client->submit($form, $formData);

        $wrongMarkOpinion = static::$container->get(ProductOpinionRepository::class)->findOneBy(['mark' => -1]);

        $this->assertSelectorTextContains('html', 'Ocena musi znajdować się w przedziale od 1 do 5 gwiazdek');

        $this->assertTrue($wrongMarkOpinion == null);
    }

    public function testIfUserCanEditOpinion()
    {
        $this->client->loginUser($this->testUserWithOpinions);

        $opinionId = $this->testUserWithOpinions->getProductOpinions()->first()->getId();

        $crawler = $this->client->request('GET', "/product/opinion/edit/{$opinionId}");
        
        $buttonCrawlerNode = $crawler->selectButton('Edytuj ocenę');

        $form = $buttonCrawlerNode->form();

        $formData['mark'] = '1';
        $formData['product_opinion[opinion]'] = 'A decent product, edit test.';
        $formData['product_opinion[advantages]'] = 'Edit test, test advantage';
        $formData['product_opinion[flaws]'] = 'Edit test, test flaw';

        $crawler = $this->client->submit($form, $formData);

        $editedOpinion = static::$container->get(ProductOpinionRepository::class)->find($opinionId);

        $this->assertSelectorTextContains('html', 'Opinia została zmieniona');

        $this->assertEquals($editedOpinion->getMark(), $formData['mark']);
        $this->assertEquals($editedOpinion->getOpinion(), $formData['product_opinion[opinion]']);
        $this->assertEquals($editedOpinion->getAdvantages(), $formData['product_opinion[advantages]']);
        $this->assertEquals($editedOpinion->getFlaws(), $formData['product_opinion[flaws]']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCannotAddOpinionToProductHeDidNotBuy()
    {
        $this->client->loginUser($this->testUserWithoutOpinions);

        $productId = static::$container->get(ProductRepository::class)->findAll()[0]->getId();

        $crawler = $this->client->request('GET', "/product/opinion/{$productId}");

        $this->assertResponseRedirects('/');
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCannotAddMoreThanOneOpinions()
    {
        $this->client->loginUser($this->testUserWithOpinions);

        $productId = $this->testUserWithOpinions->getProductOpinions()->first()->getProduct()->getId();

        $crawler = $this->client->request('GET', "/product/opinion/{$productId}");

        $this->assertResponseRedirects('/');
    }

    public function testIfUserCanDeleteOpinion()
    {
        $this->client->loginUser($this->testUserWithOpinions);

        $opinionId = $this->testUserWithOpinions->getProductOpinions()->first()->getId();

        $crawler = $this->client->request('GET', "/product/opinion/edit/{$opinionId}");

        $buttonCrawlerNode = $crawler->selectButton('Usuń');

        $form = $buttonCrawlerNode->form();

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/account/user/products/bought');

        $this->client->request('GET', "/account/user/products/bought");

        $this->assertSelectorTextContains('html', 'Opinia została usunięta.');

        $deletedOpinion = static::$container->get(ProductOpinionRepository::class)->find($opinionId);

        $this->assertTrue($deletedOpinion == null);
    }
}