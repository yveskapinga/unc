<?php
namespace App\Form;

use App\Entity\Interfederation;
use App\Entity\Membership;
use App\Entity\User;
use App\Utils\GlobalVariables;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MembershipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('theUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getEmail().' ('. $user->getFirstName() . ' ' . $user->getName().')';
                },
                'label' => 'Utilisateur',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('level', ChoiceType::class, [
                'choices'=> GlobalVariables::getLevels(),
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Structure',
                'attr' => ['class' => 'form-control', 'id' => 'level'],
            ])

            ->add('fonction', ChoiceType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Votre fonction',
                'choices'=>  [], // Initialement vide, sera rempli par JavaScript
                'attr' => ['class' => 'form-control', 'id' => 'fonction', 'disabled' => 'disabled'],
            ])

            ->add('interfederation', EntityType::class, [
                // 'choices' =>$this->interfederations,
                'class'=>Interfederation::class,
                'choice_label'=>'designation',
                'label'=>'interfédération',
                'attr'=>['class'=>'form-control'],
            ])
            ->add('membershipType', ChoiceType::class, [
                'choices' => GlobalVariables::getMembershipType(),
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Catégorie de membre',
                'data' => 'Membre éffectif', // Valeur par défaut
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membership::class,
        ]);
    }
}

