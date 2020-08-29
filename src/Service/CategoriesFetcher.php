<?php 

namespace App\Service;

use App\Repository\CategoryRepository;

class CategoriesFetcher
{
    private $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getCategories()
    {
        return $this->repository->findAll();
    }
}
