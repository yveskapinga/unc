<?php

namespace App\Repository;

use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Topic>
 *
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function findTopCommentedTopics(int $max = 5)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.posts', 'p')
            ->groupBy('t.id')
            ->orderBy('COUNT(p.id)', 'DESC')
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    public function findBestAuthor()
    {
        return $this->createQueryBuilder('t')
            ->select('a.id, a.username, COUNT(DISTINCT t.id) as articleCount, COUNT(p.id) as commentCount')
            ->join('t.author', 'a')
            ->leftJoin('t.posts', 'p')
            ->groupBy('a.id')
            ->orderBy('commentCount', 'DESC')
            ->addOrderBy('articleCount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    


//    /**
//     * @return Topic[] Returns an array of Topic objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Topic
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
