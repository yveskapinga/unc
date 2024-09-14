<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnonymousPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

            $builder
                ->add('firstname', TextType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Votre prénom']
                ])
                ->add('name', TextType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom']
                ])
                ->add('email', EmailType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Your Email*']
                ])

                ->add('content', TextareaType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Votre commentaire']            
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
