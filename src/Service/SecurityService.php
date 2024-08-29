<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class SecurityService extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function isAdmin()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            // Redirigez vers la page d'erreur personnalisée
            return $this->redirectToRoute('custom_error_page');
            throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }
    }

    public function isEnseignant()
    {
        return $this->accessAuthorisation('ROLE_ENSEIGNANT');
    }

    public function isUser()
    {
        return $this->accessAuthorisation('ROLE_USER');
    }

    // J'ai créé la méthode accessAutorisation pour prend en paramèetre le rôle ayant les autorisations et vérifie si
    // Si l'utilisateur détient ces accès là
    public function accessAuthorisation($rolesToGrant)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            // Redirigez vers la page d'erreur personnalisée
            return $this->redirectToRoute('custom_error_page');
            throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }
    }
}
