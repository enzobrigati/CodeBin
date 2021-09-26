<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'attr' => ['placeholder' => 'Votre adresse email'],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Votre mot de passe',
                    'attr' => ['placeholder' => 'Votre mot de passe']
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe',
                    'attr' => ['placeholder' => 'Répéter le mot de passe']
                ]
            ])
            ->add('pseudo', null, [
                'label' => 'Votre pseudo',
                'attr' => ['placeholder' => 'Votre pseudo']
            ])
            ->add('biographie', TextareaType::class, [
                'label' => 'Dîtes-nous en plus sur vous',
                'attr' => ['placeholder' => 'Quelques informations supplémentaires que vous aimeriez partager avec la communauté...'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
