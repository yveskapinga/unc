<?php

namespace App\Service;

use App\Service\SecurityService;
use App\Repository\MessageRepository;
use App\Repository\MembershipRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserService
{

    public function __construct(
        private SecurityService $securityService,
        private MembershipRepository $membershipRepository,
        private NotificationRepository $notificationRepository,
        private MessageRepository $messageRepository,
        )
    {
        
    }

    public function activeUsers(SessionInterface $session, int $timeDiff): array
    {
        $lastActivityAt = $session->get('lastActivityAt');
        $activeUsers = [];

        if ($lastActivityAt) {
            $now = new \DateTime();
            $interval = $now->diff($lastActivityAt);

            if ($interval->i < $timeDiff) { // Vérifie si l'utilisateur a été actif dans les 5 dernières minutes
                $activeUsers[] = $this->securityService->getConnectedUser();
            }
        }

        return $activeUsers ;
    }

    public function getUserLevel()
    {
        $user = $this->testUser();

        $membership = $this->membershipRepository->findOneBy(['theUser' => $user]);
        return $membership ? $membership->getFonction() : 'Militant';
    }

    public function getMessage()
    {
        $user = $this->testUser();
        return $user ? $this->messageRepository->findUnreadMessagesByRecipient($user) : null;
    }

    public function getNotification()
    {
        $user = $this->testUser();

        return $user ? $this->notificationRepository->findUnreadByUser($user) : null ;
    }

    private function testUser()
    {
        $user = $this->securityService->getConnectedUser();
        if (!$user) {
            return null;
        }else{
            return $user;
        }
    }
}