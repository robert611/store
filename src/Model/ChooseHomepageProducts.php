<?php 

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\ProductRepository;

class ChooseHomepageProducts
{
    private ProductRepository $productRepository;

    private array $alreadyFetchedProductsIds;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->alreadyFetchedProductsIds = array();
    }

    public function getHomepageProducts(): array 
    {
        $homepageProducts = array();

        $numberOfAllProducts = $this->productRepository->getNumberOfAllProducts();

        $amountOfAllProductsToDisplay = 19;

        $randomProductsIdsToSearchThrough = $this->productRepository->getProductsIds($amountOfAllProductsToDisplay);

        $categoriesWithAtLeastSixProducts = $this->productRepository->getCategoriesWithGivenNumberOfProducts(6);
        $categoriesWithAtLeastThreeProducts = $this->productRepository->getCategoriesWithGivenNumberOfProducts(3);
        $categoriesWithAtLeastTwoProducts = $this->productRepository->getCategoriesWithGivenNumberOfProducts(2);

        $usedCategoriesIds = array();

        $productsGroupsData = [
            ['categories' => $categoriesWithAtLeastSixProducts, 'homepage_products_table_index' => 'six_products_row', 'group_products_amount' => 6],
            ['categories' => $categoriesWithAtLeastSixProducts, 'homepage_products_table_index' => 'second_six_products_row', 'group_products_amount' => 6],
            ['categories' => $categoriesWithAtLeastThreeProducts, 'homepage_products_table_index' => 'three_products_row', 'group_products_amount' => 3],
            ['categories' => $categoriesWithAtLeastTwoProducts, 'homepage_products_table_index' => 'two_products_row', 'group_products_amount' => 2],
            ['categories' => $categoriesWithAtLeastTwoProducts, 'homepage_products_table_index' => 'second_two_products_row', 'group_products_amount' => 2],
        ];

        foreach ($productsGroupsData as $productsGroupData)
        {
            $productsAmount = $productsGroupData['group_products_amount'];
            $homepageIndex = $productsGroupData['homepage_products_table_index'];

            if (count($productsGroupData['categories']) > count($usedCategoriesIds)) {
                do {
                    $productsRowCategoryId = $productsGroupData['categories'][array_rand($productsGroupData['categories'])]['category_id'];
                } while(in_array($productsRowCategoryId, $usedCategoriesIds));
    
                $homepageProducts[$homepageIndex] = $this->productRepository->getRandomCategoryProducts($productsRowCategoryId, $productsAmount, $this->alreadyFetchedProductsIds);

                /* In this case there is less products which were not already fetched then required */
                if (count($homepageProducts[$homepageIndex]) < $productsAmount) 
                {
                    $homepageProducts[$homepageIndex] = array();
                }

                $usedCategoriesIds[] = $productsRowCategoryId;
            } else {
                if ($numberOfAllProducts >= (count($this->alreadyFetchedProductsIds) + $productsAmount))
                {
                    $unfetchedProductsIds = $this->getUnfetchedProductsIds($randomProductsIdsToSearchThrough);

                    $homepageProducts[$homepageIndex] = $this->productRepository->getRandomProducts($productsAmount, $unfetchedProductsIds);
                }
                else $homepageProducts[$homepageIndex] = array();
            }
            
            $this->saveFetchedProductsIds($homepageProducts[$homepageIndex]);
        }

        return $homepageProducts;
    }

    public function getUnfetchedProductsIds(array $randomProductsIdsToSearchThrough): array
    {
        $unfetchedProductsIds = array();

        foreach ($randomProductsIdsToSearchThrough as $productId)
        {
            /* $productId is supposed to be in array to work well with sql query */
            if (!in_array($productId['id'], $this->alreadyFetchedProductsIds)) $unfetchedProductsIds[] = [$productId];
        }

        return $unfetchedProductsIds;
    }

    public function saveFetchedProductsIds(array $fetchedProducts): void
    {
        foreach ($fetchedProducts as $product)
        {
            $this->alreadyFetchedProductsIds[] = $product->getId();
        }
    }
}