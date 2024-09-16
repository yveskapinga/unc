<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('users', CollectionType::class, [
                'entry_type' => AssignRolesType::class,
                'allow_add' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
                'prototype_name' => '__name__',
                'attr' => ['class' => 'form-collection'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
