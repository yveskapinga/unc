<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityService
{
    private $security;
    private $requestStack;
    private $urlGenerator;

    public function __construct(
        Security $security,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->security = $security;
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
    }

    private function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function getConnectedUser(): ?User
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

    public function checkUserAuthentication()
    {
        if (!$this->security->getUser()) {
            // Redirigez vers la page d'erreur personnalisée
            // return $this->redirectToRoute('custom_error_page');
            throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }
    }
}
