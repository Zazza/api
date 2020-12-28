<?php

namespace App\Repository;

use App\Entity\StaticTransactionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StaticTransactionType|null find($id, $lockMode = null, $lockVersion = null)
 * @method StaticTransactionType|null findOneBy(array $criteria, array $orderBy = null)
 * @method StaticTransactionType[]    findAll()
 * @method StaticTransactionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaticTransactionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaticTransactionType::class);
    }

    // /**
    //  * @return StaticTransactionType[] Returns an array of StaticTransactionType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StaticTransactionType
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
