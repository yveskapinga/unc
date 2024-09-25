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
        ->select('IDENTITY(m.sender) as senderId, COUNT(m.isRead) as unreadCount')
        ->where('m.recipient = :user')
        ->andWhere('m.isRead = false')
        ->setParameter('user', $user)
        ->groupBy('m.sender')
        ->orderBy('m.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function findAllSenders(User $user) {
        return $this->createQueryBuilder('m')
        ->select('(m.sender) as senderId')
        ->where('m.recipient = :user')
        ->setParameter('user', $user)
        ->groupBy('m.sender')
        ->orderBy('m.isRead', 'DESC')
        ->getQuery()
        ->getResult();
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

    public function findUnreadMessagesByRecipient(User $recipient)
    {
        return $this->createQueryBuilder('m')
            ->where('m.isRead = false')
            ->andWhere('m.recipient = :recipient')
            ->setParameter('recipient', $recipient)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllSendersWithLastMessageDate(User $user)
    {
        return $this->createQueryBuilder('m')
            ->select('IDENTITY(m.sender) as senderId, MAX(m.createdAt) as lastMessageDate, SUM(CASE WHEN m.isRead = false THEN 1 ELSE 0 END) as unreadCount')
            ->where('m.recipient = :user')
            ->setParameter('user', $user)
            ->groupBy('m.sender')
            ->orderBy('lastMessageDate', 'DESC')
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
            ->set('m.isRead', ':true')
            ->where('(m.sender = :otherUser AND m.recipient = :currentUser)')
            ->setParameter('true', true)
            ->setParameter('currentUser', $currentUser)
            ->setParameter('otherUser', $otherUser)
            ->getQuery()
            ->execute();
    }

    public function findAllMessages(User $user)
    {
        return $this->createQueryBuilder('m')
            ->where('m.sender = :user')
            ->orWhere('m.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function conversation(User $user, $senderId)
    {
        return $this->createQueryBuilder('m')
            ->where('m.sender = :user AND m.recipient = :sender')
            ->orWhere('m.sender = :sender AND m.recipient = :user')
            ->setParameter('user', $user)
            ->setParameter('sender', $senderId)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }


}
