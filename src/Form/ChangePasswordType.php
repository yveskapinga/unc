<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;


class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('current_password', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'constraints' => [
                    new UserPassword([
                        'message' => 'Le mot de passe actuel est incorrect.',]),
                ],
                ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'Votre mot de passe doit contenir au moins une lettre majuscule',
                        ]),
                        new Regex([
                            'pattern' => '/[a-z]/',
                            'message' => 'Votre mot de passe doit contenir au moins une lettre minuscule',
                        ]),
                        new Regex([
                            'pattern' => '/[0-9]/',
                            'message' => 'Votre mot de passe doit contenir au moins un chiffre',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Répétez le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}