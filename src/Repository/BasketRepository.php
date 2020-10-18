<?php

namespace App\Repository;

use App\Entity\Basket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Basket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Basket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Basket[]    findAll()
 * @method Basket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Basket::class);
    }

    public function addProductToBasket(int $userId, int $productId, int $quantity)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "INSERT INTO basket VALUES (null, :productId, :userId, :quantity)";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['productId' => $productId, 'userId' => $userId, 'quantity' => $quantity]);

        return true;
    }

    public function increaseProductQuantity(int $userId, int $productId, int $quantity)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "UPDATE basket SET quantity = quantity + :quantity WHERE user_id = :userId AND product_id = :productId";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['productId' => $productId, 'userId' => $userId, 'quantity' => $quantity]);

        return true;
    }
}
