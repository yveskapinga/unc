<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // $this->createCategories($manager);
        // $manager->flush();
    }

    private function createAddress(ObjectManager $manager){

        $faker = Factory::create('fr_FR');
        $cities = ['Kinshasa', 'Lubumbashi', 'Mbuji-Mayi', 'Kananga', 'Kisangani', 'Goma', 'Bukavu', 'Tshikapa', 'Kolwezi', 'Likasi'];

        for ($i = 0; $i < 10; $i++) {
            $address = new Address();
            $address->setNumber($faker->buildingNumber);
            $address->setStreet($faker->streetName);
            $address->setCity($faker->randomElement($cities));
            $address->setCountry('République Démocratique du Congo');
            $address->setLatitude($faker->latitude(-13.5, 5.5)); // Coordonnées de la RDC
            $address->setLongitude($faker->longitude(12, 31));

            $user = new User();
            $user->setEmail($faker->email);
            $user->setUsername($faker->userName);
            $user->setRoles(['ROLE_USER']);
            $user->setJoinedAt($faker->dateTimeThisYear); // Date de l'année en cours
            $user->setIsActive($faker->boolean);
            $user->setAddress($address);

            $password = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($password);

            $manager->persist($address);
            $manager->persist($user);
        }
    }

    private function createCategories(ObjectManager $manager){
        $categories_with_descriptions = [
            'Actualités Politiques' => 'Dernières nouvelles et analyses sur la politique nationale et internationale.',
            'Événements de l\'UNC' => 'Informations sur les événements organisés par l\'Union pour la Nation Congolaise.',
            'Propositions et Idées' => 'Espace pour partager et discuter des propositions et idées pour le développement de l\'association.',
            'Questions et Réponses' => 'Forum pour poser des questions et obtenir des réponses de la communauté.',
            'Annonces Officielles' => 'Communiqués et annonces officielles de l\'UNC.',
            'Projets de développement' => 'Détails et mises à jour sur les projets de développement en cours et futurs.',
            'Ressources et Documents' => 'Accès à des documents importants et des ressources utiles pour les membres.',
            'Présentation des Membres' => 'Profils et présentations des membres de l\'association.',
            'Aide et Assistance' => 'Support et assistance pour les membres ayant des questions ou des problèmes.',
            'Formation et Éducation' => 'Opportunités de formation et d\'éducation pour les membres.',
            'Relations Internationales' => 'Informations sur les relations et collaborations internationales de l\'UNC.',
            'Économie et social' => 'Discussions sur les questions économiques et sociales affectant la communauté.',
            'Santé et Bien-être' => 'Conseils et informations sur la santé et le bien-être des membres.',
            'Sport et Loisir' => 'Activités sportives et de loisirs pour les membres de l\'association.'
        ];
        
        foreach ($categories_with_descriptions as $name => $description) {
            $categorie = new Category();
            $categorie->setName($name);
            $categorie->setDescription($description);
            $manager->persist($categorie);
        }
        
    }
}
