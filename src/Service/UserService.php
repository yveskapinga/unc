<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class UserService
{

    public function __construct(private Security $security)
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
}