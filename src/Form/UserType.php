<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.users.form.name.placeholder'
                ]
            ])
            ->add('email', EmailType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'admin.users.form.email.placeholder'
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'admin.users.button.save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}