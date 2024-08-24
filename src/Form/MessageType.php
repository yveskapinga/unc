<?php

// src/Form/MessageType.php
namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['rows' => 5],
            ])
            ->add('recipient', ChoiceType::class, [
                'choices' => $options['recipients'],
                'choice_label' => function($user) {
                    return $user->getUsername();
                },
                'label' => 'Destinataire',
            ])
            ->add('send', SubmitType::class, ['label' => 'Envoyer']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'recipients' => [],
        ]);
    }
}

