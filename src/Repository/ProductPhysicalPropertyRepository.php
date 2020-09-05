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

    public function removeAll($productId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "DELETE FROM product_physical_property WHERE product_id = :productId";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['productId' => $productId]);

        return true;
    }
}
