<?php

// src/EventSubscriber/NotificationSubscriber.php
namespace App\EventSubscriber;

use App\Entity\Notification;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Service\NotificationService;
use App\Repository\UserRepository;

class NotificationSubscriber implements EventSubscriber
{
    private $notificationService;
    private $userRepository;

    public function __construct(NotificationService $notificationService, UserRepository $userRepository)
    {
        $this->notificationService = $notificationService;
        $this->userRepository = $userRepository;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Notification) {
            return;
        }

        // Récupérer le SUPERADMIN
        $superAdmin = $this->userRepository->findOneByRole('ROLE_SUPER_ADMIN');

        if ($superAdmin) {
            // Créer et envoyer la notification
            $this->notificationService->createNotification(
                $superAdmin,
                'Nouvelle notification',
                'Une nouvelle notification a été créée.'
            );
        }
    }
}

