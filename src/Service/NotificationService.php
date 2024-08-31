<?php

// src/Service/NotificationService.php
namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createNotification(User $user, string $type, string $message): void
    {
        $notification = (new Notification())
            ->setTheUser($user)
            ->setType($type)
            ->setContent($message)
            ->setCreatedAt(new \DateTime());

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}

