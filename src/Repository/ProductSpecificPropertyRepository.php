<?php

namespace App\Repository;

use App\Entity\ProductSpecificProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductSpecificProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductSpecificProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductSpecificProperty[]    findAll()
 * @method ProductSpecificProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductSpecificPropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductSpecificProperty::class);
    }

    public function removeAll($productId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "DELETE FROM product_specific_property WHERE product_id = :productId";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['productId' => $productId]);

        return true;
    }
}
