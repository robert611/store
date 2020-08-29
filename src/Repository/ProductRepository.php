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
    public function findProductByNameAndCategory(?string $name, ?string $category): array
    {
        $entityManager = $this->getEntityManager();

        $categoryClause = 'p.category = :category';

        /* If category is a string like for instance "Category" it will set it to 0 otherwise if it is proper number string like "1" to its number of type int */
        $category = (int) $category;

        if (is_null($category) or $category < 1) {
            $category = 0;
            $categoryClause = 'p.category != :category ';
        }

        $name = (string) $name;

        $query = $entityManager->createQuery(
            'SELECT p FROM App\Entity\Product p
            WHERE ' . $categoryClause . '
            AND p.name LIKE :name'
        )->setParameter('category', $category)->setParameter('name', "%$name%");

        return $query->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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
