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

    public function getNumberOfAllProducts(): int
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = 'SELECT count(*) as count FROM product';

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch()['count'];
    }

    public function getCategoriesWithGivenNumberOfProducts(int $productsNumber): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = 'SELECT category_id FROM product GROUP BY category_id HAVING COUNT(id) >= :productsNumber';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['productsNumber' => $productsNumber]);

        return $stmt->fetchAllAssociative();
    }

    public function getRandomProducts(int $limit, array $randomIds): array
    {
        $entityManager = $this->getEntityManager();
                
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Product p
            WHERE p.id IN(:randomIds)'
        )
        ->setParameter('randomIds', $randomIds)
        ->setMaxResults($limit);

        return $query->getResult();
    }

    public function getRandomProductsIds(int $productsAmount): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = "SELECT id FROM product ORDER BY rand() LIMIT $productsAmount";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getRandomCategoryProductsIds($categoryId, $limit, $excludedProducts): array
    {
        $conn = $this->getEntityManager()->getConnection();

        /* If you pass an empty array to Not In clause, none products will be returned */
        if (empty($excludedProducts)) $excludedProducts = -1000;

        $sql = "SELECT id FROM product WHERE category_id = :category_id AND id NOT IN(:excluded_products) ORDER BY rand() LIMIT $limit";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['category_id' => $categoryId, 'excluded_products' => is_array($excludedProducts) ? implode($excludedProducts) : $excludedProducts]);

        return $stmt->fetchAll();
    }

    /**
     * @return Product[]
     */
    public function getRandomCategoryProducts(int $categoryId, int $limit, array $excludedProducts = []): array
    {
        $entityManager = $this->getEntityManager();

        $randomIds = $this->getRandomCategoryProductsIds($categoryId, $limit, $excludedProducts);
        
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Product p
            WHERE p.id IN(:randomIds)'
        )
        ->setParameter('randomIds', $randomIds);

        return $query->getResult();
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
