<?php

// src/Service/NotificationService.php
namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Notification;
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
        $notification = $this->prepareNotification($user, $type, $message);

        $this->entityManager->persist($notification);
        
        $this->entityManager->flush();
    }

    public function createNotifications(User $user, string $type, string $message): void
    {
        $notification = $this->prepareNotification($user, $type, $message);

        $this->entityManager->persist($notification);
    }

    private function prepareNotification($user, $type, $message): ?Notification
    {
        return (new Notification())
        ->setTheUser($user)
        ->setType($type)
        ->setContent($message)
        ->setCreatedAt(new \DateTime());
    }

    public function notificate(array $users, string $type, string $message): void
    {
        foreach ($users as $user) {
            $this->createNotification($user, $type, $message);
        }
    }

    public function notifyAdminsOfNewComment(array $admins, Post $post): void
    {
        $message = sprintf('A new comment on topic "%s" is awaiting your approval.', $post->getTopic()->getTitle());
        $this->notificate($admins, 'comment', $message);
    }
}

