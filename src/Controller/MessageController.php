<?php
// src/Controller/MessageController.php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Form\MessageSearchType;
use App\Service\EncryptionService;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/message')]
class MessageController extends AbstractController
{

    public function __construct(
        private Security $security,
        private PaginatorInterface $paginator,
        EncryptionService $encryptionService,
        private MessageRepository $messageRepository,
    ){
        Message::setEncryptionService($encryptionService);
    }

    #[Route('/', name: 'app_message_index', methods: ['GET'])]
    public function index(UserRepository $userRepo, MessageRepository $messageRepository, Request $request): Response
    {
        $currentUser = $this->getUser(); // Récupère l'utilisateur actuellement connecté
        $sendersData = $messageRepository->findSendersWithUnreadCount($currentUser);
    
        // Récupère les entités User correspondantes
        $senders = [];
        foreach ($sendersData as $data) {
            $sender = $userRepo->find($data['senderId']);
            if ($sender) {
                $senders[] = [
                    'user' => $sender,
                    'unreadCount' => $data['unreadCount']
                ];
            }
        }
    
        // Détermine l'utilisateur sélectionné
        $selectedUserId = $request->query->get('userId');
        $selectedUser = null;
        $messages = [];
    
        if ($selectedUserId) {
            $selectedUser = $userRepo->find($selectedUserId);
        } elseif (!empty($senders)) {
            // Sélectionne par défaut l'expéditeur le plus récent
            $selectedUser = $senders[0]['user'];
        }
    
        if ($selectedUser) {
            $messages = $messageRepository->findMessagesBetweenUsers($currentUser, $selectedUser);
            //dd($currentUser, $selectedUser);
            $messageRepository->markMessagesAsRead($currentUser, $selectedUser);
        }
    
        return $this->render('message/index.html.twig', [
            'senders' => $senders,
            'messages' => $messages,
            'selectedUser' => $selectedUser,
        ]);
    }

    #[Route('/send', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $currentUser = $this->getUser();
        $recipientId = $request->request->get('recipientId');
        $content = $request->request->get('content');
        
        $recipient = $userRepository->find($recipientId);
        if ($recipient && $content) {
            $message = new Message();
            $message->setSender($currentUser);
            $message->setRecipient($recipient);
            $message->setContent($content);
            $message->setCreatedAt(new \DateTime());
            $message->setIsRead(false);

            $entityManager->persist($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_message_index'); //, ['userId' => $recipientId]);
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
            $message->setSender($this->security->getUser());
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
