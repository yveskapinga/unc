<?php

// src/Form/InvitationType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'email',
            'label' => 'Adresse mail',
            'attr' => ['class' => 'form-control'],
            'multiple' => true,
            'expanded' => true, // Utilisez false si vous préférez une liste déroulante
            // 'entry_options' => ['label' => false],
            // 'allow_add' => true,
            // 'allow_delete' => true,
            // 'prototype' => true,
            // 'by_reference' => false,
            //Activer cette partie uniquement lorsque les rôles sont définis
            // 'query_builder' => function ($er) { 
            //     return $er->createQueryBuilder('u')
            //         ->where('u.roles LIKE :roles')
            //         ->setParameter('roles', '%ROLE_ADMIN%');
            // },
        ])
            // ->add('email', CollectionType::class, [
            //     'entry_type' => EmailType::class,
            //     'entry_options' => ['label' => false],
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'prototype' => true,
            //     'by_reference' => false,
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => User::class,
        ]);
    }
}
