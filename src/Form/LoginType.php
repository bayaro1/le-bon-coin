<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', EmailType::class, [
                'data' => $options['lastUsername'],
                'attr' => [
                    'name' => '_username'
                ]
            ])
            ->add('_password', PasswordType::class, [
                'attr' => [
                    'name' => '_password'
                ]
            ])
        ;

        if($options['choice2FA'])
        {
            $builder->add('_token2FA', TextType::class, [
                'attr' => [
                    'name' => '_token2FA'
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice2FA' => false,
            'lastUsername' => ''
        ]);
    }
}
