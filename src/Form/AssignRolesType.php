<?php

namespace App\Form;

use App\Entity\User;
use App\Utils\GlobalVariables;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AssignRolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
        ->add('author', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'username',
            'label' => 'Membre',
            'placeholder' => 'Choisir un utilisateur',
            'attr' => ['class' => 'form-control'],])
            ->add('roles', ChoiceType::class, [
                'choices' => GlobalVariables::getRoles(),
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Choisir un rôle',
                // 'multiple' => true,
                // 'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => User::class,
        ]);
    }
}