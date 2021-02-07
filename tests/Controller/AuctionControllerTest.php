<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\AuctionBidRepository;

class AuctionControllerTest extends WebTestCase
{
    public $client = null;

    private $testCasualUser;

    private $auctionBidRepository;

    private $auctionProduct;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->testCasualUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'casual_user@interia.pl']);
        $this->auctionBidRepository = static::$container->get(AuctionBidRepository::class);
        $this->auctionProduct = static::$container->get(ProductRepository::class)->findOneBy(['name' => 'Auction Product']);
    }

    public function testIfUserCanBid()
    {
        $this->client->loginUser($this->testCasualUser);

        $crawler = $this->client->request('GET', "/product/{$this->auctionProduct->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Licytuj');

        $form = $buttonCrawlerNode->form();

        $form['bid-price'] = 150;

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/product/{$this->auctionProduct->getId()}");

        $crawler = $this->client->request('GET', "/product/{$this->auctionProduct->getId()}");

        $productBids = $this->auctionBidRepository->findBy(['product' => $this->auctionProduct]);

        $lastBid = $productBids[count($productBids) - 1];

        $this->assertEquals($lastBid->getBid(), 150);
        $this->assertEquals($lastBid->getUser()->getId(), $this->testCasualUser->getId());
        $this->assertSelectorTextContains('html', 'Przedmiot został zalicytowany');
    }

    public function testIfUserMustBeLoggedInToBid()
    {
        $crawler = $this->client->request('GET', "/product/{$this->auctionProduct->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Licytuj');

        $form = $buttonCrawlerNode->form();

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/login");
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCannotBidHisOwnAuction()
    {
        $productOwner = static::$container->get(UserRepository::class)->findOneBy(['username' => 'administrator']);

        $this->client->loginUser($productOwner);

        $crawler = $this->client->request('GET', "/product/{$this->auctionProduct->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Licytuj');

        $form = $buttonCrawlerNode->form();

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/");
    }

    public function testIfUserCannotBidEqualOrLessThanCurrentPrice()
    {
        $this->client->loginUser($this->testCasualUser);

        $crawler = $this->client->request('GET', "/product/{$this->auctionProduct->getId()}");

        $buttonCrawlerNode = $crawler->selectButton('Licytuj');

        $form = $buttonCrawlerNode->form();

        $form['bid-price'] = 6;

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/product/{$this->auctionProduct->getId()}");

        $crawler = $this->client->request('GET', "/product/{$this->auctionProduct->getId()}");

        $productBids = $this->auctionBidRepository->findBy(['product' => $this->auctionProduct]);

        $lastBid = $productBids[count($productBids) - 1];

        $this->assertEquals($lastBid->getBid(), 7);

        $this->assertSelectorTextContains('html', 'Musisz podać wyższą propozycję niż ta obecna');
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCannotBidOnProductWhichIsNotOnAuction()
    {
        $this->client->loginUser($this->testCasualUser);

        $notOnAuctionProduct = static::$container->get(ProductRepository::class)->findAll()[0];

        $crawler = $this->client->request('GET', "/auction/bid/{$notOnAuctionProduct->getId()}");

        $this->assertResponseRedirects("/");
    }
}