<?php

// src/Form/MembershipType.php
namespace App\Form;

use App\Entity\Membership;
use App\Entity\User;
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
            ->add('level', ChoiceType::class, [
                'choices' => [
                    'Basic' => 'basic',
                    'Premium' => 'premium',
                    'VIP' => 'vip',
                ],
                'label' => 'Niveau',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('feeAmount', MoneyType::class, [
                'currency' => false, // Désactive l'affichage du symbole de la devise
                'label' => 'Montant de la cotisation',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('currency', ChoiceType::class, [
                'choices' => [
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                    'JPY' => 'JPY',
                    'GBP' => 'GBP',
                    'AUD' => 'AUD',
                    'CDF' => 'CDF',

                ],
                'label' => 'Devise',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('feePaidAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de paiement',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('theUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Utilisateur',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membership::class,
        ]);
    }
}
