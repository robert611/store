<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;

class RegistrationControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIfRegistrationPageIsSuccessfull()
    {
        $this->client->request('GET', '/register');
        
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
        $this->assertSelectorTextContains('html h1', 'Rejestracja');
        $this->assertSelectorTextContains('html', 'Akceptuje Regulamin platformy store.');
    }

    public function testIfUserCanRegister()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Załóż konto')->form();

        $form['registration_form[email]'] = 'test_registration_email@test.pl';
        $form['registration_form[plainPassword]'] = 'password';
        $form['registration_form[username]'] = 'test_registration_user';
        $form['registration_form[agreeTerms]'] = true;
        $form['registration_form[agreeDataUsing]'] = true;

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/');

        $this->client->request('GET', '/');
        $this->assertSelectorTextNotContains('html', 'Exception');
        $this->assertSelectorTextContains('html', 'Rejestracja przebiegła pomyślnie');

        $users = static::$container->get('doctrine')->getRepository(User::class)->findAll();
        $user = $users[count($users) - 1];
        
        $this->assertEquals($form->get('registration_form[email]')->getValue(), $user->getEmail());
        $this->assertEquals($form->get('registration_form[username]')->getValue(), $user->getUsername());
    }
}