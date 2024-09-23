<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Address;
use App\Form\AddressType;
use App\Form\UserRoleType;
use App\Entity\Interfederation;
use App\Form\ChangePasswordType;
use App\Service\GeocoderService;
use App\Service\ReferralService;
use App\Service\SecurityService;
use App\Service\UploaderService;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InterfederationRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/user/crud')]
class UserCrudController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UploaderService $uploaderService,
        private SecurityService $securityService,
        private NotificationService $notificationService,
        private UserRepository $userRepository,
        private Security $security,
        private ReferralService $referralService,
        private TokenStorageInterface $tokenStorage
    ) {
    }
    #[Route('/', name: 'app_user_crud_index', methods: ['GET'])]
    public function index(InterfederationRepository $interfederationRepository): Response
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            // Redirigez vers la page d'erreur personnalisée
            // return $this->redirectToRoute('custom_error_page');
            throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }
        $user = $this->security->getUser();
        // $interfederation = $user->getInterfederation();

        //$users = $interfederation->getMemberships();
        return $this->render('user_crud/index.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $address = new Address();
        $user->setAddress($address);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = "Champs-Élysées, Paris, France";
            // $coordinates = $geocoderService->geocodeAddress($address);
            // dd($coordinates->getLatitude());
            // // Définir les coordonnées dans l'entité Address
            // $user->getAddress()->setLatitude($coordinates->getLatitude());
            // $user->getAddress()->setLongitude($coordinates->getLongitude());
            // dd($user);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_crud/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_crud_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $connectedUser = $this->securityService->getConnectedUser();
        
        // Vérifiez si l'utilisateur connecté est soit l'utilisateur en question, soit un administrateur
        if (!$this->security->isGranted('ROLE_ADMIN') && $connectedUser !== $user) {
            // Redirigez vers la page d'erreur personnalisée
            // return $this->redirectToRoute('custom_error_page');
        throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
            
        }
        return $this->render('user_crud/show.html.twig', [
            'user' => $user,
            'referralLink'=>$this->referralService->generateReferralLink($user)
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user = null, EntityManagerInterface $entityManager): Response
    {
        if (!$this->security->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion
        }
    
        $address = $user->getAddress() ?: new Address();
        $user->setAddress($address);
    
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $directory = $this->getParameter('photo_directory');
                $user->setPhoto($this->uploaderService->uploadPhoto($photo, $directory));
            }
    
            // Récupérer les coordonnées de géolocalisation
            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');
            if ($latitude && $longitude) {
                $address->setLatitude($latitude);
                $address->setLongitude($longitude);
            }
            $this->notificationService
                            ->createNotification(
                                $user, 
                                'info',
                                'Votre profil a été mis à jour avec succès'
                            );
            $entityManager->persist($user);
            $entityManager->flush();
    
            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('user_crud/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_user_crud_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/voir', name: 'app_user_crud_profile', methods: ['GET','POST'])]
    public function voir(): Response
    {
        // $user = $this->getUser();
        // dd($user);
        return $this->render('user_crud/profile.html.twig', [
            // 'user' => $user,
        ]);
    }



    #[Route('/update/password', name: 'update_password')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // $token = $this->tokenStorage->getToken();
        // if (null === $token) {

        //     dd('je suis ici');
        // }
        $user = $this->securityService->getConnectedUser();
        // $currentUser = $token->getUser();
        // if(!$user==$currentUser){
        //     throw new \LogicException('Vous ne pouvez pas réaliser cette opération');
        // }
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('current_password')->getData()
            ); 
            // if($currentPassword !== $user->getPassword())
            // {
            //     $this->addFlash('danger','mot de passe incorrect');
            //     return $this->redirectToRoute('update_password');
            // }
                $hashedPassword=
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                );
                $user->setPassword($hashedPassword);
            
            $this->notificationService
                ->createNotification(
                    $user, 
                    'info',
                    'Votre mot de passe a été mis à jour avec succès'
                );
            // $entityManager->persist($notification);
            $entityManager->persist($user);

            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié');


            return $this->redirectToRoute('app_user_crud_show', ['id'=>$user->getId()]);
        }

        return $this->render('user_crud/password_update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
