<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

class ActivityListener
{
    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if ($user) {
            $user->setLastActivityAt(new \DateTime());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}
