<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[]
     */
    public function findProductByNameAndCategory(?string $name, ?string $category, ?string $owner): array
    {
        $entityManager = $this->getEntityManager();

        $categoryClause = 'p.category = :category';

        $ownerClause = 'p.owner = :owner';

        /* If category is a string like for instance "Category" it will set it to 0 otherwise if it is proper number string like "1" to its number of type int */
        $category = (int) $category;

        if (is_null($category) or $category < 1) {
            $category = 0;
            $categoryClause = 'p.category != :category ';
        }

        $owner = (int) $owner;

        if (is_null($owner) or $owner < 1) {
            $owner = 0;
            $ownerClause = 'p.owner != :owner ';
        }

        $name = (string) $name;

        $query = $entityManager->createQuery(
            'SELECT p FROM App\Entity\Product p
            WHERE ' . $categoryClause . '
            AND p.name LIKE :name 
            AND p.quantity > 0 
            AND (p.is_deleted is null or p.is_deleted = false)
            AND ' . $ownerClause
        )
        ->setParameter('category', $category)
        ->setParameter('name', "%$name%")
        ->setParameter('owner', $owner);

        return $query->getResult();
    }
}
