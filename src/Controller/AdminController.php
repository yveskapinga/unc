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

    #[Route('/statistics', name: 'app_statistics')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }

    #[Route('/promote/user', name: 'promote_user')]
    public function promoteUser(){
        
    }

    #[Route('/set-timezone', name: 'set_timezone', methods: ['POST'])]
    public function setTimezone(Request $request, SessionInterface $session): Response
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['timezone'])) {
            $session->set('timezone', $data['timezone']);
        }

        return new Response('Timezone set', Response::HTTP_OK);
    }
    
    #[Route('/map', name: 'app_map')]
    public function map(): Response
    {
        return $this->render('admin/map.html.twig');
    }

    #[Route('/index', name: 'app_index')]
    public function app(): Response
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
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
