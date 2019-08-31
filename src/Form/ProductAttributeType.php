<?php

namespace App\Form;

use App\Entity\Attribute;
use App\Entity\ProductAttribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', TextType::class, [
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'admin.products.form.attribute_value.placeholder'
                ],
            ])
            ->add('attribute', EntityType::class, [
                'class' => Attribute::class,
                'choice_label' => function (Attribute $attribute) {
                    return $attribute->getName();
                },
                'attr' => [
                    'class' => 'form-control'
                ],
                'invalid_message_parameters' => [
                    'class' => ''
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductAttribute::class,
        ]);
    }
}
