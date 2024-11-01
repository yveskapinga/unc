<?php

namespace App\Repository;

use App\Entity\Interfederation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findOneByRole(string $role): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllSenders(User $currentUser)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.messages', 'm')
            // ->addSelect('MAX(m.createdAt) as lastMessageDate')
            ->where('m.recipient = :currentUser')
            ->setParameter('currentUser', $currentUser)
            ->groupBy('u.id')
            // ->orderBy('lastMessageDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countVisitsByDate(\DateTime $date): int
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.lastActivityAt >= :date')
            ->setParameter('date', $date->format('Y-m-d H:i:s'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countUsersByDateRange(\DateTime $startDate, \DateTime $endDate): int
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
            ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAdministratorsByInterfederation(Interfederation $interfederation)
    {
        return $this->createQueryBuilder('u')
            ->join('u.membership', 'm')
            ->where('m.interfederation = :interfederation')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('interfederation', $interfederation)
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();
    }

    public function findUsersByRole(string $role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%')
            ->getQuery()
            ->getResult();
    }

     /**
     * Recherche les utilisateurs par terme et pagination.
     *
     * @param string|null $term Terme de recherche
     * @param int $page Numéro de la page
     * @param int $limit Nombre d'utilisateurs par page
     * @return User[]
     */
    public function searchUsers(string $term = null, int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('u');

        if ($term) {
            $qb->andWhere('u.username LIKE :term OR u.email LIKE :term')
               ->setParameter('term', '%' . $term . '%');
        }

        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte le nombre total d'utilisateurs correspondant au terme de recherche.
     *
     * @param string|null $term Terme de recherche
     * @return int
     */
    public function countUsers(string $term = null): int
    {
        $qb = $this->createQueryBuilder('u')
                   ->select('COUNT(u.id)');

        if ($term) {
            $qb->andWhere('u.username LIKE :term OR u.email LIKE :term')
               ->setParameter('term', '%' . $term . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    
    

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
