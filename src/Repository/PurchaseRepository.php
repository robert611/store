<?php

namespace App\Repository;

use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function isCodeAvailable(string $code): bool
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('COUNT(p)');
        $qb->where($qb->expr()->eq('p.code', ':code'));

        $query = $qb->getQuery();
        $query->setParameter('code', $code);

        return 0 === (int) $query->getScalarResult()[0][1];
    }
}
