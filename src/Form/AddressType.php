<?php

// src/Form/AddressType.php

/* namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', IntegerType::class, [
                'label' => 'Numéro',
                'required' => false,
                'constraints' => [
                    new Assert\Positive([
                        'message' => 'Le numéro doit être un nombre positif.',
                    ]),
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La rue ne peut pas être vide.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'La rue ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La ville ne peut pas être vide.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'La ville ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le pays ne peut pas être vide.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le pays ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
} */

// src/Form/AddressType.php
namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Numéro',
                'trim' => true,
            ])
            ->add('street', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Rue',
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Ville',
            ])
            ->add('province', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Province/Etat',
            ])
            ->add('country', CountryType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'data' => 'CD', // Code ISO 3166-1 alpha-2 pour la République Démocratique du Congo
                'label' => 'Pays de résidence',

            ])
            ->add('postalCode', TextType::class, [
                'required' => false,
                'label' => 'Code postal',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
