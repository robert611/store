<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\ProductFixtures;
use App\Entity\Basket;


class BasketFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $basketProducts = $this->getBasketProducts();

        foreach ($basketProducts as $key => $product) {
            $basket = new Basket();

            $basket->setProduct($this->getReference("product." . ($product['product_id'] - 1)));
            $basket->setUser($this->getReference("user." . ($product['user_id'] - 1)));
            $basket->setQuantity($product['quantity']);

            $manager->persist($basket);

            $this->addReference('basket.'. $key, $basket);
        }

        $manager->flush();
    }

    public function getBasketProducts()
    {
        return [
            [
                'product_id' => 1,
                'user_id' => 2,
                'quantity' => 10
            ],
            [
                'product_id' => 2,
                'user_id' => 2,
                'quantity' => 1
            ],
            [
                'product_id' => 4,
                'user_id' => 3,
                'quantity' => 12
            ],
            [
                'product_id' => 5,
                'user_id' => 4,
                'quantity' => 20
            ],
            [
                'product_id' => 5,
                'user_id' => 3,
                'quantity' => 5
            ],
            [
                'product_id' => 9,
                'user_id' => 9,
                'quantity' => 1
            ]
        ];
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            ProductFixtures::class        
        );
    }
}
