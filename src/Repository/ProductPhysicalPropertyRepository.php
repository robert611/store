<?php

namespace App\Repository;

use App\Entity\ProductPhysicalProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductPhysicalProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductPhysicalProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductPhysicalProperty[]    findAll()
 * @method ProductPhysicalProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductPhysicalPropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductPhysicalProperty::class);
    }

    // /**
    //  * @return ProductPhysicalProperty[] Returns an array of ProductPhysicalProperty objects
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
    public function findOneBySomeField($value): ?ProductPhysicalProperty
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
