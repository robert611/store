<?php

namespace App\Repository;

use App\Entity\DeliveryType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeliveryType|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryType|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryType[]    findAll()
 * @method DeliveryType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryType::class);
    }

    public function removeAll($productId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "DELETE FROM delivery_type_product WHERE product_id = :productId";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['productId' => $productId]);

        return true;
    }
}
