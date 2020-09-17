<?php

namespace App\Form;

use App\Entity\UserAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class UserAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 60,
                        'minMessage' => 'Imię musi składać się z przynajmniej {{ limit }} znaków',
                        'maxMessage' => 'Imię nie może się składać z więcej niz {{ limit }} znaków',
                        'allowEmptyString' => false
                    ])
                ]
            ])
            ->add('surname', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 60,
                        'minMessage' => 'Nazwisko musi składać się z przynajmniej {{ limit }} znaków',
                        'maxMessage' => 'Nazwisko nie może się składać z więcej niz {{ limit }} znaków',
                        'allowEmptyString' => false
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 150,
                        'minMessage' => 'Adres musi składać się z przynajmniej {{ limit }} znaków',
                        'maxMessage' => 'Adres nie może się składać z więcej niz {{ limit }} znaków',
                        'allowEmptyString' => false
                    ])
                ]
            ])
            ->add('zip_code', TextType::class, [
                'constraints' => [
                    'Regex' => [
                        'pattern' => '[0-9]{2}-[0-9]{3}',
                        'match' => false,
                        'message'=> 'Kod pocztowy nie jest poprawny'
                    ]
                ]
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 110,
                        'minMessage' => 'Nazwa miasta musi składać się z przynajmniej {{ limit }} znaków',
                        'maxMessage' => 'Nazwa miasta nie może się składać z więcej niz {{ limit }} znaków',
                        'allowEmptyString' => false
                    ])
                ]
            ])
            ->add('country', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 80,
                        'minMessage' => 'Nazwa państwa musi składać się z przynajmniej {{ limit }} znaków',
                        'maxMessage' => 'Nazwa państwa nie może się składać z więcej niz {{ limit }} znaków',
                        'allowEmptyString' => false
                    ])
                ]
            ])
            ->add('phone_number', TextType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAddress::class,
        ]);
    }
}