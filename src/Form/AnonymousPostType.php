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
                ->add('name', TextType::class, [
                    'label' => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Your Name*']
                ])
                ->add('email', EmailType::class, [
                    'label' => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Your Email*']
                ])
                ->add('website', TextType::class, [
                    'label' => false,
                    'required' => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Your Website']
                ])
                ->add('content', TextareaType::class, [
                    'label' => false,
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Your Comment*']            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
