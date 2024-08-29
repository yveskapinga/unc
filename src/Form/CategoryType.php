<?php

// src/Form/CategoryType.php
namespace App\Form;

use App\Entity\User;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la catégorie',
                'required' => false,
            ])
            ->add('photo', FileType::class, [
                'label' => 'Votre image de profil (Des fichiers images uniquement)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ]),
                ],
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Auteur',
                'attr' => ['class' => 'form-control'],
                //Activer cette partie uniquement lorsque les rôles sont définis
                // 'query_builder' => function ($er) { 
                //     return $er->createQueryBuilder('u')
                //         ->where('u.roles LIKE :roles')
                //         ->setParameter('roles', '%ROLE_ADMIN%');
                // },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
