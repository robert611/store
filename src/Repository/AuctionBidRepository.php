<?php

namespace App\Repository;

use App\Entity\AuctionBid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AuctionBid|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuctionBid|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuctionBid[]    findAll()
 * @method AuctionBid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuctionBidRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuctionBid::class);
    }

    // /**
    //  * @return AuctionBid[] Returns an array of AuctionBid objects
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
    public function findOneBySomeField($value): ?AuctionBid
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
