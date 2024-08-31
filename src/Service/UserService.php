<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\MembershipRepository;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;

class UserService
{

    public function __construct(
        private Security $security,
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
                $activeUsers[] = $this->security->getUser();
            }
        }

        return $activeUsers ;
    }

    public function getUserLevel()
    {
        $user = $this->testUser();

        $membership = $this->membershipRepository->findOneBy(['theUser' => $user]);
        return $membership ? $membership->getLevel() : 'Militant';
    }

    public function getMessage()
    {
        $this->testUser();
        $unreadMessages = $this->messageRepository->findUnreadMessagesByRecipient($this->security->getUser());
        // foreach ($unreadMessages as $message) {
        //     // $message->decryptContent();
        //     $unreadMessages [] = $message;
        // }
        // dd($unreadMessages);      
        return $unreadMessages;
    }

    public function getNotification()
    {
        $this->testUser();
        $unreadNotifications = $this->notificationRepository->findUnreadByUser($this->security->getUser());
        return  $unreadNotifications;
    }

    private function testUser()
    {
        $user = $this->security->getUser();
        if (!$user) {
            return null;
        }else{
            return $user;
        }
    }
}