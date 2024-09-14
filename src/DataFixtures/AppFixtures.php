<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\Category;
use App\Entity\Membership;
use App\Utils\GlobalVariables;
use App\Entity\Interfederation;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
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
        $this->createUsers($manager);
         $manager->flush();
    }

    private function usersFixtures(ObjectManager $manager){
        foreach (GlobalVariables::$users as $userData) {
            $nameParts = explode(' ', $userData[0]);
            $firstName = array_shift($nameParts);
            $lastName = implode(' ', $nameParts);
            $username = strtolower($firstName[0] . '.' . str_replace(' ', '', $lastName));
            $email = $username . '@unc.iuc';

            $user = new User();
            $user->setEmail($email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setIsActive(true);
            $user->setUsername($username);
            $user->setName($lastName);
            $user->setFirstName($firstName);
            $user->setNationality('CD');

            $membership = new Membership();
            $membership->setTheUser($user);
            $membership->setMembershipType('Membre éffectif');
            $membership->setLevel('');
            $membership->setFonction($userData[1]);
            $membership->setInterfederation('Lualaba');

            $manager->persist($user);
            $manager->persist($membership);
        }
    }

    private function createUserAndAddress(ObjectManager $manager){

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
            $user->setFirstName($faker->firstname);
            $user->setName($faker->name);
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

    private function createSuperAdmin(ObjectManager $manager){

        $faker = Factory::create('fr_FR');
            $address = new Address();
            $address->setNumber(303);
            $address->setStreet('du traffic');
            $address->setCity('kolwezi');
            $address->setCountry('Congo Kinshasa');
            //$address->setLatitude(''); // Coordonnées de la RDC
            //$address->setLongitude('');

            $user = new User();
            $user->setEmail('superadmin@unc.org');
            $user->setUsername('superadmin');
            $user->setFirstName('Yves');
            $user->setName('Kayembe');
            $user->setRoles(['ROLE_SUPER_ADMIN']);
            $user->setJoinedAt(new \DateTime('2020-01-30')); // Date de l'année en cours
            $user->setIsActive(true);
            $user->setAddress($address);

            $password = $this->passwordHasher->hashPassword($user, '1.0SuperAdmin');
            $user->setPassword($password);

            $manager->persist($address);
            $manager->persist($user);
    }

    private function createCategories(ObjectManager $manager){

        
        foreach (GlobalVariables::getCategories_with_descriptions() as $name => $description) {
            $categorie = new Category();
            $categorie->setName($name);
            $categorie->setDescription($description);
            $manager->persist($categorie);
        }
        
    }

    private function createInterfederation(ObjectManager $manager){
        $interfederations = [
            '' => 'Dernières nouvelles et analyses sur la politique nationale et internationale.',

        ];
    }

    private function createUsers(ObjectManager $manager){
        // Récupérer l'interfédération 'Lualaba'
        $interfederationRepository = $manager->getRepository(Interfederation::class);
        $lualabaInterfederation = $interfederationRepository->findOneBy(['designation' => 'Lualaba']);

        // Vérifier si l'interfédération existe
        if (!$lualabaInterfederation) {
            throw new \Exception('Interfederation "Lualaba" not found in the database.');
        }

        // Tableau des utilisateurs
        $usersData = GlobalVariables::$users;
        $usedEmails = [];

        foreach ($usersData as $userData) {
            list($fullName, $fonction) = $userData;
            $nameParts = explode(' ', $fullName);
            $firstName = array_shift($nameParts);
            $name = implode(' ', $nameParts);
            $username = strtolower($firstName[0] . '.' . $name);
            $username = str_replace(' ', '', $username);

            // Générer une adresse email unique
            $email = $username . '@unc.iuc';
            $email = str_replace(' ', '', $email);
            $counter = 1;
            while (in_array($email, $usedEmails)) {
                $email = $username . $counter . '@unc.iuc';
                $email = str_replace(' ', '', $email);
                $counter++;
            }
            $usedEmails[] = $email;

            // Création de l'utilisateur
            $user = new User();
            $user->setEmail($email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setJoinedAt(new \DateTime());
            $user->setIsActive(true);
            $user->setUsername($username);
            $user->setName($name);
            $user->setFirstName($firstName);
            $user->setNationality('CD');
            $manager->persist($user);

            // Création du membership
            $membership = new Membership();
            $membership->setFonction($fonction);
            $membership->setMembershipType('Membre éffectif');
            $membership->setInterfederation($lualabaInterfederation);
            $membership->setTheUser($user);
            $membership->setLevel('');
            $manager->persist($membership);
        }

    }
}
