<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Address;
use App\Entity\Interfederation;
use App\Entity\Membership;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InterfederationType extends AbstractType
{
    public function __construct(private AddressRepository $addressRepository)
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $provinces = $this->addressRepository->createQueryBuilder('a')
        ->select('DISTINCT a.province')
        ->orderBy('a.province', 'ASC')
        ->getQuery()
        ->getResult();

        $provinceChoices = [];
        foreach ($provinces as $province) {
            $provinceChoices[$province['province']] = $province['province'];
        }
        $builder
            ->add('designation', ChoiceType::class, [
                'choices' => $provinceChoices,
                
            ])
            ->add('sif', EntityType::class,[
                'class'=>Membership::class,
                'choice_label'=>function (Membership $membership) {
                    return $membership->__toString();
                },
                'placeholder'=>'selectionnez un membre',
                'attr'=>[
                    'class'=>'select2'
                ],
                'required'=>false
            ])
            ->add('siege', AddressType::class,[
                'label'=>false
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interfederation::class,
        ]);
    }
}
