<?php

// src/Service/TopicService.php
namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;


class NotificationService
{
    
    public function __construct(
        )
    {
    }

    public function createNotification(
        User $user, 
        string $type, 
        string $message) : ?Notification
    {
        return (new Notification())
            ->setTheUser($user)
            ->setType($type)
            ->setContent($message)
            ->setCreatedAt(new \DateTime());

    }
}
