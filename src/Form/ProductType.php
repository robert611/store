<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\ProductPicture;
use App\Entity\DeliveryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 150,
                        'minMessage' => 'Nazwa musi składać się z przynajmniej {{ limit }} znaków',
                        'maxMessage' => 'Nazwa nie może się składać z więcej niz {{ limit }} znaków',
                        'allowEmptyString' => false
                    ])
                ]
            ])
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class, [
                'scale' => 3
            ])
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'Używany' => 'używany',
                    'Bardzo Dobry' => 'bardzo dobry',
                    'Nowy' => 'nowy'
                ],
                'label_attr' => ['class' => 'form-check-label'],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('pictures', FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'constraints' => [
                    new Image([
                        'maxSize' => '4096k', 
                        'mimeTypesMessage' => 'Proszę przesyłać tylko zdjęcia',
                        'groups' => true
                    ]),
                ],
                'required' => false
            ])
            ->add('auction_type', ChoiceType::class, [
                'choices' => [
                    'Kup teraz' => 'buy_now',
                    'Licytacja' => 'auction',
                    'Darmowe ogłoszenie' => 'free_advertisment'
                ],
                'label_attr' => ['class' => 'form-check-label'],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('delivery_types', EntityType::class, [
                'class' => DeliveryType::class, 
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Musisz wybrać formę przesyłki'
                    ])
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('delivery_time', TextType::class, [
                'constraints' => [
                    new NotNull([
                        'message' => 'Ta wartość nie może być pusta'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            "allow_extra_fields" => true
        ]);
    }
}
