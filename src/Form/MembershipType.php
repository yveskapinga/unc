<?php

// src/Form/MembershipType.php
/* namespace App\Form;

use App\Entity\Membership;
use App\Entity\User;
use App\Entity\Interfederation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('theUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Utilisateur',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('level', ChoiceType::class, [
                'choices' => [
                    'Fondateur' => 'fondateur',
                    'Effectif' => 'effectif',
                    'd\'Honneur' => 'honneur',
                    'Sympathisant' => 'sympathisant',
                ],
                'label' => 'Niveau',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('fonction', ChoiceType::class, [
                'choices' => [
                    'Fondateur' => 'fondateur',
                    'Effectif' => 'effectif',
                    'd\'Honneur' => 'honneur',
                    'Sympathisant' => 'sympathisant',
                ],
                'label' => 'Fonction',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('interfederation', EntityType::class, [
                'class' => Interfederation::class,
                'choice_label' => 'designation',
                'label' => 'Interfédération',
                'attr' => ['class' => 'form-control'],
            ])
            // ->add('feeAmount', MoneyType::class, [
            //     'currency' => false, // Désactive l'affichage du symbole de la devise
            //     'label' => 'Montant de la cotisation',
            //     'attr' => ['class' => 'form-control'],
            //     'required' => false,
            // ])
            // ->add('currency', ChoiceType::class, [
            //     'choices' => [
            //         'USD' => 'USD',
            //         'EUR' => 'EUR',
            //         'JPY' => 'JPY',
            //         'GBP' => 'GBP',
            //         'AUD' => 'AUD',
            //         'CDF' => 'CDF',

            //     ],
            //     'label' => 'Devise',
            //     'attr' => ['class' => 'form-control'],
            // ])
            // ->add('feePaidAt', DateTimeType::class, [
            //     'widget' => 'single_text',
            //     'label' => 'Date de paiement',
            //     'attr' => ['class' => 'form-control'],
            //     'required' => false,
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membership::class,
        ]);
    }
} */

// src/Form/MembershipType.php
namespace App\Form;

use App\Entity\Membership;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MembershipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('level', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Niveau',
            ])
            // ->add('feeAmount', MoneyType::class, [
            //     'constraints' => [
            //         new NotBlank(),
            //     ],
            // ])
            ->add('currency', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('fonction', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Votre fonction',
            ])
            ->add('membershipType', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Type de membre',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membership::class,
        ]);
    }
}

