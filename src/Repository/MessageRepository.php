<?php

// src/Repository/MessageRepository.php
namespace App\Repository;

use App\Entity\User;
use App\Entity\Message;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function createQueryBuilderForRecipient($user): QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->where('m.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC');
    }

    public function searchMessages($query): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.content LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
    public function findSendersWithUnreadCount(User $user)
    {
        return $this->createQueryBuilder('m')
        ->select('IDENTITY(m.sender) as senderId, COUNT(m.id) as unreadCount')
        ->where('m.recipient = :user')
        ->andWhere('m.isRead = false')
        ->setParameter('user', $user)
        ->groupBy('m.sender')
        ->orderBy('MAX(m.createdAt)', 'DESC')
        ->getQuery()
        ->getResult();
    }
    

    public function findMessagesBetweenUsers(User $currentUser, User $otherUser)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :currentUser AND m.recipient = :otherUser) OR (m.sender = :otherUser AND m.recipient = :currentUser)')
            ->setParameter('currentUser', $currentUser)
            ->setParameter('otherUser', $otherUser)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
    

    public function markMessagesAsRead(User $currentUser, User $otherUser)
    {
        $this->createQueryBuilder('m')
            ->update()
            ->set('m.isRead', true)
            ->where('m.sender = :otherUser AND m.recipient = :currentUser AND m.isRead = false')
            ->setParameter('currentUser', $currentUser)
            ->setParameter('otherUser', $otherUser)
            ->getQuery()
            ->execute();
    }

    public function findSenders(User $user)
    {
        return $this->createQueryBuilder('m')
            ->select('DISTINCT IDENTITY(m.sender) as senderId')
            ->where('m.recipient = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

}
