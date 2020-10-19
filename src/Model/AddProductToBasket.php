<?php 

namespace App\Model;

use App\Repository\BasketRepository;

class AddProductToBasket 
{
    private $basketRepository;
    private $quantityPossibleToAdd;

    public function __construct(BasketRepository $basketRepository)
    {
        $this->basketRepository = $basketRepository;
    }

    public function addProductToBasket(int $quantity, \App\Entity\Product $product, $user)
    {
        $productInTheBasket = $this->basketRepository->findOneBy(['user' => $user, 'product' => $product]);
    
        if ($productInTheBasket) {
            $this->increaseExistingProductAmount($quantity, $product, $user, $productInTheBasket);

            return;
        }

        $this->addNewProduct($quantity, $product, $user);
    }

    private function addNewProduct($quantity, $product, $user)
    {
        /* Make sure somebody does not want to add more products then there actually is */
        $quantity > $product->getQuantity() ? $quantity = $this->quantityPossibleToAdd = $product->getQuantity() : null;

        $this->basketRepository->addProductToBasket($user->getId(), $product->getId(), $quantity);
    }

    private function increaseExistingProductAmount($quantity, $product, $user, $basketProduct)
    {
        $potentialQuantityInTheBasket = $basketProduct->getQuantity() + $quantity;

        $quantityDiffrence = $potentialQuantityInTheBasket - $product->getQuantity();

        if ($quantityDiffrence > 0) {
            $quantityPossibleToAdd = $quantity - $quantityDiffrence;

            /* Make sure it is not less than zero */
            $quantityPossibleToAdd < 0 ? $quantityPossibleToAdd = 0 : null;

            $this->quantityPossibleToAdd = $quantityPossibleToAdd;
            
            $quantity = $quantityPossibleToAdd;
        }

        $this->basketRepository->increaseProductQuantity($user->getId(), $product->getId(), $quantity);
    }

    public function isQuantityToBig(): bool
    {
        return !is_null($this->quantityPossibleToAdd);
    }

    /**
     * Get the value of quantityPossibleToAdd
     */ 
    public function getQuantityPossibleToAdd()
    {
        return $this->quantityPossibleToAdd;
    }
}