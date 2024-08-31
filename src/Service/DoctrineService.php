<?php

// src/Service/DoctrineService.php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DoctrineService
{
    private $em;
    private $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function persistEntities(
        object $entity1, 
        ?object $entity2 = null, 
        ?string $message = null, 
        ?string $type = null
        ): void
    {
        $this->em->persist($entity1);
        if ($entity2 !== null) {
            $this->em->persist($entity2);
        }

        $this->em->flush();

        if ($message !== null && $type !== null) {
            $session = $this->requestStack->getSession();
            if ($session) {
                $flashBag = $session->getBag('flashes');
                $flashBag->add($type, $message);
            }
        }
    }
}

