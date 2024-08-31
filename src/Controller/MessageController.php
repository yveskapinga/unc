<?php
// src/Controller/MessageController.php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Form\MessageSearchType;
use Doctrine\ORM\EntityManager;
use App\Service\DoctrineService;
use App\Service\SecurityService;
use App\Repository\UserRepository;
use App\Service\EncryptionService;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/message')]
class MessageController extends AbstractController
{

    public function __construct(
        private SecurityService $securityService,
        private DoctrineService $doctrineService,
        private PaginatorInterface $paginator,
        private EncryptionService $encryptionService,
        private MessageRepository $messageRepository,

    ){
        // Message::setEncryptionService($encryptionService);
    }

    // #[Route('/', name: 'app_message_index', methods: ['GET'])]
    // public function index(UserRepository $userRepo, MessageRepository $messageRepository, Request $request): Response
    // {
    //     $currentUser = $this->securityService->getConnectedUser(); // Récupère l'utilisateur actuellement connecté
    //     $sendersData = $this->messageRepository->findSendersWithUnreadCount($currentUser);
    //     $allSendersData = $this->messageRepository->findAllSenders($currentUser);
    //     // Récupère les entités User correspondantes
    //     $senders = [];
    //     foreach ($sendersData as $data) {
    //         $sender = $userRepo->find($data['senderId']);
    //         if ($sender) {
    //             $senders[] = [
    //                 'user' => $sender,
    //                 'unreadCount' => $data['unreadCount']
    //             ];
    //         }
    //     }

    //     $allSenders = []; 
    //     foreach ($allSendersData as $data){
    //          $data = $userRepo->find($data['senderId']);
    //         if ($data){
    //             $allSenders[]['user'] = $data;
    //         }
    //     }
    //     // Détermine l'utilisateur sélectionné
    //     $selectedUserId = $request->query->get('userId');
    //     $selectedUser = null;
    //     $messages = [];
    
    //     if ($selectedUserId) {
    //         $selectedUser = $userRepo->find($selectedUserId);
    //     } elseif (!empty($senders)) {
    //         // Sélectionne par défaut l'expéditeur le plus récent
    //         $selectedUser = $senders[0]['user'];
    //     }
    
    //     if ($selectedUser) {
    //         $messages = $messageRepository->findMessagesBetweenUsers($currentUser, $selectedUser);
    //         //dd($currentUser, $selectedUser);
    //         // $messageRepository->markMessagesAsRead($currentUser, $selectedUser);
    //     }
    
    //     return $this->render('message/index.html.twig', [
    //         'senders' => $senders,
    //         'messages' => $messages,
    //         'selectedUser' => $selectedUser,
    //     ]);
    // }

    #[Route('/', name: 'app_message_index')]
    public function index(MessageRepository $messageRepository): Response
    {
        $user = $this->securityService->getConnectedUser();

        // Récupérer tous les messages envoyés et reçus par l'utilisateur
        $messages = $messageRepository->createQueryBuilder('m')
            ->where('m.sender = :user')
            ->orWhere('m.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        // Organiser les messages par expéditeur
        $senders = [];
        foreach ($messages as $message) {
            $senderId = $message->getSender()->getId() === $user->getId() ? $message->getRecipient()->getId() : $message->getSender()->getId();
            $senders[$senderId][] = $message;
        }

        return $this->render('message/try.html.twig', [
            'senders' => $senders,
        ]);
    }

    #[Route('/conversation/{senderId}', name: 'conversation')]
    public function conversation(int $senderId, MessageRepository $messageRepository, UserRepository $userRepo): JsonResponse
    {
        $user = $this->securityService->getConnectedUser();
        $sender = $userRepo->find($senderId);
        $this->messageRepository->markMessagesAsRead($user, $sender);

        $messages = $messageRepository->createQueryBuilder('m')
            ->where('m.sender = :user AND m.recipient = :sender')
            ->orWhere('m.sender = :sender AND m.recipient = :user')
            ->setParameter('user', $user)
            ->setParameter('sender', $senderId)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        $conversation = [];
        foreach ($messages as $message) {
            $message->decryptContent();
            $conversation[] = [
                'content' => $message->getContent(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'isSent' => $message->getSender()->getId() === $user->getId(),
            ];
        }

        $sender = $messages[0]->getSender()->getId() === $user->getId() ? $messages[0]->getRecipient() : $messages[0]->getSender();
        return new JsonResponse([
            'messages' => $conversation,
            'sender' => [
                'username' => $sender->getUsername(),
                'profilePicture' => $sender->getPhoto(),
            ],
        ]);
    }

    #[Route('/send_message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $recipientId = $request->request->get('recipientId');
        $content = $request->request->get('content');

        $recipient = $em->getRepository(User::class)->find($recipientId);

        $message = new Message();
        $message->setSender($user);
        $message->setRecipient($recipient);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTime());
        $message->setIsRead(false);

        $em->persist($message);
        $em->flush();
        $decryptedContent = $this->encryptionService->decrypt($message->getContent());

        return new JsonResponse([
            'content' => $decryptedContent,
            'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }


    
    
    #[Route('/messages/{id}', name: 'user_messages_with_user')]
    public function messages(User $user): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos messages.');
        }

        //$senders = $this->messageRepository->findSendersWithUnreadCount($currentUser);
        $messages = $this->messageRepository->findMessagesBetweenUsers($currentUser, $user);

        // Marquer les messages comme lus
        $this->messageRepository->markMessagesAsRead($currentUser, $user);

        return $this->render('message/messages.html.twig', [
            // 'senders' => $senders,
            'messages' => $messages,
        ]);
    }

    #[Route('/new', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message, [
            'recipients' => $entityManager->getRepository(User::class)->findAll(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->securityService->getConnectedUser());
            $message->setCreatedAt(new \DateTime());
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_message_show', methods: ['GET'])]
    #[ParamConverter('message', class: Message::class)]
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_message_edit', methods: ['GET', 'POST'])]
    #[ParamConverter('message', class: Message::class)]
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_message_delete', methods: ['POST'])]
    #[ParamConverter('message', class: Message::class)]
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/search', name: 'app_message_search', methods: ['GET', 'POST'])]
    public function search(Request $request, MessageRepository $messageRepository): Response
    {
        $form = $this->createForm(MessageSearchType::class);
        $form->handleRequest($request);

        $messages = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('query')->getData();
            $messages = $messageRepository->searchMessages($query);
        }

        return $this->render('message/search.html.twig', [
            'form' => $form->createView(),
            'messages' => $messages,
        ]);
    }
}
