<?php

namespace App\Repository;

use App\Entity\Interfederation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Interfederation>
 *
 * @method Interfederation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interfederation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interfederation[]    findAll()
 * @method Interfederation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterfederationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interfederation::class);
    }

//    /**
//     * @return Interfederation[] Returns an array of Interfederation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Interfederation
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
