<?php

namespace App\Service;

use Symfony\Component\Security\Core\Security;
use App\Repository\InterfederationRepository;

class RoleLevelService
{
    private $security;
    private $interfederationRepository;

    public function __construct(Security $security, InterfederationRepository $interfederationRepository)
    {
        $this->security = $security;
        $this->interfederationRepository = $interfederationRepository;
    }

    public function getUserInterfederations()
    {
        $user = $this->security->getUser();
        $membership = $user->getMembership();
        $level = $membership->getLevel();

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->interfederationRepository->findAll();
        }

        if ($this->security->isGranted('ROLE_NATIONAL_ADMIN') && $level === 'national') {
            return $this->interfederationRepository->findByLevel('national');
        }

        if ($this->security->isGranted('ROLE_PROVINCIAL_ADMIN') && $level === 'provincial') {
            return $this->interfederationRepository->findByLevel('provincial');
        }

        // Ajoute d'autres conditions pour les autres niveaux

        return [];
    }
}
