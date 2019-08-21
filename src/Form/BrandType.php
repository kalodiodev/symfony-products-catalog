<?php

namespace App\Form;

use App\Entity\Brand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BrandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.brands.form.name.placeholder'
                ]
            ])
            ->add('details', TextType::class, [
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'placeholder' => 'admin.brands.form.details.placeholder'
                ]
            ])
            ->add('slug', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.brands.form.slug.placeholder'
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'admin.brands.button.save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Brand::class,
        ]);
    }
}
