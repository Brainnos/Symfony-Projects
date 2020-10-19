<?php

namespace App\Repository;

use App\Entity\SubTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SubTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubTags[]    findAll()
 * @method SubTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubTags::class);
    }

    // /**
    //  * @return SubTags[] Returns an array of SubTags objects
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
    public function findOneBySomeField($value): ?SubTags
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
