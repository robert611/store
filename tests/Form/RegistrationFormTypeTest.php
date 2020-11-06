<?php 

namespace App\Tests\Form\Type;

use App\Form\RegistrationFormType;
use App\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

class RegistrationFormTypeTest extends TypeTestCase
{
    private $formData = [
        'email' => 'test_email@test.pl',
        'roles' => [],
        'plainPassword' => 'password',
        'username' => 'test_user',
        'agreeTerms' => true,
        'agreeDataUsing' => true
    ];

    public function testSubmitValidData()
    {
        $model = new User();

        // $formData will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(RegistrationFormType::class, $model);

        $expected = new User();
        
        $expected->setUsername($this->formData['username']);
        $expected->setEmail($this->formData['email']);
        $expected->setPlainPassword($this->formData['plainPassword']);
        $expected->setRoles($this->formData['roles']);
        $expected->setAgreeTerms($this->formData['agreeTerms']);
        $expected->setAgreeDataUsing($this->formData['agreeDataUsing']);

        // submit the data to the form directly
        $form->submit($this->formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $formData was modified as expected when the form was submitted
        $this->assertEquals($expected, $model);
    }
}