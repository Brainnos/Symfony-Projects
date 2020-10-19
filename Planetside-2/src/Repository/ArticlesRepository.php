<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    public function getLastArticles($nbArticles = 3)
    {
        return $this->findBy(
            array('active' => 1),
            array('date_creation' => 'DESC'),
            $nbArticles
        );
    }

    public function getArticlesActive()
    {
        return $this->findBy(
            array('active' => 1),
            array('date_creation' => 'DESC')
        );
    }

    public function getArticlesYesterday()
    {
        $hier_matin = new \DateTime("yesterday midnight");
        $hier_soir = new \DateTime("today midnight");

        return $this->createQueryBuilder('a')
            ->andWhere('a.active = 1')
            ->andWhere('a.date_creation >= :hier_matin')
                ->setParameter('hier_matin', $hier_matin)
            ->andWhere('a.date_creation < :hier_soir')
                ->setParameter('hier_soir', $hier_soir)
            ->orderBy('a.date_creation', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getArticlesBytag($tag)
    {

        return $this->createQueryBuilder('a')
            ->where('a.tag = '.$tag)
            ->orderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }








    // /**
    //  * @return Articles[] Returns an array of Articles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Articles
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
