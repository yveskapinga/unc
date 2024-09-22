<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\TopicRepository;
use App\Repository\AddressRepository;
use App\Repository\InterfederationRepository;
use App\Repository\MembershipRepository;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Service\SecurityService;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserRoleType;






#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private SecurityService $securityService,
        private Security $security,
        private EventRepository $eventRepo,
        private TopicRepository $topicRepo,
        private NotificationRepository $notificationRepo,
        private MembershipRepository $memberRepo,
        private AddressRepository $addressRepo,
        private UserRepository $userRepo,
        private MessageRepository $messageRepo,
        private InterfederationRepository $interfederationRepo

    ){
    }
    
    #[Route('/assign/roles', name: 'assign_roles', methods: ['GET', 'POST'])]
    public function assignRoles(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserRoleType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $currentUser = $this->getUser();
            $currentUserRoles = $currentUser->getRoles();
    
            // Définir la hiérarchie des rôles
            $roleHierarchy = [
                'ROLE_SUPER_ADMIN' => 3,
                'ROLE_ADMIN' => 2,
                'ROLE_MODERATOR' => 1,
                'ROLE_USER' => 0,
            ];
    
            // Obtenir le niveau de rôle le plus élevé de l'utilisateur actuel
            $currentUserMaxRoleLevel = max(array_map(function($role) use ($roleHierarchy) {
                return $roleHierarchy[$role];
            }, $currentUserRoles));
    
            foreach ($data['users'] as $userData) {
                $user = $userData['author'];
                $roles[] = $userData['roles'];
    
                foreach ($roles as $role) {
                    if ($roleHierarchy[$role] > $currentUserMaxRoleLevel) {
                        throw new AccessDeniedException('Vous ne pouvez pas attribuer un rôle supérieur au vôtre.');
                    }
                }
    
                $user->setRoles($roles);
                $entityManager->persist($user);
            }
            $entityManager->flush();
    
            $this->addFlash('success', 'Le rôle a été assigné avec succès');
            return $this->redirectToRoute('assign_roles');
        }
    
        return $this->render('user_crud/assign_roles.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/statistics', name: 'app_statistics')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }

    #[Route('/promote/user', name: 'promote_user')]
    public function promoteUser(){
        
    }
    
    #[Route('/map', name: 'app_map')]
    public function map(): Response
    {
        return $this->render('admin/map.html.twig');
    }

    #[Route('/index', name: 'app_index')]
    public function app(): Response
    {
        // if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
        //     return $this->redirectToRoute('app_login');
        // }
                        // Vérifier si l'utilisateur est authentifié
        if (!$this->security->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion
        }
            $visitsToday = $this->userRepo->countVisitsByDate(new \DateTime('today'));
            $visitsThisMonth = $this->userRepo->countVisitsByDate(new \DateTime('first day of this month'));
            $visitsThisYear = $this->userRepo->countVisitsByDate(new \DateTime('first day of January'));

        return $this->render('admin/index.html.twig', [
            'users' => $this->userRepo->findAll(),
            'visitsToday' => $visitsToday,
            'visitsThisMonth' => $visitsThisMonth,
            'visitsThisYear' => $visitsThisYear,
            'interfederations'=> $this->interfederationRepo->findAll(),
            'events'=> $this->eventRepo->findAll()
        ]);
    }
    
    #[Route('/test/save-location', name: 'test_save_location')]
    public function saveLocation(Request $request): Response
    {           
        $addresses = $this->addressRepo->findAll();
        $data = [];

        foreach ($addresses as $address) {
            if ($address->getLatitude() & $address->getLongitude()) {
                # code...
            
            $data[] = [
                'latitude' => $address->getLatitude(),
                'longitude' => $address->getLongitude(),
                // 'user' => $address->getUser()->getUsername(), // Assuming you have a getUsername() method
            ];
            }
        }
        return new JsonResponse($data);
    }

    #[Route('/new-index', name: 'new-index')]
    public function newIndex(Request $request): Response
    {
        return $this->render('site.html.twig',[
            
        ]);
    }


    #[Route('/contact-form', name: 'contact-form', methods: ['POST'])]
    public function handleForm(Request $request): Response
    {
        // Récupérer les données du formulaire
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');

        dd($name, $email, $subject, $message);
        // Valider les données
        // $validator = Validation::createValidator();
        // $violations = $validator->validate($name, [
        //     new Assert\NotBlank(),
        //     new Assert\Length(['min' => 2]),
        // ]);
        // $violations->addAll($validator->validate($email, [
        //     new Assert\NotBlank(),
        //     new Assert\Email(),
        // ]));
        // $violations->addAll($validator->validate($subject, [
        //     new Assert\NotBlank(),
        // ]));
        // $violations->addAll($validator->validate($message, [
        //     new Assert\NotBlank(),
        // ]));

        // if (count($violations) > 0) {
        //     // Si des violations sont trouvées, renvoyer un message d'erreur
        //     $errorMessage = 'Validation errors: ';
        //     foreach ($violations as $violation) {
        //         $errorMessage .= $violation->getMessage() . ' ';
        //     }
        //     return new JsonResponse(['status' => 'error', 'errors' => $errorMessage], JsonResponse::HTTP_BAD_REQUEST);
        // }

        // Si tout est valide, renvoyer un message de succès
        return new JsonResponse([
            'status' => 'success',
            'data' => [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
            ]
        ], JsonResponse::HTTP_OK);
    
    }
}
