<?php

namespace App\Form;

use App\Entity\ProductOpinion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductOpinionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('opinion', TextareaType::class, [
                'required' => false
            ])
            ->add('advantages')
            ->add('flaws')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductOpinion::class,
        ]);
    }
}
