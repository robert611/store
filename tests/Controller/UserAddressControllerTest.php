<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\UserAddressRepository;

class ProductControllerTest extends WebTestCase
{
    public $client = null;

    public $testCasualUser;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->testCasualUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'casual_user@interia.pl']);
        $this->testAdminUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'admin@interia.pl']);
    }

    public function testIfUserAddressCanBeAddedThroughApi()
    {
        $this->client->loginUser($this->testCasualUser);

        $userAddress = static::$container->get(UserAddressRepository::class)->findOneBy(['user' => $this->testCasualUser]);
        static::$container->get(UserAddressRepository::class)->remove($userAddress->getId());
        
        $shouldNotExistDatabaseUserAddress = static::$container->get(UserAddressRepository::class)->findOneBy(['user' => $this->testCasualUser]);

        $userAddress = [
            'name' => 'Test name', 
            'surname' => 'Test surname',
            'address' => 'General 1',
            'zip_code' => '312-125',
            'city' => 'Detroit',
            'country' => 'United States',
            'phone_number' => '235-135-782'
        ];

        $crawler = $this->client->request('POST', "api/user/address/new", ['user_address' => $userAddress]);

        $response = $this->client->getResponse()->getContent();

        $response = json_decode(json_decode($response), true);

        $databaseUserAddress = static::$container->get(UserAddressRepository::class)->findOneBy(['user' => $this->testCasualUser]);

        $this->assertTrue($shouldNotExistDatabaseUserAddress == NULL);
        $this->assertEquals($databaseUserAddress->getName(), $userAddress['name']);
        $this->assertEquals($databaseUserAddress->getSurname(), $userAddress['surname']);
        $this->assertEquals($databaseUserAddress->getAddress(), $userAddress['address']);

        $this->assertEquals($response['name'], $userAddress['name']);
        $this->assertEquals($response['surname'], $userAddress['surname']);
        $this->assertEquals($response['address'], $userAddress['address']);
    }

    public function testIfUserAddressCanBeEditedThroughApi()
    {
        $this->client->loginUser($this->testCasualUser);
        
        $userAddress = [
            'name' => 'Test api edit name', 
            'surname' => 'Test api edit surname',
            'address' => 'General 1 api edit',
            'zip_code' => '512-125',
            'city' => 'Detroit api edit',
            'country' => 'United States api edit',
            'phone_number' => '235-135-782'
        ];

        $crawler = $this->client->request('POST', "api/user/address/edit", ['user_address' => $userAddress]);

        $response = $this->client->getResponse()->getContent();

        $response = json_decode(json_decode($response), true);

        $databaseUserAddress = static::$container->get(UserAddressRepository::class)->findOneBy(['user' => $this->testCasualUser]);

        $this->assertEquals($databaseUserAddress->getName(), $userAddress['name']);
        $this->assertEquals($databaseUserAddress->getSurname(), $userAddress['surname']);
        $this->assertEquals($databaseUserAddress->getAddress(), $userAddress['address']);
        $this->assertEquals($databaseUserAddress->getZipCode(), $userAddress['zip_code']);
        $this->assertEquals($databaseUserAddress->getCity(), $userAddress['city']);
        $this->assertEquals($databaseUserAddress->getCountry(), $userAddress['country']);
        $this->assertEquals($databaseUserAddress->getPhoneNumber(), $userAddress['phone_number']);
  
        $this->assertEquals($response['name'], $userAddress['name']);
        $this->assertEquals($response['surname'], $userAddress['surname']);
        $this->assertEquals($response['address'], $userAddress['address']);
        $this->assertEquals($response['zipCode'], $userAddress['zip_code']);
        $this->assertEquals($response['city'], $userAddress['city']);
        $this->assertEquals($response['country'], $userAddress['country']);
        $this->assertEquals($response['phoneNumber'], $userAddress['phone_number']);
  
    }

    public function testIfUserAddressCanBeEdited()
    {
        $this->client->loginUser($this->testCasualUser);
        
        $userAddress = [
            'user_address[name]' => 'Test edit name', 
            'user_address[surname]' => 'Test edit surname',
            'user_address[address]' => 'General 1 edit',
            'user_address[zip_code]' => '012-125',
            'user_address[city]' => 'Detroit edit',
            'user_address[country]' => 'United States test',
            'user_address[phone_number]' => '235-135-721'
        ];

        $crawler = $this->client->request('GET', "user/address/edit");

        $buttonCrawlerNode = $crawler->selectButton('Zapisz');

        $form = $buttonCrawlerNode->form();

        $crawler = $this->client->submit($form, $userAddress);

        $this->assertResponseRedirects('/user/address/edit');

        $databaseUserAddress = static::$container->get(UserAddressRepository::class)->findOneBy(['user' => $this->testCasualUser]);
        
        $this->assertEquals($databaseUserAddress->getName(), $userAddress['user_address[name]']);
        $this->assertEquals($databaseUserAddress->getSurname(), $userAddress['user_address[surname]']);
        $this->assertEquals($databaseUserAddress->getAddress(), $userAddress['user_address[address]']);
        $this->assertEquals($databaseUserAddress->getZipCode(), $userAddress['user_address[zip_code]']);
        $this->assertEquals($databaseUserAddress->getCity(), $userAddress['user_address[city]']);
        $this->assertEquals($databaseUserAddress->getCountry(), $userAddress['user_address[country]']);
        $this->assertEquals($databaseUserAddress->getPhoneNumber(), $userAddress['user_address[phone_number]']);
    }

    public function testIfUserAddressDoesExist()
    {
        $this->client->loginUser($this->testCasualUser);

        $crawler = $this->client->request('GET', "api/user/address/get");

        $response = $this->client->getResponse()->getContent();

        $response = json_decode($response, true);

        $this->assertTrue($response['answer']);
    }

    public function testIfUserAddressDoesNotExist()
    {
        $this->client->loginUser($this->testCasualUser);

        $userAddress = static::$container->get(UserAddressRepository::class)->findOneBy(['user' => $this->testCasualUser]);
        static::$container->get(UserAddressRepository::class)->remove($userAddress->getId());

        $crawler = $this->client->request('GET', "api/user/address/get");

        $response = $this->client->getResponse()->getContent();

        $response = json_decode($response, true);

        $this->assertFalse($response['answer']);
    }

    /**
     * @dataProvider provideUrls
     */
    public function testIfUserMustBeLoggedIn($url)
    {
        $this->client->request('GET', $url);

        $this->assertResponseRedirects("/login");
    }


    public function provideUrls()
    {
        return [
            ['api/user/address/new'],
            ['api/user/address/edit'],
            ['user/address/edit'],
            ['api/user/address/get']
        ];
    }
}