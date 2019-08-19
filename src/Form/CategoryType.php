<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter category name'
                ]
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter category description'
                ]
            ])
            ->add('slug', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter category slug'
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Save Category'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
