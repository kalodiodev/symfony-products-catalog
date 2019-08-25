<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    PUBLIC const ALL = 'all';
    PUBLIC const INFO_ONLY = 'info_only';
    public const PASSWORD_ONLY = 'password_only';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mode = $options['mode'];

        if ($mode == self::ALL || $mode == self::INFO_ONLY) {
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
                ]);
        }

        if ($mode == self::ALL || $mode == self::PASSWORD_ONLY) {
            $builder
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'admin.users.validation.password_mismatch',
                ]);
        }

        $builder
            ->add('save', SubmitType::class, ['label' => $options['submit_label']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'mode' => self::ALL,
            'submit_label' => 'admin.users.button.save'
        ]);
    }
}