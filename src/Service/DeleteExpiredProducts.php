<?php 

namespace App\Service;

use App\Repositories\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class DeleteExpiredProducts
{
    private $productRepository;
    private $entityManager;
    
    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    public function delete()
    {
        $products = $this->productRepository->findAll();

        $currentDate = (new \DateTime('d-m-Y'))->format('d-m-Y');

        $expiredProducts = (new ArrayCollection($products))->map(function($product) use ($currentDate) {

            if ($product->getTimeToTheEndOfAnAuction() < $currentDate) {
                $this->entityManager->remove($product);
            }
        });

        $this->entityManager->flush();
    }
}