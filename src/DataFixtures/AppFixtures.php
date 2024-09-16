<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Topic;
use App\Entity\Post;
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
        $this->updateTopicAndPosts($manager);
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
            $user->setEmail('superadmin@unc.icu');
            $user->setUsername('superadmin');
            $user->setFirstName('super');
            $user->setName('admin');
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
        $interfederation = new Interfederation();
        $interfederation->setDesignation('Lualaba');
        $manager->persist($interfederation);
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
        $usersData = GlobalVariables::$user4;
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
    
    private function updateCategory(ObjectManager $manager){
        $userRepository = $manager->getRepository(User::class);
        $author = $userRepository->findOneBy([]);

        // Récupérer la date actuelle
        $currentDate = new \DateTime();

        // Parcourir le tableau des catégories
        foreach (GlobalVariables::$categories_with_pictures as $name => $imagePath) {
            // Récupérer la catégorie existante par son nom
            $categoryRepository = $manager->getRepository(Category::class);
            $category = $categoryRepository->findOneBy(['name' => $name]);

            if ($category) {
                // Mettre à jour les attributs de la catégorie
                $category->setImage($imagePath);
                $category->setAuthor($author);
                $category->setCreatedAt($currentDate);

                // Persister les changements
                $manager->persist($category);
            }
        }
    }
    
    private function updateTopicAndPosts(ObjectManager $manager){
        $topicRepository = $manager->getRepository(Topic::class);
        $postRepository = $manager->getRepository(Post::class);
        $topics = $topicRepository->findAll();
        $posts = $postRepository->findAll();
        function randomDate($startDate, $endDate) {
            $timestamp = mt_rand(strtotime($startDate), strtotime($endDate));
            return new \DateTime(date("Y-m-d H:i:s", $timestamp));
        }
        
        foreach ($topics as $topic) {
            // $topic = new Topic();
            // $topic->setTitle($data['title']);
            // $topic->setDescription($data['description']);
            // $topic->setContent($data['content']);
            // $topic->setAuthor($i % 2 == 0 ? $user1 : $user19);
            // $topic->setCategory($data['category']);
            $topic->setCreatedAt(randomDate('2024-08-10', '2024-09-14'));

            $manager->persist($topic);
        }
        foreach ($posts as $post) {
            // $post = new Post();
            // $post->setContent($data['content']);
            // $post->setAuthor($i % 2 == 0 ? $user1 : $user19);
            // $post->setTopic($topics[$data['topic']]);
            // $post->setIsValidated(true);
            $topic = $post->getTopic();
            $post->setCreatedAt(randomDate($topic->getCreatedAt()->format('Y-m-d H:i:s'), '2024-09-14'));
            $manager->persist($post);
        
        }
    }
    
    private function createTopicAndPosts(ObjectManager $manager){
        // Récupérer les utilisateurs
        $userRepository = $manager->getRepository(User::class);
        $user1 = $userRepository->find(1);
        $user19 = $userRepository->find(19);

        // Récupérer les catégories
        $categoryRepository = $manager->getRepository(Category::class);
        $categories = [
            $categoryRepository->findOneBy(['name' => 'Actualités Politiques']),
            $categoryRepository->findOneBy(['name' => 'Événements de l\'UNC']),
            $categoryRepository->findOneBy(['name' => 'Propositions et Idées']),
            $categoryRepository->findOneBy(['name' => 'Questions et Réponses']),
        ];
        


        // Créer 5 topics
        $topicsData = [
            [
                'title' => 'Vital Kamerhe élu président de l\'Assemblée nationale',
                'description' => 'Vital Kamerhe retrouve le perchoir de l\'Assemblée nationale après 15 ans.',
                'content' => 'Vital Kamerhe a été élu président de l\'Assemblée nationale de la RDC le 22 mai 2024, avec 371 voix.',
                'category' => $categories[0],
            ],
            [
                'title' => 'Conférence annuelle',
                'description' => 'L\'UNC organise sa conférence annuelle pour discuter des projets futurs.',
                'content' => 'La conférence annuelle de l\'UNC a eu lieu le 15 juin 2024, avec la participation de nombreux membres et invités.',
                'category' => $categories[1],
            ],
            [
                'title' => 'Propositions pour le développement de l\'UNC',
                'description' => 'Discussion sur les nouvelles propositions pour le développement de l\'UNC.',
                'content' => 'Les membres de l\'UNC ont proposé plusieurs idées innovantes pour le développement du parti.',
                'category' => $categories[2],
            ],
            [
                'title' => 'Questions et Réponses sur la politique de l\'UNC',
                'description' => 'Forum de discussion pour répondre aux questions sur la politique de l\'UNC.',
                'content' => 'Les membres de l\'UNC ont répondu à diverses questions sur les politiques et les objectifs du parti.',
                'category' => $categories[3],
            ],
            [
                'title' => 'Annonces officielles de l\'UNC',
                'description' => 'Dernières annonces officielles du parti UNC.',
                'content' => 'L\'UNC a publié plusieurs annonces officielles concernant ses activités et ses projets futurs.',
                'category' => $categories[0],
            ],
        ];
        
        
        // Créer et persister les commentaires
        $commentsData = [
            [
                'content' => 'Félicitations à Vital Kamerhe pour son retour à l\'Assemblée nationale. C\'est une victoire méritée après tant d\'années de lutte.',
                'topic' => 'Vital Kamerhe élu président de l\'Assemblée nationale',
            ],
            [
                'content' => 'La conférence annuelle de l\'UNC a été un grand succès. Beaucoup de nouvelles idées ont été partagées.',
                'topic' => 'Conférence annuelle',
            ],
            [
                'content' => 'Les propositions pour le développement de l\'UNC sont très prometteuses. J\'espère qu\'elles seront mises en œuvre rapidement.',
                'topic' => 'Propositions pour le développement de l\'UNC',
            ],
            [
                'content' => 'Merci pour cette session de questions et réponses. Cela a permis de clarifier beaucoup de points importants.',
                'topic' => 'Questions et Réponses sur la politique de l\'UNC',
            ],
            [
                'content' => 'Les annonces officielles de l\'UNC montrent que le parti est sur la bonne voie pour atteindre ses objectifs.',
                'topic' => 'Annonces officielles de l\'UNC',
            ],
            [
                'content' => 'Vital Kamerhe a toujours été un leader inspirant. Son retour est une bonne nouvelle pour la RDC.',
                'topic' => 'Vital Kamerhe élu président de l\'Assemblée nationale',
            ],
            [
                'content' => 'La participation à la conférence annuelle était impressionnante. L\'UNC continue de grandir.',
                'topic' => 'Conférence annuelle',
            ],
            [
                'content' => 'Les idées partagées pour le développement de l\'UNC sont innovantes et nécessaires.',
                'topic' => 'Propositions pour le développement de l\'UNC',
            ],
            [
                'content' => 'Les réponses apportées lors de la session de questions étaient très détaillées et utiles.',
                'topic' => 'Questions et Réponses sur la politique de l\'UNC',
            ],
            [
                'content' => 'Les annonces récentes montrent que l\'UNC est déterminée à faire une différence.',
                'topic' => 'Annonces officielles de l\'UNC',
            ],
            [
                'content' => 'Je suis ravi de voir Vital Kamerhe de retour à un poste de pouvoir. Il a beaucoup à offrir.',
                'topic' => 'Vital Kamerhe élu président de l\'Assemblée nationale',
            ],
            [
                'content' => 'La conférence annuelle a été une excellente occasion de réseauter et de partager des idées.',
                'topic' => 'Conférence annuelle',
            ],
            [
                'content' => 'Les propositions pour le développement de l\'UNC sont exactement ce dont nous avons besoin.',
                'topic' => 'Propositions pour le développement de l\'UNC',
            ],
            [
                'content' => 'Les sessions de questions et réponses sont toujours très instructives. Merci pour cela.',
                'topic' => 'Questions et Réponses sur la politique de l\'UNC',
            ],
            [
                'content' => 'Les annonces officielles montrent que l\'UNC est prête à relever les défis à venir.',
                'topic' => 'Annonces officielles de l\'UNC',
            ],
            [
                'content' => 'Vital Kamerhe a prouvé qu\'il est un leader résilient. Son retour est une grande victoire.',
                'topic' => 'Vital Kamerhe élu président de l\'Assemblée nationale',
            ],
        ];

        foreach ($topicsData as $i => $data) {
            $topic = new Topic();
            $topic->setTitle($data['title']);
            $topic->setDescription($data['description']);
            $topic->setContent($data['content']);
            $topic->setAuthor($i % 2 == 0 ? $user1 : $user19);
            $topic->setCategory($data['category']);
            // $topic->setCreatedAt(randomDate('2024-08-10', '2024-09-14'));

            $manager->persist($topic);
            $topics[$data['title']] = $topic;
        }
            // Créer 3-4 posts pour chaque topic
        foreach ($commentsData as $i => $data) {
            $post = new Post();
            $post->setContent($data['content']);
            $post->setAuthor($i % 2 == 0 ? $user1 : $user19);
            $post->setTopic($topics[$data['topic']]);
            $post->setIsValidated(true);
            // $post->setCreatedAt(randomDate($topics[$data['topic']]->getCreatedAt()->format('Y-m-d H:i:s'), '2024-09-14'));

            $manager->persist($post);
        
        }
    }
}
