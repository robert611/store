<?php

namespace App\Repository;

use App\Entity\ProductOpinion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductOpinion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductOpinion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductOpinion[]    findAll()
 * @method ProductOpinion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductOpinionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductOpinion::class);
    }

    // /**
    //  * @return ProductOpinion[] Returns an array of ProductOpinion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductOpinion
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
