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
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.categories.form.name.placeholder'
                ]
            ])
            ->add('description', TextType::class, [
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'placeholder' => 'admin.categories.form.description.placeholder'
                ]
            ])
            ->add('slug', TextType::class, [
                'empty_data' => null,
                'attr' => [
                    'placeholder' => 'admin.categories.form.slug.placeholder'
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'admin.categories.button.save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
