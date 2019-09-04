<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.products.form.title.placeholder'
                ]
            ])
            ->add('meta_title', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.products.form.meta_title.placeholder'
                ]
            ])
            ->add('description', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.products.form.description.placeholder'
                ]
            ])
            ->add('meta_description', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.products.form.meta_description.placeholder'
                ]
            ])
            ->add('slug', TextType::class, [
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'placeholder' => 'admin.products.form.slug.placeholder'
                ]
            ])
            ->add('sku', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.products.form.sku.placeholder'
                ]
            ])
            ->add('mpn', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.products.form.mpn.placeholder'
                ]
            ])
            ->add('brand', EntityType::class, [
                'class' => Brand::class,
                'choice_label' => 'name',
                'multiple' => false
            ])
            ->add('price', NumberType::class, [
                'empty_data' => 0,
                'attr' => [
                    'placeholder' => 'admin.products.form.price.placeholder'
                ]
            ])
            ->add('quantity', NumberType::class, [
              'empty_data' => 0,
              'attr' => [
                  'placeholder' => 'admin.products.form.quantity.placeholder'
              ]
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'custom-control-input'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
            ->add('attributes', CollectionType::class, [
                'entry_type' => ProductAttributeType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'label' => 'admin.products.button.save'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'categories' => []
        ]);
    }
}
