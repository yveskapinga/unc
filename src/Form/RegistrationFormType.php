<?php

namespace App\Form;

use App\Entity\User;
use App\Form\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom d\'utilisateur',
                    ]),
                    new Regex([
                        'pattern' => '/^\S*$/',
                        'message' => 'Le nom d\'utilisateur ne peut pas contenir d\'espaces',
                    ]),
                ],
                'label' => 'pseudo',
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom ',
                    ]),
                ],
                'label' => 'votre nom',
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom ',
                    ]),
                ],
                'label' => 'votre prénom',
            ])
            ->add('phoneNumber', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre numéro de téléphone.',
                    ]),
                    new Regex([
                        'pattern' => '/^\+?[0-9\s\-]{7,15}$/',
                        'message' => 'Veuillez saisir un numéro de téléphone valide.',
                    ]),
                ],
                'label' => 'Tél',
            ])
            ->add('nationality', CountryType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner votre nationalité.',
                    ]),
                ],
                'data' => 'CD', // Code ISO 3166-1 alpha-2 pour la République Démocratique du Congo

                'placeholder' => 'Sélectionnez votre pays',

                'label' => 'Nationalité',
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas valide.',
                    ]),
                ],
                'label' => 'Email',
            ])
            ->add('address', AddressType::class, [
                'label' => false, // Optionnel, pour ne pas afficher de label supplémentaire
            ])
            ->add('membership', MembershipType::class, [
                'label' => false, // Optionnel, pour ne pas afficher de label supplémentaire
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Termes et conditions',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les politiques',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répétez le mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre majuscule, un chiffre et un caractère spécial.',
                    ]),     
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}



