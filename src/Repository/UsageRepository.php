<?php

namespace App\Repository;

use App\Entity\Usage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Usage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usage[]    findAll()
 * @method Usage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usage::class);
    }

    // /**
    //  * @return Usage[] Returns an array of Usage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Usage
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
