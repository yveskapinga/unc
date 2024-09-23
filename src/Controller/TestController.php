<?php
// src/Controller/TestController.php
namespace App\Controller;

use App\Entity\Topic;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\AddressRepository;
use App\Service\NotificationService;
use App\Service\ReferralService;
use App\Service\SecurityService;
use DateTime;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController extends AbstractController
{
    public function __construct(
        private ReferralService $referralService,
        private SecurityService $securityService,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private AddressRepository $addressRepository,
        private NotificationService $notificationService
        )
    {
    }

    #[Route('/testons', name:'testons')]
    public function testons() : Response
    {
        $users = $this->em->getRepository(User::class)->findUsersByRole('ROLE_SUPER_ADMIN');

        foreach ($users as $superAdmin) {
            $tab [] = $superAdmin->getMembership();
        }

        dd($tab);

        foreach($users as $user){
            $user->setCreatedAt(new DateTime('2024-08-10'));
            $this->em->persist($user);
        }
        $this->em->flush();
        return new Response('Mis à jour');
    }


    #[Route('/addresses', name:'address')]
    public function address() : Response
    {
        $address = $this->addressRepository->findAll();
        foreach($address as $addres)
        {
            $addres->setNumber(rand(10,100));
            $this->em->persist($addres);
        }
        // $this->em->flush();
        return new Response('Mis à jour');
    }

    #[Route('/user', name: 'user')]
    public function user(){
        $user = $this->userRepository->find(17);
        return new JsonResponse($user);
    }


    #[Route('/test', name: 'test')]
    public function testReferral() : Response
    {
        //$user = $this->securityService->getConnectedUser();
        $users=$this->userRepository->findAll();
        foreach($users as $user){
            $link = $user->getId().$this->referralService->generateReferralCode();
            $tab[] = $user->getReferralCode();
            $user->setReferralCode($link);
            $this->em->persist($user);
        }
        $this->em->flush();
        $this->addFlash('success', 'mis à jour avec succès');
        
        return new Response('succès');
    }



    #[Route('/test/key', name: 'key')]
    public function testKey():Response{
        return new Response(bin2hex(openssl_random_pseudo_bytes(32))) ;
    }
    

    #[Route('/test/geocode', name: 'location')]
    public function geocode(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
         $coordinates = [
            [-4.441931, 15.266293], [-11.660357, 27.479397], [-6.136000, 23.589000], [0.515280, 25.190990],
            [-2.507500, 28.862500], [-5.896240, 22.416590], [-1.680000, 29.220000], [-5.816670, 13.450000],
            [-10.983330, 26.733330], [-10.716670, 25.472500], [-4.316667, 15.300000], [-5.033333, 18.783333],
            [-6.150000, 23.600000], [-4.316667, 20.600000], [-2.933333, 25.933333], [-1.566667, 30.250000],
            [-2.933333, 25.933333], [-4.316667, 15.300000], [-4.316667, 15.300000], [-4.316667, 15.300000]
        ];

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail('user'.$i.'@example.com');
            $user->setUsername('user'.$i);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($passwordHasher->hashPassword($user, 'password'));
            $user->setJoinedAt(new \DateTime());
            $user->setIsActive(true);

            $address = new Address();
            $address->setLatitude($coordinates[$i][0]);
            $address->setLongitude($coordinates[$i][1]);
            $address->setCity('City'.$i);
            $address->setStreet('Street'.$i);
            $address->setCountry('RDC');

            // Associer l'adresse à l'utilisateur
            $user->setAddress($address);

            $entityManager->persist($user);
        }

        $entityManager->flush();

        return new Response('20 users with addresses have been generated.');

    }
    
    #[Route('/test/get-location', name: 'test_geocode')]
    public function getLocation(): Response
    {
        return $this->render('get_location.html.twig');
    }



    #[Route('/test/edit', name: 'test_edit')]
    public function action(Request $request, UserInterface $user, TopicRepository $repo, EntityManagerInterface $em): Response
    {
        $topics = $repo->find(3);
//        foreach ($topics as $u){
            if (!$topics->getAuthor()){
                $topics->setAuthor($user);
                $em->persist($topics);
            }
//        }
        $em->flush();
        return $this->redirectToRoute('app_topic_index', [], Response::HTTP_SEE_OTHER);

        $data = json_decode($request->getContent(), true);
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        
        return new Response(sprintf(
        'Adresse: %s<br>Latitude: %s<br>Longitude: %s',
            //$address,
            $latitude,
            $longitude
            ));
    }
}