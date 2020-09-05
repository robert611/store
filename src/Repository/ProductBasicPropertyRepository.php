<?php

namespace App\Repository;

use App\Entity\ProductBasicProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductBasicProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductBasicProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductBasicProperty[]    findAll()
 * @method ProductBasicProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductBasicPropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductBasicProperty::class);
    }

    public function removeAll($productId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "DELETE FROM product_basic_property WHERE product_id = :productId";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['productId' => $productId]);

        return true;
    }
}
