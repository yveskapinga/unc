<?php

namespace App\Service;

use App\Entity\Interfederation;
use App\Repository\InterfederationRepository;

class InterfederationService
{
    private $interfederationRepository;

    public function __construct(InterfederationRepository $interfederationRepository)
    {
        $this->interfederationRepository = $interfederationRepository;
    }

    // Méthode pour obtenir l'interfédération basée sur la province
    public function getInterfederationByProvince(string $province): ?Interfederation
    {
        return $this->interfederationRepository->findOneBy(['designation' => $province]);
    }
}
