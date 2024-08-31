<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class SecurityService
{
    public function __construct(private Security $security)
    {
    }

    public function getConnectedUser() : ?User
    {
        /** @var User $user */
        $user = $this->security->getUser();
        return $user;
    }

    public function isAdmin()
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            // Redirigez vers la page d'erreur personnalisée
            // return $this->redirectToRoute('custom_error_page');
            throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }
    }

    public function isSuperAdmin()
    {
        if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            // Redirigez vers la page d'erreur personnalisée
            // return $this->redirectToRoute('custom_error_page');
            throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }
        
    }
}
